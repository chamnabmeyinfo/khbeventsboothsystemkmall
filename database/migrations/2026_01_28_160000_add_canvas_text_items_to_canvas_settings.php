<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores canvas text elements (from the designer) per floor plan for display on public view.
     */
    public function up(): void
    {
        if (Schema::hasTable('canvas_settings') && ! Schema::hasColumn('canvas_settings', 'canvas_text_items')) {
            Schema::table('canvas_settings', function (Blueprint $table) {
                $table->json('canvas_text_items')->nullable()->after('snap_to_grid');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('canvas_settings') && Schema::hasColumn('canvas_settings', 'canvas_text_items')) {
            Schema::table('canvas_settings', function (Blueprint $table) {
                $table->dropColumn('canvas_text_items');
            });
        }
    }
};
