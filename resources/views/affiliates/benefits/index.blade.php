@extends('layouts.admin')

@section('title', 'Affiliate Benefits Management')
@section('page-title', 'Affiliate Benefits')
@section('breadcrumb', 'Sales / Affiliates / Benefits')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.7">
<link rel="stylesheet" href="{{ asset('css/affiliates-page.css') }}?v=1.1">
@endpush

@push('body-class', 'ios-dashboard-mode affiliates-page')

@php
    $activeFilterCount = 0;
    if (filled(request('search'))) {
        $activeFilterCount++;
    }
    if (filled(request('type'))) {
        $activeFilterCount++;
    }
    if (request()->has('is_active') && request('is_active') !== '') {
        $activeFilterCount++;
    }
@endphp

@section('content')
<div class="looker-dashboard">
    <header class="looker-header animate-slide-up delay-1">
        <div class="looker-header-title">
            <h1>Affiliate benefits</h1>
            <p>Configure commissions, bonuses, and rewards for your sales team.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('affiliates.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-chart-line" aria-hidden="true"></i> Affiliates
            </a>
            <a href="{{ route('affiliates.benefits.create') }}" class="action-btn action-btn-primary">
                <i class="fas fa-plus" aria-hidden="true"></i> New benefit
            </a>
        </div>
    </header>

    <div class="kpi-wrapper">
        <div class="kpi-card-looker animate-slide-up delay-2">
            <div class="kpi-top">
                <div class="kpi-title">Matching rules</div>
                <div class="kpi-icon-wrapper primary-icon"><i class="fas fa-gift" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($benefits->total()) }}</div>
            <div class="kpi-bottom trend-neutral">With current filters</div>
        </div>
        <div class="kpi-card-looker success animate-slide-up delay-3">
            <div class="kpi-top">
                <div class="kpi-title">Active</div>
                <div class="kpi-icon-wrapper success-icon"><i class="fas fa-check-circle" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($benefits->where('is_active', true)->count()) }}</div>
            <div class="kpi-bottom trend-positive">On this page</div>
        </div>
        <div class="kpi-card-looker warning animate-slide-up delay-4">
            <div class="kpi-top">
                <div class="kpi-title">Inactive</div>
                <div class="kpi-icon-wrapper warning-icon"><i class="fas fa-pause-circle" aria-hidden="true"></i></div>
            </div>
            <div class="kpi-value-looker">{{ number_format($benefits->where('is_active', false)->count()) }}</div>
            <div class="kpi-bottom trend-warning">On this page</div>
        </div>
    </div>

    <div class="filter-bar">
        <form method="GET" action="{{ route('affiliates.benefits.index') }}" id="benefitsFilterForm">
            <div class="filter-header">
                <h6>
                    <i class="fas fa-filter" aria-hidden="true"></i> Filters
                    <span class="filter-badge {{ $activeFilterCount > 0 ? '' : 'd-none' }}">{{ $activeFilterCount }} active</span>
                </h6>
            </div>
            <div class="filter-row-primary">
                <div>
                    <label class="form-label" for="benefits_search">Search</label>
                    <input type="search" name="search" id="benefits_search" class="form-control" autocomplete="off"
                           placeholder="Benefit name…" value="{{ request('search') }}">
                </div>
                <div>
                    <label class="form-label" for="benefits_type">Type</label>
                    <select name="type" id="benefits_type" class="form-select">
                        <option value="">All types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="benefits_status">Status</label>
                    <select name="is_active" id="benefits_status" class="form-select">
                        <option value="">All</option>
                        <option value="1" {{ request('is_active') === '1' || request('is_active') === 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' || request('is_active') === 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-filter" aria-hidden="true"></i> Apply filters
                </button>
                <a href="{{ route('affiliates.benefits.index') }}" class="action-btn action-btn-secondary">Reset all</a>
            </div>
        </form>
    </div>

    <div class="canvas-panel animate-slide-up delay-5">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-table" aria-hidden="true"></i> All benefits</h2>
        </div>
        <div class="looker-table-wrapper">
            <div class="table-responsive">
                <table class="looker-table">
                    <thead>
                        <tr>
                            <th scope="col">Benefit</th>
                            <th scope="col">Type</th>
                            <th scope="col">Calculation</th>
                            <th scope="col">Value</th>
                            <th scope="col">Targets</th>
                            <th scope="col">Applies to</th>
                            <th scope="col" class="text-center">Priority</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($benefits as $benefit)
                        @php
                            $typeClass = match ($benefit->type) {
                                'commission' => 'status-badge-blue',
                                'bonus' => 'status-badge-green',
                                'incentive' => 'status-badge-purple',
                                'reward' => 'status-badge-orange',
                                default => 'status-badge-neutral',
                            };
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $benefit->name }}</strong>
                                @if($benefit->description)
                                    <br><span class="small text-secondary">{{ Str::limit($benefit->description, 50) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ $typeClass }}">{{ ucfirst($benefit->type) }}</span>
                            </td>
                            <td><span class="small text-secondary">{{ ucfirst(str_replace('_', ' ', $benefit->calculation_method)) }}</span></td>
                            <td>
                                @if($benefit->calculation_method === 'percentage')
                                    <strong>{{ number_format($benefit->percentage, 2) }}%</strong>
                                @elseif($benefit->calculation_method === 'fixed_amount')
                                    <strong>${{ number_format($benefit->fixed_amount, 2) }}</strong>
                                @else
                                    <span class="small text-secondary">Tiered</span>
                                @endif
                            </td>
                            <td>
                                @if($benefit->target_revenue)
                                    <span class="small d-block">Revenue: ${{ number_format($benefit->target_revenue, 0) }}</span>
                                @endif
                                @if($benefit->target_bookings)
                                    <span class="small d-block">Bookings: {{ $benefit->target_bookings }}</span>
                                @endif
                                @if($benefit->target_clients)
                                    <span class="small d-block">Clients: {{ $benefit->target_clients }}</span>
                                @endif
                                @if(!$benefit->target_revenue && !$benefit->target_bookings && !$benefit->target_clients)
                                    <span class="text-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                @if($benefit->user)
                                    <span class="small d-block">User: {{ $benefit->user->username }}</span>
                                @endif
                                @if($benefit->floorPlan)
                                    <span class="small d-block">Plan: {{ $benefit->floorPlan->name }}</span>
                                @endif
                                @if(!$benefit->user && !$benefit->floorPlan)
                                    <span class="text-secondary">All</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="status-badge status-badge-neutral">{{ $benefit->priority }}</span>
                            </td>
                            <td class="text-center">
                                <button type="button"
                                        class="action-btn benefit-toggle-btn {{ $benefit->is_active ? 'action-btn-primary' : 'action-btn-secondary' }}"
                                        data-id="{{ $benefit->id }}"
                                        data-status="{{ $benefit->is_active ? '1' : '0' }}"
                                        title="{{ $benefit->is_active ? 'Active — click to deactivate' : 'Inactive — click to activate' }}"
                                        aria-pressed="{{ $benefit->is_active ? 'true' : 'false' }}">
                                    <i class="fas fa-{{ $benefit->is_active ? 'check' : 'times' }}" aria-hidden="true"></i>
                                </button>
                            </td>
                            <td class="text-center">
                                <div class="affiliates-benefits-table-actions justify-content-center">
                                    <a href="{{ route('affiliates.benefits.show', $benefit->id) }}"
                                       class="action-btn action-btn-secondary"
                                       title="View"
                                       aria-label="View {{ $benefit->name }}">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </a>
                                    <a href="{{ route('affiliates.benefits.edit', $benefit->id) }}"
                                       class="action-btn action-btn-primary"
                                       title="Edit"
                                       aria-label="Edit {{ $benefit->name }}">
                                        <i class="fas fa-edit" aria-hidden="true"></i>
                                    </a>
                                    <form action="{{ route('affiliates.benefits.destroy', $benefit->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this benefit? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-secondary affiliates-btn-danger-soft" title="Delete" aria-label="Delete {{ $benefit->name }}">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-secondary">
                                <i class="fas fa-gift fa-2x mb-3 d-block opacity-50" aria-hidden="true"></i>
                                <p class="mb-2">No benefits found.</p>
                                <a href="{{ route('affiliates.benefits.create') }}" class="action-btn action-btn-primary">Create your first benefit</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($benefits->hasPages())
            <div class="affiliates-pagination-footer">
                {{ $benefits->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.benefit-toggle-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;

            fetch('/affiliates/benefits/' + id + '/toggle-status', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (!data.success) {
                        return;
                    }
                    btn.dataset.status = data.is_active ? '1' : '0';
                    btn.classList.remove('action-btn-primary', 'action-btn-secondary');
                    if (data.is_active) {
                        btn.classList.add('action-btn-primary');
                    } else {
                        btn.classList.add('action-btn-secondary');
                    }
                    btn.setAttribute('aria-pressed', data.is_active ? 'true' : 'false');
                    btn.title = data.is_active ? 'Active — click to deactivate' : 'Inactive — click to activate';
                    btn.innerHTML = '<i class="fas fa-' + (data.is_active ? 'check' : 'times') + '" aria-hidden="true"></i>';
                })
                .catch(function () {
                    alert('Failed to update status');
                });
        });
    });
</script>
@endpush
