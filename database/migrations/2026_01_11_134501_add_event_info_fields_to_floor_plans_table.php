<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('floor_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('floor_plans', 'google_map_location')) {
                $table->text('google_map_location')->nullable()->after('description');
            }
            if (!Schema::hasColumn('floor_plans', 'feature_image')) {
                $table->string('feature_image', 255)->nullable()->after('floor_image');
            }
            if (!Schema::hasColumn('floor_plans', 'proposal')) {
                $table->text('proposal')->nullable()->after('feature_image');
            }
            if (!Schema::hasColumn('floor_plans', 'event_start_date')) {
                $table->date('event_start_date')->nullable()->after('proposal');
            }
            if (!Schema::hasColumn('floor_plans', 'event_end_date')) {
                $table->date('event_end_date')->nullable()->after('event_start_date');
            }
            if (!Schema::hasColumn('floor_plans', 'event_start_time')) {
                $table->time('event_start_time')->nullable()->after('event_end_date');
            }
            if (!Schema::hasColumn('floor_plans', 'event_end_time')) {
                $table->time('event_end_time')->nullable()->after('event_start_time');
            }
            if (!Schema::hasColumn('floor_plans', 'event_location')) {
                $table->string('event_location', 255)->nullable()->after('event_end_time');
            }
            if (!Schema::hasColumn('floor_plans', 'event_venue')) {
                $table->string('event_venue', 255)->nullable()->after('event_location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('floor_plans', function (Blueprint $table) {
            $table->dropColumn([
                'google_map_location',
                'feature_image',
                'proposal',
                'event_start_date',
                'event_end_date',
                'event_start_time',
                'event_end_time',
                'event_location',
                'event_venue',
            ]);
        });
    }
};
