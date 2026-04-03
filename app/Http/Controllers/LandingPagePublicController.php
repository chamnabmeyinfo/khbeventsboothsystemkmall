<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Services\LandingTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class LandingPagePublicController extends Controller
{
    public function __construct(private readonly LandingTrackingService $tracking) {}

    public function show(LandingPage $landingPage, Request $request)
    {
        abort_unless($landingPage->is_published, 404);

        $currentLocale = $landingPage->resolveLocaleForRequest($request);
        $visual = $landingPage->visualForLocale($currentLocale);
        $enabledLocales = $landingPage->enabledLocaleList();
        $localeLabels = config('landing_locales.allowed');
        if (! is_array($localeLabels) || $localeLabels === []) {
            $localeLabels = [
                'en' => 'English',
                'km' => 'ខ្មែរ (Khmer)',
                'zh' => '中文 (Chinese)',
            ];
        }
        $langSwitcherUrls = [];
        foreach ($enabledLocales as $loc) {
            $langSwitcherUrls[$loc] = $request->fullUrlWithQuery(['lang' => $loc]);
        }
        $documentTitle = $landingPage->headline ?: ($visual['hero_title'] ?? $landingPage->name);

        $seo = $landingPage->buildSeoMeta($currentLocale, $visual, $enabledLocales, [
            'force_noindex' => $request->boolean('editor'),
        ]);

        $response = response()->view('landing-pages.public-show', compact(
            'landingPage',
            'visual',
            'currentLocale',
            'enabledLocales',
            'localeLabels',
            'langSwitcherUrls',
            'documentTitle',
            'seo'
        ));

        if ($landingPage->show_once_mode === 'session_once') {
            $request->session()->put('landing_page_seen', true);
        } else {
            $response->withCookie(Cookie::make('landing_page_seen', '1', 60 * 24 * 365, null, null, false, true, false, 'Lax'));
            $request->session()->put('landing_page_seen', true);
        }

        $capture = $this->tracking->capture($landingPage, $request, 'view', [
            'event_category' => 'impression',
            'source' => 'landing-page',
            'event_payload' => [
                'path' => $request->path(),
                'query' => $request->query(),
            ],
        ]);
        foreach ($capture['cookies'] as $cookie) {
            $response->withCookie($cookie);
        }

        $response->withCookie(Cookie::make(
            $landingPage->langCookieName(),
            $currentLocale,
            60 * 24 * 365,
            null,
            null,
            false,
            true,
            false,
            'Lax'
        ));

        return $response;
    }

    /**
     * Post-submission thank-you page (after CTA / lead flows).
     */
    public function thankYou(LandingPage $landingPage, Request $request)
    {
        abort_unless($landingPage->is_published, 404);

        $currentLocale = $landingPage->resolveLocaleForRequest($request);
        $visual = $landingPage->visualForLocale($currentLocale);
        $documentTitle = __('Thank you').' — '.($landingPage->headline ?: ($visual['hero_title'] ?? $landingPage->name));

        $enabledLocales = $landingPage->enabledLocaleList();
        $defLoc = strtolower(trim((string) ($landingPage->default_locale ?: 'en')));
        $baseThank = route('landing-pages.public.thank-you', $landingPage, true);
        $alternates = [];
        foreach ($enabledLocales as $loc) {
            $loc = strtolower(trim((string) $loc));
            if ($loc === '') {
                continue;
            }
            $alternates[$loc] = $loc === $defLoc ? $baseThank : $baseThank.'?lang='.$loc;
        }
        $canonicalThank = $alternates[$currentLocale] ?? $baseThank;
        $xDefaultUrl = $alternates[$defLoc] ?? ($alternates['en'] ?? (reset($alternates) ?: $baseThank));

        $thxDesc = trim(preg_replace('/\s+/u', ' ', strip_tags($documentTitle)));
        $seo = $landingPage->buildSeoMeta($currentLocale, $visual, $enabledLocales, [
            'force_noindex' => true,
            'title' => $documentTitle,
            'description' => Str::limit($thxDesc, 160, ''),
            'canonical_url' => $canonicalThank,
            'alternates' => $alternates,
            'x_default_url' => $xDefaultUrl,
        ]);

        $capture = $this->tracking->capture($landingPage, $request, 'thank_you', [
            'event_category' => 'conversion',
            'source' => 'thank-you-page',
            'event_payload' => [
                'path' => $request->path(),
            ],
        ]);

        $response = response()->view('landing-pages.thank-you', [
            'landingPage' => $landingPage,
            'visual' => $visual,
            'currentLocale' => $currentLocale,
            'documentTitle' => $documentTitle,
            'seo' => $seo,
        ]);

        foreach ($capture['cookies'] as $cookie) {
            $response->withCookie($cookie);
        }

        return $response;
    }

    public function continue(LandingPage $landingPage, Request $request)
    {
        abort_unless($landingPage->is_published, 404);

        $target = trim((string) ($request->input('target') ?: $landingPage->redirect_url ?: '/login'));
        if ($target === '' || (! Str::startsWith($target, '/') && ! Str::startsWith($target, ['http://', 'https://']))) {
            $target = '/login';
        }

        // Visual template: send visitors to thank-you instead of login after Continue (unless admin set another URL).
        $loginPath = url('/login');
        if ($landingPage->use_visual_builder && ($landingPage->template_key === 'canton_fair_visual')) {
            if ($target === '/login' || $target === $loginPath) {
                $target = route('landing-pages.public.thank-you', $landingPage, false);
            }
        }

        $capture = $this->tracking->capture($landingPage, $request, 'continue', [
            'event_category' => 'conversion',
            'source' => 'continue-action',
            'event_payload' => ['target' => $target],
        ]);

        $response = redirect()->to($target);
        $response->withCookie(Cookie::make('landing_page_seen', '1', 60 * 24 * 365, null, null, false, true, false, 'Lax'));
        $request->session()->put('landing_page_seen', true);
        foreach ($capture['cookies'] as $cookie) {
            $response->withCookie($cookie);
        }

        return $response;
    }
}
