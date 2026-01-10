<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration ensures:
     * 1. Bookings track which project/event and floor plan they belong to
     * 2. Canvas settings can be per-floor-plan (optional)
     * 3. All floor plan data is properly stored in database
     */
    public function up(): void
    {
        // PART 1: Add project/floor plan tracking to bookings
        if (Schema::hasTable('book')) {
            Schema::table('book', function (Blueprint $table) {
                // Add event_id (project) if it doesn't exist
                if (!Schema::hasColumn('book', 'event_id')) {
                    $table->unsignedBigInteger('event_id')->nullable()->after('id')->index('idx_event_id');
                }
                
                // Add floor_plan_id if it doesn't exist
                if (!Schema::hasColumn('book', 'floor_plan_id')) {
                    $table->unsignedBigInteger('floor_plan_id')->nullable()->after('event_id')->index('idx_floor_plan_id');
                }
            });
            
            // Backfill event_id and floor_plan_id from booths for existing bookings
            try {
                // Get bookings with booth IDs
                $bookings = DB::table('book')->whereNotNull('boothid')->get();
                
                foreach ($bookings as $booking) {
                    $boothIds = json_decode($booking->boothid, true);
                    if (!empty($boothIds) && is_array($boothIds)) {
                        // Get first booth to determine floor plan and event
                        $firstBoothId = $boothIds[0];
                        $booth = DB::table('booth')->where('id', $firstBoothId)->first();
                        
                        if ($booth && $booth->floor_plan_id) {
                            $floorPlan = DB::table('floor_plans')->where('id', $booth->floor_plan_id)->first();
                            
                            if ($floorPlan) {
                                DB::table('book')
                                    ->where('id', $booking->id)
                                    ->update([
                                        'event_id' => $floorPlan->event_id,
                                        'floor_plan_id' => $floorPlan->id
                                    ]);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Could not backfill booking project/floor plan data: ' . $e->getMessage());
            }
        }
        
        // PART 2: Make canvas_settings floor-plan-specific (optional)
        if (Schema::hasTable('canvas_settings')) {
            Schema::table('canvas_settings', function (Blueprint $table) {
                // Add floor_plan_id if it doesn't exist
                if (!Schema::hasColumn('canvas_settings', 'floor_plan_id')) {
                    $table->unsignedBigInteger('floor_plan_id')->nullable()->after('id')->index('idx_floor_plan_id');
                }
            });
            
            // Clean up invalid data in canvas_settings
            try {
                DB::table('canvas_settings')
                    ->where('floorplan_image', 'LIKE', '%asset%')
                    ->orWhere('floorplan_image', 'LIKE', '%{{%')
                    ->update(['floorplan_image' => null]);
            } catch (\Exception $e) {
                \Log::warning('Could not clean canvas_settings: ' . $e->getMessage());
            }
        }
        
        // PART 3: Ensure floor_plans table has all necessary fields
        if (Schema::hasTable('floor_plans')) {
            Schema::table('floor_plans', function (Blueprint $table) {
                // Verify all columns exist (they should, but ensure)
                if (!Schema::hasColumn('floor_plans', 'floor_image')) {
                    $table->string('floor_image', 255)->nullable()->after('project_name');
                }
                if (!Schema::hasColumn('floor_plans', 'canvas_width')) {
                    $table->integer('canvas_width')->default(1200)->after('floor_image');
                }
                if (!Schema::hasColumn('floor_plans', 'canvas_height')) {
                    $table->integer('canvas_height')->default(800)->after('canvas_width');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('book')) {
            Schema::table('book', function (Blueprint $table) {
                if (Schema::hasColumn('book', 'floor_plan_id')) {
                    $table->dropIndex('idx_floor_plan_id');
                    $table->dropColumn('floor_plan_id');
                }
                if (Schema::hasColumn('book', 'event_id')) {
                    $table->dropIndex('idx_event_id');
                    $table->dropColumn('event_id');
                }
            });
        }
        
        if (Schema::hasTable('canvas_settings')) {
            Schema::table('canvas_settings', function (Blueprint $table) {
                if (Schema::hasColumn('canvas_settings', 'floor_plan_id')) {
                    $table->dropIndex('idx_floor_plan_id');
                    $table->dropColumn('floor_plan_id');
                }
            });
        }
    }
};
