@extends('layouts.adminlte')

@section('title', 'Leave Requests')
@section('page-title', 'Leave Requests')
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
            <h1>Leave requests</h1>
            <p>Review, approve, and track time off.</p>
        </div>
        <div class="looker-actions">
            @if(auth()->user()->hasPermission('hr.leaves.create'))
            <a href="{{ route('hr.leaves.create') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus"></i> New request
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
            <form method="GET" action="{{ route('hr.leaves.index') }}" class="container-fluid px-0">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label font-weight-bold">Employee</label>
                        <select class="form-control" name="employee_id">
                            <option value="">All employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold">Status</label>
                        <select class="form-control" name="status">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold">Leave type</label>
                        <select class="form-control" name="leave_type_id">
                            <option value="">All types</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold">From</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label font-weight-bold">To</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="submit" class="action-btn action-btn-primary w-100 justify-content-center" title="Apply filters"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="canvas-panel hr-panel-flush">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-list"></i> Requests</h2>
            <span class="status-badge status-badge-blue">{{ $leaveRequests->total() }} total</span>
        </div>
        <div class="looker-table-wrapper">
            <table class="looker-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Type</th>
                        <th>Period</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $leave)
                    <tr>
                        <td class="text-muted small font-weight-bold">{{ $leave->id }}</td>
                        <td><a href="{{ route('hr.employees.show', $leave->employee) }}" class="text-link-strong">{{ $leave->employee->full_name }}</a></td>
                        <td>{{ $leave->leaveType->name }}</td>
                        <td><span class="text-muted small">{{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}</span></td>
                        <td><span class="status-badge status-badge-blue">{{ $leave->total_days }}</span></td>
                        <td>
                            @php
                                $lb = [
                                    'pending' => 'status-badge-orange',
                                    'approved' => 'status-badge-green',
                                    'rejected' => 'status-badge-red',
                                    'cancelled' => 'status-badge-neutral',
                                ];
                                $lc = $lb[$leave->status] ?? 'status-badge-neutral';
                            @endphp
                            <span class="status-badge {{ $lc }}">{{ ucfirst($leave->status) }}</span>
                        </td>
                        <td>
                            <div class="hr-table-actions">
                                <a href="{{ route('hr.leaves.show', $leave) }}" class="action-btn action-btn-secondary action-btn-icon" title="View"><i class="fas fa-eye"></i></a>
                                @if($leave->status == 'pending' && auth()->user()->hasPermission('hr.leaves.edit'))
                                <a href="{{ route('hr.leaves.edit', $leave) }}" class="action-btn action-btn-secondary action-btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                @endif
                                @if(in_array($leave->status, ['pending', 'cancelled']) && auth()->user()->hasPermission('hr.leaves.delete'))
                                <form action="{{ route('hr.leaves.destroy', $leave) }}" method="POST" onsubmit="return confirm('Delete this leave request?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-danger-soft action-btn-icon" title="Delete"><i class="fas fa-trash"></i></button>
                                </form>
                                @endif
                                @if($leave->status == 'pending' && auth()->user()->hasPermission('hr.leaves.approve'))
                                <form action="{{ route('hr.leaves.approve', $leave) }}" method="POST" onsubmit="return confirm('Approve this request?');">
                                    @csrf
                                    <button type="submit" class="action-btn action-btn-success-soft action-btn-icon" title="Approve"><i class="fas fa-check"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="hr-empty-cell">
                            <i class="fas fa-calendar-alt"></i>
                            <p class="mb-2">No leave requests</p>
                            @if(auth()->user()->hasPermission('hr.leaves.create'))
                            <a href="{{ route('hr.leaves.create') }}" class="action-btn action-btn-primary"><i class="fas fa-plus"></i> Create first request</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaveRequests->hasPages())
        <div class="hr-panel-footer">{{ $leaveRequests->links() }}</div>
        @endif
    </div>
</div>
@stop
