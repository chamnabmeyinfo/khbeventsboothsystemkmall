@extends('layouts.adminlte')

@section('title', 'Affiliate Management')
@section('page-title', 'Affiliate Management')
@section('breadcrumb', 'Sales / Affiliates')

@push('styles')
<style>
    .affiliate-table-wrapper {
        background: white;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-modern {
        margin-bottom: 0;
    }

    .table-modern thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        padding: 0.75rem 1rem;
        white-space: nowrap;
    }

    .table-modern tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.875rem;
    }

    .table-modern tbody tr {
        transition: all 0.2s;
    }

    .table-modern tbody tr:hover {
        background: #f8f9fa;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        margin-right: 0.75rem;
    }

    .user-info {
        display: flex;
        align-items: center;
    }

    .user-name {
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.125rem;
    }

    .user-role {
        font-size: 0.75rem;
        color: #6c757d;
    }

    .stat-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8125rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-primary-light {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
    }

    .badge-success-light {
        background: rgba(67, 233, 123, 0.1);
        color: #28a745;
    }

    .badge-info-light {
        background: rgba(79, 172, 254, 0.1);
        color: #17a2b8;
    }

    .badge-warning-light {
        background: rgba(250, 112, 154, 0.1);
        color: #ffc107;
    }

    .revenue-amount {
        font-weight: 700;
        color: #28a745;
        font-size: 0.9375rem;
    }

    .compact-filters {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .compact-filters .form-control,
    .compact-filters .form-select {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        border: 1px solid #dee2e6;
        border-radius: 6px;
    }

    .compact-filters .form-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.375rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .page-header-compact {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e9ecef;
    }

    .page-title-compact {
        font-size: 1.5rem;
        font-weight: 700;
        color: #212529;
        margin: 0;
    }

    .activity-toggle {
        cursor: pointer;
        color: #667eea;
        font-size: 0.875rem;
        transition: color 0.2s;
    }

    .activity-toggle:hover {
        color: #764ba2;
    }

    .activity-row {
        display: none;
    }

    .activity-row.show {
        display: table-row;
    }

    .activity-content {
        background: #f8f9fa;
        padding: 1rem;
    }

    .activity-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
        font-size: 0.8125rem;
        border-bottom: 1px solid #e9ecef;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 0.75rem;
    }

    .activity-icon.booking {
        background: rgba(67, 233, 123, 0.1);
        color: #28a745;
    }

    .activity-icon.click {
        background: rgba(79, 172, 254, 0.1);
        color: #17a2b8;
    }

    .empty-state-compact {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }

    .empty-state-compact i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .btn-sm-modern {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
        border-radius: 6px;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Compact Header -->
    <div class="page-header-compact">
        <div>
            <h2 class="page-title-compact">
                <i class="fas fa-chart-line mr-2"></i>Affiliate Management
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.875rem;">Track sales performance and commission benefits</p>
        </div>
        <div>
            <a href="{{ route('affiliates.benefits.index') }}" class="btn btn-sm btn-info btn-sm-modern me-2">
                <i class="fas fa-gift mr-1"></i>Manage Benefits
            </a>
            <a href="{{ route('affiliates.export', request()->all()) }}" class="btn btn-sm btn-success btn-sm-modern">
                <i class="fas fa-file-csv mr-1"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Compact Filters -->
    <div class="compact-filters">
        <form method="GET" action="{{ route('affiliates.index') }}" id="filterForm">
            <div class="row g-2">
                <div class="col-md-2">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Username..." value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sales Person</label>
                    <select name="user_id" class="form-select">
                        <option value="">All</option>
                        @foreach($allUsers as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                {{ $user->username }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Floor Plan</label>
                    <select name="floor_plan_id" class="form-select">
                        <option value="">All</option>
                        @foreach($floorPlans as $fp)
                            <option value="{{ $fp->id }}" {{ $floorPlanId == $fp->id ? 'selected' : '' }}>
                                {{ $fp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm-modern w-100 me-2">
                        <i class="fas fa-filter mr-1"></i>Filter
                    </button>
                    <a href="{{ route('affiliates.index') }}" class="btn btn-secondary btn-sm-modern">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Modern Table -->
    <div class="affiliate-table-wrapper">
        <table class="table table-modern">
            <thead>
                <tr>
                    <th>Sales Person</th>
                    <th class="text-center">Bookings</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-center">Clients</th>
                    <th class="text-center">Clicks</th>
                    <th class="text-center">Conversion</th>
                    <th class="text-right">Avg/Booking</th>
                    <th class="text-right">Total Benefits</th>
                    <th class="text-center">Last Activity</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $data)
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ strtoupper(substr($data['user']->username, 0, 1)) }}
                            </div>
                            <div>
                                <div class="user-name">{{ $data['user']->username }}</div>
                                <div class="user-role">
                                    @if($data['user']->isAdmin())
                                        <i class="fas fa-shield-alt mr-1"></i>Admin
                                    @else
                                        <i class="fas fa-user-tie mr-1"></i>Sales
                                    @endif
                                    @if($data['user']->role)
                                        • {{ $data['user']->role->name }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="stat-badge badge-primary-light">
                            {{ number_format($data['total_bookings']) }}
                        </span>
                    </td>
                    <td class="text-right">
                        <span class="revenue-amount">${{ number_format($data['total_revenue'], 2) }}</span>
                    </td>
                    <td class="text-center">
                        <span class="stat-badge badge-info-light">
                            {{ number_format($data['unique_clients']) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="stat-badge badge-warning-light">
                            {{ number_format($data['total_clicks'] ?? 0) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="stat-badge badge-success-light">
                            {{ number_format($data['conversion_rate'] ?? 0, 1) }}%
                        </span>
                    </td>
                    <td class="text-right">
                        <strong>${{ number_format($data['avg_booking_value'], 2) }}</strong>
                    </td>
                    <td class="text-right">
                        <strong class="text-success">${{ number_format($data['total_benefits']['total'] ?? 0, 2) }}</strong>
                    </td>
                    <td class="text-center">
                        @if($data['last_booking_at'])
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($data['last_booking_at'])->format('M d, Y') }}
                            </small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('affiliates.show', $data['user']->id) }}" 
                               class="btn btn-primary btn-sm-modern" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(isset($data['activities']) && $data['activities']->count() > 0)
                            <button type="button" 
                                    class="btn btn-outline-secondary btn-sm-modern activity-toggle" 
                                    onclick="toggleActivity({{ $data['user']->id }})"
                                    title="View Activity">
                                <i class="fas fa-history"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @if(isset($data['activities']) && $data['activities']->count() > 0)
                <tr class="activity-row" id="activity-{{ $data['user']->id }}">
                    <td colspan="9" class="activity-content">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong style="font-size: 0.875rem; color: #495057;">
                                <i class="fas fa-history mr-1"></i>Recent Activity (Last 10)
                            </strong>
                            <button type="button" class="btn btn-sm btn-link p-0" onclick="toggleActivity({{ $data['user']->id }})">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        @foreach($data['activities']->take(10) as $activity)
                        <div class="activity-item">
                            <div class="activity-icon {{ $activity['type'] }}">
                                @if($activity['type'] === 'booking')
                                    <i class="fas fa-shopping-cart"></i>
                                @else
                                    <i class="fas fa-mouse-pointer"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div>
                                    @if($activity['type'] === 'booking')
                                        <strong>Booking</strong> - 
                                        @if($activity['booking']->client)
                                            {{ $activity['booking']->client->company ?? $activity['booking']->client->name }}
                                        @else
                                            Client #{{ $activity['booking']->clientid }}
                                        @endif
                                        <span class="text-success ms-2">${{ number_format($activity['revenue'], 2) }}</span>
                                    @else
                                        <strong>Link Click</strong> - 
                                        @if($activity['click']->floorPlan)
                                            {{ $activity['click']->floorPlan->name }}
                                        @else
                                            Floor Plan #{{ $activity['click']->floor_plan_id }}
                                        @endif
                                        <span class="text-muted ms-2">{{ $activity['click']->ip_address }}</span>
                                    @endif
                                </div>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($activity['date'])->format('M d, Y • g:i A') }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                        <div class="text-center mt-2">
                            <a href="{{ route('affiliates.show', $data['user']->id) }}" class="btn btn-sm btn-outline-primary btn-sm-modern">
                                View All Activities <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endif
                @empty
                        <tr>
                            <td colspan="10" class="empty-state-compact">
                        <i class="fas fa-users"></i>
                        <h6 class="mt-2">No Affiliate Data Found</h6>
                        <p class="mb-0" style="font-size: 0.875rem;">No affiliate bookings found with the current filters.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleActivity(userId) {
        const row = document.getElementById('activity-' + userId);
        if (row) {
            row.classList.toggle('show');
        }
    }
</script>
@endpush
