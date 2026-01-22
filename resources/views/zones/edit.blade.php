@extends('layouts.adminlte')

@section('title', 'Edit Zone')
@section('page-title', 'Edit Zone: ' . $zone->zone_name)
@section('breadcrumb', 'Zones / Edit')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Zone: <strong>{{ $zone->zone_name }}</strong></h3>
                <div class="card-tools">
                    <a href="{{ route('zones.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back to List
                    </a>
                </div>
            </div>
            <form action="{{ route('zones.update', $zone) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Basic Information</h5>
                            
                            <div class="form-group">
                                <label for="floor_plan_id">Floor Plan <span class="text-muted">(Optional)</span></label>
                                <select class="form-control @error('floor_plan_id') is-invalid @enderror" 
                                        id="floor_plan_id" name="floor_plan_id">
                                    <option value="">Global (All Floor Plans)</option>
                                    @foreach($floorPlans as $floorPlan)
                                        <option value="{{ $floorPlan->id }}" {{ old('floor_plan_id', $zone->floor_plan_id) == $floorPlan->id ? 'selected' : '' }}>
                                            {{ $floorPlan->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('floor_plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="zone_name">Zone Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('zone_name') is-invalid @enderror" 
                                       id="zone_name" name="zone_name" value="{{ old('zone_name', $zone->zone_name) }}" 
                                       required>
                                @error('zone_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" step="0.01" min="0" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $zone->price ?? 500) }}">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Size & Position -->
                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="fas fa-ruler-combined mr-2"></i>Size & Position</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="width">Width</label>
                                        <input type="number" min="1" max="1000" 
                                               class="form-control @error('width') is-invalid @enderror" 
                                               id="width" name="width" value="{{ old('width', $zone->width) }}">
                                        @error('width')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="height">Height</label>
                                        <input type="number" min="1" max="1000" 
                                               class="form-control @error('height') is-invalid @enderror" 
                                               id="height" name="height" value="{{ old('height', $zone->height) }}">
                                        @error('height')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="rotation">Rotation (degrees)</label>
                                        <input type="number" min="-360" max="360" 
                                               class="form-control @error('rotation') is-invalid @enderror" 
                                               id="rotation" name="rotation" value="{{ old('rotation', $zone->rotation) }}">
                                        @error('rotation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="z_index">Z-Index</label>
                                        <input type="number" min="0" max="1000" 
                                               class="form-control @error('z_index') is-invalid @enderror" 
                                               id="z_index" name="z_index" value="{{ old('z_index', $zone->z_index) }}">
                                        @error('z_index')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Zone About - Prominent Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="fas fa-file-alt mr-2 text-primary"></i>
                                Zone About / Description 
                                <span class="badge badge-primary">Easy Update Here!</span>
                            </h5>
                            <div class="form-group">
                                <label for="zone_about">
                                    Zone Description 
                                    <span class="text-muted">(Update zone information easily)</span>
                                </label>
                                <textarea class="form-control @error('zone_about') is-invalid @enderror" 
                                          id="zone_about" name="zone_about" rows="6" 
                                          placeholder="Describe this zone, its features, location, benefits, etc. This information will be displayed to help users understand the zone better.">{{ old('zone_about', $zone->zone_about) }}</textarea>
                                @error('zone_about')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Maximum 1000 characters. Update zone information here easily!
                                </small>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <span id="charCount">{{ strlen(old('zone_about', $zone->zone_about ?? '')) }}</span> / 1000 characters
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance (Optional) -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3"><i class="fas fa-palette mr-2"></i>Appearance (Optional)</h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="background_color">Background Color</label>
                                        <input type="color" class="form-control @error('background_color') is-invalid @enderror" 
                                               id="background_color" name="background_color" 
                                               value="{{ old('background_color', $zone->background_color ?? '#ffffff') }}">
                                        @error('background_color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="border_color">Border Color</label>
                                        <input type="color" class="form-control @error('border_color') is-invalid @enderror" 
                                               id="border_color" name="border_color" 
                                               value="{{ old('border_color', $zone->border_color ?? '#000000') }}">
                                        @error('border_color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="text_color">Text Color</label>
                                        <input type="color" class="form-control @error('text_color') is-invalid @enderror" 
                                               id="text_color" name="text_color" 
                                               value="{{ old('text_color', $zone->text_color ?? '#000000') }}">
                                        @error('text_color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="border_radius">Border Radius</label>
                                        <input type="number" step="0.1" min="0" max="100" 
                                               class="form-control @error('border_radius') is-invalid @enderror" 
                                               id="border_radius" name="border_radius" 
                                               value="{{ old('border_radius', $zone->border_radius) }}">
                                        @error('border_radius')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Update Zone
                    </button>
                    <a href="{{ route('zones.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Character counter for zone_about
    document.getElementById('zone_about').addEventListener('input', function() {
        const charCount = this.value.length;
        document.getElementById('charCount').textContent = charCount;
        
        if (charCount > 1000) {
            document.getElementById('charCount').style.color = '#dc3545';
        } else if (charCount > 900) {
            document.getElementById('charCount').style.color = '#f59e0b';
        } else {
            document.getElementById('charCount').style.color = '#6b7280';
        }
    });
</script>
@endpush
@endsection
