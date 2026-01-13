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
        if (!Schema::hasTable('booth')) {
            return;
        }
        
        Schema::table('booth', function (Blueprint $table) {
            // Booth image for preview
            if (!Schema::hasColumn('booth', 'booth_image')) {
                $table->string('booth_image', 255)->nullable()->after('box_shadow');
            }
            
            // Description and additional information
            if (!Schema::hasColumn('booth', 'description')) {
                $table->text('description')->nullable()->after('booth_image');
            }
            
            if (!Schema::hasColumn('booth', 'features')) {
                $table->text('features')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('booth', 'capacity')) {
                $table->integer('capacity')->nullable()->after('features');
            }
            
            if (!Schema::hasColumn('booth', 'area_sqm')) {
                $table->decimal('area_sqm', 10, 2)->nullable()->after('capacity');
            }
            
            if (!Schema::hasColumn('booth', 'electricity_power')) {
                $table->string('electricity_power', 50)->nullable()->after('area_sqm');
            }
            
            if (!Schema::hasColumn('booth', 'notes')) {
                $table->text('notes')->nullable()->after('electricity_power');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('booth')) {
            return;
        }
        
        Schema::table('booth', function (Blueprint $table) {
            if (Schema::hasColumn('booth', 'booth_image')) {
                $table->dropColumn('booth_image');
            }
            if (Schema::hasColumn('booth', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('booth', 'features')) {
                $table->dropColumn('features');
            }
            if (Schema::hasColumn('booth', 'capacity')) {
                $table->dropColumn('capacity');
            }
            if (Schema::hasColumn('booth', 'area_sqm')) {
                $table->dropColumn('area_sqm');
            }
            if (Schema::hasColumn('booth', 'electricity_power')) {
                $table->dropColumn('electricity_power');
            }
            if (Schema::hasColumn('booth', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
