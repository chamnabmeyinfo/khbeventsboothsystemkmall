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
        Schema::create('booth_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('booth_id'); // Match booth table structure
            $table->unsignedInteger('floor_plan_id'); // Match floor_plans table structure
            $table->string('image_path');
            $table->string('image_type')->default('photo'); // photo, layout, setup, teardown, facility
            $table->text('caption')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            
            // Indexes for performance (skip foreign keys due to mixed int types)
            $table->index('booth_id');
            $table->index('floor_plan_id');
            $table->index(['booth_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booth_images');
    }
};
