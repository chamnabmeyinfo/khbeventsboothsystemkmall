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
                <div class="modal-body" style="padding: 32px; max-height: calc(100vh - 200px); overflow-y: auto; overflow-x: hidden; position: relative;">
                    <div id="createBookingError" class="alert alert-danger" style="display: none; border-radius: 12px; margin-bottom: 1.5rem;"></div>
                    
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
                    <div class="form-section mb-4 client-search-section-modal" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(102, 126, 234, 0.1); border-radius: 16px; padding: 2rem; border-left: 4px solid #48bb78; position: relative; z-index: 10; margin-bottom: 2rem !important;">
                        <h6 style="color: #1a1a2e; font-weight: 700; margin-bottom: 1.5rem; font-size: 1.1rem;">
                            <i class="fas fa-building mr-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"></i>Client Information
                        </h6>
                        <input type="hidden" id="modal_clientid" name="clientid" value="" required>
                        
                        <!-- Selected Client Display -->
                        <div id="modalSelectedClientInfo" class="mb-3" style="display: none;">
                            <div class="selected-client-card" style="background: linear-gradient(135deg, rgba(28, 200, 138, 0.08) 0%, rgba(23, 166, 115, 0.08) 100%); border: 2px solid rgba(28, 200, 138, 0.3); border-radius: 12px; padding: 1.25rem; box-shadow: 0 4px 12px rgba(28, 200, 138, 0.1);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-check-circle text-success mr-2" style="font-size: 1.2rem;"></i>
                                            <strong id="modalSelectedClientName" class="d-block mb-0" style="font-size: 1.05rem; color: #1a1a2e;"></strong>
                                        </div>
                                        <small id="modalSelectedClientDetails" class="text-muted d-block ml-4"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger ml-3" id="modalBtnClearClient">
                                        <i class="fas fa-times mr-1"></i>Change Client
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Client Search (shown when no client selected) -->
                        <div id="modalClientSearchContainer">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="modalClientSearchInline" class="form-label font-weight-bold">Search Client <span class="text-danger">*</span></label>
                                    <div class="client-search-wrapper-modal" style="position: relative;">
                                        <div class="input-group input-group-modern-modal">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white">
                                                    <i class="fas fa-search text-muted" id="modalSearchIcon"></i>
                                                </span>
                                            </div>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="modalClientSearchInline" 
                                                   placeholder="Type client name, company, email, or phone number..." 
                                                   autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-primary" id="modalBtnSearchSelectClient" data-toggle="modal" data-target="#modalSearchClientModal">
                                                    <i class="fas fa-search-plus mr-1"></i>Advanced
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Inline Search Results Dropdown -->
                                        <div id="modalInlineClientResults" class="modal-client-results-dropdown" style="display: none;">
                                            <div class="modal-dropdown-card">
                                                <div id="modalInlineClientResultsList" class="p-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="form-text text-muted mb-0">
                                            <i class="fas fa-info-circle mr-1"></i>Start typing to see instant suggestions
                                        </small>
                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#createClientModalInBooking">
                                            <i class="fas fa-plus mr-1"></i>Create New Client
                                        </button>
                                    </div>
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
                                                    <input type="checkbox" name="booth_ids[]" value="{{ $booth->id }}" class="modal-booth-checkbox">
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
                    <button type="submit" class="btn btn-success" id="createClientSubmitBtnInBooking" style="border-radius: 12px; padding: 10px 24px;">
                        <i class="fas fa-save mr-1"></i>Create Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Search & Select Client Modal (for Create Booking Modal) -->
<div class="modal fade" id="modalSearchClientModal" tabindex="-1" role="dialog" aria-labelledby="modalSearchClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-content-modern" style="border-radius: 16px; border: none; box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15); overflow: hidden;">
            <div class="modal-header modal-header-modern" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border: none;">
                <h5 class="modal-title" id="modalSearchClientModalLabel">
                    <i class="fas fa-search mr-2"></i>Search & Select Client
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="form-group">
                    <label for="modalClientSearchInput" class="font-weight-bold mb-2">
                        <i class="fas fa-search mr-1"></i> Search Client
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               class="form-control form-control-modern" 
                               id="modalClientSearchInput" 
                               placeholder="Type to search by name, company, email, or phone number..." 
                               autocomplete="off">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-modern btn-modern-primary" id="modalBtnSearchClient">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <button type="button" class="btn btn-modern" id="modalBtnClearClientSearch" style="background: #6c757d; color: white; display: none;">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted mt-2"><i class="fas fa-info-circle mr-1"></i>Type at least 2 characters to search for existing clients</small>
                </div>
                
                <div id="modalClientSearchResults" class="mt-4" style="display: none;">
                    <h6 class="mb-3 font-weight-bold"><i class="fas fa-list mr-1"></i>Search Results</h6>
                    <div id="modalClientSearchResultsList" style="max-height: 450px; overflow-y: auto; padding: 0.75rem;"></div>
                </div>
                
                <div id="modalNoClientResults" class="alert alert-modern alert-modern-info mt-4 text-center" style="display: none; border-radius: 12px; border: none; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); background: linear-gradient(135deg, rgba(54, 185, 204, 0.1) 0%, rgba(44, 159, 175, 0.1) 100%); border-left: 4px solid #36b9cc; color: #1a1a2e;">
                    <i class="fas fa-info-circle mr-2" style="font-size: 1.5rem;"></i>
                    <p class="mb-0 mt-2"><strong>No clients found.</strong> You can create a new client using the "New Client" button.</p>
                </div>
            </div>
            <div class="modal-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
                <button type="button" class="btn btn-modern" style="background: #6c757d; color: white;" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================
   MODAL CLIENT SEARCH - ISOLATED STYLES
   Prevents conflicts with main page styles
   ============================================ */

