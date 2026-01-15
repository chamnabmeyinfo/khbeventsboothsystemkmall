<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $companySettings = \App\Models\Setting::getCompanySettings();
        $appearanceSettings = \App\Models\Setting::getAppearanceSettings();
        try {
            $cdnSettings = \App\Models\Setting::getCDNSettings();
            $useCDN = $cdnSettings['use_cdn'] ?? true; // Default to true (CDN enabled)
        } catch (\Exception $e) {
            // If settings table doesn't exist or error occurs, default to CDN enabled
            $useCDN = true;
        }
    @endphp
    <title>@yield('title', $companySettings['company_name'] ?? 'KHB Booth System')</title>
    @if(!empty($companySettings['company_favicon']))
        <link rel="icon" type="image/x-icon" href="{{ asset($companySettings['company_favicon']) }}">
    @endif
    
    {{-- Performance Optimizations: Resource Hints --}}
    @if($useCDN)
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://code.ionicframework.com">
    <link rel="dns-prefetch" href="https://cdn.datatables.net">
    @else
    <link rel="preconnect" href="{{ url('/') }}">
    <link rel="dns-prefetch" href="{{ url('/') }}">
    @endif
    
    @if($useCDN)
    {{-- CDN CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    @else
    {{-- Local CSS: Preload essential stylesheets --}}
    <link rel="preload" href="{{ asset('vendor/adminlte/css/adminlte.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ asset('vendor/fontawesome/css/all.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    </noscript>
    
    {{-- Non-Critical CSS: Load asynchronously --}}
    <link rel="preload" href="{{ asset('vendor/ionicons/css/ionicons.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ asset('vendor/sweetalert2/css/sweetalert2.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ asset('vendor/toastr/css/toastr.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ asset('vendor/select2/css/select2.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ asset('vendor/select2/css/select2-bootstrap4.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('vendor/ionicons/css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/datatables/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/css/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/toastr/css/toastr.min.css') }}">
        <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('vendor/select2/css/select2-bootstrap4.min.css') }}" rel="stylesheet" />
    </noscript>
    
    {{-- Device-Optimized Performance CSS --}}
    <link rel="stylesheet" href="{{ asset('css/device-optimized.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tablet-optimized.css') }}">
    <link rel="stylesheet" href="{{ asset('css/desktop-optimized.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modern-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modern-sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global-ux-consistency.css') }}">
    @endif
    
    {{-- Async CSS Loader Script --}}
    <script>
        !function(e){"use strict";var t=function(t,n,o){var i,r=e.document,a=r.createElement("link");if(n)i=n;else{var l=(r.body||r.getElementsByTagName("head")[0]).childNodes;i=l[l.length-1]}var d=r.styleSheets;a.rel="stylesheet",a.href=t,a.media="only x",function e(t){if(r.body)return t();setTimeout(function(){e(t)})}(function(){i.parentNode.insertBefore(a,n?i:i.nextSibling)});var f=function(e){for(var t=a.href,n=d.length;n--;)if(d[n].href===t)return e();setTimeout(function(){f(e)})};return a.addEventListener&&a.addEventListener("load",function(){this.media=o||"all"}),a.onloadcssdefined=f,f(function(){a.media!==o&&(a.media=o||"all")}),a};"undefined"!=typeof exports?exports.loadCSS=t:e.loadCSS=t}("undefined"!=typeof global?global:this);
    </script>
    
    <style>
        /* Modern UX/UI Enhancements */
        :root {
            --primary-color: {{ $appearanceSettings['primary_color'] ?? '#4e73df' }};
            --secondary-color: {{ $appearanceSettings['secondary_color'] ?? '#667eea' }};
            --success-color: {{ $appearanceSettings['success_color'] ?? '#1cc88a' }};
            --info-color: {{ $appearanceSettings['info_color'] ?? '#36b9cc' }};
            --warning-color: {{ $appearanceSettings['warning_color'] ?? '#f6c23e' }};
            --danger-color: {{ $appearanceSettings['danger_color'] ?? '#e74a3b' }};
            --sidebar-bg: {{ $appearanceSettings['sidebar_bg'] ?? '#224abe' }};
            --navbar-bg: {{ $appearanceSettings['navbar_bg'] ?? '#ffffff' }};
            --footer-bg: {{ $appearanceSettings['footer_bg'] ?? '#f8f9fc' }};
            --sidebar-width: 250px;
        }
        
        /* Mobile Navigation & Responsive Styles */
        @media (max-width: 768px) {
            /* Body adjustments */
            body {
                font-size: 14px;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
                padding-bottom: 80px !important;
            }
            
            /* Hide main header completely on mobile for app-style design */
            .main-header {
                display: none !important;
                visibility: hidden !important;
                height: 0 !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            /* Content wrapper - No margin/padding for full mobile app */
            .content-wrapper {
                margin: 0 !important;
                margin-top: 0 !important;
                padding: 0 !important;
                background: transparent !important;
            }
            
            /* Hide sidebar completely on mobile */
            .main-sidebar {
                display: none !important;
                visibility: hidden !important;
                width: 0 !important;
                height: 0 !important;
                position: absolute !important;
                left: -9999px !important;
            }
            
            body.sidebar-open .main-sidebar {
                left: 0 !important;
            }
            
            /* Ensure sidebar content is visible */
            .main-sidebar .sidebar {
                padding-bottom: 60px;
                overflow-y: auto;
                height: 100%;
                background: var(--sidebar-bg) !important;
            }
            
            /* Make sure all sidebar elements are visible */
            .main-sidebar,
            .main-sidebar .brand-link,
            .main-sidebar .user-panel,
            .main-sidebar .nav,
            .main-sidebar .nav-link {
                visibility: visible !important;
                opacity: 1 !important;
                display: block !important;
            }
            
            /* Brand link on mobile */
            .main-sidebar .brand-link {
                padding: 12px 15px !important;
                font-size: 1.1rem !important;
            }
            
            /* User panel on mobile */
            .main-sidebar .user-panel {
                padding: 12px 15px !important;
            }
            
            /* Navigation on mobile */
            .main-sidebar .nav-sidebar {
                display: block !important;
            }
            
            .main-sidebar .nav-sidebar > .nav-item {
                display: block !important;
            }
            
            .main-sidebar .nav-sidebar > .nav-item > .nav-link {
                display: flex !important;
                align-items: center;
            }
            
            /* Overlay when sidebar open */
            .sidebar-overlay {
                position: fixed;
                top: 56px;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.6);
                z-index: 1040 !important; /* Below sidebar but above content */
                display: none;
                backdrop-filter: blur(2px);
            }
            
            body.sidebar-open .sidebar-overlay {
                display: block !important;
            }
            
            /* Main header z-index */
            .main-header {
                z-index: 1060 !important; /* Highest - always on top */
            }
            
            /* Content wrapper margin */
            .content-wrapper {
                margin-left: 0 !important; /* No margin on mobile */
                margin-top: 0 !important;
                padding: 0 !important;
            }
            
            /* Hide content header on mobile */
            .content-header {
                display: none !important;
            }
            
            /* Ensure content area is full width */
            .content {
                padding: 0 !important;
                margin: 0 !important;
            }
            
            /* Hamburger menu icon - Make it obvious */
            .navbar-nav .nav-link[data-widget="pushmenu"] {
                font-size: 1.5rem !important;
                padding: 8px 15px !important;
                color: #333 !important;
            }
            
            .navbar-nav .nav-link[data-widget="pushmenu"]:active {
                background: rgba(102, 126, 234, 0.1) !important;
                border-radius: 8px;
            }
            
            /* Add pulse animation to menu icon on first load */
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            
            .first-visit .navbar-nav .nav-link[data-widget="pushmenu"] i {
                animation: pulse 1s ease-in-out 3;
                color: #667eea !important;
            }
            
            /* Navbar - Touch friendly */
            .navbar-nav .nav-link {
                min-height: 44px;
                padding: 12px 15px !important;
            }
            
            /* Sidebar menu - Touch friendly */
            .nav-sidebar > .nav-item > .nav-link {
                padding: 12px 15px !important;
                font-size: 15px;
            }
            
            .nav-sidebar .nav-icon {
                font-size: 18px;
                margin-right: 10px;
            }
            
            /* Dropdowns - Full width */
            .dropdown-menu {
                position: fixed !important;
                left: 10px !important;
                right: 10px !important;
                width: calc(100% - 20px) !important;
                max-width: none !important;
            }
            
            /* Cards */
            .card {
                margin-bottom: 15px;
                border-radius: 8px;
            }
            
            .card-header {
                padding: 10px 15px;
            }
            
            .card-body {
                padding: 15px;
            }
            
            .card-title {
                font-size: 1.1rem;
            }
            
            /* Buttons - Touch friendly */
            .btn {
                min-height: 44px;
                padding: 10px 16px;
                font-size: 14px;
            }
            
            /* Modern Mobile Bottom Nav - Always Sticky at Bottom - Mobile (≤768px) */
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
                /* Ensure it stays at bottom even on scroll */
                position: -webkit-sticky !important;
                position: sticky !important;
            }
            
            /* Ensure body has padding at bottom for nav */
            body {
                padding-bottom: 80px !important;
            }
            
            /* Ensure content doesn't overlap nav */
            .content-wrapper,
            .content,
            main {
                padding-bottom: 100px !important;
            }
            
            .modern-mobile-nav-item {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 4px !important;
                padding: 8px 12px !important;
                border-radius: 12px !important;
                color: #6b7280 !important;
                text-decoration: none !important;
                transition: all 0.2s ease !important;
                min-width: 60px !important;
                will-change: transform !important;
                transform: translateZ(0) !important;
            }
            
            .modern-mobile-nav-item i {
                font-size: 22px !important;
            }
            
            .modern-mobile-nav-item span {
                font-size: 11px !important;
                font-weight: 600 !important;
            }
            
            .modern-mobile-nav-item.active {
                color: #6366f1 !important;
                background: rgba(99, 102, 241, 0.1) !important;
            }
            
            .modern-mobile-nav-item:active {
                transform: scale(0.9) translateZ(0) !important;
            }
            
            /* Modern Mobile FAB */
            .modern-mobile-fab {
                position: fixed !important;
                bottom: 100px !important;
                right: 20px !important;
                width: 64px !important;
                height: 64px !important;
                border-radius: 50% !important;
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
                color: white !important;
                box-shadow: 0 12px 32px rgba(99, 102, 241, 0.5) !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 28px !important;
                border: none !important;
                cursor: pointer !important;
                z-index: 1040 !important;
                transition: all 0.3s ease !important;
                will-change: transform, box-shadow !important;
                transform: translateZ(0) !important;
            }
            
            .modern-mobile-fab:active {
                transform: scale(0.9) translateZ(0) !important;
                box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4) !important;
            }
            
            .btn-sm {
                min-height: 38px;
                padding: 8px 12px;
                font-size: 13px;
            }
            
            .btn-xs {
                min-height: 32px;
                padding: 6px 10px;
                font-size: 12px;
            }
            
            /* Form controls */
            .form-control, 
            .form-select,
            select {
                font-size: 16px !important; /* Prevent zoom on iOS */
                padding: 10px 12px;
                min-height: 44px;
            }
            
            .form-control-sm {
                font-size: 14px !important;
                min-height: 38px;
            }
            
            .form-label {
                font-size: 14px;
                font-weight: 600;
                margin-bottom: 6px;
            }
            
            /* Tables - Horizontal scroll */
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: -15px;
                padding: 15px;
            }
            
            .table {
                min-width: 600px;
                font-size: 13px;
            }
            
            .table th,
            .table td {
                padding: 8px;
                white-space: nowrap;
            }
            
            /* Pagination */
            .pagination {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .page-link {
                padding: 8px 12px;
                min-width: 40px;
            }
            
            /* Breadcrumb */
            .breadcrumb {
                font-size: 13px;
                padding: 8px 15px;
                flex-wrap: wrap;
            }
            
            /* Footer */
            .main-footer {
                padding: 10px 15px;
                font-size: 12px;
            }
            
            /* Modals - Full screen */
            .modal {
                padding: 0 !important;
            }
            
            .modal-dialog {
                margin: 0;
                max-width: 100%;
                height: 100vh;
            }
            
            .modal-content {
                height: 100vh;
                border-radius: 0;
                border: none;
            }
            
            .modal-header {
                padding: 12px 15px;
            }
            
            .modal-body {
                padding: 15px;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .modal-footer {
                padding: 10px 15px;
                flex-wrap: wrap;
            }
            
            .modal-footer .btn {
                margin: 5px;
                flex: 1;
                min-width: calc(50% - 10px);
            }
            
            /* Hide desktop-only elements */
            .d-none-mobile {
                display: none !important;
            }
            
            /* Show mobile-only elements */
            .d-block-mobile {
                display: block !important;
            }
            
            /* Reduce white space */
            .row {
                margin-left: -10px;
                margin-right: -10px;
            }
            
            .row > [class*='col-'] {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            /* Content header */
            .content-header {
                padding: 10px 15px !important;
            }
            
            .content-header h1 {
                font-size: 1.5rem !important;
            }
        }
        
        /* Tablet specific */
        /* Tablet - Keep Mobile Nav Sticky (769px-1024px) */
        @media (min-width: 769px) and (max-width: 1024px) {
            /* Modern Mobile Bottom Nav - Always Sticky at Bottom - Tablet */
            .modern-mobile-nav {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                background: rgba(255, 255, 255, 0.98) !important;
                backdrop-filter: blur(20px) !important;
                -webkit-backdrop-filter: blur(20px) !important;
                border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
                padding: 14px 20px !important;
                padding-bottom: calc(14px + env(safe-area-inset-bottom)) !important;
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
            
            /* Ensure body has padding at bottom for nav on tablet */
            body {
                padding-bottom: 85px !important;
            }
            
            /* Ensure content doesn't overlap nav on tablet */
            .content-wrapper,
            .content,
            main {
                padding-bottom: 105px !important;
            }
            
            .modern-mobile-nav-item {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 6px !important;
                padding: 10px 16px !important;
                border-radius: 12px !important;
                color: #6b7280 !important;
                text-decoration: none !important;
                transition: all 0.2s ease !important;
                min-width: 70px !important;
                will-change: transform !important;
                transform: translateZ(0) !important;
            }
            
            .modern-mobile-nav-item i {
                font-size: 24px !important;
            }
            
            .modern-mobile-nav-item span {
                font-size: 12px !important;
                font-weight: 600 !important;
            }
            
            .modern-mobile-nav-item.active {
                color: #6366f1 !important;
                background: rgba(99, 102, 241, 0.1) !important;
            }
            
            .modern-mobile-nav-item:active {
                transform: scale(0.95) translateZ(0) !important;
            }
            
            /* Modern Mobile FAB - Tablet */
            .modern-mobile-fab {
                position: fixed !important;
                bottom: 110px !important;
                right: 24px !important;
                width: 68px !important;
                height: 68px !important;
                border-radius: 50% !important;
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
                color: white !important;
                box-shadow: 0 12px 32px rgba(99, 102, 241, 0.5) !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 30px !important;
                border: none !important;
                cursor: pointer !important;
                z-index: 1040 !important;
                transition: all 0.3s ease !important;
                will-change: transform, box-shadow !important;
                transform: translateZ(0) !important;
            }
            
            .modern-mobile-fab:active {
                transform: scale(0.9) translateZ(0) !important;
                box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4) !important;
            }
            
            /* Tablet sidebar adjustments */
            .main-sidebar {
                width: 200px;
            }
            
            .content-wrapper {
                margin-left: 200px;
            }
            
            .nav-sidebar > .nav-item > .nav-link {
                padding: 10px 12px;
                font-size: 14px;
            }
        }
        
        /* Desktop - Hide Mobile Navigation (≥1025px) */
        @media (min-width: 1025px) {
            .modern-mobile-nav,
            .modern-mobile-fab {
                display: none !important;
            }
            
            /* Remove padding on desktop */
            body {
                padding-bottom: 0 !important;
            }
            
            .content-wrapper,
            .content,
            main {
                padding-bottom: 0 !important;
            }
        }
        
        /* Touch device improvements */
        @media (hover: none) and (pointer: coarse) {
            /* Remove all hover effects */
            .btn:hover,
            .nav-link:hover,
            .nav-item:hover {
                transform: none;
                background-color: inherit;
            }
            
            /* Active states instead */
            .btn:active,
            .nav-link:active {
                opacity: 0.7;
                transform: scale(0.98);
            }
            
            /* Scrollbars */
            * {
                -webkit-overflow-scrolling: touch;
            }
        }
        
        /* Landscape phone */
        @media (max-width: 768px) and (orientation: landscape) {
            .main-header {
                height: 50px;
            }
            
            .main-sidebar {
                top: 50px;
                height: calc(100vh - 50px);
            }
            
            .content-wrapper {
                margin-top: 50px !important;
            }
        }
        
        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        
        .loading-overlay.active {
            display: flex;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Navbar - Clean Modern Design */
        .main-header {
            background: var(--navbar-bg);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            padding: 0.75rem 1.5rem;
        }
        
        .navbar-nav .nav-link {
            color: #4a5568;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            margin: 0 0.25rem;
        }
        
        .navbar-nav .nav-link:hover {
            background: #f8f9fc;
            color: #667eea;
        }
        
        .navbar-nav .nav-link i {
            margin-right: 0.5rem;
        }
        
        /* Search Form */
        .form-inline {
            position: relative;
        }
        
        .form-control-navbar {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }
        
        .form-control-navbar:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn-navbar {
            border: 1px solid #e2e8f0;
            border-left: none;
            border-radius: 0 8px 8px 0;
            color: #4a5568;
            transition: all 0.2s ease;
        }
        
        .btn-navbar:hover {
            background: #f8f9fc;
            color: #667eea;
        }
        
        /* Card Enhancements */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }
        
        /* Button Enhancements */
        .btn {
            transition: all 0.2s ease;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Form Enhancements */
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        /* ============================================
           ULTRA MODERN SIDEBAR DESIGN 2026 - CLEAN & CLEAR
           Minimalist Design with Perfect Spacing
           ============================================ */
        
        /* Sidebar Container - Clean Modern Design */
        .main-sidebar {
            background: linear-gradient(180deg, 
                var(--sidebar-bg) 0%, 
                color-mix(in srgb, var(--sidebar-bg) 90%, black) 50%,
                color-mix(in srgb, var(--sidebar-bg) 80%, black) 100%);
            box-shadow: 
                2px 0 24px rgba(0, 0, 0, 0.15),
                inset -1px 0 0 rgba(255, 255, 255, 0.03);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            position: relative;
            overflow: hidden;
        }
        
        .main-sidebar > * {
            position: relative;
            z-index: 1;
        }
        
        /* Brand Logo - Clean & Minimal */
        .brand-link {
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding: 1.25rem 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        
        .brand-link:hover {
            background: rgba(255, 255, 255, 0.04);
            text-decoration: none;
        }
        
        .brand-link > .d-flex {
            width: 100%;
            align-items: center;
            gap: 0.75rem;
        }
        
        .brand-image-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            /* Square container - maintains square shape */
            aspect-ratio: 1 / 1;
            min-width: 50px;
            min-height: 50px;
        }
        
        .brand-image {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px !important;
            height: 50px !important;
            min-width: 50px !important;
            min-height: 50px !important;
            border-radius: 50% !important;
            overflow: hidden;
            /* Perfect circle inside square wrapper */
            aspect-ratio: 1 / 1;
        }
        
        .brand-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: block;
        }
        
        .brand-link:hover .brand-image {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        
        .brand-text-wrapper {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .brand-text {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            color: #ffffff;
            line-height: 1.3;
        }
        
        /* User Panel - Clean Card */
        .user-panel {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 12px;
            padding: 1rem;
            margin: 1rem 1rem 1.25rem 1rem;
            transition: all 0.3s ease;
        }
        
        .user-panel:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(102, 126, 234, 0.2);
        }
        
        .user-panel .image {
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-panel .image .avatar-wrapper {
            width: 50px !important;
            height: 50px !important;
        }
        
        .user-panel .image .avatar-image,
        .user-panel .image .avatar-placeholder {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3) !important;
            transition: all 0.3s ease;
        }
        
        .user-panel:hover .image .avatar-wrapper {
            transform: scale(1.05);
        }
        
        .user-panel:hover .image .avatar-image,
        .user-panel:hover .image .avatar-placeholder {
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4) !important;
        }
        
        .user-panel .info a {
            font-weight: 600;
            font-size: 0.95rem;
            color: #ffffff;
            text-decoration: none;
        }
        
        .user-panel .info small {
            color: rgba(255, 255, 255, 0.65) !important;
            font-weight: 400;
            font-size: 0.8rem;
        }
        
        /* Navigation Links - Clean & Clear */
        .nav-link {
            transition: all 0.25s ease;
            border-radius: 10px;
            margin: 0.25rem 0.75rem;
            padding: 0.75rem 1rem;
            position: relative;
            background: transparent;
            border: none;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 0;
            width: 3px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            border-radius: 0 3px 3px 0;
            transition: height 0.25s ease;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.04);
            transform: translateX(4px);
        }
        
        .nav-link:hover::before {
            height: 60%;
        }
        
        .nav-link.active {
            background: rgba(102, 126, 234, 0.15);
            color: #ffffff;
            font-weight: 600;
        }
        
        .nav-link.active::before {
            height: 70%;
            box-shadow: 0 0 8px rgba(102, 126, 234, 0.5);
        }
        
        /* Navigation Icons */
        .nav-icon {
            width: 20px;
            text-align: center;
            margin-right: 0.75rem;
            font-size: 1rem;
            transition: all 0.25s ease;
            display: inline-block;
        }
        
        .nav-link:hover .nav-icon {
            transform: scale(1.1);
            color: #667eea;
        }
        
        .nav-link.active .nav-icon {
            color: #ffffff;
        }
        
        /* Section Headers - Clean Design */
        .nav-header {
            color: rgba(255, 255, 255, 0.5) !important;
            font-size: 0.65rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px;
            padding: 1rem 1.5rem 0.5rem 1.5rem !important;
            margin-top: 0.5rem;
            position: relative;
        }
        
        .nav-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 1.5rem;
            right: 1.5rem;
            height: 1px;
            background: linear-gradient(90deg, 
                transparent 0%,
                rgba(255, 255, 255, 0.1) 50%,
                transparent 100%);
        }
        
        .nav-header i {
            margin-right: 0.5rem;
            font-size: 0.75rem;
            color: rgba(102, 126, 234, 0.6);
        }
        
        /* Treeview */
        .nav-treeview {
            padding-left: 0.5rem;
            margin-top: 0.25rem;
        }
        
        .nav-treeview .nav-link {
            padding-left: 2.5rem;
            font-size: 0.875rem;
            margin: 0.2rem 0.75rem;
        }
        
        .has-treeview > .nav-link {
            font-weight: 500;
        }
        
        .has-treeview > .nav-link .right {
            transition: transform 0.3s ease;
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
        }
        
        .has-treeview.menu-open > .nav-link .right {
            transform: rotate(-90deg);
            color: #667eea;
        }
        
        /* Badge */
        .nav-link .badge {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            box-shadow: 0 2px 8px rgba(245, 87, 108, 0.3);
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(102, 126, 234, 0.5);
        }
        
        /* Spacing & Typography */
        .sidebar .nav {
            padding: 0.5rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.15rem;
        }
        
        .nav-link p {
            transition: all 0.25s ease;
            margin: 0;
            font-size: 0.9rem;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.85);
        }
        
        .nav-link.active p {
            color: #ffffff;
            font-weight: 600;
        }
        
        .nav-link:hover p {
            color: #ffffff;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-link {
                margin: 0.2rem 0.5rem;
                padding: 0.65rem 0.85rem;
            }
            
            .nav-header {
                padding: 0.85rem 1.25rem 0.4rem 1.25rem !important;
                font-size: 0.6rem !important;
            }
            
            .user-panel {
                margin: 0.75rem 0.5rem 1rem 0.5rem;
                padding: 0.85rem;
            }
        }
        
        /* Table Enhancements */
        .table {
            border-radius: 0.35rem;
            overflow: hidden;
        }
        
        .table thead {
            background-color: #f8f9fc;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fc;
            transform: scale(1.01);
        }
        
        /* Badge Enhancements */
        .badge {
            padding: 0.35em 0.65em;
            font-weight: 600;
        }
        
        /* Alert Enhancements */
        .alert {
            border: none;
            border-left: 4px solid;
            border-radius: 0.35rem;
        }
        
        .alert-success {
            border-left-color: var(--success-color);
        }
        
        .alert-danger {
            border-left-color: var(--danger-color);
        }
        
        .alert-warning {
            border-left-color: var(--warning-color);
        }
        
        .alert-info {
            border-left-color: var(--info-color);
        }
        
        /* Smooth Transitions */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
    
    <!-- Mobile Design System -->
    <link rel="stylesheet" href="{{ asset('css/mobile-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global-mobile-enhancements.css') }}">
    
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            @auth
            @if(auth()->user()->hasPermission('users.view') || auth()->user()->isAdmin())
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('users.index') }}" class="nav-link">User</a>
            </li>
            @endif
            {{-- Floor Plans - Accessible to all authenticated users --}}
            @auth
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('floor-plans.index') }}" class="nav-link">
                    <i class="fas fa-map mr-2"></i>Floor Plans
                </a>
            </li>
            @endauth
            @if(auth()->user()->hasPermission('clients.view') || auth()->user()->isAdmin())
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('clients.index') }}" class="nav-link">Clients</a>
            </li>
            @endif
            @if(auth()->user()->hasPermission('bookings.view') || auth()->user()->isAdmin())
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('books.index') }}" class="nav-link">Bookings</a>
            </li>
            @endif
            <li class="nav-item d-none d-sm-inline-block">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link text-dark" style="border: none; background: none; padding: 0.5rem 1rem;">
                        Logout ({{ auth()->user()->username }})
                    </button>
                </form>
            </li>
            @else
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('floor-plans.index') }}" class="nav-link">
                    <i class="fas fa-map mr-2"></i>Floor Plans
                </a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('login') }}" class="nav-link">Login</a>
            </li>
            @endauth
        </ul>

        <!-- SEARCH FORM -->
        <form class="form-inline ml-3 d-none d-md-block position-relative" id="globalSearchForm">
            <div class="input-group input-group-sm">
                <input class="form-control form-control-navbar" type="search" id="globalSearchInput" 
                       placeholder="Search booths, clients, bookings..." aria-label="Search" autocomplete="off">
                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div id="searchResults" class="dropdown-menu position-absolute" style="display: none; max-width: 400px; max-height: 400px; overflow-y: auto; top: 100%; left: 0; z-index: 1000; margin-top: 5px;"></div>
        </form>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container - Ultra Modern Design -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo - Modern Glass Card -->
        <a href="{{ route('dashboard') }}" class="brand-link">
            <div class="d-flex align-items-center w-100">
                @if(!empty($companySettings['company_logo']))
                    <div class="brand-image-wrapper">
                        <div class="brand-image elevation-3">
                            <img src="{{ asset($companySettings['company_logo']) }}" alt="{{ $companySettings['company_name'] ?? 'Logo' }}">
                        </div>
                    </div>
                @else
                    <div class="brand-image-wrapper">
                        <span class="brand-image elevation-3 d-flex align-items-center justify-content-center" 
                              style="color: white; font-weight: 800; font-size: 20px; letter-spacing: 0.5px;">
                            {{ strtoupper(substr($companySettings['company_name'] ?? 'KHB', 0, 3)) }}
                        </span>
                    </div>
                @endif
                <div class="brand-text-wrapper flex-grow-1 ms-3">
                    <div class="brand-text font-weight-bold" style="font-size: 1.15rem; line-height: 1.3; color: rgba(255, 255, 255, 0.95); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $companySettings['company_name'] ?? 'KHB Booth' }}</div>
                    <div class="brand-text font-weight-light" style="font-size: 0.7rem; line-height: 1.2; color: rgba(255, 255, 255, 0.7); letter-spacing: 0.5px; margin-top: 2px;">Management System</div>
                </div>
            </div>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- User Panel - Modern Glass Card -->
            @auth
            @php
                $user = auth()->user();
            @endphp
            <div class="user-panel d-flex align-items-center">
                <div class="image">
                    <x-avatar 
                        :avatar="$user->avatar" 
                        :name="$user->username" 
                        :size="'50px'"
                        :type="$user->isAdmin() ? 'admin' : 'user'"
                        :shape="'circle'"
                        class="elevation-2"
                    />
                </div>
                <div class="info flex-grow-1 ml-2">
                    <a href="{{ route('users.show', $user->id) }}" class="d-block text-white font-weight-bold" style="font-size: 1rem; text-decoration: none; letter-spacing: 0.3px;">
                        {{ $user->username }}
                    </a>
                    @if($user->employee)
                    <small class="d-block" style="font-size: 0.8rem; font-weight: 500;">
                        {{ $user->employee->position->name ?? ($user->employee->department->name ?? 'Employee') }}
                    </small>
                    @else
                    <small class="d-block" style="font-size: 0.8rem; font-weight: 500;">
                        {{ $user->isAdmin() ? 'Administrator' : 'User' }}
                    </small>
                    @endif
                </div>
            </div>
            @endauth

            <!-- Sidebar Menu - Redesigned -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    @auth
                    {{-- Quick Access Section --}}
                    <li class="nav-header">
                        <i class="fas fa-bolt"></i>Quick Access
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    @if(auth()->user()->employee)
                    <li class="nav-item">
                        <a href="{{ route('employee.dashboard') }}" class="nav-link {{ request()->routeIs('employee.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-circle"></i>
                            <p>My Portal</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->employee && auth()->user()->employee->directReports()->count() > 0)
                    <li class="nav-item">
                        <a href="{{ route('manager.dashboard') }}" class="nav-link {{ request()->routeIs('manager.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>Manager Dashboard</p>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-bell"></i>
                            <p>Notifications</p>
                            <span id="notification-badge" class="badge badge-warning navbar-badge" style="display: none;">0</span>
                        </a>
                    </li>
                    {{-- Core Features Section --}}
                    <li class="nav-header">
                        <i class="fas fa-cube"></i>Core Features
                    </li>
                    {{-- Floor Plans - Accessible to all authenticated users (admins, sales, etc.) --}}
                    @auth
                    <li class="nav-item">
                        <a href="{{ route('floor-plans.index') }}" class="nav-link {{ request()->routeIs('floor-plans.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-map"></i>
                            <p>Floor Plans</p>
                        </a>
                    </li>
                    @endauth
                    @if(auth()->user()->hasPermission('booths.view') || auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('booths.index') }}" class="nav-link {{ request()->routeIs('booths.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cube"></i>
                            <p>Booths</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasPermission('clients.view') || auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Clients</p>
                        </a>
                    </li>
                    @endif
                    
                    {{-- Business Operations Section --}}
                    <li class="nav-header">
                        <i class="fas fa-briefcase"></i>Business Operations
                    </li>
                    @if(auth()->user()->hasPermission('bookings.view') || auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Bookings</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('affiliates.view'))
                    <li class="nav-item">
                        <a href="{{ route('affiliates.index') }}" class="nav-link {{ request()->routeIs('affiliates.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-handshake"></i>
                            <p>Affiliates</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasPermission('reports.view') || auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reports & Analytics</p>
                        </a>
                    </li>
                    @endif
                    {{-- Finance Section --}}
                    @if(auth()->user()->hasPermission('finance.view') || auth()->user()->hasPermission('payments.view') || auth()->user()->isAdmin())
                    <li class="nav-header">
                        <i class="fas fa-dollar-sign"></i>Finance Management
                    </li>
                    <li class="nav-item has-treeview {{ request()->routeIs('finance.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>
                                Finance
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if(auth()->user()->hasPermission('payments.view') || auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('finance.payments.index') }}" class="nav-link {{ request()->routeIs('finance.payments.*') ? 'active' : '' }}">
                                    <i class="fas fa-money-bill-wave nav-icon"></i>
                                    <p>Payments</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('finance.costings.view') || auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('finance.costings.index') }}" class="nav-link {{ request()->routeIs('finance.costings.*') ? 'active' : '' }}">
                                    <i class="fas fa-calculator nav-icon"></i>
                                    <p>Costing Management</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('finance.expenses.view') || auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('finance.expenses.index') }}" class="nav-link {{ request()->routeIs('finance.expenses.*') ? 'active' : '' }}">
                                    <i class="fas fa-arrow-down nav-icon"></i>
                                    <p>Expense Management</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('finance.revenues.view') || auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('finance.revenues.index') }}" class="nav-link {{ request()->routeIs('finance.revenues.*') ? 'active' : '' }}">
                                    <i class="fas fa-arrow-up nav-icon"></i>
                                    <p>Revenue Management</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('finance.pricing.view') || auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('finance.booth-pricing.index') }}" class="nav-link {{ request()->routeIs('finance.booth-pricing.*') ? 'active' : '' }}">
                                    <i class="fas fa-dollar-sign nav-icon"></i>
                                    <p>Booth Pricing</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('finance.categories.view') || auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('finance.categories.index') }}" class="nav-link {{ request()->routeIs('finance.categories.*') ? 'active' : '' }}">
                                    <i class="fas fa-tags nav-icon"></i>
                                    <p>Categories</p>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    {{-- Human Resources Section --}}
                    <li class="nav-header">
                        <i class="fas fa-users"></i>Human Resources
                    </li>
                    @if(auth()->user()->hasPermission('hr.dashboard.view'))
                    <li class="nav-item has-treeview {{ request()->routeIs('hr.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('hr.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                HR Management
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            {{-- Level 1: Dashboard (Overview) --}}
                            <li class="nav-item">
                                <a href="{{ route('hr.dashboard') }}" class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">
                                    <i class="fas fa-tachometer-alt nav-icon"></i>
                                    <p>HR Dashboard</p>
                                </a>
                            </li>
                            
                            {{-- Level 2: Employee Management (Foundation) --}}
                            <li class="nav-header">
                                <i class="fas fa-users-cog"></i>Employee Management
                            </li>
                            @if(auth()->user()->hasPermission('hr.departments.view'))
                            <li class="nav-item">
                                <a href="{{ route('hr.departments.index') }}" class="nav-link {{ request()->routeIs('hr.departments.*') ? 'active' : '' }}">
                                    <i class="fas fa-building nav-icon"></i>
                                    <p>Departments</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('hr.positions.view'))
                            <li class="nav-item">
                                <a href="{{ route('hr.positions.index') }}" class="nav-link {{ request()->routeIs('hr.positions.*') ? 'active' : '' }}">
                                    <i class="fas fa-briefcase nav-icon"></i>
                                    <p>Positions</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('hr.employees.view'))
                            <li class="nav-item">
                                <a href="{{ route('hr.employees.index') }}" class="nav-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-tie nav-icon"></i>
                                    <p>Employees</p>
                                </a>
                            </li>
                            @endif
                            
                            {{-- Level 3: Time & Attendance (Daily Operations) --}}
                            <li class="nav-header">
                                <i class="fas fa-clock"></i>Time & Attendance
                            </li>
                            @if(auth()->user()->hasPermission('hr.attendance.view'))
                            <li class="nav-item">
                                <a href="{{ route('hr.attendance.index') }}" class="nav-link {{ request()->routeIs('hr.attendance.*') ? 'active' : '' }}">
                                    <i class="fas fa-clock nav-icon"></i>
                                    <p>Attendance</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('hr.leaves.manage'))
                            <li class="nav-item">
                                <a href="{{ route('hr.leave-types.index') }}" class="nav-link {{ request()->routeIs('hr.leave-types.*') ? 'active' : '' }}">
                                    <i class="fas fa-list nav-icon"></i>
                                    <p>Leave Types</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('hr.leaves.view'))
                            <li class="nav-item">
                                <a href="{{ route('hr.leaves.index') }}" class="nav-link {{ request()->routeIs('hr.leaves.*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-times nav-icon"></i>
                                    <p>Leave Requests</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('hr.leave-calendar.index') }}" class="nav-link {{ request()->routeIs('hr.leave-calendar.*') ? 'active' : '' }}">
                                    <i class="fas fa-calendar-alt nav-icon"></i>
                                    <p>Leave Calendar</p>
                                </a>
                            </li>
                            @endif
                            
                            {{-- Level 4: Performance & Development (Periodic) --}}
                            <li class="nav-header">
                                <i class="fas fa-chart-line"></i>Performance & Development
                            </li>
                            @if(auth()->user()->hasPermission('hr.performance.view'))
                            <li class="nav-item">
                                <a href="{{ route('hr.performance.index') }}" class="nav-link {{ request()->routeIs('hr.performance.*') ? 'active' : '' }}">
                                    <i class="fas fa-star nav-icon"></i>
                                    <p>Performance Reviews</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('hr.training.view'))
                            <li class="nav-item">
                                <a href="{{ route('hr.training.index') }}" class="nav-link {{ request()->routeIs('hr.training.*') ? 'active' : '' }}">
                                    <i class="fas fa-graduation-cap nav-icon"></i>
                                    <p>Training Records</p>
                                </a>
                            </li>
                            @endif
                            
                            {{-- Level 5: Records & Documents (Administrative) --}}
                            <li class="nav-header">
                                <i class="fas fa-folder-open"></i>Records & Documents
                            </li>
                            @if(auth()->user()->hasPermission('hr.documents.view'))
                            <li class="nav-item">
                                <a href="{{ route('hr.documents.index') }}" class="nav-link {{ request()->routeIs('hr.documents.*') ? 'active' : '' }}">
                                    <i class="fas fa-file-alt nav-icon"></i>
                                    <p>Documents</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('hr.salary.view'))
                            <li class="nav-item">
                                <a href="{{ route('hr.salary.index') }}" class="nav-link {{ request()->routeIs('hr.salary.*') ? 'active' : '' }}">
                                    <i class="fas fa-money-bill-wave nav-icon"></i>
                                    <p>Salary History</p>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    
                    {{-- Communication & Tools Section --}}
                    @if(auth()->user()->hasPermission('communications.view') || auth()->user()->hasPermission('export.data') || auth()->user()->isAdmin())
                    <li class="nav-header">
                        <i class="fas fa-tools"></i>Communication & Tools
                    </li>
                    @if(auth()->user()->hasPermission('communications.view') || auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('communications.index') }}" class="nav-link {{ request()->routeIs('communications.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>Messages</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasPermission('export.data') || auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('export.index') }}" class="nav-link {{ request()->routeIs('export.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-export"></i>
                            <p>Export/Import</p>
                        </a>
                    </li>
                    @endif
                    @endif
                    
                    {{-- System Administration Section --}}
                    @if(auth()->user()->hasPermission('system.admin') || auth()->user()->hasPermission('users.view') || auth()->user()->hasPermission('roles.view') || auth()->user()->hasPermission('permissions.view') || auth()->user()->isAdmin())
                    <li class="nav-header">
                        <i class="fas fa-cog"></i>System Administration
                    </li>
                    @if(auth()->user()->hasPermission('activity-logs.view') || auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Activity Logs</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasPermission('email-templates.view') || auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('email-templates.index') }}" class="nav-link {{ request()->routeIs('email-templates.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope-open-text"></i>
                            <p>Email Templates</p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->hasPermission('users.view') || auth()->user()->hasPermission('roles.view') || auth()->user()->hasPermission('permissions.view') || auth()->user()->isAdmin())
                    <li class="nav-item has-treeview {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('users.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                Staff Management
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @if(auth()->user()->hasPermission('users.view') || auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p>Users</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('roles.view') || auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-shield nav-icon"></i>
                                    <p>Roles</p>
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('permissions.view') || auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                    <i class="fas fa-key nav-icon"></i>
                                    <p>Permissions</p>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @endif
                    @if(auth()->user()->hasPermission('categories.view') || auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-folder"></i>
                            <p>Category & Sub</p>
                        </a>
                    </li>
                    @endif
                    @else
                    {{-- Public User Menu --}}
                    <li class="nav-header">
                        <i class="fas fa-map"></i>Public Access
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('floor-plans.index') }}" class="nav-link {{ request()->routeIs('floor-plans.index') || request()->routeIs('floor-plans.show') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-map"></i>
                            <p>Browse Floor Plans</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">
                            <i class="nav-icon fas fa-sign-in-alt"></i>
                            <p>Login</p>
                        </a>
                    </li>
                    @endauth
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Loading Overlay -->
        <div id="loadingOverlay" class="loading-overlay">
            <div class="loading-spinner"></div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="fas fa-info-circle mr-2"></i>{{ session('info') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <strong>Copyright &copy; {{ date('Y') }} <a href="https://www.khbmedia.asia/">KHB Media</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 2.0.0
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

@if($useCDN)
{{-- CDN JavaScript --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@else
{{-- Performance: Preload critical JavaScript with high priority --}}
<link rel="preload" href="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}" as="script" fetchpriority="high">
<link rel="preload" href="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}" as="script" fetchpriority="high">

{{-- Critical JavaScript: Load with defer (non-blocking) --}}
<script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}" defer></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
<script src="{{ asset('vendor/adminlte/js/adminlte.min.js') }}" defer></script>

{{-- Performance Optimizer - Load early --}}
<script src="{{ asset('js/performance-optimizer.js') }}" defer></script>

{{-- Non-Critical JavaScript: Load asynchronously (only when needed) --}}
<script>
// Lazy load non-critical scripts
(function() {
    'use strict';
    
    // Function to load script dynamically
    function loadScript(src, callback) {
        var script = document.createElement('script');
        script.src = src;
        script.async = true;
        if (callback) {
            script.onload = callback;
        }
        document.head.appendChild(script);
    }
    
    // Load scripts after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            // Load jQuery UI only if needed (check for elements that use it)
            if (document.querySelector('.ui-widget, .ui-dialog, [data-toggle="tooltip"]')) {
                loadScript('{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}');
            }
            
            // Load DataTables only if tables exist
            if (document.querySelector('table.dataTable, .data-table')) {
                loadScript('{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}', function() {
                    loadScript('{{ asset('vendor/datatables/js/dataTables.bootstrap4.min.js') }}');
                });
            }
            
            // Load SweetAlert2 (lightweight, load early)
            loadScript('{{ asset('vendor/sweetalert2/js/sweetalert2.min.js') }}');
            
            // Load Toastr (lightweight, load early)
            loadScript('{{ asset('vendor/toastr/js/toastr.min.js') }}');
            
            // Load Moment.js only if date formatting is needed
            if (document.querySelector('[data-moment], .moment, [data-date]')) {
                loadScript('{{ asset('vendor/moment/moment.min.js') }}');
            }
            
            // Load Select2 only if select elements with select2 class exist
            if (document.querySelector('select.select2, .select2')) {
                loadScript('{{ asset('vendor/select2/js/select2.min.js') }}');
            }
        });
    } else {
        // DOM already loaded, load immediately
        loadScript('{{ asset('vendor/sweetalert2/js/sweetalert2.min.js') }}');
        loadScript('{{ asset('vendor/toastr/js/toastr.min.js') }}');
    }
})();
</script>
@endif
<!-- Image Upload Handler -->
<script src="{{ asset('js/image-upload.js') }}"></script>

@stack('scripts')

<script>
// Configure Toastr
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

// Loading Overlay Functions
window.showLoading = function() {
    document.getElementById('loadingOverlay').classList.add('active');
};

window.hideLoading = function() {
    document.getElementById('loadingOverlay').classList.remove('active');
};

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow', function() {
        $(this).remove();
    });
}, 5000);

// Update notification badge with real-time alerts
function updateNotificationBadge() {
    fetch('{{ route("notifications.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            if (badge) {
                const oldCount = parseInt(badge.textContent) || 0;
                const newCount = data.count || 0;
                
                if (newCount > 0) {
                    badge.textContent = newCount;
                    badge.style.display = 'block';
                    
                    // Show alert if new notifications arrived
                    if (newCount > oldCount && oldCount > 0) {
                        showNotificationAlert(newCount - oldCount);
                    }
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error updating notification badge:', error);
        });
}

// Show real-time notification alert
function showNotificationAlert(count) {
    const message = count === 1 
        ? 'You have a new notification!' 
        : `You have ${count} new notifications!`;
    
    // Show toastr notification
    if (typeof toastr !== 'undefined') {
        toastr.info(message, 'New Notification', {
            timeOut: 5000,
            closeButton: true,
            progressBar: true,
            onclick: function() {
                window.location.href = '{{ route("notifications.index") }}';
            }
        });
    }
}

// Update badge on page load and every 15 seconds for real-time updates
updateNotificationBadge();
setInterval(updateNotificationBadge, 15000);

// Global Search
let searchTimeout;
$('#globalSearchInput').on('input', function() {
    const query = $(this).val();
    const resultsDiv = $('#searchResults');
    
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
        resultsDiv.hide();
        return;
    }
    
    searchTimeout = setTimeout(function() {
        fetch('{{ route("search") }}?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    let html = '';
                    data.results.forEach(function(result) {
                        html += '<a href="' + result.url + '" class="dropdown-item">';
                        html += '<i class="' + result.icon + ' mr-2"></i>';
                        html += '<div><strong>' + result.title + '</strong><br>';
                        html += '<small class="text-muted">' + result.description + '</small></div>';
                        html += '</a>';
                    });
                    resultsDiv.html(html).show();
                } else {
                    resultsDiv.html('<div class="dropdown-item text-muted">No results found</div>').show();
                }
            })
            .catch(error => {
                console.error('Search error:', error);
            });
    }, 300);
});

