<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $payment->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
        .invoice-header {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-2"></i>Print Invoice
            </button>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Payments
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="invoice-header">
                    <div class="row">
                        <div class="col-md-6">
                            <h3>INVOICE</h3>
                            <p class="mb-0"><strong>Invoice #:</strong> {{ $payment->id }}</p>
                            <p class="mb-0"><strong>Date:</strong> {{ $payment->paid_at ? $payment->paid_at->format('Y-m-d') : $payment->created_at->format('Y-m-d') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h5>KHB Booth System</h5>
                            <p class="mb-0">Payment Receipt</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Bill To:</h6>
                        <p class="mb-0"><strong>{{ $payment->client->company ?? $payment->client->name }}</strong></p>
                        <p class="mb-0">{{ $payment->client->name }}</p>
                        @if($payment->client->phone_number)
                        <p class="mb-0">{{ $payment->client->phone_number }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6>Payment Details:</h6>
                        <p class="mb-0"><strong>Booking ID:</strong> #{{ $payment->booking_id }}</p>
                        <p class="mb-0"><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                        <p class="mb-0"><strong>Status:</strong> 
                            <span class="badge bg-{{ $payment->status == 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Booth Booking Payment</td>
                            <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td class="text-end"><strong>${{ number_format($payment->amount, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>

                @if($payment->notes)
                <div class="mt-3">
                    <strong>Notes:</strong>
                    <p>{{ $payment->notes }}</p>
                </div>
                @endif

                <div class="mt-4 text-center">
                    <p class="text-muted">Thank you for your payment!</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
