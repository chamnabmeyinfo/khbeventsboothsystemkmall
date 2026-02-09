<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DatabaseImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:import 
                            {--file= : Import from specific file path}
                            {--force : Skip confirmation prompts}
                            {--backup : Create backup before import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import SQL file to database (sync from code)';

    /**
     * Default import file path
     */
    protected $defaultImportPath;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Test database connection
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful!');
            $this->newLine();

            // Set default import path
            $this->defaultImportPath = database_path('export_real_database/khbeventskmallxmas.sql');

            // Get import file path
            $importPath = $this->option('file') ?: $this->defaultImportPath;

            // Check if file exists
            if (! file_exists($importPath)) {
                $this->error("❌ SQL file not found: {$importPath}");
                $this->comment('💡 Use: php artisan db:export (to create the file)');

                return 1;
            }

            $fileSize = filesize($importPath);
            $this->info("📁 SQL File: <fg=cyan>{$importPath}</>");
            $this->info('📊 File Size: '.$this->formatBytes($fileSize));
            $this->newLine();

            // Get database info
            $database = DB::connection()->getDatabaseName();
            $this->warn("⚠️  WARNING: This will import SQL to database: <fg=yellow>{$database}</>");
            $this->warn('    All existing data in this database may be replaced!');
            $this->newLine();

            // Backup if requested
            if ($this->option('backup')) {
                $this->createBackup();
            }

            // Confirmation
            if (! $this->option('force')) {
                if (! $this->confirm('Are you sure you want to proceed?', false)) {
                    $this->warn('Import cancelled.');

                    return 0;
                }
            }

            $this->info('📥 Starting import...');
            $this->newLine();

            // Import using MySQL command if available
            $mysqlPath = $this->findMysqlPath();
            if ($mysqlPath) {
                return $this->importUsingMysql($mysqlPath, $importPath, $database);
            }

            // Fallback: Import using Laravel
            return $this->importUsingLaravel($importPath);
        } catch (\Exception $e) {
            $this->error('❌ Database import failed!');
            $this->error($e->getMessage());
            $this->newLine();
            $this->comment('💡 Alternative: Import directly via phpMyAdmin:');
            $this->comment("   File: {$importPath}");

            return 1;
        }
    }

    /**
     * Import using mysql command (preferred method)
     */
    protected function importUsingMysql($mysqlPath, $importPath, $database)
    {
        $this->info('📤 Using mysql command...');

        $config = Config::get('database.connections.mysql');
        $host = $config['host'];
        $port = $config['port'] ?? 3306;
        $username = $config['username'];
        $password = $config['password'];

        // Build command
        $command = sprintf(
            '"%s" --host=%s --port=%d --user=%s --password=%s --default-character-set=utf8mb4 %s < "%s"',
            $mysqlPath,
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($importPath)
        );

        // Execute
        $output = [];
        $returnVar = 0;
        exec($command.' 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('mysql import failed:');
            $this->error(implode("\n", $output));
            $this->newLine();
            $this->comment('💡 Alternative: Import directly via phpMyAdmin:');
            $this->comment('   1. Open phpMyAdmin: http://localhost:8000/phpmyadmin/');
            $this->comment("   2. Select database: {$database}");
            $this->comment("   3. Click 'Import' tab");
            $this->comment("   4. Choose file: {$importPath}");

            return 1;
        }

        $this->info('✅ Import completed successfully!');
        $this->newLine();
        $this->comment('💡 Database is now in sync with SQL file.');

        return 0;
    }

    /**
     * Import using Laravel (fallback method - slower)
     */
    protected function importUsingLaravel($importPath)
    {
        $this->warn('⚠️  mysql command not found. Using Laravel import (slower)...');
        $this->warn('    For better performance, use phpMyAdmin or install MySQL client.');
        $this->newLine();

        // Read SQL file
        $sql = file_get_contents($importPath);
        if (! $sql) {
            $this->error("Cannot read SQL file: {$importPath}");

            return 1;
        }

        // Split into individual queries
        $queries = $this->splitSqlQueries($sql);
        $totalQueries = count($queries);

        $this->info("Found {$totalQueries} SQL statements to execute...");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($totalQueries);
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($queries as $query) {
            $query = trim($query);

            // Skip empty queries and comments
            if (empty($query) || strpos($query, '--') === 0 || strpos($query, '/*') === 0) {
                $progressBar->advance();

                continue;
            }

            try {
                DB::unprepared($query);
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                // Skip certain errors that are expected (like table already exists during re-import)
                if (strpos($e->getMessage(), 'already exists') === false) {
                    $this->newLine();
                    $this->warn('Query warning: '.substr($query, 0, 100).'...');
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->newLine();

        if ($errorCount === 0) {
            $this->info('✅ Import completed successfully!');
            $this->info("Executed: {$successCount} queries");
        } else {
            $this->warn('⚠️  Import completed with warnings!');
            $this->info("Success: {$successCount} queries");
            $this->warn("Errors: {$errorCount} queries");
            $this->newLine();
            $this->comment('💡 Some errors may be expected (e.g., duplicate tables).');
            $this->comment('💡 For best results, use phpMyAdmin import.');
        }

        return 0;
    }

    /**
     * Split SQL file into individual queries
     */
    protected function splitSqlQueries($sql)
    {
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        // Split by semicolon (but keep strings intact)
        $queries = [];
        $currentQuery = '';
        $inString = false;
        $stringChar = '';

        for ($i = 0; $i < strlen($sql); $i++) {
            $char = $sql[$i];
            $prevChar = $i > 0 ? $sql[$i - 1] : '';

            if (! $inString && ($char === '"' || $char === "'")) {
                $inString = true;
                $stringChar = $char;
            } elseif ($inString && $char === $stringChar && $prevChar !== '\\') {
                $inString = false;
            }

            $currentQuery .= $char;

            if (! $inString && $char === ';') {
                $queries[] = $currentQuery;
                $currentQuery = '';
            }
        }

        if (! empty(trim($currentQuery))) {
            $queries[] = $currentQuery;
        }

        return array_filter($queries, function ($query) {
            return ! empty(trim($query));
        });
    }

    /**
     * Create backup before import
     */
    protected function createBackup()
    {
        $this->info('💾 Creating backup...');

        $backupPath = database_path('export_real_database/backup_'.date('Y-m-d_His').'.sql');
        $backupDir = dirname($backupPath);

        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Use export command to create backup
        $this->call('db:export', [
            '--file' => $backupPath,
            '--force' => true,
        ]);

        $this->info("✅ Backup created: <fg=cyan>{$backupPath}</>");
        $this->newLine();
    }

    /**
     * Find mysql executable path
     */
    protected function findMysqlPath()
    {
        $paths = [
            'mysql', // In PATH
            'C:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
            'C:\\Program Files\\MariaDB\\bin\\mysql.exe',
        ];

        foreach ($paths as $path) {
            if (is_executable($path) || (PHP_OS_FAMILY === 'Windows' && file_exists($path))) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }
}
