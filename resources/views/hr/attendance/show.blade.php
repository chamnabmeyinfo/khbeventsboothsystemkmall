@extends('layouts.adminlte')

@section('title', 'Attendance Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-clock mr-2"></i>Attendance Record</h1>
        <div class="btn-group">
            @if(auth()->user()->hasPermission('hr.attendance.edit'))
            <a href="{{ route('hr.attendance.edit', $attendance) }}" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</a>
            @endif
            @if(auth()->user()->hasPermission('hr.attendance.delete'))
            <form action="{{ route('hr.attendance.destroy', $attendance) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this attendance record?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Delete">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('hr.attendance.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h3 class="card-title">Attendance Information</h3></div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th width="30%">Employee:</th><td><a href="{{ route('hr.employees.show', $attendance->employee) }}">{{ $attendance->employee->full_name }}</a></td></tr>
                <tr><th>Date:</th><td>{{ $attendance->date->format('M d, Y') }}</td></tr>
                <tr><th>Check In:</th><td>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}</td></tr>
                <tr><th>Check Out:</th><td>{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}</td></tr>
                <tr><th>Break Duration:</th><td>{{ $attendance->break_duration }} minutes</td></tr>
                <tr><th>Total Hours:</th><td><strong>{{ $attendance->total_hours ?? '-' }}</strong></td></tr>
                <tr><th>Status:</th><td>
                    @php
                        $statusColors = ['present' => 'success', 'absent' => 'danger', 'late' => 'warning', 'half-day' => 'info'];
                        $color = $statusColors[$attendance->status] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $color }} badge-lg">{{ ucfirst(str_replace('-', ' ', $attendance->status)) }}</span>
                </td></tr>
                @if($attendance->late_minutes > 0)
                <tr><th>Late Minutes:</th><td>{{ $attendance->late_minutes }}</td></tr>
                @endif
                @if($attendance->overtime_hours > 0)
                <tr><th>Overtime Hours:</th><td>{{ $attendance->overtime_hours }}</td></tr>
                @endif
                @if($attendance->notes)
                <tr><th>Notes:</th><td>{{ $attendance->notes }}</td></tr>
                @endif
                @if($attendance->approved_by)
                <tr><th>Approved By:</th><td>{{ $attendance->approver->username ?? '-' }} on {{ $attendance->approved_at->format('M d, Y H:i') }}</td></tr>
                @endif
            </table>
        </div>
        @if(!$attendance->approved_by && auth()->user()->hasPermission('hr.attendance.approve'))
        <div class="card-footer">
            <form action="{{ route('hr.attendance.approve', $attendance) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this attendance record?')">
                    <i class="fas fa-check mr-1"></i>Approve
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@stop
