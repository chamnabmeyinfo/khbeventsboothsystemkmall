<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:sync 
                            {--file= : SQL file path to compare}
                            {--diff : Show detailed differences}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compare SQL file with current database structure (check if in sync)';

    /**
     * Default SQL file path
     */
    protected $defaultSqlPath;

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

            // Set default SQL file path
            $this->defaultSqlPath = database_path('export_real_database/khbeventskmallxmas.sql');

            // Get SQL file path
            $sqlPath = $this->option('file') ?: $this->defaultSqlPath;

            // Check if file exists
            if (! file_exists($sqlPath)) {
                $this->error("❌ SQL file not found: {$sqlPath}");
                $this->comment('💡 Use: php artisan db:export (to create the file)');

                return 1;
            }

            $this->info("📁 SQL File: <fg=cyan>{$sqlPath}</>");
            $this->info('📊 Database: <fg=cyan>'.DB::connection()->getDatabaseName().'</>');
            $this->newLine();

            // Extract tables from SQL file
            $this->info('📖 Reading SQL file...');
            $sqlTables = $this->extractTablesFromSql($sqlPath);
            $this->info('Found <fg=cyan>'.count($sqlTables).'</> tables in SQL file');
            $this->newLine();

            // Get tables from current database
            $this->info('🔍 Checking current database...');
            $dbTables = $this->getDatabaseTables();
            $this->info('Found <fg=cyan>'.count($dbTables).'</> tables in database');
            $this->newLine();

            // Compare
            $this->info('⚖️  Comparing...');
            $this->newLine();

            return $this->compareTables($sqlTables, $dbTables);
        } catch (\Exception $e) {
            $this->error('❌ Sync check failed!');
            $this->error($e->getMessage());

            return 1;
        }
    }

    /**
     * Extract table names from SQL file
     */
    protected function extractTablesFromSql($sqlPath)
    {
        $sql = file_get_contents($sqlPath);
        $tables = [];

        // Match CREATE TABLE statements
        preg_match_all('/CREATE TABLE `([^`]+)`/i', $sql, $matches);
        if (! empty($matches[1])) {
            $tables = array_unique($matches[1]);
        }

        return $tables;
    }

    /**
     * Get all tables from current database
     */
    protected function getDatabaseTables()
    {
        $tables = DB::select('SHOW TABLES');
        $tableName = 'Tables_in_'.DB::connection()->getDatabaseName();

        return array_map(function ($table) use ($tableName) {
            return $table->$tableName;
        }, $tables);
    }

    /**
     * Compare tables from SQL file and database
     */
    protected function compareTables($sqlTables, $dbTables)
    {
        $sqlTableList = array_map('strtolower', $sqlTables);
        $dbTableList = array_map('strtolower', $dbTables);

        $onlyInSql = array_diff($sqlTableList, $dbTableList);
        $onlyInDb = array_diff($dbTableList, $sqlTableList);
        $inBoth = array_intersect($sqlTableList, $dbTableList);

        $isSync = empty($onlyInSql) && empty($onlyInDb);

        // Show results
        if ($isSync) {
            $this->info('✅ <fg=green>Database is in sync with SQL file!</fg=green>');
            $this->info('   All <fg=cyan>'.count($inBoth).'</> tables match.');
            $this->newLine();

            return 0;
        }

        // Show differences
        $this->warn('⚠️  <fg=yellow>Database is NOT in sync with SQL file</fg=yellow>');
        $this->newLine();

        if (! empty($onlyInSql)) {
            $this->error('📋 Tables in SQL file but NOT in database ('.count($onlyInSql).'):');
            foreach ($onlyInSql as $table) {
                $this->line("   ❌ <fg=red>{$table}</>");
            }
            $this->newLine();
        }

        if (! empty($onlyInDb)) {
            $this->warn('📋 Tables in database but NOT in SQL file ('.count($onlyInDb).'):');
            foreach ($onlyInDb as $table) {
                $this->line("   ⚠️  <fg=yellow>{$table}</>");
            }
            $this->newLine();
        }

        if (! empty($inBoth)) {
            $this->info('✅ Tables in both ('.count($inBoth).'):');
            foreach ($inBoth as $table) {
                $this->line("   ✅ <fg=green>{$table}</>");
            }
            $this->newLine();
        }

        // Show detailed diff if requested
        if ($this->option('diff') && ! empty($inBoth)) {
            $this->compareTableStructures($inBoth);
        }

        // Show recommendations
        $this->comment('💡 Recommendations:');
        if (! empty($onlyInSql)) {
            $this->comment('   1. Import SQL file to sync: <fg=cyan>php artisan db:import</>');
        }
        if (! empty($onlyInDb)) {
            $this->comment('   2. Export database to update SQL file: <fg=cyan>php artisan db:export</>');
        }
        $this->newLine();

        return 1;
    }

    /**
     * Compare table structures (detailed diff)
     */
    protected function compareTableStructures($tables)
    {
        $this->newLine();
        $this->info('📊 Detailed Structure Comparison:');
        $this->line('─────────────────────────────────────────────────');
        $this->newLine();

        foreach ($tables as $table) {
            $this->line("Table: <fg=cyan>{$table}</>");

            // Get database columns
            if (Schema::hasTable($table)) {
                $dbColumns = Schema::getColumnListing($table);
                $this->line('  Database columns: '.count($dbColumns));

                if ($this->option('diff')) {
                    foreach ($dbColumns as $column) {
                        $this->line("    - {$column}");
                    }
                }
            }

            $this->newLine();
        }
    }
}
