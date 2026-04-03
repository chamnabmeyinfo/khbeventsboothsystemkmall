@extends('layouts.adminlte')

@section('title', 'Landing pages — leads & reporting')
@section('page-title', 'Leads & reporting (all landing pages)')
@section('breadcrumb', 'Landing Pages / All leads & reporting')

@section('content')
<div class="container-fluid">
    <div class="alert alert-light border mb-3">
        <strong>Marketing leads only.</strong>
        Data shown here is stored under <code>/landing-pages</code> (table <code>landing_page_leads</code>).
        For visitor funnel and engagement (views, CTA, UTM), use
        <a href="{{ route('landing-pages.analytics.index') }}" class="btn btn-sm btn-outline-info ml-1">Visitor analytics</a>.
        Booth and floor-plan <strong>event bookings</strong> use the main <strong>Bookings</strong> menu, not this screen.
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('landing-pages.reporting.index') }}" class="row align-items-end">
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <label for="lp_report_search" class="small text-muted mb-1">Search name, email, phone</label>
                    <input type="text" name="search" id="lp_report_search" class="form-control" value="{{ request('search') }}" placeholder="Search…" autocomplete="off">
                </div>
                <div class="col-12 col-md-4 mb-2 mb-md-0">
                    <label for="lp_report_page" class="small text-muted mb-1">Landing page</label>
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
                    <button type="submit" class="btn btn-primary mr-2" style="min-height:44px;">Apply</button>
                    <a href="{{ route('landing-pages.reporting.index') }}" class="btn btn-default" style="min-height:44px;">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h3 class="card-title mb-0">
                <i class="fas fa-table mr-2"></i>All submissions
            </h3>
            <a href="{{ route('landing-pages.index') }}" class="btn btn-default btn-sm">Back to landing pages</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
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
