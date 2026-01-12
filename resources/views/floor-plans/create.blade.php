@extends('layouts.adminlte')

@section('title', 'Create Floor Plan')
@section('page-title', 'Create Floor Plan')
@section('breadcrumb', 'Operations / Floor Plans / Create')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-plus mr-1"></i>Create New Floor Plan
            </h3>
            <div class="card-tools">
                <a href="{{ route('floor-plans.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('floor-plans.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Floor Plan Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required 
                                   placeholder="e.g., Ground Floor, Level 1">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="project_name">Project Name</label>
                            <input type="text" class="form-control @error('project_name') is-invalid @enderror" 
                                   id="project_name" name="project_name" value="{{ old('project_name') }}"
                                   placeholder="e.g., K Mall Xmas 2026">
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
                                        <option value="{{ $eventId }}" {{ old('event_id') == $eventId ? 'selected' : '' }}>
                                            {{ $eventTitle }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @else
                            <input type="hidden" name="event_id" value="">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>Standalone Floor Plan</strong><br>
                                <small>The Events feature is optional. You can create floor plans that work independently without linking to an event. You can still manage booths and bookings on this floor plan.</small>
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
                                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive</option>
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
                              id="description" name="description" rows="3" 
                              placeholder="Optional description of this floor plan">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4">
                <h5 class="mb-3"><i class="fas fa-image mr-2"></i>Event Information</h5>

                <div class="form-group">
                    <label for="feature_image">Feature Image</label>
                    <input type="file" 
                           class="form-control-file @error('feature_image') is-invalid @enderror" 
                           id="feature_image" 
                           name="feature_image" 
                           accept="image/jpeg,image/png,image/jpg,image/gif"
                           onchange="previewFeatureImage(this)">
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Upload a feature image for this event/floor plan (JPEG, PNG, GIF). Max size: 5MB. This is different from the floor plan map image.
                    </small>
                    @error('feature_image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    
                    <!-- Feature Image Preview -->
                    <div id="featureImagePreview" style="display: none; margin-top: 15px;">
                        <p class="text-muted small mb-2">
                            <i class="fas fa-eye mr-1"></i><strong>Preview:</strong>
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
                              placeholder="Paste Google Maps embed code (iframe) or coordinates here...">{{ old('google_map_location') }}</textarea>
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
                              placeholder="Enter proposal details, terms, conditions, or other information...">{{ old('proposal') }}</textarea>
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
                                   value="{{ old('event_start_date') }}">
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
                                   value="{{ old('event_end_date') }}">
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
                                   value="{{ old('event_start_time') }}">
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
                                   value="{{ old('event_end_time') }}">
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
                                   value="{{ old('event_location') }}"
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
                                   value="{{ old('event_venue') }}"
                                   placeholder="e.g., Convention Center, Mall">
                            @error('event_venue')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Note:</strong> Canvas size and floor plan image can be managed directly in the booth editor after creating the floor plan. The canvas will automatically adjust its size when you upload a floor plan image.
                </div>

                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Create Floor Plan
                    </button>
                    <a href="{{ route('floor-plans.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
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
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush
@endsection

