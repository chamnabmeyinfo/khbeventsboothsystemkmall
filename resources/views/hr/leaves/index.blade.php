@extends('layouts.adminlte')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('title', 'Leave Requests')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-calendar-alt mr-2"></i>Leave Requests</h1>
        @if(auth()->user()->hasPermission('hr.leaves.create'))
        <a href="{{ route('hr.leaves.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus mr-1"></i>New Leave Request
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
            <form method="GET" action="{{ route('hr.leaves.index') }}" class="row g-3">
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
                    <label class="form-label font-weight-bold">Status</label>
                    <select class="form-control form-control-modern" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">Leave Type</label>
                    <select class="form-control form-control-modern" name="leave_type_id">
                        <option value="">All Types</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}" {{ request('leave_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
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
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-modern btn-modern-primary w-100"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Leave Requests Table -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list mr-2"></i>Leave Requests List</h3>
            <span class="badge-modern badge-modern-primary">{{ $leaveRequests->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Leave Type</th>
                            <th>Period</th>
                            <th>Days</th>
                            <th>Status</th>
                            <th style="width: 220px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveRequests as $leave)
                        <tr>
                            <td class="font-weight-bold text-muted">{{ $leave->id }}</td>
                            <td>
                                <a href="{{ route('hr.employees.show', $leave->employee) }}" class="font-weight-bold text-primary">
                                    {{ $leave->employee->full_name }}
                                </a>
                            </td>
                            <td><span class="font-weight-semibold">{{ $leave->leaveType->name }}</span></td>
                            <td><small>{{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}</small></td>
                            <td><strong class="text-primary">{{ $leave->total_days }}</strong></td>
                            <td>
                                @php
                                    $statusBadges = [
                                        'pending' => 'badge-modern-warning',
                                        'approved' => 'badge-modern-success',
                                        'rejected' => 'badge-modern-danger',
                                        'cancelled' => 'badge-modern-info'
                                    ];
                                    $badgeClass = $statusBadges[$leave->status] ?? 'badge-modern-info';
                                @endphp
                                <span class="badge-modern {{ $badgeClass }}">{{ ucfirst($leave->status) }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hr.leaves.show', $leave) }}" class="btn-action btn-modern btn-modern-info" title="View"><i class="fas fa-eye"></i></a>
                                    @if($leave->status == 'pending' && auth()->user()->hasPermission('hr.leaves.edit'))
                                    <a href="{{ route('hr.leaves.edit', $leave) }}" class="btn-action btn-modern btn-modern-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(in_array($leave->status, ['pending', 'cancelled']) && auth()->user()->hasPermission('hr.leaves.delete'))
                                    <form action="{{ route('hr.leaves.destroy', $leave) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this leave request?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-modern btn-modern-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endif
                                    @if($leave->status == 'pending' && auth()->user()->hasPermission('hr.leaves.approve'))
                                    <form action="{{ route('hr.leaves.approve', $leave) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn-action btn-modern btn-modern-success" title="Approve" onclick="return confirm('Approve this leave request?')"><i class="fas fa-check"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-alt"></i>
                                    <p class="mb-3">No leave requests found</p>
                                    @if(auth()->user()->hasPermission('hr.leaves.create'))
                                    <a href="{{ route('hr.leaves.create') }}" class="btn btn-modern btn-modern-primary">
                                        <i class="fas fa-plus mr-1"></i>Create First Request
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
        @if($leaveRequests->hasPages())
        <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
            {{ $leaveRequests->links() }}
        </div>
        @endif
    </div>
</div>
@stop
