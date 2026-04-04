@extends('layouts.app')

@section('title', 'Floor Plan Details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard-looker.css') }}?v=3.6">
@endpush

@push('body-class', 'ios-dashboard-mode')

@section('content')
<div class="looker-dashboard">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-map mr-1"></i>{{ $floorPlan->name }}
                        @if($floorPlan->is_default)
                            <span class="badge badge-warning ml-2">Default</span>
                        @endif
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('booths.index', ['view' => 'canvas', 'floor_plan_id' => $floorPlan->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-paint-brush mr-1"></i>Design Canvas
                        </a>
                        <a href="{{ route('floor-plans.edit', $floorPlan) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <a href="{{ route('floor-plans.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Project Name:</dt>
                        <dd class="col-sm-9">{{ $floorPlan->project_name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Event:</dt>
                        <dd class="col-sm-9">
                            @if($floorPlan->event_id)
                                @php
                                    $eventTitle = null;
                                    try {
                                        // Try to get event title using DB::select (safer than model)
                                        $eventData = \Illuminate\Support\Facades\DB::selectOne(
                                            'SELECT id, title FROM events WHERE id = ? AND status = 1',
                                            [$floorPlan->event_id]
                                        );
                                        $eventTitle = $eventData ? ($eventData->title ?? null) : null;
                                        $eventId = $eventData ? ($eventData->id ?? null) : null;
                                    } catch (\Exception $e) {
                                        // Events table doesn't exist or query failed
                                        $eventTitle = null;
                                        $eventId = null;
                                    }
                                @endphp
                                @if($eventTitle && $eventId)
                                    @if(\Illuminate\Support\Facades\Route::has('admin.events.show'))
                                        <a href="{{ route('admin.events.show', $eventId) }}">{{ $eventTitle }}</a>
                                    @else
                                        <span>{{ $eventTitle }}</span>
                                    @endif
                                @elseif($floorPlan->event_id)
                                    <span class="text-muted">Event ID: {{ $floorPlan->event_id }} (Events table not available)</span>
                                @endif
                            @else
                                <span class="text-muted">No Event</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Description:</dt>
                        <dd class="col-sm-9">{{ $floorPlan->description ?? 'No description' }}</dd>

                        <dt class="col-sm-3">Canvas Size:</dt>
                        <dd class="col-sm-9">{{ $floorPlan->canvas_width }} x {{ $floorPlan->canvas_height }} px</dd>

                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            @if($floorPlan->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </dd>

                        <dt class="col-sm-3">Created By:</dt>
                        <dd class="col-sm-9">{{ $floorPlan->createdBy ? $floorPlan->createdBy->username : 'System' }}</dd>

                        <dt class="col-sm-3">Created At:</dt>
                        <dd class="col-sm-9">{{ $floorPlan->created_at ? $floorPlan->created_at->format('Y-m-d H:i:s') : 'N/A' }}</dd>

                        <dt class="col-sm-3">Updated At:</dt>
                        <dd class="col-sm-9">{{ $floorPlan->updated_at ? $floorPlan->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</dd>
                    </dl>

                    <div class="mt-3">
                        <h5>Floor Plan Image</h5>
                        @php
                            $imageExists = false;
                            $imagePath = null;
                            if ($floorPlan->floor_image) {
                                $fullPath = public_path($floorPlan->floor_image);
                                if (file_exists($fullPath)) {
                                    $imageExists = true;
                                    $imagePath = $floorPlan->floor_image;
                                }
                            }
                        @endphp
                        
                        @if($imageExists)
                            <img src="{{ asset($imagePath) }}" 
                                 alt="Floor plan" 
                                 class="img-fluid rounded" 
                                 style="max-height: 400px; max-width: 100%;"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23ddd\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3EImage not found%3C/text%3E%3C/svg%3E';">
                            <p class="text-muted small mt-2">
                                <i class="fas fa-image mr-1"></i>Image path: <code>{{ $imagePath }}</code>
                            </p>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                <strong>No floor plan image available</strong>
                                @if($floorPlan->floor_image)
                                    <p class="mb-0 small mt-1">
                                        Image path in database: <code>{{ $floorPlan->floor_image }}</code><br>
                                        <span class="text-danger">⚠️ File not found at this path.</span>
                                    </p>
                                @else
                                    <p class="mb-0 small mt-1">No image has been uploaded for this floor plan yet.</p>
                                @endif
                                <p class="mb-0 small mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    To add a floor plan image, go to the <strong>Booth Editor</strong> and use the "Upload Floorplan" option.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>Statistics
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-store"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Booths</span>
                                    <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Available</span>
                                    <span class="info-box-number">{{ $stats['available'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Reserved</span>
                                    <span class="info-box-number">{{ $stats['reserved'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Paid</span>
                                    <span class="info-box-number">{{ $stats['paid'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <strong>Occupancy Rate:</strong>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ $stats['occupancy_rate'] ?? 0 }}%"
                                 aria-valuenow="{{ $stats['occupancy_rate'] ?? 0 }}" 
                                 aria-valuemin="0" aria-valuemax="100">
                                {{ $stats['occupancy_rate'] ?? 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog mr-1"></i>Actions
                    </h3>
                </div>
                <div class="card-body">
                    @if(!$floorPlan->is_default)
                        <form action="{{ route('floor-plans.set-default', $floorPlan) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fas fa-star mr-1"></i>Set as Default
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('floor-plans.duplicate', $floorPlan) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-copy mr-1"></i>Duplicate Floor Plan
                        </button>
                    </form>
                    @if(!$floorPlan->is_default)
                        <button type="button" class="btn btn-danger btn-block" 
                                onclick="deleteFloorPlan({{ $floorPlan->id }}, '{{ addslashes($floorPlan->name) }}', {{ $stats['total'] ?? 0 }})">
                            <i class="fas fa-trash mr-1"></i>Delete Floor Plan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<div class="modal fade" id="deleteFloorPlanModal" tabindex="-1" role="dialog" aria-labelledby="deleteFloorPlanModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteFloorPlanModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Delete Floor Plan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteFloorPlanForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="mb-3">
                        Are you sure you want to delete <strong id="deleteFloorPlanName" data-fp-delete-name></strong>?
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
                                    @php
                                        $otherFloorPlans = \App\Models\FloorPlan::where('is_active', true)
                                            ->where('id', '!=', $floorPlan->id)
                                            ->where('is_default', false)
                                            ->orderBy('name', 'asc')
                                            ->get();
                                    @endphp
                                    @foreach($otherFloorPlans as $fp)
                                        <option value="{{ $fp->id }}">{{ $fp->name }} ({{ $fp->booths()->count() }} booths)</option>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i>Delete Floor Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
function deleteFloorPlan(floorPlanId, floorPlanName, boothCount) {
    var modalEl = document.getElementById('deleteFloorPlanModal');
    if (!modalEl) {
        console.error('deleteFloorPlanModal not in DOM');
        alert('Delete dialog could not be opened. Please refresh the page.');
        return;
    }
    var formEl = modalEl.querySelector('#deleteFloorPlanForm');
    var nameEl = modalEl.querySelector('[data-fp-delete-name]') || modalEl.querySelector('#deleteFloorPlanName');
    if (!formEl || !nameEl) {
        console.error('Delete modal form/name missing');
        alert('Delete dialog could not be opened. Please refresh the page.');
        return;
    }

    formEl.action = '{{ url("/floor-plans") }}/' + floorPlanId;
    nameEl.textContent = floorPlanName;

    var boothActionSection = document.getElementById('boothActionSection');
    var targetFloorPlanSelect = document.getElementById('targetFloorPlanSelect');
    var targetFloorPlanId = document.getElementById('target_floor_plan_id');
    var boothActionDelete = document.getElementById('boothActionDelete');
    var boothActionMove = document.getElementById('boothActionMove');

    if (boothCount > 0 && boothActionSection && boothActionDelete && boothActionMove && targetFloorPlanSelect && targetFloorPlanId) {
        boothActionSection.style.display = 'block';
        var boothCountEl = document.getElementById('boothCountText');
        if (boothCountEl) {
            boothCountEl.textContent = boothCount;
        }
        boothActionDelete.checked = true;
        boothActionMove.checked = false;
        targetFloorPlanSelect.style.display = 'none';
        targetFloorPlanId.required = false;
        targetFloorPlanId.value = '';
    } else if (boothActionSection) {
        boothActionSection.style.display = 'none';
        if (targetFloorPlanId) {
            targetFloorPlanId.required = false;
        }
    }

    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else if (typeof window.jQuery !== 'undefined' && window.jQuery.fn && window.jQuery.fn.modal) {
        window.jQuery(modalEl).modal('show');
    } else {
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        document.body.classList.add('modal-open');
    }
}

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

$(document).on('submit', '#deleteFloorPlanForm', function(e) {
    var boothActionMove = document.getElementById('boothActionMove');
    var targetFloorPlanId = document.getElementById('target_floor_plan_id');
    if (boothActionMove && boothActionMove.checked && targetFloorPlanId) {
        if (!targetFloorPlanId.value) {
            e.preventDefault();
            alert('Please select a target floor plan to move booths to.');
            targetFloorPlanId.focus();
            return false;
        }
    }
});
</script>
@endpush
@endsection

