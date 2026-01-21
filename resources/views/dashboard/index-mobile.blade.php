@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
@php
    try {
        $cdnSettings = \App\Models\Setting::getCDNSettings();
        $useCDN = $cdnSettings['use_cdn'] ?? true;
    } catch (\Exception $e) {
        $useCDN = true;
    }
@endphp
<style>
/* ============================================
   MODERN MOBILE APP DASHBOARD
   Complete Redesign - Native App Experience
   ============================================ */

* {
    -webkit-tap-highlight-color: transparent;
    -webkit-touch-callout: none;
}

html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    overflow-x: hidden;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

body {
    padding-bottom: 90px !important;
    position: relative;
}

/* Hide all desktop elements */
nav.navbar,
.navbar,
.navbar-expand-lg,
.d-none.d-md-flex,
.d-none.d-md-block {
    display: none !important;
}

main.container-fluid,
.container-fluid,
.content-wrapper,
.content {
    padding: 0 !important;
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* ============================================
   STICKY HEADER - Modern Mobile App Style
   ============================================ */
.modern-mobile-header {
    position: sticky !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    z-index: 1000 !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 16px 20px 20px 20px !important;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
    margin: 0 !important;
    width: 100% !important;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

.modern-mobile-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
}

.app-header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    z-index: 1;
}

.app-header-left {
    flex: 1;
}

.app-header-greeting {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.85);
    font-weight: 500;
    margin-bottom: 6px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    font-size: 11px;
}

