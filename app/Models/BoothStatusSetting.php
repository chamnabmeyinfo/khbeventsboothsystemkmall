<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoothStatusSetting extends Model
{
    use HasFactory;

    protected $fillable = [
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
     * Get all active statuses ordered by sort_order
     */
    public static function getActiveStatuses()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get status by code
     */
    public static function getByCode($statusCode)
    {
        return self::where('status_code', $statusCode)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get default status
     */
    public static function getDefaultStatus()
    {
        return self::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all statuses as array for dropdowns
     */
    public static function getStatusesArray()
    {
        return self::getActiveStatuses()
            ->mapWithKeys(function ($status) {
                return [$status->status_code => $status->status_name];
            })
            ->toArray();
    }

    /**
     * Get status colors as array
     */
    public static function getStatusColors()
    {
        $colors = [];
        foreach (self::getActiveStatuses() as $status) {
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
