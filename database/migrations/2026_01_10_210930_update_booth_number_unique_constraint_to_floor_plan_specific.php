<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        $result = $connection->select(
            'SELECT COUNT(*) as count FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $indexName]
        );

        return ! empty($result) && $result[0]->count > 0;
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if unique constraint exists before dropping
        try {
            Schema::table('booth', function (Blueprint $table) {
                // Drop the old global unique constraint on booth_number
                $table->dropUnique('booth_booth_number_unique');
            });
        } catch (\Exception $e) {
            // Constraint might not exist or have different name - try alternative names
            try {
                Schema::table('booth', function (Blueprint $table) {
                    $table->dropUnique(['booth_number']);
                });
            } catch (\Exception $e2) {
                // Constraint might not exist - continue
                \Log::warning('Could not drop booth_number unique constraint: '.$e2->getMessage());
            }
        }

        // Add composite unique constraint: booth_number + floor_plan_id (floor-plan-specific uniqueness)
        // Same booth number can exist in different floor plans
        Schema::table('booth', function (Blueprint $table) {
            // Make sure floor_plan_id column exists first (should already exist from previous migration)
            if (! Schema::hasColumn('booth', 'floor_plan_id')) {
                $table->unsignedBigInteger('floor_plan_id')->nullable()->after('id')->index('idx_floor_plan_id');
            }

            // Add composite unique index only if not present
            if (! $this->hasIndex('booth', 'booth_number_floor_plan_unique')) {
                $table->unique(['booth_number', 'floor_plan_id'], 'booth_number_floor_plan_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booth', function (Blueprint $table) {
            // Drop composite unique constraint
            try {
                $table->dropUnique('booth_number_floor_plan_unique');
            } catch (\Exception $e) {
                \Log::warning('Could not drop composite unique constraint: '.$e->getMessage());
            }

            // Restore global unique constraint on booth_number
            try {
                $table->unique('booth_number', 'booth_booth_number_unique');
            } catch (\Exception $e) {
                \Log::warning('Could not restore global unique constraint: '.$e->getMessage());
            }
        });
    }
};
