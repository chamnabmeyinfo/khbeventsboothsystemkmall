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
            if (!Schema::hasColumn('book', 'affiliate_user_id')) {
                $table->unsignedBigInteger('affiliate_user_id')->nullable()->after('userid');
            }
            // Add index only if column exists and index doesn't exist
            if (Schema::hasColumn('book', 'affiliate_user_id')) {
                $connection = Schema::getConnection();
                $database = $connection->getDatabaseName();
                $indexExists = $connection->select(
                    "SELECT COUNT(*) as count FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                    [$database, 'book', 'book_affiliate_user_id_index']
                );
                if (empty($indexExists) || $indexExists[0]->count == 0) {
                    $table->index('affiliate_user_id');
                }
            }
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
