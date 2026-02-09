<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            // Company Information Settings
            $companySettings = [
                ['key' => 'company_name', 'value' => 'KHB Events', 'type' => 'string', 'description' => 'Company name displayed throughout the system'],
                ['key' => 'company_logo', 'value' => '', 'type' => 'string', 'description' => 'Company logo file path (relative to public directory)'],
                ['key' => 'company_favicon', 'value' => '', 'type' => 'string', 'description' => 'Company favicon file path'],
                ['key' => 'company_email', 'value' => '', 'type' => 'string', 'description' => 'Company contact email'],
                ['key' => 'company_phone', 'value' => '', 'type' => 'string', 'description' => 'Company contact phone'],
                ['key' => 'company_address', 'value' => '', 'type' => 'string', 'description' => 'Company physical address'],
                ['key' => 'company_website', 'value' => '', 'type' => 'string', 'description' => 'Company website URL'],
            ];

            // System Color Scheme Settings
            $colorSettings = [
                ['key' => 'system_primary_color', 'value' => '#4e73df', 'type' => 'string', 'description' => 'Primary brand color (buttons, links, highlights)'],
                ['key' => 'system_secondary_color', 'value' => '#667eea', 'type' => 'string', 'description' => 'Secondary brand color'],
                ['key' => 'system_success_color', 'value' => '#1cc88a', 'type' => 'string', 'description' => 'Success/positive action color'],
                ['key' => 'system_info_color', 'value' => '#36b9cc', 'type' => 'string', 'description' => 'Information/notification color'],
                ['key' => 'system_warning_color', 'value' => '#f6c23e', 'type' => 'string', 'description' => 'Warning/alert color'],
                ['key' => 'system_danger_color', 'value' => '#e74a3b', 'type' => 'string', 'description' => 'Error/danger action color'],
                ['key' => 'system_sidebar_bg', 'value' => '#224abe', 'type' => 'string', 'description' => 'Sidebar background color'],
                ['key' => 'system_navbar_bg', 'value' => '#ffffff', 'type' => 'string', 'description' => 'Navbar background color'],
                ['key' => 'system_footer_bg', 'value' => '#f8f9fc', 'type' => 'string', 'description' => 'Footer background color'],
            ];

            // Combine all settings
            $allSettings = array_merge($companySettings, $colorSettings);

            // Insert settings if they don't exist
            foreach ($allSettings as $setting) {
                $exists = DB::table('settings')->where('key', $setting['key'])->exists();
                if (! $exists) {
                    DB::table('settings')->insert(array_merge($setting, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]));
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            $keysToRemove = [
                'company_name', 'company_logo', 'company_favicon', 'company_email',
                'company_phone', 'company_address', 'company_website',
                'system_primary_color', 'system_secondary_color', 'system_success_color',
                'system_info_color', 'system_warning_color', 'system_danger_color',
                'system_sidebar_bg', 'system_navbar_bg', 'system_footer_bg',
            ];

            DB::table('settings')->whereIn('key', $keysToRemove)->delete();
        }
    }
};
