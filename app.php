<?php

/**
 * ============================================================================
 * Dynamic Application Configuration
 * ============================================================================
 * 
 * This file provides dynamic configuration for the Laravel application
 * that automatically detects the environment (localhost vs production/cPanel)
 * and sets up paths, URLs, and database connections accordingly.
 * 
 * Usage:
 * - Include this file before Laravel bootstrap
 * - Or use it to generate .env file dynamically
 * - Works with both localhost and cPanel deployments
 * 
 * ============================================================================
 */

if (!defined('APP_CONFIG_LOADED')) {
    define('APP_CONFIG_LOADED', true);
}

/**
 * Detect Environment
 * Automatically determines if running on localhost or production
 */
function detectEnvironment() {
    // Check for common localhost indicators
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    $isLocalhost = (
        strpos($host, 'localhost') !== false ||
        strpos($host, '127.0.0.1') !== false ||
        strpos($host, '::1') !== false ||
        strpos($host, '.local') !== false ||
        strpos($host, '.test') !== false ||
        strpos($host, '.dev') !== false ||
        (isset($_SERVER['SERVER_ADDR']) && in_array($_SERVER['SERVER_ADDR'], ['127.0.0.1', '::1']))
    );
    
    return $isLocalhost ? 'local' : 'production';
}

/**
 * Get Base Path
 * Dynamically determines the application base path
 */
function getBasePath() {
    // Try to get from environment variable first
    if (defined('LARAVEL_BASE_PATH')) {
        return LARAVEL_BASE_PATH;
    }
    
    // Get the directory where this file is located
    $basePath = dirname(__FILE__);
    
    // If this file is in the root, return it
    // If in a subdirectory, go up to root
    if (file_exists($basePath . '/artisan')) {
        return $basePath;
    }
    
    // Fallback: try to find Laravel root
    $currentDir = $basePath;
    for ($i = 0; $i < 5; $i++) {
        if (file_exists($currentDir . '/artisan')) {
            return $currentDir;
        }
        $currentDir = dirname($currentDir);
    }
    
    return $basePath;
}

/**
 * Get Public Path
 * Dynamically determines the public directory path
 */
function getPublicPath() {
    $basePath = getBasePath();
    $publicPath = $basePath . '/public';
    
    // Check if public directory exists
    if (is_dir($publicPath)) {
        return $publicPath;
    }
    
    // For cPanel, public_html might be the public directory
    if (is_dir($basePath . '/public_html')) {
        return $basePath . '/public_html';
    }
    
    // Fallback to base path (for some cPanel setups)
    return $basePath;
}

/**
 * Get Application URL
 * Dynamically determines the application URL
 */
function getAppUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    
    // Get port if not standard
    $port = '';
    if (isset($_SERVER['SERVER_PORT']) && 
        (($protocol === 'http' && $_SERVER['SERVER_PORT'] != 80) || 
         ($protocol === 'https' && $_SERVER['SERVER_PORT'] != 443))) {
        $port = ':' . $_SERVER['SERVER_PORT'];
    }
    
    // Get subdirectory if application is in a subdirectory
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $subdirectory = '';
    
    // Extract subdirectory from script path
    if (strpos($scriptName, '/public/') !== false) {
        $parts = explode('/public/', $scriptName);
        if (count($parts) > 1) {
            $subdirectory = dirname($parts[0]);
            if ($subdirectory === '.' || $subdirectory === '/') {
                $subdirectory = '';
            }
        }
    }
    
    $url = $protocol . '://' . $host . $port . $subdirectory;
    
    // Remove trailing slash
    return rtrim($url, '/');
}

/**
 * Parse .env file manually (before Laravel loads)
 * Reads .env file and returns array of key-value pairs
 */
function parseEnvFile($envPath) {
    $env = [];
    
    if (!file_exists($envPath)) {
        return $env;
    }
    
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE format
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            $env[$key] = $value;
        }
    }
    
    return $env;
}

/**
 * Get Environment Variable (Native PHP - works before Laravel loads)
 * This is a replacement for Laravel's env() helper
 */
