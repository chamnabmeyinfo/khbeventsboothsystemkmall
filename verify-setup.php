<?php
/**
 * Setup Verification Script
 * 
 * Run this script to verify your setup is correct for booths.khbevents.com
 * Access via: https://booths.khbevents.com/verify-setup.php
 * 
 * ⚠️ DELETE THIS FILE AFTER VERIFICATION FOR SECURITY!
 */

// Security: Only allow access in development or with password
$allowed = false;

// Option 1: Check if APP_DEBUG is true
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/APP_DEBUG\s*=\s*true/i', $envContent)) {
        $allowed = true;
    }
}

// Option 2: Check for password (set ?password=your_secret)
if (isset($_GET['password']) && $_GET['password'] === 'khbevents2026') {
    $allowed = true;
}

if (!$allowed) {
    http_response_code(403);
    die('Access Denied');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Verification - KHB Events</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        .check-item {
            padding: 10px;
            margin: 5px 0;
            border-left: 4px solid #ddd;
            background: #f9f9f9;
        }
        .check-item.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .check-item.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .check-item.warning {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .status {
            font-weight: bold;
            margin-right: 10px;
        }
        .status.success { color: #28a745; }
        .status.error { color: #dc3545; }
        .status.warning { color: #ffc107; }
        .info {
            margin-top: 20px;
            padding: 15px;
            background: #e7f3ff;
            border-left: 4px solid #007bff;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Setup Verification - booths.khbevents.com</h1>
        
        <?php
        $checks = [];
        $allPassed = true;
        
        // Check 1: PHP Version
        $phpVersion = PHP_VERSION;
        $phpOk = version_compare($phpVersion, '8.1.0', '>=');
        $checks[] = [
            'name' => 'PHP Version',
            'status' => $phpOk ? 'success' : 'error',
            'message' => $phpOk ? "PHP {$phpVersion} ✓" : "PHP {$phpVersion} - Requires 8.1+",
            'passed' => $phpOk
        ];
        if (!$phpOk) $allPassed = false;
        
        // Check 2: .env File
        $envExists = file_exists(__DIR__ . '/.env');
        $checks[] = [
            'name' => '.env File',
            'status' => $envExists ? 'success' : 'error',
            'message' => $envExists ? 'Exists ✓' : 'Missing - Copy from .env.example',
            'passed' => $envExists
        ];
        if (!$envExists) $allPassed = false;
        
        // Check 3: APP_KEY
        $appKeySet = false;
        if ($envExists) {
            $envContent = file_get_contents(__DIR__ . '/.env');
            $appKeySet = preg_match('/APP_KEY\s*=\s*base64:[A-Za-z0-9+\/]+=*/', $envContent);
        }
        $checks[] = [
            'name' => 'APP_KEY',
            'status' => $appKeySet ? 'success' : 'error',
            'message' => $appKeySet ? 'Generated ✓' : 'Not set - Run: php artisan key:generate',
            'passed' => $appKeySet
        ];
        if (!$appKeySet) $allPassed = false;
        
        // Check 4: APP_URL
        $appUrlCorrect = false;
        if ($envExists) {
            $envContent = file_get_contents(__DIR__ . '/.env');
            $appUrlCorrect = preg_match('/APP_URL\s*=\s*https?:\/\/booths\.khbevents\.com/i', $envContent);
        }
        $checks[] = [
            'name' => 'APP_URL',
            'status' => $appUrlCorrect ? 'success' : 'warning',
            'message' => $appUrlCorrect ? 'Set to booths.khbevents.com ✓' : 'Should be: https://booths.khbevents.com',
            'passed' => $appUrlCorrect
        ];
        
        // Check 5: Storage Permissions
        $storageWritable = is_writable(__DIR__ . '/storage');
        $checks[] = [
            'name' => 'Storage Permissions',
            'status' => $storageWritable ? 'success' : 'error',
            'message' => $storageWritable ? 'Writable ✓' : 'Not writable - Run: chmod -R 755 storage',
            'passed' => $storageWritable
        ];
        if (!$storageWritable) $allPassed = false;
        
        // Check 6: Bootstrap Cache Permissions
        $bootstrapWritable = is_writable(__DIR__ . '/bootstrap/cache');
        $checks[] = [
            'name' => 'Bootstrap Cache Permissions',
            'status' => $bootstrapWritable ? 'success' : 'error',
            'message' => $bootstrapWritable ? 'Writable ✓' : 'Not writable - Run: chmod -R 755 bootstrap/cache',
            'passed' => $bootstrapWritable
        ];
        if (!$bootstrapWritable) $allPassed = false;
        
        // Check 7: Vendor Directory
        $vendorExists = is_dir(__DIR__ . '/vendor');
        $checks[] = [
            'name' => 'Vendor Directory',
            'status' => $vendorExists ? 'success' : 'error',
            'message' => $vendorExists ? 'Exists ✓' : 'Missing - Run: composer install',
            'passed' => $vendorExists
        ];
        if (!$vendorExists) $allPassed = false;
        
        // Check 8: .htaccess Files
        $rootHtaccess = file_exists(__DIR__ . '/.htaccess');
        $publicHtaccess = file_exists(__DIR__ . '/public/.htaccess');
        $htaccessOk = $rootHtaccess && $publicHtaccess;
        $checks[] = [
            'name' => '.htaccess Files',
            'status' => $htaccessOk ? 'success' : 'error',
            'message' => $htaccessOk ? 'Both exist ✓' : ($rootHtaccess ? 'Missing public/.htaccess' : 'Missing root .htaccess'),
            'passed' => $htaccessOk
        ];
        if (!$htaccessOk) $allPassed = false;
        
        // Check 9: Database Connection (if .env exists)
        $dbConnected = false;
        $dbError = '';
        if ($envExists && $vendorExists) {
            try {
                require __DIR__ . '/vendor/autoload.php';
                $envFile = file_get_contents(__DIR__ . '/.env');
                preg_match('/DB_HOST\s*=\s*([^\s]+)/', $envFile, $hostMatch);
                preg_match('/DB_DATABASE\s*=\s*([^\s]+)/', $envFile, $dbMatch);
                preg_match('/DB_USERNAME\s*=\s*([^\s]+)/', $envFile, $userMatch);
                preg_match('/DB_PASSWORD\s*=\s*([^\s]+)/', $envFile, $passMatch);
                
                if ($hostMatch && $dbMatch && $userMatch) {
                    $host = trim($hostMatch[1]);
                    $database = trim($dbMatch[1]);
                    $username = trim($userMatch[1]);
                    $password = isset($passMatch[1]) ? trim($passMatch[1]) : '';
                    
                    if (!empty($database) && !empty($username)) {
                        $conn = @mysqli_connect($host, $username, $password, $database);
                        if ($conn) {
                            $dbConnected = true;
                            mysqli_close($conn);
                        } else {
                            $dbError = mysqli_connect_error();
                        }
                    }
                }
            } catch (Exception $e) {
                $dbError = $e->getMessage();
            }
        }
        $checks[] = [
            'name' => 'Database Connection',
            'status' => $dbConnected ? 'success' : 'warning',
            'message' => $dbConnected ? 'Connected ✓' : ($dbError ? "Error: {$dbError}" : 'Check .env database settings'),
            'passed' => $dbConnected
        ];
        
        // Display checks
        foreach ($checks as $check) {
            $statusClass = $check['status'];
            $statusIcon = $check['status'] === 'success' ? '✓' : ($check['status'] === 'error' ? '✗' : '⚠');
            echo "<div class='check-item {$statusClass}'>";
            echo "<span class='status {$statusClass}'>{$statusIcon}</span>";
            echo "<strong>{$check['name']}:</strong> {$check['message']}";
            echo "</div>";
        }
        
        // Summary
        echo "<div class='info'>";
        if ($allPassed) {
            echo "<h2 style='color: #28a745;'>✅ Setup Looks Good!</h2>";
            echo "<p>All critical checks passed. Your application should be ready to run.</p>";
        } else {
            echo "<h2 style='color: #dc3545;'>⚠️ Issues Found</h2>";
            echo "<p>Please fix the errors above before proceeding.</p>";
        }
        echo "<p><strong>Next Steps:</strong></p>";
        echo "<ul>";
        echo "<li>If all checks pass, visit <a href='/'>https://booths.khbevents.com</a></li>";
        echo "<li>Login with default credentials: <code>admin</code> / <code>password</code></li>";
        echo "<li><strong>Change the admin password immediately!</strong></li>";
        echo "<li><strong>Delete this verification file for security!</strong></li>";
        echo "</ul>";
        echo "</div>";
        ?>
    </div>
</body>
</html>
