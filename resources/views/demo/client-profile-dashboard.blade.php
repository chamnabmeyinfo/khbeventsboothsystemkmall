<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Client profile') }} — KHB Events</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/js/client-dashboard/main.jsx'])
</head>
<body class="h-full min-h-screen overflow-x-auto bg-slate-100 antialiased">
    <div id="client-profile-dashboard-root" class="min-h-screen min-w-[1100px]"></div>
</body>
</html>
