@extends('layouts.admin')

@section('title', __('Client') . ' #' . $client->id)

@push('body-class', 'ios-dashboard-mode clients-page')

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
            <p class="small mb-2">
                {{ __('After git pull, run on the server:') }}
                <code>npm ci &amp;&amp; npm run build</code>
                {{ __('(Git does not include public/build.)') }}
            </p>
        </div>
    @endif

    @if (! empty($clientProfilePayload))
        <script type="application/json" id="client-profile-payload">@json($clientProfilePayload)</script>
    @endif

    <div id="client-profile-root" class="client-profile-react-root w-100"></div>

    @include('clients.partials.profile-upload-modals')
@endsection

@push('scripts')
    @include('clients.partials.profile-react-scripts')
@endpush
