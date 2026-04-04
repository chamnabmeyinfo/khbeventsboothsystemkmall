@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Lead #'.$lead->id)
@section('page-title', 'Marketing lead #'.$lead->id)
@section('breadcrumb', 'Landing Pages / Reporting / View')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Lead #{{ $lead->id }}</h1>
            <p>Submission details and metadata.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.reporting.index') }}" class="action-btn action-btn-secondary">All leads</a>
            <a href="{{ route('landing-pages.reporting.edit', $lead) }}" class="action-btn action-btn-primary">Edit</a>
            @if($lead->landingPage)
                <a href="{{ route('landing-pages.reporting', $lead->landingPage) }}" class="action-btn action-btn-secondary">Page leads</a>
            @endif
        </div>
    </header>

    <div class="canvas-panel mb-3">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-id-card" aria-hidden="true"></i> Details</h2>
        </div>
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

    @if($lead->meta && count((array) $lead->meta) > 0)
        <div class="canvas-panel mb-3">
            <div class="panel-header">
                <h2 class="panel-title">Meta (JSON)</h2>
            </div>
            <pre class="small bg-light p-3 rounded mb-0 overflow-auto lp-meta-pre">{{ json_encode($lead->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    @endif

    <div class="canvas-panel lp-canvas-panel--warning">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="mb-2 mb-md-0">
                <strong>Delete this lead</strong>
                <p class="text-muted small mb-0">This cannot be undone.</p>
            </div>
            <form method="post" action="{{ route('landing-pages.reporting.destroy', $lead) }}" onsubmit="return confirm('Delete this lead permanently?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
