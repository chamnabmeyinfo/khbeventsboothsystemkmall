@extends('layouts.adminlte')

@section('title', 'Permissions Management')
@section('page-title', 'Permissions Management')
@section('breadcrumb', 'Staff Management / Permissions')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-key mr-2"></i>Permissions</h3>
            <div class="card-tools">
                <a href="{{ route('permissions.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i>Create Permission
                </a>
            </div>
        </div>
        <div class="card-body">
            @foreach($permissions as $module => $modulePermissions)
            <div class="card mb-3">
                <div class="card-header bg-primary">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-folder mr-2"></i>{{ ucfirst($module ?: 'General') }}
                        <span class="badge badge-light ml-2">{{ $modulePermissions->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Description</th>
                                    <th>Roles</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modulePermissions as $permission)
                                <tr>
                                    <td><strong>{{ $permission->name }}</strong></td>
                                    <td><code>{{ $permission->slug }}</code></td>
                                    <td>{{ $permission->description ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $permission->roles->count() }}</span>
                                    </td>
                                    <td>
                                        @if($permission->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('permissions.show', $permission) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
