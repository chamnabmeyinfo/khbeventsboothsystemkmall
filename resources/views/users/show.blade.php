@extends('layouts.adminlte')

@section('title', 'User Details')
@section('page-title', 'User Details')
@section('breadcrumb', 'Staff Management / Users / View')

@push('styles')
<style>
    .detail-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .detail-card:hover {
        transform: translateX(5px);
    }
    .detail-card.primary { border-left-color: #007bff; }
    .detail-card.success { border-left-color: #28a745; }
    .detail-card.warning { border-left-color: #ffc107; }
    .info-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .stat-card {
        text-align: center;
        padding: 1rem;
        border-radius: 0.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header Actions -->
    <div class="row mb-4">
        <div class="col">
            <div class="btn-group">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Users
                </a>
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                    <i class="fas fa-edit mr-1"></i>Edit User
                </a>
                @if(auth()->user()->isAdmin() && $user->id != auth()->id())
                <button type="button" class="btn btn-danger" onclick="deleteUser({{ $user->id }}, '{{ $user->username }}')">
                    <i class="fas fa-trash mr-1"></i>Delete User
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Information -->
        <div class="col-md-6 mb-4">
            <div class="card detail-card primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user mr-2"></i>User Information</h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-hashtag mr-2"></i>User ID:</span>
                            <strong class="text-primary">#{{ $user->id }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-user mr-2"></i>Username:</span>
                            <strong>{{ $user->username }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-user-tag mr-2"></i>Type:</span>
                            @if($user->isAdmin())
                                <span class="badge badge-danger">
                                    <i class="fas fa-shield-alt mr-1"></i>Administrator
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fas fa-user-tie mr-1"></i>Sale Staff
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-user-shield mr-2"></i>Role:</span>
                            @if($user->role)
                                <span class="badge badge-info">
                                    <i class="fas fa-shield mr-1"></i>{{ $user->role->name }}
                                </span>
                            @else
                                <span class="badge badge-light">No Role Assigned</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-toggle-on mr-2"></i>Status:</span>
                            @if($user->isActive())
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle mr-1"></i>Active
                                </span>
                            @else
                                <span class="badge badge-warning">
                                    <i class="fas fa-times-circle mr-1"></i>Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($user->last_login)
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-clock mr-2"></i>Last Login:</span>
                            <span>{{ $user->last_login }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-md-6 mb-4">
            <div class="card detail-card success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Activity Statistics</h5>
                </div>
                <div class="card-body">
                    @php
                        try {
                            $boothCount = $user->booths()->count();
                        } catch (\Exception $e) {
                            $boothCount = 0;
                        }
                        try {
                            $bookingCount = $user->books()->count();
                        } catch (\Exception $e) {
                            $bookingCount = 0;
                        }
                        try {
                            $permissionsCount = $user->getPermissions()->count();
                        } catch (\Exception $e) {
                            $permissionsCount = 0;
                        }
                    @endphp
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-cube fa-2x mb-2"></i>
                                <h3 class="mb-0">{{ $boothCount }}</h3>
                                <small>Booths</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <i class="fas fa-calendar-check fa-2x mb-2"></i>
                                <h3 class="mb-0">{{ $bookingCount }}</h3>
                                <small>Bookings</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <i class="fas fa-key fa-2x mb-2"></i>
                                <h3 class="mb-0">{{ $permissionsCount }}</h3>
                                <small>Total Permissions</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions & Change Password -->
    <div class="row">
        <!-- Permissions -->
        @if($user->role)
        <div class="col-md-6 mb-4">
            <div class="card detail-card warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-key mr-2"></i>Role Permissions</h5>
                </div>
                <div class="card-body">
                    @php
                        $permissions = $user->getPermissions()->groupBy('module');
                    @endphp
                    @if($permissions->count() > 0)
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="mb-3">
                            <h6 class="text-muted">
                                <i class="fas fa-folder mr-1"></i>{{ ucfirst($module) }}
                            </h6>
                            <div class="d-flex flex-wrap">
                                @foreach($modulePermissions as $permission)
                                <span class="badge badge-primary mr-2 mb-2">
                                    {{ $permission->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">No permissions assigned</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Change Password -->
        <div class="col-md-6 mb-4">
            <div class="card detail-card primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-key mr-2"></i>Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.password.update', $user->id) }}" method="POST" id="passwordForm">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Enter new password" required>
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
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation" 
                                       placeholder="Confirm new password" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="passwordConfirmationToggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="passwordMatch" class="mt-2"></div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-key mr-1"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>
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
$('#passwordForm').on('submit', function(e) {
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

function deleteUser(id, username) {
    Swal.fire({
        title: 'Delete User?',
        text: `Are you sure you want to delete user "${username}"? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch(`/users/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-HTTP-Method-Override': 'DELETE'
                }
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json();
                }
            })
            .then(data => {
                hideLoading();
                if (data && data.success) {
                    Swal.fire('Deleted!', data.message || 'User has been deleted.', 'success')
                        .then(() => {
                            window.location.href = '{{ route("users.index") }}';
                        });
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error!', 'An error occurred while deleting the user.', 'error');
                console.error('Error:', error);
            });
        }
    });
}
</script>
@endpush
