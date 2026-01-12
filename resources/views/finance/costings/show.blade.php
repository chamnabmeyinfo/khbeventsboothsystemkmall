@extends('layouts.adminlte')

@section('title', 'View Costing')
@section('page-title', 'View Costing #' . $costing->id)
@section('breadcrumb', 'Finance / Costings / Show')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calculator mr-2"></i>Costing Details</h3>
            <div class="card-tools">
                <a href="{{ route('finance.costings.edit', $costing) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <a href="{{ route('finance.costings.index') }}" class="btn btn-default btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">ID</th>
                            <td>#{{ $costing->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td><strong>{{ $costing->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Estimated Cost</th>
                            <td><strong class="text-info" style="font-size: 1.2rem;">${{ number_format($costing->estimated_cost ?? 0, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Actual Cost</th>
                            <td><strong class="text-primary" style="font-size: 1.2rem;">${{ number_format($costing->actual_cost ?? 0, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Variance</th>
                            <td>
                                @php
                                    $variance = ($costing->actual_cost ?? 0) - ($costing->estimated_cost ?? 0);
                                @endphp
                                <strong class="{{ $variance >= 0 ? 'text-danger' : 'text-success' }}" style="font-size: 1.2rem;">
                                    ${{ number_format($variance, 2) }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $statusColors = ['draft' => 'secondary', 'approved' => 'info', 'in_progress' => 'warning', 'completed' => 'success', 'cancelled' => 'danger'];
                                    $color = $statusColors[$costing->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $costing->status)) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Costing Date</th>
                            <td>{{ \Carbon\Carbon::parse($costing->costing_date)->format('M d, Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Floor Plan</th>
                            <td>
                                @if($costing->floorPlan)
                                    <a href="{{ route('floor-plans.show', $costing->floorPlan) }}">{{ $costing->floorPlan->name }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Booking</th>
                            <td>
                                @if($costing->booking)
                                    <a href="{{ route('books.show', $costing->booking) }}">#{{ $costing->booking->id }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td>{{ $costing->createdBy->username ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Approved By</th>
                            <td>{{ $costing->approvedBy->username ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $costing->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $costing->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($costing->description)
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Description</h5>
                    <p class="text-muted">{{ $costing->description }}</p>
                </div>
            </div>
            @endif

            @if($costing->notes)
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Notes</h5>
                    <p class="text-muted">{{ $costing->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
