@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'New section template')
@section('page-title', 'New section template')
@section('breadcrumb', 'Landing Pages / Section templates / Create')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Create section template</h1>
            <p>Define reusable JSON for one section layout.</p>
        </div>
        <div class="looker-actions">
            <a href="{{ route('landing-pages.section-templates.index') }}" class="action-btn action-btn-secondary">Back to list</a>
        </div>
    </header>

    <div class="canvas-panel">
        <form method="POST" action="{{ route('landing-pages.section-templates.store') }}">
            @csrf
            <div class="form-group">
                <label for="st_name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="st_name" name="name" required value="{{ old('name') }}" maxlength="255">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="st_layout">Layout <span class="text-danger">*</span></label>
                <select class="form-control @error('layout_key') is-invalid @enderror" id="st_layout" name="layout_key" required>
                    <option value="">— Select —</option>
                    @foreach($layoutLabels as $key => $label)
                        <option value="{{ $key }}" {{ old('layout_key') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('layout_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="st_desc">Description</label>
                <input type="text" class="form-control" id="st_desc" name="description" value="{{ old('description') }}" maxlength="512">
            </div>
            <div class="form-group mb-0">
                <label for="st_content">Content (JSON) <span class="text-danger">*</span></label>
                <textarea class="form-control font-monospace @error('content_json') is-invalid @enderror" id="st_content" name="content_json" rows="16" required spellcheck="false">{{ old('content_json', $exampleJson) }}</textarea>
                @error('content_json')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <small class="text-muted d-block mt-1">Use an <code>i18n</code> object with locale keys (<code>en</code>, <code>km</code>, <code>zh</code>). Inside each locale, use the same field names as the visual form for that section (e.g. <code>hero_title</code>, <code>hero_subtitle</code> for Hero).</small>
            </div>
            <div class="lp-form-footer-actions pt-3 mt-3 lp-border-top-soft">
                <button type="submit" class="action-btn action-btn-primary">Save template</button>
                <a href="{{ route('landing-pages.section-templates.index') }}" class="action-btn action-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
