@extends('layouts.adminlte')

@section('title', 'Create Booking')
@section('page-title', 'Create New Booking')
@section('breadcrumb', 'Bookings / Create')

@push('styles')
<style>
    .form-section {
        background: #f8f9fc;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #007bff;
    }
    .form-section h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    .booth-selector {
        max-height: 400px;
        overflow-y: auto;
        background: #f8f9fc;
    }
    .booth-option {
        cursor: pointer;
        transition: all 0.2s;
        background: white;
    }
    .booth-option:hover {
        background-color: #e7f3ff;
        border-color: #007bff !important;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,123,255,0.2);
    }
    .booth-option.selected {
        background-color: #cfe2ff;
        border-color: #007bff !important;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    .booth-option input[type="checkbox"] {
        margin-right: 0.5rem;
        cursor: pointer;
    }
    .booth-option label {
        cursor: pointer;
        width: 100%;
    }
    .selected-booths-summary {
        position: sticky;
        top: 0;
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calendar-plus mr-2"></i>Create New Booking</h3>
            <div class="card-tools">
                @if(isset($currentFloorPlan) && $currentFloorPlan)
                <a href="{{ route('booths.index', ['floor_plan_id' => $currentFloorPlan->id]) }}" class="btn btn-sm btn-info mr-2">
                    <i class="fas fa-map-marked-alt mr-1"></i>View Floor Plan Canvas
                </a>
                @endif
                <a href="{{ route('books.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Bookings
                </a>
            </div>
        </div>
        <form action="{{ route('books.store') }}" method="POST" id="bookingForm">
            @csrf
            <div class="card-body">
                @if(isset($currentFloorPlan) && $currentFloorPlan)
                <div class="alert alert-info">
                    <i class="fas fa-map mr-2"></i>
                    <strong>Booking for Floor Plan:</strong> {{ $currentFloorPlan->name }}
                    @if($currentFloorPlan->event) - {{ $currentFloorPlan->event->title }} @endif
                    <a href="{{ route('books.create') }}" class="btn btn-sm btn-secondary float-right">
                        <i class="fas fa-times mr-1"></i>Clear Filter
                    </a>
                </div>
                @endif

                <!-- Floor Plan Selection -->
                @if(isset($floorPlans) && $floorPlans->count() > 0)
                <div class="form-section">
                    <h6><i class="fas fa-map mr-2"></i>Floor Plan (Optional Filter)</h6>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="floor_plan_filter" class="form-label">Filter Booths by Floor Plan</label>
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
                                <small class="form-text text-muted">Select a floor plan to filter available booths, or leave blank to see all booths</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Client Selection -->
                <div class="form-section">
                    <h6><i class="fas fa-building mr-2"></i>Client Information</h6>
                    <div class="row">
                        <div class="col-md-8">
                            <label for="clientid" class="form-label">Select Client <span class="text-danger">*</span></label>
                            <select class="form-control @error('clientid') is-invalid @enderror" 
                                    id="clientid" name="clientid" required>
                                <option value="">Search or select a client...</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('clientid') == $client->id ? 'selected' : '' }}>
                                        {{ $client->company ?? $client->name }} 
                                        @if($client->company && $client->name) - {{ $client->name }} @endif
                                        @if($client->email) ({{ $client->email }}) @endif
                                        @if($client->phone_number) | {{ $client->phone_number }} @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('clientid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#createClientModal">
                                    <i class="fas fa-plus mr-1"></i>New Client
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="form-section">
                    <h6><i class="fas fa-calendar-alt mr-2"></i>Booking Details</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_book" class="form-label">Booking Date & Time <span class="text-danger">*</span></label>
                                <input type="datetime-local" 
                                       class="form-control @error('date_book') is-invalid @enderror" 
                                       id="date_book" 
                                       name="date_book" 
                                       value="{{ old('date_book', now()->format('Y-m-d\TH:i')) }}" 
                                       required>
                                @error('date_book')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type" class="form-label">Booking Type</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type">
                                    <option value="1" {{ old('type', 1) == 1 ? 'selected' : '' }}>
                                        <i class="fas fa-calendar"></i> Regular
                                    </option>
                                    <option value="2" {{ old('type') == 2 ? 'selected' : '' }}>Special</option>
                                    <option value="3" {{ old('type') == 3 ? 'selected' : '' }}>Temporary</option>
                                </select>
                                <small class="form-text text-muted">Select the type of booking</small>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booth Selection -->
                <div class="form-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0"><i class="fas fa-cube mr-2"></i>Select Booths <span class="text-danger">*</span></h6>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary" onclick="selectAllBooths()">
                                <i class="fas fa-check-double mr-1"></i>Select All
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                                <i class="fas fa-times mr-1"></i>Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="booth-selector" id="boothSelector" style="max-height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.35rem; padding: 1rem;">
                                @if($booths->count() > 0)
                                    <div class="row">
                                        @foreach($booths as $booth)
                                        <div class="col-md-6 mb-2">
                                            <div class="booth-option border rounded p-2" data-booth-id="{{ $booth->id }}" data-price="{{ $booth->price }}">
                                                <label class="mb-0 w-100" style="cursor: pointer;">
                                                    <input type="checkbox" 
                                                           name="booth_ids[]" 
                                                           value="{{ $booth->id }}" 
                                                           class="booth-checkbox"
                                                           {{ in_array($booth->id, old('booth_ids', [])) ? 'checked' : '' }}
                                                           onchange="updateSelection()">
                                                    <strong class="text-primary">{{ $booth->booth_number }}</strong>
                                                    <span class="badge badge-{{ $booth->getStatusColor() }} ml-2">
                                                        {{ $booth->getStatusLabel() }}
                                                    </span>
                                                    @if($booth->category)
                                                    <br><small class="text-muted ml-4">
                                                        <i class="fas fa-folder"></i> {{ $booth->category->name }}
                                                    </small>
                                                    @endif
                                                    <div class="mt-1 text-right">
                                                        <strong class="text-success">${{ number_format($booth->price, 2) }}</strong>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>No available booths found.
                                    </div>
                                @endif
                            </div>
                            @error('booth_ids')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <div class="selected-booths-summary">
                                <h6 class="mb-3"><i class="fas fa-list mr-2"></i>Selected Booths</h6>
                                <div id="selectedBoothsList" class="mb-3" style="max-height: 250px; overflow-y: auto; min-height: 100px;">
                                    <p class="text-muted text-center mb-0 py-4">No booths selected</p>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong><i class="fas fa-cube mr-1"></i>Total Booths:</strong>
                                    <span id="totalBooths" class="badge badge-info badge-lg">0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong><i class="fas fa-dollar-sign mr-1"></i>Total Amount:</strong>
                                    <span id="totalAmount" class="text-success font-weight-bold" style="font-size: 1.2rem;">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="form-text text-muted mt-2">
                        <i class="fas fa-info-circle mr-1"></i>Click on booths to select them. You can select multiple booths.
                    </small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save mr-1"></i>Create Booking
                </button>
                <a href="{{ route('books.index') }}" class="btn btn-default">Cancel</a>
                <span id="selectionWarning" class="text-danger ml-3" style="display: none;">
                    <i class="fas fa-exclamation-triangle mr-1"></i>Please select at least one booth
                </span>
            </div>
        </form>
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
                                <label for="modal_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_name" name="name" required placeholder="Enter client full name">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_sex" class="form-label">Gender</label>
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
                                <label for="modal_company" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_company" name="company" required placeholder="Enter company name">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_position" class="form-label">Position/Title</label>
                                <input type="text" class="form-control" id="modal_position" name="position" placeholder="Enter position or title">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-phone mr-2"></i>Contact Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_phone_number" name="phone_number" required placeholder="Enter phone number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="modal_email" name="email" required placeholder="Enter email address">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="modal_address" name="address" rows="2" required placeholder="Enter complete address (street, city, country)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-group">
                        <h6><i class="fas fa-info-circle mr-2"></i>Additional Information (Optional)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_tax_id" class="form-label">Tax ID / Business Registration Number</label>
                                <input type="text" class="form-control" id="modal_tax_id" name="tax_id" placeholder="Enter tax ID or business registration number">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="modal_website" name="website" placeholder="https://example.com">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control" id="modal_notes" name="notes" rows="2" placeholder="Enter any additional information or notes"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
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
        
        // Hide error message
        errorDiv.hide();
        
        // Validate form
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }
        
        // Disable submit button and show loading
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...');
        
        // Submit via AJAX
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
                    
                    // Add new client to dropdown
                    const clientSelect = $('#clientid');
                    const optionText = client.company + (client.name ? ' - ' + client.name : '') + 
                                     (client.email ? ' (' + client.email + ')' : '') + 
                                     (client.phone_number ? ' | ' + client.phone_number : '');
                    
                    const newOption = $('<option></option>')
                        .attr('value', client.id)
                        .text(optionText);
                    
                    clientSelect.append(newOption);
                    
                    // Select the newly created client
                    clientSelect.val(client.id);
                    
                    // If Select2 is initialized, trigger change for Select2
                    if (clientSelect.hasClass('select2-hidden-accessible')) {
                        clientSelect.trigger('change.select2');
                    } else {
                        clientSelect.trigger('change');
                    }
                    
                    // Close modal and reset form
                    $('#createClientModal').modal('hide');
                    form[0].reset();
                    errorDiv.hide();
                    
                    // Show success message
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
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    const firstError = Object.values(errors)[0];
                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                }
                
                errorDiv.html('<i class="fas fa-exclamation-triangle mr-1"></i>' + errorMessage);
                errorDiv.show();
            },
            complete: function() {
                // Re-enable submit button
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
});

