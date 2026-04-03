@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('breadcrumb', 'Staff Management / Users / Edit')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<link rel="stylesheet" href="{{ asset('css/users-page.css') }}?v=1.3">
@endpush

@push('body-class', 'ios-dashboard-mode users-page users-form-page')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Edit user</h1>
            <p>Update type, role, and status for <strong>{{ $user->username }}</strong>.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('users.show', $user) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-eye text-tertiary" aria-hidden="true"></i> View details
            </a>
            <a href="{{ route('users.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left text-tertiary" aria-hidden="true"></i> Back to users
            </a>
        </div>
    </header>

    <div class="canvas-panel animate-slide-up delay-2">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-user-edit" aria-hidden="true"></i> Account</h2>
        </div>
        <div class="p-3 p-md-4">
            <form action="{{ route('users.update', $user) }}" method="POST" id="userForm">
                @csrf
                @method('PUT')

                <div class="users-form-section">
                    <h6><i class="fas fa-user me-2" aria-hidden="true"></i>Basic information</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-user text-muted" aria-hidden="true"></i></span>
                                <input type="text" class="form-control" id="username" value="{{ $user->username }}" disabled>
                            </div>
                            <small class="text-muted"><i class="fas fa-info-circle me-1" aria-hidden="true"></i>Username cannot be changed after creation</small>
                        </div>
                        <div class="col-md-6">
                            <label for="type" class="form-label">User type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="2" {{ old('type', $user->type) == 2 ? 'selected' : '' }}>Sale staff</option>
                                <option value="1" {{ old('type', $user->type) == 1 ? 'selected' : '' }}>Administrator</option>
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
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 pt-2">
                    <button type="submit" class="action-btn action-btn-primary">
                        <i class="fas fa-save" aria-hidden="true"></i> Update user
                    </button>
                    <a href="{{ route('users.show', $user) }}" class="action-btn action-btn-secondary">
                        <i class="fas fa-eye text-tertiary" aria-hidden="true"></i> View details
                    </a>
                    <a href="{{ route('users.index') }}" class="action-btn action-btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
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
