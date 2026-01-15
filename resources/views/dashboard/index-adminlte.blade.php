@extends('layouts.adminlte')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@php
    try {
        $cdnSettings = \App\Models\Setting::getCDNSettings();
        $useCDN = $cdnSettings['use_cdn'] ?? true; // Default to true (CDN enabled)
    } catch (\Exception $e) {
        // If settings table doesn't exist or error occurs, default to CDN enabled
        $useCDN = true;
    }
@endphp

@push('styles')
<link rel="stylesheet" href="{{ asset('css/modern-design-system.css') }}">
<style>
/* ============================================
   MODERN DASHBOARD REDESIGN
   ============================================ */

/* Desktop Modern Design */
@media (min-width: 769px) {
    body {
        background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%) !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif !important;
    }
    
    .container-fluid {
        max-width: 1400px !important;
        margin: 0 auto !important;
        padding: 24px !important;
    }
    
    .modern-dashboard-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%) !important;
        border-radius: 24px !important;
        padding: 32px !important;
        margin-bottom: 32px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        color: white !important;
        position: relative !important;
        overflow: hidden !important;
        display: block !important;
    }
    
    .modern-dashboard-header::before {
        content: '' !important;
        position: absolute !important;
        top: -50% !important;
        right: -20% !important;
        width: 500px !important;
        height: 500px !important;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%) !important;
        border-radius: 50% !important;
    }
    
    .modern-dashboard-header h1 {
        font-size: 36px !important;
        font-weight: 800 !important;
        margin-bottom: 8px !important;
        position: relative !important;
        z-index: 1 !important;
        color: white !important;
    }
    
    .modern-dashboard-header p {
        font-size: 18px !important;
        opacity: 0.95 !important;
        position: relative !important;
        z-index: 1 !important;
        color: white !important;
        margin: 0 !important;
    }
    
    .modern-kpi-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 24px !important;
        margin-bottom: 32px !important;
    }
    
    .modern-kpi-card {
        background: white !important;
        border-radius: 24px !important;
        padding: 24px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e5e7eb !important;
        position: relative !important;
        overflow: hidden !important;
        display: block !important;
    }
    
    .modern-kpi-card::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 4px !important;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%) !important;
        opacity: 0 !important;
        transition: opacity 0.3s ease !important;
    }
    
    .modern-kpi-card:hover {
        transform: translateY(-8px) scale(1.02) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        border-color: #818cf8 !important;
    }
    
    .modern-kpi-card:hover::before {
        opacity: 1 !important;
    }
    
    .modern-kpi-icon {
        width: 64px !important;
        height: 64px !important;
        border-radius: 16px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 28px !important;
        margin-bottom: 16px !important;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%) !important;
        color: white !important;
        box-shadow: 0 10px 30px -5px rgba(99, 102, 241, 0.3) !important;
    }
    
    .modern-kpi-card.success .modern-kpi-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }
    
    .modern-kpi-card.warning .modern-kpi-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    }
    
    .modern-kpi-card.info .modern-kpi-icon {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
    }
    
    .modern-kpi-value {
        font-size: 36px !important;
        font-weight: 800 !important;
        color: #111827 !important;
        margin: 8px 0 !important;
        line-height: 1 !important;
        display: block !important;
    }
    
    .modern-kpi-label {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #4b5563 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        display: block !important;
    }
    
    /* Modern Buttons */
    .modern-btn {
        padding: 10px 20px !important;
        border-radius: 12px !important;
        font-weight: 600 !important;
        font-size: 14px !important;
        border: none !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
    }
    
    .modern-btn-ghost {
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
    }
    
    .modern-btn-ghost:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        transform: translateY(-2px) !important;
    }
    
    .modern-chart-container {
        background: white !important;
        border-radius: 24px !important;
        padding: 24px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        margin-bottom: 24px !important;
        border: 1px solid #e5e7eb !important;
        display: block !important;
    }
    
    .modern-chart-container:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-2px) !important;
    }
    
    .modern-chart-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 24px !important;
        padding-bottom: 16px !important;
        border-bottom: 2px solid #f3f4f6 !important;
    }
    
    .modern-chart-title {
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin: 0 !important;
    }
    
    .modern-chart-title i {
        color: #6366f1 !important;
        font-size: 22px !important;
    }
    
    /* Modern Static Statistics Container */
    .modern-stat-container {
        background: white !important;
        border-radius: 24px !important;
        padding: 24px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        margin-bottom: 24px !important;
        border: 1px solid #e5e7eb !important;
        display: block !important;
    }
    
    .modern-stat-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 24px !important;
        padding-bottom: 16px !important;
        border-bottom: 2px solid #f3f4f6 !important;
    }
    
    .modern-stat-title {
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        margin: 0 !important;
    }
    
    .modern-stat-title i {
        color: #6366f1 !important;
        font-size: 22px !important;
    }
    
    /* Booking Trends Visualization */
    .modern-trend-visualization {
        min-height: 300px !important;
        padding: 16px 0 !important;
        display: block !important;
    }
    
    .modern-trend-bars {
        display: flex !important;
        align-items: flex-end !important;
        justify-content: space-between !important;
        gap: 8px !important;
        height: 250px !important;
        padding: 16px 0 !important;
    }
    
    .modern-trend-bar-item {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        height: 100% !important;
        justify-content: flex-end !important;
    }
    
    .modern-trend-bar-wrapper {
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        align-items: flex-end !important;
        justify-content: center !important;
        position: relative !important;
    }
    
    .modern-trend-bar {
        width: 100% !important;
        max-width: 40px !important;
        background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%) !important;
        border-radius: 12px 12px 0 0 !important;
        position: relative !important;
        min-height: 20px !important;
        display: block !important;
        cursor: pointer !important;
    }
    
    .modern-trend-bar:hover {
        opacity: 0.8 !important;
    }
    
    .modern-trend-bar-value {
        position: absolute !important;
        top: -28px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #374151 !important;
        white-space: nowrap !important;
        opacity: 0 !important;
        transition: opacity 0.2s ease !important;
    }
    
    .modern-trend-bar:hover .modern-trend-bar-value {
        opacity: 1 !important;
    }
    
    .modern-trend-bar-label {
        font-size: 12px !important;
        color: #4b5563 !important;
        margin-top: 8px !important;
        text-align: center !important;
        font-weight: 500 !important;
        display: block !important;
    }
    
    .modern-trend-empty {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        height: 250px !important;
        color: #9ca3af !important;
    }
    
    .modern-trend-empty i {
        font-size: 48px !important;
        margin-bottom: 16px !important;
    }
    
    /* Booth Status Breakdown */
    .modern-status-breakdown {
        padding: 8px 0 !important;
        display: block !important;
    }
    
    .modern-status-item {
        margin-bottom: 20px !important;
        display: block !important;
    }
    
    .modern-status-item:last-child {
        margin-bottom: 0 !important;
    }
    
    .modern-status-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 8px !important;
    }
    
    .modern-status-label {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #374151 !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    
    .modern-status-value {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #111827 !important;
    }
    
    .modern-status-bar {
        height: 12px !important;
        background: #f3f4f6 !important;
        border-radius: 9999px !important;
        overflow: hidden !important;
        position: relative !important;
        display: block !important;
    }
    
    .modern-status-bar-fill {
        height: 100% !important;
        border-radius: 9999px !important;
        display: block !important;
    }
}

