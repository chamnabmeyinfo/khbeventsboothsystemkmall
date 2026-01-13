@extends('layouts.adminlte')

@section('title', 'Affiliate Benefits Management')
@section('page-title', 'Affiliate Benefits')
@section('breadcrumb', 'Sales / Affiliates / Benefits')

@push('styles')
<style>
    .benefit-card {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-left: 4px solid;
        transition: all 0.2s;
    }

    .benefit-card:hover {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .benefit-card.commission { border-left-color: #667eea; }
    .benefit-card.bonus { border-left-color: #43e97b; }
    .benefit-card.incentive { border-left-color: #4facfe; }
    .benefit-card.reward { border-left-color: #fa709a; }

    .benefit-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-commission { background: rgba(102, 126, 234, 0.1); color: #667eea; }
    .badge-bonus { background: rgba(67, 233, 123, 0.1); color: #28a745; }
    .badge-incentive { background: rgba(79, 172, 254, 0.1); color: #17a2b8; }
    .badge-reward { background: rgba(250, 112, 154, 0.1); color: #dc3545; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0" style="font-weight: 700;">
                <i class="fas fa-gift mr-2"></i>Affiliate Benefits Management
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.875rem;">Configure commission, bonuses, and rewards for sales team</p>
        </div>
        <a href="{{ route('affiliates.benefits.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Create New Benefit
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('affiliates.benefits.index') }}" class="row g-2">
                <div class="col-md-3">
                    <label class="form-label small">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Search benefits..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Type</label>
                    <select name="type" class="form-control form-control-sm">
                        <option value="">All Types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select name="is_active" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('affiliates.benefits.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Benefits List -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Benefit Name</th>
                            <th>Type</th>
                            <th>Calculation</th>
                            <th>Value</th>
                            <th>Targets</th>
                            <th>Applies To</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($benefits as $benefit)
                        <tr>
                            <td>
                                <strong>{{ $benefit->name }}</strong>
                                @if($benefit->description)
                                    <br><small class="text-muted">{{ Str::limit($benefit->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="benefit-badge badge-{{ $benefit->type }}">
                                    {{ ucfirst($benefit->type) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $benefit->calculation_method)) }}</small>
                            </td>
                            <td>
                                @if($benefit->calculation_method === 'percentage')
                                    <strong>{{ number_format($benefit->percentage, 2) }}%</strong>
                                @elseif($benefit->calculation_method === 'fixed_amount')
                                    <strong>${{ number_format($benefit->fixed_amount, 2) }}</strong>
                                @else
                                    <small class="text-muted">Tiered</small>
                                @endif
                            </td>
                            <td>
                                @if($benefit->target_revenue)
                                    <small>Revenue: ${{ number_format($benefit->target_revenue, 0) }}</small><br>
                                @endif
                                @if($benefit->target_bookings)
                                    <small>Bookings: {{ $benefit->target_bookings }}</small><br>
                                @endif
                                @if($benefit->target_clients)
                                    <small>Clients: {{ $benefit->target_clients }}</small>
                                @endif
                                @if(!$benefit->target_revenue && !$benefit->target_bookings && !$benefit->target_clients)
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($benefit->user)
                                    <small>User: {{ $benefit->user->username }}</small><br>
                                @endif
                                @if($benefit->floorPlan)
                                    <small>Floor Plan: {{ $benefit->floorPlan->name }}</small>
                                @endif
                                @if(!$benefit->user && !$benefit->floorPlan)
                                    <span class="text-muted">All</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $benefit->priority }}</span>
                            </td>
                            <td>
                                <button type="button" 
                                        class="btn btn-sm btn-toggle-status {{ $benefit->is_active ? 'btn-success' : 'btn-secondary' }}"
                                        data-id="{{ $benefit->id }}"
                                        data-status="{{ $benefit->is_active }}">
                                    <i class="fas fa-{{ $benefit->is_active ? 'check' : 'times' }}"></i>
                                </button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('affiliates.benefits.show', $benefit->id) }}" 
                                       class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('affiliates.benefits.edit', $benefit->id) }}" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('affiliates.benefits.destroy', $benefit->id) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this benefit?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-gift fa-2x mb-2 d-block"></i>
                                No benefits found. <a href="{{ route('affiliates.benefits.create') }}">Create your first benefit</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($benefits->hasPages())
        <div class="card-footer">
            {{ $benefits->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-toggle-status').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const currentStatus = this.dataset.status === '1';
            
            fetch(`/affiliates/benefits/${id}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.dataset.status = data.is_active ? '1' : '0';
                    this.className = `btn btn-sm btn-toggle-status ${data.is_active ? 'btn-success' : 'btn-secondary'}`;
                    this.innerHTML = `<i class="fas fa-${data.is_active ? 'check' : 'times'}"></i>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update status');
            });
        });
    });
</script>
@endpush
