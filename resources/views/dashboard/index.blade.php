@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/chartjs/chart.min.css') }}">
<style>
/* ============================================
   DESKTOP STYLES - PRESERVED
   ============================================ */
@media (min-width: 769px) {
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .stat-card.border-primary { border-left-color: #0d6efd; }
    .stat-card.border-success { border-left-color: #198754; }
    .stat-card.border-warning { border-left-color: #ffc107; }
    .stat-card.border-info { border-left-color: #0dcaf0; }
    .stat-card.border-dark { border-left-color: #212529; }
    .stat-card.border-secondary { border-left-color: #6c757d; }
    .stat-card.border-danger { border-left-color: #dc3545; }
}

/* ============================================
   MOBILE APP UX/UI DESIGN
   ============================================ */
@media (max-width: 768px) {
    /* Hide Navbar on Mobile */
    nav.navbar,
    .navbar,
    .navbar-expand-lg {
        display: none !important;
    }
    
    /* App Container */
    body {
        background: #f5f7fa !important;
        padding-bottom: 80px !important; /* Space for bottom nav */
        margin: 0 !important;
    }
    
    main.container-fluid,
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
    }
    
    /* Remove any wrapper padding */
    .content-wrapper,
    .content {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* App-Style Header */
    .app-header {
        position: sticky !important;
        top: 0 !important;
        z-index: 100 !important;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        padding: 20px !important;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1) !important;
        margin: 0 0 24px 0 !important;
        width: 100% !important;
        display: block !important;
    }
    
    .app-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .app-header-left {
        flex: 1;
    }
    
    .app-header-greeting {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        margin-bottom: 4px;
    }
    
    .app-header-title {
        font-size: 24px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.02em;
    }
    
    .app-header-actions {
        display: flex;
        gap: 12px;
    }
    
    .app-header-btn {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.2s;
    }
    
    .app-header-btn:active {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0.95);
    }
    
    /* Stats Section - App Style */
    .app-stats-section {
        padding: 0 20px !important;
        margin-bottom: 24px !important;
        width: 100% !important;
        display: block !important;
    }
    
    .app-stats-grid {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 12px !important;
        width: 100% !important;
    }
    
    .app-stat-card {
        background: white !important;
        border-radius: 20px !important;
        padding: 20px !important;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08) !important;
        position: relative !important;
        overflow: hidden !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: none !important;
        margin: 0 !important;
    }
    
    .app-stat-card:active {
        transform: scale(0.97);
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1);
    }
    
    .app-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--stat-color, #667eea);
    }
    
    .app-stat-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #9ca3af;
        margin-bottom: 12px;
        display: block;
    }
    
    .app-stat-value {
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
        margin: 0;
        letter-spacing: -0.03em;
        color: var(--stat-color, #111827);
    }
    
    .app-stat-icon {
        position: absolute;
        bottom: 16px;
        right: 16px;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--stat-bg, rgba(102, 126, 234, 0.1));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--stat-color, #667eea);
        opacity: 0.8;
    }
    
    .app-stat-card.primary {
        --stat-color: #667eea;
        --stat-bg: rgba(102, 126, 234, 0.1);
    }
    
    .app-stat-card.success {
        --stat-color: #10b981;
        --stat-bg: rgba(16, 185, 129, 0.1);
    }
    
    .app-stat-card.warning {
        --stat-color: #f59e0b;
        --stat-bg: rgba(245, 158, 11, 0.1);
    }
    
    .app-stat-card.info {
        --stat-color: #3b82f6;
        --stat-bg: rgba(59, 130, 246, 0.1);
    }
    
    .app-stat-card.dark {
        --stat-color: #1f2937;
        --stat-bg: rgba(31, 41, 55, 0.1);
    }
    
    .app-stat-card.secondary {
        --stat-color: #6b7280;
        --stat-bg: rgba(107, 114, 128, 0.1);
    }
    
    .app-stat-card.danger {
        --stat-color: #ef4444;
        --stat-bg: rgba(239, 68, 68, 0.1);
    }
    
    /* Section Headers */
    .app-section {
        padding: 0 20px;
        margin-bottom: 24px;
    }
    
    .app-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .app-section-title {
        font-size: 20px;
        font-weight: 800;
        color: #111827;
        margin: 0;
        letter-spacing: -0.01em;
    }
    
    .app-section-link {
        font-size: 14px;
        font-weight: 600;
        color: #667eea;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    /* Chart Cards - App Style */
    .app-chart-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 16px;
    }
    
    .app-chart-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .app-chart-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        font-size: 20px;
    }
    
    .app-chart-title {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }
    
    /* Recent Bookings - App Style */
    .app-recent-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }
    
    .app-recent-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .app-recent-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s;
    }
    
    .app-recent-item:last-child {
        border-bottom: none;
    }
    
    .app-recent-item:active {
        background: #f9fafb;
        margin: 0 -20px;
        padding-left: 20px;
        padding-right: 20px;
        border-radius: 12px;
    }
    
    .app-recent-item-left {
        flex: 1;
    }
    
    .app-recent-item-id {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }
    
    .app-recent-item-client {
        font-size: 13px;
        color: #6b7280;
    }
    
    .app-recent-item-right {
        text-align: right;
    }
    
    .app-recent-item-booths {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }
    
    .app-recent-item-date {
        font-size: 12px;
        color: #9ca3af;
    }
    
    /* Empty State */
    .app-empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }
    
    .app-empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 36px;
        color: #d1d5db;
    }
    
    .app-empty-text {
        font-size: 16px;
        font-weight: 500;
        margin: 0;
    }
    
    /* Bottom Navigation - Native App Style */
    .app-bottom-nav {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        background: white !important;
        border-top: 1px solid #e5e7eb !important;
        padding: 8px 0 calc(8px + env(safe-area-inset-bottom)) !important;
        z-index: 1000 !important;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05) !important;
        display: flex !important;
        justify-content: space-around !important;
        align-items: center !important;
        width: 100% !important;
    }
    
    .app-bottom-nav-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 8px 4px;
        text-decoration: none;
        color: #9ca3af;
        transition: all 0.2s;
        min-height: 64px;
        position: relative;
    }
    
    .app-bottom-nav-item i {
        font-size: 22px;
        margin-bottom: 4px;
        transition: all 0.2s;
    }
    
    .app-bottom-nav-item span {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .app-bottom-nav-item.active {
        color: #667eea;
    }
    
    .app-bottom-nav-item.active::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 3px;
        background: #667eea;
        border-radius: 0 0 3px 3px;
    }
    
    .app-bottom-nav-item:active {
        transform: scale(0.95);
    }
    
    /* Quick Actions - Floating */
    .app-quick-actions {
        position: fixed;
        bottom: 90px;
        right: 20px;
        z-index: 999;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .app-fab {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        transition: all 0.3s;
    }
    
    .app-fab:active {
        transform: scale(0.9);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    
    /* Hide desktop elements on mobile */
    .d-none.d-md-flex,
    .row.d-none.d-md-flex {
        display: none !important;
    }
    
    /* Force mobile elements to show */
    .d-md-none {
        display: block !important;
    }
    
    /* Override any conflicting styles */
    .card {
        margin-bottom: 16px !important;
    }
    
    /* Ensure proper spacing */
    .app-section {
        padding: 0 20px !important;
        margin-bottom: 24px !important;
        width: 100% !important;
        display: block !important;
    }
    
    .app-chart-card,
    .app-recent-card {
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
}
</style>
@endpush

@section('content')
<!-- Desktop Header -->
<div class="row mb-4 d-none d-md-flex">
    <div class="col">
        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
        <p class="text-muted">Welcome back, {{ auth()->user()->username }}! Here's your overview.</p>
    </div>
    <div class="col-auto">
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshDashboard()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <a href="{{ route('settings.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-cog me-1"></i>Settings
            </a>
        </div>
    </div>
</div>

<!-- Mobile App-Style Header -->
<div class="app-header d-md-none" style="display: block !important; visibility: visible !important;">
    <div class="app-header-content">
        <div class="app-header-left">
            <div class="app-header-greeting">Welcome back</div>
            <h1 class="app-header-title">{{ auth()->user()->username }}</h1>
        </div>
        <div class="app-header-actions">
            <button type="button" class="app-header-btn" onclick="refreshDashboard()" title="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
            <a href="{{ route('settings.index') }}" class="app-header-btn" title="Settings">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards - Desktop -->
<div class="row g-4 mb-4 d-none d-md-flex">
    <div class="col-md-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Booths</h6>
                        <h2 class="mb-0 text-primary">{{ $stats['total_booths'] }}</h2>
                    </div>
                    <div class="text-primary" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-store"></i>
                    </div>
                </div>
                @if($stats['total_booths'] > 0)
                <small class="text-muted">
                    {{ number_format(($stats['available_booths'] / $stats['total_booths']) * 100, 1) }}% Available
                </small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Available</h6>
                        <h2 class="mb-0 text-success">{{ $stats['available_booths'] }}</h2>
                    </div>
                    <div class="text-success" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Reserved</h6>
                        <h2 class="mb-0 text-warning">{{ $stats['reserved_booths'] }}</h2>
                    </div>
                    <div class="text-warning" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Confirmed</h6>
                        <h2 class="mb-0 text-info">{{ $stats['confirmed_booths'] }}</h2>
                    </div>
                    <div class="text-info" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4 d-none d-md-flex">
    <div class="col-md-3">
        <div class="card stat-card border-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Paid</h6>
                        <h2 class="mb-0 text-dark">{{ $stats['paid_booths'] }}</h2>
                    </div>
                    <div class="text-dark" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Clients</h6>
                        <h2 class="mb-0 text-secondary">{{ $stats['total_clients'] }}</h2>
                    </div>
                    <div class="text-secondary" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Users</h6>
                        <h2 class="mb-0 text-danger">{{ $stats['total_users'] }}</h2>
                    </div>
                    <div class="text-danger" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Bookings</h6>
                        <h2 class="mb-0 text-primary">{{ $stats['total_bookings'] }}</h2>
                    </div>
                    <div class="text-primary" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile App-Style Statistics -->
<div class="app-stats-section d-md-none" style="display: block !important;">
    <div class="app-stats-grid">
        <div class="app-stat-card primary">
            <span class="app-stat-label">Total Booths</span>
            <h2 class="app-stat-value">{{ $stats['total_booths'] }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-store"></i>
            </div>
        </div>
        
        <div class="app-stat-card success">
            <span class="app-stat-label">Available</span>
            <h2 class="app-stat-value">{{ $stats['available_booths'] }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        
        <div class="app-stat-card warning">
            <span class="app-stat-label">Reserved</span>
            <h2 class="app-stat-value">{{ $stats['reserved_booths'] }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        
        <div class="app-stat-card info">
            <span class="app-stat-label">Confirmed</span>
            <h2 class="app-stat-value">{{ $stats['confirmed_booths'] }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
        
        <div class="app-stat-card dark">
            <span class="app-stat-label">Paid</span>
            <h2 class="app-stat-value">{{ $stats['paid_booths'] }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
        
        <div class="app-stat-card secondary">
            <span class="app-stat-label">Clients</span>
            <h2 class="app-stat-value">{{ $stats['total_clients'] }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        
        <div class="app-stat-card danger">
            <span class="app-stat-label">Users</span>
            <h2 class="app-stat-value">{{ $stats['total_users'] }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
        
        <div class="app-stat-card primary">
            <span class="app-stat-label">Bookings</span>
            <h2 class="app-stat-value">{{ $stats['total_bookings'] }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section - Mobile App Style -->
<div class="app-section d-md-none" style="display: block !important;">
    <div class="app-section-header">
        <h3 class="app-section-title">Analytics</h3>
        <a href="{{ route('reports.index') }}" class="app-section-link">
            View All
            <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
        </a>
    </div>
    
    <div class="app-chart-card">
        <div class="app-chart-header">
            <div class="app-chart-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h4 class="app-chart-title">Booth Status</h4>
        </div>
        <canvas id="boothStatusChart" height="250"></canvas>
    </div>
    
    <div class="app-chart-card">
        <div class="app-chart-header">
            <div class="app-chart-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h4 class="app-chart-title">Booking Trends</h4>
        </div>
        <canvas id="bookingTrendChart" height="250"></canvas>
    </div>
</div>

<!-- Recent Bookings - Mobile App Style -->
<div class="app-section d-md-none" style="display: block !important;">
    <div class="app-section-header">
        <h3 class="app-section-title">Recent Activity</h3>
        <a href="{{ route('books.index') }}" class="app-section-link">
            See All
            <i class="fas fa-chevron-right" style="font-size: 12px;"></i>
        </a>
    </div>
    
    <div class="app-recent-card">
        @if($recentBookings && $recentBookings->count() > 0)
        <ul class="app-recent-list">
            @foreach($recentBookings->take(5) as $booking)
            <li class="app-recent-item">
                <div class="app-recent-item-left">
                    <div class="app-recent-item-id">#{{ $booking->id }}</div>
                    <div class="app-recent-item-client">{{ $booking->client->company ?? 'N/A' }}</div>
                </div>
                <div class="app-recent-item-right">
                    <div class="app-recent-item-booths">{{ count(json_decode($booking->boothid, true) ?? []) }} booths</div>
                    <div class="app-recent-item-date">{{ $booking->date_book->format('M d, Y') }}</div>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="app-empty-state">
            <div class="app-empty-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <p class="app-empty-text">No bookings yet</p>
        </div>
        @endif
    </div>
</div>

<!-- Desktop Charts -->
<div class="row g-4 mb-4 d-none d-md-flex">
    <div class="col-md-6">
        <div class="card chart-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Booth Status Distribution</h5>
            </div>
            <div class="card-body">
                <canvas id="boothStatusChartDesktop" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card chart-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Booking Trends (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="bookingTrendChartDesktop" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Bookings (Desktop) -->
<div class="row d-none d-md-flex">
    <div class="col">
        <div class="card recent-bookings-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Recent Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Booths</th>
                                <th>Date</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td>{{ $booking->id }}</td>
                                <td>{{ $booking->client->company ?? 'N/A' }}</td>
                                <td>{{ count(json_decode($booking->boothid, true) ?? []) }} booths</td>
                                <td>{{ $booking->date_book->format('Y-m-d H:i') }}</td>
                                <td>{{ $booking->user->username }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No bookings yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isAdmin && !empty($userStats))
<div class="row mt-4">
    <div class="col">
        <div class="card user-performance-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>User Performance</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
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
                            @foreach($userStats as $stat)
                            <tr>
                                <td data-label="User"><strong>{{ $stat['username'] }}</strong></td>
                                <td data-label="Type"><span class="badge bg-{{ $stat['type'] == 'Admin' ? 'danger' : 'secondary' }}">{{ $stat['type'] }}</span></td>
                                <td data-label="Reserved">{{ $stat['reserve'] }}</td>
                                <td data-label="Confirmed">{{ $stat['booking'] }}</td>
                                <td data-label="Paid">{{ $stat['paid'] }}</td>
                                <td data-label="Total"><strong>{{ $stat['reserve'] + $stat['booking'] + $stat['paid'] }}</strong></td>
                                <td data-label="Performance">
                                    @php
                                        $total = $stat['reserve'] + $stat['booking'] + $stat['paid'];
                                        $percentage = $stats['total_booths'] > 0 ? ($total / $stats['total_booths']) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Mobile Bottom Navigation - Native App Style -->
<nav class="app-bottom-nav d-md-none" style="display: flex !important;">
    <a href="{{ route('dashboard') }}" class="app-bottom-nav-item active">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('booths.index') }}" class="app-bottom-nav-item">
        <i class="fas fa-store"></i>
        <span>Booths</span>
    </a>
    <a href="{{ route('books.index') }}" class="app-bottom-nav-item">
        <i class="fas fa-calendar-check"></i>
        <span>Bookings</span>
    </a>
    <a href="{{ route('clients.index') }}" class="app-bottom-nav-item">
        <i class="fas fa-users"></i>
        <span>Clients</span>
    </a>
    <a href="{{ route('settings.index') }}" class="app-bottom-nav-item">
        <i class="fas fa-cog"></i>
        <span>More</span>
    </a>
</nav>

<!-- Floating Action Button -->
<div class="app-quick-actions d-md-none" style="display: flex !important;">
    <a href="{{ route('books.create') }}" class="app-fab" title="New Booking">
        <i class="fas fa-plus"></i>
    </a>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
// Chart data
const chartData = {
    boothStatus: {
        labels: ['Available', 'Reserved', 'Confirmed', 'Paid', 'Hidden'],
        data: [
            {{ $stats['available_booths'] }},
            {{ $stats['reserved_booths'] }},
            {{ $stats['confirmed_booths'] }},
            {{ $stats['paid_booths'] }},
            {{ $stats['total_booths'] - $stats['available_booths'] - $stats['reserved_booths'] - $stats['confirmed_booths'] - $stats['paid_booths'] }}
        ],
        colors: ['#10b981', '#f59e0b', '#3b82f6', '#667eea', '#6b7280']
    }
};

// Mobile Booth Status Chart
const boothStatusMobile = document.getElementById('boothStatusChart');
if (boothStatusMobile) {
    new Chart(boothStatusMobile.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: chartData.boothStatus.labels,
            datasets: [{
                data: chartData.boothStatus.data,
                backgroundColor: chartData.boothStatus.colors,
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: { size: 12, weight: '600' },
                        usePointStyle: true,
                        pointStyle: 'circle',
                        color: '#374151',
                        boxWidth: 8
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#111827',
                    bodyColor: '#6b7280',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 }
                }
            },
            cutout: '70%'
        }
    });
}

// Desktop Booth Status Chart
const boothStatusDesktop = document.getElementById('boothStatusChartDesktop');
if (boothStatusDesktop) {
    new Chart(boothStatusDesktop.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: chartData.boothStatus.labels,
            datasets: [{
                data: chartData.boothStatus.data,
                backgroundColor: chartData.boothStatus.colors
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

// Booking Trends Data
const last7Days = @json($bookingTrendDates ?? []);
const bookingCounts = @json($bookingTrendCounts ?? []);

// Mobile Booking Trends Chart
const bookingTrendMobile = document.getElementById('bookingTrendChart');
if (bookingTrendMobile) {
    new Chart(bookingTrendMobile.getContext('2d'), {
        type: 'line',
        data: {
            labels: last7Days,
            datasets: [{
                label: 'Bookings',
                data: bookingCounts,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointRadius: 6,
                pointHoverRadius: 9,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1, 
                        font: { size: 12, weight: '600' },
                        color: '#6b7280'
                    },
                    grid: { 
                        color: 'rgba(102, 126, 234, 0.1)',
                        lineWidth: 1
                    }
                },
                x: {
                    ticks: { 
                        font: { size: 12, weight: '600' },
                        color: '#6b7280'
                    },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#111827',
                    bodyColor: '#6b7280',
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 12
                }
            }
        }
    });
}

// Desktop Booking Trends Chart  
const bookingTrendDesktop = document.getElementById('bookingTrendChartDesktop');
if (bookingTrendDesktop) {
    new Chart(bookingTrendDesktop.getContext('2d'), {
        type: 'line',
        data: {
            labels: last7Days,
            datasets: [{
                label: 'Bookings',
                data: bookingCounts,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

function refreshDashboard() {
    window.location.reload();
}

// Force Mobile Styles Check - AGGRESSIVE
(function() {
    function checkAndApplyMobileStyles() {
        const width = window.innerWidth;
        const isMobile = width <= 768;
        
        console.log('📱 Mobile Check - Width:', width, 'Is Mobile:', isMobile);
        
        if (isMobile) {
            // Force body background
            document.body.style.setProperty('background', '#f5f7fa', 'important');
            document.body.style.setProperty('padding-bottom', '80px', 'important');
            document.documentElement.style.setProperty('background', '#f5f7fa', 'important');
            
            // Hide navbar COMPLETELY
            const navbars = document.querySelectorAll('nav.navbar, .navbar, .navbar-expand-lg');
            navbars.forEach(nav => {
                nav.style.setProperty('display', 'none', 'important');
                nav.style.setProperty('visibility', 'hidden', 'important');
                nav.style.setProperty('height', '0', 'important');
                nav.style.setProperty('padding', '0', 'important');
                nav.style.setProperty('margin', '0', 'important');
            });
            
            // Remove main padding
            const main = document.querySelector('main.container-fluid, #main-content, main');
            if (main) {
                main.style.setProperty('padding', '0', 'important');
                main.style.setProperty('margin', '0', 'important');
            }
            
            // Remove container-fluid padding everywhere
            const containers = document.querySelectorAll('.container-fluid');
            containers.forEach(container => {
                container.style.setProperty('padding', '0', 'important');
                container.style.setProperty('margin', '0', 'important');
            });
            
            // Ensure mobile elements are visible
            const mobileElements = document.querySelectorAll('.app-header, .app-stats-section, .app-section, .app-bottom-nav, .app-quick-actions');
            mobileElements.forEach(el => {
                if (el) {
                    const tag = el.tagName.toLowerCase();
                    if (tag === 'nav') {
                        el.style.setProperty('display', 'flex', 'important');
                    } else if (tag === 'div' && el.classList.contains('app-quick-actions')) {
                        el.style.setProperty('display', 'flex', 'important');
                    } else {
                        el.style.setProperty('display', 'block', 'important');
                    }
                }
            });
            
            // Hide desktop elements
            const desktopElements = document.querySelectorAll('.d-none.d-md-flex, .row.d-none.d-md-flex');
            desktopElements.forEach(el => {
                el.style.setProperty('display', 'none', 'important');
            });
            
            // Add visual test indicator (remove after confirming it works)
            let testIndicator = document.getElementById('mobile-test-indicator');
            if (!testIndicator) {
                testIndicator = document.createElement('div');
                testIndicator.id = 'mobile-test-indicator';
                testIndicator.style.cssText = 'position:fixed;top:10px;right:10px;background:#10b981;color:white;padding:8px 12px;border-radius:8px;font-size:12px;font-weight:bold;z-index:9999;box-shadow:0 2px 8px rgba(0,0,0,0.2);';
                testIndicator.textContent = '✅ MOBILE MODE ACTIVE';
                document.body.appendChild(testIndicator);
                setTimeout(() => testIndicator.remove(), 5000);
            }
            
            console.log('✅ Mobile styles FORCED - Width:', width);
        } else {
            console.log('🖥️ Desktop mode - Width:', width);
        }
    }
    
    // Run immediately
    checkAndApplyMobileStyles();
    
    // Run on load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            checkAndApplyMobileStyles();
            setTimeout(checkAndApplyMobileStyles, 50);
        });
    } else {
        checkAndApplyMobileStyles();
        setTimeout(checkAndApplyMobileStyles, 50);
    }
    
    // Run on resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(checkAndApplyMobileStyles, 100);
    });
    
    // Run multiple times to override any late-loading styles
    setTimeout(checkAndApplyMobileStyles, 100);
    setTimeout(checkAndApplyMobileStyles, 300);
    setTimeout(checkAndApplyMobileStyles, 500);
    setTimeout(checkAndApplyMobileStyles, 1000);
    
    // Check CSS files loaded
    const checkCSSLoaded = function() {
        const stylesheets = Array.from(document.styleSheets);
        let mobileCSSFound = false;
        stylesheets.forEach(sheet => {
            try {
                if (sheet.href && (sheet.href.includes('mobile-design-system') || sheet.href.includes('global-mobile'))) {
                    mobileCSSFound = true;
                    console.log('✅ Mobile CSS loaded:', sheet.href);
                }
            } catch(e) {
                // Cross-origin stylesheet, skip
            }
        });
        if (!mobileCSSFound) {
            console.warn('⚠️ Mobile CSS files not found in stylesheets');
        }
    };
    
    setTimeout(checkCSSLoaded, 500);
})();
</script>
@endpush