function getEnvVar($key, $default = null) {
    static $envFileCache = null;
    
    // Check $_ENV first (highest priority)
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return $_ENV[$key];
    }
    
    // Check getenv() (native PHP function)
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }
    
    // Check putenv() values
    if (function_exists('apache_getenv')) {
        $value = apache_getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }
    }
    
    // Load .env file if not cached yet
    if ($envFileCache === null) {
        $basePath = getBasePath();
        $envFile = $basePath . '/.env';
        $envFileCache = parseEnvFile($envFile);
    }
    
    // Check .env file
    if (isset($envFileCache[$key]) && $envFileCache[$key] !== '') {
        return $envFileCache[$key];
    }
    
    return $default;
}

/**
 * Get Database Configuration
 * Returns database config based on environment
 */
function getDatabaseConfig() {
    $env = detectEnvironment();
    
    if ($env === 'local') {
        // Localhost configuration
        return [
            'host' => getEnvVar('DB_HOST', '127.0.0.1'),
            'port' => getEnvVar('DB_PORT', '3306'),
            'database' => getEnvVar('DB_DATABASE', 'khbevents_kmallxmas'),
            'username' => getEnvVar('DB_USERNAME', 'root'),
            'password' => getEnvVar('DB_PASSWORD', ''),
        ];
    } else {
        // Production/cPanel configuration
        // cPanel typically uses these environment variables or .env file
        return [
            'host' => getEnvVar('DB_HOST', 'localhost'),
            'port' => getEnvVar('DB_PORT', '3306'),
            'database' => getEnvVar('DB_DATABASE', ''),
            'username' => getEnvVar('DB_USERNAME', ''),
            'password' => getEnvVar('DB_PASSWORD', ''),
        ];
    }
}

/**
 * Auto-configure Environment Variables
 * Sets up $_ENV and putenv for Laravel to use
 */
function autoConfigureEnvironment() {
    $env = detectEnvironment();
    $basePath = getBasePath();
    $appUrl = getAppUrl();
    
    // Set base path
    if (!isset($_ENV['APP_BASE_PATH'])) {
        $_ENV['APP_BASE_PATH'] = $basePath;
        putenv('APP_BASE_PATH=' . $basePath);
    }
    
    // Set application environment
    if (!isset($_ENV['APP_ENV'])) {
        $_ENV['APP_ENV'] = $env;
        putenv('APP_ENV=' . $env);
    }
    
    // Set debug mode
    if (!isset($_ENV['APP_DEBUG'])) {
        $_ENV['APP_DEBUG'] = $env === 'local' ? 'true' : 'false';
        putenv('APP_DEBUG=' . ($env === 'local' ? 'true' : 'false'));
    }
    
    // Set application URL
    if (!isset($_ENV['APP_URL'])) {
        $_ENV['APP_URL'] = $appUrl;
        putenv('APP_URL=' . $appUrl);
    }
    
    // Set application name
    if (!isset($_ENV['APP_NAME'])) {
        $_ENV['APP_NAME'] = 'KHB Booths Booking System';
        putenv('APP_NAME=KHB Booths Booking System');
    }
    
    // Set timezone
    if (!isset($_ENV['APP_TIMEZONE'])) {
        $_ENV['APP_TIMEZONE'] = 'Asia/Phnom_Penh';
        putenv('APP_TIMEZONE=Asia/Phnom_Penh');
    }
    
    // Database configuration
    $dbConfig = getDatabaseConfig();
    if (!isset($_ENV['DB_CONNECTION'])) {
        $_ENV['DB_CONNECTION'] = 'mysql';
        putenv('DB_CONNECTION=mysql');
    }
    if (!isset($_ENV['DB_HOST'])) {
        $_ENV['DB_HOST'] = $dbConfig['host'];
        putenv('DB_HOST=' . $dbConfig['host']);
    }
    if (!isset($_ENV['DB_PORT'])) {
        $_ENV['DB_PORT'] = $dbConfig['port'];
        putenv('DB_PORT=' . $dbConfig['port']);
    }
    if (!isset($_ENV['DB_DATABASE'])) {
        $_ENV['DB_DATABASE'] = $dbConfig['database'];
        putenv('DB_DATABASE=' . $dbConfig['database']);
    }
    if (!isset($_ENV['DB_USERNAME'])) {
        $_ENV['DB_USERNAME'] = $dbConfig['username'];
        putenv('DB_USERNAME=' . $dbConfig['username']);
    }
    if (!isset($_ENV['DB_PASSWORD'])) {
        $_ENV['DB_PASSWORD'] = $dbConfig['password'];
        putenv('DB_PASSWORD=' . $dbConfig['password']);
    }
    
    // Session and cache drivers
    if (!isset($_ENV['SESSION_DRIVER'])) {
        $_ENV['SESSION_DRIVER'] = 'file';
        putenv('SESSION_DRIVER=file');
    }
    
    if (!isset($_ENV['CACHE_DRIVER'])) {
        $_ENV['CACHE_DRIVER'] = 'file';
        putenv('CACHE_DRIVER=file');
    }
    
    // Logging
    if (!isset($_ENV['LOG_CHANNEL'])) {
        $_ENV['LOG_CHANNEL'] = 'stack';
        putenv('LOG_CHANNEL=stack');
    }
    
    if (!isset($_ENV['LOG_LEVEL'])) {
        $_ENV['LOG_LEVEL'] = $env === 'local' ? 'debug' : 'error';
        putenv('LOG_LEVEL=' . ($env === 'local' ? 'debug' : 'error'));
    }
}

