<?php $__env->startSection('title', 'Booth Floor Plan'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Advanced Floor Plan Designer Styles - Modern Minimal */
.floorplan-designer {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 120px);
    min-height: 600px;
    background: #0b1020;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.24);
    border: 1px solid rgba(255, 255, 255, 0.04);
}

/* Toolbar - Compact, Modern */
.designer-toolbar {
    background: #0f162e;
    padding: 6px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    z-index: 1000;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    min-height: 40px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

/* Information Toolbar - Compact, Modern */
.info-toolbar {
    position: sticky;
    top: 0;
    background: #0d1326;
    padding: 6px 12px;
    display: flex !important;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    z-index: 999;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.24);
    border-bottom: 1px solid rgba(255,255,255,0.08);
    min-height: 38px;
    visibility: visible !important;
    opacity: 1 !important;
}

.info-toolbar-content {
    display: flex;
    align-items: center;
    gap: 15px;
    width: 100%;
    flex-wrap: wrap;
}

.info-section {
    display: flex;
    align-items: center;
    gap: 5px;
}

.info-label {
    color: rgba(255,255,255,0.7);
    font-size: 11px; /* Reduced from 13px */
    font-weight: 600;
    white-space: nowrap;
}

.info-value {
    color: #fff;
    font-size: 12px; /* Reduced from 14px */
    font-weight: 700;
    font-family: 'Courier New', monospace;
    background: rgba(255,255,255,0.1);
    padding: 3px 8px; /* Reduced from 4px 10px */
    border-radius: 3px; /* Reduced from 4px */
    min-width: 35px; /* Reduced from 40px */
    max-width: 55px; /* Reduced from 60px */
    width: auto;
    display: inline-block;
    text-align: center;
    cursor: default;
    transition: all 0.2s;
    box-sizing: border-box;
}

.info-value.info-editable {
    cursor: pointer;
}

.info-value.info-editable:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.05);
}

.info-value.info-editing {
    background: rgba(255,255,255,0.3);
    border: 2px solid rgba(255,255,255,0.5);
    outline: none;
}

.info-value input,
.info-edit-input {
    background: rgba(255, 255, 255, 0.2) !important;
    border: 1px solid #667eea !important;
    color: #fff !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    font-family: 'Courier New', monospace !important;
    text-align: center !important;
    width: 100% !important;
    min-width: 40px !important;
    max-width: 60px !important;
    padding: 4px 10px !important;
    margin: 0 !important;
    outline: none !important;
    border-radius: 4px !important;
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    box-sizing: border-box !important;
}

.info-value input::-webkit-inner-spin-button,
.info-value input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.info-value input[type=number] {
    -moz-appearance: textfield;
}

.info-divider {
    width: 1px;
    height: 30px;
    background: rgba(255,255,255,0.2);
    margin: 0 5px;
}

.toolbar-section {
    display: flex;
    align-items: center;
    gap: 8px;
}

.toolbar-btn {
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    color: #e5e7eb;
    padding: 5px 10px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.18s ease;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    min-height: 28px;
    backdrop-filter: blur(6px);
}

.toolbar-btn:hover {
    background: rgba(255,255,255,0.14);
    transform: translateY(-1px);
    color: #fff;
}

.toolbar-btn.active {
    background: #2563eb;
    border-color: #1d4ed8;
    color: #fff;
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
}

.toolbar-btn.btn-primary {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border: 1px solid #1d4ed8;
    color: #fff;
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
}

.toolbar-divider {
    width: 1px;
    height: 20px; /* Reduced from 24px */
    background: rgba(255,255,255,0.3);
    margin: 0 3px; /* Reduced from 4px */
}

/* Dropdown styles */
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    min-width: 200px;
    padding: 5px 0;
    margin-top: 5px;
    background: #0f162e;
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 8px;
    box-shadow: 0 14px 32px rgba(0,0,0,0.28);
}

.dropdown.show .dropdown-menu {
    display: block;
}

.dropdown-item {
    padding: 10px 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #e5e7eb;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
    cursor: pointer;
}

.dropdown-item:hover,
.dropdown-item:focus {
    background: rgba(255,255,255,0.06);
    color: #fff;
}

.dropdown-divider-item {
    margin: 5px 0;
    border-top: 1px solid #e9ecef;
}

.zoom-level {
    color: white;
    font-weight: 600;
    min-width: 45px; /* Reduced from 50px */
    font-size: 12px; /* Added smaller font */
    text-align: center;
    font-size: 13px;
}

/* Main Content Row */
.designer-main-row {
    display: flex;
    flex: 1;
    overflow: hidden;
}

/* Sidebar */
.designer-sidebar {
    width: 280px;
    background: white;
    border-right: 1px solid #dee2e6;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease;
    z-index: 500;
    flex-shrink: 0;
}

.designer-sidebar.collapsed {
    transform: translateX(-100%);
}

.sidebar-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
}

.sidebar-header h6 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
}

.sidebar-search input {
    border-radius: 5px;
    border: 1px solid #ced4da;
}

/* Canvas Container */
.canvas-container {
    flex: 1;
    position: relative;
    overflow: hidden; /* No scrollbars - use panning instead */
}

/* Zoom Selection Rectangle (Photoshop-like) */
.zoom-selection {
    position: absolute;
    border: 2px dashed #667eea;
    background: rgba(102, 126, 234, 0.1);
    pointer-events: none;
    z-index: 10000;
    display: none;
    box-sizing: border-box;
}

/* Booth Statistics - Sticky and Collapsible */
.booth-statistics-card {
    transition: all 0.3s ease;
}

.booth-statistics-card .card-header {
    position: sticky;
    top: 56px; /* Bootstrap navbar height (adjust if your navbar is different) */
    z-index: 997; /* Below navbar (typically 1000) but above content */
    user-select: none;
    margin: 0;
    border-radius: 0; /* Remove border radius when sticky */
}

.booth-statistics-card .card-header:hover {
    opacity: 0.9;
}

/* When header is sticky, remove card margin and border radius */
.booth-statistics-card.sticky-header {
    margin-bottom: 0;
}

.booth-statistics-card.sticky-header .card-header {
    border-radius: 0;
}

.booth-statistics-card .card-body {
    transition: max-height 0.3s ease, padding 0.3s ease, opacity 0.3s ease;
    overflow: hidden;
}

.booth-statistics-card.collapsed .card-body {
    max-height: 0;
    padding-top: 0;
    padding-bottom: 0;
    opacity: 0;
}

.booth-statistics-card.collapsed #boothStatisticsIcon {
    transform: rotate(-90deg);
}

#boothStatisticsIcon {
    transition: transform 0.3s ease;
}

.canvas-container {
    background: #e9ecef;
    display: block;
    /* Ensure container fits the viewport */
    min-width: 0;
    min-height: 0;
    width: 100%;
    height: 100%;
}

.floorplan-canvas {
    position: relative;
    background-size: 100% 100% !important;
    background-repeat: no-repeat !important;
    background-position: top left !important;
    cursor: default;
    pointer-events: auto;
    /* Unlimited canvas size - can expand infinitely */
    min-width: 10000px;
    min-height: 10000px;
    width: 10000px;
    height: 10000px;
    flex-shrink: 0;
    /* Ensure background image fills entire canvas */
    background-attachment: local;
    object-fit: fill;
    /* Position canvas at center initially */
    margin: 0;
    display: block;
}

.floorplan-canvas.tool-select {
    cursor: default;
}

.floorplan-canvas.tool-hand {
    cursor: grab;
}

.floorplan-canvas.tool-hand:active {
    cursor: grabbing;
}

.floorplan-canvas.drag-over {
    border: 3px dashed #007bff !important;
    background: rgba(0, 123, 255, 0.08) !important;
}

/* Booth Items in Sidebar */
#boothNumbersContainer {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.booth-number-item {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none !important;
    -webkit-user-drag: element;
    pointer-events: auto !important;
    touch-action: none;
    -webkit-touch-callout: none;
    background: #fff;
    color: #000;
    border: 1px solid #aaa;
    cursor: grab !important;
    width: 45px;
    height: 40px;
    line-height: 38px;
    text-align: center;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.booth-number-item:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.booth-number-item.dragging {
    opacity: 0.5;
    transform: scale(0.9);
    cursor: grabbing !important;
}

/* Dropped Booths on Canvas */
.dropped-booth {
    position: absolute;
    background: #fff;
    border: 2px solid #007bff;
    border-radius: 6px;
    padding: 0;
    cursor: move;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    z-index: 10;
    min-width: 5px;
    min-height: 5px;
    user-select: none;
    transform-origin: center center;
    pointer-events: auto !important;
    /* Center text both horizontally and vertically */
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    overflow: hidden;
    word-wrap: break-word;
    line-height: 1.2;
}

.dropped-booth.selected {
    border-color: #ffc107;
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.3);
    outline: 2px solid #ffc107;
    outline-offset: 2px;
}

.dropped-booth.dragging {
    opacity: 0.85;
    cursor: grabbing !important;
    z-index: 1000 !important;
    box-shadow: 0 4px 16px rgba(0,0,0,0.5);
    transform: scale(1.02) !important;
    transition: none !important;
    user-select: none !important;
}

/* Transform Controls (Compact corner display) */
.transform-controls {
    position: absolute;
    top: 2px !important;
    left: 2px !important;
    transform: none !important;
    display: none !important; /* Always hidden - info shown in toolbar instead */
    flex-direction: column !important;
    gap: 2px !important;
    background: rgba(0, 0, 0, 0.85) !important;
    padding: 4px 6px !important;
    border-radius: 4px !important;
    z-index: 9999 !important;
    pointer-events: auto !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3) !important;
    border: 1px solid rgba(255,255,255,0.3) !important;
    white-space: nowrap !important;
    font-size: 10px !important;
    line-height: 1.2 !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

.transform-controls[style*="flex"] {
    display: flex !important;
}

.transform-controls span {
    display: inline-block;
    min-width: 35px;
    padding: 2px 4px;
    font-size: 10px;
    font-weight: 600;
    text-align: center;
    color: #fff;
    pointer-events: none;
}

.transform-controls label {
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    margin-right: 3px;
    pointer-events: none;
    min-width: 12px;
    text-align: right;
    display: inline-block;
}

.transform-controls .control-group {
    display: flex;
    align-items: center;
    gap: 2px;
    flex-shrink: 0;
}

