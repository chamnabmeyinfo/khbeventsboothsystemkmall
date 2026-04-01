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

        $response = response()->view('landing-pages.public-show', compact('landingPage'));

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

        return $response;
    }

    public function continue(LandingPage $landingPage, Request $request)
    {
        abort_unless($landingPage->is_published, 404);

        $target = trim((string) ($request->input('target') ?: $landingPage->redirect_url ?: '/login'));
        if ($target === '' || (! Str::startsWith($target, '/') && ! Str::startsWith($target, ['http://', 'https://']))) {
            $target = '/login';
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
