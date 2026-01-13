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
            if (!Schema::hasColumn('booth', 'width')) {
                $table->decimal('width', 10, 2)->nullable()->after('position_y');
            }
            if (!Schema::hasColumn('booth', 'height')) {
                $table->decimal('height', 10, 2)->nullable()->after('width');
            }
            if (!Schema::hasColumn('booth', 'rotation')) {
                $table->decimal('rotation', 10, 2)->nullable()->default(0)->after('height');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booth', function (Blueprint $table) {
            $table->dropColumn(['width', 'height', 'rotation']);
        });
    }
};
