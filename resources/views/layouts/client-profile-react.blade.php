<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Client profile')) — KHB Events</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    @stack('styles')
    @vite(['resources/js/client-dashboard/main.jsx'])
</head>
<body class="h-full min-h-screen overflow-x-auto bg-slate-100 antialiased">
    @yield('before-root')
    <div id="client-profile-root" class="min-h-screen min-w-[1100px]"></div>
    @yield('after-root')
    @stack('scripts')
</body>
</html>
