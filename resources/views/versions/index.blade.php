@extends('layouts.adminlte')

@section('title', 'System Versions')
@section('page-title', 'System Versions')
@section('breadcrumb', 'System / Versions')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-code-branch mr-2"></i>Version History</h5>
            <a href="{{ route('versions.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-1"></i>Add Version
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Version</th>
                            <th>Released</th>
                            <th>Summary</th>
                            <th>Current</th>
                            <th width="140">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($versions as $v)
                            <tr>
                                <td><strong>{{ $v->version }}</strong></td>
                                <td>{{ $v->released_at->format('Y-m-d') }}</td>
                                <td>{{ Str::limit($v->summary, 60) }}</td>
                                <td>
                                    @if($v->is_current)
                                        <span class="badge badge-success">Current</span>
                                    @else
                                        <form action="{{ route('versions.set-current', $v) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-outline-secondary">Set current</button>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('versions.show', $v) }}" class="btn btn-sm btn-info">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No versions recorded yet. Add one to track releases.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($versions->hasPages())
            <div class="card-footer">
                {{ $versions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
