<!-- Upload Floorplan Modal -->
<div class="modal fade" id="uploadFloorplanModal" tabindex="-1" role="dialog" aria-labelledby="uploadFloorplanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadFloorplanModalLabel">
                    <i class="fas fa-upload"></i> Upload Floorplan Image
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadFloorplanForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="floorplanImage">
                            <i class="fas fa-image"></i> Select Floorplan Image
                        </label>
                        <input type="file" class="form-control-file" id="floorplanImageInput" name="floorplan_image" accept="image/*" required>
                        <small class="form-text text-muted" id="uploadSizeLimitText">Supported formats: JPG, PNG, GIF. Maximum size: 10MB</small>
                    </div>
                    <div class="form-group">
                        <div class="preview-container" id="imagePreview" style="display: none; margin-top: 15px;">
                            <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> Uploading a new floorplan will replace the existing one. The image will be automatically resized to fit the canvas.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnUploadFloorplanSubmit">
                    <i class="fas fa-upload"></i> Upload
                </button>
                    </div>
        </div>
    </div>
</div>

<!-- Canvas Settings Modal -->
<div class="modal fade" id="canvasSettingsModal" tabindex="-1" role="dialog" aria-labelledby="canvasSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="canvasSettingsModalLabel">
                    <i class="fas fa-cog"></i> Canvas Settings
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="canvasSettingsForm">
                    <div class="form-group">
                        <label for="canvasWidth">
                            <i class="fas fa-arrows-alt-h"></i> Canvas Width (px)
                        </label>
                        <input type="number" class="form-control" id="canvasWidth" min="5" step="1" value="1200">
                        <small class="form-text text-muted">Width of the canvas in pixels (minimum: 5px, no maximum limit)</small>
                    </div>
                    <div class="form-group">
                        <label for="canvasHeight">
                            <i class="fas fa-arrows-alt-v"></i> Canvas Height (px)
                        </label>
                        <input type="number" class="form-control" id="canvasHeight" min="5" step="1" value="800">
                        <small class="form-text text-muted">Height of the canvas in pixels (minimum: 5px, no maximum limit)</small>
                    </div>
                    <div class="form-group">
                        <label for="canvasResolution">
                            <i class="fas fa-image"></i> Export Resolution (DPI)
                        </label>
                        <input type="number" class="form-control" id="canvasResolution" min="72" max="600" step="1" value="300">
                        <small class="form-text text-muted">Resolution for PNG export (72-600 DPI). Higher DPI = better quality but larger file size.</small>
                    </div>
                    <div class="form-group">
                        <label for="gridSize">
                            <i class="fas fa-th"></i> Grid Size (px)
                        </label>
                        <input type="number" class="form-control" id="gridSize" min="5" max="100" step="1" value="10">
                        <small class="form-text text-muted">Size of grid cells in pixels. Affects both visual grid and snap-to-grid behavior.</small>
                    </div>
                    <div class="form-group">
                        <label for="uploadSizeLimit">
                            <i class="fas fa-upload"></i> Upload Size Limit (MB)
                        </label>
                        <input type="number" class="form-control" id="uploadSizeLimit" min="1" step="1" value="10">
                        <small class="form-text text-muted">Maximum file size allowed for floorplan image uploads (in megabytes). No maximum limit if left empty or set to 0.</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> Changing canvas size will not affect existing booths. Resolution only affects PNG exports. Grid size affects snap-to-grid alignment.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyCanvasSettings">
                    <i class="fas fa-check"></i> Apply Settings
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add New Booth Modal -->
<div class="modal fade" id="addBoothModal" tabindex="-1" role="dialog" aria-labelledby="addBoothModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBoothModalLabel">
                    <i class="fas fa-plus-circle"></i> Add New Booth to Zone <span id="addBoothZoneName"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addBoothForm">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-hashtag"></i> Create Booths in Range
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="boothFrom" class="small">From Number</label>
                                <input type="number" class="form-control" id="boothFrom" min="1" max="9999" value="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="boothTo" class="small">To Number</label>
                                <input type="number" class="form-control" id="boothTo" min="1" max="9999" value="1" required>
                            </div>
                        </div>
                        <small class="form-text text-muted">Enter the range of booth numbers to create. For example, From: 1, To: 50 will create A01, A02, A03... A50</small>
                    </div>
                    <div class="form-group">
                        <label for="boothNumberFormat">
                            <i class="fas fa-tag"></i> Number Format
                        </label>
                        <select class="form-control" id="boothNumberFormat">
                            <option value="2">2 digits (A01, A02, A03...)</option>
                            <option value="3">3 digits (A001, A002, A003...)</option>
                            <option value="4">4 digits (A0001, A0002, A0003...)</option>
                        </select>
                        <small class="form-text text-muted">Choose how many digits to use for the booth number</small>
                    </div>
                    <div class="form-group" id="previewGroup" style="display: none;">
                        <label>
                            <i class="fas fa-eye"></i> Preview
                        </label>
                        <div class="alert alert-light" id="boothPreview" style="max-height: 150px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                            <!-- Preview will be shown here -->
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Smart Generation:</strong> The system will create booths in the specified range. If any booth numbers already exist, they will be skipped automatically.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnAddBoothSubmit">
                    <i class="fas fa-plus"></i> Add Booth(s)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Zone Modal -->
