<?php

namespace App\Models\HR;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'employee_id',
        'date',
        'check_in_time',
        'check_out_time',
        'break_duration',
        'total_hours',
        'status',
        'late_minutes',
        'overtime_hours',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'break_duration' => 'integer',
        'total_hours' => 'decimal:2',
        'late_minutes' => 'integer',
        'overtime_hours' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calculate total hours worked
     */
    public function calculateTotalHours()
    {
        if (! $this->check_in_time || ! $this->check_out_time) {
            return 0;
        }

        $checkIn = \Carbon\Carbon::parse($this->date.' '.$this->check_in_time);
        $checkOut = \Carbon\Carbon::parse($this->date.' '.$this->check_out_time);
        $totalMinutes = $checkOut->diffInMinutes($checkIn) - ($this->break_duration ?? 0);

        return round($totalMinutes / 60, 2);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for employee
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
