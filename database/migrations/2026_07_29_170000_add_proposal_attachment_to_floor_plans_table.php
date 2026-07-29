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
        Schema::table('floor_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('floor_plans', 'proposal_attachment')) {
                $table->string('proposal_attachment', 255)->nullable()->after('proposal');
            }
            if (! Schema::hasColumn('floor_plans', 'proposal_attachment_name')) {
                $table->string('proposal_attachment_name', 255)->nullable()->after('proposal_attachment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('floor_plans', function (Blueprint $table) {
            $table->dropColumn(['proposal_attachment', 'proposal_attachment_name']);
        });
    }
};
