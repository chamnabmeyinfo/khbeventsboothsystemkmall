@extends('layouts.admin')

@section('title', 'Booths')
@section('page-title', 'Booths')
@section('breadcrumb', 'Booths')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
<style>
    .booths-list-page .looker-dashboard { padding: 0; max-width: 100%; }
    .booths-list-page .booth-row-card { transition: all var(--transition-base, 0.25s ease); }
    .booths-list-page .booth-row-card:hover { background: var(--color-gray-50, #f9fafb); }
</style>
@endpush

@push('body-class', 'ios-dashboard-mode booths-list-page')

@section('content')
<div class="looker-dashboard">
    {{-- Top section: Title + New Booth --}}
    <header class="looker-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end gap-3 mb-4">
        <div class="looker-header-title">
            <h1 class="mb-0" style="font-size: var(--font-size-2xl, 1.5rem); font-weight: 700; color: var(--text-primary, #1d1d1f);">Booths</h1>
        </div>
        <div class="looker-actions">
            <a href="{{ route('booths.index', ['view' => 'management', 'create' => 1]) }}" class="action-btn action-btn-primary" style="box-shadow: var(--shadow-sm, 0 1px 3px rgba(0,0,0,0.1));">
                <i class="fas fa-plus"></i> New Booth
            </a>
        </div>
    </header>

    {{-- Secondary bar: Search + Filter chips --}}
    <form method="GET" action="{{ route('booths.index', ['view' => 'table']) }}" id="boothFilterForm" class="filter-bar mb-4">
        <div class="d-flex flex-column flex-md-row gap-3 align-items-stretch align-items-md-center justify-content-between">
            <div class="flex-grow-1" style="min-width: 0;">
                <label for="boothSearch" class="form-label mb-1" style="font-size: var(--font-size-sm); font-weight: 600; color: var(--color-gray-600);">
                    <i class="fas fa-search me-1"></i>Search booths
                </label>
                <input type="text" name="search" id="boothSearch" class="form-control" placeholder="Booth number, company, category..."
                    value="{{ request('search') }}" style="border-radius: var(--radius-lg); border: 1px solid var(--color-gray-200); padding: 0.5rem 1rem;">
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-end">
                @php
                    $activeFilter = request('status', 'all');
                    if ($activeFilter === '1') $activeFilter = 'available';
                    elseif ($activeFilter === '3') $activeFilter = 'reserved';
                    elseif (in_array($activeFilter, ['2','4'])) $activeFilter = 'booked';
                @endphp
                <input type="hidden" name="view" value="table">
                <a href="{{ route('booths.index', ['view' => 'table', 'search' => request('search')]) }}" class="filter-chip text-decoration-none {{ $activeFilter === 'all' ? 'active' : '' }}" style="padding: 0.5rem 1rem; border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: 500; background: {{ $activeFilter === 'all' ? 'var(--color-primary)' : 'var(--color-gray-100)' }}; color: {{ $activeFilter === 'all' ? 'white' : 'var(--color-gray-700)' }};">All</a>
                <button type="submit" name="status" value="1" class="filter-chip border-0 {{ $activeFilter === 'available' ? 'active' : '' }}" style="padding: 0.5rem 1rem; border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: 500; background: {{ $activeFilter === 'available' ? 'var(--color-primary)' : 'var(--color-gray-100)' }}; color: {{ $activeFilter === 'available' ? 'white' : 'var(--color-gray-700)' }};">Available</button>
                <button type="submit" name="status" value="3" class="filter-chip border-0 {{ $activeFilter === 'reserved' ? 'active' : '' }}" style="padding: 0.5rem 1rem; border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: 500; background: {{ $activeFilter === 'reserved' ? 'var(--color-primary)' : 'var(--color-gray-100)' }}; color: {{ $activeFilter === 'reserved' ? 'white' : 'var(--color-gray-700)' }};">Reserved</button>
                <button type="submit" name="status" value="2" class="filter-chip border-0 {{ $activeFilter === 'booked' ? 'active' : '' }}" style="padding: 0.5rem 1rem; border-radius: var(--radius-lg); font-size: var(--font-size-sm); font-weight: 500; background: {{ $activeFilter === 'booked' ? 'var(--color-primary)' : 'var(--color-gray-100)' }}; color: {{ $activeFilter === 'booked' ? 'white' : 'var(--color-gray-700)' }};">Booked</button>
            </div>
        </div>
    </form>

    {{-- Main content: Table (desktop) / Card list (mobile) --}}
    <div class="glass-card" style="border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-md);">
        {{-- Desktop: Table --}}
        <div class="d-none d-md-block">
            <div class="looker-table-container" style="overflow-x: auto;">
                <table class="looker-table table mb-0">
                    <thead>
                        <tr>
                            <th style="font-weight: 600; font-size: var(--font-size-sm); color: var(--color-gray-600); padding: 1rem;">Name</th>
                            <th style="font-weight: 600; font-size: var(--font-size-sm); color: var(--color-gray-600); padding: 1rem;">Status</th>
                            <th style="font-weight: 600; font-size: var(--font-size-sm); color: var(--color-gray-600); padding: 1rem;">Client</th>
                            <th style="font-weight: 600; font-size: var(--font-size-sm); color: var(--color-gray-600); padding: 1rem;">Event</th>
                            <th style="font-weight: 600; font-size: var(--font-size-sm); color: var(--color-gray-600); padding: 1rem;">Price</th>
                            <th style="font-weight: 600; font-size: var(--font-size-sm); color: var(--color-gray-600); padding: 1rem;">Next Booking</th>
                            <th style="font-weight: 600; font-size: var(--font-size-sm); color: var(--color-gray-600); padding: 1rem; width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booths as $booth)
                        <tr class="booth-row-card" style="border-bottom: 1px solid var(--color-gray-200);">
                            <td style="padding: 1rem; vertical-align: middle;">
                                <div style="font-weight: 600; font-size: var(--font-size-base); color: var(--color-gray-900);">{{ $booth->booth_number }}</div>
                                <div style="font-size: var(--font-size-sm); color: var(--color-gray-500);">{{ $booth->boothType ? $booth->boothType->name : 'Booth' }}</div>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle;">
                                <span class="badge badge-{{ $booth->getStatusColor() }}" style="padding: 0.35rem 0.75rem; border-radius: var(--radius-lg); font-weight: 600; font-size: var(--font-size-xs);">
                                    {{ $booth->getStatusLabel() }}
                                </span>
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; font-size: var(--font-size-sm); color: var(--color-gray-700);">
                                {{ $booth->client ? $booth->client->company : '—' }}
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; font-size: var(--font-size-sm); color: var(--color-gray-600);">
                                {{ $booth->floorPlan ? $booth->floorPlan->name : '—' }}
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; font-weight: 600; font-size: var(--font-size-sm); color: var(--color-success);">
                                ${{ number_format($booth->price, 2) }}
                            </td>
                            <td style="padding: 1rem; vertical-align: middle; font-size: var(--font-size-sm); color: var(--color-gray-600);">
                                @if($booth->book && $booth->book->date_book)
                                    {{ $booth->book->date_book->format('M d, Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td style="padding: 1rem; vertical-align: middle;">
                                <div class="d-flex align-items-center gap-1">
                                    <a href="{{ route('booths.show', $booth) }}" class="btn btn-sm btn-outline-primary" title="View" style="padding: 0.35rem 0.6rem; border-radius: var(--radius-md);">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('booths.edit', $booth) }}" class="btn btn-sm btn-outline-secondary" title="Edit" style="padding: 0.35rem 0.6rem; border-radius: var(--radius-md);">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More" style="padding: 0.35rem 0.6rem; border-radius: var(--radius-md);">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('booths.show', $booth) }}"><i class="fas fa-eye me-2"></i>View</a></li>
                                            <li><a class="dropdown-item" href="{{ route('booths.edit', $booth) }}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('booths.destroy', $booth) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this booth?');">
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
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No booths found</p>
                                <a href="{{ route('booths.index', ['view' => 'management', 'create' => 1]) }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-plus me-1"></i>New Booth
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile: Card list --}}
        <div class="d-md-none p-3">
            @forelse($booths as $booth)
            <div class="d-block mb-3 p-3 rounded-3" style="background: var(--color-gray-50); border: 1px solid var(--color-gray-200); transition: all 0.2s ease;">
                <a href="{{ route('booths.show', $booth) }}" class="text-decoration-none text-dark d-block">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div style="font-weight: 600; font-size: 1.0625rem;">{{ $booth->booth_number }}</div>
                            <div style="font-size: 0.8125rem; color: var(--color-gray-500);">{{ $booth->floorPlan ? $booth->floorPlan->name : 'N/A' }} · {{ $booth->client ? $booth->client->company : '—' }}</div>
                        </div>
                        <span class="badge badge-{{ $booth->getStatusColor() }}" style="padding: 0.35rem 0.75rem; border-radius: var(--radius-lg); font-size: 0.75rem;">{{ $booth->getStatusLabel() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span style="font-weight: 600; color: var(--color-success);">${{ number_format($booth->price, 2) }}</span>
                        <span style="font-size: 0.8125rem; color: var(--color-gray-500);">
                            @if($booth->book && $booth->book->date_book){{ $booth->book->date_book->format('M d') }}@else—@endif
                        </span>
                    </div>
                </a>
                <div class="d-flex gap-2 mt-2 pt-2" style="border-top: 1px solid var(--color-gray-200);">
                    <a href="{{ route('booths.show', $booth) }}" class="btn btn-sm btn-outline-primary flex-fill">View</a>
                    <a href="{{ route('booths.edit', $booth) }}" class="btn btn-sm btn-outline-secondary flex-fill">Edit</a>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No booths found</p>
                <a href="{{ route('booths.index', ['view' => 'management', 'create' => 1]) }}" class="btn btn-primary mt-3">New Booth</a>
            </div>
            @endforelse
        </div>

        @if(isset($booths) && $booths->hasPages())
        <div class="p-3 border-top" style="background: var(--color-gray-50);">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <small class="text-muted">
                    Showing {{ $booths->firstItem() ?? 0 }}–{{ $booths->lastItem() ?? 0 }} of {{ $booths->total() }}
                </small>
                {{ $booths->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
