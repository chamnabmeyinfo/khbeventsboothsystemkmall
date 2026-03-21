@extends('layouts.admin')

@section('title', 'Clients Management')
@section('page-title', 'Clients Management')
@section('breadcrumb', 'Clients')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.1">
<link rel="stylesheet" href="{{ asset('css/clients-page.css') }}?v=1.4">
@endpush

@push('body-class', 'ios-dashboard-mode clients-page')

@php
    $clientsColumnDefs = [
        ['key' => 'id', 'label' => 'ID'],
        ['key' => 'company', 'label' => 'Company'],
        ['key' => 'company_name_khmer', 'label' => 'Company (Khmer)'],
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'sex', 'label' => 'Gender'],
        ['key' => 'position', 'label' => 'Position'],
        ['key' => 'phone_number', 'label' => 'Phone'],
        ['key' => 'phone_1', 'label' => 'Phone 1'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'address', 'label' => 'Address'],
        ['key' => 'tax_id', 'label' => 'Tax ID'],
        ['key' => 'website', 'label' => 'Website'],
        ['key' => 'notes', 'label' => 'Notes'],
        ['key' => 'booths_count', 'label' => 'Booths'],
        ['key' => 'books_count', 'label' => 'Bookings'],
    ];
@endphp

@section('content')
@php
    $clientsSortUrl = function (string $column) use ($sortBy, $sortDir) {
        $nextDir = ($sortBy === $column && $sortDir === 'asc') ? 'desc' : 'asc';
        return request()->fullUrlWithQuery(['sort_by' => $column, 'sort_dir' => $nextDir, 'page' => 1]);
    };
    $clientsSortIcon = function (string $column) use ($sortBy, $sortDir) {
        if ($sortBy !== $column) {
            return '<i class="fas fa-sort text-muted opacity-50" style="font-size:.75rem" aria-hidden="true"></i>';
        }
        return $sortDir === 'desc'
            ? '<i class="fas fa-sort-down" aria-hidden="true"></i>'
            : '<i class="fas fa-sort-up" aria-hidden="true"></i>';
    };
@endphp

