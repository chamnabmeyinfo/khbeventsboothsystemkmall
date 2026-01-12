@extends('layouts.adminlte')

@section('title', 'Edit Leave Request')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-edit mr-2"></i>Edit Leave Request</h1>
        <a href="{{ route('hr.leaves.show', $leaveRequest) }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    @if($leaveRequest->status != 'pending')
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Only pending leave requests can be edited.
        </div>
        <a href="{{ route('hr.leaves.show', $leaveRequest) }}" class="btn btn-secondary">Back to Leave Request</a>
    @else
    <form action="{{ route('hr.leaves.update', $leaveRequest) }}" method="POST">
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
                                <option value="">Select Employee...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $leaveRequest->employee_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }} ({{ $emp->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="leave_type_id">Leave Type <span class="text-danger">*</span></label>
                            <select class="form-control select2 @error('leave_type_id') is-invalid @enderror" 
                                    id="leave_type_id" name="leave_type_id" required>
                                <option value="">Select Leave Type...</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('leave_type_id', $leaveRequest->leave_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="start_date">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                   id="start_date" name="start_date" 
                                   value="{{ old('start_date', $leaveRequest->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="end_date">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                   id="end_date" name="end_date" 
                                   value="{{ old('end_date', $leaveRequest->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reason">Reason</label>
                    <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="Please provide a reason for this leave request...">{{ old('reason', $leaveRequest->reason) }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Update Leave Request</button>
                <a href="{{ route('hr.leaves.show', $leaveRequest) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
    @endif
</div>
@stop

@push('styles')
@include('hr._mobile-styles')
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({ theme: 'bootstrap4' });
        
        // Set minimum date for end date
        $('#start_date').on('change', function() {
            $('#end_date').attr('min', $(this).val());
        });
    });
</script>
@endpush
