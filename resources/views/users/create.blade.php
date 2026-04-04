@extends('layouts.admin')

@section('title', 'Create User')
@section('page-title', 'Create New User')
@section('breadcrumb', 'Staff Management / Users / Create')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<link rel="stylesheet" href="{{ asset('css/users-page.css') }}?v=1.3">
<style>
    .password-strength { font-size: 0.875rem; margin-top: 0.25rem; }
</style>
@endpush

@push('body-class', 'ios-dashboard-mode users-page users-form-page')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Create user</h1>
            <p>Add a new staff account with username, role, and password.</p>
        </div>
        <div class="looker-actions">
            <a href="{{ route('users.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left text-tertiary" aria-hidden="true"></i> Back to users
            </a>
        </div>
    </header>

    <div class="canvas-panel animate-slide-up delay-2">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-user-plus" aria-hidden="true"></i> New account</h2>
        </div>
        <div class="p-3 p-md-4">
            <form action="{{ route('users.store') }}" method="POST" id="userForm">
                @csrf

                <div class="users-form-section">
                    <h6><i class="fas fa-user me-2" aria-hidden="true"></i>Basic information</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-user text-muted" aria-hidden="true"></i></span>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                       id="username" name="username" value="{{ old('username') }}"
                                       placeholder="Enter username" required autocomplete="username">
                            </div>
                            @error('username')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Username must be unique</small>
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">User type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="2" {{ old('type', 2) == 2 ? 'selected' : '' }}>Sale staff</option>
                                <option value="1" {{ old('type') == 1 ? 'selected' : '' }}>Administrator</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="users-form-section">
                    <h6><i class="fas fa-user-shield me-2" aria-hidden="true"></i>Role &amp; status</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="role_id" class="form-label">Assign role</label>
                            <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id">
                                <option value="">No role assigned</option>
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
                            <small class="text-muted">Optional role for permission-based access</small>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @if(\Illuminate\Support\Facades\Schema::hasColumn('user', 'account_kind'))
                        <div class="col-md-6">
                            <label for="account_kind" class="form-label">Account kind</label>
                            <select class="form-select @error('account_kind') is-invalid @enderror" id="account_kind" name="account_kind">
                                <option value="{{ \App\Models\User::ACCOUNT_KIND_REAL }}" {{ old('account_kind', \App\Models\User::ACCOUNT_KIND_REAL) === \App\Models\User::ACCOUNT_KIND_REAL ? 'selected' : '' }}>Real staff (production)</option>
                                <option value="{{ \App\Models\User::ACCOUNT_KIND_DEMO }}" {{ old('account_kind') === \App\Models\User::ACCOUNT_KIND_DEMO ? 'selected' : '' }}>Demo (testing / training)</option>
                            </select>
                            @error('account_kind')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                    </div>
                </div>

                <div class="users-form-section">
                    <h6><i class="fas fa-key me-2" aria-hidden="true"></i>Password</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-lock text-muted" aria-hidden="true"></i></span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" placeholder="Enter password" required autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')" aria-label="Show password">
                                    <i class="fas fa-eye" id="passwordToggleIcon" aria-hidden="true"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimum 6 characters</small>
                            <div class="password-strength" id="passwordStrength"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Confirm password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-lock text-muted" aria-hidden="true"></i></span>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation"
                                       placeholder="Confirm password" required autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')" aria-label="Show password">
                                    <i class="fas fa-eye" id="password_confirmationToggleIcon" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="password-strength" id="passwordMatch"></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 pt-2">
                    <button type="submit" class="action-btn action-btn-primary" id="submitBtn">
                        <i class="fas fa-save" aria-hidden="true"></i> Create user
                    </button>
                    <a href="{{ route('users.index') }}" class="action-btn action-btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
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

    strengthDiv.html('<small class="' + strengthClass + '"><i class="fas fa-shield-alt me-1" aria-hidden="true"></i>Password strength: ' + strengthText + '</small>');
});

$('#password_confirmation').on('input', function() {
    const password = $('#password').val();
    const confirmation = $(this).val();
    const matchDiv = $('#passwordMatch');

    if (confirmation.length === 0) {
        matchDiv.html('');
        return;
    }

    if (password === confirmation) {
        matchDiv.html('<small class="text-success"><i class="fas fa-check-circle me-1" aria-hidden="true"></i>Passwords match</small>');
    } else {
        matchDiv.html('<small class="text-danger"><i class="fas fa-times-circle me-1" aria-hidden="true"></i>Passwords do not match</small>');
    }
});

$('#userForm').on('submit', function(e) {
    const password = $('#password').val();
    const confirmation = $('#password_confirmation').val();

    if (password !== confirmation) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password mismatch',
            text: 'Password and confirmation password do not match.',
            confirmButtonColor: '#007AFF'
        });
        return false;
    }

    showLoading();
});
</script>
@endpush
