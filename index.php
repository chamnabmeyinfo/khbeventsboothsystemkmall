<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * This file redirects all requests to the public directory
 * when Apache is configured to use the project root as document root.
 * 
 * For direct domain access: booths.khbevents.com
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
if ($requestUri !== '/' && $requestUri !== '/index.php' && file_exists(__DIR__ . $requestUri) && is_file(__DIR__ . $requestUri)) {
    return false; // Let Apache serve the file
}

// If requesting a directory that exists, let Apache handle it
if ($requestUri !== '/' && is_dir(__DIR__ . $requestUri)) {
    return false;
}

// Otherwise, route through Laravel's public/index.php
// Change directory to public to maintain proper context
$publicPath = __DIR__ . '/public';

// Ensure public directory exists
if (!is_dir($publicPath)) {
    http_response_code(500);
    die('Error: Public directory not found. Please check your installation.');
}

// Update server variables to reflect we're in the public directory
// This ensures Laravel's asset helpers work correctly
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';
$_SERVER['DOCUMENT_ROOT'] = __DIR__;

// Adjust REQUEST_URI if it doesn't start with /public
// This handles cases where mod_rewrite hasn't redirected yet
if (strpos($requestUri, '/public/') !== 0 && $requestUri !== '/') {
    // If the request is for a public asset, adjust the path
    if (file_exists($publicPath . $requestUri)) {
        // Asset exists in public, keep the URI as is
    } else {
        // Route request through Laravel
    }
}

// Change to public directory to maintain proper context
chdir($publicPath);

// Require Laravel's bootstrap
require $publicPath . '/index.php';