.app-header-title {
    font-size: 28px;
    font-weight: 800;
    color: #ffffff;
    margin: 0;
    letter-spacing: -0.5px;
    line-height: 1.2;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.app-header-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.app-header-btn {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    cursor: pointer;
}

.app-header-btn:active {
    background: rgba(255, 255, 255, 0.28);
    transform: scale(0.92);
    border-color: rgba(255, 255, 255, 0.3);
}

/* ============================================
   STATISTICS CARDS - Modern Glassmorphism
   ============================================ */
.app-stats-section {
    padding: 20px 16px !important;
    margin-bottom: 8px !important;
}

.app-stats-grid {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 12px !important;
    width: 100% !important;
}

.app-stat-card {
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border-radius: 20px !important;
    padding: 18px 16px !important;
    box-shadow: 
        0 4px 16px rgba(0, 0, 0, 0.06),
        0 1px 4px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
    position: relative !important;
    overflow: hidden !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: 1px solid rgba(255, 255, 255, 0.8) !important;
    cursor: pointer;
}

.app-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--stat-gradient, linear-gradient(90deg, #667eea, #764ba2));
    border-radius: 20px 20px 0 0;
}

.app-stat-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 120px;
    height: 120px;
    background: radial-gradient(circle, var(--stat-glow, rgba(102, 126, 234, 0.08)) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.app-stat-card:active {
    transform: scale(0.96) translateY(1px);
    box-shadow: 
        0 2px 8px rgba(0, 0, 0, 0.08),
        0 1px 2px rgba(0, 0, 0, 0.04) !important;
}

.app-stat-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #64748b;
    margin-bottom: 10px;
    display: block;
    opacity: 0.8;
}

.app-stat-value {
    font-size: 32px;
    font-weight: 800;
    line-height: 1;
    margin: 0 0 8px 0;
    color: var(--stat-color, #1e293b);
    letter-spacing: -1px;
    background: var(--stat-gradient-text, linear-gradient(135deg, var(--stat-color, #667eea), var(--stat-color-dark, #764ba2)));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.app-stat-icon {
    position: absolute;
    bottom: 14px;
    right: 14px;
    width: 44px;
    height: 44px;
    border-radius: 14px;
    background: var(--stat-bg, linear-gradient(135deg, rgba(102, 126, 234, 0.12), rgba(118, 75, 162, 0.12)));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--stat-color, #667eea);
    box-shadow: 0 2px 8px var(--stat-shadow, rgba(102, 126, 234, 0.15));
}

.app-stat-card.primary { 
    --stat-color: #667eea; 
    --stat-color-dark: #764ba2;
    --stat-bg: linear-gradient(135deg, rgba(102, 126, 234, 0.12), rgba(118, 75, 162, 0.12));
    --stat-gradient: linear-gradient(90deg, #667eea, #764ba2);
    --stat-gradient-text: linear-gradient(135deg, #667eea, #764ba2);
    --stat-glow: rgba(102, 126, 234, 0.1);
    --stat-shadow: rgba(102, 126, 234, 0.2);
}

.app-stat-card.success { 
    --stat-color: #10b981; 
    --stat-color-dark: #059669;
    --stat-bg: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(5, 150, 105, 0.12));
    --stat-gradient: linear-gradient(90deg, #10b981, #059669);
    --stat-gradient-text: linear-gradient(135deg, #10b981, #059669);
    --stat-glow: rgba(16, 185, 129, 0.1);
    --stat-shadow: rgba(16, 185, 129, 0.2);
}

.app-stat-card.warning { 
    --stat-color: #f59e0b; 
    --stat-color-dark: #d97706;
    --stat-bg: linear-gradient(135deg, rgba(245, 158, 11, 0.12), rgba(217, 119, 6, 0.12));
    --stat-gradient: linear-gradient(90deg, #f59e0b, #d97706);
    --stat-gradient-text: linear-gradient(135deg, #f59e0b, #d97706);
    --stat-glow: rgba(245, 158, 11, 0.1);
    --stat-shadow: rgba(245, 158, 11, 0.2);
}

.app-stat-card.info { 
    --stat-color: #3b82f6; 
    --stat-color-dark: #2563eb;
    --stat-bg: linear-gradient(135deg, rgba(59, 130, 246, 0.12), rgba(37, 99, 235, 0.12));
    --stat-gradient: linear-gradient(90deg, #3b82f6, #2563eb);
    --stat-gradient-text: linear-gradient(135deg, #3b82f6, #2563eb);
    --stat-glow: rgba(59, 130, 246, 0.1);
    --stat-shadow: rgba(59, 130, 246, 0.2);
}

.app-stat-card.dark { 
    --stat-color: #1f2937; 
    --stat-color-dark: #111827;
    --stat-bg: linear-gradient(135deg, rgba(31, 41, 55, 0.12), rgba(17, 24, 39, 0.12));
    --stat-gradient: linear-gradient(90deg, #1f2937, #111827);
    --stat-gradient-text: linear-gradient(135deg, #1f2937, #111827);
    --stat-glow: rgba(31, 41, 55, 0.1);
    --stat-shadow: rgba(31, 41, 55, 0.2);
}

.app-stat-card.secondary { 
    --stat-color: #6b7280; 
    --stat-color-dark: #4b5563;
    --stat-bg: linear-gradient(135deg, rgba(107, 114, 128, 0.12), rgba(75, 85, 99, 0.12));
    --stat-gradient: linear-gradient(90deg, #6b7280, #4b5563);
    --stat-gradient-text: linear-gradient(135deg, #6b7280, #4b5563);
    --stat-glow: rgba(107, 114, 128, 0.1);
    --stat-shadow: rgba(107, 114, 128, 0.2);
}

.app-stat-card.danger { 
    --stat-color: #ef4444; 
    --stat-color-dark: #dc2626;
    --stat-bg: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(220, 38, 38, 0.12));
    --stat-gradient: linear-gradient(90deg, #ef4444, #dc2626);
    --stat-gradient-text: linear-gradient(135deg, #ef4444, #dc2626);
    --stat-glow: rgba(239, 68, 68, 0.1);
    --stat-shadow: rgba(239, 68, 68, 0.2);
}

/* ============================================
   SECTIONS - Modern Card Design
   ============================================ */
.app-section {
    padding: 0 16px;
    margin-bottom: 20px;
}

.app-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    padding: 0 4px;
}

.app-section-title {
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    letter-spacing: -0.5px;
}

.app-section-link {
    font-size: 13px;
    font-weight: 600;
    color: #667eea;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
    border-radius: 12px;
    background: rgba(102, 126, 234, 0.08);
    transition: all 0.2s;
}

.app-section-link:active {
    background: rgba(102, 126, 234, 0.15);
    transform: scale(0.95);
}


/* ============================================
   RECENT BOOKINGS - Modern List Design
   ============================================ */
.app-recent-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 20px;
    box-shadow: 
        0 8px 24px rgba(0, 0, 0, 0.06),
        0 2px 8px rgba(0, 0, 0, 0.04),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 0.8);
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
    border-bottom: 1px solid rgba(226, 232, 240, 0.6);
    transition: all 0.2s;
    cursor: pointer;
    border-radius: 12px;
    margin: 0 -8px;
    padding-left: 8px;
    padding-right: 8px;
}

.app-recent-item:last-child {
    border-bottom: none;
}

.app-recent-item:active {
    background: rgba(102, 126, 234, 0.05);
    transform: scale(0.98);
}

.app-recent-item-left {
    flex: 1;
    min-width: 0;
}

.app-recent-item-id {
    font-size: 17px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
    letter-spacing: -0.3px;
}

.app-recent-item-client {
    font-size: 13px;
    color: #64748b;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.app-recent-item-right {
    text-align: right;
    margin-left: 12px;
    flex-shrink: 0;
}

.app-recent-item-booths {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
}

.app-recent-item-date {
    font-size: 12px;
    color: #94a3b8;
    font-weight: 500;
}

.app-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}

.app-empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    background: linear-gradient(135deg, rgba(148, 163, 184, 0.1), rgba(148, 163, 184, 0.05));
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 36px;
    color: #cbd5e1;
}

.app-empty-text {
    font-size: 16px;
    font-weight: 500;
    margin: 0;
    color: #94a3b8;
}

/* ============================================
   BOTTOM NAVIGATION - Always Sticky
   ============================================ */
.modern-mobile-nav {
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(20px) !important;
    -webkit-backdrop-filter: blur(20px) !important;
    border-top: 1px solid rgba(226, 232, 240, 0.8) !important;
    padding: 10px 0 calc(10px + env(safe-area-inset-bottom)) !important;
    z-index: 1000 !important;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08) !important;
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
    color: #94a3b8;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 64px;
    position: relative;
    border-radius: 12px;
    margin: 0 4px;
}

.app-bottom-nav-item i {
    font-size: 22px;
    margin-bottom: 4px;
    transition: all 0.2s;
}

.app-bottom-nav-item span {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.2s;
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
    width: 36px;
    height: 3px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 0 0 3px 3px;
}

.app-bottom-nav-item:active {
    transform: scale(0.92);
    background: rgba(102, 126, 234, 0.08);
}

/* ============================================
   FLOATING ACTION BUTTON
   ============================================ */
.app-quick-actions {
    position: fixed;
    bottom: 100px;
    right: 20px;
    z-index: 999;
}

.app-fab {
    width: 60px;
    height: 60px;
    border-radius: 18px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    box-shadow: 
        0 8px 24px rgba(102, 126, 234, 0.4),
        0 4px 12px rgba(102, 126, 234, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    cursor: pointer;
}

.app-fab:active {
    transform: scale(0.88);
    box-shadow: 
        0 4px 12px rgba(102, 126, 234, 0.3),
        0 2px 6px rgba(102, 126, 234, 0.2);
}

/* ============================================
   SMOOTH SCROLLING
   ============================================ */
@media (max-width: 768px) {
    html {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }
    
    body {
        overscroll-behavior-y: contain;
    }
}

/* ============================================
   LOADING ANIMATIONS
   ============================================ */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.app-stat-card,
.app-recent-card {
    animation: fadeInUp 0.4s ease-out backwards;
}

.app-stat-card:nth-child(1) { animation-delay: 0.05s; }
.app-stat-card:nth-child(2) { animation-delay: 0.1s; }
.app-stat-card:nth-child(3) { animation-delay: 0.15s; }
.app-stat-card:nth-child(4) { animation-delay: 0.2s; }
.app-stat-card:nth-child(5) { animation-delay: 0.25s; }
.app-stat-card:nth-child(6) { animation-delay: 0.3s; }
.app-stat-card:nth-child(7) { animation-delay: 0.35s; }
.app-stat-card:nth-child(8) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<!-- Modern Mobile Header -->
<div class="modern-mobile-header">
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

<!-- Statistics Cards Grid -->
<div class="app-stats-section">
    <div class="app-stats-grid">
        <div class="app-stat-card primary">
            <span class="app-stat-label">Total Booths</span>
            <h2 class="app-stat-value">{{ number_format($stats['total_booths']) }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-store"></i>
            </div>
        </div>
        
        <div class="app-stat-card success">
            <span class="app-stat-label">Available</span>
            <h2 class="app-stat-value">{{ number_format($stats['available_booths']) }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        
        <div class="app-stat-card warning">
            <span class="app-stat-label">Reserved</span>
            <h2 class="app-stat-value">{{ number_format($stats['reserved_booths']) }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        
        <div class="app-stat-card info">
            <span class="app-stat-label">Confirmed</span>
            <h2 class="app-stat-value">{{ number_format($stats['confirmed_booths']) }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-check-double"></i>
            </div>
        </div>
        
        <div class="app-stat-card dark">
            <span class="app-stat-label">Paid</span>
            <h2 class="app-stat-value">{{ number_format($stats['paid_booths']) }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
        
        <div class="app-stat-card secondary">
            <span class="app-stat-label">Clients</span>
            <h2 class="app-stat-value">{{ number_format($stats['total_clients']) }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        
        <div class="app-stat-card danger">
            <span class="app-stat-label">Users</span>
            <h2 class="app-stat-value">{{ number_format($stats['total_users']) }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
        
        <div class="app-stat-card primary">
            <span class="app-stat-label">Bookings</span>
            <h2 class="app-stat-value">{{ number_format($stats['total_bookings']) }}</h2>
            <div class="app-stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="app-section">
    <div class="app-section-header">
        <h3 class="app-section-title">Recent Activity</h3>
        <a href="{{ route('books.index') }}" class="app-section-link">
            See All
            <i class="fas fa-chevron-right" style="font-size: 10px;"></i>
        </a>
    </div>
    
    <div class="app-recent-card">
        @if($recentBookings && $recentBookings->count() > 0)
        <ul class="app-recent-list">
            @foreach($recentBookings->take(5) as $booking)
            <li class="app-recent-item" onclick="window.location.href='{{ route('books.show', $booking->id) }}'">
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

<!-- Bottom Navigation - Always Visible -->
<nav class="modern-mobile-nav">
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
<div class="app-quick-actions">
    <a href="{{ route('books.create') }}" class="app-fab" title="New Booking">
        <i class="fas fa-plus"></i>
    </a>
</div>

@endsection

@push('scripts')
<script>
// Send screen width to server
(function() {
    const screenWidth = window.innerWidth;
    sessionStorage.setItem('screen_width', screenWidth);
    
    if (window.fetch) {
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            if (!args[1]) args[1] = {};
            args[1].headers = args[1].headers || {};
            if (typeof args[1].headers === 'object' && !(args[1].headers instanceof Headers)) {
                args[1].headers['X-Screen-Width'] = screenWidth;
            }
            return originalFetch.apply(this, args);
        };
    }
})();

function refreshDashboard() {
    const btn = event.target.closest('.app-header-btn');
    if (btn) {
        btn.style.transform = 'rotate(360deg)';
        setTimeout(() => {
            btn.style.transform = '';
            window.location.reload();
        }, 500);
    } else {
        window.location.reload();
    }
}

// Smooth scroll behavior
document.addEventListener('DOMContentLoaded', function() {
    // Add touch feedback
    const cards = document.querySelectorAll('.app-stat-card, .app-recent-item');
    cards.forEach(card => {
        card.addEventListener('touchstart', function() {
            this.style.transition = 'transform 0.1s';
        });
        card.addEventListener('touchend', function() {
            setTimeout(() => {
                this.style.transition = '';
            }, 100);
        });
    });
});
</script>
@endpush
