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
        if (!Schema::hasTable('costings')) {
            Schema::create('costings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255); // Costing name/title
                $table->text('description')->nullable();
                $table->unsignedBigInteger('floor_plan_id')->nullable(); // Link to floor plan/event
                $table->unsignedBigInteger('booking_id')->nullable(); // Link to booking
                $table->decimal('estimated_cost', 15, 2)->nullable(); // Estimated cost
                $table->decimal('actual_cost', 15, 2)->nullable(); // Actual cost
                $table->date('costing_date');
                $table->string('status', 50)->default('draft'); // draft, approved, in_progress, completed, cancelled
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
                
                $table->index('costing_date');
                $table->index('status');
                $table->index('floor_plan_id');
                $table->index('booking_id');
                $table->foreign('floor_plan_id')->references('id')->on('floor_plans')->onDelete('set null');
                $table->foreign('booking_id')->references('id')->on('book')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('user')->onDelete('set null');
                $table->foreign('approved_by')->references('id')->on('user')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costings');
    }
};
