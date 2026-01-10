@extends('layouts.adminlte')

@section('title', 'Roles Management')
@section('page-title', 'Roles Management')
@section('breadcrumb', 'Staff Management / Roles')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-shield mr-2"></i>Roles</h3>
            <div class="card-tools">
                <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i>Create Role
                </a>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Users</th>
                        <th>Permissions</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                    <tr>
                        <td>
                            <strong>{{ $role->name }}</strong>
                            @if($role->description)
                            <br><small class="text-muted">{{ $role->description }}</small>
                            @endif
                        </td>
                        <td><code>{{ $role->slug }}</code></td>
                        <td>
                            <span class="badge badge-info">{{ $role->users->count() }}</span>
                        </td>
                        <td>
                            <span class="badge badge-success">{{ $role->permissions->count() }}</span>
                        </td>
                        <td>
                            @if($role->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No roles found. <a href="{{ route('roles.create') }}">Create one</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
