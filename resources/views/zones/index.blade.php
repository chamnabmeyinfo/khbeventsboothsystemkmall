@extends('layouts.adminlte')

@section('title', 'Zones Management')
@section('page-title', 'Zones Management')
@section('breadcrumb', 'Zones')

@push('styles')
<style>
    .zone-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-left: 4px solid #667eea;
        cursor: pointer;
    }

    .zone-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .zone-about-preview {
        color: #6b7280;
        font-size: 0.875rem;
        max-height: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
</style>
@endpush

@section('content')
<div class="content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="m-0">Zones Management</h1>
                    <p class="text-muted mb-0">Manage zone settings and information</p>
                </div>
                <div>
                    <a href="{{ route('zones.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create New Zone
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('zones.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Search Zone Name</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Search zones...">
                    </div>
                    <div class="col-md-4">
                        <label for="floor_plan_id" class="form-label">Floor Plan</label>
                        <select class="form-control" id="floor_plan_id" name="floor_plan_id">
                            <option value="">All Floor Plans</option>
                            @foreach($floorPlans as $floorPlan)
                                <option value="{{ $floorPlan->id }}" {{ request('floor_plan_id') == $floorPlan->id ? 'selected' : '' }}>
                                    {{ $floorPlan->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search mr-1"></i>Filter
                        </button>
                        <a href="{{ route('zones.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i>Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Zones List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Zones ({{ $zones->total() }})</h3>
            </div>
            <div class="card-body p-0">
                @if($zones->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('zones.index', array_merge(request()->all(), ['sort_by' => 'zone_name', 'sort_dir' => $sortBy == 'zone_name' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}">
                                        Zone Name
                                        @if($sortBy == 'zone_name')
                                            <i class="fas fa-sort-{{ $sortDir == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Floor Plan</th>
                                <th>Zone About</th>
                                <th>
                                    <a href="{{ route('zones.index', array_merge(request()->all(), ['sort_by' => 'price', 'sort_dir' => $sortBy == 'price' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}">
                                        Price
                                        @if($sortBy == 'price')
                                            <i class="fas fa-sort-{{ $sortDir == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>Size (W×H)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($zones as $zone)
                            <tr class="table-row-hover">
                                <td>
                                    <strong>{{ $zone->zone_name }}</strong>
                                </td>
                                <td>
                                    @if($zone->floorPlan)
                                        <span class="badge badge-info">{{ $zone->floorPlan->name }}</span>
                                    @else
                                        <span class="badge badge-secondary">Global</span>
                                    @endif
                                </td>
                                <td>
                                    @if($zone->zone_about)
                                        <div class="zone-about-preview">{{ Str::limit($zone->zone_about, 100) }}</div>
                                    @else
                                        <span class="text-muted">No description</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>${{ number_format($zone->price ?? 0, 2) }}</strong>
                                </td>
                                <td>
                                    {{ $zone->width }}×{{ $zone->height }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('zones.show', $zone) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('zones.edit', $zone) }}" class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('zones.destroy', $zone) }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this zone?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    {{ $zones->links() }}
                </div>
                @else
                <div class="text-center p-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No zones found.</p>
                    <a href="{{ route('zones.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create First Zone
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
