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
                        <button type="button" class="btn btn-primary" onclick="showCreateUserModal()">
                            <i class="fas fa-plus mr-1"></i>New User
                        </button>
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
                    <span class="badge badge-primary">{{ $total ?? count($users) }} Total</span>
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
                        <tbody id="tableUsersBody">
                            @forelse($users as $user)
                                @include('users.partials.table-row', ['user' => $user])
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-user-slash fa-3x mb-3"></i>
                                        <p class="mb-0">No users found</p>
                                        <button type="button" class="btn btn-primary btn-sm mt-3" onclick="showCreateUserModal()">
                                            <i class="fas fa-plus mr-1"></i>Create First User
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Lazy Loading Trigger -->
                <div id="usersLazyLoadTrigger" style="height: 20px; margin: 10px 0;"></div>
                <!-- Lazy Loading Spinner -->
                <div id="usersLazyLoadSpinner" class="text-center py-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span class="ml-2 text-muted">Loading more users...</span>
                </div>
                <!-- Lazy Loading End -->
                <div id="usersLazyLoadEnd" class="text-center py-3" style="display: none;">
                    <span class="text-muted">No more users to load</span>
                </div>
            </div>
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
                        <button type="button" class="btn btn-primary" onclick="showCreateUserModal()">
                            <i class="fas fa-plus mr-1"></i>Create First User
                        </button>
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

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 800px;">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 20px 20px 0 0; padding: 24px 32px;">
                <h5 class="modal-title" id="createUserModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-user-plus mr-2"></i>Create New User
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 1.5rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createUserForm" method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body" style="padding: 32px; max-height: calc(100vh - 200px); overflow-y: auto;">
                    <div id="createUserError" class="alert alert-danger" style="display: none; border-radius: 12px;"></div>
                    
                    <!-- Basic Information -->
                    <div class="form-group mb-4">
                        <h6 style="font-weight: 600; margin-bottom: 16px; color: #495057;"><i class="fas fa-user mr-2 text-primary"></i>Basic Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_user_username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_user_username" name="username" required placeholder="Enter username" style="border-radius: 8px;">
                                <small class="form-text text-muted">Username must be unique</small>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_user_type" class="form-label">User Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_user_type" name="type" required style="border-radius: 8px;">
                                    <option value="2" selected>Sale Staff</option>
                                    <option value="1">Administrator</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Role & Status -->
                    <div class="form-group mb-4">
                        <h6 style="font-weight: 600; margin-bottom: 16px; color: #495057;"><i class="fas fa-user-shield mr-2 text-primary"></i>Role & Status</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_user_role_id" class="form-label">Assign Role</label>
                                <select class="form-control" id="modal_user_role_id" name="role_id" style="border-radius: 8px;">
                                    <option value="">No Role Assigned</option>
                                    @foreach(\App\Models\Role::where('is_active', true)->orderBy('name')->get() as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->name }}
                                        @if($role->permissions->count() > 0)
                                            ({{ $role->permissions->count() }} permissions)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Optional: Assign a role for permission-based access control</small>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_user_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_user_status" name="status" required style="border-radius: 8px;">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group mb-0">
                        <h6 style="font-weight: 600; margin-bottom: 16px; color: #495057;"><i class="fas fa-key mr-2 text-primary"></i>Password</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_user_password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="modal_user_password" name="password" required placeholder="Enter password" style="border-radius: 8px;">
                                <small class="form-text text-muted">Minimum 6 characters</small>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_user_password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="modal_user_password_confirmation" name="password_confirmation" required placeholder="Confirm password" style="border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 20px 32px; border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 12px; padding: 10px 24px;">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="createUserSubmitBtn" style="border-radius: 12px; padding: 10px 24px;">
                        <i class="fas fa-save mr-1"></i>Create User
                    </button>
                </div>
            </form>
        </div>
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

// Show Create User Modal
function showCreateUserModal() {
    $('#createUserModal').modal('show');
    $('#createUserForm')[0].reset();
    $('#createUserError').hide();
    $('#modal_user_type').val('2');
    $('#modal_user_status').val('1');
}

