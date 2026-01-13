@extends('layouts.adminlte')

@section('title', 'Affiliate Management')
@section('page-title', 'Affiliate Management')
@section('breadcrumb', 'Sales / Affiliates')

@push('styles')
<style>
    .affiliate-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 24px;
        margin-bottom: 24px;
        transition: all 0.3s;
    }
    
    .affiliate-card:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
    }
    
    .affiliate-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .affiliate-user {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .affiliate-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: 700;
    }
    
    .affiliate-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 16px;
        border-radius: 12px;
        text-align: center;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #495057;
        margin-bottom: 4px;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-card.primary .stat-value {
        color: #667eea;
    }
    
    .stat-card.success .stat-value {
        color: #28a745;
    }
    
    .stat-card.info .stat-value {
        color: #17a2b8;
    }
    
    .stat-card.warning .stat-value {
        color: #ffc107;
    }
    
    .floor-plan-badge {
        display: inline-block;
        padding: 6px 12px;
        background: #e9ecef;
        border-radius: 20px;
        font-size: 0.8125rem;
        margin: 4px;
        color: #495057;
    }
    
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('affiliates.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Search User</label>
                <input type="text" name="search" class="form-control" placeholder="Search by username..." value="{{ $search }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sales Person</label>
                <select name="user_id" class="form-control">
                    <option value="">All Sales People</option>
                    @foreach($allUsers as $user)
                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                            {{ $user->username }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Floor Plan</label>
                <select name="floor_plan_id" class="form-control">
                    <option value="">All Floor Plans</option>
                    @foreach($floorPlans as $fp)
                        <option value="{{ $fp->id }}" {{ $floorPlanId == $fp->id ? 'selected' : '' }}>
                            {{ $fp->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
                <a href="{{ route('affiliates.export', request()->all()) }}" class="btn btn-success">
                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                </a>
                <a href="{{ route('affiliates.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Affiliates List -->
    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-chart-bar mr-2"></i>Sales Performance (Affiliate Links)</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>User</th>
                            <th class="text-center">Bookings</th>
                            <th class="text-right">Revenue</th>
                            <th class="text-center">Unique Clients</th>
                            <th class="text-center">Floor Plans</th>
                            <th class="text-right">Avg / Booking</th>
                            <th class="text-center">Last Booking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $data)
                        <tr>
                            <td>
                                <strong>{{ $data['user']->username }}</strong><br>
                                <small class="text-muted">{{ $data['user']->isAdmin() ? 'Administrator' : 'Sales' }}</small>
                            </td>
                            <td class="text-center">{{ $data['total_bookings'] }}</td>
                            <td class="text-right">${{ number_format($data['total_revenue'], 2) }}</td>
                            <td class="text-center">{{ $data['unique_clients'] }}</td>
                            <td class="text-center">{{ $data['unique_floor_plans'] }}</td>
                            <td class="text-right">${{ number_format($data['avg_booking_value'], 2) }}</td>
                            <td class="text-center">
                                @if($data['last_booking_at'])
                                    {{ \Carbon\Carbon::parse($data['last_booking_at'])->format('M d, Y') }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No affiliate performance data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @forelse($users as $data)
    <div class="affiliate-card">
        <div class="affiliate-header">
            <div class="affiliate-user">
                <div class="affiliate-avatar">
                    {{ strtoupper(substr($data['user']->username, 0, 1)) }}
                </div>
                <div>
                    <h4 class="mb-1" style="font-weight: 700; color: #212529;">
                        {{ $data['user']->username }}
                    </h4>
                    <p class="text-muted mb-0" style="font-size: 0.875rem;">
                        @if($data['user']->isAdmin())
                            <span class="badge badge-primary">Administrator</span>
                        @else
                            <span class="badge badge-secondary">Sales Person</span>
                        @endif
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('affiliates.show', $data['user']->id) }}" class="btn btn-primary">
                    <i class="fas fa-chart-line mr-2"></i>View Details
                </a>
            </div>
        </div>
        
        <div class="affiliate-stats">
            <div class="stat-card primary">
                <div class="stat-value">{{ $data['total_bookings'] }}</div>
                <div class="stat-label">Total Bookings</div>
            </div>
            <div class="stat-card success">
                <div class="stat-value">${{ number_format($data['total_revenue'], 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card info">
                <div class="stat-value">{{ $data['unique_clients'] }}</div>
                <div class="stat-label">Unique Clients</div>
            </div>
            <div class="stat-card warning">
                <div class="stat-value">{{ $data['bookings_by_floor_plan']->count() }}</div>
                <div class="stat-label">Floor Plans</div>
            </div>
        </div>
        
        @if($data['bookings_by_floor_plan']->count() > 0)
        <div style="margin-top: 20px;">
            <h6 style="font-weight: 600; margin-bottom: 12px; color: #495057;">
                <i class="fas fa-map mr-2"></i>Bookings by Floor Plan:
            </h6>
            <div>
                @foreach($data['bookings_by_floor_plan'] as $fpId => $fpData)
                    @php
                        $floorPlan = \App\Models\FloorPlan::find($fpId);
                    @endphp
                    @if($floorPlan)
                    <span class="floor-plan-badge">
                        <strong>{{ $floorPlan->name }}</strong>: 
                        {{ $fpData['count'] }} bookings 
                        (${{ number_format($fpData['revenue'], 2) }})
                    </span>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
        
        @if($data['recent_bookings']->count() > 0)
        <div style="margin-top: 20px;">
            <h6 style="font-weight: 600; margin-bottom: 12px; color: #495057;">
                <i class="fas fa-clock mr-2"></i>Recent Bookings:
            </h6>
            <div style="font-size: 0.875rem; color: #6c757d;">
                @foreach($data['recent_bookings'] as $booking)
                    <div style="margin-bottom: 8px;">
                        <i class="fas fa-calendar-check mr-2"></i>
                        {{ \Carbon\Carbon::parse($booking->date_book)->format('M d, Y') }} - 
                        @if($booking->client)
                            {{ $booking->client->company ?? $booking->client->name }}
                        @else
                            Client #{{ $booking->clientid }}
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5>No Affiliate Data Found</h5>
            <p class="text-muted">No affiliate bookings found with the current filters.</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
