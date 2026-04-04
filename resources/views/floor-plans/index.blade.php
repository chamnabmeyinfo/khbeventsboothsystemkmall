@extends('layouts.app')

@php
    $isAuthenticated = auth()->check();
    $pageTitle = $isAuthenticated ? 'Floor Plan Management' : 'Browse Floor Plans';
@endphp

@section('title', $pageTitle)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<style>
    .floor-plan-card {
        background: #ffffff;
        border-radius: 20px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        margin-bottom: 24px;
        position: relative;
    }

    .floor-plan-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .floor-plan-card.default::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
        z-index: 1;
    }
    
    .floor-plan-card .card-body {
        padding: 20px;
    }
    
    .floor-plan-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 16px;
    }
    
    .floor-plan-actions .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 16px;
        transition: all 0.2s;
        border: none;
        font-size: 0.875rem;
    }
    
    .floor-plan-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .floor-plan-actions .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .floor-plan-actions .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    
    .floor-plan-actions .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    }
    
    .floor-plan-actions .btn-secondary {
        background: #6c757d;
    }
    
    .floor-plan-actions .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #212529;
    }
    
    .floor-plan-actions .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }
    
    .floor-plan-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin: 16px 0;
        padding: 16px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
    }
    
    .floor-plan-stat {
        text-align: center;
    }
    
    .floor-plan-stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #495057;
        line-height: 1.2;
    }
    
    .floor-plan-stat-label {
        font-size: 0.75rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }
    
    .floor-plan-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .floor-plan-meta {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 12px;
    }
    
    .floor-plan-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 12px;
    }
    
    .floor-plan-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .action-menu-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .action-menu-btn:hover {
        background: #fff;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .action-dropdown {
        position: absolute;
        top: 50px;
        right: 12px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        min-width: 200px;
        z-index: 100;
        display: none;
        overflow: hidden;
    }
    
    .action-dropdown.show {
        display: block;
    }
    
    .action-dropdown-item {
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: background 0.2s;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        font-size: 0.875rem;
    }
    
    .action-dropdown-item:hover {
        background: #f8f9fa;
    }
    
    .action-dropdown-item.danger {
        color: #dc3545;
    }
    
    .action-dropdown-item.danger:hover {
        background: #fee;
    }

    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        height: 100%;
    }

    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .kpi-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 16px;
    }

    .kpi-card.primary .kpi-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success .kpi-icon { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .kpi-card.info .kpi-icon { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }

    /* Keep search/filter above floor-plan cards (cards use transform on hover and would steal clicks) */
    .floor-plans-toolbar {
        position: relative;
        z-index: 30;
    }
    .floor-plans-toolbar .form-control,
    .floor-plans-toolbar .btn {
        position: relative;
        z-index: 1;
    }

    /* Floor plans toolbar: one column — no 50/50 split (avoids empty center gap) */
    .fp-toolbar-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }
    .fp-filter-panel {
        --fp-filter-bg: #f4f6f9;
        --fp-filter-border: #dee2e6;
        background: var(--fp-filter-bg);
        border: 1px solid var(--fp-filter-border);
        border-radius: 12px;
        padding: 1rem 1.125rem;
    }
    .fp-filter-panel-title {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 0.625rem;
    }
    .fp-filter-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.35rem;
    }
    .fp-search-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 0.75rem;
    }
    .fp-search-row .fp-search-input-wrap {
        flex: 1 1 220px;
        min-width: 0;
    }
    .fp-filter-field {
        flex: 1 1 140px;
        min-width: 140px;
        max-width: 100%;
    }
    .fp-filter-field .form-control {
        min-height: 42px;
    }
    @media (min-width: 576px) {
        .fp-filter-field {
            flex: 0 1 160px;
            max-width: 220px;
        }
    }
    .fp-refine-row {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 0.75rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid var(--fp-filter-border);
    }
    .fp-refine-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.25rem;
    }
    @media (min-width: 768px) {
        .fp-refine-actions {
            margin-top: 0;
            margin-left: auto;
        }
    }

    /* First row of cards: clip paint so hover lift does not cover toolbar */
    .floor-plans-grid {
        position: relative;
        z-index: 1;
    }
