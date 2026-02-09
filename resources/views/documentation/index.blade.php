@extends('layouts.adminlte')

@section('title', 'Documentation & Changelog')
@section('page-title', 'Documentation & Changelog')
@section('breadcrumb', 'Documentation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            {{-- Current version --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Current version</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>Version:</strong> {{ $appVersion }}</p>
                    @if($current['released_at'] ?? null)
                        <p class="mb-1"><strong>Released:</strong> {{ $current['released_at'] }}</p>
                    @endif
                    @if(!empty($current['summary']))
                        <p class="mb-1"><strong>Summary:</strong> {{ $current['summary'] }}</p>
                    @endif
                    @if(!empty($current['changelog']))
                        <div class="mt-2">
                            <strong>Changelog:</strong>
                            <pre class="mb-0 p-3 bg-light rounded small" style="white-space: pre-wrap;">{{ $current['changelog'] }}</pre>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Changelog (all versions) --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history mr-2"></i>Changelog</h5>
                </div>
                <div class="card-body">
                    @forelse($allVersions as $v)
                        <div class="border-left border-primary pl-3 mb-4">
                            <div class="d-flex align-items-center flex-wrap">
                                <strong class="mr-2">v{{ $v->version }}</strong>
                                <span class="text-muted">{{ $v->released_at->format('Y-m-d') }}</span>
                                @if($v->is_current)
                                    <span class="badge badge-success ml-2">Current</span>
                                @endif
                            </div>
                            @if($v->summary)
                                <p class="mb-1 mt-1 text-muted">{{ $v->summary }}</p>
                            @endif
                            @if($v->changelog)
                                <pre class="mb-0 small" style="white-space: pre-wrap;">{{ $v->changelog }}</pre>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No version history yet. Admins can add versions under <strong>System Administration → Versions</strong>.</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-book mr-2"></i>Website documentation</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Project documentation is maintained in the <code>docs/</code> folder and in the repository.</p>
                    <ul class="list-unstyled small">
                        <li><i class="fas fa-file-alt text-info mr-1"></i> <a href="{{ asset('docs/README.md') }}" target="_blank" rel="noopener">docs/README.md</a> (if accessible)</li>
                        <li><i class="fas fa-list text-info mr-1"></i> <strong>CHANGELOG.md</strong> in project root for release notes</li>
                    </ul>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <p class="small mb-0">Manage system versions and changelog from <a href="{{ route('versions.index') }}">Versions</a>.</p>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