// Hide search results when clicking outside
$(document).on('click', function(e) {
    if (!$(e.target).closest('#globalSearchForm').length) {
        $('#searchResults').hide();
    }
});

// Show toast notifications from session
@if(session('success'))
    toastr.success('{{ session('success') }}', 'Success');
@endif

@if(session('error'))
    toastr.error('{{ session('error') }}', 'Error');
@endif

@if(session('warning'))
    toastr.warning('{{ session('warning') }}', 'Warning');
@endif

@if(session('info'))
    toastr.info('{{ session('info') }}', 'Information');
@endif

// Form submission loading indicator
$(document).ready(function() {
    $('form').on('submit', function() {
        showLoading();
    });
    
    // AJAX form submissions
    $(document).ajaxStart(function() {
        showLoading();
    }).ajaxStop(function() {
        hideLoading();
    });
});

// Resolve conflict in jQuery UI tooltip with Bootstrap tooltip
$.widget.bridge('uibutton', $.ui.button);

// Mobile Navigation Enhancement
(function() {
    'use strict';
    
    function initMobileMenu() {
        console.log('Initializing mobile menu...');
        
        // Create overlay element if it doesn't exist
        if (!document.querySelector('.sidebar-overlay')) {
            const overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
            
            // Close sidebar when overlay clicked
            overlay.addEventListener('click', function() {
                console.log('Overlay clicked - closing sidebar');
                document.body.classList.remove('sidebar-open');
            });
            
            console.log('Overlay created');
        }
        
        // Handle menu toggle button
        const menuToggle = document.querySelector('[data-widget="pushmenu"]');
        if (menuToggle) {
            console.log('Menu toggle found');
            
            // Remove any existing listeners
            menuToggle.removeEventListener('click', handleMenuClick);
            
            // Add new listener
            menuToggle.addEventListener('click', handleMenuClick);
        } else {
            console.warn('Menu toggle button not found!');
        }
        
        // Close sidebar when navigation link clicked on mobile
        const navLinks = document.querySelectorAll('.main-sidebar .nav-link');
        console.log('Found ' + navLinks.length + ' nav links');
        
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    setTimeout(function() {
                        document.body.classList.remove('sidebar-open');
                    }, 200);
                }
            });
        });
        
        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
                document.body.classList.remove('sidebar-open');
            }
        });
    }
    
    // Menu click handler
    function handleMenuClick(e) {
        console.log('Menu button clicked, window width:', window.innerWidth);
        
        if (window.innerWidth <= 768) {
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = document.body.classList.contains('sidebar-open');
            console.log('Sidebar is currently:', isOpen ? 'open' : 'closed');
            
            document.body.classList.toggle('sidebar-open');
            
            console.log('Sidebar toggled to:', document.body.classList.contains('sidebar-open') ? 'open' : 'closed');
        }
    }
    
    function setVh() {
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', vh + 'px');
    }
    
    // Initialize on DOM ready
    $(document).ready(function() {
        console.log('DOM Ready - Starting mobile menu initialization');
        initMobileMenu();
        setVh();
        
        if (window.innerWidth <= 768) {
            document.body.classList.add('mobile-device');
            console.log('Mobile device class added');
        }
        
        if ('ontouchstart' in window) {
            document.body.classList.add('touch-device');
            console.log('Touch device class added');
        }
        
        // Force sidebar to be visible initially on desktop
        if (window.innerWidth > 768) {
            document.body.classList.remove('sidebar-open');
        }
    });
    
    // Handle window resize
    $(window).on('resize', function() {
        setVh();
        
        // Close sidebar on resize to desktop
        if (window.innerWidth > 768) {
            document.body.classList.remove('sidebar-open');
        }
    });
})();
</script>

