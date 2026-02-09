@extends('layouts.adminlte')

@section('title', 'Version ' . $version->version)
@section('page-title', 'Version ' . $version->version)
@section('breadcrumb', 'System / Versions / ' . $version->version)

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-code-branch mr-2"></i>{{ $version->version }}
                @if($version->is_current)
                    <span class="badge badge-success ml-2">Current</span>
                @endif
            </h5>
            <div>
                @if(!$version->is_current)
                    <form action="{{ route('versions.set-current', $version) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success">Set as current</button>
                    </form>
                @endif
                <a href="{{ route('versions.index') }}" class="btn btn-sm btn-secondary">Back to list</a>
            </div>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-2">Released</dt>
                <dd class="col-sm-10">{{ $version->released_at->format('F j, Y') }}</dd>

                @if($version->summary)
                    <dt class="col-sm-2">Summary</dt>
                    <dd class="col-sm-10">{{ $version->summary }}</dd>
                @endif

                @if($version->changelog)
                    <dt class="col-sm-2">Changelog</dt>
                    <dd class="col-sm-10"><pre class="mb-0 p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $version->changelog }}</pre></dd>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection
