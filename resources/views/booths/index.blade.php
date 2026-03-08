@extends('layouts.app')

@section('title', isset($currentFloorPlan) && $currentFloorPlan ? (e($currentFloorPlan->name) . ' — Floor Plan') : 'Floor Plan Management')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/floor-plan-designer.css') }}?v={{ config('app.asset_version') ?? filemtime(public_path('css/floor-plan-designer.css')) }}">
<style>

@if($showBookedTick ?? true)
@php
    $sizeMode = $bookedTickSizeMode ?? 'fixed';
    $tickSize = $bookedTickSize ?? 'medium';
    $tickSizePx = $tickSize === 'small' ? 10 : ($tickSize === 'large' ? 16 : 12);
    $tickBox = $tickSizePx + 4;
    $relativePercent = (string) ($bookedTickRelativePercent ?? '12');
    $tickRadius = ($bookedTickShape ?? 'round') === 'round' ? '50%' : '2px';
    $pos = $bookedTickPosition ?? 'top-right';
    $ins = $pos === 'inside';
    $tickTop = $ins ? '2px' : (in_array($pos, ['top-right', 'top-left'], true) ? '-8px' : 'auto');
    $tickRight = $ins ? '2px' : (in_array($pos, ['top-right', 'bottom-right'], true) ? '-8px' : 'auto');
    $tickBottom = $ins ? 'auto' : (in_array($pos, ['bottom-right', 'bottom-left'], true) ? '-8px' : 'auto');
    $tickLeft = $ins ? 'auto' : (in_array($pos, ['top-left', 'bottom-left'], true) ? '-8px' : 'auto');
    $tickColor = e($bookedTickColor ?? '#28a745');
    $tickAnim = ($bookedTickAnimation ?? 'pulse') === 'pulse' ? 'bookedIconPulse 2s ease-in-out infinite' : 'none';
    $tickBg = ($bookedTickBgColor ?? '') === '' || ($bookedTickBgColor ?? '') === 'transparent' ? 'transparent' : e($bookedTickBgColor);
    $tickFontSize = $bookedTickFontSize ?? 'medium';
    $tickFontSizePx = $tickFontSize === 'small' ? 10 : ($tickFontSize === 'large' ? 16 : 12);
    $tickBorderW = $bookedTickBorderWidth ?? '0';
    $tickBorderC = $tickBorderW !== '0' ? e($bookedTickBorderColor ?? '#ffffff') : 'transparent';
@endphp
/* Booked tick: customizable color, font size, box size (fixed or relative to booth), shape, position, background, border (canvas + sidebar). */
.booth-number-item.booked::after,
.dropped-booth.booked::after {
    content: '✓';
    font-family: inherit;
    font-weight: bold;
    position: absolute;
    top: {{ $tickTop }};
    right: {{ $tickRight }};
    bottom: {{ $tickBottom }};
    left: {{ $tickLeft }};
    @if($sizeMode === 'relative')
    width: {{ $relativePercent }}%;
    height: {{ $relativePercent }}%;
    min-width: 12px;
    min-height: 12px;
    font-size: 0.55em;
    @else
    width: {{ $tickBox }}px;
    height: {{ $tickBox }}px;
    font-size: {{ $tickFontSizePx }}px;
    @endif
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    border-radius: {{ $tickRadius }};
    color: {{ $tickColor }};
    background-color: {{ $tickBg }};
    border-width: {{ $tickBorderW }}px;
    border-style: solid;
    border-color: {{ $tickBorderC }};
    z-index: 99999;
    animation: {{ $tickAnim }};
}
@endif

/* Dynamic status colors from database */
@if(isset($statusSettings) && $statusSettings->count() > 0)
                @foreach($statusSettings as $status)
                    /* Status colors - only apply if booth doesn't have custom colors */
                    .dropped-booth.status-{{ $status->status_code }}:not(.has-custom-colors) { 
                        background: {{ $status->status_color }} !important; 
                        border-color: {{ $status->border_color ?? $status->status_color }} !important; 
                        border-width: {{ $status->border_width ?? 2 }}px !important;
                        border-style: {{ $status->border_style ?? 'solid' }} !important;
                        border-radius: {{ $status->border_radius ?? 4 }}px !important;
                        color: {{ $status->text_color }} !important; 
                    }
                @endforeach
@else
    {{-- Fallback to defaults if no custom statuses --}}
