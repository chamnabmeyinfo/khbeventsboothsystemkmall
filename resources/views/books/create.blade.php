@extends('layouts.adminlte')

@section('title', 'Create Booking')
@section('page-title', 'Create New Booking')
@section('breadcrumb', 'Bookings / Create')


@section('content')
<section class="content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="m-0">Create New Booking</h1>
                    <p class="text-muted mb-0">Fill in the form below to create a new booking</p>
                </div>
                <div>
                    @if(isset($currentFloorPlan) && $currentFloorPlan)
                    <a href="{{ route('booths.index', ['view' => 'canvas', 'floor_plan_id' => $currentFloorPlan->id]) }}" class="btn btn-info btn-sm mr-2">
                        <i class="fas fa-map-marked-alt mr-1"></i>View Floor Plan
                    </a>
                    @endif
                    <a href="{{ route('books.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking Information</h3>
            </div>
            <form action="{{ route('books.store') }}" method="POST" id="bookingForm">
                @csrf
                <div class="card-body">
                    <!-- Floor Plan Alert -->
                    @if(isset($currentFloorPlan) && $currentFloorPlan)
                    <div class="alert alert-info mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-map mr-2"></i>
                                <strong>Floor Plan:</strong> {{ $currentFloorPlan->name }}
                                @if($currentFloorPlan->event) - {{ $currentFloorPlan->event->title }} @endif
                            </div>
                            <a href="{{ route('books.create') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times mr-1"></i>Clear Filter
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Floor Plan Selection -->
                    @if(isset($floorPlans) && $floorPlans->count() > 0)
                    <div class="form-group">
                        <label for="floor_plan_filter">Floor Plan</label>
                        <select class="form-control" id="floor_plan_filter" name="floor_plan_filter" onchange="filterByFloorPlan(this.value)">
                            <option value="">All Floor Plans</option>
                            @foreach($floorPlans as $fp)
                                <option value="{{ $fp->id }}" {{ (isset($floorPlanId) && $floorPlanId == $fp->id) ? 'selected' : '' }}>
                                    {{ $fp->name }}
                                    @if($fp->is_default) (Default) @endif
                                    @if($fp->event) - {{ $fp->event->title }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Client Selection -->
                    <div class="form-group">
                        <label for="clientSearchInline">Client <span class="text-danger">*</span></label>
                        <input type="hidden" id="clientid" name="clientid" value="{{ old('clientid') }}" required>
                        
                        <!-- Selected Client Display -->
                        <div id="selectedClientInfo" style="display: none;">
                            <div class="alert alert-success">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong id="selectedClientName"></strong>
                                        <div id="selectedClientDetails" class="text-muted"></div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnClearClient">Change</button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Client Search -->
                        <div id="clientSearchContainer">
                            <div style="position: relative;">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control @error('clientid') is-invalid @enderror" 
                                           id="clientSearchInline" 
                                           placeholder="Search client..." 
                                           autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary" id="btnSearchSelectClient" data-toggle="modal" data-target="#searchClientModal">Search</button>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createClientModal">New</button>
                                    </div>
                                </div>
                                
                                <!-- Search Results Dropdown -->
                                <div id="inlineClientResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1050; margin-top: 2px;">
                                    <div class="card">
                                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                            <div id="inlineClientResultsList"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @error('clientid')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_book">Date & Time</label>
                                <input type="datetime-local" 
                                       class="form-control @error('date_book') is-invalid @enderror" 
                                       id="date_book" 
                                       name="date_book" 
                                       value="{{ old('date_book', now()->format('Y-m-d\TH:i')) }}">
                                @error('date_book')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Booking Type</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                    <option value="1" {{ old('type', 1) == 1 ? 'selected' : '' }}>Regular</option>
                                    <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>Special</option>
                                    <option value="3" {{ old('type') == 3 ? 'selected' : '' }}>Temporary</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Booth Selection -->
                    <div class="form-group">
                        <label>Select Booths <span class="text-danger">*</span></label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-primary btn-sm" onclick="selectAllBooths()">All</button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelection()">Clear</button>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div id="boothSelector" style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; padding: 10px; border-radius: 4px;">
                                    @if($booths->count() > 0)
                                        <div class="row">
                                            @foreach($booths as $booth)
                                            <div class="col-md-6 mb-2">
                                                <div class="booth-option" data-booth-id="{{ $booth->id }}" data-price="{{ $booth->price }}">
                                                    <label style="cursor: pointer;">
                                                        <input type="checkbox" 
                                                               name="booth_ids[]" 
                                                               value="{{ $booth->id }}" 
                                                               class="booth-checkbox mr-2"
                                                               {{ in_array($booth->id, old('booth_ids', [])) ? 'checked' : '' }}
                                                               onchange="updateSelection()">
                                                        <strong>{{ $booth->booth_number }}</strong>
                                                        <span class="badge badge-sm ml-1" style="background: {{ $booth->getStatusColor() == 'success' ? '#28a745' : ($booth->getStatusColor() == 'warning' ? '#ffc107' : ($booth->getStatusColor() == 'danger' ? '#dc3545' : '#17a2b8')) }}; color: white;">
                                                            {{ $booth->getStatusLabel() }}
                                                        </span>
                                                        <strong class="text-success float-right">${{ number_format($booth->price, 2) }}</strong>
                                                        @if($booth->category)
                                                        <br><small class="text-muted">{{ $booth->category->name }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-warning text-center">
                                            <strong>No available booths found.</strong>
                                        </div>
                                    @endif
                                </div>
                                @error('booth_ids')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="selectedBoothsList" style="max-height: 200px; overflow-y: auto; min-height: 60px;">
                                            <p class="text-muted text-center mb-0 py-3 small">No booths selected</p>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Total Booths:</span>
                                            <span id="totalBooths" class="badge badge-primary">0</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <strong>Total Amount:</strong>
                                            <strong id="totalAmount" class="text-success">$0.00</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save mr-1"></i>Create Booking
                    </button>
                    <a href="{{ route('books.index') }}" class="btn btn-secondary ml-2">Cancel</a>
                    <span id="selectionWarning" class="text-danger ml-3" style="display: none;">Please select at least one booth</span>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Search & Select Client Modal -->
<div class="modal fade" id="searchClientModal" tabindex="-1" role="dialog" aria-labelledby="searchClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchClientModalLabel">
                    <i class="fas fa-search mr-2"></i>Search & Select Client
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="clientSearchInput">Search Client</label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="clientSearchInput" 
                               placeholder="Type to search by name, company, email, or phone number..." 
                               autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btnSearchClient">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-secondary" id="btnClearClientSearch" style="display: none;">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted mt-2">Type at least 2 characters to search for existing clients</small>
                </div>
                
                <div id="clientSearchResults" class="mt-4" style="display: none;">
                    <h6 class="mb-3 font-weight-bold">Search Results</h6>
                    <div id="clientSearchResultsList" style="max-height: 450px; overflow-y: auto; padding: 0.75rem;"></div>
                </div>
                
                <div id="noClientResults" class="alert alert-info mt-4 text-center" style="display: none;">
                    <p class="mb-0"><strong>No clients found.</strong> You can create a new client using the "New Client" button.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create Client Modal -->
<div class="modal fade" id="createClientModal" tabindex="-1" role="dialog" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createClientModalLabel">
                    <i class="fas fa-user-plus mr-2"></i>Create New Client
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createClientForm" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div id="createClientError" class="alert alert-danger" style="display: none;"></div>
                    
                    <!-- Basic Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-user mr-2"></i>Basic Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_name">Full Name</label>
                                <input type="text" class="form-control" id="modal_name" name="name" placeholder="Enter client full name">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_sex">Gender</label>
                                <select class="form-control" id="modal_sex" name="sex">
                                    <option value="">Select Gender...</option>
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                    <option value="3">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Company Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-building mr-2"></i>Company Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_company">Company Name</label>
                                <input type="text" class="form-control" id="modal_company" name="company" placeholder="Enter company name">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_company_name_khmer">Company Name (Khmer)</label>
                                <input type="text" class="form-control" id="modal_company_name_khmer" name="company_name_khmer" placeholder="Enter company name in Khmer">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_position">Position/Title</label>
                                <input type="text" class="form-control" id="modal_position" name="position" placeholder="Enter position or title">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-phone mr-2"></i>Contact Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_phone_number">Phone Number</label>
                                <input type="text" class="form-control" id="modal_phone_number" name="phone_number" placeholder="Enter phone number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_phone_1">Phone 1</label>
                                <input type="text" class="form-control" id="modal_phone_1" name="phone_1" placeholder="Enter primary phone number">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_phone_2">Phone 2</label>
                                <input type="text" class="form-control" id="modal_phone_2" name="phone_2" placeholder="Enter secondary phone number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_email">Email Address</label>
                                <input type="email" class="form-control" id="modal_email" name="email" placeholder="Enter email address">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label for="modal_email_1">Email 1</label>
                                <input type="email" class="form-control" id="modal_email_1" name="email_1" placeholder="Enter primary email address">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_email_2">Email 2</label>
                                <input type="email" class="form-control" id="modal_email_2" name="email_2" placeholder="Enter secondary email address">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_address">Address</label>
                                <textarea class="form-control" id="modal_address" name="address" rows="2" placeholder="Enter complete address (street, city, country)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-info-circle mr-2"></i>Additional Information (Optional)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_tax_id">Tax ID / Business Registration Number</label>
                                <input type="text" class="form-control" id="modal_tax_id" name="tax_id" placeholder="Enter tax ID or business registration number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_website">Website</label>
                                <input type="url" class="form-control" id="modal_website" name="website" placeholder="https://example.com">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_notes">Additional Notes</label>
                                <textarea class="form-control" id="modal_notes" name="notes" rows="2" placeholder="Enter any additional information or notes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="createClientSubmitBtn">
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
function filterByFloorPlan(floorPlanId) {
    if (floorPlanId) {
        window.location.href = '{{ route("books.create") }}?floor_plan_id=' + floorPlanId;
    } else {
        window.location.href = '{{ route("books.create") }}';
    }
}

// Handle Create Client Modal Form Submission
$(document).ready(function() {
    $('#createClientForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = $('#createClientSubmitBtn');
        const errorDiv = $('#createClientError');
        const originalText = submitBtn.html();
        
        errorDiv.hide();
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
                    const client = response.client;
                    
                    if (typeof selectClient === 'function') {
                        selectClient(client);
                    } else {
                        $('#clientid').val(client.id);
                        const displayText = client.company + (client.name ? ' - ' + client.name : '') + 
                                         (client.email ? ' (' + client.email + ')' : '') + 
                                         (client.phone_number ? ' | ' + client.phone_number : '');
                        $('#clientSearchInline').val(displayText);
                        $('#selectedClientName').text(client.company || client.name);
                        let details = [];
                        if (client.name && client.company) details.push(client.name);
                        if (client.email) details.push(client.email);
                        if (client.phone_number) details.push(client.phone_number);
                        $('#selectedClientDetails').text(details.join(' • '));
                        $('#selectedClientInfo').show();
                    }
                    
                    $('#createClientModal').modal('hide');
                    form[0].reset();
                    errorDiv.hide();
                    
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Client Created!',
                            text: 'Client "' + client.company + '" has been created and selected.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('Client created successfully!');
                    }
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
                if (errorDiv.length > 0 && errorDiv[0]) {
                    try {
                        errorDiv[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } catch (e) {
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
    
    $('#createClientModal').on('hidden.bs.modal', function() {
        $('#createClientForm')[0].reset();
        $('#createClientError').hide();
    });
});

// Client Search & Select Functionality
$(document).ready(function() {
    let clientSearchTimeout;
    let inlineSearchTimeout;
    let selectedClient = null;
    
    @if(old('clientid'))
        const oldClientId = {{ old('clientid') }};
        $.ajax({
            url: '{{ route("clients.search") }}',
            method: 'GET',
            data: { q: '', id: oldClientId },
            success: function(clients) {
                if (clients && clients.length > 0) {
                    const client = clients.find(c => c.id == oldClientId);
                    if (client) {
                        selectClient(client);
                    }
                }
            }
        });
    @endif
    
    function searchClientsInline(query) {
        if (!query || query.length < 2) {
            $('#inlineClientResults').hide();
            return;
        }
        
        const resultsDiv = $('#inlineClientResults');
        const resultsList = $('#inlineClientResultsList');
        const searchIcon = $('#searchIcon');
        
        if (searchIcon.length) {
            searchIcon.removeClass('fa-search').addClass('fa-spinner fa-spin');
        }
        
        resultsDiv.show();
        resultsList.html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-3">Searching...</p></div>');
        
        $.ajax({
            url: '{{ route("clients.search") }}',
            method: 'GET',
            data: { q: query },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(clients) {
                if (searchIcon.length) {
                    searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                }
                
                resultsList.empty();
                const clientsArray = Array.isArray(clients) ? clients : (clients ? Object.values(clients) : []);
                
                if (!clientsArray || clientsArray.length === 0) {
                    resultsList.html('<div class="text-center p-4 text-muted"><i class="fas fa-search fa-2x mb-3"></i><p class="mb-0"><strong>No clients found</strong></p><p class="mb-0 mt-2 small">Try different keywords or create a new client</p></div>');
                    return;
                }
                
                clientsArray.slice(0, 8).forEach(function(client) {
                    const displayName = (client.company || client.name || 'N/A');
                    const item = $('<div class="client-search-result"></div>')
                        .html(
                            '<div class="d-flex justify-content-between align-items-center">' +
                                '<div class="flex-grow-1">' +
                                    '<strong>' + displayName + '</strong>' +
                                    (client.email ? '<br><small class="text-muted"><i class="fas fa-envelope mr-1"></i>' + client.email + '</small>' : '') +
                                    (client.phone_number ? '<br><small class="text-muted"><i class="fas fa-phone mr-1"></i>' + client.phone_number + '</small>' : '') +
                                '</div>' +
                                '<button type="button" class="btn btn-primary btn-sm select-client-inline-btn ml-3" data-client-id="' + client.id + '">Select</button>' +
                            '</div>'
                        )
                        .data('client', client);
                    
                    resultsList.append(item);
                });
                
                $(document).off('click', '.select-client-inline-btn').on('click', '.select-client-inline-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const client = $(this).closest('.client-search-result').data('client');
                    if (client) {
                        selectClient(client);
                        $('#inlineClientResults').hide();
                    }
                });
                
                $(document).off('click', '#inlineClientResultsList .client-search-result').on('click', '#inlineClientResultsList .client-search-result', function(e) {
                    if (!$(e.target).closest('.select-client-inline-btn').length) {
                        e.preventDefault();
                        const client = $(this).data('client');
                        if (client) {
                            selectClient(client);
                            $('#inlineClientResults').hide();
                        }
                    }
                });
            },
            error: function(xhr) {
                if (searchIcon.length) {
                    searchIcon.removeClass('fa-spinner fa-spin').addClass('fa-search');
                }
                resultsList.html('<div class="text-center p-4 text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><p class="mb-0"><strong>Error</strong></p><p class="mb-0 mt-2 small">Error searching clients. Please try again.</p></div>');
            }
        });
    }
    
    $('#clientSearchInline').on('input keyup paste', function(e) {
        if ([38, 40, 13, 27].includes(e.keyCode)) {
            return;
        }
        
        const query = $(this).val().trim();
        clearTimeout(inlineSearchTimeout);
        
        if (query.length < 2) {
            $('#inlineClientResults').hide();
            if (query.length === 0 && selectedClient) {
                selectedClient = null;
                $('#clientid').val('');
                $('#selectedClientInfo').hide();
                $('#clientSearchContainer').show();
            }
            return;
        }
        
        inlineSearchTimeout = setTimeout(function() {
            searchClientsInline(query);
        }, 300);
    });
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#clientSearchInline, #inlineClientResults').length) {
            $('#inlineClientResults').hide();
        }
    });
    
    function searchClients(query) {
        if (!query || query.length < 2) {
            $('#clientSearchResults').hide();
            $('#noClientResults').hide();
            return;
        }
        
        $.ajax({
            url: '{{ route("clients.search") }}',
            method: 'GET',
            data: { q: query },
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(clients) {
                const resultsDiv = $('#clientSearchResults');
                const resultsList = $('#clientSearchResultsList');
                const noResultsDiv = $('#noClientResults');
                
                resultsList.empty();
                const clientsArray = Array.isArray(clients) ? clients : (clients ? Object.values(clients) : []);
                
                if (!clientsArray || clientsArray.length === 0) {
                    resultsDiv.hide();
                    noResultsDiv.show();
                    return;
                }
                
                noResultsDiv.hide();
                resultsDiv.show();
                
                clientsArray.forEach(function(client) {
                    const displayName = (client.company || client.name || 'N/A');
                    const item = $('<div class="client-search-result"></div>')
                        .html(
                            '<div class="d-flex justify-content-between align-items-center">' +
                                '<div class="flex-grow-1">' +
                                    '<strong>' + displayName + '</strong>' +
                                    (client.email ? '<br><small class="text-muted"><i class="fas fa-envelope mr-1"></i>' + client.email + '</small>' : '') +
                                    (client.phone_number ? '<br><small class="text-muted"><i class="fas fa-phone mr-1"></i>' + client.phone_number + '</small>' : '') +
                                '</div>' +
                                '<button type="button" class="btn btn-primary btn-sm select-client-btn ml-3" data-client-id="' + client.id + '">Select</button>' +
                            '</div>'
                        )
                        .data('client', client);
                    
                    resultsList.append(item);
                });
                
                $('.select-client-btn').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const client = $(this).closest('.client-search-result').data('client');
                    selectClient(client);
                });
                
                resultsList.find('.client-search-result').on('click', function(e) {
                    if (!$(e.target).closest('.select-client-btn').length) {
                        e.preventDefault();
                        const client = $(this).data('client');
                        selectClient(client);
                    }
                });
            },
            error: function(xhr) {
                $('#clientSearchResults').hide();
                $('#noClientResults').show();
            }
        });
    }
    
    function selectClient(client) {
        selectedClient = client;
        $('#clientid').val(client.id);
        
        const displayName = client.company || client.name || 'N/A';
        let details = [];
        if (client.email) details.push(client.email);
        if (client.phone_number) details.push(client.phone_number);
        
        $('#selectedClientName').text(displayName);
        $('#selectedClientDetails').html(details.length > 0 ? details.join(' <span class="mx-2">|</span> ') : '');
        $('#selectedClientInfo').show();
        $('#clientSearchContainer').hide();
        $('#clientSearchInline').val('');
        $('#inlineClientResults').hide();
        $('#searchClientModal').modal('hide');
        $('#clientSearchInput').val('');
        $('#clientSearchResults').hide();
        $('#noClientResults').hide();
        $('#btnClearClientSearch').hide();
    }
    
    $('#btnClearClient').on('click', function() {
        selectedClient = null;
        $('#clientid').val('');
        $('#selectedClientInfo').hide();
        $('#clientSearchContainer').show();
        $('#clientSearchInline').val('');
        $('#inlineClientResults').hide();
        setTimeout(function() {
            $('#clientSearchInline').focus();
        }, 100);
    });
    
    $('#clientSearchInput').on('input keyup', function(e) {
        const query = $(this).val().trim();
        clearTimeout(clientSearchTimeout);
        
        if (query.length < 2) {
            $('#clientSearchResults').hide();
            $('#noClientResults').hide();
            $('#btnClearClientSearch').hide();
            return;
        }
        
        $('#btnClearClientSearch').show();
        clientSearchTimeout = setTimeout(function() {
            searchClients(query);
        }, 300);
    });
    
    $('#btnSearchClient').on('click', function() {
        const query = $('#clientSearchInput').val().trim();
        if (query.length >= 2) {
            searchClients(query);
        }
    });
    
    $('#btnClearClientSearch').on('click', function() {
        $('#clientSearchInput').val('');
        $('#clientSearchResults').hide();
        $('#noClientResults').hide();
        $(this).hide();
    });
    
    $('#searchClientModal').on('hidden.bs.modal', function() {
        $('#clientSearchInput').val('');
        $('#clientSearchResults').hide();
        $('#noClientResults').hide();
        $('#btnClearClientSearch').hide();
    });
    
    updateSelection();
    $('.booth-checkbox').on('change', function() {
        updateSelection();
    });
});

