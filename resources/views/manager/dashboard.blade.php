@extends('layouts.adminlte')

@section('title', 'Manager Dashboard')

@push('styles')
@include('hr._mobile-styles')
<style>
    .manager-dashboard {
        /* Additional manager-specific styles */
    }
</style>
@endpush

@section('content_header')
    <h1 class="m-0"><i class="fas fa-users-cog mr-2"></i>Manager Dashboard</h1>
@stop

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-body">
                    <h3 class="mb-0">Welcome, {{ $manager->full_name }}!</h3>
                    <p class="mb-0 text-muted">Managing {{ $teamSize }} team member(s)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $teamSize }}</h3>
                    <p>Team Members</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingLeaveRequests->count() }}</h3>
                    <p>Pending Leave Requests</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <a href="#pending-leaves" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $presentToday }}/{{ $teamSize }}</h3>
                    <p>Present Today</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ number_format($attendanceRate, 1) }}%</h3>
                    <p>Attendance Rate (This Month)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pending Leave Requests -->
        <div class="col-md-6">
            <div class="card" id="pending-leaves">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock mr-2"></i>Pending Leave Requests</h3>
                    @if($pendingLeaveRequests->count() > 0)
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-primary" onclick="selectAllLeaves()">
                            <i class="fas fa-check-square"></i> Select All
                        </button>
                    </div>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($pendingLeaveRequests->count() > 0)
                    <form id="bulkLeaveForm" action="{{ route('manager.leaves.bulk-approve') }}" method="POST">
                        @csrf
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="selectAllLeaves" onchange="toggleAllLeaves(this)">
                                    </th>
                                    <th>Employee</th>
                                    <th>Leave Type</th>
                                    <th>Period</th>
                                    <th>Days</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingLeaveRequests as $leave)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="leave_ids[]" value="{{ $leave->id }}" class="leave-checkbox">
                                    </td>
                                    <td>
                                        <a href="{{ route('hr.employees.show', $leave->employee) }}">
                                            {{ $leave->employee->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $leave->leaveType->name }}</td>
                                    <td>
                                        {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                                    </td>
                                    <td><strong>{{ $leave->total_days }}</strong></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <form action="{{ route('manager.leaves.approve', $leave) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger" onclick="rejectLeave({{ $leave->id }})" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-check-double mr-1"></i>Approve Selected
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p class="mb-0">No pending leave requests</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Pending Attendance Approvals -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock mr-2"></i>Pending Attendance Approvals</h3>
                </div>
                <div class="card-body p-0">
                    @if($pendingAttendanceApprovals->count() > 0)
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingAttendanceApprovals as $attendance)
                            <tr>
                                <td>
                                    <a href="{{ route('hr.employees.show', $attendance->employee) }}">
                                        {{ $attendance->employee->full_name }}
                                    </a>
                                </td>
                                <td>{{ $attendance->date->format('M d, Y') }}</td>
                                <td>{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') : '-' }}</td>
                                <td>{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('h:i A') : '-' }}</td>
                                <td>
                                    <span class="badge badge-warning">{{ ucfirst($attendance->status) }}</span>
                                </td>
                                <td>
                                    <form action="{{ route('manager.attendance.approve', $attendance) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <p class="mb-0">No pending attendance approvals</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Team Members -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users mr-2"></i>Team Members</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Department</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teamMembers as $member)
                            <tr>
                                <td>
                                    <a href="{{ route('hr.employees.show', $member) }}">
                                        {{ $member->full_name }}
                                    </a>
                                </td>
                                <td>{{ $member->position->name ?? '-' }}</td>
                                <td>{{ $member->department->name ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $member->status == 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst(str_replace('-', ' ', $member->status)) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upcoming Leaves -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-alt mr-2"></i>Upcoming Leaves (Next 7 Days)</h3>
                </div>
                <div class="card-body p-0">
                    @if($upcomingLeaves->count() > 0)
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingLeaves as $leave)
                            <tr>
                                <td>{{ $leave->employee->full_name }}</td>
                                <td>{{ $leave->leaveType->name }}</td>
                                <td>{{ $leave->start_date->format('M d, Y') }}</td>
                                <td><strong>{{ $leave->total_days }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="p-3 text-center text-muted">
                        No upcoming leaves in the next 7 days
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Team Calendar (Next 30 Days) -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar mr-2"></i>Team Leave Calendar (Next 30 Days)</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    @for($i = 0; $i < 30; $i++)
                                        @php $date = Carbon::today()->addDays($i); @endphp
                                        <th class="text-center" style="width: 30px;">
                                            {{ $date->format('d') }}<br>
                                            <small>{{ $date->format('D') }}</small>
                                        </th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teamMembers as $member)
                                <tr>
                                    <td><strong>{{ $member->full_name }}</strong></td>
                                    @for($i = 0; $i < 30; $i++)
                                        @php 
                                            $date = Carbon::today()->addDays($i);
                                            $leave = $teamLeaves->first(function($l) use ($member, $date) {
                                                return $l->employee_id == $member->id 
                                                    && $date->between($l->start_date, $l->end_date);
                                            });
                                        @endphp
                                        <td class="text-center {{ $leave ? 'bg-warning' : '' }}" style="padding: 2px;">
                                            @if($leave)
                                                <i class="fas fa-calendar-times text-warning" title="{{ $leave->leaveType->name }}"></i>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Leave Modal -->
<div class="modal fade" id="rejectLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectLeaveForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Reject Leave Request</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Rejection Reason</label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required></textarea>
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

@section('js')
<script>
    function toggleAllLeaves(checkbox) {
        document.querySelectorAll('.leave-checkbox').forEach(cb => {
            cb.checked = checkbox.checked;
        });
    }

    function selectAllLeaves() {
        document.querySelectorAll('.leave-checkbox').forEach(cb => {
            cb.checked = true;
        });
        document.getElementById('selectAllLeaves').checked = true;
    }

    function rejectLeave(leaveId) {
        const form = document.getElementById('rejectLeaveForm');
        form.action = '{{ url("/manager/leaves") }}/' + leaveId + '/reject';
        $('#rejectLeaveModal').modal('show');
    }
</script>
@stop
