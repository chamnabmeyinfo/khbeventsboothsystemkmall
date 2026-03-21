<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client', function (Blueprint $table) {
            if (Schema::hasColumn('client', 'email_2')) {
                $table->dropColumn('email_2');
            }
            if (Schema::hasColumn('client', 'email_1')) {
                $table->dropColumn('email_1');
            }
            if (Schema::hasColumn('client', 'phone_2')) {
                $table->dropColumn('phone_2');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client', function (Blueprint $table) {
            if (! Schema::hasColumn('client', 'phone_2')) {
                $table->string('phone_2', 20)->nullable()->after('phone_1');
            }
            if (! Schema::hasColumn('client', 'email_1')) {
                $table->string('email_1', 191)->nullable()->after('email');
            }
            if (! Schema::hasColumn('client', 'email_2')) {
                $table->string('email_2', 191)->nullable()->after('email_1');
            }
        });
    }
};
