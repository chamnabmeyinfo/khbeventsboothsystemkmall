<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FloorPlan;

class CanvasSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_plan_id',
        'canvas_width',
        'canvas_height',
        'canvas_resolution',
        'grid_size',
        'zoom_level',
        'pan_x',
        'pan_y',
        'floorplan_image',
        'grid_enabled',
        'snap_to_grid',
    ];

    protected $casts = [
        'floor_plan_id' => 'integer',
        'canvas_width' => 'integer',
        'canvas_height' => 'integer',
        'canvas_resolution' => 'integer',
        'grid_size' => 'integer',
        'zoom_level' => 'float',
        'pan_x' => 'float',
        'pan_y' => 'float',
        'grid_enabled' => 'boolean',
        'snap_to_grid' => 'boolean',
    ];

    /**
     * Get canvas settings for a specific floor plan (floor-plan-specific)
     * If floor_plan_id is null, returns first available (for backward compatibility)
     */
    public static function getForFloorPlan(?int $floorPlanId = null)
    {
        // If floor_plan_id is provided, get settings for that floor plan
        if ($floorPlanId !== null) {
            $settings = self::where('floor_plan_id', $floorPlanId)->first();
            if ($settings) {
                return $settings;
            }
            
            // If not found, create default settings for this floor plan
            $floorPlan = FloorPlan::find($floorPlanId);
            if ($floorPlan) {
                return self::create([
                    'floor_plan_id' => $floorPlanId,
                    'canvas_width' => $floorPlan->canvas_width ?? 1200,
                    'canvas_height' => $floorPlan->canvas_height ?? 800,
                    'canvas_resolution' => 300,
                    'grid_size' => 10,
                    'zoom_level' => 1.00,
                    'pan_x' => 0,
                    'pan_y' => 0,
                    'floorplan_image' => $floorPlan->floor_image ?? null,
                    'grid_enabled' => true,
                    'snap_to_grid' => false,
                ]);
            }
        }
        
        // Fallback: get first available settings (for backward compatibility)
        return self::first() ?? self::create([
            'floor_plan_id' => null,
            'canvas_width' => 1200,
            'canvas_height' => 800,
            'canvas_resolution' => 300,
            'grid_size' => 10,
            'zoom_level' => 1.00,
            'pan_x' => 0,
            'pan_y' => 0,
            'grid_enabled' => true,
            'snap_to_grid' => false,
        ]);
    }

    /**
     * Get the current canvas settings (singleton pattern - for backward compatibility)
     * @deprecated Use getForFloorPlan() instead
     */
    public static function getCurrent()
    {
        return self::getForFloorPlan(null);
    }

    /**
     * Update canvas settings for a specific floor plan
     */
    public static function updateSettings(array $settings, ?int $floorPlanId = null)
    {
        $canvasSetting = self::getForFloorPlan($floorPlanId);
        $canvasSetting->update($settings);
        return $canvasSetting;
    }

    /**
     * Relationship: Get the floor plan for these canvas settings
     */
    public function floorPlan()
    {
        return $this->belongsTo(FloorPlan::class, 'floor_plan_id');
    }
}
