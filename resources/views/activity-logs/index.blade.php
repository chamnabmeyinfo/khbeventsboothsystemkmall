@extends('layouts.adminlte')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')
@section('breadcrumb', 'Activity Logs')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history mr-2"></i>Activity Logs</h3>
            <div class="card-tools">
                <a href="{{ route('activity-logs.export') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-download mr-1"></i>Export
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-2">
                        <select name="action" class="form-control form-control-sm">
                            <option value="">All Actions</option>
                            <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                            <option value="viewed" {{ request('action') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="model_type" class="form-control form-control-sm">
                            <option value="">All Models</option>
                            <option value="App\Models\Booth" {{ request('model_type') == 'App\Models\Booth' ? 'selected' : '' }}>Booths</option>
                            <option value="App\Models\Client" {{ request('model_type') == 'App\Models\Client' ? 'selected' : '' }}>Clients</option>
                            <option value="App\Models\Book" {{ request('model_type') == 'App\Models\Book' ? 'selected' : '' }}>Bookings</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="From">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="To">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-filter mr-1"></i>Filter
                        </button>
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-secondary">Clear</a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Model</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $log->user ? $log->user->username : 'System' }}</td>
                            <td>
                                <span class="badge badge-{{ $log->action == 'created' ? 'success' : ($log->action == 'updated' ? 'info' : ($log->action == 'deleted' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td>{{ $log->model_type ? class_basename($log->model_type) : 'N/A' }}</td>
                            <td>{{ $log->description ?? 'N/A' }}</td>
                            <td>{{ $log->ip_address ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('activity-logs.show', $log) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No activity logs found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
