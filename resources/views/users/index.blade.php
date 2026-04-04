@extends('layouts.admin')

@section('title', 'Users Management')
@section('page-title', 'Users Management')
@section('breadcrumb', 'Staff Management / Users')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<link rel="stylesheet" href="{{ asset('css/users-page.css') }}?v=1.3">
@endpush

@push('body-class', 'ios-dashboard-mode users-page')


@section('content')
@php
    /**
     * Default filter preset for “Default filters” (GET query only).
     * Change keys to match your workspace norm, e.g. type => 2, role_id => id, or [] for “show all” (same as Reset).
     */
    $usersIndexDefaultFilters = [
        'status' => '1',
    ];
@endphp
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Users</h1>
            <p>Manage staff accounts, roles, and status. Mark <strong>demo</strong> logins for training or QA so they are easy to filter apart from <strong>real</strong> production staff.</p>
        </div>
        <div class="looker-actions flex-wrap align-items-center gap-2">
            <button type="button" class="action-btn action-btn-primary" onclick="showCreateUserModal()">
                <i class="fas fa-plus" aria-hidden="true"></i> New user
            </button>
            <button type="button" class="action-btn action-btn-secondary" onclick="refreshPage()">
                <i class="fas fa-sync-alt text-tertiary" aria-hidden="true"></i> Refresh
            </button>
            <div class="btn-group users-view-toggle" role="group" aria-label="View mode">
                <button type="button" class="btn active" onclick="switchView('table')" id="viewTable" title="Table view" aria-pressed="true">
                    <i class="fas fa-table" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn" onclick="switchView('cards')" id="viewCards" title="Card view" aria-pressed="false">
                    <i class="fas fa-th-large" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="kpi-wrapper">
        <div class="kpi-card-looker animate-slide-up delay-2">
            <div class="kpi-top">
                <div class="kpi-title">Total users</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-users" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format(\App\Models\User::count()) }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-circle" style="font-size: 6px;" aria-hidden="true"></i> All accounts
            </div>
        </div>
        <div class="kpi-card-looker success animate-slide-up delay-3">
            <div class="kpi-top">
                <div class="kpi-title">Active</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-user-check" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format(\App\Models\User::where('status', 1)->count()) }}</div>
            <div class="kpi-bottom trend-positive">
                <i class="fas fa-fw fa-check" aria-hidden="true"></i> Can sign in
            </div>
        </div>
        <div class="kpi-card-looker purple animate-slide-up delay-4">
            <div class="kpi-top">
                <div class="kpi-title">Administrators</div>
                <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-user-shield" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format(\App\Models\User::where('type', 1)->count()) }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-shield-alt" aria-hidden="true"></i> Type = admin
            </div>
        </div>
        <div class="kpi-card-looker warning animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Sales staff</div>
                <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-user-tie" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format(\App\Models\User::where('type', 2)->count()) }}</div>
            <div class="kpi-bottom trend-warning">
                <i class="fas fa-fw fa-briefcase" aria-hidden="true"></i> Type = sale
            </div>
        </div>
        @if(\Illuminate\Support\Facades\Schema::hasColumn('user', 'account_kind'))
        <div class="kpi-card-looker success animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Real logins</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-user-check" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format(\App\Models\User::realStaff()->count()) }}</div>
            <div class="kpi-bottom trend-positive">
                <a href="{{ route('users.index', array_merge(request()->except('page'), ['account_kind' => \App\Models\User::ACCOUNT_KIND_REAL])) }}" class="text-reset text-decoration-none">Production accounts</a>
            </div>
        </div>
        <div class="kpi-card-looker purple animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Demo logins</div>
                <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-flask" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format(\App\Models\User::demoAccounts()->count()) }}</div>
            <div class="kpi-bottom trend-neutral">
                <a href="{{ route('users.index', array_merge(request()->except('page'), ['account_kind' => \App\Models\User::ACCOUNT_KIND_DEMO])) }}" class="text-reset text-decoration-none">Testing / training</a>
            </div>
        </div>
        @endif
    </div>

    <div class="canvas-panel users-filter-panel animate-slide-up delay-3 mb-3" aria-labelledby="users-filter-heading">
        <div class="users-filter-panel__header">
            <div class="users-filter-panel__intro">
                <h2 class="panel-title mb-0" id="users-filter-heading">
                    <span class="users-filter-panel__icon" aria-hidden="true"><i class="fas fa-sliders-h"></i></span>
                    Refine directory
                </h2>
                <p class="users-filter-panel__hint">Match username, role, type, or status—then apply.@if(! empty($usersIndexDefaultFilters)) Use <strong>Default filters</strong> for the usual workspace preset.@endif</p>
            </div>
            <div class="users-filter-panel__actions">
                @if(request()->hasAny(['search', 'type', 'role_id', 'status', 'account_kind']))
                    <span class="status-badge status-badge-blue users-filter-panel__badge">{{ number_format($total) }} match(es)</span>
                @endif
                @if(! empty($usersIndexDefaultFilters))
                <a href="{{ route('users.index', $usersIndexDefaultFilters) }}" class="action-btn action-btn-secondary"
                   title="Apply workspace default filters (clears search and other criteria)">
                    <i class="fas fa-bookmark text-tertiary" aria-hidden="true"></i> <span class="d-none d-sm-inline">Default filters</span>
                </a>
                @endif
                <a href="{{ route('users.index') }}" class="action-btn action-btn-secondary">
                    <i class="fas fa-rotate-left text-tertiary" aria-hidden="true"></i> <span class="d-none d-sm-inline">Reset</span>
                </a>
                <button type="submit" form="filterForm" class="action-btn action-btn-primary">
                    <i class="fas fa-check" aria-hidden="true"></i> <span class="d-none d-sm-inline">Apply</span>
                </button>
            </div>
        </div>
        <div class="users-filter-panel__surface">
            <form method="GET" action="{{ route('users.index') }}" id="filterForm">
                <div class="row g-3 g-md-4">
                    <div class="col-12 col-lg-4 users-filter-panel__field">
                        <label class="users-filter-panel__label" for="users_filter_search"><i class="fas fa-search" aria-hidden="true"></i> Search</label>
                        <input type="text" name="search" id="users_filter_search" class="form-control users-filter-panel__control"
                               placeholder="Username or role…"
                               value="{{ request('search') }}"
                               autocomplete="off">
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 users-filter-panel__field">
                        <label class="users-filter-panel__label" for="users_filter_type"><i class="fas fa-user-tag" aria-hidden="true"></i> Type</label>
                        <select name="type" id="users_filter_type" class="form-select users-filter-panel__control">
                            <option value="">All types</option>
                            <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Sale</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 col-lg-3 users-filter-panel__field">
                        <label class="users-filter-panel__label" for="users_filter_role"><i class="fas fa-user-shield" aria-hidden="true"></i> Role</label>
                        <select name="role_id" id="users_filter_role" class="form-select users-filter-panel__control">
                            <option value="">All roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                            <option value="0" {{ request('role_id') === '0' ? 'selected' : '' }}>No role</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3 users-filter-panel__field">
                        <label class="users-filter-panel__label" for="users_filter_status"><i class="fas fa-toggle-on" aria-hidden="true"></i> Status</label>
                        <select name="status" id="users_filter_status" class="form-select users-filter-panel__control">
                            <option value="">All</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    @if(\Illuminate\Support\Facades\Schema::hasColumn('user', 'account_kind'))
                    <div class="col-12 col-md-4 col-lg-3 users-filter-panel__field">
                        <label class="users-filter-panel__label" for="users_filter_account_kind"><i class="fas fa-flask" aria-hidden="true"></i> Account kind</label>
                        <select name="account_kind" id="users_filter_account_kind" class="form-select users-filter-panel__control">
                            <option value="">All</option>
                            <option value="{{ \App\Models\User::ACCOUNT_KIND_REAL }}" {{ request('account_kind') === \App\Models\User::ACCOUNT_KIND_REAL ? 'selected' : '' }}>Real staff</option>
                            <option value="{{ \App\Models\User::ACCOUNT_KIND_DEMO }}" {{ request('account_kind') === \App\Models\User::ACCOUNT_KIND_DEMO ? 'selected' : '' }}>Demo (testing)</option>
                        </select>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div id="tableView" class="view-content">
        <div class="canvas-panel animate-slide-up delay-4">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-list-ul" aria-hidden="true"></i> All users</h2>
                <span class="status-badge status-badge-green">{{ number_format($total) }} total</span>
            </div>
            <div class="users-table-scroll px-2 px-md-3 pb-2">
                <div class="looker-table-wrapper">
                    <table class="looker-table mb-0">
                        <thead>
                            <tr>
                                <th class="users-th-shrink">
                                    <input type="checkbox" id="selectAllUsers" class="form-check-input" title="Select all" aria-label="Select all users">
                                </th>
                                <th class="users-th-shrink">ID</th>
                                <th>Username</th>
                                <th class="users-th-shrink">Type</th>
                                <th>Role</th>
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('user', 'account_kind'))
                                <th class="users-th-shrink">Kind</th>
                                @endif
                                <th class="users-th-shrink">Status</th>
                                <th>Activity</th>
                                <th class="users-th-shrink">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tableUsersBody">
                            @forelse($users as $user)
                                @include('users.partials.table-row', ['user' => $user])
                            @empty
                            <tr class="table-row-no-press">
                                <td colspan="{{ \Illuminate\Support\Facades\Schema::hasColumn('user', 'account_kind') ? 9 : 8 }}" class="text-center py-5">
                                    <div class="text-tertiary">
                                        <i class="fas fa-user-slash fa-3x mb-3 text-secondary opacity-50" aria-hidden="true"></i>
                                        <p class="mb-0 fw-semibold text-secondary">No users found</p>
                                        <button type="button" class="action-btn action-btn-primary mt-3" onclick="showCreateUserModal()">
                                            <i class="fas fa-plus" aria-hidden="true"></i> Create first user
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="usersLazyLoadTrigger" class="users-lazy-trigger" aria-hidden="true"></div>
                <div id="usersLazyLoadSpinner" class="text-center py-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Loading</span>
                    </div>
                    <span class="ms-2 text-muted">Loading more users…</span>
                </div>
                <div id="usersLazyLoadEnd" class="text-center py-3" style="display: none;">
                    <span class="text-muted">No more users to load</span>
                </div>
            </div>
        </div>
    </div>

    <div id="cardView" class="view-content d-none">
        <div class="row g-4">
            @forelse($users as $user)
            <div class="col-md-6 col-xl-4">
                <div class="users-entity-card" role="link" tabindex="0" data-user-url="{{ route('users.show', $user) }}"
                     onclick="window.location=this.getAttribute('data-user-url')"
                     onkeydown="if(event.key==='Enter'){ window.location=this.getAttribute('data-user-url'); }">
                    <div class="users-entity-head">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <x-avatar
                                    :avatar="$user->avatar"
                                    :name="$user->username"
                                    :size="'xs'"
                                    :type="$user->isAdmin() ? 'admin' : 'user'"
                                    :shape="'circle'"
                                />
                                <h3 class="h6 mb-0 text-truncate">{{ $user->username }}</h3>
                            </div>
                            <div class="d-flex flex-wrap gap-1 justify-content-end">
                            @if($user->isAdmin())
                                <span class="status-badge status-badge-purple flex-shrink-0">Admin</span>
                            @else
                                <span class="status-badge status-badge-neutral flex-shrink-0">Sale</span>
                            @endif
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('user', 'account_kind') && ($user->account_kind ?? \App\Models\User::ACCOUNT_KIND_REAL) === \App\Models\User::ACCOUNT_KIND_DEMO)
                                <span class="status-badge status-badge-orange flex-shrink-0">Demo</span>
                            @endif
                            </div>
                        </div>
                    </div>
                    <div class="users-entity-body">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fas fa-hashtag text-muted" aria-hidden="true"></i>
                            <span class="status-badge status-badge-blue">#{{ $user->id }}</span>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                <i class="fas fa-user-shield text-muted" aria-hidden="true"></i>
                                @if($user->role)
                                    <span class="status-badge status-badge-blue">{{ $user->role->name }}</span>
                                    <small class="text-muted">{{ $user->role->permissions->count() }} permissions</small>
                                @else
                                    <span class="status-badge status-badge-neutral">No role</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <i class="fas fa-toggle-on text-muted" aria-hidden="true"></i>
                                @if($user->isActive())
                                    <span class="status-badge status-badge-green">Active</span>
                                @else
                                    <span class="status-badge status-badge-orange">Inactive</span>
                                @endif
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('user', 'account_kind'))
                                    @if(($user->account_kind ?? \App\Models\User::ACCOUNT_KIND_REAL) === \App\Models\User::ACCOUNT_KIND_DEMO)
                                        <span class="status-badge status-badge-orange">Demo</span>
                                    @else
                                        <span class="status-badge status-badge-green">Real</span>
                                    @endif
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
                                <i class="fas fa-cube me-1" aria-hidden="true"></i>{{ $boothCount }} booths
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1" aria-hidden="true"></i>{{ $bookingCount }} bookings
                            </small>
                        </div>
                    </div>
                    <div class="users-entity-foot">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('users.show', $user) }}" class="btn btn-outline-primary" onclick="event.stopPropagation()">
                                <i class="fas fa-eye me-1" aria-hidden="true"></i>View
                            </a>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-secondary" onclick="event.stopPropagation()">
                                <i class="fas fa-edit me-1" aria-hidden="true"></i>Edit
                            </a>
                            @if(auth()->user()->isAdmin() && $user->id != auth()->id())
                            <button type="button" class="btn btn-outline-danger" onclick="event.stopPropagation(); deleteUser({{ $user->id }}, {{ json_encode($user->username) }});">
                                <i class="fas fa-trash me-1" aria-hidden="true"></i>Delete
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="canvas-panel text-center py-5">
                    <i class="fas fa-user-slash fa-3x text-secondary opacity-50 mb-3" aria-hidden="true"></i>
                    <p class="text-muted mb-3">No users found</p>
                    <button type="button" class="action-btn action-btn-primary" onclick="showCreateUserModal()">
                        <i class="fas fa-plus" aria-hidden="true"></i> Create first user
                    </button>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content users-modal-content">
            <div class="modal-header users-modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2" id="createUserModalLabel">
                    <i class="fas fa-user-plus" aria-hidden="true"></i> Create new user
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createUserForm" method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div id="createUserError" class="alert alert-danger d-none rounded-3" role="alert"></div>

                    <div class="mb-4">
                        <h6 class="fw-semibold text-secondary mb-3"><i class="fas fa-user me-2 text-primary" aria-hidden="true"></i>Basic information</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modal_user_username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control rounded-3" id="modal_user_username" name="username" required placeholder="Enter username" autocomplete="username">
                                <small class="text-muted">Username must be unique</small>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_user_type" class="form-label">User type <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3" id="modal_user_type" name="type" required>
                                    <option value="2" selected>Sale staff</option>
                                    <option value="1">Administrator</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold text-secondary mb-3"><i class="fas fa-user-shield me-2 text-primary" aria-hidden="true"></i>Role &amp; status</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modal_user_role_id" class="form-label">Assign role</label>
                                <select class="form-select rounded-3" id="modal_user_role_id" name="role_id">
                                    <option value="">No role assigned</option>
                                    @foreach(\App\Models\Role::where('is_active', true)->orderBy('name')->get() as $role)
                                    <option value="{{ $role->id }}">
                                        {{ $role->name }}
                                        @if($role->permissions->count() > 0)
                                            ({{ $role->permissions->count() }} permissions)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Optional role for permission-based access</small>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_user_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select rounded-3" id="modal_user_status" name="status" required>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            @if(\Illuminate\Support\Facades\Schema::hasColumn('user', 'account_kind'))
                            <div class="col-md-6">
                                <label for="modal_user_account_kind" class="form-label">Account kind</label>
                                <select class="form-select rounded-3" id="modal_user_account_kind" name="account_kind">
                                    <option value="{{ \App\Models\User::ACCOUNT_KIND_REAL }}" selected>Real staff (production)</option>
                                    <option value="{{ \App\Models\User::ACCOUNT_KIND_DEMO }}">Demo (testing / training)</option>
                                </select>
                                <small class="text-muted">Demo logins are excluded from HR dashboard production KPIs.</small>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-0">
                        <h6 class="fw-semibold text-secondary mb-3"><i class="fas fa-key me-2 text-primary" aria-hidden="true"></i>Password</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modal_user_password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-3" id="modal_user_password" name="password" required placeholder="Enter password" autocomplete="new-password">
                                <small class="text-muted">Minimum 6 characters</small>
                            </div>
                            <div class="col-md-6">
                                <label for="modal_user_password_confirmation" class="form-label">Confirm password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control rounded-3" id="modal_user_password_confirmation" name="password_confirmation" required placeholder="Confirm password" autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light bg-opacity-50">
                    <button type="button" class="action-btn action-btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times text-tertiary" aria-hidden="true"></i> Cancel
                    </button>
                    <button type="submit" class="action-btn action-btn-primary" id="createUserSubmitBtn">
                        <i class="fas fa-save" aria-hidden="true"></i> Create user
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
    const tableEl = document.getElementById('tableView');
    const cardEl = document.getElementById('cardView');
    const btnTable = document.getElementById('viewTable');
    const btnCards = document.getElementById('viewCards');
    if (view === 'table') {
        tableEl.classList.remove('d-none');
        cardEl.classList.add('d-none');
        btnTable.classList.add('active');
        btnTable.setAttribute('aria-pressed', 'true');
        btnCards.classList.remove('active');
        btnCards.setAttribute('aria-pressed', 'false');
        localStorage.setItem('usersView', 'table');
    } else {
        tableEl.classList.add('d-none');
        cardEl.classList.remove('d-none');
        btnTable.classList.remove('active');
        btnTable.setAttribute('aria-pressed', 'false');
        btnCards.classList.add('active');
        btnCards.setAttribute('aria-pressed', 'true');
        localStorage.setItem('usersView', 'cards');
    }
}

