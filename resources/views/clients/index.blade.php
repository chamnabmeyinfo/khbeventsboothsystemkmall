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
                        <button type="button" class="btn btn-primary" onclick="showCreateClientModal()">
                            <i class="fas fa-plus mr-1"></i>New Client
                        </button>
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
                    <span class="badge badge-primary">{{ $total ?? count($clients) }} Total</span>
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
                        <tbody id="tableClientsBody">
                            @forelse($clients as $client)
                                @include('clients.partials.table-row', ['client' => $client])
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users-slash fa-3x mb-3"></i>
                                        <p class="mb-0">No clients found</p>
                                        <button type="button" class="btn btn-primary btn-sm mt-3" onclick="showCreateClientModal()">
                                            <i class="fas fa-plus mr-1"></i>Create First Client
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Lazy Loading Trigger -->
                <div id="clientsLazyLoadTrigger" style="height: 20px; margin: 10px 0;"></div>
                <!-- Lazy Loading Spinner -->
                <div id="clientsLazyLoadSpinner" class="text-center py-3" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span class="ml-2 text-muted">Loading more clients...</span>
                </div>
                <!-- Lazy Loading End -->
                <div id="clientsLazyLoadEnd" class="text-center py-3" style="display: none;">
                    <span class="text-muted">No more clients to load</span>
                </div>
            </div>
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
                        <button type="button" class="btn btn-primary" onclick="showCreateClientModal()">
                            <i class="fas fa-plus mr-1"></i>Create First Client
                        </button>
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

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" role="dialog" aria-labelledby="createClientModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; border-radius: 20px 20px 0 0; padding: 24px 32px;">
                <h5 class="modal-title" id="createClientModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-user-plus mr-2"></i>Create New Client
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 1.5rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createClientForm" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-body" style="padding: 32px; max-height: calc(100vh - 200px); overflow-y: auto;">
                    <div id="createClientError" class="alert alert-danger" style="display: none; border-radius: 12px;"></div>
                    
                    <!-- Basic Information -->
                    <div class="form-group mb-4">
                        <h6 style="font-weight: 600; margin-bottom: 16px; color: #495057;"><i class="fas fa-user mr-2 text-primary"></i>Basic Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_client_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="modal_client_name" name="name" placeholder="Enter client full name" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_sex" class="form-label">Gender</label>
                                <select class="form-control" id="modal_client_sex" name="sex" style="border-radius: 8px;">
                                    <option value="">Select Gender...</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                    <option value="3">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="form-group mb-4">
                        <h6 style="font-weight: 600; margin-bottom: 16px; color: #495057;"><i class="fas fa-building mr-2 text-primary"></i>Company Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_client_company" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="modal_client_company" name="company" placeholder="Enter company name" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_company_name_khmer" class="form-label">Company Name (Khmer)</label>
                                <input type="text" class="form-control" id="modal_client_company_name_khmer" name="company_name_khmer" placeholder="Enter company name in Khmer" style="border-radius: 8px;">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_client_position" class="form-label">Position/Title</label>
                                <input type="text" class="form-control" id="modal_client_position" name="position" placeholder="Enter position or title" style="border-radius: 8px;">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-group mb-4">
                        <h6 style="font-weight: 600; margin-bottom: 16px; color: #495057;"><i class="fas fa-phone mr-2 text-primary"></i>Contact Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_client_phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="modal_client_phone" name="phone_number" placeholder="Enter phone number" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_phone_1" class="form-label">Phone 1</label>
                                <input type="text" class="form-control" id="modal_client_phone_1" name="phone_1" placeholder="Enter primary phone number" style="border-radius: 8px;">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_client_phone_2" class="form-label">Phone 2</label>
                                <input type="text" class="form-control" id="modal_client_phone_2" name="phone_2" placeholder="Enter secondary phone number" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="modal_client_email" name="email" placeholder="Enter email address" style="border-radius: 8px;">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_client_email_1" class="form-label">Email 1</label>
                                <input type="email" class="form-control" id="modal_client_email_1" name="email_1" placeholder="Enter primary email address" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_email_2" class="form-label">Email 2</label>
                                <input type="email" class="form-control" id="modal_client_email_2" name="email_2" placeholder="Enter secondary email address" style="border-radius: 8px;">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_client_address" class="form-label">Address</label>
                                <textarea class="form-control" id="modal_client_address" name="address" rows="2" placeholder="Enter complete address" style="border-radius: 8px;"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-group mb-0">
                        <h6 style="font-weight: 600; margin-bottom: 16px; color: #495057;"><i class="fas fa-info-circle mr-2 text-primary"></i>Additional Information (Optional)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_client_tax_id" class="form-label">Tax ID / Business Registration Number</label>
                                <input type="text" class="form-control" id="modal_client_tax_id" name="tax_id" placeholder="Enter tax ID" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="modal_client_website" name="website" placeholder="https://example.com" style="border-radius: 8px;">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_client_notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="modal_client_notes" name="notes" rows="2" placeholder="Enter any additional information" style="border-radius: 8px;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 20px 32px; border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 12px; padding: 10px 24px;">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="createClientSubmitBtn" style="border-radius: 12px; padding: 10px 24px;">
                        <i class="fas fa-save mr-1"></i>Create Client
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

