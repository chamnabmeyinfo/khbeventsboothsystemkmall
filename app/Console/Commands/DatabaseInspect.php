<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseInspect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:inspect {--table= : Inspect specific table} {--tables : List all tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect database structure and data - Useful for checking database state';

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

            $database = DB::connection()->getDatabaseName();
            $this->info("Database: <fg=cyan>{$database}</>");
            $this->newLine();

            // List all tables
            if ($this->option('tables')) {
                return $this->listTables();
            }

            // Inspect specific table
            if ($table = $this->option('table')) {
                return $this->inspectTable($table);
            }

            // Default: Show overview
            return $this->showOverview();
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed!');
            $this->error($e->getMessage());
            $this->newLine();
            $this->comment('Please check your .env file database credentials.');
            return 1;
        }
    }

    /**
     * Show database overview
     */
    protected function showOverview()
    {
        $this->info('📊 Database Overview');
        $this->line('─────────────────────────────────────────────────');
        $this->newLine();

        // Get all tables
        $tables = DB::select('SHOW TABLES');
        $tableName = 'Tables_in_' . DB::connection()->getDatabaseName();
        
        $this->info('📋 Tables (' . count($tables) . '):');
        foreach ($tables as $table) {
            $tableNameValue = $table->$tableName;
            $rowCount = DB::table($tableNameValue)->count();
            $this->line("  • <fg=cyan>{$tableNameValue}</> ({$rowCount} rows)");
        }
        $this->newLine();

        // Check key tables
        $keyTables = ['floor_plans', 'booth', 'book', 'zone_settings', 'users', 'clients'];
        $this->info('🔍 Key Tables Status:');
        foreach ($keyTables as $table) {
            if (Schema::hasTable($table)) {
                $rowCount = DB::table($table)->count();
                $columns = Schema::getColumnListing($table);
                $this->line("  ✅ <fg=green>{$table}</> - {$rowCount} rows, " . count($columns) . " columns");
                
                // Check for important columns
                if ($table === 'floor_plans') {
                    $hasEventId = Schema::hasColumn($table, 'event_id');
                    $hasFloorImage = Schema::hasColumn($table, 'floor_image');
                    $this->line("     └─ event_id: " . ($hasEventId ? '✅' : '❌') . ", floor_image: " . ($hasFloorImage ? '✅' : '❌'));
                }
                
                if ($table === 'booth') {
                    $hasFloorPlanId = Schema::hasColumn($table, 'floor_plan_id');
                    $this->line("     └─ floor_plan_id: " . ($hasFloorPlanId ? '✅' : '❌'));
                }
                
                if ($table === 'book') {
                    $hasEventId = Schema::hasColumn($table, 'event_id');
                    $hasFloorPlanId = Schema::hasColumn($table, 'floor_plan_id');
                    $nullCount = DB::table($table)->whereNull('event_id')->orWhereNull('floor_plan_id')->count();
                    $this->line("     └─ event_id: " . ($hasEventId ? '✅' : '❌') . ", floor_plan_id: " . ($hasFloorPlanId ? '✅' : '❌'));
                    if ($hasEventId && $hasFloorPlanId && $nullCount > 0) {
                        $this->warn("     ⚠️  {$nullCount} bookings need backfilling (event_id or floor_plan_id is NULL)");
                    }
                }
                
                if ($table === 'zone_settings') {
                    $hasFloorPlanId = Schema::hasColumn($table, 'floor_plan_id');
                    $this->line("     └─ floor_plan_id: " . ($hasFloorPlanId ? '✅' : '❌'));
                }
            } else {
                $this->line("  ❌ <fg=red>{$table}</> - Table does not exist");
            }
        }
        $this->newLine();

        $this->comment('💡 Usage:');
        $this->comment('  php artisan db:inspect --tables              List all tables');
        $this->comment('  php artisan db:inspect --table=floor_plans   Inspect specific table');
        
        return 0;
    }

    /**
     * List all tables
     */
    protected function listTables()
    {
        $this->info('📋 All Tables:');
        $this->line('─────────────────────────────────────────────────');
        $this->newLine();

        $tables = DB::select('SHOW TABLES');
        $tableName = 'Tables_in_' . DB::connection()->getDatabaseName();
        
        $tableData = [];
        foreach ($tables as $table) {
            $tableNameValue = $table->$tableName;
            $rowCount = DB::table($tableNameValue)->count();
            $columns = Schema::getColumnListing($tableNameValue);
            $tableData[] = [
                'Table' => $tableNameValue,
                'Rows' => $rowCount,
                'Columns' => count($columns)
            ];
        }

        $this->table(['Table', 'Rows', 'Columns'], $tableData);
        return 0;
    }

    /**
     * Inspect specific table
     */
    protected function inspectTable($table)
    {
        if (!Schema::hasTable($table)) {
            $this->error("❌ Table '{$table}' does not exist!");
            return 1;
        }

        $this->info("📊 Inspecting table: <fg=cyan>{$table}</>");
        $this->line('─────────────────────────────────────────────────');
        $this->newLine();

        // Get columns
        $columns = Schema::getColumnListing($table);
        $columnDetails = DB::select("DESCRIBE `{$table}`");

        $this->info('📋 Columns (' . count($columns) . '):');
        $this->newLine();
        
        $columnData = [];
        foreach ($columnDetails as $col) {
            $columnData[] = [
                'Column' => $col->Field,
                'Type' => $col->Type,
                'Null' => $col->Null,
                'Key' => $col->Key ?: '-',
                'Default' => $col->Default !== null ? $col->Default : 'NULL',
                'Extra' => $col->Extra ?: '-'
            ];
        }
        $this->table(['Column', 'Type', 'Null', 'Key', 'Default', 'Extra'], $columnData);
        $this->newLine();

        // Get indexes
        $indexes = DB::select("SHOW INDEXES FROM `{$table}`");
        if (count($indexes) > 0) {
            $this->info('🔑 Indexes:');
            $indexData = [];
            $uniqueIndexes = [];
            foreach ($indexes as $index) {
                if (!in_array($index->Key_name, $uniqueIndexes)) {
                    $indexData[] = [
                        'Index' => $index->Key_name,
                        'Columns' => $index->Column_name,
                        'Unique' => $index->Non_unique == 0 ? 'Yes' : 'No',
                        'Type' => $index->Index_type
                    ];
                    $uniqueIndexes[] = $index->Key_name;
                }
            }
            $this->table(['Index', 'Columns', 'Unique', 'Type'], $indexData);
            $this->newLine();
        }

        // Get row count
        $rowCount = DB::table($table)->count();
        $this->info("📊 Row Count: <fg=cyan>{$rowCount}</>");
        $this->newLine();

        // Show sample data (first 5 rows)
        if ($rowCount > 0) {
            $this->info('📝 Sample Data (first 5 rows):');
            $sampleData = DB::table($table)->limit(5)->get();
            
            if ($sampleData->count() > 0) {
                $headers = array_keys((array)$sampleData->first());
                $rows = [];
                foreach ($sampleData as $row) {
                    $rowArray = [];
                    foreach ($headers as $header) {
                        $value = $row->$header;
                        if (is_string($value) && strlen($value) > 50) {
                            $value = substr($value, 0, 47) . '...';
                        }
                        $rowArray[] = $value ?? 'NULL';
                    }
                    $rows[] = $rowArray;
                }
                $this->table($headers, $rows);
            }
        }

        return 0;
    }
}