// Load saved view preference
$(document).ready(function() {
    const savedView = localStorage.getItem('usersView') || 'table';
    switchView(savedView);
});

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
    const el = document.getElementById('createUserModal');
    if (window.bootstrap && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(el).show();
    } else if (typeof $ !== 'undefined' && $(el).modal) {
        $(el).modal('show');
    }
    const form = document.getElementById('createUserForm');
    if (form) form.reset();
    const err = document.getElementById('createUserError');
    if (err) {
        err.classList.add('d-none');
        err.textContent = '';
    }
    $('#modal_user_type').val('2');
    $('#modal_user_status').val('1');
    if ($('#modal_user_account_kind').length) {
        $('#modal_user_account_kind').val('{{ \App\Models\User::ACCOUNT_KIND_REAL }}');
    }
}

// Handle Create User Form Submission
$(document).ready(function() {
    $('#createUserForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#createUserSubmitBtn');
        const errorDiv = $('#createUserError');
        const originalText = submitBtn.html();
        
        errorDiv.addClass('d-none');
        
        // Validate password match
        const password = $('#modal_user_password').val();
        const passwordConfirmation = $('#modal_user_password_confirmation').val();
        if (password !== passwordConfirmation) {
            errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i>Password and confirmation password do not match.').removeClass('d-none');
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
                    const modalEl = document.getElementById('createUserModal');
                    if (window.bootstrap && bootstrap.Modal && modalEl) {
                        bootstrap.Modal.getInstance(modalEl)?.hide();
                    } else if (typeof $ !== 'undefined') {
                        $('#createUserModal').modal('hide');
                    }
                    form[0].reset();
                    errorDiv.addClass('d-none');
                    
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
                errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i>' + errorMessage).removeClass('d-none');
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
        $('#createUserError').addClass('d-none').text('');
        $('#modal_user_type').val('2');
        $('#modal_user_status').val('1');
        if ($('#modal_user_account_kind').length) {
            $('#modal_user_account_kind').val('{{ \App\Models\User::ACCOUNT_KIND_REAL }}');
        }
    });
    
    // Lazy Loading Variables
    let usersCurrentPage = 1;
    let usersIsLoading = false;
    let usersHasMoreData = {{ $total > count($users) ? 'true' : 'false' }};
    let usersFilterParams = {
        search: '{{ request('search') }}',
        type: '{{ request('type') }}',
        status: '{{ request('status') }}',
        role_id: '{{ request('role_id') }}',
        account_kind: '{{ request('account_kind') }}'
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
        if (usersFilterParams.account_kind) params.append('account_kind', usersFilterParams.account_kind);
        
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

