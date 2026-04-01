<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingTrackingVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_uuid',
        'first_seen_at',
        'last_seen_at',
        'first_ip_address',
        'last_ip_address',
        'first_user_agent',
        'last_user_agent',
        'first_referrer_url',
        'last_referrer_url',
        'first_utm_source',
        'first_utm_medium',
        'first_utm_campaign',
        'first_utm_term',
        'first_utm_content',
        'last_utm_source',
        'last_utm_medium',
        'last_utm_campaign',
        'last_utm_term',
        'last_utm_content',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(LandingTrackingEvent::class, 'visitor_id');
    }
}
