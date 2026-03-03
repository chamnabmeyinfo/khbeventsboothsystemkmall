<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    | Create a bot via @BotFather and paste the token here.
    */
    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Telegram Chat ID
    |--------------------------------------------------------------------------
    | The chat/group/channel ID to receive notifications.
    | For a group: add bot, send a message, then get ID from:
    | https://api.telegram.org/bot<TOKEN>/getUpdates
    */
    'chat_id' => env('TELEGRAM_CHAT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Activity Notifications
    |--------------------------------------------------------------------------
    | Send Telegram notifications when user activities are logged.
    */
    'activity_enabled' => env('TELEGRAM_ACTIVITY_ENABLED', true),
];
