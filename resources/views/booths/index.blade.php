@extends('layouts.admin')

@section('title', 'Booths')
@section('page-title', 'Booths')
@section('breadcrumb', 'Booths')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=2.8">
<style>
/* Apple iOS/macOS Settings-style: calm, spacious, premium. Single responsive implementation. */
.booths-apple-page {
    --apple-bg: #f5f5f7;
    --apple-surface: #ffffff;
    --apple-surface-alt: #fafafa;
    --apple-border: rgba(0, 0, 0, 0.06);
    --apple-border-light: rgba(0, 0, 0, 0.04);
    --apple-text: #1d1d1f;
    --apple-text-secondary: rgba(29, 29, 31, 0.7);
    --apple-accent: #007AFF;
    --apple-accent-muted: rgba(0, 122, 255, 0.12);
    --apple-radius: 12px;
    --apple-radius-sm: 8px;
    --apple-shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.04);
    --apple-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.06);
    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "SF Pro Display", "Inter", "Segoe UI", sans-serif;
}
.booths-apple-page .booths-container { padding: 0; max-width: 100%; margin-bottom: 2rem; }
.booths-apple-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 2rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid var(--apple-border-light);
}
.booths-apple-page .page-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--apple-text);
    letter-spacing: -0.02em;
    margin: 0;
}
.booths-apple-page .btn-new-booth {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: linear-gradient(180deg, #007AFF 0%, #0056b3 100%);
    color: white;
    border: none;
    border-radius: var(--apple-radius-sm);
    font-weight: 600;
    font-size: 0.9375rem;
    text-decoration: none;
    box-shadow: var(--apple-shadow-md);
    transition: box-shadow 0.2s ease, transform 0.15s ease;
}
.booths-apple-page .btn-new-booth:hover {
    color: white;
    box-shadow: 0 6px 16px rgba(0, 122, 255, 0.25);
    transform: scale(1.01);
}
.booths-apple-page .filter-bar-apple {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
    margin-bottom: 2rem;
}
.booths-apple-page .search-wrap {
    flex: 1;
    min-width: 200px;
}
.booths-apple-page .search-input {
    width: 100%;
    padding: 0.625rem 1rem 0.625rem 2.5rem;
    border: 1px solid var(--apple-border);
    border-radius: var(--apple-radius-sm);
    font-size: 0.9375rem;
    background: var(--apple-surface);
    transition: border-color 0.2s, box-shadow 0.2s;
}
.booths-apple-page .search-input:focus {
    outline: none;
    border-color: var(--apple-accent);
    box-shadow: 0 0 0 3px var(--apple-accent-muted);
}
.booths-apple-page .filter-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.booths-apple-page .filter-chip {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid var(--apple-border);
    background: var(--apple-surface);
    color: var(--apple-text-secondary);
    text-decoration: none;
    transition: all 0.2s ease;
}
.booths-apple-page .filter-chip:hover { background: var(--apple-surface-alt); color: var(--apple-text); }
.booths-apple-page .filter-chip.active {
    background: rgba(0, 122, 255, 0.2);
    color: var(--apple-accent);
    border: 1px solid rgba(0, 122, 255, 0.3);
}
.booths-apple-page .table-card {
    background: var(--apple-surface);
    border-radius: var(--apple-radius);
    overflow: hidden;
    box-shadow: var(--apple-shadow-sm);
    border: 1px solid var(--apple-border-light);
}
.booths-apple-page .table-card .table { margin-bottom: 0; }
.booths-apple-page .table-card .table thead tr {
    background: var(--apple-surface-alt);
}
.booths-apple-page .table-card .table thead th {
    border: none;
    padding: 1rem 1.5rem;
    font-weight: 600;
    font-size: 0.8125rem;
    color: var(--apple-text-secondary);
}
.booths-apple-page .table-card .table tbody tr {
    border-bottom: 1px solid var(--apple-border-light);
    transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
}
.booths-apple-page .table-card .table tbody tr:last-child { border-bottom: none; }
.booths-apple-page .table-card .table tbody tr:hover {
    background: rgba(0, 0, 0, 0.015);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    transform: scale(1.001);
}
.booths-apple-page .table-card .table tbody td {
    padding: 1.25rem 1.5rem;
    vertical-align: middle;
}
.booths-apple-page .booth-row {
    display: flex;
    align-items: center;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--apple-border-light);
    transition: background 0.15s ease, transform 0.15s ease;
}
.booths-apple-page .booth-row:last-child { border-bottom: none; }
.booths-apple-page .booth-row:hover {
    background: rgba(0, 0, 0, 0.015);
    transform: scale(1.001);
}
.booths-apple-page .booth-name { font-weight: 700; font-size: 1rem; color: var(--apple-text); }
.booths-apple-page .booth-meta { font-size: 0.8125rem; font-weight: 400; color: var(--apple-text-secondary); margin-top: 2px; }
.booths-apple-page .badge-apple {
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.6875rem;
    font-weight: 600;
}
.booths-apple-page .badge-apple.badge-success { background: rgba(52, 199, 89, 0.2); color: #248a3d; }
.booths-apple-page .badge-apple.badge-warning { background: rgba(255, 149, 0, 0.2); color: #c93400; }
.booths-apple-page .badge-apple.badge-info { background: rgba(0, 122, 255, 0.2); color: #0056b3; }
.booths-apple-page .badge-apple.badge-secondary { background: rgba(110, 110, 115, 0.2); color: #6e6e73; }
.booths-apple-page .badge-apple.badge-danger { background: rgba(255, 59, 48, 0.2); color: #d70015; }
.booths-apple-page .badge-apple.badge-primary { background: rgba(0, 122, 255, 0.2); color: #007AFF; }
.booths-apple-page .btn-action-apple {
    padding: 0.35rem 0.75rem;
    border-radius: var(--apple-radius-sm);
    font-size: 0.8125rem;
    font-weight: 500;
    transition: all 0.15s ease;
}
.booths-apple-page .btn-action-apple.btn-outline-primary {
    border: 1px solid rgba(0, 122, 255, 0.4);
    color: var(--apple-accent);
    background: transparent;
}
.booths-apple-page .btn-action-apple.btn-outline-primary:hover {
    background: rgba(0, 122, 255, 0.08);
    transform: scale(1.01);
}
.booths-apple-page .btn-action-apple.btn-outline-secondary {
    border: 1px solid var(--apple-border);
    color: var(--apple-text-secondary);
    background: transparent;
}
.booths-apple-page .btn-action-apple.btn-outline-secondary:hover {
    background: var(--apple-surface-alt);
    color: var(--apple-text);
    transform: scale(1.01);
}
.booths-apple-page .table-card .pagination-wrapper,
.booths-apple-page .table-card .border-top {
    background: var(--apple-surface-alt);
}
</style>
@endpush

@push('body-class', 'ios-dashboard-mode booths-apple-page')

@section('content')
<div class="booths-container">
    {{-- Top: H1 + New Booth --}}
    <header class="page-header">
        <h1 class="page-title">Booths</h1>
        <a href="{{ route('booths.index', ['view' => 'management', 'create' => 1]) }}" class="btn-new-booth">
            <i class="fas fa-plus"></i> New Booth
        </a>
    </header>

    {{-- Filter bar: Search + Chips --}}
    <form method="GET" action="{{ route('booths.index', ['view' => 'table']) }}" class="filter-bar-apple">
        <input type="hidden" name="view" value="table">
        <div class="search-wrap position-relative">
            <i class="fas fa-search position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: var(--apple-text-secondary); font-size: 0.875rem;"></i>
            <input type="text" name="search" class="search-input" placeholder="Search booths..." value="{{ request('search') }}">
        </div>
        <div class="filter-chips">
            @php
                $active = request('status', 'all');
                if ($active === '1') $active = 'available';
                elseif ($active === '3') $active = 'reserved';
                elseif (in_array($active, ['2','4'])) $active = 'booked';
            @endphp
            <a href="{{ route('booths.index', ['view' => 'table', 'search' => request('search')]) }}" class="filter-chip {{ $active === 'all' ? 'active' : '' }}">All</a>
            <button type="submit" name="status" value="1" class="filter-chip border-0 {{ $active === 'available' ? 'active' : '' }}" style="cursor:pointer;">Available</button>
            <button type="submit" name="status" value="3" class="filter-chip border-0 {{ $active === 'reserved' ? 'active' : '' }}" style="cursor:pointer;">Reserved</button>
            <button type="submit" name="status" value="2" class="filter-chip border-0 {{ $active === 'booked' ? 'active' : '' }}" style="cursor:pointer;">Booked</button>
        </div>
    </form>

    {{-- Main: Grouped table (iOS Settings style) --}}
    <div class="table-card">
        {{-- Desktop table --}}
        <div class="d-none d-md-block">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Client</th>
                            <th>Event</th>
                            <th>Price</th>
                            <th>Next Booking</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booths as $booth)
                        <tr class="booth-row">
                            <td>
                                <div class="booth-name">{{ $booth->booth_number }}</div>
                                <div class="booth-meta">{{ $booth->boothType ? $booth->boothType->name : 'Booth' }}</div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $booth->getStatusColor() }} badge-apple">{{ $booth->getStatusLabel() }}</span>
                            </td>
                            <td style="font-size: 0.9375rem; color: var(--apple-text-secondary);">{{ $booth->client ? $booth->client->company : '—' }}</td>
                            <td style="font-size: 0.9375rem; color: var(--apple-text-secondary);">{{ $booth->floorPlan ? $booth->floorPlan->name : '—' }}</td>
                            <td style="font-weight: 600; font-size: 0.9375rem; color: rgba(52, 199, 89, 0.85);">${{ number_format($booth->price, 2) }}</td>
                            <td style="font-size: 0.9375rem; color: var(--apple-text-secondary);">
                                @if($booth->book && $booth->book->date_book){{ $booth->book->date_book->format('M d, Y') }}@else—@endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('booths.show', $booth) }}" class="btn btn-sm btn-outline-primary btn-action-apple">View</a>
                                    <a href="{{ route('booths.edit', $booth) }}" class="btn btn-sm btn-outline-secondary btn-action-apple">Edit</a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary btn-action-apple" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.35rem 0.5rem;"><i class="fas fa-ellipsis-v"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('booths.show', $booth) }}"><i class="fas fa-eye me-2"></i>View</a></li>
                                            <li><a class="dropdown-item" href="{{ route('booths.edit', $booth) }}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('booths.destroy', $booth) }}" method="POST" onsubmit="return confirm('Delete this booth?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash me-2"></i>Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding: 3rem 1.5rem;">
                                <i class="fas fa-inbox fa-3x mb-3 d-block" style="color: var(--apple-text-secondary); opacity: 0.5;"></i>
                                <p class="mb-0" style="color: var(--apple-text-secondary);">No booths found</p>
                                <a href="{{ route('booths.index', ['view' => 'management', 'create' => 1]) }}" class="btn-new-booth mt-3 d-inline-flex">New Booth</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile: Card list --}}
        <div class="d-md-none p-4">
            @forelse($booths as $booth)
            <div class="booth-row">
                <div class="d-flex justify-content-between align-items-start w-100">
                    <a href="{{ route('booths.show', $booth) }}" class="text-decoration-none text-dark flex-grow-1">
                        <div class="booth-name">{{ $booth->booth_number }}</div>
                        <div class="booth-meta">{{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }} · {{ $booth->client ? $booth->client->company : '—' }}</div>
                        <div class="mt-2" style="font-weight: 600; color: rgba(52, 199, 89, 0.85);">${{ number_format($booth->price, 2) }}</div>
                    </a>
                    <span class="badge badge-{{ $booth->getStatusColor() }} badge-apple ms-2">{{ $booth->getStatusLabel() }}</span>
                </div>
                <div class="d-flex gap-2 mt-2 pt-2" style="border-top: 1px solid var(--apple-border-light);">
                    <a href="{{ route('booths.show', $booth) }}" class="btn btn-sm btn-outline-primary btn-action-apple flex-fill">View</a>
                    <a href="{{ route('booths.edit', $booth) }}" class="btn btn-sm btn-outline-secondary btn-action-apple flex-fill">Edit</a>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x mb-3" style="color: var(--apple-text-secondary); opacity: 0.5;"></i>
                <p class="mb-0" style="color: var(--apple-text-secondary);">No booths found</p>
                <a href="{{ route('booths.index', ['view' => 'management', 'create' => 1]) }}" class="btn-new-booth mt-3 d-inline-flex">New Booth</a>
            </div>
            @endforelse
        </div>

        @if(isset($booths) && $booths->hasPages())
        <div class="p-4 border-top" style="background: var(--apple-surface-alt); border-color: var(--apple-border-light);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <small style="color: var(--apple-text-secondary);">Showing {{ $booths->firstItem() ?? 0 }}–{{ $booths->lastItem() ?? 0 }} of {{ $booths->total() }}</small>
                {{ $booths->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
