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
            $table->integer('z_index')->nullable()->default(10)->after('rotation');
            $table->integer('font_size')->nullable()->default(14)->after('z_index');
            $table->integer('border_width')->nullable()->default(2)->after('font_size');
            $table->integer('border_radius')->nullable()->default(6)->after('border_width');
            $table->decimal('opacity', 3, 2)->nullable()->default(1.00)->after('border_radius');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booth', function (Blueprint $table) {
            $table->dropColumn(['z_index', 'font_size', 'border_width', 'border_radius', 'opacity']);
        });
    }
};
