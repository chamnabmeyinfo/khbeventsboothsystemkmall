<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class DeviceDetector
{
    /**
     * Detect device type from request
     *
     * @return string 'mobile', 'tablet', or 'desktop'
     */
    public static function detect(Request $request): string
    {
        // Viewport-based: no redirects or URL params. Use X-Screen-Width header when present
        // (e.g. from AJAX), otherwise User-Agent.
        $screenWidth = $request->header('X-Screen-Width');
        if ($screenWidth && is_numeric($screenWidth)) {
            $width = (int) $screenWidth;
            if ($width <= 768) {
                return 'mobile';
            }
            if ($width <= 1024) {
                return 'tablet';
            }

            return 'desktop';
        }

        // User-Agent detection (no query params or cookies)
        $userAgent = $request->header('User-Agent', '');

        // Mobile detection - comprehensive patterns
        $mobilePatterns = [
            '/(android|webos|iphone|ipod|blackberry|iemobile|opera mini|mobile|palm|windows phone|windows mobile|symbian|nokia|sony|ericsson|motorola|samsung|lg|htc|huawei|xiaomi|oppo|vivo|oneplus|realme|poco|redmi)/i',
            '/Mobile|Android|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i',
        ];

        foreach ($mobilePatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                // Exclude iPad from mobile (it's a tablet)
                if (! preg_match('/iPad/i', $userAgent)) {
                    return 'mobile';
                }
            }
        }

        // Tablet detection - comprehensive patterns
        $tabletPatterns = [
            '/(ipad|tablet|playbook|silk|kindle|nook|nexus 7|nexus 10|galaxy tab|surface|windows rt)/i',
        ];

        foreach ($tabletPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return 'tablet';
            }
        }

        // Check viewport meta tag or screen size hints
        // This is a fallback - default to desktop but allow override
        return 'desktop';
    }

    /**
     * Check if device is mobile
     */
    public static function isMobile(Request $request): bool
    {
        return self::detect($request) === 'mobile';
    }

    /**
     * Check if device is tablet
     */
    public static function isTablet(Request $request): bool
    {
        return self::detect($request) === 'tablet';
    }

    /**
     * Check if device is desktop
     */
    public static function isDesktop(Request $request): bool
    {
        return self::detect($request) === 'desktop';
    }

    /**
     * Get view name based on device type
     *
     * @param  string  $baseView  Base view name (e.g., 'dashboard.index')
     * @return string View name (e.g., 'dashboard.index-mobile' or 'dashboard.index-adminlte')
     */
    public static function getViewName(string $baseView, Request $request): string
    {
        $device = self::detect($request);

        // For mobile, return mobile-specific view
        if ($device === 'mobile') {
            return $baseView.'-mobile';
        }

        // For tablet, can return tablet-specific view or mobile view
        if ($device === 'tablet') {
            return $baseView.'-mobile'; // Use mobile view for tablet for now
        }

        // For desktop, return the adminlte view (existing desktop view)
        return $baseView.'-adminlte';
    }
}
