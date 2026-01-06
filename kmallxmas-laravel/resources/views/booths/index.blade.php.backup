@extends('layouts.app')

@section('title', 'Booths')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-store me-2"></i>Booths</h2>
    </div>
    @auth
    @if(auth()->user()->isAdmin())
    <div class="col-auto">
        <a href="{{ route('booths.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Booth
        </a>
    </div>
    @endif
    @endauth
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('booths.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Available</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Confirmed</option>
                    <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>Reserved</option>
                    <option value="5" {{ request('status') == '5' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select">
                    <option value="">All</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Company</label>
                <input type="text" name="company" class="form-control" value="{{ request('company') }}" placeholder="Search company...">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Filter</button>
                <a href="{{ route('booths.index') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Booths Grid -->
<div class="row g-3">
    @foreach($booths as $booth)
    <div class="col-md-3">
        <div class="card h-100 border-{{ $booth->getStatusColor() }}">
            <div class="card-header bg-{{ $booth->getStatusColor() }} text-white">
                <h5 class="mb-0">{{ $booth->booth_number }}</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Status:</strong> 
                    <span class="badge bg-{{ $booth->getStatusColor() }}">{{ $booth->getStatusLabel() }}</span>
                </p>
                <p class="mb-1"><strong>Price:</strong> ${{ number_format($booth->price, 2) }}</p>
                @if($booth->client)
                <p class="mb-1"><strong>Client:</strong> {{ $booth->client->company }}</p>
                @endif
                @if($booth->category)
                <p class="mb-1"><strong>Category:</strong> {{ $booth->category->name }}</p>
                @endif
            </div>
            <div class="card-footer">
                <a href="{{ route('booths.show', $booth) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i> View
                </a>
                @auth
                @if(auth()->user()->isAdmin())
                <a href="{{ route('booths.edit', $booth) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
                @endauth
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($booths->isEmpty())
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>No booths found.
</div>
@endif
@endsection
