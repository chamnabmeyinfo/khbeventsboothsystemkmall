@extends('layouts.adminlte')

@section('title', 'Landing Pages')
@section('page-title', 'Landing Page Management')
@section('breadcrumb', 'Marketing / Landing Pages')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 col-md-3 mb-2 mb-md-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Total Pages</div>
                    <div class="h3 mb-0">{{ number_format($stats['total_pages'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-2 mb-md-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Published</div>
                    <div class="h3 mb-0">{{ number_format($stats['published_pages'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-2 mb-md-0">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Active Page</div>
                    <div class="h3 mb-0">{{ number_format($stats['active_page'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small">Tracked Events</div>
                    <div class="h3 mb-0">{{ number_format($stats['total_events'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-2">
            <span class="text-muted small mr-auto mb-2 mb-md-0">
                Marketing leads (all pages): <strong>{{ number_format($stats['total_leads'] ?? 0) }}</strong>
                <span class="d-none d-md-inline"> — stored under <code>/landing-pages/reporting</code></span>
            </span>
            <a href="{{ route('landing-pages.create') }}" class="btn btn-primary" style="min-height:44px;">
                <i class="fas fa-plus mr-1"></i>Create Landing Page
            </a>
            <a href="{{ route('landing-pages.analytics.index') }}" class="btn btn-outline-info" style="min-height:44px;">
                <i class="fas fa-chart-area mr-1"></i>Visitor analytics
            </a>
            <a href="{{ route('landing-pages.reporting.index') }}" class="btn btn-outline-primary" style="min-height:44px;">
                <i class="fas fa-chart-line mr-1"></i>Leads &amp; reporting
            </a>
            <a href="{{ route('landing-pages.index') }}" class="btn btn-default" style="min-height:44px;">
                <i class="fas fa-sync-alt mr-1"></i>Refresh
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('landing-pages.index') }}" class="row">
                <div class="col-12 col-md-5 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, slug, industry..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <select name="industry" class="form-control">
                        <option value="">All industries</option>
                        @foreach($industries as $industry)
                            <option value="{{ $industry }}" {{ request('industry') === $industry ? 'selected' : '' }}>
                                {{ $industry }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 mb-2">
                    <select name="status" class="form-control">
                        <option value="">All status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary btn-block" style="min-height:44px;">
                        <i class="fas fa-filter mr-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Industry</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Updated</th>
                        <th class="text-nowrap">Leads</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($landingPages as $landingPage)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $landingPage->name }}</div>
                                <div class="small text-muted">{{ $landingPage->headline }}</div>
                            </td>
                            <td>{{ $landingPage->industry ?: '-' }}</td>
                            <td><code>{{ $landingPage->slug }}</code></td>
                            <td>
                                @if($landingPage->is_active && $landingPage->is_published)
                                    <span class="badge badge-success">Active</span>
                                @elseif($landingPage->is_published)
                                    <span class="badge badge-info">Published</span>
                                @else
                                    <span class="badge badge-secondary">Draft</span>
                                @endif
                            </td>
                            <td>{{ $landingPage->priority }}</td>
                            <td>{{ optional($landingPage->updated_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="badge badge-light">{{ (int) ($landingPage->leads_count ?? 0) }}</span>
                                <a href="{{ route('landing-pages.analytics', $landingPage) }}" class="small d-inline-block ml-1">Analytics</a>
                                <span class="text-muted small">·</span>
                                <a href="{{ route('landing-pages.reporting', $landingPage) }}" class="small d-inline-block ml-1">Leads</a>
                            </td>
                            <td class="text-right">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('landing-pages.preview', $landingPage) }}" class="btn btn-warning" target="_blank">Preview</a>
                                    <a href="{{ route('landing-pages.edit', $landingPage) }}" class="btn btn-primary">Edit</a>
                                </div>
                                <div class="btn-group btn-group-sm mt-1">
                                    @if(!$landingPage->is_published)
                                        <form method="POST" action="{{ route('landing-pages.publish', $landingPage) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success">Publish</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('landing-pages.unpublish', $landingPage) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary">Unpublish</button>
                                        </form>
                                    @endif
                                    @if($landingPage->is_published && !$landingPage->is_active)
                                        <form method="POST" action="{{ route('landing-pages.set-active', $landingPage) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-info">Set Active</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('landing-pages.destroy', $landingPage) }}" onsubmit="return confirm('Delete this landing page?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No landing pages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($landingPages, 'links'))
            <div class="card-footer">{{ $landingPages->links() }}</div>
        @endif
    </div>
</div>
@endsection
