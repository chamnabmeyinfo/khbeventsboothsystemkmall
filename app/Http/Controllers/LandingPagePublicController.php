<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\LandingPageEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class LandingPagePublicController extends Controller
{
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

        LandingPageEvent::create([
            'landing_page_id' => $landingPage->id,
            'event_type' => 'view',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 65000, ''),
            'referrer_url' => Str::limit((string) $request->headers->get('referer'), 65535, ''),
            'meta' => [
                'path' => $request->path(),
                'query' => $request->query(),
            ],
        ]);

        return $response;
    }

    public function continue(LandingPage $landingPage, Request $request)
    {
        abort_unless($landingPage->is_published, 404);

        $target = trim((string) ($request->input('target') ?: $landingPage->redirect_url ?: '/login'));
        if ($target === '' || (! Str::startsWith($target, '/') && ! Str::startsWith($target, ['http://', 'https://']))) {
            $target = '/login';
        }

        LandingPageEvent::create([
            'landing_page_id' => $landingPage->id,
            'event_type' => 'continue',
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 65000, ''),
            'referrer_url' => Str::limit((string) $request->headers->get('referer'), 65535, ''),
            'meta' => ['target' => $target],
        ]);

        $response = redirect()->to($target);
        $response->withCookie(Cookie::make('landing_page_seen', '1', 60 * 24 * 365, null, null, false, true, false, 'Lax'));
        $request->session()->put('landing_page_seen', true);

        return $response;
    }
}
