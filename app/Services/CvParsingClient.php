<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

/**
 * CV Parsing Client
 * Communicates with external Python microservice for CV parsing
 */
class CvParsingClient
{
    private Client $client;
    private string $basePath;
    private string $apiKey;
    private bool $enabled;

    public function __construct()
    {
        $this->basePath = config('CvParsing')->basePath;
        $this->apiKey = config('CvParsing')->apiKey;
        $this->enabled = config('CvParsing')->enabled;

        $this->client = new Client([
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

            // Create multipart form data
            $response = $this->client->post(
                $this->basePath . '/api/parse-cv',
                [
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
                ]
            );

            $body = json_decode($response->getBody(), true);

            if (!isset($body['success']) || !$body['success']) {
                throw new \Exception($body['error'] ?? 'Parsing failed');
            }

            return $body;

        } catch (ConnectException $e) {
            log_message('error', 'Cannot reach CV Parsing service at ' . $this->basePath);
            throw new \Exception('CV Parsing service unavailable. Please try again later.');
        } catch (RequestException $e) {
            $message = $e->getResponse()?->getBody()?->getContents() ?? $e->getMessage();
            log_message('error', 'CV Parsing error: ' . $message);
            throw new \Exception('Error parsing CV: ' . $message);
        } catch (GuzzleException $e) {
            log_message('error', 'Guzzle error: ' . $e->getMessage());
            throw new \Exception('Network error. Please try again.');
        }
    }

    /**
     * Check if parsing service is healthy
     * Used for status checks
     */
    public function isHealthy(): bool
    {
        try {
            $response = $this->client->get($this->basePath . '/health');
            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            log_message('warning', 'CV Parsing service health check failed: ' . $e->getMessage());
            return false;
        }
    }
}
