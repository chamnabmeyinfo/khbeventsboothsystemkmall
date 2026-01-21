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
        Schema::table('book', function (Blueprint $table) {
            $table->integer('status')->default(1)->after('type')->comment('Booking status (1=Pending, 2=Confirmed, 3=Reserved, 4=Paid, 5=Cancelled)');
            $table->decimal('total_amount', 10, 2)->nullable()->after('status')->comment('Total booking amount');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('total_amount')->comment('Total amount paid');
            $table->decimal('balance_amount', 10, 2)->nullable()->after('paid_amount')->comment('Remaining balance');
            $table->date('payment_due_date')->nullable()->after('balance_amount')->comment('Payment due date');
            $table->text('notes')->nullable()->after('payment_due_date')->comment('Booking notes');
            
            $table->index('status');
            $table->index('payment_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_due_date']);
            $table->dropColumn(['status', 'total_amount', 'paid_amount', 'balance_amount', 'payment_due_date', 'notes']);
        });
    }
};
