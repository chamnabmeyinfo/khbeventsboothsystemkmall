@extends('layouts.adminlte')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('breadcrumb', 'Staff Management / Users / Edit')

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
            <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i>Edit User: {{ $user->username }}</h3>
            <div class="card-tools">
                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye mr-1"></i>View Details
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Users
                </a>
            </div>
        </div>
        <form action="{{ route('users.update', $user) }}" method="POST" id="userForm">
            @csrf
            @method('PUT')
            <div class="card-body">
                <!-- Basic Information -->
                <div class="form-section">
                    <h6><i class="fas fa-user mr-2"></i>Basic Information</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control" 
                                           id="username" value="{{ $user->username }}" disabled>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>Username cannot be changed after creation
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type" class="form-label">User Type <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="2" {{ old('type', $user->type) == 2 ? 'selected' : '' }}>Sale Staff</option>
                                    <option value="1" {{ old('type', $user->type) == 1 ? 'selected' : '' }}>Administrator</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Change user type</small>
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
                                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
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
                                    <i class="fas fa-info-circle mr-1"></i>Change user role for permission-based access
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Change user account status</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Update User
                </button>
                <a href="{{ route('users.show', $user) }}" class="btn btn-info">
                    <i class="fas fa-eye mr-1"></i>View Details
                </a>
                <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#userForm').on('submit', function() {
    showLoading();
});
</script>
@endpush

