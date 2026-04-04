@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Create Landing Page')
@section('page-title', 'Create Landing Page')
@section('breadcrumb', 'Landing Pages / Create')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Create landing page</h1>
            <p>Add a new public marketing page with the visual builder.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to list
            </a>
        </div>
    </header>

    <div class="canvas-panel lp-landing-form-shell">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-rocket" aria-hidden="true"></i> Details &amp; content</h2>
        </div>
        <form id="lpLandingPageAdminForm" action="{{ route('landing-pages.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @include('landing-pages.partials.form', ['landingPage' => null])
            <div class="card-footer lp-sticky-card-footer lp-form-footer-actions">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-save" aria-hidden="true"></i> Create
                </button>
                <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