// Select2 for client dropdown
$(document).ready(function() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('#clientid').select2({
            placeholder: 'Search or select a client...',
            allowClear: true,
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('#clientid').closest('.form-section')
        });
    }
    
    // Update selection on page load if there are checked boxes
    updateSelection();
    
    // Also update when checkboxes change
    $('.booth-checkbox').on('change', function() {
        updateSelection();
    });
});

// Select/Deselect booth options (delegated event handler for dynamic content)
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
        const boothNumber = boothOption.find('strong').text();
        const price = parseFloat(boothOption.data('price')) || 0;
        
        selected.push({ id: boothId, number: boothNumber, price: price });
        totalAmount += price;
    });
    
    // Update selected list
    const listContainer = $('#selectedBoothsList');
    if (selected.length > 0) {
        let html = '';
        selected.forEach(function(booth) {
            html += '<div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded border">';
            html += '<div><i class="fas fa-cube text-primary mr-1"></i><strong>' + booth.number + '</strong></div>';
            html += '<strong class="text-success">$' + booth.price.toFixed(2) + '</strong>';
            html += '</div>';
        });
        listContainer.html(html);
    } else {
        listContainer.html('<p class="text-muted text-center mb-0">No booths selected</p>');
    }
    
    // Update summary
    $('#totalBooths').text(selected.length);
    $('#totalAmount').text('$' + totalAmount.toFixed(2));
    
    // Update visual state
    $('.booth-option').removeClass('selected');
    $('.booth-checkbox:checked').closest('.booth-option').addClass('selected');
    
    // Show/hide warning
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

// Form validation
$('#bookingForm').on('submit', function(e) {
    const selectedCount = $('.booth-checkbox:checked').length;
    if (selectedCount === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'No Booths Selected',
            text: 'Please select at least one booth for this booking.',
            confirmButtonColor: '#007bff'
        });
        return false;
    }
    
    showLoading();
});

// Initialize on page load
updateSelection();
</script>
@endpush

