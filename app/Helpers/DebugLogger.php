<?php

namespace App\Helpers;

class DebugLogger
{
    /**
     * Get the debug log file path
     * Uses Laravel's storage directory structure
     */
    public static function getLogPath(): string
    {
        // Use Laravel's storage directory for debug logs
        $logPath = storage_path('logs/debug.log');

        // Ensure the logs directory exists
        $logDir = dirname($logPath);
        if (! is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        return $logPath;
    }

    /**
     * Write debug log entry
     * Only logs in local/development environment
     */
    public static function log(array $data, string $location = '', string $message = ''): void
    {
        // Only log in local/development environment
        if (app()->environment(['local', 'development'])) {
            $logEntry = [
                'id' => 'log_'.time().'_'.uniqid(),
                'timestamp' => time() * 1000,
                'location' => $location,
                'message' => $message,
                'data' => $data,
                'sessionId' => session()->getId() ?? 'no-session',
                'runId' => 'run1',
                'hypothesisId' => 'A',
            ];

            @file_put_contents(
                self::getLogPath(),
                json_encode($logEntry)."\n",
                FILE_APPEND
            );
        }
    }

    /**
     * Clear debug log
     */
    public static function clear(): bool
    {
        $logPath = self::getLogPath();
        if (file_exists($logPath)) {
            return @unlink($logPath);
        }

        return true;
    }
}
