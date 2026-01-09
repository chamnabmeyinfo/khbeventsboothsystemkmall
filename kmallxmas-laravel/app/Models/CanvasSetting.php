<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanvasSetting extends Model
{
    use HasFactory;

    protected $fillable = [
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
     * Get the current canvas settings (singleton pattern)
     */
    public static function getCurrent()
    {
        return self::first() ?? self::create([
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
     * Update canvas settings
     */
    public static function updateSettings(array $settings)
    {
        $canvasSetting = self::getCurrent();
        $canvasSetting->update($settings);
        return $canvasSetting;
    }
}