// Show Create Client Modal
function showCreateClientModal() {
    $('#createClientModal').modal('show');
    $('#createClientForm')[0].reset();
    $('#createClientError').hide();
}

// Handle Create Client Form Submission
$(document).ready(function() {
    $('#createClientForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#createClientSubmitBtn');
        const errorDiv = $('#createClientError');
        const originalText = submitBtn.html();
        
        errorDiv.hide();
        
        // Remove HTML5 validation since all fields are optional
        // if (!form[0].checkValidity()) {
        //     form[0].reportValidity();
        //     return;
        // }
        
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
                if (response.status === 'success' && response.client) {
                    $('#createClientModal').modal('hide');
                    form[0].reset();
                    errorDiv.hide();
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Client created successfully');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Client created successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(response.message || 'Client created successfully');
                    }
                    
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the client.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    // Collect all error messages
                    const errorMessages = [];
                    Object.keys(errors).forEach(function(key) {
                        const fieldErrors = errors[key];
                        if (Array.isArray(fieldErrors)) {
                            fieldErrors.forEach(function(err) {
                                errorMessages.push('<div>' + err + '</div>');
                            });
                        } else {
                            errorMessages.push('<div>' + fieldErrors + '</div>');
                        }
                    });
                    if (errorMessages.length > 0) {
                        errorMessage = errorMessages.join('');
                    }
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i><strong>Validation Error:</strong><br>' + errorMessage).show();
                // Safely scroll to error div if it exists
                if (errorDiv.length > 0 && errorDiv[0]) {
                    try {
                        errorDiv[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } catch (e) {
                        // Fallback: scroll to top of modal body
                        const modalBody = errorDiv.closest('.modal-body');
                        if (modalBody.length > 0 && modalBody[0]) {
                            modalBody[0].scrollTop = 0;
                        }
                    }
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });
    
    // Reset form when modal is closed
    $('#createClientModal').on('hidden.bs.modal', function() {
        $('#createClientForm')[0].reset();
        $('#createClientError').hide();
    });
    
    // Lazy Loading Variables
    let clientsCurrentPage = 1;
    let clientsIsLoading = false;
    let clientsHasMoreData = {{ $total > count($clients) ? 'true' : 'false' }};
    let clientsFilterParams = {
        search: '{{ request('search') }}',
        company: '{{ request('company') }}',
        sort_by: '{{ request('sort_by', 'company') }}',
        sort_dir: '{{ request('sort_dir', 'asc') }}'
    };
    
    // Lazy Loading Observer
    let clientsLazyLoadObserver = null;
    
    // Initialize Lazy Loading
    function initClientsLazyLoading() {
        // Disconnect existing observer if any
        if (clientsLazyLoadObserver) {
            clientsLazyLoadObserver.disconnect();
        }
        
        // Use Intersection Observer API for better performance
        const trigger = document.getElementById('clientsLazyLoadTrigger');
        
        if (!trigger) return;
        
        const observerOptions = {
            root: null,
            rootMargin: '200px',
            threshold: 0.1
        };
        
        clientsLazyLoadObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && clientsHasMoreData && !clientsIsLoading) {
                    loadMoreClients();
                }
            });
        }, observerOptions);
        
        clientsLazyLoadObserver.observe(trigger);
    }
    
    // Load More Clients
    function loadMoreClients() {
        if (clientsIsLoading || !clientsHasMoreData) return;
        
        clientsIsLoading = true;
        clientsCurrentPage++;
        
        $('#clientsLazyLoadSpinner').show();
        
        // Build params exactly like initial load
        const params = new URLSearchParams({
            page: clientsCurrentPage
        });
        
        // Add filter params if they exist
        if (clientsFilterParams.search) params.append('search', clientsFilterParams.search);
        if (clientsFilterParams.company) params.append('company', clientsFilterParams.company);
        if (clientsFilterParams.sort_by) params.append('sort_by', clientsFilterParams.sort_by);
        if (clientsFilterParams.sort_dir) params.append('sort_dir', clientsFilterParams.sort_dir);
        
        fetch('{{ route("clients.index") }}?' + params.toString(), {
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
                $('#tableClientsBody').append(data.html);
                
                clientsHasMoreData = data.hasMore !== false;
                
                if (!data.hasMore) {
                    $('#clientsLazyLoadEnd').show();
                    $('#clientsLazyLoadSpinner').hide();
                } else {
                    // Re-initialize lazy loading observer for new content after a brief delay
                    setTimeout(function() {
                        initClientsLazyLoading();
                    }, 100);
                }
            } else {
                clientsHasMoreData = false;
                $('#clientsLazyLoadSpinner').hide();
            }
        })
        .catch(error => {
            console.error('Error loading more clients:', error);
            clientsHasMoreData = false;
            $('#clientsLazyLoadSpinner').hide();
            if (typeof toastr !== 'undefined') {
                toastr.error('Failed to load more clients. Please try again.');
            }
        })
        .finally(() => {
            clientsIsLoading = false;
            $('#clientsLazyLoadSpinner').hide();
        });
    }
    
    // Initialize lazy loading on page load
    $(document).ready(function() {
        initClientsLazyLoading();
    });
});
</script>
@endpush

