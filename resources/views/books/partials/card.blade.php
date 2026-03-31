@php
    $boothsByBookId = $boothsByBookId ?? [];
    $boothsForBook = $boothsByBookId[$book->id] ?? collect();
    $boothCount = $boothsForBook->count() ?: (is_array(json_decode($book->boothid, true) ?? null) ? count(json_decode($book->boothid, true)) : 0);
    $boothNumbers = $boothsForBook->pluck('booth_number')->join(', ') ?: '—';
    $floorPlanName = $book->floorPlan->name ?? '—';
    $eventName = optional($book->floorPlan)->event?->title;
    $typeBadgeClass = 'books-type-regular';
    if ($book->type == 2) {
        $typeBadgeClass = 'books-type-special';
    } elseif ($book->type == 3) {
        $typeBadgeClass = 'books-type-temporary';
    }
    $totalAmount = $book->total_amount ?? 0;
    $paidAmount = $book->paid_amount ?? 0;
    $balanceAmount = $book->balance_amount ?? ($totalAmount - $paidAmount);
    try {
        $statusSetting = isset($statusSetting) ? $statusSetting : ($book->statusSetting ?? \App\Models\BookingStatusSetting::getByCode($book->status ?? 1));
        $statusColor = $statusSetting ? $statusSetting->status_color : '#6c757d';
        $statusTextColor = $statusSetting && $statusSetting->text_color ? $statusSetting->text_color : '#ffffff';
        $statusName = $statusSetting ? $statusSetting->status_name : 'Pending';
    } catch (\Exception $e) {
        $statusColor = '#6c757d';
        $statusTextColor = '#ffffff';
        $statusName = 'Pending';
    }
    $rowNumber = $rowNumber ?? null;
@endphp
<div class="booking-card books-booking-card books-booking-card--type-{{ (int) $book->type }}" onclick="window.location='{{ route('books.show', $book) }}'">
    <div class="books-booking-card__head">
        <div class="books-booking-card__head-text">
            @if($rowNumber !== null)
                <span class="books-booking-card__row">Row {{ $rowNumber }}</span>
            @endif
            <h3 class="books-booking-card__title">
                <i class="fas fa-calendar-check me-2" aria-hidden="true"></i>Booking #{{ $book->id }}
            </h3>
            <div class="books-booking-card__badges">
                <span class="books-type-badge {{ $typeBadgeClass }}">
                    @if($book->type == 1) Regular
                    @elseif($book->type == 2) Special
                    @elseif($book->type == 3) Temporary
                    @else {{ $book->type }}
                    @endif
                </span>
                <span class="books-status-pill" style="background-color: {{ $statusColor }}; color: {{ $statusTextColor }};">
                    {{ $statusName }}
                </span>
            </div>
        </div>
        <div class="books-booking-card__actions" onclick="event.stopPropagation()">
            <div class="books-table-actions" role="group" aria-label="Card actions">
                <button type="button" class="books-table-btn plastic-btn-press" onclick="showBookingInfo({{ $book->id }})" title="Quick view">
                    <i class="fas fa-eye" aria-hidden="true"></i>
                </button>
                @if(auth()->user()->isAdmin())
                <button type="button" class="books-table-btn books-table-btn-danger plastic-btn-press" onclick="deleteBooking({{ $book->id }})" title="Delete booking">
                    <i class="fas fa-trash" aria-hidden="true"></i>
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="books-booking-card__client">
        @if($book->client)
            @php $c = $book->client; @endphp
            <div class="books-booking-card__avatar">
                <x-avatar
                    :avatar="$c->avatar ?? null"
                    :name="$c->name ?? 'N/A'"
                    :size="'md'"
                    :type="'client'"
                    :shape="'circle'"
                />
            </div>
            <div class="books-booking-card__client-text">
                <div class="books-booking-card__client-name">{{ $c->company ?? $c->name }}</div>
                <div class="books-booking-card__client-meta">
                    Contact Name: {{ $c->name ?? $c->company ?? '—' }} · ID {{ $c->id }}
                </div>
            </div>
        @else
            <span class="text-muted">N/A</span>
        @endif
    </div>

    @if($floorPlanName !== '—')
    <div class="books-booking-card__field">
        <span class="books-booking-card__label">Floor plan</span>
        <span class="books-booking-card__value" title="{{ $eventName ? 'Event: ' . $eventName : '' }}">
            <i class="fas fa-map me-1" aria-hidden="true"></i>{{ $floorPlanName }}
            @if($eventName)
                <span class="books-booking-card__sub d-block">{{ Str::limit($eventName, 42) }}</span>
            @endif
        </span>
    </div>
    @endif

    <div class="books-booking-card__grid">
        <div>
            <span class="books-booking-card__label">Date</span>
            <div class="books-booking-card__value">{{ $book->date_book->format('M d, Y') }}</div>
            <div class="books-booking-card__sub"><i class="fas fa-clock me-1" aria-hidden="true"></i>{{ $book->date_book->format('h:i A') }}</div>
        </div>
        <div>
            <span class="books-booking-card__label">Booths</span>
            <div class="books-booking-card__value"><i class="fas fa-cube me-1" aria-hidden="true"></i>{{ $boothCount }} {{ $boothCount == 1 ? 'Booth' : 'Booths' }}</div>
            @if($boothNumbers !== '—')
            <div class="books-booking-card__sub" title="{{ $boothNumbers }}">{{ Str::limit($boothNumbers, 36) }}</div>
            @endif
        </div>
    </div>

    <div class="books-booking-card__footer">
        <div>
            <span class="books-booking-card__label">Total</span>
            <div class="books-amount-cell books-booking-card__amount">${{ number_format($totalAmount, 2) }}</div>
        </div>
        @if($balanceAmount > 0)
        <div class="text-end">
            <span class="books-booking-card__label">Balance</span>
            <div class="books-booking-card__balance">${{ number_format($balanceAmount, 2) }}</div>
        </div>
        @else
        <div class="text-end align-self-end">
            <span class="status-badge status-badge-green">Paid</span>
        </div>
        @endif
    </div>

    @if($book->user)
    <div class="books-booking-card__by">
        <span class="books-booking-card__label me-2">Booked by</span>
        <x-avatar
            :avatar="$book->user->avatar"
            :name="$book->user->username"
            :size="'xs'"
            :type="$book->user->isAdmin() ? 'admin' : 'user'"
            :shape="'circle'"
        />
        <span class="books-booking-card__by-name">{{ $book->user->username }}</span>
    </div>
    @endif
</div>
