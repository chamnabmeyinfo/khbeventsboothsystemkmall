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
<link rel="stylesheet" href="{{ asset('css/dashboard/modern-v1.css') }}">
<style>
    /* Inline overrides for specific Looker-Studio-like touches */
    .kpi-card.primary { background: linear-gradient(to bottom right, #ffffff, #f0f9ff); border-left: 4px solid #3b82f6; }
    .kpi-card.success { background: linear-gradient(to bottom right, #ffffff, #f0fdf4); border-left: 4px solid #10b981; }
    .kpi-card.warning { background: linear-gradient(to bottom right, #ffffff, #fffbeb); border-left: 4px solid #f59e0b; }
    .kpi-card.info { background: linear-gradient(to bottom right, #ffffff, #f5f3ff); border-left: 4px solid #6366f1; }
    
    .db-header-actions .btn {
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <!-- Header -->
    <header class="db-header animate-fade-in">
        <div class="db-title">
            <h1>Analytics Overview</h1>
            <p>Welcome back, <strong>{{ auth()->user()->username }}</strong>. Here's what's happening today.</p>
        </div>
        <div class="db-header-actions">
            <button class="btn btn-outline-secondary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('settings.index') }}" class="btn btn-primary">
                <i class="fas fa-cog"></i> Settings
            </a>
            @endif
        </div>
    </header>

    <!-- KPI Grid -->
    <div class="kpi-grid">
        <div class="kpi-card primary animate-fade-in" style="animation-delay: 0.1s">
            <div class="kpi-header">
                <div class="kpi-label">Total Booths</div>
                <div class="kpi-icon" style="background: #dbeafe; color: #1d4ed8;">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            <div class="kpi-value text-primary">{{ $stats['total_booths'] }}</div>
            <div class="kpi-trend">
                <span class="text-muted">Capacity Status</span>
            </div>
        </div>

        <div class="kpi-card success animate-fade-in" style="animation-delay: 0.2s">
            <div class="kpi-header">
                <div class="kpi-label">Available</div>
                <div class="kpi-icon" style="background: #dcfce7; color: #15803d;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="kpi-value text-success">{{ $stats['available_booths'] }}</div>
            <div class="progress-slim mt-2">
                <div class="progress-bar-fill" style="width: {{ $stats['total_booths'] > 0 ? ($stats['available_booths'] / $stats['total_booths']) * 100 : 0 }}%; background: #10b981;"></div>
            </div>
        </div>

        <div class="kpi-card warning animate-fade-in" style="animation-delay: 0.3s">
            <div class="kpi-header">
                <div class="kpi-label">Reserved</div>
                <div class="kpi-icon" style="background: #fef3c7; color: #b45309;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="kpi-value text-warning">{{ $stats['reserved_booths'] }}</div>
            <div class="kpi-trend trend-up">
                <i class="fas fa-arrow-up"></i> Pending Validation
            </div>
        </div>

        <div class="kpi-card info animate-fade-in" style="animation-delay: 0.4s">
            <div class="kpi-header">
                <div class="kpi-label">Revenue (MTD)</div>
                <div class="kpi-icon" style="background: #ede9fe; color: #6d28d9;">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="kpi-value" style="color: #6d28d9;">${{ number_format($stats['this_month_revenue'] ?? 0, 2) }}</div>
            <div class="kpi-trend trend-up">
                <i class="fas fa-chart-line"></i> Monthly Growth
            </div>
        </div>

        <div class="kpi-card info animate-fade-in" style="animation-delay: 0.45s">
            <div class="kpi-header">
                <div class="kpi-label">Potential Revenue</div>
                <div class="kpi-icon" style="background: #ffedd5; color: #ea580c;">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
            <div class="kpi-value" style="color: #ea580c;">${{ number_format($stats['potential_revenue'] ?? 0, 2) }}</div>
            <div class="kpi-trend">
                <i class="fas fa-clock"></i> Projected
            </div>
        </div>
    </div>

    <!-- Main Analytics Grid -->
    <div class="db-grid">
        <!-- Trends Section -->
        <section class="animate-fade-in" style="animation-delay: 0.5s">
            <div class="analytics-card">
                <div class="card-header-alt">
                    <h2 class="card-title-alt">Booking Trends</h2>
                    <div class="badge rounded-pill bg-light text-dark">Last {{ $days }} Days</div>
                </div>
                <div style="height: 300px;">
                    <canvas id="bookingTrendsChartAdvanced"></canvas>
                </div>
                
                <div class="card-header-alt mt-5">
                    <h2 class="card-title-alt">Revenue Progression</h2>
                </div>
                <div style="height: 300px;">
                    <canvas id="revenueTrendsChartAdvanced"></canvas>
                </div>
            </div>
        </section>

        <!-- Status Distribution -->
        <section class="animate-fade-in" style="animation-delay: 0.6s">
            <div class="analytics-card">
                <div class="card-header-alt">
                    <h2 class="card-title-alt">Booth Distribution</h2>
                </div>
                <div style="height: 350px;">
                    <canvas id="boothStatusChartAdvanced"></canvas>
                </div>
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Occupancy Rate</span>
                        <span class="fw-bold">{{ $stats['occupancy_rate'] ?? 0 }}%</span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 10px;">
                        <div class="progress-bar bg-primary" style="width: {{ $stats['occupancy_rate'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Bottom Section: Recent Activity & Performance -->
    <div class="db-grid mt-4">
        <!-- Recent Bookings Table -->
        <section class="animate-fade-in" style="animation-delay: 0.7s">
            <div class="analytics-card">
                <div class="card-header-alt">
                    <h2 class="card-title-alt">Recent Bookings</h2>
                    <a href="{{ route('books.index') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
                </div>
                <div class="custom-table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Client / Company</th>
                                <th>Booths</th>
                                <th>Date</th>
                                <th>Sales Rep</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings ?? [] as $booking)
                            <tr>
                                <td>
                                    <div class="user-avatar-pill">
                                        <div class="avatar-initial">{{ substr($booking->client->name ?? 'C', 0, 1) }}</div>
                                        <div>
                                            <div class="fw-bold">{{ $booking->client->name ?? 'Unknown' }}</div>
                                            <div class="text-muted small">{{ $booking->client->company ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary" style="background: #e0e7ff;">
                                        {{ count(json_decode($booking->boothid, true) ?? []) }} Units
                                    </span>
                                </td>
                                <td class="text-muted">{{ $booking->date_book ? $booking->date_book->format('M d, H:i') : 'N/A' }}</td>
                                <td>
                                    <span class="text-main fw-600">{{ $booking->user->username ?? 'System' }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">No recent activity detected</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Top Users / Sales Performance -->
        @if($isAdmin && !empty($userStats))
        <section class="animate-fade-in" style="animation-delay: 0.8s">
            <div class="analytics-card">
                <div class="card-header-alt">
                    <h2 class="card-title-alt">Top Performers</h2>
                </div>
                <div class="custom-table-container">
                    <table class="custom-table">
                        <tbody>
                            @foreach(array_slice($userStats, 0, 5) as $stat)
                            <tr>
                                <td>
                                    <div class="user-avatar-pill">
                                        <div class="avatar-initial" style="background: {{ $loop->first ? 'var(--primary-gradient)' : '#94a3b8' }}">
                                            {{ $loop->iteration }}
                                        </div>
                                        <span class="fw-bold">{{ $stat['username'] }}</span>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-800 text-main">{{ $stat['reserve'] + $stat['booking'] + $stat['paid'] }}</div>
                                    <div class="text-muted x-small">CONVERSIONS</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 p-3 rounded-3" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                    <p class="small text-muted mb-0">Performance is calculated based on total unique booth reservations and confirmations across all active events.</p>
                </div>
            </div>
        </section>
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