.dropped-booth.status-1 { background: #fff; border-color: #28a745; }
.dropped-booth.status-2 { background: #ef7070; border-color: #dc3545; }
.dropped-booth.status-3 { background: #28a745; border-color: #20c997; }
.dropped-booth.status-5 { background: #dc3545; border-color: #c82333; }

/* Resize Handles */
.resize-handle {
    position: absolute;
    background: #007bff;
    border: 2px solid #fff;
    border-radius: 50%;
    cursor: nwse-resize;
    z-index: 100;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    pointer-events: auto !important;
    touch-action: none;
    /* Size will be set dynamically via JavaScript based on booth dimensions */
}

.resize-handle.nw { cursor: nwse-resize; }
.resize-handle.ne { cursor: nesw-resize; }
.resize-handle.sw { cursor: nesw-resize; }
.resize-handle.se { cursor: nwse-resize; }
.resize-handle.n { cursor: ns-resize; }
.resize-handle.s { cursor: ns-resize; }
.resize-handle.w { cursor: ew-resize; }
.resize-handle.e { cursor: ew-resize; }

.resize-handle:hover {
    background: #0056b3;
    transform: scale(1.2);
}

/* Rotation Handle */
.rotate-handle {
    position: absolute;
    top: -30px;
    left: 50%;
    margin-left: -10px;
    width: 20px;
    height: 20px;
    background: #ffc107;
    border: 2px solid #fff;
    border-radius: 50%;
    cursor: grab;
    z-index: 100;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: auto !important;
    touch-action: none;
}

.rotate-handle:active {
    cursor: grabbing;
}

.rotate-handle:hover {
    background: #e0a800;
    transform: scale(1.2);
}

.rotate-handle::before {
    content: '↻';
    font-size: 12px;
    color: #fff;
    font-weight: bold;
}

/* Rotation Indicator */
.rotation-indicator {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 3px;
    height: 0;
    background: linear-gradient(to top, #ffc107, #ff9800);
    transform-origin: bottom center;
    z-index: 99;
    pointer-events: none;
    opacity: 0.9;
    transition: opacity 0.2s;
    box-shadow: 0 0 4px rgba(255, 193, 7, 0.5);
}

.rotation-indicator::after {
    content: '';
    position: absolute;
    top: -6px;
    left: -4px;
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 8px solid #ffc107;
    filter: drop-shadow(0 2px 2px rgba(0,0,0,0.3));
}

.rotation-indicator-text {
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    color: #000;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    white-space: nowrap;
    pointer-events: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

/* Properties Panel - Popup Modal */
.properties-panel {
    width: 350px;
    max-width: 90vw;
    max-height: 80vh;
    background: white;
    border-radius: 12px;
    display: none;
    flex-direction: column;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    transition: transform 0.2s ease, opacity 0.2s ease;
    z-index: 10000;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    opacity: 0;
    overflow: hidden;
}

.properties-panel.active {
    display: flex;
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
}

/* Backdrop overlay */
.properties-panel-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: none;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.properties-panel-backdrop.active {
    display: block;
    opacity: 1;
}

.panel-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
}

.panel-content {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    max-height: calc(80vh - 60px); /* Account for header height */
}

/* Grid Overlay */
.grid-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        linear-gradient(rgba(0, 0, 0, 0.08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 0, 0, 0.08) 1px, transparent 1px);
    background-size: 10px 10px; /* Smaller grid cells (10px x 10px) */
    pointer-events: none;
    z-index: 1;
    display: none;
    opacity: 0.6; /* Make grid more visible */
}

.grid-overlay.visible {
    display: block;
}

/* Selection Box */
.selection-box {
    position: absolute;
    border: 2px dashed #007bff;
    background: rgba(0, 123, 255, 0.1);
    pointer-events: none;
    z-index: 100;
    display: none;
}

.selection-box.active {
    display: block;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-2 mb-2">
    <?php if(auth()->guard()->check()): ?>
    <!-- Statistics Dashboard -->
    <div class="card mb-2 shadow-sm booth-statistics-card" id="boothStatisticsCard">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center sticky-top" style="cursor: pointer; top: 56px; z-index: 997; padding: 8px 12px;" id="boothStatisticsHeader">
            <h6 class="mb-0" style="font-size: 14px;"><i class="fas fa-chart-bar me-1"></i>Booth Statistics</h6>
            <button class="btn btn-sm btn-light" id="boothStatisticsToggle" title="Expand/Collapse">
                <i class="fas fa-chevron-down" id="boothStatisticsIcon"></i>
            </button>
        </div>
        <div class="card-body" id="boothStatisticsBody" style="padding: 10px 12px;">
            <div class="row g-2">
                <div class="col-md-3 col-sm-6 mb-1">
                    <div class="stat-box text-center p-2 border rounded">
                        <div class="stat-value text-primary fw-bold" style="font-size: 1.5rem;"><?php echo e($totalBooths); ?></div>
                        <div class="stat-label text-muted" style="font-size: 0.75rem;">Total Booths</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-1">
                    <div class="stat-box text-center p-2 border rounded bg-success bg-opacity-10">
                        <div class="stat-value text-success fw-bold" style="font-size: 1.5rem;"><?php echo e($availableBooths); ?></div>
                        <div class="stat-label text-muted" style="font-size: 0.75rem;">Available</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-1">
                    <div class="stat-box text-center p-2 border rounded bg-info bg-opacity-10">
                        <div class="stat-value text-info fw-bold" style="font-size: 1.5rem;"><?php echo e($bookedBooths); ?></div>
                        <div class="stat-label text-muted" style="font-size: 0.75rem;">Booked</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-1">
                    <div class="stat-box text-center p-2 border rounded bg-warning bg-opacity-10">
                        <div class="stat-value text-warning fw-bold" style="font-size: 1.5rem;"><?php echo e($reservedBoothsCount); ?></div>
                        <div class="stat-label text-muted" style="font-size: 0.75rem;">Reserved</div>
                    </div>
                </div>
                    </div>
                </div>
                    </div>
    <?php endif; ?>

    <!-- Advanced Floor Plan Designer -->
    <div class="floorplan-designer">
        <!-- Toolbar -->
        <div class="designer-toolbar">
            <div class="toolbar-section">
                <!-- Tools (Photoshop-like) -->
                <button class="toolbar-btn active" id="btnSelect" title="Move Tool (V)" data-tool="select">
                    <i class="fas fa-mouse-pointer"></i>
            </button>
                <button class="toolbar-btn" id="btnHand" title="Hand Tool (H)" data-tool="hand">
                    <i class="fas fa-hand-paper"></i>
            </button>
                <div class="toolbar-divider"></div>
                <button class="toolbar-btn" id="btnGrid" title="Toggle Grid (Show/Hide)" data-toggle="grid">
                    <i class="fas fa-th"></i>
            </button>
                <button class="toolbar-btn active" id="btnSnap" title="Snap to Grid" data-toggle="snap">
                    <i class="fas fa-magnet"></i>
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
                <button class="toolbar-btn" id="btnToggleProperties" title="Toggle Properties Panel (Double-click to open)" style="background: rgba(40, 167, 69, 0.3);">
                    <i class="fas fa-cog"></i> <span id="propertiesToggleText" style="font-size: 10px;">ON</span>
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
                <button class="toolbar-btn" id="btnSettings" title="Canvas Settings">
                    <i class="fas fa-cog"></i>
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
        
        <!-- Main Content Row: Sidebar + Canvas -->
        <div class="designer-main-row">
            <!-- Sidebar - Booth Numbers Panel (Left Side) -->
            <div class="designer-sidebar">
                <div class="sidebar-header">
                    <h6><i class="fas fa-th"></i> Booth Numbers</h6>
                    <button class="btn btn-sm btn-link text-white" id="toggleSidebar" title="Toggle Sidebar">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                </div>
                <div class="sidebar-content">
                    <div class="sidebar-search mb-2">
                        <input type="text" class="form-control form-control-sm" id="boothSearchSidebar" placeholder="Search booths...">
                    </div>
                    <div class="blogs" id="boothNumbersContainer">
        <?php $__currentLoopData = $booths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booth): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="booth-number-item" 
                                 draggable="true"
                data-booth-id="<?php echo e($booth->id); ?>"
                data-booth-number="<?php echo e($booth->booth_number); ?>"
                data-booth-status="<?php echo e($booth->status); ?>"
                data-client-id="<?php echo e($booth->client_id); ?>"
                data-user-id="<?php echo e($booth->userid); ?>"
                data-category-id="<?php echo e($booth->category_id); ?>"
                data-sub-category-id="<?php echo e($booth->sub_category_id); ?>"
                data-asset-id="<?php echo e($booth->asset_id); ?>"
                                 data-booth-type-id="<?php echo e($booth->booth_type_id); ?>">
                <?php echo e($booth->booth_number); ?>

    </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                    </div>
                    </div>
                    
            <!-- Main Canvas Container -->
            <div id="printContainer" class="canvas-container">
                <!-- Canvas Area -->
                <div id="print" class="floorplan-canvas" 
                     style="<?php if(file_exists(public_path('images/map.jpg'))): ?>
                     background-image: url('<?php echo e(asset('images/map.jpg')); ?>'); background-size: 100% 100%; background-repeat: no-repeat; background-position: top left; background-attachment: local;
                     <?php else: ?>
                     background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                     <?php endif; ?>">
                    <?php if(file_exists(public_path('images/map.jpg'))): ?>
                        <img src="<?php echo e(asset('images/map.jpg')); ?>" 
                             id="floorplanImageElement"
                             alt="Floor Plan Map"
                             style="display: none;"/>
                    <?php endif; ?>
                    </div>
                    
                <!-- Grid Overlay -->
                <div id="gridOverlay" class="grid-overlay"></div>
                
                <!-- Selection Box -->
                <div id="selectionBox" class="selection-box"></div>
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
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="floorplanImage">
                            <i class="fas fa-image"></i> Select Floorplan Image
                        </label>
                        <input type="file" class="form-control-file" id="floorplanImageInput" name="floorplan_image" accept="image/*" required>
                        <small class="form-text text-muted" id="uploadSizeLimitText">Supported formats: JPG, PNG, GIF. Maximum size: 10MB</small>
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
                        <input type="number" class="form-control" id="gridSize" min="5" max="100" step="1" value="10">
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
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- html2canvas for PNG export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
// ============================================
// Advanced Floor Plan Designer
// ============================================

// Global State
const FloorPlanDesigner = {
    // State
    draggedElement: null,
    draggedBoothData: null,
    selectedBooths: [],
    history: [],
    historyIndex: -1,
    gridEnabled: false, // Grid visibility (can be toggled)
    snapEnabled: true,
    gridSize: 10, // Smaller grid size (10px instead of 20px)
    zoomLevel: 1,
    panzoomInstance: null,
    canvasWidth: 1200, // Default canvas width
    canvasHeight: 800, // Default canvas height
    canvasResolution: 300, // Default export resolution (DPI)
    isZoomSelecting: false, // Track if user is selecting area to zoom (Ctrl+Space)
    zoomSelectionStart: null, // Start position of zoom selection {x, y}
    zoomSelectionElement: null, // The selection rectangle element
    ctrlSpacePressed: false, // Track Ctrl+Space key combination
    uploadSizeLimit: 10, // Default upload size limit in MB (0 = no limit)
    propertiesPanelEnabled: true, // Enable/disable Properties panel auto-open
    // Default booth settings
    defaultBoothWidth: 80,
    defaultBoothHeight: 50,
    defaultBoothRotation: 0,
    defaultBoothZIndex: 10,
    defaultBoothFontSize: 14,
    defaultBoothBorderWidth: 2,
    defaultBoothBorderRadius: 6,
    defaultBoothOpacity: 1.00,
    
    // Initialize
    init: function() {
        // Ensure currentTool is set
        if (!this.currentTool) {
            this.currentTool = 'select';
        }
    
        this.setupDragAndDrop();
        this.setupToolbar();
        this.setupCanvas();
        this.setupKeyboard();
        this.setupZoomSelection(); // Setup Photoshop-like zoom selection (Ctrl+Space)
        this.loadCanvasSettings(); // Load saved canvas settings
        // Ensure canvas has a fixed size (in case no saved settings exist)
        if (!localStorage.getItem('canvasWidth') || !localStorage.getItem('canvasHeight')) {
            this.setCanvasSize(this.canvasWidth, this.canvasHeight);
        }
        
        // Check if there's an existing floorplan image and resize canvas to match
        this.detectAndResizeCanvasToImage();
        
        this.loadSavedPositions();
        this.saveState();
        
        // Auto-fit canvas to show entire image on page load
        // Wait a bit to ensure everything is fully loaded (canvas, image, booths, panzoom)
        setTimeout(function() {
            if (self.panzoomInstance) {
                // Use no animation for initial load (faster)
                self.fitCanvasToView(false);
            }
        }, 800);
    },
    
    // Setup Drag and Drop
    setupDragAndDrop: function() {
        const self = this;
        const canvas = document.getElementById('print');
        
        if (!canvas) {
            return;
        }
        
        // Drag start from sidebar - use native event listeners
        document.addEventListener('dragstart', function(e) {
            let item = e.target;
            while (item && !item.classList.contains('booth-number-item')) {
                item = item.parentElement;
            }
            
            if (!item || !item.classList.contains('booth-number-item')) {
                return;
            }
            
            self.draggedElement = item;
            self.draggedBoothData = {
                id: item.getAttribute('data-booth-id'),
                number: item.getAttribute('data-booth-number'),
                status: item.getAttribute('data-booth-status'),
                clientId: item.getAttribute('data-client-id') || '',
                userId: item.getAttribute('data-user-id') || '',
                categoryId: item.getAttribute('data-category-id') || '',
                subCategoryId: item.getAttribute('data-sub-category-id') || '',
                assetId: item.getAttribute('data-asset-id') || '',
                boothTypeId: item.getAttribute('data-booth-type-id') || ''
            };
            
            item.classList.add('dragging');
            
            if (e.dataTransfer) {
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', self.draggedBoothData.id);
                e.dataTransfer.setData('application/json', JSON.stringify(self.draggedBoothData));
            }
        }, true);
        
        // Drag end
        document.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('booth-number-item')) {
                e.target.classList.remove('dragging');
            }
        });
        
        // Canvas drop handlers - MUST use capture phase to fire before mousedown handler
        canvas.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (e.dataTransfer) {
                e.dataTransfer.dropEffect = 'move';
            }
            this.classList.add('drag-over');
            return false;
        }, true); // Capture phase
        
        canvas.addEventListener('dragleave', function(e) {
            e.preventDefault();
            if (!this.contains(e.relatedTarget)) {
                this.classList.remove('drag-over');
            }
            return false;
        }, true); // Capture phase
        
        canvas.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            this.classList.remove('drag-over');
            
            if (!self.draggedBoothData) {
                return false;
            }
            
            const rect = this.getBoundingClientRect();
            
            // Get current zoom and pan transform from Panzoom
            let scale = 1;
            let panX = 0;
            let panY = 0;
            if (self.panzoomInstance) {
                if (self.panzoomInstance.getScale) {
                    scale = self.panzoomInstance.getScale();
                }
                if (self.panzoomInstance.getTransform) {
                    const transform = self.panzoomInstance.getTransform();
                    panX = transform.x || 0;
                    panY = transform.y || 0;
                }
            }
            
            // Convert screen coordinates to canvas coordinates (accounting for zoom and pan)
            let x = (e.clientX - rect.left - panX) / scale;
            let y = (e.clientY - rect.top - panY) / scale;
            
            // Always snap to grid when dropping (force grid snapping)
            x = Math.round(x / self.gridSize) * self.gridSize;
            y = Math.round(y / self.gridSize) * self.gridSize;
            
            self.addBoothToCanvas(self.draggedBoothData, x, y);
            self.draggedBoothData = null;
            return false;
        }, true); // Capture phase - fires BEFORE mousedown handler
        
        // Ensure all booth items are draggable
        const boothItems = document.querySelectorAll('.booth-number-item');
        boothItems.forEach(function(item) {
            item.setAttribute('draggable', 'true');
            item.draggable = true; // Also set property directly
            item.style.pointerEvents = 'auto';
            item.style.userSelect = 'none';
            item.style.webkitUserDrag = 'element';
            item.style.cursor = 'grab';
            
        });
        
    },
    
    // Add booth to canvas
    addBoothToCanvas: function(boothData, x, y) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Check if booth already exists on canvas
        const existingBooth = canvas.querySelector('[data-booth-id="' + boothData.id + '"]');
        if (existingBooth) {
            // Update position
            existingBooth.style.left = x + 'px';
            existingBooth.style.top = y + 'px';
            existingBooth.setAttribute('data-x', x);
            existingBooth.setAttribute('data-y', y);
            
            // Get all properties from the element before saving
            const width = parseFloat(existingBooth.style.width) || parseFloat(existingBooth.getAttribute('data-width')) || self.defaultBoothWidth;
            const height = parseFloat(existingBooth.style.height) || parseFloat(existingBooth.getAttribute('data-height')) || self.defaultBoothHeight;
            const rotation = parseFloat(existingBooth.getAttribute('data-rotation')) || parseFloat(existingBooth.style.transform.match(/rotate\(([^)]+)\)/)?.[1]) || self.defaultBoothRotation;
            const zIndex = parseFloat(existingBooth.style.zIndex) || parseFloat(existingBooth.getAttribute('data-z-index')) || self.defaultBoothZIndex;
            const fontSize = parseFloat(existingBooth.style.fontSize) || parseFloat(existingBooth.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const borderWidth = parseFloat(existingBooth.style.borderWidth) || parseFloat(existingBooth.getAttribute('data-border-width')) || self.defaultBoothBorderWidth;
            const borderRadius = parseFloat(existingBooth.style.borderRadius) || parseFloat(existingBooth.getAttribute('data-border-radius')) || self.defaultBoothBorderRadius;
            const opacity = parseFloat(existingBooth.style.opacity) || parseFloat(existingBooth.getAttribute('data-opacity')) || self.defaultBoothOpacity;
            
            this.saveBoothPosition(boothData.id, x, y, width, height, rotation, zIndex, fontSize, borderWidth, borderRadius, opacity);
            return;
        }
        
        const boothElement = this.createBoothElement(boothData);
        boothElement.style.left = x + 'px';
        boothElement.style.top = y + 'px';
        boothElement.setAttribute('data-x', x);
        boothElement.setAttribute('data-y', y);
        canvas.appendChild(boothElement);
        
        // Verify transform controls exist after appending
        const controlsCheck = boothElement.querySelector('.transform-controls');
        console.log('After appending to canvas - Transform controls found:', controlsCheck !== null);
        if (controlsCheck) {
            console.log('Transform controls computed display:', window.getComputedStyle(controlsCheck).display);
            console.log('Transform controls inline display:', controlsCheck.style.display);
        }
        
        this.makeBoothDraggable(boothElement);
        
        // Get all properties from the element after it's created and appended
        // Wait a moment to ensure element is fully rendered
        setTimeout(function() {
            const width = parseFloat(boothElement.style.width) || parseFloat(boothElement.getAttribute('data-width')) || self.defaultBoothWidth;
            const height = parseFloat(boothElement.style.height) || parseFloat(boothElement.getAttribute('data-height')) || self.defaultBoothHeight;
            const rotation = parseFloat(boothElement.getAttribute('data-rotation')) || self.defaultBoothRotation;
            const zIndex = parseFloat(boothElement.style.zIndex) || parseFloat(boothElement.getAttribute('data-z-index')) || self.defaultBoothZIndex;
            const fontSize = parseFloat(boothElement.style.fontSize) || parseFloat(boothElement.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const borderWidth = parseFloat(boothElement.style.borderWidth) || parseFloat(boothElement.getAttribute('data-border-width')) || self.defaultBoothBorderWidth;
            const borderRadius = parseFloat(boothElement.style.borderRadius) || parseFloat(boothElement.getAttribute('data-border-radius')) || self.defaultBoothBorderRadius;
            const opacity = parseFloat(boothElement.style.opacity) || parseFloat(boothElement.getAttribute('data-opacity')) || self.defaultBoothOpacity;
            
            self.saveBoothPosition(boothData.id, x, y, width, height, rotation, zIndex, fontSize, borderWidth, borderRadius, opacity);
            self.saveState();
        }, 100);
    },
    
    // Create booth element
    createBoothElement: function(boothData) {
        const div = document.createElement('div');
        div.className = 'dropped-booth status-' + boothData.status;
        div.setAttribute('data-booth-id', boothData.id);
        
        // Use boothData values if available, otherwise use default settings
        const width = boothData.width !== undefined && boothData.width !== null ? boothData.width : this.defaultBoothWidth;
        const height = boothData.height !== undefined && boothData.height !== null ? boothData.height : this.defaultBoothHeight;
        const rotation = boothData.rotation !== undefined && boothData.rotation !== null ? boothData.rotation : this.defaultBoothRotation;
        const zIndex = boothData.z_index !== undefined && boothData.z_index !== null ? boothData.z_index : this.defaultBoothZIndex;
        const fontSize = boothData.font_size !== undefined && boothData.font_size !== null ? boothData.font_size : this.defaultBoothFontSize;
        const borderWidth = boothData.border_width !== undefined && boothData.border_width !== null ? boothData.border_width : this.defaultBoothBorderWidth;
        const borderRadius = boothData.border_radius !== undefined && boothData.border_radius !== null ? boothData.border_radius : this.defaultBoothBorderRadius;
        const opacity = boothData.opacity !== undefined && boothData.opacity !== null ? boothData.opacity : this.defaultBoothOpacity;
        
        div.setAttribute('data-width', width);
        div.setAttribute('data-height', height);
        div.setAttribute('data-rotation', rotation);
        div.setAttribute('data-z-index', zIndex);
        div.setAttribute('data-font-size', fontSize);
        div.setAttribute('data-border-width', borderWidth);
        div.setAttribute('data-border-radius', borderRadius);
        div.setAttribute('data-opacity', opacity);
        div.setAttribute('data-x', boothData.x || 0);
        div.setAttribute('data-y', boothData.y || 0);
        div.textContent = boothData.number;
        div.style.position = 'absolute';
        div.style.width = width + 'px';
        div.style.height = height + 'px';
        div.style.transform = 'rotate(' + rotation + 'deg)';
        div.style.zIndex = zIndex;
        div.style.borderWidth = borderWidth + 'px';
        div.style.borderRadius = borderRadius + 'px';
        div.style.opacity = opacity;
        div.style.pointerEvents = 'auto';
        div.style.cursor = 'move';
        div.style.userSelect = 'none';
        
        // Calculate font size based on booth dimensions to ensure text fits
        // Use the smaller dimension to ensure text fits in both width and height
        const minDimension = Math.min(width, height);
        // Calculate font size: use 30-40% of the smallest dimension, but respect user's fontSize if set
        // If user fontSize is too large for the booth, scale it down
        const calculatedFontSize = Math.min(fontSize, Math.max(8, minDimension * 0.35));
        div.style.fontSize = calculatedFontSize + 'px';
        
        // Store both the user's preferred fontSize and the calculated one
        div.setAttribute('data-font-size', fontSize);
        div.setAttribute('data-calculated-font-size', calculatedFontSize);
        
        // Add resize handles (8 handles: corners + edges)
        const resizeHandles = ['nw', 'ne', 'sw', 'se', 'n', 's', 'w', 'e'];
        resizeHandles.forEach(function(handleClass) {
            const handle = document.createElement('div');
            handle.className = 'resize-handle ' + handleClass;
            handle.style.display = 'none'; // Hidden by default, shown when selected
            div.appendChild(handle);
        });
        
        // Calculate and set resize handle size based on booth dimensions
        this.updateResizeHandlesSize(div);
        
        // Add rotation handle
        const rotateHandle = document.createElement('div');
        rotateHandle.className = 'rotate-handle';
        rotateHandle.style.display = 'none'; // Hidden by default, shown when selected
        div.appendChild(rotateHandle);
        
        // Add rotation indicator (line showing rotation angle)
        const rotationIndicator = document.createElement('div');
        rotationIndicator.className = 'rotation-indicator';
        rotationIndicator.style.display = 'none'; // Hidden by default, shown when selected
        const rotationText = document.createElement('div');
        rotationText.className = 'rotation-indicator-text';
        rotationIndicator.appendChild(rotationText);
        div.appendChild(rotationIndicator);
        
        // Update rotation indicator
        this.updateRotationIndicator(div);
        
        // Add transform controls (Photoshop-like) - visible when selected
        const transformControls = document.createElement('div');
        transformControls.className = 'transform-controls';
        transformControls.innerHTML = '<div class="control-group"><label>X:</label><span class="transform-x">0</span></div>' +
                                      '<div class="control-group"><label>Y:</label><span class="transform-y">0</span></div>' +
                                      '<div class="control-group"><label>W:</label><span class="transform-w">' + width + '</span></div>' +
                                      '<div class="control-group"><label>H:</label><span class="transform-h">' + height + '</span></div>' +
                                      '<div class="control-group"><label>R:</label><span class="transform-r">' + rotation + '</span></div>';
        transformControls.style.display = 'none';
        div.appendChild(transformControls);
        
        return div;
    },
    
    // Update rotation indicator to show current rotation angle
    updateRotationIndicator: function(element) {
        const rotation = parseFloat(element.getAttribute('data-rotation')) || parseFloat(element.style.transform.match(/rotate\(([^)]+)\)/)?.[1]) || 0;
        const width = parseFloat(element.style.width) || parseFloat(element.getAttribute('data-width')) || 80;
        const height = parseFloat(element.style.height) || parseFloat(element.getAttribute('data-height')) || 50;
        
        const rotationIndicator = element.querySelector('.rotation-indicator');
        if (!rotationIndicator) return;
        
        // Calculate indicator line length (35-45% of smallest dimension)
        const minDimension = Math.min(width, height);
        const lineLength = Math.max(25, Math.min(50, minDimension * 0.4));
        
        // Set line height and position
        rotationIndicator.style.height = lineLength + 'px';
        rotationIndicator.style.width = Math.max(2, Math.min(4, minDimension * 0.05)) + 'px';
        rotationIndicator.style.transform = 'translate(-50%, -100%) rotate(' + rotation + 'deg)';
        
        // Update text
        const rotationText = rotationIndicator.querySelector('.rotation-indicator-text');
        if (rotationText) {
            rotationText.textContent = Math.round(rotation) + '°';
            // Scale text size based on booth size
            const textSize = Math.max(9, Math.min(13, minDimension * 0.15));
            rotationText.style.fontSize = textSize + 'px';
        }
        
        // Show indicator only if booth is selected
        if (element.classList.contains('selected')) {
            rotationIndicator.style.display = 'block';
        } else {
            rotationIndicator.style.display = 'none';
        }
    },
    
    // Update resize handles size based on booth dimensions
    updateResizeHandlesSize: function(element) {
        const width = parseFloat(element.style.width) || parseFloat(element.getAttribute('data-width')) || 80;
        const height = parseFloat(element.style.height) || parseFloat(element.getAttribute('data-height')) || 50;
        
        // Calculate handle size: 8-12% of the smallest dimension, with min 8px and max 16px
        const minDimension = Math.min(width, height);
        const handleSize = Math.max(8, Math.min(16, minDimension * 0.1));
        const handleOffset = handleSize / 2; // Half the size for positioning
        
        // Update all resize handles
        const handles = element.querySelectorAll('.resize-handle');
        handles.forEach(function(handle) {
            const handleClass = handle.className.split(' ')[1]; // Get direction class (nw, ne, etc.)
            
            // Set size
            handle.style.width = handleSize + 'px';
            handle.style.height = handleSize + 'px';
            
            // Update border width proportionally (1-2px based on handle size)
            const borderWidth = Math.max(1, Math.min(2, Math.round(handleSize / 8)));
            handle.style.borderWidth = borderWidth + 'px';
            
            // Update position based on handle type
            if (handleClass === 'nw') {
                handle.style.top = -handleOffset + 'px';
                handle.style.left = -handleOffset + 'px';
            } else if (handleClass === 'ne') {
                handle.style.top = -handleOffset + 'px';
                handle.style.right = -handleOffset + 'px';
            } else if (handleClass === 'sw') {
                handle.style.bottom = -handleOffset + 'px';
                handle.style.left = -handleOffset + 'px';
            } else if (handleClass === 'se') {
                handle.style.bottom = -handleOffset + 'px';
                handle.style.right = -handleOffset + 'px';
            } else if (handleClass === 'n') {
                handle.style.top = -handleOffset + 'px';
                handle.style.left = '50%';
                handle.style.marginLeft = -handleOffset + 'px';
            } else if (handleClass === 's') {
                handle.style.bottom = -handleOffset + 'px';
                handle.style.left = '50%';
                handle.style.marginLeft = -handleOffset + 'px';
            } else if (handleClass === 'w') {
                handle.style.left = -handleOffset + 'px';
                handle.style.top = '50%';
                handle.style.marginTop = -handleOffset + 'px';
            } else if (handleClass === 'e') {
                handle.style.right = -handleOffset + 'px';
                handle.style.top = '50%';
                handle.style.marginTop = -handleOffset + 'px';
            }
        });
        
        // Also update rotation handle size and position
        const rotateHandle = element.querySelector('.rotate-handle');
        if (rotateHandle) {
            const rotateHandleSize = Math.max(16, Math.min(24, minDimension * 0.15));
            const rotateHandleOffset = rotateHandleSize / 2;
            rotateHandle.style.width = rotateHandleSize + 'px';
            rotateHandle.style.height = rotateHandleSize + 'px';
            rotateHandle.style.marginLeft = -rotateHandleOffset + 'px';
            rotateHandle.style.top = -(rotateHandleSize + 5) + 'px';
            const rotateBorderWidth = Math.max(2, Math.min(3, Math.round(rotateHandleSize / 10)));
            rotateHandle.style.borderWidth = rotateBorderWidth + 'px';
        }
    },
    
    // Make booth draggable, resizable, and rotatable on canvas
    makeBoothDraggable: function(element) {
        const self = this;
        let isDragging = false;
        let startX, startY, initialX, initialY;
        
        // Click to select - only if it was a quick click (not a drag)
        // Use bubble phase so it fires AFTER mousedown
        element.addEventListener('click', function(e) {
            // Only handle left-clicks for selection
            if (e.button !== 0) {
                return;
            }
            
            // If mousedown never fired, initialize it now (fallback)
            if (mouseDownTime === 0) {
                mouseDownTime = Date.now();
                mouseDownPos.x = e.clientX;
                mouseDownPos.y = e.clientY;
            }
            
            // If we dragged, don't treat this as a selection click
            if (hasDragged) {
                hasDragged = false;
                mouseDownTime = 0;
                return;
            }
            
            // Check if this was a drag (mouse moved more than 5px or took more than 200ms)
            const clickTime = Date.now();
            const clickPos = {x: e.clientX, y: e.clientY};
            const timeDiff = clickTime - mouseDownTime;
            const moveDiff = Math.abs(clickPos.x - mouseDownPos.x) + Math.abs(clickPos.y - mouseDownPos.y);
            
            // If it was a drag, don't treat it as a click
            // Also check if timeDiff is unreasonably large (mousedown never fired properly)
            if (timeDiff > 200 || moveDiff > 5 || timeDiff > 10000) {
                mouseDownTime = 0;
                return;
            }
            
            // Stop propagation IMMEDIATELY to prevent canvas handler
            e.stopPropagation();
            e.stopImmediatePropagation();
            e.preventDefault();
            
            // Don't select if clicking on transform controls
            if (e.target.classList.contains('transform-controls') ||
                e.target.closest('.transform-controls')) {
                return;
            }
            
            // Only allow selection if select tool is active
            if (self.currentTool !== 'select') {
                self.currentTool = 'select';
            }
            
            document.querySelectorAll('.dropped-booth').forEach(function(booth) {
                booth.classList.remove('selected');
                const ctrl = booth.querySelector('.transform-controls');
                if (ctrl) {
                    ctrl.style.display = 'none';
                    ctrl.style.visibility = 'hidden';
                    ctrl.style.opacity = '0';
                }
            });
            
            // Clear info toolbar when deselecting
            self.updateInfoToolbar(null);
            
            element.classList.add('selected');
            const controls = element.querySelector('.transform-controls');
            
            // Update information toolbar with booth data first (but don't update if in edit mode)
            // Check if toolbar is already in edit mode - if so, skip update to preserve inputs
            const isToolbarEditing = document.querySelector('.info-value.info-editing');
            if (!isToolbarEditing) {
                self.updateInfoToolbar(element);
            }
            
            // Hide transform controls on booth (we show info in toolbar instead)
            if (controls) {
                controls.style.display = 'none';
                controls.style.visibility = 'hidden';
                controls.style.opacity = '0';
            }
            
            // Update transform control values (for internal use, but hidden)
                const x = parseFloat(element.style.left) || 0;
                const y = parseFloat(element.style.top) || 0;
                const w = parseFloat(element.style.width) || 80;
                const h = parseFloat(element.style.height) || 50;
                const r = parseFloat(element.getAttribute('data-rotation')) || 0;
                
            if (controls) {
                const xInput = controls.querySelector('.transform-x');
                const yInput = controls.querySelector('.transform-y');
                const wInput = controls.querySelector('.transform-w');
                const hInput = controls.querySelector('.transform-h');
                const rInput = controls.querySelector('.transform-r');
                
                if (xInput) xInput.textContent = Math.round(x);
                if (yInput) yInput.textContent = Math.round(y);
                if (wInput) wInput.textContent = Math.round(w);
                if (hInput) hInput.textContent = Math.round(h);
                if (rInput) rInput.textContent = Math.round(r);
            }
                
                // Show resize handles
                const handles = element.querySelectorAll('.resize-handle');
                handles.forEach(function(handle) {
                    handle.style.display = 'block';
                });
                // Update resize handles size when showing them
                self.updateResizeHandlesSize(element);
                // Show rotation handle
                const rotateHandle = element.querySelector('.rotate-handle');
                if (rotateHandle) {
                    rotateHandle.style.display = 'flex';
                }
                
                // Show and update rotation indicator
                self.updateRotationIndicator(element);
                
            self.selectedBooths = [element];
            
            // Update toolbar with booth values (but don't auto-enable edit mode)
            // User can click on individual fields to edit them
            requestAnimationFrame(function() {
                setTimeout(function() {
                    // Clear any existing edit state first
                    const editableFields = document.querySelectorAll('.info-value.info-editable');
                    editableFields.forEach(function(field) {
                        const existingInput = field.querySelector('input');
                        if (existingInput) {
                            existingInput.remove();
                        }
                        field.classList.remove('info-editing');
                    });
                    
                    // Update toolbar with current values (this will set textContent)
                    self.updateInfoToolbar(element);
                }, 50);
            });
            
            // Don't auto-open Properties panel on single click - only update content if panel is already open
            // Properties panel will open on double-click if enabled
            const panel = document.getElementById('propertiesPanel');
            if (panel && panel.classList.contains('active')) {
            self.updatePropertiesPanel(element);
            }
            
        }, true); // CAPTURE PHASE
        
        // Double-click handler to open Properties panel
        element.addEventListener('dblclick', function(e) {
            // Only handle left double-click
            if (e.button !== 0 && e.detail !== 2) {
                return;
            }
            
            e.stopPropagation();
            e.stopImmediatePropagation();
            e.preventDefault();
            
            // Don't open if clicking on transform controls
            if (e.target.classList.contains('transform-controls') ||
                e.target.closest('.transform-controls')) {
                return;
            }
            
            // Only open if Properties panel is enabled
            if (self.propertiesPanelEnabled) {
                self.updatePropertiesPanel(element);
            }
        }, true); // CAPTURE PHASE
        
        // Mousedown for dragging - separate from click for selection
        // Use a flag to prevent click from interfering with drag
        let mouseDownTime = 0;
        let mouseDownPos = {x: 0, y: 0};
        let hasDragged = false;
        
        // Double middle-click handler for toggling transform controls
        let middleClickTime = 0;
        let middleClickTimeout = null;
        
        const handleMiddleClick = function(e) {
            if (e.button === 1) { // Middle mouse button
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                const now = Date.now();
                const timeSinceLastClick = now - middleClickTime;
                
                // Clear any pending timeout
                if (middleClickTimeout) {
                    clearTimeout(middleClickTimeout);
                    middleClickTimeout = null;
                }
                
                if (timeSinceLastClick < 300 && timeSinceLastClick > 0 && middleClickTime > 0) {
                    // Double middle-click detected - toggle transform controls
                    middleClickTime = 0;
                    
                    const controls = element.querySelector('.transform-controls');
                    if (controls) {
                        const isVisible = controls.style.display === 'flex' || (controls.style.display === '' && window.getComputedStyle(controls).display === 'flex');
                        if (isVisible) {
                            controls.style.display = 'none';
                        } else {
                            controls.style.cssText = 'display: flex !important; visibility: visible !important; opacity: 1 !important; position: absolute !important; top: 2px !important; left: 2px !important; transform: none !important; z-index: 9999 !important; background: rgba(0, 0, 0, 0.85) !important; padding: 4px 6px !important; border: 1px solid rgba(255,255,255,0.3) !important; border-radius: 4px !important; font-size: 10px !important;';
                            
                            // Update transform control values
                            const x = parseFloat(element.style.left) || 0;
                            const y = parseFloat(element.style.top) || 0;
                            const w = parseFloat(element.style.width) || 80;
                            const h = parseFloat(element.style.height) || 50;
                            const r = parseFloat(element.getAttribute('data-rotation')) || 0;
                            
                            const xInput = controls.querySelector('.transform-x');
                            const yInput = controls.querySelector('.transform-y');
                            const wInput = controls.querySelector('.transform-w');
                            const hInput = controls.querySelector('.transform-h');
                            const rInput = controls.querySelector('.transform-r');
                            
                            if (xInput) xInput.textContent = Math.round(x);
                            if (yInput) yInput.textContent = Math.round(y);
                            if (wInput) wInput.textContent = Math.round(w);
                            if (hInput) hInput.textContent = Math.round(h);
                            if (rInput) rInput.textContent = Math.round(r);
                        }
                    }
                } else {
                    // First click - wait for potential second click
                    middleClickTime = now;
                    middleClickTimeout = setTimeout(function() {
                        middleClickTime = 0;
                        middleClickTimeout = null;
                    }, 300);
                }
            }
        };
        
        // Add middle-click handler (use mousedown with capture phase)
        element.addEventListener('mousedown', handleMiddleClick, true);
        
        // Mousedown handler - MUST fire BEFORE click handler
        // Use capture phase to ensure it fires before canvas handler AND Panzoom
        // Attach to the element itself, not the document, so it fires in capture phase
        const boothMousedownHandler = function(e) {
            // Skip if middle mouse button (handled separately)
            if (e.button === 1) {
                return;
            }
            
            // Only handle left mouse button (button 0) for dragging
            if (e.button !== 0) {
                return;
            }
            
            // Don't start dragging if clicking on transform controls
            if (e.target.classList.contains('transform-controls') ||
                e.target.closest('.transform-controls')) {
                return;
            }
            
            // Only allow dragging if select tool is active
            if (self.currentTool !== 'select') {
                return;
            }
            
            // Record mousedown time and position for click detection
            mouseDownTime = Date.now();
            mouseDownPos.x = e.clientX;
            mouseDownPos.y = e.clientY;
            hasDragged = false;
            
            // Stop propagation FIRST to prevent other handlers
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Start dragging immediately
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            
            const canvas = document.getElementById('print');
            const canvasRect = canvas.getBoundingClientRect();
            const elementRect = element.getBoundingClientRect();
            
            // Get current zoom and pan transform from Panzoom
            let scale = 1;
            let panX = 0;
            let panY = 0;
            if (self.panzoomInstance) {
                if (self.panzoomInstance.getScale) {
                    scale = self.panzoomInstance.getScale();
                }
                if (self.panzoomInstance.getTransform) {
                    const transform = self.panzoomInstance.getTransform();
                    panX = transform.x || 0;
                    panY = transform.y || 0;
                }
            }
            
            // Calculate initial position relative to canvas, accounting for zoom and pan
            const canvasX = (elementRect.left - canvasRect.left - panX) / scale;
            const canvasY = (elementRect.top - canvasRect.top - panY) / scale;
            
            // Use the actual stored position from the element style
            initialX = parseFloat(element.style.left) || canvasX;
            initialY = parseFloat(element.style.top) || canvasY;
            
            element.style.cursor = 'grabbing';
            element.style.userSelect = 'none';
            element.classList.add('dragging');
        };
        
        // Attach mousedown handler in CAPTURE phase (fires BEFORE bubble phase handlers like canvas and Panzoom)
        element.addEventListener('mousedown', boothMousedownHandler, true);
        
        // ALSO attach to document in capture phase as a backup to catch it before Panzoom
        // This ensures we catch the event even if Panzoom is interfering
        const documentMousedownHandler = function(e) {
            // Only handle if the event target is this booth element or its children
            if (e.target === element || element.contains(e.target)) {
                // Only handle left-clicks
                if (e.button === 0) {
                    boothMousedownHandler(e);
                }
            }
        };
        document.addEventListener('mousedown', documentMousedownHandler, true);
        
        // Transform controls are now display-only (numbers only, no input fields)
        
        // Resize handles and rotation handle removed - using input fields in properties panel instead
        
        // Mouse move handler for smooth dragging
        const handleMouseMove = function(e) {
            // Check if this booth is being dragged
            if (!isDragging) {
                return;
            }
            
            hasDragged = true;
            e.preventDefault();
            e.stopPropagation();
            
            const canvas = document.getElementById('print');
            const canvasRect = canvas.getBoundingClientRect();
            
            // Get current zoom and pan transform from Panzoom
            let scale = 1;
            if (self.panzoomInstance && self.panzoomInstance.getScale) {
                scale = self.panzoomInstance.getScale();
            }
            
            // Calculate mouse movement delta in screen coordinates
            const screenDeltaX = e.clientX - startX;
            const screenDeltaY = e.clientY - startY;
            
            // Convert screen delta to canvas coordinates (divide by zoom scale)
            const deltaX = screenDeltaX / scale;
            const deltaY = screenDeltaY / scale;
            
            // Calculate new position
            let newX = initialX + deltaX;
            let newY = initialY + deltaY;
            
            // Constrain to canvas bounds
            const elementWidth = parseFloat(element.style.width) || 80;
            const elementHeight = parseFloat(element.style.height) || 50;
            const canvasWidth = canvas.offsetWidth;
            const canvasHeight = canvas.offsetHeight;
            
            newX = Math.max(0, Math.min(newX, canvasWidth - elementWidth));
            newY = Math.max(0, Math.min(newY, canvasHeight - elementHeight));
            
            // Always snap to grid when dragging
            newX = Math.round(newX / self.gridSize) * self.gridSize;
            newY = Math.round(newY / self.gridSize) * self.gridSize;
            
            // Apply new position smoothly
            element.style.left = newX + 'px';
            element.style.top = newY + 'px';
            element.setAttribute('data-x', newX);
            element.setAttribute('data-y', newY);
            
            // Update info toolbar in real-time during drag (throttled for performance)
            // Only update if booth is selected and toolbar is not in edit mode
            if (element.classList.contains('selected') && self.selectedBooths.includes(element)) {
                // Check if toolbar is in edit mode - if so, don't update to preserve inputs
                const anyFieldEditing = document.querySelector('.info-value.info-editable.info-editing');
                if (!anyFieldEditing) {
                    // Use requestAnimationFrame to throttle updates for smooth performance
                    if (!handleMouseMove.updateScheduled) {
                        handleMouseMove.updateScheduled = true;
                        requestAnimationFrame(function() {
                            self.updateInfoToolbar(element);
                            handleMouseMove.updateScheduled = false;
                        });
                    }
                }
            }
            
            // Update transform controls (hidden, but keep values updated)
            const controls = element.querySelector('.transform-controls');
            if (controls) {
                const xInput = controls.querySelector('.transform-x');
                const yInput = controls.querySelector('.transform-y');
                if (xInput) xInput.textContent = Math.round(newX);
                if (yInput) yInput.textContent = Math.round(newY);
            }
        };
        
        // Add mousemove listener to document for smooth dragging even outside element
        document.addEventListener('mousemove', handleMouseMove);
        console.log('✅ Mousemove handler attached for booth:', element.getAttribute('data-booth-id'));
        
        // Initialize mousemove logging flag and update scheduling
        handleMouseMove.logged = false;
        handleMouseMove.updateScheduled = false;
        
        // Mouse up handler - end dragging
        const handleMouseUp = function(e) {
            if (!isDragging) {
                return;
            }
            
            console.log('🖱️ Mouseup - ending drag for booth:', element.getAttribute('data-booth-id'));
            
            isDragging = false;
            element.style.cursor = 'move';
            element.style.userSelect = '';
            element.classList.remove('dragging');
            
            // Final snap to grid
            const currentX = parseFloat(element.style.left) || 0;
            const currentY = parseFloat(element.style.top) || 0;
            const snappedX = Math.round(currentX / self.gridSize) * self.gridSize;
            const snappedY = Math.round(currentY / self.gridSize) * self.gridSize;
            
            element.style.left = snappedX + 'px';
            element.style.top = snappedY + 'px';
            element.setAttribute('data-x', snappedX);
            element.setAttribute('data-y', snappedY);
            
            // Get final position relative to canvas
            const canvas = document.getElementById('print');
            const canvasRect = canvas.getBoundingClientRect();
            const elementRect = element.getBoundingClientRect();
            
            const finalX = elementRect.left - canvasRect.left;
            const finalY = elementRect.top - canvasRect.top;
            const width = parseFloat(element.style.width) || 80;
            const height = parseFloat(element.style.height) || 50;
            const rotation = parseFloat(element.getAttribute('data-rotation')) || 0;
            
            // Update transform controls with final values (if visible)
            const controls = element.querySelector('.transform-controls');
            if (controls && (controls.style.display === 'flex' || controls.style.display === '')) {
                const xInput = controls.querySelector('.transform-x');
                const yInput = controls.querySelector('.transform-y');
                const wInput = controls.querySelector('.transform-w');
                const hInput = controls.querySelector('.transform-h');
                const rInput = controls.querySelector('.transform-r');
                if (xInput) xInput.textContent = Math.round(snappedX);
                if (yInput) yInput.textContent = Math.round(snappedY);
                if (wInput) wInput.textContent = Math.round(width);
                if (hInput) hInput.textContent = Math.round(height);
                if (rInput) rInput.textContent = Math.round(rotation);
            }
            
            // Save to database
            const boothId = element.getAttribute('data-booth-id');
            self.saveBoothPosition(boothId, snappedX, snappedY, width, height, rotation);
            self.saveState();
        };
        
        // Add mouseup listener to document
        document.addEventListener('mouseup', handleMouseUp);
        
        // Setup resize handles
        self.setupResizeHandles(element);
        
        // Setup rotation handle
        self.setupRotateHandle(element);
        
        // Add wheel event to scale booth ID font size on hover (left-click as normal, no right-click needed)
        element.addEventListener('wheel', function(e) {
            // Only scale if the booth is selected or if Ctrl key is not pressed (normal wheel behavior)
            // Allow scaling when hovering over any booth, not just selected ones
            if (e.ctrlKey || e.metaKey) {
                // If Ctrl is pressed, let the canvas handle zoom
                return;
            }
            
            // Prevent default scrolling behavior
            e.preventDefault();
            e.stopPropagation();
            
            // Get current font size
            let currentFontSize = parseFloat(element.style.fontSize) || self.defaultBoothFontSize;
            const minFontSize = 8;
            const maxFontSize = 48;
            
            // Calculate new font size based on wheel delta
            // Scroll up (negative deltaY) = increase font size
            // Scroll down (positive deltaY) = decrease font size
            const delta = e.deltaY > 0 ? -1 : 1;
            let newFontSize = currentFontSize + delta;
            
            // Apply min/max constraints
            newFontSize = Math.max(minFontSize, Math.min(maxFontSize, newFontSize));
            
            // Only update if font size actually changed
            if (newFontSize !== currentFontSize) {
                // Update font size
                element.style.fontSize = newFontSize + 'px';
                element.setAttribute('data-font-size', newFontSize);
                
                // Update info toolbar if this booth is selected
                if (element.classList.contains('selected')) {
                    const infoFontSize = document.getElementById('infoFontSize');
                    if (infoFontSize && !infoFontSize.classList.contains('info-editing') && !infoFontSize.querySelector('input')) {
                        infoFontSize.textContent = Math.round(newFontSize);
                    }
                }
                
                // Save to database
                const boothId = element.getAttribute('data-booth-id');
                const x = parseFloat(element.style.left) || 0;
                const y = parseFloat(element.style.top) || 0;
                const width = parseFloat(element.style.width) || 80;
                const height = parseFloat(element.style.height) || 50;
                const rotation = parseFloat(element.getAttribute('data-rotation')) || 0;
                const zIndex = parseFloat(element.style.zIndex) || 10;
                const borderWidth = parseFloat(element.style.borderWidth) || 2;
                const borderRadius = parseFloat(element.style.borderRadius) || 6;
                const opacity = parseFloat(element.style.opacity) || 1;
                
                self.saveBoothPosition(boothId, x, y, width, height, rotation, zIndex, newFontSize, borderWidth, borderRadius, opacity);
            }
        }, { passive: false });
    },
    
    // Update properties panel
    // Update Information Toolbar with selected booth data
    updateInfoToolbar: function(element) {
        const infoToolbar = document.getElementById('infoToolbar');
        if (!infoToolbar) return;
        
        // Check if ANY field is in edit mode - if so, don't update to preserve inputs
        const anyFieldEditing = document.querySelector('.info-value.info-editable.info-editing');
        if (anyFieldEditing) {
            return;
        }
        
        if (!element) {
            // Show default values when no booth selected (toolbar stays visible)
            const infoX = document.getElementById('infoX');
            const infoY = document.getElementById('infoY');
            const infoW = document.getElementById('infoW');
            const infoH = document.getElementById('infoH');
            const infoR = document.getElementById('infoR');
            const infoZ = document.getElementById('infoZ');
            const infoFontSize = document.getElementById('infoFontSize');
            const infoBorderWidth = document.getElementById('infoBorderWidth');
            const infoBorderRadius = document.getElementById('infoBorderRadius');
            const infoOpacity = document.getElementById('infoOpacity');
            const infoStatus = document.getElementById('infoStatus');
            const infoCompany = document.getElementById('infoCompany');
            if (infoX && !infoX.classList.contains('info-editing') && !infoX.querySelector('input')) {
                infoX.textContent = '0';
            }
            if (infoY && !infoY.classList.contains('info-editing') && !infoY.querySelector('input')) {
                infoY.textContent = '0';
            }
            if (infoW && !infoW.classList.contains('info-editing') && !infoW.querySelector('input')) {
                infoW.textContent = '0';
            }
            if (infoH && !infoH.classList.contains('info-editing') && !infoH.querySelector('input')) {
                infoH.textContent = '0';
            }
            if (infoR && !infoR.classList.contains('info-editing') && !infoR.querySelector('input')) {
                infoR.textContent = '0°';
            }
            if (infoZ && !infoZ.classList.contains('info-editing') && !infoZ.querySelector('input')) {
                infoZ.textContent = '10';
            }
            if (infoFontSize && !infoFontSize.classList.contains('info-editing') && !infoFontSize.querySelector('input')) {
                infoFontSize.textContent = '14';
            }
            if (infoBorderWidth && !infoBorderWidth.classList.contains('info-editing') && !infoBorderWidth.querySelector('input')) {
                infoBorderWidth.textContent = '2';
            }
            if (infoBorderRadius && !infoBorderRadius.classList.contains('info-editing') && !infoBorderRadius.querySelector('input')) {
                infoBorderRadius.textContent = '6';
            }
            if (infoOpacity && !infoOpacity.classList.contains('info-editing') && !infoOpacity.querySelector('input')) {
                infoOpacity.textContent = '1.00';
            }
            if (infoStatus) {
                infoStatus.textContent = '-';
            }
            if (infoCompany) {
                infoCompany.textContent = '-';
            }
            
            // Ensure toolbar is visible
            const infoToolbar = document.getElementById('infoToolbar');
            if (infoToolbar) {
                infoToolbar.style.display = 'flex';
                infoToolbar.style.visibility = 'visible';
                infoToolbar.style.opacity = '1';
            }
            return;
        }
        
        // Get booth data from element attributes
        const boothId = element.getAttribute('data-booth-id');
        const boothNumber = element.getAttribute('data-booth-number') || element.textContent.trim() || '-';
        const x = Math.round(parseFloat(element.style.left) || 0);
        const y = Math.round(parseFloat(element.style.top) || 0);
        const w = Math.round(parseFloat(element.style.width) || 80);
        const h = Math.round(parseFloat(element.style.height) || 50);
        const r = Math.round(parseFloat(element.getAttribute('data-rotation')) || 0);
        const z = Math.round(parseFloat(element.style.zIndex) || 10);
        const fontSize = Math.round(parseFloat(element.style.fontSize) || 14);
        const borderWidth = Math.round(parseFloat(element.style.borderWidth) || 2);
        const borderRadius = Math.round(parseFloat(element.style.borderRadius) || 6);
        const opacity = parseFloat(element.style.opacity) || 1;
        const status = element.getAttribute('data-booth-status') || '1';
        
        // Get company name from booth data
        let company = '-';
        
        // Try to get from global boothsData array (from controller)
        if (typeof window.boothsData !== 'undefined' && Array.isArray(window.boothsData)) {
            const boothData = window.boothsData.find(b => b.id == boothId);
            if (boothData && boothData.company) {
                company = boothData.company;
            }
        }
        
        // Also try to get from companyMap if available (fallback)
        if (company === '-' && typeof companyMap !== 'undefined') {
            // companyMap is organized by company name, need to find booth in it
            for (const [compName, boothIds] of Object.entries(companyMap)) {
                if (Array.isArray(boothIds) && boothIds.includes(parseInt(boothId))) {
                    company = compName;
                    break;
                }
            }
        }
        
        // Status labels
        const statusLabels = {
            '1': 'Available',
            '2': 'Confirmed',
            '3': 'Reserved',
            '4': 'Hidden',
            '5': 'Paid'
        };
        
        // Update toolbar (only if not in edit mode)
        const infoX = document.getElementById('infoX');
        const infoY = document.getElementById('infoY');
        const infoW = document.getElementById('infoW');
        const infoH = document.getElementById('infoH');
        const infoR = document.getElementById('infoR');
        
        // Check if any field is in edit mode - if so, don't update (preserve edit mode)
        const isEditing = infoX && infoX.classList.contains('info-editing') ||
                         infoY && infoY.classList.contains('info-editing') ||
                         infoW && infoW.classList.contains('info-editing') ||
                         infoH && infoH.classList.contains('info-editing') ||
                         infoR && infoR.classList.contains('info-editing');
        
        if (!isEditing) {
            if (infoX && !infoX.classList.contains('info-editing')) {
                // Only update if not an input field
                if (!infoX.querySelector('input')) {
                    infoX.textContent = x;
                }
            }
            if (infoY && !infoY.classList.contains('info-editing')) {
                if (!infoY.querySelector('input')) {
                    infoY.textContent = y;
                }
            }
            if (infoW && !infoW.classList.contains('info-editing')) {
                if (!infoW.querySelector('input')) {
                    infoW.textContent = w;
                }
            }
            if (infoH && !infoH.classList.contains('info-editing')) {
                if (!infoH.querySelector('input')) {
                    infoH.textContent = h;
                }
            }
            if (infoR && !infoR.classList.contains('info-editing')) {
                if (!infoR.querySelector('input')) {
                    infoR.textContent = r + '°';
                }
            }
            const infoZ = document.getElementById('infoZ');
            if (infoZ && !infoZ.classList.contains('info-editing') && !infoZ.querySelector('input')) {
                infoZ.textContent = z;
            }
            const infoFontSize = document.getElementById('infoFontSize');
            if (infoFontSize && !infoFontSize.classList.contains('info-editing') && !infoFontSize.querySelector('input')) {
                infoFontSize.textContent = fontSize;
            }
            const infoBorderWidth = document.getElementById('infoBorderWidth');
            if (infoBorderWidth && !infoBorderWidth.classList.contains('info-editing') && !infoBorderWidth.querySelector('input')) {
                infoBorderWidth.textContent = borderWidth;
            }
            const infoBorderRadius = document.getElementById('infoBorderRadius');
            if (infoBorderRadius && !infoBorderRadius.classList.contains('info-editing') && !infoBorderRadius.querySelector('input')) {
                infoBorderRadius.textContent = borderRadius;
            }
            const infoOpacity = document.getElementById('infoOpacity');
            if (infoOpacity && !infoOpacity.classList.contains('info-editing') && !infoOpacity.querySelector('input')) {
                infoOpacity.textContent = opacity.toFixed(2);
            }
            document.getElementById('infoStatus').textContent = statusLabels[status] || 'Unknown';
            document.getElementById('infoCompany').textContent = company || '-';
        }
    },
    
    // Enable edit mode in info toolbar - convert text values to input fields
    enableInfoToolbarEditMode: function() {
        const self = this;
        const editableFields = document.querySelectorAll('.info-value.info-editable');
        
        if (editableFields.length === 0) {
            return;
        }
        
        // Get booth element if one is selected
        const boothElement = self.selectedBooths.length > 0 ? self.selectedBooths[0] : null;
        
        let fieldsConverted = 0;
        
        editableFields.forEach(function(field) {
            // Skip if already in edit mode (but allow re-enabling if called explicitly)
            if (field.classList.contains('info-editing')) {
                // If already has an input, make sure it's focused
                const existingInput = field.querySelector('input');
                if (existingInput) {
                    existingInput.focus();
                }
                fieldsConverted++;
                return;
            }
            
            // Check if field already has an input (might be from previous attempt)
            const existingInput = field.querySelector('input');
            if (existingInput) {
                // Already has input, just mark as editing
                field.classList.add('info-editing');
                fieldsConverted++;
                return;
            }
            
            const property = field.getAttribute('data-property');
            if (!property) {
                console.log('Field missing data-property attribute:', field.id);
                return;
            }
            
            // Get current value - prioritize getting from booth element, then from field text
            let currentValue = '';
            
            // First, try to get from booth element if available
            if (boothElement) {
                switch(property) {
                    case 'x':
                        currentValue = (parseFloat(boothElement.style.left) || 0).toString();
                        break;
                    case 'y':
                        currentValue = (parseFloat(boothElement.style.top) || 0).toString();
                        break;
                    case 'w':
                        currentValue = (parseFloat(boothElement.style.width) || 80).toString();
                        break;
                    case 'h':
                        currentValue = (parseFloat(boothElement.style.height) || 50).toString();
                        break;
                    case 'r':
                        currentValue = (parseFloat(boothElement.getAttribute('data-rotation')) || 0).toString();
                        break;
                    case 'z':
                        currentValue = (parseFloat(boothElement.style.zIndex) || 10).toString();
                        break;
                    case 'fontsize':
                        currentValue = (parseFloat(boothElement.style.fontSize) || 14).toString();
                        break;
                    case 'borderwidth':
                        currentValue = (parseFloat(boothElement.style.borderWidth) || 2).toString();
                        break;
                    case 'borderradius':
                        currentValue = (parseFloat(boothElement.style.borderRadius) || 6).toString();
                        break;
                    case 'opacity':
                        currentValue = (parseFloat(boothElement.style.opacity) || 1).toFixed(2);
                        break;
                }
            }
            
            // If still empty, get from field text content
            if (!currentValue || currentValue === '-') {
                const existingInput = field.querySelector('input');
                if (existingInput) {
                    currentValue = existingInput.value;
                } else {
                    currentValue = field.textContent.trim();
                }
            }
            
            // Remove units for editing (e.g., "0°" -> "0", "1.00" -> "1.00")
            let numericValue = currentValue.replace(/[°%]/g, '').trim();
            
            // If still empty, use default based on property
            if (!numericValue || numericValue === '-') {
                switch(property) {
                    case 'x':
                    case 'y':
                    case 'r':
                        numericValue = '0';
                        break;
                    case 'w':
                        numericValue = '80';
                        break;
                    case 'h':
                        numericValue = '50';
                        break;
                    case 'z':
                        numericValue = '10';
                        break;
                    case 'fontsize':
                        numericValue = '14';
                        break;
                    case 'borderwidth':
                        numericValue = '2';
                        break;
                    case 'borderradius':
                        numericValue = '6';
                        break;
                    case 'opacity':
                        numericValue = '1.00';
                        break;
                    default:
                        numericValue = '0';
                }
            }
            
            // Create input field - maintain original field size
            const input = document.createElement('input');
            input.type = 'number';
            input.className = 'info-edit-input';
            input.value = numericValue;
            
            // Get the original field dimensions to maintain size
            const fieldWidth = field.offsetWidth || 60;
            const fieldHeight = field.offsetHeight || 30;
            
            // Set inline styles to ensure visibility and maintain size
            input.style.cssText = 'width: ' + fieldWidth + 'px !important; min-width: 40px !important; max-width: 60px !important; padding: 4px 10px !important; border: 1px solid #667eea !important; border-radius: 4px !important; font-size: 14px !important; background: rgba(255, 255, 255, 0.2) !important; color: #fff !important; font-weight: 700 !important; font-family: "Courier New", monospace !important; text-align: center !important; display: inline-block !important; visibility: visible !important; opacity: 1 !important; margin: 0 !important; outline: none !important; box-sizing: border-box !important;';
            
            // Set step and constraints based on property
            switch(property) {
                case 'x':
                case 'y':
                    input.step = self.gridSize || 10;
                    break;
                case 'w':
                    input.min = 5;
                    input.step = 1;
                    break;
                case 'h':
                    input.min = 5;
                    input.step = 1;
                    break;
                case 'r':
                    input.step = 1;
                    break;
                case 'z':
                    input.min = 1;
                    input.max = 1000;
                    input.step = 1;
                    break;
                case 'fontsize':
                    input.min = 8;
                    input.max = 48;
                    input.step = 1;
                    break;
                case 'borderwidth':
                    input.min = 0;
                    input.max = 10;
                    input.step = 1;
                    break;
                case 'borderradius':
                    input.min = 0;
                    input.max = 50;
                    input.step = 1;
                    break;
                case 'opacity':
                    input.min = 0;
                    input.max = 1;
                    input.step = 0.1;
                    break;
            }
            
            // Store original content before converting to input
            const originalDisplayValue = field.textContent.trim();
            field.setAttribute('data-original-value', originalDisplayValue);
            
            // Clear field and add input - ensure field is visible
            field.innerHTML = ''; // Use innerHTML to completely clear
            field.appendChild(input);
            field.classList.add('info-editing');
            
            // Ensure field itself is visible
            field.style.display = 'inline-block';
            field.style.visibility = 'visible';
            field.style.opacity = '1';
            
            // Don't auto-focus all inputs - only focus the first one (X) to avoid confusion
            // User can click on any field to edit it
            if (property === 'x') {
                setTimeout(function() {
                    if (input && input.parentNode === field) {
                        input.focus();
                        input.select();
                    }
                }, 50);
            }
            
            // Handle input change on blur
            input.addEventListener('blur', function() {
                self.applyInfoToolbarValue(field, property, input.value);
            });
            
            // Handle Enter key - save and move to next field
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    input.blur();
                    // Move focus to next editable field
                    const allFields = Array.from(document.querySelectorAll('.info-value.info-editable'));
                    const currentIndex = allFields.indexOf(field);
                    if (currentIndex < allFields.length - 1) {
                        const nextField = allFields[currentIndex + 1];
                        if (nextField && !nextField.classList.contains('info-editing')) {
                            // Enable edit mode for next field
                            setTimeout(function() {
                                self.enableInfoToolbarEditMode();
                                const nextInput = nextField.querySelector('input');
                                if (nextInput) {
                                    nextInput.focus();
                                    nextInput.select();
                                }
                            }, 50);
                        }
                    }
                } else if (e.key === 'Escape') {
                    e.preventDefault();
                    // Restore original value
                    const originalValue = field.getAttribute('data-original-value');
                    field.textContent = originalValue || numericValue;
                    field.classList.remove('info-editing');
                    input.remove();
                }
            });
            
            // Prevent updateInfoToolbar from interfering while editing
            input.addEventListener('focus', function() {
                field.classList.add('info-editing');
            });
            
            fieldsConverted++;
        });
        
        console.log('Edit mode enabled for', fieldsConverted, 'out of', editableFields.length, 'fields');
        
        // If not all fields were converted, try again after a short delay
        if (fieldsConverted < editableFields.length) {
            console.log('Some fields were not converted, retrying...');
            setTimeout(function() {
                self.enableInfoToolbarEditMode();
            }, 100);
        }
    },
    
    // Enable edit mode for a single field
    enableInfoToolbarEditModeForField: function(field, property) {
        const self = this;
        
        // Skip if already in edit mode
        if (field.classList.contains('info-editing')) {
            const existingInput = field.querySelector('input');
            if (existingInput) {
                existingInput.focus();
                existingInput.select();
            }
            return;
        }
        
        // Get booth element if one is selected
        const boothElement = self.selectedBooths.length > 0 ? self.selectedBooths[0] : null;
        
        // Get current value
        let currentValue = '';
        if (boothElement) {
            switch(property) {
                case 'x':
                    currentValue = (parseFloat(boothElement.style.left) || 0).toString();
                    break;
                case 'y':
                    currentValue = (parseFloat(boothElement.style.top) || 0).toString();
                    break;
                case 'w':
                    currentValue = (parseFloat(boothElement.style.width) || 80).toString();
                    break;
                case 'h':
                    currentValue = (parseFloat(boothElement.style.height) || 50).toString();
                    break;
                case 'r':
                    currentValue = (parseFloat(boothElement.getAttribute('data-rotation')) || 0).toString();
                    break;
                case 'z':
                    currentValue = (parseFloat(boothElement.style.zIndex) || 10).toString();
                    break;
                case 'fontsize':
                    currentValue = (parseFloat(boothElement.style.fontSize) || 14).toString();
                    break;
                case 'borderwidth':
                    currentValue = (parseFloat(boothElement.style.borderWidth) || 2).toString();
                    break;
                case 'borderradius':
                    currentValue = (parseFloat(boothElement.style.borderRadius) || 6).toString();
                    break;
                case 'opacity':
                    currentValue = (parseFloat(boothElement.style.opacity) || 1).toFixed(2);
                    break;
            }
        }
        
        // If still empty, get from field text
        if (!currentValue || currentValue === '-') {
            currentValue = field.textContent.trim();
        }
        
        // Remove units
        let numericValue = currentValue.replace(/[°%]/g, '').trim();
        
        // Use defaults if empty
        if (!numericValue || numericValue === '-') {
            switch(property) {
                case 'x':
                case 'y':
                case 'r':
                    numericValue = '0';
                    break;
                case 'w':
                    numericValue = '80';
                    break;
                case 'h':
                    numericValue = '50';
                    break;
                case 'z':
                    numericValue = '10';
                    break;
                case 'fontsize':
                    numericValue = '14';
                    break;
                case 'borderwidth':
                    numericValue = '2';
                    break;
                case 'borderradius':
                    numericValue = '6';
                    break;
                case 'opacity':
                    numericValue = '1.00';
                    break;
                default:
                    numericValue = '0';
            }
        }
        
        // Create input - maintain original field size
        const input = document.createElement('input');
        input.type = 'number';
        input.className = 'info-edit-input';
        input.value = numericValue;
        
        // Get the original field dimensions to maintain size
        const fieldWidth = field.offsetWidth || 60;
        const fieldHeight = field.offsetHeight || 30;
        
        // Set inline styles to maintain original size
        input.style.cssText = 'width: ' + fieldWidth + 'px !important; min-width: 40px !important; max-width: 60px !important; padding: 4px 10px !important; border: 1px solid #667eea !important; border-radius: 4px !important; font-size: 14px !important; background: rgba(255, 255, 255, 0.2) !important; color: #fff !important; font-weight: 700 !important; font-family: "Courier New", monospace !important; text-align: center !important; display: inline-block !important; visibility: visible !important; opacity: 1 !important; margin: 0 !important; outline: none !important; box-sizing: border-box !important;';
        
        // Set constraints
        switch(property) {
            case 'x':
            case 'y':
                input.step = self.gridSize || 10;
                break;
            case 'w':
                input.min = 5;
                input.step = 1;
                break;
            case 'h':
                input.min = 5;
                input.step = 1;
                break;
            case 'r':
                input.step = 1;
                break;
            case 'z':
                input.min = 1;
                input.max = 1000;
                input.step = 1;
                break;
            case 'fontsize':
                input.min = 8;
                input.max = 48;
                input.step = 1;
                break;
            case 'borderwidth':
                input.min = 0;
                input.max = 10;
                input.step = 1;
                break;
            case 'borderradius':
                input.min = 0;
                input.max = 50;
                input.step = 1;
                break;
            case 'opacity':
                input.min = 0;
                input.max = 1;
                input.step = 0.1;
                break;
        }
        
        // Store original value
        const originalValue = field.textContent.trim();
        field.setAttribute('data-original-value', originalValue);
        
        // Replace text with input
        field.innerHTML = '';
        field.appendChild(input);
        field.classList.add('info-editing');
        field.style.display = 'inline-block';
        field.style.visibility = 'visible';
        field.style.opacity = '1';
        
        // Focus and select
        setTimeout(function() {
            input.focus();
            input.select();
        }, 10);
        
        // Handle blur
        input.addEventListener('blur', function() {
            self.applyInfoToolbarValue(field, property, input.value);
        });
        
        // Handle Enter and Escape
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                input.blur();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                const original = field.getAttribute('data-original-value');
                field.textContent = original || numericValue;
                field.classList.remove('info-editing');
                input.remove();
            }
        });
        
        console.log('Edit mode enabled for single field:', property);
    },
    
    // Apply value from info toolbar input to booth element
    applyInfoToolbarValue: function(field, property, value) {
        const self = this;
        
        // Exit edit mode
        field.classList.remove('info-editing');
        
        // Get selected booth
        if (self.selectedBooths.length === 0) {
            // No booth selected, just update display
            const displayValue = this.formatInfoToolbarValue(property, value);
            field.textContent = displayValue;
            return;
        }
        
        const element = self.selectedBooths[0];
        const numericValue = parseFloat(value) || 0;
        
        // Apply value to booth element
        switch(property) {
            case 'x':
                element.style.left = numericValue + 'px';
                element.setAttribute('data-x', numericValue);
                // Snap to grid
                const snappedX = Math.round(numericValue / self.gridSize) * self.gridSize;
                element.style.left = snappedX + 'px';
                field.textContent = snappedX;
                break;
            case 'y':
                element.style.top = numericValue + 'px';
                element.setAttribute('data-y', numericValue);
                // Snap to grid
                const snappedY = Math.round(numericValue / self.gridSize) * self.gridSize;
                element.style.top = snappedY + 'px';
                field.textContent = snappedY;
                break;
            case 'w':
                const w = Math.max(5, numericValue);
                element.style.width = w + 'px';
                element.setAttribute('data-width', w);
                field.textContent = w;
                break;
            case 'h':
                const h = Math.max(5, numericValue);
                element.style.height = h + 'px';
                element.setAttribute('data-height', h);
                field.textContent = h;
                break;
            case 'r':
                element.style.transform = 'rotate(' + numericValue + 'deg)';
                element.setAttribute('data-rotation', numericValue);
                field.textContent = numericValue + '°';
                break;
            case 'z':
                const z = Math.max(1, Math.min(1000, numericValue));
                element.style.zIndex = z;
                field.textContent = z;
                break;
            case 'fontsize':
                const fontSize = Math.max(8, Math.min(48, numericValue));
                element.style.fontSize = fontSize + 'px';
                field.textContent = fontSize;
                break;
            case 'borderwidth':
                const borderWidth = Math.max(0, Math.min(10, numericValue));
                element.style.borderWidth = borderWidth + 'px';
                field.textContent = borderWidth;
                break;
            case 'borderradius':
                const borderRadius = Math.max(0, Math.min(50, numericValue));
                element.style.borderRadius = borderRadius + 'px';
                field.textContent = borderRadius;
                break;
            case 'opacity':
                const opacity = Math.max(0, Math.min(1, numericValue));
                element.style.opacity = opacity;
                field.textContent = opacity.toFixed(2);
                break;
        }
        
        // Save to database with all properties
        const boothId = element.getAttribute('data-booth-id');
        const x = parseFloat(element.style.left) || 0;
        const y = parseFloat(element.style.top) || 0;
        const w = parseFloat(element.style.width) || 80;
        const h = parseFloat(element.style.height) || 50;
        const r = parseFloat(element.getAttribute('data-rotation')) || 0;
        const z = parseFloat(element.style.zIndex) || 10;
        const fs = parseFloat(element.style.fontSize) || 14;
        const bw = parseFloat(element.style.borderWidth) || 2;
        const br = parseFloat(element.style.borderRadius) || 6;
        const op = parseFloat(element.style.opacity) || 1.00;
        self.saveBoothPosition(boothId, x, y, w, h, r, z, fs, bw, br, op);
        
        // Update toolbar to reflect all values
        self.updateInfoToolbar(element);
    },
    
    // Format value for display in info toolbar
    formatInfoToolbarValue: function(property, value) {
        switch(property) {
            case 'r':
                return value + '°';
            case 'opacity':
                return parseFloat(value).toFixed(2);
            default:
                return value;
        }
    },
    
    updatePropertiesPanel: function(element) {
        const panel = document.getElementById('propertiesPanel');
        const content = document.getElementById('propertiesContent');
        
        if (!panel || !content) {
            return;
        }
        
        const boothId = element.getAttribute('data-booth-id');
        const boothNumber = element.textContent.trim();
        const x = parseFloat(element.style.left) || 0;
        const y = parseFloat(element.style.top) || 0;
        const width = parseFloat(element.style.width) || 80;
        const height = parseFloat(element.style.height) || 50;
        const rotation = parseFloat(element.getAttribute('data-rotation')) || 0;
        
        // Get booth data attributes for additional properties
        const status = element.getAttribute('data-status') || element.className.match(/status-(\d+)/) ? element.className.match(/status-(\d+)/)[1] : '1';
        const fontSize = parseFloat(element.style.fontSize) || 14;
        const borderWidth = parseFloat(element.style.borderWidth) || 2;
        const borderRadius = parseFloat(element.style.borderRadius) || 6;
        const opacity = parseFloat(element.style.opacity) || 1;
        const zIndex = parseFloat(element.style.zIndex) || 10;
        
        content.innerHTML = '<h6 class="mb-3"><i class="fas fa-cube"></i> Booth: ' + boothNumber + '</h6>' +
                           '<div class="mb-3"><strong>Position</strong></div>' +
                           '<div class="mb-2"><label class="form-label small"><i class="fas fa-arrows-alt-h"></i> Position X (px):</label><input type="number" class="form-control form-control-sm prop-x" value="' + Math.round(x) + '" step="' + self.gridSize + '"></div>' +
                           '<div class="mb-2"><label class="form-label small"><i class="fas fa-arrows-alt-v"></i> Position Y (px):</label><input type="number" class="form-control form-control-sm prop-y" value="' + Math.round(y) + '" step="' + self.gridSize + '"></div>' +
                           '<div class="mb-3 mt-3"><strong>Size</strong></div>' +
                           '<div class="mb-2"><label class="form-label small"><i class="fas fa-arrows-alt-h"></i> Width (px):</label><input type="number" class="form-control form-control-sm prop-w" value="' + Math.round(width) + '" min="5" step="1"></div>' +
                           '<div class="mb-2"><label class="form-label small"><i class="fas fa-arrows-alt-v"></i> Height (px):</label><input type="number" class="form-control form-control-sm prop-h" value="' + Math.round(height) + '" min="5" step="1"></div>' +
                           '<div class="mb-3 mt-3"><strong>Transform</strong></div>' +
                           '<div class="mb-2"><label class="form-label small"><i class="fas fa-redo"></i> Rotation (deg):</label><input type="number" class="form-control form-control-sm prop-r" value="' + Math.round(rotation) + '" step="1"></div>' +
                           '<div class="mb-2"><label class="form-label small"><i class="fas fa-layer-group"></i> Z-Index:</label><input type="number" class="form-control form-control-sm prop-z" value="' + Math.round(zIndex) + '" min="1" max="1000" step="1"></div>' +
                           '<div class="mb-3 mt-3"><strong>Appearance</strong></div>' +
                           '<div class="mb-2"><label class="form-label small"><i class="fas fa-font"></i> Font Size (px):</label><input type="number" class="form-control form-control-sm prop-fontsize" value="' + Math.round(fontSize) + '" min="8" max="48" step="1"></div>' +
                           '<div class="mb-2"><label class="form-label small"><i class="fas fa-border-style"></i> Border Width (px):</label><input type="number" class="form-control form-control-sm prop-borderwidth" value="' + Math.round(borderWidth) + '" min="0" max="10" step="1"></div>' +
                           '<div class="mb-2"><label class="form-label small"><i class="fas fa-circle"></i> Border Radius (px):</label><input type="number" class="form-control form-control-sm prop-borderradius" value="' + Math.round(borderRadius) + '" min="0" max="50" step="1"></div>' +
                           '<div class="mb-2"><label class="form-label small"><i class="fas fa-adjust"></i> Opacity:</label><input type="number" class="form-control form-control-sm prop-opacity" value="' + opacity.toFixed(2) + '" min="0" max="1" step="0.1"></div>';
        
        // Helper function to apply grid snapping to position values
        const snapToGrid = function(value) {
            return Math.round(value / self.gridSize) * self.gridSize;
        };
        
        // Helper function to save booth position
        const saveBoothProps = function() {
            const x = parseFloat(element.style.left) || 0;
            const y = parseFloat(element.style.top) || 0;
            const w = parseFloat(element.style.width) || 80;
            const h = parseFloat(element.style.height) || 50;
            const r = parseFloat(element.getAttribute('data-rotation')) || 0;
            self.saveBoothPosition(boothId, x, y, w, h, r);
        };
        
        // Helper function to add mouse wheel support to input
        const addWheelSupport = function(input, step, min, max, updateFn) {
            input.addEventListener('wheel', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                let currentValue = parseFloat(this.value) || 0;
                const delta = e.deltaY > 0 ? -step : step; // Invert: scroll down = decrease, scroll up = increase
                let newValue = currentValue + delta;
                
                // Apply min/max constraints if provided
                if (typeof min !== 'undefined') {
                    newValue = Math.max(min, newValue);
                }
                if (typeof max !== 'undefined') {
                    newValue = Math.min(max, newValue);
                }
                
                this.value = newValue;
                this.dispatchEvent(new Event('change', { bubbles: true }));
            }, { passive: false });
        };
        
        // Add event listeners to property inputs
        content.querySelector('.prop-x').addEventListener('change', function() {
            let x = parseFloat(this.value) || 0;
            x = snapToGrid(x); // Snap to grid
            this.value = x; // Update input with snapped value
            element.style.left = x + 'px';
            element.setAttribute('data-x', x);
            self.updateInfoToolbar(element);
            saveBoothProps();
        });
        // Add mouse wheel support for X
        const propX = content.querySelector('.prop-x');
        addWheelSupport(propX, self.gridSize, undefined, undefined, function(val) {
            let x = snapToGrid(val);
            element.style.left = x + 'px';
            element.setAttribute('data-x', x);
            self.updateInfoToolbar(element);
            saveBoothProps();
        });
        
        content.querySelector('.prop-y').addEventListener('change', function() {
            let y = parseFloat(this.value) || 0;
            y = snapToGrid(y); // Snap to grid
            this.value = y; // Update input with snapped value
            element.style.top = y + 'px';
            element.setAttribute('data-y', y);
            self.updateInfoToolbar(element);
            saveBoothProps();
        });
        // Add mouse wheel support for Y
        const propY = content.querySelector('.prop-y');
        addWheelSupport(propY, self.gridSize, undefined, undefined, function(val) {
            let y = snapToGrid(val);
            element.style.top = y + 'px';
            element.setAttribute('data-y', y);
            self.updateInfoToolbar(element);
            saveBoothProps();
        });
        
        content.querySelector('.prop-w').addEventListener('change', function() {
            const w = Math.max(5, parseFloat(this.value) || 80);
            element.style.width = w + 'px';
            element.setAttribute('data-width', w);
            
            // Recalculate font size based on new dimensions
            const h = parseFloat(element.style.height) || 50;
            const userFontSize = parseFloat(element.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const minDimension = Math.min(w, h);
            const calculatedFontSize = Math.min(userFontSize, Math.max(8, minDimension * 0.35));
            element.style.fontSize = calculatedFontSize + 'px';
            element.setAttribute('data-calculated-font-size', calculatedFontSize);
            
            // Update resize handles size
            self.updateResizeHandlesSize(element);
            
            // Update rotation indicator size
            self.updateRotationIndicator(element);
            
            self.updateInfoToolbar(element);
            saveBoothProps();
        });
        // Add mouse wheel support for W
        const propW = content.querySelector('.prop-w');
        addWheelSupport(propW, 10, 50, undefined, null);
        
        content.querySelector('.prop-h').addEventListener('change', function() {
            const h = Math.max(5, parseFloat(this.value) || 50);
            element.style.height = h + 'px';
            element.setAttribute('data-height', h);
            
            // Recalculate font size based on new dimensions
            const w = parseFloat(element.style.width) || 80;
            const userFontSize = parseFloat(element.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const minDimension = Math.min(w, h);
            const calculatedFontSize = Math.min(userFontSize, Math.max(8, minDimension * 0.35));
            element.style.fontSize = calculatedFontSize + 'px';
            element.setAttribute('data-calculated-font-size', calculatedFontSize);
            
            // Update resize handles size
            self.updateResizeHandlesSize(element);
            
            // Update rotation indicator size
            self.updateRotationIndicator(element);
            
            self.updateInfoToolbar(element);
            saveBoothProps();
        });
        // Add mouse wheel support for H
        const propH = content.querySelector('.prop-h');
        addWheelSupport(propH, 10, 40, undefined, null);
        
        content.querySelector('.prop-r').addEventListener('change', function() {
            const r = parseFloat(this.value) || 0;
            element.style.transform = 'rotate(' + r + 'deg)';
            element.setAttribute('data-rotation', r);
            self.updateRotationIndicator(element);
            self.updateInfoToolbar(element);
            saveBoothProps();
        });
        // Add mouse wheel support for R
        const propR = content.querySelector('.prop-r');
        addWheelSupport(propR, 1, undefined, undefined, null);
        
        // Additional property inputs
        if (content.querySelector('.prop-z')) {
            content.querySelector('.prop-z').addEventListener('change', function() {
                const z = Math.max(1, Math.min(1000, parseFloat(this.value) || 10));
                element.style.zIndex = z;
                saveBoothProps();
            });
            // Add mouse wheel support for Z
            const propZ = content.querySelector('.prop-z');
            addWheelSupport(propZ, 1, 1, 1000, null);
        }
        
        if (content.querySelector('.prop-fontsize')) {
            content.querySelector('.prop-fontsize').addEventListener('change', function() {
                const userFontSize = Math.max(8, Math.min(48, parseFloat(this.value) || 14));
                // Store user's preferred font size
                element.setAttribute('data-font-size', userFontSize);
                
                // Calculate actual font size based on booth dimensions
                const w = parseFloat(element.style.width) || 80;
                const h = parseFloat(element.style.height) || 50;
                const minDimension = Math.min(w, h);
                const calculatedFontSize = Math.min(userFontSize, Math.max(8, minDimension * 0.35));
                element.style.fontSize = calculatedFontSize + 'px';
                element.setAttribute('data-calculated-font-size', calculatedFontSize);
                
                saveBoothProps();
            });
            // Add mouse wheel support for Font Size
            const propFontSize = content.querySelector('.prop-fontsize');
            addWheelSupport(propFontSize, 1, 8, 48, null);
        }
        
        if (content.querySelector('.prop-borderwidth')) {
            content.querySelector('.prop-borderwidth').addEventListener('change', function() {
                const borderWidth = Math.max(0, Math.min(10, parseFloat(this.value) || 2));
                element.style.borderWidth = borderWidth + 'px';
                saveBoothProps();
            });
            // Add mouse wheel support for Border Width
            const propBorderWidth = content.querySelector('.prop-borderwidth');
            addWheelSupport(propBorderWidth, 1, 0, 10, null);
        }
        
        if (content.querySelector('.prop-borderradius')) {
            content.querySelector('.prop-borderradius').addEventListener('change', function() {
                const borderRadius = Math.max(0, Math.min(50, parseFloat(this.value) || 6));
                element.style.borderRadius = borderRadius + 'px';
                saveBoothProps();
            });
            // Add mouse wheel support for Border Radius
            const propBorderRadius = content.querySelector('.prop-borderradius');
            addWheelSupport(propBorderRadius, 1, 0, 50, null);
        }
        
        if (content.querySelector('.prop-opacity')) {
            content.querySelector('.prop-opacity').addEventListener('change', function() {
                const opacity = Math.max(0, Math.min(1, parseFloat(this.value) || 1));
                element.style.opacity = opacity;
                saveBoothProps();
            });
            // Add mouse wheel support for Opacity
            const propOpacity = content.querySelector('.prop-opacity');
            addWheelSupport(propOpacity, 0.1, 0, 1, null);
        }
        
        // Show properties panel as popup modal (only if enabled)
        if (self.propertiesPanelEnabled) {
        panel.classList.add('active');
        const backdrop = document.getElementById('propertiesPanelBackdrop');
        if (backdrop) {
            backdrop.classList.add('active');
            }
        }
    },
    
    // Setup resize handles for a booth element
    setupResizeHandles: function(element) {
        const self = this;
        const handles = element.querySelectorAll('.resize-handle');
        let isResizing = false;
        let startX, startY, startWidth, startHeight, startLeft, startTop;
        let resizeHandle = null;
        
        handles.forEach(function(handle) {
            handle.addEventListener('mousedown', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                
                isResizing = true;
                resizeHandle = handle.className.split(' ')[1]; // Get handle class (nw, ne, sw, se, n, s, w, e)
                startX = e.clientX;
                startY = e.clientY;
                startWidth = parseFloat(element.style.width) || 80;
                startHeight = parseFloat(element.style.height) || 50;
                startLeft = parseFloat(element.style.left) || 0;
                startTop = parseFloat(element.style.top) || 0;
                
                document.addEventListener('mousemove', handleResize);
                document.addEventListener('mouseup', stopResize);
            });
        });
        
        function handleResize(e) {
            if (!isResizing || !resizeHandle) return;
            
            // Get current zoom scale from Panzoom
            let scale = 1;
            if (self.panzoomInstance && self.panzoomInstance.getScale) {
                scale = self.panzoomInstance.getScale();
            }
            
            // Calculate mouse movement delta in screen coordinates
            const screenDeltaX = e.clientX - startX;
            const screenDeltaY = e.clientY - startY;
            
            // Convert screen delta to canvas coordinates (divide by zoom scale)
            const deltaX = screenDeltaX / scale;
            const deltaY = screenDeltaY / scale;
            
            let newWidth = startWidth;
            let newHeight = startHeight;
            let newLeft = startLeft;
            let newTop = startTop;
            
            // Calculate new dimensions based on which handle is being dragged
            if (resizeHandle.includes('e')) { // East (right)
                newWidth = Math.max(5, startWidth + deltaX);
            }
            if (resizeHandle.includes('w')) { // West (left)
                newWidth = Math.max(5, startWidth - deltaX);
                newLeft = startLeft + deltaX;
            }
            if (resizeHandle.includes('s')) { // South (bottom)
                newHeight = Math.max(5, startHeight + deltaY);
            }
            if (resizeHandle.includes('n')) { // North (top)
                newHeight = Math.max(5, startHeight - deltaY);
                newTop = startTop + deltaY;
            }
            
            // Snap to grid
            newWidth = Math.round(newWidth / self.gridSize) * self.gridSize;
            newHeight = Math.round(newHeight / self.gridSize) * self.gridSize;
            newLeft = Math.round(newLeft / self.gridSize) * self.gridSize;
            newTop = Math.round(newTop / self.gridSize) * self.gridSize;
            
            // Apply new size and position
            element.style.width = newWidth + 'px';
            element.style.height = newHeight + 'px';
            element.style.left = newLeft + 'px';
            element.style.top = newTop + 'px';
            element.setAttribute('data-width', newWidth);
            element.setAttribute('data-height', newHeight);
            element.setAttribute('data-x', newLeft);
            element.setAttribute('data-y', newTop);
            
            // Recalculate font size based on new dimensions
            const userFontSize = parseFloat(element.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const minDimension = Math.min(newWidth, newHeight);
            const calculatedFontSize = Math.min(userFontSize, Math.max(8, minDimension * 0.35));
            element.style.fontSize = calculatedFontSize + 'px';
            element.setAttribute('data-calculated-font-size', calculatedFontSize);
            
            // Update resize handles size based on new dimensions
            self.updateResizeHandlesSize(element);
            
            // Update rotation indicator size
            self.updateRotationIndicator(element);
            
            // Update info toolbar in real-time during resize (throttled for performance)
            // Only update if booth is selected and toolbar is not in edit mode
            if (element.classList.contains('selected') && self.selectedBooths.includes(element)) {
                // Check if toolbar is in edit mode - if so, don't update to preserve inputs
                const anyFieldEditing = document.querySelector('.info-value.info-editable.info-editing');
                if (!anyFieldEditing) {
                    // Use requestAnimationFrame to throttle updates for smooth performance
                    if (!handleResize.updateScheduled) {
                        handleResize.updateScheduled = true;
                        requestAnimationFrame(function() {
                            self.updateInfoToolbar(element);
                            handleResize.updateScheduled = false;
                        });
                    }
                }
            }
            
            // Update transform controls (hidden, but keep values updated)
            const controls = element.querySelector('.transform-controls');
            if (controls) {
                const wInput = controls.querySelector('.transform-w');
                const hInput = controls.querySelector('.transform-h');
                const xInput = controls.querySelector('.transform-x');
                const yInput = controls.querySelector('.transform-y');
                if (wInput) wInput.textContent = Math.round(newWidth);
                if (hInput) hInput.textContent = Math.round(newHeight);
                if (xInput) xInput.textContent = Math.round(newLeft);
                if (yInput) yInput.textContent = Math.round(newTop);
            }
        }
        
        function stopResize(e) {
            if (!isResizing) return;
            
            isResizing = false;
            resizeHandle = null;
            
            // Save to database
            const boothId = element.getAttribute('data-booth-id');
            const x = parseFloat(element.style.left) || 0;
            const y = parseFloat(element.style.top) || 0;
            const w = parseFloat(element.style.width) || 80;
            const h = parseFloat(element.style.height) || 50;
            const r = parseFloat(element.getAttribute('data-rotation')) || 0;
            self.saveBoothPosition(boothId, x, y, w, h, r);
            
            // Reset update scheduling flag
            if (handleResize.updateScheduled !== undefined) {
                handleResize.updateScheduled = false;
            }
            
            document.removeEventListener('mousemove', handleResize);
            document.removeEventListener('mouseup', stopResize);
        }
        
        // Initialize resize update scheduling flag
        handleResize.updateScheduled = false;
    },
    
    // Setup rotation handle for a booth element
    setupRotateHandle: function(element) {
        const self = this;
        const rotateHandle = element.querySelector('.rotate-handle');
        if (!rotateHandle) return;
        
        let isRotating = false;
        let startAngle = 0;
        let initialRotation = 0;
        let centerX = 0;
        let centerY = 0;
        
        rotateHandle.addEventListener('mousedown', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            isRotating = true;
            initialRotation = parseFloat(element.getAttribute('data-rotation')) || 0;
            
            const rect = element.getBoundingClientRect();
            centerX = rect.left + rect.width / 2;
            centerY = rect.top + rect.height / 2;
            
            const dx = e.clientX - centerX;
            const dy = e.clientY - centerY;
            startAngle = Math.atan2(dy, dx) * 180 / Math.PI;
            
            document.addEventListener('mousemove', handleRotate);
            document.addEventListener('mouseup', stopRotate);
        });
        
        function handleRotate(e) {
            if (!isRotating) return;
            
            const dx = e.clientX - centerX;
            const dy = e.clientY - centerY;
            const currentAngle = Math.atan2(dy, dx) * 180 / Math.PI;
            const deltaAngle = currentAngle - startAngle;
            let newRotation = initialRotation + deltaAngle;
            
            // Normalize to -360 to 360 range
            newRotation = newRotation % 360;
            if (newRotation > 360) newRotation -= 360;
            if (newRotation < -360) newRotation += 360;
            
            element.style.transform = 'rotate(' + newRotation + 'deg)';
            element.setAttribute('data-rotation', newRotation);
            
            // Update rotation indicator in real-time
            self.updateRotationIndicator(element);
            
            // Update info toolbar in real-time during rotation (throttled for performance)
            // Only update if booth is selected and toolbar is not in edit mode
            if (element.classList.contains('selected') && self.selectedBooths.includes(element)) {
                // Check if toolbar is in edit mode - if so, don't update to preserve inputs
                const anyFieldEditing = document.querySelector('.info-value.info-editable.info-editing');
                if (!anyFieldEditing) {
                    // Use requestAnimationFrame to throttle updates for smooth performance
                    if (!handleRotate.updateScheduled) {
                        handleRotate.updateScheduled = true;
                        requestAnimationFrame(function() {
                            self.updateInfoToolbar(element);
                            handleRotate.updateScheduled = false;
                        });
                    }
                }
            }
            
            // Update transform controls (hidden, but keep values updated)
            const controls = element.querySelector('.transform-controls');
            if (controls && controls.style.display === 'flex') {
                const rInput = controls.querySelector('.transform-r');
                if (rInput) rInput.textContent = Math.round(newRotation);
            }
        }
        
        function stopRotate(e) {
            if (!isRotating) return;
            
            isRotating = false;
            
            // Reset update scheduling flag
            if (handleRotate.updateScheduled !== undefined) {
                handleRotate.updateScheduled = false;
            }
            
            // Save to database
            const boothId = element.getAttribute('data-booth-id');
            const x = parseFloat(element.style.left) || 0;
            const y = parseFloat(element.style.top) || 0;
            const w = parseFloat(element.style.width) || 80;
            const h = parseFloat(element.style.height) || 50;
            const r = parseFloat(element.getAttribute('data-rotation')) || 0;
            self.saveBoothPosition(boothId, x, y, w, h, r);
            
            document.removeEventListener('mousemove', handleRotate);
            document.removeEventListener('mouseup', stopRotate);
        }
        
        // Initialize rotation update scheduling flag
        handleRotate.updateScheduled = false;
    },
    
    // Fit canvas to view - Center and fit the entire image to show it completely
    fitCanvasToView: function(animate) {
        const self = this;
        if (!self.panzoomInstance) return;
        
        const canvas = document.getElementById('print');
        const container = document.getElementById('printContainer');
        if (!canvas || !container) return;
        
        // Get canvas dimensions (image size)
        const canvasWidth = canvas.offsetWidth || self.canvasWidth || 1200;
        const canvasHeight = canvas.offsetHeight || self.canvasHeight || 800;
        
        // Get container dimensions (viewport size)
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;
        
        // Calculate scale to fit entire canvas in viewport
        // Use the smaller scale to ensure entire image is visible
        const scaleX = containerWidth / canvasWidth;
        const scaleY = containerHeight / canvasHeight;
        const fitScale = Math.min(scaleX, scaleY); // Fit to show entire image (can zoom out if needed)
        
        // Apply zoom first
        if (self.panzoomInstance.zoom) {
            self.panzoomInstance.zoom(fitScale, { animate: animate !== false });
        }
        
        // Wait for zoom to complete, then center
        setTimeout(function() {
            // Get current transform after zoom
            const transform = self.panzoomInstance.getTransform ? self.panzoomInstance.getTransform() : { x: 0, y: 0, scale: fitScale };
            const currentScale = transform.scale || fitScale;
            
            // Calculate scaled canvas dimensions
            const scaledWidth = canvasWidth * currentScale;
            const scaledHeight = canvasHeight * currentScale;
            
            // Calculate center position to align image center with viewport center
            // The pan position should move the canvas so its center aligns with viewport center
            const viewportCenterX = containerWidth / 2;
            const viewportCenterY = containerHeight / 2;
            const canvasCenterX = canvasWidth / 2;
            const canvasCenterY = canvasHeight / 2;
            
            // Calculate pan offset to center the canvas
            // Pan position = viewport center - (canvas center * scale)
            const panX = viewportCenterX - (canvasCenterX * currentScale);
            const panY = viewportCenterY - (canvasCenterY * currentScale);
            
            // Apply pan to center using setTransform for precise control
            if (self.panzoomInstance.setTransform) {
                self.panzoomInstance.setTransform({ x: panX, y: panY, scale: currentScale });
            } else if (self.panzoomInstance.pan) {
                self.panzoomInstance.pan(panX, panY, { animate: animate !== false });
            } else if (self.panzoomInstance.moveTo) {
                self.panzoomInstance.moveTo(panX, panY);
            }
            
            // Update zoom level display
            self.zoomLevel = currentScale;
            $('#zoomLevel').text(Math.round(currentScale * 100) + '%');
        }, animate !== false ? 200 : 50);
    },
    
    // Save booth position, size, and rotation
    saveBoothPosition: function(boothId, x, y, width, height, rotation, zIndex, fontSize, borderWidth, borderRadius, opacity) {
        const canvas = document.getElementById('print');
        const boothElement = canvas ? canvas.querySelector('[data-booth-id="' + boothId + '"]') : null;
        
        // Get style properties from element if not provided
        if (boothElement) {
            zIndex = zIndex !== undefined ? zIndex : (parseFloat(boothElement.style.zIndex) || 10);
            fontSize = fontSize !== undefined ? fontSize : (parseFloat(boothElement.style.fontSize) || 14);
            borderWidth = borderWidth !== undefined ? borderWidth : (parseFloat(boothElement.style.borderWidth) || 2);
            borderRadius = borderRadius !== undefined ? borderRadius : (parseFloat(boothElement.style.borderRadius) || 6);
            opacity = opacity !== undefined ? opacity : (parseFloat(boothElement.style.opacity) || 1.00);
        } else {
            zIndex = zIndex || 10;
            fontSize = fontSize || 14;
            borderWidth = borderWidth || 2;
            borderRadius = borderRadius || 6;
            opacity = opacity !== undefined ? opacity : 1.00;
        }
        
        return fetch('/booths/' + boothId + '/save-position', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                position_x: x,
                position_y: y,
                width: width || null,
                height: height || null,
                rotation: rotation || 0,
                z_index: zIndex,
                font_size: fontSize,
                border_width: borderWidth,
                border_radius: borderRadius,
                opacity: opacity
            })
        }).then(function(response) {
            if (!response.ok) {
                return response.json().then(function(data) {
                    throw new Error(data.message || 'Failed to save booth ' + boothId);
                });
            }
            return response.json();
        }).then(function(data) {
            console.log('✅ Booth position saved successfully:', boothId, data);
            return data;
        }).catch(function(error) {
            console.error('❌ Error saving booth position:', error);
            // Don't throw error - just log it so it doesn't break other functionality
            return { error: error.message };
        });
    },
    
    // Save all booths on the canvas
    saveAllBooths: function() {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) {
            alert('Canvas not found!');
            return;
        }
        
        // Get all booths on the canvas
        const booths = canvas.querySelectorAll('.dropped-booth');
        if (booths.length === 0) {
            alert('No booths to save!');
            return;
        }
        
        // Disable save button and show loading state
        const saveBtn = $('#btnSave');
        const originalText = saveBtn.html();
        saveBtn.prop('disabled', true);
        saveBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        // Collect all booth data including style properties
        const boothData = [];
        booths.forEach(function(booth) {
            const boothId = booth.getAttribute('data-booth-id');
            const x = parseFloat(booth.style.left) || null;
            const y = parseFloat(booth.style.top) || null;
            const width = parseFloat(booth.style.width) || null;
            const height = parseFloat(booth.style.height) || null;
            const rotation = parseFloat(booth.getAttribute('data-rotation')) || 0;
            const zIndex = parseFloat(booth.style.zIndex) || 10;
            const fontSize = parseFloat(booth.style.fontSize) || 14;
            const borderWidth = parseFloat(booth.style.borderWidth) || 2;
            const borderRadius = parseFloat(booth.style.borderRadius) || 6;
            const opacity = parseFloat(booth.style.opacity) || 1.00;
            
            if (boothId && (x !== null || y !== null)) {
                boothData.push({
                    id: parseInt(boothId),
                    position_x: x,
                    position_y: y,
                    width: width,
                    height: height,
                    rotation: rotation,
                    z_index: zIndex,
                    font_size: fontSize,
                    border_width: borderWidth,
                    border_radius: borderRadius,
                    opacity: opacity
                });
            }
        });
        
        if (boothData.length === 0) {
            saveBtn.prop('disabled', false);
            saveBtn.html(originalText);
            alert('No booths with valid positions to save!');
            return;
        }
        
        // Use bulk save endpoint for better performance
        fetch('/booths/save-all-positions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                booths: boothData
            })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            // Success
            saveBtn.prop('disabled', false);
            saveBtn.html('<i class="fas fa-check"></i> Saved!');
            
            // Show success message
            let successMsg = 'Successfully saved ' + data.saved + ' out of ' + data.total + ' booth(s)!';
            if (data.errors && data.errors.length > 0) {
                successMsg += '\n\nErrors: ' + data.errors.length + ' booth(s) failed to save.';
                console.warn('Save errors:', data.errors);
            }
            alert(successMsg);
            
            // Reset button text after 2 seconds
            setTimeout(function() {
                saveBtn.html(originalText);
            }, 2000);
        })
        .catch(function(error) {
            // Error
            saveBtn.prop('disabled', false);
            saveBtn.html(originalText);
            console.error('Error saving booths:', error);
            alert('Error saving booths. Please check the console for details.');
        });
    },
    
    // Set canvas size
    setCanvasSize: function(width, height) {
        const canvas = document.getElementById('print');
        const container = document.getElementById('printContainer');
        if (!canvas) return;
        
        this.canvasWidth = width;
        this.canvasHeight = height;
        
        // Set canvas dimensions - unlimited size, expand as needed
        // Canvas can be larger than viewport - use panning to navigate
        canvas.style.width = Math.max(width, 10000) + 'px';
        canvas.style.height = Math.max(height, 10000) + 'px';
        canvas.style.minWidth = Math.max(width, 10000) + 'px';
        canvas.style.minHeight = Math.max(height, 10000) + 'px';
        // No max width/height - unlimited canvas
        canvas.style.maxWidth = 'none';
        canvas.style.maxHeight = 'none';
        canvas.style.flexShrink = '0';
        // Ensure background image fills entire canvas and positioned at top-left
        canvas.style.backgroundSize = '100% 100%';
        canvas.style.backgroundRepeat = 'no-repeat';
        canvas.style.backgroundPosition = 'top left';
        canvas.style.backgroundAttachment = 'local';
        canvas.style.margin = '0';
        canvas.style.display = 'block';
        
        // Ensure container has no scrollbars - use panning instead
        if (container) {
            container.style.width = '100%';
            container.style.height = '100%';
            container.style.minWidth = '0';
            container.style.minHeight = '0';
            container.style.display = 'block';
            container.style.overflow = 'hidden'; // No scrollbars - panning only
        }
        
        // Update grid overlay size
        const gridOverlay = document.getElementById('gridOverlay');
        if (gridOverlay) {
            gridOverlay.style.width = width + 'px';
            gridOverlay.style.height = height + 'px';
        }
        
        console.log('Canvas size set to: ' + width + 'x' + height + 'px');
    },
    
    // Set grid size
    setGridSize: function(size) {
        this.gridSize = size;
        
        // Update grid overlay CSS
        const gridOverlay = document.getElementById('gridOverlay');
        if (gridOverlay) {
            gridOverlay.style.backgroundSize = size + 'px ' + size + 'px';
        }
        
        console.log('Grid size set to: ' + size + 'px');
    },
    
    // Load canvas settings from localStorage
    // Load booth default settings from localStorage
    loadBoothSettings: function() {
        const savedWidth = localStorage.getItem('defaultBoothWidth');
        const savedHeight = localStorage.getItem('defaultBoothHeight');
        const savedRotation = localStorage.getItem('defaultBoothRotation');
        const savedZIndex = localStorage.getItem('defaultBoothZIndex');
        const savedFontSize = localStorage.getItem('defaultBoothFontSize');
        const savedBorderWidth = localStorage.getItem('defaultBoothBorderWidth');
        const savedBorderRadius = localStorage.getItem('defaultBoothBorderRadius');
        const savedOpacity = localStorage.getItem('defaultBoothOpacity');
        
        if (savedWidth) {
            this.defaultBoothWidth = parseInt(savedWidth);
        }
        if (savedHeight) {
            this.defaultBoothHeight = parseInt(savedHeight);
        }
        if (savedRotation) {
            this.defaultBoothRotation = parseInt(savedRotation);
        }
        if (savedZIndex) {
            this.defaultBoothZIndex = parseInt(savedZIndex);
        }
        if (savedFontSize) {
            this.defaultBoothFontSize = parseInt(savedFontSize);
        }
        if (savedBorderWidth) {
            this.defaultBoothBorderWidth = parseInt(savedBorderWidth);
        }
        if (savedBorderRadius) {
            this.defaultBoothBorderRadius = parseInt(savedBorderRadius);
        }
        if (savedOpacity) {
            this.defaultBoothOpacity = parseFloat(savedOpacity);
        }
    },
    
    // Save booth default settings to localStorage
    saveBoothSettings: function() {
        localStorage.setItem('defaultBoothWidth', this.defaultBoothWidth);
        localStorage.setItem('defaultBoothHeight', this.defaultBoothHeight);
        localStorage.setItem('defaultBoothRotation', this.defaultBoothRotation);
        localStorage.setItem('defaultBoothZIndex', this.defaultBoothZIndex);
        localStorage.setItem('defaultBoothFontSize', this.defaultBoothFontSize);
        localStorage.setItem('defaultBoothBorderWidth', this.defaultBoothBorderWidth);
        localStorage.setItem('defaultBoothBorderRadius', this.defaultBoothBorderRadius);
        localStorage.setItem('defaultBoothOpacity', this.defaultBoothOpacity);
    },
    
    loadCanvasSettings: function() {
        const savedWidth = localStorage.getItem('canvasWidth');
        const savedHeight = localStorage.getItem('canvasHeight');
        const savedResolution = localStorage.getItem('canvasResolution');
        const savedGridSize = localStorage.getItem('gridSize');
        const savedUploadSizeLimit = localStorage.getItem('uploadSizeLimit');
        
        if (savedWidth) {
            this.canvasWidth = parseInt(savedWidth);
        }
        if (savedHeight) {
            this.canvasHeight = parseInt(savedHeight);
        }
        if (savedResolution) {
            this.canvasResolution = parseInt(savedResolution);
        }
        if (savedGridSize) {
            this.setGridSize(parseInt(savedGridSize));
        }
        if (savedUploadSizeLimit) {
            this.uploadSizeLimit = parseInt(savedUploadSizeLimit);
        }
        
        // Apply saved dimensions
        if (savedWidth && savedHeight) {
            this.setCanvasSize(this.canvasWidth, this.canvasHeight);
        }
        
        // Prevent canvas from resizing when browser window resizes
        // This ensures booths stay in their fixed positions
        const self = this;
        let resizeTimeout;
        window.addEventListener('resize', function() {
            // Debounce resize events
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                // Maintain fixed canvas size regardless of browser resize
                const canvas = document.getElementById('print');
                if (canvas && self.canvasWidth && self.canvasHeight) {
                    canvas.style.width = self.canvasWidth + 'px';
                    canvas.style.height = self.canvasHeight + 'px';
                    canvas.style.minWidth = self.canvasWidth + 'px';
                    canvas.style.minHeight = self.canvasHeight + 'px';
                    canvas.style.maxWidth = self.canvasWidth + 'px';
                    canvas.style.maxHeight = self.canvasHeight + 'px';
                    // Ensure background image fills entire canvas and positioned at top-left
                    canvas.style.backgroundSize = '100% 100%';
                    canvas.style.backgroundRepeat = 'no-repeat';
                    canvas.style.backgroundPosition = 'top left';
                    canvas.style.backgroundAttachment = 'local';
                    canvas.style.margin = '0';
                    canvas.style.display = 'block';
                    canvas.style.float = 'left';
                }
            }, 100);
        });
    },
    
    // Clear all booths from canvas
    clearCanvas: function() {
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        const self = this;
        const allBooths = canvas.querySelectorAll('.dropped-booth');
        const boothIds = [];
        
        // Collect all booth IDs
        allBooths.forEach(function(booth) {
            const boothId = booth.getAttribute('data-booth-id');
            if (boothId) {
                boothIds.push(boothId);
            }
        });
        
        // Remove all booths from canvas
        allBooths.forEach(function(booth) {
            booth.remove();
        });
        
        // Clear positions in database for all booths
        boothIds.forEach(function(boothId) {
            self.saveBoothPosition(boothId, null, null, null, null, null);
        });
        
        // Clear selection and hide properties panel
        self.selectedBooths = [];
        self.updateInfoToolbar(null);
        const propertiesPanel = document.getElementById('propertiesPanel');
        const backdrop = document.getElementById('propertiesPanelBackdrop');
        if (propertiesPanel) {
            propertiesPanel.classList.remove('active');
        }
        if (backdrop) {
            backdrop.classList.remove('active');
        }
        
        // Save state for undo/redo
        self.saveState();
        
        console.log('Canvas cleared: ' + boothIds.length + ' booths removed');
    },
    
    // Detect existing floorplan image and resize canvas to match its dimensions
    detectAndResizeCanvasToImage: function() {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Check if there's a background image
        const bgImage = canvas.style.backgroundImage;
        const floorplanImg = document.getElementById('floorplanImageElement');
        
        // Try to get image from either background or img element
        let imageUrl = null;
        if (bgImage && bgImage !== 'none' && bgImage !== '') {
            imageUrl = bgImage.replace(/url\(['"]?([^'"]+)['"]?\)/, '$1');
        } else if (floorplanImg && floorplanImg.src) {
            imageUrl = floorplanImg.src;
        }
        
        if (imageUrl) {
            // Create a new image to get dimensions
            const img = new Image();
            img.onload = function() {
                const imageWidth = img.width;
                const imageHeight = img.height;
                
                if (imageWidth > 0 && imageHeight > 0) {
                    // Update canvas size to match image resolution
                    self.setCanvasSize(imageWidth, imageHeight);
                    
                    // Save new canvas size to localStorage
                    localStorage.setItem('canvasWidth', imageWidth);
                    localStorage.setItem('canvasHeight', imageHeight);
                    
                    // Update canvas settings in memory
                    self.canvasWidth = imageWidth;
                    self.canvasHeight = imageHeight;
                    
                    // Lock canvas at image size and set zoom constraints
                    if (self.panzoomInstance) {
                        setTimeout(function() {
                            // Reset zoom to 100%
                            if (self.panzoomInstance.reset) {
                                self.panzoomInstance.reset();
                            }
                            
                            // Keep default minScale (0.1) to allow free zooming like n8n
                            if (self.panzoomInstance.setOptions) {
                                self.panzoomInstance.setOptions({
                                    minScale: 0.1,  // Allow zooming out freely like n8n
                                    maxScale: 5,    // Keep max zoom at 5x
                                    contain: 'outside'
                                });
                            }
                            
                            // Update zoom level display
                            self.zoomLevel = 1;
                            $('#zoomLevel').text('100%');
                            
                            console.log('Canvas auto-resized to image size:', imageWidth, 'x', imageHeight);
                        }, 300);
                    }
                }
            };
            
            img.onerror = function() {
                console.log('Could not load image to detect dimensions');
            };
            
            // Load image to get dimensions
            img.src = imageUrl;
        }
    },
    
    // Load saved positions
    loadSavedPositions: function() {
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Make booths data globally available for info toolbar
        if (typeof window.boothsData === 'undefined') {
            window.boothsData = <?php echo json_encode($boothsForJS, 15, 512) ?>;
        }
        
        const booths = <?php echo json_encode($booths, 15, 512) ?>;
        const self = this;
        
        booths.forEach(function(booth) {
            if (booth.position_x !== null && booth.position_y !== null) {
                const boothData = {
                    id: booth.id,
                    number: booth.booth_number,
                    status: booth.status,
                    clientId: booth.client_id || '',
                    userId: booth.userid || '',
                    categoryId: booth.category_id || '',
                    subCategoryId: booth.sub_category_id || '',
                    assetId: booth.asset_id || '',
                    boothTypeId: booth.booth_type_id || ''
                };
                
                const extendedBoothData = {
                    id: boothData.id,
                    number: boothData.number,
                    status: boothData.status,
                    clientId: boothData.clientId,
                    userId: boothData.userId,
                    categoryId: boothData.categoryId,
                    subCategoryId: boothData.subCategoryId,
                    assetId: boothData.assetId,
                    boothTypeId: boothData.boothTypeId,
                    width: booth.width || 80,
                    height: booth.height || 50,
                    rotation: booth.rotation || 0,
                    x: booth.position_x,
                    y: booth.position_y
                };
                const boothElement = self.createBoothElement(extendedBoothData);
                boothElement.style.left = booth.position_x + 'px';
                boothElement.style.top = booth.position_y + 'px';
                // Apply saved width and height if they exist
                if (booth.width) {
                    boothElement.style.width = booth.width + 'px';
                }
                if (booth.height) {
                    boothElement.style.height = booth.height + 'px';
                }
                // Apply saved rotation if it exists
                if (booth.rotation) {
                    boothElement.style.transform = 'rotate(' + booth.rotation + 'deg)';
                }
                // Apply saved style properties if they exist
                if (booth.z_index) {
                    boothElement.style.zIndex = booth.z_index;
                }
                if (booth.font_size) {
                    boothElement.style.fontSize = booth.font_size + 'px';
                }
                if (booth.border_width !== null && booth.border_width !== undefined) {
                    boothElement.style.borderWidth = booth.border_width + 'px';
                }
                if (booth.border_radius !== null && booth.border_radius !== undefined) {
                    boothElement.style.borderRadius = booth.border_radius + 'px';
                }
                if (booth.opacity !== null && booth.opacity !== undefined) {
                    boothElement.style.opacity = booth.opacity;
                }
                canvas.appendChild(boothElement);
                self.makeBoothDraggable(boothElement);
            }
        });
    },
    
    // Setup toolbar
    setupToolbar: function() {
        const self = this;
        
        // Tool selection (Photoshop-like)
        $('#btnSelect').on('click', function() {
            self.currentTool = 'select';
            $('.toolbar-btn[data-tool]').removeClass('active');
            $(this).addClass('active');
            $('#print').removeClass('tool-hand').addClass('tool-select');
            const canvas = document.getElementById('print');
            if (canvas) canvas.style.cursor = 'default';
            // Disable Panzoom panning for Select tool (will be enabled when Space is pressed)
            if (self.panzoomInstance && self.panzoomInstance.setOptions) {
                self.panzoomInstance.setOptions({ disablePan: true });
            }
        });
        
        // Set Select tool as active by default
        $('#btnSelect').addClass('active');
        self.currentTool = 'select';
        
        $('#btnHand').on('click', function() {
            self.currentTool = 'hand';
            $('.toolbar-btn[data-tool]').removeClass('active');
            $(this).addClass('active');
            $('#print').removeClass('tool-select').addClass('tool-hand');
        });
        
        // Set default tool
        self.currentTool = 'select';
        $('#btnGrid').on('click', function() {
            self.gridEnabled = !self.gridEnabled;
            const gridOverlay = $('#gridOverlay');
            if (self.gridEnabled) {
                gridOverlay.addClass('visible');
                $(this).addClass('active');
                console.log('Grid enabled - showing grid overlay');
                } else {
                gridOverlay.removeClass('visible');
                $(this).removeClass('active');
                console.log('Grid disabled - hiding grid overlay');
            }
        });
        
        // Initialize grid state on page load
        if (self.gridEnabled) {
            $('#gridOverlay').addClass('visible');
            $('#btnGrid').addClass('active');
        }
        
        $('#btnSnap').on('click', function() {
            self.snapEnabled = !self.snapEnabled;
            $(this).toggleClass('active', self.snapEnabled);
        });
        
        $('#btnSave').on('click', function() {
            self.saveAllBooths();
        });
        
        // Floorplan Dropdown toggle
        $('#btnFloorplanDropdown').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const dropdown = $(this).closest('.dropdown');
            dropdown.toggleClass('show');
        });
        
        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown').removeClass('show');
            }
        });
        
        // Upload Floorplan button (from dropdown)
        $('#btnUploadFloorplan').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.dropdown').removeClass('show');
            $('#uploadFloorplanModal').modal('show');
            // Reset form
            $('#uploadFloorplanForm')[0].reset();
            $('#imagePreview').hide();
            // Update upload size limit text
            const uploadLimit = self.uploadSizeLimit || 10;
            const uploadLimitText = $('#uploadSizeLimitText');
            if (uploadLimit > 0) {
                uploadLimitText.text('Supported formats: JPG, PNG, GIF. Maximum size: ' + uploadLimit + 'MB');
            } else {
                uploadLimitText.text('Supported formats: JPG, PNG, GIF. No size limit.');
            }
        });
        
        // Remove Floorplan button (from dropdown)
        $('#btnRemoveFloorplan').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.dropdown').removeClass('show');
            
            if (!confirm('Are you sure you want to remove the floorplan image? This action cannot be undone.')) {
                return;
            }
            
            // Show loading state
            const removeBtn = $(this);
            const originalHtml = removeBtn.html();
            removeBtn.html('<i class="fas fa-spinner fa-spin"></i> Removing...');
            removeBtn.css('pointer-events', 'none');
            
            // Send AJAX request to remove floorplan
            $.ajax({
                url: '<?php echo e(route("booths.remove-floorplan")); ?>',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 200) {
                        // Remove background image from canvas
                        const canvas = document.getElementById('print');
                        if (canvas) {
                            canvas.style.backgroundImage = 'none';
                            canvas.style.background = 'linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%)';
                        }
                        
                        // Remove floorplan image element
                        const floorplanImg = document.getElementById('floorplanImageElement');
                        if (floorplanImg) {
                            floorplanImg.remove();
                        }
                        
                        alert('Floorplan removed successfully!');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to remove floorplan'));
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to remove floorplan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                },
                complete: function() {
                    removeBtn.html(originalHtml);
                    removeBtn.css('pointer-events', 'auto');
                }
            });
        });
        
        // Preview image before upload
        $('#floorplanImageInput').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImage').attr('src', e.target.result);
                    $('#imagePreview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').hide();
            }
        });
        
        // Handle floorplan upload
        $('#btnUploadFloorplanSubmit').on('click', function() {
            const form = $('#uploadFloorplanForm')[0];
            const submitBtn = $(this);
            const originalText = submitBtn.html();
            
            // Validate file
            const fileInput = document.getElementById('floorplanImageInput');
            if (!fileInput || !fileInput.files || !fileInput.files[0]) {
                alert('Please select an image file to upload.');
                return;
            }
            
            // Create FormData
            const formData = new FormData(form);
            
            const file = fileInput.files[0];
            
            // Check file size (configurable limit)
            const fileSize = file.size / 1024 / 1024; // Size in MB
            const uploadLimit = self.uploadSizeLimit || 10; // Default to 10MB if not set
            if (uploadLimit > 0 && fileSize > uploadLimit) {
                alert('File size exceeds ' + uploadLimit + 'MB limit. Please choose a smaller image.');
                return;
            }
            
            // Check file type
            if (!file.type.match('image.*')) {
                alert('Please select a valid image file (JPG, PNG, GIF).');
                return;
            }
            
            // Disable button and show loading
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
            
            // Upload file
            $.ajax({
                url: '/booths/upload-floorplan',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 200) {
                        // Update canvas background - fill entire canvas and center
                        const canvas = document.getElementById('print');
                        if (canvas) {
                            canvas.style.backgroundImage = 'url(\'' + response.image_url + '?t=' + Date.now() + '\')';
                            canvas.style.backgroundSize = '100% 100%';
                            canvas.style.backgroundRepeat = 'no-repeat';
                            canvas.style.backgroundPosition = 'top left';
                            canvas.style.backgroundAttachment = 'local';
                            canvas.style.margin = '0';
                            canvas.style.display = 'block';
                            canvas.style.float = 'left';
                            // Force reflow to ensure styles are applied
                            canvas.offsetHeight;
                        }
                        
                        // Update or create floorplan image element
                        let floorplanImg = document.getElementById('floorplanImageElement');
                        if (!floorplanImg) {
                            floorplanImg = document.createElement('img');
                            floorplanImg.id = 'floorplanImageElement';
                            floorplanImg.alt = 'Floor Plan Map';
                            floorplanImg.style.display = 'none';
                            canvas.appendChild(floorplanImg);
                        }
                        // Wait for image to load, then resize canvas to match image dimensions
                        floorplanImg.onload = function() {
                            // Resize canvas to match image dimensions if provided
                            if (response.image_width && response.image_height) {
                                const imageWidth = parseInt(response.image_width);
                                const imageHeight = parseInt(response.image_height);
                                
                                if (imageWidth > 0 && imageHeight > 0) {
                                    // Update canvas size to match image resolution
                                    self.setCanvasSize(imageWidth, imageHeight);
                                    
                                    // Save new canvas size to localStorage
                                    localStorage.setItem('canvasWidth', imageWidth);
                                    localStorage.setItem('canvasHeight', imageHeight);
                                    
                                    // Update canvas settings in memory
                                    self.canvasWidth = imageWidth;
                                    self.canvasHeight = imageHeight;
                                    
                                    // Lock canvas at image size and set zoom constraints
                                    if (self.panzoomInstance) {
                                        // Wait a bit for canvas to resize, then reset zoom and lock
                                        setTimeout(function() {
                                            // Reset zoom to 100% first
                                            if (self.panzoomInstance.reset) {
                                                self.panzoomInstance.reset();
                                            }
                                            
                                            // Keep default minScale (0.1) to allow free zooming like n8n
                                            if (self.panzoomInstance.setOptions) {
                                                self.panzoomInstance.setOptions({
                                                    minScale: 0.1,  // Allow zooming out freely like n8n
                                                    maxScale: 5,    // Keep max zoom at 5x
                                                    contain: 'outside'
                                                });
                                            }
                                            
                                            // Update zoom level display
                                            self.zoomLevel = 1;
                                            $('#zoomLevel').text('100%');
                                            
                                            console.log('Canvas resized to image size:', imageWidth, 'x', imageHeight);
                                        }, 300);
                                    }
                                }
                            }
                        };
                        
                        floorplanImg.src = response.image_url + '?t=' + Date.now();
                        
                        // Close modal
                        $('#uploadFloorplanModal').modal('hide');
                        alert('Floorplan uploaded successfully! Canvas size adjusted to match image dimensions.');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to upload floorplan'));
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to upload floorplan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                },
                complete: function() {
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }
            });
        });
        
        // Booth Settings button
        $('#btnBoothSettings').on('click', function() {
            $('#boothSettingsModal').modal('show');
            // Load current default settings
            $('#defaultWidth').val(self.defaultBoothWidth);
            $('#defaultHeight').val(self.defaultBoothHeight);
            $('#defaultRotation').val(self.defaultBoothRotation);
            $('#defaultZIndex').val(self.defaultBoothZIndex);
            $('#defaultFontSize').val(self.defaultBoothFontSize);
            $('#defaultBorderWidth').val(self.defaultBoothBorderWidth);
            $('#defaultBorderRadius').val(self.defaultBoothBorderRadius);
            $('#defaultOpacity').val(self.defaultBoothOpacity);
        });
        
        // Apply Booth Settings
        $('#applyBoothSettings').on('click', function() {
            self.defaultBoothWidth = parseInt($('#defaultWidth').val()) || 80;
            self.defaultBoothHeight = parseInt($('#defaultHeight').val()) || 50;
            self.defaultBoothRotation = parseInt($('#defaultRotation').val()) || 0;
            self.defaultBoothZIndex = parseInt($('#defaultZIndex').val()) || 10;
            self.defaultBoothFontSize = parseInt($('#defaultFontSize').val()) || 14;
            self.defaultBoothBorderWidth = parseInt($('#defaultBorderWidth').val()) || 2;
            self.defaultBoothBorderRadius = parseInt($('#defaultBorderRadius').val()) || 6;
            self.defaultBoothOpacity = parseFloat($('#defaultOpacity').val()) || 1.00;
            
            // Save to localStorage
            self.saveBoothSettings();
            
            $('#boothSettingsModal').modal('hide');
            alert('Booth default settings saved! New booths will use these settings.');
        });
        
        // Canvas Settings button
        $('#btnSettings').on('click', function() {
            $('#canvasSettingsModal').modal('show');
            // Load current settings
            $('#canvasWidth').val(self.canvasWidth);
            $('#canvasHeight').val(self.canvasHeight);
            $('#canvasResolution').val(self.canvasResolution);
            $('#gridSize').val(self.gridSize);
            $('#uploadSizeLimit').val(self.uploadSizeLimit);
        });
        
        // Apply Canvas Settings
        $('#applyCanvasSettings').on('click', function() {
            const width = parseInt($('#canvasWidth').val()) || 1200;
            const height = parseInt($('#canvasHeight').val()) || 800;
            const resolution = parseInt($('#canvasResolution').val()) || 300;
            const gridSize = parseInt($('#gridSize').val()) || 10;
            const uploadSizeLimit = parseInt($('#uploadSizeLimit').val()) || 10;
            
            self.setCanvasSize(width, height);
            self.canvasResolution = resolution;
            self.setGridSize(gridSize);
            self.uploadSizeLimit = uploadSizeLimit;
            
            // Save to localStorage
            localStorage.setItem('canvasWidth', width);
            localStorage.setItem('canvasHeight', height);
            localStorage.setItem('canvasResolution', resolution);
            localStorage.setItem('gridSize', gridSize);
            localStorage.setItem('uploadSizeLimit', uploadSizeLimit);
            
            $('#canvasSettingsModal').modal('hide');
            alert('Canvas settings applied successfully!');
        });
        
        // Toggle Properties Panel button
        $('#btnToggleProperties').on('click', function() {
            self.propertiesPanelEnabled = !self.propertiesPanelEnabled;
            const toggleText = $('#propertiesToggleText');
            const btn = $(this);
            
            if (self.propertiesPanelEnabled) {
                toggleText.text('Properties: ON');
                btn.css('background', 'rgba(40, 167, 69, 0.3)');
                btn.attr('title', 'Properties Panel: Enabled (Double-click booth to open)');
            } else {
                toggleText.text('Properties: OFF');
                btn.css('background', 'rgba(108, 117, 125, 0.3)');
                btn.attr('title', 'Properties Panel: Disabled');
                // Close panel if it's open
                $('#propertiesPanel').removeClass('active');
                $('#propertiesPanelBackdrop').removeClass('active');
            }
        });
        
        // Clear Canvas button
        $('#btnClearCanvas').on('click', function() {
            if (confirm('Are you sure you want to clear all booths from the canvas? This action cannot be undone.')) {
                self.clearCanvas();
            }
        });
        
        // Helper function to get the center point of all booths (or selected booths)
        function getBoothsCenter() {
            const canvas = document.getElementById('print');
            if (!canvas) return null;
            
            // Get selected booths first, or all booths if none selected
            let booths = Array.from(document.querySelectorAll('.dropped-booth.selected'));
            if (booths.length === 0) {
                booths = Array.from(document.querySelectorAll('.dropped-booth'));
            }
            
            if (booths.length === 0) {
                // No booths found, return canvas center
                return {
                    x: canvas.offsetWidth / 2,
                    y: canvas.offsetHeight / 2
                };
            }
            
            // Calculate bounding box of all booths
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            
            booths.forEach(function(booth) {
                const x = parseFloat(booth.style.left) || 0;
                const y = parseFloat(booth.style.top) || 0;
                const width = parseFloat(booth.style.width) || 80;
                const height = parseFloat(booth.style.height) || 50;
                
                minX = Math.min(minX, x);
                minY = Math.min(minY, y);
                maxX = Math.max(maxX, x + width);
                maxY = Math.max(maxY, y + height);
            });
            
            // Return center of bounding box
            return {
                x: (minX + maxX) / 2,
                y: (minY + maxY) / 2
            };
        }
        
        $('#zoomIn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (self.panzoomInstance) {
                const canvas = document.getElementById('print');
                const container = document.getElementById('printContainer');
                if (canvas && container) {
                    // Get current scale
                    const currentScale = self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : 1;
                    const newScale = Math.min(currentScale * 1.2, 5); // Increase by 20%, max 5x
                    
                    // Get the center point of booths
                    const boothsCenter = getBoothsCenter();
                    
                    if (boothsCenter) {
                        // Get current transform
                        const currentTransform = self.panzoomInstance.getTransform ? self.panzoomInstance.getTransform() : { x: 0, y: 0, scale: currentScale };
                        const currentX = currentTransform.x || 0;
                        const currentY = currentTransform.y || 0;
                        
                        // Calculate container center (viewport center)
                        const containerRect = container.getBoundingClientRect();
                        const containerCenterX = containerRect.width / 2;
                        const containerCenterY = containerRect.height / 2;
                        
                        // Calculate the point in canvas coordinates that should be at the viewport center after zoom
                        // This is the booths center point
                        const targetCanvasX = boothsCenter.x;
                        const targetCanvasY = boothsCenter.y;
                        
                        // Calculate where this point is currently on screen (before zoom)
                        const currentScreenX = targetCanvasX * currentScale + currentX;
                        const currentScreenY = targetCanvasY * currentScale + currentY;
                        
                        // After zoom, we want this point to be at the container center
                        // So: targetCanvasX * newScale + newX = containerCenterX
                        // Therefore: newX = containerCenterX - targetCanvasX * newScale
                        const newX = containerCenterX - targetCanvasX * newScale;
                        const newY = containerCenterY - targetCanvasY * newScale;
                        
                        // Apply zoom and pan together
                        if (self.panzoomInstance.setTransform) {
                            self.panzoomInstance.setTransform(newX, newY, newScale, { animate: true });
                        } else if (self.panzoomInstance.zoom) {
                            // If setTransform is not available, zoom first then adjust pan
                            self.panzoomInstance.zoom(newScale, { animate: true });
                            setTimeout(function() {
                                if (self.panzoomInstance.moveTo) {
                                    self.panzoomInstance.moveTo(newX, newY, { animate: true });
                                } else if (self.panzoomInstance.setTransform) {
                                    self.panzoomInstance.setTransform(newX, newY, newScale, { animate: true });
                                }
                            }, 50);
                        }
                    } else {
                        // Fallback to center zoom if no booths found
                        if (self.panzoomInstance.zoom) {
                            self.panzoomInstance.zoom(newScale, { animate: true });
                        }
                    }
                    
                    // Update zoom level display
                    setTimeout(function() {
                        const scale = self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : newScale;
                        self.zoomLevel = scale;
                        $('#zoomLevel').text(Math.round(scale * 100) + '%');
                    }, 100);
                }
            }
        });
        
        $('#zoomOut').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (self.panzoomInstance) {
                const canvas = document.getElementById('print');
                const container = document.getElementById('printContainer');
                if (canvas && container) {
                    // Get current scale and minimum scale
                    const currentScale = self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : 1;
                    let minScale = 0.1;
                    if (self.panzoomInstance.getOptions) {
                        const options = self.panzoomInstance.getOptions();
                        minScale = options.minScale || 0.1;
                    }
                    
                    const newScale = Math.max(currentScale / 1.2, minScale); // Decrease by 20%, respect minScale
                    
                    // Get the center point of booths
                    const boothsCenter = getBoothsCenter();
                    
                    if (boothsCenter) {
                        // Get current transform
                        const currentTransform = self.panzoomInstance.getTransform ? self.panzoomInstance.getTransform() : { x: 0, y: 0, scale: currentScale };
                        const currentX = currentTransform.x || 0;
                        const currentY = currentTransform.y || 0;
                        
                        // Calculate container center (viewport center)
                        const containerRect = container.getBoundingClientRect();
                        const containerCenterX = containerRect.width / 2;
                        const containerCenterY = containerRect.height / 2;
                        
                        // Calculate the point in canvas coordinates that should be at the viewport center after zoom
                        const targetCanvasX = boothsCenter.x;
                        const targetCanvasY = boothsCenter.y;
                        
                        // After zoom, we want this point to be at the container center
                        const newX = containerCenterX - targetCanvasX * newScale;
                        const newY = containerCenterY - targetCanvasY * newScale;
                        
                        // Apply zoom and pan together
                        if (self.panzoomInstance.setTransform) {
                            self.panzoomInstance.setTransform(newX, newY, newScale, { animate: true });
                        } else if (self.panzoomInstance.zoom) {
                            // If setTransform is not available, zoom first then adjust pan
                            self.panzoomInstance.zoom(newScale, { animate: true });
                            setTimeout(function() {
                                if (self.panzoomInstance.moveTo) {
                                    self.panzoomInstance.moveTo(newX, newY, { animate: true });
                                } else if (self.panzoomInstance.setTransform) {
                                    self.panzoomInstance.setTransform(newX, newY, newScale, { animate: true });
                                }
                            }, 50);
                        }
                    } else {
                        // Fallback to center zoom if no booths found
                        if (self.panzoomInstance.zoom) {
                            self.panzoomInstance.zoom(newScale, { animate: true });
                        }
                    }
                    
                    // Update zoom level display
                    setTimeout(function() {
                        const scale = self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : newScale;
                        self.zoomLevel = scale;
                        $('#zoomLevel').text(Math.round(scale * 100) + '%');
                    }, 100);
                }
            }
        });
        
        $('#zoomReset').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (self.panzoomInstance) {
                self.panzoomInstance.reset();
                self.zoomLevel = 1;
                $('#zoomLevel').text('100%');
                
                // Ensure zoom level is updated
                setTimeout(function() {
                    const scale = self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : 1;
                    self.zoomLevel = scale;
                    $('#zoomLevel').text(Math.round(scale * 100) + '%');
                }, 100);
            }
        });
        
        // Fit to Canvas - Center and fit the entire image to show it completely
        $('#zoomFit').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.fitCanvasToView(true); // true = animate
        });
        
        $('#toggleSidebar').on('click', function() {
            $('.designer-sidebar').toggleClass('collapsed');
            $(this).find('i').toggleClass('fa-chevron-left fa-chevron-right');
        });
        
        // Double-click on info toolbar to enable edit mode
        $('#infoToolbar').on('dblclick', function(e) {
            // Only enable edit mode if clicking on the toolbar content (not on dividers or labels)
            if (e.target.classList.contains('info-value') || e.target.closest('.info-value')) {
                self.enableInfoToolbarEditMode();
            }
        });
        
        // Close properties panel
        $('#closePropertiesPanel').on('click', function() {
            $('#propertiesPanel').removeClass('active');
            $('#propertiesPanelBackdrop').removeClass('active');
        });
        
        // Close properties panel when clicking backdrop
        $('#propertiesPanelBackdrop').on('click', function(e) {
            if (e.target === this) {
                $('#propertiesPanel').removeClass('active');
                $('#propertiesPanelBackdrop').removeClass('active');
            }
        });
        
        // Click on individual info value to enable edit mode for that field only
        $(document).on('click', '.info-value.info-editable', function(e) {
            // Don't trigger if clicking on an input that's already there
            if ($(this).find('input').length > 0) {
                // If input exists, just focus it
                $(this).find('input').focus().select();
                return;
            }
            
            // Don't trigger if already in edit mode
            if ($(this).hasClass('info-editing')) {
                return;
            }
            
            e.stopPropagation();
            
            const field = this;
            const property = $(field).attr('data-property');
            if (!property) return;
            
            console.log('Clicking on field to edit:', property);
            
            // Enable edit mode for just this field
            self.enableInfoToolbarEditModeForField(field, property);
        });
        
        // Double-click on info toolbar to enable edit mode for all fields
        $('#infoToolbar').on('dblclick', function(e) {
            // Only enable edit mode if clicking on the toolbar content (not on dividers or labels)
            if (e.target.classList.contains('info-value') || e.target.closest('.info-value')) {
                self.enableInfoToolbarEditMode();
            }
        });
        
        // Close properties panel with Escape key (priority handler)
        $(document).on('keydown', function(e) {
            // Close properties panel if it's open
            if (e.key === 'Escape' || e.keyCode === 27) {
                if ($('#propertiesPanel').hasClass('active')) {
                    e.preventDefault();
                    e.stopPropagation();
                    $('#propertiesPanel').removeClass('active');
                    $('#propertiesPanelBackdrop').removeClass('active');
                    return false;
                }
            }
        });
        
        // Keyboard shortcuts
        $(document).on('keydown', function(e) {
            // V for Move Tool
            if (e.key === 'v' && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                $('#btnSelect').click();
            }
            // H for Hand Tool
            if (e.key === 'h' && !e.ctrlKey && !e.metaKey) {
                e.preventDefault();
                $('#btnHand').click();
            }
        });
    },
    
    // Setup Photoshop-like zoom selection (Ctrl+Space + Drag)
    setupZoomSelection: function() {
        const self = this;
        const canvas = document.getElementById('print');
        const container = document.getElementById('printContainer');
        if (!canvas || !container) return;
        
        // Create zoom selection rectangle element
        const zoomSelection = document.createElement('div');
        zoomSelection.className = 'zoom-selection';
        container.appendChild(zoomSelection);
        self.zoomSelectionElement = zoomSelection;
        
        // Track Ctrl+Space key combination
        document.addEventListener('keydown', function(e) {
            // Check for Ctrl+Space (or Cmd+Space on Mac)
            if ((e.ctrlKey || e.metaKey) && e.code === 'Space' && !e.repeat) {
                self.ctrlSpacePressed = true;
                canvas.style.cursor = 'crosshair';
                e.preventDefault();
            }
        });
        
        document.addEventListener('keyup', function(e) {
            if (e.code === 'Space' || e.key === 'Control' || e.key === 'Meta') {
                // Only cancel if Ctrl/Cmd is released or Space is released
                if (!e.ctrlKey && !e.metaKey) {
                    self.ctrlSpacePressed = false;
                    if (self.isZoomSelecting) {
                        // Cancel zoom selection
                        self.cancelZoomSelection();
                    }
                    if (self.currentTool === 'select' && !self.isZoomSelecting) {
                        canvas.style.cursor = 'default';
                    }
                }
            }
        });
        
        // Handle mouse down for zoom selection
        container.addEventListener('mousedown', function(e) {
            // Only activate if Ctrl+Space is pressed and not clicking on a booth
            if (!self.ctrlSpacePressed) return;
            
            const target = e.target;
            const isBoothElement = target.closest('.dropped-booth') || 
                                  target.classList.contains('resize-handle') ||
                                  target.classList.contains('rotate-handle') ||
                                  target.closest('.transform-controls') ||
                                  target.closest('.booth-number-item');
            
            if (isBoothElement) return; // Don't interfere with booth interactions
            
            e.preventDefault();
            e.stopPropagation();
            
            // Get container bounds
            const rect = container.getBoundingClientRect();
            const startX = e.clientX - rect.left;
            const startY = e.clientY - rect.top;
            
            self.isZoomSelecting = true;
            self.zoomSelectionStart = { x: startX, y: startY };
            
            // Show selection rectangle
            zoomSelection.style.display = 'block';
            zoomSelection.style.left = startX + 'px';
            zoomSelection.style.top = startY + 'px';
            zoomSelection.style.width = '0px';
            zoomSelection.style.height = '0px';
            
            // Disable Panzoom during zoom selection
            if (self.panzoomInstance && self.panzoomInstance.setOptions) {
                self.panzoomInstance.setOptions({ disablePan: true });
            }
        });
        
        // Handle mouse move for zoom selection
        container.addEventListener('mousemove', function(e) {
            if (!self.isZoomSelecting || !self.zoomSelectionStart) return;
            
            e.preventDefault();
            e.stopPropagation();
            
            const rect = container.getBoundingClientRect();
            const currentX = e.clientX - rect.left;
            const currentY = e.clientY - rect.top;
            
            // Calculate selection rectangle
            const left = Math.min(self.zoomSelectionStart.x, currentX);
            const top = Math.min(self.zoomSelectionStart.y, currentY);
            const width = Math.abs(currentX - self.zoomSelectionStart.x);
            const height = Math.abs(currentY - self.zoomSelectionStart.y);
            
            // Update selection rectangle
            zoomSelection.style.left = left + 'px';
            zoomSelection.style.top = top + 'px';
            zoomSelection.style.width = width + 'px';
            zoomSelection.style.height = height + 'px';
        });
        
        // Handle mouse up for zoom selection
        container.addEventListener('mouseup', function(e) {
            if (!self.isZoomSelecting) return;
            
            e.preventDefault();
            e.stopPropagation();
            
            const rect = container.getBoundingClientRect();
            const endX = e.clientX - rect.left;
            const endY = e.clientY - rect.top;
            
            const selectionWidth = Math.abs(endX - self.zoomSelectionStart.x);
            const selectionHeight = Math.abs(endY - self.zoomSelectionStart.y);
            
            // Only zoom if selection is large enough (at least 10px)
            if (selectionWidth > 10 && selectionHeight > 10) {
                self.zoomToSelection(self.zoomSelectionStart.x, self.zoomSelectionStart.y, endX, endY);
            }
            
            // Clean up
            self.cancelZoomSelection();
        });
        
        // Handle mouse wheel for zoom at cursor location when Ctrl+Space is pressed
        container.addEventListener('wheel', function(e) {
            // Only activate if Ctrl+Space is pressed
            if (!self.ctrlSpacePressed) return;
            
            // Prevent default scrolling
            e.preventDefault();
            e.stopPropagation();
            
            // Don't interfere if hovering over a booth (let booth wheel handler work)
            const target = e.target;
            const isBoothElement = target.closest('.dropped-booth');
            if (isBoothElement) return;
            
            // Get current scale
            const currentScale = self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : 1;
            const minScale = 0.1;
            const maxScale = 10;
            
            // Calculate zoom factor (scroll up = zoom in, scroll down = zoom out)
            const zoomFactor = e.deltaY > 0 ? 0.9 : 1.1; // 10% zoom per scroll step
            const newScale = Math.max(minScale, Math.min(maxScale, currentScale * zoomFactor));
            
            // Get container and canvas dimensions
            const containerRect = container.getBoundingClientRect();
            const canvas = document.getElementById('print');
            const canvasWidth = canvas.offsetWidth || self.canvasWidth || 1200;
            const canvasHeight = canvas.offsetHeight || self.canvasHeight || 800;
            
            // Get cursor position relative to container
            const cursorX = e.clientX - containerRect.left;
            const cursorY = e.clientY - containerRect.top;
            
            // Get current transform
            const transform = self.panzoomInstance.getTransform ? self.panzoomInstance.getTransform() : { x: 0, y: 0, scale: currentScale };
            const currentX = transform.x || 0;
            const currentY = transform.y || 0;
            
            // Convert cursor position to canvas coordinates (before zoom)
            const canvasX = (cursorX - currentX) / currentScale;
            const canvasY = (cursorY - currentY) / currentScale;
            
            // Calculate new pan position to keep cursor point fixed
            // After zoom, we want: cursorX = canvasX * newScale + newX
            // Therefore: newX = cursorX - canvasX * newScale
            const newX = cursorX - canvasX * newScale;
            const newY = cursorY - canvasY * newScale;
            
            // Apply zoom and pan
            if (self.panzoomInstance.setTransform) {
                self.panzoomInstance.setTransform({ x: newX, y: newY, scale: newScale });
            } else if (self.panzoomInstance.zoom) {
                self.panzoomInstance.zoom(newScale, { animate: false });
                setTimeout(function() {
                    if (self.panzoomInstance.moveTo) {
                        self.panzoomInstance.moveTo(newX, newY, { animate: false });
                    } else if (self.panzoomInstance.setTransform) {
                        self.panzoomInstance.setTransform({ x: newX, y: newY, scale: newScale });
                    }
                }, 10);
            }
            
            // Update zoom level display
            setTimeout(function() {
                const scale = self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : newScale;
                self.zoomLevel = scale;
                $('#zoomLevel').text(Math.round(scale * 100) + '%');
            }, 50);
        }, { passive: false });
    },
    
    // Cancel zoom selection
    cancelZoomSelection: function() {
        this.isZoomSelecting = false;
        this.zoomSelectionStart = null;
        if (this.zoomSelectionElement) {
            this.zoomSelectionElement.style.display = 'none';
        }
        // Re-enable Panzoom
        if (this.panzoomInstance && this.panzoomInstance.setOptions) {
            this.panzoomInstance.setOptions({ disablePan: false });
        }
        // Reset cursor
        const canvas = document.getElementById('print');
        if (canvas && this.currentTool === 'select') {
            canvas.style.cursor = 'default';
        }
    },
    
    // Zoom to selected area
    zoomToSelection: function(startX, startY, endX, endY) {
        const self = this;
        const canvas = document.getElementById('print');
        const container = document.getElementById('printContainer');
        if (!canvas || !container || !self.panzoomInstance) return;
        
        // Get container dimensions
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;
        
        // Calculate selection rectangle
        const selectionLeft = Math.min(startX, endX);
        const selectionTop = Math.min(startY, endY);
        const selectionWidth = Math.abs(endX - startX);
        const selectionHeight = Math.abs(endY - startY);
        
        // Get current transform
        const transform = self.panzoomInstance.getTransform ? self.panzoomInstance.getTransform() : { x: 0, y: 0, scale: 1 };
        const currentScale = transform.scale || 1;
        
        // Get canvas dimensions
        const canvasWidth = canvas.offsetWidth || self.canvasWidth || 1200;
        const canvasHeight = canvas.offsetHeight || self.canvasHeight || 800;
        
        // Calculate the scale needed to fit the selection in the viewport
        const scaleX = containerWidth / selectionWidth;
        const scaleY = containerHeight / selectionHeight;
        const newScale = Math.min(scaleX, scaleY) * currentScale;
        
        // Clamp scale to reasonable limits
        const minScale = 0.1;
        const maxScale = 10;
        const clampedScale = Math.max(minScale, Math.min(maxScale, newScale));
        
        // Calculate the center of the selection in canvas coordinates
        // First, convert selection coordinates to canvas coordinates
        const selectionCenterX = selectionLeft + selectionWidth / 2;
        const selectionCenterY = selectionTop + selectionHeight / 2;
        
        // Convert container coordinates to canvas coordinates (accounting for current pan/zoom)
        const canvasCenterX = (selectionCenterX - transform.x) / currentScale;
        const canvasCenterY = (selectionCenterY - transform.y) / currentScale;
        
        // Calculate pan position to center the selection
        const viewportCenterX = containerWidth / 2;
        const viewportCenterY = containerHeight / 2;
        const panX = viewportCenterX - (canvasCenterX * clampedScale);
        const panY = viewportCenterY - (canvasCenterY * clampedScale);
        
        // Apply zoom and pan
        if (self.panzoomInstance.setTransform) {
            self.panzoomInstance.setTransform({ x: panX, y: panY, scale: clampedScale });
        } else if (self.panzoomInstance.zoom) {
            self.panzoomInstance.zoom(clampedScale, { animate: true });
            setTimeout(function() {
                if (self.panzoomInstance.pan) {
                    self.panzoomInstance.pan(panX, panY, { animate: true });
                }
            }, 200);
        }
        
        // Update zoom level display
        self.zoomLevel = clampedScale;
        $('#zoomLevel').text(Math.round(clampedScale * 100) + '%');
    },
    
    // Setup canvas with Panzoom (n8n-like behavior)
    setupCanvas: function() {
        const canvas = document.getElementById('print');
        if (!canvas || typeof Panzoom === 'undefined') {
            console.warn('Panzoom not available');
            return;
        }
        
        const self = this;
        
        // Initialize Panzoom with proper options (n8n-like behavior)
        // Enable free panning - no limits, work like n8n canvas
        this.panzoomInstance = Panzoom(canvas, {
            maxScale: 5,
            minScale: 0.1,
            contain: 'outside',
            disablePan: false, // Enable panning by default - free movement like n8n
            disableZoom: false,
            panOnlyWhenZoomed: false,
            // Exclude booth elements from Panzoom interactions
            // This prevents Panzoom from handling events on these elements
            exclude: ['.dropped-booth', '.resize-handle', '.rotate-handle', '.transform-controls', '.booth-number-item'],
            // Also exclude by checking the target in event handlers
            handleStartEvent: function(e) {
                // Don't let Panzoom handle events on booth elements
                const target = e.target;
                // Check if clicking on booth or any booth-related element
                if (target.closest('.dropped-booth') || 
                    target.classList.contains('dropped-booth') ||
                    target.classList.contains('resize-handle') ||
                    target.classList.contains('rotate-handle') ||
                    target.classList.contains('transform-controls') ||
                    target.closest('.transform-controls') ||
                    target.closest('.booth-number-item') ||
                    target.closest('.resize-handle') ||
                    target.closest('.rotate-handle')) {
                    return false; // Don't let Panzoom handle this - let booth handlers work
                }
                // Allow panning on canvas background
                return true;
            }
        });
        
        // #region agent log
        fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:setupCanvas','message':'Panzoom initialized','data':{'disablePan':false,'excludeElements':['.dropped-booth','.resize-handle','.rotate-handle','.transform-controls']},timestamp:Date.now(),sessionId:'debug-session','runId':'run1','hypothesisId':'A'})}).catch(function(){});
        // #endregion
        
        // Handle zoom events - Panzoom uses events on the element
        // Listen to panzoomzoom event on the canvas
        canvas.addEventListener('panzoomzoom', function(e) {
            const scale = e.detail ? e.detail.scale : (self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : 1);
            self.zoomLevel = scale;
            $('#zoomLevel').text(Math.round(scale * 100) + '%');
        });
        
        // Also update on panzoomchange (more reliable for all zoom operations)
        canvas.addEventListener('panzoomchange', function(e) {
            const scale = self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : 1;
            self.zoomLevel = scale;
            $('#zoomLevel').text(Math.round(scale * 100) + '%');
        });
        
        // Listen for wheel zoom events to update display
        canvas.addEventListener('wheel', function(e) {
            if (self.panzoomInstance && self.panzoomInstance.getScale) {
                setTimeout(function() {
                    const scale = self.panzoomInstance.getScale();
                    self.zoomLevel = scale;
                    $('#zoomLevel').text(Math.round(scale * 100) + '%');
                }, 50);
            }
        }, { passive: true });
        
        // Initial zoom level - use setTimeout to ensure Panzoom is fully initialized
        setTimeout(function() {
            if (self.panzoomInstance && self.panzoomInstance.getScale) {
                self.zoomLevel = self.panzoomInstance.getScale();
                $('#zoomLevel').text(Math.round(self.zoomLevel * 100) + '%');
            }
        }, 100);
        
        // Track if we're interacting with a booth
        let isInteractingWithBooth = false;
        let spacePressed = false;
        
        // Space + Drag for canvas panning (n8n style)
        document.addEventListener('keydown', function(e) {
            if (e.code === 'Space' && !e.repeat) {
                spacePressed = true;
    // #region agent log
                fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:setupCanvas','message':'Space key pressed','data':{'currentTool':self.currentTool},timestamp:Date.now(),sessionId:'debug-session','runId':'run1','hypothesisId':'B'})}).catch(function(){});
    // #endregion
                if (self.currentTool === 'select') {
                    canvas.style.cursor = 'grab';
                }
                // Panning is always enabled (n8n-like), but we can still track Space key for cursor
                // Panning is already enabled by default
                e.preventDefault();
            }
        });
        
        document.addEventListener('keyup', function(e) {
            if (e.code === 'Space') {
                spacePressed = false;
                // #region agent log
                fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:setupCanvas','message':'Space key released','data':{'currentTool':self.currentTool},timestamp:Date.now(),sessionId:'debug-session','runId':'run1','hypothesisId':'B'})}).catch(function(){});
                // #endregion
                if (self.currentTool === 'select' && !isInteractingWithBooth) {
                    canvas.style.cursor = 'default';
                }
                // Panning is always enabled (n8n-like) - no need to disable
            }
        });
        
        // Prevent Panzoom from interfering with booth interactions (n8n-like)
        // DON'T add a handler on canvas for booth elements - let them handle their own events
        // Only handle canvas background clicks
        // Use bubble phase (false) so booth handlers (capture phase) fire first
        // BUT we need to make sure we don't interfere with booth mousedown
        canvas.addEventListener('mousedown', function(e) {
            // CRITICAL: Check for booth elements FIRST, before any other logic
            const target = e.target;
            const isBoothElement = target.closest('.dropped-booth') || 
                                  target.classList.contains('resize-handle') ||
                                  target.classList.contains('rotate-handle') ||
                                  target.closest('.transform-controls') ||
                                  target.closest('.booth-number-item');
            
            // If clicking on a booth element, do NOTHING - let the booth's own handler run
            // The booth handler uses capture phase, so it fires BEFORE this handler
            // But we still need to exit early to avoid any interference
            if (isBoothElement) {
                console.log('✅ Canvas detected booth element - NOT interfering, button:', e.button, 'target:', target.className);
                isInteractingWithBooth = true;
        // #region agent log
                fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:setupCanvas','message':'Booth element detected - NOT interfering','data':{'targetTag':target.tagName,'targetClass':target.className,'button':e.button},timestamp:Date.now(),sessionId:'debug-session','runId':'run1','hypothesisId':'D'})}).catch(function(){});
        // #endregion
                // CRITICAL: Don't prevent default or stop propagation - let booth handler work
                return; // Exit immediately - don't prevent anything, don't stop propagation
            }
            
            // Continue with canvas background handling (only for non-booth elements)
            // #region agent log
            fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:setupCanvas','message':'Canvas mousedown - background','data':{'targetTag':target.tagName,'targetClass':target.className,'currentTool':self.currentTool,'spacePressed':spacePressed},timestamp:Date.now(),sessionId:'debug-session','runId':'run1','hypothesisId':'D'})}).catch(function(){});
            // #endregion
            
            // Only handle canvas background clicks (not booth elements)
            // Enable panning only with Space+drag, middle mouse, or Hand tool
            const shouldPan = (spacePressed || self.currentTool === 'hand' || e.button === 1) && e.target === canvas;
            
            if (shouldPan) {
                // Enable Panzoom panning for this interaction
                isInteractingWithBooth = false;
                if (self.panzoomInstance && self.panzoomInstance.setOptions) {
                    self.panzoomInstance.setOptions({ disablePan: false });
                }
            } else if (e.target === canvas) {
                // Allow clicking on canvas background - Panzoom will handle panning only when dragging
                isInteractingWithBooth = false;
                // Don't prevent default - let Panzoom handle it for panning
            }
        }, false); // Use bubble phase - booth handlers run first, then canvas handler only for background
        
        // Panning is always enabled (n8n-like) - no need to disable on mouseup
        document.addEventListener('mouseup', function() {
            setTimeout(function() {
                isInteractingWithBooth = false;
            }, 50);
        });
        
        // Update Panzoom panning state when Space is pressed/released
        const spaceKeyHandler = function(e) {
            if (e.code === 'Space') {
                if (e.type === 'keydown' && !e.repeat) {
                    if (self.panzoomInstance && self.panzoomInstance.setOptions) {
                        self.panzoomInstance.setOptions({ disablePan: false });
                    }
                } else if (e.type === 'keyup') {
                    if (self.currentTool !== 'hand' && self.panzoomInstance && self.panzoomInstance.setOptions) {
                        self.panzoomInstance.setOptions({ disablePan: true });
                    }
                }
            }
        };
        
        // Update existing Space key handlers to also control Panzoom
        const existingKeydown = document.querySelector('body').onkeydown;
        document.addEventListener('keydown', spaceKeyHandler);
        document.addEventListener('keyup', spaceKeyHandler);
        
        // Click on canvas background to deselect booths
        canvas.addEventListener('click', function(e) {
            // Only deselect if clicking directly on canvas (not on booths)
            if (e.target === canvas || e.target.id === 'print') {
                // Deselect all booths
                document.querySelectorAll('.dropped-booth').forEach(function(booth) {
                    booth.classList.remove('selected');
                    const ctrl = booth.querySelector('.transform-controls');
                    if (ctrl) {
                        ctrl.style.display = 'none';
                        ctrl.style.visibility = 'hidden';
                        ctrl.style.opacity = '0';
                    }
                    // Hide resize handles
                    const handles = booth.querySelectorAll('.resize-handle');
                    handles.forEach(function(handle) {
                        handle.style.display = 'none';
                    });
                    // Hide rotation handle
                    const rotateHandle = booth.querySelector('.rotate-handle');
                    if (rotateHandle) {
                        rotateHandle.style.display = 'none';
                    }
                });
                
                self.selectedBooths = [];
                self.updateInfoToolbar(null);
                
                // Hide properties panel
                const propertiesPanel = document.getElementById('propertiesPanel');
                const backdrop = document.getElementById('propertiesPanelBackdrop');
                if (propertiesPanel) {
                    propertiesPanel.classList.remove('active');
                }
                if (backdrop) {
                    backdrop.classList.remove('active');
                }
            }
        }, false);
        
        // Middle mouse button for panning (n8n style)
        canvas.addEventListener('mousedown', function(e) {
            if (e.button === 1) { // Middle mouse button
                e.preventDefault();
                canvas.style.cursor = 'grabbing';
                let startX = e.clientX;
                let startY = e.clientY;
                const transform = self.panzoomInstance.getTransform();
                let startPanX = transform.x;
                let startPanY = transform.y;
                
                const onMove = function(e) {
                    const deltaX = e.clientX - startX;
                    const deltaY = e.clientY - startY;
                    self.panzoomInstance.pan(startPanX + deltaX, startPanY + deltaY, { relative: false });
                };
                
                const onUp = function() {
                    canvas.style.cursor = 'default';
                    document.removeEventListener('mousemove', onMove);
                    document.removeEventListener('mouseup', onUp);
                };
                
                document.addEventListener('mousemove', onMove);
                document.addEventListener('mouseup', onUp);
            }
        });
    },
    
    // Setup keyboard shortcuts
    setupKeyboard: function() {
        const self = this;
        
        $(document).on('keydown', function(e) {
            if (e.key === 'Delete' && self.selectedBooths.length > 0) {
                self.selectedBooths.forEach(function(booth) {
                    booth.remove();
                });
                self.selectedBooths = [];
                self.updateInfoToolbar(null);
                self.saveState();
            }
        });
    },
    
    // Save state for undo/redo
    saveState: function() {
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        const booths = canvas.querySelectorAll('.dropped-booth');
        const state = [];
        
        booths.forEach(function(booth) {
            state.push({
                id: booth.getAttribute('data-booth-id'),
                x: parseInt(booth.style.left),
                y: parseInt(booth.style.top)
            });
        });
        
        this.history = this.history.slice(0, this.historyIndex + 1);
        this.history.push(state);
        this.historyIndex = this.history.length - 1;
        
        // Limit history size
        if (this.history.length > 50) {
            this.history.shift();
            this.historyIndex--;
        }
    },
    
    // Undo
    undo: function() {
        if (this.historyIndex > 0) {
            this.historyIndex--;
            this.restoreState(this.history[this.historyIndex]);
        }
    },
    
    // Redo
    redo: function() {
        if (this.historyIndex < this.history.length - 1) {
            this.historyIndex++;
            this.restoreState(this.history[this.historyIndex]);
        }
    },
    
    // Restore state
    restoreState: function(state) {
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        const booths = canvas.querySelectorAll('.dropped-booth');
        booths.forEach(function(booth) {
            const boothId = booth.getAttribute('data-booth-id');
            const savedState = state.find(function(s) { return s.id === boothId; });
            if (savedState) {
                booth.style.left = savedState.x + 'px';
                booth.style.top = savedState.y + 'px';
            }
        });
    }
};

