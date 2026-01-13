@extends('layouts.adminlte')

@section('title', 'Affiliate Details - ' . $user->username)
@section('page-title', 'Affiliate Details')
@section('breadcrumb', 'Sales / Affiliates / ' . $user->username)

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        text-align: center;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .booking-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.2s;
    }
    
    .booking-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- User Info Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: 700; margin-right: 20px;">
                            {{ strtoupper(substr($user->username, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="mb-1" style="font-weight: 700;">{{ $user->username }}</h3>
                            <p class="text-muted mb-0">
                                @if($user->isAdmin())
                                    <span class="badge badge-primary">Administrator</span>
                                @else
                                    <span class="badge badge-secondary">Sales Person</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <a href="{{ route('affiliates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-primary">{{ $totalBookings }}</div>
                <div class="stat-label">Total Bookings</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-success">${{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-info">{{ $uniqueClients }}</div>
                <div class="stat-label">Unique Clients</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-value text-warning">{{ $bookingsByFloorPlan->count() }}</div>
                <div class="stat-label">Floor Plans</div>
            </div>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
            <div class="stat-card">
                <div class="stat-value text-info">${{ number_format($avgBookingValue, 2) }}</div>
                <div class="stat-label">Avg / Booking</div>
            </div>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
            <div class="stat-card">
                <div class="stat-value" style="font-size:1.25rem;">
                    @if($lastBookingAt)
                        {{ \Carbon\Carbon::parse($lastBookingAt)->format('M d, Y') }}
                    @else
                        <span class="text-muted">No bookings</span>
                    @endif
                </div>
                <div class="stat-label">Last Booking</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('affiliates.show', $user->id) }}" class="row g-3">
                <div class="col-md-4">
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
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list mr-2"></i>Affiliate Bookings</h5>
        </div>
        <div class="card-body">
            @forelse($bookings as $booking)
            <div class="booking-card">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->date_book)->format('M d, Y') }}<br>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($booking->date_book)->format('g:i A') }}</small>
                    </div>
                    <div class="col-md-3">
                        <strong>Client:</strong><br>
                        @if($booking->client)
                            {{ $booking->client->company ?? $booking->client->name }}
                        @else
                            Client #{{ $booking->clientid }}
                        @endif
                    </div>
                    <div class="col-md-2">
                        <strong>Floor Plan:</strong><br>
                        @if($booking->floorPlan)
                            {{ $booking->floorPlan->name }}
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="col-md-2">
                        <strong>Booths:</strong><br>
                        {{ $booking->booths()->count() }}
                    </div>
                    <div class="col-md-2 text-right">
                        <strong>Revenue:</strong><br>
                        <span class="text-success font-weight-bold">
                            ${{ number_format($booking->booths()->sum('price') ?? 0, 2) }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted">No bookings found with the current filters.</p>
            </div>
            @endforelse
            
            <!-- Pagination -->
            @if($bookings->hasPages())
            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
