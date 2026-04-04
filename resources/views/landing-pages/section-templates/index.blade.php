@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Landing section templates')
@section('page-title', 'Landing section templates')
@section('breadcrumb', 'Landing Pages / Section templates')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Section templates</h1>
            <p>Reusable copy blocks for hero, FAQ, and other sections.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">Back to landing pages</a>
            <a href="{{ route('landing-pages.section-templates.create') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> New template
            </a>
        </div>
    </header>

    <div class="canvas-panel mb-3">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-file-alt" aria-hidden="true"></i> Reusable section copy</h2>
        </div>
        <div class="looker-table-wrapper">
            <table class="looker-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Layout</th>
                        <th>Updated</th>
                        <th class="text-nowrap lp-col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $t)
                        <tr>
                            <td>
                                <strong>{{ $t->name }}</strong>
                                @if($t->description)
                                    <br><span class="text-muted small">{{ Str::limit($t->description, 80) }}</span>
                                @endif
                            </td>
                            <td>{{ \App\Models\LandingPage::sectionLayoutLabels()[$t->layout_key] ?? $t->layout_key }}</td>
                            <td class="text-muted small text-nowrap">{{ $t->updated_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="lp-row-actions justify-content-start">
                                    <a href="{{ route('landing-pages.section-templates.edit', $t) }}" class="action-btn action-btn-primary lp-btn-compact">Edit</a>
                                    <form action="{{ route('landing-pages.section-templates.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this template?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger lp-btn-compact">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No templates yet. Create one to reuse hero, FAQ, or other section copy across pages.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
            <div class="lp-pagination-wrap">{{ $templates->withQueryString()->links() }}</div>
        @endif
    </div>

    <p class="text-muted small">Content is stored as JSON with an <code>i18n</code> object per language (same field names as the landing visual form). Applying a template updates the page in the database immediately from the edit screen.</p>
</div>
@endsection
