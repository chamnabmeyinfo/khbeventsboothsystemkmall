<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Send a message to the Telegram chat.
     *
     * @param  string  $message  Plain text or HTML message (use parse_mode for HTML)
     * @param  string  $parseMode  'HTML' or 'Markdown'
     * @return bool Success
     */
    public function sendMessage(string $message, string $parseMode = 'HTML'): bool
    {
        $token = config('telegram.bot_token');
        $chatId = config('telegram.chat_id');

        if (empty($token) || empty($chatId)) {
            Log::debug('Telegram: Missing TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID');

            return false;
        }

        try {
            $response = Http::timeout(10)->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => $parseMode,
                    'disable_web_page_preview' => true,
                ]
            );

            if (! $response->successful()) {
                Log::warning('Telegram send failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Telegram send error: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Format and send an activity log as a Telegram notification.
     */
    public function notifyActivity(ActivityLog $activity): bool
    {
        if (! config('telegram.activity_enabled', true)) {
            return false;
        }

        $userName = $activity->user_id
            ? ($activity->user?->username ?? "User #{$activity->user_id}")
            : 'System';

        $action = $this->formatAction($activity->action ?? 'unknown');
        $description = $activity->description ?? $action;

        $message = "<b>📋 Activity</b>\n"
            .'<b>Action:</b> '.$this->escapeHtml($action)."\n"
            .'<b>By:</b> '.$this->escapeHtml($userName)."\n"
            ."<b>Description:</b> ".$this->escapeHtml($description)."\n";

        if ($activity->route) {
            $message .= '<b>Route:</b> <code>'.$this->escapeHtml($activity->route)."</code>\n";
        }

        if ($activity->ip_address) {
            $message .= '<b>IP:</b> '.$this->escapeHtml($activity->ip_address)."\n";
        }

        $message .= '<b>Time:</b> '.$activity->created_at?->format('Y-m-d H:i:s')."\n";

        return $this->sendMessage($message);
    }

    private function formatAction(string $action): string
    {
        return str_replace(['.', '_'], [' › ', ' '], ucfirst($action));
    }

    private function escapeHtml(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