/* ============================================
   MOBILE APP REDESIGN (≤768px)
   ============================================ */
@media (max-width: 768px) {
    /* Reset for mobile */
    * {
        box-sizing: border-box;
    }
    
    /* Hide AdminLTE elements */
    .main-sidebar,
    .main-header,
    .content-header,
    .breadcrumb {
        display: none !important;
    }
    
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #ec4899 100%) !important;
        padding: 0 !important;
        margin: 0 !important;
        padding-bottom: 90px !important;
        overflow-x: hidden !important;
        min-height: 100vh !important;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif !important;
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
    
    /* Modern Mobile Header - Always Sticky at Top */
    .modern-mobile-header {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        z-index: 9998 !important;
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        padding: 20px !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;
        display: block !important;
        margin: 0 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Add padding to body to account for fixed header */
    body {
        padding-top: 80px !important;
    }
    
    .modern-mobile-header-content {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
    }
    
    .modern-mobile-greeting {
        font-size: 12px !important;
        color: rgba(255, 255, 255, 0.8) !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        margin-bottom: 4px !important;
        display: block !important;
    }
    
    .modern-mobile-title {
        font-size: 24px !important;
        font-weight: 800 !important;
        color: white !important;
        margin: 0 !important;
        letter-spacing: -0.02em !important;
        display: block !important;
    }
    
    .modern-mobile-actions {
        display: flex;
        gap: var(--spacing-2);
    }
    
    .modern-mobile-action-btn {
        width: 44px;
        height: 44px;
        border-radius: var(--radius-xl);
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        transition: all var(--transition-fast);
        will-change: transform;
        transform: translateZ(0);
    }
    
    .modern-mobile-action-btn:active {
        transform: scale(0.9) translateZ(0);
        background: rgba(255, 255, 255, 0.3);
    }
    
    /* Modern Mobile Stats */
    .modern-mobile-stats {
        padding: 20px !important;
        display: block !important;
    }
    
    .modern-mobile-stats-grid {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 16px !important;
        margin-bottom: 24px !important;
    }
    
    .modern-mobile-stat {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-radius: 24px !important;
        padding: 20px !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        position: relative !important;
        overflow: hidden !important;
        display: block !important;
    }
    
    .modern-mobile-stat::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 3px !important;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%) !important;
    }
    
    .modern-mobile-stat.success::before {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }
    
    .modern-mobile-stat.warning::before {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    }
    
    .modern-mobile-stat.info::before {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
    }
    
    .modern-mobile-stat-label {
        font-size: 12px !important;
        font-weight: 700 !important;
        color: #6b7280 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        margin-bottom: 8px !important;
        display: block !important;
    }
    
    .modern-mobile-stat-value {
        font-size: 30px !important;
        font-weight: 800 !important;
        color: #111827 !important;
        line-height: 1 !important;
        margin: 8px 0 !important;
        letter-spacing: -0.02em !important;
        display: block !important;
    }
    
    .modern-mobile-stat-icon {
        position: absolute !important;
        bottom: 16px !important;
        right: 16px !important;
        width: 48px !important;
        height: 48px !important;
        border-radius: 12px !important;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%) !important;
        color: white !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 20px !important;
        opacity: 0.9 !important;
    }
    
    .modern-mobile-stat.success .modern-mobile-stat-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    }
    
    .modern-mobile-stat.warning .modern-mobile-stat-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    }
    
    .modern-mobile-stat.info .modern-mobile-stat-icon {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%) !important;
    }
    
    /* Modern Mobile Statistics Section */
    .modern-mobile-stat-section {
        padding: 0 20px 24px !important;
    }
    
    .modern-mobile-stat-card {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 24px !important;
        padding: 20px !important;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        margin-bottom: 16px !important;
        display: block !important;
    }
    
    .modern-mobile-stat-card-title {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin-bottom: 16px !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    
    .modern-mobile-stat-card-title i {
        color: #6366f1 !important;
        font-size: 20px !important;
    }
    
    /* Mobile Trend Visualization */
    .modern-mobile-trend-visualization {
        min-height: 200px !important;
        padding: 12px 0 !important;
        display: block !important;
    }
    
    .modern-mobile-trend-bars {
        display: flex !important;
        align-items: flex-end !important;
        justify-content: space-between !important;
        gap: 4px !important;
        height: 180px !important;
        padding: 12px 0 !important;
    }
    
    .modern-mobile-trend-item {
        flex: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        height: 100% !important;
        justify-content: flex-end !important;
    }
    
    .modern-mobile-trend-bar-wrapper {
        width: 100% !important;
        height: 100% !important;
        display: flex !important;
        align-items: flex-end !important;
        justify-content: center !important;
        position: relative !important;
    }
    
    .modern-mobile-trend-bar {
        width: 100% !important;
        max-width: 24px !important;
        background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%) !important;
        border-radius: 6px 6px 0 0 !important;
        position: relative !important;
        min-height: 16px !important;
        display: block !important;
    }
    
    .modern-mobile-trend-value {
        position: absolute !important;
        top: -20px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        color: #374151 !important;
        white-space: nowrap !important;
    }
    
    .modern-mobile-trend-label {
        font-size: 9px !important;
        color: #4b5563 !important;
        margin-top: 4px !important;
        text-align: center !important;
        font-weight: 500 !important;
        display: block !important;
    }
    
    .modern-mobile-trend-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 180px;
        color: var(--color-gray-400);
    }
    
    .modern-mobile-trend-empty i {
        font-size: 36px;
        margin-bottom: var(--spacing-3);
    }
    
    /* Mobile Status Breakdown */
    .modern-mobile-status-breakdown {
        padding: 8px 0 !important;
        display: block !important;
    }
    
    .modern-mobile-status-item {
        margin-bottom: 16px !important;
        display: block !important;
    }
    
    .modern-mobile-status-item:last-child {
        margin-bottom: 0 !important;
    }
    
    .modern-mobile-status-row {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        margin-bottom: 4px !important;
    }
    
    .modern-mobile-status-label {
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #374151 !important;
        display: flex !important;
        align-items: center !important;
        gap: 4px !important;
    }
    
    .modern-mobile-status-value {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #111827 !important;
    }
    
    .modern-mobile-status-bar {
        height: 8px !important;
        background: #f3f4f6 !important;
        border-radius: 9999px !important;
        overflow: hidden !important;
        position: relative !important;
        display: block !important;
    }
    
    .modern-mobile-status-fill {
        height: 100% !important;
        border-radius: 9999px !important;
        display: block !important;
    }
    
    /* Modern Mobile Bottom Nav - Always Sticky at Bottom */
    .modern-mobile-nav {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
        padding: 12px 16px !important;
        padding-bottom: calc(12px + env(safe-area-inset-bottom)) !important;
        z-index: 9999 !important;
        display: flex !important;
        justify-content: space-around !important;
        align-items: center !important;
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1) !important;
        will-change: transform !important;
        transform: translateZ(0) !important;
        margin: 0 !important;
        box-sizing: border-box !important;
    }
    
    /* Ensure nav stays at bottom even on scroll */
    html, body {
        overflow-x: hidden !important;
    }
    
    /* Add padding to content to prevent overlap */
    .modern-mobile-stat-section {
        padding-bottom: 100px !important;
    }
    
    .modern-mobile-nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: var(--spacing-1);
        padding: var(--spacing-2);
        border-radius: var(--radius-lg);
        color: var(--color-gray-600);
        text-decoration: none;
        transition: all var(--transition-fast);
        min-width: 60px;
        will-change: transform;
        transform: translateZ(0);
    }
    
    .modern-mobile-nav-item i {
        font-size: 22px;
    }
    
    .modern-mobile-nav-item span {
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
    }
    
    .modern-mobile-nav-item.active {
        color: var(--color-primary);
        background: rgba(99, 102, 241, 0.1);
    }
    
    .modern-mobile-nav-item:active {
        transform: scale(0.9) translateZ(0);
    }
    
    /* Modern Mobile FAB */
    .modern-mobile-fab {
        position: fixed;
        bottom: 100px;
        right: var(--spacing-5);
        width: 64px;
        height: 64px;
        border-radius: var(--radius-full);
        background: var(--gradient-primary);
        color: white;
        box-shadow: 0 12px 32px rgba(99, 102, 241, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        border: none;
        cursor: pointer;
        z-index: 1040;
        transition: all var(--transition-spring);
        will-change: transform, box-shadow;
        transform: translateZ(0);
    }
    
    .modern-mobile-fab:active {
        transform: scale(0.9) translateZ(0);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
    }
    
    /* Hide desktop elements on mobile */
    .d-none.d-md-block {
        display: none !important;
    }
}

