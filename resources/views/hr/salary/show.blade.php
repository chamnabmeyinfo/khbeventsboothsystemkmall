@extends('layouts.adminlte')

@section('title', 'Salary History Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-money-bill-wave mr-2"></i>Salary History Details</h1>
        <div>
            @if(auth()->user()->hasPermission('hr.salary.edit'))
            <a href="{{ route('hr.salary.edit', $salary) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
            @endif
            @if(auth()->user()->hasPermission('hr.salary.delete'))
            <form action="{{ route('hr.salary.destroy', $salary) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this salary history entry?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Delete">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('hr.salary.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Back
            </a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Salary Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Employee</th>
                            <td>
                                <a href="{{ route('hr.employees.show', $salary->employee) }}">
                                    <strong>{{ $salary->employee->full_name }}</strong> ({{ $salary->employee->employee_code }})
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Effective Date</th>
                            <td><strong>{{ $salary->effective_date->format('M d, Y') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Salary Amount</th>
                            <td>
                                <h4 class="m-0 text-primary">
                                    {{ number_format($salary->salary, 2) }} {{ $salary->currency ?? 'USD' }}
                                </h4>
                            </td>
                        </tr>
                        <tr>
                            <th>Currency</th>
                            <td>{{ $salary->currency ?? 'USD' }}</td>
                        </tr>
                        @if($salary->reason)
                        <tr>
                            <th>Reason for Change</th>
                            <td>{{ $salary->reason }}</td>
                        </tr>
                        @endif
                        @if($salary->notes)
                        <tr>
                            <th>Notes</th>
                            <td>{{ $salary->notes }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Approved By</th>
                            <td>{{ $salary->approver->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $salary->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $salary->updated_at->format('M d, Y h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    @if(auth()->user()->hasPermission('hr.salary.edit'))
                    <a href="{{ route('hr.salary.edit', $salary) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit mr-1"></i>Edit Salary Entry
                    </a>
                    @endif
                    <a href="{{ route('hr.employees.show', $salary->employee) }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-user mr-1"></i>View Employee
                    </a>
                    <a href="{{ route('hr.salary.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left mr-1"></i>Back to List
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employee Current Salary</h3>
                </div>
                <div class="card-body">
                    @if($salary->employee->salary)
                        <h4 class="text-primary">
                            {{ number_format($salary->employee->salary, 2) }} 
                            {{ $salary->employee->currency ?? 'USD' }}
                        </h4>
                        <small class="text-muted">
                            @if($salary->employee->salary == $salary->salary)
                                <i class="fas fa-check-circle text-success"></i> This is the current salary
                            @else
                                <i class="fas fa-info-circle"></i> Employee has a different current salary
                            @endif
                        </small>
                    @else
                        <span class="text-muted">No current salary set</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop
