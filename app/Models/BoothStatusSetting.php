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
            $query->where(function($q) use ($floorPlanId) {
                $q->where('floor_plan_id', $floorPlanId)
                  ->orWhereNull('floor_plan_id');
            });
        } else {
            // Get only global statuses (no floor plan assigned)
            $query->whereNull('floor_plan_id');
        }
        
        return $query->orderBy('sort_order')->get();
    }

    /**
     * Get status by code (optionally filtered by floor plan)
     */
    public static function getByCode($statusCode, $floorPlanId = null)
    {
        $query = self::where('status_code', $statusCode)
            ->where('is_active', true);
        
        if ($floorPlanId !== null) {
            // Get status for specific floor plan OR global status
            $query->where(function($q) use ($floorPlanId) {
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
            $query->where(function($q) use ($floorPlanId) {
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
                'text' => $status->text_color,
                'badge' => $status->badge_color,
            ];
        }
        return $colors;
    }
}
