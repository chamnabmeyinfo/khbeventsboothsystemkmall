<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is deprecated - booth numbers are now unique per floor plan
        // The unique constraint was moved to a composite key (booth_number + floor_plan_id)
        // See: 2026_01_10_210930_update_booth_number_unique_constraint_to_floor_plan_specific.php
        
        // Only run if the old unique index doesn't exist and floor_plan_id column doesn't exist
        // (meaning this is a very old database)
        if (!Schema::hasColumn('booth', 'floor_plan_id')) {
            Schema::table('booth', function (Blueprint $table) {
                // Check if unique index already exists
                $connection = Schema::getConnection();
                $database = $connection->getDatabaseName();
                $indexExists = $connection->select(
                    "SELECT COUNT(*) as count FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                    [$database, 'booth', 'booth_booth_number_unique']
                );
                
                if (empty($indexExists) || $indexExists[0]->count == 0) {
                    // Only add if it doesn't exist - but don't add "-dup" suffixes
                    // Booth numbers can be the same across different floor plans
                    $table->unique('booth_number', 'booth_booth_number_unique');
                }
            });
        }
        // If floor_plan_id exists, skip this migration - use floor-plan-specific uniqueness instead
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booth', function (Blueprint $table) {
            $table->dropUnique('booth_booth_number_unique');
        });
    }
};
