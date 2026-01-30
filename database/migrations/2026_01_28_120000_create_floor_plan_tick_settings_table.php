<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Booked tick appearance settings per floor plan (overrides global settings when set).
     */
    public function up(): void
    {
        Schema::create('floor_plan_tick_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('floor_plan_id')->unique();
            $table->boolean('show_tick')->default(true);
            $table->string('color', 20)->default('#28a745');
            $table->string('size', 20)->default('medium');
            $table->string('shape', 20)->default('round');
            $table->string('position', 20)->default('top-right');
            $table->string('animation', 20)->default('pulse');
            $table->string('bg_color', 20)->nullable();
            $table->string('border_width', 5)->default('0');
            $table->string('border_color', 20)->default('#ffffff');
            $table->string('font_size', 20)->default('medium');
            $table->string('size_mode', 20)->default('fixed');
            $table->string('relative_percent', 5)->default('12');
            $table->timestamps();

            $table->foreign('floor_plan_id')->references('id')->on('floor_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floor_plan_tick_settings');
    }
};
