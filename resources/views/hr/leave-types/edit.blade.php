@extends('layouts.adminlte')

@section('title', 'Edit Leave Type')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-edit mr-2"></i>Edit Leave Type: {{ $leaveType->name }}</h1>
        <a href="{{ route('hr.leave-types.show', $leaveType) }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <form action="{{ route('hr.leave-types.update', $leaveType) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Leave Type Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $leaveType->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                   id="code" name="code" value="{{ old('code', $leaveType->code) }}">
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $leaveType->description) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="max_days_per_year">Max Days Per Year</label>
                            <input type="number" class="form-control" id="max_days_per_year" name="max_days_per_year" value="{{ old('max_days_per_year', $leaveType->max_days_per_year) }}" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="sort_order">Sort Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="{{ old('sort_order', $leaveType->sort_order) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="carry_forward" name="carry_forward" value="1" {{ old('carry_forward', $leaveType->carry_forward) ? 'checked' : '' }}>
                            <label class="form-check-label" for="carry_forward">Carry Forward</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="requires_approval" name="requires_approval" value="1" {{ old('requires_approval', $leaveType->requires_approval) ? 'checked' : '' }}>
                            <label class="form-check-label" for="requires_approval">Requires Approval</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_paid" name="is_paid" value="1" {{ old('is_paid', $leaveType->is_paid) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_paid">Paid Leave</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $leaveType->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Update Leave Type</button>
                <a href="{{ route('hr.leave-types.show', $leaveType) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@stop
