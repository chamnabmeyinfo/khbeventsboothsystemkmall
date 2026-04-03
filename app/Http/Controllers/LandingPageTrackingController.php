<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use App\Models\LandingPageLead;
use App\Services\LandingTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LandingPageTrackingController extends Controller
{
    public function __construct(private readonly LandingTrackingService $tracking) {}

    public function track(LandingPage $landingPage, Request $request)
    {
        abort_unless($landingPage->is_published, 404);

        $validated = $request->validate([
            'event_type' => 'required|in:cta_click,form_view,lead_submit,lang_switch,lang_welcome_shown,lang_welcome_dismiss',
            'cta_label' => 'nullable|string|max:255',
            'source' => 'nullable|string|max:255',
            'lang' => 'nullable|string|max:32',
            'meta' => 'nullable|array',
        ]);

        $meta = array_merge($validated['meta'] ?? [], array_filter([
            'lang' => $validated['lang'] ?? null,
        ]));

        $capture = $this->tracking->capture($landingPage, $request, $validated['event_type'], [
            'event_category' => 'engagement',
            'cta_label' => $validated['cta_label'] ?? null,
            'source' => $validated['source'] ?? null,
            'meta' => $meta,
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
            'meta.tripDate' => 'nullable|string|max:500',
            'meta.phase_label' => 'nullable|string|max:255',
            'meta.locale' => 'nullable|string|max:32',
        ]);

        $meta = $validated['meta'] ?? [];
        $locale = $meta['locale'] ?? (is_string($request->query('lang')) ? strtolower(trim((string) $request->query('lang'))) : null);

        $capture = $this->tracking->capture($landingPage, $request, 'lead_submit', [
            'event_category' => 'conversion',
            'lead_name' => $validated['lead_name'] ?? null,
            'lead_email' => $validated['lead_email'] ?? null,
            'lead_phone' => $validated['lead_phone'] ?? null,
            'source' => $validated['source'] ?? null,
            'meta' => $meta,
        ]);

        $event = $capture['event'];
        try {
            LandingPageLead::create([
                'landing_page_id' => $landingPage->id,
                'landing_tracking_event_id' => $event->id,
                'visitor_id' => $event->visitor_id,
                'session_uuid' => $event->session_uuid,
                'name' => $validated['lead_name'] ?? null,
                'email' => $validated['lead_email'] ?? null,
                'phone' => $validated['lead_phone'] ?? null,
                'preferred_trip_date' => isset($meta['tripDate']) ? trim((string) $meta['tripDate']) : null,
                'locale' => $locale,
                'source' => $validated['source'] ?? null,
                'meta' => $meta,
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 65000, ''),
                'referrer_url' => Str::limit((string) $request->headers->get('referer'), 65535, ''),
            ]);
        } catch (\Throwable $e) {
            Log::error('Landing page lead record failed', [
                'landing_page_id' => $landingPage->id,
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Could not save your submission. Please try again later.',
            ], 503);
        }

        $response = response()->json(['ok' => true]);
        foreach ($capture['cookies'] as $cookie) {
            $response->withCookie($cookie);
        }

        return $response;
    }

    public function analytics(Request $request, LandingPage $landingPage)
    {
        $days = max(7, min(365, (int) $request->query('days', 30)));
        $report = $this->tracking->analyticsReport($landingPage, $days);

        if ($request->wantsJson() || $request->query('format') === 'json') {
            $summary = $report['summary'];
            unset($report['summary']);

            return response()->json([
                'ok' => true,
                'data' => array_merge($summary, [
                    'analytics' => $report,
                ]),
            ]);
        }

        return view('landing-pages.analytics', compact('landingPage', 'report'));
    }

    /**
     * Admin: delete tracking events for this landing page only (day, month, or year).
     */
    public function analyticsClear(Request $request, LandingPage $landingPage)
    {
        $validated = $request->validate([
            'scope' => 'required|in:day,month,year',
            'day' => 'required_if:scope,day|nullable|date_format:Y-m-d',
            'month' => 'required_if:scope,month|nullable|date_format:Y-m',
            'year' => 'required_if:scope,year|nullable|integer|min:2000|max:2100',
            'confirm' => 'required|accepted',
        ]);

        $value = match ($validated['scope']) {
            'day' => $validated['day'],
            'month' => $validated['month'],
            'year' => (string) $validated['year'],
        };

        $deleted = $this->tracking->purgeTrackingEvents($landingPage->id, $validated['scope'], $value);

        return redirect()->route('landing-pages.analytics', $landingPage)
            ->with('success', "Deleted {$deleted} tracking event(s) for this page in the selected period. Marketing lead records were not removed.");
    }
}
