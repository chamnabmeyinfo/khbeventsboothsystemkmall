@extends('layouts.adminlte')

@section('title', 'Preview Landing Page')
@section('page-title', 'Preview Landing Page')
@section('breadcrumb', 'Landing Pages / Preview')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">{{ $landingPage->name }}</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('landing-pages.edit', $landingPage) }}" class="btn btn-primary btn-sm">Edit</a>
                <a href="{{ route('landing-pages.public.show', $landingPage) }}" target="_blank" class="btn btn-info btn-sm">Open Public URL</a>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info py-2">
                <strong>Inline edit:</strong> Click text blocks in preview to edit directly, then click <em>Save Changes</em>.
                For image blocks, use <em>Alt + Click</em> to set image URL/path quickly. Use Lock/Undo/Reset/Publish from the toolbar.
            </div>
            <p class="text-muted mb-2">
                Public URL:
                <a href="{{ route('landing-pages.public.show', $landingPage) }}" target="_blank">{{ route('landing-pages.public.show', $landingPage) }}</a>
            </p>
            <div class="border rounded" style="height: 75vh; min-height: 500px;">
                <iframe
                    src="{{ route('landing-pages.public.show', [$landingPage, 'editor' => 1]) }}"
                    title="Landing preview"
                    style="width: 100%; height: 100%; border: 0;"
                    loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
