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

