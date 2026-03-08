<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Event Management')</title>
    
    <!-- Bootstrap 5 CSS - Local -->
    <link href="{{ asset('vendor/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome - Local -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- Global UX Consistency CSS -->
    <link rel="stylesheet" href="{{ asset('css/modern-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global-ux-consistency.css') }}">
    
    <link rel="stylesheet" href="{{ asset('css/modern-header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modern-sidebar.css') }}">
    
    @stack('styles')
</head>
<body class="admin-body @stack('body-class')">
    @include('partials.modern-header')

    <div class="layout-wrapper">
        @auth
            @include('partials.modern-sidebar')
        @endauth

        <main class="main-content-pushed container-fluid py-4" id="main-content" style="min-height: calc(100vh - 70px);">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS - Local -->
    <script src="{{ asset('vendor/bootstrap5/js/bootstrap.bundle.min.js') }}"></script>
    <!-- jQuery - Local -->
    <script src="{{ asset('vendor/jquery/jquery-3.7.0.min.js') }}"></script>
    
    @stack('scripts')
</body>
</html>

