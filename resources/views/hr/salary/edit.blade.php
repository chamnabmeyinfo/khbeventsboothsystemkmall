@extends('layouts.adminlte')

@section('title', 'Edit Salary Entry')

@section('content_header')
    <h1 class="m-0"><i class="fas fa-edit mr-2"></i>Edit Salary History Entry</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('hr.salary.update', $salary) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="employee_id">Employee <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-control select2" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('employee_id', $salary->employee_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }} ({{ $emp->employee_code }})
                                        @if($emp->salary)
                                            - Current: {{ number_format($emp->salary, 2) }} {{ $emp->currency ?? 'USD' }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="effective_date">Effective Date <span class="text-danger">*</span></label>
                            <input type="date" name="effective_date" id="effective_date" class="form-control" 
                                   value="{{ old('effective_date', $salary->effective_date->format('Y-m-d')) }}" required>
                            @error('effective_date')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="salary">Salary Amount <span class="text-danger">*</span></label>
                            <input type="number" name="salary" id="salary" class="form-control" 
                                   value="{{ old('salary', $salary->salary) }}" step="0.01" min="0" required>
                            @error('salary')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select name="currency" id="currency" class="form-control">
                                <option value="USD" {{ old('currency', $salary->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="KHR" {{ old('currency', $salary->currency) == 'KHR' ? 'selected' : '' }}>KHR</option>
                                <option value="EUR" {{ old('currency', $salary->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ old('currency', $salary->currency) == 'GBP' ? 'selected' : '' }}>GBP</option>
                            </select>
                            @error('currency')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reason">Reason for Change</label>
                    <input type="text" name="reason" id="reason" class="form-control" 
                           value="{{ old('reason', $salary->reason) }}" 
                           placeholder="e.g., Annual increment, Promotion, Adjustment">
                    @error('reason')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $salary->notes) }}</textarea>
                    @error('notes')<span class="text-danger">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>Update Salary Entry
                    </button>
                    <a href="{{ route('hr.salary.show', $salary) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@stop
