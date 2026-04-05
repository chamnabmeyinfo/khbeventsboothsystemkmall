@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Public copy — '.$landingPage->name)
@section('page-title', 'Public copy hub')
@section('breadcrumb', 'Landing Pages / '.$landingPage->name.' / Public copy')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Public copy: {{ $landingPage->name }}</h1>
            <p>Every field visitors see in each language, in one place. This uses the same storage as the main page editor; deleting this landing page removes all of this copy.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.edit', $landingPage) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-edit" aria-hidden="true"></i> Full page settings
            </a>
            <a href="{{ route('landing-pages.preview', $landingPage) }}" class="action-btn action-btn-secondary" target="_blank" rel="noopener">Preview</a>
            <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">All pages</a>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success border">{{ session('success') }}</div>
    @endif
    @if($errors->has('translation_hub'))
        <div class="alert alert-danger border">{{ $errors->first('translation_hub') }}</div>
    @endif

    <div class="canvas-panel lp-landing-form-shell">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-language" aria-hidden="true"></i> Translate &amp; edit public text</h2>
        </div>
        <form id="lpTranslationCenterForm"
              class="lp-landing-i18n-copy-form"
              action="{{ route('landing-pages.translation-center.update', $landingPage) }}"
              method="POST"
              enctype="multipart/form-data"
              novalidate
              data-lp-i18n-root="translation_hub"
              data-lp-enabled-non-en='@json($enabledNonEnLocales)'>
            @csrf
            @method('PUT')
            <div class="card-body lp-visual-form-root">
                @include('landing-pages.partials.lp-public-copy-workspace', [
                    'lpI18nInputRoot' => 'translation_hub',
                    'lpPublicCopyHeading' => 'All public text by language',
                ])
            </div>
            <div class="card-footer lp-sticky-card-footer lp-form-footer-actions">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-save" aria-hidden="true"></i> Save public copy
                </button>
                <a href="{{ route('landing-pages.edit', $landingPage) }}" class="action-btn action-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@include('landing-pages.partials.lp-landing-copy-form-scripts')
@include('landing-pages.partials.lp-machine-translate-landing-script', ['landingPage' => $landingPage])
@endpush
