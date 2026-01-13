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
            // Add affiliate_user_id to track which sales person's link was used
            // This helps avoid conflicts of interest between sales team members
            $table->unsignedBigInteger('affiliate_user_id')->nullable()->after('userid');
            $table->index('affiliate_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book', function (Blueprint $table) {
            $table->dropIndex(['affiliate_user_id']);
            $table->dropColumn('affiliate_user_id');
        });
    }
};
