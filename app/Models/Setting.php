<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value by key
     */
    public static function setValue($key, $value, $type = 'string', $description = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'type' => $type,
                'description' => $description,
            ]
        );

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Get all booth default settings
     */
    public static function getBoothDefaults()
    {
        return [
            'width' => self::getValue('booth_default_width', 80),
            'height' => self::getValue('booth_default_height', 50),
            'rotation' => self::getValue('booth_default_rotation', 0),
            'z_index' => self::getValue('booth_default_z_index', 10),
            'font_size' => self::getValue('booth_default_font_size', 14),
            'border_width' => self::getValue('booth_default_border_width', 2),
            'border_radius' => self::getValue('booth_default_border_radius', 6),
            'opacity' => self::getValue('booth_default_opacity', 1.00),
            // Appearance settings
            'background_color' => self::getValue('booth_default_background_color', '#ffffff'),
            'border_color' => self::getValue('booth_default_border_color', '#007bff'),
            'text_color' => self::getValue('booth_default_text_color', '#000000'),
            'font_weight' => self::getValue('booth_default_font_weight', '700'),
            'font_family' => self::getValue('booth_default_font_family', 'Arial, sans-serif'),
            'text_align' => self::getValue('booth_default_text_align', 'center'),
            'box_shadow' => self::getValue('booth_default_box_shadow', '0 2px 8px rgba(0,0,0,0.2)'),
        ];
    }

    /**
     * Save all booth default settings
     */
    public static function saveBoothDefaults($settings)
    {
        $defaults = [
            'booth_default_width' => ['type' => 'integer', 'description' => 'Default width for new booths (in pixels)'],
            'booth_default_height' => ['type' => 'integer', 'description' => 'Default height for new booths (in pixels)'],
            'booth_default_rotation' => ['type' => 'integer', 'description' => 'Default rotation angle for new booths (in degrees)'],
            'booth_default_z_index' => ['type' => 'integer', 'description' => 'Default z-index for new booths'],
            'booth_default_font_size' => ['type' => 'integer', 'description' => 'Default font size for booth number text (in pixels)'],
            'booth_default_border_width' => ['type' => 'integer', 'description' => 'Default border width for booths (in pixels)'],
            'booth_default_border_radius' => ['type' => 'integer', 'description' => 'Default border radius for booths (in pixels)'],
            'booth_default_opacity' => ['type' => 'float', 'description' => 'Default opacity for booths (0.0 to 1.0)'],
            // Appearance settings
            'booth_default_background_color' => ['type' => 'string', 'description' => 'Default background color for booths'],
            'booth_default_border_color' => ['type' => 'string', 'description' => 'Default border color for booths'],
            'booth_default_text_color' => ['type' => 'string', 'description' => 'Default text color for booth numbers'],
            'booth_default_font_weight' => ['type' => 'string', 'description' => 'Default font weight for booth numbers'],
            'booth_default_font_family' => ['type' => 'string', 'description' => 'Default font family for booth numbers'],
            'booth_default_text_align' => ['type' => 'string', 'description' => 'Default text alignment for booth numbers'],
            'booth_default_box_shadow' => ['type' => 'string', 'description' => 'Default box shadow for booths'],
        ];

        foreach ($defaults as $key => $config) {
            $settingKey = str_replace('booth_default_', '', $key);
            if (isset($settings[$settingKey])) {
                self::setValue($key, $settings[$settingKey], $config['type'], $config['description']);
            }
        }

        return self::getBoothDefaults();
    }
}