// Global error handler
window.addEventListener('error', function(e) {
    // #region agent log
    fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:globalError','message':'JavaScript Error','data':{'message':e.message,'filename':e.filename,'lineno':e.lineno,'colno':e.colno,'error':e.error ? e.error.toString() : 'null'},timestamp:Date.now(),sessionId:'debug-session','runId':'run1','hypothesisId':'E'})}).catch(function(){});
    // #endregion
    console.error('JavaScript Error:', e);
});

// Initialize when document is ready
$(document).ready(function() {
    // #region agent log
    fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:documentReady','message':'Document ready - starting initialization','data':{'jQueryLoaded':typeof $ !== 'undefined','FloorPlanDesignerDefined':typeof FloorPlanDesigner !== 'undefined'},timestamp:Date.now(),sessionId:'debug-session','runId':'run1','hypothesisId':'E'})}).catch(function(){});
    // #endregion
    
    // Setup Booth Statistics expand/collapse functionality
    const boothStatsCard = $('#boothStatisticsCard');
    const boothStatsHeader = $('#boothStatisticsHeader');
    const boothStatsToggle = $('#boothStatisticsToggle');
    const boothStatsIcon = $('#boothStatisticsIcon');
    
    if (boothStatsCard.length) {
        // Calculate navbar height dynamically
        const navbar = document.querySelector('.navbar');
        const navbarHeight = navbar ? navbar.offsetHeight : 56;
        boothStatsHeader.css('top', navbarHeight + 'px');
        
        // Update on window resize (in case navbar height changes)
        $(window).on('resize', function() {
            const newNavbarHeight = navbar ? navbar.offsetHeight : 56;
            boothStatsHeader.css('top', newNavbarHeight + 'px');
        });
        
        // Load saved state from localStorage
        const isCollapsed = localStorage.getItem('boothStatisticsCollapsed') === 'true';
        if (isCollapsed) {
            boothStatsCard.addClass('collapsed');
        }
        
        // Toggle function
        function toggleBoothStatistics() {
            boothStatsCard.toggleClass('collapsed');
            const isNowCollapsed = boothStatsCard.hasClass('collapsed');
            localStorage.setItem('boothStatisticsCollapsed', isNowCollapsed);
        }
        
        // Click handlers
        boothStatsHeader.on('click', function(e) {
            // Don't toggle if clicking the button itself (it has its own handler)
            if (!$(e.target).closest('#boothStatisticsToggle').length) {
                toggleBoothStatistics();
            }
        });
        
        boothStatsToggle.on('click', function(e) {
            e.stopPropagation(); // Prevent header click
            toggleBoothStatistics();
        });
    }
    
    // Ensure info toolbar is always visible
    const infoToolbar = document.getElementById('infoToolbar');
    if (infoToolbar) {
        infoToolbar.style.display = 'flex';
        infoToolbar.style.visibility = 'visible';
        infoToolbar.style.opacity = '1';
    }
    
    try {
        FloorPlanDesigner.init();
    } catch (error) {
        // #region agent log
        fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:documentReady','message':'ERROR: FloorPlanDesigner.init() failed','data':{'error':error.message,'stack':error.stack},timestamp:Date.now(),sessionId:'debug-session','runId':'run1','hypothesisId':'E'})}).catch(function(){});
        // #endregion
        console.error('Error initializing FloorPlanDesigner:', error);
        alert('Error initializing Floor Plan Designer: ' + error.message);
    }
    
    // Periodic check to ensure toolbar stays visible
    setInterval(function() {
        const toolbar = document.getElementById('infoToolbar');
        if (toolbar) {
            if (toolbar.style.display === 'none' || toolbar.style.visibility === 'hidden') {
                toolbar.style.display = 'flex';
                toolbar.style.visibility = 'visible';
                toolbar.style.opacity = '1';
            }
        }
    }, 1000);
    
    // Undo/Redo keyboard shortcuts
    $(document).on('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            if (e.key === 'z' && !e.shiftKey) {
                e.preventDefault();
                FloorPlanDesigner.undo();
            } else if ((e.key === 'y') || (e.key === 'z' && e.shiftKey)) {
                e.preventDefault();
                FloorPlanDesigner.redo();
            }
        }
    });
    
    $('#btnUndo').on('click', function() {
        FloorPlanDesigner.undo();
    });
    
    $('#btnRedo').on('click', function() {
        FloorPlanDesigner.redo();
    });
    
    $('#btnDelete').on('click', function() {
        if (FloorPlanDesigner.selectedBooths.length > 0) {
            FloorPlanDesigner.selectedBooths.forEach(function(booth) {
                booth.remove();
            });
            FloorPlanDesigner.selectedBooths = [];
            FloorPlanDesigner.saveState();
        }
    });
    
    console.log('✅ Floor Plan Designer ready!');
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\KHB\khbevents\kmall\kmallxmas-laravel\resources\views/booths/index.blade.php ENDPATH**/ ?>