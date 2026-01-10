@extends('layouts.adminlte')

@section('title', 'Payments')
@section('page-title', 'Payment Management')
@section('breadcrumb', 'Payments')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-money-bill-wave mr-2"></i>Payment Records</h3>
            <div class="card-tools">
                <a href="{{ route('payments.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i>Record Payment
                </a>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->client->company ?? 'N/A' }}</td>
                        <td><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>
                            <span class="badge badge-{{ $payment->status == 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d') : 'N/A' }}</td>
                        <td>
                            <a href="{{ route('payments.invoice', $payment->id) }}" class="btn btn-sm btn-info" target="_blank">
                                <i class="fas fa-file-invoice"></i> Invoice
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No payments found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
