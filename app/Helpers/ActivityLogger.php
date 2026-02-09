<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogger
{
    /**
     * Log an activity (non-intrusive - won't break if logging fails)
     */
    public static function log($action, $model = null, $description = null, $oldValues = null, $newValues = null)
    {
        try {
            return ActivityLog::log($action, $model, $description, $oldValues, $newValues);
        } catch (\Exception $e) {
            // Silently fail - don't break the application
            \Log::error('Activity logging failed: '.$e->getMessage());

            return null;
        }
    }
}
