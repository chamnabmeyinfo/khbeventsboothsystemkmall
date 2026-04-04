@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Landing pages — leads & reporting')
@section('page-title', 'Leads & reporting (all landing pages)')
@section('breadcrumb', 'Landing Pages / All leads & reporting')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Leads &amp; reporting</h1>
            <p>Marketing submissions captured from landing pages.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.analytics.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-chart-area" aria-hidden="true"></i> Visitor analytics
            </a>
            <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> All landing pages
            </a>
        </div>
    </header>

    <div class="lp-callout">
        <strong>Marketing leads only.</strong>
        Data shown here is stored under <code>/landing-pages</code> (table <code>landing_page_leads</code>).
        For visitor funnel and engagement (views, CTA, UTM), use
        <a href="{{ route('landing-pages.analytics.index') }}">Visitor analytics</a>.
        Booth and floor-plan <strong>event bookings</strong> use the main <strong>Bookings</strong> menu, not this screen.
    </div>

    <div class="canvas-panel mb-3">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-search" aria-hidden="true"></i> Filter</h2>
        </div>
        <form method="GET" action="{{ route('landing-pages.reporting.index') }}" class="row align-items-end">
            <div class="col-12 col-md-4 mb-2 mb-md-0">
                <label for="lp_report_search" class="small text-muted mb-1 d-block">Search name, email, phone</label>
                <input type="text" name="search" id="lp_report_search" class="form-control" value="{{ request('search') }}" placeholder="Search…" autocomplete="off">
            </div>
            <div class="col-12 col-md-4 mb-2 mb-md-0">
                <label for="lp_report_page" class="small text-muted mb-1 d-block">Landing page</label>
                <select name="landing_page_id" id="lp_report_page" class="form-control">
                    <option value="">All pages</option>
                    @foreach($landingPageOptions as $opt)
                        <option value="{{ $opt->id }}" {{ (string) request('landing_page_id') === (string) $opt->id ? 'selected' : '' }}>
                            {{ $opt->name }} ({{ $opt->slug }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <div class="lp-quick-actions">
                    <button type="submit" class="action-btn action-btn-primary">Apply</button>
                    <a href="{{ route('landing-pages.reporting.index') }}" class="action-btn action-btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="canvas-panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-table" aria-hidden="true"></i> All submissions</h2>
            <div class="looker-actions flex-wrap">
                <a href="{{ route('landing-pages.reporting.create') }}" class="action-btn action-btn-primary">
                    <i class="fas fa-plus" aria-hidden="true"></i> Add lead
                </a>
            </div>
        </div>
        <div class="looker-table-wrapper">
            <table class="looker-table">
                <thead>
                    <tr>
                        <th>Received</th>
                        <th>Landing page</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Trip / phase</th>
                        <th>Lang</th>
                        <th>Source</th>
                        <th>IP</th>
                        <th class="text-right text-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leads as $row)
                        <tr>
                            <td class="text-nowrap">{{ $row->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($row->landingPage)
                                    <a href="{{ route('landing-pages.reporting', $row->landingPage) }}">{{ $row->landingPage->name }}</a>
                                    <div class="small text-muted"><code>{{ $row->landingPage->slug }}</code></div>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $row->name ?: '—' }}</td>
                            <td>@if($row->email)<a href="mailto:{{ $row->email }}">{{ $row->email }}</a>@else—@endif</td>
                            <td>{{ $row->phone ?: '—' }}</td>
                            <td class="small">{{ $row->preferred_trip_date ?: '—' }}</td>
                            <td><code>{{ $row->locale ?: '—' }}</code></td>
                            <td class="small">{{ $row->source ?: '—' }}</td>
                            <td class="small text-muted">{{ $row->ip_address ?: '—' }}</td>
                            <td class="text-right text-nowrap">
                                <div class="lp-row-actions">
                                    <a href="{{ route('landing-pages.reporting.show', $row) }}" class="action-btn action-btn-secondary lp-btn-compact">View</a>
                                    <a href="{{ route('landing-pages.reporting.edit', $row) }}" class="action-btn action-btn-primary lp-btn-compact">Edit</a>
                                    <form method="post" action="{{ route('landing-pages.reporting.destroy', $row) }}" class="d-inline" onsubmit="return confirm('Delete this lead?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger lp-btn-compact">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No submissions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($leads, 'links'))
            <div class="lp-pagination-wrap">{{ $leads->links() }}</div>
        @endif
    </div>
</div>
@endsection
