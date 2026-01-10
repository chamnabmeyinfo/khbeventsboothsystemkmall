<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('client_id');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('cash'); // cash, bank_transfer, online, etc.
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Who processed the payment
            $table->timestamps();
            
            $table->foreign('booking_id')->references('id')->on('book')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('client')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('user')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
