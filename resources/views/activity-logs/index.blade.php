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

    <!-- Activity Info Modal -->
    <div class="modal fade" id="activityInfoModal" tabindex="-1" role="dialog" aria-labelledby="activityInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width: 900px;">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 16px 16px 0 0; padding: 20px 30px;">
                    <h5 class="modal-title" id="activityInfoModalLabel" style="font-weight: 700; font-size: 1.25rem;">
                        <i class="fas fa-history mr-2"></i>Activity Log Details
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 1.5rem;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 30px; max-height: 70vh; overflow-y: auto;">
                    <div id="activityInfoContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading activity information...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 20px 30px; border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 10px 24px;">
                        <i class="fas fa-times mr-1"></i>Close
                    </button>
                    <a href="#" id="activityViewFullLink" class="btn btn-primary" style="border-radius: 8px; padding: 10px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fas fa-external-link-alt mr-1"></i>View Full Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- List View -->
    <div id="listView" class="view-content">
        @forelse($logs as $log)
        <div class="log-item {{ strtolower($log->action) }}" onclick="showActivityInfo({{ $log->id }})" style="cursor: pointer;">
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
// Activity Info Modal Function
function showActivityInfo(logId) {
    $('#activityInfoModal').modal('show');
    $('#activityInfoContent').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div><p class="mt-3 text-muted">Loading activity information...</p></div>');
    
    // Fetch activity log details via AJAX
    fetch(`{{ url('activity-logs') }}/${logId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.log) {
            const log = data.log;
            let html = `
                <div class="row">
                    <!-- Activity Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card" style="border-left: 4px solid #667eea; border-radius: 12px;">
                            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0;">
                                <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i>Activity Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted"><i class="fas fa-hashtag mr-2"></i>Log ID:</span>
                                        <strong class="text-primary">#${log.id}</strong>
                                    </div>
                                </div>
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fas fa-bolt mr-2"></i>Action:</span>
                                        <span class="badge ${log.action == 'created' ? 'badge-success' : (log.action == 'updated' ? 'badge-info' : (log.action == 'deleted' ? 'badge-danger' : 'badge-secondary'))}">
                                            ${log.action.charAt(0).toUpperCase() + log.action.slice(1)}
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted"><i class="fas fa-calendar mr-2"></i>Date:</span>
                                        <strong>${log.created_at_date}</strong>
                                    </div>
                                    <small class="text-muted ml-4">${log.created_at_time}</small>
                                </div>
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fas fa-folder mr-2"></i>Module:</span>
                                        <strong>${log.model_type || 'N/A'}</strong>
                                    </div>
                                </div>
                                ${log.model_id ? `
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fas fa-key mr-2"></i>Model ID:</span>
                                        <strong>#${log.model_id}</strong>
                                    </div>
                                </div>
                                ` : ''}
                                <div class="mb-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span class="text-muted"><i class="fas fa-align-left mr-2"></i>Description:</span>
                                        <span class="text-right" style="max-width: 60%;">${log.description || 'No description'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card" style="border-left: 4px solid #48bb78; border-radius: 12px;">
                            <div class="card-header" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; border-radius: 12px 12px 0 0;">
                                <h6 class="mb-0"><i class="fas fa-user mr-2"></i>User Information</h6>
                            </div>
                            <div class="card-body">
            `;
            
            if (log.user) {
                html += `
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fas fa-user mr-2"></i>Username:</span>
                                        <strong>${log.user.username}</strong>
                                    </div>
                                </div>
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fas fa-shield-alt mr-2"></i>Role:</span>
                                        <span class="badge ${log.user.is_admin ? 'badge-danger' : 'badge-info'}">
                                            ${log.user.is_admin ? 'Admin' : 'User'}
                                        </span>
                                    </div>
                                </div>
                `;
            } else {
                html += `
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-server fa-2x mb-2"></i>
                                    <p>System Activity</p>
                                </div>
                `;
            }
            
            html += `
                                ${log.ip_address ? `
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fas fa-network-wired mr-2"></i>IP Address:</span>
                                        <code>${log.ip_address}</code>
                                    </div>
                                </div>
                                ` : ''}
                                ${log.route ? `
                                <div class="mb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fas fa-route mr-2"></i>Route:</span>
                                        <code style="font-size: 0.75rem;">${log.route}</code>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Show old_values and new_values if they exist
            if (log.old_values && Object.keys(log.old_values).length > 0 || log.new_values && Object.keys(log.new_values).length > 0) {
                html += `
                <div class="row">
                    <div class="col-12">
                        <div class="card" style="border-left: 4px solid #4299e1; border-radius: 12px;">
                            <div class="card-header" style="background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); color: white; border-radius: 12px 12px 0 0;">
                                <h6 class="mb-0"><i class="fas fa-exchange-alt mr-2"></i>Changes</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                `;
                
                if (log.old_values && Object.keys(log.old_values).length > 0) {
                    html += `
                                    <div class="col-md-6">
                                        <h6 class="text-danger mb-3"><i class="fas fa-arrow-left mr-1"></i>Old Values</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Field</th>
                                                        <th>Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                    `;
                    for (const [key, value] of Object.entries(log.old_values)) {
                        html += `
                                                    <tr>
                                                        <td><strong>${key}</strong></td>
                                                        <td>${value !== null && value !== undefined ? (typeof value === 'object' ? JSON.stringify(value) : String(value)) : '<em>null</em>'}</td>
                                                    </tr>
                        `;
                    }
                    html += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                    `;
                }
                
                if (log.new_values && Object.keys(log.new_values).length > 0) {
                    html += `
                                    <div class="col-md-6">
                                        <h6 class="text-success mb-3"><i class="fas fa-arrow-right mr-1"></i>New Values</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Field</th>
                                                        <th>Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                    `;
                    for (const [key, value] of Object.entries(log.new_values)) {
                        html += `
                                                    <tr>
                                                        <td><strong>${key}</strong></td>
                                                        <td>${value !== null && value !== undefined ? (typeof value === 'object' ? JSON.stringify(value) : String(value)) : '<em>null</em>'}</td>
                                                    </tr>
                        `;
                    }
                    html += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                    `;
                }
                
                html += `
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
            }
            
            if (log.user_agent) {
                html += `
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card" style="border-left: 4px solid #ed8936; border-radius: 12px;">
                            <div class="card-header" style="background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); color: white; border-radius: 12px 12px 0 0;">
                                <h6 class="mb-0"><i class="fas fa-desktop mr-2"></i>Technical Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <span class="text-muted"><i class="fas fa-info-circle mr-2"></i>User Agent:</span>
                                        <code style="font-size: 0.75rem; max-width: 70%; word-break: break-all;">${log.user_agent}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;
            }
            
            $('#activityInfoContent').html(html);
            $('#activityViewFullLink').attr('href', `{{ url('activity-logs') }}/${logId}`);
        } else {
            $('#activityInfoContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Failed to load activity information. Please try again.</div>');
        }
    })
    .catch(error => {
        console.error('Error loading activity info:', error);
        $('#activityInfoContent').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading activity information. Please try again.</div>');
    });
}

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

