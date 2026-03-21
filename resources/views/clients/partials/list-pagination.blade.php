{{-- Clients list: page size + Laravel paginator (used above and below table/cards). --}}
@php $showPerPage = $showPerPage ?? true; @endphp
@if($clients->total() > 0 || $clients->hasPages())
<div class="clients-pagination-bar d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 px-1 py-2">
    <div class="text-muted small">
        @if($clients->total() === 0)
            0 clients
        @else
            Showing <strong>{{ $clients->firstItem() }}</strong>–<strong>{{ $clients->lastItem() }}</strong> of <strong>{{ $clients->total() }}</strong>
        @endif
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        @if($showPerPage)
        <label class="mb-0 small text-muted d-flex align-items-center gap-2">
            <span>Per page</span>
            <select name="per_page" class="form-select form-select-sm clients-per-page-select" form="clientsFilterToolbarForm" onchange="document.getElementById('clientsFilterToolbarForm').submit()">
                @foreach($allowedPerPage as $n)
                    <option value="{{ $n }}" {{ (int) $perPage === (int) $n ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
            </select>
        </label>
        @endif
        <div class="clients-pagination-links">{{ $clients->links('vendor.pagination.bootstrap-5-compact') }}</div>
    </div>
</div>
@endif
