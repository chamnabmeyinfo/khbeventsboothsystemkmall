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
                                <label for="name" class="form-label">Full Name</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $client->name) }}" 
                                           placeholder="Enter client full name">
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
                                    <option value="3" {{ old('sex', $client->sex) == 3 ? 'selected' : '' }}>Other</option>
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
                                <small class="form-text text-muted">Company or organization name</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_name_khmer" class="form-label">Company Name (Khmer)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-building"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('company_name_khmer') is-invalid @enderror" 
                                           id="company_name_khmer" name="company_name_khmer" value="{{ old('company_name_khmer', $client->company_name_khmer ?? '') }}" 
                                           placeholder="Enter company name in Khmer">
                                </div>
                                @error('company_name_khmer')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Company name in Khmer language</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
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
                                <small class="form-text text-muted">Contact phone number</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone_1" class="form-label">Phone 1</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('phone_1') is-invalid @enderror" 
                                           id="phone_1" name="phone_1" value="{{ old('phone_1', $client->phone_1 ?? '') }}" 
                                           placeholder="Enter primary phone number">
                                </div>
                                @error('phone_1')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Primary phone number</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone_2" class="form-label">Phone 2</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('phone_2') is-invalid @enderror" 
                                           id="phone_2" name="phone_2" value="{{ old('phone_2', $client->phone_2 ?? '') }}" 
                                           placeholder="Enter secondary phone number">
                                </div>
                                @error('phone_2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Secondary phone number</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $client->email) }}" 
                                           placeholder="Enter email address">
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Email address (must be unique if provided)</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_1" class="form-label">Email 1</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control @error('email_1') is-invalid @enderror" 
                                           id="email_1" name="email_1" value="{{ old('email_1', $client->email_1 ?? '') }}" 
                                           placeholder="Enter primary email address">
                                </div>
                                @error('email_1')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Primary email address</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email_2" class="form-label">Email 2</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control @error('email_2') is-invalid @enderror" 
                                           id="email_2" name="email_2" value="{{ old('email_2', $client->email_2 ?? '') }}" 
                                           placeholder="Enter secondary email address">
                                </div>
                                @error('email_2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Secondary email address</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address" class="form-label">Address</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    </div>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="2" 
                                              placeholder="Enter complete address (street, city, country)">{{ old('address', $client->address) }}</textarea>
                                </div>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Complete physical address</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="form-section">
                    <h6><i class="fas fa-info-circle mr-2"></i>Additional Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tax_id" class="form-label">Tax ID / Business Registration Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('tax_id') is-invalid @enderror" 
                                           id="tax_id" name="tax_id" value="{{ old('tax_id', $client->tax_id) }}" 
                                           placeholder="Enter tax ID or business registration number">
                                </div>
                                @error('tax_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Optional: Tax ID or business registration number</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="website" class="form-label">Website</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                    </div>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                           id="website" name="website" value="{{ old('website', $client->website) }}" 
                                           placeholder="https://example.com">
                                </div>
                                @error('website')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Optional: Company website URL</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes" class="form-label">Additional Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Enter any additional information or notes">{{ old('notes', $client->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Optional: Additional information or notes about the client</small>
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

