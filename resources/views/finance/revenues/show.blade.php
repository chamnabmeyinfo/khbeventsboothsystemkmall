@extends('layouts.adminlte')

@section('title', 'View Revenue')
@section('page-title', 'View Revenue #' . $revenue->id)
@section('breadcrumb', 'Finance / Revenues / Show')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-arrow-up mr-2"></i>Revenue Details</h3>
            <div class="card-tools">
                <a href="{{ route('finance.revenues.edit', $revenue) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <a href="{{ route('finance.revenues.index') }}" class="btn btn-default btn-sm">
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
                            <td>#{{ $revenue->id }}</td>
                        </tr>
                        <tr>
                            <th>Title</th>
                            <td><strong>{{ $revenue->title }}</strong></td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td><strong class="text-success" style="font-size: 1.2rem;">${{ number_format($revenue->amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'received' => 'success',
                                        'cancelled' => 'secondary'
                                    ];
                                    $color = $statusColors[$revenue->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ ucfirst($revenue->status) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>
                                @if($revenue->category)
                                    <span class="badge badge-info">{{ $revenue->category->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Revenue Date</th>
                            <td>{{ \Carbon\Carbon::parse($revenue->revenue_date)->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $revenue->payment_method)) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Client</th>
                            <td>
                                @if($revenue->client)
                                    <a href="{{ route('clients.show', $revenue->client) }}">{{ $revenue->client->company ?? $revenue->client->name }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Reference Number</th>
                            <td>{{ $revenue->reference_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Floor Plan</th>
                            <td>
                                @if($revenue->floorPlan)
                                    <a href="{{ route('floor-plans.show', $revenue->floorPlan) }}">{{ $revenue->floorPlan->name }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Booking</th>
                            <td>
                                @if($revenue->booking)
                                    <a href="{{ route('books.show', $revenue->booking) }}">#{{ $revenue->booking->id }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td>{{ $revenue->createdBy->username ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $revenue->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $revenue->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($revenue->description)
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Description</h5>
                    <p class="text-muted">{{ $revenue->description }}</p>
                </div>
            </div>
            @endif

            @if($revenue->notes)
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Notes</h5>
                    <p class="text-muted">{{ $revenue->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
