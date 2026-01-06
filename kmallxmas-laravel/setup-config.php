<?php

/**
 * ============================================================================
 * Dynamic Configuration Setup Script
 * ============================================================================
 * 
 * This script helps set up the application configuration for deployment.
 * Run this script once after uploading to cPanel or when setting up locally.
 * 
 * Usage:
 * - Via browser: http://localhost:8000/setup-config.php
 * - Via command line: php setup-config.php
 * 
 * ============================================================================
 */

// Load the dynamic configuration
require_once __DIR__ . '/app.php';

// Get configuration summary
$config = getConfigSummary();

// Check if running from command line
$isCli = php_sapi_name() === 'cli';

if (!$isCli) {
    // Browser output
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>KHB Booths - Configuration Setup</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
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
                border-bottom: 3px solid #667eea;
                padding-bottom: 10px;
            }
            .config-item {
                margin: 15px 0;
                padding: 15px;
                background: #f8f9fa;
                border-left: 4px solid #667eea;
                border-radius: 4px;
            }
            .config-label {
                font-weight: bold;
                color: #555;
                display: inline-block;
                width: 200px;
            }
            .config-value {
                color: #333;
                font-family: 'Courier New', monospace;
            }
            .success {
                background: #d4edda;
                border-color: #28a745;
                color: #155724;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
            .warning {
                background: #fff3cd;
                border-color: #ffc107;
                color: #856404;
                padding: 15px;
                border-radius: 4px;
                margin: 20px 0;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #667eea;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                margin: 10px 5px 0 0;
                border: none;
                cursor: pointer;
            }
            .btn:hover {
                background: #5568d3;
            }
            .btn-danger {
                background: #dc3545;
            }
            .btn-danger:hover {
                background: #c82333;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🚀 KHB Booths - Configuration Setup</h1>
            
            <?php
            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['generate_env'])) {
                    $generated = generateEnvFile(true);
                    if ($generated) {
                        echo '<div class="success">✅ .env file generated successfully!</div>';
                    } else {
                        echo '<div class="warning">⚠️ .env file already exists. Use "Force Regenerate" to overwrite.</div>';
                    }
                } elseif (isset($_POST['force_generate_env'])) {
                    $generated = generateEnvFile(true);
                    echo '<div class="success">✅ .env file regenerated successfully!</div>';
                }
            }
            ?>
            
            <h2>📋 Detected Configuration</h2>
            
            <div class="config-item">
                <span class="config-label">Environment:</span>
                <span class="config-value"><?php echo htmlspecialchars($config['environment']); ?></span>
            </div>
            
            <div class="config-item">
                <span class="config-label">Is Localhost:</span>
                <span class="config-value"><?php echo $config['is_localhost'] ? 'Yes ✅' : 'No (Production)'; ?></span>
            </div>
            
            <div class="config-item">
                <span class="config-label">Base Path:</span>
                <span class="config-value"><?php echo htmlspecialchars($config['base_path']); ?></span>
            </div>
            
            <div class="config-item">
                <span class="config-label">Public Path:</span>
                <span class="config-value"><?php echo htmlspecialchars($config['public_path']); ?></span>
            </div>
            
            <div class="config-item">
                <span class="config-label">Application URL:</span>
                <span class="config-value"><?php echo htmlspecialchars($config['app_url']); ?></span>
            </div>
            
            <div class="config-item">
                <span class="config-label">Database Host:</span>
                <span class="config-value"><?php echo htmlspecialchars($config['database']['host']); ?></span>
            </div>
            
            <div class="config-item">
                <span class="config-label">Database Name:</span>
                <span class="config-value"><?php echo htmlspecialchars($config['database']['database'] ?: '(Not set)'); ?></span>
            </div>
            
            <div class="config-item">
                <span class="config-label">Database Username:</span>
                <span class="config-value"><?php echo htmlspecialchars($config['database']['username'] ?: '(Not set)'); ?></span>
            </div>
            
            <hr style="margin: 30px 0;">
            
            <h2>⚙️ Configuration Actions</h2>
            
            <form method="POST" style="margin: 20px 0;">
                <p>
                    <strong>Generate .env File:</strong><br>
                    This will create a .env file with the detected configuration.
                </p>
                <button type="submit" name="generate_env" class="btn">Generate .env File</button>
                <button type="submit" name="force_generate_env" class="btn btn-danger">Force Regenerate .env</button>
            </form>
            
            <div class="warning">
                <strong>⚠️ Important Notes:</strong>
                <ul>
                    <li>After generating .env file, you need to set <code>APP_KEY</code> by running: <code>php artisan key:generate</code></li>
                    <li>Update database credentials in .env file if they differ from defaults</li>
                    <li>For production, make sure <code>APP_DEBUG=false</code> in .env file</li>
                    <li>Delete this setup-config.php file after configuration is complete for security</li>
                </ul>
            </div>
            
            <hr style="margin: 30px 0;">
            
            <h2>📝 Next Steps</h2>
            <ol>
                <li>Review the detected configuration above</li>
                <li>Generate .env file using the button above</li>
                <li>Edit .env file and update database credentials if needed</li>
                <li>Run <code>php artisan key:generate</code> to generate application key</li>
                <li>Run <code>php artisan migrate</code> to set up database tables</li>
                <li>Run <code>php artisan db:seed</code> to seed initial data (optional)</li>
                <li>Delete this setup-config.php file for security</li>
            </ol>
        </div>
    </body>
    </html>
    <?php
} else {
    // Command line output
    echo "========================================\n";
    echo "KHB Booths - Configuration Setup\n";
    echo "========================================\n\n";
    
    echo "Detected Configuration:\n";
    echo "-----------------------\n";
    echo "Environment: " . $config['environment'] . "\n";
    echo "Is Localhost: " . ($config['is_localhost'] ? 'Yes' : 'No (Production)') . "\n";
    echo "Base Path: " . $config['base_path'] . "\n";
    echo "Public Path: " . $config['public_path'] . "\n";
    echo "Application URL: " . $config['app_url'] . "\n";
    echo "Database Host: " . $config['database']['host'] . "\n";
    echo "Database Name: " . ($config['database']['database'] ?: '(Not set)') . "\n";
    echo "Database Username: " . ($config['database']['username'] ?: '(Not set)') . "\n\n";
    
    echo "Generating .env file...\n";
    $generated = generateEnvFile();
    if ($generated) {
        echo "✅ .env file generated successfully!\n\n";
    } else {
        echo "⚠️ .env file already exists. Use --force to overwrite.\n\n";
    }
    
    echo "Next Steps:\n";
    echo "1. Edit .env file and update database credentials if needed\n";
    echo "2. Run: php artisan key:generate\n";
    echo "3. Run: php artisan migrate\n";
    echo "4. Run: php artisan db:seed (optional)\n";
    echo "5. Delete this setup-config.php file for security\n";
}