<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Clients</h1>
            <p>Filter per column in the table, sort from headers, and manage your client directory.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <button type="button" class="action-btn action-btn-primary" onclick="showCreateClientModal()">
                <i class="fas fa-plus" aria-hidden="true"></i> New client
            </button>
            <a href="{{ route('export.clients', request()->query()) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-file-csv text-tertiary" aria-hidden="true"></i> Export
            </a>
            <button type="button" class="action-btn action-btn-secondary" onclick="refreshPage()">
                <i class="fas fa-sync-alt text-tertiary" aria-hidden="true"></i> Refresh
            </button>
            <div class="dropdown">
                <button class="action-btn action-btn-secondary dropdown-toggle clients-density-trigger" type="button" id="clientsDensityMenu" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true">
                    <i class="fas fa-compress-alt" aria-hidden="true"></i> Density
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 py-2" aria-labelledby="clientsDensityMenu">
                    <li><button type="button" class="dropdown-item clients-density-option" data-density="compact">Compact</button></li>
                    <li><button type="button" class="dropdown-item clients-density-option" data-density="default">Default</button></li>
                    <li><button type="button" class="dropdown-item clients-density-option" data-density="comfort">Comfort</button></li>
                </ul>
            </div>
            <div class="btn-group clients-view-toggle" role="group" aria-label="View mode">
                <button type="button" class="btn active" onclick="switchView('table')" id="viewTable" title="Table view" aria-pressed="true">
                    <i class="fas fa-table" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn" onclick="switchView('cards')" id="viewCards" title="Card view" aria-pressed="false">
                    <i class="fas fa-th-large" aria-hidden="true"></i>
                </button>
            </div>
            <button type="submit" form="clientsFilterToolbarForm" class="action-btn action-btn-primary">
                <i class="fas fa-filter" aria-hidden="true"></i> Apply filters
            </button>
            <a href="{{ route('clients.index', ['per_page' => $perPage]) }}" class="action-btn action-btn-secondary">Reset all</a>
        </div>
    </header>

    <div class="kpi-wrapper">
        <div class="kpi-card-looker animate-slide-up delay-2">
            <div class="kpi-top">
                <div class="kpi-title">Total clients</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-users" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($stats['total_clients'] ?? 0) }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-circle" style="font-size: 6px;" aria-hidden="true"></i> All records in directory
            </div>
        </div>
        <div class="kpi-card-looker success animate-slide-up delay-3">
            <div class="kpi-top">
                <div class="kpi-title">With bookings</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-calendar-check" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($stats['clients_with_bookings'] ?? 0) }}</div>
            <div class="kpi-bottom trend-positive">
                <i class="fas fa-fw fa-link" aria-hidden="true"></i> Active booking history
            </div>
        </div>
        <div class="kpi-card-looker warning animate-slide-up delay-4">
            <div class="kpi-top">
                <div class="kpi-title">With booths</div>
                <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-store" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($stats['clients_with_booths'] ?? 0) }}</div>
            <div class="kpi-bottom trend-warning">
                <i class="fas fa-fw fa-map-pin" aria-hidden="true"></i> Linked to floor plan
            </div>
        </div>
        <div class="kpi-card-looker purple animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Total bookings</div>
                <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-file-invoice" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($stats['total_bookings'] ?? 0) }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-layer-group" aria-hidden="true"></i> Across all clients
            </div>
        </div>
    </div>

    @php
        $filterQuery = request()->input('filter', []);
        $activeColumnFilterCount = is_array($filterQuery)
            ? collect($filterQuery)->filter(fn ($v) => $v !== null && trim((string) $v) !== '')->count()
            : 0;
    @endphp

    {{-- GET form: sort + optional legacy search (preserved on paginate); column filters use form="clientsFilterToolbarForm" in the table --}}
    <form method="GET" action="{{ route('clients.index') }}" id="clientsFilterToolbarForm" class="d-none" aria-hidden="true">
        <input type="hidden" name="sort_by" value="{{ $sortBy }}">
        <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
        @if(request()->filled('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
    </form>

    @if($activeColumnFilterCount > 0)
    <div class="clients-filter-chips mb-3 d-flex flex-wrap align-items-center gap-2">
        <span class="clients-hint me-1">Active</span>
        <span class="clients-chip clients-chip-meta">{{ $activeColumnFilterCount }} column filter(s)</span>
        <a href="{{ route('clients.index', array_merge(request()->except('filter'), ['page' => 1, 'per_page' => $perPage])) }}" class="clients-chip">Clear column filters <i class="fas fa-times ms-1" aria-hidden="true"></i></a>
    </div>
    @endif

    <div id="clientsBulkBar" class="clients-bulk-bar d-none mb-3 p-3" role="region" aria-label="Bulk actions">
        <strong id="clientsBulkCount" class="me-2">0 selected</strong>
        <button type="button" class="btn btn-sm btn-outline-light" id="clientsBulkMergeBtn" disabled>Merge selected</button>
        <button type="button" class="btn btn-sm btn-outline-light" id="clientsBulkExportBtn">Export selected</button>
        <button type="button" class="btn btn-sm btn-outline-danger" id="clientsBulkDeleteBtn">Delete selected</button>
        <button type="button" class="btn btn-sm btn-link text-white text-decoration-none ms-auto" id="clientsBulkClearBtn">Clear selection</button>
    </div>

    <div id="tableView" class="view-content">
        <div class="canvas-panel clients-data-canvas">
            <div class="clients-canvas-header">
                <div>
                    <h2 class="panel-title mb-1"><i class="fas fa-list" aria-hidden="true"></i> All clients</h2>
                    <p class="clients-hint mb-0">Scroll horizontally on smaller screens to reach every column. Use Columns to show or hide fields (saved in this browser).</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    @include('clients.partials.column-visibility-menu', ['clientsColumnDefs' => $clientsColumnDefs])
                    <span class="status-badge status-badge-green">{{ $clients->total() }} total</span>
                </div>
            </div>
            <div class="clients-pagination-wrap">
                @include('clients.partials.list-pagination')
            </div>
            <div class="clients-canvas-body">
                @include('clients.partials.clients-data-table')
            </div>
            <div class="px-3 pb-3">
                @include('clients.partials.list-pagination', ['showPerPage' => false])
            </div>
        </div>
    </div>

    <div id="cardView" class="view-content d-none">
        <div class="canvas-panel mb-3">
            <div class="p-3">
                @include('clients.partials.list-pagination')
            </div>
        </div>
        <div class="row g-4">
            @forelse($clients as $client)
            <div class="col-md-6 col-xl-4">
                <div class="clients-entity-card" role="link" tabindex="0" data-client-url="{{ route('clients.show', $client) }}" onclick="window.location=this.getAttribute('data-client-url')" onkeydown="if(event.key==='Enter'){ window.location=this.getAttribute('data-client-url'); }">
                    <div class="clients-entity-head">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <div class="d-flex align-items-center gap-2 min-w-0">
                                <x-avatar
                                    :avatar="$client->avatar"
                                    :name="$client->name"
                                    :size="'xs'"
                                    :type="'client'"
                                    :shape="'circle'"
                                />
                                <h3 class="h6 mb-0 text-truncate">{{ $client->name }}</h3>
                            </div>
                            <span class="status-badge status-badge-blue flex-shrink-0">#{{ $client->id }}</span>
                        </div>
                    </div>
                    <div class="clients-entity-body">
                        <div class="d-flex align-items-start gap-2 mb-3">
                            <i class="fas fa-building text-muted mt-1" aria-hidden="true"></i>
                            <span class="fw-semibold">{{ ($client->company !== null && $client->company !== '') ? $client->company : '—' }}</span>
                        </div>
                        @if($client->position)
                        <div class="mb-3">
                            <span class="clients-position-badge text-wrap">{{ $client->position }}</span>
                        </div>
                        @endif
                        @if($client->phone_number)
                        <div class="mb-3">
                            <i class="fas fa-phone text-muted me-2" aria-hidden="true"></i>
                            <a href="tel:{{ $client->phone_number }}" class="clients-link-tel" onclick="event.stopPropagation()">{{ $client->phone_number }}</a>
                        </div>
                        @endif
                        <div class="d-flex flex-wrap gap-2">
                            <span class="status-badge-muted">{{ (int) ($client->booths_count ?? 0) }} Booth{{ (int) ($client->booths_count ?? 0) === 1 ? '' : 's' }}</span>
                            <span class="status-badge status-badge-green">{{ (int) ($client->books_count ?? 0) }} Booking{{ (int) ($client->books_count ?? 0) === 1 ? '' : 's' }}</span>
                        </div>
                    </div>
                    <div class="clients-entity-foot">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary" onclick="event.stopPropagation()"><i class="fas fa-eye me-1" aria-hidden="true"></i>View</a>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-secondary" onclick="event.stopPropagation()"><i class="fas fa-edit me-1" aria-hidden="true"></i>Edit</a>
                            <button type="button" class="btn btn-outline-danger" onclick="event.stopPropagation(); deleteClient({{ $client->id }}, @json($client->name));"><i class="fas fa-trash me-1" aria-hidden="true"></i>Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="canvas-panel text-center py-5">
                    <i class="fas fa-users-slash fa-3x mb-3 opacity-50" aria-hidden="true"></i>
                    <p class="text-muted mb-3">No clients found</p>
                    <button type="button" class="action-btn action-btn-primary" onclick="showCreateClientModal()"><i class="fas fa-plus" aria-hidden="true"></i> Create first client</button>
                </div>
            </div>
            @endforelse
        </div>
        <div class="canvas-panel mt-3">
            <div class="p-3">
                @include('clients.partials.list-pagination', ['showPerPage' => false])
            </div>
        </div>
    </div>
</div>

{{-- Create Client Modal (Bootstrap 5) --}}
<div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content clients-modal-content">
            <div class="modal-header clients-modal-header">
                <h5 class="modal-title" id="createClientModalLabel"><i class="fas fa-user-plus me-2" aria-hidden="true"></i>Create new client</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createClientForm" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-body clients-modal-body">
                    <div id="createClientError" class="alert alert-danger d-none rounded-3" role="alert"></div>

                    <div class="mb-4">
                        <div class="clients-modal-section-title"><i class="fas fa-user text-primary" aria-hidden="true"></i> Basic information</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modal_client_name" class="form-label">Full name</label>
                                <input type="text" class="form-control" id="modal_client_name" name="name" placeholder="Client full name" autocomplete="name">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_sex" class="form-label">Gender</label>
                                <select class="form-select" id="modal_client_sex" name="sex">
                                    <option value="">Select…</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                    <option value="3">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="clients-modal-section-title"><i class="fas fa-building text-primary" aria-hidden="true"></i> Company</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modal_client_company" class="form-label">Company name</label>
                                <input type="text" class="form-control" id="modal_client_company" name="company" placeholder="Company" autocomplete="organization">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_company_name_khmer" class="form-label">Company name (Khmer)</label>
                                <input type="text" class="form-control" id="modal_client_company_name_khmer" name="company_name_khmer" placeholder="ឈ្មោះក្រុមហ៊ុន" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_position" class="form-label">Position / title</label>
                                <input type="text" class="form-control" id="modal_client_position" name="position" placeholder="Role" autocomplete="organization-title">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="clients-modal-section-title"><i class="fas fa-phone text-primary" aria-hidden="true"></i> Contact</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modal_client_phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="modal_client_phone" name="phone_number" placeholder="Primary phone" autocomplete="tel">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_phone_1" class="form-label">Phone 1</label>
                                <input type="text" class="form-control" id="modal_client_phone_1" name="phone_1" placeholder="Alternate" autocomplete="tel">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="modal_client_email" name="email" placeholder="email@example.com" autocomplete="email">
                            </div>
                            <div class="col-12">
                                <label for="modal_client_address" class="form-label">Address</label>
                                <textarea class="form-control" id="modal_client_address" name="address" rows="2" placeholder="Full address" autocomplete="street-address"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <div class="clients-modal-section-title"><i class="fas fa-info-circle text-primary" aria-hidden="true"></i> Additional (optional)</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="modal_client_tax_id" class="form-label">Tax ID</label>
                                <input type="text" class="form-control" id="modal_client_tax_id" name="tax_id" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="modal_client_website" name="website" placeholder="https://…" autocomplete="url">
                            </div>
                            <div class="col-12">
                                <label for="modal_client_notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="modal_client_notes" name="notes" rows="2" autocomplete="off"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer clients-modal-footer">
                    <button type="button" class="action-btn action-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="action-btn action-btn-primary" id="createClientSubmitBtn">
                        <i class="fas fa-save" aria-hidden="true"></i> Create client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CLIENTS_EXPORT_BASE = @json(route('export.clients'));
const CLIENTS_BULK_DELETE_URL = @json(route('bulk.clients.delete'));
const CLIENTS_BULK_MERGE_URL = @json(route('bulk.clients.merge'));
const CLIENTS_COLUMN_KEYS = @json(collect($clientsColumnDefs)->pluck('key')->values());
const CLIENTS_TABLE_COL_STORAGE = 'clientsTableColumnVisibility';

function clientsGetModal() {
    const el = document.getElementById('createClientModal');
    return el && window.bootstrap ? bootstrap.Modal.getOrCreateInstance(el) : null;
}

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
        localStorage.setItem('clientsView', 'table');
    } else {
        tableEl.classList.add('d-none');
        cardEl.classList.remove('d-none');
        btnTable.classList.remove('active');
        btnTable.setAttribute('aria-pressed', 'false');
        btnCards.classList.add('active');
        btnCards.setAttribute('aria-pressed', 'true');
        localStorage.setItem('clientsView', 'cards');
    }
}

