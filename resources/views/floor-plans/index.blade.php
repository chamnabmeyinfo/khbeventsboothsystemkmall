@extends('layouts.adminlte')

@section('title', 'Floor Plan Management')
@section('page-title', 'Floor Plan Management')
@section('breadcrumb', 'Operations / Floor Plans')

@push('styles')
<style>
    .floor-plan-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        border-left: 4px solid #667eea;
        margin-bottom: 16px;
    }

    .floor-plan-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .floor-plan-card.default {
        border-left-color: #f59e0b;
    }

    .kpi-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        transition: all 0.3s;
        height: 100%;
    }

    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
    }

    .kpi-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        margin-bottom: 16px;
    }

    .kpi-card.primary .kpi-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .kpi-card.success .kpi-icon { background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); }
    .kpi-card.info .kpi-icon { background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card primary">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-map"></i>
                    </div>
                    <div class="kpi-label">Total Floor Plans</div>
                    <div class="kpi-value">{{ number_format($floorPlans->total()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card success">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="kpi-label">Active Floor Plans</div>
                    <div class="kpi-value">{{ number_format($floorPlans->where('is_active', true)->count()) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card kpi-card info">
                <div class="card-body" style="padding: 24px;">
                    <div class="kpi-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="kpi-label">Total Booths</div>
                    <div class="kpi-value">{{ number_format($floorPlans->sum('booths_count')) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <a href="{{ route('floor-plans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create Floor Plan
                    </a>
                    <a href="{{ route('booths.index') }}" class="btn btn-info">
                        <i class="fas fa-map-marked-alt mr-1"></i>View Booths
                    </a>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('floor-plans.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control mr-2" placeholder="Search floor plans..." value="{{ request('search') }}">
                        @if($events && $events->count() > 0)
                        <select name="event_id" class="form-control mr-2">
                            <option value="">All Events</option>
                            @foreach($events as $event)
                                @php
                                    $eventId = is_object($event) ? ($event->id ?? null) : ($event['id'] ?? null);
                                    $eventTitle = is_object($event) ? ($event->title ?? 'N/A') : ($event['title'] ?? 'N/A');
                                @endphp
                                @if($eventId)
                                    <option value="{{ $eventId }}" {{ request('event_id') == $eventId ? 'selected' : '' }}>
                                        {{ $eventTitle }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @else
                        <select name="event_id" class="form-control mr-2" disabled title="Events table not available">
                            <option value="">Events Not Available</option>
                        </select>
                        @endif
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Floor Plans List -->
    <div class="row">
        @forelse($floorPlans as $floorPlan)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card floor-plan-card {{ $floorPlan->is_default ? 'default' : '' }}">
                @php
                    // Get floor plan statistics
                    $stats = $floorPlan->getStats();
                    
                    // Get zone settings count (already loaded in controller)
                    $zoneCount = $floorPlan->zone_count ?? 0;
                    
                    // Get canvas settings (already loaded in controller)
                    $canvasSettings = $floorPlan->canvas_settings ?? null;
                @endphp
                
                <!-- Floor Plan Image Preview -->
                @if($floorPlan->floor_image && file_exists(public_path($floorPlan->floor_image)))
                <div style="position: relative; height: 180px; overflow: hidden; border-radius: 12px 12px 0 0; background: #f5f5f5;">
                    <img src="{{ asset($floorPlan->floor_image) }}" 
                         alt="{{ $floorPlan->name }}" 
                         style="width: 100%; height: 100%; object-fit: cover;"
                         onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=\'display:flex;align-items:center;justify-content:center;height:100%;color:#999;\'><i class=\'fas fa-image fa-3x\'></i></div>'">
                    @if($floorPlan->is_default)
                    <div style="position: absolute; top: 8px; right: 8px; background: rgba(245, 158, 11, 0.9); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                        <i class="fas fa-star mr-1"></i>Default
                    </div>
                    @endif
                </div>
                @else
                <div style="height: 180px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; border-radius: 12px 12px 0 0;">
                    <div style="text-align: center; color: white;">
                        <i class="fas fa-map fa-4x mb-2"></i>
                        <div style="font-size: 0.875rem; opacity: 0.9;">No Image</div>
                        @if($floorPlan->is_default)
                        <div style="margin-top: 8px; background: rgba(255, 255, 255, 0.2); padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; display: inline-block;">
                            <i class="fas fa-star mr-1"></i>Default
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div style="flex: 1;">
                            <h5 class="mb-2" style="font-weight: 700;">
                                <i class="fas fa-map text-primary mr-2"></i>{{ $floorPlan->name }}
                            </h5>
                            @if($floorPlan->project_name)
                                <p class="text-muted mb-1" style="font-size: 0.875rem;">
                                    <i class="fas fa-project-diagram mr-1"></i>{{ $floorPlan->project_name }}
                                </p>
                            @endif
                            @if($floorPlan->event_id)
                                @php
                                    $eventTitle = null;
                                    try {
                                        $eventData = \Illuminate\Support\Facades\DB::selectOne(
                                            'SELECT title FROM events WHERE id = ? AND status = 1',
                                            [$floorPlan->event_id]
                                        );
                                        $eventTitle = $eventData ? ($eventData->title ?? null) : null;
                                    } catch (\Exception $e) {
                                        $eventTitle = null;
                                    }
                                @endphp
                                @if($eventTitle)
                                    <p class="text-muted mb-1" style="font-size: 0.875rem;">
                                        <i class="fas fa-calendar-alt mr-1"></i>{{ $eventTitle }}
                                    </p>
                                @elseif($floorPlan->event_id)
                                    <p class="text-muted mb-1" style="font-size: 0.875rem;">
                                        <i class="fas fa-calendar-alt mr-1"></i>Event ID: {{ $floorPlan->event_id }}
                                    </p>
                                @endif
                            @endif
                            
                            <!-- Floor Plan Settings & Statistics -->
                            <div class="mt-3 mb-2" style="background: #f8f9fa; padding: 12px; border-radius: 8px;">
                                <div class="row text-center" style="font-size: 0.8125rem;">
                                    <div class="col-4">
                                        <div style="font-weight: 600; color: #495057;">Canvas</div>
                                        <div style="color: #6c757d; font-size: 0.75rem;">
                                            {{ $floorPlan->canvas_width ?? $canvasSettings->canvas_width ?? 1200 }}×{{ $floorPlan->canvas_height ?? $canvasSettings->canvas_height ?? 800 }}
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div style="font-weight: 600; color: #495057;">Zones</div>
                                        <div style="color: #6c757d; font-size: 0.75rem;">{{ $zoneCount }}</div>
                                    </div>
                                    <div class="col-4">
                                        <div style="font-weight: 600; color: #495057;">Booths</div>
                                        <div style="color: #6c757d; font-size: 0.75rem;">{{ $floorPlan->booths_count ?? 0 }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Booth Status Summary -->
                            @if($stats['total'] > 0)
                            <div class="mt-2">
                                <div class="d-flex flex-wrap gap-1" style="font-size: 0.75rem;">
                                    <span class="badge badge-success">{{ $stats['available'] }} Available</span>
                                    <span class="badge badge-warning">{{ $stats['reserved'] }} Reserved</span>
                                    <span class="badge badge-info">{{ $stats['confirmed'] }} Confirmed</span>
                                    <span class="badge badge-primary">{{ $stats['paid'] }} Paid</span>
                                    @if($stats['occupancy_rate'] > 0)
                                    <span class="badge badge-secondary" style="background: #6c757d;">{{ $stats['occupancy_rate'] }}% Occupied</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            @if($floorPlan->description)
                                <p class="text-muted mt-2 mb-0" style="font-size: 0.8125rem; line-height: 1.4;">
                                    {{ Str::limit($floorPlan->description, 80) }}
                                </p>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="mt-2">
                                @if($floorPlan->is_active)
                                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Active</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-times-circle mr-1"></i>Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="btn-group btn-group-sm w-100" role="group">
                        <a href="{{ route('booths.index', ['floor_plan_id' => $floorPlan->id]) }}" class="btn btn-primary">
                            <i class="fas fa-eye mr-1"></i>View Booths
                        </a>
                        <a href="{{ route('floor-plans.show', $floorPlan) }}" class="btn btn-info">
                            <i class="fas fa-info-circle"></i>
                        </a>
                        <a href="{{ route('floor-plans.edit', $floorPlan) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if(!$floorPlan->is_default)
                            <form action="{{ route('floor-plans.set-default', $floorPlan) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-secondary" title="Set as Default">
                                    <i class="fas fa-star"></i>
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('floor-plans.duplicate', $floorPlan) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" title="Duplicate">
                                <i class="fas fa-copy"></i>
                            </button>
                        </form>
                        @if(!$floorPlan->is_default)
                            <button type="button" class="btn btn-danger" 
                                    onclick="deleteFloorPlan({{ $floorPlan->id }}, '{{ addslashes($floorPlan->name) }}', {{ $floorPlan->booths_count }})"
                                    title="Delete Floor Plan">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-map fa-3x text-muted mb-3"></i>
                    <h5>No Floor Plans Found</h5>
                    <p class="text-muted">Create your first floor plan to get started.</p>
                    <a href="{{ route('floor-plans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>Create Floor Plan
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($floorPlans->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $floorPlans->links() }}
    </div>
    @endif

    <!-- Delete Floor Plan Modal -->
    <div class="modal fade" id="deleteFloorPlanModal" tabindex="-1" role="dialog" aria-labelledby="deleteFloorPlanModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteFloorPlanModalLabel">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Delete Floor Plan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="deleteFloorPlanForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-3">
                            Are you sure you want to delete <strong id="deleteFloorPlanName"></strong>?
                        </p>
                        
                        <div id="boothActionSection" style="display: none;">
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-info-circle mr-2"></i>
                                This floor plan has <strong id="boothCountText"></strong> booth(s). What would you like to do with them?
                            </div>
                            
                            <div class="form-group">
                                <label>Action for booths:</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="booth_action" id="boothActionDelete" value="delete" checked>
                                    <label class="form-check-label" for="boothActionDelete">
                                        <strong>Delete all booths</strong> (This will also delete all bookings associated with these booths)
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="booth_action" id="boothActionMove" value="move">
                                    <label class="form-check-label" for="boothActionMove">
                                        <strong>Move booths to another floor plan</strong>
                                    </label>
                                </div>
                                
                                <div id="targetFloorPlanSelect" style="display: none;">
                                    <label for="target_floor_plan_id">Select target floor plan:</label>
                                    <select class="form-control" name="target_floor_plan_id" id="target_floor_plan_id">
                                        <option value="">-- Select Floor Plan --</option>
                                        @foreach($floorPlans as $fp)
                                            <option value="{{ $fp->id }}" data-floor-plan-id="{{ $fp->id }}">{{ $fp->name }} ({{ $fp->booths_count }} booths)</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">All booths will be moved to the selected floor plan.</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Warning:</strong> This action cannot be undone!
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i>Delete Floor Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentFloorPlanId = null;

function deleteFloorPlan(floorPlanId, floorPlanName, boothCount) {
    currentFloorPlanId = floorPlanId;
    
    // Set form action
    document.getElementById('deleteFloorPlanForm').action = '{{ url("/floor-plans") }}/' + floorPlanId;
    
    // Set floor plan name
    document.getElementById('deleteFloorPlanName').textContent = floorPlanName;
    
    // Handle booth action section
    const boothActionSection = document.getElementById('boothActionSection');
    const targetFloorPlanSelect = document.getElementById('targetFloorPlanSelect');
    const targetFloorPlanId = document.getElementById('target_floor_plan_id');
    const boothActionMove = document.getElementById('boothActionMove');
    const boothActionDelete = document.getElementById('boothActionDelete');
    
    // Filter out current floor plan from options
    const options = targetFloorPlanId.querySelectorAll('option[data-floor-plan-id]');
    options.forEach(function(option) {
        const optionId = parseInt(option.getAttribute('data-floor-plan-id'));
        if (optionId === floorPlanId) {
            option.style.display = 'none';
            option.disabled = true;
            option.selected = false;
        } else {
            option.style.display = '';
            option.disabled = false;
        }
    });
    
    // Set default option as selected
    targetFloorPlanId.querySelector('option[value=""]').selected = true;
    
    if (boothCount > 0) {
        boothActionSection.style.display = 'block';
        document.getElementById('boothCountText').textContent = boothCount;
        
        // Reset radio buttons
        boothActionDelete.checked = true;
        boothActionMove.checked = false;
        targetFloorPlanSelect.style.display = 'none';
        targetFloorPlanId.required = false;
        targetFloorPlanId.value = '';
    } else {
        boothActionSection.style.display = 'none';
        targetFloorPlanId.value = '';
        targetFloorPlanId.required = false;
    }
    
    // Show modal
    $('#deleteFloorPlanModal').modal('show');
}

// Handle radio button changes (using event delegation)
$(document).on('change', '#boothActionMove', function() {
    if (this.checked) {
        $('#targetFloorPlanSelect').show();
        $('#target_floor_plan_id').prop('required', true);
    }
});

$(document).on('change', '#boothActionDelete', function() {
    if (this.checked) {
        $('#targetFloorPlanSelect').hide();
        $('#target_floor_plan_id').prop('required', false);
        $('#target_floor_plan_id').val('');
    }
});

// Validate form before submit
$(document).on('submit', '#deleteFloorPlanForm', function(e) {
    const boothActionMove = $('#boothActionMove').is(':checked');
    const targetFloorPlanId = $('#target_floor_plan_id').val();
    
    if (boothActionMove) {
        if (!targetFloorPlanId) {
            e.preventDefault();
            alert('Please select a target floor plan to move booths to.');
            $('#target_floor_plan_id').focus();
            return false;
        }
        
        if (parseInt(targetFloorPlanId) === currentFloorPlanId) {
            e.preventDefault();
            alert('Cannot move booths to the same floor plan. Please select a different floor plan.');
            $('#target_floor_plan_id').focus();
            return false;
        }
    }
});
</script>
@endpush
@endsection
