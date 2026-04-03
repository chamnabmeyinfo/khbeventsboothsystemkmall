<?php

namespace App\Services;

use App\Models\LandingPage;
use App\Models\LandingTrackingEvent;
use App\Models\LandingTrackingVisitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;

class LandingTrackingService
{
    private const VISITOR_COOKIE = 'lp_vid';

    private const SESSION_COOKIE = 'lp_sid';

    /**
     * @return array{event: LandingTrackingEvent, cookies: array<int, SymfonyCookie>}
     */
    public function capture(LandingPage $landingPage, Request $request, string $eventName, array $payload = []): array
    {
        [$visitor, $visitorCookie] = $this->resolveVisitor($request);
        [$sessionUuid, $sessionCookie] = $this->resolveSession($request);
        $utm = $this->extractUtm($request, $payload);
        $referrer = Str::limit((string) $request->headers->get('referer'), 65535, '');
        $ipAddress = $request->ip();
        $userAgent = Str::limit((string) $request->userAgent(), 65000, '');

        $this->updateVisitorLastSeen($visitor, $utm, $ipAddress, $userAgent, $referrer);

        $event = LandingTrackingEvent::create([
            'landing_page_id' => $landingPage->id,
            'visitor_id' => $visitor->id,
            'session_uuid' => $sessionUuid,
            'event_name' => $eventName,
            'event_category' => $payload['event_category'] ?? 'landing',
            'cta_label' => $payload['cta_label'] ?? null,
            'source' => $payload['source'] ?? null,
            'lead_name' => $payload['lead_name'] ?? null,
            'lead_email' => $payload['lead_email'] ?? null,
            'lead_phone' => $payload['lead_phone'] ?? null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'referrer_url' => $referrer,
            'utm_source' => $utm['utm_source'],
            'utm_medium' => $utm['utm_medium'],
            'utm_campaign' => $utm['utm_campaign'],
            'utm_term' => $utm['utm_term'],
            'utm_content' => $utm['utm_content'],
            'event_payload' => $payload['event_payload'] ?? ($payload['meta'] ?? []),
            'occurred_at' => now(),
        ]);

        $cookies = [];
        if ($visitorCookie) {
            $cookies[] = $visitorCookie;
        }
        if ($sessionCookie) {
            $cookies[] = $sessionCookie;
        }

        return ['event' => $event, 'cookies' => $cookies];
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(LandingPage $landingPage): array
    {
        $events = LandingTrackingEvent::query()->where('landing_page_id', $landingPage->id);

        $views = (clone $events)->where('event_name', 'view')->count();
        $ctaClicks = (clone $events)->where('event_name', 'cta_click')->count();
        $leads = (clone $events)->where('event_name', 'lead_submit')->count();
        $continues = (clone $events)->where('event_name', 'continue')->count();
        $uniqueVisitors = (clone $events)->distinct('visitor_id')->count('visitor_id');

        $conversionRate = $views > 0 ? round(($leads / $views) * 100, 2) : 0.0;

        $bySource = (clone $events)
            ->selectRaw('COALESCE(source, "unknown") as source_key, COUNT(*) as total')
            ->groupBy('source_key')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'landing_page_id' => $landingPage->id,
            'slug' => $landingPage->slug,
            'views' => $views,
            'cta_clicks' => $ctaClicks,
            'leads' => $leads,
            'continues' => $continues,
            'thank_you' => (clone $events)->where('event_name', 'thank_you')->count(),
            'unique_visitors' => $uniqueVisitors,
            'lead_conversion_rate_percent' => $conversionRate,
            'top_sources' => $bySource,
        ];
    }

