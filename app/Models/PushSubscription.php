<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'endpoint',
        'endpoint_hash',
        'public_key',
        'auth_token',
        'content_encoding',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Convert to array format expected by Minishlink\WebPush\Subscription::create()
     */
    public function toWebPushSubscriptionArray(): array
    {
        $arr = [
            'endpoint' => $this->endpoint,
            'contentEncoding' => $this->content_encoding ?? 'aesgcm',
        ];
        if ($this->public_key && $this->auth_token) {
            $arr['keys'] = [
                'p256dh' => $this->public_key,
                'auth' => $this->auth_token,
            ];
        }

        return $arr;
    }
}
