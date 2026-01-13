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
        if (!Schema::hasTable('finance_categories')) {
            Schema::create('finance_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('type', 50); // 'expense', 'revenue', 'costing'
                $table->text('description')->nullable();
                $table->string('color', 50)->nullable(); // For UI display
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('type');
                $table->index('is_active');
                $table->foreign('created_by')->references('id')->on('user')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_categories');
    }
};
