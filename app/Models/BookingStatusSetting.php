<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingStatusSetting extends Model
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
    public static function getDefault()
    {
        return self::where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Factory default rows (matches initial migration seed).
     */
    public static function defaultSeedRows(): array
    {
        $now = now();

        return [
            ['status_code' => 1, 'status_name' => 'Pending', 'status_color' => '#6c757d', 'border_color' => '#6c757d', 'text_color' => '#ffffff', 'badge_color' => 'secondary', 'description' => 'Booking is pending confirmation', 'is_active' => true, 'sort_order' => 1, 'is_default' => true, 'created_at' => $now, 'updated_at' => $now],
            ['status_code' => 2, 'status_name' => 'Confirmed', 'status_color' => '#0dcaf0', 'border_color' => '#0dcaf0', 'text_color' => '#ffffff', 'badge_color' => 'info', 'description' => 'Booking has been confirmed', 'is_active' => true, 'sort_order' => 2, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['status_code' => 3, 'status_name' => 'Reserved', 'status_color' => '#ffc107', 'border_color' => '#ffc107', 'text_color' => '#333333', 'badge_color' => 'warning', 'description' => 'Booking is reserved', 'is_active' => true, 'sort_order' => 3, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['status_code' => 4, 'status_name' => 'Paid', 'status_color' => '#28a745', 'border_color' => '#28a745', 'text_color' => '#ffffff', 'badge_color' => 'success', 'description' => 'Payment has been received', 'is_active' => true, 'sort_order' => 4, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['status_code' => 5, 'status_name' => 'Partially Paid', 'status_color' => '#17a2b8', 'border_color' => '#17a2b8', 'text_color' => '#ffffff', 'badge_color' => 'info', 'description' => 'Partial payment received', 'is_active' => true, 'sort_order' => 5, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
            ['status_code' => 6, 'status_name' => 'Cancelled', 'status_color' => '#dc3545', 'border_color' => '#dc3545', 'text_color' => '#ffffff', 'badge_color' => 'danger', 'description' => 'Booking has been cancelled', 'is_active' => true, 'sort_order' => 6, 'is_default' => false, 'created_at' => $now, 'updated_at' => $now],
        ];
    }
}
