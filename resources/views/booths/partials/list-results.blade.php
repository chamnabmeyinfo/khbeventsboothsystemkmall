@if(isset($booths) && method_exists($booths, 'total'))
    <div class="booths-section-meta">
        {{ $booths->total() }} booth{{ $booths->total() !== 1 ? 's' : '' }}
        @if(request('search')) · matching "{{ request('search') }}"@endif
    </div>
    @endif

    <div class="canvas-panel books-data-panel" id="boothsContainer">
        <div class="table-view">
            <div class="looker-table-wrapper">
                <table class="looker-table books-looker-table mb-0" data-books-column-resize-key="booths">
                    <thead>
                        <tr>
                            <th scope="col">Booth</th>
                            <th scope="col">Status</th>
                            <th scope="col">Client</th>
                            <th scope="col">Event / Floor Plan</th>
                            <th scope="col">Price</th>
                            <th scope="col">Next Booking</th>
                            <th scope="col" class="books-col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booths as $booth)
                        <tr class="books-table-row books-table-row--booths">
                            <td>
                                <div class="avatar-chip">
                                    <div class="avatar-circle" aria-hidden="true">
                                        {{ strtoupper(substr($booth->booth_number ?? 'B', 0, 2)) }}
                                    </div>
                                    <div class="avatar-chip-text">
                                        <span class="avatar-chip-title">{{ $booth->booth_number }}</span>
                                        <span class="avatar-chip-sub">{{ $booth->boothType?->name ?? 'Standard' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="ap-badge badge-{{ $booth->getStatusColor() }}">
                                    {{ $booth->getStatusLabel() }}
                                </span>
                            </td>
                            <td class="cell-muted">
                                @if($booth->client)
                                    <a href="{{ route('clients.show', $booth->client) }}" class="booths-client-link">
                                        {{ $booth->client->company }}
                                    </a>
                                @else
                                    <span class="booths-dash">—</span>
                                @endif
                            </td>
                            <td class="cell-muted">
                                {{ $booth->floorPlan?->name ?? '—' }}
                            </td>
                            <td>
                                <strong class="books-amount-cell">${{ number_format($booth->price, 2) }}</strong>
                            </td>
                            <td class="cell-muted">
                                @if($booth->book && $booth->book->date_book)
                                    <span class="booths-booking-date">
                                        {{ $booth->book->date_book->format('M d, Y') }}
                                    </span>
                                @else
                                    <span class="booths-dash">—</span>
                                @endif
                            </td>
                            <td class="books-col-actions" onclick="event.stopPropagation()">
                                <div class="books-table-actions" role="group" aria-label="Booth actions">
                                    <a href="{{ route('booths.show', $booth) }}"
                                       class="books-table-btn plastic-btn-press"
                                       title="View booth"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                    <a href="{{ route('booths.edit', $booth) }}"
                                       class="books-table-btn plastic-btn-press"
                                       title="Edit booth"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                                    <div class="dropdown d-inline-block">
                                        <button type="button"
                                                class="books-table-btn plastic-btn-press"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                aria-label="More options for booth {{ $booth->booth_number }}"
                                                title="More options">
                                            <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('booths.show', $booth) }}">
                                                    <i class="fas fa-eye me-2 text-muted" style="width:14px;" aria-hidden="true"></i>View Details
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('booths.edit', $booth) }}">
                                                    <i class="fas fa-pencil-alt me-2 text-muted" style="width:14px;" aria-hidden="true"></i>Edit Booth
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form action="{{ route('booths.destroy', $booth) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Delete booth {{ addslashes($booth->booth_number) }}? This cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fas fa-trash-alt me-2" style="width:14px;" aria-hidden="true"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-store-slash empty-state-icon" aria-hidden="true"></i>
                                    <h3>No booths found</h3>
                                    <p class="text-muted">
                                        @if(request('search'))Try clearing filters or add a booth for "{{ request('search') }}".@else Add booths from the floor plan.@endif
                                    </p>
                                    <a href="{{ route('booths.index', ['view' => 'canvas', 'create' => 1]) }}" class="action-btn action-btn-primary mt-3">
                                        <i class="fas fa-plus"></i> Add your first booth
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-view" style="display: none;">
            <div class="books-card-view-inner booths-card-density--medium" id="boothsCardViewInner">
                @forelse($booths as $booth)
                @php
                    $boothCardImageUrl = $booth->getDisplayImageUrl();
                @endphp
                <div class="booking-card books-booking-card books-booking-card--type-1"
                     onclick="window.location='{{ route('booths.show', $booth) }}'">
                    <div class="booths-card-media">
                        @if($boothCardImageUrl)
                            <img
                                src="{{ $boothCardImageUrl }}"
                                alt="Booth {{ $booth->booth_number }} photo"
                                class="booths-card-media__img"
                                loading="lazy"
                                decoding="async"
                            >
                        @else
                            <div class="booths-card-media__placeholder">
                                <i class="fas fa-image" aria-hidden="true"></i>
                                <span class="visually-hidden">No booth photo</span>
                            </div>
                        @endif
                    </div>
                    <div class="books-booking-card__head">
                        <div class="books-booking-card__head-text">
                            <span class="books-booking-card__row">Booth</span>
                            <h3 class="books-booking-card__title">
                                <i class="fas fa-cube me-2" aria-hidden="true"></i>{{ $booth->booth_number }}
                            </h3>
                            <div class="books-booking-card__badges">
                                <span class="ap-badge badge-{{ $booth->getStatusColor() }}">{{ $booth->getStatusLabel() }}</span>
                            </div>
                        </div>
                        <div class="books-booking-card__actions" onclick="event.stopPropagation()">
                            <div class="books-table-actions" role="group" aria-label="Booth actions">
                                <a href="{{ route('booths.show', $booth) }}" class="books-table-btn plastic-btn-press" title="View"><i class="fas fa-eye" aria-hidden="true"></i></a>
                                <a href="{{ route('booths.edit', $booth) }}" class="books-table-btn plastic-btn-press" title="Edit"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="books-booking-card__mid booths-card-mid">
                        <div class="books-booking-card__field">
                            <span class="books-booking-card__label">Floor plan</span>
                            <div class="books-booking-card__value">{{ $booth->floorPlan?->name ?? '—' }}</div>
                        </div>
                        <div class="books-booking-card__client">
                            <div class="books-booking-card__client-text">
                                <div class="books-booking-card__client-name">{{ $booth->client?->company ?? 'No client' }}</div>
                                <div class="books-booking-card__client-meta">{{ $booth->boothType?->name ?? 'Standard type' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="books-booking-card__footer">
                        <span class="books-booking-card__amount books-amount-cell">${{ number_format($booth->price, 2) }}</span>
                    </div>
                </div>
                @empty
                <div class="empty-state books-card-view-empty">
                    <i class="fas fa-store-slash empty-state-icon" aria-hidden="true"></i>
                    <h3>No booths found</h3>
                    <p class="text-muted">Try adjusting filters or create a booth on the floor plan.</p>
                    <a href="{{ route('booths.index', ['view' => 'canvas', 'create' => 1]) }}" class="action-btn action-btn-primary mt-3">
                        <i class="fas fa-plus"></i> New Booth
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        @if(isset($booths) && $booths->hasPages())
        <div class="booths-table-foot">
            <small>
                Showing {{ $booths->firstItem() ?? 0 }}–{{ $booths->lastItem() ?? 0 }}
                of {{ $booths->total() }} booths
            </small>
            <nav aria-label="Booths pagination">
                <ul class="pagination">
                    <li class="page-item {{ $booths->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $booths->onFirstPage() ? '#' : $booths->appends(request()->query())->url(1) }}" aria-label="First"><i class="fas fa-angle-double-left"></i></a>
                    </li>
                    <li class="page-item {{ $booths->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $booths->onFirstPage() ? '#' : $booths->appends(request()->query())->previousPageUrl() }}" aria-label="Previous"><i class="fas fa-angle-left"></i></a>
                    </li>
                    @php
                        $currentPage = $booths->currentPage();
                        $lastPage    = $booths->lastPage();
                        $winStart    = max(1, $currentPage - 2);
                        $winEnd      = min($lastPage, $currentPage + 2);
                    @endphp
                    @if($winStart > 1)
                        <li class="page-item"><a class="page-link" href="{{ $booths->appends(request()->query())->url(1) }}">1</a></li>
                        @if($winStart > 2)
                            <li class="page-item disabled"><span class="page-link booths-page-ellipsis">…</span></li>
                        @endif
                    @endif
                    @for($p = $winStart; $p <= $winEnd; $p++)
                        <li class="page-item {{ $p === $currentPage ? 'active' : '' }}">
                            <a class="page-link" href="{{ $booths->appends(request()->query())->url($p) }}">{{ $p }}</a>
                        </li>
                    @endfor
                    @if($winEnd < $lastPage)
                        @if($winEnd < $lastPage - 1)
                            <li class="page-item disabled"><span class="page-link booths-page-ellipsis">…</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $booths->appends(request()->query())->url($lastPage) }}">{{ $lastPage }}</a></li>
                    @endif
                    <li class="page-item {{ $booths->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $booths->hasMorePages() ? $booths->appends(request()->query())->nextPageUrl() : '#' }}" aria-label="Next"><i class="fas fa-angle-right"></i></a>
                    </li>
                    <li class="page-item {{ $booths->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $booths->hasMorePages() ? $booths->appends(request()->query())->url($lastPage) : '#' }}" aria-label="Last"><i class="fas fa-angle-double-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
        @endif
    </div>
