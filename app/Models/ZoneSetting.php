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
        ];
    }
}

