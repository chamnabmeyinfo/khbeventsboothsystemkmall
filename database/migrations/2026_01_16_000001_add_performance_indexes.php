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
        // Add indexes to frequently queried columns for better performance
        Schema::table('booth', function (Blueprint $table) {
            // Index on status (used in many queries)
            if (! $this->hasIndex('booth', 'booth_status_index')) {
                $table->index('status', 'booth_status_index');
            }

            // Index on userid (used in user statistics)
            if (! $this->hasIndex('booth', 'booth_userid_index')) {
                $table->index('userid', 'booth_userid_index');
            }

            // Index on client_id (used in joins)
            if (! $this->hasIndex('booth', 'booth_client_id_index')) {
                $table->index('client_id', 'booth_client_id_index');
            }

            // Composite index for common query pattern (status + userid)
            if (! $this->hasIndex('booth', 'booth_status_userid_index')) {
                $table->index(['status', 'userid'], 'booth_status_userid_index');
            }
        });

        // Index on book table for date_book (used in ordering)
        if (Schema::hasTable('book')) {
            Schema::table('book', function (Blueprint $table) {
                if (! $this->hasIndex('book', 'book_date_book_index')) {
                    $table->index('date_book', 'book_date_book_index');
                }

                if (! $this->hasIndex('book', 'book_clientid_index')) {
                    $table->index('clientid', 'book_clientid_index');
                }

                if (! $this->hasIndex('book', 'book_userid_index')) {
                    $table->index('userid', 'book_userid_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booth', function (Blueprint $table) {
            $table->dropIndex('booth_status_index');
            $table->dropIndex('booth_userid_index');
            $table->dropIndex('booth_client_id_index');
            $table->dropIndex('booth_status_userid_index');
        });

        if (Schema::hasTable('book')) {
            Schema::table('book', function (Blueprint $table) {
                $table->dropIndex('book_date_book_index');
                $table->dropIndex('book_clientid_index');
                $table->dropIndex('book_userid_index');
            });
        }
    }

    /**
     * Check if index exists
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = $connection->select(
            'SELECT COUNT(*) as count 
             FROM information_schema.statistics 
             WHERE table_schema = ? 
             AND table_name = ? 
             AND index_name = ?',
            [$database, $table, $indexName]
        );

        return $result[0]->count > 0;
    }
};
