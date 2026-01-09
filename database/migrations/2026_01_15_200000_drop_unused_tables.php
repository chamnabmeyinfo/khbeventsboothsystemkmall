<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration drops unused tables to clean up the database
     * and focus on essential functionality.
     */
    public function up(): void
    {
        // Drop 'web' table if it exists (unused model)
        if (Schema::hasTable('web')) {
            Schema::dropIfExists('web');
        }

        // Drop 'webs' table if it exists (alternative naming)
        if (Schema::hasTable('webs')) {
            Schema::dropIfExists('webs');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate web table if needed (minimal structure)
        if (!Schema::hasTable('web')) {
            Schema::create('web', function (Blueprint $table) {
                $table->id();
                $table->string('url')->nullable();
                $table->timestamp('create_time')->nullable();
                $table->timestamp('update_time')->nullable();
            });
        }
    }
};
