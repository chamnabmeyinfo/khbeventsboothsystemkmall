<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AffiliateBenefit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'calculation_method',
        'percentage',
        'fixed_amount',
        'target_revenue',
        'target_bookings',
        'target_clients',
        'tier_structure',
        'floor_plan_id',
        'user_id',
        'start_date',
        'end_date',
        'is_active',
        'priority',
        'description',
        'conditions',
        'min_revenue',
        'max_benefit',
        'created_by',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'target_revenue' => 'decimal:2',
        'target_bookings' => 'integer',
        'target_clients' => 'integer',
        'tier_structure' => 'array',
        'conditions' => 'array',
        'min_revenue' => 'decimal:2',
        'max_benefit' => 'decimal:2',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Benefit types
    const TYPE_COMMISSION = 'commission';

    const TYPE_BONUS = 'bonus';

    const TYPE_INCENTIVE = 'incentive';

    const TYPE_REWARD = 'reward';

    // Calculation methods
    const METHOD_PERCENTAGE = 'percentage';

    const METHOD_FIXED_AMOUNT = 'fixed_amount';

    const METHOD_TIERED_PERCENTAGE = 'tiered_percentage';

    const METHOD_TIERED_AMOUNT = 'tiered_amount';

    /**
     * Get the floor plan this benefit applies to
     */
    public function floorPlan()
    {
        return $this->belongsTo(FloorPlan::class, 'floor_plan_id');
    }

    /**
     * Get the user this benefit applies to (null = all users)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who created this benefit
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Calculate benefit amount based on revenue/booking
     */
    public function calculateBenefit($revenue, $bookings = 0, $clients = 0, $floorPlanId = null, $userId = null)
    {
        // Check if benefit is active
        if (! $this->is_active) {
            return 0;
        }

        // Check date range
        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return 0;
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return 0;
        }

        // Check if applies to specific user
        if ($this->user_id && $this->user_id != $userId) {
            return 0;
        }

        // Check if applies to specific floor plan
        if ($this->floor_plan_id && $this->floor_plan_id != $floorPlanId) {
            return 0;
        }

        // Check minimum revenue requirement
        if ($this->min_revenue && $revenue < $this->min_revenue) {
            return 0;
        }

        // Check targets
        if ($this->target_revenue && $revenue < $this->target_revenue) {
            return 0;
        }
        if ($this->target_bookings && $bookings < $this->target_bookings) {
            return 0;
        }
        if ($this->target_clients && $clients < $this->target_clients) {
            return 0;
        }

        // Calculate benefit based on method
        $benefit = 0;

        switch ($this->calculation_method) {
            case self::METHOD_PERCENTAGE:
                $benefit = $revenue * ($this->percentage / 100);
                break;

            case self::METHOD_FIXED_AMOUNT:
                $benefit = $this->fixed_amount;
                break;

            case self::METHOD_TIERED_PERCENTAGE:
                $benefit = $this->calculateTieredPercentage($revenue);
                break;

            case self::METHOD_TIERED_AMOUNT:
                $benefit = $this->calculateTieredAmount($revenue);
                break;
        }

        // Apply maximum benefit cap
        if ($this->max_benefit && $benefit > $this->max_benefit) {
            $benefit = $this->max_benefit;
        }

        return round($benefit, 2);
    }

    /**
     * Calculate tiered percentage benefit
     */
    private function calculateTieredPercentage($revenue)
    {
        if (! $this->tier_structure || ! is_array($this->tier_structure)) {
            return 0;
        }

        $totalBenefit = 0;
        $remainingRevenue = $revenue;

        foreach ($this->tier_structure as $tier) {
            $min = $tier['min'] ?? 0;
            $max = $tier['max'] ?? PHP_INT_MAX;
            $percentage = $tier['percentage'] ?? 0;

            if ($remainingRevenue <= 0) {
                break;
            }

            $tierRevenue = min($remainingRevenue, $max - $min);
            if ($tierRevenue > 0) {
                $totalBenefit += $tierRevenue * ($percentage / 100);
                $remainingRevenue -= $tierRevenue;
            }
        }

        return $totalBenefit;
    }

    /**
     * Calculate tiered amount benefit
     */
    private function calculateTieredAmount($revenue)
    {
        if (! $this->tier_structure || ! is_array($this->tier_structure)) {
            return 0;
        }

        foreach ($this->tier_structure as $tier) {
            $min = $tier['min'] ?? 0;
            $max = $tier['max'] ?? PHP_INT_MAX;
            $amount = $tier['amount'] ?? 0;

            if ($revenue >= $min && $revenue <= $max) {
                return $amount;
            }
        }

        return 0;
    }

    /**
     * Scope: Get active benefits
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get benefits for a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Get benefits applicable to a user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('user_id')
                ->orWhere('user_id', $userId);
        });
    }

    /**
     * Scope: Get benefits applicable to a floor plan
     */
    public function scopeForFloorPlan($query, $floorPlanId)
    {
        return $query->where(function ($q) use ($floorPlanId) {
            $q->whereNull('floor_plan_id')
                ->orWhere('floor_plan_id', $floorPlanId);
        });
    }
}
