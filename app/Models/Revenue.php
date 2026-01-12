<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'category_id',
        'revenue_date',
        'payment_method',
        'reference_number',
        'client_id',
        'floor_plan_id',
        'booking_id',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'revenue_date' => 'date',
        'created_by' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_RECEIVED = 'received';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    /**
     * Get the client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Get the floor plan
     */
    public function floorPlan()
    {
        return $this->belongsTo(FloorPlan::class, 'floor_plan_id');
    }

    /**
     * Get the booking
     */
    public function booking()
    {
        return $this->belongsTo(Book::class, 'booking_id');
    }

    /**
     * Get the user who created this revenue
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Get revenues by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get revenues by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('revenue_date', [$from, $to]);
    }
}
