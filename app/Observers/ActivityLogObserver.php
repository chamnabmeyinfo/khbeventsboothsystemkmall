<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Services\TelegramService;

class ActivityLogObserver
{
    public function __construct(
        private TelegramService $telegram
    ) {}

    /**
     * Send Telegram notification when an activity is logged.
     */
    public function created(ActivityLog $activityLog): void
    {
        try {
            $this->telegram->notifyActivity($activityLog);
        } catch (\Exception $e) {
            \Log::error('Telegram activity notification failed: '.$e->getMessage());
        }
    }
}
