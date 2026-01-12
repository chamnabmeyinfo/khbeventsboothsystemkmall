@extends('layouts.adminlte')

@section('title', 'Create User')
@section('page-title', 'Create New User')
@section('breadcrumb', 'Staff Management / Users / Create')

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
    .password-strength {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-plus mr-2"></i>Create New User</h3>
            <div class="card-tools">
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Users
                </a>
            </div>
        </div>
        <form action="{{ route('users.store') }}" method="POST" id="userForm">
            @csrf
            <div class="card-body">
                <!-- Basic Information -->
                <div class="form-section">
                    <h6><i class="fas fa-user mr-2"></i>Basic Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                           id="username" name="username" value="{{ old('username') }}" 
                                           placeholder="Enter username" required>
                                </div>
                                @error('username')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Username must be unique</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type" class="form-label">User Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="2" {{ old('type', 2) == 2 ? 'selected' : '' }}>Sale Staff</option>
                                    <option value="1" {{ old('type') == 1 ? 'selected' : '' }}>Administrator</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Select the user type</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Assignment -->
                <div class="form-section">
                    <h6><i class="fas fa-user-shield mr-2"></i>Role & Permissions</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role_id" class="form-label">Assign Role</label>
                                <select class="form-control @error('role_id') is-invalid @enderror" id="role_id" name="role_id">
                                    <option value="">No Role Assigned</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                        @if($role->permissions->count() > 0)
                                            ({{ $role->permissions->count() }} permissions)
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>Optional: Assign a role for permission-based access control
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">User account status</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="form-section">
                    <h6><i class="fas fa-key mr-2"></i>Password</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Enter password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                            <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Minimum 6 characters</small>
                                <div class="password-strength" id="passwordStrength"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" 
                                           placeholder="Confirm password" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye" id="passwordConfirmationToggleIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Re-enter password for confirmation</small>
                                <div class="password-strength" id="passwordMatch"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save mr-1"></i>Create User
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + 'ToggleIcon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password strength indicator
$('#password').on('input', function() {
    const password = $(this).val();
    const strengthDiv = $('#passwordStrength');
    
    if (password.length === 0) {
        strengthDiv.html('');
        return;
    }
    
    let strength = 0;
    let strengthText = '';
    let strengthClass = '';
    
    if (password.length >= 6) strength++;
    if (password.length >= 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    if (strength <= 2) {
        strengthText = 'Weak';
        strengthClass = 'text-danger';
    } else if (strength <= 3) {
        strengthText = 'Medium';
        strengthClass = 'text-warning';
    } else {
        strengthText = 'Strong';
        strengthClass = 'text-success';
    }
    
    strengthDiv.html(`<small class="${strengthClass}"><i class="fas fa-shield-alt mr-1"></i>Password strength: ${strengthText}</small>`);
});

// Password match indicator
$('#password_confirmation').on('input', function() {
    const password = $('#password').val();
    const confirmation = $(this).val();
    const matchDiv = $('#passwordMatch');
    
    if (confirmation.length === 0) {
        matchDiv.html('');
        return;
    }
    
    if (password === confirmation) {
        matchDiv.html('<small class="text-success"><i class="fas fa-check-circle mr-1"></i>Passwords match</small>');
    } else {
        matchDiv.html('<small class="text-danger"><i class="fas fa-times-circle mr-1"></i>Passwords do not match</small>');
    }
});

// Form validation
$('#userForm').on('submit', function(e) {
    const password = $('#password').val();
    const confirmation = $('#password_confirmation').val();
    
    if (password !== confirmation) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password Mismatch',
            text: 'Password and confirmation password do not match.',
            confirmButtonColor: '#007bff'
        });
        return false;
    }
    
    showLoading();
});
</script>
@endpush

