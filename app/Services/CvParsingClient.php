<?php

namespace App\Services;

/**
 * CV Parsing Client
 * 
 * Communicates with external Python microservice for CV parsing.
 * Uses CodeIgniter's native CURLRequest - no external dependencies needed.
 */
class CvParsingClient
{
    private $client;
    private string $basePath;
    private string $apiKey;
    private bool $enabled;

    public function __construct()
    {
        $this->basePath = config('CvParsing')->basePath;
        $this->apiKey = config('CvParsing')->apiKey;
        $this->enabled = config('CvParsing')->enabled;

        // Use CodeIgniter's native CURLRequest service
        $this->client = \Config\Services::curlrequest([
            'timeout' => (int) config('CvParsing')->timeout,
            'connect_timeout' => (int) config('CvParsing')->connectTimeout,
            'verify' => false, // For local dev - enable in production
        ]);
    }

    /**
     * Parse CV file using external Python service
     *
     * @param string $filePath Absolute path to CV file
     * @return array Parsed CV data with confidence scores
     * @throws \Exception
     */
    public function parseCv(string $filePath): array
    {
        if (!$this->enabled) {
            throw new \Exception('CV Parsing service is disabled');
        }

        if (!file_exists($filePath)) {
            throw new \Exception("File not found: $filePath");
        }

        try {
            $fileContent = file_get_contents($filePath);
            $fileName = basename($filePath);

            // Make multipart request to Python service endpoint
            $response = $this->client->request('POST', $this->basePath . '/api/parse-cv', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $fileContent,
                        'filename' => $fileName,
                    ],
                ],
                'headers' => [
                    'X-API-Key' => $this->apiKey,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();

            if ($statusCode !== 200) {
                log_message('error', "CV Parsing service returned HTTP {$statusCode}: {$body}");
                throw new \Exception("Service error (HTTP {$statusCode})");
            }

            $data = json_decode($body, true);

            if ($data === null) {
                log_message('error', "Invalid JSON from CV Parsing service: {$body}");
                throw new \Exception('Invalid response from parsing service');
            }

            if (!isset($data['success']) || !$data['success']) {
                throw new \Exception($data['error'] ?? 'CV parsing failed');
            }

            log_message('info', "CV parsing successful: {$filePath}");
            return $data;

        } catch (\Exception $e) {
            $message = $e->getMessage();
            
            // Log the error
            log_message('error', "CV Parsing error: {$message}");
            
            // Return user-friendly error messages
            if (strpos($message, 'Connection refused') !== false || 
                strpos($message, 'Failed to connect') !== false) {
                throw new \Exception('CV Parsing service is unavailable. Please try again later.');
            } elseif (strpos($message, 'timed out') !== false) {
                throw new \Exception('CV Parsing took too long. Please try with a smaller file.');
            }
            
            throw new \Exception($message);
        }
    }

    /**
     * Check if parsing service is healthy
     * Used for status checks and diagnostics
     *
     * @return bool True if service is responding, false otherwise
     */
    public function isHealthy(): bool
    {
        try {
            $response = $this->client->request('GET', $this->basePath . '/health', [
                'timeout' => 5,
                'connect_timeout' => 3,
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            log_message('warning', 'CV Parsing service health check failed: ' . $e->getMessage());
            return false;
        }
    }
}
