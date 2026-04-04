@extends('layouts.admin')

@section('title', 'Affiliate Management')
@section('page-title', 'Affiliate Management')
@section('breadcrumb', 'Sales / Affiliates')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/affiliates-page.css') }}?v=1.0">
@endpush

@push('body-class', 'ios-dashboard-mode affiliates-page')

@php
    $usersCollection = $users instanceof \Illuminate\Support\Collection ? $users : collect($users);
    $kpiAffiliates = $usersCollection->count();
    $kpiBookings = $usersCollection->sum(fn ($d) => (int) ($d['total_bookings'] ?? 0));
    $kpiRevenue = $usersCollection->sum(fn ($d) => (float) ($d['total_revenue'] ?? 0));
    $kpiBenefits = $usersCollection->sum(fn ($d) => (float) ($d['total_benefits']['total'] ?? 0));
    $activeFilterCount = 0;
    if (filled($search)) {
        $activeFilterCount++;
    }
    if (filled($userId)) {
        $activeFilterCount++;
    }
    if (filled($floorPlanId)) {
        $activeFilterCount++;
    }
    if (filled($dateFrom)) {
        $activeFilterCount++;
    }
    if (filled($dateTo)) {
        $activeFilterCount++;
    }
@endphp

@section('content')
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Affiliates</h1>
            <p>Track sales performance, link activity, and commission benefits across your team.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('affiliates.benefits.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-gift" aria-hidden="true"></i> Manage benefits
            </a>
            <a href="{{ route('affiliates.export', request()->all()) }}" class="action-btn action-btn-primary">
                <i class="fas fa-file-csv" aria-hidden="true"></i> Export CSV
            </a>
        </div>
    </header>

    <div class="kpi-wrapper">
        <div class="kpi-card-looker animate-slide-up delay-2">
            <div class="kpi-top">
                <div class="kpi-title">Active affiliates</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-user-tie" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($kpiAffiliates) }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-filter" aria-hidden="true"></i> With bookings in this view
            </div>
        </div>
        <div class="kpi-card-looker success animate-slide-up delay-3">
            <div class="kpi-top">
                <div class="kpi-title">Bookings</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-calendar-check" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($kpiBookings) }}</div>
            <div class="kpi-bottom trend-positive">
                <i class="fas fa-fw fa-link" aria-hidden="true"></i> Attributed to affiliates
            </div>
        </div>
        <div class="kpi-card-looker warning animate-slide-up delay-4">
            <div class="kpi-top">
                <div class="kpi-title">Revenue</div>
                <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-dollar-sign" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">${{ number_format($kpiRevenue, 0) }}</div>
            <div class="kpi-bottom trend-warning">
                <i class="fas fa-fw fa-chart-line" aria-hidden="true"></i> Booth totals (filtered)
            </div>
        </div>
        <div class="kpi-card-looker purple animate-slide-up delay-5">
            <div class="kpi-top">
                <div class="kpi-title">Benefits</div>
                <div class="kpi-icon-wrapper purple-icon"><i class="fas fa-hand-holding-usd" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">${{ number_format($kpiBenefits, 0) }}</div>
            <div class="kpi-bottom trend-neutral">
                <i class="fas fa-fw fa-gift" aria-hidden="true"></i> Estimated from rules
            </div>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" action="{{ route('affiliates.index') }}" id="filterForm">
            <div class="filter-header">
                <h6>
                    <i class="fas fa-filter" aria-hidden="true"></i> Filters
                    <span class="filter-badge {{ $activeFilterCount > 0 ? '' : 'd-none' }}" id="affiliatesFilterBadge">{{ $activeFilterCount }} active</span>
                </h6>
            </div>
            <div class="filter-row-primary">
                <div>
                    <label class="form-label" for="affiliates_search">Search</label>
                    <input type="search" name="search" id="affiliates_search" class="form-control" autocomplete="off"
                           placeholder="Username…" value="{{ $search }}">
                </div>
                <div>
                    <label class="form-label" for="affiliates_user_id">Sales person</label>
                    <select name="user_id" id="affiliates_user_id" class="form-select">
                        <option value="">All</option>
                        @foreach($allUsers as $user)
                            <option value="{{ $user->id }}" {{ (string) $userId === (string) $user->id ? 'selected' : '' }}>
                                {{ $user->username }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="affiliates_floor_plan">Floor plan</label>
                    <select name="floor_plan_id" id="affiliates_floor_plan" class="form-select">
                        <option value="">All</option>
                        @foreach($floorPlans as $fp)
                            <option value="{{ $fp->id }}" {{ (string) $floorPlanId === (string) $fp->id ? 'selected' : '' }}>
                                {{ $fp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="affiliates_date_from">Date from</label>
                    <input type="date" name="date_from" id="affiliates_date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div>
                    <label class="form-label" for="affiliates_date_to">Date to</label>
                    <input type="date" name="date_to" id="affiliates_date_to" class="form-control" value="{{ $dateTo }}">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-filter" aria-hidden="true"></i> Apply filters
                </button>
                <a href="{{ route('affiliates.index') }}" class="action-btn action-btn-secondary">Reset all</a>
            </div>
        </form>
    </div>

    <div class="canvas-panel animate-slide-up delay-5">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-table" aria-hidden="true"></i> Performance by sales person</h2>
        </div>
        <div class="looker-table-wrapper">
            <div class="table-responsive">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th scope="col">Sales person</th>
                            <th scope="col" class="text-center">Bookings</th>
                            <th scope="col" class="text-end">Revenue</th>
                            <th scope="col" class="text-center">Clients</th>
                            <th scope="col" class="text-center">Clicks</th>
                            <th scope="col" class="text-center">Conversion</th>
                            <th scope="col" class="text-end">Avg / booking</th>
                            <th scope="col" class="text-end">Benefits</th>
                            <th scope="col" class="text-center">Last activity</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $data)
                        <tr>
                            <td>
                                <div class="avatar-chip">
                                    <div class="avatar-circle" aria-hidden="true">{{ strtoupper(substr($data['user']->username, 0, 1)) }}</div>
                                    <div class="avatar-chip-text">
                                        <span class="avatar-chip-title">{{ $data['user']->username }}</span>
                                        <span class="avatar-chip-sub">
                                            @if($data['user']->isAdmin())
                                                <i class="fas fa-shield-alt me-1" aria-hidden="true"></i>Admin
                                            @else
                                                <i class="fas fa-user-tie me-1" aria-hidden="true"></i>Sales
                                            @endif
                                            @if($data['user']->role)
                                                · {{ $data['user']->role->name }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-badge-blue">{{ number_format($data['total_bookings']) }}</span>
                            </td>
                            <td class="text-end">
                                <span class="affiliates-revenue">${{ number_format($data['total_revenue'], 2) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-badge-purple">{{ number_format($data['unique_clients']) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-badge-orange">{{ number_format($data['total_clicks'] ?? 0) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-badge-green">{{ number_format($data['conversion_rate'] ?? 0, 1) }}%</span>
                            </td>
                            <td class="text-end">
                                <strong>${{ number_format($data['avg_booking_value'], 2) }}</strong>
                            </td>
                            <td class="text-end">
                                <strong class="text-success">${{ number_format($data['total_benefits']['total'] ?? 0, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                @if($data['last_booking_at'])
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($data['last_booking_at'])->format('M d, Y') }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="affiliates-table-actions">
                                    <a href="{{ route('affiliates.show', $data['user']->id) }}"
                                       class="action-btn action-btn-primary"
                                       title="View details"
                                       aria-label="View details for {{ $data['user']->username }}">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>
                                    @if(isset($data['activities']) && $data['activities']->count() > 0)
                                    <button type="button"
                                            class="action-btn action-btn-secondary affiliates-activity-toggle"
                                            onclick="toggleAffiliateActivity({{ $data['user']->id }})"
                                            title="Recent activity"
                                            aria-expanded="false"
                                            aria-controls="affiliate-activity-{{ $data['user']->id }}"
                                            id="affiliate-activity-btn-{{ $data['user']->id }}">
                                        <i class="fas fa-history" aria-hidden="true"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if(isset($data['activities']) && $data['activities']->count() > 0)
                        <tr class="affiliates-activity-row table-row-no-press" id="affiliate-activity-{{ $data['user']->id }}">
                            <td colspan="10" class="affiliates-activity-cell">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="small text-secondary">
                                        <i class="fas fa-history me-1" aria-hidden="true"></i>Recent activity (last 10)
                                    </strong>
                                    <button type="button" class="btn btn-link btn-sm text-secondary p-0 affiliates-activity-toggle" onclick="toggleAffiliateActivity({{ $data['user']->id }})" aria-label="Close activity panel">
                                        <i class="fas fa-times" aria-hidden="true"></i>
                                    </button>
                                </div>
                                @foreach($data['activities']->take(10) as $activity)
                                <div class="affiliates-activity-item">
                                    <div class="affiliates-activity-icon {{ $activity['type'] }}" aria-hidden="true">
                                        @if($activity['type'] === 'booking')
                                            <i class="fas fa-shopping-cart"></i>
                                        @else
                                            <i class="fas fa-mouse-pointer"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div>
                                            @if($activity['type'] === 'booking')
                                                <strong>Booking</strong>
                                                —
                                                @if($activity['booking']->client)
                                                    {{ $activity['booking']->client->company ?? $activity['booking']->client->name }}
                                                @else
                                                    Client #{{ $activity['booking']->clientid }}
                                                @endif
                                                <span class="text-success ms-2">${{ number_format($activity['revenue'], 2) }}</span>
                                            @else
                                                <strong>Link click</strong>
                                                —
                                                @if($activity['click']->floorPlan)
                                                    {{ $activity['click']->floorPlan->name }}
                                                @else
                                                    Floor plan #{{ $activity['click']->floor_plan_id }}
                                                @endif
                                                <span class="text-muted ms-2">{{ $activity['click']->ip_address }}</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($activity['date'])->format('M d, Y · g:i A') }}
                                        </small>
                                    </div>
                                </div>
                                @endforeach
                                <div class="text-center mt-3">
                                    <a href="{{ route('affiliates.show', $data['user']->id) }}" class="action-btn action-btn-secondary">
                                        View all activities <i class="fas fa-arrow-right ms-1" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-secondary">
                                <i class="fas fa-users fa-2x mb-3 d-block opacity-50" aria-hidden="true"></i>
                                <h6 class="text-body">No affiliate data</h6>
                                <p class="mb-0 small">No affiliate bookings match the current filters.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAffiliateActivity(userId) {
        const row = document.getElementById('affiliate-activity-' + userId);
        const btn = document.getElementById('affiliate-activity-btn-' + userId);
        if (!row) return;
        row.classList.toggle('is-open');
        if (btn) {
            const open = row.classList.contains('is-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
    }
</script>
@endpush
