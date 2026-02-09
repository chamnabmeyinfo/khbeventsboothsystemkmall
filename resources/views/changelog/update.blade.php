@extends('layouts.adminlte')

@section('title', 'Update Changelog')
@section('page-title', 'Update Changelog')
@section('breadcrumb', 'System / Update Changelog')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            {{-- Quick add entry to current version --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-plus-circle mr-2"></i>Quick add changelog entry</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Add a line to the <strong>current version</strong> changelog. If no current version exists, one will be created.</p>
                    <form action="{{ route('changelog.append') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="entry">New entry</label>
                            <textarea name="entry" id="entry" class="form-control @error('entry') is-invalid @enderror" rows="3" placeholder="e.g. Added new report export feature.">{{ old('entry') }}</textarea>
                            @error('entry')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Add to changelog</button>
                    </form>
                </div>
            </div>

            {{-- Edit current version --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit mr-2"></i>Edit current version</h5>
                </div>
                <div class="card-body">
                    @if($currentVersion)
                        <form action="{{ route('changelog.update.save') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Version</label>
                                <p class="form-control-plaintext font-weight-bold">{{ $currentVersion->version }}</p>
                                <small class="text-muted">Released: {{ $currentVersion->released_at->format('Y-m-d') }}</small>
                            </div>
                            <div class="form-group">
                                <label for="summary">Summary</label>
                                <input type="text" name="summary" id="summary" class="form-control @error('summary') is-invalid @enderror" value="{{ old('summary', $currentVersion->summary) }}" maxlength="500" placeholder="Short description of this release">
                                @error('summary')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="changelog">Changelog (full text)</label>
                                <textarea name="changelog" id="changelog" class="form-control @error('changelog') is-invalid @enderror" rows="12" placeholder="List all changes for this version...">{{ old('changelog', $currentVersion->changelog) }}</textarea>
                                @error('changelog')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </form>
                    @else
                        <p class="text-muted mb-0">No current version set. <a href="{{ route('versions.create') }}">Create a new version</a> or <a href="{{ route('versions.index') }}">set one as current</a> from the version list.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-code-branch mr-2"></i>New release</h5>
                    <a href="{{ route('versions.create') }}" class="btn btn-sm btn-success">Create version</a>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-0">When you ship a new release, create a new version with version number, date, and changelog. You can then set it as current from the list below.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent versions</h5>
                    <a href="{{ route('versions.index') }}" class="btn btn-sm btn-outline-secondary">All</a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentVersions as $v)
                        <div class="border-bottom p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>v{{ $v->version }}</strong>
                                    @if($v->is_current)
                                        <span class="badge badge-success ml-1">Current</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $v->released_at->format('Y-m-d') }}</small>
                                </div>
                                <a href="{{ route('versions.show', $v) }}" class="btn btn-xs btn-outline-primary">View</a>
                            </div>
                            @if($v->summary)
                                <p class="small mb-0 mt-1">{{ Str::limit($v->summary, 50) }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="p-3 text-muted small">No versions yet. Create one to start tracking changes.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