<div class="modal fade" id="addZoneModal" tabindex="-1" role="dialog" aria-labelledby="addZoneModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addZoneModalLabel">
                    <i class="fas fa-plus-circle"></i> Add New Zone
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addZoneForm">
                    <div class="form-group">
                        <label for="zoneNameInput">
                            <i class="fas fa-vector-square"></i> Zone Name (letters only)
                        </label>
                        <input type="text" class="form-control" id="zoneNameInput" maxlength="3" placeholder="e.g., A or B or ABC" required>
                        <small class="form-text text-muted">Letters only, 1-3 characters. The first booth will be created as ZoneName + 01 (e.g., A01).</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> We will automatically create the first booth for this zone (e.g., A01) so that the zone appears immediately.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnAddZoneSubmit">
                    <i class="fas fa-plus"></i> Create Zone
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Booth Modal -->
<div class="modal fade" id="deleteBoothModal" tabindex="-1" role="dialog" aria-labelledby="deleteBoothModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteBoothModalLabel">
                    <i class="fas fa-trash"></i> Delete Booths from Zone <span id="deleteBoothZoneName"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="deleteBoothForm">
                    <!-- Delete Option Tabs -->
                    <ul class="nav nav-tabs mb-3" id="deleteOptionTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="delete-all-tab" data-toggle="tab" href="#delete-all" role="tab">
                                <i class="fas fa-trash-alt"></i> Delete All
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="delete-specific-tab" data-toggle="tab" href="#delete-specific" role="tab">
                                <i class="fas fa-list"></i> Delete Specific
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="delete-range-tab" data-toggle="tab" href="#delete-range" role="tab">
                                <i class="fas fa-arrows-alt-h"></i> Delete Range
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content" id="deleteOptionTabsContent">
                        <!-- Delete All Tab -->
                        <div class="tab-pane fade show active" id="delete-all" role="tabpanel">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> This will delete ALL booths in Zone <span id="deleteAllZoneName"></span>. This action cannot be undone!
                            </div>
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-hashtag"></i> Total Booths in Zone
                                </label>
                                <input type="text" class="form-control" id="deleteAllCount" readonly style="background-color: #f8f9fa;">
                                <small class="form-text text-muted">All these booths will be permanently deleted.</small>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmDeleteAll" required>
                                    <label class="form-check-label" for="confirmDeleteAll">
                                        I understand this will delete ALL booths in this zone permanently
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Delete Specific Tab -->
                        <div class="tab-pane fade" id="delete-specific" role="tabpanel">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-list"></i> Select Booths to Delete
                                </label>
                                <div id="deleteSpecificBoothsList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                                    <!-- Booth checkboxes will be populated here -->
                                </div>
                                <small class="form-text text-muted">Check the booths you want to delete. You can select multiple booths.</small>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-sm btn-secondary" id="selectAllBooths">
                                    <i class="fas fa-check-square"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" id="deselectAllBooths">
                                    <i class="fas fa-square"></i> Deselect All
                                </button>
                            </div>
                        </div>
                        
                        <!-- Delete Range Tab -->
                        <div class="tab-pane fade" id="delete-range" role="tabpanel">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-arrows-alt-h"></i> Delete Booths in Range
                                </label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="deleteFrom" class="small">From Number</label>
                                        <input type="number" class="form-control" id="deleteFrom" min="1" max="9999" value="1">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="deleteTo" class="small">To Number</label>
                                        <input type="number" class="form-control" id="deleteTo" min="1" max="9999" value="1">
                                    </div>
                                </div>
                                <small class="form-text text-muted">Enter the range of booth numbers to delete. For example, From: 1, To: 50 will delete A01, A02, A03... A50</small>
                            </div>
                            <div class="form-group" id="deleteRangePreviewGroup" style="display: none;">
                                <label>
                                    <i class="fas fa-eye"></i> Preview
                                </label>
                                <div class="alert alert-light" id="deleteRangePreview" style="max-height: 150px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                                    <!-- Preview will be shown here -->
                                </div>
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> This will permanently delete all booths in the specified range. This action cannot be undone!
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnDeleteBoothSubmit">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Book Booth Modal -->
<div class="modal fade" id="bookBoothModal" tabindex="-1" role="dialog" aria-labelledby="bookBoothModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookBoothModalLabel">
                    <i class="fas fa-calendar-check"></i> Book Booth: <span id="bookBoothNumber"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bookBoothForm">
                    <input type="hidden" id="bookBoothId" name="booth_id">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Fill in the client information below to secure this booth location.</strong>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientName">
                                    <i class="fas fa-user"></i> Client Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="clientName" name="name" required placeholder="Enter client name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientSex">
                                    <i class="fas fa-venus-mars"></i> Gender
                                </label>
                                <select class="form-control" id="clientSex" name="sex">
                            <option value="">Select Gender</option>
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                            <option value="3">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientCompany">
                                    <i class="fas fa-building"></i> Company <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="clientCompany" name="company" required placeholder="Enter company name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientPosition">
                                    <i class="fas fa-briefcase"></i> Position
                                </label>
                                <input type="text" class="form-control" id="clientPosition" name="position" placeholder="Enter position/title">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="clientPhone">
                            <i class="fas fa-phone"></i> Phone Number <span class="text-danger">*</span>
                        </label>
                        <input type="tel" class="form-control" id="clientPhone" name="phone_number" required placeholder="Enter phone number">
                    </div>
                    
                    <div class="form-group">
                        <label for="bookingStatus">
                            <i class="fas fa-tag"></i> Booking Status
                        </label>
                        <select class="form-control" id="bookingStatus" name="status">
                            <option value="2">Confirmed</option>
                            <option value="3">Reserved</option>
                            <option value="5">Paid</option>
                        </select>
                        <small class="form-text text-muted">
                            <strong>Confirmed:</strong> Client has confirmed the booking<br>
                            <strong>Reserved:</strong> Booth is reserved but not yet confirmed<br>
                            <strong>Paid:</strong> Payment has been received
                        </small>
                    </div>
                    
                    <div id="bookBoothError" class="alert alert-danger" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="btnBookBoothSubmit">
                    <i class="fas fa-save"></i> Save Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Booth Settings Modal -->
