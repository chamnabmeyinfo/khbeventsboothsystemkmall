@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col">
        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Booths</h5>
                <h2>{{ $stats['total_booths'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Available</h5>
                <h2>{{ $stats['available_booths'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Reserved</h5>
                <h2>{{ $stats['reserved_booths'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Confirmed</h5>
                <h2>{{ $stats['confirmed_booths'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-dark">
            <div class="card-body">
                <h5 class="card-title">Paid</h5>
                <h2>{{ $stats['paid_booths'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-secondary">
            <div class="card-body">
                <h5 class="card-title">Total Clients</h5>
                <h2>{{ $stats['total_clients'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <h2>{{ $stats['total_users'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Bookings</h5>
                <h2>{{ $stats['total_bookings'] }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Booths</th>
                                <th>Date</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                            <tr>
                                <td>{{ $booking->id }}</td>
                                <td>{{ $booking->client->company ?? 'N/A' }}</td>
                                <td>{{ count($booking->booth_ids ?? []) }} booths</td>
                                <td>{{ $booking->date_book->format('Y-m-d H:i') }}</td>
                                <td>{{ $booking->user->username }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No bookings yet</td>
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
