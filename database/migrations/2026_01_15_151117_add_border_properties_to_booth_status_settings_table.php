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
        if (Schema::hasTable('booth_status_settings')) {
            Schema::table('booth_status_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('booth_status_settings', 'border_width')) {
                    $table->integer('border_width')->nullable()->default(2)->after('border_color')->comment('Border width in pixels (0-10)');
                }
                if (! Schema::hasColumn('booth_status_settings', 'border_style')) {
                    $table->string('border_style', 20)->nullable()->default('solid')->after('border_width')->comment('Border style (solid, dashed, dotted, double, etc.)');
                }
                if (! Schema::hasColumn('booth_status_settings', 'border_radius')) {
                    $table->integer('border_radius')->nullable()->default(4)->after('border_style')->comment('Border radius in pixels (0-50)');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booth_status_settings')) {
            Schema::table('booth_status_settings', function (Blueprint $table) {
                if (Schema::hasColumn('booth_status_settings', 'border_radius')) {
                    $table->dropColumn('border_radius');
                }
                if (Schema::hasColumn('booth_status_settings', 'border_style')) {
                    $table->dropColumn('border_style');
                }
                if (Schema::hasColumn('booth_status_settings', 'border_width')) {
                    $table->dropColumn('border_width');
                }
            });
        }
    }
};
