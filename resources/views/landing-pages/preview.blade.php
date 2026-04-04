@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Preview Landing Page')
@section('page-title', 'Preview Landing Page')
@section('breadcrumb', 'Landing Pages / Preview')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Preview: {{ $landingPage->name }}</h1>
            <p>Inline editing in an embedded public view.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.edit', $landingPage) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-edit" aria-hidden="true"></i> Edit settings
            </a>
            <a href="{{ route('landing-pages.public.show', $landingPage) }}" target="_blank" rel="noopener" class="action-btn action-btn-primary">
                <i class="fas fa-external-link-alt" aria-hidden="true"></i> Open public URL
            </a>
        </div>
    </header>

    <div class="lp-callout" role="status">
        <strong>Inline edit:</strong> Click text blocks in the preview to edit, then use <em>Save Changes</em>.
        For images, use <em>Alt + Click</em> to set URL/path. Use Lock / Undo / Reset / Publish from the toolbar.
    </div>

    <div class="canvas-panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-eye" aria-hidden="true"></i> Live preview</h2>
        </div>
        <p class="text-muted small mb-3">
            Public URL:
            <a href="{{ route('landing-pages.public.show', $landingPage) }}" target="_blank" rel="noopener">{{ route('landing-pages.public.show', $landingPage) }}</a>
        </p>
        <div class="lp-preview-frame">
            <iframe
                src="{{ route('landing-pages.public.show', [$landingPage, 'editor' => 1, 'lang' => $landingPage->default_locale ?? 'en']) }}"
                title="Landing preview"
                loading="lazy"></iframe>
        </div>
    </div>
</div>
@endsection
