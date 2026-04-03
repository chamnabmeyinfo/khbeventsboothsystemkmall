@extends('layouts.adminlte')

@section('title', 'Landing leads & reporting')
@section('page-title', 'Leads & reporting: '.$landingPage->name)
@section('breadcrumb', 'Landing Pages / Reporting')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="alert alert-light border mb-3">
        <strong>Marketing leads only.</strong>
        Admin URL: <code>/landing-pages/{{ $landingPage->slug }}/reporting</code>. Public page: <code>/l/{{ $landingPage->slug }}</code>.
        Booth and floor-plan <strong>event bookings</strong> are managed under the main <strong>Bookings</strong> menu, not here.
    </div>
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h3 class="card-title mb-0">
                <i class="fas fa-chart-line mr-2"></i>Lead submissions
            </h3>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('landing-pages.reporting.create', ['landing_page_id' => $landingPage->id]) }}" class="btn btn-success btn-sm">Add lead</a>
                <a href="{{ route('landing-pages.edit', $landingPage) }}" class="btn btn-default btn-sm">Edit page</a>
                <a href="{{ route('landing-pages.analytics', $landingPage) }}" class="btn btn-primary btn-sm">Visitor analytics</a>
                <a href="{{ route('landing-pages.public.show', $landingPage) }}" class="btn btn-info btn-sm" target="_blank" rel="noopener">Public URL</a>
                <a href="{{ route('landing-pages.reporting.index') }}" class="btn btn-outline-primary btn-sm">All pages — leads</a>
                <a href="{{ route('landing-pages.index') }}" class="btn btn-default btn-sm">All pages</a>
            </div>
        </div>
        <div class="card-body py-2">
            <p class="text-muted small mb-0">
                Rows are created when visitors submit the on-page form or the continue modal. Engagement events are stored separately for funnel reporting.
            </p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
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
                                <a href="{{ route('landing-pages.reporting.show', $row) }}" class="btn btn-xs btn-default">View</a>
                                <a href="{{ route('landing-pages.reporting.edit', $row) }}" class="btn btn-xs btn-primary">Edit</a>
                                <form method="post" action="{{ route('landing-pages.reporting.destroy', $row) }}" class="d-inline" onsubmit="return confirm('Delete this lead?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger">Delete</button>
                                </form>
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
            <div class="card-footer">{{ $leads->links() }}</div>
        @endif
    </div>
</div>
@endsection
