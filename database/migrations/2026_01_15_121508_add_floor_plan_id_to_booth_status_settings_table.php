<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('booth_status_settings')) {
            Schema::table('booth_status_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('booth_status_settings', 'floor_plan_id')) {
                    $table->unsignedBigInteger('floor_plan_id')->nullable()->after('id');
                    $table->foreign('floor_plan_id')->references('id')->on('floor_plans')->onDelete('cascade');
                    $table->index('floor_plan_id');

                    // Drop existing unique constraint on status_code to allow same code for different floor plans
                    // We'll enforce uniqueness (status_code + floor_plan_id) in application logic
                    // Try to drop the unique constraint if it exists
                    try {
                        DB::statement('ALTER TABLE booth_status_settings DROP INDEX booth_status_settings_status_code_unique');
                    } catch (\Exception $e) {
                        // Constraint might not exist, try alternative name
                        try {
                            DB::statement('ALTER TABLE booth_status_settings DROP INDEX IF EXISTS booth_status_settings_status_code_unique');
                        } catch (\Exception $e2) {
                            // Ignore - constraint might not exist or already dropped
                        }
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booth_status_settings')) {
            Schema::table('booth_status_settings', function (Blueprint $table) {
                if (Schema::hasColumn('booth_status_settings', 'floor_plan_id')) {
                    $table->dropForeign(['floor_plan_id']);
                    $table->dropIndex(['floor_plan_id']);
                    $table->dropColumn('floor_plan_id');

                    // Re-add unique constraint on status_code if needed
                    try {
                        $table->unique('status_code');
                    } catch (\Exception $e) {
                        // Ignore if already exists
                    }
                }
            });
        }
    }
};
