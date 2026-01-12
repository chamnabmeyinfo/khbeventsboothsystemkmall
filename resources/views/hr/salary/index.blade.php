@extends('layouts.adminlte')

@section('title', 'Salary History')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-money-bill-wave mr-2"></i>Salary History</h1>
        @if(auth()->user()->hasPermission('hr.salary.create'))
        <a href="{{ route('hr.salary.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus mr-1"></i>Add Salary Entry
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
            <form method="GET" action="{{ route('hr.salary.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label font-weight-bold">Employee</label>
                    <select class="form-control form-control-modern" name="employee_id">
                        <option value="">All Employees</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                                {{ $emp->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">From Date</label>
                    <input type="date" class="form-control form-control-modern" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">To Date</label>
                    <input type="date" class="form-control form-control-modern" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-5 d-flex align-items-end">
                    <button type="submit" class="btn btn-modern btn-modern-primary mr-2"><i class="fas fa-search mr-1"></i>Filter</button>
                    <a href="{{ route('hr.salary.index') }}" class="btn btn-modern btn-modern-info">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Salary History Table -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list mr-2"></i>Salary History List</h3>
            <span class="badge-modern badge-modern-primary">{{ $salaries->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Effective Date</th>
                            <th>Salary</th>
                            <th>Currency</th>
                            <th>Reason</th>
                            <th>Approved By</th>
                            <th style="width: 180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $salary)
                        <tr>
                            <td class="font-weight-bold text-muted">{{ $salary->id }}</td>
                            <td>
                                <a href="{{ route('hr.employees.show', $salary->employee) }}" class="font-weight-bold text-primary">
                                    {{ $salary->employee->full_name }}
                                </a>
                            </td>
                            <td><span class="font-weight-semibold">{{ $salary->effective_date->format('M d, Y') }}</span></td>
                            <td><strong class="text-success">{{ number_format($salary->salary, 2) }}</strong></td>
                            <td><span class="badge-modern badge-modern-primary">{{ $salary->currency ?? 'USD' }}</span></td>
                            <td><span class="text-muted">{{ $salary->reason ?? '-' }}</span></td>
                            <td><span class="text-muted">{{ $salary->approver->name ?? '-' }}</span></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hr.salary.show', $salary) }}" class="btn-action btn-modern btn-modern-info" title="View"><i class="fas fa-eye"></i></a>
                                    @if(auth()->user()->hasPermission('hr.salary.edit'))
                                    <a href="{{ route('hr.salary.edit', $salary) }}" class="btn-action btn-modern btn-modern-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(auth()->user()->hasPermission('hr.salary.delete'))
                                    <form action="{{ route('hr.salary.destroy', $salary) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this salary history entry?');">
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
                                    <i class="fas fa-money-bill-wave"></i>
                                    <p class="mb-3">No salary history found</p>
                                    @if(auth()->user()->hasPermission('hr.salary.create'))
                                    <a href="{{ route('hr.salary.create') }}" class="btn btn-modern btn-modern-primary">
                                        <i class="fas fa-plus mr-1"></i>Create First Entry
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
        @if($salaries->hasPages())
        <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
            {{ $salaries->links() }}
        </div>
        @endif
    </div>
</div>
@stop
