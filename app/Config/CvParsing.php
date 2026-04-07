<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class CvParsing extends BaseConfig
{
    /**
     * Enable/Disable CV Parsing integration
     */
    public $enabled;

    /**
     * Base URL of Python CV Parsing service
     * Example: http://localhost:8001
     */
    public $basePath;

    /**
     * API Key for authentication with Python service
     */
    public $apiKey;

    /**
     * Request timeout in seconds
     */
    public $timeout;

    /**
     * Connection timeout in seconds
     */
    public $connectTimeout;

    /**
     * Allowed file types for upload
     */
    public $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

    /**
     * Max file size in MB
     */
    public $maxFileSizeMB;

    /**
     * Constructor initializes all properties from environment variables
     */
    public function __construct()
    {
        $this->enabled = (bool) env('CV_PARSING_ENABLED', true);
        $this->basePath = env('CV_PARSING_BASE_URL', 'http://localhost:8001');
        $this->apiKey = env('CV_PARSING_API_KEY', 'your-secret-key-here');
        $this->timeout = (int) env('CV_PARSING_TIMEOUT', 60);
        $this->connectTimeout = (int) env('CV_PARSING_CONNECT_TIMEOUT', 10);
        $this->maxFileSizeMB = (int) env('CV_PARSING_MAX_SIZE_MB', 10);
    }
}