function getSelectedClientIds() {
    return $('.client-checkbox:checked').map(function () { return parseInt($(this).val(), 10); }).get().filter(Boolean);
}

function updateClientsBulkBar() {
    const ids = getSelectedClientIds();
    const n = ids.length;
    const bar = document.getElementById('clientsBulkBar');
    if (!bar) return;
    if (n < 1) {
        bar.classList.add('d-none');
        return;
    }
    bar.classList.remove('d-none');
    document.getElementById('clientsBulkCount').textContent = n + ' selected';
    document.getElementById('clientsBulkMergeBtn').disabled = n < 2;
}

function applyClientsTableDensity(mode) {
    const t = document.getElementById('clientsDataTable');
    if (!t) return;
    t.classList.remove('clients-density-compact', 'clients-density-default', 'clients-density-comfort');
    const m = ['compact', 'default', 'comfort'].indexOf(mode) >= 0 ? mode : 'default';
    t.classList.add('clients-density-' + m);
    localStorage.setItem('clientsTableDensity', m);
}

function clientsDefaultColumnVisibility() {
    const o = {};
    CLIENTS_COLUMN_KEYS.forEach(function (k) { o[k] = true; });
    return o;
}

function clientsLoadColumnVisibility() {
    try {
        const raw = localStorage.getItem(CLIENTS_TABLE_COL_STORAGE);
        const def = clientsDefaultColumnVisibility();
        if (!raw) return def;
        const parsed = JSON.parse(raw);
        if (!parsed || typeof parsed !== 'object') return def;
        CLIENTS_COLUMN_KEYS.forEach(function (k) {
            if (typeof parsed[k] === 'boolean') {
                def[k] = parsed[k];
            }
        });
        return def;
    } catch (e) {
        return clientsDefaultColumnVisibility();
    }
}

