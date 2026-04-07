<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class CvParsing extends BaseConfig
{
    /**
     * Enable/Disable CV Parsing integration
     */
    public bool $enabled = (bool) env('CV_PARSING_ENABLED', true);

    /**
     * Base URL of Python CV Parsing service
     * Example: http://localhost:8001
     */
    public string $basePath = env('CV_PARSING_BASE_URL', 'http://localhost:8001');

    /**
     * API Key for authentication with Python service
     */
    public string $apiKey = env('CV_PARSING_API_KEY', 'your-secret-key-here');

    /**
     * Request timeout in seconds
     */
    public int $timeout = (int) env('CV_PARSING_TIMEOUT', 60);

    /**
     * Connection timeout in seconds
     */
    public int $connectTimeout = (int) env('CV_PARSING_CONNECT_TIMEOUT', 10);

    /**
     * Allowed file types for upload
     */
    public array $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

    /**
     * Max file size in MB
     */
    public int $maxFileSizeMB = (int) env('CV_PARSING_MAX_SIZE_MB', 10);
}
