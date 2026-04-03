<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Marketing lead captured from a public landing page form (not booth / floor-plan bookings).
 */
class LandingPageLead extends Model
{
    protected $table = 'landing_page_leads';

    protected $fillable = [
        'landing_page_id',
        'landing_tracking_event_id',
        'visitor_id',
        'session_uuid',
        'name',
        'email',
        'phone',
        'preferred_trip_date',
        'locale',
        'source',
        'meta',
        'ip_address',
        'user_agent',
        'referrer_url',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function trackingEvent(): BelongsTo
    {
        return $this->belongsTo(LandingTrackingEvent::class, 'landing_tracking_event_id');
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(LandingTrackingVisitor::class, 'visitor_id');
    }
}