$(document).on('click', '.booth-option', function(e) {
    if (e.target.type !== 'checkbox' && !$(e.target).closest('input').length) {
        const checkbox = $(this).find('input[type="checkbox"]');
        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
    }
});

function updateSelection() {
    const selected = [];
    let totalAmount = 0;
    
    $('.booth-checkbox:checked').each(function() {
        const boothId = $(this).val();
        const boothOption = $(this).closest('.booth-option');
        const boothNumber = boothOption.find('strong').first().text();
        const price = parseFloat(boothOption.data('price')) || 0;
        
        selected.push({ id: boothId, number: boothNumber, price: price });
        totalAmount += price;
    });
    
    const listContainer = $('#selectedBoothsList');
    if (selected.length > 0) {
        let html = '';
        selected.forEach(function(booth) {
            html += '<div class="selected-booth-item">';
            html += '<div class="d-flex justify-content-between align-items-center">';
            html += '<div><strong>' + booth.number + '</strong></div>';
            html += '<strong class="text-success">$' + booth.price.toFixed(2) + '</strong>';
            html += '</div>';
            html += '</div>';
        });
        listContainer.html(html);
    } else {
        listContainer.html('<p class="text-muted text-center mb-0 py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i><small>No booths selected</small></p>');
    }
    
    $('#totalBooths').text(selected.length);
    $('#totalAmount').text('$' + totalAmount.toFixed(2));
    
    $('.booth-option').removeClass('selected');
    $('.booth-checkbox:checked').closest('.booth-option').addClass('selected');
    
    if (selected.length === 0) {
        $('#selectionWarning').show();
        $('#submitBtn').prop('disabled', true);
    } else {
        $('#selectionWarning').hide();
        $('#submitBtn').prop('disabled', false);
    }
}

