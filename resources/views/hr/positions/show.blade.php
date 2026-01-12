@extends('layouts.adminlte')

@section('title', 'Position Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-briefcase mr-2"></i>{{ $position->name }}</h1>
        <div class="btn-group">
            @if(auth()->user()->hasPermission('hr.positions.edit'))
            <a href="{{ route('hr.positions.edit', $position) }}" class="btn btn-warning"><i class="fas fa-edit mr-1"></i>Edit</a>
            @endif
            @if(auth()->user()->hasPermission('hr.positions.create'))
            <form action="{{ route('hr.positions.duplicate', $position) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" title="Duplicate Position">
                    <i class="fas fa-copy mr-1"></i>Duplicate
                </button>
            </form>
            @endif
            @if(auth()->user()->hasPermission('hr.positions.delete'))
            <form action="{{ route('hr.positions.destroy', $position) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this position?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Delete Position">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('hr.positions.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left mr-1"></i>Back</a>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Position Information</h3></div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="30%">Name:</th><td>{{ $position->name }}</td></tr>
                        <tr><th>Code:</th><td>{{ $position->code ?? '-' }}</td></tr>
                        <tr><th>Department:</th><td>{{ $position->department->name ?? '-' }}</td></tr>
                        <tr><th>Description:</th><td>{{ $position->description ?? '-' }}</td></tr>
                        <tr><th>Requirements:</th><td>{{ $position->requirements ?? '-' }}</td></tr>
                        <tr><th>Salary Range:</th><td>
                            @if($position->min_salary || $position->max_salary)
                                {{ $position->min_salary ? number_format($position->min_salary, 2) : '-' }} - 
                                {{ $position->max_salary ? number_format($position->max_salary, 2) : '-' }}
                            @else
                                -
                            @endif
                        </td></tr>
                        <tr><th>Status:</th><td>
                            <span class="badge badge-{{ $position->is_active ? 'success' : 'secondary' }}">
                                {{ $position->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Employees ({{ $position->employees->count() }})</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Name</th><th>Department</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @forelse($position->employees as $emp)
                            <tr>
                                <td><a href="{{ route('hr.employees.show', $emp) }}">{{ $emp->full_name }}</a></td>
                                <td>{{ $emp->department->name ?? '-' }}</td>
                                <td><span class="badge badge-{{ $emp->status == 'active' ? 'success' : 'secondary' }}">{{ ucfirst($emp->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted">No employees</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
