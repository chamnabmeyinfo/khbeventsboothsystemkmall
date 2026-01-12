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
            // Google Map location (can store embed URL, iframe code, or coordinates)
            $table->text('google_map_location')->nullable()->after('description');
            
            // Feature image for the event/floor plan (different from floor_image)
            $table->string('feature_image', 255)->nullable()->after('floor_image');
            
            // Proposal text/content
            $table->text('proposal')->nullable()->after('feature_image');
            
            // Additional event information fields
            $table->date('event_start_date')->nullable()->after('proposal');
            $table->date('event_end_date')->nullable()->after('event_start_date');
            $table->time('event_start_time')->nullable()->after('event_end_date');
            $table->time('event_end_time')->nullable()->after('event_start_time');
            $table->string('event_location', 255)->nullable()->after('event_end_time');
            $table->string('event_venue', 255)->nullable()->after('event_location');
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
