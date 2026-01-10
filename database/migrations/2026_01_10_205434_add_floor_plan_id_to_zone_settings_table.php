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
        // Check if column already exists
        if (Schema::hasColumn('zone_settings', 'floor_plan_id')) {
            return; // Already migrated
        }
        
        Schema::table('zone_settings', function (Blueprint $table) {
            // Remove unique constraint on zone_name (will recreate as composite)
            try {
                $table->dropUnique(['zone_name']);
            } catch (\Exception $e) {
                // Constraint might not exist or have different name - continue
            }
            
            // Add floor_plan_id column
            $table->unsignedBigInteger('floor_plan_id')->nullable()->after('id')->index('idx_floor_plan_id');
            
            // Add composite unique index: zone_name + floor_plan_id (same zone name can exist in different floor plans)
            $table->unique(['zone_name', 'floor_plan_id'], 'zone_name_floor_plan_unique');
        });
        
        // Add foreign key constraint if floor_plans table exists
        try {
            $hasFloorPlansTable = DB::select("SHOW TABLES LIKE 'floor_plans'");
            if (!empty($hasFloorPlansTable)) {
                Schema::table('zone_settings', function (Blueprint $table) {
                    $table->foreign('floor_plan_id', 'fk_zone_settings_floor_plan')
                        ->references('id')
                        ->on('floor_plans')
                        ->onDelete('cascade');
                });
                
                // Update existing zone settings to assign to default floor plan
                $defaultFloorPlan = DB::table('floor_plans')->where('is_default', true)->first();
                if ($defaultFloorPlan) {
                    DB::table('zone_settings')
                        ->whereNull('floor_plan_id')
                        ->update(['floor_plan_id' => $defaultFloorPlan->id]);
                }
            }
        } catch (\Exception $e) {
            // Foreign key or assignment failed - continue without it (can be added later)
            \Log::warning('Could not create foreign key or assign default floor plan: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zone_settings', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign('fk_zone_settings_floor_plan');
            
            // Drop composite unique index
            $table->dropUnique('zone_name_floor_plan_unique');
            
            // Drop floor_plan_id column
            $table->dropColumn('floor_plan_id');
            
            // Restore unique constraint on zone_name
            $table->unique('zone_name');
        });
    }
};
