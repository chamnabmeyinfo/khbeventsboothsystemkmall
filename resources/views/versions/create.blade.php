@extends('layouts.adminlte')

@section('title', 'Add System Version')
@section('page-title', 'Add System Version')
@section('breadcrumb', 'System / Versions / Create')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-tag mr-2"></i>New release</h5>
        </div>
        <form action="{{ route('versions.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="version">Version number <span class="text-danger">*</span></label>
                    <input type="text" name="version" id="version" class="form-control @error('version') is-invalid @enderror" value="{{ old('version') }}" placeholder="e.g. 1.0.0" maxlength="45" required>
                    @error('version')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="released_at">Release date <span class="text-danger">*</span></label>
                    <input type="date" name="released_at" id="released_at" class="form-control @error('released_at') is-invalid @enderror" value="{{ old('released_at', now()->format('Y-m-d')) }}" required>
                    @error('released_at')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="summary">Summary</label>
                    <input type="text" name="summary" id="summary" class="form-control @error('summary') is-invalid @enderror" value="{{ old('summary') }}" placeholder="Short description of this release" maxlength="500">
                    @error('summary')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="changelog">Changelog</label>
                    <textarea name="changelog" id="changelog" class="form-control @error('changelog') is-invalid @enderror" rows="10" placeholder="List changes (e.g. Added X, Fixed Y, Changed Z)">{{ old('changelog') }}</textarea>
                    @error('changelog')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="is_current" id="is_current" value="1" {{ old('is_current') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_current">Mark as current version</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save Version</button>
                <a href="{{ route('versions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