/* Modal-specific client search wrapper */
.client-search-wrapper-modal {
    position: relative;
    z-index: 1050;
    margin-bottom: 0;
}

.input-group-modern-modal {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border-radius: 12px;
    overflow: visible;
    position: relative;
    background: white;
    border: 2px solid rgba(102, 126, 234, 0.1);
}

.input-group-modern-modal .input-group-prepend .input-group-text {
    border-radius: 12px 0 0 12px;
    border: 2px solid rgba(102, 126, 234, 0.1);
    border-right: none;
    background: white;
    padding: 0.75rem 1rem;
}

.input-group-modern-modal .form-control {
    border: none;
    border-top: 2px solid rgba(102, 126, 234, 0.1);
    border-bottom: 2px solid rgba(102, 126, 234, 0.1);
    padding: 0.75rem 1rem;
    background: white;
}

.input-group-modern-modal .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    z-index: 2;
}

.input-group-modern-modal .input-group-append .btn {
    border-radius: 0 12px 12px 0;
    border-left: 2px solid rgba(102, 126, 234, 0.1);
}

/* Modal dropdown - isolated from main page */
.modal-client-results-dropdown {
    position: absolute;
    top: calc(100% + 0.5rem);
    left: 0;
    right: 0;
    z-index: 10000 !important; /* Higher than Bootstrap modal (1050) */
    animation: modalSlideDown 0.2s ease-out;
    pointer-events: auto;
}

@keyframes modalSlideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-dropdown-card {
    max-height: 400px;
    overflow-y: auto;
    overflow-x: hidden;
    box-shadow: 
        0 12px 48px rgba(0, 0, 0, 0.25),
        0 4px 16px rgba(102, 126, 234, 0.2);
    border: 2px solid rgba(102, 126, 234, 0.3);
    border-radius: 12px;
    background: white;
    position: relative;
    z-index: 10000;
}

/* Ensure modal sections don't clip dropdowns */
.client-search-section-modal {
    overflow: visible !important;
    position: relative;
    z-index: 10;
}

/* Modal body adjustments */
#createBookingModal .modal-body {
    overflow-x: hidden;
    position: relative;
}

/* Prevent modal backdrop from interfering */
.modal-backdrop {
    z-index: 1040;
}

#createBookingModal {
    z-index: 1050;
}

#modalSearchClientModal {
    z-index: 1060;
}

#createClientModalInBooking {
    z-index: 1070;
}

/* Client Search Result Items - Modal Specific */
#modalInlineClientResultsList .client-search-result {
    border: 2px solid rgba(102, 126, 234, 0.1);
    border-radius: 10px;
    padding: 0.875rem 1rem;
    margin-bottom: 0.5rem;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    background: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}

#modalInlineClientResultsList .client-search-result::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    transform: scaleY(0);
    transition: transform 0.25s ease;
}

#modalInlineClientResultsList .client-search-result:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
    transform: translateX(4px);
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
}

#modalInlineClientResultsList .client-search-result:hover::before {
    transform: scaleY(1);
}

#modalInlineClientResultsList .client-search-result.highlighted {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.12) 0%, rgba(118, 75, 162, 0.12) 100%);
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.25);
}

#modalInlineClientResultsList .client-search-result.highlighted::before {
    transform: scaleY(1);
}

#modalInlineClientResultsList .client-result-content {
    flex: 1;
    min-width: 0;
}

#modalInlineClientResultsList .client-result-name {
    color: #1a1a2e;
    font-weight: 700;
    font-size: 0.95rem;
    margin-bottom: 0.375rem;
    display: flex;
    align-items: center;
}

