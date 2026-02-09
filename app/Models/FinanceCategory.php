<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'color',
        'is_active',
        'sort_order',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_by' => 'integer',
    ];

    const TYPE_EXPENSE = 'expense';

    const TYPE_REVENUE = 'revenue';

    const TYPE_COSTING = 'costing';

    /**
     * Get the user who created this category
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all expenses in this category
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }

    /**
     * Get all revenues in this category
     */
    public function revenues()
    {
        return $this->hasMany(Revenue::class, 'category_id');
    }

    /**
     * Scope: Get categories by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
