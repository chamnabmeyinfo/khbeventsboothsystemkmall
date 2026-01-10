@extends('layouts.adminlte')

@section('title', 'Create Role')
@section('page-title', 'Create Role')
@section('breadcrumb', 'Staff Management / Roles / Create')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-shield mr-2"></i>Create New Role</h3>
        </div>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <!-- General Error Display -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="fas fa-exclamation-triangle mr-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                   value="{{ old('slug') }}" required placeholder="e.g., sales-manager">
                            <small class="form-text text-muted">Unique identifier (lowercase, hyphens)</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <h5><i class="fas fa-key mr-2"></i>Permissions</h5>
                <p class="text-muted">Select permissions for this role</p>
                
                @foreach($permissions as $module => $modulePermissions)
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-folder mr-2"></i>{{ ucfirst($module ?: 'General') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($modulePermissions as $permission)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" name="permissions[]" 
                                           class="form-check-input" 
                                           id="permission_{{ $permission->id }}" 
                                           value="{{ $permission->id }}">
                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                        {{ $permission->name }}
                                        @if($permission->description)
                                        <br><small class="text-muted">{{ $permission->description }}</small>
                                        @endif
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i>Create Role
                </button>
                <a href="{{ route('roles.index') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
