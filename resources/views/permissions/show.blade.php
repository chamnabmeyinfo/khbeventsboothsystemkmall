@extends('layouts.adminlte')

@section('title', 'View Permission')
@section('page-title', 'View Permission')
@section('breadcrumb', 'Staff Management / Permissions / View')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
            <li class="breadcrumb-item active">{{ $permission->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-key mr-2"></i>{{ $permission->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Slug:</dt>
                        <dd class="col-sm-9"><code>{{ $permission->slug }}</code></dd>
                        
                        <dt class="col-sm-3">Module:</dt>
                        <dd class="col-sm-9">{{ $permission->module ?? 'General' }}</dd>
                        
                        <dt class="col-sm-3">Description:</dt>
                        <dd class="col-sm-9">{{ $permission->description ?? 'N/A' }}</dd>
                        
                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            @if($permission->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </dd>
                        
                        <dt class="col-sm-3">Roles:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-info">{{ $permission->roles->count() }}</span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-shield mr-2"></i>Roles with this Permission</h5>
                </div>
                <div class="card-body">
                    @if($permission->roles->count() > 0)
                        <ul class="list-unstyled">
                            @foreach($permission->roles as $role)
                            <li class="mb-2">
                                <i class="fas fa-shield-alt mr-2"></i>
                                <a href="{{ route('roles.show', $role) }}">{{ $role->name }}</a>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No roles have this permission</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

