@extends('layouts.adminlte')

@section('title', 'View Affiliate Benefit')
@section('page-title', 'Benefit Details')
@section('breadcrumb', 'Sales / Affiliates / Benefits / View')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-gift mr-2"></i>{{ $benefit->name }}</h5>
                <div>
                    <a href="{{ route('affiliates.benefits.edit', $benefit->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </a>
                    <a href="{{ route('affiliates.benefits.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left mr-1"></i>Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Type:</th>
                            <td>
                                <span class="badge badge-primary">{{ ucfirst($benefit->type) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Calculation Method:</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $benefit->calculation_method)) }}</td>
                        </tr>
                        @if($benefit->calculation_method === 'percentage')
                        <tr>
                            <th>Percentage:</th>
                            <td><strong>{{ number_format($benefit->percentage, 2) }}%</strong></td>
                        </tr>
                        @elseif($benefit->calculation_method === 'fixed_amount')
                        <tr>
                            <th>Fixed Amount:</th>
                            <td><strong>${{ number_format($benefit->fixed_amount, 2) }}</strong></td>
                        </tr>
                        @elseif($benefit->tier_structure)
                        <tr>
                            <th>Tier Structure:</th>
                            <td>
                                <pre style="background: #f8f9fa; padding: 0.5rem; border-radius: 4px; font-size: 0.875rem;">{{ json_encode($benefit->tier_structure, JSON_PRETTY_PRINT) }}</pre>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Priority:</th>
                            <td>{{ $benefit->priority }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($benefit->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        @if($benefit->target_revenue || $benefit->target_bookings || $benefit->target_clients)
                        <tr>
                            <th width="40%">Targets:</th>
                            <td>
                                @if($benefit->target_revenue)
                                    <div>Revenue: ${{ number_format($benefit->target_revenue, 2) }}</div>
                                @endif
                                @if($benefit->target_bookings)
                                    <div>Bookings: {{ $benefit->target_bookings }}</div>
                                @endif
                                @if($benefit->target_clients)
                                    <div>Clients: {{ $benefit->target_clients }}</div>
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if($benefit->min_revenue || $benefit->max_benefit)
                        <tr>
                            <th>Conditions:</th>
                            <td>
                                @if($benefit->min_revenue)
                                    <div>Min Revenue: ${{ number_format($benefit->min_revenue, 2) }}</div>
                                @endif
                                @if($benefit->max_benefit)
                                    <div>Max Benefit: ${{ number_format($benefit->max_benefit, 2) }}</div>
                                @endif
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Applies To:</th>
                            <td>
                                @if($benefit->user)
                                    User: <strong>{{ $benefit->user->username }}</strong><br>
                                @endif
                                @if($benefit->floorPlan)
                                    Floor Plan: <strong>{{ $benefit->floorPlan->name }}</strong><br>
                                @endif
                                @if(!$benefit->user && !$benefit->floorPlan)
                                    <span class="text-muted">All Users & Floor Plans</span>
                                @endif
                            </td>
                        </tr>
                        @if($benefit->start_date || $benefit->end_date)
                        <tr>
                            <th>Date Range:</th>
                            <td>
                                @if($benefit->start_date)
                                    Start: {{ $benefit->start_date->format('M d, Y') }}<br>
                                @endif
                                @if($benefit->end_date)
                                    End: {{ $benefit->end_date->format('M d, Y') }}
                                @endif
                            </td>
                        </tr>
                        @endif
                        @if($benefit->description)
                        <tr>
                            <th>Description:</th>
                            <td>{{ $benefit->description }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
