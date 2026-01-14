<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_plan_id',
        'zone_name',
        'width',
        'height',
        'rotation',
        'z_index',
        'border_radius',
        'border_width',
        'opacity',
        'price',
        // Appearance/Color fields (zone-specific customization)
        'background_color',
        'border_color',
        'text_color',
        'font_weight',
        'font_family',
        'text_align',
        'box_shadow',
    ];

    protected $casts = [
        'floor_plan_id' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'rotation' => 'integer',
        'z_index' => 'integer',
        'border_radius' => 'float',
        'border_width' => 'float',
        'opacity' => 'float',
        'price' => 'decimal:2',
    ];
    
    /**
     * Get the floor plan that owns this zone setting
     */
    public function floorPlan()
    {
        return $this->belongsTo(FloorPlan::class, 'floor_plan_id');
    }

    /**
     * Get zone settings by zone name and floor plan (floor-plan-specific)
     */
    public static function getByZoneName($zoneName, $floorPlanId = null)
    {
        $query = self::where('zone_name', $zoneName);
        
        // Filter by floor plan if specified
        if ($floorPlanId) {
            $query->where('floor_plan_id', $floorPlanId);
        } else {
            // If no floor plan specified, get global settings (backward compatibility)
            $query->whereNull('floor_plan_id');
        }
        
        return $query->first();
    }

    /**
     * Save or update zone settings (floor-plan-specific)
     */
    public static function saveZoneSettings($zoneName, $settings, $floorPlanId = null)
    {
        $whereClause = ['zone_name' => $zoneName];
        
        // Include floor_plan_id in unique constraint
        if ($floorPlanId) {
            $whereClause['floor_plan_id'] = $floorPlanId;
        } else {
            $whereClause['floor_plan_id'] = null; // Global settings (backward compatibility)
        }
        
        return self::updateOrCreate(
            $whereClause,
            [
                'floor_plan_id' => $floorPlanId,
                'width' => $settings['width'] ?? 80,
                'height' => $settings['height'] ?? 50,
                'rotation' => $settings['rotation'] ?? 0,
                'z_index' => $settings['zIndex'] ?? $settings['z_index'] ?? 10,
                'border_radius' => $settings['borderRadius'] ?? $settings['border_radius'] ?? 6,
                'border_width' => $settings['borderWidth'] ?? $settings['border_width'] ?? 2,
                'opacity' => $settings['opacity'] ?? 1.0,
                'price' => $settings['price'] ?? 500,
                // Appearance/Color fields (zone-specific customization)
                'background_color' => $settings['background_color'] ?? $settings['backgroundColor'] ?? null,
                'border_color' => $settings['border_color'] ?? $settings['borderColor'] ?? null,
                'text_color' => $settings['text_color'] ?? $settings['textColor'] ?? null,
                'font_weight' => $settings['font_weight'] ?? $settings['fontWeight'] ?? null,
                'font_family' => $settings['font_family'] ?? $settings['fontFamily'] ?? null,
                'text_align' => $settings['text_align'] ?? $settings['textAlign'] ?? null,
                'box_shadow' => $settings['box_shadow'] ?? $settings['boxShadow'] ?? null,
            ]
        );
    }

    /**
     * Get default settings for a zone (returns saved settings or defaults, floor-plan-specific)
     */
    public static function getZoneDefaults($zoneName, $floorPlanId = null)
    {
        $zoneSetting = self::getByZoneName($zoneName, $floorPlanId);
        
        if ($zoneSetting) {
            return [
                'width' => $zoneSetting->width,
                'height' => $zoneSetting->height,
                'rotation' => $zoneSetting->rotation,
                'zIndex' => $zoneSetting->z_index,
                'borderRadius' => $zoneSetting->border_radius,
                'borderWidth' => $zoneSetting->border_width,
                'opacity' => $zoneSetting->opacity,
                'price' => $zoneSetting->price ?? 500,
                // Appearance/Color fields (zone-specific customization)
                'background_color' => $zoneSetting->background_color,
                'border_color' => $zoneSetting->border_color,
                'text_color' => $zoneSetting->text_color,
                'font_weight' => $zoneSetting->font_weight,
                'font_family' => $zoneSetting->font_family,
                'text_align' => $zoneSetting->text_align,
                'box_shadow' => $zoneSetting->box_shadow,
            ];
        }

        // Return default values if no saved settings for this floor plan/zone
        return [
            'width' => 80,
            'height' => 50,
            'rotation' => 0,
            'zIndex' => 10,
            'borderRadius' => 6,
            'borderWidth' => 2,
            'opacity' => 1.0,
            'price' => 500,
            // Default appearance (null means use booth defaults)
            'background_color' => null,
            'border_color' => null,
            'text_color' => null,
            'font_weight' => null,
            'font_family' => null,
            'text_align' => null,
            'box_shadow' => null,
        ];
    }
}

