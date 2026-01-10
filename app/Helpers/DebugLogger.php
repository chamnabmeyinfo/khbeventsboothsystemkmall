<?php

namespace App\Helpers;

class DebugLogger
{
    /**
     * Check if debug logging is enabled
     * Can be disabled via config or .env (DEBUG_LOGGING=false)
     * 
     * @return bool
     */
    public static function isEnabled(): bool
    {
        // Check environment variable first (allows quick disable)
        if (env('DEBUG_LOGGING', null) === false) {
            return false;
        }
        
        // Only enable in local/development environments
        return app()->environment(['local', 'development']);
    }

    /**
     * Get the debug log file path
     * Uses Laravel's storage directory structure
     * 
     * @return string
     */
    public static function getLogPath(): string
    {
        // Use Laravel's storage directory for debug logs
        $logPath = storage_path('logs/debug.log');
        
        // Ensure the logs directory exists
        $logDir = dirname($logPath);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        return $logPath;
    }
    
    /**
     * Write debug log entry
     * Only logs when enabled (local/development + DEBUG_LOGGING not false)
     * 
     * Performance: Early return when disabled to minimize overhead
     * 
     * @param array $data
     * @param string $location
     * @param string $message
     * @return void
     */
    public static function log(array $data, string $location = '', string $message = ''): void
    {
        // Early return for production - minimal overhead
        if (!self::isEnabled()) {
            return;
        }
        
        $logEntry = [
            'id' => 'log_' . time() . '_' . uniqid(),
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
            json_encode($logEntry) . "\n",
            FILE_APPEND
        );
    }
    
    /**
     * Clear debug log
     * 
     * @return bool
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
