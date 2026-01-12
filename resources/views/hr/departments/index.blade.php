@extends('layouts.adminlte')

@section('title', 'Departments')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-building mr-2"></i>Departments Management
        </h1>
        @if(auth()->user()->hasPermission('hr.departments.create'))
        <a href="{{ route('hr.departments.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus mr-1"></i>Add New Department
        </a>
        @endif
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Filters Card -->
    <div class="card-modern mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-filter mr-2"></i>Filters</h3>
            <button type="button" class="btn btn-sm btn-modern btn-modern-primary" data-toggle="collapse" data-target="#filtersCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="card-body collapse show" id="filtersCollapse">
            <form method="GET" action="{{ route('hr.departments.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label font-weight-bold">Search</label>
                    <input type="text" class="form-control form-control-modern" name="search" value="{{ request('search') }}" placeholder="Search by name or code...">
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Status</label>
                    <select class="form-control form-control-modern" name="is_active">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-modern btn-modern-primary w-100">
                        <i class="fas fa-search mr-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Departments Table -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list mr-2"></i>Departments List</h3>
            <span class="badge-modern badge-modern-primary">{{ $departments->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Manager</th>
                            <th>Parent</th>
                            <th>Employees</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th style="width: 180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $dept)
                        <tr>
                            <td class="font-weight-bold text-muted">{{ $dept->id }}</td>
                            <td><strong class="text-primary">{{ $dept->name }}</strong></td>
                            <td><span class="text-muted">{{ $dept->code ?? '-' }}</span></td>
                            <td>
                                @if($dept->manager)
                                    <a href="{{ route('hr.employees.show', $dept->manager) }}" class="font-weight-semibold">{{ $dept->manager->full_name }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td><span class="text-muted">{{ $dept->parent->name ?? '-' }}</span></td>
                            <td><span class="badge-modern badge-modern-info">{{ $dept->employees->count() }}</span></td>
                            <td>
                                @if($dept->budget)
                                    <span class="font-weight-semibold">{{ number_format($dept->budget, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-modern {{ $dept->is_active ? 'badge-modern-success' : 'badge-modern-info' }}">
                                    {{ $dept->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hr.departments.show', $dept) }}" class="btn-action btn-modern btn-modern-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->hasPermission('hr.departments.edit'))
                                    <a href="{{ route('hr.departments.edit', $dept) }}" class="btn-action btn-modern btn-modern-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('hr.departments.delete'))
                                    <form action="{{ route('hr.departments.destroy', $dept) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-modern btn-modern-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-building"></i>
                                    <p class="mb-3">No departments found</p>
                                    @if(auth()->user()->hasPermission('hr.departments.create'))
                                    <a href="{{ route('hr.departments.create') }}" class="btn btn-modern btn-modern-primary">
                                        <i class="fas fa-plus mr-1"></i>Create First Department
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($departments->hasPages())
        <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
            {{ $departments->links() }}
        </div>
        @endif
    </div>
</div>
@stop
