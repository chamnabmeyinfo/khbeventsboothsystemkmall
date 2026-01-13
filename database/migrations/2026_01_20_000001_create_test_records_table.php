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
        // Guard: only create if not present
        if (!Schema::hasTable('test_records')) {
            Schema::create('test_records', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->text('notes')->nullable();
                $table->string('status', 50)->default('active');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_records');
    }
};
#test 