@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
<style>
/* ============================================
   DESKTOP STYLES
   ============================================ */

/* Desktop Styles */
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

/* ============================================
   🚀 MOBILE APP - COMPLETELY UNIQUE DESIGN
   Full-screen native app experience
   ============================================ */
@media (max-width: 768px) {
    /* HIDE ALL DESKTOP ELEMENTS */
    .navbar,
    .navbar-collapse,
    nav.navbar {
        display: none !important;
    }
    /* FULL-SCREEN APP LAYOUT */
    html {
        height: 100%;
        overflow-x: hidden;
    }
    
    body {
        background: #0f0f1e !important;
        margin: 0 !important;
        padding: 0 !important;
        height: 100% !important;
        overflow-x: hidden !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    
    .content-wrapper,
    .container,
    .container-fluid {
        background: transparent !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    
    /* 📱 MOBILE APP HEADER - Dark Theme */
    .dashboard-header {
        background: linear-gradient(180deg, #1a1a2e 0%, #16162a 100%);
        padding: 50px 20px 24px;
        margin: 0 !important;
        position: relative;
        overflow: hidden;
    }
    
    /* Animated background circles */
    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15), transparent);
        border-radius: 50%;
        animation: pulse 4s ease-in-out infinite;
    }
    
    .dashboard-header::after {
        content: '';
        position: absolute;
        bottom: -80px;
        left: -80px;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.1), transparent);
        border-radius: 50%;
        animation: pulse 6s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.6; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    .dashboard-header h2 {
        font-size: 32px !important;
        font-weight: 900 !important;
        color: #ffffff !important;
        margin-bottom: 6px !important;
        letter-spacing: -1px !important;
        position: relative;
        z-index: 2;
        background: linear-gradient(135deg, #fff 0%, #e0e7ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .dashboard-header p {
        font-size: 14px !important;
        color: #9ca3af !important;
        font-weight: 500 !important;
        position: relative;
        z-index: 2;
        margin-bottom: 20px !important;
    }
    
    /* Icon buttons in header */
    .dashboard-actions {
        display: flex;
        gap: 12px;
        position: relative;
        z-index: 2;
    }
    
    .dashboard-actions .btn {
        flex: 1;
        min-height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 700;
        border-radius: 16px;
        background: rgba(99, 102, 241, 0.15);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1.5px solid rgba(99, 102, 241, 0.3);
        color: #818cf8 !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .dashboard-actions .btn:active {
        transform: scale(0.93);
        background: rgba(99, 102, 241, 0.25);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }
    
    /* 🎯 MODERN KPI CARDS - Neumorphism Dark */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        padding: 20px;
        margin: 0;
        background: #0f0f1e;
    }
    
    .stat-card {
        border: none !important;
        border-radius: 24px !important;
        position: relative;
        overflow: visible !important;
        background: linear-gradient(145deg, #1e1e32, #16162a) !important;
        box-shadow: 8px 8px 16px #0a0a14,
                   -8px -8px 16px #24243c,
                   inset 0 1px 0 rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(99, 102, 241, 0.1) !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 140px;
    }
    
    /* Glowing border effect */
    .stat-card::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 24px;
        padding: 2px;
        background: linear-gradient(135deg, transparent, transparent);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: 0;
        transition: opacity 0.4s;
    }
    
    .stat-card.border-primary::before { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
    .stat-card.border-success::before { background: linear-gradient(135deg, #10b981, #06b6d4); }
    .stat-card.border-warning::before { background: linear-gradient(135deg, #f59e0b, #ef4444); }
    .stat-card.border-info::before { background: linear-gradient(135deg, #3b82f6, #6366f1); }
    .stat-card.border-dark::before { background: linear-gradient(135deg, #6b7280, #374151); }
    .stat-card.border-secondary::before { background: linear-gradient(135deg, #8b5cf6, #ec4899); }
    .stat-card.border-danger::before { background: linear-gradient(135deg, #ef4444, #dc2626); }
    
    /* Floating icon with glow */
    .stat-card::after {
        content: '';
        position: absolute;
        top: 16px;
        right: 16px;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.15);
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        transition: all 0.3s;
    }
    
    .stat-card:active {
        transform: scale(0.97) translateY(2px);
        box-shadow: 4px 4px 8px #0a0a14,
                   -4px -4px 8px #24243c;
    }
    
    .stat-card:active::before {
        opacity: 1;
    }
    
    .stat-card:active::after {
        box-shadow: 0 0 30px rgba(99, 102, 241, 0.6);
    }
    
    .stat-card .card-body {
        padding: 20px !important;
        position: relative;
        z-index: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
    }
    
    .stat-card h6 {
        font-size: 10px !important;
        font-weight: 800 !important;
        text-transform: uppercase;
        letter-spacing: 1.2px !important;
        margin-bottom: 12px !important;
        color: #6b7280 !important;
        line-height: 1;
    }
    
    .stat-card h2 {
        font-size: 36px !important;
        font-weight: 900 !important;
        margin-bottom: 6px !important;
        color: #ffffff !important;
        line-height: 1 !important;
        letter-spacing: -1.5px !important;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
    }
    
    /* Gradient numbers for each card type */
    .stat-card.border-primary h2 {
        background: linear-gradient(135deg, #818cf8, #c4b5fd);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .stat-card.border-success h2 {
        background: linear-gradient(135deg, #34d399, #6ee7b7);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .stat-card.border-warning h2 {
        background: linear-gradient(135deg, #fbbf24, #fcd34d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .stat-card.border-info h2 {
        background: linear-gradient(135deg, #60a5fa, #93c5fd);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .stat-card.border-dark h2,
    .stat-card.border-secondary h2,
    .stat-card.border-danger h2 {
        background: linear-gradient(135deg, #f87171, #fca5a5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .stat-card small {
        font-size: 11px !important;
        color: #9ca3af !important;
        font-weight: 600 !important;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .stat-card small i {
        font-size: 10px;
        color: #6b7280;
    }
    
    .stat-card .d-flex > div:last-child {
        display: none !important;
    }
    
    /* 📊 CHARTS SECTION - Dark Cards */
    .dashboard-charts {
        background: #0f0f1e;
        padding: 0 20px 20px;
        margin: 0;
        min-height: auto;
    }
    
    /* Section Headers with glow */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 24px 0 16px;
        padding: 0;
    }
    
    .section-title {
        font-size: 22px;
        font-weight: 900;
        color: #ffffff;
        letter-spacing: -0.5px;
        text-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
    }
    
    .section-link {
        font-size: 13px;
        color: #818cf8;
        font-weight: 700;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 8px 16px;
        border-radius: 12px;
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.2);
        transition: all 0.3s;
    }
    
    .section-link:active {
        background: rgba(99, 102, 241, 0.2);
        transform: scale(0.95);
    }
    
    /* Chart Cards - Dark Neumorphism */
    .chart-card {
        margin-bottom: 20px;
        border-radius: 24px;
        overflow: hidden;
        background: linear-gradient(145deg, #1e1e32, #16162a);
        border: 1px solid rgba(99, 102, 241, 0.1);
        box-shadow: 8px 8px 16px #0a0a14,
                   -8px -8px 16px #24243c;
    }
    
    .chart-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(99, 102, 241, 0.1);
        padding: 20px;
    }
    
    .chart-card .card-header h5 {
        font-size: 16px;
        font-weight: 800;
        margin: 0;
        color: #ffffff;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .chart-card .card-header h5 i {
        color: #818cf8;
        font-size: 18px;
        filter: drop-shadow(0 0 8px rgba(129, 140, 248, 0.6));
    }
    
    .chart-card .card-body {
        padding: 24px 20px;
        background: transparent;
    }
    
    .chart-card canvas {
        max-height: 240px !important;
        border-radius: 12px;
        filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.3));
    }
    
    /* 📝 RECENT BOOKINGS - Dark List */
    .recent-bookings-card {
        border-radius: 24px;
        margin-bottom: 80px;
        background: linear-gradient(145deg, #1e1e32, #16162a);
        border: 1px solid rgba(99, 102, 241, 0.1);
        box-shadow: 8px 8px 16px #0a0a14,
                   -8px -8px 16px #24243c;
        overflow: hidden;
    }
    
    .recent-bookings-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(99, 102, 241, 0.1);
        padding: 20px;
    }
    
    .recent-bookings-card .card-header h5 {
        font-size: 16px;
        font-weight: 800;
        margin: 0;
        color: #ffffff;
        letter-spacing: -0.3px;
    }
    
    .recent-bookings-card .card-body {
        padding: 0 !important;
    }
    
    .recent-bookings-card .table thead {
        display: none;
    }
    
    /* Dark theme list items */
    .recent-bookings-card .table tbody tr {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(99, 102, 241, 0.08);
        background: transparent;
        transition: all 0.3s;
        margin: 0;
        border-radius: 0;
    }
    
    .recent-bookings-card .table tbody tr:last-child {
        border-bottom: none;
    }
    
    .recent-bookings-card .table tbody tr:active {
        background: rgba(99, 102, 241, 0.05);
        transform: scale(0.98);
    }
    
    .recent-bookings-card .table tbody td {
        display: none;
        padding: 0;
        border: none;
    }
    
    /* Glowing ID badge */
    .recent-bookings-card .table tbody td:nth-child(1) {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        font-weight: 800;
        font-size: 14px;
        border-radius: 14px;
        margin-right: 14px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4),
                   inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    
    .recent-bookings-card .table tbody td:nth-child(1)::before {
        content: none !important;
    }
    
    /* Company name - bold white */
    .recent-bookings-card .table tbody td:nth-child(2) {
        display: block;
        font-size: 16px;
        font-weight: 700;
        color: #ffffff;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .recent-bookings-card .table tbody td:nth-child(2)::before {
        content: none !important;
    }
    
    /* Booth count badge */
    .recent-bookings-card .table tbody td:nth-child(3) {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: #818cf8;
        font-weight: 700;
        padding: 6px 12px;
        background: rgba(99, 102, 241, 0.15);
        border-radius: 10px;
        margin-left: 12px;
        flex-shrink: 0;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }
    
    .recent-bookings-card .table tbody td:nth-child(3)::before {
        content: none !important;
    }
    
    /* Chevron arrow */
    .recent-bookings-card .table tbody td:nth-child(5) {
        display: flex;
        align-items: center;
        font-size: 24px;
        color: #4b5563;
        margin-left: 12px;
        flex-shrink: 0;
    }
    
    .recent-bookings-card .table tbody td:nth-child(5)::before {
        content: none !important;
    }
    
    .recent-bookings-card .table tbody td:nth-child(5)::after {
        content: '›';
    }
    
    /* User Performance Table */
    .user-performance-card .table thead {
        display: none;
    }
    
    .user-performance-card .table tbody tr {
        display: block;
        margin-bottom: var(--space-4);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--space-4);
        background: white;
    }
    
    .user-performance-card .table tbody td {
        display: flex;
        justify-content: space-between;
        padding: var(--space-2) 0;
        border-bottom: 1px solid var(--gray-100);
    }
    
    .user-performance-card .table tbody td:last-child {
        border-bottom: none;
        flex-direction: column;
        align-items: stretch;
    }
    
    .user-performance-card .table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        font-size: var(--font-size-xs);
        color: var(--gray-600);
        text-transform: uppercase;
    }
    
    .user-performance-card .progress {
        margin-top: var(--space-2);
    }
    
    /* Badge styles */
    .badge {
        padding: var(--space-1) var(--space-2);
        font-size: var(--font-size-xs);
        font-weight: 600;
        border-radius: var(--radius-sm);
    }
    
    /* Remove hover effects on mobile (replaced with active) */
    .stat-card:hover {
        transform: none;
    }
    
    /* 📱 BOTTOM NAVIGATION BAR */
    .mobile-bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(180deg, #1e1e32, #1a1a2e);
        border-top: 1px solid rgba(99, 102, 241, 0.2);
        padding: 12px 20px 20px;
        display: flex;
        justify-content: space-around;
        align-items: center;
        z-index: 1000;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.3),
                   inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }
    
    .mobile-bottom-nav a {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        text-decoration: none;
        color: #6b7280;
        transition: all 0.3s;
        padding: 8px 12px;
        border-radius: 12px;
        flex: 1;
        max-width: 80px;
    }
    
    .mobile-bottom-nav a.active {
        color: #818cf8;
        background: rgba(99, 102, 241, 0.15);
    }
    
    .mobile-bottom-nav a:active {
        transform: scale(0.9);
    }
    
    .mobile-bottom-nav a i {
        font-size: 22px;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
    }
    
    .mobile-bottom-nav a.active i {
        filter: drop-shadow(0 0 12px rgba(129, 140, 248, 0.6));
    }
    
    .mobile-bottom-nav a span {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Empty state styling */
    .recent-bookings-card table tbody tr td[colspan] {
        display: flex !important;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px !important;
        color: #6b7280 !important;
    }
    
    .recent-bookings-card table tbody tr td[colspan] i {
        font-size: 64px !important;
        margin-bottom: 16px;
        color: #4b5563;
        opacity: 0.3;
    }
    
    .recent-bookings-card table tbody tr td[colspan] p {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #9ca3af;
    }
    
    /* User performance card mobile */
    .user-performance-card {
        background: linear-gradient(145deg, #1e1e32, #16162a) !important;
        border: 1px solid rgba(99, 102, 241, 0.1) !important;
        box-shadow: 8px 8px 16px #0a0a14,
                   -8px -8px 16px #24243c !important;
        border-radius: 24px !important;
        margin-bottom: 80px;
    }
    
    .user-performance-card .card-header {
        background: transparent !important;
        border-bottom: 1px solid rgba(99, 102, 241, 0.1) !important;
        padding: 20px !important;
    }
    
    .user-performance-card .card-header h5 {
        color: #ffffff !important;
        font-weight: 800 !important;
    }
    
    .user-performance-card .card-header h5 i {
        color: #818cf8 !important;
    }
}
</style>
@endpush

@section('content')
<!-- Mobile-Optimized Header -->
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
<div class="dashboard-header d-md-none">
    <h2>Dashboard</h2>
    <p>Welcome back, {{ auth()->user()->username }}!</p>
    <div class="dashboard-actions">
        <button type="button" class="btn" onclick="refreshDashboard()">
            <i class="fas fa-sync-alt"></i>
            <span>Refresh</span>
        </button>
        <a href="{{ route('settings.index') }}" class="btn">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
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

<div class="row g-4 mb-4">
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

<!-- Statistics Cards - Mobile (2 Column Grid) -->
<div class="stats-grid d-md-none">
    <div class="card stat-card border-primary">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Booths</h6>
                    <h2 class="mb-0 text-primary">{{ $stats['total_booths'] }}</h2>
                    @if($stats['total_booths'] > 0)
                    <small class="text-muted">{{ number_format(($stats['available_booths'] / $stats['total_booths']) * 100, 1) }}% Available</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="card stat-card border-success">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Available</h6>
                    <h2 class="mb-0 text-success">{{ $stats['available_booths'] }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card stat-card border-warning">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Reserved</h6>
                    <h2 class="mb-0 text-warning">{{ $stats['reserved_booths'] }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card stat-card border-info">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Confirmed</h6>
                    <h2 class="mb-0 text-info">{{ $stats['confirmed_booths'] }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card stat-card border-dark">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Paid</h6>
                    <h2 class="mb-0 text-dark">{{ $stats['paid_booths'] }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card stat-card border-secondary">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Clients</h6>
                    <h2 class="mb-0 text-secondary">{{ $stats['total_clients'] }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card stat-card border-danger">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Users</h6>
                    <h2 class="mb-0 text-danger">{{ $stats['total_users'] }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card stat-card border-primary">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-2">Total Bookings</h6>
                    <h2 class="mb-0 text-primary">{{ $stats['total_bookings'] }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="dashboard-charts d-md-none">
    <div class="section-header">
        <h3 class="section-title">Analytics</h3>
        <a href="#" class="section-link">View All</a>
    </div>
    
    <div class="card chart-card">
        <div class="card-header">
            <h5><i class="fas fa-chart-pie"></i>Booth Status</h5>
        </div>
        <div class="card-body">
            <canvas id="boothStatusChart" height="250"></canvas>
        </div>
    </div>
    
    <div class="card chart-card">
        <div class="card-header">
            <h5><i class="fas fa-chart-line"></i>Booking Trends</h5>
        </div>
        <div class="card-body">
            <canvas id="bookingTrendChart" height="250"></canvas>
        </div>
    </div>
    
    <!-- Recent Bookings (Mobile) -->
    <div class="section-header" style="margin-top: 24px;">
        <h3 class="section-title">Recent Activity</h3>
        <a href="#" class="section-link">See All</a>
    </div>
    
    <div class="card recent-bookings-card">
        <div class="card-header">
            <h5>Recent Bookings</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <tbody>
                        @forelse($recentBookings as $booking)
                        <tr>
                            <td data-label="ID">{{ $booking->id }}</td>
                            <td data-label="Client">{{ $booking->client->company ?? 'N/A' }}</td>
                            <td data-label="Booths">{{ count(json_decode($booking->boothid, true) ?? []) }} booths</td>
                            <td data-label="Date">{{ $booking->date_book->format('M d') }}</td>
                            <td data-label="User">{{ $booking->user->username }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px 20px; color: #9ca3af;">
                                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 12px; display: block;"></i>
                                <p style="margin: 0;">No bookings yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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
                <h5 class="mb-0">Recent Bookings</h5>
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
                                <td data-label="ID">{{ $booking->id }}</td>
                                <td data-label="Client">{{ $booking->client->company ?? 'N/A' }}</td>
                                <td data-label="Booths">{{ count(json_decode($booking->boothid, true) ?? []) }} booths</td>
                                <td data-label="Date">{{ $booking->date_book->format('Y-m-d H:i') }}</td>
                                <td data-label="User">{{ $booking->user->username }}</td>
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

<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav d-md-none">
    <a href="{{ route('dashboard') }}" class="active">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('booths.index') }}">
        <i class="fas fa-store"></i>
        <span>Booths</span>
    </a>
    <a href="{{ route('books.index') }}">
        <i class="fas fa-calendar-check"></i>
        <span>Bookings</span>
    </a>
    <a href="{{ route('clients.index') }}">
        <i class="fas fa-users"></i>
        <span>Clients</span>
    </a>
    <a href="{{ route('settings.index') }}">
        <i class="fas fa-cog"></i>
        <span>Settings</span>
    </a>
</nav>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
        colors: ['#34d399', '#fbbf24', '#60a5fa', '#818cf8', '#6b7280']
    }
};

// Check if mobile
const isMobile = window.innerWidth <= 768;

// Mobile Booth Status Chart - Dark Theme
const boothStatusMobile = document.getElementById('boothStatusChart');
if (boothStatusMobile) {
    new Chart(boothStatusMobile.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: chartData.boothStatus.labels,
            datasets: [{
                data: chartData.boothStatus.data,
                backgroundColor: chartData.boothStatus.colors,
                borderWidth: 3,
                borderColor: '#1a1a2e',
                hoverOffset: 12,
                hoverBorderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        font: { size: 13, weight: '700' },
                        usePointStyle: true,
                        pointStyle: 'circle',
                        color: '#e5e7eb',
                        boxWidth: 8,
                        boxHeight: 8
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 30, 50, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#e5e7eb',
                    borderColor: 'rgba(99, 102, 241, 0.3)',
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

// Mobile Booking Trends Chart - Dark Theme
const bookingTrendMobile = document.getElementById('bookingTrendChart');
if (bookingTrendMobile) {
    new Chart(bookingTrendMobile.getContext('2d'), {
        type: 'line',
        data: {
            labels: last7Days,
            datasets: [{
                label: 'Bookings',
                data: bookingCounts,
                borderColor: '#818cf8',
                backgroundColor: 'rgba(129, 140, 248, 0.15)',
                tension: 0.4,
                fill: true,
                borderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointBackgroundColor: '#818cf8',
                pointBorderColor: '#1a1a2e',
                pointBorderWidth: 3,
                pointHoverBackgroundColor: '#a5b4fc',
                pointHoverBorderWidth: 4
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
                        color: '#9ca3af'
                    },
                    grid: { 
                        color: 'rgba(99, 102, 241, 0.08)',
                        lineWidth: 1
                    },
                    border: { color: 'rgba(99, 102, 241, 0.2)' }
                },
                x: {
                    ticks: { 
                        font: { size: 12, weight: '600' },
                        color: '#9ca3af'
                    },
                    grid: { display: false },
                    border: { color: 'rgba(99, 102, 241, 0.2)' }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(30, 30, 50, 0.95)',
                    titleColor: '#ffffff',
                    bodyColor: '#e5e7eb',
                    borderColor: 'rgba(99, 102, 241, 0.3)',
                    borderWidth: 1,
                    padding: 12,
                    cornerRadius: 12,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 }
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
</script>
@endpush

