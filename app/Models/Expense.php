<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'category_id',
        'expense_date',
        'payment_method',
        'reference_number',
        'vendor_name',
        'notes',
        'floor_plan_id',
        'booking_id',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
        'created_by' => 'integer',
        'approved_by' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
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
     * Get the user who created this expense
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved this expense
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Get expenses by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get expenses by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('expense_date', [$from, $to]);
    }
}