/* Status 1 (Available) - custom colors can override */
.dropped-booth.status-1:not(.has-custom-colors) { background: #28a745 !important; border-color: #28a745 !important; color: #ffffff !important; }
.dropped-booth.status-2:not(.has-custom-colors) { background: #0dcaf0 !important; border-color: #0dcaf0 !important; color: #ffffff !important; }
.dropped-booth.status-3:not(.has-custom-colors) { background: #ffc107 !important; border-color: #ffc107 !important; color: #333333 !important; }
.dropped-booth.status-4:not(.has-custom-colors) { background: #6c757d !important; border-color: #6c757d !important; color: #ffffff !important; }
.dropped-booth.status-5:not(.has-custom-colors) { background: #212529 !important; border-color: #212529 !important; color: #ffffff !important; }
@endif

</style>
@endpush

@section('content')
<div class="container-fluid mt-2 mb-2">
    @if(!isset($currentFloorPlan) || !$currentFloorPlan)
    {{-- No floor plan chosen: show message at top and list at bottom; all other UI is hidden/blocked --}}
    <div class="no-floorplan-gate" role="alert" aria-live="polite">
        <div class="no-floorplan-message">
            <i class="fas fa-map-marked-alt" aria-hidden="true"></i>
            <span>Please choose a floor plan to continue. Buttons and tools are unavailable until you select one below.</span>
        </div>
        <div class="no-floorplan-list">
            <p class="no-floorplan-list-title"><i class="fas fa-list mr-1"></i>Choose a floor plan</p>
            @if(isset($floorPlans) && $floorPlans->count() > 0)
            <div class="no-floorplan-list-items">
                @foreach($floorPlans as $fp)
                <a href="{{ route('booths.index', ['view' => 'canvas', 'floor_plan_id' => $fp->id]) }}" class="no-floorplan-item">
                    <strong>{{ $fp->name }}</strong>
                    @if($fp->is_default)<small>(Default)</small>@endif
                    @if($fp->event)<small> — {{ $fp->event->title }}</small>@endif
                    @if($fp->project_name)<small> — {{ $fp->project_name }}</small>@endif
                </a>
                @endforeach
            </div>
            @else
            <div class="no-floorplan-empty">
                <p class="mb-2">No floor plans are available.</p>
                <a href="{{ route('floor-plans.index') }}">Create or manage floor plans</a>
            </div>
            @endif
        </div>
    </div>
    @else
    <!-- Floor Plan Selector -->
    @if(isset($floorPlans) && $floorPlans->count() > 0)
    <div class="card mb-3" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.18); box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="mb-0" style="font-weight: 600; color: #2d3748;">
                        <i class="fas fa-map mr-2 text-primary"></i>Select Floor Plan:
                    </label>
                </div>
                <div class="col-md-6">
                    <select id="floorPlanSelector" class="form-control" onchange="switchFloorPlan(this.value)" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                        @foreach($floorPlans as $fp)
                            <option value="{{ $fp->id }}" {{ (isset($currentFloorPlan) && $currentFloorPlan && $currentFloorPlan->id == $fp->id) || (isset($floorPlanId) && $floorPlanId == $fp->id) ? 'selected' : '' }}>
                                {{ $fp->name }}
                                @if($fp->is_default) (Default) @endif
                                @if($fp->event) - {{ $fp->event->title }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-right">
                    <a href="{{ route('booths.index', ['view' => 'table']) }}" class="btn btn-sm btn-success mr-1" title="Booth Management">
                        <i class="fas fa-table mr-1"></i>Management
                    </a>
                    <a href="{{ route('floor-plans.index') }}" class="btn btn-sm btn-info" title="Manage Floor Plans">
                        <i class="fas fa-cog mr-1"></i>Floor Plans
                    </a>
                </div>
            </div>
            @if(isset($currentFloorPlan) && $currentFloorPlan)
            <div class="row mt-2">
                <div class="col-12">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Current: <strong>{{ $currentFloorPlan->name }}</strong>
                        @if($currentFloorPlan->project_name) | Project: {{ $currentFloorPlan->project_name }} @endif
                        @if($currentFloorPlan->event) | Event: {{ $currentFloorPlan->event->title }} @endif
                        | <i class="fas fa-th-large mr-1"></i>Booths on canvas: <strong>{{ $totalBooths ?? 0 }}</strong>
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    @auth
    <!-- Statistics Dashboard -->
    @endauth

    <!-- Advanced Floor Plan Designer -->
    
    <!-- Error Log Panel (Hidden by default) -->
    <div id="errorLogPanel" style="display: none; position: fixed; top: 60px; right: 20px; width: 450px; max-height: 600px; background: white; border: 2px solid #ff4444; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.3); z-index: 10000; overflow: hidden;">
        <div style="background: #ff4444; color: white; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Error Log</strong>
                <span id="errorLogBadge" style="background: rgba(255,255,255,0.3); padding: 2px 8px; border-radius: 10px; font-size: 11px;">0</span>
            </div>
            <div style="display: flex; gap: 8px;">
                <button onclick="ErrorLogger.exportLogs(); return false;" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;" title="Export Logs">
                    <i class="fas fa-download"></i>
                </button>
                <button onclick="ErrorLogger.clearLogs(); return false;" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;" title="Clear Logs">
                    <i class="fas fa-trash"></i>
                </button>
                <button onclick="ErrorLogger.togglePanel(); return false;" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 11px;" title="Close">
                    <i class="fas fa-times"></i>
            </button>
        </div>
                    </div>
        <div id="errorLogList" style="max-height: 540px; overflow-y: auto; padding: 10px;">
            <div style="padding: 20px; text-align: center; color: #888;">No errors logged</div>
                </div>
                    </div>
    
    <div class="floorplan-designer">
        <!-- Canvas loading overlay: hidden once floor plan and booths are ready -->
        <div id="canvasLoadingOverlay" class="canvas-loading-overlay" aria-live="polite" aria-busy="true">
            <div class="canvas-loading-content">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"><span class="sr-only">Loading...</span></div>
                <p class="mt-3 mb-0 font-weight-bold">Loading floor plan…</p>
                <p class="small text-muted">Setting up canvas and booths</p>
            </div>
        </div>
        <!-- Toolbar -->
        <div class="designer-toolbar">
            <div class="toolbar-section">
                <!-- Design Tools -->
                <div class="tool-group" title="Design Tools">
                    <button class="toolbar-btn active" id="btnSelectTool" title="Select Tool (V)" data-tool="select">
                    <i class="fas fa-mouse-pointer"></i>
                        <span class="tool-label">Select</span>
            </button>
                    <button class="toolbar-btn" id="btnPanTool" title="Pan Tool (H)" data-tool="pan">
                    <i class="fas fa-hand-paper"></i>
                        <span class="tool-label">Pan</span>
            </button>
                    <button class="toolbar-btn" id="btnZoomTool" title="Zoom Tool (Z)" data-tool="zoom">
                        <i class="fas fa-search-plus"></i>
                        <span class="tool-label">Zoom</span>
                    </button>
                </div>
                <div class="toolbar-divider"></div>
                <div class="tool-group" title="Alignment Tools">
                    <div class="dropdown">
                        <button class="toolbar-btn" id="btnAlignTool" title="Align Tool (A)" data-tool="align" data-toggle="dropdown">
                            <i class="fas fa-align-center"></i>
                            <span class="tool-label">Align</span>
                            <i class="fas fa-caret-down" style="font-size: 8px; margin-left: 2px;"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="FloorPlanDesigner.alignBooths('left'); return false;">
                                <i class="fas fa-align-left"></i> Align Left
                            </a>
                            <a class="dropdown-item" href="#" onclick="FloorPlanDesigner.alignBooths('right'); return false;">
                                <i class="fas fa-align-right"></i> Align Right
                            </a>
                            <a class="dropdown-item" href="#" onclick="FloorPlanDesigner.alignBooths('top'); return false;">
                                <i class="fas fa-align-center"></i> Align Top
                            </a>
                            <a class="dropdown-item" href="#" onclick="FloorPlanDesigner.alignBooths('bottom'); return false;">
                                <i class="fas fa-align-center"></i> Align Bottom
                            </a>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="toolbar-btn" id="btnDistributeTool" title="Distribute Tool (D)" data-tool="distribute" data-toggle="dropdown">
                            <i class="fas fa-arrows-alt-h"></i>
                            <span class="tool-label">Distribute</span>
                            <i class="fas fa-caret-down" style="font-size: 8px; margin-left: 2px;"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#" onclick="FloorPlanDesigner.distributeBooths('horizontal'); return false;">
                                <i class="fas fa-arrows-alt-h"></i> Distribute Horizontally
                            </a>
                            <a class="dropdown-item" href="#" onclick="FloorPlanDesigner.distributeBooths('vertical'); return false;">
                                <i class="fas fa-arrows-alt-v"></i> Distribute Vertically
                            </a>
                        </div>
                    </div>
                    <button class="toolbar-btn" id="btnSelectAll" title="Select All Booths on Canvas" onclick="FloorPlanDesigner.selectAllBoothsOnCanvas(); return false;">
                        <i class="fas fa-object-group"></i>
                        <span class="tool-label">Select All</span>
                    </button>
                    <button class="toolbar-btn" id="btnMeasureTool" title="Measure Tool (M)" data-tool="measure">
                        <i class="fas fa-ruler"></i>
                        <span class="tool-label">Measure</span>
                    </button>
                    <button class="toolbar-btn" id="btnTextTool" title="Text Tool (T) - Click on canvas to add text" data-tool="text">
                        <i class="fas fa-font"></i>
                        <span class="tool-label">Text</span>
                    </button>
                </div>
                <div class="toolbar-divider"></div>
                <button class="toolbar-btn" id="btnGrid" title="Toggle Grid (Show/Hide)" data-toggle="grid">
                    <i class="fas fa-th"></i>
            </button>
                <button class="toolbar-btn active" id="btnSnap" title="Snap to Grid" data-toggle="snap">
                    <i class="fas fa-magnet"></i>
            </button>
                <div class="toolbar-divider"></div>
                @if(isset($canEditCanvas) && $canEditCanvas)
                <!-- Center Marker Toggle -->
                <button class="toolbar-btn" id="btnCenter" title="Toggle Canvas Center Marker">
                    <i class="fas fa-crosshairs"></i>
                </button>
                <div class="toolbar-divider"></div>
                <!-- Rotation Controls -->
                <button class="toolbar-btn" id="btnRotateLeft" title="Rotate Left (-90°) [Shift+R]">
                    <i class="fas fa-undo"></i>
            </button>
                <button class="toolbar-btn" id="btnRotateRight" title="Rotate Right (+90°) [R]">
                    <i class="fas fa-redo"></i>
            </button>
                <div class="toolbar-divider"></div>
                <button class="toolbar-btn" id="btnUndo" title="Undo (Ctrl+Z)">
                    <i class="fas fa-undo"></i>
            </button>
                <button class="toolbar-btn" id="btnRedo" title="Redo (Ctrl+Y)">
                    <i class="fas fa-redo"></i>
            </button>
                <div class="toolbar-divider"></div>
                @endif
                @if(isset($canEditCanvas) && $canEditCanvas)
                <button class="toolbar-btn" id="btnDelete" title="Delete (Del)">
                    <i class="fas fa-trash"></i>
                </button>
                <div class="toolbar-divider"></div>
                @endif
                <button class="toolbar-btn" id="btnErrorLog" title="Error Log" onclick="ErrorLogger.togglePanel(); return false;" style="position: relative;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="errorLogBadge" style="display: none; position: absolute; top: -5px; right: -5px; background: #ff4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 10px; font-weight: bold;">0</span>
                </button>
                <div class="toolbar-divider"></div>
                @if(isset($canEditCanvas) && $canEditCanvas)
                <button class="toolbar-btn" id="btnToggleProperties" title="Toggle Properties Panel (Double-click to open)" style="background: rgba(40, 167, 69, 0.3);">
                    <i class="fas fa-cog"></i> <span id="propertiesToggleText" style="font-size: 10px;">ON</span>
            </button>
                @endif
                <button class="toolbar-btn" id="btnToggleBoothNumbers" title="Toggle Booth Numbers Sidebar" style="background: rgba(102, 126, 234, 0.3);">
                    <i class="fas fa-th"></i>
            </button>
                @if(isset($canEditCanvas) && $canEditCanvas)
                <button class="toolbar-btn" id="btnClearCanvas" title="Clear Canvas" style="background: rgba(220, 53, 69, 0.3);">
                    <i class="fas fa-eraser"></i>
            </button>
                @endif
            </div>
            <div class="toolbar-section">
                <div class="zoom-controls-group">
                    <button class="toolbar-btn" id="zoomOut" title="Zoom Out">
                <i class="fas fa-minus"></i>
            </button>
                    <span class="zoom-level" id="zoomLevel">100%</span>
                    <button class="toolbar-btn" id="zoomIn" title="Zoom In">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button class="toolbar-btn" id="zoomFit" title="Fit to Canvas (Center and Fit Image)">
                <i class="fas fa-expand-arrows-alt"></i>
            </button>
                </div>
            </div>
            <div class="toolbar-section">
                @if(isset($canEditCanvas) && $canEditCanvas)
                <!-- Floorplan Dropdown -->
                <div class="dropdown" style="position: relative; display: inline-block;">
                    <button class="toolbar-btn dropdown-toggle" id="btnFloorplanDropdown" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Floorplan Options">
                        <i class="fas fa-image"></i> <i class="fas fa-caret-down" style="margin-left: 2px; font-size: 9px;"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnFloorplanDropdown" style="min-width: 200px; padding: 5px 0; margin-top: 5px; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <a class="dropdown-item" href="#" id="btnUploadFloorplan" style="padding: 10px 15px; display: flex; align-items: center; gap: 10px; color: #333; text-decoration: none; transition: background 0.2s;">
                            <i class="fas fa-upload" style="color: #667eea;"></i> Upload Floorplan
                        </a>
                        <div class="dropdown-divider-item" style="margin: 5px 0; border-top: 1px solid #e9ecef;"></div>
                        <a class="dropdown-item" href="#" id="btnRemoveFloorplan" style="padding: 10px 15px; display: flex; align-items: center; gap: 10px; color: #dc3545; text-decoration: none; transition: background 0.2s;">
                            <i class="fas fa-trash-alt"></i> Remove Floorplan
                        </a>
                    </div>
                </div>
                <button class="toolbar-btn" id="btnBoothSettings" title="Booth Default Settings">
                    <i class="fas fa-cube"></i>
                </button>
                <div class="dropdown d-inline-block">
                    <button class="toolbar-btn dropdown-toggle" id="btnLockBoothsDropdown" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Lock/Unlock Booths">
                        <i class="fas fa-lock"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="btnLockBoothsDropdown" style="min-width: 220px;">
                        <h6 class="dropdown-header">Selected Booths</h6>
                        <a class="dropdown-item" href="#" id="btnLockSelected"><i class="fas fa-lock"></i> Lock Selected</a>
                        <a class="dropdown-item" href="#" id="btnUnlockSelected"><i class="fas fa-unlock"></i> Unlock Selected</a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">All Booths</h6>
                        <a class="dropdown-item" href="#" id="btnLockAll"><i class="fas fa-lock"></i> Lock All</a>
                        <a class="dropdown-item" href="#" id="btnUnlockAll"><i class="fas fa-unlock"></i> Unlock All</a>
                    </div>
                </div>
                @endif
                <button class="toolbar-btn" id="btnShowBooths" title="Show All Booths (Flash Effect) — {{ $totalBooths ?? 0 }} booths on canvas" style="background: rgba(255, 193, 7, 0.3);">
                    <i class="fas fa-th"></i> <span id="boothCountBadge" style="font-size: 10px; margin-left: 4px;" aria-label="Booths on canvas">{{ $totalBooths ?? 0 }}</span>
                </button>
                @if(isset($canEditCanvas) && $canEditCanvas)
                <button class="toolbar-btn" id="btnSettings" title="Canvas Settings">
                    <i class="fas fa-cog"></i>
                </button>
                @endif
                <button class="toolbar-btn" id="btnPrint" title="Print Floorplan" style="background: rgba(40, 167, 69, 0.3);">
                    <i class="fas fa-print"></i>
                </button>
                <button class="toolbar-btn" id="btnPreview" title="Preview Floorplan" style="background: rgba(0, 123, 255, 0.3);">
                    <i class="fas fa-eye"></i>
                </button>
                @if(isset($canEditCanvas) && $canEditCanvas)
                <button class="toolbar-btn btn-primary" id="btnSave" title="Save Floor Plan">
                    <i class="fas fa-save"></i> Save
            </button>
                @endif
        </div>
        </div>
        
        <!-- Information Toolbar - Sticky Top -->
        <div class="info-toolbar" id="infoToolbar">
            <div class="info-toolbar-content">
                <div class="info-section">
                    <span class="info-label">X:</span>
                    <span class="info-value info-editable" data-property="x" id="infoX">0</span>
                </div>
                <div class="info-section">
                    <span class="info-label">Y:</span>
                    <span class="info-value info-editable" data-property="y" id="infoY">0</span>
                </div>
                <div class="info-section">
                    <span class="info-label">W:</span>
                    <span class="info-value info-editable" data-property="w" id="infoW">0</span>
                </div>
                <div class="info-section">
                    <span class="info-label">H:</span>
                    <span class="info-value info-editable" data-property="h" id="infoH">0</span>
                </div>
                <div class="info-section">
                    <span class="info-label">R:</span>
                    <span class="info-value info-editable" data-property="r" id="infoR">0°</span>
                </div>
                <div class="info-divider"></div>
                <div class="info-section">
                    <span class="info-label">Z:</span>
                    <span class="info-value info-editable" data-property="z" id="infoZ">10</span>
                </div>
                <div class="info-section">
                    <span class="info-label">Font:</span>
                    <span class="info-value info-editable" data-property="fontsize" id="infoFontSize">14</span>
                </div>
                <div class="info-section">
                    <span class="info-label">Border:</span>
                    <span class="info-value info-editable" data-property="borderwidth" id="infoBorderWidth">2</span>
                </div>
                <div class="info-section">
                    <span class="info-label">Radius:</span>
                    <span class="info-value info-editable" data-property="borderradius" id="infoBorderRadius">6</span>
                </div>
                <div class="info-section">
                    <span class="info-label">Opacity:</span>
                    <span class="info-value info-editable" data-property="opacity" id="infoOpacity">1.00</span>
                </div>
                <div class="info-divider"></div>
                <div class="info-section">
                    <span class="info-label">Status:</span>
                    <span class="info-value" id="infoStatus">-</span>
                </div>
                <div class="info-section">
                    <span class="info-label">Company:</span>
                    <span class="info-value" id="infoCompany">-</span>
                </div>
                <div class="info-divider"></div>
                <div class="info-section" title="Total booths on this floor plan">
                    <span class="info-label"><i class="fas fa-th-large"></i> Booths:</span>
                    <span class="info-value" id="infoBoothsOnCanvas">{{ $totalBooths ?? 0 }}</span>
                </div>
            </div>
            </div>
            <!-- Text Format Panel (Photoshop-style) - visible when a canvas text is selected -->
            <div class="text-format-panel" id="textFormatPanel" style="display: none;" aria-label="Text format options">
                <div class="text-format-row">
                    <label class="text-format-label">Font</label>
                    <select id="textFormatFontFamily" class="text-format-select" title="Font family">
                        <option value="Arial, sans-serif">Arial</option>
                        <option value="'Helvetica Neue', Helvetica, sans-serif">Helvetica</option>
                        <option value="Georgia, serif">Georgia</option>
                        <option value="'Times New Roman', Times, serif">Times New Roman</option>
                        <option value="'Courier New', Courier, monospace">Courier New</option>
                        <option value="Verdana, sans-serif">Verdana</option>
                        <option value="Tahoma, sans-serif">Tahoma</option>
                        <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
                        <option value="'Palatino Linotype', Palatino, serif">Palatino</option>
                        <option value="'Khmer OS Battambang', 'Hanuman', sans-serif">Khmer OS</option>
                    </select>
                    <label class="text-format-label">Size</label>
                    <input type="number" id="textFormatFontSize" class="text-format-input text-format-input-num" min="8" max="120" value="14" title="Font size (px)">
                    <button type="button" class="text-format-btn" id="textFormatBold" title="Bold"><i class="fas fa-bold"></i></button>
                    <button type="button" class="text-format-btn" id="textFormatItalic" title="Italic"><i class="fas fa-italic"></i></button>
                    <button type="button" class="text-format-btn" id="textFormatUnderline" title="Underline"><i class="fas fa-underline"></i></button>
                    <button type="button" class="text-format-btn" id="textFormatStrike" title="Strikethrough"><i class="fas fa-strikethrough"></i></button>
                </div>
                <div class="text-format-row">
                    <span class="text-format-label">Align</span>
                    <button type="button" class="text-format-btn text-format-align" data-align="left" title="Align left"><i class="fas fa-align-left"></i></button>
                    <button type="button" class="text-format-btn text-format-align" data-align="center" title="Align center"><i class="fas fa-align-center"></i></button>
                    <button type="button" class="text-format-btn text-format-align" data-align="right" title="Align right"><i class="fas fa-align-right"></i></button>
                    <button type="button" class="text-format-btn text-format-align" data-align="justify" title="Justify"><i class="fas fa-align-justify"></i></button>
                    <label class="text-format-label">Color</label>
                    <input type="color" id="textFormatColor" class="text-format-color" value="#333333" title="Text color">
                    <label class="text-format-label">Bg</label>
                    <input type="color" id="textFormatBg" class="text-format-color" value="#ffffff" title="Background color">
                    <label class="text-format-label">Border</label>
                    <input type="color" id="textFormatBorderColor" class="text-format-color" value="#667eea" title="Border color">
                    <input type="number" id="textFormatBorderWidth" class="text-format-input text-format-input-num" min="0" max="20" value="1" title="Border width (px)">
                    <select id="textFormatBorderStyle" class="text-format-select text-format-select-sm" title="Border style">
                        <option value="solid">Solid</option>
                        <option value="dashed">Dashed</option>
                        <option value="dotted">Dotted</option>
                        <option value="double">Double</option>
                    </select>
                    <label class="text-format-label">Radius</label>
                    <input type="number" id="textFormatBorderRadius" class="text-format-input text-format-input-num" min="0" max="50" value="6" title="Border radius (px)">
                </div>
                <div class="text-format-row">
                    <label class="text-format-label">Box shadow</label>
                    <input type="text" id="textFormatBoxShadow" class="text-format-input text-format-input-wide" placeholder="0 2px 8px rgba(0,0,0,0.2)" title="Box shadow (CSS)">
                    <label class="text-format-label">Text shadow</label>
                    <input type="text" id="textFormatTextShadow" class="text-format-input text-format-input-wide" placeholder="0 1px 2px rgba(0,0,0,0.3)" title="Text shadow (CSS)">
                    <label class="text-format-label">Spacing</label>
                    <input type="text" id="textFormatLetterSpacing" class="text-format-input text-format-input-num" placeholder="0px" title="Letter spacing">
                    <label class="text-format-label">Line H</label>
                    <input type="text" id="textFormatLineHeight" class="text-format-input text-format-input-num" placeholder="1.2" title="Line height">
                </div>
            </div>
        
        <!-- Main Content Row: Canvas and Sidebar -->
        <div class="designer-main-row">
            <!-- Sidebar - Booth Numbers Panel (25% width) -->
            <div class="designer-sidebar" id="designerSidebar">
                <div class="sidebar-header">
                        <h6><i class="fas fa-th"></i> Booth Number Area</h6>
                        <div class="sidebar-header-actions">
                            <button class="btn-toggle-stock" id="toggleInStock" title="Show Only Booths In Stock" data-active="false">
                                <i class="fas fa-check-circle"></i>
                                <span>In Stock</span>
                            </button>
                            <button class="btn btn-sm btn-link text-white" id="toggleSidebar" title="Collapse Sidebar">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                        </div>
                </div>
                <div class="sidebar-content">
                    <div class="mb-3 d-flex align-items-center" style="gap:8px;">
                        <button class="btn btn-sm btn-primary btn-block" id="btnAddZoneMain" style="flex:1; border-radius: 10px; padding: 10px 16px; font-weight: 600; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                            <i class="fas fa-plus"></i> Add Zone
                        </button>
                    </div>
                    <div class="sidebar-search mb-2">
                        <input type="text" class="form-control form-control-sm" id="boothSearchSidebar" placeholder="Search booths...">
                    </div>
                    
                    <!-- Zone Header Button Controls -->
                    <div class="zone-controls-panel collapsed mb-3">
                        <div class="zone-controls-header" id="zoneControlsToggle">
                            <i class="fas fa-cog"></i>
                            <span>Button Controls</span>
                            <i class="fas fa-chevron-down zone-controls-chevron"></i>
                        </div>
                        <div class="zone-controls-content" id="zoneControlsContent">
                            <div class="zone-control-item">
                                <label class="zone-control-label">
                                    <input type="checkbox" class="zone-control-checkbox" data-button="add-all" checked>
                                    <span class="zone-control-text">Add All</span>
                                </label>
                            </div>
                            <div class="zone-control-item">
                                <label class="zone-control-label">
                                    <input type="checkbox" class="zone-control-checkbox" data-button="add-selected" checked>
                                    <span class="zone-control-text">Add Selected</span>
                                </label>
                            </div>
                            <div class="zone-control-item">
                                <label class="zone-control-label">
                                    <input type="checkbox" class="zone-control-checkbox" data-button="add-click" checked>
                                    <span class="zone-control-text">Click to Place</span>
                                </label>
                            </div>
                            <div class="zone-control-item">
                                <label class="zone-control-label">
                                    <input type="checkbox" class="zone-control-checkbox" data-button="settings" checked>
                                    <span class="zone-control-text">Zone Settings</span>
                                </label>
                            </div>
                            <div class="zone-control-item">
                                <label class="zone-control-label">
                                    <input type="checkbox" class="zone-control-checkbox" data-button="appearance" checked>
                                    <span class="zone-control-text">Appearance</span>
                                </label>
                            </div>
                            <div class="zone-control-item">
                                <label class="zone-control-label">
                                    <input type="checkbox" class="zone-control-checkbox" data-button="clear" checked>
                                    <span class="zone-control-text">Clear Zone</span>
                                </label>
                            </div>
                            <div class="zone-control-item">
                                <label class="zone-control-label">
                                    <input type="checkbox" class="zone-control-checkbox" data-button="zoom" checked>
                                    <span class="zone-control-text">Zoom to Zone</span>
                                </label>
                            </div>
                            <div class="zone-control-item">
                                <label class="zone-control-label">
                                    <input type="checkbox" class="zone-control-checkbox" data-button="add-new" checked>
                                    <span class="zone-control-text">Add New Booth</span>
                                </label>
                            </div>
                            <div class="zone-control-item">
                                <label class="zone-control-label">
                                    <input type="checkbox" class="zone-control-checkbox" data-button="delete" checked>
                                    <span class="zone-control-text">Delete</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="blogs" id="boothNumbersContainer">
                            @php
                                // Group booths by zone (extract first letter from booth number)
                                // Note: Booths are already filtered by floor_plan_id in controller, so zones are floor-plan-specific
                                $zones = [];
                                foreach($booths as $booth) {
                                    // Only process booths that belong to current floor plan (safety check)
                                    if (isset($floorPlanId) && $floorPlanId && $booth->floor_plan_id != $floorPlanId) {
                                        continue; // Skip booths from other floor plans
                                    }
                                    
                                    $boothNumber = $booth->booth_number;
                                    // Extract zone from booth number (first letter(s) - can be 1-3 characters)
                                    $zone = '';
                                    if (preg_match('/^([A-Za-z]{1,3})/', $boothNumber, $matches)) {
                                        $zone = strtoupper($matches[1]);
                                    } else {
                                        // If no letter found, use first character or default to "OTHER"
                                        $zone = !empty($boothNumber) ? strtoupper(substr($boothNumber, 0, 1)) : 'OTHER';
                                    }
                                    
                                    if (!isset($zones[$zone])) {
                                        $zones[$zone] = [];
                                    }
                                    $zones[$zone][] = $booth;
                                }
                                // Sort zones alphabetically
                                ksort($zones);
                            @endphp
                            
                            @foreach($zones as $zoneName => $zoneBooths)
                                <div class="zone-section" data-zone="{{ $zoneName }}">
                                    <div class="zone-header" data-zone-toggle="{{ $zoneName }}">
                                        <div class="zone-header-left">
                                            <i class="fas fa-chevron-down zone-chevron" aria-hidden="true"></i>
                                            <span class="zone-name">Zone {{ $zoneName }}</span>
                                            <span class="zone-count">({{ count($zoneBooths) }})</span>
                                        </div>
                                        <div class="zone-header-actions">
                                        <button class="btn-add-all-zone" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Add All Booths in Zone {{ $zoneName }} to Canvas"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.addAllZoneToCanvas({{ json_encode($zoneName) }});">
                                            <i class="fas fa-plus-circle"></i> Add All
                                        </button>
                                        <button class="btn-add-selected-zone" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Add Selected Booths in Zone {{ $zoneName }} to Canvas (Stick Together)"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.addSelectedZoneBoothsToCanvas({{ json_encode($zoneName) }});">
                                            <i class="fas fa-layer-group"></i> Add Selected
                                        </button>
                                        <button class="btn-add-all-zone-click" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Add All Booths in Zone {{ $zoneName }} - Click on Canvas to Place"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.enableClickToPlaceMode({{ json_encode($zoneName) }});">
                                            <i class="fas fa-crosshairs"></i>
                                        </button>
                                        <button class="btn-zone-settings" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Zone Settings - Adjust Size, Rotation & Dimensions for All Booths in Zone {{ $zoneName }}"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.openZoneSettings({{ json_encode($zoneName) }});">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <button class="btn-zone-appearance" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Zone Appearance - Customize Colors & Appearance Only for Zone {{ $zoneName }}"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.openZoneAppearanceSettings({{ json_encode($zoneName) }});">
                                            <i class="fas fa-palette"></i>
                                        </button>
                                        <button class="btn-zone-clear" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Clear Zone {{ $zoneName }} - Return All Booths to Booth Number Area"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.clearZoneBooths({{ json_encode($zoneName) }});">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn-zone-zoom" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Zoom to Zone {{ $zoneName }} - Fit All Booths in View"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.zoomToZone({{ json_encode($zoneName) }});">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                        <button class="btn-zone-select-all" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Select All Booths from Zone {{ $zoneName }} on Canvas"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.selectAllBoothsByZone({{ json_encode($zoneName) }});">
                                            <i class="fas fa-mouse-pointer"></i>
                                        </button>
                                        <button class="btn-zone-add-new" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Add New Booth ID to Zone {{ $zoneName }}"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.showAddBoothModal({{ json_encode($zoneName) }});">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button class="btn-zone-delete" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Delete Booths from Zone {{ $zoneName }}"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.showDeleteBoothModal({{ json_encode($zoneName) }});">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        </div>
                                    </div>
                                    <div class="zone-content" id="zoneContent{{ $zoneName }}">
                                        @foreach($zoneBooths as $booth)
                            <div class="booth-number-item {{ ($booth->client_id || $booth->status != 1) ? 'booked' : '' }}" 
                                 draggable="true"
                data-booth-id="{{ $booth->id }}"
                data-booth-number="{{ $booth->booth_number }}"
                data-booth-status="{{ $booth->status }}"
                                                 data-booth-zone="{{ $zoneName }}"
                data-client-id="{{ $booth->client_id }}"
                data-user-id="{{ $booth->userid }}"
                data-category-id="{{ $booth->category_id }}"
                data-sub-category-id="{{ $booth->sub_category_id }}"
                data-asset-id="{{ $booth->asset_id }}"
                                 data-booth-type-id="{{ $booth->booth_type_id }}">
                {{ $booth->booth_number }}
                                            </div>
                                        @endforeach
                                    </div>
    </div>
                            @endforeach
                </div>
                    </div>
            </div>
            
            <!-- Main Canvas Container (75% width) -->
            <div id="printContainer" class="canvas-container">
                <!-- Canvas Area -->
                <div id="print" class="floorplan-canvas" 
                     style="@if(isset($currentFloorPlan) && $currentFloorPlan && $currentFloorPlan->floor_image)
                     @php
                         // Ensure absolute URL for inline style
                         $inlineImageUrl = $currentFloorPlan->floor_image;
                         if (strpos($inlineImageUrl, 'http') !== 0) {
                             $inlineImageUrl = asset($inlineImageUrl);
                             if (strpos($inlineImageUrl, 'http') !== 0) {
                                 $inlineImageUrl = url($currentFloorPlan->floor_image);
                             }
                         }
                     @endphp
                     background-image: url('{{ $inlineImageUrl }}'); background-size: 100% 100%; background-repeat: no-repeat; background-position: top left; background-attachment: local;
                     @elseif(file_exists(public_path('images/map.jpg')))
                     background-image: url('{{ asset('images/map.jpg') }}'); background-size: 100% 100%; background-repeat: no-repeat; background-position: top left; background-attachment: local;
                     @else
                     background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                     @endif">
                    @if(isset($currentFloorPlan) && $currentFloorPlan && $currentFloorPlan->floor_image)
                        @php
                            // Ensure absolute URL for img src
                            $imgSrcUrl = $currentFloorPlan->floor_image;
                            if (strpos($imgSrcUrl, 'http') !== 0) {
                                $imgSrcUrl = asset($imgSrcUrl);
                                if (strpos($imgSrcUrl, 'http') !== 0) {
                                    $imgSrcUrl = url($currentFloorPlan->floor_image);
                                }
                            }
                        @endphp
                        <img src="{{ $imgSrcUrl }}" 
                             id="floorplanImageElement"
                             alt="Floor Plan Map"
                             style="display: none;"
                             onerror="console.error('Failed to load floor plan image:', this.src);"/>
                    @elseif(file_exists(public_path('images/map.jpg')))
                        <img src="{{ asset('images/map.jpg') }}" 
                             id="floorplanImageElement"
                             alt="Floor Plan Map"
                             style="display: none;"
                             onerror="console.error('Failed to load default map image');"/>
                    @endif
                    <!-- Canvas Center Marker -->
                    <div id="canvasCenterMarker" class="center-marker" aria-hidden="true"></div>
                    </div>
                    
                <!-- Grid Overlay -->
                <div id="gridOverlay" class="grid-overlay"></div>
                <!-- Zoom Selection Box (for Zoom Tool) -->
                <div id="zoomSelection" class="zoom-selection" style="display: none;"></div>
                    </div>
                    </div>
                    
        <!-- Properties Panel Backdrop -->
        <div class="properties-panel-backdrop" id="propertiesPanelBackdrop"></div>
        
        <!-- Properties Panel - Popup Modal -->
        <div class="properties-panel" id="propertiesPanel">
            <div class="panel-header">
                <h6><i class="fas fa-info-circle"></i> Properties</h6>
                <button class="btn btn-sm btn-link text-white" id="closePropertiesPanel" title="Close">
                    <i class="fas fa-times"></i>
                </button>
                    </div>
            <div class="panel-content" id="propertiesContent">
                <p class="text-muted text-center">Select a booth to view properties.</p>
                    </div>
        </div>
    </div>
</div>

<!-- Update Booth Info Modal (Basic Info, Details, Content, Media) - same as Edit at /booths -->
@if(isset($floorPlans) && isset($boothTypes) && isset($categories))
@include('booths.partials.edit-form-modal', [
    'floorPlans' => $floorPlans ?? collect(),
    'boothTypes' => $boothTypes ?? collect(),
    'categories' => $categories ?? collect(),
    'clients' => $clients ?? [],
])
@endif

<!-- Upload Floorplan Modal -->
<div class="modal fade" id="uploadFloorplanModal" tabindex="-1" role="dialog" aria-labelledby="uploadFloorplanModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadFloorplanModalLabel">
                    <i class="fas fa-upload"></i> Upload Floorplan Image
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadFloorplanForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="floorplanImage">
                            <i class="fas fa-image"></i> Select Floorplan Image
                        </label>
                        <input type="file" class="form-control-file" id="floorplanImageInput" name="floorplan_image" accept="image/*" required>
                        <small class="form-text text-muted" id="uploadSizeLimitText">{{ \App\Helpers\UploadSettingsHelper::getHint('floor_plan') }}</small>
                    </div>
                    <div class="form-group">
                        <div class="preview-container" id="imagePreview" style="display: none; margin-top: 15px;">
                            <img id="previewImage" src="" alt="Preview" style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> Uploading a new floorplan will replace the existing one. The image will be automatically resized to fit the canvas.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnUploadFloorplanSubmit">
                    <i class="fas fa-upload"></i> Upload
                </button>
                    </div>
        </div>
    </div>
</div>

<!-- Canvas Settings Modal -->
<div class="modal fade" id="canvasSettingsModal" tabindex="-1" role="dialog" aria-labelledby="canvasSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="canvasSettingsModalLabel">
                    <i class="fas fa-cog"></i> Canvas Settings
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="canvasSettingsForm">
                    <div class="form-group">
                        <label for="canvasWidth">
                            <i class="fas fa-arrows-alt-h"></i> Canvas Width (px)
                        </label>
                        <input type="number" class="form-control" id="canvasWidth" min="5" step="1" value="1200">
                        <small class="form-text text-muted">Width of the canvas in pixels (minimum: 5px, no maximum limit)</small>
                    </div>
                    <div class="form-group">
                        <label for="canvasHeight">
                            <i class="fas fa-arrows-alt-v"></i> Canvas Height (px)
                        </label>
                        <input type="number" class="form-control" id="canvasHeight" min="5" step="1" value="800">
                        <small class="form-text text-muted">Height of the canvas in pixels (minimum: 5px, no maximum limit)</small>
                    </div>
                    <div class="form-group">
                        <label for="canvasResolution">
                            <i class="fas fa-image"></i> Export Resolution (DPI)
                        </label>
                        <input type="number" class="form-control" id="canvasResolution" min="72" max="600" step="1" value="300">
                        <small class="form-text text-muted">Resolution for PNG export (72-600 DPI). Higher DPI = better quality but larger file size.</small>
                    </div>
                    <div class="form-group">
                        <label for="gridSize">
                            <i class="fas fa-th"></i> Grid Size (px)
                        </label>
                        <input type="number" class="form-control" id="gridSize" min="1" max="100" step="1" value="1" placeholder="1">
                        <small class="form-text text-muted">Size of grid cells in pixels. Affects both visual grid and snap-to-grid behavior.</small>
                    </div>
                    <div class="form-group">
                        <label for="uploadSizeLimit">
                            <i class="fas fa-upload"></i> Upload Size Limit (MB)
                        </label>
                        <input type="number" class="form-control" id="uploadSizeLimit" min="1" step="1" value="10">
                        <small class="form-text text-muted">Maximum file size allowed for floorplan image uploads (in megabytes). No maximum limit if left empty or set to 0.</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> Changing canvas size will not affect existing booths. Resolution only affects PNG exports. Grid size affects snap-to-grid alignment.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyCanvasSettings">
                    <i class="fas fa-check"></i> Apply Settings
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add New Booth Modal -->
<div class="modal fade" id="addBoothModal" tabindex="-1" role="dialog" aria-labelledby="addBoothModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBoothModalLabel">
                    <i class="fas fa-plus-circle"></i> Add New Booth to Zone <span id="addBoothZoneName"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addBoothForm">
                    <div class="form-group">
                        <label>
                            <i class="fas fa-hashtag"></i> Create Booths in Range
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="boothFrom" class="small">From Number</label>
                                <input type="number" class="form-control" id="boothFrom" min="1" max="9999" value="1" required>
                            </div>
                            <div class="col-md-6">
                                <label for="boothTo" class="small">To Number</label>
                                <input type="number" class="form-control" id="boothTo" min="1" max="9999" value="1" required>
                            </div>
                        </div>
                        <small class="form-text text-muted">Enter the range of booth numbers to create. For example, From: 1, To: 50 will create A01, A02, A03... A50</small>
                    </div>
                    <div class="form-group">
                        <label for="boothNumberFormat">
                            <i class="fas fa-tag"></i> Number Format
                        </label>
                        <select class="form-control" id="boothNumberFormat">
                            <option value="2">2 digits (A01, A02, A03...)</option>
                            <option value="3">3 digits (A001, A002, A003...)</option>
                            <option value="4">4 digits (A0001, A0002, A0003...)</option>
                        </select>
                        <small class="form-text text-muted">Choose how many digits to use for the booth number</small>
                    </div>
                    <div class="form-group" id="previewGroup" style="display: none;">
                        <label>
                            <i class="fas fa-eye"></i> Preview
                        </label>
                        <div class="alert alert-light" id="boothPreview" style="max-height: 150px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                            <!-- Preview will be shown here -->
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Smart Generation:</strong> The system will create booths in the specified range. If any booth numbers already exist, they will be skipped automatically.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnAddBoothSubmit">
                    <i class="fas fa-plus"></i> Add Booth(s)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Zone Modal -->
<div class="modal fade" id="addZoneModal" tabindex="-1" role="dialog" aria-labelledby="addZoneModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addZoneModalLabel">
                    <i class="fas fa-plus-circle"></i> Add New Zone
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addZoneForm">
                    <div class="form-group">
                        <label for="zoneNameInput">
                            <i class="fas fa-vector-square"></i> Zone Name (letters only)
                        </label>
                        <input type="text" class="form-control" id="zoneNameInput" maxlength="3" placeholder="e.g., A or B or ABC" required>
                        <small class="form-text text-muted">Letters only, 1-3 characters. The first booth will be created as ZoneName + 01 (e.g., A01).</small>
                    </div>
                    <div class="form-group">
                        <label for="zoneAboutInput">
                            <i class="fas fa-info-circle"></i> Zone About
                        </label>
                        <textarea class="form-control" id="zoneAboutInput" rows="3" placeholder="Enter description or information about this zone..."></textarea>
                        <small class="form-text text-muted">Optional: Add a description or important information about this zone.</small>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> We will automatically create the first booth for this zone (e.g., A01) so that the zone appears immediately.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnAddZoneSubmit">
                    <i class="fas fa-plus"></i> Create Zone
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Booth Modal -->
<div class="modal fade" id="deleteBoothModal" tabindex="-1" role="dialog" aria-labelledby="deleteBoothModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteBoothModalLabel">
                    <i class="fas fa-trash"></i> Delete Booths from Zone <span id="deleteBoothZoneName"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="deleteBoothForm">
                    <!-- Delete Option Tabs -->
                    <ul class="nav nav-tabs mb-3" id="deleteOptionTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="delete-all-tab" data-bs-toggle="tab" href="#delete-all" role="tab">
                                <i class="fas fa-trash-alt"></i> Delete All
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="delete-specific-tab" data-bs-toggle="tab" href="#delete-specific" role="tab">
                                <i class="fas fa-list"></i> Delete Specific
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="delete-range-tab" data-bs-toggle="tab" href="#delete-range" role="tab">
                                <i class="fas fa-arrows-alt-h"></i> Delete Range
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="delete-by-ids-tab" data-bs-toggle="tab" href="#delete-by-ids" role="tab">
                                <i class="fas fa-hashtag"></i> By Booth ID(s)
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content" id="deleteOptionTabsContent">
                        <!-- Delete All Tab -->
                        <div class="tab-pane fade show active" id="delete-all" role="tabpanel">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> This will delete ALL booths in Zone <span id="deleteAllZoneName"></span>. This action cannot be undone!
                            </div>
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-hashtag"></i> Total Booths in Zone
                                </label>
                                <input type="text" class="form-control" id="deleteAllCount" readonly style="background-color: #f8f9fa;">
                                <small class="form-text text-muted">All these booths will be permanently deleted.</small>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirmDeleteAll" required>
                                    <label class="form-check-label" for="confirmDeleteAll">
                                        I understand this will delete ALL booths in this zone permanently
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Delete Specific Tab -->
                        <div class="tab-pane fade" id="delete-specific" role="tabpanel">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-list"></i> Select Booths to Delete
                                </label>
                                <div id="deleteSpecificBoothsList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; padding: 10px;">
                                    <!-- Booth checkboxes will be populated here -->
                                </div>
                                <small class="form-text text-muted">Check the booths you want to delete. You can select multiple booths.</small>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-sm btn-secondary" id="selectAllBooths">
                                    <i class="fas fa-check-square"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" id="deselectAllBooths">
                                    <i class="fas fa-square"></i> Deselect All
                                </button>
                            </div>
                        </div>
                        
                        <!-- Delete Range Tab -->
                        <div class="tab-pane fade" id="delete-range" role="tabpanel">
                            <div class="form-group">
                                <label>
                                    <i class="fas fa-arrows-alt-h"></i> Delete Booths in Range
                                </label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="deleteFrom" class="small">From Number</label>
                                        <input type="number" class="form-control" id="deleteFrom" min="1" max="9999" value="1">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="deleteTo" class="small">To Number</label>
                                        <input type="number" class="form-control" id="deleteTo" min="1" max="9999" value="1">
                                    </div>
                                </div>
                                <small class="form-text text-muted">Enter the range of booth numbers to delete. For example, From: 1, To: 50 will delete A01, A02, A03... A50</small>
                            </div>
                            <div class="form-group" id="deleteRangePreviewGroup" style="display: none;">
                                <label>
                                    <i class="fas fa-eye"></i> Preview
                                </label>
                                <div class="alert alert-light" id="deleteRangePreview" style="max-height: 150px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                                    <!-- Preview will be shown here -->
                                </div>
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> This will permanently delete all booths in the specified range. This action cannot be undone!
                            </div>
                        </div>

                        <!-- By Booth ID(s) Tab - any zone, any floor plan -->
                        <div class="tab-pane fade" id="delete-by-ids" role="tabpanel">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Delete specific booths by their ID, from <strong>any zone and any floor plan</strong>. Use when you know the booth ID(s) and want to remove them regardless of current view.
                            </div>
                            <div class="form-group">
                                <label for="deleteByBoothIdsInput">
                                    <i class="fas fa-hashtag"></i> Booth ID(s) (comma-separated)
                                </label>
                                <input type="text" class="form-control" id="deleteByBoothIdsInput" placeholder="e.g. 123, 456, 789" autocomplete="off">
                                <small class="form-text text-muted">Enter one or more booth IDs. They will be deleted from whichever zone and floor plan they belong to.</small>
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> This permanently deletes the booths. Booths with active bookings are skipped unless you force delete.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnDeleteBoothSubmit">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Book Booth Modal -->
<div class="modal fade" id="bookBoothModal" tabindex="-1" role="dialog" aria-labelledby="bookBoothModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookBoothModalLabel">
                    <i class="fas fa-calendar-check"></i> Book Booth: <span id="bookBoothNumber"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="bookBoothForm">
                    <input type="hidden" id="bookBoothId" name="booth_id">
                    <input type="hidden" id="selectedClientId" name="client_id" value="">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Search for an existing client or fill in the form to create a new client. All fields marked with * are mandatory.</strong>
                    </div>
                    
                    <!-- Client Search -->
                    <div class="form-group">
                        <label for="clientSearch">
                            <i class="fas fa-search"></i> Search Existing Client
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="clientSearch" placeholder="Search by name, company, email, or phone number..." autocomplete="off">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" id="btnSearchClient">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <button type="button" class="btn btn-secondary" id="btnClearSearch" style="display: none;">
                                    <i class="fas fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                        <div id="clientSearchResults" class="mt-2" style="display: none; position: relative; z-index: 1050;">
                            <div class="list-group" id="clientSearchResultsList" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
                        </div>
                        <small class="form-text text-muted">Type at least 2 characters to search for existing clients</small>
                    </div>
                    
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-user-edit mr-2"></i>Client Information</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientName">
                                    <i class="fas fa-user"></i> Client Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="clientName" name="name" required placeholder="Enter client name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientSex">
                                    <i class="fas fa-venus-mars"></i> Gender
                                </label>
                                <select class="form-control" id="clientSex" name="sex">
                            <option value="">Select Gender</option>
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                            <option value="3">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientCompany">
                                    <i class="fas fa-building"></i> Company <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="clientCompany" name="company" required placeholder="Enter company name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientPosition">
                                    <i class="fas fa-briefcase"></i> Position
                                </label>
                                <input type="text" class="form-control" id="clientPosition" name="position" placeholder="Enter position/title">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientPhone">
                                    <i class="fas fa-phone"></i> Phone Number <span class="text-danger">*</span>
                                </label>
                                <input type="tel" class="form-control" id="clientPhone" name="phone_number" required placeholder="Enter phone number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientEmail">
                                    <i class="fas fa-envelope"></i> Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="clientEmail" name="email" required placeholder="Enter email address">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="clientAddress">
                            <i class="fas fa-map-marker-alt"></i> Address <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="clientAddress" name="address" rows="2" required placeholder="Enter complete address (street, city, country)"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientTaxId">
                                    <i class="fas fa-id-card"></i> Tax ID / Business Registration Number
                                </label>
                                <input type="text" class="form-control" id="clientTaxId" name="tax_id" placeholder="Enter tax ID or business registration number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="clientWebsite">
                                    <i class="fas fa-globe"></i> Website
                                </label>
                                <input type="url" class="form-control" id="clientWebsite" name="website" placeholder="Enter website URL (e.g., https://example.com)">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="clientNotes">
                            <i class="fas fa-sticky-note"></i> Additional Notes
                        </label>
                        <textarea class="form-control" id="clientNotes" name="notes" rows="2" placeholder="Enter any additional information or notes"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="bookingStatus">
                            <i class="fas fa-tag"></i> Booking Status
                        </label>
                        <select class="form-control" id="bookingStatus" name="status">
                            @php
                                $bookingStatuses = isset($statusSettings) ? $statusSettings->filter(function($s) {
                                    return $s->status_code != 1 && $s->status_code != 4; // Exclude Available and Hidden
                                }) : collect();
                            @endphp
                            @forelse($bookingStatuses as $status)
                                <option value="{{ $status->status_code }}">{{ $status->status_name }}</option>
                            @empty
                                {{-- Fallback to defaults --}}
                                <option value="2">Confirmed</option>
                                <option value="3">Reserved</option>
                                <option value="5">Paid</option>
                            @endforelse
                        </select>
                        <small class="form-text text-muted">
                            @forelse($bookingStatuses as $status)
                                <strong>{{ $status->status_name }}:</strong> {{ $status->description ?? 'No description' }}<br>
                            @empty
                                <strong>Confirmed:</strong> Client has confirmed the booking<br>
                                <strong>Reserved:</strong> Booth is reserved but not yet confirmed<br>
                                <strong>Paid:</strong> Payment has been received
                            @endforelse
                        </small>
                    </div>
                    
                    <div id="bookBoothError" class="alert alert-danger" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="btnBookBoothSubmit">
                    <i class="fas fa-save"></i> Save Booking
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Individual Booth Color Picker Modal -->
<div class="modal fade" id="boothColorPickerModal" tabindex="-1" role="dialog" aria-labelledby="boothColorPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="boothColorPickerModalLabel">
                    <i class="fas fa-palette"></i> Set Colors for Booth: <span id="colorPickerBoothNumber"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="boothColorPickerForm">
                    <input type="hidden" id="colorPickerBoothId" value="">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> These colors are specific to this individual booth and are separate from Zone Area settings. Custom colors will override status-based colors for this booth only.
                    </div>
                    
                    <hr class="my-4" style="border-color: #dee2e6;">
                    <h6 class="mb-3" style="color: #495057; font-weight: 600;">
                        <i class="fas fa-palette"></i> Individual Booth Colors
                    </h6>
                    
                    <div class="form-group">
                        <label for="boothBackgroundColor">
                            <i class="fas fa-fill"></i> Background Color
                        </label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="boothBackgroundColor" value="#ffffff" title="Choose background color">
                            <input type="text" class="form-control" id="boothBackgroundColorText" value="#ffffff" placeholder="#ffffff" maxlength="7">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="clearBoothBackgroundColor" title="Clear (use status color)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Background color for this specific booth</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="boothBorderColor">
                            <i class="fas fa-border-all"></i> Border Color
                        </label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="boothBorderColor" value="#007bff" title="Choose border color">
                            <input type="text" class="form-control" id="boothBorderColorText" value="#007bff" placeholder="#007bff" maxlength="7">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="clearBoothBorderColor" title="Clear (use status color)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Border color for this specific booth</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="boothTextColor">
                            <i class="fas fa-font"></i> Text Color
                        </label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="boothTextColor" value="#000000" title="Choose text color">
                            <input type="text" class="form-control" id="boothTextColorText" value="#000000" placeholder="#000000" maxlength="7">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" id="clearBoothTextColor" title="Clear (use status color)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Text color for booth number</small>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Warning:</strong> Custom colors will override status-based colors. If you clear a color, the booth will use its status color instead.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn btn-warning" id="resetBoothColors">
                    <i class="fas fa-undo"></i> Reset to Status Colors
                </button>
                <button type="button" class="btn btn-primary" id="saveBoothColors">
                    <i class="fas fa-save"></i> Save Colors
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Booth Settings Modal -->
<div class="modal fade" id="boothSettingsModal" tabindex="-1" role="dialog" aria-labelledby="boothSettingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="boothSettingsModalLabel">
                    <i class="fas fa-cube"></i> Booth Default Settings
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="boothSettingsForm">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> These settings will be applied to all new booths when you add them to the canvas. Existing booths are not affected.
                    </div>
                    <div class="form-group">
                        <label for="defaultWidth">
                            <i class="fas fa-arrows-alt-h"></i> Default Width (px)
                        </label>
                        <input type="number" class="form-control" id="defaultWidth" min="5" step="1" value="80">
                        <small class="form-text text-muted">Default width for new booths (minimum: 5px)</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultHeight">
                            <i class="fas fa-arrows-alt-v"></i> Default Height (px)
                        </label>
                        <input type="number" class="form-control" id="defaultHeight" min="5" step="1" value="50">
                        <small class="form-text text-muted">Default height for new booths (minimum: 5px)</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultRotation">
                            <i class="fas fa-redo"></i> Default Rotation (degrees)
                        </label>
                        <input type="number" class="form-control" id="defaultRotation" min="-360" max="360" step="1" value="0">
                        <small class="form-text text-muted">Default rotation angle for new booths</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultZIndex">
                            <i class="fas fa-layer-group"></i> Default Z-Index
                        </label>
                        <input type="number" class="form-control" id="defaultZIndex" min="1" max="1000" step="1" value="10">
                        <small class="form-text text-muted">Default stacking order (higher = on top)</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultFontSize">
                            <i class="fas fa-font"></i> Default Font Size (px)
                        </label>
                        <input type="number" class="form-control" id="defaultFontSize" min="8" max="48" step="1" value="14">
                        <small class="form-text text-muted">Default font size for booth number text</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultBorderWidth">
                            <i class="fas fa-border-style"></i> Default Border Width (px)
                        </label>
                        <input type="number" class="form-control" id="defaultBorderWidth" min="0" max="10" step="1" value="2">
                        <small class="form-text text-muted">Default border width for booths</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultBorderRadius">
                            <i class="fas fa-circle"></i> Default Border Radius (px)
                        </label>
                        <input type="number" class="form-control" id="defaultBorderRadius" min="0" max="50" step="1" value="6">
                        <small class="form-text text-muted">Default corner rounding for booths</small>
                    </div>
                    <div class="form-group">
                        <label for="defaultOpacity">
                            <i class="fas fa-adjust"></i> Default Opacity
                        </label>
                        <input type="number" class="form-control" id="defaultOpacity" min="0" max="1" step="0.1" value="1.00">
                        <small class="form-text text-muted">Default transparency (0.0 = transparent, 1.0 = opaque)</small>
                    </div>
                    
                    <hr class="my-4" style="border-color: #dee2e6;">
                    <h6 class="mb-3" style="color: #495057; font-weight: 600;">
                        <i class="fas fa-palette"></i> Color Settings
                    </h6>
                    
                    <div class="form-group">
                        <label for="defaultBackgroundColor">
                            <i class="fas fa-fill"></i> Default Background Color
                        </label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="defaultBackgroundColor" value="#ffffff" title="Choose background color">
                            <input type="text" class="form-control" id="defaultBackgroundColorText" value="#ffffff" placeholder="#ffffff">
                        </div>
                        <small class="form-text text-muted">Default background color for new booths</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="defaultBorderColor">
                            <i class="fas fa-border-all"></i> Default Border Color
                        </label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="defaultBorderColor" value="#007bff" title="Choose border color">
                            <input type="text" class="form-control" id="defaultBorderColorText" value="#007bff" placeholder="#007bff">
                        </div>
                        <small class="form-text text-muted">Default border color for new booths</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="defaultTextColor">
                            <i class="fas fa-font"></i> Default Text Color
                        </label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="defaultTextColor" value="#000000" title="Choose text color">
                            <input type="text" class="form-control" id="defaultTextColorText" value="#000000" placeholder="#000000">
                        </div>
                        <small class="form-text text-muted">Default text color for booth numbers</small>
                    </div>
                    
                    <hr class="my-4" style="border-color: #dee2e6;">
                    <h6 class="mb-3" style="color: #495057; font-weight: 600;">
                        <i class="fas fa-typography"></i> Typography Settings
                    </h6>
                    
                    <div class="form-group">
                        <label for="defaultFontWeight">
                            <i class="fas fa-bold"></i> Default Font Weight
                        </label>
                        <select class="form-control" id="defaultFontWeight">
                            <option value="300">Light (300)</option>
                            <option value="400">Normal (400)</option>
                            <option value="500">Medium (500)</option>
                            <option value="600">Semi Bold (600)</option>
                            <option value="700" selected>Bold (700)</option>
                            <option value="800">Extra Bold (800)</option>
                            <option value="900">Black (900)</option>
                        </select>
                        <small class="form-text text-muted">Default font weight for booth number text</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="defaultFontFamily">
                            <i class="fas fa-font"></i> Default Font Family
                        </label>
                        <select class="form-control" id="defaultFontFamily">
                            <option value="Arial, sans-serif">Arial</option>
                            <option value="'Helvetica Neue', Helvetica, sans-serif">Helvetica</option>
                            <option value="'Times New Roman', serif">Times New Roman</option>
                            <option value="'Courier New', monospace">Courier New</option>
                            <option value="Georgia, serif">Georgia</option>
                            <option value="Verdana, sans-serif">Verdana</option>
                            <option value="'Trebuchet MS', sans-serif">Trebuchet MS</option>
                            <option value="Impact, sans-serif">Impact</option>
                            <option value="'Comic Sans MS', cursive">Comic Sans MS</option>
                            <option value="'Roboto', sans-serif">Roboto</option>
                            <option value="'Open Sans', sans-serif">Open Sans</option>
                            <option value="'Montserrat', sans-serif">Montserrat</option>
                        </select>
                        <small class="form-text text-muted">Default font family for booth number text</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="defaultTextAlign">
                            <i class="fas fa-align-center"></i> Default Text Alignment
                        </label>
                        <select class="form-control" id="defaultTextAlign">
                            <option value="center" selected>Center</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="justify">Justify</option>
                        </select>
                        <small class="form-text text-muted">Default text alignment for booth numbers</small>
                    </div>
                    
                    <hr class="my-4" style="border-color: #dee2e6;">
                    <h6 class="mb-3" style="color: #495057; font-weight: 600;">
                        <i class="fas fa-shadow"></i> Shadow & Effects
                    </h6>
                    
                    <div class="form-group">
                        <label for="defaultBoxShadow">
                            <i class="fas fa-box"></i> Default Box Shadow
                        </label>
                        <select class="form-control" id="defaultBoxShadow">
                            <option value="none">None</option>
                            <option value="0 2px 4px rgba(0,0,0,0.1)">Small</option>
                            <option value="0 2px 8px rgba(0,0,0,0.2)" selected>Medium (Default)</option>
                            <option value="0 4px 12px rgba(0,0,0,0.3)">Large</option>
                            <option value="0 6px 16px rgba(0,0,0,0.4)">Extra Large</option>
                            <option value="0 0 10px rgba(0,123,255,0.5)">Glow Blue</option>
                            <option value="0 0 10px rgba(40,167,69,0.5)">Glow Green</option>
                            <option value="0 0 10px rgba(255,193,7,0.5)">Glow Yellow</option>
                        </select>
                        <small class="form-text text-muted">Default shadow effect for new booths</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyBoothSettings">
                    <i class="fas fa-check"></i> Apply Settings
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/html2canvas/html2canvas.min.js') }}"></script>
@php
    $fpdCurrentFloorPlan = (isset($currentFloorPlan) && $currentFloorPlan)
        ? ['id' => $currentFloorPlan->id, 'name' => $currentFloorPlan->name, 'floor_image' => $currentFloorPlan->floor_image]
        : null;
@endphp
<script>
window.FPD = {
    floorPlanId: @json($floorPlanId ?? null),
    canEditCanvas: @json(isset($canEditCanvas) && $canEditCanvas ? true : false),
    canvasWidth: @json(isset($canvasWidth) && $canvasWidth ? (int)$canvasWidth : 1200),
    canvasHeight: @json(isset($canvasHeight) && $canvasHeight ? (int)$canvasHeight : 800),
    canvasTextItems: @json($canvasTextItems ?? []),
    boothsData: @json($boothsForJS),
    currentFloorPlan: @json($fpdCurrentFloorPlan),
    floorPlanImageUrl: @json($floorImageUrl ?? ''),
    floorPlanImagePath: @json($floorImage ?? ''),
    routes: {
        boothsIndex: @json(route('booths.index')),
        clientsSearch: @json(route('clients.search')),
        boothsRemoveFloorplan: @json(route('booths.remove-floorplan')),
        floorPlansPublic: @json(route('floor-plans.public', ':id')),
    },
    urls: {
        booths: @json(url('booths')),
        books: @json(url('books')),
    },
};
</script>
<script src="{{ asset('js/floor-plan-designer.js') }}"></script>
@endpush
