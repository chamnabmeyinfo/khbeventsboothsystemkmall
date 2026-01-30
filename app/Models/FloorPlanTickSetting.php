<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FloorPlanTickSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_plan_id',
        'show_tick',
        'color',
        'size',
        'shape',
        'position',
        'animation',
        'bg_color',
        'border_width',
        'border_color',
        'font_size',
        'size_mode',
        'relative_percent',
    ];

    protected $casts = [
        'floor_plan_id' => 'integer',
        'show_tick' => 'boolean',
    ];

    public function floorPlan()
    {
        return $this->belongsTo(FloorPlan::class, 'floor_plan_id');
    }

    /**
     * Get tick settings for a floor plan. If the floor plan has its own row, use it; otherwise fall back to global Setting.
     */
    public static function getForFloorPlan(?int $floorPlanId): array
    {
        $defaults = [
            'show_tick' => true,
            'color' => '#28a745',
            'size' => 'medium',
            'shape' => 'round',
            'position' => 'top-right',
            'animation' => 'pulse',
            'bg_color' => '',
            'border_width' => '0',
            'border_color' => '#ffffff',
            'font_size' => 'medium',
            'size_mode' => 'fixed',
            'relative_percent' => '12',
        ];

        if ($floorPlanId) {
            $row = self::where('floor_plan_id', $floorPlanId)->first();
            if ($row) {
                return [
                    'show_tick' => (bool) $row->show_tick,
                    'color' => $row->color ?? $defaults['color'],
                    'size' => $row->size ?? $defaults['size'],
                    'shape' => $row->shape ?? $defaults['shape'],
                    'position' => $row->position ?? $defaults['position'],
                    'animation' => $row->animation ?? $defaults['animation'],
                    'bg_color' => $row->bg_color ?? $defaults['bg_color'],
                    'border_width' => $row->border_width ?? $defaults['border_width'],
                    'border_color' => $row->border_color ?? $defaults['border_color'],
                    'font_size' => $row->font_size ?? $defaults['font_size'],
                    'size_mode' => $row->size_mode ?? $defaults['size_mode'],
                    'relative_percent' => $row->relative_percent ?? $defaults['relative_percent'],
                ];
            }
        }

        return [
            'show_tick' => Setting::getValue('booth_booked_show_tick', $defaults['show_tick']),
            'color' => Setting::getValue('booth_booked_tick_color', $defaults['color']),
            'size' => Setting::getValue('booth_booked_tick_size', $defaults['size']),
            'shape' => Setting::getValue('booth_booked_tick_shape', $defaults['shape']),
            'position' => Setting::getValue('booth_booked_tick_position', $defaults['position']),
            'animation' => Setting::getValue('booth_booked_tick_animation', $defaults['animation']),
            'bg_color' => Setting::getValue('booth_booked_tick_bg_color', $defaults['bg_color']) ?? '',
            'border_width' => (string) Setting::getValue('booth_booked_tick_border_width', $defaults['border_width']),
            'border_color' => Setting::getValue('booth_booked_tick_border_color', $defaults['border_color']),
            'font_size' => Setting::getValue('booth_booked_tick_font_size', $defaults['font_size']),
            'size_mode' => Setting::getValue('booth_booked_tick_size_mode', $defaults['size_mode']),
            'relative_percent' => (string) Setting::getValue('booth_booked_tick_relative_percent', $defaults['relative_percent']),
        ];
    }

    /**
     * Save tick settings for a floor plan.
     */
    public static function saveForFloorPlan(int $floorPlanId, array $data): self
    {
        return self::updateOrCreate(
            ['floor_plan_id' => $floorPlanId],
            [
                'show_tick' => $data['show_tick'] ?? true,
                'color' => $data['color'] ?? '#28a745',
                'size' => $data['size'] ?? 'medium',
                'shape' => $data['shape'] ?? 'round',
                'position' => $data['position'] ?? 'top-right',
                'animation' => $data['animation'] ?? 'pulse',
                'bg_color' => $data['bg_color'] ?? null,
                'border_width' => $data['border_width'] ?? '0',
                'border_color' => $data['border_color'] ?? '#ffffff',
                'font_size' => $data['font_size'] ?? 'medium',
                'size_mode' => $data['size_mode'] ?? 'fixed',
                'relative_percent' => $data['relative_percent'] ?? '12',
            ]
        );
    }
}