#modalInlineClientResultsList .client-result-name i {
    color: #667eea;
    margin-right: 0.5rem;
    font-size: 1rem;
}

#modalInlineClientResultsList .client-result-name mark {
    background: rgba(102, 126, 234, 0.25);
    padding: 0.1rem 0.25rem;
    border-radius: 4px;
    font-weight: 800;
    color: #667eea;
}

#modalInlineClientResultsList .client-result-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    margin-top: 0.375rem;
}

#modalInlineClientResultsList .client-result-detail {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
    color: #6c757d;
}

#modalInlineClientResultsList .client-result-detail i {
    width: 16px;
    margin-right: 0.5rem;
    font-size: 0.8rem;
}

#modalInlineClientResultsList .client-result-detail.email i {
    color: #667eea;
}

#modalInlineClientResultsList .client-result-detail.phone i {
    color: #1cc88a;
}

#modalInlineClientResultsList .client-result-detail.user i {
    color: #36b9cc;
}

#modalInlineClientResultsList .select-client-inline-btn {
    margin-left: 0.75rem;
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.25s ease;
}

#modalInlineClientResultsList .select-client-inline-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

#modalInlineClientResultsList .client-results-empty {
    text-align: center;
    padding: 2rem 1rem;
    color: #6c757d;
}

#modalInlineClientResultsList .client-results-empty i {
    font-size: 2.5rem;
    color: #dee2e6;
    margin-bottom: 0.75rem;
    display: block;
}

#modalInlineClientResultsList .client-results-loading {
    text-align: center;
    padding: 2rem 1rem;
    color: #667eea;
}

#modalInlineClientResultsList .client-results-loading i {
    font-size: 1.5rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.client-search-result {
    border: 2px solid rgba(102, 126, 234, 0.1);
    border-radius: 10px;
    padding: 0.875rem 1rem;
    margin-bottom: 0.5rem;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    background: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
}

.client-search-result::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    transform: scaleY(0);
    transition: transform 0.25s ease;
}

.client-search-result:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
    transform: translateX(4px);
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.15);
}

.client-search-result:hover::before {
    transform: scaleY(1);
}

.client-search-result.highlighted {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.12) 0%, rgba(118, 75, 162, 0.12) 100%);
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.25);
}

.client-search-result.highlighted::before {
    transform: scaleY(1);
}

.client-result-content {
    flex: 1;
    min-width: 0;
}

.client-result-name {
    color: #1a1a2e;
    font-weight: 700;
    font-size: 0.95rem;
    margin-bottom: 0.375rem;
    display: flex;
    align-items: center;
}

.client-result-name i {
    color: #667eea;
    margin-right: 0.5rem;
    font-size: 1rem;
}

.client-result-name mark {
    background: rgba(102, 126, 234, 0.25);
    padding: 0.1rem 0.25rem;
    border-radius: 4px;
    font-weight: 800;
    color: #667eea;
}

.client-result-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    margin-top: 0.375rem;
}

.client-result-detail {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
    color: #6c757d;
}

.client-result-detail i {
    width: 16px;
    margin-right: 0.5rem;
    font-size: 0.8rem;
}

.client-result-detail.email i {
    color: #667eea;
}

.client-result-detail.phone i {
    color: #1cc88a;
}

.client-result-detail.user i {
    color: #36b9cc;
}

.select-client-inline-btn, .select-client-btn {
    margin-left: 0.75rem;
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.25s ease;
}

.select-client-inline-btn:hover, .select-client-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.client-results-empty {
    text-align: center;
    padding: 2rem 1rem;
    color: #6c757d;
}

.client-results-empty i {
    font-size: 2.5rem;
    color: #dee2e6;
    margin-bottom: 0.75rem;
    display: block;
}

.client-results-loading {
    text-align: center;
    padding: 2rem 1rem;
    color: #667eea;
}

.client-results-loading i {
    font-size: 1.5rem;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Booth Selection Styles */
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

/* Form Section Styles - Modal Specific */
#createBookingModal .form-section {
    transition: all 0.3s;
    position: relative;
    z-index: 1;
    overflow: visible;
    margin-bottom: 2rem;
}

#createBookingModal .form-section:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

/* Ensure modal content doesn't clip */
#createBookingModal .modal-content {
    overflow: visible;
}

#createBookingModal .modal-body {
    position: relative;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Prevent stacking context issues */
#createBookingModal {
    position: fixed;
    z-index: 1050;
}

/* Responsive adjustments for modal */
@media (max-width: 768px) {
    .modal-client-results-dropdown {
        left: -1rem;
        right: -1rem;
        margin-left: 1rem;
        margin-right: 1rem;
    }
    
    .modal-dropdown-card {
        max-height: 300px;
    }
}
</style>

