@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Edit section template')
@section('page-title', 'Edit section template')
@section('breadcrumb', 'Landing Pages / Section templates / Edit')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Edit: {{ $sectionTemplate->name }}</h1>
            <p>Update layout JSON for this template.</p>
        </div>
        <div class="looker-actions">
            <a href="{{ route('landing-pages.section-templates.index') }}" class="action-btn action-btn-secondary">Back to list</a>
        </div>
    </header>

    <div class="canvas-panel">
        <form method="POST" action="{{ route('landing-pages.section-templates.update', $sectionTemplate) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="st_name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="st_name" name="name" required value="{{ old('name', $sectionTemplate->name) }}" maxlength="255">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="st_layout">Layout <span class="text-danger">*</span></label>
                <select class="form-control @error('layout_key') is-invalid @enderror" id="st_layout" name="layout_key" required>
                    @foreach($layoutLabels as $key => $label)
                        <option value="{{ $key }}" {{ old('layout_key', $sectionTemplate->layout_key) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('layout_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label for="st_desc">Description</label>
                <input type="text" class="form-control" id="st_desc" name="description" value="{{ old('description', $sectionTemplate->description) }}" maxlength="512">
            </div>
            <div class="form-group mb-0">
                <label for="st_content">Content (JSON) <span class="text-danger">*</span></label>
                <textarea class="form-control font-monospace @error('content_json') is-invalid @enderror" id="st_content" name="content_json" rows="18" required spellcheck="false">{{ old('content_json', json_encode($sectionTemplate->content ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}</textarea>
                @error('content_json')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="lp-form-footer-actions pt-3 mt-3 lp-border-top-soft">
                <button type="submit" class="action-btn action-btn-primary">Update template</button>
                <a href="{{ route('landing-pages.section-templates.index') }}" class="action-btn action-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
