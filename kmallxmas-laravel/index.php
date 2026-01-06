<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * This file redirects all requests to the public directory
 * when Apache is configured to use the project root as document root.
 */

/*
|--------------------------------------------------------------------------
| Load Dynamic Configuration
|--------------------------------------------------------------------------
|
| Load the dynamic app.php configuration that auto-detects environment
| and sets up paths, URLs, and database connections automatically.
| This works for both localhost and cPanel deployments.
|
*/

// Load dynamic configuration
$appConfigPath = __DIR__ . '/app.php';
if (file_exists($appConfigPath)) {
    require_once $appConfigPath;
}

// Get the current request URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Remove query string if present
$requestUri = strtok($requestUri, '?');

// If the request is for a file that exists in the root, serve it directly
if ($requestUri !== '/' && file_exists(__DIR__ . $requestUri) && is_file(__DIR__ . $requestUri)) {
    return false; // Let Apache serve the file
}

// Otherwise, route through Laravel's public/index.php
// Change directory to public to maintain proper context
$publicPath = __DIR__ . '/public';

// Update server variables to reflect we're in the public directory
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';

// Change to public directory
chdir($publicPath);

// Require Laravel's bootstrap
require $publicPath . '/index.php';