/* ============================================
   DESKTOP STYLES FOR MOBILE ELEMENTS (d-md-none)
   ============================================ */
@media (min-width: 769px) {
    /* Hide mobile elements on desktop - d-md-none should work */
    .d-md-none {
        display: none !important;
    }
    
    /* Modern Mobile Header - Desktop Version */
    .modern-mobile-header {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        z-index: 9998 !important;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%) !important;
        border-radius: 0 !important;
        padding: 24px 32px !important;
        margin-bottom: 0 !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1) !important;
        overflow: hidden !important;
    }
    
    /* Add padding to body for fixed header on desktop */
    body {
        padding-top: 90px !important;
    }
    
    .modern-mobile-header::before {
        content: '' !important;
        position: absolute !important;
        top: -50% !important;
        right: -20% !important;
        width: 500px !important;
        height: 500px !important;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%) !important;
        border-radius: 50% !important;
    }
    
    .modern-mobile-greeting {
        font-size: 14px !important;
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        margin-bottom: 8px !important;
    }
    
    .modern-mobile-title {
        font-size: 36px !important;
        font-weight: 800 !important;
        color: white !important;
        margin: 0 !important;
        letter-spacing: -0.02em !important;
    }
    
    .modern-mobile-action-btn {
        width: 48px !important;
        height: 48px !important;
        border-radius: 16px !important;
        background: rgba(255, 255, 255, 0.2) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: white !important;
        font-size: 20px !important;
        transition: all 0.3s ease !important;
    }
    
    .modern-mobile-action-btn:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        transform: translateY(-2px) !important;
    }
    
    /* Modern Mobile Stats - Desktop Version */
    .modern-mobile-stats {
        padding: 0 !important;
        margin-bottom: 32px !important;
    }
    
    .modern-mobile-stats-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 24px !important;
        margin-bottom: 0 !important;
    }
    
    .modern-mobile-stat {
        background: white !important;
        border-radius: 24px !important;
        padding: 24px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e5e7eb !important;
        transition: all 0.3s ease !important;
        position: relative !important;
        overflow: hidden !important;
    }
    
    .modern-mobile-stat:hover {
        transform: translateY(-8px) scale(1.02) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        border-color: #818cf8 !important;
    }
    
    .modern-mobile-stat-label {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #4b5563 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        margin-bottom: 12px !important;
    }
    
    .modern-mobile-stat-value {
        font-size: 36px !important;
        font-weight: 800 !important;
        color: #111827 !important;
        line-height: 1 !important;
        margin: 12px 0 !important;
    }
    
    .modern-mobile-stat-icon {
        position: absolute !important;
        bottom: 20px !important;
        right: 20px !important;
        width: 64px !important;
        height: 64px !important;
        border-radius: 16px !important;
        font-size: 28px !important;
    }
    
    /* Modern Mobile Stat Section - Desktop Version */
    .modern-mobile-stat-section {
        padding: 0 !important;
        margin-bottom: 32px !important;
    }
    
    .modern-mobile-stat-card {
        background: white !important;
        border-radius: 24px !important;
        padding: 32px !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1) !important;
        border: 1px solid #e5e7eb !important;
        margin-bottom: 24px !important;
        transition: all 0.3s ease !important;
    }
    
    .modern-mobile-stat-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        transform: translateY(-4px) !important;
    }
    
    .modern-mobile-stat-card-title {
        font-size: 24px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin-bottom: 24px !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }
    
    .modern-mobile-stat-card-title i {
        color: #6366f1 !important;
        font-size: 28px !important;
    }
    
    /* Mobile Trend Visualization - Desktop Version */
    .modern-mobile-trend-visualization {
        min-height: 350px !important;
        padding: 24px 0 !important;
    }
    
    .modern-mobile-trend-bars {
        height: 300px !important;
        gap: 8px !important;
        padding: 24px 0 !important;
    }
    
    .modern-mobile-trend-bar {
        max-width: 50px !important;
        border-radius: 12px 12px 0 0 !important;
    }
    
    .modern-mobile-trend-value {
        font-size: 12px !important;
        top: -28px !important;
    }
    
    .modern-mobile-trend-label {
        font-size: 12px !important;
        margin-top: 8px !important;
    }
    
    /* Mobile Status Breakdown - Desktop Version */
    .modern-mobile-status-breakdown {
        padding: 16px 0 !important;
    }
    
    .modern-mobile-status-item {
        margin-bottom: 24px !important;
    }
    
    .modern-mobile-status-row {
        margin-bottom: 8px !important;
    }
    
    .modern-mobile-status-label {
        font-size: 16px !important;
        font-weight: 600 !important;
        gap: 8px !important;
    }
    
    .modern-mobile-status-label i {
        font-size: 12px !important;
    }
    
    .modern-mobile-status-value {
        font-size: 20px !important;
        font-weight: 700 !important;
    }
    
    .modern-mobile-status-bar {
        height: 12px !important;
    }
    
    /* Hide Mobile Nav and FAB on Desktop */
    .modern-mobile-nav,
    .modern-mobile-fab {
        display: none !important;
    }
}

