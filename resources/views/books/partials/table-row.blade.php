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
    $totalAmount = $book->total_amount ?? \App\Models\Booth::whereIn('id', $boothIds)->sum('price');
    $paidAmount = $book->paid_amount ?? 0;
    $balanceAmount = $book->balance_amount ?? ($totalAmount - $paidAmount);
@endphp
<tr onclick="window.location='{{ route('books.show', $book) }}'" style="cursor: pointer;">
    <td><strong>#{{ $book->id }}</strong></td>
    <td>
        <span class="badge" style="background: {{ $statusColor }}; color: {{ $statusTextColor }};">
            {{ $statusName }}
        </span>
    </td>
    <td>
        <strong>{{ $book->client ? ($book->client->company ?? $book->client->name) : 'N/A' }}</strong>
        @if($book->client && $book->client->name && $book->client->company)
        <br><small class="text-muted">{{ $book->client->name }}</small>
        @endif
    </td>
    <td>{{ $boothCount }} {{ $boothCount == 1 ? 'Booth' : 'Booths' }}</td>
    <td>
        {{ $book->date_book->format('M d, Y') }}<br>
        <small class="text-muted">{{ $book->date_book->format('h:i A') }}</small>
    </td>
    <td><strong style="color: #10b981;">${{ number_format($totalAmount, 2) }}</strong></td>
    <td>
        @if($balanceAmount > 0)
        <span style="color: #f59e0b;">${{ number_format($balanceAmount, 2) }}</span>
        @else
        <span class="text-success">Paid</span>
        @endif
    </td>
    <td>
        @if($book->user)
        <x-avatar :avatar="$book->user->avatar" :name="$book->user->username" :size="'xs'" :type="$book->user->isAdmin() ? 'admin' : 'user'" :shape="'circle'" />
        <small>{{ $book->user->username }}</small>
        @else
        <i class="fas fa-server text-muted"></i> System
        @endif
    </td>
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
