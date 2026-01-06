@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-user me-2"></i>User Details</h2>
    </div>
    <div class="col-auto">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">User Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">ID:</th>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th>Username:</th>
                        <td>{{ $user->username }}</td>
                    </tr>
                    <tr>
                        <th>Type:</th>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge bg-danger">Admin</span>
                            @else
                                <span class="badge bg-secondary">Sale</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if($user->isActive())
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-warning">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @if($user->last_login)
                    <tr>
                        <th>Last Login:</th>
                        <td>{{ $user->last_login }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Change Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('users.password.update', $user) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" 
                               id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key me-2"></i>Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
