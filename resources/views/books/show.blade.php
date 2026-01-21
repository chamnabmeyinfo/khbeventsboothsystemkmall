@extends('layouts.adminlte')

@section('title', 'Booking Details')
@section('page-title', 'Booking Details')
@section('breadcrumb', 'Bookings / View')

@push('styles')
<style>
    .detail-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .detail-card:hover {
        transform: translateX(5px);
    }
    .detail-card.primary { border-left-color: #007bff; }
    .detail-card.success { border-left-color: #28a745; }
    .detail-card.info { border-left-color: #17a2b8; }
    .info-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .booth-card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    .booth-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('books.index') }}">Bookings</a></li>
            <li class="breadcrumb-item active">Booking #{{ $book->id }}</li>
        </ol>
    </nav>

    <!-- Header Actions -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="btn-group" role="group">
                <a href="{{ route('books.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Bookings
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">
                    <i class="fas fa-edit mr-1"></i>Edit Booking
                </a>
                @endif
                @if($book->client)
                <a href="{{ route('clients.show', $book->client) }}" class="btn btn-info">
                    <i class="fas fa-user mr-1"></i>View Client: {{ $book->client->company ?? $book->client->name }}
                </a>
                @endif
                @if(!isset($payment) || !$payment)
                <a href="{{ route('finance.payments.create', ['booking_id' => $book->id]) }}" class="btn btn-success">
                    <i class="fas fa-money-bill-wave mr-1"></i>Record Payment
                </a>
                @else
                <a href="{{ route('finance.payments.index', ['search' => '#'.$book->id]) }}" class="btn btn-info">
                    <i class="fas fa-receipt mr-1"></i>View Payment
                </a>
                @endif
            </div>
        </div>
        <div class="col-md-4 text-right">
            @if(auth()->user()->isAdmin())
            <button type="button" class="btn btn-danger" onclick="deleteBooking({{ $book->id }})">
                <i class="fas fa-trash mr-1"></i>Delete Booking
            </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Booking Information -->
        <div class="col-md-6 mb-4">
            <div class="card detail-card primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-check mr-2"></i>Booking Information</h5>
                </div>
                <div class="card-body">
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-hashtag mr-2"></i>Booking ID:</span>
                            <strong class="text-primary">#{{ $book->id }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-calendar mr-2"></i>Booking Date:</span>
                            <strong>{{ $book->date_book->format('F d, Y') }}</strong>
                        </div>
                        <small class="text-muted ml-4">{{ $book->date_book->format('h:i A') }}</small>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-tag mr-2"></i>Type:</span>
                            <span class="badge booking-type-badge 
                                {{ $book->type == 1 ? 'badge-primary' : ($book->type == 2 ? 'badge-warning' : 'badge-danger') }}">
                                @if($book->type == 1) Regular
                                @elseif($book->type == 2) Special
                                @elseif($book->type == 3) Temporary
                                @else {{ $book->type }}
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-user mr-2"></i>Booked By:</span>
                            <strong>{{ $book->user->username ?? 'System' }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-cube mr-2"></i>Total Booths:</span>
                            <strong class="text-info">{{ count($booths) }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-dollar-sign mr-2"></i>Total Amount:</span>
                            <strong class="text-success">${{ number_format($book->total_amount ?? $booths->sum('price') ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-check-circle mr-2"></i>Paid Amount:</span>
                            <strong class="text-info">${{ number_format($book->paid_amount ?? 0, 2) }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-balance-scale mr-2"></i>Balance:</span>
                            <strong class="{{ ($book->balance_amount ?? 0) > 0 ? 'text-warning' : 'text-success' }}">
                                ${{ number_format($book->balance_amount ?? ($book->total_amount ?? 0) - ($book->paid_amount ?? 0), 2) }}
                            </strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-info-circle mr-2"></i>Status:</span>
                            @php
                                try {
                                    $statusSetting = $book->statusSetting ?? \App\Models\BookingStatusSetting::getByCode($book->status ?? 1);
                                    $statusColor = $statusSetting ? $statusSetting->status_color : '#6c757d';
                                    $statusTextColor = $statusSetting && $statusSetting->text_color ? $statusSetting->text_color : '#ffffff';
                                    $statusName = $statusSetting ? $statusSetting->status_name : 'Pending';
                                } catch (\Exception $e) {
                                    $statusColor = '#6c757d';
                                    $statusTextColor = '#ffffff';
                                    $statusName = 'Pending';
                                }
                            @endphp
                            <span class="badge" style="background-color: {{ $statusColor }}; color: {{ $statusTextColor }};">
                                {{ $statusName }}
                            </span>
                        </div>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-edit mr-2"></i>Change Status:</span>
                            <select id="bookingStatusSelect" class="form-control form-control-sm" style="max-width: 200px; display: inline-block;">
                                @if($statusSettings && $statusSettings->count() > 0)
                                    @foreach($statusSettings as $status)
                                        <option value="{{ $status->status_code }}" 
                                            {{ ($book->status ?? 1) == $status->status_code ? 'selected' : '' }}
                                            data-color="{{ $status->status_color }}"
                                            data-text-color="{{ $status->text_color }}">
                                            {{ $status->status_name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="1" {{ ($book->status ?? 1) == 1 ? 'selected' : '' }}>Pending</option>
                                    <option value="2" {{ ($book->status ?? 1) == 2 ? 'selected' : '' }}>Confirmed</option>
                                    <option value="3" {{ ($book->status ?? 1) == 3 ? 'selected' : '' }}>Reserved</option>
                                    <option value="4" {{ ($book->status ?? 1) == 4 ? 'selected' : '' }}>Paid</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Client Information -->
        <div class="col-md-6 mb-4">
            <div class="card detail-card success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building mr-2"></i>Client Information</h5>
                    @if($book->client)
                    <a href="{{ route('clients.show', $book->client) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-external-link-alt mr-1"></i>View Profile
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($book->client)
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-building mr-2"></i>Company:</span>
                            <strong>{{ $book->client->company ?? 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-user mr-2"></i>Contact Name:</span>
                            <strong>{{ $book->client->name }}</strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-briefcase mr-2"></i>Position:</span>
                            <span>{{ $book->client->position ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-phone mr-2"></i>Phone:</span>
                            <a href="tel:{{ $book->client->phone_number }}" class="text-primary">
                                {{ $book->client->phone_number ?? 'N/A' }}
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('clients.show', $book->client) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt mr-1"></i>View Client Details
                        </a>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-user-slash fa-2x mb-2"></i>
                        <p>No client information available</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Booths in Booking -->
    <div class="row">
        <div class="col-12">
            <div class="card detail-card info">
                <div class="card-header bg-info text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-cube mr-2"></i>Booths in Booking ({{ count($booths) }})
                            </h5>
                            <span class="badge badge-light">{{ ($booths->sum('price') ?? 0) > 0 ? '$' . number_format($booths->sum('price'), 2) : 'Free' }}</span>
                        </div>
                </div>
                <div class="card-body">
                    @if(count($booths) > 0)
                        <div class="row">
                            @foreach($booths as $booth)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="booth-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">
                                                <a href="{{ route('booths.show', $booth) }}" class="text-primary">
                                                    <i class="fas fa-cube mr-1"></i>{{ $booth->booth_number }}
                                                </a>
                                            </h6>
                                            @if($booth->category)
                                            <small class="text-muted">
                                                <i class="fas fa-folder mr-1"></i>{{ $booth->category->name }}
                                            </small>
                                            @endif
                                        </div>
                                        <span class="badge badge-{{ $booth->getStatusColor() }}">
                                            {{ $booth->getStatusLabel() }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">
                                            <i class="fas fa-dollar-sign mr-1"></i>Price:
                                        </span>
                                        <strong class="text-success">${{ number_format($booth->price, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-cube fa-3x mb-3"></i>
                        <p>No booths in this booking</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card detail-card success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-money-bill-wave mr-2"></i>Payment Information
                    </h5>
                    <a href="{{ route('finance.payments.create', ['booking_id' => $book->id]) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-plus mr-1"></i>Record Payment
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted mb-1">Total Amount</div>
                                <h4 class="text-success mb-0">${{ number_format($book->total_amount ?? 0, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted mb-1">Paid Amount</div>
                                <h4 class="text-info mb-0">${{ number_format($book->paid_amount ?? 0, 2) }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="text-muted mb-1">Balance</div>
                                <h4 class="{{ ($book->balance_amount ?? 0) > 0 ? 'text-warning' : 'text-success' }} mb-0">
                                    ${{ number_format($book->balance_amount ?? 0, 2) }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    @if($payments && $payments->count() > 0)
                    <h6 class="mb-3"><i class="fas fa-history mr-2"></i>Payment History</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                    <th>Recorded By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->paid_at->format('M d, Y h:i A') }}</td>
                                    <td class="text-success font-weight-bold">${{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->user->username ?? 'System' }}</td>
                                    <td>{{ $payment->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-money-bill-wave fa-3x mb-3"></i>
                        <p>No payments recorded yet</p>
                        <a href="{{ route('finance.payments.create', ['booking_id' => $book->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus mr-1"></i>Record First Payment
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
@if(auth()->user()->isAdmin())
// Update booking status
document.getElementById('bookingStatusSelect')?.addEventListener('change', function() {
    const status = this.value;
    const selectedOption = this.options[this.selectedIndex];
    const statusColor = selectedOption.getAttribute('data-color');
    const statusTextColor = selectedOption.getAttribute('data-text-color');
    
    Swal.fire({
        title: 'Update Booking Status?',
        text: 'Are you sure you want to change the booking status?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: statusColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, update it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route('books.update-status', $book->id) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge display
                    const statusBadge = document.querySelector('.info-row:has(#bookingStatusSelect)').previousElementSibling?.querySelector('.badge');
                    if (statusBadge) {
                        statusBadge.textContent = data.status_label;
                        statusBadge.style.backgroundColor = statusColor;
                        statusBadge.style.color = statusTextColor;
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Updated!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // Revert selection
                    this.value = '{{ $book->status ?? 1 }}';
                    Swal.fire('Error!', data.message || 'Failed to update status.', 'error');
                }
            })
            .catch(error => {
                // Revert selection
                this.value = '{{ $book->status ?? 1 }}';
                Swal.fire('Error!', 'An error occurred while updating status.', 'error');
                console.error('Error:', error);
            });
        } else {
            // Revert selection if cancelled
            this.value = '{{ $book->status ?? 1 }}';
        }
    });
});
@endif

function deleteBooking(id) {
    Swal.fire({
        title: 'Delete Booking?',
        text: "This will release all booths in this booking. This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoading();
            fetch(`/books/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    Swal.fire('Deleted!', data.message || 'Booking has been deleted.', 'success')
                        .then(() => {
                            window.location.href = '{{ route("books.index") }}';
                        });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to delete booking.', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                Swal.fire('Error!', 'An error occurred while deleting the booking.', 'error');
                console.error('Error:', error);
            });
        }
    });
}
</script>
@endpush