function clientsSaveColumnVisibility(state) {
    try {
        localStorage.setItem(CLIENTS_TABLE_COL_STORAGE, JSON.stringify(state));
    } catch (e) { /* ignore */ }
}

function clientsApplyColumnVisibility(state) {
    const table = document.getElementById('clientsDataTable');
    if (!table) return;
    CLIENTS_COLUMN_KEYS.forEach(function (key) {
        const show = state[key] !== false;
        table.querySelectorAll('[data-clients-col="' + key + '"]').forEach(function (el) {
            el.classList.toggle('clients-col-hidden', !show);
        });
    });
}

function clientsInitColumnVisibility() {
    const state = clientsLoadColumnVisibility();
    CLIENTS_COLUMN_KEYS.forEach(function (key) {
        const cb = document.getElementById('clients-col-toggle-' + key);
        if (cb) {
            cb.checked = state[key] !== false;
        }
    });
    clientsApplyColumnVisibility(state);

    document.querySelectorAll('.clients-col-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            const key = cb.getAttribute('data-clients-col-key');
            if (!key) return;
            const s = clientsLoadColumnVisibility();
            s[key] = cb.checked;
            clientsSaveColumnVisibility(s);
            clientsApplyColumnVisibility(s);
        });
    });

    const showAll = document.getElementById('clientsColsShowAll');
    if (showAll) {
        showAll.addEventListener('click', function (e) {
            e.preventDefault();
            const s = clientsDefaultColumnVisibility();
            clientsSaveColumnVisibility(s);
            CLIENTS_COLUMN_KEYS.forEach(function (key) {
                const c = document.getElementById('clients-col-toggle-' + key);
                if (c) c.checked = true;
            });
            clientsApplyColumnVisibility(s);
        });
    }
}

