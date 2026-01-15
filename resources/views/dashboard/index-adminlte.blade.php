@extends('layouts.adminlte')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@php
    $cdnSettings = \App\Models\Setting::getCDNSettings();
    $useCDN = $cdnSettings['use_cdn'] ?? false;
@endphp

@push('styles')
<style>
/* ============================================
   DESKTOP STYLES - PRESERVED
   ============================================ */
@media (min-width: 769px) {
    .kpi-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        cursor: pointer;
    }
    
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }
    
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .kpi-card:hover::before {
        opacity: 1;
    }
}

/* ============================================
   MOBILE APP UX/UI - COMPLETE REDESIGN
   ============================================ */
@media (max-width: 768px) {
    /* Reset everything for mobile */
    * {
        box-sizing: border-box;
    }
    
    /* Hide AdminLTE sidebar and header on mobile */
    .main-sidebar,
    .main-header,
    .content-header,
    .breadcrumb {
        display: none !important;
    }
    
    /* Full mobile app container */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
        padding: 0 !important;
        margin: 0 !important;
        padding-bottom: 80px !important; /* Space for bottom nav */
        overflow-x: hidden !important;
    }
    
    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        background: transparent !important;
    }
    
    .content {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
    }
    
    /* ============================================
       MOBILE APP HEADER
       ============================================ */
    .mobile-app-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        margin: 0;
        width: 100%;
    }
    
    .mobile-app-header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .mobile-app-header-left {
        flex: 1;
    }
    
    .mobile-app-greeting {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .mobile-app-title {
        font-size: 26px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
        letter-spacing: -0.02em;
        line-height: 1.2;
    }
    
    .mobile-app-header-actions {
        display: flex;
        gap: 10px;
    }
    
    .mobile-app-header-btn {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all 0.2s;
        text-decoration: none;
        cursor: pointer;
    }
    
    .mobile-app-header-btn:active {
        background: rgba(255, 255, 255, 0.4);
        transform: scale(0.95);
    }
    
    /* ============================================
       MOBILE STATS GRID
       ============================================ */
    .mobile-stats-container {
        padding: 20px;
    }
    
    .mobile-stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }
    
    .mobile-stat-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .mobile-stat-card:active {
        transform: scale(0.97);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    }
    
    .mobile-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--stat-gradient, linear-gradient(90deg, #667eea, #764ba2));
    }
    
    .mobile-stat-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #9ca3af;
        margin-bottom: 10px;
        display: block;
    }
    
    .mobile-stat-value {
        font-size: 32px;
        font-weight: 800;
        line-height: 1;
        margin: 8px 0;
        letter-spacing: -0.03em;
        color: var(--stat-color, #111827);
    }
    
    .mobile-stat-icon {
        position: absolute;
        bottom: 16px;
        right: 16px;
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: var(--stat-bg, rgba(102, 126, 234, 0.15));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: var(--stat-color, #667eea);
    }
    
    .mobile-stat-card.primary {
        --stat-gradient: linear-gradient(90deg, #667eea, #764ba2);
        --stat-color: #667eea;
        --stat-bg: rgba(102, 126, 234, 0.15);
    }
    
    .mobile-stat-card.success {
        --stat-gradient: linear-gradient(90deg, #10b981, #059669);
        --stat-color: #10b981;
        --stat-bg: rgba(16, 185, 129, 0.15);
    }
    
    .mobile-stat-card.warning {
        --stat-gradient: linear-gradient(90deg, #f59e0b, #d97706);
        --stat-color: #f59e0b;
        --stat-bg: rgba(245, 158, 11, 0.15);
    }
    
    .mobile-stat-card.info {
        --stat-gradient: linear-gradient(90deg, #3b82f6, #2563eb);
        --stat-color: #3b82f6;
        --stat-bg: rgba(59, 130, 246, 0.15);
    }
    
    .mobile-stat-card.danger {
        --stat-gradient: linear-gradient(90deg, #ef4444, #dc2626);
        --stat-color: #ef4444;
        --stat-bg: rgba(239, 68, 68, 0.15);
    }
    
    .mobile-stat-card.dark {
        --stat-gradient: linear-gradient(90deg, #1f2937, #111827);
        --stat-color: #1f2937;
        --stat-bg: rgba(31, 41, 55, 0.15);
    }
    
    /* ============================================
       MOBILE SECTION HEADERS
       ============================================ */
    .mobile-section {
        padding: 0 20px;
        margin-bottom: 24px;
    }
    
    .mobile-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .mobile-section-title {
        font-size: 20px;
        font-weight: 800;
        color: #111827;
        margin: 0;
        letter-spacing: -0.01em;
    }
    
    .mobile-section-link {
        font-size: 13px;
        font-weight: 600;
        color: #667eea;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    /* ============================================
       MOBILE CHART CARDS
       ============================================ */
    .mobile-chart-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 16px;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .mobile-chart-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .mobile-chart-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        font-size: 20px;
    }
    
    .mobile-chart-title {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }
    
    /* ============================================
       MOBILE RECENT ACTIVITY
       ============================================ */
    .mobile-activity-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .mobile-activity-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .mobile-activity-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 0;
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s;
    }
    
    .mobile-activity-item:last-child {
        border-bottom: none;
    }
    
    .mobile-activity-item:active {
        background: #f9fafb;
        margin: 0 -20px;
        padding-left: 20px;
        padding-right: 20px;
        border-radius: 12px;
    }
    
    .mobile-activity-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(102, 126, 234, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #667eea;
        font-size: 18px;
        flex-shrink: 0;
    }
    
    .mobile-activity-content {
        flex: 1;
        min-width: 0;
    }
    
    .mobile-activity-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }
    
    .mobile-activity-subtitle {
        font-size: 12px;
        color: #6b7280;
    }
    
    /* ============================================
       MOBILE BOTTOM NAVIGATION
       ============================================ */
    .mobile-bottom-nav {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px) !important;
        border-top: 1px solid rgba(229, 231, 235, 0.5) !important;
        padding: 10px 0 calc(10px + env(safe-area-inset-bottom)) !important;
        z-index: 1050 !important;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1) !important;
        display: flex !important;
        justify-content: space-around !important;
        align-items: center !important;
        width: 100% !important;
    }
    
    .mobile-bottom-nav-item {
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
    
    .mobile-bottom-nav-item i {
        font-size: 22px;
        margin-bottom: 4px;
        transition: all 0.2s;
    }
    
    .mobile-bottom-nav-item span {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .mobile-bottom-nav-item.active {
        color: #667eea;
    }
    
    .mobile-bottom-nav-item.active::before {
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
    
    .mobile-bottom-nav-item:active {
        transform: scale(0.95);
    }
    
    /* ============================================
       FLOATING ACTION BUTTON
       ============================================ */
    .mobile-fab {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 18px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        box-shadow: 0 6px 24px rgba(102, 126, 234, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        z-index: 1040;
        transition: all 0.3s;
        cursor: pointer;
        text-decoration: none;
    }
    
    .mobile-fab:active {
        transform: scale(0.9);
        box-shadow: 0 3px 12px rgba(102, 126, 234, 0.4);
    }
    
    /* ============================================
       EMPTY STATES
       ============================================ */
    .mobile-empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
    }
    
    .mobile-empty-icon {
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
    
    .mobile-empty-text {
        font-size: 15px;
        font-weight: 500;
        margin: 0;
    }
    
    /* Hide all desktop elements */
    .row.d-none.d-md-flex,
    .d-none.d-md-flex,
    .d-md-none.d-none {
        display: none !important;
    }
    
    /* Force mobile elements visible */
    .mobile-only {
        display: block !important;
    }
}
</style>
@endpush

@section('content')
<!-- Desktop View - Preserved -->
<div class="container-fluid d-none d-md-block">
    <!-- Welcome Header -->
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
    </div>

    <!-- KPI Cards Grid -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="kpi-card primary">
                <div style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-value">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card success">
                <div style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="kpi-label">Total Bookings</div>
                    <div class="kpi-value">{{ number_format($stats['total_bookings'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card info">
                <div style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-store"></i></div>
                    <div class="kpi-label">Total Booths</div>
                    <div class="kpi-value">{{ number_format($stats['total_booths'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card warning">
                <div style="padding: 24px;">
                    <div class="kpi-icon"><i class="fas fa-users"></i></div>
                    <div class="kpi-label">Total Clients</div>
                    <div class="kpi-value">{{ number_format($stats['total_clients'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title"><i class="fas fa-chart-line mr-2 text-primary"></i>Booking Trends</h3>
                </div>
                <canvas id="trendChartDesktop" height="80"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title"><i class="fas fa-chart-pie mr-2 text-success"></i>Booth Status</h3>
                </div>
                <canvas id="boothStatusChartDesktop" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
   MOBILE APP VIEW - COMPLETE REDESIGN
   ============================================ -->

<!-- Mobile App Header -->
<div class="mobile-app-header d-md-none" style="display: block !important;">
    <div class="mobile-app-header-content">
        <div class="mobile-app-header-left">
            <div class="mobile-app-greeting">Welcome back</div>
            <h1 class="mobile-app-title">{{ auth()->user()->username }}</h1>
        </div>
        <div class="mobile-app-header-actions">
            <button type="button" class="mobile-app-header-btn" onclick="refreshDashboard()" title="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
            <a href="{{ route('settings.index') }}" class="mobile-app-header-btn" title="Settings">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </div>
</div>

<!-- Mobile Stats Grid -->
<div class="mobile-stats-container d-md-none" style="display: block !important;">
    <div class="mobile-stats-grid">
        <div class="mobile-stat-card primary">
            <span class="mobile-stat-label">Total Booths</span>
            <h2 class="mobile-stat-value">{{ $stats['total_booths'] ?? 0 }}</h2>
            <div class="mobile-stat-icon">
                <i class="fas fa-store"></i>
            </div>
        </div>
        
        <div class="mobile-stat-card success">
            <span class="mobile-stat-label">Available</span>
            <h2 class="mobile-stat-value">{{ $stats['available_booths'] ?? 0 }}</h2>
            <div class="mobile-stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        
        <div class="mobile-stat-card warning">
            <span class="mobile-stat-label">Reserved</span>
            <h2 class="mobile-stat-value">{{ $stats['reserved_booths'] ?? 0 }}</h2>
            <div class="mobile-stat-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        
        <div class="mobile-stat-card info">
            <span class="mobile-stat-label">Confirmed</span>
            <h2 class="mobile-stat-value">{{ $stats['confirmed_booths'] ?? 0 }}</h2>
            <div class="mobile-stat-icon">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
        
        <div class="mobile-stat-card dark">
            <span class="mobile-stat-label">Paid</span>
            <h2 class="mobile-stat-value">{{ $stats['paid_booths'] ?? 0 }}</h2>
            <div class="mobile-stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
        
        <div class="mobile-stat-card primary">
            <span class="mobile-stat-label">Bookings</span>
            <h2 class="mobile-stat-value">{{ $stats['total_bookings'] ?? 0 }}</h2>
            <div class="mobile-stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        
        <div class="mobile-stat-card success">
            <span class="mobile-stat-label">Clients</span>
            <h2 class="mobile-stat-value">{{ $stats['total_clients'] ?? 0 }}</h2>
            <div class="mobile-stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        
        <div class="mobile-stat-card info">
            <span class="mobile-stat-label">Revenue</span>
            <h2 class="mobile-stat-value">${{ number_format($stats['total_revenue'] ?? 0, 0) }}</h2>
            <div class="mobile-stat-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Charts Section -->
<div class="mobile-section d-md-none" style="display: block !important;">
    <div class="mobile-section-header">
        <h3 class="mobile-section-title">Analytics</h3>
        <a href="{{ route('reports.index') }}" class="mobile-section-link">
            View All
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
        </a>
    </div>
    
    <div class="mobile-chart-card">
        <div class="mobile-chart-header">
            <div class="mobile-chart-icon">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h4 class="mobile-chart-title">Booth Status</h4>
        </div>
        <canvas id="boothStatusChartMobile" height="250"></canvas>
    </div>
    
    <div class="mobile-chart-card">
        <div class="mobile-chart-header">
            <div class="mobile-chart-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h4 class="mobile-chart-title">Booking Trends</h4>
        </div>
        <canvas id="trendChartMobile" height="250"></canvas>
    </div>
</div>

<!-- Mobile Recent Activity -->
<div class="mobile-section d-md-none" style="display: block !important;">
    <div class="mobile-section-header">
        <h3 class="mobile-section-title">Recent Activity</h3>
        <a href="{{ route('books.index') }}" class="mobile-section-link">
            See All
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
        </a>
    </div>
    
    <div class="mobile-activity-card">
        @if(!empty($recentActivities) && $recentActivities->count() > 0)
        <ul class="mobile-activity-list">
            @foreach($recentActivities->take(5) as $activity)
            <li class="mobile-activity-item">
                <div class="mobile-activity-icon">
                    @if(str_contains(strtolower($activity->action ?? ''), 'create'))
                        <i class="fas fa-plus"></i>
                    @elseif(str_contains(strtolower($activity->action ?? ''), 'update'))
                        <i class="fas fa-edit"></i>
                    @elseif(str_contains(strtolower($activity->action ?? ''), 'delete'))
                        <i class="fas fa-trash"></i>
                    @else
                        <i class="fas fa-circle"></i>
                    @endif
                </div>
                <div class="mobile-activity-content">
                    <div class="mobile-activity-title">{{ $activity->description ?? $activity->action ?? 'Activity' }}</div>
                    <div class="mobile-activity-subtitle">
                        @if($activity->user){{ $activity->user->username }} • @endif{{ $activity->created_at->diffForHumans() }}
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <div class="mobile-empty-state">
            <div class="mobile-empty-icon">
                <i class="fas fa-inbox"></i>
            </div>
            <p class="mobile-empty-text">No recent activity</p>
        </div>
        @endif
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav d-md-none" style="display: flex !important;">
    <a href="{{ route('dashboard') }}" class="mobile-bottom-nav-item active">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('booths.index') }}" class="mobile-bottom-nav-item">
        <i class="fas fa-store"></i>
        <span>Booths</span>
    </a>
    <a href="{{ route('books.index') }}" class="mobile-bottom-nav-item">
        <i class="fas fa-calendar-check"></i>
        <span>Bookings</span>
    </a>
    <a href="{{ route('clients.index') }}" class="mobile-bottom-nav-item">
        <i class="fas fa-users"></i>
        <span>Clients</span>
    </a>
    <a href="{{ route('settings.index') }}" class="mobile-bottom-nav-item">
        <i class="fas fa-cog"></i>
        <span>More</span>
    </a>
</nav>

<!-- Floating Action Button -->
<a href="{{ route('books.create') }}" class="mobile-fab d-md-none" style="display: flex !important;" title="New Booking">
    <i class="fas fa-plus"></i>
</a>

@endsection

@push('scripts')
<!-- Chart.js -->
@if($useCDN)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@else
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
@endif

<script>
// Wait for Chart.js to load
function initCharts() {
    if (typeof Chart === 'undefined') {
        setTimeout(initCharts, 100);
        return;
    }
    
    // Mobile Booth Status Chart
    const boothStatusMobile = document.getElementById('boothStatusChartMobile');
    if (boothStatusMobile && typeof Chart !== 'undefined') {
        try {
            new Chart(boothStatusMobile.getContext('2d'), {
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
                        backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#667eea'],
                        borderWidth: 0
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
                                usePointStyle: true
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        } catch(e) {
            console.error('Chart error:', e);
        }
    }
    
    // Mobile Booking Trends Chart
    const trendChartMobile = document.getElementById('trendChartMobile');
    if (trendChartMobile && typeof Chart !== 'undefined') {
        try {
            const bookingDates = @json($bookingTrendDates ?? []);
            const bookingCounts = @json($bookingTrendCounts ?? []);
            
            new Chart(trendChartMobile.getContext('2d'), {
                type: 'line',
                data: {
                    labels: bookingDates,
                    datasets: [{
                        label: 'Bookings',
                        data: bookingCounts,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 6
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
        } catch(e) {
            console.error('Chart error:', e);
        }
    }
    
    // Desktop Charts
    const trendChartDesktop = document.getElementById('trendChartDesktop');
    if (trendChartDesktop && typeof Chart !== 'undefined') {
        try {
            const bookingDates = @json($bookingTrendDates ?? []);
            const bookingCounts = @json($bookingTrendCounts ?? []);
            const revenueData = @json($revenueTrendData ?? []);
            
            new Chart(trendChartDesktop.getContext('2d'), {
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
                            fill: true
                        },
                        {
                            label: 'Revenue ($)',
                            data: revenueData,
                            borderColor: 'rgb(72, 187, 120)',
                            backgroundColor: 'rgba(72, 187, 120, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        } catch(e) {
            console.error('Chart error:', e);
        }
    }
    
    const boothStatusDesktop = document.getElementById('boothStatusChartDesktop');
    if (boothStatusDesktop && typeof Chart !== 'undefined') {
        try {
            new Chart(boothStatusDesktop.getContext('2d'), {
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
                        backgroundColor: ['rgba(25, 135, 84, 0.8)', 'rgba(255, 193, 7, 0.8)', 'rgba(13, 202, 240, 0.8)', 'rgba(33, 37, 41, 0.8)']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        } catch(e) {
            console.error('Chart error:', e);
        }
    }
}

// Initialize charts when ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initCharts, 500);
    });
} else {
    setTimeout(initCharts, 500);
}

function refreshDashboard() {
    window.location.reload();
}

// Force Mobile Styles
(function() {
    function applyMobileStyles() {
        if (window.innerWidth <= 768) {
            // Hide sidebar and header
            const sidebar = document.querySelector('.main-sidebar');
            const header = document.querySelector('.main-header');
            if (sidebar) sidebar.style.display = 'none';
            if (header) header.style.display = 'none';
            
            // Force mobile elements visible
            document.querySelectorAll('.mobile-app-header, .mobile-stats-container, .mobile-section, .mobile-bottom-nav, .mobile-fab').forEach(el => {
                if (el) el.style.display = 'block';
            });
            document.querySelectorAll('.mobile-bottom-nav').forEach(el => {
                if (el) el.style.display = 'flex';
            });
            
            // Remove content wrapper margin
            const contentWrapper = document.querySelector('.content-wrapper');
            if (contentWrapper) {
                contentWrapper.style.marginLeft = '0';
                contentWrapper.style.padding = '0';
            }
        }
    }
    
    applyMobileStyles();
    window.addEventListener('resize', applyMobileStyles);
    setTimeout(applyMobileStyles, 100);
    setTimeout(applyMobileStyles, 500);
})();
</script>
@endpush
