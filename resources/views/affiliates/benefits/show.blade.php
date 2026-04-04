@extends('layouts.admin')

@section('title', 'View Affiliate Benefit')
@section('page-title', 'Benefit Details')
@section('breadcrumb', 'Sales / Affiliates / Benefits / View')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/affiliates-page.css') }}?v=1.1">
@endpush

@push('body-class', 'ios-dashboard-mode affiliates-page')

@php
    $typeClass = match ($benefit->type) {
        'commission' => 'status-badge-blue',
        'bonus' => 'status-badge-green',
        'incentive' => 'status-badge-purple',
        'reward' => 'status-badge-orange',
        default => 'status-badge-neutral',
    };
@endphp

@section('content')
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>{{ $benefit->name }}</h1>
            <p class="mb-0">
                <span class="status-badge {{ $typeClass }}">{{ ucfirst($benefit->type) }}</span>
                @if($benefit->is_active)
                    <span class="status-badge status-badge-green ms-1">Active</span>
                @else
                    <span class="status-badge status-badge-neutral ms-1">Inactive</span>
                @endif
            </p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('affiliates.benefits.edit', $benefit->id) }}" class="action-btn action-btn-primary">
                <i class="fas fa-edit" aria-hidden="true"></i> Edit
            </a>
            <a href="{{ route('affiliates.benefits.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to list
            </a>
        </div>
    </header>

    <div class="data-canvas-grid">
        <div class="col-span-6 canvas-panel animate-slide-up delay-2">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-calculator" aria-hidden="true"></i> Calculation</h2>
            </div>
            <dl class="row mb-0 small">
                <dt class="col-sm-5 text-secondary">Method</dt>
                <dd class="col-sm-7">{{ ucfirst(str_replace('_', ' ', $benefit->calculation_method)) }}</dd>

                @if($benefit->calculation_method === 'percentage')
                    <dt class="col-sm-5 text-secondary">Percentage</dt>
                    <dd class="col-sm-7"><strong>{{ number_format($benefit->percentage, 2) }}%</strong></dd>
                @elseif($benefit->calculation_method === 'fixed_amount')
                    <dt class="col-sm-5 text-secondary">Fixed amount</dt>
                    <dd class="col-sm-7"><strong>${{ number_format($benefit->fixed_amount, 2) }}</strong></dd>
                @elseif($benefit->tier_structure)
                    <dt class="col-sm-5 text-secondary align-self-start pt-1">Tier structure</dt>
                    <dd class="col-sm-7">
                        <pre class="affiliates-json-pre mb-0">{{ json_encode($benefit->tier_structure, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </dd>
                @endif

                <dt class="col-sm-5 text-secondary">Priority</dt>
                <dd class="col-sm-7">{{ $benefit->priority }}</dd>
            </dl>
        </div>

        <div class="col-span-6 canvas-panel animate-slide-up delay-3">
            <div class="panel-header">
                <h2 class="panel-title"><i class="fas fa-bullseye" aria-hidden="true"></i> Targets &amp; scope</h2>
            </div>
            <dl class="row mb-0 small">
                @if($benefit->target_revenue || $benefit->target_bookings || $benefit->target_clients)
                    <dt class="col-sm-5 text-secondary">Targets</dt>
                    <dd class="col-sm-7">
                        @if($benefit->target_revenue)
                            <div>Revenue: ${{ number_format($benefit->target_revenue, 2) }}</div>
                        @endif
                        @if($benefit->target_bookings)
                            <div>Bookings: {{ $benefit->target_bookings }}</div>
                        @endif
                        @if($benefit->target_clients)
                            <div>Clients: {{ $benefit->target_clients }}</div>
                        @endif
                    </dd>
                @endif
                @if($benefit->min_revenue || $benefit->max_benefit)
                    <dt class="col-sm-5 text-secondary">Conditions</dt>
                    <dd class="col-sm-7">
                        @if($benefit->min_revenue)
                            <div>Min revenue: ${{ number_format($benefit->min_revenue, 2) }}</div>
                        @endif
                        @if($benefit->max_benefit)
                            <div>Max benefit: ${{ number_format($benefit->max_benefit, 2) }}</div>
                        @endif
                    </dd>
                @endif
                <dt class="col-sm-5 text-secondary">Applies to</dt>
                <dd class="col-sm-7">
                    @if($benefit->user)
                        User: <strong>{{ $benefit->user->username }}</strong><br>
                    @endif
                    @if($benefit->floorPlan)
                        Floor plan: <strong>{{ $benefit->floorPlan->name }}</strong><br>
                    @endif
                    @if(!$benefit->user && !$benefit->floorPlan)
                        <span class="text-secondary">All users &amp; floor plans</span>
                    @endif
                </dd>
                @if($benefit->start_date || $benefit->end_date)
                    <dt class="col-sm-5 text-secondary">Date range</dt>
                    <dd class="col-sm-7">
                        @if($benefit->start_date)
                            Start: {{ $benefit->start_date->format('M j, Y') }}<br>
                        @endif
                        @if($benefit->end_date)
                            End: {{ $benefit->end_date->format('M j, Y') }}
                        @endif
                    </dd>
                @endif
                @if($benefit->description)
                    <dt class="col-sm-5 text-secondary align-self-start pt-1">Description</dt>
                    <dd class="col-sm-7">{{ $benefit->description }}</dd>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection
