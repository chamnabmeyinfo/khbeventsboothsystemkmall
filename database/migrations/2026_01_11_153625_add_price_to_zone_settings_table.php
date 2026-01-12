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
        Schema::table('zone_settings', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->nullable()->default(500)->after('opacity')->comment('Default price for booths in this zone (floor-plan-specific)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zone_settings', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
