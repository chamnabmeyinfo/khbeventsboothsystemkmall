<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = null)
    {
        try {
            $setting = self::where('key', $key)->first();

            if (! $setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        } catch (\Exception $e) {
            // If table doesn't exist or any error occurs, return default
            return $default;
        }
    }

    /**
     * Set a setting value by key
     */
    public static function setValue($key, $value, $type = 'string', $description = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => (string) $value,
                'type' => $type,
                'description' => $description,
            ]
        );

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Cast value based on type
     */
    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Get all booth default settings
     */
    public static function getBoothDefaults()
    {
        return [
            'width' => self::getValue('booth_default_width', 80),
            'height' => self::getValue('booth_default_height', 50),
            'rotation' => self::getValue('booth_default_rotation', 0),
            'z_index' => self::getValue('booth_default_z_index', 10),
            'font_size' => self::getValue('booth_default_font_size', 14),
            'border_width' => self::getValue('booth_default_border_width', 2),
            'border_radius' => self::getValue('booth_default_border_radius', 6),
            'opacity' => self::getValue('booth_default_opacity', 1.00),
            // Appearance settings
            'background_color' => self::getValue('booth_default_background_color', '#ffffff'),
            'border_color' => self::getValue('booth_default_border_color', '#007bff'),
            'text_color' => self::getValue('booth_default_text_color', '#000000'),
            'font_weight' => self::getValue('booth_default_font_weight', '700'),
            'font_family' => self::getValue('booth_default_font_family', 'Arial, sans-serif'),
            'text_align' => self::getValue('booth_default_text_align', 'center'),
            'box_shadow' => self::getValue('booth_default_box_shadow', '0 2px 8px rgba(0,0,0,0.2)'),
        ];
    }

    /**
     * Save all booth default settings
     */
    public static function saveBoothDefaults($settings)
    {
        $defaults = [
            'booth_default_width' => ['type' => 'integer', 'description' => 'Default width for new booths (in pixels)'],
            'booth_default_height' => ['type' => 'integer', 'description' => 'Default height for new booths (in pixels)'],
            'booth_default_rotation' => ['type' => 'integer', 'description' => 'Default rotation angle for new booths (in degrees)'],
            'booth_default_z_index' => ['type' => 'integer', 'description' => 'Default z-index for new booths'],
            'booth_default_font_size' => ['type' => 'integer', 'description' => 'Default font size for booth number text (in pixels)'],
            'booth_default_border_width' => ['type' => 'integer', 'description' => 'Default border width for booths (in pixels)'],
            'booth_default_border_radius' => ['type' => 'integer', 'description' => 'Default border radius for booths (in pixels)'],
            'booth_default_opacity' => ['type' => 'float', 'description' => 'Default opacity for booths (0.0 to 1.0)'],
            // Appearance settings
            'booth_default_background_color' => ['type' => 'string', 'description' => 'Default background color for booths'],
            'booth_default_border_color' => ['type' => 'string', 'description' => 'Default border color for booths'],
            'booth_default_text_color' => ['type' => 'string', 'description' => 'Default text color for booth numbers'],
            'booth_default_font_weight' => ['type' => 'string', 'description' => 'Default font weight for booth numbers'],
            'booth_default_font_family' => ['type' => 'string', 'description' => 'Default font family for booth numbers'],
            'booth_default_text_align' => ['type' => 'string', 'description' => 'Default text alignment for booth numbers'],
            'booth_default_box_shadow' => ['type' => 'string', 'description' => 'Default box shadow for booths'],
        ];

        foreach ($defaults as $key => $config) {
            $settingKey = str_replace('booth_default_', '', $key);
            if (isset($settings[$settingKey])) {
                self::setValue($key, $settings[$settingKey], $config['type'], $config['description']);
            }
        }

        return self::getBoothDefaults();
    }

    /**
     * Get all company information settings
     */
    public static function getCompanySettings()
    {
        return [
            'company_name' => self::getValue('company_name', 'KHB Events'),
            'company_logo' => self::getValue('company_logo', ''),
            'company_favicon' => self::getValue('company_favicon', ''),
            'company_email' => self::getValue('company_email', ''),
            'company_phone' => self::getValue('company_phone', ''),
            'company_address' => self::getValue('company_address', ''),
            'company_website' => self::getValue('company_website', ''),
        ];
    }

    /**
     * Get all system appearance/color settings
     */
    public static function getAppearanceSettings()
    {
        return [
            'primary_color' => self::getValue('system_primary_color', '#4e73df'),
            'secondary_color' => self::getValue('system_secondary_color', '#667eea'),
            'success_color' => self::getValue('system_success_color', '#1cc88a'),
            'info_color' => self::getValue('system_info_color', '#36b9cc'),
            'warning_color' => self::getValue('system_warning_color', '#f6c23e'),
            'danger_color' => self::getValue('system_danger_color', '#e74a3b'),
            'sidebar_bg' => self::getValue('system_sidebar_bg', '#224abe'),
            'navbar_bg' => self::getValue('system_navbar_bg', '#ffffff'),
            'footer_bg' => self::getValue('system_footer_bg', '#f8f9fc'),
        ];
    }

    /**
     * Save company information settings
     */
    public static function saveCompanySettings($settings)
    {
        $defaults = [
            'company_name' => ['type' => 'string', 'description' => 'Company name displayed throughout the system'],
            'company_logo' => ['type' => 'string', 'description' => 'Company logo file path'],
            'company_favicon' => ['type' => 'string', 'description' => 'Company favicon file path'],
            'company_email' => ['type' => 'string', 'description' => 'Company contact email'],
            'company_phone' => ['type' => 'string', 'description' => 'Company contact phone'],
            'company_address' => ['type' => 'string', 'description' => 'Company physical address'],
            'company_website' => ['type' => 'string', 'description' => 'Company website URL'],
        ];

        foreach ($defaults as $key => $config) {
            if (isset($settings[$key])) {
                self::setValue($key, $settings[$key], $config['type'], $config['description']);
            }
        }

        return self::getCompanySettings();
    }

    /**
     * Save system appearance/color settings
     */
    public static function saveAppearanceSettings($settings)
    {
        $defaults = [
            'system_primary_color' => ['type' => 'string', 'description' => 'Primary brand color'],
            'system_secondary_color' => ['type' => 'string', 'description' => 'Secondary brand color'],
            'system_success_color' => ['type' => 'string', 'description' => 'Success/positive action color'],
            'system_info_color' => ['type' => 'string', 'description' => 'Information/notification color'],
            'system_warning_color' => ['type' => 'string', 'description' => 'Warning/alert color'],
            'system_danger_color' => ['type' => 'string', 'description' => 'Error/danger action color'],
            'system_sidebar_bg' => ['type' => 'string', 'description' => 'Sidebar background color'],
            'system_navbar_bg' => ['type' => 'string', 'description' => 'Navbar background color'],
            'system_footer_bg' => ['type' => 'string', 'description' => 'Footer background color'],
        ];

        foreach ($defaults as $key => $config) {
            $settingKey = str_replace('system_', '', $key);
            if (isset($settings[$settingKey])) {
                self::setValue($key, $settings[$settingKey], $config['type'], $config['description']);
            }
        }

        return self::getAppearanceSettings();
    }

    /**
     * Get CDN settings
     */
    public static function getCDNSettings()
    {
        try {
            return [
                'use_cdn' => self::getValue('cdn_use_cdn', true), // Default: true (use CDN)
            ];
        } catch (\Exception $e) {
            // If error occurs, return default CDN enabled
            return [
                'use_cdn' => true,
            ];
        }
    }

    /**
     * Save CDN settings
     */
    public static function saveCDNSettings($settings)
    {
        $defaults = [
            'use_cdn' => ['type' => 'boolean', 'description' => 'Use CDN for loading assets (CSS/JS libraries)'],
        ];

        foreach ($defaults as $key => $config) {
            if (isset($settings[$key])) {
                self::setValue('cdn_'.$key, $settings[$key], $config['type'], $config['description']);
            }
        }

        return self::getCDNSettings();
    }

    /**
     * Get module display settings for mobile and tablet
     */
    public static function getModuleDisplaySettings()
    {
        try {
            $defaultSettings = [
                'dashboard' => ['mobile' => true, 'tablet' => true],
                'booths' => ['mobile' => true, 'tablet' => true],
                'bookings' => ['mobile' => true, 'tablet' => true],
                'clients' => ['mobile' => true, 'tablet' => true],
                'settings' => ['mobile' => true, 'tablet' => true],
                'reports' => ['mobile' => false, 'tablet' => true],
                'finance' => ['mobile' => false, 'tablet' => true],
                'hr' => ['mobile' => false, 'tablet' => false],
                'users' => ['mobile' => false, 'tablet' => false],
                'categories' => ['mobile' => false, 'tablet' => false],
            ];

            $savedSettings = self::getValue('module_display_settings', json_encode($defaultSettings));

            if (is_string($savedSettings)) {
                $decoded = json_decode($savedSettings, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // Merge with defaults to ensure all modules are present
                    return array_merge($defaultSettings, $decoded);
                }
            }

            return $defaultSettings;
        } catch (\Exception $e) {
            // Return defaults on error
            return [
                'dashboard' => ['mobile' => true, 'tablet' => true],
                'booths' => ['mobile' => true, 'tablet' => true],
                'bookings' => ['mobile' => true, 'tablet' => true],
                'clients' => ['mobile' => true, 'tablet' => true],
                'settings' => ['mobile' => true, 'tablet' => true],
                'reports' => ['mobile' => false, 'tablet' => true],
                'finance' => ['mobile' => false, 'tablet' => true],
                'hr' => ['mobile' => false, 'tablet' => false],
                'users' => ['mobile' => false, 'tablet' => false],
                'categories' => ['mobile' => false, 'tablet' => false],
            ];
        }
    }

    /**
     * Save module display settings
     */
    public static function saveModuleDisplaySettings($settings)
    {
        try {
            // Validate structure
            $validated = [];
            $allowedModules = ['dashboard', 'booths', 'bookings', 'clients', 'settings', 'reports', 'finance', 'hr', 'users', 'categories'];

            foreach ($allowedModules as $module) {
                if (isset($settings[$module])) {
                    $validated[$module] = [
                        'mobile' => isset($settings[$module]['mobile']) ? (bool) $settings[$module]['mobile'] : true,
                        'tablet' => isset($settings[$module]['tablet']) ? (bool) $settings[$module]['tablet'] : true,
                    ];
                } else {
                    // Default to enabled if not provided
                    $validated[$module] = ['mobile' => true, 'tablet' => true];
                }
            }

            self::setValue(
                'module_display_settings',
                json_encode($validated),
                'json',
                'Module display settings for mobile and tablet devices'
            );

            return self::getModuleDisplaySettings();
        } catch (\Exception $e) {
            \Log::error('Error saving module display settings: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if a module should be displayed on a specific device
     */
    public static function isModuleVisible($module, $device = 'mobile')
    {
        try {
            $settings = self::getModuleDisplaySettings();
            if (isset($settings[$module])) {
                return $settings[$module][$device] ?? true;
            }

            return true; // Default to visible if module not found
        } catch (\Exception $e) {
            return true; // Default to visible on error
        }
    }
}
