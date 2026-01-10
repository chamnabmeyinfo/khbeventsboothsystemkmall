@extends('layouts.adminlte')

@section('title', 'Users Management')
@section('page-title', 'Users Management')
@section('breadcrumb', 'Staff Management / Users')

@push('styles')
<style>
    /* Modern Glassmorphism KPI Cards */
    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .kpi-card:hover::before {
        opacity: 1;
    }

    .kpi-card.primary::before { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success::before { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .kpi-card.danger::before { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); }
    .kpi-card.info::before { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }

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
    .kpi-card.danger .kpi-icon { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); }
    .kpi-card.info .kpi-icon { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }

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

    .user-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        border-left: 4px solid;
        cursor: pointer;
    }
    .user-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }
    .user-card.admin { border-left-color: #dc3545; }
    .user-card.sale { border-left-color: #6c757d; }
    
    .table-row-hover {
        transition: all 0.2s;
    }
    .table-row-hover:hover {
        background-color: #f8f9fc;
        transform: translateX(4px);
    }
    
    .filter-bar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .status-toggle {
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="kpi-label">Total Users</div>
                    <div class="kpi-value">{{ number_format(\App\Models\User::count()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="kpi-label">Active Users</div>
                    <div class="kpi-value">{{ number_format(\App\Models\User::where('status', 1)->count()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card kpi-card danger">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="kpi-label">Admins</div>
                    <div class="kpi-value">{{ number_format(\App\Models\User::where('type', 1)->count()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="kpi-label">Sales Staff</div>
                    <div class="kpi-value">{{ number_format(\App\Models\User::where('type', 2)->count()) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>New User
                        </a>
                        <button type="button" class="btn btn-info" onclick="refreshPage()">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary active" onclick="switchView('table')" id="viewTable">
                            <i class="fas fa-table mr-1"></i>Table
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="switchView('cards')" id="viewCards">
                            <i class="fas fa-th-large mr-1"></i>Cards
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Search and Filter -->
    <div class="filter-bar">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 style="font-weight: 600; color: #2d3748;">
                <i class="fas fa-filter mr-2 text-primary"></i>Search & Filters
            </h3>
        </div>
        </div>
        <div id="filterSection">
            <form method="GET" action="{{ route('users.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label><i class="fas fa-search mr-1"></i>Search</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search by username or role..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label><i class="fas fa-user-tag mr-1"></i>Type</label>
                        <select name="type" class="form-control">
                            <option value="">All Types</option>
                            <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Sale</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label><i class="fas fa-user-shield mr-1"></i>Role</label>
                        <select name="role_id" class="form-control">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                            <option value="0" {{ request('role_id') === '0' ? 'selected' : '' }}>No Role</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label><i class="fas fa-toggle-on mr-1"></i>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
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
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times mr-1"></i>Clear Filters
                        </a>
                        @if(request()->hasAny(['search', 'type', 'role_id', 'status']))
                        <span class="badge badge-info ml-2">
                            {{ $users->total() }} result(s) found
                        </span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table View -->
    <div id="tableView" class="view-content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-2"></i>All Users</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">{{ $users->total() }} Total</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="selectAllUsers" class="form-check-input">
                                </th>
                                <th style="width: 80px;">ID</th>
                                <th>Username</th>
                                <th style="width: 120px;">Type</th>
                                <th style="width: 150px;">Role</th>
                                <th style="width: 120px;">Status</th>
                                <th style="width: 150px;">Activity</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr class="table-row-hover">
                                <td>
                                    <input type="checkbox" class="form-check-input user-checkbox" value="{{ $user->id }}">
                                </td>
                                <td>
                                    <strong class="text-primary">#{{ $user->id }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <x-avatar 
                                                :avatar="$user->avatar" 
                                                :name="$user->username" 
                                                :size="'sm'" 
                                                :type="$user->isAdmin() ? 'admin' : 'user'"
                                                :shape="'circle'"
                                            />
                                        </div>
                                        <div>
                                            <strong>{{ $user->username }}</strong>
                                            @php
                                                try {
                                                    $bookingCount = $user->books()->count();
                                                    if ($bookingCount > 0) {
                                                        echo '<br><small class="text-muted"><i class="fas fa-calendar-check mr-1"></i>' . $bookingCount . ' booking(s)</small>';
                                                    }
                                                } catch (\Exception $e) {
                                                    // Skip if relationship fails
                                                }
                                            @endphp
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge badge-danger">
                                            <i class="fas fa-shield-alt mr-1"></i>Admin
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-user-tie mr-1"></i>Sale
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role)
                                        <span class="badge badge-info">
                                            <i class="fas fa-user-shield mr-1"></i>{{ $user->role->name }}
                                        </span>
                                        @if($user->role->permissions->count() > 0)
                                        <br><small class="text-muted">
                                            {{ $user->role->permissions->count() }} permission(s)
                                        </small>
                                        @endif
                                    @else
                                        <span class="badge badge-light">
                                            <i class="fas fa-minus-circle mr-1"></i>No Role
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->isActive())
                                        <span class="badge badge-success status-toggle" onclick="toggleUserStatus({{ $user->id }}, {{ $user->status }})" style="cursor: pointer;" title="Click to deactivate">
                                            <i class="fas fa-check-circle mr-1"></i>Active
                                        </span>
                                    @else
                                        <span class="badge badge-warning status-toggle" onclick="toggleUserStatus({{ $user->id }}, {{ $user->status }})" style="cursor: pointer;" title="Click to activate">
                                            <i class="fas fa-times-circle mr-1"></i>Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        try {
                                            $boothCount = $user->booths()->count();
                                            $bookingCount = $user->books()->count();
                                        } catch (\Exception $e) {
                                            $boothCount = 0;
                                            $bookingCount = 0;
                                        }
                                    @endphp
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-cube mr-1"></i>{{ $boothCount }} booth(s)
                                        </small>
                                    </div>
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar mr-1"></i>{{ $bookingCount }} booking(s)
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin() && $user->id != auth()->id())
                                        <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }}, '{{ $user->username }}')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-user-slash fa-3x mb-3"></i>
                                        <p class="mb-0">No users found</p>
                                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm mt-3">
                                            <i class="fas fa-plus mr-1"></i>Create First User
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($users, 'hasPages') && $users->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="text-muted">
                            @if($users->firstItem())
                            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                            @else
                            {{ $users->total() }} user(s) total
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Card View -->
    <div id="cardView" class="view-content" style="display: none;">
        <div class="row">
            @forelse($users as $user)
            @php
                $typeClass = $user->isAdmin() ? 'admin' : 'sale';
            @endphp
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card user-card {{ $typeClass }}" onclick="window.location='{{ route('users.show', $user) }}'">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <x-avatar 
                                    :avatar="$user->avatar" 
                                    :name="$user->username" 
                                    :size="'xs'" 
                                    :type="$user->isAdmin() ? 'admin' : 'user'"
                                    :shape="'circle'"
                                />
                                <span class="ml-2">{{ $user->username }}</span>
                            </h6>
                            @if($user->isAdmin())
                                <span class="badge badge-danger">Admin</span>
                            @else
                                <span class="badge badge-secondary">Sale</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-hashtag text-muted mr-2"></i>
                                <strong>ID: #{{ $user->id }}</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-user-shield text-muted mr-2"></i>
                                @if($user->role)
                                    <span class="badge badge-info">{{ $user->role->name }}</span>
                                    <small class="text-muted ml-2">{{ $user->role->permissions->count() }} permissions</small>
                                @else
                                    <span class="badge badge-light">No Role</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-toggle-on text-muted mr-2"></i>
                                @if($user->isActive())
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-warning">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            @php
                                try {
                                    $boothCount = $user->booths()->count();
                                    $bookingCount = $user->books()->count();
                                } catch (\Exception $e) {
                                    $boothCount = 0;
                                    $bookingCount = 0;
                                }
                            @endphp
                            <small class="text-muted">
                                <i class="fas fa-cube mr-1"></i>{{ $boothCount }} booths
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-calendar mr-1"></i>{{ $bookingCount }} bookings
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-info" onclick="event.stopPropagation()">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning" onclick="event.stopPropagation()">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            @if(auth()->user()->isAdmin() && $user->id != auth()->id())
                            <button type="button" class="btn btn-danger" onclick="event.stopPropagation(); deleteUser({{ $user->id }}, '{{ $user->username }}');">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">No users found</p>
                        <a href="{{ route('users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Create First User
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        @if(method_exists($users, 'hasPages') && $users->hasPages())
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="text-muted">
                                    @if($users->firstItem())
                                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                                    @else
                                    {{ $users->total() }} user(s) total
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="float-right">
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// View Toggle
function switchView(view) {
    if (view === 'table') {
        $('#tableView').show();
        $('#cardView').hide();
        $('#viewTable').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
        $('#viewCards').removeClass('active').removeClass('btn-primary').addClass('btn-outline-secondary');
        localStorage.setItem('usersView', 'table');
    } else {
        $('#tableView').hide();
        $('#cardView').show();
        $('#viewTable').removeClass('active').removeClass('btn-primary').addClass('btn-outline-secondary');
        $('#viewCards').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
        localStorage.setItem('usersView', 'cards');
    }
}

// Load saved view preference
$(document).ready(function() {
    const savedView = localStorage.getItem('usersView') || 'table';
    switchView(savedView);
});

// Toggle Filters
function toggleFilters() {
    $('#filterSection').slideToggle();
    const icon = $('#filterToggleIcon');
    icon.toggleClass('fa-chevron-down fa-chevron-up');
}

// Select All Checkboxes
$('#selectAllUsers').on('change', function() {
    $('.user-checkbox').prop('checked', $(this).prop('checked'));
});

// Toggle User Status
function toggleUserStatus(userId, currentStatus) {
    const newStatus = currentStatus == 1 ? 0 : 1;
    const statusText = newStatus == 1 ? 'activate' : 'deactivate';
    
    Swal.fire({
        title: statusText.charAt(0).toUpperCase() + statusText.slice(1) + ' User?',
        text: `Are you sure you want to ${statusText} this user?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: newStatus == 1 ? '#28a745' : '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${statusText} it!`,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch(`/users/${userId}/status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.status == 200) {
                    toastr.success('User status updated successfully');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error(data.message || 'Failed to update user status');
                }
            })
            .catch(error => {
                hideLoading();
                toastr.error('Error: ' + error.message);
            });
        }
    });
}

// Delete User
function deleteUser(id, username) {
    Swal.fire({
        title: 'Delete User?',
        text: `Are you sure you want to delete user "${username}"? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/users/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            
            fetch(`/users/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE'
                }
            })
            .then(response => {
                hideLoading();
                if (response.redirected) {
                    Swal.fire('Deleted!', 'User has been deleted.', 'success')
                        .then(() => {
                            window.location.href = response.url;
                        });
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.success) {
                    Swal.fire('Deleted!', data.message || 'User has been deleted.', 'success')
                        .then(() => {
                            location.reload();
                        });
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error!', 'An error occurred while deleting the user.', 'error');
                console.error('Error:', error);
            });
        }
    });
}

// Refresh Page
function refreshPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}
</script>
@endpush
