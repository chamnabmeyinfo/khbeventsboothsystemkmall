@extends('layouts.app')

@section('title', 'Create Client')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-user-plus me-2"></i>Create New Client</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Clients
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="company" class="form-label">Company</label>
                    <input type="text" class="form-control @error('company') is-invalid @enderror" 
                           id="company" name="company" value="{{ old('company') }}">
                    @error('company')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="position" class="form-label">Position</label>
                    <input type="text" class="form-control @error('position') is-invalid @enderror" 
                           id="position" name="position" value="{{ old('position') }}">
                    @error('position')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                           id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="sex" class="form-label">Gender</label>
                    <select class="form-control @error('sex') is-invalid @enderror" id="sex" name="sex">
                        <option value="">Select...</option>
                        <option value="1" {{ old('sex') == 1 ? 'selected' : '' }}>Male</option>
                        <option value="2" {{ old('sex') == 2 ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('sex')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Create Client
                </button>
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
