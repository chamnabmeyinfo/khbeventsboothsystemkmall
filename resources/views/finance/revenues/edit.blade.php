@extends('layouts.adminlte')

@section('title', 'Edit Revenue')
@section('page-title', 'Edit Revenue #' . $revenue->id)
@section('breadcrumb', 'Finance / Revenues / Edit')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-arrow-up mr-2"></i>Edit Revenue #{{ $revenue->id }}</h3>
        </div>
        <form action="{{ route('finance.revenues.update', $revenue) }}" method="POST">
            @csrf
            @method('PUT')
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
                            <label for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                                   required value="{{ old('title', $revenue->title) }}" placeholder="Enter revenue title">
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount">Amount ($) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                                   step="0.01" min="0" required value="{{ old('amount', $revenue->amount) }}" placeholder="0.00">
                            @error('amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="3" placeholder="Enter revenue description...">{{ old('description', $revenue->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                <option value="">Select Category</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $revenue->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="revenue_date">Revenue Date <span class="text-danger">*</span></label>
                            <input type="date" name="revenue_date" id="revenue_date" class="form-control @error('revenue_date') is-invalid @enderror" 
                                   required value="{{ old('revenue_date', \Carbon\Carbon::parse($revenue->revenue_date)->format('Y-m-d')) }}">
                            @error('revenue_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                                <option value="">Select Payment Method...</option>
                                <option value="cash" {{ old('payment_method', $revenue->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank_transfer" {{ old('payment_method', $revenue->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="check" {{ old('payment_method', $revenue->payment_method) == 'check' ? 'selected' : '' }}>Check</option>
                                <option value="credit_card" {{ old('payment_method', $revenue->payment_method) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="pending" {{ old('status', $revenue->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ old('status', $revenue->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="received" {{ old('status', $revenue->status) == 'received' ? 'selected' : '' }}>Received</option>
                                <option value="cancelled" {{ old('status', $revenue->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="client_id">Client</label>
                            <select name="client_id" id="client_id" class="form-control @error('client_id') is-invalid @enderror">
                                <option value="">Select Client</option>
                                @foreach($clients ?? [] as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $revenue->client_id) == $client->id ? 'selected' : '' }}>
                                        {{ $client->company ?? $client->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="reference_number">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control @error('reference_number') is-invalid @enderror" 
                                   value="{{ old('reference_number', $revenue->reference_number) }}" placeholder="Enter reference number">
                            @error('reference_number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="floor_plan_id">Floor Plan</label>
                            <select name="floor_plan_id" id="floor_plan_id" class="form-control @error('floor_plan_id') is-invalid @enderror">
                                <option value="">Select Floor Plan</option>
                                @foreach($floorPlans ?? [] as $floorPlan)
                                    <option value="{{ $floorPlan->id }}" {{ old('floor_plan_id', $revenue->floor_plan_id) == $floorPlan->id ? 'selected' : '' }}>
                                        {{ $floorPlan->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('floor_plan_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="booking_id">Booking</label>
                            <select name="booking_id" id="booking_id" class="form-control @error('booking_id') is-invalid @enderror">
                                <option value="">Select Booking</option>
                                @foreach($bookings ?? [] as $booking)
                                    <option value="{{ $booking->id }}" {{ old('booking_id', $revenue->booking_id) == $booking->id ? 'selected' : '' }}>
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
                              placeholder="Optional: Add any additional notes...">{{ old('notes', $revenue->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Update Revenue
                </button>
                <a href="{{ route('finance.revenues.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
