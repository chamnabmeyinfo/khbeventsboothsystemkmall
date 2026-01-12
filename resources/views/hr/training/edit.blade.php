@extends('layouts.adminlte')

@section('title', 'Edit Training Record')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-edit mr-2"></i>Edit Training: {{ $training->training_name }}</h1>
        <a href="{{ route('hr.training.show', $training) }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('hr.training.update', $training) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employee_id">Employee <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="employee_id" name="employee_id" required>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $training->employee_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }} ({{ $emp->employee_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="scheduled" {{ old('status', $training->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in-progress" {{ old('status', $training->status) == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $training->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $training->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="training_name">Training Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="training_name" name="training_name" value="{{ old('training_name', $training->training_name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="training_provider">Training Provider</label>
                            <input type="text" class="form-control" id="training_provider" name="training_provider" value="{{ old('training_provider', $training->training_provider) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ old('start_date', $training->start_date ? $training->start_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="{{ old('end_date', $training->end_date ? $training->end_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="cost">Cost</label>
                            <input type="number" step="0.01" class="form-control" id="cost" name="cost" 
                                   value="{{ old('cost', $training->cost) }}" min="0">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="certificate_number">Certificate Number</label>
                            <input type="text" class="form-control" id="certificate_number" name="certificate_number" 
                                   value="{{ old('certificate_number', $training->certificate_number) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="certificate_file">Certificate File</label>
                            <input type="file" class="form-control" id="certificate_file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png">
                            @if($training->certificate_file)
                                <small class="form-text text-muted">Current: <a href="{{ asset('storage/' . $training->certificate_file) }}" target="_blank">View Certificate</a></small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes', $training->notes) }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Update Training</button>
                <a href="{{ route('hr.training.show', $training) }}" class="btn btn-secondary">Cancel</a>
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
