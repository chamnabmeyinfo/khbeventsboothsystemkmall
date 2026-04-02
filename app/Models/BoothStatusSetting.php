<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoothStatusSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_plan_id',
        'status_code',
        'status_name',
        'status_color',
        'border_color',
        'border_width',
        'border_style',
        'border_radius',
        'text_color',
        'badge_color',
        'description',
        'is_active',
        'sort_order',
        'is_default',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'is_default' => 'boolean',
        'border_width' => 'integer',
        'border_radius' => 'integer',
    ];

    /**
     * Get floor plan relationship
     */
    public function floorPlan()
    {
        return $this->belongsTo(\App\Models\FloorPlan::class, 'floor_plan_id');
    }

    /**
     * Get all active statuses ordered by sort_order (optionally filtered by floor plan)
     */
    public static function getActiveStatuses($floorPlanId = null)
    {
        $query = self::where('is_active', true);

        if ($floorPlanId !== null) {
            // Get statuses for specific floor plan OR global statuses (floor_plan_id is null)
            $query->where(function ($q) use ($floorPlanId) {
                $q->where('floor_plan_id', $floorPlanId)
                    ->orWhereNull('floor_plan_id');
            });
        } else {
            // Get only global statuses (no floor plan assigned)
            $query->whereNull('floor_plan_id');
        }

        // Global rows first, floor-plan-specific rows last so the same status_code resolves to
        // the floor-specific definition when both exist (getStatusColors / CSS / dropdowns).
        return $query
            ->orderByRaw('CASE WHEN floor_plan_id IS NOT NULL THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Global-only factory defaults (initial migration seed + border fields).
     */
    public static function defaultGlobalSeedRows(): array
    {
        $now = now();

        return [
            ['floor_plan_id' => null, 'status_code' => 1, 'status_name' => 'Available', 'status_color' => '#28a745', 'border_color' => '#28a745', 'border_width' => 2, 'border_style' => 'solid', 'border_radius' => 4, 'text_color' => '#ffffff', 'badge_color' => 'success', 'description' => 'Booth is available for booking', 'is_active' => true, 'sort_order' => 1, 'is_default' => true, 'created_at' => $now, 'updated_at' => $now],
            ['floor_plan_id' => null, 'status_code' => 2, 'status_name' => 'Confirmed', 'status_color' => '#0dcaf0', 'border_color' => '#0dcaf0', 'border_width' => 2, 'border_style' => 'solid', 'border_radius' => 4, 'text_color' => '#ffffff', 'badge_color' => 'info', 'description' => 'Booking has been confirmed', 'is_active' => true, 'sort_order' => 2, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['floor_plan_id' => null, 'status_code' => 3, 'status_name' => 'Reserved', 'status_color' => '#ffc107', 'border_color' => '#ffc107', 'border_width' => 2, 'border_style' => 'solid', 'border_radius' => 4, 'text_color' => '#333333', 'badge_color' => 'warning', 'description' => 'Booth is reserved but not yet confirmed', 'is_active' => true, 'sort_order' => 3, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['floor_plan_id' => null, 'status_code' => 4, 'status_name' => 'Hidden', 'status_color' => '#6c757d', 'border_color' => '#6c757d', 'border_width' => 2, 'border_style' => 'solid', 'border_radius' => 4, 'text_color' => '#ffffff', 'badge_color' => 'secondary', 'description' => 'Booth is hidden from public view', 'is_active' => true, 'sort_order' => 4, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['floor_plan_id' => null, 'status_code' => 5, 'status_name' => 'Paid', 'status_color' => '#212529', 'border_color' => '#212529', 'border_width' => 2, 'border_style' => 'solid', 'border_radius' => 4, 'text_color' => '#ffffff', 'badge_color' => 'primary', 'description' => 'Payment has been received', 'is_active' => true, 'sort_order' => 5, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
        ];
    }

    /**
     * Get status by code (optionally filtered by floor plan).
     */
    public static function getByCode($statusCode, $floorPlanId = null)
    {
        $query = self::where('status_code', $statusCode)
            ->where('is_active', true);

        if ($floorPlanId !== null) {
            // Get status for specific floor plan OR global status
            $query->where(function ($q) use ($floorPlanId) {
                $q->where('floor_plan_id', $floorPlanId)
                    ->orWhereNull('floor_plan_id');
            })->orderByRaw('CASE WHEN floor_plan_id IS NOT NULL THEN 0 ELSE 1 END'); // Prefer floor-plan-specific over global
        } else {
            // Get only global status
            $query->whereNull('floor_plan_id');
        }

        return $query->first();
    }

    /**
     * Get default status (optionally filtered by floor plan)
     */
    public static function getDefaultStatus($floorPlanId = null)
    {
        $query = self::where('is_default', true)
            ->where('is_active', true);

        if ($floorPlanId !== null) {
            // Get default status for specific floor plan OR global default
            $query->where(function ($q) use ($floorPlanId) {
                $q->where('floor_plan_id', $floorPlanId)
                    ->orWhereNull('floor_plan_id');
            })->orderByRaw('CASE WHEN floor_plan_id IS NOT NULL THEN 0 ELSE 1 END'); // Prefer floor-plan-specific over global
        } else {
            // Get only global default
            $query->whereNull('floor_plan_id');
        }

        return $query->first();
    }

    /**
     * Get all statuses as array for dropdowns (optionally filtered by floor plan)
     */
    public static function getStatusesArray($floorPlanId = null)
    {
        return self::getActiveStatuses($floorPlanId)
            ->mapWithKeys(function ($status) {
                return [$status->status_code => $status->status_name];
            })
            ->toArray();
    }

    /**
     * Get status colors as array (optionally filtered by floor plan)
     */
    public static function getStatusColors($floorPlanId = null)
    {
        $colors = [];
        foreach (self::getActiveStatuses($floorPlanId) as $status) {
            $colors[$status->status_code] = [
                'background' => $status->status_color,
                'border' => $status->border_color ?? $status->status_color,
                'border_width' => $status->border_width ?? 2,
                'border_style' => $status->border_style ?? 'solid',
                'border_radius' => $status->border_radius ?? 4,
                'text' => $status->text_color,
                'badge' => $status->badge_color,
            ];
        }

        return $colors;
    }
}
