@extends('layouts.adminlte')

@section('title', 'Attendance')

@push('styles')
@include('hr._modern-styles')
@include('hr._mobile-styles')
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-clock mr-2"></i>Attendance Management</h1>
        @if(auth()->user()->hasPermission('hr.attendance.create'))
        <a href="{{ route('hr.attendance.create') }}" class="btn btn-modern btn-modern-primary">
            <i class="fas fa-plus mr-1"></i>Add Attendance
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
            <form method="GET" action="{{ route('hr.attendance.index') }}" class="row g-3">
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
                <div class="col-md-2">
                    <label class="form-label font-weight-bold">Status</label>
                    <select class="form-control form-control-modern" name="status">
                        <option value="">All Status</option>
                        <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                        <option value="half-day" {{ request('status') == 'half-day' ? 'selected' : '' }}>Half Day</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-modern btn-modern-primary w-100"><i class="fas fa-search mr-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <h3><i class="fas fa-list mr-2"></i>Attendance Records</h3>
            <span class="badge-modern badge-modern-primary">{{ $attendances->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th style="width: 180px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $att)
                        <tr>
                            <td class="font-weight-bold text-muted">{{ $att->id }}</td>
                            <td>
                                <a href="{{ route('hr.employees.show', $att->employee) }}" class="font-weight-bold text-primary">
                                    {{ $att->employee->full_name }}
                                </a>
                            </td>
                            <td><span class="font-weight-semibold">{{ $att->date->format('M d, Y') }}</span></td>
                            <td><span class="text-muted">{{ $att->check_in_time ? \Carbon\Carbon::parse($att->check_in_time)->format('H:i') : '-' }}</span></td>
                            <td><span class="text-muted">{{ $att->check_out_time ? \Carbon\Carbon::parse($att->check_out_time)->format('H:i') : '-' }}</span></td>
                            <td><span class="font-weight-semibold">{{ $att->total_hours ?? '-' }}</span></td>
                            <td>
                                @php
                                    $statusBadges = [
                                        'present' => 'badge-modern-success',
                                        'absent' => 'badge-modern-danger',
                                        'late' => 'badge-modern-warning',
                                        'half-day' => 'badge-modern-info'
                                    ];
                                    $badgeClass = $statusBadges[$att->status] ?? 'badge-modern-info';
                                @endphp
                                <span class="badge-modern {{ $badgeClass }}">{{ ucfirst(str_replace('-', ' ', $att->status)) }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('hr.attendance.show', $att) }}" class="btn-action btn-modern btn-modern-info" title="View"><i class="fas fa-eye"></i></a>
                                    @if(auth()->user()->hasPermission('hr.attendance.edit'))
                                    <a href="{{ route('hr.attendance.edit', $att) }}" class="btn-action btn-modern btn-modern-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(auth()->user()->hasPermission('hr.attendance.delete'))
                                    <form action="{{ route('hr.attendance.destroy', $att) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this attendance record?');">
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
                                    <i class="fas fa-clock"></i>
                                    <p class="mb-3">No attendance records found</p>
                                    @if(auth()->user()->hasPermission('hr.attendance.create'))
                                    <a href="{{ route('hr.attendance.create') }}" class="btn btn-modern btn-modern-primary">
                                        <i class="fas fa-plus mr-1"></i>Create First Record
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
        @if($attendances->hasPages())
        <div class="card-footer" style="background: rgba(102, 126, 234, 0.05); border-top: 1px solid rgba(102, 126, 234, 0.1);">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>
</div>
@stop
