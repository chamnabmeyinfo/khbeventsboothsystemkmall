<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReviewCriterion extends Model
{
    use HasFactory;

    protected $table = 'performance_review_criteria';

    protected $fillable = [
        'review_id',
        'criterion_name',
        'rating',
        'comments',
        'weight',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    /**
     * Get the performance review
     */
    public function review()
    {
        return $this->belongsTo(PerformanceReview::class, 'review_id');
    }
}
