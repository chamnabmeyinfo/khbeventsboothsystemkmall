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
@endsection
