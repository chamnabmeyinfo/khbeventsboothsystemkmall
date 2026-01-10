@extends('layouts.adminlte')

@section('title', 'Clients Management')
@section('page-title', 'Clients Management')
@section('breadcrumb', 'Clients')

@push('styles')
<style>
    /* Modern Glassmorphism Styles */
    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
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
    .kpi-card.warning::before { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
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
    .kpi-card.warning .kpi-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
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

    .client-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid;
        cursor: pointer;
    }

    .client-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .client-card.primary { border-left-color: #667eea; }
    .table-row-hover {
        transition: all 0.2s;
    }

    .table-row-hover:hover {
        background-color: #f8f9fc;
    }

    .filter-bar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 16px 24px;
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
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="kpi-label">Total Clients</div>
                    <div class="kpi-value">{{ number_format($stats['total_clients'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="kpi-label">With Bookings</div>
                    <div class="kpi-value">{{ number_format($stats['clients_with_bookings'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card warning">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="kpi-label">With Booths</div>
                    <div class="kpi-value">{{ number_format($stats['clients_with_booths'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="kpi-label">Total Bookings</div>
                    <div class="kpi-value">{{ number_format($stats['total_bookings'] ?? 0) }}</div>
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
                        <a href="{{ route('clients.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>New Client
                        </a>
                        <a href="{{ route('export.clients') }}" class="btn btn-success">
                            <i class="fas fa-file-csv mr-1"></i>Export CSV
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
        <form method="GET" action="{{ route('clients.index') }}" id="filterForm">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label><i class="fas fa-search mr-1"></i>Search</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search by name, company, phone, or position..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-building mr-1"></i>Company</label>
                    <select name="company" class="form-control">
                        <option value="">All Companies</option>
                        @foreach($companies ?? [] as $company)
                            <option value="{{ $company }}" {{ request('company') == $company ? 'selected' : '' }}>
                                {{ $company }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-sort mr-1"></i>Sort By</label>
                    <select name="sort_by" class="form-control">
                        <option value="company" {{ request('sort_by', 'company') == 'company' ? 'selected' : '' }}>Company</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="position" {{ request('sort_by') == 'position' ? 'selected' : '' }}>Position</option>
                        <option value="phone_number" {{ request('sort_by') == 'phone_number' ? 'selected' : '' }}>Phone</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-sort-amount-down mr-1"></i>Order</label>
                    <select name="sort_dir" class="form-control">
                        <option value="asc" {{ request('sort_dir', 'asc') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Descending</option>
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
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </a>
                    @if(request()->hasAny(['search', 'company', 'sort_by', 'sort_dir']))
                    <span class="badge badge-info ml-2">
                        {{ $clients->total() }} result(s) found
                    </span>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table View -->
    <div id="tableView" class="view-content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-2"></i>All Clients</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">{{ $clients->total() }} Total</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover text-nowrap mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" id="selectAllClients" class="form-check-input">
                                </th>
                                <th style="width: 80px;">ID</th>
                                <th>Company</th>
                                <th>Name</th>
                                <th style="width: 150px;">Position</th>
                                <th style="width: 150px;">Phone</th>
                                <th style="width: 120px;">Activity</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clients as $client)
                            <tr class="table-row-hover">
                                <td>
                                    <input type="checkbox" class="form-check-input client-checkbox" value="{{ $client->id }}">
                                </td>
                                <td>
                                    <strong class="text-primary">#{{ $client->id }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <i class="fas fa-building text-muted" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $client->company ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                            <x-avatar 
                                                :avatar="$client->avatar" 
                                                :name="$client->name" 
                                                :size="'sm'" 
                                                :type="'client'"
                                                :shape="'circle'"
                                            />
                                        </div>
                                        <div>
                                            <strong>{{ $client->name }}</strong>
                                            @if($client->sex)
                                            <br><small class="text-muted">
                                                <i class="fas fa-{{ $client->sex == 1 ? 'mars' : 'venus' }} mr-1"></i>
                                                {{ $client->sex == 1 ? 'Male' : 'Female' }}
                                            </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($client->position)
                                        <span class="badge badge-info">
                                            <i class="fas fa-briefcase mr-1"></i>{{ $client->position }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($client->phone_number)
                                        <a href="tel:{{ $client->phone_number }}" class="text-primary">
                                            <i class="fas fa-phone mr-1"></i>{{ $client->phone_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-store mr-1"></i>{{ $client->booths_count ?? 0 }} booth(s)
                                        </small>
                                    </div>
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar mr-1"></i>{{ $client->books_count ?? 0 }} booking(s)
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('clients.show', $client) }}" class="btn btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger" onclick="deleteClient({{ $client->id }}, '{{ $client->name }}')" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3"></i>
                                        <p class="mb-0">No clients found</p>
                                        <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm mt-3">
                                            <i class="fas fa-plus mr-1"></i>Create First Client
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if(method_exists($clients, 'hasPages') && $clients->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="text-muted">
                            @if($clients->firstItem())
                            Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} of {{ $clients->total() }} clients
                            @else
                            {{ $clients->total() }} client(s) total
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $clients->links() }}
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
            @forelse($clients as $client)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card client-card primary" onclick="window.location='{{ route('clients.show', $client) }}'">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <x-avatar 
                                    :avatar="$client->avatar" 
                                    :name="$client->name" 
                                    :size="'xs'" 
                                    :type="'client'"
                                    :shape="'circle'"
                                />
                                <span class="ml-2">{{ $client->name }}</span>
                            </h6>
                            <span class="badge badge-primary">#{{ $client->id }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-building text-muted mr-2"></i>
                                <strong>{{ $client->company ?? 'No Company' }}</strong>
                            </div>
                        </div>
                        @if($client->position)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-briefcase text-muted mr-2"></i>
                                <span class="badge badge-info">{{ $client->position }}</span>
                            </div>
                        </div>
                        @endif
                        @if($client->phone_number)
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-phone text-muted mr-2"></i>
                                <a href="tel:{{ $client->phone_number }}" class="text-primary" onclick="event.stopPropagation()">
                                    {{ $client->phone_number }}
                                </a>
                            </div>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-store mr-1"></i>{{ $client->booths_count ?? 0 }} booths
                            </small>
                            <small class="text-muted">
                                <i class="fas fa-calendar mr-1"></i>{{ $client->books_count ?? 0 }} bookings
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-info" onclick="event.stopPropagation()">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-warning" onclick="event.stopPropagation()">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <button type="button" class="btn btn-danger" onclick="event.stopPropagation(); deleteClient({{ $client->id }}, '{{ $client->name }}');">
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
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-3">No clients found</p>
                        <a href="{{ route('clients.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Create First Client
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        @if(method_exists($clients, 'hasPages') && $clients->hasPages())
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-footer">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="text-muted">
                                    @if($clients->firstItem())
                                    Showing {{ $clients->firstItem() }} to {{ $clients->lastItem() }} of {{ $clients->total() }} clients
                                    @else
                                    {{ $clients->total() }} client(s) total
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="float-right">
                                    {{ $clients->links() }}
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
        localStorage.setItem('clientsView', 'table');
    } else {
        $('#tableView').hide();
        $('#cardView').show();
        $('#viewTable').removeClass('active').removeClass('btn-primary').addClass('btn-outline-secondary');
        $('#viewCards').addClass('active').removeClass('btn-outline-secondary').addClass('btn-primary');
        localStorage.setItem('clientsView', 'cards');
    }
}

// Load saved view preference
$(document).ready(function() {
    const savedView = localStorage.getItem('clientsView') || 'table';
    switchView(savedView);
});

// Select All Checkboxes
$('#selectAllClients').on('change', function() {
    $('.client-checkbox').prop('checked', $(this).prop('checked'));
});

// Delete Client
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
                        .then(() => {
                            window.location.href = response.url;
                        });
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.success) {
                    Swal.fire('Deleted!', data.message || 'Client has been deleted.', 'success')
                        .then(() => {
                            location.reload();
                        });
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

// Refresh Page
function refreshPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}
</script>
@endpush
