@extends('layouts.adminlte')

@section('title', 'My Leaves')

@push('styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-calendar-alt mr-2"></i>My Leaves</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#applyLeaveModal">
            <i class="fas fa-plus mr-1"></i>Apply for Leave
        </button>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Leave Balances -->
    <div class="row mb-3">
        @foreach($leaveBalances as $balance)
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ $balance->leaveType->name }}</span>
                    <span class="info-box-number">{{ number_format($balance->balance, 1) }} days</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Leave Requests -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Leave Requests</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('employee.leaves') }}" class="d-inline">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default"><i class="fas fa-filter"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Applied On</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr>
                        <td>{{ $leave->leaveType->name }}</td>
                        <td>{{ $leave->start_date->format('M d, Y') }}</td>
                        <td>{{ $leave->end_date->format('M d, Y') }}</td>
                        <td><strong>{{ $leave->total_days }}</strong></td>
                        <td>{{ Str::limit($leave->reason, 50) }}</td>
                        <td>
                            <span class="badge badge-{{ $leave->status == 'approved' ? 'success' : ($leave->status == 'rejected' ? 'danger' : ($leave->status == 'cancelled' ? 'secondary' : 'warning')) }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                        <td>{{ $leave->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No leave requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($leaves->hasPages())
        <div class="card-footer">{{ $leaves->links() }}</div>
        @endif
    </div>
</div>

<!-- Apply Leave Modal -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('employee.leaves.apply') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Apply for Leave</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="leave_type_id">Leave Type <span class="text-danger">*</span></label>
                        <select name="leave_type_id" id="leave_type_id" class="form-control" required>
                            <option value="">Select Leave Type</option>
                            @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('leave_type_id')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" required>
                                @error('start_date')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" required>
                                @error('end_date')<span class="text-danger">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="half_day" id="half_day" class="form-check-input" value="1">
                            <label class="form-check-label" for="half_day">Half Day</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" required placeholder="Please provide a reason for your leave request"></textarea>
                        @error('reason')<span class="text-danger">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Set minimum date to today
        var today = new Date().toISOString().split('T')[0];
        $('#start_date').attr('min', today);
        $('#end_date').attr('min', today);

        // Update end date minimum when start date changes
        $('#start_date').on('change', function() {
            $('#end_date').attr('min', $(this).val());
        });
    });
</script>
@stop
