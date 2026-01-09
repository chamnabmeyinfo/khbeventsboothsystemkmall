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
            // Add appearance properties columns if they don't exist
            if (!Schema::hasColumn('booth', 'background_color')) {
                $table->string('background_color', 50)->nullable()->default('#ffffff')->after('opacity');
            }
            if (!Schema::hasColumn('booth', 'border_color')) {
                $table->string('border_color', 50)->nullable()->default('#007bff')->after('background_color');
            }
            if (!Schema::hasColumn('booth', 'text_color')) {
                $table->string('text_color', 50)->nullable()->default('#000000')->after('border_color');
            }
            if (!Schema::hasColumn('booth', 'font_weight')) {
                $table->string('font_weight', 20)->nullable()->default('700')->after('text_color');
            }
            if (!Schema::hasColumn('booth', 'font_family')) {
                $table->string('font_family', 255)->nullable()->default('Arial, sans-serif')->after('font_weight');
            }
            if (!Schema::hasColumn('booth', 'text_align')) {
                $table->string('text_align', 20)->nullable()->default('center')->after('font_family');
            }
            if (!Schema::hasColumn('booth', 'box_shadow')) {
                $table->string('box_shadow', 255)->nullable()->default('0 2px 8px rgba(0,0,0,0.2)')->after('text_align');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booth', function (Blueprint $table) {
            $table->dropColumn([
                'background_color',
                'border_color',
                'text_color',
                'font_weight',
                'font_family',
                'text_align',
                'box_shadow'
            ]);
        });
    }
};
