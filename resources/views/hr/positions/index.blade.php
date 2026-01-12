@extends('layouts.adminlte')

@section('title', 'Positions')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-briefcase mr-2"></i>Positions Management</h1>
        @if(auth()->user()->hasPermission('hr.positions.create'))
        <a href="{{ route('hr.positions.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus mr-1"></i>Add New Position
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
            <form method="GET" action="{{ route('hr.positions.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label font-weight-bold">Search</label>
                    <input type="text" class="form-control form-control-modern" name="search" value="{{ request('search') }}" placeholder="Search by name or code...">
                </div>
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Department</label>
                    <select class="form-control form-control-modern" name="department_id">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-modern btn-modern-primary w-100"><i class="fas fa-search mr-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Positions Table -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list mr-2"></i>Positions List</h3>
            <span class="badge-modern badge-modern-primary">{{ $positions->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Department</th>
                            <th>Employees</th>
                            <th>Salary Range</th>
                            <th>Status</th>
                            <th style="width: 180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($positions as $pos)
                        <tr>
                            <td class="font-weight-bold text-muted">{{ $pos->id }}</td>
                            <td><strong class="text-primary">{{ $pos->name }}</strong></td>
                            <td><span class="text-muted">{{ $pos->code ?? '-' }}</span></td>
                            <td><span class="font-weight-semibold">{{ $pos->department->name ?? '-' }}</span></td>
                            <td><span class="badge-modern badge-modern-info">{{ $pos->employees->count() }}</span></td>
                            <td>
                                @if($pos->min_salary || $pos->max_salary)
                                    <span class="font-weight-semibold">
                                        {{ $pos->min_salary ? number_format($pos->min_salary, 2) : '-' }} - 
                                        {{ $pos->max_salary ? number_format($pos->max_salary, 2) : '-' }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-modern {{ $pos->is_active ? 'badge-modern-success' : 'badge-modern-info' }}">
                                    {{ $pos->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hr.positions.show', $pos) }}" class="btn-action btn-modern btn-modern-info" title="View"><i class="fas fa-eye"></i></a>
                                    @if(auth()->user()->hasPermission('hr.positions.edit'))
                                    <a href="{{ route('hr.positions.edit', $pos) }}" class="btn-action btn-modern btn-modern-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(auth()->user()->hasPermission('hr.positions.delete'))
                                    <form action="{{ route('hr.positions.destroy', $pos) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this position?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-modern btn-modern-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-briefcase"></i>
                                    <p class="mb-3">No positions found</p>
                                    @if(auth()->user()->hasPermission('hr.positions.create'))
                                    <a href="{{ route('hr.positions.create') }}" class="btn btn-modern btn-modern-primary">
                                        <i class="fas fa-plus mr-1"></i>Create First Position
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
        @if($positions->hasPages())
        <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
            {{ $positions->links() }}
        </div>
        @endif
    </div>
</div>
@stop
