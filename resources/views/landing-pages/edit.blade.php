@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Edit Landing Page')
@section('page-title', 'Edit Landing Page')
@section('breadcrumb', 'Landing Pages / Edit')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Edit: {{ $landingPage->name }}</h1>
            <p>Update settings, visuals, and multilingual copy.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.reporting', $landingPage) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-chart-line" aria-hidden="true"></i> Leads ({{ (int) ($landingPage->leads_count ?? 0) }})
            </a>
            <a href="{{ route('landing-pages.translation-center', $landingPage) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-language" aria-hidden="true"></i> Public copy hub
            </a>
            <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to list
            </a>
        </div>
    </header>

    <div class="canvas-panel lp-landing-form-shell">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-edit" aria-hidden="true"></i> Page configuration</h2>
        </div>
        <form id="lpLandingPageAdminForm" class="lp-landing-i18n-copy-form" action="{{ route('landing-pages.update', $landingPage) }}" method="POST" enctype="multipart/form-data" novalidate data-lp-i18n-root="visual">
            @csrf
            @method('PUT')
            @include('landing-pages.partials.form', ['landingPage' => $landingPage])
            <div class="card-footer lp-sticky-card-footer lp-form-footer-actions">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-save" aria-hidden="true"></i> Update
                </button>
                <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@include('landing-pages.partials.lp-machine-translate-landing-script', ['landingPage' => $landingPage])
@endpush
