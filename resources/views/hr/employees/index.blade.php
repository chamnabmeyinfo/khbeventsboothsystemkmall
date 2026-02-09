@extends('layouts.adminlte')

@section('title', 'Employees')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">
            <i class="fas fa-users mr-2"></i>Employees Management
        </h1>
        @if(auth()->user()->hasPermission('hr.employees.create'))
        <a href="{{ route('hr.employees.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus mr-1"></i>Add New Employee
        </a>
        @endif
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Filters Card - Modern Design -->
    <div class="card-modern mb-4">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-filter mr-2"></i>Filters</h3>
            <button type="button" class="btn btn-sm btn-modern btn-modern-primary" data-toggle="collapse" data-target="#filtersCollapse">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="card-body collapse show" id="filtersCollapse">
            <form method="GET" action="{{ route('hr.employees.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label font-weight-bold">Search</label>
                    <input type="text" class="form-control form-control-modern" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Name, Code, Email...">
                </div>
                <div class="col-md-2">
                    <label for="department_id" class="form-label font-weight-bold">Department</label>
                    <select class="form-control form-control-modern" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="position_id" class="form-label font-weight-bold">Position</label>
                    <select class="form-control form-control-modern" id="position_id" name="position_id">
                        <option value="">All Positions</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>
                                {{ $pos->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label font-weight-bold">Status</label>
                    <select class="form-control form-control-modern" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        <option value="on-leave" {{ request('status') == 'on-leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="employment_type" class="form-label font-weight-bold">Employment Type</label>
                    <select class="form-control form-control-modern" id="employment_type" name="employment_type">
                        <option value="">All Types</option>
                        <option value="full-time" {{ request('employment_type') == 'full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="part-time" {{ request('employment_type') == 'part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="contract" {{ request('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="intern" {{ request('employment_type') == 'intern' ? 'selected' : '' }}>Intern</option>
                        <option value="temporary" {{ request('employment_type') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-modern btn-modern-primary w-100">
                        <i class="fas fa-search mr-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Employees Table - Modern Design -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list mr-2"></i>Employees List</h3>
            <span class="badge-modern badge-modern-primary">{{ $employees->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Employment Type</th>
                            <th>Status</th>
                            <th>Hire Date</th>
                            <th style="width: 180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td class="font-weight-bold text-muted">{{ $employee->id }}</td>
                            <td>
                                <strong class="text-primary">{{ $employee->employee_code }}</strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if(($employeeAvatarUrl = \App\Helpers\AssetHelper::imageUrl($employee->avatar ? 'storage/' . ltrim($employee->avatar, '/') : null)))
                                        <img src="{{ $employeeAvatarUrl }}" 
                                             alt="{{ $employee->full_name }}" 
                                             class="img-circle img-size-40 mr-2" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid rgba(102, 126, 234, 0.2);">
                                    @else
                                        <div class="img-circle img-size-40 d-flex align-items-center justify-content-center mr-2" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
                                            <span class="text-white font-weight-bold">{{ substr($employee->first_name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <strong class="d-block">{{ $employee->full_name }}</strong>
                                        @if($employee->email)
                                            <small class="text-muted">{{ $employee->email }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($employee->department)
                                    <span class="badge-modern badge-modern-info">{{ $employee->department->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($employee->position)
                                    <span class="font-weight-semibold">{{ $employee->position->name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-modern badge-modern-primary">{{ ucfirst(str_replace('-', ' ', $employee->employment_type)) }}</span>
                            </td>
                            <td>
                                @php
                                    $statusBadges = [
                                        'active' => 'badge-modern-success',
                                        'inactive' => 'badge-modern-info',
                                        'terminated' => 'badge-modern-danger',
                                        'on-leave' => 'badge-modern-warning',
                                        'suspended' => 'badge-modern-danger'
                                    ];
                                    $badgeClass = $statusBadges[$employee->status] ?? 'badge-modern-info';
                                @endphp
                                <span class="badge-modern {{ $badgeClass }}">{{ ucfirst(str_replace('-', ' ', $employee->status)) }}</span>
                            </td>
                            <td><span class="text-muted">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '-' }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hr.employees.show', $employee) }}" 
                                       class="btn-action btn-modern btn-modern-info" 
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->hasPermission('hr.employees.edit'))
                                    <a href="{{ route('hr.employees.edit', $employee) }}" 
                                       class="btn-action btn-modern btn-modern-warning" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->hasPermission('hr.employees.delete'))
                                    <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
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
                                    <i class="fas fa-user-slash"></i>
                                    <p class="mb-3">No employees found</p>
                                    @if(auth()->user()->hasPermission('hr.employees.create'))
                                    <a href="{{ route('hr.employees.create') }}" class="btn btn-modern btn-modern-primary">
                                        <i class="fas fa-plus mr-1"></i>Create First Employee
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
        @if($employees->hasPages())
        <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</div>
@stop

@push('styles')
<style>
    .img-size-40 {
        width: 40px;
        height: 40px;
    }
    .table-modern td {
        vertical-align: middle;
    }
</style>
@endpush
