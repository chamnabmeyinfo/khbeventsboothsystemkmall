@extends('layouts.adminlte')

@section('title', 'Landing section templates')
@section('page-title', 'Landing section templates')
@section('breadcrumb', 'Landing Pages / Section templates')

@section('content')
<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h3 class="card-title mb-0"><i class="fas fa-file-alt mr-2" aria-hidden="true"></i>Reusable section copy</h3>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('landing-pages.index') }}" class="btn btn-default" style="min-height:44px;">Back to landing pages</a>
                <a href="{{ route('landing-pages.section-templates.create') }}" class="btn btn-primary" style="min-height:44px;"><i class="fas fa-plus mr-1" aria-hidden="true"></i>New template</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Layout</th>
                            <th>Updated</th>
                            <th style="width:10rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $t)
                            <tr>
                                <td><strong>{{ $t->name }}</strong>@if($t->description)<br><span class="text-muted small">{{ Str::limit($t->description, 80) }}</span>@endif</td>
                                <td>{{ \App\Models\LandingPage::sectionLayoutLabels()[$t->layout_key] ?? $t->layout_key }}</td>
                                <td class="text-muted small">{{ $t->updated_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('landing-pages.section-templates.edit', $t) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('landing-pages.section-templates.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this template?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
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
        </div>
        @if($templates->hasPages())
            <div class="card-footer">{{ $templates->withQueryString()->links() }}</div>
        @endif
    </div>
    <p class="text-muted small">Content is stored as JSON with an <code>i18n</code> object per language (same field names as the landing visual form). Applying a template updates the page in the database immediately from the edit screen.</p>
</div>
@endsection
