@extends('layouts.adminlte')

@section('title', 'Payments')
@section('page-title', 'Payment Management')
@section('breadcrumb', 'Finance / Payments')

@push('styles')
<style>
    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .kpi-card:hover::before {
        opacity: 1;
    }

    .kpi-card.primary::before { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success::before { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .kpi-card.warning::before { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .kpi-card.danger::before { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); }

    .kpi-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 16px;
    }

    .kpi-card.primary .kpi-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success .kpi-icon { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .kpi-card.warning .kpi-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .kpi-card.danger .kpi-icon { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); }

    .kpi-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 8px 0;
        line-height: 1;
    }

    .kpi-label {
        font-size: 0.875rem;
        color: #718096;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-bar {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        padding: 24px;
        margin-bottom: 24px;
    }

    .table-row-hover {
        transition: all 0.2s;
    }

    .table-row-hover:hover {
        background-color: #f8f9fc;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Payments</li>
        </ol>
    </nav>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="kpi-label">Total Revenue</div>
                    <div class="kpi-value">${{ number_format($stats['total_amount'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="kpi-label">Total Payments</div>
                    <div class="kpi-value">{{ number_format($stats['total_payments'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card warning">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="kpi-label">Pending</div>
                    <div class="kpi-value">{{ number_format($stats['pending_payments'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card kpi-card danger">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="kpi-label">Failed</div>
                    <div class="kpi-value">{{ number_format($stats['failed_payments'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <a href="{{ route('finance.payments.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i>Record Payment
                        </a>
                        <button type="button" class="btn btn-success" onclick="exportPayments()">
                            <i class="fas fa-file-csv mr-1"></i>Export CSV
                        </button>
                        <button type="button" class="btn btn-info" onclick="refreshPage()">
                            <i class="fas fa-sync-alt mr-1"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="filter-bar">
        <form method="GET" action="{{ route('finance.payments.index') }}" id="filterForm">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label><i class="fas fa-search mr-1"></i>Search</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Transaction ID, amount, client..." 
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-toggle-on mr-1"></i>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-credit-card mr-1"></i>Method</label>
                    <select name="payment_method" class="form-control">
                        <option value="">All Methods</option>
                        @foreach($paymentMethods ?? [] as $method)
                            <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $method)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-calendar-alt mr-1"></i>Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label><i class="fas fa-calendar-check mr-1"></i>Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 mb-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="{{ route('finance.payments.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </a>
                    @if(request()->hasAny(['search', 'status', 'payment_method', 'date_from', 'date_to']))
                    <span class="badge badge-info ml-2">
                        {{ $payments->total() }} result(s) found
                    </span>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-2"></i>Payment Records</h3>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $payments->total() }} Total</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Client</th>
                            <th style="width: 150px;">Amount</th>
                            <th style="width: 120px;">Method</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 150px;">Date</th>
                            <th style="width: 120px;">User</th>
                            <th style="width: 150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr class="table-row-hover">
                            <td>
                                <strong class="text-primary">#{{ $payment->id }}</strong>
                            </td>
                            <td>
                                @if($payment->client)
                                <div class="d-flex align-items-center">
                                    <div class="mr-2">
                                        <x-avatar 
                                            :avatar="$payment->client->avatar" 
                                            :name="$payment->client->name" 
                                            :size="'xs'" 
                                            :type="'client'"
                                            :shape="'circle'"
                                        />
                                    </div>
                                    <div>
                                        <a href="{{ route('clients.show', $payment->client) }}" class="text-primary">
                                            <strong>{{ $payment->client->company ?? 'N/A' }}</strong>
                                        </a>
                                        @if($payment->client->name && $payment->client->company)
                                        <br><small class="text-muted">{{ $payment->client->name }}</small>
                                        @endif
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-success" style="font-size: 1.1rem;">
                                    ${{ number_format($payment->amount, 2) }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    <i class="fas fa-{{ $payment->payment_method == 'cash' ? 'money-bill-wave' : ($payment->payment_method == 'bank_transfer' ? 'university' : ($payment->payment_method == 'online' ? 'credit-card' : 'file-invoice')) }} mr-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        'refunded' => 'secondary'
                                    ];
                                    $statusIcons = [
                                        'completed' => 'check-circle',
                                        'pending' => 'clock',
                                        'failed' => 'times-circle',
                                        'refunded' => 'undo'
                                    ];
                                    $color = $statusColors[$payment->status] ?? 'secondary';
                                    $icon = $statusIcons[$payment->status] ?? 'circle';
                                @endphp
                                <span class="badge badge-{{ $color }}">
                                    <i class="fas fa-{{ $icon }} mr-1"></i>
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                @if($payment->paid_at)
                                    <div>
                                        <strong>{{ $payment->paid_at->format('M d, Y') }}</strong>
                                        <br><small class="text-muted">{{ $payment->paid_at->format('H:i') }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->user)
                                    <span class="badge badge-secondary">{{ $payment->user->username }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('finance.payments.invoice', $payment->id) }}" 
                                       class="btn btn-info" 
                                       target="_blank"
                                       title="View Invoice">
                                        <i class="fas fa-file-invoice"></i> Invoice
                                    </a>
                                    @if($payment->booking_id)
                                    <a href="{{ route('books.show', $payment->booking_id) }}" 
                                       class="btn btn-primary"
                                       title="View Booking">
                                        <i class="fas fa-calendar-check"></i> Booking
                                    </a>
                                    @endif
                                    @if($payment->client_id)
                                    <a href="{{ route('clients.show', $payment->client_id) }}" 
                                       class="btn btn-success"
                                       title="View Client">
                                        <i class="fas fa-user"></i> Client
                                    </a>
                                    @endif
                                    @if($payment->status === \App\Models\Payment::STATUS_COMPLETED)
                                    <button type="button" 
                                            class="btn btn-warning" 
                                            onclick="refundPayment({{ $payment->id }}, '{{ number_format($payment->amount, 2) }}')"
                                            title="Refund Payment">
                                        <i class="fas fa-undo"></i> Refund
                                    </button>
                                    @endif
                                    @if(in_array($payment->status, [\App\Models\Payment::STATUS_COMPLETED, \App\Models\Payment::STATUS_PENDING]))
                                    <button type="button" 
                                            class="btn btn-danger" 
                                            onclick="voidPayment({{ $payment->id }})"
                                            title="Void Payment">
                                        <i class="fas fa-ban"></i> Void
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-money-bill-wave-slash fa-3x mb-3"></i>
                                    <p class="mb-0">No payments found</p>
                                    <a href="{{ route('finance.payments.create') }}" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-plus mr-1"></i>Record First Payment
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($payments, 'hasPages') && $payments->hasPages())
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="text-muted">
                        @if($payments->firstItem())
                        Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} payments
                        @else
                        {{ $payments->total() }} payment(s) total
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="float-right">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportPayments() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = '{{ route("export.bookings") }}?' + params.toString();
}

function refreshPage() {
    showLoading();
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Refund Payment
function refundPayment(paymentId, amount) {
    Swal.fire({
        title: 'Refund Payment?',
        html: `Are you sure you want to refund payment #${paymentId}?<br><br><strong>Amount: $${amount}</strong><br><br>This will:<br>• Create a refund payment entry<br>• Revert booth status to confirmed<br>• Update payment status to refunded`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, refund it!',
        cancelButtonText: 'Cancel',
        input: 'textarea',
        inputPlaceholder: 'Optional: Add refund reason...',
        inputAttributes: {
            'aria-label': 'Refund reason'
        },
        showLoaderOnConfirm: true,
        preConfirm: (notes) => {
            return fetch(`/finance/payments/${paymentId}/refund`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    notes: notes || ''
                })
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed || result.value) {
            if (result.value && result.value.success !== undefined) {
                Swal.fire('Refunded!', result.value.message || 'Payment has been refunded.', 'success')
                    .then(() => location.reload());
            } else {
                // Redirect handled
                location.reload();
            }
        }
    });
}

// Void Payment
function voidPayment(paymentId) {
    Swal.fire({
        title: 'Void Payment?',
        html: `Are you sure you want to void payment #${paymentId}?<br><br>This will:<br>• Mark payment as failed/voided<br>• Revert booth status if payment was completed<br><br><strong>This action cannot be undone!</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, void it!',
        cancelButtonText: 'Cancel',
        input: 'textarea',
        inputPlaceholder: 'Optional: Add void reason...',
        inputAttributes: {
            'aria-label': 'Void reason'
        },
        showLoaderOnConfirm: true,
        preConfirm: (notes) => {
            return fetch(`/finance/payments/${paymentId}/void`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    notes: notes || ''
                })
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed || result.value) {
            if (result.value && result.value.success !== undefined) {
                Swal.fire('Voided!', result.value.message || 'Payment has been voided.', 'success')
                    .then(() => location.reload());
            } else {
                // Redirect handled
                location.reload();
            }
        }
    });
}
</script>
@endpush


