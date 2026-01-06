@extends('layouts.app')

@section('title', 'Clients')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-users me-2"></i>Clients</h2>
    </div>
    <div class="col-auto">
        <div class="btn-group">
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Client
            </a>
            <a href="{{ route('export.clients') }}" class="btn btn-success">
                <i class="fas fa-file-csv me-2"></i>Export CSV
            </a>
        </div>
    </div>
</div>

<!-- Advanced Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('clients.index') }}" class="row g-3">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by name, company, phone, or position..." 
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <select name="sort_by" class="form-select">
                    <option value="company" {{ request('sort_by') == 'company' ? 'selected' : '' }}>Company</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="position" {{ request('sort_by') == 'position' ? 'selected' : '' }}>Position</option>
                    <option value="phone_number" {{ request('sort_by') == 'phone_number' ? 'selected' : '' }}>Phone</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>
                                    <a href="{{ route('clients.index', ['sort_by' => 'company', 'sort_dir' => $sortBy == 'company' && $sortDir == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none text-dark">
                                        Company
                                        @if($sortBy == 'company')
                                            <i class="fas fa-sort-{{ $sortDir == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('clients.index', ['sort_by' => 'name', 'sort_dir' => $sortBy == 'name' && $sortDir == 'asc' ? 'desc' : 'asc', 'search' => request('search')]) }}" class="text-decoration-none text-dark">
                                        Name
                                        @if($sortBy == 'name')
                                            <i class="fas fa-sort-{{ $sortDir == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Position</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                <tbody>
                    @forelse($clients as $client)
                    <tr>
                        <td>{{ $client->id }}</td>
                        <td>{{ $client->company ?? 'N/A' }}</td>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->position ?? 'N/A' }}</td>
                        <td>{{ $client->phone_number ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No clients found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $clients->links() }}
        </div>
    </div>
</div>
@endsection
