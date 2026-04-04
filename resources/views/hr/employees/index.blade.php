@extends('layouts.adminlte')

@section('title', 'Employees')
@section('page-title', 'Employees')
@section('breadcrumb', 'Human Resources')

@push('body-class', 'ios-dashboard-mode')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/hr-looker.css') }}?v=1">
@include('hr._mobile-styles')
@endpush

@section('content')
<div class="looker-dashboard hr-index-page">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Employees</h1>
            <p>Search, filter, and manage your workforce.</p>
        </div>
        <div class="looker-actions">
            @if(auth()->user()->hasPermission('hr.employees.create'))
            <a href="{{ route('hr.employees.create') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus"></i> Add Employee
            </a>
            @endif
        </div>
    </header>

    <div class="canvas-panel mb-4">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-filter"></i> Filters</h2>
            <button type="button" class="action-btn action-btn-secondary action-btn-icon" data-toggle="collapse" data-target="#filtersCollapse" aria-expanded="true" aria-controls="filtersCollapse" title="Toggle filters">
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="collapse show hr-filters-body" id="filtersCollapse">
            <form method="GET" action="{{ route('hr.employees.index') }}" class="container-fluid px-0">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label font-weight-bold">Search</label>
                        <input type="text" class="form-control" id="search" name="search"
                               value="{{ request('search') }}" placeholder="Name, code, email…" autocomplete="off">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="department_id" class="form-label font-weight-bold">Department</label>
                        <select class="form-control" id="department_id" name="department_id">
                            <option value="">All</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="position_id" class="form-label font-weight-bold">Position</label>
                        <select class="form-control" id="position_id" name="position_id">
                            <option value="">All</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>{{ $pos->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label font-weight-bold">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                            <option value="on-leave" {{ request('status') == 'on-leave' ? 'selected' : '' }}>On leave</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="employment_type" class="form-label font-weight-bold">Employment</label>
                        <select class="form-control" id="employment_type" name="employment_type">
                            <option value="">All</option>
                            <option value="full-time" {{ request('employment_type') == 'full-time' ? 'selected' : '' }}>Full-time</option>
                            <option value="part-time" {{ request('employment_type') == 'part-time' ? 'selected' : '' }}>Part-time</option>
                            <option value="contract" {{ request('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="intern" {{ request('employment_type') == 'intern' ? 'selected' : '' }}>Intern</option>
                            <option value="temporary" {{ request('employment_type') == 'temporary' ? 'selected' : '' }}>Temporary</option>
                        </select>
                    </div>
                    <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="submit" class="action-btn action-btn-primary w-100 justify-content-center"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="canvas-panel hr-panel-flush">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-list"></i> Directory</h2>
            <span class="status-badge status-badge-blue">{{ $employees->total() }} total</span>
        </div>
        <div class="looker-table-wrapper">
            <table class="looker-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Hired</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td class="text-muted small font-weight-bold">{{ $employee->id }}</td>
                        <td><span class="status-badge status-badge-neutral">{{ $employee->employee_code }}</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if(($employeeAvatarUrl = \App\Helpers\AssetHelper::imageUrl($employee->avatar ? 'storage/' . ltrim($employee->avatar, '/') : null)))
                                    <img src="{{ $employeeAvatarUrl }}" alt="" class="hr-emp-avatar mr-2">
                                @else
                                    <span class="hr-emp-avatar-fallback mr-2">{{ strtoupper(substr($employee->first_name, 0, 1)) }}</span>
                                @endif
                                <div>
                                    <a href="{{ route('hr.employees.show', $employee) }}" class="text-link-strong d-block">{{ $employee->full_name }}</a>
                                    @if($employee->email)<small class="text-muted">{{ $employee->email }}</small>@endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($employee->department)
                                <span class="status-badge status-badge-blue">{{ $employee->department->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $employee->position->name ?? '—' }}</td>
                        <td><span class="status-badge status-badge-purple">{{ ucfirst(str_replace('-', ' ', $employee->employment_type)) }}</span></td>
                        <td>
                            @php
                                $st = [
                                    'active' => 'status-badge-green',
                                    'inactive' => 'status-badge-neutral',
                                    'terminated' => 'status-badge-red',
                                    'on-leave' => 'status-badge-orange',
                                    'suspended' => 'status-badge-red',
                                ];
                                $sc = $st[$employee->status] ?? 'status-badge-neutral';
                            @endphp
                            <span class="status-badge {{ $sc }}">{{ ucfirst(str_replace('-', ' ', $employee->status)) }}</span>
                        </td>
                        <td><span class="text-muted small">{{ $employee->hire_date ? $employee->hire_date->format('M d, Y') : '—' }}</span></td>
                        <td>
                            <div class="hr-table-actions">
                                <a href="{{ route('hr.employees.show', $employee) }}" class="action-btn action-btn-secondary action-btn-icon" title="View"><i class="fas fa-eye"></i></a>
                                @if(auth()->user()->hasPermission('hr.employees.edit'))
                                <a href="{{ route('hr.employees.edit', $employee) }}" class="action-btn action-btn-secondary action-btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                @endif
                                @if(auth()->user()->hasPermission('hr.employees.delete'))
                                <form action="{{ route('hr.employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Delete this employee? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-danger-soft action-btn-icon" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="hr-empty-cell">
                            <i class="fas fa-user-slash"></i>
                            <p class="mb-2">No employees found</p>
                            @if(auth()->user()->hasPermission('hr.employees.create'))
                            <a href="{{ route('hr.employees.create') }}" class="action-btn action-btn-primary"><i class="fas fa-plus"></i> Create first employee</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
        <div class="hr-panel-footer">
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</div>
@stop
