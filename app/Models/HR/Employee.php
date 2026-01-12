<?php

namespace App\Models\HR;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'phone',
        'mobile',
        'date_of_birth',
        'gender',
        'nationality',
        'id_card_number',
        'passport_number',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'department_id',
        'position_id',
        'manager_id',
        'employment_type',
        'hire_date',
        'probation_end_date',
        'contract_start_date',
        'contract_end_date',
        'termination_date',
        'termination_reason',
        'status',
        'salary',
        'currency',
        'bank_name',
        'bank_account',
        'tax_id',
        'social_security_number',
        'avatar',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'probation_end_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'termination_date' => 'date',
        'salary' => 'decimal:2',
    ];

    /**
     * Get the user account associated with this employee
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the department
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the position
     */
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Get the manager (employee)
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Get direct reports (employees managed by this employee)
     */
    public function directReports()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    /**
     * Get attendance records
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    /**
     * Get leave requests
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    /**
     * Get leave balances
     */
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'employee_id');
    }

    /**
     * Get performance reviews
     */
    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class, 'employee_id');
    }

    /**
     * Get documents
     */
    public function documents()
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id');
    }

    /**
     * Get training records
     */
    public function training()
    {
        return $this->hasMany(EmployeeTraining::class, 'employee_id');
    }

    /**
     * Get salary history
     */
    public function salaryHistory()
    {
        return $this->hasMany(SalaryHistory::class, 'employee_id');
    }

    /**
     * Get the creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        $name = trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
        return $name;
    }

    /**
     * Scope for active employees
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for employees in a department
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope for employees with a position
     */
    public function scopeWithPosition($query, $positionId)
    {
        return $query->where('position_id', $positionId);
    }
}
