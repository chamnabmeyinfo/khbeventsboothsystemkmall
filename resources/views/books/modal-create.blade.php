<!-- Create Booking Modal -->
<div class="modal fade" id="createBookingModal" tabindex="-1" role="dialog" aria-labelledby="createBookingModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 1200px;">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 20px 20px 0 0; padding: 24px 32px;">
                <h5 class="modal-title" id="createBookingModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-calendar-plus mr-2"></i>Create New Booking
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9; font-size: 1.5rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createBookingForm" method="POST" action="{{ route('books.store') }}">
                @csrf
                <div class="modal-body" style="padding: 32px; max-height: calc(100vh - 200px); overflow-y: auto;">
                    <div id="createBookingError" class="alert alert-danger" style="display: none; border-radius: 12px;"></div>
                    
                    <!-- Floor Plan Selection -->
                    <div class="form-section mb-4" style="background: #f8f9fc; padding: 20px; border-radius: 12px; border-left: 4px solid #667eea;">
                        <h6 style="color: #495057; font-weight: 600; margin-bottom: 16px;">
                            <i class="fas fa-map mr-2 text-primary"></i>Floor Plan (Optional Filter)
                        </h6>
                        <div class="form-group mb-0">
                            <select class="form-control" id="modal_floor_plan_filter" name="floor_plan_filter" style="border-radius: 8px;">
                                <option value="">All Floor Plans</option>
                                @foreach(\App\Models\FloorPlan::where('is_active', true)->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get() as $fp)
                                    <option value="{{ $fp->id }}">
                                        {{ $fp->name }}
                                        @if($fp->is_default) (Default) @endif
                                        @if($fp->event) - {{ $fp->event->title }} @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted mt-2">Select a floor plan to filter available booths</small>
                        </div>
                    </div>

                    <!-- Client Selection -->
                    <div class="form-section mb-4" style="background: #f8f9fc; padding: 20px; border-radius: 12px; border-left: 4px solid #48bb78;">
                        <h6 style="color: #495057; font-weight: 600; margin-bottom: 16px;">
                            <i class="fas fa-building mr-2 text-success"></i>Client Information
                        </h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-0">
                                    <label for="modal_clientid" class="form-label">Select Client <span class="text-danger">*</span></label>
                                    <select class="form-control" id="modal_clientid" name="clientid" required style="border-radius: 8px;">
                                        <option value="">Search or select a client...</option>
                                        @foreach(\App\Models\Client::orderBy('company')->get() as $client)
                                            <option value="{{ $client->id }}">
                                                {{ $client->company ?? $client->name }} 
                                                @if($client->company && $client->name) - {{ $client->name }} @endif
                                                @if($client->email) ({{ $client->email }}) @endif
                                                @if($client->phone_number) | {{ $client->phone_number }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-success btn-block" data-toggle="modal" data-target="#createClientModalInBooking" style="border-radius: 8px;">
                                        <i class="fas fa-plus mr-1"></i>New Client
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details -->
                    <div class="form-section mb-4" style="background: #f8f9fc; padding: 20px; border-radius: 12px; border-left: 4px solid #4299e1;">
                        <h6 style="color: #495057; font-weight: 600; margin-bottom: 16px;">
                            <i class="fas fa-calendar-alt mr-2 text-info"></i>Booking Details
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="modal_date_book" class="form-label">Booking Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="modal_date_book" name="date_book" 
                                           value="{{ now()->format('Y-m-d\TH:i') }}" required style="border-radius: 8px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label for="modal_type" class="form-label">Booking Type</label>
                                    <select class="form-control" id="modal_type" name="type" style="border-radius: 8px;">
                                        <option value="1" selected>Regular</option>
                                        <option value="2">Special</option>
                                        <option value="3">Temporary</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booth Selection -->
                    <div class="form-section mb-0" style="background: #f8f9fc; padding: 20px; border-radius: 12px; border-left: 4px solid #ed8936;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 style="color: #495057; font-weight: 600; margin-bottom: 0;">
                                <i class="fas fa-cube mr-2 text-warning"></i>Select Booths <span class="text-danger">*</span>
                            </h6>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" onclick="modalSelectAllBooths()" style="border-radius: 6px;">
                                    <i class="fas fa-check-double mr-1"></i>Select All
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="modalClearSelection()" style="border-radius: 6px;">
                                    <i class="fas fa-times mr-1"></i>Clear
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div id="modalBoothSelector" style="max-height: 350px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 16px; background: white;">
                                    <div class="row" id="modalBoothList">
                                        @foreach(\App\Models\Booth::whereIn('status', [\App\Models\Booth::STATUS_AVAILABLE, \App\Models\Booth::STATUS_HIDDEN])->orderBy('booth_number')->get() as $booth)
                                        <div class="col-md-6 mb-2">
                                            <div class="booth-option-modal border rounded p-2" data-booth-id="{{ $booth->id }}" data-price="{{ $booth->price ?? 0 }}" style="cursor: pointer; transition: all 0.2s; background: white;">
                                                <label class="mb-0 w-100" style="cursor: pointer;">
                                                    <input type="checkbox" name="booth_ids[]" value="{{ $booth->id }}" class="modal-booth-checkbox" onchange="modalUpdateSelection()">
                                                    <strong class="text-primary">{{ $booth->booth_number }}</strong>
                                                    <span class="badge badge-{{ $booth->getStatusColor() ?? 'secondary' }} ml-2" style="font-size: 0.75rem;">
                                                        {{ $booth->getStatusLabel() ?? 'Available' }}
                                                    </span>
                                                    @if($booth->category)
                                                    <br><small class="text-muted ml-4" style="font-size: 0.8125rem;">
                                                        <i class="fas fa-folder"></i> {{ $booth->category->name }}
                                                    </small>
                                                    @endif
                                                    <div class="mt-1 text-right">
                                                        <strong class="text-success" style="font-size: 0.875rem;">${{ number_format($booth->price ?? 0, 2) }}</strong>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="selected-booths-summary-modal" style="position: sticky; top: 0; background: white; padding: 16px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
                                    <h6 class="mb-3" style="font-weight: 600;"><i class="fas fa-list mr-2"></i>Selected Booths</h6>
                                    <div id="modalSelectedBoothsList" class="mb-3" style="max-height: 200px; overflow-y: auto; min-height: 100px;">
                                        <p class="text-muted text-center mb-0 py-4" style="font-size: 0.875rem;">No booths selected</p>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong style="font-size: 0.875rem;"><i class="fas fa-cube mr-1"></i>Total Booths:</strong>
                                        <span id="modalTotalBooths" class="badge badge-info" style="font-size: 0.875rem;">0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong style="font-size: 0.875rem;"><i class="fas fa-dollar-sign mr-1"></i>Total Amount:</strong>
                                        <span id="modalTotalAmount" class="text-success font-weight-bold" style="font-size: 1.1rem;">$0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted mt-2">
                            <i class="fas fa-info-circle mr-1"></i>Click on booths to select them. You can select multiple booths.
                        </small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 20px 32px; border-radius: 0 0 20px 20px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 12px; padding: 10px 24px;">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="createBookingSubmitBtn" style="border-radius: 12px; padding: 10px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                        <i class="fas fa-save mr-1"></i>Create Booking
                    </button>
                    <span id="modalSelectionWarning" class="text-danger ml-3" style="display: none; font-weight: 600;">
                        <i class="fas fa-exclamation-triangle mr-1"></i>Please select at least one booth
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Client Modal (Nested in Booking Modal) -->
<div class="modal fade" id="createClientModalInBooking" tabindex="-1" role="dialog" aria-labelledby="createClientModalInBookingLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title" id="createClientModalInBookingLabel" style="font-size: 1.25rem; font-weight: 700;">
                    <i class="fas fa-user-plus mr-2"></i>Create New Client
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createClientFormInBooking" method="POST" action="{{ route('clients.store') }}">
                @csrf
                <div class="modal-body" style="padding: 32px; max-height: 70vh; overflow-y: auto;">
                    <div id="createClientErrorInBooking" class="alert alert-danger" style="display: none; border-radius: 12px;"></div>
                    
                    <!-- Basic Information -->
                    <div class="form-group mb-4">
                        <h6 style="font-weight: 600; margin-bottom: 16px; color: #495057;"><i class="fas fa-user mr-2 text-primary"></i>Basic Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="modal_client_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_client_name" name="name" required placeholder="Enter client full name" style="border-radius: 8px;">
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
                                <label for="modal_client_company" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_client_company" name="company" required placeholder="Enter company name" style="border-radius: 8px;">
                            </div>
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
                                <label for="modal_client_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="modal_client_phone" name="phone_number" required placeholder="Enter phone number" style="border-radius: 8px;">
                            </div>
                            <div class="col-md-6">
                                <label for="modal_client_email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="modal_client_email" name="email" required placeholder="Enter email address" style="border-radius: 8px;">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <label for="modal_client_address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="modal_client_address" name="address" rows="2" required placeholder="Enter complete address" style="border-radius: 8px;"></textarea>
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
                    <button type="submit" class="btn btn-success" id="createClientSubmitBtnInBooking" style="border-radius: 12px; padding: 10px 24px;">
                        <i class="fas fa-save mr-1"></i>Create Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.booth-option-modal:hover {
    background-color: #e7f3ff !important;
    border-color: #667eea !important;
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
}

.booth-option-modal.selected {
    background-color: #cfe2ff !important;
    border-color: #667eea !important;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-section {
    transition: all 0.3s;
}

.form-section:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}
</style>

