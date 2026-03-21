@extends('layouts.admin')

@section('title', __('Client profile demo'))

@php
    $clientProfileViteReady = file_exists(public_path('build/manifest.json'));
@endphp

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if ($clientProfileViteReady)
        @vite(['resources/js/client-dashboard/main.jsx'])
    @endif
@endpush

@section('content')
    @if (! $clientProfileViteReady)
        <div class="alert alert-warning shadow-sm" role="alert">
            <strong>{{ __('Client profile UI — assets missing') }}</strong>
            <p class="small mb-0">{{ __('Run') }} <code>npm ci &amp;&amp; npm run build</code> {{ __('on the server.') }}</p>
        </div>
    @endif

    {{-- Falls back to built-in demo data in main.jsx --}}
    <div id="client-profile-root" class="client-profile-react-root w-100"></div>
@endsection
