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
        if (! Schema::hasTable('floor_plans')) {
            Schema::create('floor_plans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id')->nullable()->index('idx_event_id');
                $table->string('name', 255)->notNull();
                $table->text('description')->nullable();
                $table->string('project_name', 255)->nullable();
                $table->string('floor_image', 255)->nullable();
                $table->integer('canvas_width')->default(1200);
                $table->integer('canvas_height')->default(800);
                $table->boolean('is_active')->default(true)->index('idx_is_active');
                $table->boolean('is_default')->default(false)->index('idx_is_default');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });

            // Create default floor plan for existing booths
            $defaultFloorPlanId = DB::table('floor_plans')->insertGetId([
                'name' => 'Default Floor Plan',
                'project_name' => 'Main Project',
                'is_default' => true,
                'is_active' => true,
                'canvas_width' => 1200,
                'canvas_height' => 800,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign all existing booths to default floor plan
            if (Schema::hasTable('booth')) {
                DB::table('booth')->whereNull('floor_plan_id')->update([
                    'floor_plan_id' => $defaultFloorPlanId,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floor_plans');
    }
};
