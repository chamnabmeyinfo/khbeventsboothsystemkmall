@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Landing pages — visitor analytics')
@section('page-title', 'Visitor analytics (all landing pages)')
@section('breadcrumb', 'Landing Pages / Analytics overview')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Visitor analytics</h1>
            <p>Funnel and engagement across all landing pages.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> All landing pages
            </a>
            <a href="{{ route('landing-pages.reporting.index') }}" class="action-btn action-btn-primary">
                <i class="fas fa-address-book" aria-hidden="true"></i> Leads &amp; reporting
            </a>
        </div>
    </header>

    <div class="lp-callout">
        <strong>Per-page detail.</strong>
        Open a page’s funnel, daily chart, UTM breakdown, and recent events from the <strong>Analytics</strong> link in each row.
        Public traffic is tracked from <code>/l/{slug}</code> (views, CTA, leads, language, thank-you).
    </div>

    @include('landing-pages.partials.analytics-clear-form', [
        'actionUrl' => route('landing-pages.analytics.clear'),
        'showPageSelector' => true,
        'landingPageOptions' => $landingPageOptions ?? collect(),
        'formId' => 'lp-an-clear-all',
    ])

    <div class="canvas-panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-users" aria-hidden="true"></i> Summary by page</h2>
        </div>
        <div class="looker-table-wrapper">
            <table class="looker-table">
                <thead>
                    <tr>
                        <th>Page</th>
                        <th class="text-right">Views</th>
                        <th class="text-right">Visitors</th>
                        <th class="text-right">Leads</th>
                        <th class="text-right">CTA</th>
                        <th class="text-right">Thank-you</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php $p = $row['page']; $s = $row['summary']; @endphp
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $p->name }}</div>
                                <div class="small text-muted"><code>{{ $p->slug }}</code>
                                    @if(!$p->is_published)
                                        <span class="status-badge status-badge-neutral">Draft</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-right">{{ number_format((int) ($s['views'] ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($s['unique_visitors'] ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($s['leads'] ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($s['cta_clicks'] ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($s['thank_you'] ?? 0)) }}</td>
                            <td class="text-right text-nowrap">
                                <div class="lp-row-actions justify-content-end">
                                    <a href="{{ route('landing-pages.analytics', $p) }}" class="action-btn action-btn-primary lp-btn-compact">Analytics</a>
                                    <a href="{{ route('landing-pages.reporting', $p) }}" class="action-btn action-btn-secondary lp-btn-compact">Leads</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No landing pages yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
