<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class FloorPlan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'name',
        'description',
        'project_name',
        'floor_image',
        'google_map_location',
        'feature_image',
        'proposal',
        'event_start_date',
        'event_end_date',
        'event_start_time',
        'event_end_time',
        'event_location',
        'event_venue',
        'canvas_width',
        'canvas_height',
        'is_active',
        'is_default',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'canvas_width' => 'integer',
        'canvas_height' => 'integer',
        'created_by' => 'integer', // Ensure created_by is always cast as integer
        'event_id' => 'integer', // Ensure event_id is always cast as integer
        'event_start_date' => 'date',
        'event_end_date' => 'date',
    ];

    /**
     * Get the event that owns this floor plan
     */
    public function event()
    {
        // Always return the relationship - it will only query when accessed
        // We check table existence in controllers before eager loading
        return $this->belongsTo(Event::class, 'event_id');
    }
    
    /**
     * Get event safely (checks if table exists)
     */
    public function getEventSafely()
    {
        if (Schema::hasTable('events') && $this->event_id) {
            try {
                return Event::find($this->event_id);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Get all booths for this floor plan
     */
    public function booths()
    {
        return $this->hasMany(Booth::class, 'floor_plan_id');
    }

    /**
     * Get the user who created this floor plan
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if this is the default floor plan
     */
    public function isDefault(): bool
    {
        return $this->is_default === true;
    }

    /**
     * Get the count of booths in this floor plan
     */
    public function getBoothCount(): int
    {
        return $this->booths()->count();
    }

    /**
     * Get statistics for this floor plan
     */
    public function getStats(): array
    {
        $booths = $this->booths;
        $total = $booths->count();
        $available = $booths->where('status', Booth::STATUS_AVAILABLE)->count();
        $reserved = $booths->where('status', Booth::STATUS_RESERVED)->count();
        $confirmed = $booths->where('status', Booth::STATUS_CONFIRMED)->count();
        $paid = $booths->where('status', Booth::STATUS_PAID)->count();
        $hidden = $booths->where('status', Booth::STATUS_HIDDEN)->count();

        return [
            'total' => $total,
            'available' => $available,
            'reserved' => $reserved,
            'confirmed' => $confirmed,
            'paid' => $paid,
            'hidden' => $hidden,
            'occupied' => $total - $available - $hidden,
            'occupancy_rate' => $total > 0 ? round((($total - $available - $hidden) / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Scope: Get only active floor plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get default floor plan
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope: Get floor plans for a specific event
     */
    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    /**
     * Set this floor plan as default (and unset others)
     */
    public function setAsDefault(): void
    {
        // Unset all other default floor plans
        static::where('is_default', true)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this as default
        $this->update(['is_default' => true]);
    }
}
