<?php

namespace App\Models\HR;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryHistory extends Model
{
    use HasFactory;

    protected $table = 'salary_history';

    protected $fillable = [
        'employee_id',
        'effective_date',
        'salary',
        'currency',
        'reason',
        'notes',
        'approved_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'salary' => 'decimal:2',
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
}
