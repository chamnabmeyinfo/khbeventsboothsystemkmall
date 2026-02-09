<?php

namespace App\Helpers;

use App\Models\Setting;

class AssetHelper
{
    /**
     * Get asset URL - CDN or local based on settings
     */
    public static function asset($path, $cdnUrl = null)
    {
        $cdnSettings = Setting::getCDNSettings();
        $useCDN = $cdnSettings['use_cdn'] ?? false;

        if ($useCDN && $cdnUrl) {
            return $cdnUrl;
        }

        return asset($path);
    }

    /**
     * Get Chart.js URL
     */
    public static function chartJs()
    {
        return self::asset(
            'vendor/chartjs/chart.umd.min.js',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js'
        );
    }

    /**
     * Get Chart.js CSS URL
     */
    public static function chartJsCSS()
    {
        return self::asset(
            'vendor/chartjs/chart.min.css',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css'
        );
    }
}
