<?php

namespace App\Http\Middleware;

use App\Models\LandingPage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Add security-related HTTP headers to responses.
     * Supports Month 1 checklist: HSTS, X-Frame-Options, X-Content-Type-Options, CSP.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add HSTS when running over HTTPS (e.g. production).
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Prevent clickjacking: disallow embedding in iframes (use 'SAMEORIGIN' if you need same-origin frames).
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME-type sniffing.
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // CSP: allow same origin, inline scripts, and CDNs used by adminlte/app (jsdelivr, cloudflare, jquery, ionicons, datatables, fonts).
        // font-src: ionicons are loaded from code.ionicframework.com when using CDN, or 'self' when using local vendor copy.
        // connect-src: allow CDNs for source-map requests (.map) and other fetch/XHR to same CDNs as script/style.
        // In local env only: allow debug ingest (e.g. 127.0.0.1:7244/7245) so CSP does not block it and clutter the console.
        // Tighten later with nonces/hashes if you remove 'unsafe-inline' / 'unsafe-eval'.
        $connectSrc = "'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.google-analytics.com https://www.googletagmanager.com";
        if (app()->environment('local')) {
            $connectSrc .= ' http://127.0.0.1:7244 http://127.0.0.1:7245 http://localhost:7244 http://localhost:7245';
        }
        $scriptSrc = "'self' 'unsafe-inline' 'unsafe-eval' blob: https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net";
        $scriptElemSrc = "'self' 'unsafe-inline' blob: https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.datatables.net";

        // Tighten CSP for public landing pages when inline scripts are disabled.
        if ($request->is('l/*')) {
            $slug = (string) $request->route('landingPage');
            $landingPage = $slug !== '' ? LandingPage::query()->where('slug', $slug)->first() : null;
            if ($landingPage && ! $landingPage->allow_inline_scripts) {
                $scriptSrc = "'self' blob: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com";
                $scriptElemSrc = "'self' blob: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com";
            }
        }

        $csp = "default-src 'self'; "
            ."script-src {$scriptSrc}; "
            ."script-src-elem {$scriptElemSrc}; "
            ."style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.ionicframework.com https://cdn.datatables.net https://fonts.googleapis.com; "
            ."img-src 'self' data: blob: https:; "
            ."font-src 'self' data: https://fonts.gstatic.com https://fonts.googleapis.com https://cdnjs.cloudflare.com https://code.ionicframework.com; "
            .'connect-src '.$connectSrc."; frame-ancestors 'self';";
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
