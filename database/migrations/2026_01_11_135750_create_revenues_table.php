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
        if (! Schema::hasTable('revenues')) {
            Schema::create('revenues', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->text('description')->nullable();
                $table->decimal('amount', 15, 2);
                $table->unsignedBigInteger('category_id')->nullable();
                $table->date('revenue_date');
                $table->string('payment_method', 50)->default('cash'); // cash, bank_transfer, check, credit_card
                $table->string('reference_number', 255)->nullable(); // Invoice number, receipt number, etc.
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('floor_plan_id')->nullable(); // Link to floor plan/event
                $table->unsignedBigInteger('booking_id')->nullable(); // Link to booking
                $table->string('status', 50)->default('pending'); // pending, confirmed, received, cancelled
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index('revenue_date');
                $table->index('category_id');
                $table->index('status');
                $table->index('client_id');
                $table->index('floor_plan_id');
                $table->index('booking_id');
                $table->foreign('category_id')->references('id')->on('finance_categories')->onDelete('set null');
                $table->foreign('client_id')->references('id')->on('client')->onDelete('set null');
                $table->foreign('floor_plan_id')->references('id')->on('floor_plans')->onDelete('set null');
                $table->foreign('booking_id')->references('id')->on('book')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('user')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};
