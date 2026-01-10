@extends('layouts.adminlte')

@section('title', 'Analytics Dashboard')
@section('page-title', 'Analytics Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    /* Modern Dashboard Styles - Glassmorphism & Neumorphism */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --info-gradient: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        --card-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        --card-background: rgba(255, 255, 255, 0.95);
        --glass-border: 1px solid rgba(255, 255, 255, 0.18);
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: 'Inter', 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* Glassmorphism KPI Cards */
    .kpi-card {
        background: var(--card-background);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 16px;
        border: var(--glass-border);
        box-shadow: var(--card-shadow);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .kpi-card:hover::before {
        opacity: 1;
    }

    .kpi-card.primary::before { background: var(--primary-gradient); }
    .kpi-card.success::before { background: var(--success-gradient); }
    .kpi-card.warning::before { background: var(--warning-gradient); }
    .kpi-card.info::before { background: var(--info-gradient); }
    .kpi-card.danger::before { background: var(--danger-gradient); }

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
        position: relative;
        overflow: hidden;
    }

    .kpi-card.primary .kpi-icon { background: var(--primary-gradient); }
    .kpi-card.success .kpi-icon { background: var(--success-gradient); }
    .kpi-card.warning .kpi-icon { background: var(--warning-gradient); }
    .kpi-card.info .kpi-icon { background: var(--info-gradient); }
    .kpi-card.danger .kpi-icon { background: var(--danger-gradient); }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 8px 0;
        line-height: 1;
    }

    .kpi-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .kpi-change {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        margin-top: 8px;
    }

    .kpi-change.positive {
        background: rgba(72, 187, 120, 0.1);
        color: #38a169;
    }

    .kpi-change.negative {
        background: rgba(245, 101, 101, 0.1);
        color: #e53e3e;
    }

    .kpi-change i {
        margin-right: 4px;
        font-size: 0.625rem;
    }

    /* Chart Cards */
    .chart-card {
        background: var(--card-background);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: var(--glass-border);
        box-shadow: var(--card-shadow);
        padding: 24px;
        margin-bottom: 24px;
    }

    .chart-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .chart-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #2d3748;
        margin: 0;
    }

    /* Activity Feed */
    .activity-item {
        padding: 12px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: flex-start;
        transition: background 0.2s;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background: rgba(0, 0, 0, 0.02);
        margin: 0 -12px;
        padding: 12px;
        border-radius: 8px;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 16px;
        flex-shrink: 0;
    }

    /* Notification Badge */
    .notification-item {
        padding: 12px;
        border-radius: 12px;
        margin-bottom: 8px;
        background: rgba(255, 255, 255, 0.7);
        border-left: 3px solid;
        transition: all 0.2s;
        cursor: pointer;
    }

    .notification-item:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateX(4px);
    }

    .notification-item.unread {
        background: rgba(66, 153, 225, 0.1);
        border-left-color: #4299e1;
    }

    /* Filter Controls */
    .filter-bar {
        background: var(--card-background);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: var(--glass-border);
        padding: 16px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-group label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #4a5568;
        margin: 0;
    }

    .btn-filter {
        background: white;
        border: 1px solid #e2e8f0;
        color: #4a5568;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-filter:hover,
    .btn-filter.active {
        background: var(--primary-gradient);
        border-color: transparent;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .kpi-value {
            font-size: 2rem;
        }
        
        .filter-bar {
            flex-direction: column;
            align-items: stretch;
        }
    }

    /* Loading Animation */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .loading {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #718096;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 16px;
        opacity: 0.3;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="kpi-card" style="padding: 24px; background: var(--primary-gradient); border: none;">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="text-white mb-2" style="font-weight: 700;">
                            Welcome back, {{ auth()->user()->username }}! 👋
                        </h2>
                        <p class="text-white mb-0" style="opacity: 0.9;">
                            Here's your system overview and analytics
                        </p>
                    </div>
                    <div class="d-flex gap-2 mt-3 mt-md-0">
                        <button type="button" class="btn btn-light btn-sm" onclick="refreshDashboard()">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                        <a href="{{ route('settings.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-cog mr-1"></i>Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-group">
            <label><i class="fas fa-calendar mr-1"></i>Period:</label>
            <a href="{{ route('dashboard', ['days' => 7]) }}" class="btn-filter {{ $days == 7 ? 'active' : '' }}">7 Days</a>
            <a href="{{ route('dashboard', ['days' => 30]) }}" class="btn-filter {{ $days == 30 ? 'active' : '' }}">30 Days</a>
            <a href="{{ route('dashboard', ['days' => 60]) }}" class="btn-filter {{ $days == 60 ? 'active' : '' }}">60 Days</a>
            <a href="{{ route('dashboard', ['days' => 90]) }}" class="btn-filter {{ $days == 90 ? 'active' : '' }}">90 Days</a>
        </div>
        <div class="ml-auto d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportDashboard()">
                <i class="fas fa-download mr-1"></i>Export
            </button>
        </div>
    </div>

    <!-- Main KPI Cards -->
    <div class="stats-grid">
        <!-- Total Revenue -->
        <div class="kpi-card primary">
            <div style="padding: 24px;">
                <div class="kpi-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="kpi-label">Total Revenue</div>
                <div class="kpi-value">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                @if(isset($stats['this_month_revenue']) && $stats['this_month_revenue'] > 0)
                <div class="kpi-change positive">
                    <i class="fas fa-arrow-up"></i>
                    ${{ number_format($stats['this_month_revenue'], 2) }} this month
                </div>
                @endif
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="kpi-card success">
            <div style="padding: 24px;">
                <div class="kpi-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="kpi-label">Total Bookings</div>
                <div class="kpi-value">{{ number_format($stats['total_bookings'] ?? 0) }}</div>
                @if(isset($stats['booking_growth']))
                <div class="kpi-change {{ $stats['booking_growth'] >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-arrow-{{ $stats['booking_growth'] >= 0 ? 'up' : 'down' }}"></i>
                    {{ abs($stats['booking_growth']) }}% vs yesterday
                </div>
                @endif
            </div>
        </div>

        <!-- Occupancy Rate -->
        <div class="kpi-card info">
            <div style="padding: 24px;">
                <div class="kpi-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="kpi-label">Occupancy Rate</div>
                <div class="kpi-value">{{ number_format($stats['occupancy_rate'] ?? 0, 1) }}%</div>
                <div class="kpi-change positive">
                    <i class="fas fa-info-circle"></i>
                    {{ number_format($stats['available_rate'] ?? 0, 1) }}% available
                </div>
            </div>
        </div>

        <!-- Total Booths -->
        <div class="kpi-card warning">
            <div style="padding: 24px;">
                <div class="kpi-icon">
                    <i class="fas fa-store"></i>
                </div>
                <div class="kpi-label">Total Booths</div>
                <div class="kpi-value">{{ number_format($stats['total_booths'] ?? 0) }}</div>
                <div class="kpi-change positive">
                    <i class="fas fa-check-circle"></i>
                    {{ number_format($stats['available_booths'] ?? 0) }} available
                </div>
            </div>
        </div>

        <!-- Total Clients -->
        <div class="kpi-card primary">
            <div style="padding: 24px;">
                <div class="kpi-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="kpi-label">Total Clients</div>
                <div class="kpi-value">{{ number_format($stats['total_clients'] ?? 0) }}</div>
            </div>
        </div>

        <!-- Today's Activity -->
        <div class="kpi-card danger">
            <div style="padding: 24px;">
                <div class="kpi-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="kpi-label">Today's Bookings</div>
                <div class="kpi-value">{{ number_format($stats['today_bookings'] ?? 0) }}</div>
                @if(isset($stats['today_revenue']) && $stats['today_revenue'] > 0)
                <div class="kpi-change positive">
                    <i class="fas fa-dollar-sign"></i>
                    ${{ number_format($stats['today_revenue'], 2) }} revenue
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Revenue & Booking Trends -->
        <div class="col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-line mr-2 text-primary"></i>Revenue & Booking Trends
                    </h3>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary active" onclick="switchChartView('combined')">Combined</button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchChartView('bookings')">Bookings</button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchChartView('revenue')">Revenue</button>
                    </div>
                </div>
                <canvas id="trendChart" height="80"></canvas>
            </div>

            <!-- Booth Status Distribution -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-pie mr-2 text-success"></i>Booth Status Distribution
                    </h3>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="boothStatusChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6">
                        <div class="mt-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 p-3" style="background: rgba(25, 135, 84, 0.1); border-radius: 8px;">
                                <div>
                                    <div class="font-weight-bold">Available</div>
                                    <div class="text-muted small">{{ $stats['available_booths'] ?? 0 }} booths</div>
                                </div>
                                <div class="h4 mb-0 text-success">{{ $stats['available_booths'] > 0 ? number_format(($stats['available_booths'] / $stats['total_booths']) * 100, 1) : 0 }}%</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-3 p-3" style="background: rgba(255, 193, 7, 0.1); border-radius: 8px;">
                                <div>
                                    <div class="font-weight-bold">Reserved</div>
                                    <div class="text-muted small">{{ $stats['reserved_booths'] ?? 0 }} booths</div>
                                </div>
                                <div class="h4 mb-0 text-warning">{{ $stats['reserved_booths'] > 0 ? number_format(($stats['reserved_booths'] / $stats['total_booths']) * 100, 1) : 0 }}%</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-3 p-3" style="background: rgba(13, 202, 240, 0.1); border-radius: 8px;">
                                <div>
                                    <div class="font-weight-bold">Confirmed</div>
                                    <div class="text-muted small">{{ $stats['confirmed_booths'] ?? 0 }} booths</div>
                                </div>
                                <div class="h4 mb-0 text-info">{{ $stats['confirmed_booths'] > 0 ? number_format(($stats['confirmed_booths'] / $stats['total_booths']) * 100, 1) : 0 }}%</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-3" style="background: rgba(33, 37, 41, 0.1); border-radius: 8px;">
                                <div>
                                    <div class="font-weight-bold">Paid</div>
                                    <div class="text-muted small">{{ $stats['paid_booths'] ?? 0 }} booths</div>
                                </div>
                                <div class="h4 mb-0 text-dark">{{ $stats['paid_booths'] > 0 ? number_format(($stats['paid_booths'] / $stats['total_booths']) * 100, 1) : 0 }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Notifications -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-bell mr-2 text-warning"></i>Notifications
                    </h3>
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-link">View All</a>
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentNotifications ?? [] as $notification)
                    <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}" onclick="window.location='{{ route('notifications.index') }}'">
                        <div class="font-weight-bold mb-1">{{ $notification->title }}</div>
                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($notification->message, 60) }}</div>
                        <div class="small text-muted mt-1">
                            <i class="fas fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <p class="mb-0">No new notifications</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-history mr-2 text-info"></i>Recent Activity
                    </h3>
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentActivities ?? [] as $activity)
                    <div class="activity-item">
                        <div class="activity-icon" style="background: rgba(102, 126, 234, 0.1); color: #667eea;">
                            @if(str_contains(strtolower($activity->action), 'create')) <i class="fas fa-plus"></i>
                            @elseif(str_contains(strtolower($activity->action), 'update')) <i class="fas fa-edit"></i>
                            @elseif(str_contains(strtolower($activity->action), 'delete')) <i class="fas fa-trash"></i>
                            @else <i class="fas fa-circle"></i>
                            @endif
                        </div>
                        <div style="flex: 1;">
                            <div class="font-weight-bold">{{ $activity->description ?? $activity->action }}</div>
                            <div class="small text-muted">
                                @if($activity->user){{ $activity->user->username }} • @endif{{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p class="mb-0">No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>

            @if($isAdmin && !empty($topUsers))
            <!-- Top Performers -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-trophy mr-2 text-warning"></i>Top Performers
                    </h3>
                </div>
                <div>
                    @foreach($topUsers as $index => $user)
                    <div class="activity-item">
                        <div class="activity-icon" style="background: {{ $index < 3 ? 'rgba(255, 193, 7, 0.2)' : 'rgba(0,0,0,0.05)' }}; color: {{ $index < 3 ? '#ffc107' : '#718096' }};">
                            <strong>#{{ $index + 1 }}</strong>
                        </div>
                        <div style="flex: 1;">
                            <div class="font-weight-bold">{{ $user['username'] }}</div>
                            <div class="small text-muted">
                                {{ $user['reserve'] + $user['booking'] + $user['paid'] }} total booths
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($isAdmin)
    <!-- User Performance Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-users-cog mr-2 text-primary"></i>User Performance
                    </h3>
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-link">View All Users</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Type</th>
                                <th>Reserved</th>
                                <th>Confirmed</th>
                                <th>Paid</th>
                                <th>Total</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($userStats ?? [] as $stat)
                            <tr>
                                <td><strong>{{ $stat['username'] ?? 'N/A' }}</strong></td>
                                <td>
                                    <span class="badge badge-{{ ($stat['type'] ?? '') == 'Admin' ? 'danger' : 'secondary' }}">
                                        {{ $stat['type'] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $stat['reserve'] ?? 0 }}</td>
                                <td>{{ $stat['booking'] ?? 0 }}</td>
                                <td>{{ $stat['paid'] ?? 0 }}</td>
                                <td><strong>{{ ($stat['reserve'] ?? 0) + ($stat['booking'] ?? 0) + ($stat['paid'] ?? 0) }}</strong></td>
                                <td>
                                    @php
                                        $total = ($stat['reserve'] ?? 0) + ($stat['booking'] ?? 0) + ($stat['paid'] ?? 0);
                                        $percentage = ($stats['total_booths'] ?? 1) > 0 ? ($total / ($stats['total_booths'] ?? 1)) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 24px; border-radius: 12px;">
                                        <div class="progress-bar bg-gradient-primary" role="progressbar" style="width: {{ min($percentage, 100) }}%; border-radius: 12px;">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <p class="mb-0">No user data available</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let trendChart, boothStatusChart;
let currentChartView = 'combined';

// Revenue & Booking Trends Chart
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    const bookingDates = @json($bookingTrendDates ?? []);
    const bookingCounts = @json($bookingTrendCounts ?? []);
    const revenueData = @json($revenueTrendData ?? []);

    trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: bookingDates,
            datasets: [
                {
                    label: 'Bookings',
                    data: bookingCounts,
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y',
                    hidden: currentChartView === 'revenue'
                },
                {
                    label: 'Revenue ($)',
                    data: revenueData,
                    borderColor: 'rgb(72, 187, 120)',
                    backgroundColor: 'rgba(72, 187, 120, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1',
                    hidden: currentChartView === 'bookings'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: currentChartView !== 'revenue',
                    position: 'left',
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: currentChartView !== 'bookings',
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// Booth Status Chart
const boothStatusCtx = document.getElementById('boothStatusChart');
if (boothStatusCtx) {
    boothStatusChart = new Chart(boothStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Reserved', 'Confirmed', 'Paid'],
            datasets: [{
                data: [
                    {{ $stats['available_booths'] ?? 0 }},
                    {{ $stats['reserved_booths'] ?? 0 }},
                    {{ $stats['confirmed_booths'] ?? 0 }},
                    {{ $stats['paid_booths'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(13, 202, 240, 0.8)',
                    'rgba(33, 37, 41, 0.8)'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            label += context.parsed + ' (' + percentage + '%)';
                            return label;
                        }
                    }
                }
            }
        }
    });
}

function switchChartView(view) {
    currentChartView = view;
    
    // Update button states
    document.querySelectorAll('.btn-outline-primary').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Update chart visibility
    if (trendChart) {
        trendChart.data.datasets[0].hidden = view === 'revenue';
        trendChart.data.datasets[1].hidden = view === 'bookings';
        trendChart.options.scales.y.display = view !== 'revenue';
        trendChart.options.scales.y1.display = view !== 'bookings';
        trendChart.update();
    }
}

function refreshDashboard() {
    showLoading();
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

function exportDashboard() {
    Swal.fire({
        icon: 'info',
        title: 'Export Dashboard',
        text: 'Export functionality will be available soon.',
        confirmButtonColor: '#667eea'
    });
}

// Auto-refresh every 5 minutes (optional)
// setInterval(refreshDashboard, 300000);
</script>
@endpush
