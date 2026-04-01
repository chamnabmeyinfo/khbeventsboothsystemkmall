<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingTrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_page_id',
        'visitor_id',
        'session_uuid',
        'event_name',
        'event_category',
        'cta_label',
        'source',
        'lead_name',
        'lead_email',
        'lead_phone',
        'ip_address',
        'user_agent',
        'referrer_url',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'event_payload',
        'occurred_at',
    ];

    protected $casts = [
        'event_payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(LandingTrackingVisitor::class, 'visitor_id');
    }
}
