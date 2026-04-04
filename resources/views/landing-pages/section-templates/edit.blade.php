@extends('layouts.adminlte')

@section('title', 'Edit section template')
@section('page-title', 'Edit section template')
@section('breadcrumb', 'Landing Pages / Section templates / Edit')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">Edit: {{ $sectionTemplate->name }}</h3>
        </div>
        <form method="POST" action="{{ route('landing-pages.section-templates.update', $sectionTemplate) }}">
            @csrf
            @method('PUT')
            <div class="card-body">
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
            </div>
            <div class="card-footer d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary" style="min-height:44px;">Update template</button>
                <a href="{{ route('landing-pages.section-templates.index') }}" class="btn btn-default" style="min-height:44px;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
