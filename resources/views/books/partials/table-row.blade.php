@php
    $boothsByBookId = $boothsByBookId ?? [];
    $boothsForBook = $boothsByBookId[$book->id] ?? collect();
    $boothCount = $boothsForBook->count() ?: (is_array(json_decode($book->boothid, true) ?? null) ? count(json_decode($book->boothid, true)) : 0);
    $boothNumbers = $boothsForBook->pluck('booth_number')->join(', ') ?: '—';
    $floorPlanName = $book->floorPlan->name ?? '—';
    $eventName = optional($book->floorPlan)->event?->title;
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
@endphp
<tr onclick="window.location='{{ route('books.show', $book) }}'" style="cursor: pointer;">
    <td><strong>#{{ $book->id }}</strong></td>
    <td>
        <strong>{{ $book->client ? ($book->client->company ?? $book->client->name) : 'N/A' }}</strong>
        @if($book->client && $book->client->name && $book->client->company)
        <br><small class="text-muted">{{ $book->client->name }}</small>
        @endif
    </td>
    <td>
        <span title="{{ $eventName ? 'Event: ' . $eventName : '' }}">{{ $floorPlanName }}</span>
        @if($eventName)
        <br><small class="text-muted">{{ Str::limit($eventName, 20) }}</small>
        @endif
    </td>
    <td>
        {{ $book->date_book->format('M d, Y') }}<br>
        <small class="text-muted">{{ $book->date_book->format('h:i A') }}</small>
    </td>
    <td>
        <strong>{{ $boothCount }}</strong> {{ $boothCount == 1 ? 'Booth' : 'Booths' }}
        <br><small class="text-muted" title="{{ $boothNumbers }}">{{ Str::limit($boothNumbers, 30) ?: '—' }}</small>
    </td>
    <td>
        <span class="{{ $typeBadge }}">
            @if($book->type == 1) Regular
            @elseif($book->type == 2) Special
            @elseif($book->type == 3) Temporary
            @else {{ $book->type }}
            @endif
        </span>
    </td>
    <td>
        <span class="badge" style="background-color: {{ $statusColor }}; color: {{ $statusTextColor }};">
            {{ $statusName }}
        </span>
    </td>
    <td><strong style="color: #10b981;">${{ number_format($totalAmount, 2) }}</strong></td>
    <td onclick="event.stopPropagation()">
        <div class="btn-group btn-group-sm">
            <button type="button" class="btn btn-info" onclick="showBookingInfo({{ $book->id }})" title="View">
                <i class="fas fa-eye"></i>
            </button>
            @if(auth()->user()->isAdmin())
            <button type="button" class="btn btn-danger" onclick="deleteBooking({{ $book->id }})" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
            @endif
        </div>
    </td>
</tr>
