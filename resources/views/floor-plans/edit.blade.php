@extends('layouts.adminlte')

@section('title', 'Edit Floor Plan')
@section('page-title', 'Edit Floor Plan')
@section('breadcrumb', 'Operations / Floor Plans / Edit')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit mr-1"></i>Edit Floor Plan: {{ $floorPlan->name }}
            </h3>
            <div class="card-tools">
                <a href="{{ route('floor-plans.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('floor-plans.update', $floorPlan) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Floor Plan Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $floorPlan->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="project_name">Project Name</label>
                            <input type="text" class="form-control @error('project_name') is-invalid @enderror" 
                                   id="project_name" name="project_name" value="{{ old('project_name', $floorPlan->project_name) }}">
                            @error('project_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="event_id">Event</label>
                            @if($events && $events->count() > 0)
                            <select class="form-control @error('event_id') is-invalid @enderror" id="event_id" name="event_id">
                                <option value="">No Event (Standalone)</option>
                                @foreach($events as $event)
                                    @php
                                        $eventId = is_object($event) ? ($event->id ?? null) : ($event['id'] ?? null);
                                        $eventTitle = is_object($event) ? ($event->title ?? 'N/A') : ($event['title'] ?? 'N/A');
                                    @endphp
                                    @if($eventId)
                                        <option value="{{ $eventId }}" {{ old('event_id', $floorPlan->event_id) == $eventId ? 'selected' : '' }}>
                                            {{ $eventTitle }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @else
                            <input type="hidden" name="event_id" value="{{ $floorPlan->event_id }}">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Standalone Floor Plan</strong><br>
                                <small>The Events feature is optional. This floor plan can work independently without linking to an event. You can still manage booths and bookings on this floor plan.</small>
                            </div>
                            @endif
                            @error('event_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="is_active">Status</label>
                            <select class="form-control @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', $floorPlan->is_active) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active', $floorPlan->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $floorPlan->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">
                <h5 class="mb-3"><i class="fas fa-image mr-2"></i>Event Information</h5>

                <div class="form-group">
                    <label for="feature_image">Feature Image</label>
                    @php
                        $featureImageExists = false;
                        $featureImagePath = null;
                        if ($floorPlan->feature_image) {
                            $fullPath = public_path($floorPlan->feature_image);
                            if (file_exists($fullPath)) {
                                $featureImageExists = true;
                                $featureImagePath = $floorPlan->feature_image;
                            }
                        }
                    @endphp
                    
                    @if($featureImageExists)
                    <div class="mb-3">
                        <p class="text-muted small mb-2">
                            <i class="fas fa-image mr-1"></i><strong>Current Feature Image:</strong>
                        </p>
                        <img src="{{ asset($featureImagePath) }}" 
                             id="currentFeatureImagePreview"
                             alt="Current feature image" 
                             class="img-thumbnail" 
                             style="max-height: 200px; max-width: 100%; display: block; margin-bottom: 10px;"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'150\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'150\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3EImage not found%3C/text%3E%3C/svg%3E';">
                    </div>
                    @endif
                    
                    <input type="file" 
                           class="form-control-file @error('feature_image') is-invalid @enderror" 
                           id="feature_image" 
                           name="feature_image" 
                           accept="image/jpeg,image/png,image/jpg,image/gif"
                           onchange="previewFeatureImage(this)">
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Upload a feature image for this event/floor plan (JPEG, PNG, GIF). Max size: 5MB. This is different from the floor plan map image.
                        @if($featureImageExists)
                            <br><span class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Uploading a new image will replace the current one.</span>
                        @endif
                    </small>
                    @error('feature_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    
                    <!-- Feature Image Preview (for new upload) -->
                    <div id="featureImagePreview" style="display: none; margin-top: 15px;">
                        <p class="text-muted small mb-2">
                            <i class="fas fa-eye mr-1"></i><strong>Preview (New Image):</strong>
                        </p>
                        <img id="previewFeatureImage" 
                             src="" 
                             alt="Preview" 
                             class="img-thumbnail" 
                             style="max-height: 200px; max-width: 100%;">
                    </div>
                </div>

                <div class="form-group">
                    <label for="google_map_location">Google Map Location</label>
                    <textarea class="form-control @error('google_map_location') is-invalid @enderror" 
                              id="google_map_location" name="google_map_location" rows="4" 
                              placeholder="Paste Google Maps embed code (iframe) or coordinates here...">{{ old('google_map_location', $floorPlan->google_map_location) }}</textarea>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Paste the Google Maps embed iframe code or coordinates. You can get this from Google Maps by clicking "Share" → "Embed a map".
                    </small>
                    @error('google_map_location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="proposal">Proposal</label>
                    <textarea class="form-control @error('proposal') is-invalid @enderror" 
                              id="proposal" name="proposal" rows="5" 
                              placeholder="Enter proposal details, terms, conditions, or other information...">{{ old('proposal', $floorPlan->proposal) }}</textarea>
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Enter proposal content, terms, conditions, or any additional information about this event/floor plan.
                    </small>
                    @error('proposal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="event_start_date">Event Start Date</label>
                            <input type="date" 
                                   class="form-control @error('event_start_date') is-invalid @enderror" 
                                   id="event_start_date" 
                                   name="event_start_date" 
                                   value="{{ old('event_start_date', $floorPlan->event_start_date ? \Carbon\Carbon::parse($floorPlan->event_start_date)->format('Y-m-d') : '') }}">
                            @error('event_start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="event_end_date">Event End Date</label>
                            <input type="date" 
                                   class="form-control @error('event_end_date') is-invalid @enderror" 
                                   id="event_end_date" 
                                   name="event_end_date" 
                                   value="{{ old('event_end_date', $floorPlan->event_end_date ? \Carbon\Carbon::parse($floorPlan->event_end_date)->format('Y-m-d') : '') }}">
                            @error('event_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="event_start_time">Event Start Time</label>
                            <input type="time" 
                                   class="form-control @error('event_start_time') is-invalid @enderror" 
                                   id="event_start_time" 
                                   name="event_start_time" 
                                   value="{{ old('event_start_time', $floorPlan->event_start_time ? \Carbon\Carbon::parse($floorPlan->event_start_time)->format('H:i') : '') }}">
                            @error('event_start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="event_end_time">Event End Time</label>
                            <input type="time" 
                                   class="form-control @error('event_end_time') is-invalid @enderror" 
                                   id="event_end_time" 
                                   name="event_end_time" 
                                   value="{{ old('event_end_time', $floorPlan->event_end_time ? \Carbon\Carbon::parse($floorPlan->event_end_time)->format('H:i') : '') }}">
                            @error('event_end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="event_location">Event Location</label>
                            <input type="text" 
                                   class="form-control @error('event_location') is-invalid @enderror" 
                                   id="event_location" 
                                   name="event_location" 
                                   value="{{ old('event_location', $floorPlan->event_location) }}"
                                   placeholder="e.g., 123 Main Street, City">
                            @error('event_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="event_venue">Event Venue</label>
                            <input type="text" 
                                   class="form-control @error('event_venue') is-invalid @enderror" 
                                   id="event_venue" 
                                   name="event_venue" 
                                   value="{{ old('event_venue', $floorPlan->event_venue) }}"
                                   placeholder="e.g., Convention Center, Mall">
                            @error('event_venue')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="floor_image">Floor Plan Image</label>
                    @php
                        $imageExists = false;
                        $imagePath = null;
                        if ($floorPlan->floor_image) {
                            $fullPath = public_path($floorPlan->floor_image);
                            if (file_exists($fullPath)) {
                                $imageExists = true;
                                $imagePath = $floorPlan->floor_image;
                            }
                        }
                    @endphp
                    
                    @if($imageExists)
                    <div class="mb-3">
                        <p class="text-muted small mb-2">
                            <i class="fas fa-image mr-1"></i><strong>Current Image:</strong>
                        </p>
                        <img src="{{ asset($imagePath) }}" 
                             id="currentImagePreview"
                             alt="Current floor plan" 
                             class="img-thumbnail" 
                             style="max-height: 300px; max-width: 100%; display: block; margin-bottom: 10px;"
                             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'150\'%3E%3Crect fill=\'%23ddd\' width=\'200\' height=\'150\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3EImage not found%3C/text%3E%3C/svg%3E';">
                        <p class="text-muted small">
                            <i class="fas fa-file mr-1"></i>Path: <code>{{ $imagePath }}</code>
                        </p>
                        <p class="text-muted small">
                            <i class="fas fa-ruler-combined mr-1"></i>Canvas size: <strong>{{ $floorPlan->canvas_width ?? 1200 }} x {{ $floorPlan->canvas_height ?? 800 }}</strong> px
                        </p>
                    </div>
                    @else
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>No floor plan image uploaded yet</strong>
                        @if($floorPlan->floor_image)
                            <p class="mb-0 small mt-1">
                                Image path in database: <code>{{ $floorPlan->floor_image }}</code><br>
                                <span class="text-danger">⚠️ File not found at this path.</span>
                            </p>
                        @endif
                    </div>
                    @endif
                    
                    <div class="form-group">
                        <label for="floor_image">
                            @if($imageExists)
                                Replace Floor Plan Image
                            @else
                                Upload Floor Plan Image
                            @endif
                        </label>
                        <input type="file" 
                               class="form-control-file @error('floor_image') is-invalid @enderror" 
                               id="floor_image" 
                               name="floor_image" 
                               accept="image/jpeg,image/png,image/jpg,image/gif"
                               onchange="previewImage(this)">
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Upload a floor plan image (JPEG, PNG, GIF). Max size: 5MB. 
                            The canvas will automatically adjust its size to match the uploaded image.
                            @if($imageExists)
                                <br><span class="text-warning"><i class="fas fa-exclamation-triangle mr-1"></i>Uploading a new image will replace the current one.</span>
                            @endif
                        </small>
                        @error('floor_image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        
                        <!-- Image Preview (for new upload) -->
                        <div id="imagePreview" style="display: none; margin-top: 15px;">
                            <p class="text-muted small mb-2">
                                <i class="fas fa-eye mr-1"></i><strong>Preview (New Image):</strong>
                            </p>
                            <img id="previewImage" 
                                 src="" 
                                 alt="Preview" 
                                 class="img-thumbnail" 
                                 style="max-height: 300px; max-width: 100%;">
                            <p class="text-muted small mt-2">
                                <i class="fas fa-info-circle mr-1"></i>The canvas will automatically resize to match this image's dimensions.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-1"></i>
                    <strong>Automatic Canvas Loading:</strong> After uploading and saving, the canvas will automatically load this image when you click "View Booths" for this floor plan.
                    <br><small>The canvas size will automatically adjust to match the uploaded image dimensions.</small>
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Update Floor Plan
                    </button>
                    <a href="{{ route('floor-plans.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                    @if($floorPlan->floor_image && $imageExists)
                    <a href="{{ route('booths.index', ['floor_plan_id' => $floorPlan->id]) }}" class="btn btn-info" target="_blank">
                        <i class="fas fa-eye mr-1"></i>View Canvas with Image
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewFeatureImage(input) {
    const preview = document.getElementById('featureImagePreview');
    const previewImg = document.getElementById('previewFeatureImage');
    const currentImg = document.getElementById('currentFeatureImagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            
            // Hide current image preview when new image is selected
            if (currentImg) {
                currentImg.style.opacity = '0.5';
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        if (currentImg) {
            currentImg.style.opacity = '1';
        }
    }
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImage');
    const currentImg = document.getElementById('currentImagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            
            // Hide current image preview when new image is selected
            if (currentImg) {
                currentImg.style.opacity = '0.5';
            }
            
            // Show image dimensions in preview
            const img = new Image();
            img.onload = function() {
                const width = img.naturalWidth || img.width;
                const height = img.naturalHeight || img.height;
                console.log('[Floor Plan Edit] New image preview:', {
                    width: width,
                    height: height,
                    size: (input.files[0].size / 1024 / 1024).toFixed(2) + ' MB'
                });
            };
            img.src = e.target.result;
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        if (currentImg) {
            currentImg.style.opacity = '1';
        }
    }
}

// Show success message if image was just uploaded
@if(session('success'))
    console.log('[Floor Plan Edit] Floor plan updated successfully');
    @if($floorPlan->floor_image)
        console.log('[Floor Plan Edit] Image saved:', '{{ $floorPlan->floor_image }}');
        console.log('[Floor Plan Edit] Canvas will automatically load this image when viewing booths');
    @endif
@endif
</script>
@endpush
@endsection

