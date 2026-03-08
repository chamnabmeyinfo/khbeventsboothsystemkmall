@extends('layouts.app')

@section('title', 'Advanced Analytics Dashboard')

@push('styles')
@php
    try {
        $cdnSettings = \App\Models\Setting::getCDNSettings();
        $useCDN = $cdnSettings['use_cdn'] ?? true;
    } catch (\Exception $e) {
        $useCDN = true;
    }
@endphp
@if($useCDN)
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
@else
<link rel="stylesheet" href="{{ asset('vendor/chartjs/chart.min.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}">
@endpush

@section('content')
<div class="looker-dashboard">
    <!-- Premium Header -->
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Analytics Overview</h1>
            <p>Welcome back, <strong>{{ auth()->user()->username ?? auth()->user()->name }}</strong>. Here's your enterprise data overview.</p>
        </div>
        <div class="looker-actions">
            <button class="action-btn action-btn-secondary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt text-tertiary"></i> Refresh Data
            </button>
            @if(method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
            <a href="{{ route('settings.index') }}" class="action-btn action-btn-primary">
                <i class="fas fa-sliders-h"></i> System Configuration
            </a>
            @endif
        </div>
    </header>

    <!-- KPI Master Grid -->
    <div class="kpi-wrapper">
        <!-- Booths -->
        <div class="kpi-card-looker animate-slide-up delay-2">
            <div class="kpi-top">
                <div class="kpi-title">Total Booths</div>
                <div class="kpi-icon-wrapper primary-icon">
                    <i class="fas fa-cube"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $stats['total_booths'] ?? 0 }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-circle" style="font-size: 8px;"></i> Total Grid Capacity
            </div>
        </div>

        <!-- Available -->
        <div class="kpi-card-looker success animate-slide-up delay-3">
            <div class="kpi-top">
                <div class="kpi-title">Available Space</div>
                <div class="kpi-icon-wrapper success-icon">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $stats['available_booths'] ?? 0 }}</div>
            <div class="progress-bar-container mt-3">
                <div class="progress-bar-fill" style="width: {{ isset($stats['total_booths']) && $stats['total_booths'] > 0 ? ($stats['available_booths'] / $stats['total_booths']) * 100 : 0 }}%; background: var(--accent-green);"></div>
            </div>
        </div>

        <!-- Reserved/Pending -->
        <div class="kpi-card-looker warning animate-slide-up delay-4">
            <div class="kpi-top">
                <div class="kpi-title">Reserved Units</div>
                <div class="kpi-icon-wrapper warning-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
            <div class="kpi-value-looker">{{ $stats['reserved_booths'] ?? 0 }}</div>
            <div class="kpi-bottom trend-warning">
                <i class="fas fa-fw fa-arrow-up"></i> Pending Validation
            </div>
        </div>

        <!-- Revenue -->
        <div class="kpi-card-looker purple animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Revenue (MTD)</div>
                <div class="kpi-icon-wrapper purple-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="kpi-value-looker">${{ number_format($stats['this_month_revenue'] ?? 0, 2) }}</div>
            <div class="kpi-bottom trend-positive">
                <i class="fas fa-fw fa-arrow-trend-up"></i> Monthly Growth
            </div>
        </div>
    </div>

    <!-- Analytics Dashboard Canvas -->
    <div class="data-canvas-grid">
        <!-- Main Chart Column -->
        <div class="col-span-8 canvas-panel animate-slide-up delay-5">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="fas fa-chart-area"></i> Revenue & Booking Trends
                </h2>
                <div class="status-badge status-badge-blue">Last {{ $days ?? 30 }} Days</div>
            </div>
            <div style="height: 320px; position: relative;">
                <canvas id="bookingTrendsChartAdvanced"></canvas>
            </div>
        </div>

        <!-- Secondary Chart Column -->
        <div class="col-span-4 canvas-panel animate-slide-up delay-5">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="fas fa-chart-pie"></i> Distribution
                </h2>
                <div class="status-badge status-badge-green">Live</div>
            </div>
            <div style="height: 250px; position: relative;" class="mb-4">
                <canvas id="boothStatusChartAdvanced"></canvas>
            </div>
            <div class="mt-auto">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-tertiary fw-bold" style="font-size: 0.8rem; text-transform: uppercase;">Total Occupancy</span>
                    <span class="fw-bold fs-5 text-primary">{{ $stats['occupancy_rate'] ?? 0 }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: {{ $stats['occupancy_rate'] ?? 0 }}%; background: var(--accent-blue);"></div>
                </div>
            </div>
        </div>

        <!-- Datatable Column: Recent Bookings -->
        <div class="col-span-8 canvas-panel animate-slide-up delay-5">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="fas fa-list-ul"></i> Target Accounts (Recent Bookings)
                </h2>
                <a href="{{ route('books.index') }}" class="action-btn action-btn-secondary" style="padding: 0.25rem 0.75rem; font-size: 0.75rem;">View All</a>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th>Client Profile</th>
                            <th>Volume</th>
                            <th>Timeline</th>
                            <th>Representative</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentBookings ?? [] as $booking)
                        <tr>
                            <td>
                                <div class="avatar-chip">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($booking->client->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div class="avatar-chip-text">
                                        <div class="avatar-chip-title">{{ $booking->client->name ?? 'Unknown' }}</div>
                                        <div class="avatar-chip-sub">{{ $booking->client->company ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-badge-blue">
                                    {{ is_array(json_decode($booking->boothid, true)) ? count(json_decode($booking->boothid, true)) : 1 }} Units
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem; color: var(--text-secondary); font-weight: 600;">
                                    {{ $booking->date_book ? (\Carbon\Carbon::parse($booking->date_book)->format('M d, H:i')) : 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--text-primary); font-size: 0.85rem;">
                                    {{ $booking->user->username ?? 'System' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5" style="color: var(--text-tertiary); font-weight: 600;">
                                No recent activity detected
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Performers -->
        @if(isset($isAdmin) && $isAdmin && !empty($userStats))
        <div class="col-span-4 canvas-panel animate-slide-up delay-5">
            <div class="panel-header">
                <h2 class="panel-title">
                    <i class="fas fa-trophy text-warning"></i> Top Performers
                </h2>
            </div>
            <div class="looker-table-wrapper">
                <table class="looker-table" style="border: none;">
                    <tbody>
                        @foreach(array_slice($userStats, 0, 4) as $stat)
                        <tr style="border-bottom: 1px dashed var(--border-light);">
                            <td style="border: none;">
                                <div class="avatar-chip">
                                    <div class="avatar-circle" style="background: {{ $loop->first ? 'var(--accent-green-light)' : 'var(--accent-blue-light)' }}; color: {{ $loop->first ? 'var(--accent-green)' : 'var(--accent-blue)' }}; width: 30px; height: 30px; font-size: 0.85rem;">
                                        #{{ $loop->iteration }}
                                    </div>
                                    <span class="avatar-chip-title" style="font-size: 0.9rem;">{{ $stat['username'] }}</span>
                                </div>
                            </td>
                            <td style="border: none; text-align: right;">
                                <div style="font-weight: 800; font-size: 1.1rem; color: var(--text-primary);">{{ ($stat['reserve'] ?? 0) + ($stat['booking'] ?? 0) + ($stat['paid'] ?? 0) }}</div>
                                <div style="font-size: 0.65rem; color: var(--text-tertiary); font-weight: 700; letter-spacing: 0.05em;">CONVERSIONS</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                <p class="small text-muted mb-0">Performance is calculated based on total unique booth conversions.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($useCDN)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@else
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
@endif
<script src="{{ asset('js/dashboard/modern-v1.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dashboardData = {
        boothStatus: {
            labels: ['Available', 'Reserved', 'Confirmed', 'Paid', 'Others'],
            values: [
                {{ $stats['available_booths'] }},
                {{ $stats['reserved_booths'] }},
                {{ $stats['confirmed_booths'] }},
                {{ $stats['paid_booths'] }},
                {{ max(0, $stats['total_booths'] - ($stats['available_booths'] + $stats['reserved_booths'] + $stats['confirmed_booths'] + $stats['paid_booths'])) }}
            ]
        },
        trends: {
            dates: @json($bookingTrendDates ?? []),
            counts: @json($bookingTrendCounts ?? []),
            revenue: @json($revenueTrendData ?? [])
        }
    };

    new AdvancedDashboardCharts(dashboardData);
});
</script>
@endpush
