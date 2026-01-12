@extends('layouts.adminlte')

@section('title', 'Create Costing')
@section('page-title', 'Create Costing')
@section('breadcrumb', 'Finance / Costings / Create')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calculator mr-2"></i>Create New Costing</h3>
        </div>
        <form action="{{ route('finance.costings.store') }}" method="POST">
            @csrf
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                   required value="{{ old('name') }}" placeholder="Enter costing name">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="costing_date">Costing Date <span class="text-danger">*</span></label>
                            <input type="date" name="costing_date" id="costing_date" class="form-control @error('costing_date') is-invalid @enderror" 
                                   required value="{{ old('costing_date', date('Y-m-d')) }}">
                            @error('costing_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3" placeholder="Enter costing description...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="estimated_cost">Estimated Cost ($)</label>
                            <input type="number" name="estimated_cost" id="estimated_cost" class="form-control @error('estimated_cost') is-invalid @enderror" 
                                   step="0.01" min="0" value="{{ old('estimated_cost') }}" placeholder="0.00">
                            @error('estimated_cost')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="actual_cost">Actual Cost ($)</label>
                            <input type="number" name="actual_cost" id="actual_cost" class="form-control @error('actual_cost') is-invalid @enderror" 
                                   step="0.01" min="0" value="{{ old('actual_cost') }}" placeholder="0.00">
                            @error('actual_cost')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="floor_plan_id">Floor Plan</label>
                            <select name="floor_plan_id" id="floor_plan_id" class="form-control @error('floor_plan_id') is-invalid @enderror">
                                <option value="">Select Floor Plan</option>
                                @foreach($floorPlans ?? [] as $floorPlan)
                                    <option value="{{ $floorPlan->id }}" {{ old('floor_plan_id') == $floorPlan->id ? 'selected' : '' }}>
                                        {{ $floorPlan->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('floor_plan_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="booking_id">Booking</label>
                            <select name="booking_id" id="booking_id" class="form-control @error('booking_id') is-invalid @enderror">
                                <option value="">Select Booking</option>
                                @foreach($bookings ?? [] as $booking)
                                    <option value="{{ $booking->id }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                                        #{{ $booking->id }} - {{ $booking->client->company ?? 'N/A' }} ({{ $booking->date_book->format('Y-m-d') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('booking_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" 
                              placeholder="Optional: Add any additional notes...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Create Costing
                </button>
                <a href="{{ route('finance.costings.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
