{{-- Booth Edit Modal: Basic Info, Details, Content, Media. Used on canvas (Update Booth Info). --}}
@php
    $floorPlans = $floorPlans ?? collect();
    $boothTypes = $boothTypes ?? collect();
    $categories = $categories ?? collect();
    $clients = $clients ?? [];
@endphp
<div class="modal fade" id="canvasBoothInfoModal" tabindex="-1" role="dialog" aria-labelledby="canvasBoothInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px 12px 0 0; padding: 20px 30px; border-bottom: none;">
                <h5 class="modal-title text-white" id="canvasBoothInfoModalLabel" style="font-size: 1.5rem; font-weight: 700;">
                    <i class="fas fa-store mr-2"></i><span id="canvasModalTitleText">Update Booth Info</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.9;"></button>
            </div>
            <form id="canvasBoothInfoForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="canvasBoothId" name="id">
                <div class="modal-body" style="padding: 30px;">
                    <ul class="nav nav-pills nav-fill mb-4" id="canvasBoothFormTabs" role="tablist" style="border-bottom: 2px solid #e9ecef; padding-bottom: 15px;">
                        <li class="nav-item">
                            <a class="nav-link active" id="canvas-basic-tab" data-bs-toggle="tab" href="#canvas-basic-info" role="tab" aria-controls="canvas-basic-info" aria-selected="true">
                                <i class="fas fa-info-circle mr-2"></i>Basic Information
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="canvas-details-tab" data-bs-toggle="tab" href="#canvas-details-info" role="tab" aria-controls="canvas-details-info" aria-selected="false">
                                <i class="fas fa-clipboard-list mr-2"></i>Details & Specifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="canvas-content-tab" data-bs-toggle="tab" href="#canvas-content-info" role="tab" aria-controls="canvas-content-info" aria-selected="false">
                                <i class="fas fa-align-left mr-2"></i>Content & Description
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="canvas-media-tab" data-bs-toggle="tab" href="#canvas-media-info" role="tab" aria-controls="canvas-media-info" aria-selected="false">
                                <i class="fas fa-image mr-2"></i>Media
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="canvasBoothFormTabContent">
                        <div class="tab-pane fade show active" id="canvas-basic-info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="canvas_booth_number" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-hashtag text-primary mr-2"></i>Booth Number <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="booth_number" id="canvas_booth_number" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                    </div>
                                    <div class="form-group">
                                        <label for="canvas_floor_plan_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-map text-primary mr-2"></i>Floor Plan <span class="text-danger">*</span>
                                        </label>
                                        <select name="floor_plan_id" id="canvas_floor_plan_id" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="">Select Floor Plan</option>
                                            @foreach($floorPlans as $fp)
                                                <option value="{{ $fp->id }}">{{ $fp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="canvas_booth_type_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-tags text-primary mr-2"></i>Booth Type
                                        </label>
                                        <select name="booth_type_id" id="canvas_booth_type_id" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="">Select Type</option>
                                            @foreach($boothTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="canvas_type" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-cube text-primary mr-2"></i>Type <span class="text-danger">*</span>
                                        </label>
                                        <select name="type" id="canvas_type" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="1">Booth</option>
                                            <option value="2">Space Only</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="canvas_price" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-dollar-sign text-success mr-2"></i>Price <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" style="border-radius: 8px 0 0 8px; background: #f8f9fa; border: 1px solid #dee2e6;">$</span>
                                            </div>
                                            <input type="number" name="price" id="canvas_price" class="form-control" step="0.01" min="0" required style="border-radius: 0 8px 8px 0; border: 1px solid #dee2e6; padding: 10px 15px;">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="canvas_status" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-toggle-on text-primary mr-2"></i>Status <span class="text-danger">*</span>
                                        </label>
                                        <select name="status" id="canvas_status" class="form-control" required style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="1">Available</option>
                                            <option value="2">Confirmed</option>
                                            <option value="3">Reserved</option>
                                            <option value="4">Hidden</option>
                                            <option value="5">Paid</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="canvas_client_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-user-tie text-primary mr-2"></i>Client
                                        </label>
                                        <select name="client_id" id="canvas_client_id" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->company ?? $client->name ?? 'Client' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="canvas_category_id" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-folder text-primary mr-2"></i>Category
                                        </label>
                                        <select name="category_id" id="canvas_category_id" class="form-control" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="canvas-details-info" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="canvas_area_sqm" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-ruler-combined text-primary mr-2"></i>Area (m²)
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="area_sqm" id="canvas_area_sqm" class="form-control" step="0.01" min="0" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="border-radius: 0 8px 8px 0; background: #f8f9fa; border: 1px solid #dee2e6;">m²</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="canvas_capacity" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-users text-primary mr-2"></i>Capacity (people)
                                        </label>
                                        <div class="input-group">
                                            <input type="number" name="capacity" id="canvas_capacity" class="form-control" min="0" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="border-radius: 0 8px 8px 0; background: #f8f9fa; border: 1px solid #dee2e6;"><i class="fas fa-user"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="canvas_electricity_power" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                            <i class="fas fa-bolt text-warning mr-2"></i>Electricity Power
                                        </label>
                                        <input type="text" name="electricity_power" id="canvas_electricity_power" class="form-control" placeholder="e.g., 10A, 20A, 30A" style="border-radius: 8px; border: 1px solid #dee2e6; padding: 10px 15px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="canvas-content-info" role="tabpanel">
                            <div class="form-group">
                                <label for="canvas_description" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                    <i class="fas fa-align-left text-primary mr-2"></i>Description
                                </label>
                                <textarea name="description" id="canvas_description" class="form-control" rows="5" placeholder="Enter a detailed description..." style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px 15px; resize: vertical;"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="canvas_features" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                    <i class="fas fa-list-check text-primary mr-2"></i>Features
                                </label>
                                <textarea name="features" id="canvas_features" class="form-control" rows="5" placeholder="List booth features (one per line)..." style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px 15px; resize: vertical;"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="canvas_notes" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 8px;">
                                    <i class="fas fa-sticky-note text-primary mr-2"></i>Additional Notes
                                </label>
                                <textarea name="notes" id="canvas_notes" class="form-control" rows="4" placeholder="Additional notes..." style="border-radius: 8px; border: 1px solid #dee2e6; padding: 12px 15px; resize: vertical;"></textarea>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="canvas-media-info" role="tabpanel">
                            <div class="form-group">
                                <label class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 15px;">
                                    <i class="fas fa-image text-primary mr-2"></i>Booth Image
                                </label>
                                <div class="image-upload-wrapper" style="position: relative;">
                                    <div class="image-upload-area" id="canvasImageUploadArea" onclick="document.getElementById('canvas_booth_image').click()" style="border: 2px dashed #667eea; border-radius: 12px; padding: 40px; text-align: center; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); cursor: pointer;">
                                        <i class="fas fa-cloud-upload-alt fa-4x mb-3" style="color: #667eea;"></i>
                                        <p class="mb-2" style="font-size: 1.1rem; font-weight: 600; color: #495057;">Click to upload or drag and drop</p>
                                        <small class="text-muted">PNG, JPG, GIF up to 5MB</small>
                                    </div>
                                    <input type="file" name="booth_image" id="canvas_booth_image" class="d-none" accept="image/*">
                                    <div id="canvasImagePreviewContainer" style="display: none; margin-top: 20px; text-align: center;">
                                        <img id="canvasImagePreview" src="" alt="Preview" style="max-width: 100%; max-height: 400px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                        <button type="button" class="btn btn-sm btn-danger mt-2" id="canvasRemoveImageBtn"><i class="fas fa-times"></i> Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 20px 30px; background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 10px 25px; font-weight: 600;">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 8px; padding: 10px 30px; font-weight: 600;">
                        <i class="fas fa-save mr-2"></i>Save Booth
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
