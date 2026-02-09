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
        if (! Schema::hasTable('canvas_settings')) {
            Schema::create('canvas_settings', function (Blueprint $table) {
                $table->id();
                $table->integer('canvas_width')->default(1200);
                $table->integer('canvas_height')->default(800);
                $table->integer('canvas_resolution')->default(300);
                $table->integer('grid_size')->default(10);
                $table->decimal('zoom_level', 5, 2)->default(1.00);
                $table->decimal('pan_x', 10, 2)->default(0);
                $table->decimal('pan_y', 10, 2)->default(0);
                $table->string('floorplan_image')->nullable();
                $table->boolean('grid_enabled')->default(true);
                $table->boolean('snap_to_grid')->default(false);
                $table->timestamps();
            });

            // Insert default canvas settings
            DB::table('canvas_settings')->insert([
                'canvas_width' => 1200,
                'canvas_height' => 800,
                'canvas_resolution' => 300,
                'grid_size' => 10,
                'zoom_level' => 1.00,
                'pan_x' => 0,
                'pan_y' => 0,
                'grid_enabled' => true,
                'snap_to_grid' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canvas_settings');
    }
};
