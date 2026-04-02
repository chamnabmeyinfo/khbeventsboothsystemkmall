<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Default booked-tick (checkmark) color changed from green (#28a745) to pink (#ec4899).
 * Updates stored global and per-floor-plan tick colors that still use the old default.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            DB::table('settings')
                ->where('key', 'booth_booked_tick_color')
                ->where('value', '#28a745')
                ->update(['value' => '#ec4899']);
        }
        if (Schema::hasTable('floor_plan_tick_settings')) {
            DB::table('floor_plan_tick_settings')
                ->where('color', '#28a745')
                ->update(['color' => '#ec4899']);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            DB::table('settings')
                ->where('key', 'booth_booked_tick_color')
                ->where('value', '#ec4899')
                ->update(['value' => '#28a745']);
        }
        if (Schema::hasTable('floor_plan_tick_settings')) {
            DB::table('floor_plan_tick_settings')
                ->where('color', '#ec4899')
                ->update(['color' => '#28a745']);
        }
    }
};
