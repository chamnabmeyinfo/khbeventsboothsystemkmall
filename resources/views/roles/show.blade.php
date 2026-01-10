@extends('layouts.adminlte')

@section('title', 'View Role')
@section('page-title', 'View Role')
@section('breadcrumb', 'Staff Management / Roles / View')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
            <li class="breadcrumb-item active">{{ $role->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-shield mr-2"></i>{{ $role->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Slug:</dt>
                        <dd class="col-sm-9"><code>{{ $role->slug }}</code></dd>
                        
                        <dt class="col-sm-3">Description:</dt>
                        <dd class="col-sm-9">{{ $role->description ?? 'N/A' }}</dd>
                        
                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            @if($role->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Users:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-info">{{ $role->users->count() }}</span>
                        </dd>
                        
                        <dt class="col-sm-3">Permissions:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-success">{{ $role->permissions->count() }}</span>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-key mr-2"></i>Permissions</h5>
                </div>
                <div class="card-body">
                    @if($role->permissions->count() > 0)
                        <div class="row">
                            @foreach($role->permissions->groupBy('module') as $module => $modulePermissions)
                            <div class="col-md-6 mb-3">
                                <h6>{{ ucfirst($module ?: 'General') }}</h6>
                                <ul class="list-unstyled">
                                    @foreach($modulePermissions as $permission)
                                    <li>
                                        <i class="fas fa-check text-success mr-1"></i>
                                        {{ $permission->name }}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No permissions assigned</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-users mr-2"></i>Users with this Role</h5>
                </div>
                <div class="card-body">
                    @if($role->users->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($role->users as $user)
                            <li class="mb-2">
                                <i class="fas fa-user mr-2"></i>
                                <a href="{{ route('users.show', $user) }}">{{ $user->username }}</a>
                                @if($user->isAdmin())
                                    <span class="badge badge-danger">Admin</span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No users assigned to this role</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
