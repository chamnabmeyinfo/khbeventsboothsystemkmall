@extends('layouts.adminlte')

@section('title', 'Leave Request Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-calendar-alt mr-2"></i>Leave Request Details</h1>
        <div class="btn-group">
            @if($leaveRequest->status == 'pending' && auth()->user()->hasPermission('hr.leaves.edit'))
            <a href="{{ route('hr.leaves.edit', $leaveRequest) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
            @endif
            @if(in_array($leaveRequest->status, ['pending', 'cancelled']) && auth()->user()->hasPermission('hr.leaves.delete'))
            <form action="{{ route('hr.leaves.destroy', $leaveRequest) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this leave request? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('hr.leaves.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Leave Request Information</h3></div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="30%">Employee:</th><td><a href="{{ route('hr.employees.show', $leaveRequest->employee) }}">{{ $leaveRequest->employee->full_name }}</a></td></tr>
                        <tr><th>Leave Type:</th><td>{{ $leaveRequest->leaveType->name }}</td></tr>
                        <tr><th>Start Date:</th><td>{{ $leaveRequest->start_date->format('M d, Y') }}</td></tr>
                        <tr><th>End Date:</th><td>{{ $leaveRequest->end_date->format('M d, Y') }}</td></tr>
                        <tr><th>Total Days:</th><td><strong>{{ $leaveRequest->total_days }}</strong></td></tr>
                        <tr><th>Reason:</th><td>{{ $leaveRequest->reason ?? '-' }}</td></tr>
                        <tr><th>Status:</th><td>
                            @php
                                $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', 'cancelled' => 'secondary'];
                                $color = $statusColors[$leaveRequest->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $color }} badge-lg">{{ ucfirst($leaveRequest->status) }}</span>
                        </td></tr>
                        @if($leaveRequest->approved_by)
                        <tr><th>Approved By:</th><td>{{ $leaveRequest->approver->username ?? '-' }} on {{ $leaveRequest->approved_at->format('M d, Y H:i') }}</td></tr>
                        @endif
                        @if($leaveRequest->rejected_by)
                        <tr><th>Rejected By:</th><td>{{ $leaveRequest->rejector->username ?? '-' }} on {{ $leaveRequest->rejected_at->format('M d, Y H:i') }}</td></tr>
                        <tr><th>Rejection Reason:</th><td>{{ $leaveRequest->rejection_reason ?? '-' }}</td></tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            @if($leaveRequest->status == 'pending' && auth()->user()->hasPermission('hr.leaves.approve'))
            <div class="card">
                <div class="card-header"><h3 class="card-title">Actions</h3></div>
                <div class="card-body">
                    <form action="{{ route('hr.leaves.approve', $leaveRequest) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Approve this leave request?')">
                            <i class="fas fa-check mr-1"></i>Approve
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectModal">
                        <i class="fas fa-times mr-1"></i>Reject
                    </button>
                </div>
            </div>
            @endif

            @if($leaveRequest->status == 'pending' && auth()->user()->hasPermission('hr.leaves.manage'))
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('hr.leaves.cancel', $leaveRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-block" onclick="return confirm('Cancel this leave request?')">
                            <i class="fas fa-ban mr-1"></i>Cancel Request
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hr.leaves.reject', $leaveRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Leave Request</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Leave</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
