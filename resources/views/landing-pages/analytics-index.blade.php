@extends('layouts.adminlte')

@section('title', 'Landing pages — visitor analytics')
@section('page-title', 'Visitor analytics (all landing pages)')
@section('breadcrumb', 'Landing Pages / Analytics overview')

@section('content')
<div class="container-fluid">
    <div class="alert alert-light border mb-3">
        <strong>Per-page detail.</strong>
        Open a landing page’s full funnel, daily chart, UTM breakdown, and recent events from the <strong>Analytics</strong> link in each row. Public traffic is tracked from <code>/l/{slug}</code> (views, CTA, leads, language, thank-you).
    </div>

    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('landing-pages.index') }}" class="btn btn-default" style="min-height:44px;">
                <i class="fas fa-arrow-left mr-1"></i>All landing pages
            </a>
            <a href="{{ route('landing-pages.reporting.index') }}" class="btn btn-outline-primary" style="min-height:44px;">
                <i class="fas fa-address-book mr-1"></i>Leads &amp; reporting
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="fas fa-users mr-2"></i>Summary by page
            </h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
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
                                        <span class="badge badge-secondary">Draft</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-right">{{ number_format((int) ($s['views'] ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($s['unique_visitors'] ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($s['leads'] ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($s['cta_clicks'] ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($s['thank_you'] ?? 0)) }}</td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('landing-pages.analytics', $p) }}" class="btn btn-sm btn-primary" style="min-height:44px;">Analytics</a>
                                <a href="{{ route('landing-pages.reporting', $p) }}" class="btn btn-sm btn-default" style="min-height:44px;">Leads</a>
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
