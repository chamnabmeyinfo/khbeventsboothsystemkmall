@extends('layouts.admin')

@section('title', 'Admin Settings Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=2.3">
@endpush

@push('body-class', 'ios-dashboard-mode')

@section('content')
<div class="looker-dashboard">
    <!-- Premium Header -->
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Event Core Engine</h1>
            <p>Welcome back admin. Here's your global event management overview.</p>
        </div>
        <div class="looker-actions">
            <button class="action-btn action-btn-secondary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt text-tertiary"></i> Sync Events
            </button>
            <a href="{{ route('admin.events.index') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus"></i> New Event
            </a>
        </div>
    </header>

    <!-- KPI Master Grid -->
    <div class="kpi-wrapper">
        <!-- Events -->
        <div class="kpi-card-looker animate-slide-up delay-2">
            <div class="kpi-top">
                <div class="kpi-title">Total Hosted Events</div>
                <div class="kpi-icon-wrapper primary-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $stats['total_events'] }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-cubes"></i> Global Ecosystem Count
            </div>
        </div>

        <!-- Active Events -->
        <div class="kpi-card-looker success animate-slide-up delay-3">
            <div class="kpi-top">
                <div class="kpi-title">Active Events</div>
                <div class="kpi-icon-wrapper success-icon">
                    <i class="fas fa-satellite-dish"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $stats['active_events'] }}</div>
            <div class="progress-bar-container mt-3">
                <div class="progress-bar-fill" style="width: {{ $stats['total_events'] > 0 ? ($stats['active_events'] / $stats['total_events']) * 100 : 0 }}%; background: var(--accent-green);"></div>
            </div>
        </div>

        <!-- Upcoming -->
        <div class="kpi-card-looker warning animate-slide-up delay-4">
            <div class="kpi-top">
                <div class="kpi-title">Upcoming</div>
                <div class="kpi-icon-wrapper warning-icon">
                    <i class="fas fa-bolt"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $stats['upcoming_events'] }}</div>
            <div class="kpi-bottom trend-warning">
                <i class="fas fa-fw fa-clock"></i> In Preparation
            </div>
        </div>

        <!-- Categories -->
        <div class="kpi-card-looker purple animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Active Categories</div>
                <div class="kpi-icon-wrapper purple-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $stats['total_categories'] }}</div>
            <div class="kpi-bottom trend-positive">
                <i class="fas fa-fw fa-sitemap"></i> Event Taxonomy
            </div>
        </div>
    </div>

    <!-- Analytics Dashboard Canvas -->
    <div class="data-canvas-grid">

        <!-- Datatable Column: Recent Events -->
        <div class="col-span-8 canvas-panel animate-slide-up delay-5">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="fas fa-stream"></i> Core Engine Stream (Recent Events)
                </h2>
                <a href="{{ route('admin.events.index') }}" class="action-btn action-btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">Manage Database</a>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th>Ecosystem Profile</th>
                            <th>Subsystem</th>
                            <th>Launch Window</th>
                            <th>Signal Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEvents as $event)
                        <tr>
                            <td>
                                <div class="avatar-chip">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($event->title ?? 'E', 0, 1)) }}
                                    </div>
                                    <div class="avatar-chip-text">
                                        <div class="avatar-chip-title">
                                            <a href="{{ route('admin.events.show', $event) }}" class="text-decoration-none text-dark">{{ $event->title }}</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-badge-blue">
                                    {{ $event->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem; color: var(--text-secondary); font-weight: 600;">
                                    {{ $event->formatted_start_date ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($event->status == 1)
                                    <span class="status-badge status-badge-green"><i class="fas fa-circle me-1" style="font-size: 0.5rem"></i> Active</span>
                                @else
                                    <span class="status-badge" style="background:#e2e8f0; color:#475569;"><i class="fas fa-power-off me-1" style="font-size: 0.5rem"></i> Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5" style="color: var(--text-tertiary); font-weight: 600;">
                                Database Empty. No events registered.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Categories -->
        <div class="col-span-4 canvas-panel animate-slide-up delay-5">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="fas fa-chart-pie"></i> Distribution Share
                </h2>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table" style="border: none;">
                    <tbody>
                        @forelse($topCategories as $category)
                        <tr style="border-bottom: 1px dashed var(--border-light);">
                            <td style="border: none;">
                                <div class="avatar-chip">
                                    <div class="avatar-circle" style="background: var(--accent-blue-light); color: var(--accent-blue); width: 30px; height: 30px; font-size: 0.85rem;">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <span class="avatar-chip-title" style="font-size: 0.9rem;">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td style="border: none; text-align: right;">
                                <div style="font-weight: 800; font-size: 1.1rem; color: var(--text-primary);">{{ $category->events_count }}</div>
                                <div style="font-size: 0.65rem; color: var(--text-tertiary); font-weight: 700; letter-spacing: 0.05em;">LINKED EVENTS</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center py-5" style="color: var(--text-tertiary); font-weight: 600;">
                                No distribution data available
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                <p class="small text-muted mb-0">Highest saturated categories by volume.</p>
            </div>
        </div>
    </div>
</div>
@endsection