    /**
     * Full analytics for admin: funnel, time series, UTM, recent activity.
     *
     * @return array<string, mixed>
     */
    public function analyticsReport(LandingPage $landingPage, int $days = 30, int $recentLimit = 75): array
    {
        $days = max(7, min(365, $days));
        $recentLimit = max(10, min(200, $recentLimit));

        $summary = $this->summary($landingPage);
        $since = now()->subDays($days)->startOfDay();

        $base = LandingTrackingEvent::query()
            ->where('landing_page_id', $landingPage->id)
            ->where('occurred_at', '>=', $since);

        $byEventName = (clone $base)
            ->selectRaw('event_name, COUNT(*) as total')
            ->groupBy('event_name')
            ->orderByDesc('total')
            ->get();

        $dateExpr = $this->sqlDateExpressionForOccurredAt();

        $dailyRows = LandingTrackingEvent::query()
            ->where('landing_page_id', $landingPage->id)
            ->where('occurred_at', '>=', $since)
            ->selectRaw(
                "{$dateExpr} as day, SUM(CASE WHEN event_name = 'view' THEN 1 ELSE 0 END) as views, COUNT(*) as total_events"
            )
            ->groupBy(DB::raw($dateExpr))
            ->orderBy('day')
            ->get();

        $dailyActivity = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $dailyActivity[$d] = ['views' => 0, 'total_events' => 0];
        }
        foreach ($dailyRows as $row) {
            $d = $this->normalizeDayKey($row->day ?? null);
            if ($d !== null && isset($dailyActivity[$d])) {
                $dailyActivity[$d] = [
                    'views' => (int) ($row->views ?? 0),
                    'total_events' => (int) ($row->total_events ?? 0),
                ];
            }
        }

        $utmSources = (clone $base)
            ->whereNotNull('utm_source')
            ->where('utm_source', '!=', '')
            ->selectRaw('utm_source, COUNT(*) as total')
            ->groupBy('utm_source')
            ->orderByDesc('total')
            ->limit(12)
            ->get();

        $utmCampaigns = (clone $base)
            ->whereNotNull('utm_campaign')
            ->where('utm_campaign', '!=', '')
            ->selectRaw('utm_campaign, COUNT(*) as total')
            ->groupBy('utm_campaign')
            ->orderByDesc('total')
            ->limit(12)
            ->get();

        $topSourcesPeriod = (clone $base)
            ->selectRaw("COALESCE(source, 'unknown') as source_key, COUNT(*) as total")
            ->groupBy(DB::raw("COALESCE(source, 'unknown')"))
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $recentEvents = LandingTrackingEvent::query()
            ->where('landing_page_id', $landingPage->id)
            ->orderByDesc('occurred_at')
            ->limit($recentLimit)
            ->get([
                'id',
                'occurred_at',
                'event_name',
                'event_category',
                'source',
                'cta_label',
                'visitor_id',
                'ip_address',
                'utm_source',
                'utm_medium',
                'utm_campaign',
            ]);

