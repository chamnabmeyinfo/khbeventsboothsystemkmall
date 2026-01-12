<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KHB Booth System')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- AdminLTE -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Toastr for notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Select2 for better dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    
    <style>
        /* Modern UX/UI Enhancements */
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --sidebar-width: 250px;
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
        
        /* Card Enhancements */
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.25);
            transform: translateY(-2px);
        }
        
        /* Button Enhancements */
        .btn {
            transition: all 0.3s ease;
            border-radius: 0.35rem;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        /* Form Enhancements */
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        /* ============================================
           ULTRA MODERN SIDEBAR DESIGN 2026
           Glassmorphism + Vibrant Gradients + Smooth Animations
           ============================================ */
        
        /* Sidebar Container - Glassmorphism Effect */
        .main-sidebar {
            background: linear-gradient(135deg, 
                rgba(30, 30, 46, 0.95) 0%, 
                rgba(20, 20, 35, 0.98) 50%,
                rgba(15, 15, 30, 1) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 
                4px 0 20px rgba(0, 0, 0, 0.3),
                inset -1px 0 0 rgba(255, 255, 255, 0.05);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
            overflow: hidden;
        }
        
        .main-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            background: linear-gradient(180deg, 
                rgba(102, 126, 234, 0.15) 0%,
                rgba(118, 75, 162, 0.1) 50%,
                transparent 100%);
            pointer-events: none;
            z-index: 0;
        }
        
        .main-sidebar > * {
            position: relative;
            z-index: 1;
        }
        
        /* Brand Logo - Modern Glass Card */
        .brand-link {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            padding: 1.5rem 1.5rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .brand-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(102, 126, 234, 0.1), 
                transparent);
            transition: left 0.6s ease;
        }
        
        .brand-link:hover::before {
            left: 100%;
        }
        
        .brand-link:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2);
        }
        
        .brand-image {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%) !important;
            box-shadow: 
                0 8px 32px rgba(102, 126, 234, 0.4),
                0 0 0 3px rgba(102, 126, 234, 0.2),
                inset 0 2px 4px rgba(255, 255, 255, 0.2);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            animation: gradientShift 3s ease infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% { 
                background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            }
            50% { 
                background: linear-gradient(135deg, #764ba2 0%, #f093fb 50%, #667eea 100%);
            }
        }
        
        .brand-link:hover .brand-image {
            transform: scale(1.15) rotate(5deg);
            box-shadow: 
                0 12px 40px rgba(102, 126, 234, 0.5),
                0 0 0 4px rgba(102, 126, 234, 0.3),
                inset 0 2px 4px rgba(255, 255, 255, 0.3);
        }
        
        .brand-text {
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: 0.8px;
            background: linear-gradient(135deg, #ffffff 0%, #e0e0e0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* User Panel - Modern Card Design */
        .user-panel {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1.25rem 1rem;
            margin: 1rem 1rem 1.5rem 1rem;
            box-shadow: 
                0 4px 16px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .user-panel:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-2px);
            box-shadow: 
                0 8px 24px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        
        .user-panel .image span {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            box-shadow: 
                0 6px 20px rgba(102, 126, 234, 0.4),
                0 0 0 2px rgba(102, 126, 234, 0.2);
            transition: all 0.3s ease;
        }
        
        .user-panel:hover .image span {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 
                0 8px 28px rgba(102, 126, 234, 0.5),
                0 0 0 3px rgba(102, 126, 234, 0.3);
        }
        
        .user-panel .info a {
            font-weight: 700;
            font-size: 1rem;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: 0.3px;
        }
        
        .user-panel .info small {
            color: rgba(255, 255, 255, 0.7) !important;
            font-weight: 500;
        }
        
        /* Navigation Links - Ultra Modern Design */
        .nav-link {
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 12px;
            margin: 0.3rem 0.75rem;
            padding: 0.85rem 1.15rem;
            position: relative;
            overflow: hidden;
            background: transparent;
            border: 1px solid transparent;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0 4px 4px 0;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 0;
            background: linear-gradient(90deg, 
                rgba(102, 126, 234, 0.15) 0%,
                rgba(118, 75, 162, 0.1) 100%);
            transform: translateY(-50%) scaleX(0);
            transform-origin: left;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: -1;
            border-radius: 12px;
        }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(102, 126, 234, 0.3);
            transform: translateX(6px);
            box-shadow: 
                0 4px 12px rgba(102, 126, 234, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        .nav-link:hover::before {
            transform: scaleY(1);
        }
        
        .nav-link:hover::after {
            transform: translateY(-50%) scaleX(1);
        }
        
        .nav-link.active {
            background: linear-gradient(90deg, 
                rgba(102, 126, 234, 0.25) 0%,
                rgba(118, 75, 162, 0.15) 100%);
            border-color: rgba(102, 126, 234, 0.4);
            color: #ffffff;
            font-weight: 700;
            box-shadow: 
                0 6px 20px rgba(102, 126, 234, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.15),
                0 0 0 1px rgba(102, 126, 234, 0.2);
            transform: translateX(4px);
        }
        
        .nav-link.active::before {
            transform: scaleY(1);
            box-shadow: 0 0 10px rgba(102, 126, 234, 0.6);
        }
        
        .nav-link.active::after {
            transform: translateY(-50%) scaleX(1);
        }
        
        /* Navigation Icons - Animated */
        .nav-icon {
            width: 22px;
            text-align: center;
            margin-right: 0.85rem;
            font-size: 1.15rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: inline-block;
        }
        
        .nav-link:hover .nav-icon {
            transform: scale(1.2) rotate(5deg);
            color: #667eea;
            text-shadow: 0 0 10px rgba(102, 126, 234, 0.5);
        }
        
        .nav-link.active .nav-icon {
            color: #ffffff;
            transform: scale(1.15);
            text-shadow: 0 0 15px rgba(102, 126, 234, 0.6);
        }
        
        /* Section Headers - Modern Gradient Design */
        .nav-header {
            color: rgba(255, 255, 255, 0.6) !important;
            font-size: 0.7rem !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 1.5px;
            padding: 1.25rem 1.5rem 0.75rem 1.5rem !important;
            margin-top: 0.75rem;
            position: relative;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            margin-left: 0.75rem;
            margin-right: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .nav-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            border-radius: 0 3px 3px 0;
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
                rgba(102, 126, 234, 0.3) 50%,
                transparent 100%);
        }
        
        .nav-header i {
            margin-right: 0.6rem;
            font-size: 0.9rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Treeview - Modern Collapsible */
        .nav-treeview {
            padding-left: 0.75rem;
            margin-top: 0.25rem;
        }
        
        .nav-treeview .nav-link {
            padding-left: 3rem;
            font-size: 0.9rem;
            margin: 0.25rem 0.75rem;
            border-left: 2px solid transparent;
        }
        
        .nav-treeview .nav-link:hover {
            padding-left: 3.25rem;
            border-left-color: rgba(102, 126, 234, 0.4);
        }
        
        .has-treeview > .nav-link {
            font-weight: 600;
        }
        
        .has-treeview > .nav-link .right {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: rgba(255, 255, 255, 0.5);
        }
        
        .has-treeview.menu-open > .nav-link .right {
            transform: rotate(-90deg);
            color: #667eea;
        }
        
        /* Badge - Modern Pulse Animation */
        .nav-link .badge {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
            animation: pulseGlow 2s ease-in-out infinite;
        }
        
        @keyframes pulseGlow {
            0%, 100% { 
                opacity: 1;
                box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
            }
            50% { 
                opacity: 0.8;
                box-shadow: 0 4px 20px rgba(245, 87, 108, 0.6);
                transform: translateY(-50%) scale(1.1);
            }
        }
        
        /* Scrollbar - Modern Design */
        .sidebar::-webkit-scrollbar {
            width: 8px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #764ba2 0%, #667eea 100%);
        }
        
        /* Spacing & Typography */
        .sidebar .nav {
            padding: 0.75rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.2rem;
        }
        
        .nav-link p {
            transition: all 0.3s ease;
            margin: 0;
            font-size: 0.95rem;
            font-weight: 500;
            letter-spacing: 0.2px;
        }
        
        .nav-link.active p {
            color: #ffffff;
            font-weight: 700;
        }
        
        .nav-link:hover p {
            color: #ffffff;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-link {
                margin: 0.25rem 0.5rem;
                padding: 0.75rem 1rem;
            }
            
            .nav-header {
                padding: 1rem 1.25rem 0.5rem 1.25rem !important;
                font-size: 0.65rem !important;
                margin-left: 0.5rem;
                margin-right: 0.5rem;
            }
            
            .user-panel {
                margin: 0.75rem 0.5rem 1rem 0.5rem;
                padding: 1rem 0.75rem;
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
            @if(auth()->user()->isAdmin())
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('users.index') }}" class="nav-link">User</a>
            </li>
            @endif
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('floor-plans.index') }}" class="nav-link">
                    <i class="fas fa-map mr-2"></i>Floor Plan Management
                </a>
            </li>
            @if(auth()->user()->isAdmin())
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{ route('clients.index') }}" class="nav-link">Clients</a>
            </li>
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
            <div class="d-flex align-items-center">
                <span class="brand-image img-circle elevation-3 d-inline-flex align-items-center justify-content-center" 
                      style="width: 48px; height: 48px; color: white; font-weight: 800; font-size: 16px; letter-spacing: 1px;">
                    KHB
                </span>
                <div class="ml-3">
                    <span class="brand-text font-weight-bold d-block" style="font-size: 1.2rem; line-height: 1.3;">KHB Booth</span>
                    <span class="brand-text font-weight-light d-block" style="font-size: 0.7rem; opacity: 0.7; line-height: 1.2; letter-spacing: 0.5px;">Management System</span>
                </div>
            </div>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- User Panel - Modern Glass Card -->
            @auth
            <div class="user-panel d-flex align-items-center">
                <div class="image">
                    <span class="img-circle elevation-2 d-inline-flex align-items-center justify-content-center" 
                          style="width: 50px; height: 50px; color: white; font-weight: 800; font-size: 20px; letter-spacing: 0.5px;">
                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                    </span>
                </div>
                <div class="info flex-grow-1 ml-2">
                    <a href="javascript:void(0);" class="d-block text-white font-weight-bold" style="font-size: 1rem; text-decoration: none; letter-spacing: 0.3px;">
                        {{ auth()->user()->username }}
                    </a>
                    @if(auth()->user()->employee)
                    <small class="d-block" style="font-size: 0.8rem; font-weight: 500;">
                        {{ auth()->user()->employee->position->name ?? (auth()->user()->employee->department->name ?? 'Employee') }}
                    </small>
                    @else
                    <small class="d-block" style="font-size: 0.8rem; font-weight: 500;">
                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'User' }}
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
                    {{-- Business Operations Section --}}
                    <li class="nav-header">
                        <i class="fas fa-briefcase"></i>Business Operations
                    </li>
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>Bookings</p>
                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reports & Analytics</p>
                        </a>
                    </li>
                    {{-- Finance Section --}}
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
                            <li class="nav-item">
                                <a href="{{ route('finance.payments.index') }}" class="nav-link {{ request()->routeIs('finance.payments.*') ? 'active' : '' }}">
                                    <i class="fas fa-money-bill-wave nav-icon"></i>
                                    <p>Payments</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('finance.costings.index') }}" class="nav-link {{ request()->routeIs('finance.costings.*') ? 'active' : '' }}">
                                    <i class="fas fa-calculator nav-icon"></i>
                                    <p>Costing Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('finance.expenses.index') }}" class="nav-link {{ request()->routeIs('finance.expenses.*') ? 'active' : '' }}">
                                    <i class="fas fa-arrow-down nav-icon"></i>
                                    <p>Expense Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('finance.revenues.index') }}" class="nav-link {{ request()->routeIs('finance.revenues.*') ? 'active' : '' }}">
                                    <i class="fas fa-arrow-up nav-icon"></i>
                                    <p>Revenue Management</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('finance.booth-pricing.index') }}" class="nav-link {{ request()->routeIs('finance.booth-pricing.*') ? 'active' : '' }}">
                                    <i class="fas fa-dollar-sign nav-icon"></i>
                                    <p>Booth Pricing</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('finance.categories.index') }}" class="nav-link {{ request()->routeIs('finance.categories.*') ? 'active' : '' }}">
                                    <i class="fas fa-tags nav-icon"></i>
                                    <p>Categories</p>
                                </a>
                            </li>
                        </ul>
                    </li>
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
                    <li class="nav-header">
                        <i class="fas fa-tools"></i>Communication & Tools
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('communications.index') }}" class="nav-link {{ request()->routeIs('communications.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>Messages</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('export.index') }}" class="nav-link {{ request()->routeIs('export.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-export"></i>
                            <p>Export/Import</p>
                        </a>
                    </li>
                    
                    {{-- System Administration Section --}}
                    @if(auth()->user()->isAdmin())
                    <li class="nav-header">
                        <i class="fas fa-cog"></i>System Administration
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('activity-logs.index') }}" class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Activity Logs</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('email-templates.index') }}" class="nav-link {{ request()->routeIs('email-templates.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope-open-text"></i>
                            <p>Email Templates</p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('users.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('roles.*') || request()->routeIs('permissions.*') || request()->routeIs('users.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                Staff Management
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                    <i class="fas fa-users nav-icon"></i>
                                    <p>Users</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-shield nav-icon"></i>
                                    <p>Roles</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                                    <i class="fas fa-key nav-icon"></i>
                                    <p>Permissions</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-folder"></i>
                            <p>Category & Sub</p>
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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Toastr for notifications -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<!-- Select2 for better dropdowns -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

// Update notification badge
function updateNotificationBadge() {
    fetch('{{ route("notifications.unread-count") }}')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error updating notification badge:', error);
        });
}

// Update badge on page load and every 30 seconds
updateNotificationBadge();
setInterval(updateNotificationBadge, 30000);

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
</script>

</body>
</html>

