<div class="staff-access-tab-toolbar d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <h2 class="h5 mb-0 text-secondary" id="staff-access-roles-heading">Roles directory</h2>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <button type="button" class="action-btn action-btn-primary" onclick="showCreateRoleModal()">
            <i class="fas fa-plus" aria-hidden="true"></i> New role
        </button>
        <button type="button" class="action-btn action-btn-secondary" onclick="refreshStaffAccessPage()">
            <i class="fas fa-sync-alt text-tertiary" aria-hidden="true"></i> Refresh
        </button>
        <div class="btn-group roles-view-toggle" role="group" aria-label="Roles view mode">
            <button type="button" class="btn active" onclick="switchRolesView('cards')" id="viewCards" title="Card view" aria-pressed="true">
                <i class="fas fa-th-large" aria-hidden="true"></i>
            </button>
            <button type="button" class="btn" onclick="switchRolesView('table')" id="viewTable" title="Table view" aria-pressed="false">
                <i class="fas fa-table" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>

<div class="kpi-wrapper">
    <div class="kpi-card-looker animate-slide-up delay-2">
        <div class="kpi-top">
            <div class="kpi-title">Total roles</div>
            <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-user-shield" aria-hidden="true"></i></div>
        </div>
        <div class="kpi-value-looker">{{ number_format($roleStats['total_roles'] ?? 0) }}</div>
        <div class="kpi-bottom trend-neutral">
            <i class="fas fa-fw fa-circle" style="font-size: 6px;" aria-hidden="true"></i> In directory
        </div>
    </div>
    <div class="kpi-card-looker success animate-slide-up delay-3">
        <div class="kpi-top">
            <div class="kpi-title">Active</div>
            <div class="kpi-icon-wrapper success-icon"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
        </div>
        <div class="kpi-value-looker">{{ number_format($roleStats['active_roles'] ?? 0) }}</div>
        <div class="kpi-bottom trend-positive">
            <i class="fas fa-fw fa-check" aria-hidden="true"></i> Available for assignment
        </div>
    </div>
    <div class="kpi-card-looker purple animate-slide-up delay-4">
        <div class="kpi-top">
            <div class="kpi-title">Users with roles</div>
            <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-users" aria-hidden="true"></i></div>
        </div>
        <div class="kpi-value-looker">{{ number_format($roleStats['total_users_with_roles'] ?? 0) }}</div>
        <div class="kpi-bottom trend-neutral">
            <i class="fas fa-fw fa-link" aria-hidden="true"></i> Staff linked to a role
        </div>
    </div>
</div>

<div class="canvas-panel mb-3 animate-slide-up delay-3" aria-labelledby="roles-filter-heading">
    <div class="panel-header flex-wrap gap-2">
        <h3 class="panel-title mb-0 h6 text-body" id="roles-filter-heading">
            <i class="fas fa-sliders-h" aria-hidden="true"></i> Refine list
        </h3>
        @if(request()->hasAny(['search', 'status']) && $activeTab === 'roles')
            <span class="status-badge status-badge-blue">{{ $roles->total() }} match(es)</span>
        @endif
    </div>
    <div class="px-3 px-md-4 pb-3">
        <form method="GET" action="{{ route('staff.access') }}" id="rolesFilterForm" class="row g-3 align-items-end">
            <input type="hidden" name="tab" value="roles">
            <div class="col-12 col-md-5 col-lg-4">
                <label class="form-label small text-secondary fw-semibold mb-1" for="roles_filter_search">
                    <i class="fas fa-search text-primary" aria-hidden="true"></i> Search
                </label>
                <input type="text" name="search" id="roles_filter_search" class="form-control rounded-3"
                       placeholder="Name, slug, or description…"
                       value="{{ request('search') }}"
                       autocomplete="off">
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label class="form-label small text-secondary fw-semibold mb-1" for="roles_filter_status">
                    <i class="fas fa-toggle-on text-primary" aria-hidden="true"></i> Status
                </label>
                <select name="status" id="roles_filter_status" class="form-select rounded-3">
                    <option value="">All status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-3 col-lg-auto d-flex flex-wrap gap-2">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-check" aria-hidden="true"></i> <span class="d-none d-sm-inline">Apply</span>
                </button>
                <a href="{{ route('staff.access', ['tab' => 'roles']) }}" class="action-btn action-btn-secondary">
                    <i class="fas fa-rotate-left text-tertiary" aria-hidden="true"></i> <span class="d-none d-sm-inline">Reset</span>
                </a>
            </div>
        </form>
    </div>
