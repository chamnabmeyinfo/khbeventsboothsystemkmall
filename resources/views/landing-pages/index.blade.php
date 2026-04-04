@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Landing Pages')
@section('page-title', 'Landing Page Management')
@section('breadcrumb', 'Marketing / Landing Pages')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Landing pages</h1>
            <p>Create and manage public marketing pages, leads, and visitor analytics.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-sync-alt" aria-hidden="true"></i> Refresh
            </a>
        </div>
    </header>

    <div class="kpi-wrapper">
        <div class="kpi-card-looker">
            <div class="kpi-top">
                <div class="kpi-title">Total pages</div>
                <div class="kpi-icon-wrapper primary-icon">
                    <i class="fas fa-file-alt" aria-hidden="true"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ number_format($stats['total_pages'] ?? 0) }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-circle" style="font-size: 8px;" aria-hidden="true"></i> In directory
            </div>
        </div>
        <div class="kpi-card-looker success">
            <div class="kpi-top">
                <div class="kpi-title">Published</div>
                <div class="kpi-icon-wrapper success-icon">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ number_format($stats['published_pages'] ?? 0) }}</div>
            <div class="kpi-bottom trend-positive">
                <i class="fas fa-fw fa-eye" aria-hidden="true"></i> Live or ready
            </div>
        </div>
        <div class="kpi-card-looker warning">
            <div class="kpi-top">
                <div class="kpi-title">Active page</div>
                <div class="kpi-icon-wrapper warning-icon">
                    <i class="fas fa-bullseye" aria-hidden="true"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ number_format($stats['active_page'] ?? 0) }}</div>
            <div class="kpi-bottom trend-warning">
                <i class="fas fa-fw fa-star" aria-hidden="true"></i> Primary campaign slot
            </div>
        </div>
        <div class="kpi-card-looker purple">
            <div class="kpi-top">
                <div class="kpi-title">Tracked events</div>
                <div class="kpi-icon-wrapper purple-icon">
                    <i class="fas fa-chart-line" aria-hidden="true"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ number_format($stats['total_events'] ?? 0) }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-mouse-pointer" aria-hidden="true"></i> Views, CTAs, funnel
            </div>
        </div>
    </div>

    <div class="canvas-panel mb-3">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-bullhorn" aria-hidden="true"></i> Quick actions</h2>
        </div>
        <p class="text-muted small mb-3 mb-md-0">
            Marketing leads (all pages): <strong>{{ number_format($stats['total_leads'] ?? 0) }}</strong>
            <span class="d-none d-md-inline"> — full list under <strong>Leads &amp; reporting</strong></span>
        </p>
        <div class="lp-quick-actions mt-3">
            <a href="{{ route('landing-pages.create') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> Create landing page
            </a>
            <a href="{{ route('landing-pages.section-templates.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-layer-group" aria-hidden="true"></i> Section templates
            </a>
            <a href="{{ route('landing-pages.analytics.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-chart-area" aria-hidden="true"></i> Visitor analytics
            </a>
            <a href="{{ route('landing-pages.reporting.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-address-book" aria-hidden="true"></i> Leads &amp; reporting
            </a>
        </div>
    </div>

    <div class="canvas-panel mb-3">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-filter" aria-hidden="true"></i> Filter</h2>
        </div>
        <form method="GET" action="{{ route('landing-pages.index') }}" class="row align-items-end">
            <div class="col-12 col-md-5 mb-2 mb-md-0">
                <label for="lp_idx_search" class="small text-muted mb-1 d-block">Search</label>
                <input type="text" name="search" id="lp_idx_search" class="form-control" placeholder="Name, slug, industry…" value="{{ request('search') }}" autocomplete="off">
            </div>
            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <label for="lp_idx_industry" class="small text-muted mb-1 d-block">Industry</label>
                <select name="industry" id="lp_idx_industry" class="form-control">
                    <option value="">All industries</option>
                    @foreach($industries as $industry)
                        <option value="{{ $industry }}" {{ request('industry') === $industry ? 'selected' : '' }}>
                            {{ $industry }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 mb-2 mb-md-0">
                <label for="lp_idx_status" class="small text-muted mb-1 d-block">Status</label>
                <select name="status" id="lp_idx_status" class="form-control">
                    <option value="">All status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="action-btn action-btn-primary w-100 justify-content-center">
                    <i class="fas fa-filter" aria-hidden="true"></i> Apply
                </button>
            </div>
        </form>
    </div>

    <div class="canvas-panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-list" aria-hidden="true"></i> All landing pages</h2>
        </div>
        <div class="looker-table-wrapper">
            <table class="looker-table">
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
                            <td>{{ $landingPage->industry ?: '—' }}</td>
                            <td><code>{{ $landingPage->slug }}</code></td>
                            <td>
                                @if($landingPage->is_active && $landingPage->is_published)
                                    <span class="status-badge status-badge-green">Active</span>
                                @elseif($landingPage->is_published)
                                    <span class="status-badge status-badge-blue">Published</span>
                                @else
                                    <span class="status-badge status-badge-neutral">Draft</span>
                                @endif
                            </td>
                            <td>{{ $landingPage->priority }}</td>
                            <td class="text-nowrap small">{{ optional($landingPage->updated_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="status-badge status-badge-neutral">{{ (int) ($landingPage->leads_count ?? 0) }}</span>
                                <div class="small mt-1">
                                    <a href="{{ route('landing-pages.analytics', $landingPage) }}">Analytics</a>
                                    <span class="text-muted">·</span>
                                    <a href="{{ route('landing-pages.reporting', $landingPage) }}">Leads</a>
                                </div>
                            </td>
                            <td class="text-right">
                                <div class="lp-row-actions">
                                    <a href="{{ route('landing-pages.preview', $landingPage) }}" class="action-btn action-btn-secondary lp-btn-compact" target="_blank" rel="noopener">Preview</a>
                                    <a href="{{ route('landing-pages.edit', $landingPage) }}" class="action-btn action-btn-primary lp-btn-compact">Edit</a>
                                    @if(!$landingPage->is_published)
                                        <form method="POST" action="{{ route('landing-pages.publish', $landingPage) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="action-btn action-btn-primary lp-btn-compact">Publish</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('landing-pages.unpublish', $landingPage) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="action-btn action-btn-secondary lp-btn-compact">Unpublish</button>
                                        </form>
                                    @endif
                                    @if($landingPage->is_published && !$landingPage->is_active)
                                        <form method="POST" action="{{ route('landing-pages.set-active', $landingPage) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="action-btn action-btn-primary lp-btn-compact">Set active</button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('landing-pages.destroy', $landingPage) }}" class="d-inline" onsubmit="return confirm('Delete this landing page?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger lp-btn-compact">Delete</button>
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
            <div class="lp-pagination-wrap">{{ $landingPages->links() }}</div>
        @endif
    </div>
</div>
@endsection
