<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoothImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'booth_id',
        'floor_plan_id',
        'image_path',
        'image_type',
        'caption',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the booth that owns the image
     */
    public function booth()
    {
        return $this->belongsTo(Booth::class);
    }

    /**
     * Get the floor plan
     */
    public function floorPlan()
    {
        return $this->belongsTo(FloorPlan::class);
    }

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute()
    {
        return asset($this->image_path);
    }

    /**
     * Get image type label
     */
    public function getTypeLabel()
    {
        $labels = [
            'photo' => 'Photo',
            'layout' => 'Layout Plan',
            'setup' => 'Setup',
            'teardown' => 'Teardown',
            'facility' => 'Facility',
        ];

        return $labels[$this->image_type] ?? 'Photo';
    }

    /**
     * Scope for primary images only
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for specific booth
     */
    public function scopeForBooth($query, $boothId)
    {
        return $query->where('booth_id', $boothId)->orderBy('sort_order');
    }
}
