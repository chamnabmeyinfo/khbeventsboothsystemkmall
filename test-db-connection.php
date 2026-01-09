<?php
/**
 * Database Connection Test Script for cPanel
 * 
 * This script helps verify your database configuration before running Laravel migrations.
 * 
 * Usage:
 * 1. Update the database credentials below
 * 2. Upload this file to your server
 * 3. Access it via browser: https://yourdomain.com/test-db-connection.php
 * 4. Delete this file after successful connection test
 */

// Database Configuration - UPDATE THESE VALUES
$db_host = 'localhost';  // Usually 'localhost' for cPanel
$db_port = '3306';       // Standard MySQL port
$db_name = 'your_cpanel_username_boothsystem_db';  // Full database name with prefix
$db_user = 'your_cpanel_username_boothsystem_user'; // Full username with prefix
$db_pass = 'your_database_password';  // Database password

// Test Connection
echo "<h1>Database Connection Test</h1>";
echo "<p>Testing connection to: <strong>{$db_host}:{$db_port}</strong></p>";
echo "<p>Database: <strong>{$db_name}</strong></p>";
echo "<p>Username: <strong>{$db_user}</strong></p>";
echo "<hr>";

try {
    // Create connection
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    
    echo "<div style='color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px;'>";
    echo "<h2>✅ Connection Successful!</h2>";
    echo "<p>Your database connection is working correctly.</p>";
    echo "</div>";
    
    // Get MySQL version
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "<p><strong>MySQL Version:</strong> {$version['version']}</p>";
    
    // List tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>Tables in database:</strong> " . (count($tables) > 0 ? implode(', ', $tables) : 'No tables found') . "</p>";
    
    // Test write permissions (if tables exist)
    if (count($tables) > 0) {
        echo "<p><strong>Database Status:</strong> Ready for Laravel migrations</p>";
    } else {
        echo "<p><strong>Database Status:</strong> Empty database - ready for initial migrations</p>";
    }
    
    echo "<hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Update your <code>.env</code> file with these credentials</li>";
    echo "<li>Run <code>php artisan key:generate</code></li>";
    echo "<li>Run <code>php artisan migrate</code></li>";
    echo "<li><strong>Delete this test file</strong> for security</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h2>❌ Connection Failed!</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
    
    echo "<hr>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<li>Check that the username includes your cPanel prefix (e.g., <code>username_dbuser</code>)</li>";
        echo "<li>Verify the password is correct</li>";
        echo "<li>Ensure the user is assigned to the database in cPanel</li>";
    } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "<li>Check that the database name includes your cPanel prefix (e.g., <code>username_dbname</code>)</li>";
        echo "<li>Verify the database exists in cPanel MySQL Databases section</li>";
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false || strpos($e->getMessage(), 'timed out') !== false) {
        echo "<li>Try changing <code>DB_HOST</code> from <code>localhost</code> to <code>127.0.0.1</code></li>";
        echo "<li>Verify MySQL is running on your server</li>";
        echo "<li>Check with your hosting provider for the correct host value</li>";
    } else {
        echo "<li>Double-check all credentials match what's shown in cPanel</li>";
        echo "<li>Verify the database user has ALL PRIVILEGES</li>";
    }
    
    echo "</ul>";
}

echo "<hr>";
echo "<p style='color: #666; font-size: 12px;'>";
echo "<strong>Security Note:</strong> Delete this file after testing your connection!";
echo "</p>";
?>