$(document).ready(function () {
    const savedView = localStorage.getItem('clientsView') || 'table';
    switchView(savedView);

    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    }

    applyClientsTableDensity(localStorage.getItem('clientsTableDensity') || 'default');

    clientsInitColumnVisibility();

    $('.clients-density-option').on('click', function () {
        applyClientsTableDensity($(this).data('density'));
    });

    $('#selectAllClients').on('change', function () {
        const checked = $(this).prop('checked');
        $('#tableClientsBody .client-checkbox').prop('checked', checked);
        updateClientsBulkBar();
    });

    $(document).on('change', '.client-checkbox', function () {
        const $rows = $('#tableClientsBody .client-checkbox');
        const total = $rows.length;
        const checked = $rows.filter(':checked').length;
        const $all = $('#selectAllClients');
        $all.prop('checked', total > 0 && checked === total);
        $all.prop('indeterminate', checked > 0 && checked < total);
        updateClientsBulkBar();
    });

    $(document).on('click', '.btn-client-delete-row', function () {
        const id = $(this).data('client-id');
        const name = $(this).data('client-name') || '';
        deleteClient(id, name);
    });

    $('#clientsBulkClearBtn').on('click', function () {
        $('.client-checkbox, #selectAllClients').prop('checked', false);
        $('#selectAllClients').prop('indeterminate', false);
        updateClientsBulkBar();
    });

    $('#clientsBulkDeleteBtn').on('click', function () {
        const ids = getSelectedClientIds();
        if (!ids.length) return;
        Swal.fire({
            title: 'Delete selected clients?',
            text: 'This will remove ' + ids.length + ' client record(s). This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            showLoading();
            fetch(CLIENTS_BULK_DELETE_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    hideLoading();
                    if (data && data.success) {
                        Swal.fire('Deleted', data.message || 'Clients removed.', 'success').then(function () { location.reload(); });
                    } else {
                        Swal.fire('Error', (data && data.message) || 'Bulk delete failed.', 'error');
                    }
                })
                .catch(function () {
                    hideLoading();
                    Swal.fire('Error', 'Bulk delete failed.', 'error');
                });
        });
    });

    $('#clientsBulkExportBtn').on('click', function () {
        const ids = getSelectedClientIds();
        if (!ids.length) return;
        const q = ids.map(function (id) { return 'ids[]=' + encodeURIComponent(id); }).join('&');
        window.location.href = CLIENTS_EXPORT_BASE + (CLIENTS_EXPORT_BASE.indexOf('?') >= 0 ? '&' : '?') + q;
    });

    $('#clientsBulkMergeBtn').on('click', function () {
        const ids = getSelectedClientIds();
        if (ids.length < 2) return;
        Swal.fire({
            title: 'Merge selected clients?',
            html: 'Records will be merged into the client with the <strong>lowest ID</strong>. Booths and bookings move to that record. This cannot be undone.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007aff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Merge',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            showLoading();
            fetch(CLIENTS_BULK_MERGE_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ids: ids })
            })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    hideLoading();
                    if (data && data.success) {
                        Swal.fire('Merged', data.message || 'Clients merged.', 'success').then(function () { location.reload(); });
                    } else {
                        Swal.fire('Error', (data && data.message) || 'Merge failed.', 'error');
                    }
                })
                .catch(function () {
                    hideLoading();
                    Swal.fire('Error', 'Merge failed.', 'error');
                });
        });
    });
});