</style>
@endpush

@push('body-class', 'ios-dashboard-mode')

@section('content')
<div class="looker-dashboard floor-plans-page">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-map"></i>
                    </div>
                    <div class="kpi-label">Total Floor Plans</div>
                    <div class="kpi-value">{{ number_format($floorPlans->total()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="kpi-label">Active Floor Plans</div>
                    <div class="kpi-value">{{ number_format($floorPlans->where('is_active', true)->count()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="kpi-label">Total Booths</div>
                    <div class="kpi-value">{{ number_format($floorPlans->sum('booths_count')) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar: actions full width, then one filter panel (no split columns) -->
    <div class="card mb-4 floor-plans-toolbar">
        <div class="card-body py-3 px-3 px-lg-4">
            @auth
            <div class="fp-toolbar-actions">
                <a href="{{ route('floor-plans.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i>Create Floor Plan
                </a>
                <a href="{{ route('booths.index') }}" class="btn btn-info">
                    <i class="fas fa-map-marked-alt mr-1"></i>View Booths
                </a>
            </div>
            @else
            <div class="fp-toolbar-actions">
                <h5 class="mb-0 mr-2"><i class="fas fa-map mr-2"></i>Browse Floor Plans</h5>
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-sign-in-alt mr-1"></i>Login to Manage
                </a>
            </div>
            @endauth

            <form method="GET" action="{{ route('floor-plans.index') }}" class="floor-plans-filter-form fp-filter-panel mt-3">
                @php
                    $activationVal = request('activation', 'active');
                    $boothsFilterVal = request('booths_filter', 'any');
                    $sortVal = request('sort', 'newest');
                @endphp
                <p class="fp-filter-panel-title mb-2">Search &amp; filter</p>
                <div class="fp-search-row">
                    <div class="fp-search-input-wrap">
                        <label class="fp-filter-label" for="fp-filter-search">Search</label>
                        <input type="text" id="fp-filter-search" name="search" class="form-control" placeholder="Name, project, or description…" value="{{ request('search') }}" autocomplete="off">
                    </div>
                    <div class="fp-filter-field" style="flex: 1 1 200px; max-width: 280px;">
                        <label class="fp-filter-label" for="fp-filter-event">Event</label>
                        <select id="fp-filter-event" name="event_id" class="form-control" aria-label="Filter by event">
                            <option value="">All events</option>
                            @foreach($events ?? [] as $event)
                                @php
                                    $eventId = is_object($event) ? ($event->id ?? null) : ($event['id'] ?? null);
                                    $eventTitle = is_object($event) ? ($event->title ?? 'N/A') : ($event['title'] ?? 'N/A');
                                @endphp
                                @if($eventId)
                                    <option value="{{ $eventId }}" {{ (string) request('event_id') === (string) $eventId ? 'selected' : '' }}>
                                        {{ $eventTitle }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="fp-refine-row">
                    <div class="fp-filter-field">
                        <label class="fp-filter-label" for="fp-filter-activation">Status</label>
                        <select id="fp-filter-activation" name="activation" class="form-control" aria-label="Filter by active status">
                            <option value="active" {{ $activationVal === 'active' ? 'selected' : '' }}>Active only</option>
                            <option value="inactive" {{ $activationVal === 'inactive' ? 'selected' : '' }}>Inactive only</option>
                            <option value="all" {{ $activationVal === 'all' ? 'selected' : '' }}>All statuses</option>
                        </select>
                    </div>
                    <div class="fp-filter-field">
                        <label class="fp-filter-label" for="fp-filter-default">Plan type</label>
                        <select id="fp-filter-default" name="default_only" class="form-control" aria-label="Filter default floor plan">
                            <option value="" {{ request('default_only') !== '1' ? 'selected' : '' }}>Any plan</option>
                            <option value="1" {{ request('default_only') === '1' ? 'selected' : '' }}>Default only</option>
                        </select>
                    </div>
                    <div class="fp-filter-field">
                        <label class="fp-filter-label" for="fp-filter-booths">Booths</label>
                        <select id="fp-filter-booths" name="booths_filter" class="form-control" aria-label="Filter by booth presence">
                            <option value="any" {{ $boothsFilterVal === 'any' ? 'selected' : '' }}>Any count</option>
                            <option value="with" {{ $boothsFilterVal === 'with' ? 'selected' : '' }}>Has booths</option>
                            <option value="without" {{ $boothsFilterVal === 'without' ? 'selected' : '' }}>No booths</option>
                        </select>
                    </div>
                    <div class="fp-filter-field">
                        <label class="fp-filter-label" for="fp-filter-sort">Sort</label>
                        <select id="fp-filter-sort" name="sort" class="form-control" aria-label="Sort floor plans">
                            <option value="newest" {{ $sortVal === 'newest' ? 'selected' : '' }}>Newest first</option>
                            <option value="oldest" {{ $sortVal === 'oldest' ? 'selected' : '' }}>Oldest first</option>
                            <option value="name_asc" {{ $sortVal === 'name_asc' ? 'selected' : '' }}>Name A–Z</option>
                            <option value="name_desc" {{ $sortVal === 'name_desc' ? 'selected' : '' }}>Name Z–A</option>
                            <option value="booths_desc" {{ $sortVal === 'booths_desc' ? 'selected' : '' }}>Most booths</option>
                            <option value="booths_asc" {{ $sortVal === 'booths_asc' ? 'selected' : '' }}>Fewest booths</option>
                        </select>
                    </div>
                    <div class="fp-refine-actions">
                        <button type="submit" class="btn btn-primary px-3">
                            <i class="fas fa-search mr-1" aria-hidden="true"></i>Apply filters
                        </button>
                        <a href="{{ route('floor-plans.index') }}" class="btn btn-outline-secondary">Clear all</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Floor Plans List -->
    <div class="row floor-plans-grid">
        @forelse($floorPlans as $floorPlan)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card floor-plan-card {{ $floorPlan->is_default ? 'default' : '' }}">
                @php
                    // Get floor plan statistics
                    $stats = $floorPlan->getStats();
                    
                    // Get zone settings count (already loaded in controller)
                    $zoneCount = $floorPlan->zone_count ?? 0;
                    
                    // Get canvas settings (already loaded in controller)
                    $canvasSettings = $floorPlan->canvas_settings ?? null;

                    $currentUser = auth()->user();
                    $roleSlug = $currentUser?->role->slug ?? null;
                    $canGenerateAffiliate = $currentUser && ($currentUser->isAdmin() || in_array($roleSlug, ['administrator', 'sales-manager', 'owner']));
                @endphp
                
                <!-- Feature Image Preview (if available, otherwise floor_image) -->
                @php
                    $previewImage = null;
                    if ($floorPlan->feature_image && file_exists(public_path($floorPlan->feature_image))) {
                        $previewImage = $floorPlan->feature_image;
                    } elseif ($floorPlan->floor_image && file_exists(public_path($floorPlan->floor_image))) {
                        $previewImage = $floorPlan->floor_image;
                    }
                @endphp
                @if($previewImage)
                <div style="position: relative; height: 200px; overflow: hidden; border-radius: 12px 12px 0 0; background: #f5f5f5;">
                    <img src="{{ asset($previewImage) }}" 
                         alt="{{ $floorPlan->name }}" 
                         style="width: 100%; height: 100%; object-fit: cover;"
                         onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'display:flex;align-items:center;justify-content:center;height:100%;color:#999;\'><i class=\'fas fa-image fa-3x\'></i></div>'">
                    @if($floorPlan->is_default)
                    <div style="position: absolute; top: 8px; right: 8px; background: rgba(245, 158, 11, 0.9); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; z-index: 10;">
                        <i class="fas fa-star mr-1"></i>Default
                    </div>
                    @endif
                    @if($floorPlan->feature_image)
                    <div style="position: absolute; top: 8px; left: 8px; background: rgba(40, 167, 69, 0.9); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; z-index: 10;">
                        <i class="fas fa-image mr-1"></i>Feature
                    </div>
                    @endif
                </div>
                @else
                <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; border-radius: 12px 12px 0 0;">
                    <div style="text-align: center; color: white;">
                        <i class="fas fa-map fa-4x mb-2"></i>
                        <div style="font-size: 0.875rem; opacity: 0.9;">No Image</div>
                        @if($floorPlan->is_default)
                        <div style="margin-top: 8px; background: rgba(255, 255, 255, 0.2); padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; display: inline-block;">
                            <i class="fas fa-star mr-1"></i>Default
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <div class="card-body" style="position: relative;">
                    <!-- Action Menu Button (for admin/editors) -->
                    @auth
                    @if(auth()->user()->hasPermission('floor-plans.edit') || auth()->user()->isAdmin())
                    <button type="button" class="action-menu-btn" onclick="toggleActionMenu({{ $floorPlan->id }})" title="More Actions">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="action-dropdown" id="actionMenu{{ $floorPlan->id }}">
                        <button class="action-dropdown-item" onclick="window.location.href='{{ route('floor-plans.edit', $floorPlan) }}'">
                            <i class="fas fa-edit"></i> Edit Floor Plan
                        </button>
                        @if(!$floorPlan->is_default)
                        <form action="{{ route('floor-plans.set-default', $floorPlan) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="action-dropdown-item">
                                <i class="fas fa-star"></i> Set as Default
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('floor-plans.duplicate', $floorPlan) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="action-dropdown-item">
                                <i class="fas fa-copy"></i> Duplicate
                            </button>
                        </form>
                        @if(!$floorPlan->is_default && (auth()->user()->hasPermission('floor-plans.delete') || auth()->user()->isAdmin()))
                        <button type="button" class="action-dropdown-item danger" 
                                onclick="deleteFloorPlan({{ $floorPlan->id }}, '{{ addslashes($floorPlan->name) }}', {{ $floorPlan->booths_count }})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        @endif
                    </div>
                    @endif
                    @endauth
                    
                    <!-- Title and Meta -->
                    <div class="floor-plan-title">
                        <i class="fas fa-map" style="color: #667eea;"></i>
                        <span>{{ $floorPlan->name }}</span>
                    </div>
                    
                    @if($floorPlan->project_name)
                    <div class="floor-plan-meta">
                        <i class="fas fa-project-diagram mr-1"></i>{{ $floorPlan->project_name }}
                    </div>
                    @endif
                    
                    @if($floorPlan->description)
                    <p class="text-muted" style="font-size: 0.875rem; line-height: 1.5; margin-bottom: 16px;">
                        {{ Str::limit($floorPlan->description, 100) }}
                    </p>
                    @endif
                    
                    <!-- Statistics Grid -->
                    <div class="floor-plan-stats">
                        <div class="floor-plan-stat">
                            <div class="floor-plan-stat-value">{{ $floorPlan->booths_count ?? 0 }}</div>
                            <div class="floor-plan-stat-label">Booths</div>
                        </div>
                        <div class="floor-plan-stat">
                            <div class="floor-plan-stat-value">{{ $zoneCount }}</div>
                            <div class="floor-plan-stat-label">Zones</div>
                        </div>
                        <div class="floor-plan-stat">
                            <div class="floor-plan-stat-value">{{ $stats['occupancy_rate'] ?? 0 }}%</div>
                            <div class="floor-plan-stat-label">Occupied</div>
                        </div>
                    </div>
                    
                    <!-- Status Badges -->
                    @if($stats['total'] > 0)
                    <div class="floor-plan-badges">
                        <span class="badge badge-success floor-plan-badge">{{ $stats['available'] }} Available</span>
                        <span class="badge badge-warning floor-plan-badge">{{ $stats['reserved'] }} Reserved</span>
                        <span class="badge badge-info floor-plan-badge">{{ $stats['confirmed'] }} Confirmed</span>
                    </div>
                    @endif
                    
                    <!-- Event Info (if available) -->
                    @if($floorPlan->event_start_date)
                    <div style="margin-top: 12px; padding: 12px; background: #e8f4f8; border-radius: 10px; font-size: 0.8125rem;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <i class="fas fa-calendar" style="color: #17a2b8;"></i>
                            <strong>{{ \Carbon\Carbon::parse($floorPlan->event_start_date)->format('M d, Y') }}</strong>
                            @if($floorPlan->event_end_date && $floorPlan->event_end_date != $floorPlan->event_start_date)
                                - {{ \Carbon\Carbon::parse($floorPlan->event_end_date)->format('M d, Y') }}
                            @endif
                        </div>
                        @if($floorPlan->event_venue)
                        <div style="display: flex; align-items: center; gap: 8px; color: #6c757d;">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ Str::limit($floorPlan->event_venue, 50) }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Action Buttons -->
                    <div class="floor-plan-actions">
                        <a href="{{ route('floor-plans.public', $floorPlan->id) }}" 
                           class="btn btn-success" 
                           target="_blank"
                           title="View Public Floor Plan">
                            <i class="fas fa-globe mr-2"></i>View Public
                        </a>
                        
                        @auth
                        @if($canGenerateAffiliate)
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <select id="affiliateExpiry{{ $floorPlan->id }}" class="form-control form-control-sm" style="max-width: 170px;">
                                <option value="7">Expire in 1 week</option>
                                <option value="14">Expire in 2 weeks</option>
                                <option value="21">Expire in 3 weeks</option>
                                <option value="28" selected>Expire in 4 weeks</option>
                                <option value="60">Expire in 2 months</option>
                                <option value="90">Expire in 3 months</option>
                            </select>
                            <button type="button" 
                                    class="btn btn-primary" 
                                    onclick="copyAffiliateLink({{ $floorPlan->id }})" 
                                    id="copyLinkBtn{{ $floorPlan->id }}"
                                    title="Copy Your Unique Affiliate Link">
                                <i class="fas fa-link mr-2"></i>Copy My Link
                            </button>
                        </div>
                        @else
                        <div class="alert alert-light border" role="alert" style="padding: 10px 12px; margin-bottom: 8px;">
                            <i class="fas fa-info-circle mr-2 text-muted"></i>
                            Link sharing is limited to Owners, Admins, or Sales Managers.
                        </div>
                        @endif
                        
                        <a href="{{ route('booths.index', ['view' => 'canvas', 'floor_plan_id' => $floorPlan->id]) }}" 
                           class="btn btn-primary" 
                           title="Open Floor Plan Designer">
                            <i class="fas fa-paint-brush mr-2"></i>Design Canvas
                        </a>
                        
                        <a href="{{ route('books.create', ['floor_plan_id' => $floorPlan->id]) }}" 
                           class="btn btn-info" 
                           title="Book Booths">
                            <i class="fas fa-calendar-plus mr-2"></i>Book Booths
                        </a>
                        
                        <a href="{{ route('floor-plans.show', $floorPlan) }}" 
                           class="btn btn-secondary" 
                           title="View Details">
                            <i class="fas fa-info-circle mr-2"></i>View Details
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary" title="Login Required">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login to Access
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-map fa-3x text-muted mb-3"></i>
                    <h5>No Floor Plans Found</h5>
                    <p class="text-muted">@auth Create your first floor plan to get started. @else No floor plans available at this time. @endauth</p>
                    @auth
                    <a href="{{ route('floor-plans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create Floor Plan
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt mr-1"></i>Login
                    </a>
                    @endauth
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($floorPlans->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $floorPlans->links() }}
    </div>
    @endif

</div>

@push('modals')
    <!-- Delete modal at end of body (avoids nested layout / aria-hidden focus issues) -->
    <div class="modal fade" id="deleteFloorPlanModal" tabindex="-1" role="dialog" aria-labelledby="deleteFloorPlanModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteFloorPlanModalLabel">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Delete Floor Plan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteFloorPlanForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-3">
                            Are you sure you want to delete <strong id="deleteFloorPlanName" data-fp-delete-name></strong>?
                        </p>
                        
                        <div id="boothActionSection" style="display: none;">
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-info-circle mr-2"></i>
                                This floor plan has <strong id="boothCountText"></strong> booth(s). What would you like to do with them?
                            </div>
                            
                            <div class="form-group">
                                <label>Action for booths:</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="booth_action" id="boothActionDelete" value="delete" checked>
                                    <label class="form-check-label" for="boothActionDelete">
                                        <strong>Delete all booths</strong> (This will also delete all bookings associated with these booths)
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="booth_action" id="boothActionMove" value="move">
                                    <label class="form-check-label" for="boothActionMove">
                                        <strong>Move booths to another floor plan</strong>
                                    </label>
                                </div>
                                
                                <div id="targetFloorPlanSelect" style="display: none;">
                                    <label for="target_floor_plan_id">Select target floor plan:</label>
                                    <select class="form-control" name="target_floor_plan_id" id="target_floor_plan_id">
                                        <option value="">-- Select Floor Plan --</option>
                                        @foreach($floorPlans as $fp)
                                            <option value="{{ $fp->id }}" data-floor-plan-id="{{ $fp->id }}">{{ $fp->name }} ({{ $fp->booths_count }} booths)</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">All booths will be moved to the selected floor plan.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Warning:</strong> This action cannot be undone!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i>Delete Floor Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
let currentFloorPlanId = null;

function deleteFloorPlan(floorPlanId, floorPlanName, boothCount) {
    currentFloorPlanId = floorPlanId;

    var modalEl = document.getElementById('deleteFloorPlanModal');
    if (!modalEl) {
        console.error('deleteFloorPlanModal not in DOM');
        alert('Delete dialog could not be opened. Please refresh the page.');
        return;
    }

    var formEl = modalEl.querySelector('#deleteFloorPlanForm');
    var nameEl = modalEl.querySelector('[data-fp-delete-name]') || modalEl.querySelector('#deleteFloorPlanName');
    if (!formEl || !nameEl) {
        console.error('Delete modal form/name missing', { formEl: formEl, nameEl: nameEl });
        alert('Delete dialog could not be opened. Please refresh the page.');
        return;
    }

    formEl.action = '{{ url("/floor-plans") }}/' + floorPlanId;
    nameEl.textContent = floorPlanName;

    var boothActionSection = document.getElementById('boothActionSection');
    var targetFloorPlanSelect = document.getElementById('targetFloorPlanSelect');
    var targetFloorPlanId = document.getElementById('target_floor_plan_id');
    var boothActionMove = document.getElementById('boothActionMove');
    var boothActionDelete = document.getElementById('boothActionDelete');

    if (targetFloorPlanId) {
        var options = targetFloorPlanId.querySelectorAll('option[data-floor-plan-id]');
        options.forEach(function(option) {
            var optionId = parseInt(option.getAttribute('data-floor-plan-id'), 10);
            if (optionId === floorPlanId) {
                option.style.display = 'none';
                option.disabled = true;
                option.selected = false;
            } else {
                option.style.display = '';
                option.disabled = false;
            }
        });
        var blankOpt = targetFloorPlanId.querySelector('option[value=""]');
        if (blankOpt) {
            blankOpt.selected = true;
        }
    }

    if (boothCount > 0 && boothActionSection && boothActionDelete && boothActionMove && targetFloorPlanSelect && targetFloorPlanId) {
        boothActionSection.style.display = 'block';
        var boothCountEl = document.getElementById('boothCountText');
        if (boothCountEl) {
            boothCountEl.textContent = boothCount;
        }
        boothActionDelete.checked = true;
        boothActionMove.checked = false;
        targetFloorPlanSelect.style.display = 'none';
        targetFloorPlanId.required = false;
        targetFloorPlanId.value = '';
    } else if (boothActionSection && targetFloorPlanId) {
        boothActionSection.style.display = 'none';
        targetFloorPlanId.value = '';
        targetFloorPlanId.required = false;
    }

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else if (typeof window.jQuery !== 'undefined' && window.jQuery.fn && window.jQuery.fn.modal) {
        window.jQuery(modalEl).modal('show');
    } else {
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        document.body.classList.add('modal-open');
    }
}

// Handle radio button changes (using event delegation)
$(document).on('change', '#boothActionMove', function() {
    if (this.checked) {
        $('#targetFloorPlanSelect').show();
        $('#target_floor_plan_id').prop('required', true);
    }
});

$(document).on('change', '#boothActionDelete', function() {
    if (this.checked) {
        $('#targetFloorPlanSelect').hide();
        $('#target_floor_plan_id').prop('required', false);
        $('#target_floor_plan_id').val('');
    }
});

// Validate form before submit
$(document).on('submit', '#deleteFloorPlanForm', function(e) {
    const boothActionMove = $('#boothActionMove').is(':checked');
    const targetFloorPlanId = $('#target_floor_plan_id').val();
    
    if (boothActionMove) {
        if (!targetFloorPlanId) {
            e.preventDefault();
            alert('Please select a target floor plan to move booths to.');
            $('#target_floor_plan_id').focus();
            return false;
        }
        
        if (parseInt(targetFloorPlanId) === currentFloorPlanId) {
            e.preventDefault();
            alert('Cannot move booths to the same floor plan. Please select a different floor plan.');
            $('#target_floor_plan_id').focus();
            return false;
        }
    }
});

// Toggle Action Menu
function toggleActionMenu(floorPlanId) {
    const menu = document.getElementById('actionMenu' + floorPlanId);
    const isOpen = menu.classList.contains('show');
    
    // Close all other menus
    document.querySelectorAll('.action-dropdown').forEach(m => m.classList.remove('show'));
    
    // Toggle current menu
    if (!isOpen) {
        menu.classList.add('show');
        // Close on outside click
        setTimeout(() => {
            document.addEventListener('click', function closeMenu(e) {
                if (!menu.contains(e.target) && !e.target.closest('.action-menu-btn')) {
                    menu.classList.remove('show');
                    document.removeEventListener('click', closeMenu);
                }
            });
        }, 10);
    }
}

// Copy Affiliate Link Function
function copyAffiliateLink(floorPlanId) {
    const btn = document.getElementById('copyLinkBtn' + floorPlanId);
    const originalText = btn ? btn.innerHTML : '';
    const expiryField = document.getElementById('affiliateExpiry' + floorPlanId);
    const expiryDays = expiryField ? parseInt(expiryField.value, 10) || 28 : 28;
    
    // Show loading state
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating...';
    }
    
    // Generate affiliate link
    fetch('{{ url("/floor-plans") }}/' + floorPlanId + '/affiliate-link', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ expiry_days: expiryDays })
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) {
            const message = data.message || 'Failed to generate affiliate link';
            throw new Error(message);
        }
        return data;
    })
    .then(data => {
        // Copy to clipboard - data.link should already be a full URL or path
        let fullUrl = data.link;
        // If link doesn't start with http, prepend origin
        if (!fullUrl.startsWith('http://') && !fullUrl.startsWith('https://')) {
            fullUrl = window.location.origin + (fullUrl.startsWith('/') ? fullUrl : '/' + fullUrl);
        }
        navigator.clipboard.writeText(fullUrl).then(function() {
            // Show success feedback
            if (btn) {
                btn.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
                btn.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
            }
            
            // Show toast notification if available
            if (typeof toastr !== 'undefined') {
                toastr.success('Tracking link copied. The first click wins attribution.', 'Link Copied!');
            } else {
                alert('Affiliate link copied to clipboard!\n\n' + fullUrl);
            }
            
            // Reset button after 2 seconds
            setTimeout(function() {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    btn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                }
            }, 2000);
        }).catch(function(err) {
            // Fallback: show link in alert
            alert('Your affiliate link:\n\n' + fullUrl + '\n\nPlease copy this link manually.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'Error generating affiliate link. Please try again.');
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
}
</script>
@endpush
@endsection

