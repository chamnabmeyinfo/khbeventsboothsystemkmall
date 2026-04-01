<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPageEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_page_id',
        'event_type',
        'cta_label',
        'lead_name',
        'lead_email',
        'lead_phone',
        'source',
        'ip_address',
        'user_agent',
        'referrer_url',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class);
    }
}
