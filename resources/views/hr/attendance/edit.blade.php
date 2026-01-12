@extends('layouts.adminlte')

@section('title', 'Edit Attendance')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-edit mr-2"></i>Edit Attendance Record</h1>
        <a href="{{ route('hr.attendance.show', $attendance) }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('hr.attendance.update', $attendance) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employee_id">Employee <span class="text-danger">*</span></label>
                            <select class="form-control select2 @error('employee_id') is-invalid @enderror" 
                                    id="employee_id" name="employee_id" required>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $attendance->employee_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }} ({{ $emp->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                   id="date" name="date" 
                                   value="{{ old('date', $attendance->date->format('Y-m-d')) }}" required>
                            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="check_in_time">Check In Time</label>
                            <input type="time" class="form-control" id="check_in_time" name="check_in_time" 
                                   value="{{ old('check_in_time', $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="check_out_time">Check Out Time</label>
                            <input type="time" class="form-control" id="check_out_time" name="check_out_time" 
                                   value="{{ old('check_out_time', $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="break_duration">Break Duration (minutes)</label>
                            <input type="number" class="form-control" id="break_duration" name="break_duration" 
                                   value="{{ old('break_duration', $attendance->break_duration) }}" min="0">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="late" {{ old('status', $attendance->status) == 'late' ? 'selected' : '' }}>Late</option>
                                <option value="half-day" {{ old('status', $attendance->status) == 'half-day' ? 'selected' : '' }}>Half Day</option>
                                <option value="on-leave" {{ old('status', $attendance->status) == 'on-leave' ? 'selected' : '' }}>On Leave</option>
                                <option value="holiday" {{ old('status', $attendance->status) == 'holiday' ? 'selected' : '' }}>Holiday</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Update Attendance</button>
                <a href="{{ route('hr.attendance.show', $attendance) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@stop

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap4' });
    });
</script>
@endpush
