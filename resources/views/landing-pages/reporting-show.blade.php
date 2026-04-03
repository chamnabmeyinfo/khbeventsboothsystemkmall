@extends('layouts.adminlte')

@section('title', 'Lead #'.$lead->id)
@section('page-title', 'Marketing lead #'.$lead->id)
@section('breadcrumb', 'Landing Pages / Reporting / View')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h3 class="card-title mb-0">
                <i class="fas fa-id-card mr-2"></i>Lead #{{ $lead->id }}
            </h3>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('landing-pages.reporting.index') }}" class="btn btn-default btn-sm" style="min-height:44px;">All leads</a>
                <a href="{{ route('landing-pages.reporting.edit', $lead) }}" class="btn btn-primary btn-sm" style="min-height:44px;">Edit</a>
                @if($lead->landingPage)
                    <a href="{{ route('landing-pages.reporting', $lead->landingPage) }}" class="btn btn-outline-primary btn-sm" style="min-height:44px;">Page leads</a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Received</dt>
                <dd class="col-sm-9">{{ $lead->created_at?->format('Y-m-d H:i:s') }}</dd>
                <dt class="col-sm-3">Landing page</dt>
                <dd class="col-sm-9">
                    @if($lead->landingPage)
                        <a href="{{ route('landing-pages.reporting', $lead->landingPage) }}">{{ $lead->landingPage->name }}</a>
                        <span class="text-muted small">(<code>{{ $lead->landingPage->slug }}</code>)</span>
                    @else
                        —
                    @endif
                </dd>
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $lead->name ?: '—' }}</dd>
                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">@if($lead->email)<a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>@else—@endif</dd>
                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">{{ $lead->phone ?: '—' }}</dd>
                <dt class="col-sm-3">Trip / phase</dt>
                <dd class="col-sm-9">{{ $lead->preferred_trip_date ?: '—' }}</dd>
                <dt class="col-sm-3">Locale</dt>
                <dd class="col-sm-9"><code>{{ $lead->locale ?: '—' }}</code></dd>
                <dt class="col-sm-3">Source</dt>
                <dd class="col-sm-9">{{ $lead->source ?: '—' }}</dd>
                <dt class="col-sm-3">IP</dt>
                <dd class="col-sm-9 text-muted small">{{ $lead->ip_address ?: '—' }}</dd>
                <dt class="col-sm-3">User agent</dt>
                <dd class="col-sm-9 text-muted small text-break">{{ $lead->user_agent ? Str::limit($lead->user_agent, 500) : '—' }}</dd>
                <dt class="col-sm-3">Referrer</dt>
                <dd class="col-sm-9 text-muted small text-break">{{ $lead->referrer_url ? Str::limit($lead->referrer_url, 500) : '—' }}</dd>
                <dt class="col-sm-3">Visitor</dt>
                <dd class="col-sm-9 text-muted small">{{ $lead->visitor_id ? '#'.$lead->visitor_id : '—' }}</dd>
                <dt class="col-sm-3">Tracking event</dt>
                <dd class="col-sm-9 text-muted small">{{ $lead->landing_tracking_event_id ? '#'.$lead->landing_tracking_event_id : '— (manual entry)' }}</dd>
            </dl>
        </div>
    </div>

    @if($lead->meta && count((array) $lead->meta) > 0)
        <div class="card mb-3">
            <div class="card-header"><h3 class="card-title mb-0">Meta (JSON)</h3></div>
            <div class="card-body">
                <pre class="small bg-light p-3 rounded mb-0 overflow-auto" style="max-height:320px;">{{ json_encode($lead->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif

    <div class="card border-danger">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <strong>Delete this lead</strong>
                <p class="text-muted small mb-0">This cannot be undone.</p>
            </div>
            <form method="post" action="{{ route('landing-pages.reporting.destroy', $lead) }}" onsubmit="return confirm('Delete this lead permanently?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" style="min-height:44px;">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
