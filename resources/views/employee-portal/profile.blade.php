@extends('layouts.adminlte')

@section('title', 'My Profile')

@section('content_header')
    <h1 class="m-0"><i class="fas fa-user mr-2"></i>My Profile</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card card-primary card-outline">
                <div class="card-body text-center">
                    @if($employee->avatar)
                        <img src="{{ asset('storage/' . $employee->avatar) }}" 
                             alt="{{ $employee->full_name }}" 
                             class="img-circle img-size-128 mb-3">
                    @else
                        <div class="img-circle bg-primary img-size-128 d-flex align-items-center justify-content-center mx-auto mb-3">
                            <span class="text-white font-weight-bold" style="font-size: 48px;">
                                {{ strtoupper(substr($employee->first_name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <h3>{{ $employee->full_name }}</h3>
                    <p class="text-muted">{{ $employee->employee_code }}</p>
                    @if($employee->position)
                        <p><strong>{{ $employee->position->name }}</strong></p>
                    @endif
                    @if($employee->department)
                        <p class="text-muted">{{ $employee->department->name }}</p>
                    @endif
                </div>
            </div>

            <!-- Quick Info -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>Quick Info</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Email:</th>
                            <td>{{ $employee->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $employee->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Mobile:</th>
                            <td>{{ $employee->mobile ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Hire Date:</th>
                            <td>{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge badge-{{ $employee->status == 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst(str_replace('-', ' ', $employee->status)) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Edit Profile Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit Profile</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" 
                                           value="{{ old('phone', $employee->phone) }}">
                                    @error('phone')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" name="mobile" id="mobile" class="form-control" 
                                           value="{{ old('mobile', $employee->mobile) }}">
                                    @error('mobile')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" class="form-control" rows="2">{{ old('address', $employee->address) }}</textarea>
                            @error('address')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" name="city" id="city" class="form-control" 
                                           value="{{ old('city', $employee->city) }}">
                                    @error('city')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="state">State/Province</label>
                                    <input type="text" name="state" id="state" class="form-control" 
                                           value="{{ old('state', $employee->state) }}">
                                    @error('state')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="text" name="postal_code" id="postal_code" class="form-control" 
                                           value="{{ old('postal_code', $employee->postal_code) }}">
                                    @error('postal_code')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" name="country" id="country" class="form-control" 
                                   value="{{ old('country', $employee->country) }}">
                            @error('country')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <hr>

                        <h5>Emergency Contact</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emergency_contact_name">Name</label>
                                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" class="form-control" 
                                           value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}">
                                    @error('emergency_contact_name')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emergency_contact_phone">Phone</label>
                                    <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" class="form-control" 
                                           value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}">
                                    @error('emergency_contact_phone')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="emergency_contact_relationship">Relationship</label>
                                    <input type="text" name="emergency_contact_relationship" id="emergency_contact_relationship" class="form-control" 
                                           value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}">
                                    @error('emergency_contact_relationship')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="avatar">Profile Photo</label>
                            <div class="custom-file">
                                <input type="file" name="avatar" id="avatar" class="custom-file-input" accept="image/*">
                                <label class="custom-file-label" for="avatar">Choose file</label>
                            </div>
                            <small class="form-text text-muted">Max size: 2MB. Formats: JPG, PNG, JPEG</small>
                            @error('avatar')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Update Profile
                            </button>
                            <a href="{{ route('employee.dashboard') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
@stop
