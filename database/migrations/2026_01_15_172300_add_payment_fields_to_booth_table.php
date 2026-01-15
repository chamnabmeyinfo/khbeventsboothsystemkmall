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
        Schema::table('booth', function (Blueprint $table) {
            // Payment tracking fields
            $table->decimal('deposit_amount', 10, 2)->default(0)->after('price');
            $table->decimal('deposit_paid', 10, 2)->default(0)->after('deposit_amount');
            $table->decimal('balance_due', 10, 2)->default(0)->after('deposit_paid');
            $table->decimal('balance_paid', 10, 2)->default(0)->after('balance_due');
            $table->date('payment_due_date')->nullable()->after('balance_paid');
            $table->date('deposit_paid_date')->nullable()->after('payment_due_date');
            $table->date('balance_paid_date')->nullable()->after('deposit_paid_date');
            $table->string('payment_status')->default('pending')->after('balance_paid_date'); // pending, partial, paid
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booth', function (Blueprint $table) {
            $table->dropColumn([
                'deposit_amount',
                'deposit_paid',
                'balance_due',
                'balance_paid',
                'payment_due_date',
                'deposit_paid_date',
                'balance_paid_date',
                'payment_status',
            ]);
        });
    }
};
