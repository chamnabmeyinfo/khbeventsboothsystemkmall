@php
    $boothIds = json_decode($book->boothid, true) ?? [];
    $boothCount = count($boothIds);
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

<div class="mobile-booking-card" onclick="showBookingInfo({{ $book->id }})">
    <div class="mobile-card-header">
        <div class="mobile-card-id">
            <i class="fas fa-hashtag"></i>
            <span>#{{ $book->id }}</span>
        </div>
        <span class="mobile-card-status" style="background-color: {{ $statusColor }}; color: {{ $statusTextColor }};">
            {{ $statusName }}
        </span>
    </div>
    
    <div class="mobile-card-body">
        <!-- Client Info -->
        <div class="mobile-card-section">
            <div class="mobile-card-row">
                <i class="fas fa-building"></i>
                <div class="mobile-card-content">
                    <div class="mobile-card-label">Client</div>
                    <div class="mobile-card-value">{{ $book->client ? ($book->client->company ?? $book->client->name ?? 'N/A') : 'N/A' }}</div>
                    @if($book->client && $book->client->name && $book->client->company)
                    <div class="mobile-card-subvalue">{{ $book->client->name }}</div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Date & Time -->
        <div class="mobile-card-section">
            <div class="mobile-card-row">
                <i class="fas fa-calendar-alt"></i>
                <div class="mobile-card-content">
                    <div class="mobile-card-label">Date & Time</div>
                    <div class="mobile-card-value">
                        {{ $book->date_book ? $book->date_book->format('M d, Y') : 'N/A' }}
                    </div>
                    @if($book->date_book)
                    <div class="mobile-card-subvalue">{{ $book->date_book->format('h:i A') }}</div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Booths -->
        <div class="mobile-card-section">
            <div class="mobile-card-row">
                <i class="fas fa-cube"></i>
                <div class="mobile-card-content">
                    <div class="mobile-card-label">Booths</div>
                    <div class="mobile-card-value">{{ $boothCount }} {{ $boothCount == 1 ? 'Booth' : 'Booths' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Amount -->
        <div class="mobile-card-section">
            <div class="mobile-card-row">
                <i class="fas fa-dollar-sign"></i>
                <div class="mobile-card-content">
                    <div class="mobile-card-label">Total Amount</div>
                    <div class="mobile-card-value mobile-card-amount">
                        ${{ number_format($book->total_amount ?? 0, 2) }}
                    </div>
                    @if($book->balance_amount > 0)
                    <div class="mobile-card-subvalue mobile-card-balance">
                        Balance: ${{ number_format($book->balance_amount, 2) }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Type Badge -->
        <div class="mobile-card-type">
            @if($book->type == 1)
                <span class="mobile-type-badge mobile-type-regular">Regular</span>
            @elseif($book->type == 2)
                <span class="mobile-type-badge mobile-type-special">Special</span>
            @elseif($book->type == 3)
                <span class="mobile-type-badge mobile-type-temporary">Temporary</span>
            @endif
        </div>
    </div>
    
    <div class="mobile-card-footer">
        <button type="button" class="mobile-card-action" onclick="event.stopPropagation(); showBookingInfo({{ $book->id }});">
            <i class="fas fa-eye"></i>
            <span>View Details</span>
        </button>
    </div>
</div>
