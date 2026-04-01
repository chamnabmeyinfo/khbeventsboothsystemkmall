<?php

namespace App\Http\Middleware;

use App\Models\LandingPage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyLandingPageGate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldGateRequest($request)) {
            return $next($request);
        }

        if ($this->alreadySeenLanding($request)) {
            return $next($request);
        }

        $activeLanding = LandingPage::getActivePublished();
        if (! $activeLanding) {
            return $next($request);
        }

        return redirect()->route('landing-pages.public.show', $activeLanding);
    }

    private function shouldGateRequest(Request $request): bool
    {
        if (! $request->isMethod('GET') || $request->expectsJson()) {
            return false;
        }

        if ($request->user()) {
            return false;
        }

        if (! in_array($request->path(), ['/', 'login'], true)) {
            return false;
        }

        return true;
    }

    private function alreadySeenLanding(Request $request): bool
    {
        if ($request->cookie('landing_page_seen') === '1') {
            return true;
        }

        return (bool) $request->session()->get('landing_page_seen', false);
    }
}
