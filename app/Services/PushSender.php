<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushSender
{
    /**
     * Send a push notification to a user (all their subscriptions).
     */
    public static function sendToUser(int $userId, string $title, string $body, ?string $url = null): void
    {
        if (! self::isPushEnabled()) {
            return;
        }

        $subscriptions = PushSubscription::where('user_id', $userId)->get();
        if ($subscriptions->isEmpty()) {
            return;
        }

        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => '/favicon.ico',
        ]);

        $vapid = self::getVapidAuth();
        if (! $vapid) {
            Log::warning('Push notifications enabled but VAPID keys not configured.');

            return;
        }

        try {
            $webPush = new WebPush(['VAPID' => $vapid], [], 20);

            foreach ($subscriptions as $sub) {
                try {
                    $wps = Subscription::create($sub->toWebPushSubscriptionArray());
                    $webPush->queueNotification($wps, $payload);
                } catch (\Throwable $e) {
                    Log::warning('Push subscription invalid for user '.$userId.': '.$e->getMessage());
                }
            }

            foreach ($webPush->flush() as $report) {
                if (! $report->isSuccess()) {
                    Log::warning('Push failed: '.$report->getReason());
                    if ($report->isSubscriptionExpired()) {
                        PushSubscription::where('endpoint', $report->getEndpoint())->delete();
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Push send failed: '.$e->getMessage());
        }
    }

    public static function isPushEnabled(): bool
    {
        $fromSetting = Setting::getValue('push_notifications_enabled', true);

        return $fromSetting && config('notifications.push.enabled', true);
    }

    /**
     * @return array{subject: string, publicKey: string, privateKey: string}|null
     */
    protected static function getVapidAuth(): ?array
    {
        $publicKey = Setting::getValue('push_vapid_public_key', '') ?: config('notifications.push.vapid_public_key', '');
        $privateKey = config('notifications.push.vapid_private_key', '');

        if ($publicKey === '' || $privateKey === '') {
            return null;
        }

        return [
            'subject' => 'mailto:'.(config('mail.from.address', 'noreply@localhost')),
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
        ];
    }
}
