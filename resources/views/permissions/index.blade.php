@extends('layouts.adminlte')

@section('title', 'Permissions Management')
@section('page-title', 'Permissions Management')
@section('breadcrumb', 'Staff Management / Permissions')

@push('styles')
<style>
    .module-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        margin-bottom: 24px;
    }

    .module-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px 24px;
        border-radius: 16px 16px 0 0;
    }

    .permission-item {
        padding: 12px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        transition: background 0.2s;
    }

    .permission-item:last-child {
        border-bottom: none;
    }

    .permission-item:hover {
        background: rgba(0,0,0,0.02);
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
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="kpi-label">Total Permissions</div>
                    <div class="kpi-value">{{ number_format($stats['total_permissions'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="kpi-label">Active Permissions</div>
                    <div class="kpi-value">{{ number_format($stats['active_permissions'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="kpi-label">Modules</div>
                    <div class="kpi-value">{{ number_format($stats['modules_count'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create Permission
                    </a>
                    <button type="button" class="btn btn-info" onclick="refreshPage()">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('permissions.index') }}" class="row">
                <div class="col-md-4 mb-3">
                    <label><i class="fas fa-search mr-1"></i>Search</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search permissions..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-folder mr-1"></i>Module</label>
                    <select name="module" class="form-control">
                        <option value="">All Modules</option>
                        @foreach($modules ?? [] as $module)
                            <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                {{ $module }}
                            </option>
                        @endforeach
                    </select>
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

    <!-- Permissions by Module -->
    @forelse($permissions as $module => $modulePermissions)
    <div class="module-card">
        <div class="module-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-folder mr-2"></i>{{ ucfirst($module ?: 'General') }}
                    <span class="badge badge-light ml-2">{{ $modulePermissions->count() }}</span>
                </h5>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modulePermissions as $permission)
                        <tr class="permission-item">
                            <td><strong>{{ $permission->name }}</strong></td>
                            <td><code>{{ $permission->slug }}</code></td>
                            <td>{{ $permission->description ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-info">{{ $permission->roles->count() }}</span>
                            </td>
                            <td>
                                @if($permission->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('permissions.show', $permission) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="deletePermission({{ $permission->id }}, '{{ $permission->name }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-key fa-3x text-muted mb-3"></i>
            <p class="text-muted mb-3">No permissions found</p>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i>Create First Permission
            </a>
        </div>
    </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
function deletePermission(id, name) {
    Swal.fire({
        title: 'Delete Permission?',
        text: `Are you sure you want to delete permission "${name}"? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch(`/permissions/${id}`, {
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
                    Swal.fire('Deleted!', data.message || 'Permission has been deleted.', 'success')
                        .then(() => {
                            location.reload();
                        });
                } else if (data && data.error) {
                    Swal.fire('Error!', data.error, 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error!', 'An error occurred while deleting the permission.', 'error');
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

