<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Push Notifications (Web Push)
    |--------------------------------------------------------------------------
    |
    | When enabled, the app can send browser push notifications (e.g. when
    | a new in-app notification is created). VAPID keys are used for Web Push.
    | Enable/disable and VAPID public key can also be set in Settings (DB);
    | those override these env defaults. Private key must only be in .env.
    |
    */

    'push' => [
        'enabled' => env('PUSH_NOTIFICATIONS_ENABLED', true),
        'vapid_public_key' => env('PUSH_VAPID_PUBLIC_KEY', ''),
        'vapid_private_key' => env('PUSH_VAPID_PRIVATE_KEY', ''),
    ],

];
