@extends('layouts.adminlte')

@section('title', 'Training Record Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-graduation-cap mr-2"></i>{{ $training->training_name }}</h1>
        <div class="btn-group">
            @if(auth()->user()->hasPermission('hr.training.edit'))
            <a href="{{ route('hr.training.edit', $training) }}" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</a>
            @endif
            @if(auth()->user()->hasPermission('hr.training.delete'))
            <form action="{{ route('hr.training.destroy', $training) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this training record?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Delete">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('hr.training.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><h3 class="card-title">Training Information</h3></div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th width="30%">Employee:</th><td><a href="{{ route('hr.employees.show', $training->employee) }}">{{ $training->employee->full_name }}</a></td></tr>
                <tr><th>Training Name:</th><td><strong>{{ $training->training_name }}</strong></td></tr>
                <tr><th>Provider:</th><td>{{ $training->training_provider ?? '-' }}</td></tr>
                <tr><th>Start Date:</th><td>{{ $training->start_date ? $training->start_date->format('M d, Y') : '-' }}</td></tr>
                <tr><th>End Date:</th><td>{{ $training->end_date ? $training->end_date->format('M d, Y') : '-' }}</td></tr>
                <tr><th>Status:</th><td>
                    <span class="badge badge-{{ $training->status == 'completed' ? 'success' : ($training->status == 'in-progress' ? 'info' : 'warning') }} badge-lg">
                        {{ ucfirst(str_replace('-', ' ', $training->status)) }}
                    </span>
                </td></tr>
                @if($training->cost)
                <tr><th>Cost:</th><td>{{ number_format($training->cost, 2) }}</td></tr>
                @endif
                @if($training->certificate_number)
                <tr><th>Certificate Number:</th><td>{{ $training->certificate_number }}</td></tr>
                @endif
                @if($training->notes)
                <tr><th>Notes:</th><td>{{ $training->notes }}</td></tr>
                @endif
            </table>

            @if($training->certificate_file)
            <div class="mt-3">
                <a href="{{ asset('storage/' . $training->certificate_file) }}" target="_blank" class="btn btn-info">
                    <i class="fas fa-download mr-1"></i>Download Certificate
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@stop
