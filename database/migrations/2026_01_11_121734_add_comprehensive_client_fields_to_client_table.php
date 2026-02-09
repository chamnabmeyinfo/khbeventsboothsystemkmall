<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds comprehensive client information fields required for successful bookings
     */
    public function up(): void
    {
        if (Schema::hasTable('client')) {
            Schema::table('client', function (Blueprint $table) {
                // Email - Required for communication
                if (! Schema::hasColumn('client', 'email')) {
                    $table->string('email', 191)->nullable()->after('phone_number');
                }

                // Address - Required for physical location
                if (! Schema::hasColumn('client', 'address')) {
                    $table->text('address')->nullable()->after('email');
                }

                // Tax ID / Business Registration Number - Optional but important for businesses
                if (! Schema::hasColumn('client', 'tax_id')) {
                    $table->string('tax_id', 50)->nullable()->after('address');
                }

                // Website - Optional business website
                if (! Schema::hasColumn('client', 'website')) {
                    $table->string('website', 255)->nullable()->after('tax_id');
                }

                // Notes / Additional Information - Optional additional details
                if (! Schema::hasColumn('client', 'notes')) {
                    $table->text('notes')->nullable()->after('website');
                }
            });

            // Add index on email for faster lookups
            try {
                $connection = Schema::getConnection();
                $database = $connection->getDatabaseName();

                $indexes = $connection->select(
                    "SELECT COUNT(*) as count 
                     FROM information_schema.statistics 
                     WHERE table_schema = ? 
                     AND table_name = 'client' 
                     AND index_name = 'client_email_index'",
                    [$database]
                );

                if ($indexes[0]->count == 0) {
                    Schema::table('client', function (Blueprint $table) {
                        $table->index('email', 'client_email_index');
                    });
                }
            } catch (\Exception $e) {
                // Silently fail if index already exists or table doesn't support indexes
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('client')) {
            Schema::table('client', function (Blueprint $table) {
                // Drop index first
                try {
                    $connection = Schema::getConnection();
                    $database = $connection->getDatabaseName();

                    $indexes = $connection->select(
                        "SELECT COUNT(*) as count 
                         FROM information_schema.statistics 
                         WHERE table_schema = ? 
                         AND table_name = 'client' 
                         AND index_name = 'client_email_index'",
                        [$database]
                    );

                    if ($indexes[0]->count > 0) {
                        $table->dropIndex('client_email_index');
                    }
                } catch (\Exception $e) {
                    // Silently fail
                }

                // Drop columns
                $columnsToDrop = ['notes', 'website', 'tax_id', 'address', 'email'];
                foreach ($columnsToDrop as $column) {
                    if (Schema::hasColumn('client', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