</div>

<div id="cardView" class="view-content">
    <div class="row g-4">
        @forelse($roles as $role)
        <div class="col-md-6 col-xl-4">
            <div class="roles-entity-card">
                <div class="roles-entity-head">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="min-w-0">
                            <h3 class="h6 mb-1 text-truncate">{{ $role->name }}</h3>
                            <code class="small text-muted text-break">{{ $role->slug }}</code>
                        </div>
                        @if($role->is_active)
                            <span class="status-badge status-badge-green flex-shrink-0">Active</span>
                        @else
                            <span class="status-badge status-badge-neutral flex-shrink-0">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="roles-entity-body">
                    @if($role->description)
                        <p class="text-muted small mb-3">{{ \Illuminate\Support\Str::limit($role->description, 100) }}</p>
                    @endif
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="status-badge status-badge-blue"><i class="fas fa-users me-1" aria-hidden="true"></i>{{ $role->users->count() }} users</span>
                        <span class="status-badge status-badge-green"><i class="fas fa-key me-1" aria-hidden="true"></i>{{ $role->permissions->count() }} permissions</span>
                    </div>
                </div>
                <div class="roles-entity-foot">
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-1" aria-hidden="true"></i>View
                        </a>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-edit me-1" aria-hidden="true"></i>Edit
                        </a>
                        <button type="button" class="btn btn-outline-danger" onclick="deleteRole({{ $role->id }}, {{ json_encode($role->name) }})">
                            <i class="fas fa-trash me-1" aria-hidden="true"></i>Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="canvas-panel text-center py-5">
                <i class="fas fa-user-shield fa-3x text-secondary opacity-50 mb-3" aria-hidden="true"></i>
                <p class="text-muted mb-3">No roles found</p>
                <button type="button" class="action-btn action-btn-primary" onclick="showCreateRoleModal()">
                    <i class="fas fa-plus" aria-hidden="true"></i> Create first role
                </button>
            </div>
        </div>
        @endforelse
    </div>
    @if(method_exists($roles, 'hasPages') && $roles->hasPages())
        <div class="d-flex justify-content-end mt-3 px-1">
            {{ $roles->links() }}
        </div>
    @endif
</div>

<div id="tableView" class="view-content d-none">
    <div class="canvas-panel animate-slide-up delay-4">
        <div class="panel-header">
            <h3 class="panel-title h6 text-body mb-0"><i class="fas fa-list-ul" aria-hidden="true"></i> All roles</h3>
            <span class="status-badge status-badge-green">{{ $roles->total() }} total</span>
        </div>
        <div class="roles-table-scroll px-2 px-md-3 pb-2">
            <div class="looker-table-wrapper">
                <table class="looker-table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
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
                            <td><code class="small">{{ $role->slug }}</code></td>
                            <td><span class="status-badge status-badge-blue">{{ $role->users->count() }}</span></td>
                            <td><span class="status-badge status-badge-green">{{ $role->permissions->count() }}</span></td>
                            <td>
                                @if($role->is_active)
                                    <span class="status-badge status-badge-green">Active</span>
                                @else
                                    <span class="status-badge status-badge-neutral">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-primary" title="View">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-outline-secondary" title="Edit">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete" onclick="deleteRole({{ $role->id }}, {{ json_encode($role->name) }})">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-tertiary">
                                    <i class="fas fa-user-shield fa-3x mb-3 text-secondary opacity-50" aria-hidden="true"></i>
                                    <p class="mb-0 fw-semibold text-secondary">No roles found</p>
                                    <button type="button" class="action-btn action-btn-primary mt-3" onclick="showCreateRoleModal()">
                                        <i class="fas fa-plus" aria-hidden="true"></i> Create first role
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($roles, 'hasPages') && $roles->hasPages())
            <div class="d-flex justify-content-end px-3 pb-3">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>
