<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Services\LandingTrackingService;
use Illuminate\Http\Request;

class LandingPageTrackingController extends Controller
{
    public function __construct(private readonly LandingTrackingService $tracking) {}

    public function track(LandingPage $landingPage, Request $request)
    {
        abort_unless($landingPage->is_published, 404);

        $validated = $request->validate([
            'event_type' => 'required|in:cta_click,form_view,lead_submit',
            'cta_label' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
        ]);

        $capture = $this->tracking->capture($landingPage, $request, $validated['event_type'], [
            'event_category' => 'engagement',
            'cta_label' => $validated['cta_label'] ?? null,
            'source' => $validated['source'] ?? null,
            'meta' => $validated['meta'] ?? [],
        ]);

        $response = response()->json(['ok' => true]);
        foreach ($capture['cookies'] as $cookie) {
            $response->withCookie($cookie);
        }

        return $response;
    }

    public function lead(LandingPage $landingPage, Request $request)
    {
        abort_unless($landingPage->is_published, 404);

        $validated = $request->validate([
            'lead_name' => 'nullable|string|max:255',
            'lead_email' => 'nullable|email|max:255',
            'lead_phone' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
        ]);

        $capture = $this->tracking->capture($landingPage, $request, 'lead_submit', [
            'event_category' => 'conversion',
            'lead_name' => $validated['lead_name'] ?? null,
            'lead_email' => $validated['lead_email'] ?? null,
            'lead_phone' => $validated['lead_phone'] ?? null,
            'source' => $validated['source'] ?? null,
            'meta' => $validated['meta'] ?? [],
        ]);

        $response = response()->json(['ok' => true]);
        foreach ($capture['cookies'] as $cookie) {
            $response->withCookie($cookie);
        }

        return $response;
    }

    public function analytics(LandingPage $landingPage)
    {
        abort_unless($landingPage->is_published, 404);

        return response()->json([
            'ok' => true,
            'data' => $this->tracking->summary($landingPage),
        ]);
    }
}
