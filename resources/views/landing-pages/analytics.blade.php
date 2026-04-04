@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@php
    $s = $report['summary'] ?? [];
    $days = (int) ($report['period_days'] ?? 30);
    $eventLabels = [
        'view' => 'Page view',
        'thank_you' => 'Thank-you page',
        'continue' => 'Continue / redirect',
        'lead_submit' => 'Lead submit',
        'cta_click' => 'CTA click',
        'form_view' => 'Form view',
        'lang_switch' => 'Language switch',
        'lang_welcome_shown' => 'Language welcome shown',
        'lang_welcome_dismiss' => 'Language welcome dismissed',
    ];
@endphp

@section('title', 'Analytics — '.$landingPage->name)
@section('page-title', 'Visitor analytics: '.$landingPage->name)
@section('breadcrumb', 'Landing Pages / Analytics')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Analytics: {{ $landingPage->name }}</h1>
            <p>Visitor funnel, sources, and recent events.</p>
        </div>
        <div class="looker-actions flex-wrap align-items-center">
            <form method="get" action="{{ route('landing-pages.analytics', $landingPage) }}" class="d-flex align-items-center flex-wrap mb-2 mb-md-0 mr-md-2">
                <label for="lp_an_days" class="small text-muted mb-0 mr-2">Period</label>
                <select name="days" id="lp_an_days" class="form-control form-control-sm lp-an-period-select" onchange="this.form.submit()">
                    @foreach([7 => '7 days', 14 => '14 days', 30 => '30 days', 60 => '60 days', 90 => '90 days'] as $v => $label)
                        <option value="{{ $v }}" {{ $days === $v ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('landing-pages.reporting', $landingPage) }}" class="action-btn action-btn-secondary">Leads</a>
            <a href="{{ route('landing-pages.analytics.index') }}" class="action-btn action-btn-secondary">All pages</a>
            <a href="{{ route('landing-pages.edit', $landingPage) }}" class="action-btn action-btn-primary">Edit page</a>
            @if($landingPage->is_published)
                <a href="{{ route('landing-pages.public.show', $landingPage) }}" class="action-btn action-btn-secondary" target="_blank" rel="noopener">Public URL</a>
            @endif
        </div>
    </header>

    <div class="lp-callout">
        <strong>Visitor &amp; engagement analytics.</strong>
        Data comes from the public page (<code>/l/{{ $landingPage->slug }}</code>) via the tracking endpoint. JSON API:
        <code>{{ route('landing-pages.analytics', $landingPage) }}?format=json</code>
        (same summary keys as before, plus <code>analytics</code> for detail).
    </div>

    <div class="canvas-panel mb-3">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-chart-area" aria-hidden="true"></i> Overview</h2>
        </div>
        <p class="text-muted small mb-3">
            Funnel metrics below are <strong>all-time</strong> (stored totals). Charts and UTM tables use the selected period (last {{ $days }} days).
        </p>
        <div class="kpi-wrapper lp-kpi-nudge">
            <div class="kpi-card-looker">
                <div class="kpi-top">
                    <div class="kpi-title">Page views</div>
                    <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-eye" aria-hidden="true"></i></div>
                </div>
                <div class="kpi-value-looker">{{ number_format((int) ($s['views'] ?? 0)) }}</div>
            </div>
            <div class="kpi-card-looker success">
                <div class="kpi-top">
                    <div class="kpi-title">Unique visitors</div>
                    <div class="kpi-icon-wrapper success-icon"><i class="fas fa-users" aria-hidden="true"></i></div>
                </div>
                <div class="kpi-value-looker">{{ number_format((int) ($s['unique_visitors'] ?? 0)) }}</div>
            </div>
            <div class="kpi-card-looker purple">
                <div class="kpi-top">
                    <div class="kpi-title">Leads</div>
                    <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-address-book" aria-hidden="true"></i></div>
                </div>
                <div class="kpi-value-looker">{{ number_format((int) ($s['leads'] ?? 0)) }}</div>
            </div>
            <div class="kpi-card-looker warning">
                <div class="kpi-top">
                    <div class="kpi-title">CTA clicks</div>
                    <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-mouse-pointer" aria-hidden="true"></i></div>
                </div>
                <div class="kpi-value-looker">{{ number_format((int) ($s['cta_clicks'] ?? 0)) }}</div>
            </div>
            <div class="kpi-card-looker">
                <div class="kpi-top">
                    <div class="kpi-title">Continue</div>
                    <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-arrow-right" aria-hidden="true"></i></div>
                </div>
                <div class="kpi-value-looker">{{ number_format((int) ($s['continues'] ?? 0)) }}</div>
            </div>
            <div class="kpi-card-looker success">
                <div class="kpi-top">
                    <div class="kpi-title">Thank-you</div>
                    <div class="kpi-icon-wrapper success-icon"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
                </div>
                <div class="kpi-value-looker">{{ number_format((int) ($s['thank_you'] ?? 0)) }}</div>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-12 col-md-6">
                <div class="small text-muted">Lead conversion (leads ÷ views)</div>
                <div class="h5 mb-0">{{ number_format((float) ($s['lead_conversion_rate_percent'] ?? 0), 2) }}%</div>
            </div>
        </div>
    </div>

    @include('landing-pages.partials.analytics-clear-form', [
        'actionUrl' => route('landing-pages.analytics.clear-page', $landingPage),
        'showPageSelector' => false,
        'formId' => 'lp-an-clear-single',
    ])

    <div class="data-canvas-grid">
        <div class="col-span-6 canvas-panel">
            <div class="panel-header">
                <h2 class="panel-title">Events by type (period)</h2>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th class="text-right">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['by_event_name'] ?? [] as $row)
                            <tr>
                                <td>{{ $eventLabels[$row->event_name] ?? $row->event_name }}</td>
                                <td class="text-right">{{ number_format((int) $row->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">No events in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-span-6 canvas-panel">
            <div class="panel-header">
                <h2 class="panel-title">Top sources (period)</h2>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th class="text-right">Events</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['top_sources_period'] ?? [] as $row)
                            <tr>
                                <td><code>{{ $row->source_key }}</code></td>
                                <td class="text-right">{{ number_format((int) $row->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">No source data in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="data-canvas-grid">
        <div class="col-span-6 canvas-panel">
            <div class="panel-header">
                <h2 class="panel-title">UTM sources (period)</h2>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th>utm_source</th>
                            <th class="text-right">Events</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['utm_top_sources'] ?? [] as $row)
                            <tr>
                                <td>{{ $row->utm_source }}</td>
                                <td class="text-right">{{ number_format((int) $row->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">No UTM source data in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-span-6 canvas-panel">
            <div class="panel-header">
                <h2 class="panel-title">UTM campaigns (period)</h2>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th>utm_campaign</th>
                            <th class="text-right">Events</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['utm_top_campaigns'] ?? [] as $row)
                            <tr>
                                <td>{{ $row->utm_campaign }}</td>
                                <td class="text-right">{{ number_format((int) $row->total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">No UTM campaign data in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="canvas-panel mb-3">
        <div class="panel-header">
            <h2 class="panel-title">Daily activity (period) — views vs all events</h2>
        </div>
        <div class="looker-table-wrapper">
            <table class="looker-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th class="text-right">Views</th>
                        <th class="text-right">All events</th>
                        <th style="min-width: 180px;">Views (bar)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $daily = $report['daily_activity'] ?? [];
                        $maxViews = max(1, (int) collect($daily)->max(fn ($x) => $x['views'] ?? 0));
                    @endphp
                    @foreach($daily as $date => $counts)
                        @php
                            $v = (int) ($counts['views'] ?? 0);
                            $pct = round(($v / $maxViews) * 100);
                        @endphp
                        <tr>
                            <td class="text-nowrap">{{ $date }}</td>
                            <td class="text-right">{{ number_format($v) }}</td>
                            <td class="text-right">{{ number_format((int) ($counts['total_events'] ?? 0)) }}</td>
                            <td>
                                <div class="progress lp-progress-thin">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="canvas-panel mb-3">
        <div class="panel-header">
            <h2 class="panel-title">Recent activity (latest {{ count($report['recent_events'] ?? []) }} events)</h2>
        </div>
        <div class="looker-table-wrapper">
            <table class="looker-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Event</th>
                        <th>Source</th>
                        <th>Visitor</th>
                        <th>UTM</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($report['recent_events'] ?? [] as $ev)
                        <tr>
                            <td class="text-nowrap small">{{ $ev->occurred_at?->format('Y-m-d H:i:s') }}</td>
                            <td><span class="small">{{ $eventLabels[$ev->event_name] ?? $ev->event_name }}</span></td>
                            <td class="small">{{ $ev->source ?: '—' }}</td>
                            <td class="small text-muted">#{{ $ev->visitor_id }}</td>
                            <td class="small">
                                @if($ev->utm_source || $ev->utm_campaign)
                                    {{ $ev->utm_source }}{{ $ev->utm_campaign ? ' / '.$ev->utm_campaign : '' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small text-muted">{{ $ev->ip_address ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No events recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
