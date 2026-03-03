<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class FloorPlanTickSetting extends Model
{
    use HasFactory;

    protected $table = 'floor_plan_tick_settings';

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
     * Safe when table does not exist (e.g. migration not run yet).
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

        try {
            if (! Schema::hasTable('floor_plan_tick_settings')) {
                return self::getGlobalTickSettings($defaults);
            }
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
        } catch (\Throwable $e) {
            \Log::warning('FloorPlanTickSetting::getForFloorPlan failed, using global', ['error' => $e->getMessage()]);
        }

        return self::getGlobalTickSettings($defaults);
    }

    protected static function getGlobalTickSettings(array $defaults): array
    {
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
     * Persist tick settings to global Setting (used when per-floor-plan table is missing or save fails).
     */
    protected static function saveToGlobalSettings(array $data): void
    {
        Setting::setValue('booth_booked_show_tick', (! empty($data['show_tick'])) ? '1' : '0', 'boolean', 'Show tick on booked booths.');
        Setting::setValue('booth_booked_tick_color', $data['color'] ?? '#28a745', 'string', 'Tick color.');
        Setting::setValue('booth_booked_tick_size', $data['size'] ?? 'medium', 'string', 'Tick size.');
        Setting::setValue('booth_booked_tick_shape', $data['shape'] ?? 'round', 'string', 'Tick shape.');
        Setting::setValue('booth_booked_tick_position', $data['position'] ?? 'top-right', 'string', 'Tick position.');
        Setting::setValue('booth_booked_tick_animation', $data['animation'] ?? 'pulse', 'string', 'Tick animation.');
        Setting::setValue('booth_booked_tick_bg_color', $data['bg_color'] ?? '', 'string', 'Tick background.');
        Setting::setValue('booth_booked_tick_border_width', $data['border_width'] ?? '0', 'string', 'Tick border width.');
        Setting::setValue('booth_booked_tick_border_color', $data['border_color'] ?? '#ffffff', 'string', 'Tick border color.');
        Setting::setValue('booth_booked_tick_font_size', $data['font_size'] ?? 'medium', 'string', 'Tick font size.');
        Setting::setValue('booth_booked_tick_size_mode', $data['size_mode'] ?? 'fixed', 'string', 'Tick size mode.');
        Setting::setValue('booth_booked_tick_relative_percent', $data['relative_percent'] ?? '12', 'string', 'Tick relative percent.');
    }

    /**
     * Save tick settings for a floor plan.
     * Safe when table does not exist: falls back to global Setting.
     */
    public static function saveForFloorPlan(int $floorPlanId, array $data): self
    {
        try {
            if (! Schema::hasTable('floor_plan_tick_settings')) {
                self::saveToGlobalSettings($data);
                $m = new self;
                $m->floor_plan_id = $floorPlanId;
                return $m;
            }
        } catch (\Throwable $e) {
            \Log::warning('FloorPlanTickSetting::saveForFloorPlan failed, saving to global', ['error' => $e->getMessage()]);
            self::saveToGlobalSettings($data);
            $m = new self;
            $m->floor_plan_id = $floorPlanId;
            return $m;
        }

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