        return [
            'summary' => $summary,
            'period_days' => $days,
            'period_start' => $since->toIso8601String(),
            'by_event_name' => $byEventName,
            'daily_activity' => $dailyActivity,
            'utm_top_sources' => $utmSources,
            'utm_top_campaigns' => $utmCampaigns,
            'top_sources_period' => $topSourcesPeriod,
            'recent_events' => $recentEvents,
        ];
    }

    private function sqlDateExpressionForOccurredAt(): string
    {
        $driver = LandingTrackingEvent::query()->getConnection()->getDriverName();

        return match ($driver) {
            'sqlite' => "date(occurred_at)",
            default => 'DATE(occurred_at)',
        };
    }

    private function normalizeDayKey(mixed $day): ?string
    {
        if ($day === null) {
            return null;
        }
        if ($day instanceof \DateTimeInterface) {
            return $day->format('Y-m-d');
        }
        $s = (string) $day;

        return strlen($s) >= 10 ? substr($s, 0, 10) : $s;
    }

    /**
     * @return array{0: LandingTrackingVisitor, 1: SymfonyCookie|null}
     */
    private function resolveVisitor(Request $request): array
    {
        $visitorUuid = $request->cookie(self::VISITOR_COOKIE);
        $newCookie = null;

        if (! is_string($visitorUuid) || ! Str::isUuid($visitorUuid)) {
            $visitorUuid = (string) Str::uuid();
            $newCookie = Cookie::make(self::VISITOR_COOKIE, $visitorUuid, 60 * 24 * 365, null, null, false, true, false, 'Lax');
        }

        $utm = $this->extractUtm($request, []);
        $referrer = Str::limit((string) $request->headers->get('referer'), 65535, '');
        $ipAddress = $request->ip();
        $userAgent = Str::limit((string) $request->userAgent(), 65000, '');

        $visitor = LandingTrackingVisitor::firstOrCreate(
            ['visitor_uuid' => $visitorUuid],
            [
                'first_seen_at' => now(),
                'last_seen_at' => now(),
                'first_ip_address' => $ipAddress,
                'last_ip_address' => $ipAddress,
                'first_user_agent' => $userAgent,
                'last_user_agent' => $userAgent,
                'first_referrer_url' => $referrer,
                'last_referrer_url' => $referrer,
                'first_utm_source' => $utm['utm_source'],
                'first_utm_medium' => $utm['utm_medium'],
                'first_utm_campaign' => $utm['utm_campaign'],
                'first_utm_term' => $utm['utm_term'],
                'first_utm_content' => $utm['utm_content'],
                'last_utm_source' => $utm['utm_source'],
                'last_utm_medium' => $utm['utm_medium'],
                'last_utm_campaign' => $utm['utm_campaign'],
                'last_utm_term' => $utm['utm_term'],
                'last_utm_content' => $utm['utm_content'],
            ]
        );

        return [$visitor, $newCookie];
    }

    /**
     * @return array{0: string, 1: SymfonyCookie|null}
     */
    private function resolveSession(Request $request): array
    {
        $sessionUuid = $request->cookie(self::SESSION_COOKIE);
        $newCookie = null;

        if (! is_string($sessionUuid) || ! Str::isUuid($sessionUuid)) {
            $sessionUuid = (string) Str::uuid();
            $newCookie = Cookie::make(self::SESSION_COOKIE, $sessionUuid, 60 * 24, null, null, false, true, false, 'Lax');
        }

        return [$sessionUuid, $newCookie];
    }

    /**
     * @return array{utm_source: ?string, utm_medium: ?string, utm_campaign: ?string, utm_term: ?string, utm_content: ?string}
     */
    private function extractUtm(Request $request, array $payload): array
    {
        $meta = is_array($payload['meta'] ?? null) ? $payload['meta'] : [];

        return [
            'utm_source' => $request->query('utm_source', $meta['utm_source'] ?? null),
            'utm_medium' => $request->query('utm_medium', $meta['utm_medium'] ?? null),
            'utm_campaign' => $request->query('utm_campaign', $meta['utm_campaign'] ?? null),
            'utm_term' => $request->query('utm_term', $meta['utm_term'] ?? null),
            'utm_content' => $request->query('utm_content', $meta['utm_content'] ?? null),
        ];
    }

    /**
     * @param  array{utm_source: ?string, utm_medium: ?string, utm_campaign: ?string, utm_term: ?string, utm_content: ?string}  $utm
     */
    private function updateVisitorLastSeen(LandingTrackingVisitor $visitor, array $utm, ?string $ipAddress, string $userAgent, string $referrer): void
    {
        $visitor->update([
            'last_seen_at' => now(),
            'last_ip_address' => $ipAddress,
            'last_user_agent' => $userAgent,
            'last_referrer_url' => $referrer,
            'last_utm_source' => $utm['utm_source'],
            'last_utm_medium' => $utm['utm_medium'],
            'last_utm_campaign' => $utm['utm_campaign'],
            'last_utm_term' => $utm['utm_term'],
            'last_utm_content' => $utm['utm_content'],
        ]);
    }
}
