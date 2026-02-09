<?php

namespace App\Helpers;

use App\Models\Setting;

class AssetHelper
{
    /**
     * Resolve a safe URL for an image path (avatar, cover, logo, etc.).
     * Returns null for empty/invalid path so views can show a placeholder.
     * Normalizes path and uses current app URL so images load correctly.
     *
     * @param  string|null  $path  Relative path (e.g. images/avatars/user/xxx.jpg) or full URL
     * @return string|null Full URL or null
     */
    public static function imageUrl(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $path = trim($path);
        $path = str_replace('\\', '/', $path);

        // Already a full URL
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        // Trim leading slash so asset() builds URL correctly (avoid double slash)
        $path = ltrim($path, '/');

        if ($path === '') {
            return null;
        }

        return asset($path);
    }

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
