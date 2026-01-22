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
@endphp
<tr>
    <td>
        <input type="checkbox" class="form-check-input booking-checkbox" value="{{ $book->id }}">
    </td>
    <td>
        <strong class="text-primary" style="font-size: 1.125rem;">#{{ $book->id }}</strong>
    </td>
    <td>
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <x-avatar 
                    :avatar="$book->client->avatar ?? null" 
                    :name="$book->client->name ?? 'N/A'" 
                    :size="'sm'" 
                    :type="'client'"
                    :shape="'circle'"
                />
            </div>
            <div>
                <div class="font-weight-700" style="color: #1a202c; font-size: 1rem;">
                    {{ $book->client ? ($book->client->company ?? $book->client->name) : 'N/A' }}
                </div>
                @if($book->client && $book->client->company && $book->client->name)
                <div class="text-muted" style="font-size: 0.875rem; margin-top: 4px;">
                    <i class="fas fa-user mr-1"></i>{{ $book->client->name }}
                </div>
                @endif
                @if($book->client && $book->client->email)
                <div class="text-muted" style="font-size: 0.8125rem; margin-top: 2px;">
                    <i class="fas fa-envelope mr-1"></i>{{ $book->client->email }}
                </div>
                @endif
            </div>
        </div>
    </td>
    <td>
        <div class="d-flex align-items-center">
            <span class="badge-modern badge-modern-info">
                <i class="fas fa-cube mr-1"></i>{{ $boothCount }}
            </span>
        </div>
    </td>
    <td>
        <div style="color: #1a202c;">
            <div class="font-weight-700" style="font-size: 0.9375rem;">
                <i class="fas fa-calendar text-muted mr-1"></i>{{ $book->date_book->format('M d, Y') }}
            </div>
            <div class="text-muted" style="font-size: 0.8125rem; margin-top: 4px;">
                <i class="fas fa-clock mr-1"></i>{{ $book->date_book->format('h:i A') }}
            </div>
        </div>
    </td>
    <td>
        <span class="badge-modern {{ $typeBadge }}">
            @if($book->type == 1) Regular
            @elseif($book->type == 2) Special
            @elseif($book->type == 3) Temporary
            @else {{ $book->type }}
            @endif
        </span>
    </td>
    <td>
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
        <span class="badge-modern" style="background-color: {{ $statusColor }}; color: {{ $statusTextColor }};">
            {{ $statusName }}
        </span>
    </td>
    <td>
        @php
            $totalAmount = $book->total_amount ?? \App\Models\Booth::whereIn('id', $boothIds)->sum('price');
            $paidAmount = $book->paid_amount ?? 0;
            $balanceAmount = $book->balance_amount ?? ($totalAmount - $paidAmount);
        @endphp
        <div>
            <div class="font-weight-700 text-success" style="font-size: 0.9375rem;">
                ${{ number_format($totalAmount, 2) }}
            </div>
            @if($paidAmount > 0)
            <div class="text-muted" style="font-size: 0.8125rem;">
                Paid: ${{ number_format($paidAmount, 2) }}
            </div>
            @endif
            @if($balanceAmount > 0)
            <div class="text-warning" style="font-size: 0.8125rem;">
                Balance: ${{ number_format($balanceAmount, 2) }}
            </div>
            @endif
        </div>
    </td>
    <td>
        <div class="d-flex align-items-center">
            @if($book->user)
                <x-avatar 
                    :avatar="$book->user->avatar" 
                    :name="$book->user->username" 
                    :size="'xs'" 
                    :type="$book->user->isAdmin() ? 'admin' : 'user'"
                    :shape="'circle'"
                />
                <span class="ml-2 text-muted" style="font-size: 0.875rem;">{{ $book->user->username }}</span>
            @else
                <i class="fas fa-server text-muted mr-2"></i>
                <span class="text-muted" style="font-size: 0.875rem;">System</span>
            @endif
        </div>
    </td>
    <td>
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ route('books.show', $book) }}" class="btn btn-info" title="View Details" style="border-radius: 8px 0 0 8px;">
                <i class="fas fa-eye"></i>
            </a>
            @if(auth()->user()->isAdmin())
            <button type="button" class="btn btn-danger" onclick="deleteBooking({{ $book->id }})" title="Delete" style="border-radius: 0 8px 8px 0;">
                <i class="fas fa-trash"></i>
            </button>
            @endif
        </div>
    </td>
</tr>

