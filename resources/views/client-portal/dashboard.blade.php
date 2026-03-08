<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Client Dashboard - KHB Booth System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --ios-blue: #007AFF;
            --text-main: #1d1d1f;
            --text-muted: #86868b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;
        }

        body {
            background-color: #ffb8d2;
            background-image: 
                radial-gradient(at 0% 0%, #4facfe 0px, transparent 50%),
                radial-gradient(at 100% 0%, #00f2fe 0px, transparent 50%),
                radial-gradient(at 100% 100%, #f093fb 0px, transparent 50%),
                radial-gradient(at 0% 100%, #f5576c 0px, transparent 50%),
                radial-gradient(at 50% 50%, #5ee7df 0px, transparent 50%);
            background-attachment: fixed;
            background-size: 200% 200%;
            animation: gradientMove 15s ease infinite;
            min-height: 100vh;
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
            padding-top: 100px; /* Space for fixed header */
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(40px) saturate(180%);
            -webkit-backdrop-filter: blur(40px) saturate(180%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i { color: var(--ios-blue); }

        .nav-actions { display: flex; gap: 1rem; }

        .btn-ios {
            padding: 0.6rem 1.25rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-ios-outline {
            background: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.8);
            color: var(--text-main);
        }

        .btn-ios-outline:hover { background: rgba(255, 255, 255, 0.8); }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(40px) saturate(180%);
            -webkit-backdrop-filter: blur(40px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 24px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2), inset 0 0 0 1px rgba(255, 255, 255, 0.4);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            padding: 1.5rem;
            border-radius: 24px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 12px 32px rgba(0, 122, 255, 0.3);
            transition: transform 0.3s;
        }

        
        .bg-primary-ios { background: linear-gradient(135deg, #007AFF 0%, #00C7BE 100%); }
        .bg-success-ios { background: linear-gradient(135deg, #34C759 0%, #30B0C7 100%); }
        .bg-info-ios { background: linear-gradient(135deg, #5856D6 0%, #AF52DE 100%); }

        .stat-card h3 { font-size: 2.5rem; font-weight: 800; }
        .stat-card h6 { font-size: 0.85rem; font-weight: 600; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.05em; }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            padding-bottom: 1rem;
        }

        .panel-title { font-size: 1.25rem; font-weight: 700; color: var(--text-main); }

        .booking-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .booking-row:last-child { border-bottom: none; }

        .booking-info h6 { font-size: 1rem; font-weight: 700; margin-bottom: 0.25rem; }
        .booking-info p { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; }

        .btn-view {
            background: var(--ios-blue);
            color: white;
            padding: 0.5rem 1.25rem;
            border-radius: 100px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: 0.2s;
        }

        

        @media (max-width: 768px) {
            .navbar { padding: 0 1rem; }
            .navbar-brand span { display: none; }
            body { padding-top: 80px; }
        }
    
        .stat-card, .glass-card, .btn-ios, .btn-view, .booking-row {
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .stat-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(31, 38, 135, 0.25);
        }

        .btn-ios:hover, .btn-view:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .btn-ios:active, .btn-view:active {
            transform: scale(0.95);
        }

        .booking-row {
            border-radius: 12px;
            padding: 1.25rem 1rem;
            margin: 0 -1rem;
        }

        .booking-row:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(10px);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a class="navbar-brand" href="#">
            <i class="fas fa-building"></i>
            <span>{{ $client->company ?? $client->name }} Portal</span>
        </a>
        <div class="nav-actions">
            <a href="{{ route('client-portal.profile') }}" class="btn-ios btn-ios-outline">
                <i class="fas fa-user-circle"></i>Profile
            </a>
            <form method="POST" action="{{ route('client-portal.logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn-ios btn-ios-outline" style="color: #ff3b30;">
                    <i class="fas fa-sign-out-alt"></i>Logout
                </button>
            </form>
        </div>
    </nav>

    <div class="container">
        <header style="margin-bottom: 3rem;">
            <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;">Dashboard</h1>
            <p style="color: var(--text-muted); font-weight: 500;">Welcome to your secure event workspace.</p>
        </header>

        <div class="stat-grid">
            <div class="stat-card bg-primary-ios">
                <div>
                    <h6>Total Bookings</h6>
                    <h3>{{ $bookings->count() }}</h3>
                </div>
                <i class="fas fa-calendar-check fa-2x"></i>
            </div>
            <div class="stat-card bg-success-ios">
                <div>
                    <h6>Total Payments</h6>
                    <h3>{{ $payments->count() }}</h3>
                </div>
                <i class="fas fa-wallet fa-2x"></i>
            </div>
            <div class="stat-card bg-info-ios">
                <div>
                    <h6>Active Booths</h6>
                    <h3>{{ $client->booths->count() ?? 0 }}</h3>
                </div>
                <i class="fas fa-cube fa-2x"></i>
            </div>
        </div>

        <div class="glass-card">
            <div class="panel-header">
                <h5 class="panel-title"><i class="fas fa-history me-2"></i>Recent Activity</h5>
            </div>
            
            @forelse($bookings as $booking)
            <div class="booking-row">
                <div class="booking-info">
                    <h6>Booking #{{ $booking->id }}</h6>
                    <p>
                        <i class="fas fa-calendar-alt me-1"></i>{{ $booking->date_book ? $booking->date_book->format('M d, Y • H:i') : 'N/A' }}
                        <span style="margin: 0 10px;">•</span>
                        <i class="fas fa-box me-1"></i>{{ $booking->booths()->count() }} Booth(s)
                    </p>
                </div>
                <a href="{{ route('client-portal.booking', $booking->id) }}" class="btn-view">
                    View Details
                </a>
            </div>
            @empty
            <div style="text-align: center; padding: 3rem 0; color: var(--text-muted);">
                <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.3;"></i>
                <p>No activity records found in your stream.</p>
            </div>
            @endforelse
        </div>
    </div>
</body>
</html>

