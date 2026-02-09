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
        Schema::create('booking_timeline', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('booking_id')->nullable(); // References book.id
            $table->unsignedInteger('booth_id'); // References booth.id
            $table->string('action'); // created, reserved, confirmed, deposit_paid, balance_paid, cancelled, modified
            $table->text('details')->nullable(); // JSON or text description
            $table->unsignedInteger('user_id')->nullable(); // Who performed the action
            $table->decimal('amount', 10, 2)->nullable(); // Amount if payment action
            $table->string('old_status')->nullable(); // Previous status
            $table->string('new_status')->nullable(); // New status
            $table->timestamps();

            // Indexes
            $table->index('booking_id');
            $table->index('booth_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_timeline');
    }
};
