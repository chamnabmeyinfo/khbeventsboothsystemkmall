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
                            <strong class="text-success">${{ number_format($booths->sum('price') ?? 0, 2) }}</strong>
                        </div>
                    </div>
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
</div>
@endsection

@push('scripts')
<script>
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

