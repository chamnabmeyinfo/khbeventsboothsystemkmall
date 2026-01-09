<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_name',
        'width',
        'height',
        'rotation',
        'z_index',
        'border_radius',
        'border_width',
        'opacity',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
        'rotation' => 'integer',
        'z_index' => 'integer',
        'border_radius' => 'float',
        'border_width' => 'float',
        'opacity' => 'float',
    ];

    /**
     * Get zone settings by zone name
     */
    public static function getByZoneName($zoneName)
    {
        return self::where('zone_name', $zoneName)->first();
    }

    /**
     * Save or update zone settings
     */
    public static function saveZoneSettings($zoneName, $settings)
    {
        return self::updateOrCreate(
            ['zone_name' => $zoneName],
            [
                'width' => $settings['width'] ?? 80,
                'height' => $settings['height'] ?? 50,
                'rotation' => $settings['rotation'] ?? 0,
                'z_index' => $settings['zIndex'] ?? $settings['z_index'] ?? 10,
                'border_radius' => $settings['borderRadius'] ?? $settings['border_radius'] ?? 6,
                'border_width' => $settings['borderWidth'] ?? $settings['border_width'] ?? 2,
                'opacity' => $settings['opacity'] ?? 1.0,
            ]
        );
    }

    /**
     * Get default settings for a zone (returns saved settings or defaults)
     */
    public static function getZoneDefaults($zoneName)
    {
        $zoneSetting = self::getByZoneName($zoneName);
        
        if ($zoneSetting) {
            return [
                'width' => $zoneSetting->width,
                'height' => $zoneSetting->height,
                'rotation' => $zoneSetting->rotation,
                'zIndex' => $zoneSetting->z_index,
                'borderRadius' => $zoneSetting->border_radius,
                'borderWidth' => $zoneSetting->border_width,
                'opacity' => $zoneSetting->opacity,
            ];
        }

        // Return default values if no saved settings
        return [
            'width' => 80,
            'height' => 50,
            'rotation' => 0,
            'zIndex' => 10,
            'borderRadius' => 6,
            'borderWidth' => 2,
            'opacity' => 1.0,
        ];
    }
}

