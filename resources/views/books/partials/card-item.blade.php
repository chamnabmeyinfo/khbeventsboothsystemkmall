<div class="col-md-6 col-lg-4 mb-4">
    <div class="booking-card-modern {{ $typeClass }}" onclick="window.location='{{ route('books.show', $book) }}'">
        <div class="booking-card-header">
            <div>
                <h5 class="mb-0" style="font-weight: 700; color: #1a202c;">
                    <i class="fas fa-calendar-check mr-2"></i>Booking #{{ $book->id }}
                </h5>
            </div>
            <span class="badge-modern {{ $typeBadge }}">
                @if($book->type == 1) Regular
                @elseif($book->type == 2) Special
                @elseif($book->type == 3) Temporary
                @else {{ $book->type }}
                @endif
            </span>
        </div>
        <div class="booking-card-body">
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <div class="mr-3">
                        <x-avatar 
                            :avatar="$book->client->avatar ?? null" 
                            :name="$book->client->name ?? 'N/A'" 
                            :size="'md'" 
                            :type="'client'"
                            :shape="'circle'"
                        />
                    </div>
                    <div>
                        <div class="font-weight-700" style="font-size: 1.125rem; color: #1a202c;">
                            {{ $book->client ? ($book->client->company ?? $book->client->name) : 'N/A' }}
                        </div>
                        @if($book->client && $book->client->name && $book->client->company)
                        <div class="text-muted" style="font-size: 0.875rem; margin-top: 4px;">
                            <i class="fas fa-user mr-1"></i>{{ $book->client->name }}
                        </div>
                        @endif
                    </div>
                </div>
                @if($book->client && $book->client->email)
                <div class="text-muted ml-5" style="font-size: 0.8125rem;">
                    <i class="fas fa-envelope mr-1"></i>{{ $book->client->email }}
                </div>
                @endif
            </div>
            
            @php
                $boothsByBookId = $boothsByBookId ?? [];
                $boothsForBook = $boothsByBookId[$book->id] ?? collect();
                $boothNumbers = $boothsForBook->pluck('booth_number')->join(', ') ?: null;
                $floorPlanName = $book->floorPlan->name ?? null;
            @endphp
            @if($floorPlanName)
            <div class="mb-2">
                <span class="badge-modern badge-modern-info" style="font-size: 0.875rem; padding: 6px 12px;">
                    <i class="fas fa-map mr-1"></i>{{ $floorPlanName }}
                </span>
            </div>
            @endif
            <div class="mb-3 d-flex align-items-center">
                <div class="mr-3">
                    <span class="badge-modern badge-modern-info" style="font-size: 1rem; padding: 10px 18px;">
                        <i class="fas fa-cube mr-2"></i>{{ $boothCount }} {{ $boothCount == 1 ? 'Booth' : 'Booths' }}{{ $boothNumbers ? ' (' . Str::limit($boothNumbers, 30) . ')' : '' }}
                    </span>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="d-flex align-items-center mb-1">
                    <i class="fas fa-calendar text-muted mr-2" style="width: 20px;"></i>
                    <strong style="color: #1a202c;">{{ $book->date_book->format('M d, Y') }}</strong>
                </div>
                <div class="ml-5 text-muted" style="font-size: 0.875rem;">
                    <i class="fas fa-clock mr-1"></i>{{ $book->date_book->format('h:i A') }}
                </div>
            </div>
            
            <div class="d-flex align-items-center">
                <span class="text-muted mr-2" style="font-size: 0.875rem;">Booked by:</span>
                @if($book->user)
                    <x-avatar 
                        :avatar="$book->user->avatar" 
                        :name="$book->user->username" 
                        :size="'xs'" 
                        :type="$book->user->isAdmin() ? 'admin' : 'user'"
                        :shape="'circle'"
                    />
                    <span class="text-muted ml-2" style="font-size: 0.875rem;">{{ $book->user->username }}</span>
                @else
                    <i class="fas fa-server text-muted mr-1"></i>
                    <span class="text-muted" style="font-size: 0.875rem;">System</span>
                @endif
            </div>
        </div>
        <div class="booking-card-footer">
            <div class="btn-group btn-group-sm w-100" role="group">
                <button type="button" class="btn btn-info" onclick="event.stopPropagation(); showBookingInfo({{ $book->id }});" style="border-radius: 12px 0 0 12px;">
                    <i class="fas fa-eye mr-1"></i>View
                </button>
                @if(auth()->user()->isAdmin())
                <button type="button" class="btn btn-danger" onclick="event.stopPropagation(); deleteBooking({{ $book->id }});" style="border-radius: 0 12px 12px 0;">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

