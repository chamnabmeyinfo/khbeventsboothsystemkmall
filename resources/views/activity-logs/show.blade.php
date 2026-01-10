@extends('layouts.adminlte')

@section('title', 'Activity Log Details')
@section('page-title', 'Activity Log Details')
@section('breadcrumb', 'Activity Logs / View')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('activity-logs.index') }}">Activity Logs</a></li>
            <li class="breadcrumb-item active">Log #{{ $activityLog->id }}</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history mr-2"></i>Activity Log Details</h3>
        </div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Date & Time:</dt>
                <dd class="col-sm-9">{{ $activityLog->created_at->format('Y-m-d H:i:s') }}</dd>
                
                <dt class="col-sm-3">User:</dt>
                <dd class="col-sm-9">{{ $activityLog->user ? $activityLog->user->username : 'System' }}</dd>
                
                <dt class="col-sm-3">Action:</dt>
                <dd class="col-sm-9">
                    <span class="badge badge-{{ $activityLog->action == 'created' ? 'success' : ($activityLog->action == 'updated' ? 'info' : ($activityLog->action == 'deleted' ? 'danger' : 'secondary')) }}">
                        {{ ucfirst($activityLog->action) }}
                    </span>
                </dd>
                
                <dt class="col-sm-3">Model:</dt>
                <dd class="col-sm-9">{{ $activityLog->model_type ? class_basename($activityLog->model_type) : 'N/A' }}</dd>
                
                <dt class="col-sm-3">Description:</dt>
                <dd class="col-sm-9">{{ $activityLog->description ?? 'N/A' }}</dd>
                
                <dt class="col-sm-3">IP Address:</dt>
                <dd class="col-sm-9">{{ $activityLog->ip_address ?? 'N/A' }}</dd>
                
                <dt class="col-sm-3">Route:</dt>
                <dd class="col-sm-9"><code>{{ $activityLog->route ?? 'N/A' }}</code></dd>
            </dl>

            @if($activityLog->old_values || $activityLog->new_values)
            <hr>
            <div class="row">
                @if($activityLog->old_values)
                <div class="col-md-6">
                    <h5>Old Values:</h5>
                    <pre class="bg-light p-3">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
                @if($activityLog->new_values)
                <div class="col-md-6">
                    <h5>New Values:</h5>
                    <pre class="bg-light p-3">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT) }}</pre>
                </div>
                @endif
            </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('activity-logs.index') }}" class="btn btn-default">
                <i class="fas fa-arrow-left mr-1"></i>Back to Logs
            </a>
        </div>
    </div>
</div>
@endsection
