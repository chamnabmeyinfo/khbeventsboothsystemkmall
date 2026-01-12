<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $table = 'leave_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'max_days_per_year',
        'carry_forward',
        'requires_approval',
        'is_paid',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'max_days_per_year' => 'integer',
        'carry_forward' => 'boolean',
        'requires_approval' => 'boolean',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get leave requests of this type
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }

    /**
     * Get leave balances of this type
     */
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'leave_type_id');
    }

    /**
     * Scope for active leave types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
