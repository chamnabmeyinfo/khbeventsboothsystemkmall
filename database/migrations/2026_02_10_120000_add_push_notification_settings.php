<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds settings for browser push notifications (Web Push / VAPID).
     */
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $defaults = [
            [
                'key' => 'push_notifications_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable browser push notifications so users receive alerts when the tab is in the background.',
            ],
            [
                'key' => 'push_vapid_public_key',
                'value' => '',
                'type' => 'string',
                'description' => 'VAPID public key for Web Push (optional; can be set in .env as PUSH_VAPID_PUBLIC_KEY instead).',
            ],
        ];

        foreach ($defaults as $default) {
            $exists = DB::table('settings')->where('key', $default['key'])->exists();
            if (! $exists) {
                DB::table('settings')->insert(array_merge($default, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }
        DB::table('settings')->whereIn('key', [
            'push_notifications_enabled',
            'push_vapid_public_key',
        ])->delete();
    }
};
