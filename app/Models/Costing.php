<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Costing extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'floor_plan_id',
        'booking_id',
        'estimated_cost',
        'actual_cost',
        'costing_date',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'costing_date' => 'date',
        'approved_at' => 'datetime',
        'created_by' => 'integer',
        'approved_by' => 'integer',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

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
     * Get the user who created this costing
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this costing
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the variance between estimated and actual cost
     */
    public function getVarianceAttribute()
    {
        if ($this->estimated_cost && $this->actual_cost) {
            return $this->actual_cost - $this->estimated_cost;
        }
        return null;
    }

    /**
     * Scope: Get costings by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get costings by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('costing_date', [$from, $to]);
    }
}