function selectAllBooths() {
    $('.booth-checkbox').prop('checked', true);
    updateSelection();
}

function clearSelection() {
    $('.booth-checkbox').prop('checked', false);
    updateSelection();
}

$('#bookingForm').on('submit', function(e) {
    const clientId = $('#clientid').val();
    if (!clientId || clientId === '' || clientId === null) {
        e.preventDefault();
        e.stopPropagation();
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Client Required',
                text: 'Please select a client before submitting the booking.',
                confirmButtonColor: '#667eea'
            });
        } else {
            alert('Please select a client before submitting the booking.');
        }
        
        if ($('#clientSearchContainer').is(':hidden')) {
            $('#selectedClientInfo').hide();
            $('#clientSearchContainer').show();
        }
        
        setTimeout(function() {
            $('#clientSearchInline').focus();
        }, 100);
        
        return false;
    }
    
    const selectedCount = $('.booth-checkbox:checked').length;
    if (selectedCount === 0) {
        e.preventDefault();
        e.stopPropagation();
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'No Booths Selected',
                text: 'Please select at least one booth for this booking.',
                confirmButtonColor: '#667eea'
            });
        } else {
            alert('Please select at least one booth for this booking.');
        }
        
        return false;
    }
});

updateSelection();
</script>
@endpush
