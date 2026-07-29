<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Lets an admin choose, per floor plan, which of the existing event-info
     * fields (description, dates, times, location, venue, map) are shown to
     * the public on the front-end floor plan view. Stored as a JSON array of
     * field keys rather than one boolean column per field, since the field
     * set is a fixed enum defined in code (FloorPlan::PUBLIC_VIEW_INFO_FIELDS)
     * and this avoids a schema change every time that set changes.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('floor_plans', 'public_view_info_fields')) {
            Schema::table('floor_plans', function (Blueprint $table) {
                $table->json('public_view_info_fields')->nullable()->after('event_venue');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('floor_plans', 'public_view_info_fields')) {
            Schema::table('floor_plans', function (Blueprint $table) {
                $table->dropColumn('public_view_info_fields');
            });
        }
    }
};
