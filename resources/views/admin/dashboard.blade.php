@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
.stat-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-left: 4px solid;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.stat-card.border-primary { border-left-color: #0d6efd; }
.stat-card.border-success { border-left-color: #198754; }
.stat-card.border-info { border-left-color: #0dcaf0; }
.stat-card.border-warning { border-left-color: #ffc107; }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</h2>
        <p class="text-muted">Event Management System Overview</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total Events</h6>
                        <h2 class="mb-0 text-primary">{{ $stats['total_events'] }}</h2>
                    </div>
                    <div class="text-primary" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Active Events</h6>
                        <h2 class="mb-0 text-success">{{ $stats['active_events'] }}</h2>
                    </div>
                    <div class="text-success" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Upcoming Events</h6>
                        <h2 class="mb-0 text-info">{{ $stats['upcoming_events'] }}</h2>
                    </div>
                    <div class="text-info" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Categories</h6>
                        <h2 class="mb-0 text-warning">{{ $stats['total_categories'] }}</h2>
                    </div>
                    <div class="text-warning" style="font-size: 2.5rem; opacity: 0.3;">
                        <i class="fas fa-tags"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Recent Events</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Start Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEvents as $event)
                            <tr>
                                <td><a href="{{ route('admin.events.show', $event) }}">{{ $event->title }}</a></td>
                                <td>{{ $event->category->name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        // #region agent log
                                        try {
                                            $formattedDate = $event->formatted_start_date ?? 'N/A';
                                            @php
                                                \App\Helpers\DebugLogger::log(['event_id'=>$event->id,'formatted_date'=>$formattedDate], 'admin/dashboard.blade.php:121', 'Processing event in view');
                                            @endphp
                                        } catch (\Exception $e) {
                                            $formattedDate = 'Error: ' . $e->getMessage();
                                            @php
                                                \App\Helpers\DebugLogger::log(['error'=>$e->getMessage(),'event_id'=>$event->id], 'admin/dashboard.blade.php:124', 'Error accessing formatted_start_date');
                                            @endphp
                                        }
                                        // #endregion
                                    @endphp
                                    {{ $formattedDate }}
                                </td>
                                <td>
                                    @if($event->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No events found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Top Categories</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Events Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCategories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td><span class="badge bg-primary">{{ $category->events_count }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center">No categories found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

