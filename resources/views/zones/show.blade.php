@extends('layouts.adminlte')

@section('title', 'Zone Details')
@section('page-title', 'Zone: ' . $zone->zone_name)
@section('breadcrumb', 'Zones / View')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Zone Information</h3>
                        <div class="card-tools">
                            <a href="{{ route('zones.edit', $zone) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <a href="{{ route('zones.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i>Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Zone Name:</dt>
                            <dd class="col-sm-8"><strong>{{ $zone->zone_name }}</strong></dd>

                            <dt class="col-sm-4">Floor Plan:</dt>
                            <dd class="col-sm-8">
                                @if($zone->floorPlan)
                                    <span class="badge badge-info">{{ $zone->floorPlan->name }}</span>
                                @else
                                    <span class="badge badge-secondary">Global</span>
                                @endif
                            </dd>

                            <dt class="col-sm-4">Price:</dt>
                            <dd class="col-sm-8"><strong>${{ number_format($zone->price ?? 0, 2) }}</strong></dd>

                            <dt class="col-sm-4">Size:</dt>
                            <dd class="col-sm-8">{{ $zone->width }} × {{ $zone->height }} pixels</dd>

                            <dt class="col-sm-4">Rotation:</dt>
                            <dd class="col-sm-8">{{ $zone->rotation }}°</dd>

                            <dt class="col-sm-4">Z-Index:</dt>
                            <dd class="col-sm-8">{{ $zone->z_index }}</dd>

                            <dt class="col-sm-4">Border Radius:</dt>
                            <dd class="col-sm-8">{{ $zone->border_radius }}</dd>

                            <dt class="col-sm-4">Border Width:</dt>
                            <dd class="col-sm-8">{{ $zone->border_width }}</dd>

                            <dt class="col-sm-4">Opacity:</dt>
                            <dd class="col-sm-8">{{ $zone->opacity }}</dd>

                            @if($zone->background_color)
                            <dt class="col-sm-4">Background Color:</dt>
                            <dd class="col-sm-8">
                                <span class="badge" style="background-color: {{ $zone->background_color }}; color: white;">
                                    {{ $zone->background_color }}
                                </span>
                            </dd>
                            @endif

                            @if($zone->border_color)
                            <dt class="col-sm-4">Border Color:</dt>
                            <dd class="col-sm-8">
                                <span class="badge" style="background-color: {{ $zone->border_color }}; color: white;">
                                    {{ $zone->border_color }}
                                </span>
                            </dd>
                            @endif

                            <dt class="col-sm-4">Created:</dt>
                            <dd class="col-sm-8">{{ $zone->created_at->format('M d, Y H:i') }}</dd>

                            <dt class="col-sm-4">Last Updated:</dt>
                            <dd class="col-sm-8">{{ $zone->updated_at->format('M d, Y H:i') }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Zone About Section -->
                <div class="card mt-3">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-2"></i>Zone About / Description
                        </h3>
                    </div>
                    <div class="card-body">
                        @if($zone->zone_about)
                            <div class="zone-about-content" style="white-space: pre-wrap; line-height: 1.8;">
                                {{ $zone->zone_about }}
                            </div>
                        @else
                            <p class="text-muted">
                                <i class="fas fa-info-circle mr-1"></i>
                                No description available. 
                                <a href="{{ route('zones.edit', $zone) }}">Add a description</a> to help users understand this zone better.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('zones.edit', $zone) }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-edit mr-1"></i>Edit Zone
                        </a>
                        <a href="{{ route('zones.index') }}" class="btn btn-secondary btn-block mb-2">
                            <i class="fas fa-list mr-1"></i>View All Zones
                        </a>
                        <form action="{{ route('zones.destroy', $zone) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this zone?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash mr-1"></i>Delete Zone
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