<div class="modal fade" id="boothSettingsModal" tabindex="-1" role="dialog" aria-labelledby="boothSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="boothSettingsModalLabel">
                    <i class="fas fa-cube"></i> Booth Default Settings
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="boothSettingsForm">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> These settings will be applied to all new booths when you add them to the canvas. Existing booths are not affected.
                    </div>
                    <div class="form-group">
                        <label for="defaultWidth">
                            <i class="fas fa-arrows-alt-h"></i> Default Width (px)
                        </label>
                        <input type="number" class="form-control" id="defaultWidth" min="5" step="1" value="80">
                        <small class="form-text text-muted">Default width for new booths (minimum: 5px)</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultHeight">
                            <i class="fas fa-arrows-alt-v"></i> Default Height (px)
                        </label>
                        <input type="number" class="form-control" id="defaultHeight" min="5" step="1" value="50">
                        <small class="form-text text-muted">Default height for new booths (minimum: 5px)</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultRotation">
                            <i class="fas fa-redo"></i> Default Rotation (degrees)
                        </label>
                        <input type="number" class="form-control" id="defaultRotation" min="-360" max="360" step="1" value="0">
                        <small class="form-text text-muted">Default rotation angle for new booths</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultZIndex">
                            <i class="fas fa-layer-group"></i> Default Z-Index
                        </label>
                        <input type="number" class="form-control" id="defaultZIndex" min="1" max="1000" step="1" value="10">
                        <small class="form-text text-muted">Default stacking order (higher = on top)</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultFontSize">
                            <i class="fas fa-font"></i> Default Font Size (px)
                        </label>
                        <input type="number" class="form-control" id="defaultFontSize" min="8" max="48" step="1" value="14">
                        <small class="form-text text-muted">Default font size for booth number text</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultBorderWidth">
                            <i class="fas fa-border-style"></i> Default Border Width (px)
                        </label>
                        <input type="number" class="form-control" id="defaultBorderWidth" min="0" max="10" step="1" value="2">
                        <small class="form-text text-muted">Default border width for booths</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultBorderRadius">
                            <i class="fas fa-circle"></i> Default Border Radius (px)
                        </label>
                        <input type="number" class="form-control" id="defaultBorderRadius" min="0" max="50" step="1" value="6">
                        <small class="form-text text-muted">Default corner rounding for booths</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultOpacity">
                            <i class="fas fa-adjust"></i> Default Opacity
                        </label>
                        <input type="number" class="form-control" id="defaultOpacity" min="0" max="1" step="0.1" value="1.00">
                        <small class="form-text text-muted">Default transparency (0.0 = transparent, 1.0 = opaque)</small>
                    </div>
                    
                    <hr class="my-4" style="border-color: #dee2e6;">
                    <h6 class="mb-3" style="color: #495057; font-weight: 600;">
                        <i class="fas fa-palette"></i> Color Settings
                    </h6>
                    
                    <div class="form-group">
                        <label for="defaultBackgroundColor">
                            <i class="fas fa-fill"></i> Default Background Color
                        </label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="defaultBackgroundColor" value="#ffffff" title="Choose background color">
                            <input type="text" class="form-control" id="defaultBackgroundColorText" value="#ffffff" placeholder="#ffffff">
                        </div>
                        <small class="form-text text-muted">Default background color for new booths</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="defaultBorderColor">
                            <i class="fas fa-border-all"></i> Default Border Color
                        </label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="defaultBorderColor" value="#007bff" title="Choose border color">
                            <input type="text" class="form-control" id="defaultBorderColorText" value="#007bff" placeholder="#007bff">
                        </div>
                        <small class="form-text text-muted">Default border color for new booths</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="defaultTextColor">
                            <i class="fas fa-font"></i> Default Text Color
                        </label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="defaultTextColor" value="#000000" title="Choose text color">
                            <input type="text" class="form-control" id="defaultTextColorText" value="#000000" placeholder="#000000">
                        </div>
                        <small class="form-text text-muted">Default text color for booth numbers</small>
                    </div>
                    
                    <hr class="my-4" style="border-color: #dee2e6;">
                    <h6 class="mb-3" style="color: #495057; font-weight: 600;">
                        <i class="fas fa-typography"></i> Typography Settings
                    </h6>
                    
                    <div class="form-group">
                        <label for="defaultFontWeight">
                            <i class="fas fa-bold"></i> Default Font Weight
                        </label>
                        <select class="form-control" id="defaultFontWeight">
                            <option value="300">Light (300)</option>
                            <option value="400">Normal (400)</option>
                            <option value="500">Medium (500)</option>
                            <option value="600">Semi Bold (600)</option>
                            <option value="700" selected>Bold (700)</option>
                            <option value="800">Extra Bold (800)</option>
                            <option value="900">Black (900)</option>
                        </select>
                        <small class="form-text text-muted">Default font weight for booth number text</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="defaultFontFamily">
                            <i class="fas fa-font"></i> Default Font Family
                        </label>
                        <select class="form-control" id="defaultFontFamily">
                            <option value="Arial, sans-serif">Arial</option>
                            <option value="'Helvetica Neue', Helvetica, sans-serif">Helvetica</option>
                            <option value="'Times New Roman', serif">Times New Roman</option>
                            <option value="'Courier New', monospace">Courier New</option>
                            <option value="Georgia, serif">Georgia</option>
                            <option value="Verdana, sans-serif">Verdana</option>
                            <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
                            <option value="Impact, sans-serif">Impact</option>
                            <option value="'Comic Sans MS', cursive">Comic Sans MS</option>
                            <option value="'Roboto', sans-serif">Roboto</option>
                            <option value="'Open Sans', sans-serif">Open Sans</option>
                            <option value="'Montserrat', sans-serif">Montserrat</option>
                        </select>
                        <small class="form-text text-muted">Default font family for booth number text</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="defaultTextAlign">
                            <i class="fas fa-align-center"></i> Default Text Alignment
                        </label>
                        <select class="form-control" id="defaultTextAlign">
                            <option value="center" selected>Center</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="justify">Justify</option>
                        </select>
                        <small class="form-text text-muted">Default text alignment for booth numbers</small>
                    </div>
                    
                    <hr class="my-4" style="border-color: #dee2e6;">
                    <h6 class="mb-3" style="color: #495057; font-weight: 600;">
                        <i class="fas fa-shadow"></i> Shadow & Effects
                    </h6>
                    
                    <div class="form-group">
                        <label for="defaultBoxShadow">
                            <i class="fas fa-box"></i> Default Box Shadow
                        </label>
                        <select class="form-control" id="defaultBoxShadow">
                            <option value="none">None</option>
                            <option value="0 2px 4px rgba(0,0,0,0.1)">Small</option>
                            <option value="0 2px 8px rgba(0,0,0,0.2)" selected>Medium (Default)</option>
                            <option value="0 4px 12px rgba(0,0,0,0.3)">Large</option>
                            <option value="0 6px 16px rgba(0,0,0,0.4)">Extra Large</option>
                            <option value="0 0 10px rgba(0,123,255,0.5)">Glow Blue</option>
                            <option value="0 0 10px rgba(40,167,69,0.5)">Glow Green</option>
                            <option value="0 0 10px rgba(255,193,7,0.5)">Glow Yellow</option>
                        </select>
                        <small class="form-text text-muted">Default shadow effect for new booths</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyBoothSettings">
                    <i class="fas fa-check"></i> Apply Settings
                </button>
            </div>
        </div>
    </div>
</div>
