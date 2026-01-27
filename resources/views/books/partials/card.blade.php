@php
    $boothIds = json_decode($book->boothid, true) ?? [];
    $boothCount = count($boothIds);
    $typeClass = 'regular';
    $typeBadge = 'badge-modern-primary';
    if ($book->type == 2) {
        $typeClass = 'special';
        $typeBadge = 'badge-modern-warning';
    } elseif ($book->type == 3) {
        $typeClass = 'temporary';
        $typeBadge = 'badge-modern-danger';
    }
    $totalAmount = $book->total_amount ?? 0;
    $paidAmount = $book->paid_amount ?? 0;
    $balanceAmount = $book->balance_amount ?? ($totalAmount - $paidAmount);
@endphp
<div class="booking-card {{ $typeClass }}" onclick="window.location='{{ route('books.show', $book) }}'">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1" style="font-weight: 700; color: #1a202c;">
                <i class="fas fa-calendar-check me-2"></i>Booking #{{ $book->id }}
            </h5>
            <span class="{{ $typeBadge }}">
                @if($book->type == 1) Regular
                @elseif($book->type == 2) Special
                @elseif($book->type == 3) Temporary
                @else {{ $book->type }}
                @endif
            </span>
        </div>
        <button type="button" class="btn btn-sm btn-info" onclick="event.stopPropagation(); showBookingInfo({{ $book->id }});" title="View">
            <i class="fas fa-eye"></i>
        </button>
    </div>
    
    <div class="mb-3">
        <div class="d-flex align-items-center mb-2">
            @if($book->client)
                <div class="me-3">
                    <x-avatar 
                        :avatar="$book->client->avatar ?? null" 
                        :name="$book->client->name ?? 'N/A'" 
                        :size="'md'" 
                        :type="'client'"
                        :shape="'circle'"
                    />
                </div>
                <div>
                    <div style="font-weight: 700; color: #1a202c;">
                        {{ $book->client->company ?? $book->client->name }}
                    </div>
                    @if($book->client->name && $book->client->company)
                    <small class="text-muted">
                        <i class="fas fa-user me-1"></i>{{ $book->client->name }}
                    </small>
                    @endif
                </div>
            @else
                <span class="text-muted">N/A</span>
            @endif
        </div>
    </div>
    
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="text-muted small">Date</div>
            <div style="font-weight: 600;">
                <i class="fas fa-calendar me-1"></i>{{ $book->date_book->format('M d, Y') }}
            </div>
            <div class="text-muted small">
                <i class="fas fa-clock me-1"></i>{{ $book->date_book->format('h:i A') }}
            </div>
        </div>
        <div class="col-6">
            <div class="text-muted small">Booths</div>
            <div style="font-weight: 600;">
                <i class="fas fa-cube me-1"></i>{{ $boothCount }} {{ $boothCount == 1 ? 'Booth' : 'Booths' }}
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
        <div>
            <div class="text-muted small">Total Amount</div>
            <div style="font-weight: 700; color: #10b981; font-size: 1.125rem;">
                ${{ number_format($totalAmount, 2) }}
            </div>
        </div>
        @if($balanceAmount > 0)
        <div class="text-end">
            <div class="text-muted small">Balance</div>
            <div style="font-weight: 600; color: #f59e0b;">
                ${{ number_format($balanceAmount, 2) }}
            </div>
        </div>
        @else
        <span class="badge bg-success">Paid</span>
        @endif
    </div>
    
    @if($book->user)
    <div class="mt-3 pt-3 border-top">
        <div class="d-flex align-items-center">
            <span class="text-muted small me-2">Booked by:</span>
            <x-avatar 
                :avatar="$book->user->avatar" 
                :name="$book->user->username" 
                :size="'xs'" 
                :type="$book->user->isAdmin() ? 'admin' : 'user'"
                :shape="'circle'"
            />
            <span class="text-muted small ms-2">{{ $book->user->username }}</span>
        </div>
    </div>
    @endif
</div>
