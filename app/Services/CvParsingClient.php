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
    private string $basePath;
    private string $apiKey;
    private bool $enabled;
    private int $timeout;
    private int $connectTimeout;

    public function __construct()
    {
        $cfg                  = config('CvParsing');
        $this->basePath       = $cfg->basePath;
        $this->apiKey         = $cfg->apiKey;
        $this->enabled        = $cfg->enabled;
        $this->timeout        = (int) $cfg->timeout;
        $this->connectTimeout = (int) $cfg->connectTimeout;
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
            $url      = rtrim($this->basePath, '/') . '/api/parse-cv';
            $fileName = basename($filePath);
            $mimeType = $this->guessMimeType($filePath);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => ['file' => new \CURLFile($filePath, $mimeType, $fileName)],
                CURLOPT_HTTPHEADER     => ['X-Api-Key: ' . $this->apiKey],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => $this->timeout ?? 60,
                CURLOPT_CONNECTTIMEOUT => $this->connectTimeout ?? 10,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $body       = curl_exec($ch);
            $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError  = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                throw new \Exception('cURL error: ' . $curlError);
            }

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
     * Guess MIME type from file extension — avoids finfo_file() failures on some servers.
     */
    private function guessMimeType(string $filePath): string
    {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $map = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];
        if (isset($map[$ext])) {
            return $map[$ext];
        }
        // Try finfo as a fallback, suppress warnings
        if (function_exists('mime_content_type') && file_exists($filePath)) {
            $mime = @mime_content_type($filePath);
            if ($mime) {
                return $mime;
            }
        }
        return 'application/octet-stream';
    }

    /**
     * Check if parsing service is healthy
     */
    public function isHealthy(): bool
    {
        try {
            $ch = curl_init(rtrim($this->basePath, '/') . '/health');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            curl_exec($ch);
            $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            return $code === 200;
        } catch (\Exception $e) {
            log_message('warning', 'CV Parsing service health check failed: ' . $e->getMessage());
            return false;
        }
    }
}
