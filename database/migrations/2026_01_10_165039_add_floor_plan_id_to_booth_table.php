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
        // Check if floor_plans table exists and get default floor plan
        $defaultFloorPlanId = null;
        if (Schema::hasTable('floor_plans')) {
            $defaultFloorPlan = DB::table('floor_plans')->where('is_default', true)->first();
            $defaultFloorPlanId = $defaultFloorPlan ? $defaultFloorPlan->id : null;
        }

        Schema::table('booth', function (Blueprint $table) use ($defaultFloorPlanId) {
            // Add floor_plan_id column after id
            if (!Schema::hasColumn('booth', 'floor_plan_id')) {
                $table->unsignedBigInteger('floor_plan_id')->nullable()->after('id');
                $table->index('floor_plan_id', 'idx_floor_plan_id');
            }
        });

        // Assign existing booths to default floor plan if exists
        if ($defaultFloorPlanId && Schema::hasColumn('booth', 'floor_plan_id')) {
            DB::table('booth')->whereNull('floor_plan_id')->update([
                'floor_plan_id' => $defaultFloorPlanId
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booth', function (Blueprint $table) {
            if (Schema::hasColumn('booth', 'floor_plan_id')) {
                $table->dropIndex('idx_floor_plan_id');
                $table->dropColumn('floor_plan_id');
            }
        });
    }
};
