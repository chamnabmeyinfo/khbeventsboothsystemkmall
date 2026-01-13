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
        if (!Schema::hasTable('zone_settings')) {
            Schema::create('zone_settings', function (Blueprint $table) {
            $table->id();
            $table->string('zone_name')->unique()->index();
            $table->integer('width')->default(80);
            $table->integer('height')->default(50);
            $table->integer('rotation')->default(0);
            $table->integer('z_index')->default(10);
            $table->float('border_radius')->default(6);
            $table->float('border_width')->default(2);
            $table->float('opacity')->default(1.0);
            $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_settings');
    }
};

