@extends('layouts.adminlte')

@section('title', 'Edit Booth Pricing')
@section('page-title', 'Edit Booth Pricing')
@section('breadcrumb', 'Finance / Booth Pricing / Edit')

@push('styles')
<style>
    .info-box {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit mr-2"></i>Edit Pricing for Booth {{ $booth->booth_number }}
            </h3>
            <div class="card-tools">
                <a href="{{ route('finance.booth-pricing.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Back to List
                </a>
            </div>
        </div>
        <form action="{{ route('finance.booth-pricing.update', $booth->id) }}" method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="card-body">
                <!-- Booth Information -->
                <div class="info-box">
                    <h6 class="mb-3"><i class="fas fa-info-circle mr-2"></i>Booth Information</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Booth ID:</strong> {{ $booth->id }}
                        </div>
                        <div class="col-md-4">
                            <strong>Booth Number:</strong> {{ $booth->booth_number }}
                        </div>
                        <div class="col-md-4">
                            <strong>Floor Plan:</strong> {{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }}
                        </div>
                        <div class="col-md-4 mt-2">
                            <strong>Status:</strong>
                            @if($booth->status == 1)
                                <span class="badge badge-success">Available</span>
                            @elseif($booth->status == 2)
                                <span class="badge badge-warning">Booked</span>
                            @else
                                <span class="badge badge-secondary">Unavailable</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Pricing Form -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="price">Booth Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                <input type="number" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', $booth->price ?? 0) }}" 
                                       min="0" 
                                       step="0.01" 
                                       required>
                            </div>
                            @error('price')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Enter the pricing for this booth (must be >= 0)</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Current Price</label>
                            <div class="form-control-plaintext">
                                <h4 class="text-primary mb-0">${{ number_format($booth->price ?? 0, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Update Pricing
                </button>
                <a href="{{ route('finance.booth-pricing.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#editForm').on('submit', function() {
        var price = parseFloat($('#price').val());
        if (isNaN(price) || price < 0) {
            alert('Please enter a valid price (must be >= 0)');
            return false;
        }
        return true;
    });
});
</script>
@endpush
