<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_period_start',
        'review_period_end',
        'review_date',
        'overall_rating',
        'goals_achieved',
        'strengths',
        'areas_for_improvement',
        'comments',
        'employee_comments',
        'status',
    ];

    protected $casts = [
        'review_period_start' => 'date',
        'review_period_end' => 'date',
        'review_date' => 'date',
        'overall_rating' => 'decimal:2',
    ];

    /**
     * Get the employee being reviewed
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the reviewer (employee)
     */
    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    /**
     * Get review criteria
     */
    public function criteria()
    {
        return $this->hasMany(PerformanceReviewCriterion::class, 'review_id');
    }
}
