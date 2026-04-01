<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\LandingPageEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LandingPageTrackingController extends Controller
{
    public function track(LandingPage $landingPage, Request $request)
    {
        abort_unless($landingPage->is_published, 404);

        $validated = $request->validate([
            'event_type' => 'required|in:cta_click,form_view,lead_submit',
            'cta_label' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'meta' => 'nullable|array',
        ]);

        LandingPageEvent::create([
            'landing_page_id' => $landingPage->id,
            'event_type' => $validated['event_type'],
            'cta_label' => $validated['cta_label'] ?? null,
            'source' => $validated['source'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 65000, ''),
            'referrer_url' => Str::limit((string) $request->headers->get('referer'), 65535, ''),
            'meta' => $validated['meta'] ?? [],
        ]);

        return response()->json(['ok' => true]);
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

        LandingPageEvent::create([
            'landing_page_id' => $landingPage->id,
            'event_type' => 'lead_submit',
            'lead_name' => $validated['lead_name'] ?? null,
            'lead_email' => $validated['lead_email'] ?? null,
            'lead_phone' => $validated['lead_phone'] ?? null,
            'source' => $validated['source'] ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 65000, ''),
            'referrer_url' => Str::limit((string) $request->headers->get('referer'), 65535, ''),
            'meta' => $validated['meta'] ?? [],
        ]);

        return response()->json(['ok' => true]);
    }
}
