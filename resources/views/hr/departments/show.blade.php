@extends('layouts.adminlte')

@section('title', 'Department Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0"><i class="fas fa-building mr-2"></i>{{ $department->name }}</h1>
        <div class="btn-group">
            @if(auth()->user()->hasPermission('hr.departments.edit'))
            <a href="{{ route('hr.departments.edit', $department) }}" class="btn btn-warning">
                <i class="fas fa-edit mr-1"></i>Edit
            </a>
            @endif
            @if(auth()->user()->hasPermission('hr.departments.create'))
            <form action="{{ route('hr.departments.duplicate', $department) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" title="Duplicate Department">
                    <i class="fas fa-copy mr-1"></i>Duplicate
                </button>
            </form>
            @endif
            @if(auth()->user()->hasPermission('hr.departments.delete'))
            <form action="{{ route('hr.departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" title="Delete Department">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
            </form>
            @endif
            <a href="{{ route('hr.departments.index') }}" class="btn btn-secondary">
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
                    <h3 class="card-title">Department Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="30%">Name:</th><td>{{ $department->name }}</td></tr>
                        <tr><th>Code:</th><td>{{ $department->code ?? '-' }}</td></tr>
                        <tr><th>Description:</th><td>{{ $department->description ?? '-' }}</td></tr>
                        <tr><th>Manager:</th><td>
                            @if($department->manager)
                                <a href="{{ route('hr.employees.show', $department->manager) }}">{{ $department->manager->full_name }}</a>
                            @else
                                -
                            @endif
                        </td></tr>
                        <tr><th>Parent Department:</th><td>{{ $department->parent->name ?? '-' }}</td></tr>
                        <tr><th>Budget:</th><td>{{ $department->budget ? number_format($department->budget, 2) : '-' }}</td></tr>
                        <tr><th>Status:</th><td>
                            <span class="badge badge-{{ $department->is_active ? 'success' : 'secondary' }}">
                                {{ $department->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Employees ({{ $department->employees->count() }})</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($department->employees as $emp)
                            <tr>
                                <td><a href="{{ route('hr.employees.show', $emp) }}">{{ $emp->full_name }}</a></td>
                                <td>{{ $emp->position->name ?? '-' }}</td>
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
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Positions ({{ $department->positions->count() }})</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($department->positions as $pos)
                        <li class="list-group-item">
                            <a href="{{ route('hr.positions.show', $pos) }}">{{ $pos->name }}</a>
                        </li>
                        @empty
                        <li class="list-group-item text-muted">No positions</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
