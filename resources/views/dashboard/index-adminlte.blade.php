@extends('layouts.adminlte')

@section('title', 'Analytics Dashboard')
@section('page-title', 'Analytics Dashboard')
@section('breadcrumb', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
    /* Modern Dashboard Styles - Clean & Clear Design */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #1cc88a 0%, #17a673 100%);
        --warning-gradient: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        --info-gradient: linear-gradient(135deg, #36b9cc 0%, #2c9faf 100%);
        --danger-gradient: linear-gradient(135deg, #e74a3b 0%, #c23321 100%);
        --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        --card-hover-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        --card-background: #ffffff;
        --border-color: #e2e8f0;
    }

    body {
        background: #f8f9fc;
        font-family: 'Inter', 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Clean KPI Cards */
    .kpi-card {
        background: var(--card-background);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        cursor: pointer;
    }
    
    .kpi-card a {
        text-decoration: none;
        color: inherit;
        display: block;
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
        transform: translateY(-4px);
        box-shadow: var(--card-hover-shadow);
        border-color: rgba(102, 126, 234, 0.2);
    }
    
    .kpi-card:hover .kpi-value {
        color: #667eea;
        transition: color 0.2s ease;
    }
    
    .kpi-card a:hover {
        text-decoration: none;
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
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        margin-bottom: 12px;
        position: relative;
    }

    .kpi-card.primary .kpi-icon { background: var(--primary-gradient); }
    .kpi-card.success .kpi-icon { background: var(--success-gradient); }
    .kpi-card.warning .kpi-icon { background: var(--warning-gradient); }
    .kpi-card.info .kpi-icon { background: var(--info-gradient); }
    .kpi-card.danger .kpi-icon { background: var(--danger-gradient); }

    .kpi-value {
        font-size: 2.25rem;
        font-weight: 700;
        color: #1a202c;
        margin: 6px 0;
        line-height: 1.2;
    }

    .kpi-label {
        font-size: 0.8rem;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .kpi-change {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        margin-top: 6px;
    }

    .kpi-change.positive {
        background: rgba(28, 200, 138, 0.1);
        color: #1cc88a;
    }

    .kpi-change.negative {
        background: rgba(231, 74, 59, 0.1);
        color: #e74a3b;
    }

    .kpi-change i {
        margin-right: 4px;
        font-size: 0.625rem;
    }

    /* Chart Cards */
    .chart-card {
        background: var(--card-background);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        padding: 24px;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        box-shadow: var(--card-hover-shadow);
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a202c;
        margin: 0;
    }

    /* Activity Feed */
    .activity-item {
        padding: 12px 0;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: flex-start;
        transition: all 0.2s ease;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-item:hover {
        background: #f8f9fc;
        margin: 0 -12px;
        padding: 12px;
        border-radius: 8px;
    }

    .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 14px;
        flex-shrink: 0;
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    /* Notification Badge */
    .notification-item {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 8px;
        background: #f8f9fc;
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .notification-item:hover {
        background: #ffffff;
        transform: translateX(2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .notification-item.unread {
        background: rgba(102, 126, 234, 0.05);
        border-left-color: #667eea;
    }

    /* Filter Controls */
    .filter-bar {
        background: var(--card-background);
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 16px 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        box-shadow: var(--card-shadow);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-group label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #4a5568;
        margin: 0;
    }

    .btn-filter {
        background: #f8f9fc;
        border: 1px solid var(--border-color);
        color: #4a5568;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-filter:hover,
    .btn-filter.active {
        background: var(--primary-gradient);
        border-color: transparent;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    
    /* Content Wrapper */
    .content-wrapper {
        background: #f8f9fc;
    }
    
    /* Card Enhancements */
    .card {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        box-shadow: var(--card-hover-shadow);
    }
    
    .card-header {
        background: #ffffff;
        border-bottom: 1px solid var(--border-color);
        padding: 16px 24px;
        border-radius: 12px 12px 0 0;
    }
    
    .card-body {
        padding: 24px;
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
        
        .chart-card {
            padding: 16px;
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
    
    /* Content Area Spacing */
    .content-wrapper .content {
        padding: 24px;
    }
    
    /* Page Header */
    .content-header {
        background: transparent;
        padding: 0 0 24px 0;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .content-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1a202c;
        margin: 0;
    }
    
    /* Breadcrumb */
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        font-size: 0.875rem;
    }
    
    .breadcrumb-item a {
        color: #667eea;
        text-decoration: none;
    }
    
    .breadcrumb-item.active {
        color: #718096;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        color: #718096;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 12px;
        opacity: 0.25;
        color: #cbd5e0;
    }
    
    /* Table Enhancements */
    .table {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table thead {
        background: #f8f9fc;
    }
    
    .table thead th {
        font-weight: 600;
        color: #4a5568;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border-bottom: 2px solid var(--border-color);
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background: #f8f9fc;
    }
    
    .table tbody td {
        padding: 12px 16px;
        vertical-align: middle;
    }
    
    /* Progress Bar */
    .progress {
        height: 24px;
        border-radius: 12px;
        background: #e2e8f0;
        overflow: hidden;
    }
    
    .progress-bar {
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bg-gradient-primary {
        background: var(--primary-gradient) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Welcome Header - Clean Design -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body" style="padding: 28px 32px;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="text-white mb-2" style="font-weight: 700; font-size: 1.75rem;">
                                Welcome back, {{ auth()->user()->username }}! 👋
                            </h2>
                            <p class="text-white mb-0" style="opacity: 0.95; font-size: 0.95rem;">
                                Here's your system overview and analytics
                            </p>
                        </div>
                        <div class="d-flex gap-2 mt-3 mt-md-0">
                            <button type="button" class="btn btn-light btn-sm" onclick="refreshDashboard()" style="border-radius: 8px; padding: 8px 16px; font-weight: 500;">
                                <i class="fas fa-sync-alt mr-1"></i>Refresh
                            </button>
                            <a href="{{ route('settings.index') }}" class="btn btn-light btn-sm" style="border-radius: 8px; padding: 8px 16px; font-weight: 500;">
                                <i class="fas fa-cog mr-1"></i>Settings
                            </a>
                        </div>
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
        <div class="kpi-card primary" onclick="window.location.href='{{ route('reports.sales') }}'">
            <a href="{{ route('reports.sales') }}">
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
                    <div class="mt-2" style="font-size: 0.75rem; color: #667eea; font-weight: 600;">
                        <i class="fas fa-external-link-alt mr-1"></i>View Details
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Bookings -->
        <div class="kpi-card success" onclick="window.location.href='{{ route('books.index') }}'">
            <a href="{{ route('books.index') }}">
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
                    <div class="mt-2" style="font-size: 0.75rem; color: #1cc88a; font-weight: 600;">
                        <i class="fas fa-external-link-alt mr-1"></i>View All Bookings
                    </div>
                </div>
            </a>
        </div>

        <!-- Occupancy Rate -->
        <div class="kpi-card info" onclick="window.location.href='{{ route('booths.index') }}'">
            <a href="{{ route('booths.index') }}">
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
                    <div class="mt-2" style="font-size: 0.75rem; color: #36b9cc; font-weight: 600;">
                        <i class="fas fa-external-link-alt mr-1"></i>View Booths
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Booths -->
        <div class="kpi-card warning" onclick="window.location.href='{{ route('booths.index') }}'">
            <a href="{{ route('booths.index') }}">
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
                    <div class="mt-2" style="font-size: 0.75rem; color: #f6c23e; font-weight: 600;">
                        <i class="fas fa-external-link-alt mr-1"></i>View All Booths
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Clients -->
        <div class="kpi-card primary" onclick="window.location.href='{{ route('clients.index') }}'">
            <a href="{{ route('clients.index') }}">
                <div style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="kpi-label">Total Clients</div>
                    <div class="kpi-value">{{ number_format($stats['total_clients'] ?? 0) }}</div>
                    <div class="mt-2" style="font-size: 0.75rem; color: #667eea; font-weight: 600;">
                        <i class="fas fa-external-link-alt mr-1"></i>View All Clients
                    </div>
                </div>
            </a>
        </div>

        <!-- Today's Activity -->
        <div class="kpi-card danger" onclick="window.location.href='{{ route('books.index', ['date_from' => \Carbon\Carbon::today()->format('Y-m-d'), 'date_to' => \Carbon\Carbon::today()->format('Y-m-d')]) }}'">
            <a href="{{ route('books.index', ['date_from' => \Carbon\Carbon::today()->format('Y-m-d'), 'date_to' => \Carbon\Carbon::today()->format('Y-m-d')]) }}">
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
                    <div class="mt-2" style="font-size: 0.75rem; color: #e74a3b; font-weight: 600;">
                        <i class="fas fa-external-link-alt mr-1"></i>View Today's Bookings
                    </div>
                </div>
            </a>
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
                            @php
                                // Calculate maxTotal once before the loop for better performance
                                $maxTotal = 0;
                                if (!empty($userStats)) {
                                    foreach ($userStats as $s) {
                                        $sTotal = ($s['reserve'] ?? 0) + ($s['booking'] ?? 0) + ($s['paid'] ?? 0);
                                        if ($sTotal > $maxTotal) {
                                            $maxTotal = $sTotal;
                                        }
                                    }
                                }
                                // If maxTotal is 0, use total_booths as fallback
                                if ($maxTotal == 0) {
                                    $maxTotal = $stats['total_booths'] ?? 1;
                                }
                            @endphp
                            @forelse($userStats ?? [] as $stat)
                            <tr style="cursor: pointer;" onclick="window.location.href='{{ route('users.show', $stat['id'] ?? '#') }}'">
                                <td>
                                    <a href="{{ route('users.show', $stat['id'] ?? '#') }}" style="text-decoration: none; color: inherit; font-weight: 600;">
                                        {{ $stat['username'] ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-{{ ($stat['type'] ?? '') == 'Admin' ? 'danger' : 'secondary' }}">
                                        {{ $stat['type'] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('booths.index', ['status' => \App\Models\Booth::STATUS_RESERVED, 'userid' => $stat['id'] ?? null]) }}" style="text-decoration: none; color: inherit;">
                                        {{ $stat['reserve'] ?? 0 }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('booths.index', ['status' => \App\Models\Booth::STATUS_CONFIRMED, 'userid' => $stat['id'] ?? null]) }}" style="text-decoration: none; color: inherit;">
                                        {{ $stat['booking'] ?? 0 }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('booths.index', ['status' => \App\Models\Booth::STATUS_PAID, 'userid' => $stat['id'] ?? null]) }}" style="text-decoration: none; color: inherit;">
                                        {{ $stat['paid'] ?? 0 }}
                                    </a>
                                </td>
                                <td><strong>{{ ($stat['reserve'] ?? 0) + ($stat['booking'] ?? 0) + ($stat['paid'] ?? 0) }}</strong></td>
                                <td>
                                    @php
                                        $userTotal = ($stat['reserve'] ?? 0) + ($stat['booking'] ?? 0) + ($stat['paid'] ?? 0);
                                        
                                        // Calculate percentage relative to the top performer
                                        $percentage = $maxTotal > 0 ? ($userTotal / $maxTotal) * 100 : 0;
                                        
                                        // Ensure percentage doesn't exceed 100%
                                        $percentage = min($percentage, 100);
                                        
                                        // Determine progress bar color based on performance
                                        $progressColor = 'bg-gradient-primary';
                                        if ($percentage >= 80) {
                                            $progressColor = 'bg-gradient-success';
                                        } elseif ($percentage >= 50) {
                                            $progressColor = 'bg-gradient-info';
                                        } elseif ($percentage >= 25) {
                                            $progressColor = 'bg-gradient-warning';
                                        } else {
                                            $progressColor = 'bg-gradient-secondary';
                                        }
                                    @endphp
                                    <div class="progress" style="height: 28px; border-radius: 14px; background-color: #e9ecef; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                                        <div class="progress-bar {{ $progressColor }}" 
                                             role="progressbar" 
                                             style="width: {{ $percentage }}%; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem; color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"
                                             aria-valuenow="{{ $percentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            @if($percentage >= 10)
                                                {{ number_format($percentage, 1) }}%
                                            @else
                                                <span style="color: #495057; margin-left: 8px;">{{ number_format($percentage, 1) }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                        {{ $userTotal }} / {{ $maxTotal }} booths
                                    </small>
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

