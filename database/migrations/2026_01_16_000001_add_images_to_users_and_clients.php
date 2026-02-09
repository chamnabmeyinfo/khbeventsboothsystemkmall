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
        // Add avatar and cover_image to user table
        if (! Schema::hasColumn('user', 'avatar')) {
            Schema::table('user', function (Blueprint $table) {
                $table->string('avatar', 255)->nullable()->after('status');
                $table->string('cover_image', 255)->nullable()->after('avatar');
            });
        }

        // Add avatar and cover_image to client table
        if (! Schema::hasColumn('client', 'avatar')) {
            Schema::table('client', function (Blueprint $table) {
                $table->string('avatar', 255)->nullable()->after('phone_number');
                $table->string('cover_image', 255)->nullable()->after('avatar');
            });
        }

        // Add avatar and cover_image to category table (if needed)
        if (Schema::hasTable('category') && ! Schema::hasColumn('category', 'avatar')) {
            Schema::table('category', function (Blueprint $table) {
                $table->string('avatar', 255)->nullable()->after('status');
                $table->string('cover_image', 255)->nullable()->after('avatar');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('user', 'avatar')) {
            Schema::table('user', function (Blueprint $table) {
                $table->dropColumn(['avatar', 'cover_image']);
            });
        }

        if (Schema::hasColumn('client', 'avatar')) {
            Schema::table('client', function (Blueprint $table) {
                $table->dropColumn(['avatar', 'cover_image']);
            });
        }

        if (Schema::hasTable('category') && Schema::hasColumn('category', 'avatar')) {
            Schema::table('category', function (Blueprint $table) {
                $table->dropColumn(['avatar', 'cover_image']);
            });
        }
    }
};
