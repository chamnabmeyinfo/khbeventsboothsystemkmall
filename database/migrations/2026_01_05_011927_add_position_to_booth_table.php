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
        Schema::table('booth', function (Blueprint $table) {
            if (! Schema::hasColumn('booth', 'position_x')) {
                $table->decimal('position_x', 10, 2)->nullable()->after('booth_type_id');
            }
            if (! Schema::hasColumn('booth', 'position_y')) {
                $table->decimal('position_y', 10, 2)->nullable()->after('position_x');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booth', function (Blueprint $table) {
            $table->dropColumn(['position_x', 'position_y']);
        });
    }
};
