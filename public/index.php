<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

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

// Load dynamic configuration (if app.php exists in parent directory)
$appConfigPath = dirname(__DIR__) . '/app.php';
if (file_exists($appConfigPath)) {
    require_once $appConfigPath;
}

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$kernel = $app->make(Kernel::class);

// Ensure required $_SERVER variables are set before capturing request
// Fix null values that cause "Trying to access array offset on value of type null" error
if (empty($_SERVER['REQUEST_URI'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['REDIRECT_URL'] ?? $_SERVER['SCRIPT_NAME'] ?? '/';
}
if (empty($_SERVER['REQUEST_METHOD'])) {
    $_SERVER['REQUEST_METHOD'] = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? 'GET';
}
if (empty($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] ?? 'localhost';
}
if (empty($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'localhost';
}
if (empty($_SERVER['SERVER_PORT'])) {
    $_SERVER['SERVER_PORT'] = '8000';
}
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === null) {
    $_SERVER['HTTPS'] = '';
}
if (empty($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = '/index.php';
}
if (!isset($_SERVER['QUERY_STRING'])) {
    $_SERVER['QUERY_STRING'] = '';
}
if (!isset($_SERVER['SERVER_PROTOCOL'])) {
    $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
}
if (!isset($_SERVER['REQUEST_SCHEME'])) {
    $_SERVER['REQUEST_SCHEME'] = 'http';
}

// Ensure all superglobals are initialized and don't have null values
if (!isset($_GET)) $_GET = [];
if (!isset($_POST)) $_POST = [];
if (!isset($_COOKIE)) $_COOKIE = [];
if (!isset($_FILES)) $_FILES = [];
if (!isset($_SERVER)) $_SERVER = [];

// Ensure $_SERVER array doesn't have null values that could cause issues
// Also ensure nested arrays don't have null values
// This is critical to prevent "Trying to access array offset on value of type null" errors
foreach ($_SERVER as $key => $value) {
    if ($value === null) {
        $_SERVER[$key] = '';
    } elseif (is_array($value)) {
        // Recursively clean nested arrays - ensure no null values
        $_SERVER[$key] = array_filter(array_map(function($v) {
            return $v === null ? '' : (is_array($v) ? array_filter($v, function($item) { return $item !== null; }) : $v);
        }, $value), function($v) { return $v !== null; });
    }
}

// Ensure all HTTP_* headers are strings (not null or arrays)
foreach ($_SERVER as $key => $value) {
    if (str_starts_with($key, 'HTTP_') && (!is_string($value) || $value === null)) {
        $_SERVER[$key] = is_string($value) ? $value : '';
    }
}

// Also ensure $_ENV doesn't have null values
if (isset($_ENV)) {
    foreach ($_ENV as $key => $value) {
        if ($value === null) {
            $_ENV[$key] = '';
        }
    }
}

// Ensure common HTTP headers are set to prevent null access errors
$requiredHeaders = [
    'HTTP_ACCEPT' => '*/*',
    'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.9',
    'HTTP_USER_AGENT' => 'Mozilla/5.0',
    'HTTP_CONNECTION' => 'keep-alive',
    'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, br',
    'HTTP_CACHE_CONTROL' => 'no-cache',
    'HTTP_PRAGMA' => 'no-cache',
];

foreach ($requiredHeaders as $header => $default) {
    if (!isset($_SERVER[$header]) || $_SERVER[$header] === null || $_SERVER[$header] === '') {
        $_SERVER[$header] = $default;
    }
}

// Ensure additional required server variables
$requiredServerVars = [
    'REMOTE_ADDR' => '127.0.0.1',
    'REQUEST_TIME' => time(),
    'REQUEST_TIME_FLOAT' => microtime(true),
    'PATH_INFO' => '',
    'SCRIPT_FILENAME' => __FILE__,
];

foreach ($requiredServerVars as $var => $default) {
    if (!isset($_SERVER[$var]) || $_SERVER[$var] === null || $_SERVER[$var] === '') {
        $_SERVER[$var] = $default;
    }
}

// Final cleanup: ensure no null values remain in $_SERVER before request capture
// Convert all null values to empty strings to prevent array offset errors
$cleanServer = [];
foreach ($_SERVER as $key => $value) {
    if ($value === null) {
        $cleanServer[$key] = '';
    } elseif (is_array($value)) {
        // Clean nested arrays - remove null values completely
        $cleanServer[$key] = array_filter(array_map(function($v) {
            return $v === null ? '' : $v;
        }, $value), function($v) { return $v !== null; });
    } else {
        $cleanServer[$key] = (string)$value; // Convert to string to ensure type safety
    }
}
$_SERVER = $cleanServer;

// Ensure all HTTP_* headers are strings and not null
foreach ($_SERVER as $key => $value) {
    if (str_starts_with($key, 'HTTP_')) {
        if ($value === null || is_array($value)) {
            $_SERVER[$key] = '';
        } else {
            $_SERVER[$key] = (string)$value;
        }
    }
}

// Build a completely clean server array with only non-null string values
$cleanServerArray = [];
$requiredKeys = [
    'REQUEST_URI', 'REQUEST_METHOD', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT',
    'SCRIPT_NAME', 'QUERY_STRING', 'SERVER_PROTOCOL', 'REQUEST_SCHEME',
    'REMOTE_ADDR', 'REQUEST_TIME', 'REQUEST_TIME_FLOAT', 'PATH_INFO',
    'SCRIPT_FILENAME', 'HTTPS', 'DOCUMENT_ROOT'
];

foreach ($requiredKeys as $key) {
    $value = $_SERVER[$key] ?? '';
    if ($key === 'REQUEST_TIME') {
        $cleanServerArray[$key] = is_numeric($value) ? (int)$value : time();
    } elseif ($key === 'REQUEST_TIME_FLOAT') {
        $cleanServerArray[$key] = is_numeric($value) ? (float)$value : microtime(true);
    } elseif ($key === 'SERVER_PORT') {
        $cleanServerArray[$key] = is_numeric($value) ? (int)$value : 8000;
    } else {
        $cleanServerArray[$key] = (string)$value;
    }
}

// Add all HTTP_* headers as strings (only non-null, non-array values)
foreach ($_SERVER as $key => $value) {
    if (str_starts_with($key, 'HTTP_') && $value !== null && !is_array($value)) {
        $cleanServerArray[$key] = (string)$value;
    }
}

// Add CONTENT_TYPE, CONTENT_LENGTH if they exist
foreach (['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'] as $key) {
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== null && !is_array($_SERVER[$key])) {
        $cleanServerArray[$key] = (string)$_SERVER[$key];
    }
}

// Create request manually using Symfony Request::create to avoid null issues
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

// Parse query string from URI if needed
$queryParams = $_GET ?? [];
if (strpos($uri, '?') !== false) {
    parse_str(parse_url($uri, PHP_URL_QUERY), $uriParams);
    $queryParams = array_merge($uriParams, $queryParams);
}

// For POST/PUT/PATCH requests, merge POST data with query params
$requestParams = $queryParams;
if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE']) && isset($_POST) && is_array($_POST)) {
    $requestParams = array_merge($requestParams, $_POST);
}

try {
    // Create Symfony request manually with clean server array
    $symfonyRequest = \Symfony\Component\HttpFoundation\Request::create(
        $uri,
        $method,
        $requestParams,
        $_COOKIE ?? [],
        $_FILES ?? [],
        $cleanServerArray
    );
    
    // Convert to Laravel Request
    Request::enableHttpMethodParameterOverride();
    $request = Request::createFromBase($symfonyRequest);
    
    $response = $kernel->handle($request)->send();
    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    // Final fallback: show error details for debugging
    if (strpos($e->getMessage(), 'array offset') !== false) {
        // Last attempt with absolute minimal server array
        $minimalServer = [
            'REQUEST_URI' => '/',
            'REQUEST_METHOD' => 'GET',
            'HTTP_HOST' => 'localhost:8000',
            'SERVER_NAME' => 'localhost',
            'SERVER_PORT' => 8000,
            'SCRIPT_NAME' => '/index.php',
            'QUERY_STRING' => '',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'REQUEST_SCHEME' => 'http',
            'REMOTE_ADDR' => '127.0.0.1',
            'REQUEST_TIME' => time(),
            'REQUEST_TIME_FLOAT' => microtime(true),
            'PATH_INFO' => '',
            'SCRIPT_FILENAME' => __FILE__,
            'HTTPS' => '',
        ];
        
        $symfonyRequest = \Symfony\Component\HttpFoundation\Request::create(
            '/',
            'GET',
            [],
            [],
            [],
            $minimalServer
        );
        
        Request::enableHttpMethodParameterOverride();
        $request = Request::createFromBase($symfonyRequest);
        
        $response = $kernel->handle($request)->send();
        $kernel->terminate($request, $response);
    } else {
        throw $e;
    }
}
