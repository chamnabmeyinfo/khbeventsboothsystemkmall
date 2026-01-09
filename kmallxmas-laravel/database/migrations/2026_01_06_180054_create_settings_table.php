<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->index();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, float, boolean, json
            $table->text('description')->nullable();
            $table->timestamps();
        });
        
        // Insert default booth settings
        $defaults = [
            ['key' => 'booth_default_width', 'value' => '80', 'type' => 'integer', 'description' => 'Default width for new booths (in pixels)'],
            ['key' => 'booth_default_height', 'value' => '50', 'type' => 'integer', 'description' => 'Default height for new booths (in pixels)'],
            ['key' => 'booth_default_rotation', 'value' => '0', 'type' => 'integer', 'description' => 'Default rotation angle for new booths (in degrees)'],
            ['key' => 'booth_default_z_index', 'value' => '10', 'type' => 'integer', 'description' => 'Default z-index for new booths'],
            ['key' => 'booth_default_font_size', 'value' => '14', 'type' => 'integer', 'description' => 'Default font size for booth number text (in pixels)'],
            ['key' => 'booth_default_border_width', 'value' => '2', 'type' => 'integer', 'description' => 'Default border width for booths (in pixels)'],
            ['key' => 'booth_default_border_radius', 'value' => '6', 'type' => 'integer', 'description' => 'Default border radius for booths (in pixels)'],
            ['key' => 'booth_default_opacity', 'value' => '1.00', 'type' => 'float', 'description' => 'Default opacity for booths (0.0 to 1.0)'],
            // Appearance settings
            ['key' => 'booth_default_background_color', 'value' => '#ffffff', 'type' => 'string', 'description' => 'Default background color for booths'],
            ['key' => 'booth_default_border_color', 'value' => '#007bff', 'type' => 'string', 'description' => 'Default border color for booths'],
            ['key' => 'booth_default_text_color', 'value' => '#000000', 'type' => 'string', 'description' => 'Default text color for booth numbers'],
            ['key' => 'booth_default_font_weight', 'value' => '700', 'type' => 'string', 'description' => 'Default font weight for booth numbers'],
            ['key' => 'booth_default_font_family', 'value' => 'Arial, sans-serif', 'type' => 'string', 'description' => 'Default font family for booth numbers'],
            ['key' => 'booth_default_text_align', 'value' => 'center', 'type' => 'string', 'description' => 'Default text alignment for booth numbers'],
            ['key' => 'booth_default_box_shadow', 'value' => '0 2px 8px rgba(0,0,0,0.2)', 'type' => 'string', 'description' => 'Default box shadow for booths'],
        ];
        
        foreach ($defaults as $default) {
            DB::table('settings')->insert(array_merge($default, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
