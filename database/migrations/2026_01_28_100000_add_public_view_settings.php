<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds settings for public floor plan view: allow logged-in users to create
     * bookings from public view, and restrict non-admin users to CRUD only their own bookings.
     */
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $defaults = [
            [
                'key' => 'public_view_allow_create_booking',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow logged-in users with Create Bookings permission to create a booking from the public floor plan view.',
            ],
            [
                'key' => 'public_view_restrict_crud_to_own_booking',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'When enabled, non-admin users can only view, edit, update, and delete their own bookings (created by them). Administrators can manage all bookings.',
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
            'public_view_allow_create_booking',
            'public_view_restrict_crud_to_own_booking',
        ])->delete();
    }
};