/**
 * Generate .env file dynamically
 * Creates or updates .env file with detected configuration
 */
function generateEnvFile($force = false) {
    $basePath = getBasePath();
    $envFile = $basePath . '/.env';
    $envExample = $basePath . '/.env.example';
    
    // If .env exists and not forcing, don't overwrite
    if (file_exists($envFile) && !$force) {
        return false;
    }
    
    $env = detectEnvironment();
    $appUrl = getAppUrl();
    $dbConfig = getDatabaseConfig();
    
    // Read .env.example if exists
    $envContent = '';
    if (file_exists($envExample)) {
        $envContent = file_get_contents($envExample);
    }
    
    // Generate .env content
    $newEnvContent = "APP_NAME=\"KHB Booths Booking System\"\n";
    $newEnvContent .= "APP_ENV={$env}\n";
    $newEnvContent .= "APP_KEY=\n";
    $newEnvContent .= "APP_DEBUG=" . ($env === 'local' ? 'true' : 'false') . "\n";
    $newEnvContent .= "APP_URL={$appUrl}\n";
    $newEnvContent .= "APP_TIMEZONE=Asia/Phnom_Penh\n\n";
    
    $newEnvContent .= "DB_CONNECTION=mysql\n";
    $newEnvContent .= "DB_HOST={$dbConfig['host']}\n";
    $newEnvContent .= "DB_PORT={$dbConfig['port']}\n";
    $newEnvContent .= "DB_DATABASE={$dbConfig['database']}\n";
    $newEnvContent .= "DB_USERNAME={$dbConfig['username']}\n";
    $newEnvContent .= "DB_PASSWORD={$dbConfig['password']}\n\n";
    
    $newEnvContent .= "SESSION_DRIVER=file\n";
    $newEnvContent .= "SESSION_LIFETIME=120\n\n";
    
    $newEnvContent .= "CACHE_DRIVER=file\n\n";
    
    $newEnvContent .= "LOG_CHANNEL=stack\n";
    $newEnvContent .= "LOG_LEVEL=" . ($env === 'local' ? 'debug' : 'error') . "\n";
    
    // Write .env file
    file_put_contents($envFile, $newEnvContent);
    
    return true;
}

/**
 * Get Configuration Summary
 * Returns array with all detected configuration
 */
function getConfigSummary() {
    return [
        'environment' => detectEnvironment(),
        'base_path' => getBasePath(),
        'public_path' => getPublicPath(),
        'app_url' => getAppUrl(),
        'database' => getDatabaseConfig(),
        'is_localhost' => detectEnvironment() === 'local',
        'is_production' => detectEnvironment() === 'production',
    ];
}

// Auto-configure when this file is included
autoConfigureEnvironment();

// Return configuration helper functions
return [
    'detectEnvironment' => 'detectEnvironment',
    'getBasePath' => 'getBasePath',
    'getPublicPath' => 'getPublicPath',
    'getAppUrl' => 'getAppUrl',
    'getDatabaseConfig' => 'getDatabaseConfig',
    'autoConfigureEnvironment' => 'autoConfigureEnvironment',
    'generateEnvFile' => 'generateEnvFile',
    'getConfigSummary' => 'getConfigSummary',
];

