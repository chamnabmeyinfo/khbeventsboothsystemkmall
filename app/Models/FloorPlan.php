<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class FloorPlan extends Model
{
    use HasFactory;

    /**
     * Fields an admin can choose to show on the public floor plan view, via
     * the "Public View Display Settings" section on the edit form. Keyed by
     * the underlying FloorPlan attribute; the label is what the admin sees.
     * Adding an entry here is enough to make it available in the toggle UI
     * and in publicViewInfoFields() below -- no migration needed unless the
     * underlying attribute itself is new.
     *
     * @var array<string, string>
     */
    public const PUBLIC_VIEW_INFO_FIELDS = [
        'description' => 'Description',
        'event_start_date' => 'Event Start Date',
        'event_end_date' => 'Event End Date',
        'event_start_time' => 'Event Start Time',
        'event_end_time' => 'Event End Time',
        'event_location' => 'Event Location',
        'event_venue' => 'Event Venue',
        'google_map_location' => 'Map / Location Embed',
    ];

    /**
     * Shown by default for floor plans created before this setting existed
     * (public_view_info_fields is null) and as the pre-checked defaults on
     * the edit form for a brand new floor plan.
     *
     * @var array<int, string>
     */
    public const DEFAULT_PUBLIC_VIEW_INFO_FIELDS = [
        'event_start_date',
        'event_end_date',
        'event_location',
        'event_venue',
    ];

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
        'proposal_attachment',
        'proposal_attachment_name',
        'event_start_date',
        'event_end_date',
        'event_start_time',
        'event_end_time',
        'event_location',
        'event_venue',
        'public_view_info_fields',
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
        'public_view_info_fields' => 'array',
    ];

    /**
     * Which PUBLIC_VIEW_INFO_FIELDS keys are enabled for this floor plan, with
     * fallback to DEFAULT_PUBLIC_VIEW_INFO_FIELDS for existing rows saved
     * before this setting existed (column is null, not an empty array --
     * null means "never configured", [] means "admin explicitly chose none").
     * Filters out any stored key that isn't a currently-known field, in case
     * PUBLIC_VIEW_INFO_FIELDS shrinks in a future change.
     *
     * @return array<int, string>
     */
    public function publicViewInfoFields(): array
    {
        $selected = $this->public_view_info_fields ?? self::DEFAULT_PUBLIC_VIEW_INFO_FIELDS;

        return array_values(array_intersect($selected, array_keys(self::PUBLIC_VIEW_INFO_FIELDS)));
    }

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