<!-- Modern Mobile Bottom Navigation - Global (Shows on Mobile & Tablet) -->
<div class="d-md-none d-lg-none">
    <div class="modern-mobile-nav">
        <a href="{{ route('dashboard') }}" class="modern-mobile-nav-item" data-route="dashboard">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('booths.index', ['view' => 'table']) }}" class="modern-mobile-nav-item" data-route="booths">
            <i class="fas fa-store"></i>
            <span>Booths</span>
        </a>
        <a href="{{ route('books.index') }}" class="modern-mobile-nav-item" data-route="books">
            <i class="fas fa-calendar-check"></i>
            <span>Bookings</span>
        </a>
        <a href="{{ route('clients.index') }}" class="modern-mobile-nav-item" data-route="clients">
            <i class="fas fa-users"></i>
            <span>Clients</span>
        </a>
        <a href="{{ route('settings.index') }}" class="modern-mobile-nav-item" data-route="settings">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </div>
    
    <!-- Modern Mobile FAB (Floating Action Button) -->
    @auth
    @if(auth()->user()->isAdmin() || auth()->user()->can('create', App\Models\Book::class))
    <button class="modern-mobile-fab" onclick="window.location.href='{{ route('books.create') }}'" title="New Booking">
        <i class="fas fa-plus"></i>
    </button>
    @endif
    @endauth
</div>

<script>
// Set active state for mobile navigation based on current route
(function() {
    'use strict';
    
    function setActiveNavItem() {
        const currentPath = window.location.pathname;
        const navItems = document.querySelectorAll('.modern-mobile-nav-item');
        
        navItems.forEach(function(item) {
            item.classList.remove('active');
            const href = item.getAttribute('href');
            const route = item.getAttribute('data-route');
            
            if (href && currentPath) {
                // Check if current path matches the route
                if (currentPath === href || currentPath.startsWith(href + '/')) {
                    item.classList.add('active');
                } else if (route) {
                    // Check by route name
                    if (currentPath.includes('/' + route) || currentPath === '/' + route) {
                        item.classList.add('active');
                    }
                }
            }
        });
    }
    
    // Set active on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setActiveNavItem);
    } else {
        setActiveNavItem();
    }
    
    // Update active state when navigating
    document.querySelectorAll('.modern-mobile-nav-item').forEach(function(item) {
        item.addEventListener('click', function() {
            setTimeout(setActiveNavItem, 100);
        });
    });
})();
</script>

</body>
</html>

