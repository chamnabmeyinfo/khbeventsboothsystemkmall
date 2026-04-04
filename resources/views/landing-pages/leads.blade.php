@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Landing leads & reporting')
@section('page-title', 'Leads & reporting: '.$landingPage->name)
@section('breadcrumb', 'Landing Pages / Reporting')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Leads: {{ $landingPage->name }}</h1>
            <p>Submissions from this landing page only.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.reporting.create', ['landing_page_id' => $landingPage->id]) }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> Add lead
            </a>
            <a href="{{ route('landing-pages.edit', $landingPage) }}" class="action-btn action-btn-secondary">Edit page</a>
            <a href="{{ route('landing-pages.analytics', $landingPage) }}" class="action-btn action-btn-secondary">Visitor analytics</a>
            @if($landingPage->is_published)
                <a href="{{ route('landing-pages.public.show', $landingPage) }}" class="action-btn action-btn-secondary" target="_blank" rel="noopener">Public URL</a>
            @endif
            <a href="{{ route('landing-pages.reporting.index') }}" class="action-btn action-btn-secondary">All pages — leads</a>
            <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">All pages</a>
        </div>
    </header>

    <div class="lp-callout">
        <strong>Marketing leads only.</strong>
        Admin URL: <code>/landing-pages/{{ $landingPage->slug }}/reporting</code>. Public page: <code>/l/{{ $landingPage->slug }}</code>.
        Booth and floor-plan <strong>event bookings</strong> are managed under the main <strong>Bookings</strong> menu, not here.
    </div>

    <div class="canvas-panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-chart-line" aria-hidden="true"></i> Lead submissions</h2>
        </div>
        <p class="text-muted small mb-3">
            Rows are created when visitors submit the on-page form or the continue modal. Engagement events are stored separately for funnel reporting.
        </p>
        <div class="looker-table-wrapper">
            <table class="looker-table">
                <thead>
                    <tr>
                        <th>Received</th>
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
                            <td colspan="9" class="text-center text-muted py-4">No submissions yet.</td>
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
