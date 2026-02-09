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
        if (! Schema::hasTable('zone_settings')) {
            return;
        }

        Schema::table('zone_settings', function (Blueprint $table) {
            // Add appearance/color fields for zone-specific customization
            // These will override default booth appearance for booths in this zone
            if (! Schema::hasColumn('zone_settings', 'background_color')) {
                $table->string('background_color', 50)->nullable()->after('opacity')->comment('Default background color for booths in this zone');
            }
            if (! Schema::hasColumn('zone_settings', 'border_color')) {
                $table->string('border_color', 50)->nullable()->after('background_color')->comment('Default border color for booths in this zone');
            }
            if (! Schema::hasColumn('zone_settings', 'text_color')) {
                $table->string('text_color', 50)->nullable()->after('border_color')->comment('Default text color for booths in this zone');
            }
            if (! Schema::hasColumn('zone_settings', 'font_weight')) {
                $table->string('font_weight', 20)->nullable()->after('text_color')->comment('Default font weight for booths in this zone');
            }
            if (! Schema::hasColumn('zone_settings', 'font_family')) {
                $table->string('font_family', 255)->nullable()->after('font_weight')->comment('Default font family for booths in this zone');
            }
            if (! Schema::hasColumn('zone_settings', 'text_align')) {
                $table->string('text_align', 20)->nullable()->after('font_family')->comment('Default text alignment for booths in this zone');
            }
            if (! Schema::hasColumn('zone_settings', 'box_shadow')) {
                $table->string('box_shadow', 255)->nullable()->after('text_align')->comment('Default box shadow for booths in this zone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('zone_settings')) {
            return;
        }

        Schema::table('zone_settings', function (Blueprint $table) {
            if (Schema::hasColumn('zone_settings', 'box_shadow')) {
                $table->dropColumn('box_shadow');
            }
            if (Schema::hasColumn('zone_settings', 'text_align')) {
                $table->dropColumn('text_align');
            }
            if (Schema::hasColumn('zone_settings', 'font_family')) {
                $table->dropColumn('font_family');
            }
            if (Schema::hasColumn('zone_settings', 'font_weight')) {
                $table->dropColumn('font_weight');
            }
            if (Schema::hasColumn('zone_settings', 'text_color')) {
                $table->dropColumn('text_color');
            }
            if (Schema::hasColumn('zone_settings', 'border_color')) {
                $table->dropColumn('border_color');
            }
            if (Schema::hasColumn('zone_settings', 'background_color')) {
                $table->dropColumn('background_color');
            }
        });
    }
};
