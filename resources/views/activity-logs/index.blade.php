@extends('layouts.adminlte')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')
@section('breadcrumb', 'Insights / Activity Logs')

@push('styles')
<style>
    .log-item {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        border-left: 4px solid;
        padding: 16px;
        margin-bottom: 12px;
        transition: all 0.2s;
    }

    .log-item:hover {
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .log-item.created { border-left-color: #28a745; }
    .log-item.updated { border-left-color: #17a2b8; }
    .log-item.deleted { border-left-color: #dc3545; }
    .log-item.viewed { border-left-color: #6c757d; }

    .log-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
        flex-shrink: 0;
    }

    .log-icon.created { background: #28a745; }
    .log-icon.updated { background: #17a2b8; }
    .log-icon.deleted { background: #dc3545; }
    .log-icon.viewed { background: #6c757d; }

    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        height: 100%;
    }

    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .kpi-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 16px;
    }

    .kpi-card.primary .kpi-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success .kpi-icon { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .kpi-card.info .kpi-icon { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
    .kpi-card.warning .kpi-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 8px 0;
        line-height: 1;
    }

    .kpi-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-bar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 24px;
        margin-bottom: 24px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="kpi-label">Total Logs</div>
                    <div class="kpi-value">{{ number_format($stats['total_logs'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="kpi-label">Today</div>
                    <div class="kpi-value">{{ number_format($stats['today_logs'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="kpi-label">This Week</div>
                    <div class="kpi-value">{{ number_format($stats['this_week_logs'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card warning">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="kpi-label">Active Users</div>
                    <div class="kpi-value">{{ number_format($stats['unique_users'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <button type="button" class="btn btn-success" onclick="exportLogs()">
                        <i class="fas fa-file-csv mr-1"></i>Export CSV
                    </button>
                    <button type="button" class="btn btn-info" onclick="refreshPage()">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary active" onclick="switchView('list')" id="viewList">
                            <i class="fas fa-list mr-1"></i>List
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="switchView('timeline')" id="viewTimeline">
                            <i class="fas fa-stream mr-1"></i>Timeline
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('activity-logs.index') }}" id="filterForm">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-search mr-1"></i>Search</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search logs..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-user mr-1"></i>User</label>
                    <select name="user_id" class="form-control">
                        <option value="">All Users</option>
                        @foreach($users ?? [] as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->username }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-bolt mr-1"></i>Action</label>
                    <select name="action" class="form-control">
                        <option value="">All Actions</option>
                        @foreach($actions ?? [] as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-folder mr-1"></i>Module</label>
                    <select name="model_type" class="form-control">
                        <option value="">All Modules</option>
                        @foreach($modelTypes ?? [] as $modelType)
                            <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                                {{ class_basename($modelType) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 mb-3">
                    <label><i class="fas fa-calendar-alt mr-1"></i>From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-1 mb-3">
                    <label><i class="fas fa-calendar-check mr-1"></i>To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 mb-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </a>
                    @if(request()->hasAny(['search', 'user_id', 'action', 'model_type', 'date_from', 'date_to']))
                    <span class="badge badge-info ml-2">
                        {{ $logs->total() }} result(s) found
                    </span>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- List View -->
    <div id="listView" class="view-content">
        @forelse($logs as $log)
        <div class="log-item {{ strtolower($log->action) }}" onclick="window.location='{{ route('activity-logs.show', $log) }}'">
            <div class="d-flex align-items-start">
                <div class="log-icon {{ strtolower($log->action) }} mr-3">
                    @if($log->action == 'created')
                        <i class="fas fa-plus"></i>
                    @elseif($log->action == 'updated')
                        <i class="fas fa-edit"></i>
                    @elseif($log->action == 'deleted')
                        <i class="fas fa-trash"></i>
                    @else
                        <i class="fas fa-eye"></i>
                    @endif
                </div>
                <div style="flex: 1;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1" style="font-weight: 600;">
                                <span class="badge badge-{{ $log->action == 'created' ? 'success' : ($log->action == 'updated' ? 'info' : ($log->action == 'deleted' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                                @if($log->model_type)
                                <span class="badge badge-light ml-2">
                                    {{ class_basename($log->model_type) }}
                                </span>
                                @endif
                                @if($log->model_id && $log->model_type)
                                    @php
                                        $modelClass = $log->model_type;
                                        $routeName = null;
                                        $routeParam = null;
                                        
                                        if ($modelClass === 'App\\Models\\Book' || $modelClass === \App\Models\Book::class) {
                                            $routeName = 'books.show';
                                            $routeParam = $log->model_id;
                                        } elseif ($modelClass === 'App\\Models\\Client' || $modelClass === \App\Models\Client::class) {
                                            $routeName = 'clients.show';
                                            $routeParam = $log->model_id;
                                        } elseif ($modelClass === 'App\\Models\\User' || $modelClass === \App\Models\User::class) {
                                            $routeName = 'users.show';
                                            $routeParam = $log->model_id;
                                        } elseif ($modelClass === 'App\\Models\\Booth' || $modelClass === \App\Models\Booth::class) {
                                            $routeName = 'booths.show';
                                            $routeParam = $log->model_id;
                                        }
                                    @endphp
                                    @if($routeName)
                                    <a href="{{ route($routeName, $routeParam) }}" class="btn btn-xs btn-info ml-2" title="View {{ class_basename($log->model_type) }}">
                                        <i class="fas fa-external-link-alt"></i> View
                                    </a>
                                    @endif
                                @endif
                            </h6>
                            <p class="mb-2" style="color: #4a5568;">
                                {{ $log->description ?? 'No description' }}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-flex align-items-center">
                                @if($log->user)
                                    <x-avatar 
                                        :avatar="$log->user->avatar" 
                                        :name="$log->user->username" 
                                        :size="'xs'" 
                                        :type="$log->user->isAdmin() ? 'admin' : 'user'"
                                        :shape="'circle'"
                                    />
                                    <span class="ml-1">{{ $log->user->username }}</span>
                                @else
                                    <i class="fas fa-server mr-1"></i>
                                    <span>System</span>
                                @endif
                                @if($log->ip_address)
                                    <span class="ml-2">
                                        <i class="fas fa-network-wired mr-1"></i>{{ $log->ip_address }}
                                    </span>
                                @endif
                            </small>
                        </div>
                        <div>
                            <small class="text-muted">
                                <i class="fas fa-clock mr-1"></i>{{ $log->created_at->format('M d, Y H:i:s') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No activity logs found</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Timeline View (Same as list for now) -->
    <div id="timelineView" class="view-content" style="display: none;">
        <!-- Same content as list view -->
        <div id="listViewContent"></div>
    </div>

    <!-- Pagination -->
    @if(method_exists($logs, 'hasPages') && $logs->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="text-muted">
                                @if($logs->firstItem())
                                Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} logs
                                @else
                                {{ $logs->total() }} log(s) total
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="float-right">
                                {{ $logs->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function switchView(view) {
    if (view === 'list') {
        $('#listView').show();
        $('#timelineView').hide();
        $('#viewList').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
        $('#viewTimeline').removeClass('active').removeClass('btn-primary').addClass('btn-outline-secondary');
        localStorage.setItem('activityLogsView', 'list');
    } else {
        $('#listView').hide();
        $('#timelineView').show();
        $('#viewList').removeClass('active').removeClass('btn-primary').addClass('btn-outline-secondary');
        $('#viewTimeline').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
        localStorage.setItem('activityLogsView', 'timeline');
    }
}

$(document).ready(function() {
    const savedView = localStorage.getItem('activityLogsView') || 'list';
    switchView(savedView);
});

function exportLogs() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = '{{ route("activity-logs.export") }}?' + params.toString();
}

function refreshPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}
</script>
@endpush

