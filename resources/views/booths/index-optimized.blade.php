@extends('layouts.app')

@section('title', 'Booth Floor Plan')

@push('styles')
{{-- Load external CSS for better performance and caching --}}
<link rel="stylesheet" href="{{ asset('css/floorplan.css') }}?v={{ filemtime(public_path('css/floorplan.css')) }}">
@endpush

@section('content')
<div class="container-fluid mt-2 mb-2">
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
                    <button class="toolbar-btn" id="btnMeasureTool" title="Measure Tool (M)" data-tool="measure">
                        <i class="fas fa-ruler"></i>
                        <span class="tool-label">Measure</span>
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
                <button class="toolbar-btn" id="btnDelete" title="Delete (Del)">
                    <i class="fas fa-trash"></i>
                </button>
                <div class="toolbar-divider"></div>
                <button class="toolbar-btn" id="btnErrorLog" title="Error Log" onclick="ErrorLogger.togglePanel(); return false;" style="position: relative;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="errorLogBadge" style="display: none; position: absolute; top: -5px; right: -5px; background: #ff4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 10px; font-weight: bold;">0</span>
                </button>
                <div class="toolbar-divider"></div>
                <button class="toolbar-btn" id="btnToggleProperties" title="Toggle Properties Panel (Double-click to open)" style="background: rgba(40, 167, 69, 0.3);">
                    <i class="fas fa-cog"></i> <span id="propertiesToggleText" style="font-size: 10px;">ON</span>
            </button>
                <button class="toolbar-btn" id="btnToggleBoothNumbers" title="Toggle Booth Numbers Sidebar" style="background: rgba(102, 126, 234, 0.3);">
                    <i class="fas fa-th"></i>
            </button>
                <button class="toolbar-btn" id="btnClearCanvas" title="Clear Canvas" style="background: rgba(220, 53, 69, 0.3);">
                    <i class="fas fa-eraser"></i>
            </button>
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
                    <button class="toolbar-btn" id="zoomReset" title="Reset Zoom">
                <i class="fas fa-home"></i>
            </button>
                    <button class="toolbar-btn" id="zoomFit" title="Fit to Canvas (Center and Fit Image)">
                <i class="fas fa-expand-arrows-alt"></i>
            </button>
                </div>
            </div>
            <div class="toolbar-section">
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
                <button class="toolbar-btn" id="btnShowBooths" title="Show All Booths (Flash Effect)" style="background: rgba(255, 193, 7, 0.3);">
                    <i class="fas fa-th"></i> <span id="boothCountBadge" style="font-size: 10px; margin-left: 4px;">0</span>
                </button>
                <button class="toolbar-btn" id="btnSettings" title="Canvas Settings">
                    <i class="fas fa-cog"></i>
                </button>
                <button class="toolbar-btn" id="btnPrint" title="Print Floorplan" style="background: rgba(40, 167, 69, 0.3);">
                    <i class="fas fa-print"></i>
                </button>
                <button class="toolbar-btn btn-primary" id="btnSave" title="Save Floor Plan">
                    <i class="fas fa-save"></i> Save
            </button>
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
                    <div class="mb-2 d-flex align-items-center" style="gap:8px;">
                        <button class="btn btn-sm btn-primary btn-block" id="btnAddZoneMain" style="flex:1;">
                            <i class="fas fa-plus"></i> Add Zone
                        </button>
                    </div>
                    <div class="sidebar-search mb-2">
                        <input type="text" class="form-control form-control-sm" id="boothSearchSidebar" placeholder="Search booths...">
                    </div>
                    <div class="blogs" id="boothNumbersContainer">
                            @php
                                // Group booths by zone (extract first letter from booth number)
                                $zones = [];
                                foreach($booths as $booth) {
                                    $boothNumber = $booth->booth_number;
                                    // Extract zone from booth number (first letter or first character)
                                    $zone = '';
                                    if (preg_match('/^([A-Za-z]+)/', $boothNumber, $matches)) {
                                        $zone = strtoupper($matches[1]);
                                    } else {
                                        // If no letter found, use first character or default to "Other"
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
                                            <i class="fas fa-chevron-down zone-chevron"></i>
                                            <span class="zone-name">Zone {{ $zoneName }}</span>
                                            <span class="zone-count">({{ count($zoneBooths) }})</span>
                                        </div>
                                        <button class="btn-add-all-zone" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Add All Booths in Zone {{ $zoneName }} to Canvas"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.addAllZoneToCanvas('{{ $zoneName }}');">
                                            <i class="fas fa-plus-circle"></i> Add All
                                        </button>
                                        <button class="btn-add-all-zone-click" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Add All Booths in Zone {{ $zoneName }} - Click on Canvas to Place"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.enableClickToPlaceMode('{{ $zoneName }}');">
                                            <i class="fas fa-crosshairs"></i>
                                        </button>
                                        <button class="btn-zone-settings" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Zone Settings - Adjust All Booths in Zone {{ $zoneName }}"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.openZoneSettings('{{ $zoneName }}');">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <button class="btn-zone-clear" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Clear Zone {{ $zoneName }} - Return All Booths to Booth Number Area"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.clearZoneBooths('{{ $zoneName }}');">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        <button class="btn-zone-zoom" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Zoom to Zone {{ $zoneName }} - Fit All Booths in View"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.zoomToZone('{{ $zoneName }}');">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                        <button class="btn-zone-add-new" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Add New Booth ID to Zone {{ $zoneName }}"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.showAddBoothModal('{{ $zoneName }}');">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button class="btn-zone-delete" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Delete Booths from Zone {{ $zoneName }}"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.showDeleteBoothModal('{{ $zoneName }}');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="zone-content" id="zoneContent{{ $zoneName }}">
                                        @foreach($zoneBooths as $booth)
                            <div class="booth-number-item" 
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
                     style="@if(file_exists(public_path('images/map.jpg')))
                     background-image: url('{{ asset('images/map.jpg') }}'); background-size: 100% 100%; background-repeat: no-repeat; background-position: top left; background-attachment: local;
                     @else
                     background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                     @endif">
                    @if(file_exists(public_path('images/map.jpg')))
                        <img src="{{ asset('images/map.jpg') }}" 
                             id="floorplanImageElement"
                             alt="Floor Plan Map"
                             style="display: none;"/>
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

{{-- Include all modal dialogs --}}
@include('booths.partials.modals')
@endsection

@push('scripts')
{{-- Initialize booth data for JavaScript --}}
<script>
    // Pass booth data from PHP to JavaScript
    window.boothsData = @json($boothsForJS);
</script>
{{-- html2canvas for PNG export --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
{{-- Load external JS for better performance and caching --}}
<script src="{{ asset('js/floorplan.js') }}?v={{ filemtime(public_path('js/floorplan.js')) }}"></script>
@endpush