// Handle Create User Form Submission
$(document).ready(function() {
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#createUserSubmitBtn');
        const errorDiv = $('#createUserError');
        const originalText = submitBtn.html();
        
        errorDiv.hide();
        
        // Validate password match
        const password = $('#modal_user_password').val();
        const passwordConfirmation = $('#modal_user_password_confirmation').val();
        if (password !== passwordConfirmation) {
            errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i>Password and confirmation password do not match.').show();
            return;
        }
        
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }
        
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    $('#createUserModal').modal('hide');
                    form[0].reset();
                    errorDiv.hide();
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'User created successfully');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'User created successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(response.message || 'User created successfully');
                    }
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the user.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const firstError = Object.values(errors)[0];
                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                }
                errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i>' + errorMessage).show();
                errorDiv[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
    
    // Reset form when modal is closed
    $('#createUserModal').on('hidden.bs.modal', function() {
        $('#createUserForm')[0].reset();
        $('#createUserError').hide();
        $('#modal_user_type').val('2');
        $('#modal_user_status').val('1');
    });
    
    // Lazy Loading Variables
    let usersCurrentPage = 1;
    let usersIsLoading = false;
    let usersHasMoreData = {{ $total > count($users) ? 'true' : 'false' }};
    let usersFilterParams = {
        search: '{{ request('search') }}',
        type: '{{ request('type') }}',
        status: '{{ request('status') }}',
        role_id: '{{ request('role_id') }}'
    };
    
    // Lazy Loading Observer
    let usersLazyLoadObserver = null;
    
    // Initialize Lazy Loading
    function initUsersLazyLoading() {
        // Disconnect existing observer if any
        if (usersLazyLoadObserver) {
            usersLazyLoadObserver.disconnect();
        }
        
        // Use Intersection Observer API for better performance
        const trigger = document.getElementById('usersLazyLoadTrigger');
        
        if (!trigger) return;
        
        const observerOptions = {
            root: null,
            rootMargin: '200px',
            threshold: 0.1
        };
        
        usersLazyLoadObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && usersHasMoreData && !usersIsLoading) {
                    loadMoreUsers();
                }
            });
        }, observerOptions);
        
        usersLazyLoadObserver.observe(trigger);
    }
    
    // Load More Users
    function loadMoreUsers() {
        if (usersIsLoading || !usersHasMoreData) return;
        
        usersIsLoading = true;
        usersCurrentPage++;
        
        $('#usersLazyLoadSpinner').show();
        
        // Build params exactly like initial load
        const params = new URLSearchParams({
            page: usersCurrentPage
        });
        
        // Add filter params if they exist
        if (usersFilterParams.search) params.append('search', usersFilterParams.search);
        if (usersFilterParams.type) params.append('type', usersFilterParams.type);
        if (usersFilterParams.status) params.append('status', usersFilterParams.status);
        if (usersFilterParams.role_id) params.append('role_id', usersFilterParams.role_id);
        
        fetch('{{ route("users.index") }}?' + params.toString(), {
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
            if (data.success && data.html) {
                // Append new rows to table body
                $('#tableUsersBody').append(data.html);
                
                usersHasMoreData = data.hasMore !== false;
                
                if (!data.hasMore) {
                    $('#usersLazyLoadEnd').show();
                    $('#usersLazyLoadSpinner').hide();
                } else {
                    // Re-initialize lazy loading observer for new content after a brief delay
                    setTimeout(function() {
                        initUsersLazyLoading();
                    }, 100);
                }
            } else {
                usersHasMoreData = false;
                $('#usersLazyLoadSpinner').hide();
            }
        })
        .catch(error => {
            console.error('Error loading more users:', error);
            usersHasMoreData = false;
            $('#usersLazyLoadSpinner').hide();
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to load more users. Please try again.');
            }
        })
        .finally(() => {
            usersIsLoading = false;
            $('#usersLazyLoadSpinner').hide();
        });
    }
    
    // Initialize lazy loading on page load
    $(document).ready(function() {
        initUsersLazyLoading();
    });
});
</script>
@endpush

