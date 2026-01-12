@extends('layouts.adminlte')

@section('title', 'View Expense')
@section('page-title', 'View Expense #' . $expense->id)
@section('breadcrumb', 'Finance / Expenses / Show')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-receipt mr-2"></i>Expense Details</h3>
            <div class="card-tools">
                <a href="{{ route('finance.expenses.edit', $expense) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <a href="{{ route('finance.expenses.index') }}" class="btn btn-default btn-sm">
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
                            <td>#{{ $expense->id }}</td>
                        </tr>
                        <tr>
                            <th>Title</th>
                            <td><strong>{{ $expense->title }}</strong></td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td><strong class="text-danger" style="font-size: 1.2rem;">${{ number_format($expense->amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'info',
                                        'paid' => 'success',
                                        'cancelled' => 'secondary'
                                    ];
                                    $color = $statusColors[$expense->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ ucfirst($expense->status) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>
                                @if($expense->category)
                                    <span class="badge badge-info">{{ $expense->category->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Expense Date</th>
                            <td>{{ $expense->expense_date->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px;">Vendor Name</th>
                            <td>{{ $expense->vendor_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Reference Number</th>
                            <td>{{ $expense->reference_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Floor Plan</th>
                            <td>
                                @if($expense->floorPlan)
                                    <a href="{{ route('floor-plans.show', $expense->floorPlan) }}">{{ $expense->floorPlan->name }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Booking</th>
                            <td>
                                @if($expense->booking)
                                    <a href="{{ route('books.show', $expense->booking) }}">#{{ $expense->booking->id }}</a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td>{{ $expense->createdBy->username ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Approved By</th>
                            <td>{{ $expense->approvedBy->username ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $expense->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($expense->description)
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Description</h5>
                    <p class="text-muted">{{ $expense->description }}</p>
                </div>
            </div>
            @endif

            @if($expense->notes)
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Notes</h5>
                    <p class="text-muted">{{ $expense->notes }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
