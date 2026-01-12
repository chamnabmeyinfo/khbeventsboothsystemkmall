@extends('layouts.adminlte')

@section('title', 'Leave Type Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-calendar-check mr-2"></i>{{ $leaveType->name }}</h1>
        <div class="btn-group">
            @if(auth()->user()->hasPermission('hr.leaves.manage'))
            <a href="{{ route('hr.leave-types.edit', $leaveType) }}" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</a>
            @endif
            @if(auth()->user()->hasPermission('hr.leaves.manage'))
            <form action="{{ route('hr.leave-types.destroy', $leaveType) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this leave type?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Delete">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('hr.leave-types.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h3 class="card-title">Leave Type Information</h3></div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th width="30%">Name:</th><td>{{ $leaveType->name }}</td></tr>
                <tr><th>Code:</th><td>{{ $leaveType->code ?? '-' }}</td></tr>
                <tr><th>Description:</th><td>{{ $leaveType->description ?? '-' }}</td></tr>
                <tr><th>Max Days Per Year:</th><td>{{ $leaveType->max_days_per_year ?? 'Unlimited' }}</td></tr>
                <tr><th>Carry Forward:</th><td><span class="badge badge-{{ $leaveType->carry_forward ? 'success' : 'secondary' }}">{{ $leaveType->carry_forward ? 'Yes' : 'No' }}</span></td></tr>
                <tr><th>Requires Approval:</th><td><span class="badge badge-{{ $leaveType->requires_approval ? 'info' : 'secondary' }}">{{ $leaveType->requires_approval ? 'Yes' : 'No' }}</span></td></tr>
                <tr><th>Paid Leave:</th><td><span class="badge badge-{{ $leaveType->is_paid ? 'success' : 'warning' }}">{{ $leaveType->is_paid ? 'Paid' : 'Unpaid' }}</span></td></tr>
                <tr><th>Status:</th><td><span class="badge badge-{{ $leaveType->is_active ? 'success' : 'secondary' }}">{{ $leaveType->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
                <tr><th>Total Requests:</th><td><span class="badge badge-info">{{ $leaveType->leave_requests_count ?? 0 }}</span></td></tr>
            </table>
        </div>
    </div>
</div>
@stop
