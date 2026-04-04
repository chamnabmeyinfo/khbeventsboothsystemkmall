<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public const KIND_REAL = 'real';

    public const KIND_DEMO = 'demo';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('user') && ! Schema::hasColumn('user', 'account_kind')) {
            Schema::table('user', function (Blueprint $table) {
                $table->string('account_kind', 20)->default(self::KIND_REAL)->after('status');
            });
        }

        if (Schema::hasTable('employees') && ! Schema::hasColumn('employees', 'account_kind')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('account_kind', 20)->default(self::KIND_REAL)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user') && Schema::hasColumn('user', 'account_kind')) {
            Schema::table('user', function (Blueprint $table) {
                $table->dropColumn('account_kind');
            });
        }

        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'account_kind')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('account_kind');
            });
        }
    }
};
