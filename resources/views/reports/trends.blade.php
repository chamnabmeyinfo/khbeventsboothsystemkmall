@extends('layouts.adminlte')

@section('title', 'Booking Trends')
@section('page-title', 'Booking Trends')
@section('breadcrumb', 'Reports / Booking Trends')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.css">
@endpush

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Booking Trends (Last {{ $days }} Days)</h3>
            <div class="card-tools">
                <form method="GET" class="d-inline">
                    <select name="days" onchange="this.form.submit()" class="form-control form-control-sm">
                        <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 days</option>
                        <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 days</option>
                        <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 days</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="card-body">
            <canvas id="trendsChart" height="100"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    const trends = @json($trends);
    const ctx = document.getElementById('trendsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: trends.map(t => t.date),
            datasets: [{
                label: 'Bookings',
                data: trends.map(t => t.bookings),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
            }, {
                label: 'Confirmed',
                data: trends.map(t => t.confirmed),
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
            }, {
                label: 'Paid',
                data: trends.map(t => t.paid),
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
