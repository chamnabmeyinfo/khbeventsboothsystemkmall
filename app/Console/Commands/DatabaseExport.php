<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export 
                            {--file= : Export to specific file path}
                            {--no-data : Export structure only (no data)}
                            {--tables= : Export specific tables only (comma-separated)}
                            {--force : Overwrite existing file without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export current database to SQL file (sync with code)';

    /**
     * Default export file path
     */
    protected $defaultExportPath;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Test database connection
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful!');

            // Set default export path
            $this->defaultExportPath = database_path('export_real_database/khbeventskmallxmas.sql');

            // Get export file path
            $exportPath = $this->option('file') ?: $this->defaultExportPath;

            // Check if file exists
            if (file_exists($exportPath) && ! $this->option('force')) {
                if (! $this->confirm("File '{$exportPath}' already exists. Overwrite?", false)) {
                    $this->warn('Export cancelled.');

                    return 0;
                }
            }

            // Create directory if it doesn't exist
            $exportDir = dirname($exportPath);
            if (! is_dir($exportDir)) {
                mkdir($exportDir, 0755, true);
                $this->info("Created directory: {$exportDir}");
            }

            $this->info("Exporting database to: <fg=cyan>{$exportPath}</>");
            $this->newLine();

            // Get database connection info
            $config = Config::get('database.connections.mysql');
            $host = $config['host'];
            $port = $config['port'] ?? 3306;
            $database = $config['database'];
            $username = $config['username'];
            $password = $config['password'];

            // Build mysqldump command
            $mysqldumpPath = $this->findMysqldumpPath();
            if (! $mysqldumpPath) {
                return $this->exportUsingLaravel($exportPath);
            }

            return $this->exportUsingMysqldump($mysqldumpPath, $host, $port, $database, $username, $password, $exportPath);
        } catch (\Exception $e) {
            $this->error('❌ Database export failed!');
            $this->error($e->getMessage());

            return 1;
        }
    }

    /**
     * Export using mysqldump (preferred method)
     */
    protected function exportUsingMysqldump($mysqldumpPath, $host, $port, $database, $username, $password, $exportPath)
    {
        $this->info('📤 Using mysqldump...');

        // Build command
        $command = sprintf(
            '"%s" --host=%s --port=%d --user=%s --password=%s --single-transaction --routines --triggers --no-create-info=false --default-character-set=utf8mb4 %s',
            $mysqldumpPath,
            escapeshellarg($host),
            $port,
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database)
        );

        // Add options
        if ($this->option('no-data')) {
            $command .= ' --no-data';
        }

        if ($tables = $this->option('tables')) {
            $tableList = explode(',', $tables);
            $command .= ' '.implode(' ', array_map('escapeshellarg', $tableList));
        }

        // Execute and save to file
        $output = [];
        $returnVar = 0;
        exec($command.' > "'.$exportPath.'" 2>&1', $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('mysqldump failed:');
            $this->error(implode("\n", $output));

            return 1;
        }

        $fileSize = filesize($exportPath);
        $this->info('✅ Export completed successfully!');
        $this->info('File size: '.$this->formatBytes($fileSize));
        $this->info("Location: <fg=cyan>{$exportPath}</>");
        $this->newLine();
        $this->comment('💡 You can now import this file directly to phpMyAdmin.');

        return 0;
    }

    /**
     * Export using Laravel (fallback method)
     */
    protected function exportUsingLaravel($exportPath)
    {
        $this->warn('⚠️  mysqldump not found. Using Laravel export (slower but works)...');
        $this->newLine();

        $handle = fopen($exportPath, 'w');
        if (! $handle) {
            $this->error("Cannot create file: {$exportPath}");

            return 1;
        }

        // Write header
        $this->writeSqlHeader($handle);

        // Get all tables
        $tables = $this->getTables();
        $progressBar = $this->output->createProgressBar(count($tables));
        $progressBar->start();

        foreach ($tables as $table) {
            // Export table structure
            $this->exportTableStructure($handle, $table);

            // Export table data (unless --no-data)
            if (! $this->option('no-data')) {
                $this->exportTableData($handle, $table);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->newLine();

        // Write footer
        $this->writeSqlFooter($handle);
        fclose($handle);

        $fileSize = filesize($exportPath);
        $this->info('✅ Export completed successfully!');
        $this->info('File size: '.$this->formatBytes($fileSize));
        $this->info("Location: <fg=cyan>{$exportPath}</>");
        $this->newLine();
        $this->comment('💡 You can now import this file directly to phpMyAdmin.');

        return 0;
    }

    /**
     * Write SQL file header
     */
    protected function writeSqlHeader($handle)
    {
        $timestamp = date('M d, Y \a\t h:i A');
        $serverVersion = DB::select('SELECT VERSION() as version')[0]->version ?? 'Unknown';

        fwrite($handle, "-- phpMyAdmin SQL Dump\n");
        fwrite($handle, "-- Generated by Laravel db:export command\n");
        fwrite($handle, "-- Generation Time: {$timestamp}\n");
        fwrite($handle, "-- Server version: {$serverVersion}\n");
        fwrite($handle, '-- PHP Version: '.PHP_VERSION."\n");
        fwrite($handle, "\n");
        fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($handle, "START TRANSACTION;\n");
        fwrite($handle, "SET time_zone = \"+00:00\";\n");
        fwrite($handle, "\n");
        fwrite($handle, "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n");
        fwrite($handle, "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n");
        fwrite($handle, "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n");
        fwrite($handle, "/*!40101 SET NAMES utf8mb4 */;\n");
        fwrite($handle, "\n");
        fwrite($handle, "--\n");
        fwrite($handle, '-- Database: `'.DB::connection()->getDatabaseName()."`\n");
        fwrite($handle, "--\n");
        fwrite($handle, "\n");
    }

    /**
     * Write SQL file footer
     */
    protected function writeSqlFooter($handle)
    {
        fwrite($handle, "\n");
        fwrite($handle, "COMMIT;\n");
        fwrite($handle, "\n");
        fwrite($handle, "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n");
        fwrite($handle, "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n");
        fwrite($handle, "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n");
    }

    /**
     * Export table structure
     */
    protected function exportTableStructure($handle, $table)
    {
        fwrite($handle, "-- --------------------------------------------------------\n");
        fwrite($handle, "\n");
        fwrite($handle, "--\n");
        fwrite($handle, "-- Table structure for table `{$table}`\n");
        fwrite($handle, "--\n");
        fwrite($handle, "\n");

        $createTable = DB::select("SHOW CREATE TABLE `{$table}`")[0];
        $createTableSql = $createTable->{'Create Table'} ?? null;

        if ($createTableSql) {
            fwrite($handle, $createTableSql.";\n");
            fwrite($handle, "\n");
        }
    }

    /**
     * Export table data
     */
    protected function exportTableData($handle, $table)
    {
        $rowCount = DB::table($table)->count();
        if ($rowCount === 0) {
            return;
        }

        fwrite($handle, "--\n");
        fwrite($handle, "-- Dumping data for table `{$table}`\n");
        fwrite($handle, "--\n");
        fwrite($handle, "\n");

        // Get columns
        $columns = Schema::getColumnListing($table);
        $columnList = implode('`, `', $columns);

        // Get data in chunks to avoid memory issues
        DB::table($table)->orderBy('id')->chunk(100, function ($rows) use ($handle, $table, $columns) {
            $values = [];
            foreach ($rows as $row) {
                $rowValues = [];
                foreach ($columns as $column) {
                    $value = $row->$column ?? null;
                    if ($value === null) {
                        $rowValues[] = 'NULL';
                    } elseif (is_numeric($value)) {
                        $rowValues[] = $value;
                    } else {
                        $rowValues[] = "'".addslashes($value)."'";
                    }
                }
                $values[] = '('.implode(', ', $rowValues).')';
            }

            if (! empty($values)) {
                $columnList = implode('`, `', $columns);
                fwrite($handle, "INSERT INTO `{$table}` (`{$columnList}`) VALUES\n");
                fwrite($handle, implode(",\n", $values).";\n");
                fwrite($handle, "\n");
            }
        });
    }

    /**
     * Get all tables
     */
    protected function getTables()
    {
        if ($tables = $this->option('tables')) {
            return array_map('trim', explode(',', $tables));
        }

        $tables = DB::select('SHOW TABLES');
        $tableName = 'Tables_in_'.DB::connection()->getDatabaseName();

        return array_map(function ($table) use ($tableName) {
            return $table->$tableName;
        }, $tables);
    }

    /**
     * Find mysqldump executable path
     */
    protected function findMysqldumpPath()
    {
        // Common paths
        $paths = [
            'mysqldump', // In PATH
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MariaDB\\bin\\mysqldump.exe',
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