function deleteClient(id, name) {
    Swal.fire({
        title: 'Delete Client?',
        text: `Are you sure you want to delete client "${name}"? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch(`/clients/${id}`, {
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
                    Swal.fire('Deleted!', 'Client has been deleted.', 'success')
                        .then(() => { window.location.href = response.url; });
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.success) {
                    Swal.fire('Deleted!', data.message || 'Client has been deleted.', 'success')
                        .then(() => { location.reload(); });
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error!', 'An error occurred while deleting the client.', 'error');
                console.error('Error:', error);
            });
        }
    });
}

function refreshPage() {
    showLoading();
    setTimeout(() => { location.reload(); }, 400);
}

function showCreateClientModal() {
    const form = document.getElementById('createClientForm');
    const err = document.getElementById('createClientError');
    if (form) form.reset();
    if (err) { err.classList.add('d-none'); err.innerHTML = ''; }
    const m = clientsGetModal();
    if (m) m.show();
}

$(document).ready(function() {
    $('#createClientForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = $('#createClientSubmitBtn');
        const errorDiv = $('#createClientError');
        const originalText = submitBtn.html();
        errorDiv.addClass('d-none').empty();
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin me-1" aria-hidden="true"></i>Creating…');
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.status === 'success' && response.client) {
                    const m = clientsGetModal();
                    if (m) m.hide();
                    form[0].reset();
                    errorDiv.addClass('d-none');
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Client created successfully');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({ icon: 'success', title: 'Success!', text: response.message || 'Client created successfully', timer: 2000, showConfirmButton: false });
                    } else {
                        alert(response.message || 'Client created successfully');
                    }
                    setTimeout(() => { location.reload(); }, 1500);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the client.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = [];
                    Object.keys(errors).forEach(function(key) {
                        const fieldErrors = errors[key];
                        if (Array.isArray(fieldErrors)) {
                            fieldErrors.forEach(function(err) { errorMessages.push('<div>' + err + '</div>'); });
                        } else {
                            errorMessages.push('<div>' + fieldErrors + '</div>');
                        }
                    });
                    if (errorMessages.length > 0) errorMessage = errorMessages.join('');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                errorDiv.html('<i class="fas fa-exclamation-triangle me-1" aria-hidden="true"></i><strong>Validation</strong><br>' + errorMessage).removeClass('d-none');
                const el = errorDiv[0];
                if (el) {
                    try { el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); } catch (e) {}
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });

    const modalEl = document.getElementById('createClientModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            const f = document.getElementById('createClientForm');
            const e = document.getElementById('createClientError');
            if (f) f.reset();
            if (e) { e.classList.add('d-none'); e.innerHTML = ''; }
        });
    }
});
</script>
@endpush
