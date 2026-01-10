@extends('layouts.adminlte')

@section('title', 'Roles Management')
@section('page-title', 'Roles Management')
@section('breadcrumb', 'Staff Management / Roles')

@push('styles')
<style>
    .role-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        border-left: 4px solid #667eea;
        height: 100%;
    }

    .role-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .role-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 16px;
    }

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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="kpi-label">Total Roles</div>
                    <div class="kpi-value">{{ number_format($stats['total_roles'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="kpi-label">Active Roles</div>
                    <div class="kpi-value">{{ number_format($stats['active_roles'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="kpi-label">Users with Roles</div>
                    <div class="kpi-value">{{ number_format($stats['total_users_with_roles'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="{{ route('roles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create Role
                    </a>
                    <button type="button" class="btn btn-info" onclick="refreshPage()">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary active" onclick="switchView('cards')" id="viewCards">
                            <i class="fas fa-th-large mr-1"></i>Cards
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="switchView('table')" id="viewTable">
                            <i class="fas fa-table mr-1"></i>Table
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('roles.index') }}" class="row">
                <div class="col-md-4 mb-3">
                    <label><i class="fas fa-search mr-1"></i>Search</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search roles..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-toggle-on mr-1"></i>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card View -->
    <div id="cardView" class="view-content">
        <div class="row">
            @forelse($roles as $role)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card role-card">
                    <div class="card-body" style="padding: 24px;">
                        <div class="role-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h5 class="mb-2" style="font-weight: 700;">{{ $role->name }}</h5>
                        @if($role->description)
                        <p class="text-muted mb-3 small">{{ \Illuminate\Support\Str::limit($role->description, 80) }}</p>
                        @endif
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Users:</span>
                                <span class="badge badge-info">{{ $role->users->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Permissions:</span>
                                <span class="badge badge-success">{{ $role->permissions->count() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Status:</span>
                                @if($role->is_active)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle mr-1"></i>Active
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-times-circle mr-1"></i>Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('roles.show', $role) }}" class="btn btn-info">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <button type="button" class="btn btn-danger" onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">No roles found</p>
                        <a href="{{ route('roles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Create First Role
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Table View -->
    <div id="tableView" class="view-content" style="display: none;">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Users</th>
                                <th>Permissions</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                            <tr>
                                <td>
                                    <strong>{{ $role->name }}</strong>
                                    @if($role->description)
                                    <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($role->description, 50) }}</small>
                                    @endif
                                </td>
                                <td><code>{{ $role->slug }}</code></td>
                                <td>
                                    <span class="badge badge-info">{{ $role->users->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-success">{{ $role->permissions->count() }}</span>
                                </td>
                                <td>
                                    @if($role->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('roles.show', $role) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger" onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-user-shield fa-3x mb-3"></i>
                                        <p class="mb-0">No roles found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($roles, 'hasPages') && $roles->hasPages())
            <div class="card-footer">
                <div class="float-right">
                    {{ $roles->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchView(view) {
    if (view === 'cards') {
        $('#cardView').show();
        $('#tableView').hide();
        $('#viewCards').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
        $('#viewTable').removeClass('active').removeClass('btn-primary').addClass('btn-outline-secondary');
        localStorage.setItem('rolesView', 'cards');
    } else {
        $('#cardView').hide();
        $('#tableView').show();
        $('#viewCards').removeClass('active').removeClass('btn-primary').addClass('btn-outline-secondary');
        $('#viewTable').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
        localStorage.setItem('rolesView', 'table');
    }
}

$(document).ready(function() {
    const savedView = localStorage.getItem('rolesView') || 'cards';
    switchView(savedView);
});

function deleteRole(id, name) {
    Swal.fire({
        title: 'Delete Role?',
        text: `Are you sure you want to delete role "${name}"? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch(`/roles/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE'
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json();
                }
            })
            .then(data => {
                hideLoading();
                if (data && data.success) {
                    Swal.fire('Deleted!', data.message || 'Role has been deleted.', 'success')
                        .then(() => {
                            location.reload();
                        });
                } else if (data && data.error) {
                    Swal.fire('Error!', data.error, 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error!', 'An error occurred while deleting the role.', 'error');
                console.error('Error:', error);
            });
        }
    });
}

function refreshPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}
</script>
@endpush
