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
        if (!Schema::hasTable('client')) {
            return;
        }
        
        Schema::table('client', function (Blueprint $table) {
            // Add phone 1 and phone 2 fields
            if (!Schema::hasColumn('client', 'phone_1')) {
                $table->string('phone_1', 20)->nullable()->after('phone_number')->comment('Primary phone number');
            }
            if (!Schema::hasColumn('client', 'phone_2')) {
                $table->string('phone_2', 20)->nullable()->after('phone_1')->comment('Secondary phone number');
            }
            
            // Add email 1 and email 2 fields
            if (!Schema::hasColumn('client', 'email_1')) {
                $table->string('email_1', 191)->nullable()->after('email')->comment('Primary email address');
            }
            if (!Schema::hasColumn('client', 'email_2')) {
                $table->string('email_2', 191)->nullable()->after('email_1')->comment('Secondary email address');
            }
            
            // Add company name in Khmer
            if (!Schema::hasColumn('client', 'company_name_khmer')) {
                $table->string('company_name_khmer', 255)->nullable()->after('company')->comment('Company name in Khmer language');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('client')) {
            return;
        }
        
        Schema::table('client', function (Blueprint $table) {
            if (Schema::hasColumn('client', 'company_name_khmer')) {
                $table->dropColumn('company_name_khmer');
            }
            if (Schema::hasColumn('client', 'email_2')) {
                $table->dropColumn('email_2');
            }
            if (Schema::hasColumn('client', 'email_1')) {
                $table->dropColumn('email_1');
            }
            if (Schema::hasColumn('client', 'phone_2')) {
                $table->dropColumn('phone_2');
            }
            if (Schema::hasColumn('client', 'phone_1')) {
                $table->dropColumn('phone_1');
            }
        });
    }
};
