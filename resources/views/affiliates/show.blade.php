@extends('layouts.admin')

@section('title', 'Affiliate Details - ' . $user->username)
@section('page-title', 'Affiliate Details')
@section('breadcrumb', 'Sales / Affiliates / ' . $user->username)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/affiliates-page.css') }}?v=1.1">
@endpush

@push('body-class', 'ios-dashboard-mode affiliates-page')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div class="affiliates-profile-avatar" aria-hidden="true">{{ strtoupper(substr($user->username, 0, 1)) }}</div>
                <div>
                    <h1 class="mb-1">{{ $user->username }}</h1>
                    <p class="mb-0 text-secondary small">
                        @if($user->isAdmin())
                            <span class="status-badge status-badge-blue">Administrator</span>
                        @else
                            <span class="status-badge status-badge-neutral">Sales person</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="looker-actions">
            <a href="{{ route('affiliates.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to list
            </a>
        </div>
    </header>

    <div class="kpi-wrapper">
        <div class="kpi-card-looker animate-slide-up delay-2">
            <div class="kpi-top">
                <div class="kpi-title">Bookings</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-calendar-check" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($totalBookings) }}</div>
            <div class="kpi-bottom trend-neutral">Attributed bookings</div>
        </div>
        <div class="kpi-card-looker success animate-slide-up delay-3">
            <div class="kpi-top">
                <div class="kpi-title">Revenue</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-dollar-sign" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">${{ number_format($totalRevenue, 0) }}</div>
            <div class="kpi-bottom trend-positive">Booth totals</div>
        </div>
        <div class="kpi-card-looker purple animate-slide-up delay-4">
            <div class="kpi-top">
                <div class="kpi-title">Clients</div>
                <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-users" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($uniqueClients) }}</div>
            <div class="kpi-bottom trend-neutral">Unique clients</div>
        </div>
        <div class="kpi-card-looker warning animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Floor plans</div>
                <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-map" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($bookingsByFloorPlan->count()) }}</div>
            <div class="kpi-bottom trend-warning">With activity</div>
        </div>
        <div class="kpi-card-looker animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Avg / booking</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-chart-bar" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">${{ number_format($avgBookingValue, 0) }}</div>
            <div class="kpi-bottom trend-neutral">Mean revenue</div>
        </div>
        <div class="kpi-card-looker animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Last booking</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-clock" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker fs-5">
                @if($lastBookingAt)
                    {{ \Carbon\Carbon::parse($lastBookingAt)->format('M j, Y') }}
                @else
                    <span class="text-secondary">—</span>
                @endif
            </div>
            <div class="kpi-bottom trend-neutral">Most recent</div>
        </div>
        @if(isset($totalBenefits) && $totalBenefits['total'] > 0)
        <div class="kpi-card-looker success animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Benefits</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-gift" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">${{ number_format($totalBenefits['total'], 0) }}</div>
            <div class="kpi-bottom trend-positive">Estimated total</div>
        </div>
        @endif
    </div>

    @if(isset($totalBenefits) && count($totalBenefits['breakdown']) > 0)
    <div class="canvas-panel mb-4 animate-slide-up delay-5">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-gift" aria-hidden="true"></i> Benefits breakdown</h2>
        </div>
        <div class="looker-table-wrapper">
            <div class="table-responsive">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th scope="col">Benefit</th>
                            <th scope="col">Type</th>
                            <th scope="col">Calculation</th>
                            <th scope="col" class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($totalBenefits['breakdown'] as $item)
                        <tr>
                            <td>
                                <strong>{{ $item['benefit']->name }}</strong>
                                @if($item['benefit']->description)
                                    <br><span class="small text-secondary">{{ Str::limit($item['benefit']->description, 60) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-badge-blue">{{ ucfirst($item['benefit']->type) }}</span>
                            </td>
                            <td>
                                @if($item['benefit']->calculation_method === 'percentage')
                                    {{ number_format($item['benefit']->percentage, 2) }}% of revenue
                                @elseif($item['benefit']->calculation_method === 'fixed_amount')
                                    Fixed amount
                                @else
                                    Tiered structure
                                @endif
                            </td>
                            <td class="text-end"><strong class="text-success">${{ number_format($item['amount'], 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total benefits</th>
                            <th class="text-end text-success">${{ number_format($totalBenefits['total'], 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="filter-bar">
        <form method="GET" action="{{ route('affiliates.show', $user->id) }}" id="affiliateShowFilters">
            <div class="filter-header">
                <h6><i class="fas fa-filter" aria-hidden="true"></i> Booking filters</h6>
            </div>
            <div class="filter-row-primary">
                <div>
                    <label class="form-label" for="show_floor_plan_id">Floor plan</label>
                    <select name="floor_plan_id" id="show_floor_plan_id" class="form-select">
                        <option value="">All floor plans</option>
                        @foreach($floorPlans as $fp)
                            <option value="{{ $fp->id }}" {{ (string) $floorPlanId === (string) $fp->id ? 'selected' : '' }}>
                                {{ $fp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="show_date_from">Date from</label>
                    <input type="date" name="date_from" id="show_date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div>
                    <label class="form-label" for="show_date_to">Date to</label>
                    <input type="date" name="date_to" id="show_date_to" class="form-control" value="{{ $dateTo }}">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-filter" aria-hidden="true"></i> Apply filters
                </button>
                <a href="{{ route('affiliates.show', $user->id) }}" class="action-btn action-btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="canvas-panel animate-slide-up delay-5">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-list" aria-hidden="true"></i> Affiliate bookings</h2>
        </div>
        @if($bookings->isEmpty())
            <div class="text-center py-5 text-secondary">
                <i class="fas fa-inbox fa-2x mb-3 d-block opacity-50" aria-hidden="true"></i>
                <p class="mb-0">No bookings match the current filters.</p>
            </div>
        @else
            <div class="looker-table-wrapper">
                <div class="table-responsive">
                    <table class="looker-table">
                        <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Client</th>
                                <th scope="col">Floor plan</th>
                                <th scope="col" class="text-center">Booths</th>
                                <th scope="col" class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <strong>{{ \Carbon\Carbon::parse($booking->date_book)->format('M j, Y') }}</strong>
                                    <br><span class="small text-secondary">{{ \Carbon\Carbon::parse($booking->date_book)->format('g:i A') }}</span>
                                </td>
                                <td>
                                    @if($booking->client)
                                        {{ $booking->client->company ?? $booking->client->name }}
                                    @else
                                        Client #{{ $booking->clientid }}
                                    @endif
                                </td>
                                <td>
                                    {{ $booking->floorPlan ? $booking->floorPlan->name : '—' }}
                                </td>
                                <td class="text-center">{{ $booking->booths()->count() }}</td>
                                <td class="text-end"><span class="affiliates-revenue">${{ number_format($booking->booths()->sum('price') ?? 0, 2) }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if($bookings->hasPages())
            <div class="affiliates-pagination-footer">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
