@php
    $permissionsFlat = $permissions->flatten();
    $filteredTotal = $permissionsFlat->count();
@endphp

<div class="staff-access-tab-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <h2 class="h5 mb-0 text-secondary" id="staff-access-permissions-heading">Permissions catalog</h2>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('permissions.create') }}" class="action-btn action-btn-primary">
            <i class="fas fa-plus" aria-hidden="true"></i> New permission
        </a>
        <button type="button" class="action-btn action-btn-secondary" onclick="refreshStaffAccessPage()">
            <i class="fas fa-sync-alt text-tertiary" aria-hidden="true"></i> Refresh
        </button>
    </div>
</div>

<div class="kpi-wrapper">
    <div class="kpi-card-looker animate-slide-up delay-2">
        <div class="kpi-top">
            <div class="kpi-title">Total permissions</div>
            <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-key" aria-hidden="true"></i></div>
        </div>
        <div class="kpi-value-looker">{{ number_format($permissionStats['total_permissions'] ?? 0) }}</div>
        <div class="kpi-bottom trend-neutral">
            <i class="fas fa-fw fa-circle" style="font-size: 6px;" aria-hidden="true"></i> System-wide
        </div>
    </div>
    <div class="kpi-card-looker success animate-slide-up delay-3">
        <div class="kpi-top">
            <div class="kpi-title">Active</div>
            <div class="kpi-icon-wrapper success-icon"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
        </div>
        <div class="kpi-value-looker">{{ number_format($permissionStats['active_permissions'] ?? 0) }}</div>
        <div class="kpi-bottom trend-positive">
            <i class="fas fa-fw fa-check" aria-hidden="true"></i> In use
        </div>
    </div>
    <div class="kpi-card-looker purple animate-slide-up delay-4">
        <div class="kpi-top">
            <div class="kpi-title">Modules</div>
            <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-folder" aria-hidden="true"></i></div>
        </div>
        <div class="kpi-value-looker">{{ number_format($permissionStats['modules_count'] ?? 0) }}</div>
        <div class="kpi-bottom trend-neutral">
            <i class="fas fa-fw fa-layer-group" aria-hidden="true"></i> Distinct groups
        </div>
    </div>
</div>

<div class="canvas-panel mb-3 animate-slide-up delay-3" aria-labelledby="permissions-filter-heading">
    <div class="panel-header flex-wrap gap-2">
        <h3 class="panel-title mb-0 h6 text-body" id="permissions-filter-heading">
            <i class="fas fa-sliders-h" aria-hidden="true"></i> Refine list
        </h3>
        @if(request()->hasAny(['search', 'module', 'status']) && $activeTab === 'permissions')
            <span class="status-badge status-badge-blue">{{ number_format($filteredTotal) }} match(es)</span>
        @endif
    </div>
    <div class="px-3 px-md-4 pb-3">
        <form method="GET" action="{{ route('staff.access') }}" id="permissionsFilterForm" class="row g-3 align-items-end">
            <input type="hidden" name="tab" value="permissions">
            <div class="col-12 col-md-4 col-lg-4">
                <label class="form-label small text-secondary fw-semibold mb-1" for="permissions_filter_search">
                    <i class="fas fa-search text-primary" aria-hidden="true"></i> Search
                </label>
                <input type="text" name="search" id="permissions_filter_search" class="form-control rounded-3"
                       placeholder="Name, slug, or description…"
                       value="{{ request('search') }}"
                       autocomplete="off">
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label small text-secondary fw-semibold mb-1" for="permissions_filter_module">
                    <i class="fas fa-folder text-primary" aria-hidden="true"></i> Module
                </label>
                <select name="module" id="permissions_filter_module" class="form-select rounded-3">
                    <option value="">All modules</option>
                    @foreach($modules ?? [] as $module)
                        <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                            {{ $module }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label small text-secondary fw-semibold mb-1" for="permissions_filter_status">
                    <i class="fas fa-toggle-on text-primary" aria-hidden="true"></i> Status
                </label>
                <select name="status" id="permissions_filter_status" class="form-select rounded-3">
                    <option value="">All status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-12 col-lg-auto d-flex flex-wrap gap-2">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-check" aria-hidden="true"></i> <span class="d-none d-sm-inline">Apply</span>
                </button>
                <a href="{{ route('staff.access', ['tab' => 'permissions']) }}" class="action-btn action-btn-secondary">
                    <i class="fas fa-rotate-left text-tertiary" aria-hidden="true"></i> <span class="d-none d-sm-inline">Reset</span>
                </a>
            </div>
        </form>
    </div>
</div>

@forelse($permissions as $module => $modulePermissions)
<div class="canvas-panel permissions-module-panel mb-4 animate-slide-up delay-4">
    <div class="panel-header permissions-module-panel__header">
        <h3 class="panel-title mb-0 h6">
            <i class="fas fa-folder me-2" aria-hidden="true"></i>{{ ucfirst($module ?: 'General') }}
        </h3>
        <span class="status-badge">{{ $modulePermissions->count() }}</span>
    </div>
    <div class="permissions-table-scroll px-2 px-md-3 pb-2 pt-2">
        <div class="looker-table-wrapper">
            <table class="looker-table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th class="d-none d-md-table-cell">Description</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modulePermissions as $permission)
                    <tr>
                        <td><strong>{{ $permission->name }}</strong></td>
                        <td><code class="small">{{ $permission->slug }}</code></td>
                        <td class="d-none d-md-table-cell text-muted small">{{ \Illuminate\Support\Str::limit($permission->description ?? '—', 80) }}</td>
                        <td><span class="status-badge status-badge-blue">{{ $permission->roles->count() }}</span></td>
                        <td>
                            @if($permission->is_active)
                                <span class="status-badge status-badge-green">Active</span>
                            @else
                                <span class="status-badge status-badge-neutral">Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('permissions.show', $permission) }}" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </a>
                                <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit" aria-hidden="true"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" title="Delete" onclick="deletePermission({{ $permission->id }}, {{ json_encode($permission->name) }})">
                                    <i class="fas fa-trash" aria-hidden="true"></i>
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
<div class="canvas-panel text-center py-5">
    <i class="fas fa-key fa-3x text-secondary opacity-50 mb-3" aria-hidden="true"></i>
    <p class="text-muted mb-3">No permissions found</p>
    <a href="{{ route('permissions.create') }}" class="action-btn action-btn-primary">
        <i class="fas fa-plus" aria-hidden="true"></i> Create first permission
    </a>
</div>
@endforelse