/* ============================================
   TABLET OPTIMIZATIONS (769px-1024px)
   ============================================ */
@media (min-width: 769px) and (max-width: 1024px) {
    .modern-kpi-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px !important;
    }
    
    .modern-kpi-card:hover {
        transform: translateY(-6px) !important;
    }
    
    .modern-dashboard-header {
        padding: 24px !important;
    }
    
    .modern-dashboard-header h1 {
        font-size: 28px !important;
    }
    
    .modern-dashboard-header p {
        font-size: 16px !important;
    }
    
    .modern-stat-container {
        padding: 20px !important;
    }
    
    .modern-trend-bars {
        gap: 6px !important;
    }
    
    .modern-trend-bar {
        max-width: 35px !important;
    }
}
</style>
@endpush

@section('content')
<!-- Desktop Modern Dashboard -->
<div class="container-fluid d-none d-md-block">
    <!-- Modern Welcome Header -->
    <div class="modern-dashboard-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1>Welcome back, {{ auth()->user()->username }}! 👋</h1>
                <p>Here's your comprehensive system overview and analytics</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="modern-btn modern-btn-ghost" onclick="refreshDashboard()" style="background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.3);">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <a href="{{ route('settings.index') }}" class="modern-btn modern-btn-ghost" style="background: rgba(255,255,255,0.2); color: white; border-color: rgba(255,255,255,0.3);">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </div>
        </div>
    </div>
    
    <!-- Modern KPI Grid -->
    <div class="modern-kpi-grid">
        <div class="modern-kpi-card animate-fade-in-up">
            <div class="modern-kpi-icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="modern-kpi-label">Total Revenue</div>
            <div class="modern-kpi-value">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
        </div>
        
        <div class="modern-kpi-card success animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="modern-kpi-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="modern-kpi-label">Total Bookings</div>
            <div class="modern-kpi-value">{{ number_format($stats['total_bookings'] ?? 0) }}</div>
        </div>
        
        <div class="modern-kpi-card info animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="modern-kpi-icon">
                <i class="fas fa-store"></i>
            </div>
            <div class="modern-kpi-label">Total Booths</div>
            <div class="modern-kpi-value">{{ number_format($stats['total_booths'] ?? 0) }}</div>
        </div>
        
        <div class="modern-kpi-card warning animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="modern-kpi-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="modern-kpi-label">Total Clients</div>
            <div class="modern-kpi-value">{{ number_format($stats['total_clients'] ?? 0) }}</div>
        </div>
    </div>
    
    <!-- Modern Statistics Row - Static Design -->
    <div class="row">
        <div class="col-md-8">
            <div class="modern-stat-container">
                <div class="modern-stat-header">
                    <h3 class="modern-stat-title">
                        <i class="fas fa-chart-line"></i>
                        Booking Trends (Last 30 Days)
                    </h3>
                </div>
                <div class="modern-trend-visualization">
                    @php
                        $maxCount = max($bookingTrendCounts ?? [0]);
                        $trendData = array_combine($bookingTrendDates ?? [], $bookingTrendCounts ?? []);
                        $displayDays = array_slice($trendData, -14, 14, true); // Show last 14 days
                    @endphp
                    @if(!empty($displayDays) && $maxCount > 0)
                        <div class="modern-trend-bars">
                            @foreach($displayDays as $date => $count)
                                @php
                                    $height = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                                    $height = max($height, 5); // Minimum 5% height for visibility
                                @endphp
                                <div class="modern-trend-bar-item">
                                    <div class="modern-trend-bar-wrapper">
                                        <div class="modern-trend-bar" style="height: {{ $height }}%;" title="{{ $date }}: {{ $count }} bookings">
                                            <span class="modern-trend-bar-value">{{ $count }}</span>
                                        </div>
                                    </div>
                                    <div class="modern-trend-bar-label">{{ $date }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="modern-trend-empty">
                            <i class="fas fa-chart-line"></i>
                            <p>No booking data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="modern-stat-container">
                <div class="modern-stat-header">
                    <h3 class="modern-stat-title">
                        <i class="fas fa-chart-pie"></i>
                        Booth Status
                    </h3>
                </div>
                <div class="modern-status-breakdown">
                    @php
                        $totalBooths = ($stats['available_booths'] ?? 0) + ($stats['reserved_booths'] ?? 0) + ($stats['confirmed_booths'] ?? 0) + ($stats['paid_booths'] ?? 0);
                        $totalBooths = max($totalBooths, 1); // Prevent division by zero
                    @endphp
                    <div class="modern-status-item">
                        <div class="modern-status-header">
                            <span class="modern-status-label">
                                <i class="fas fa-circle" style="color: #10b981;"></i>
                                Available
                            </span>
                            <span class="modern-status-value">{{ $stats['available_booths'] ?? 0 }}</span>
                        </div>
                        <div class="modern-status-bar">
                            <div class="modern-status-bar-fill" style="width: {{ (($stats['available_booths'] ?? 0) / $totalBooths) * 100 }}%; background: #10b981;"></div>
                        </div>
                    </div>
                    <div class="modern-status-item">
                        <div class="modern-status-header">
                            <span class="modern-status-label">
                                <i class="fas fa-circle" style="color: #f59e0b;"></i>
                                Reserved
                            </span>
                            <span class="modern-status-value">{{ $stats['reserved_booths'] ?? 0 }}</span>
                        </div>
                        <div class="modern-status-bar">
                            <div class="modern-status-bar-fill" style="width: {{ (($stats['reserved_booths'] ?? 0) / $totalBooths) * 100 }}%; background: #f59e0b;"></div>
                        </div>
                    </div>
                    <div class="modern-status-item">
                        <div class="modern-status-header">
                            <span class="modern-status-label">
                                <i class="fas fa-circle" style="color: #3b82f6;"></i>
                                Confirmed
                            </span>
                            <span class="modern-status-value">{{ $stats['confirmed_booths'] ?? 0 }}</span>
                        </div>
                        <div class="modern-status-bar">
                            <div class="modern-status-bar-fill" style="width: {{ (($stats['confirmed_booths'] ?? 0) / $totalBooths) * 100 }}%; background: #3b82f6;"></div>
                        </div>
                    </div>
                    <div class="modern-status-item">
                        <div class="modern-status-header">
                            <span class="modern-status-label">
                                <i class="fas fa-circle" style="color: #6366f1;"></i>
                                Paid
                            </span>
                            <span class="modern-status-value">{{ $stats['paid_booths'] ?? 0 }}</span>
                        </div>
                        <div class="modern-status-bar">
                            <div class="modern-status-bar-fill" style="width: {{ (($stats['paid_booths'] ?? 0) / $totalBooths) * 100 }}%; background: #6366f1;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================
   MOBILE APP VIEW - MODERN REDESIGN
   ============================================ -->
<div class="d-md-none">
    <!-- Modern Mobile Header -->
    <div class="modern-mobile-header">
        <div class="modern-mobile-header-content">
            <div>
                <div class="modern-mobile-greeting">Welcome back</div>
                <h1 class="modern-mobile-title">{{ auth()->user()->username }}</h1>
            </div>
            <div class="modern-mobile-actions">
                <button type="button" class="modern-mobile-action-btn" onclick="refreshDashboard()" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <a href="{{ route('settings.index') }}" class="modern-mobile-action-btn" title="Settings">
                    <i class="fas fa-cog"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Modern Mobile Stats Grid -->
    <div class="modern-mobile-stats">
        <div class="modern-mobile-stats-grid">
            <div class="modern-mobile-stat">
                <div class="modern-mobile-stat-label">Total Booths</div>
                <div class="modern-mobile-stat-value">{{ $stats['total_booths'] ?? 0 }}</div>
                <div class="modern-mobile-stat-icon">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            
            <div class="modern-mobile-stat success">
                <div class="modern-mobile-stat-label">Total Bookings</div>
                <div class="modern-mobile-stat-value">{{ $stats['total_bookings'] ?? 0 }}</div>
                <div class="modern-mobile-stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            
            <div class="modern-mobile-stat info">
                <div class="modern-mobile-stat-label">Total Clients</div>
                <div class="modern-mobile-stat-value">{{ $stats['total_clients'] ?? 0 }}</div>
                <div class="modern-mobile-stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            
            <div class="modern-mobile-stat warning">
                <div class="modern-mobile-stat-label">Total Revenue</div>
                <div class="modern-mobile-stat-value">${{ number_format(($stats['total_revenue'] ?? 0) / 1000, 1) }}K</div>
                <div class="modern-mobile-stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modern Mobile Statistics - Static Design -->
    <div class="modern-mobile-stat-section">
        <div class="modern-mobile-stat-card">
            <div class="modern-mobile-stat-card-title">
                <i class="fas fa-chart-line"></i>
                Booking Trends (Last 14 Days)
            </div>
            <div class="modern-mobile-trend-visualization">
                @php
                    $maxCount = max($bookingTrendCounts ?? [0]);
                    $trendData = array_combine($bookingTrendDates ?? [], $bookingTrendCounts ?? []);
                    $displayDays = array_slice($trendData, -14, 14, true);
                @endphp
                @if(!empty($displayDays) && $maxCount > 0)
                    <div class="modern-mobile-trend-bars">
                        @foreach($displayDays as $date => $count)
                            @php
                                $height = $maxCount > 0 ? ($count / $maxCount) * 100 : 0;
                                $height = max($height, 8);
                            @endphp
                            <div class="modern-mobile-trend-item">
                                <div class="modern-mobile-trend-bar-wrapper">
                                    <div class="modern-mobile-trend-bar" style="height: {{ $height }}%;" title="{{ $date }}: {{ $count }}">
                                        <span class="modern-mobile-trend-value">{{ $count }}</span>
                                    </div>
                                </div>
                                <div class="modern-mobile-trend-label">{{ substr($date, 0, 3) }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="modern-mobile-trend-empty">
                        <i class="fas fa-chart-line"></i>
                        <p>No data available</p>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="modern-mobile-stat-card">
            <div class="modern-mobile-stat-card-title">
                <i class="fas fa-chart-pie"></i>
                Booth Status
            </div>
            <div class="modern-mobile-status-breakdown">
                @php
                    $totalBooths = ($stats['available_booths'] ?? 0) + ($stats['reserved_booths'] ?? 0) + ($stats['confirmed_booths'] ?? 0) + ($stats['paid_booths'] ?? 0);
                    $totalBooths = max($totalBooths, 1);
                @endphp
                <div class="modern-mobile-status-item">
                    <div class="modern-mobile-status-row">
                        <span class="modern-mobile-status-label">
                            <i class="fas fa-circle" style="color: #10b981; font-size: 10px;"></i>
                            Available
                        </span>
                        <span class="modern-mobile-status-value">{{ $stats['available_booths'] ?? 0 }}</span>
                    </div>
                    <div class="modern-mobile-status-bar">
                        <div class="modern-mobile-status-fill" style="width: {{ (($stats['available_booths'] ?? 0) / $totalBooths) * 100 }}%; background: #10b981;"></div>
                    </div>
                </div>
                <div class="modern-mobile-status-item">
                    <div class="modern-mobile-status-row">
                        <span class="modern-mobile-status-label">
                            <i class="fas fa-circle" style="color: #f59e0b; font-size: 10px;"></i>
                            Reserved
                        </span>
                        <span class="modern-mobile-status-value">{{ $stats['reserved_booths'] ?? 0 }}</span>
                    </div>
                    <div class="modern-mobile-status-bar">
                        <div class="modern-mobile-status-fill" style="width: {{ (($stats['reserved_booths'] ?? 0) / $totalBooths) * 100 }}%; background: #f59e0b;"></div>
                    </div>
                </div>
                <div class="modern-mobile-status-item">
                    <div class="modern-mobile-status-row">
                        <span class="modern-mobile-status-label">
                            <i class="fas fa-circle" style="color: #3b82f6; font-size: 10px;"></i>
                            Confirmed
                        </span>
                        <span class="modern-mobile-status-value">{{ $stats['confirmed_booths'] ?? 0 }}</span>
                    </div>
                    <div class="modern-mobile-status-bar">
                        <div class="modern-mobile-status-fill" style="width: {{ (($stats['confirmed_booths'] ?? 0) / $totalBooths) * 100 }}%; background: #3b82f6;"></div>
                    </div>
                </div>
                <div class="modern-mobile-status-item">
                    <div class="modern-mobile-status-row">
                        <span class="modern-mobile-status-label">
                            <i class="fas fa-circle" style="color: #6366f1; font-size: 10px;"></i>
                            Paid
                        </span>
                        <span class="modern-mobile-status-value">{{ $stats['paid_booths'] ?? 0 }}</span>
                    </div>
                    <div class="modern-mobile-status-bar">
                        <div class="modern-mobile-status-fill" style="width: {{ (($stats['paid_booths'] ?? 0) / $totalBooths) * 100 }}%; background: #6366f1;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modern Mobile Bottom Navigation -->
    <div class="modern-mobile-nav">
        <a href="{{ route('dashboard') }}" class="modern-mobile-nav-item active">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('booths.index') }}" class="modern-mobile-nav-item">
            <i class="fas fa-store"></i>
            <span>Booths</span>
        </a>
        <a href="{{ route('books.index') }}" class="modern-mobile-nav-item">
            <i class="fas fa-calendar-check"></i>
            <span>Bookings</span>
        </a>
        <a href="{{ route('clients.index') }}" class="modern-mobile-nav-item">
            <i class="fas fa-users"></i>
            <span>Clients</span>
        </a>
        <a href="{{ route('settings.index') }}" class="modern-mobile-nav-item">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
    
    <!-- Modern Mobile FAB -->
    <button class="modern-mobile-fab" onclick="window.location.href='{{ route('books.create') }}'" title="New Booking">
        <i class="fas fa-plus"></i>
    </button>
</div>
@endsection

@push('scripts')
<script>
// Refresh Dashboard Function
function refreshDashboard() {
    window.location.reload();
}

// No Chart.js needed - all visualizations are static CSS/HTML
// This ensures zero animations, zero loops, and maximum performance
</script>
@endpush
