<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Client Dashboard - KHB Booth System</title>
    <link href="{{ asset('vendor/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-building me-2"></i>{{ $client->company ?? $client->name }}
            </a>
            <div>
                <a href="{{ route('client-portal.profile') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-user me-1"></i>Profile
                </a>
                <form method="POST" action="{{ route('client-portal.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-white-50">Total Bookings</h6>
                                <h3>{{ $bookings->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-white-50">Total Payments</h6>
                                <h3>{{ $payments->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-money-bill-wave fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-white-50">Total Booths</h6>
                                <h3>{{ $client->booths->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-cube fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Recent Bookings</h5>
            </div>
            <div class="card-body">
                @forelse($bookings as $booking)
                <div class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6>Booking #{{ $booking->id }}</h6>
                            <p class="text-muted mb-1">
                                <i class="fas fa-calendar me-1"></i>{{ $booking->date_book->format('Y-m-d H:i') }}
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-cube me-1"></i>{{ $booking->booths()->count() }} Booth(s)
                            </p>
                        </div>
                        <a href="{{ route('client-portal.booking', $booking->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-1"></i>View Details
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-4">No bookings found</p>
                @endforelse
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/bootstrap5/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>

