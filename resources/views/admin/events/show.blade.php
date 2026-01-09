@extends('layouts.admin')

@section('title', 'Event Details')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-calendar-alt me-2"></i>Event Details</h2>
    </div>
    <div class="col-auto">
        <div class="btn-group">
            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('admin.events.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">{{ $event->title }}</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Category:</strong> {{ $event->category->name ?? 'N/A' }}
                </div>
                
                <div class="mb-3">
                    <strong>Start Date:</strong> {{ $event->formatted_start_date ?? 'N/A' }}
                </div>
                
                <div class="mb-3">
                    <strong>End Date:</strong> {{ $event->formatted_end_date ?? 'N/A' }}
                </div>
                
                @if($event->location_address)
                <div class="mb-3">
                    <strong>Location:</strong> {{ $event->location_address }}
                </div>
                @endif
                
                @if($event->short_description)
                <div class="mb-3">
                    <strong>Short Description:</strong>
                    <p>{{ $event->short_description }}</p>
                </div>
                @endif
                
                @if($event->description)
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p>{{ $event->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Event Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Status:</strong>
                    @if($event->status == 1)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>
                
                <div class="mb-3">
                    <strong>Total Seats:</strong> {{ $event->seats }}
                </div>
                
                <div class="mb-3">
                    <strong>Booked Seats:</strong> {{ $event->seats_booked }}
                </div>
                
                <div class="mb-3">
                    <strong>Available Seats:</strong> {{ $event->available_seats }}
                </div>
                
                <div class="mb-3">
                    <strong>Price:</strong> ${{ number_format($event->price ?? 0, 2) }}
                </div>
                
                <div class="mb-3">
                    <strong>Created:</strong> {{ $event->created_at->format('M d, Y') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
