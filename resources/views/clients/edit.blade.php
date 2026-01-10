@extends('layouts.adminlte')

@section('title', 'Edit Client')
@section('page-title', 'Edit Client')
@section('breadcrumb', 'Clients / Edit')

@push('styles')
<style>
    .form-section {
        background: #f8f9fc;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #007bff;
    }
    .form-section h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Edit Client: {{ $client->name }}</h3>
            <div class="card-tools">
                <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye mr-1"></i>View Details
                </a>
                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Clients
                </a>
            </div>
        </div>
        <form action="{{ route('clients.update', $client) }}" method="POST" id="clientForm">
            @csrf
            @method('PUT')
            <div class="card-body">
                <!-- Basic Information -->
                <div class="form-section">
                    <h6><i class="fas fa-user mr-2"></i>Basic Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $client->name) }}" 
                                           placeholder="Enter client full name" required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Client's full name</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sex" class="form-label">Gender</label>
                                <select class="form-control @error('sex') is-invalid @enderror" id="sex" name="sex">
                                    <option value="">Select Gender...</option>
                                    <option value="1" {{ old('sex', $client->sex) == 1 ? 'selected' : '' }}>Male</option>
                                    <option value="2" {{ old('sex', $client->sex) == 2 ? 'selected' : '' }}>Female</option>
                                </select>
                                @error('sex')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Optional: Select gender</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Company Information -->
                <div class="form-section">
                    <h6><i class="fas fa-building mr-2"></i>Company Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company" class="form-label">Company Name</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('company') is-invalid @enderror" 
                                           id="company" name="company" value="{{ old('company', $client->company) }}" 
                                           placeholder="Enter company name">
                                </div>
                                @error('company')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Optional: Company or organization name</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="position" class="form-label">Position/Title</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                           id="position" name="position" value="{{ old('position', $client->position) }}" 
                                           placeholder="Enter position or title">
                                </div>
                                @error('position')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Optional: Job title or position</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-section">
                    <h6><i class="fas fa-phone mr-2"></i>Contact Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                           id="phone_number" name="phone_number" value="{{ old('phone_number', $client->phone_number) }}" 
                                           placeholder="Enter phone number">
                                </div>
                                @error('phone_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Optional: Contact phone number</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Update Client
                </button>
                <a href="{{ route('clients.show', $client) }}" class="btn btn-info">
                    <i class="fas fa-eye mr-1"></i>View Details
                </a>
                <a href="{{ route('clients.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#clientForm').on('submit', function() {
    showLoading();
});
</script>
@endpush
