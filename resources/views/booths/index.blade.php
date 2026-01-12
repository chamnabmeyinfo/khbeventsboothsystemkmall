@extends('layouts.app')

@section('title', 'Floor Plan Management')

@push('styles')
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

/* Tool Groups */
.tool-group {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px;
    background: rgba(255,255,255,0.03);
    border-radius: 6px;
    border: 1px solid rgba(255,255,255,0.05);
}

.tool-label {
    font-size: 10px;
    font-weight: 500;
    margin-left: 2px;
}

/* Tool-specific cursors */
.floorplan-canvas.tool-select {
    cursor: default;
}

.floorplan-canvas.tool-pan {
    cursor: grab;
}

.floorplan-canvas.tool-pan:active {
    cursor: grabbing;
}

.floorplan-canvas.tool-zoom {
    cursor: zoom-in;
}

.floorplan-canvas.tool-zoom.zooming-out {
    cursor: zoom-out;
}

.floorplan-canvas.tool-align {
    cursor: crosshair;
}

.floorplan-canvas.tool-distribute {
    cursor: move;
}

.floorplan-canvas.tool-measure {
    cursor: crosshair;
}

/* Measure tool overlay */
.measure-line {
    position: absolute;
    pointer-events: none;
    z-index: 400;
    stroke: #00ff00;
    stroke-width: 2;
    stroke-dasharray: 5,5;
}

.measure-label {
    position: absolute;
    background: rgba(0, 255, 0, 0.9);
    color: #000;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    pointer-events: none;
    z-index: 401;
    white-space: nowrap;
}

/* Dropdown styles */
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown[data-toggle] {
    position: relative;
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
    position: relative;
    width: 100%;
    flex-direction: row; /* Side by side layout */
    align-items: stretch; /* Stretch both sidebar and canvas to same height */
}

/* Sidebar - 25% width, flex layout */
.designer-sidebar {
    width: 25%; /* 25% of parent container */
    flex: 0 0 25%; /* Don't grow or shrink, fixed at 25% */
    height: 100%; /* Full height of parent */
    max-height: 100%;
    background: rgba(255, 255, 255, 0.1); /* 10% transparent background */
    backdrop-filter: blur(10px); /* Add blur effect for better readability */
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(222, 226, 230, 0.3);
    border-right: 1px solid rgba(222, 226, 230, 0.3);
    border-left: none;
    border-radius: 0 8px 8px 0;
    box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    position: relative; /* Relative positioning within flex container */
    z-index: 1000 !important; /* Always on top of canvas content */
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease, width 0.3s ease;
    transform: translateX(0);
    opacity: 1;
    pointer-events: auto; /* Ensure sidebar is clickable */
    visibility: visible !important; /* Ensure sidebar is visible */
    overflow-y: auto; /* Allow scrolling if content overflows */
    overflow-x: hidden;
}

.designer-sidebar.hidden {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    width: 0 !important;
    flex: 0 0 0 !important;
    pointer-events: none !important;
    overflow: hidden !important;
}

/* Force show when not hidden - override any conflicting styles */
.designer-sidebar:not(.hidden):not(.collapsed) {
    display: flex !important;
    visibility: visible !important;
    opacity: 1 !important;
    transform: translateX(0) !important;
    width: 25% !important;
    flex: 0 0 25% !important;
    pointer-events: auto !important;
}

/* When sidebar is hidden, canvas takes full width */
.designer-sidebar.hidden + .canvas-container {
    flex: 0 0 100% !important;
    width: 100% !important;
}

.designer-sidebar.collapsed {
    width: 50px !important;
    flex: 0 0 50px !important;
    min-width: 50px !important;
    max-width: 50px !important;
    overflow: visible !important;
}

.designer-sidebar.collapsed .sidebar-content {
    display: none !important;
    opacity: 0;
    pointer-events: none;
    visibility: hidden;
}

.designer-sidebar.collapsed .sidebar-header {
    width: 100% !important;
    min-width: 50px !important;
    max-width: 50px !important;
    padding: 12px 8px;
    justify-content: center;
    position: relative;
}

.designer-sidebar.collapsed .sidebar-header h6 {
    display: none !important;
}

.designer-sidebar.collapsed .sidebar-header-actions {
    display: none !important;
}

.designer-sidebar.collapsed .sidebar-header::after {
    content: '\f054'; /* FontAwesome chevron-right */
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-size: 18px;
    color: white;
    display: flex !important;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    cursor: pointer;
    position: absolute;
    left: 0;
    top: 0;
    z-index: 10;
}

/* When sidebar is collapsed, canvas takes remaining width */
.designer-sidebar.collapsed + .canvas-container {
    flex: 0 0 calc(100% - 50px) !important;
    width: calc(100% - 50px) !important;
}

/* Zone sections styling for transparent sidebar */
.zone-section {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(224, 224, 224, 0.2);
}

.zone-header {
    background: rgba(102, 126, 234, 0.7) !important;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    cursor: pointer;
}

.zone-header-left {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}

.btn-add-all-zone {
    background: rgba(40, 167, 69, 0.8);
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.btn-add-all-zone:hover {
    background: rgba(40, 167, 69, 1);
    transform: scale(1.05);
}

.btn-add-all-zone:active {
    transform: scale(0.95);
}

.btn-add-all-zone i {
    font-size: 10px;
}

.btn-add-all-zone-click {
    background: rgba(0, 123, 255, 0.8);
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 10px;
    cursor: pointer;
    margin-left: 4px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-add-all-zone-click:hover {
    background: rgba(0, 123, 255, 1);
    transform: scale(1.05);
}

.btn-add-all-zone-click:active {
    transform: scale(0.95);
}

.btn-add-all-zone-click.active {
    background: rgba(255, 193, 7, 0.9);
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.btn-add-all-zone-click i {
    font-size: 10px;
}

.btn-zone-clear {
    background: rgba(220, 53, 69, 0.8);
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 10px;
    cursor: pointer;
    margin-left: 4px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-zone-clear:hover {
    background: rgba(220, 53, 69, 1);
    transform: scale(1.05);
}

.btn-zone-clear:active {
    transform: scale(0.95);
}

.btn-zone-clear i {
    font-size: 10px;
}

.btn-zone-zoom {
    background: rgba(0, 123, 255, 0.8);
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 10px;
    cursor: pointer;
    margin-left: 4px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-zone-zoom:hover {
    background: rgba(0, 123, 255, 1);
    transform: scale(1.05);
}

.btn-zone-zoom:active {
    transform: scale(0.95);
}

.btn-zone-zoom i {
    font-size: 10px;
}

.btn-zone-add-new {
    background: rgba(40, 167, 69, 0.8);
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 10px;
    cursor: pointer;
    margin-left: 4px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-zone-add-new:hover {
    background: rgba(40, 167, 69, 1);
    transform: scale(1.05);
}

.btn-zone-add-new:active {
    transform: scale(0.95);
}

.btn-zone-add-new i {
    font-size: 10px;
}

.btn-zone-delete {
    background: rgba(220, 53, 69, 0.8);
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 10px;
    cursor: pointer;
    margin-left: 4px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-zone-delete:hover {
    background: rgba(220, 53, 69, 1);
    transform: scale(1.05);
}

.btn-zone-delete:active {
    transform: scale(0.95);
}

.btn-zone-delete i {
    font-size: 10px;
}

.booth-number-item {
    background: rgba(255, 255, 255, 0.9) !important;
    border-color: rgba(170, 170, 170, 0.6) !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15) !important;
}

/* Responsive adjustments for sidebar */
@media (max-width: 768px) {
    .designer-sidebar {
        width: 250px;
        left: 0; /* Stick to left edge */
        top: 0; /* Stick to top of canvas container */
        height: calc(100vh - 120px);
        max-height: calc(100vh - 120px);
    }
    
    .designer-sidebar.collapsed {
        width: 50px !important;
        flex: 0 0 50px !important;
    }
    
    .designer-sidebar.collapsed + .canvas-container {
        flex: 0 0 calc(100% - 50px) !important;
        width: calc(100% - 50px) !important;
    }
    
    .btn-toggle-stock span {
        display: none;
    }
    
    .btn-toggle-stock {
        padding: 4px 6px;
    }
}

.sidebar-header {
    background: rgba(102, 126, 234, 0.85); /* Semi-transparent gradient background */
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    color: white;
    padding: 12px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    border-radius: 0 8px 0 0;
    transition: opacity 0.3s ease;
    flex-shrink: 0;
    gap: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.sidebar-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-toggle-stock {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.btn-toggle-stock:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
}

.btn-toggle-stock.active {
    background: rgba(255, 255, 255, 0.9);
    color: #667eea;
    border-color: rgba(255, 255, 255, 0.9);
}

.btn-toggle-stock i {
    font-size: 14px;
}

/* Hide booths that are not in stock when filter is active */
.booth-number-item.hide-not-in-stock {
    display: none !important;
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
    transition: opacity 0.3s ease;
    opacity: 1;
    background: rgba(255, 255, 255, 0.05); /* Slightly more opaque for content area */
}

.sidebar-search input {
    border-radius: 5px;
    border: 1px solid #ced4da;
}

/* Canvas Container - 75% width */
.canvas-container {
    flex: 0 0 75%; /* 75% of parent container, don't grow or shrink */
    width: 75%; /* 75% width */
    position: relative;
    overflow: hidden; /* No scrollbars - use panning instead */
    height: 100%;
    display: flex;
    align-items: flex-start;
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


.canvas-container {
    background: #e9ecef;
    display: flex; /* Use flex to position sidebar */
    align-items: flex-start; /* Align sidebar to top */
    /* Ensure container fits the viewport */
    min-width: 0;
    min-height: 0;
    width: 100%;
    height: 100%;
    position: relative; /* Ensure z-index stacking context */
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
    flex-grow: 1; /* Allow canvas to grow and fill remaining space */
    /* Ensure background image fills entire canvas */
    background-attachment: local;
    object-fit: fill;
    /* Position canvas at center initially */
    margin: 0;
    display: block;
    z-index: 1; /* Base layer for floorplan image */
    /* Add margin to account for sidebar when not collapsed */
    margin-left: 0; /* Sidebar is sticky, so no margin needed - it overlays */
}



.floorplan-canvas.drag-over {
    border: 3px dashed #007bff !important;
    background: rgba(0, 123, 255, 0.08) !important;
}

/* Booth Items in Sidebar */
#boothNumbersContainer {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* Zone Sections */
.zone-section {
    margin-bottom: 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    overflow: hidden;
    background: #f8f9fa;
}

.zone-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 10px 12px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 13px;
    user-select: none;
    transition: background 0.2s ease;
}

.zone-header:hover {
    background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
}

.zone-header .zone-chevron {
    font-size: 11px;
    transition: transform 0.3s ease;
    width: 16px;
    text-align: center;
}

.zone-section.collapsed .zone-chevron {
    transform: rotate(-90deg);
}

.zone-name {
    flex: 1;
    font-weight: 600;
}

.zone-count {
    font-size: 11px;
    opacity: 0.9;
    background: rgba(255, 255, 255, 0.2);
    padding: 2px 8px;
    border-radius: 12px;
}

.zone-content {
    padding: 8px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    max-height: 500px;
    overflow-y: auto;
    transition: max-height 0.3s ease, padding 0.3s ease;
}

.zone-section.collapsed .zone-content {
    max-height: 0;
    padding: 0 8px;
    overflow: hidden;
}

.zone-content::-webkit-scrollbar {
    width: 6px;
}

.zone-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.zone-content::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.zone-content::-webkit-scrollbar-thumb:hover {
    background: #555;
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

.booth-number-item.selected {
    background: #667eea !important;
    color: #fff !important;
    border-color: #5568d3 !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3) !important;
    transform: scale(1.05);
}

.booth-number-item.selected:hover {
    background: #5568d3 !important;
    transform: scale(1.08);
}

/* Dropped Booths on Canvas */
.dropped-booth {
    position: absolute;
    background: #fff;
    border: 2px solid #007bff;
    border-radius: 6px;
    padding: 0;
    cursor: move;
    font-weight: bold;
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

/* Context menu for booths (right-click) */
.booth-context-menu {
    position: fixed;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 10000;
    min-width: 180px;
    padding: 4px 0;
    font-size: 14px;
}

.context-menu-item {
    padding: 8px 16px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #333;
    transition: background-color 0.2s;
}

.context-menu-item:hover {
    background-color: #f0f0f0;
}

.context-menu-item i {
    width: 18px;
    text-align: center;
    color: #007bff;
}

/* Locked booth styles */
.dropped-booth.locked {
    cursor: not-allowed !important;
    opacity: 0.7;
    position: relative;
}

.dropped-booth.locked::before {
    content: '\f023'; /* Font Awesome lock icon */
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    top: 2px;
    left: 2px;
    background: rgba(108, 117, 125, 0.9);
    color: white;
    width: 18px;
    height: 18px;
    border-radius: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    z-index: 1001;
    pointer-events: none;
}

.dropped-booth.locked .resize-handle,
.dropped-booth.locked .rotate-handle {
    display: none !important;
    pointer-events: none !important;
}

/* Flash/Blink animation for booth highlighting */
@keyframes boothFlash {
    0%, 100% {
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        transform: scale(1);
        border-width: 2px;
    }
    25% {
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.8), 0 0 40px rgba(255, 193, 7, 0.6);
        transform: scale(1.05);
        border-width: 4px;
    }
    50% {
        box-shadow: 0 0 30px rgba(255, 193, 7, 1), 0 0 60px rgba(255, 193, 7, 0.8);
        transform: scale(1.1);
        border-width: 5px;
    }
    75% {
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.8), 0 0 40px rgba(255, 193, 7, 0.6);
        transform: scale(1.05);
        border-width: 4px;
    }
}

.dropped-booth.flashing {
    animation: boothFlash 0.6s ease-in-out 3;
    border-color: #ffc107 !important;
    z-index: 2000 !important;
}

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
    /* Size, position, and border width will be set dynamically via JavaScript based on booth dimensions */
    /* Default fallback values (will be overridden by JavaScript) */
    width: 12px;
    height: 12px;
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
    /* Size, position, and border width will be set dynamically via JavaScript based on booth dimensions */
    /* Default fallback values (will be overridden by JavaScript) */
    width: 20px;
    height: 20px;
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
    pointer-events: none; /* Allow clicks to pass through when not active */
}

.properties-panel-backdrop.active {
    display: block;
    opacity: 1;
    pointer-events: auto; /* Block clicks when active (to close panel on backdrop click) */
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


/* Zoom Selection Box (Photoshop-like zoom tool) */
.zoom-selection {
    position: absolute;
    border: 2px dashed #ffc107;
    background: rgba(255, 193, 7, 0.15);
    pointer-events: none;
    z-index: 600;
    display: none;
    box-sizing: border-box;
}

.zoom-selection.active {
    display: block;
}

/* Booth Settings Modal - Color Picker Styling */
.form-control-color {
    width: 60px;
    height: 38px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    cursor: pointer;
    padding: 2px;
}

.input-group .form-control-color {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: none;
}

.input-group .form-control:not(:first-child) {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.input-group {
    display: flex;
    align-items: stretch;
}

.input-group .form-control {
    flex: 1;
}

/* Grid Overlay - Stacked on top of floorplan image */
.grid-overlay {
    position: absolute;
    top: 0;
    left: 0;
    /* Width and height are set dynamically in JavaScript to match canvas size */
    background-image: 
        linear-gradient(rgba(0, 0, 0, 0.08) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 0, 0, 0.08) 1px, transparent 1px);
    background-size: 10px 10px; /* Smaller grid cells (10px x 10px) */
    pointer-events: none;
    z-index: 5; /* Above floorplan image (z-index: 1) but below booths (z-index: 10+) */
    display: none;
    opacity: 0.6; /* Make grid more visible */
    background-attachment: local; /* Ensure grid scrolls with canvas */
}

.grid-overlay.visible {
    display: block;
}

/* Canvas Center Marker */
.center-marker {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 0;
    height: 0;
    pointer-events: none;
    z-index: 6; /* Above grid overlay */
    display: none;
}
.center-marker.visible {
    display: block;
}
.center-marker::before,
.center-marker::after {
    content: '';
    position: absolute;
    background: rgba(255, 0, 0, 0.8); /* red center lines */
}
/* Horizontal line */
.center-marker::before {
    left: -40px;
    right: -40px;
    top: 0;
    height: 2px;
}
/* Vertical line */
.center-marker::after {
    top: -40px;
    bottom: -40px;
    left: 0;
    width: 2px;
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

/* Error Log Styles */
.error-log-item {
    background: #f8f9fa;
    border-left: 3px solid #ff4444;
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 4px;
    font-size: 12px;
}

.error-log-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.error-log-type {
    background: #ff4444;
    color: white;
    padding: 2px 8px;
    border-radius: 3px;
    font-weight: bold;
    font-size: 10px;
}

.error-log-time {
    color: #666;
    font-size: 10px;
}

.error-log-message {
    color: #333;
    font-weight: 600;
    margin-bottom: 4px;
    word-break: break-word;
}

.error-log-context {
    color: #666;
    font-size: 11px;
    margin-bottom: 4px;
}

.error-log-location {
    color: #007bff;
    font-size: 10px;
    font-family: 'Courier New', monospace;
}
</style>
@endpush

@section('content')
<div class="container-fluid mt-2 mb-2">
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
                    <a href="{{ route('floor-plans.index') }}" class="btn btn-sm btn-info" title="Manage Floor Plans">
                        <i class="fas fa-cog mr-1"></i>Manage
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
                                        <button class="btn-add-selected-zone" 
                                                data-zone="{{ $zoneName }}" 
                                                title="Add Selected Booths in Zone {{ $zoneName }} to Canvas (Stick Together)"
                                                onclick="event.stopPropagation(); FloorPlanDesigner.addSelectedZoneBoothsToCanvas('{{ $zoneName }}');">
                                            <i class="fas fa-layer-group"></i> Add Selected
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
                     style="@if(isset($currentFloorPlan) && $currentFloorPlan && $currentFloorPlan->floor_image)
                     background-image: url('{{ asset($currentFloorPlan->floor_image) }}'); background-size: 100% 100%; background-repeat: no-repeat; background-position: top left; background-attachment: local;
                     @elseif(file_exists(public_path('images/map.jpg')))
                     background-image: url('{{ asset('images/map.jpg') }}'); background-size: 100% 100%; background-repeat: no-repeat; background-position: top left; background-attachment: local;
                     @else
                     background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                     @endif">
                    @if(isset($currentFloorPlan) && $currentFloorPlan && $currentFloorPlan->floor_image)
                        <img src="{{ asset($currentFloorPlan->floor_image) }}" 
                             id="floorplanImageElement"
                             alt="Floor Plan Map"
                             style="display: none;"/>
                    @elseif(file_exists(public_path('images/map.jpg')))
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
                            <a class="nav-link active" id="delete-all-tab" data-toggle="tab" href="#delete-all" role="tab">
                                <i class="fas fa-trash-alt"></i> Delete All
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="delete-specific-tab" data-toggle="tab" href="#delete-specific" role="tab">
                                <i class="fas fa-list"></i> Delete Specific
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="delete-range-tab" data-toggle="tab" href="#delete-range" role="tab">
                                <i class="fas fa-arrows-alt-h"></i> Delete Range
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
                            <option value="2">Confirmed</option>
                            <option value="3">Reserved</option>
                            <option value="5">Paid</option>
                        </select>
                        <small class="form-text text-muted">
                            <strong>Confirmed:</strong> Client has confirmed the booking<br>
                            <strong>Reserved:</strong> Booth is reserved but not yet confirmed<br>
                            <strong>Paid:</strong> Payment has been received
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
</div>
@endsection

@push('scripts')
<!-- html2canvas for PNG export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
// ============================================================================
// ERROR LOGGING SYSTEM
// ============================================================================
var ErrorLogger = (function() {
    'use strict';
    
    var config = {
        enabled: true, // Set to false in production
        maxLogs: 100,
        storageKey: 'floorplan_error_logs',
        showNotifications: true,
        logToConsole: true,
        logToServer: false, // Set to true to send errors to server
        serverEndpoint: '/api/log-error'
    };
    
    var errorLogs = [];
    var errorCount = 0;
    
    // Initialize from localStorage
    function init() {
        try {
            var stored = localStorage.getItem(config.storageKey);
            if (stored) {
                errorLogs = JSON.parse(stored);
                errorCount = errorLogs.length;
            }
        } catch (e) {
            console.warn('Failed to load error logs from storage:', e);
        }
        
        // Update UI if error panel exists
        updateErrorPanel();
    }
    
    // ============================================================================
    // INTELLIGENT ERROR ANALYSIS
    // ============================================================================
    
    // Analyze error and provide insights
    function analyzeError(error, context) {
        var analysis = {
            category: 'Unknown',
            relatedTo: [],
            suggestedFix: 'Check the console for more details.',
            severity: 'medium',
            affectedFeatures: []
        };
        
        var message = (error.message || String(error)).toLowerCase();
        var stack = (error.stack || '').toLowerCase();
        var filename = (error.filename || '').toLowerCase();
        
        // Syntax Errors
        if (message.includes('syntax') || message.includes('unexpected token') || message.includes('unexpected end')) {
            analysis.category = 'Syntax Error';
            analysis.severity = 'critical';
            analysis.relatedTo = ['JavaScript Code', 'Template Rendering'];
            analysis.suggestedFix = '🔧 Fix: Check for unclosed brackets, quotes, or template literal issues. Look for Blade syntax conflicts (use @{{ }} for literal braces).';
            analysis.affectedFeatures = ['All Features - Page Cannot Load'];
        }
        
        // Reference Errors - Undefined variables/functions
        else if (message.includes('is not defined') || message.includes('undefined')) {
            var varName = extractVariableName(message);
            analysis.category = 'Reference Error';
            analysis.severity = 'high';
            analysis.relatedTo = ['Variable/Function: ' + varName, 'Initialization Order'];
            
            if (varName.includes('floorplan') || varName.includes('designer')) {
                analysis.suggestedFix = '🔧 Fix: Ensure FloorPlanDesigner is initialized before use. Check if the script loaded correctly.';
                analysis.affectedFeatures = ['Floor Plan Designer'];
            } else if (varName.includes('swal')) {
                analysis.suggestedFix = '🔧 Fix: SweetAlert2 library not loaded. Check if the CDN link is working or add the SweetAlert2 script tag to your page.';
                analysis.affectedFeatures = ['Notifications', 'Alerts'];
            } else if (varName.includes('$') || varName.includes('jquery')) {
                analysis.suggestedFix = '🔧 Fix: jQuery not loaded. Ensure jQuery is included before other scripts.';
                analysis.affectedFeatures = ['AJAX', 'DOM Manipulation'];
            } else {
                analysis.suggestedFix = '🔧 Fix: Variable "' + varName + '" is not defined. Check spelling, scope, or ensure it\'s initialized before use.';
            }
        }
        
        // Type Errors - Wrong data type operations
        else if (message.includes('is not a function') || message.includes('cannot read') || message.includes('null')) {
            analysis.category = 'Type Error';
            analysis.severity = 'high';
            analysis.relatedTo = ['Data Type Mismatch', 'Null/Undefined Value'];
            
            if (message.includes('cannot read') && message.includes('null')) {
                var property = extractPropertyName(message);
                analysis.suggestedFix = '🔧 Fix: Trying to access property "' + property + '" on null/undefined. Add null check: if (obj && obj.' + property + ') { ... }';
                analysis.affectedFeatures = ['DOM Element Access', 'Data Processing'];
            } else if (message.includes('is not a function')) {
                var funcName = extractFunctionName(message);
                analysis.suggestedFix = '🔧 Fix: "' + funcName + '" is not a function. Check if the object/method exists and is spelled correctly.';
                analysis.affectedFeatures = ['Function Calls'];
            }
        }
        
        // AJAX Errors
        else if (context === 'AJAX Request Failed' || message.includes('ajax') || message.includes('fetch')) {
            analysis.category = 'AJAX/Network Error';
            analysis.severity = 'medium';
            analysis.relatedTo = ['Server Communication', 'API Endpoint'];
            
            if (message.includes('404')) {
                analysis.suggestedFix = '🔧 Fix: API endpoint not found (404). Check the URL and ensure the route exists on the server.';
                analysis.affectedFeatures = ['Data Loading', 'Saving'];
            } else if (message.includes('500')) {
                analysis.suggestedFix = '🔧 Fix: Server error (500). Check server logs for PHP/Laravel errors. Verify database connection.';
                analysis.affectedFeatures = ['Server-side Processing'];
            } else if (message.includes('403') || message.includes('unauthorized')) {
                analysis.suggestedFix = '🔧 Fix: Authorization error. Check CSRF token, user permissions, or session timeout.';
                analysis.affectedFeatures = ['Authentication', 'Authorization'];
            } else if (message.includes('cors')) {
                analysis.suggestedFix = '🔧 Fix: CORS error. Configure server to allow cross-origin requests or check API endpoint.';
                analysis.affectedFeatures = ['Cross-Origin Requests'];
            } else {
                analysis.suggestedFix = '🔧 Fix: Network request failed. Check internet connection, server status, and API endpoint.';
                analysis.affectedFeatures = ['Data Synchronization'];
            }
        }
        
        // Promise Rejections
        else if (context === 'Unhandled Promise Rejection') {
            analysis.category = 'Promise Rejection';
            analysis.severity = 'medium';
            analysis.relatedTo = ['Async Operations', 'Error Handling'];
            analysis.suggestedFix = '🔧 Fix: Add .catch() handler to the promise or use try-catch with async/await. Check the async operation for errors.';
            analysis.affectedFeatures = ['Async Operations'];
        }
        
        // DOM Errors
        else if (message.includes('element') || message.includes('node') || message.includes('dom')) {
            analysis.category = 'DOM Error';
            analysis.severity = 'medium';
            analysis.relatedTo = ['DOM Manipulation', 'Element Selection'];
            analysis.suggestedFix = '🔧 Fix: DOM element not found or invalid. Ensure element exists before manipulation. Use document.getElementById() or querySelector() with null checks.';
            analysis.affectedFeatures = ['UI Rendering', 'Element Manipulation'];
        }
        
        // Canvas/Rendering Errors
        else if (message.includes('canvas') || message.includes('render') || message.includes('draw')) {
            analysis.category = 'Rendering Error';
            analysis.severity = 'medium';
            analysis.relatedTo = ['Canvas Rendering', 'Graphics'];
            analysis.suggestedFix = '🔧 Fix: Canvas rendering issue. Check if canvas element exists, has valid dimensions, and context is properly initialized.';
            analysis.affectedFeatures = ['Floor Plan Canvas', 'Visual Rendering'];
        }
        
        // Booth-specific errors
        else if (message.includes('booth') || stack.includes('booth')) {
            analysis.category = 'Booth Operation Error';
            analysis.severity = 'medium';
            analysis.relatedTo = ['Booth Management', 'Floor Plan Designer'];
            analysis.suggestedFix = '🔧 Fix: Booth operation failed. Check booth data attributes, ensure booth element exists, and verify booth ID is valid.';
            analysis.affectedFeatures = ['Booth Placement', 'Booth Editing', 'Booth Deletion'];
        }
        
        // Panzoom errors
        else if (message.includes('panzoom') || stack.includes('panzoom')) {
            analysis.category = 'Zoom/Pan Error';
            analysis.severity = 'low';
            analysis.relatedTo = ['Canvas Navigation', 'Panzoom Library'];
            analysis.suggestedFix = '🔧 Fix: Panzoom operation failed. Ensure panzoom is initialized and canvas element is valid.';
            analysis.affectedFeatures = ['Canvas Zoom', 'Canvas Pan'];
        }
        
        // Storage errors
        else if (message.includes('localstorage') || message.includes('storage')) {
            analysis.category = 'Storage Error';
            analysis.severity = 'low';
            analysis.relatedTo = ['Local Storage', 'Data Persistence'];
            analysis.suggestedFix = '🔧 Fix: Storage quota exceeded or disabled. Clear localStorage or enable it in browser settings.';
            analysis.affectedFeatures = ['Settings Persistence', 'Error Log Storage'];
        }
        
        return analysis;
    }
    
    // Helper: Extract variable name from error message
    function extractVariableName(message) {
        var match = message.match(/['"]?(\w+)['"]?\s+is not defined/i);
        if (match) return match[1];
        match = message.match(/(\w+)\s+is undefined/i);
        if (match) return match[1];
        return 'unknown';
    }
    
    // Helper: Extract property name from error message
    function extractPropertyName(message) {
        var match = message.match(/property ['"]?(\w+)['"]?/i);
        if (match) return match[1];
        match = message.match(/of ['"]?(\w+)['"]?/i);
        if (match) return match[1];
        return 'unknown';
    }
    
    // Helper: Extract function name from error message
    function extractFunctionName(message) {
        var match = message.match(/['"]?(\w+)['"]?\s+is not a function/i);
        if (match) return match[1];
        return 'unknown';
    }
    
    // ============================================================================
    
    // Log error
    function logError(error, context) {
        if (!config.enabled) return;
        
        // Analyze error
        var analysis = analyzeError(error, context);
        
        var errorEntry = {
            id: Date.now() + '_' + Math.random().toString(36).substr(2, 9),
            timestamp: new Date().toISOString(),
            message: error.message || String(error),
            stack: error.stack || '',
            type: error.name || 'Error',
            context: context || 'Unknown',
            url: window.location.href,
            userAgent: navigator.userAgent,
            line: error.lineno || null,
            column: error.colno || null,
            filename: error.filename || null,
            // Add analysis data
            category: analysis.category,
            relatedTo: analysis.relatedTo,
            suggestedFix: analysis.suggestedFix,
            severity: analysis.severity,
            affectedFeatures: analysis.affectedFeatures
        };
        
        errorLogs.push(errorEntry);
        errorCount++;
        
        // Limit stored logs
        if (errorLogs.length > config.maxLogs) {
            errorLogs.shift();
        }
        
        // Save to localStorage
        try {
            localStorage.setItem(config.storageKey, JSON.stringify(errorLogs));
        } catch (e) {
            console.warn('Failed to save error logs to storage:', e);
        }
        
        // Log to console with analysis
        if (config.logToConsole) {
            var severityEmoji = errorEntry.severity === 'critical' ? '🔴' : 
                               errorEntry.severity === 'high' ? '🟠' : 
                               errorEntry.severity === 'medium' ? '🟡' : '🟢';
            
            console.group('%c' + severityEmoji + ' Error Logged - ' + errorEntry.category, 'color: #ff4444; font-weight: bold; font-size: 13px;');
            console.error('Message:', errorEntry.message);
            console.log('%c📍 Context:', 'font-weight: bold;', errorEntry.context);
            console.log('%c⏰ Timestamp:', 'font-weight: bold;', errorEntry.timestamp);
            
            if (errorEntry.relatedTo && errorEntry.relatedTo.length > 0) {
                console.log('%c🔗 Related To:', 'font-weight: bold; color: #1976d2;', errorEntry.relatedTo.join(', '));
            }
            
            if (errorEntry.affectedFeatures && errorEntry.affectedFeatures.length > 0) {
                console.log('%c⚠️ Affected Features:', 'font-weight: bold; color: #f57c00;', errorEntry.affectedFeatures.join(', '));
            }
            
            if (errorEntry.suggestedFix) {
                console.log('%c💡 Suggested Fix:', 'font-weight: bold; color: #4caf50;', errorEntry.suggestedFix);
            }
            
            if (errorEntry.stack) {
                console.log('%c📋 Stack Trace:', 'font-weight: bold;');
                console.log(errorEntry.stack);
            }
            
            console.groupEnd();
        }
        
        // Show notification
        if (config.showNotifications && typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error Detected',
                text: errorEntry.message,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'bottom-right'
            });
        }
        
        // Send to server
        if (config.logToServer) {
            sendToServer(errorEntry);
        }
        
        // Update UI
        updateErrorPanel();
        
        return errorEntry;
    }
    
    // Send error to server
    function sendToServer(errorEntry) {
        if (!config.serverEndpoint) return;
        
        fetch(config.serverEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify(errorEntry)
        }).catch(function(err) {
            console.warn('Failed to send error to server:', err);
        });
    }
    
    // Get all logs
    function getLogs() {
        return errorLogs.slice();
    }
    
    // Get recent logs
    function getRecentLogs(count) {
        count = count || 10;
        return errorLogs.slice(-count);
    }
    
    // Clear logs
    function clearLogs() {
        errorLogs = [];
        errorCount = 0;
        try {
            localStorage.removeItem(config.storageKey);
        } catch (e) {
            console.warn('Failed to clear error logs from storage:', e);
        }
        updateErrorPanel();
        console.log('%c✅ Error logs cleared', 'color: #00cc00; font-weight: bold;');
    }
    
    // Export logs as JSON
    function exportLogs() {
        var dataStr = JSON.stringify(errorLogs, null, 2);
        var dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);
        var exportFileDefaultName = 'error-logs-' + new Date().toISOString().split('T')[0] + '.json';
        
        var linkElement = document.createElement('a');
        linkElement.setAttribute('href', dataUri);
        linkElement.setAttribute('download', exportFileDefaultName);
        linkElement.click();
        
        console.log('%c📥 Error logs exported', 'color: #0088ff; font-weight: bold;');
    }
    
    // Update error panel UI
    function updateErrorPanel() {
        var panel = document.getElementById('errorLogPanel');
        if (!panel) return;
        
        var badge = document.getElementById('errorLogBadge');
        var list = document.getElementById('errorLogList');
        
        if (badge) {
            badge.textContent = errorCount;
            badge.style.display = errorCount > 0 ? 'inline-block' : 'none';
        }
        
        if (list) {
            if (errorLogs.length === 0) {
                list.innerHTML = '<div style="padding: 20px; text-align: center; color: #888;">No errors logged</div>';
            } else {
                var html = '';
                var recentLogs = errorLogs.slice(-20).reverse();
                recentLogs.forEach(function(log) {
                    var date = new Date(log.timestamp);
                    var timeStr = date.toLocaleTimeString();
                    
                    // Severity color coding
                    var severityColor = '#ff4444'; // default red
                    var severityIcon = 'fa-exclamation-circle';
                    if (log.severity === 'critical') {
                        severityColor = '#dc3545';
                        severityIcon = 'fa-times-circle';
                    } else if (log.severity === 'high') {
                        severityColor = '#ff6b6b';
                        severityIcon = 'fa-exclamation-triangle';
                    } else if (log.severity === 'medium') {
                        severityColor = '#ffa500';
                        severityIcon = 'fa-exclamation-circle';
                    } else if (log.severity === 'low') {
                        severityColor = '#ffc107';
                        severityIcon = 'fa-info-circle';
                    }
                    
                    html += '<div class="error-log-item" data-log-id="' + log.id + '" style="border-left-color: ' + severityColor + ';">';
                    
                    // Header with severity and time
                    html += '<div class="error-log-header">';
                    html += '<div style="display: flex; align-items: center; gap: 6px;">';
                    html += '<i class="fas ' + severityIcon + '" style="color: ' + severityColor + ';"></i>';
                    html += '<span class="error-log-type" style="background: ' + severityColor + ';">' + log.type + '</span>';
                    if (log.category && log.category !== 'Unknown') {
                        html += '<span style="background: #6c757d; color: white; padding: 2px 6px; border-radius: 3px; font-size: 9px;">' + escapeHtml(log.category) + '</span>';
                    }
                    html += '</div>';
                    html += '<span class="error-log-time">' + timeStr + '</span>';
                    html += '</div>';
                    
                    // Error message
                    html += '<div class="error-log-message">' + escapeHtml(log.message) + '</div>';
                    
                    // Context
                    html += '<div class="error-log-context">📍 Context: ' + escapeHtml(log.context) + '</div>';
                    
                    // Location
                    if (log.filename) {
                        html += '<div class="error-log-location">📄 Location: ' + escapeHtml(log.filename);
                        if (log.line) html += ':' + log.line;
                        if (log.column) html += ':' + log.column;
                        html += '</div>';
                    }
                    
                    // Related To (NEW)
                    if (log.relatedTo && log.relatedTo.length > 0) {
                        html += '<div style="margin-top: 6px; padding: 6px; background: #e3f2fd; border-radius: 3px;">';
                        html += '<div style="font-weight: 600; font-size: 10px; color: #1976d2; margin-bottom: 3px;">🔗 Related To:</div>';
                        log.relatedTo.forEach(function(item) {
                            html += '<div style="font-size: 10px; color: #1565c0; margin-left: 8px;">• ' + escapeHtml(item) + '</div>';
                        });
                        html += '</div>';
                    }
                    
                    // Affected Features (NEW)
                    if (log.affectedFeatures && log.affectedFeatures.length > 0) {
                        html += '<div style="margin-top: 6px; padding: 6px; background: #fff3e0; border-radius: 3px;">';
                        html += '<div style="font-weight: 600; font-size: 10px; color: #f57c00; margin-bottom: 3px;">⚠️ Affected Features:</div>';
                        log.affectedFeatures.forEach(function(feature) {
                            html += '<div style="font-size: 10px; color: #e65100; margin-left: 8px;">• ' + escapeHtml(feature) + '</div>';
                        });
                        html += '</div>';
                    }
                    
                    // Suggested Fix (NEW)
                    if (log.suggestedFix) {
                        html += '<div style="margin-top: 6px; padding: 8px; background: #e8f5e9; border-radius: 3px; border-left: 3px solid #4caf50;">';
                        html += '<div style="font-weight: 600; font-size: 10px; color: #2e7d32; margin-bottom: 3px;">💡 Suggested Fix:</div>';
                        html += '<div style="font-size: 11px; color: #1b5e20; line-height: 1.4;">' + escapeHtml(log.suggestedFix) + '</div>';
                        html += '</div>';
                    }
                    
                    // Expandable stack trace
                    if (log.stack) {
                        html += '<div style="margin-top: 6px;">';
                        html += '<button onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === \'none\' ? \'block\' : \'none\'; return false;" style="background: #f5f5f5; border: 1px solid #ddd; padding: 3px 8px; border-radius: 3px; font-size: 10px; cursor: pointer;">📋 Show Stack Trace</button>';
                        html += '<pre style="display: none; margin-top: 4px; padding: 6px; background: #f5f5f5; border-radius: 3px; font-size: 9px; overflow-x: auto; max-height: 150px;">' + escapeHtml(log.stack) + '</pre>';
                        html += '</div>';
                    }
                    
                    html += '</div>';
                });
                list.innerHTML = html;
            }
        }
    }
    
    // Helper: Escape HTML
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Toggle error panel
    function togglePanel() {
        var panel = document.getElementById('errorLogPanel');
        if (!panel) return;
        
        if (panel.style.display === 'none' || !panel.style.display) {
            panel.style.display = 'block';
            updateErrorPanel();
        } else {
            panel.style.display = 'none';
        }
    }
    
    // Public API
    return {
        init: init,
        log: logError,
        getLogs: getLogs,
        getRecentLogs: getRecentLogs,
        clearLogs: clearLogs,
        exportLogs: exportLogs,
        togglePanel: togglePanel,
        config: config
    };
})();

// Initialize error logger
ErrorLogger.init();

// ============================================================================
// GLOBAL ERROR HANDLERS
// ============================================================================

// Catch uncaught JavaScript errors
window.addEventListener('error', function(event) {
    ErrorLogger.log({
        message: event.message,
        filename: event.filename,
        lineno: event.lineno,
        colno: event.colno,
        stack: event.error ? event.error.stack : ''
    }, 'Uncaught Error');
    return false; // Don't prevent default error handling
});

// Catch unhandled promise rejections
window.addEventListener('unhandledrejection', function(event) {
    ErrorLogger.log({
        message: event.reason ? event.reason.message || String(event.reason) : 'Unhandled Promise Rejection',
        stack: event.reason ? event.reason.stack : ''
    }, 'Unhandled Promise Rejection');
});

// ============================================================================
// AJAX ERROR INTERCEPTOR (jQuery)
// ============================================================================
$(document).ajaxError(function(event, jqXHR, settings, thrownError) {
    var errorMsg = 'AJAX Error: ' + thrownError;
    if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
        errorMsg = jqXHR.responseJSON.message;
    } else if (jqXHR.responseText) {
        errorMsg = jqXHR.responseText.substring(0, 200);
    }
    
    ErrorLogger.log({
        message: errorMsg,
        name: 'AjaxError',
        stack: 'URL: ' + settings.url + '\nStatus: ' + jqXHR.status + '\nMethod: ' + settings.type
    }, 'AJAX Request Failed');
});

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
    centerMarkerEnabled: false, // Show/hide canvas center marker
    zoomLevel: 1,
    panzoomInstance: null,
    canvasWidth: @php echo isset($canvasWidth) && $canvasWidth ? (int)$canvasWidth : 1200; @endphp, // Floor plan canvas width or default
    canvasHeight: @php echo isset($canvasHeight) && $canvasHeight ? (int)$canvasHeight : 800; @endphp, // Floor plan canvas height or default
    canvasResolution: 300, // Default export resolution (DPI)
    isZoomSelecting: false, // Track if user is selecting area to zoom (Ctrl+Space)
    zoomSelectionStart: null, // Start position of zoom selection {x, y}
    zoomSelectionElement: null, // The selection rectangle element
    ctrlSpacePressed: false, // Track Ctrl+Space key combination
    lastMousePosition: null, // Track last mouse position for zoom focal point {x, y} in canvas coordinates
    zoomFocalPoint: null, // Track zoom focal point set by clicking while holding Space {x, y} in canvas coordinates
    uploadSizeLimit: 10, // Default upload size limit in MB (0 = no limit)
    propertiesPanelEnabled: true, // Enable/disable Properties panel auto-open
    
    // Cached DOM elements (for performance optimization)
    _cachedElements: {
        canvas: null,
        container: null,
        infoToolbar: null,
        floorplanImage: null
    },
    
    // Debounce timers
    _debounceTimers: {},
    
    // Debounce helper function
    debounce: function(key, func, delay) {
        const self = this;
        if (self._debounceTimers[key]) {
            clearTimeout(self._debounceTimers[key]);
        }
        self._debounceTimers[key] = setTimeout(func, delay || 300);
    },
    
    // Helper: Get booth data from element (optimized)
    getBoothData: function(element) {
        if (!element) return null;
        return {
            id: element.getAttribute('data-booth-id'),
            number: element.getAttribute('data-booth-number'),
            status: element.getAttribute('data-booth-status') || '1',
            clientId: element.getAttribute('data-client-id') || '',
            userId: element.getAttribute('data-user-id') || '',
            categoryId: element.getAttribute('data-category-id') || '',
            subCategoryId: element.getAttribute('data-sub-category-id') || '',
            assetId: element.getAttribute('data-asset-id') || '',
            boothTypeId: element.getAttribute('data-booth-type-id') || ''
        };
    },
    
    // Default booth settings
    defaultBoothWidth: 80,
    defaultBoothHeight: 50,
    defaultBoothRotation: 0,
    defaultBoothZIndex: 10,
    defaultBoothFontSize: 14,
    defaultBoothBorderWidth: 2,
    defaultBoothBorderRadius: 6,
    defaultBoothOpacity: 1.00,
    defaultBackgroundColor: '#ffffff',
    defaultBorderColor: '#007bff',
    defaultTextColor: '#000000',
    defaultFontWeight: '700',
    defaultFontFamily: 'Arial, sans-serif',
    defaultTextAlign: 'center',
    defaultBoxShadow: '0 2px 8px rgba(0,0,0,0.2)',
    
    // Zone settings cache
    zoneSettingsCache: {},
    zoneSettingsLoading: {},
    
    // Helper: Get cached DOM element (with lazy initialization)
    getElement: function(id) {
        const self = this;
        const cacheMap = {
            'print': 'canvas',
            'printContainer': 'container',
            'infoToolbar': 'infoToolbar',
            'floorplanImageElement': 'floorplanImage'
        };
        
        // #region agent log
        fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:2068',message:'getElement called',data:{id:id,cacheKey:cacheMap[id],cachedExists:!!(cacheMap[id]&&self._cachedElements[cacheMap[id]])},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H2'})}).catch(()=>{});
        // #endregion
        
        const cacheKey = cacheMap[id];
        if (cacheKey && self._cachedElements[cacheKey]) {
            return self._cachedElements[cacheKey];
        }
        
        const element = document.getElementById(id);
        if (element && cacheKey) {
            self._cachedElements[cacheKey] = element;
        }
        
        // #region agent log
        fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:2086',message:'getElement result',data:{id:id,elementFound:!!element},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H2'})}).catch(()=>{});
        // #endregion
        
        return element;
    },
    
    // Initialize
    init: function() {
        const self = this;
        
        // #region agent log
        fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:2090',message:'init() called',data:{timestamp:Date.now()},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H4'})}).catch(()=>{});
        // #endregion
        
        // Cache frequently used DOM elements
        self._cachedElements.canvas = document.getElementById('print');
        self._cachedElements.container = document.getElementById('printContainer');
        self._cachedElements.infoToolbar = document.getElementById('infoToolbar');
        self._cachedElements.floorplanImage = document.getElementById('floorplanImageElement');
        
        // #region agent log
        fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:2097',message:'Elements cached',data:{canvas:!!self._cachedElements.canvas,container:!!self._cachedElements.container,infoToolbar:!!self._cachedElements.infoToolbar,floorplanImage:!!self._cachedElements.floorplanImage},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H4'})}).catch(()=>{});
        // #endregion
        
        // Initialize Design Tools System
        self.currentTool = 'select'; // Default tool
        self.previousTool = 'select'; // Track previous tool for Space key switching
        self.isSpacePanning = false; // Track if Space key is being held for panning
        
        // Tool button handlers
        $('.toolbar-btn[data-tool]').on('click', function(e) {
            const tool = $(this).data('tool');
            const $btn = $(this);
            
            // Handle dropdown menus for align and distribute
            if (tool === 'align' || tool === 'distribute') {
                const $dropdown = $btn.closest('.dropdown');
                if ($dropdown.length) {
                    e.stopPropagation();
                    $dropdown.toggleClass('show');
                    // Close other dropdowns
                    $('.dropdown').not($dropdown).removeClass('show');
                }
            } else {
                // Regular tool - switch immediately
                // Update previous tool if not currently space-panning
                if (!self.isSpacePanning && self.currentTool !== tool) {
                    self.previousTool = self.currentTool;
                }
                self.switchTool(tool);
                // Close any open dropdowns
                $('.dropdown').removeClass('show');
            }
        });
        
        // Close dropdowns when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown').removeClass('show');
        }
        });
        
        // Set Select tool as active by default
        self.switchTool('select');
        
        // Setup tool-specific functionality
        self.setupToolHandlers();
        
        // Keyboard shortcuts for tools
        $(document).on('keydown', function(e) {
            // Only activate shortcuts when not typing in inputs
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
                return;
            }
            
            if (e.key === 'v' || e.key === 'V') {
                e.preventDefault();
                if (!self.isSpacePanning && self.currentTool !== 'select') {
                    self.previousTool = self.currentTool;
                }
                self.switchTool('select');
                const btn = document.getElementById('btnSelectTool');
                if (btn) btn.click();
            } else if (e.key === 'h' || e.key === 'H') {
                e.preventDefault();
                if (!self.isSpacePanning && self.currentTool !== 'pan') {
                    self.previousTool = self.currentTool;
                }
                self.switchTool('pan');
                const btn = document.getElementById('btnPanTool');
                if (btn) btn.click();
            } else if (e.key === 'z' || e.key === 'Z') {
                e.preventDefault();
                if (!self.isSpacePanning && self.currentTool !== 'zoom') {
                    self.previousTool = self.currentTool;
                }
                self.switchTool('zoom');
                const btn = document.getElementById('btnZoomTool');
                if (btn) btn.click();
            } else if (e.key === 'a' || e.key === 'A') {
                e.preventDefault();
                if (!self.isSpacePanning && self.currentTool !== 'align') {
                    self.previousTool = self.currentTool;
                }
                self.switchTool('align');
                const btn = document.getElementById('btnAlignTool');
                if (btn) btn.click();
            } else if (e.key === 'd' || e.key === 'D') {
                e.preventDefault();
                if (!self.isSpacePanning && self.currentTool !== 'distribute') {
                    self.previousTool = self.currentTool;
                }
                self.switchTool('distribute');
                const btn = document.getElementById('btnDistributeTool');
                if (btn) btn.click();
            } else if (e.key === 'm' || e.key === 'M') {
                e.preventDefault();
                if (!self.isSpacePanning && self.currentTool !== 'measure') {
                    self.previousTool = self.currentTool;
                }
                self.switchTool('measure');
                const btn = document.getElementById('btnMeasureTool');
                if (btn) btn.click();
            }
            // Ctrl + Plus/Equal (=) to zoom in at cursor position
            // Handles both main keyboard (=/+ with Shift) and numeric keypad (+)
            else if ((e.ctrlKey || e.metaKey) && 
                     (e.key === '+' || e.key === '=' || 
                      e.code === 'Equal' || e.code === 'NumpadAdd' ||
                      (e.shiftKey && e.key === '='))) {
                e.preventDefault();
                self.zoomAtCursor(1.2); // Zoom in by 20%
            }
            // Ctrl + Minus (-) to zoom out at cursor position
            // Handles both main keyboard (-) and numeric keypad (-)
            else if ((e.ctrlKey || e.metaKey) && 
                     (e.key === '-' || e.code === 'Minus' || e.code === 'NumpadSubtract')) {
                e.preventDefault();
                self.zoomAtCursor(1 / 1.2); // Zoom out by 20%
            }
            // Ctrl + 0 to fit canvas to viewport
            else if ((e.ctrlKey || e.metaKey) && (e.key === '0' || e.code === 'Digit0' || e.code === 'Numpad0')) {
                e.preventDefault();
                self.fitCanvasToView(true); // true = animate
            }
        });
        
        // CRITICAL: Initialize with floor plan canvas settings IMMEDIATELY on page load
        // This ensures the canvas loads the correct floor plan image automatically
        @if(isset($currentFloorPlan) && $currentFloorPlan)
            @if(isset($canvasWidth) && isset($canvasHeight) && $canvasWidth && $canvasHeight)
                // Use floor plan canvas dimensions as defaults
                self.canvasWidth = @php echo (int)$canvasWidth; @endphp;
                self.canvasHeight = @php echo (int)$canvasHeight; @endphp;
            @endif
            
            @if($currentFloorPlan->floor_image)
                // CRITICAL: Always use floor_plans.floor_image as source of truth
                // Store floor plan image URL for immediate use
                @php
                    $floorPlanImageUrl = asset($currentFloorPlan->floor_image);
                    $floorPlanImagePath = $currentFloorPlan->floor_image;
                @endphp
                self.floorPlanImageUrl = '{{ $floorPlanImageUrl }}';
                self.floorplanImage = '{{ $floorPlanImagePath }}'; // Relative path
                
                // IMMEDIATELY set canvas background from floor plan (highest priority)
                // This ensures the image loads automatically when user clicks "View Booths"
                const canvas = document.getElementById('print');
                if (canvas) {
                    console.log('[Floor Plan] Loading image for floor plan {{ $currentFloorPlan->id }}: {{ $currentFloorPlan->name }}');
                    console.log('[Floor Plan] Image path:', self.floorplanImage);
                    console.log('[Floor Plan] Image URL:', self.floorPlanImageUrl);
                    
                    // Always set the background image from current floor plan (automatic load)
                    canvas.style.backgroundImage = 'url(\'' + self.floorPlanImageUrl + '?t=' + Date.now() + '\')';
                    canvas.style.backgroundSize = '100% 100%';
                    canvas.style.backgroundRepeat = 'no-repeat';
                    canvas.style.backgroundPosition = 'top left';
                    canvas.style.backgroundAttachment = 'local';
                    canvas.style.margin = '0';
                    canvas.style.display = 'block';
                    canvas.style.float = 'left';
                    
                    console.log('[Floor Plan] Canvas background image set:', canvas.style.backgroundImage);
                }
            @else
                console.log('[Floor Plan] No image for floor plan {{ $currentFloorPlan->id }}: {{ $currentFloorPlan->name }}');
            @endif
        @endif
        
        // Load booth default settings from database first, then setup
        this.loadBoothSettingsFromDatabase().then(function() {
            self.setupDragAndDrop();
            self.setupToolbar();
            self.setupCanvas();
            self.setupKeyboard();
            self.setupZoomSelection(); // Setup Photoshop-like zoom selection (Ctrl+Space)
            
            // AUTOMATICALLY load floor plan image and resize canvas to match image resolution
            @if(isset($currentFloorPlan) && $currentFloorPlan && $currentFloorPlan->floor_image)
                // CRITICAL: Floor plan has image - automatically load it and resize canvas
                // This ensures the canvas loads the correct image when user clicks "View Booths"
                const canvas = document.getElementById('print');
                if (canvas && self.floorPlanImageUrl) {
                    console.log('[Floor Plan Auto-Load] Starting automatic image load for floor plan {{ $currentFloorPlan->id }}');
                    
                    // Ensure background image is set (already set above, but double-check)
                    if (!canvas.style.backgroundImage || canvas.style.backgroundImage === 'none') {
                        canvas.style.backgroundImage = 'url(\'' + self.floorPlanImageUrl + '?t=' + Date.now() + '\')';
                        canvas.style.backgroundSize = '100% 100%';
                        canvas.style.backgroundRepeat = 'no-repeat';
                        canvas.style.backgroundPosition = 'top left';
                        canvas.style.backgroundAttachment = 'local';
                    }
                    
                    // Load image to get dimensions and AUTOMATICALLY resize canvas to match
                    const img = new Image();
                    img.crossOrigin = 'anonymous'; // Handle CORS if needed
                    
                    img.onload = function() {
                        const imageWidth = img.naturalWidth || img.width;
                        const imageHeight = img.naturalHeight || img.height;
                        
                        console.log('[Floor Plan Auto-Load] Image loaded successfully:', {
                            floor_plan_id: {{ $currentFloorPlan->id }},
                            floor_plan_name: '{{ $currentFloorPlan->name }}',
                            image_width: imageWidth,
                            image_height: imageHeight,
                            image_url: self.floorPlanImageUrl
                        });
                        
                        if (imageWidth > 0 && imageHeight > 0) {
                            // AUTOMATICALLY update canvas size to match image resolution EXACTLY
                            self.setCanvasSize(imageWidth, imageHeight);
                            
                            // Update canvas settings in memory
                            self.canvasWidth = imageWidth;
                            self.canvasHeight = imageHeight;
                            
                            console.log('[Floor Plan Auto-Load] Canvas resized to match image:', imageWidth, 'x', imageHeight);
                            
                            // Update panzoom after canvas resize
                            if (self.panzoomInstance) {
                                setTimeout(function() {
                                    if (self.panzoomInstance.setOptions) {
                                        self.panzoomInstance.setOptions({
                                            minScale: 0.1,
                                            maxScale: 5,
                                            contain: 'outside'
                                        });
                                    }
                                    // AUTOMATICALLY fit canvas to view after resize
                                    self.fitCanvasToView(false);
                                }, 100);
                            }
                        }
                    };
                    
                    img.onerror = function() {
                        console.error('[Floor Plan Auto-Load] Failed to load floor plan image:', {
                            floor_plan_id: {{ $currentFloorPlan->id }},
                            floor_plan_name: '{{ $currentFloorPlan->name }}',
                            image_url: self.floorPlanImageUrl,
                            image_path: self.floorplanImage
                        });
                        // Fallback to floor plan canvas dimensions if image fails to load
                        if (!localStorage.getItem('canvasWidth') || !localStorage.getItem('canvasHeight')) {
                            self.setCanvasSize(self.canvasWidth, self.canvasHeight);
                        }
                    };
                    
                    // AUTOMATICALLY load the image immediately
                    img.src = self.floorPlanImageUrl;
                    
                    // Load canvas settings AFTER image is loaded (zoom/pan/grid - but don't override image)
                    // The image is already set from floor_plans.floor_image, so loadCanvasSettings won't clear it
                    setTimeout(function() {
                        console.log('[Floor Plan Auto-Load] Loading canvas settings for floor plan {{ $currentFloorPlan->id }}');
                        self.loadCanvasSettings();
                    }, 300); // Small delay to ensure image starts loading first
                } else {
                    console.warn('[Floor Plan Auto-Load] Canvas or image URL not found', {
                        canvas_exists: !!canvas,
                        floorPlanImageUrl: self.floorPlanImageUrl
                    });
                    // Fallback: load canvas settings
                    self.loadCanvasSettings();
                }
            @else
                // No floor plan image from Blade - load canvas settings (may have image from canvas_settings)
                console.log('[Floor Plan Auto-Load] No image for current floor plan, loading canvas settings');
                self.loadCanvasSettings();
                // Ensure canvas has a fixed size (in case no saved settings exist)
                if (!localStorage.getItem('canvasWidth') || !localStorage.getItem('canvasHeight')) {
                    self.setCanvasSize(self.canvasWidth, self.canvasHeight);
                }
            @endif
            
            // Check if there's an existing floorplan image and resize canvas to match (fallback for other cases)
            // Only if we haven't already loaded from Blade template
            @if(!isset($currentFloorPlan) || !$currentFloorPlan || !$currentFloorPlan->floor_image)
                self.detectAndResizeCanvasToImage();
            @endif
        
            self.loadSavedPositions();
            // After loading positions, sync sidebar to remove booths already on canvas
            setTimeout(function() {
                self.syncSidebarWithCanvas();
            }, 500); // Small delay to ensure all booths are loaded
            
            // Auto-fit canvas to show entire image after positions are loaded (early)
        setTimeout(function() {
            if (self.panzoomInstance) {
                self.fitCanvasToView(false);
            }
            }, 300);

            // Bind Add Zone button (main)
            const addZoneBtn = document.getElementById('btnAddZoneMain');
            if (addZoneBtn) {
                addZoneBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    self.showAddZoneModal();
                });
            }
        }).catch(function(error) {
            console.error('Error loading settings from database, using fallback:', error);
            // Continue with initialization even if settings load fails
            self.setupDragAndDrop();
            self.setupToolbar();
            self.setupCanvas();
            self.setupZoneBoothSelection();
            self.setupKeyboard();
            self.setupZoomSelection();
            self.loadCanvasSettings();
            if (!localStorage.getItem('canvasWidth') || !localStorage.getItem('canvasHeight')) {
                self.setCanvasSize(self.canvasWidth, self.canvasHeight);
            }
            self.detectAndResizeCanvasToImage();
            self.loadSavedPositions();
            self.saveState();
        });
        
        // Auto-fit canvas to show entire image on EVERY page load
        // Always fit to canvas to show 100% of the floorplan
        const fitOnLoad = function() {
            if (self.panzoomInstance) {
                const canvas = self.getElement('print');
                const container = self.getElement('printContainer');
                
                if (canvas && container) {
                    // Check if image is loaded
                    const floorplanImg = self.getElement('floorplanImageElement');
                    const bgImage = canvas.style.backgroundImage;
                    const hasImage = (bgImage && bgImage !== 'none' && bgImage !== '') || (floorplanImg && floorplanImg.complete);
                    
                    if (hasImage || canvas.offsetWidth > 0) {
                        // Image is loaded or canvas has size - fit to view
                        self.fitCanvasToView(false);
                    } else {
                        // Image not loaded yet, wait for it
                        if (floorplanImg) {
                            floorplanImg.onload = function() {
                                self.fitCanvasToView(false);
                            };
                        }
                        // Also try after a delay in case onload already fired
                        setTimeout(function() {
                            if (canvas && container && self.panzoomInstance) {
                                self.fitCanvasToView(false);
                            }
                        }, 500);
                    }
                } else {
                    // Elements not ready, wait a bit more
                    setTimeout(fitOnLoad, 200);
                }
            } else {
                // Panzoom not ready, wait a bit more
                setTimeout(fitOnLoad, 200);
            }
        };
        
        // Start fitting early - reduced delay for faster loading
        // Start checking immediately and retry if needed
        setTimeout(fitOnLoad, 100);
    },
    
    // Setup Drag and Drop
    setupDragAndDrop: function() {
        const self = this;
        const canvas = self.getElement('print');
        
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
            self.draggedBoothData = self.getBoothData(item);
            
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
            
            // Snap to grid when dropping (if snap is enabled)
            if (self.snapEnabled) {
            x = Math.round(x / self.gridSize) * self.gridSize;
            y = Math.round(y / self.gridSize) * self.gridSize;
            }
            
            self.addBoothToCanvas(self.draggedBoothData, x, y);
            
            // Remove booth from sidebar after successfully adding to canvas
            if (self.draggedElement) {
                self.removeBoothFromSidebar(self.draggedElement);
            }
            
            self.draggedBoothData = null;
            self.draggedElement = null;
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
    
    // Remove booth from sidebar
    removeBoothFromSidebar: function(boothElement) {
        if (boothElement && boothElement.parentNode) {
            const zoneName = boothElement.getAttribute('data-booth-zone');
            boothElement.remove();
            
            // Update zone count after removal
            if (zoneName) {
                this.updateZoneCount(zoneName);
                
                // If zone is now empty, optionally hide it (or keep it visible)
                const zoneSection = document.querySelector('[data-zone="' + zoneName + '"]');
                if (zoneSection) {
                    const zoneContent = zoneSection.querySelector('.zone-content');
                    const remainingBooths = zoneContent ? zoneContent.querySelectorAll('.booth-number-item').length : 0;
                    // Keep zone visible even if empty, so user can add booths back
                }
            }
        }
    },
    
    // Add all booths from a zone to canvas
    addAllZoneToCanvas: function(zoneName) {
        const self = this;
        const canvas = self._cachedElements.canvas;
        if (!canvas) {
            console.error('Canvas not found');
            return;
        }
        
        // Ensure zoneName is a string and trimmed
        zoneName = String(zoneName).trim();
        console.log('Adding all booths from Zone:', zoneName);
        
        // Find the zone section - try multiple selectors to be safe
        let zoneSection = document.querySelector('[data-zone="' + zoneName + '"]');
        if (!zoneSection) {
            // Try case-insensitive search
            const allZoneSections = document.querySelectorAll('[data-zone]');
            for (let i = 0; i < allZoneSections.length; i++) {
                const section = allZoneSections[i];
                if (section.getAttribute('data-zone').toUpperCase() === zoneName.toUpperCase()) {
                    zoneSection = section;
                    break;
                }
            }
        }
        
        if (!zoneSection) {
            console.error('Zone section not found for:', zoneName);
            showNotification('Zone ' + zoneName + ' not found', 'error');
            return;
        }
        
        // Ensure zone section is expanded (not collapsed)
        if (zoneSection.classList.contains('collapsed')) {
            zoneSection.classList.remove('collapsed');
        }
        
        // Get all booth items in this zone
        const zoneContent = zoneSection.querySelector('.zone-content');
        if (!zoneContent) {
            console.error('Zone content not found for:', zoneName);
            return;
        }
        
        const boothItems = zoneContent.querySelectorAll('.booth-number-item');
        console.log('Found', boothItems.length, 'booths in Zone', zoneName);
        
        if (boothItems.length === 0) {
            showNotification('No booths in Zone ' + zoneName, 'info');
            return;
        }
        
        // Fetch zone settings first (this will cache them for use in createBoothElement)
        self.getZoneSettings(zoneName, function(zoneSettings) {
            // Get canvas center or use a starting position
            const canvasRect = canvas.getBoundingClientRect();
            const container = self.getElement('printContainer');
            const containerRect = container ? container.getBoundingClientRect() : null;
            
            // Get current pan/zoom
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
            
            // Calculate starting position (center of visible canvas area)
            let startX = 500; // Default starting X
            let startY = 300; // Default starting Y
            
            if (containerRect) {
                // Center of visible container area, converted to canvas coordinates
                const containerCenterX = containerRect.width / 2;
                const containerCenterY = containerRect.height / 2;
                startX = (containerCenterX - panX) / scale;
                startY = (containerCenterY - panY) / scale;
            }
            
            // Use zone settings for spacing if available, otherwise use defaults
            const effectiveSettings = zoneSettings || self.getEffectiveBoothSettings('');
            const spacingX = effectiveSettings.width + 20; // Spacing between booths
            const spacingY = effectiveSettings.height + 20;
            const gridCols = Math.ceil(Math.sqrt(boothItems.length)); // Square-ish grid
            
            let addedCount = 0;
            let skippedCount = 0;
            const boothsToRemove = []; // Collect booths to remove after adding
            const boothsToSave = []; // Collect booth data for batch save
            
            // First pass: Add all booths to canvas and collect items to remove
            Array.from(boothItems).forEach(function(boothItem, index) {
                // Check if booth already exists on canvas
                const boothId = boothItem.getAttribute('data-booth-id');
                const boothNumber = boothItem.getAttribute('data-booth-number');
                
                if (!boothId) {
                    console.warn('Booth item missing data-booth-id:', boothItem, 'Zone:', zoneName);
                    return;
                }
                
                const existingBooth = canvas.querySelector('[data-booth-id="' + boothId + '"]');
                if (existingBooth) {
                    console.log('Booth', boothNumber, '(ID:', boothId, ') already on canvas, skipping');
                    skippedCount++;
                    return; // Skip if already on canvas
                }
                
                // Prepare booth data
                const boothData = {
                    id: boothId,
                    number: boothNumber,
                    status: boothItem.getAttribute('data-booth-status'),
                    clientId: boothItem.getAttribute('data-client-id') || '',
                    userId: boothItem.getAttribute('data-user-id') || '',
                    categoryId: boothItem.getAttribute('data-category-id') || '',
                    subCategoryId: boothItem.getAttribute('data-sub-category-id') || '',
                    assetId: boothItem.getAttribute('data-asset-id') || '',
                    boothTypeId: boothItem.getAttribute('data-booth-type-id') || ''
                };
                
                // Calculate grid position
                const col = index % gridCols;
                const row = Math.floor(index / gridCols);
                const x = startX + (col * spacingX);
                const y = startY + (row * spacingY);
                
                // Snap to grid if enabled
                let finalX = x;
                let finalY = y;
                if (self.snapEnabled) {
                    finalX = Math.round(x / self.gridSize) * self.gridSize;
                    finalY = Math.round(y / self.gridSize) * self.gridSize;
                }
                
                console.log('Adding booth', boothNumber, 'to canvas at', finalX, finalY, 'Zone:', zoneName);
                
                // Add booth to canvas (skip individual save)
                self.addBoothToCanvas(boothData, finalX, finalY, true); // true = skip save
                
                // Collect booth data for batch save
                const boothElement = canvas.querySelector('[data-booth-id="' + boothId + '"]');
                if (boothElement) {
                    const width = parseFloat(boothElement.style.width) || effectiveSettings.width;
                    const height = parseFloat(boothElement.style.height) || effectiveSettings.height;
                    const rotation = parseFloat(boothElement.getAttribute('data-rotation')) || effectiveSettings.rotation;
                    const zIndex = parseFloat(boothElement.style.zIndex) || effectiveSettings.zIndex;
                    const fontSize = parseFloat(boothElement.style.fontSize) || effectiveSettings.fontSize;
                    const borderWidth = parseFloat(boothElement.style.borderWidth) || effectiveSettings.borderWidth;
                    const borderRadius = parseFloat(boothElement.style.borderRadius) || effectiveSettings.borderRadius;
                    const opacity = parseFloat(boothElement.style.opacity) || effectiveSettings.opacity;
                    
                    // Get appearance properties
                    const backgroundColor = boothElement.style.backgroundColor || boothElement.getAttribute('data-background-color') || effectiveSettings.background_color || self.defaultBackgroundColor;
                    const borderColor = boothElement.style.borderColor || boothElement.getAttribute('data-border-color') || effectiveSettings.border_color || self.defaultBorderColor;
                    const textColor = boothElement.style.color || boothElement.getAttribute('data-text-color') || effectiveSettings.text_color || self.defaultTextColor;
                    const fontWeight = boothElement.style.fontWeight || boothElement.getAttribute('data-font-weight') || effectiveSettings.font_weight || self.defaultFontWeight;
                    const fontFamily = boothElement.style.fontFamily || boothElement.getAttribute('data-font-family') || effectiveSettings.font_family || self.defaultFontFamily;
                    const textAlign = boothElement.style.textAlign || boothElement.getAttribute('data-text-align') || effectiveSettings.text_align || self.defaultTextAlign;
                    const boxShadow = boothElement.style.boxShadow || boothElement.getAttribute('data-box-shadow') || effectiveSettings.box_shadow || self.defaultBoxShadow;
                    
                    // Ensure boothId is a valid integer
                    const boothIdInt = parseInt(boothId);
                    if (isNaN(boothIdInt) || boothIdInt <= 0) {
                        console.error('Invalid booth ID:', boothId);
                        return;
                    }
                    
                    // Ensure all numeric values are valid numbers (not NaN) and properly formatted
                    const boothDataToSave = {
                        id: boothIdInt,
                        position_x: (isNaN(finalX) || finalX === null || finalX === undefined) ? null : Number(finalX),
                        position_y: (isNaN(finalY) || finalY === null || finalY === undefined) ? null : Number(finalY),
                        width: (isNaN(width) || width === null || width === undefined) ? null : Number(width),
                        height: (isNaN(height) || height === null || height === undefined) ? null : Number(height),
                        rotation: (isNaN(rotation) || rotation === null || rotation === undefined) ? 0 : Number(rotation),
                        z_index: (isNaN(zIndex) || zIndex === null || zIndex === undefined) ? 10 : parseInt(zIndex),
                        font_size: (isNaN(fontSize) || fontSize === null || fontSize === undefined) ? 14 : parseInt(fontSize),
                        border_width: (isNaN(borderWidth) || borderWidth === null || borderWidth === undefined) ? 2 : parseInt(borderWidth),
                        border_radius: (isNaN(borderRadius) || borderRadius === null || borderRadius === undefined) ? 6 : parseInt(borderRadius),
                        opacity: (isNaN(opacity) || opacity === null || opacity === undefined) ? 1.00 : Number(opacity),
                        // Appearance properties - ensure they're strings or null
                        background_color: backgroundColor ? String(backgroundColor) : null,
                        border_color: borderColor ? String(borderColor) : null,
                        text_color: textColor ? String(textColor) : null,
                        font_weight: fontWeight ? String(fontWeight) : null,
                        font_family: fontFamily ? String(fontFamily) : null,
                        text_align: textAlign ? String(textAlign) : null,
                        box_shadow: boxShadow ? String(boxShadow) : null
                    };
                    
                    console.log('Preparing booth data for save (ID:', boothIdInt, '):', boothDataToSave);
                    boothsToSave.push(boothDataToSave);
                }
                
                // Collect booth item to remove (don't remove during iteration)
                boothsToRemove.push(boothItem);
                
                addedCount++;
            });
            
            // Second pass: Remove all booths from sidebar after adding to canvas
            boothsToRemove.forEach(function(boothItem) {
                self.removeBoothFromSidebar(boothItem);
            });
            
            // Batch save all booths at once (much faster than individual saves)
            if (boothsToSave.length > 0) {
                console.log('Saving', boothsToSave.length, 'booths from Zone', zoneName, 'to database');
                self.saveBoothsBatch(boothsToSave).then(function(result) {
                    console.log('✅ Zone', zoneName, 'booths saved successfully:', result);
                }).catch(function(error) {
                    console.error('❌ Error saving zone', zoneName, 'booths:', error);
                    showNotification('Error saving booths from Zone ' + zoneName + ' to database', 'error');
                });
            } else {
                console.warn('No booths to save for Zone', zoneName, '- addedCount:', addedCount, 'skippedCount:', skippedCount);
            }
            
            // Show notification
            if (addedCount > 0) {
                console.log('✅ Successfully added', addedCount, 'booth(s) from Zone', zoneName, 'to canvas');
                showNotification(addedCount + ' booth' + (addedCount !== 1 ? 's' : '') + ' from Zone ' + zoneName + ' added to canvas' + (skippedCount > 0 ? ' (' + skippedCount + ' already on canvas)' : ''), 'success');
            } else if (skippedCount > 0) {
                console.log('ℹ️ All booths from Zone', zoneName, 'are already on canvas');
                showNotification('All booths from Zone ' + zoneName + ' are already on canvas', 'info');
            } else {
                console.warn('⚠️ No booths were added from Zone', zoneName);
                showNotification('No booths were added from Zone ' + zoneName, 'warning');
            }
            
            // Update booth count
            if (self.updateBoothCount) {
                self.updateBoothCount();
            }
            
            // Save state
            self.saveState();
        });
    },
    
    // Add selected booths from a zone to canvas (stick together)
    addSelectedZoneBoothsToCanvas: function(zoneName) {
        const self = this;
        const canvas = self._cachedElements.canvas;
        if (!canvas) {
            console.error('Canvas not found');
            return;
        }
        
        // Ensure zoneName is a string and trimmed
        zoneName = String(zoneName).trim();
        console.log('Adding selected booths from Zone:', zoneName);
        
        // Find the zone section
        let zoneSection = document.querySelector('[data-zone="' + zoneName + '"]');
        if (!zoneSection) {
            const allZoneSections = document.querySelectorAll('[data-zone]');
            for (let i = 0; i < allZoneSections.length; i++) {
                const section = allZoneSections[i];
                if (section.getAttribute('data-zone').toUpperCase() === zoneName.toUpperCase()) {
                    zoneSection = section;
                    break;
                }
            }
        }
        
        if (!zoneSection) {
            console.error('Zone section not found for:', zoneName);
            showNotification('Zone ' + zoneName + ' not found', 'error');
            return;
        }
        
        // Ensure zone section is expanded
        if (zoneSection.classList.contains('collapsed')) {
            zoneSection.classList.remove('collapsed');
        }
        
        // Get selected booth items in this zone
        const zoneContent = zoneSection.querySelector('.zone-content');
        if (!zoneContent) {
            console.error('Zone content not found for:', zoneName);
            return;
        }
        
        const selectedBoothItems = zoneContent.querySelectorAll('.booth-number-item.selected');
        console.log('Found', selectedBoothItems.length, 'selected booths in Zone', zoneName);
        
        if (selectedBoothItems.length === 0) {
            showNotification('Please select at least one booth from Zone ' + zoneName, 'warning');
            return;
        }
        
        // Fetch zone settings first
        self.getZoneSettings(zoneName, function(zoneSettings) {
            const effectiveSettings = zoneSettings || self.getEffectiveBoothSettings('');
            const spacingX = effectiveSettings.width + 15; // Tighter spacing for sticking together
            const spacingY = effectiveSettings.height + 15;
            
            // Calculate grid dimensions for selected booths
            const gridCols = Math.ceil(Math.sqrt(selectedBoothItems.length));
            
            // Calculate starting position (center of visible canvas area)
            let startX = 500;
            let startY = 300;
            const container = self.getElement('printContainer');
            if (container) {
                const containerRect = container.getBoundingClientRect();
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
                const containerCenterX = containerRect.width / 2;
                const containerCenterY = containerRect.height / 2;
                startX = (containerCenterX - panX) / scale;
                startY = (containerCenterY - panY) / scale;
            }
            
            let addedCount = 0;
            let skippedCount = 0;
            const boothsToRemove = [];
            const boothsToSave = [];
            
            // Add selected booths to canvas in a compact grid (stick together)
            Array.from(selectedBoothItems).forEach(function(boothItem, index) {
                const boothId = boothItem.getAttribute('data-booth-id');
                const boothNumber = boothItem.getAttribute('data-booth-number');
                
                if (!boothId) {
                    console.warn('Booth item missing data-booth-id:', boothItem, 'Zone:', zoneName);
                    return;
                }
                
                const existingBooth = canvas.querySelector('[data-booth-id="' + boothId + '"]');
                if (existingBooth) {
                    console.log('Booth', boothNumber, '(ID:', boothId, ') already on canvas, skipping');
                    skippedCount++;
                    return;
                }
                
                // Prepare booth data
                const boothData = {
                    id: boothId,
                    number: boothNumber,
                    status: boothItem.getAttribute('data-booth-status'),
                    clientId: boothItem.getAttribute('data-client-id') || '',
                    userId: boothItem.getAttribute('data-user-id') || '',
                    categoryId: boothItem.getAttribute('data-category-id') || '',
                    subCategoryId: boothItem.getAttribute('data-sub-category-id') || '',
                    assetId: boothItem.getAttribute('data-asset-id') || '',
                    boothTypeId: boothItem.getAttribute('data-booth-type-id') || ''
                };
                
                // Calculate compact grid position (stick together)
                const col = index % gridCols;
                const row = Math.floor(index / gridCols);
                const x = startX + (col * spacingX);
                const y = startY + (row * spacingY);
                
                // Snap to grid if enabled
                let finalX = x;
                let finalY = y;
                if (self.snapEnabled) {
                    finalX = Math.round(x / self.gridSize) * self.gridSize;
                    finalY = Math.round(y / self.gridSize) * self.gridSize;
                }
                
                console.log('Adding selected booth', boothNumber, 'to canvas at', finalX, finalY, 'Zone:', zoneName);
                
                // Add booth to canvas (skip individual save)
                self.addBoothToCanvas(boothData, finalX, finalY, true);
                
                // Collect booth data for batch save
                const boothElement = canvas.querySelector('[data-booth-id="' + boothId + '"]');
                if (boothElement) {
                    const width = parseFloat(boothElement.style.width) || effectiveSettings.width;
                    const height = parseFloat(boothElement.style.height) || effectiveSettings.height;
                    const rotation = parseFloat(boothElement.getAttribute('data-rotation')) || effectiveSettings.rotation;
                    const zIndex = parseFloat(boothElement.style.zIndex) || effectiveSettings.zIndex;
                    const fontSize = parseFloat(boothElement.style.fontSize) || effectiveSettings.fontSize;
                    const borderWidth = parseFloat(boothElement.style.borderWidth) || effectiveSettings.borderWidth;
                    const borderRadius = parseFloat(boothElement.style.borderRadius) || effectiveSettings.borderRadius;
                    const opacity = parseFloat(boothElement.style.opacity) || effectiveSettings.opacity;
                    
                    const backgroundColor = boothElement.style.backgroundColor || boothElement.getAttribute('data-background-color') || effectiveSettings.background_color || self.defaultBackgroundColor;
                    const borderColor = boothElement.style.borderColor || boothElement.getAttribute('data-border-color') || effectiveSettings.border_color || self.defaultBorderColor;
                    const textColor = boothElement.style.color || boothElement.getAttribute('data-text-color') || effectiveSettings.text_color || self.defaultTextColor;
                    const fontWeight = boothElement.style.fontWeight || boothElement.getAttribute('data-font-weight') || effectiveSettings.font_weight || self.defaultFontWeight;
                    const fontFamily = boothElement.style.fontFamily || boothElement.getAttribute('data-font-family') || effectiveSettings.font_family || self.defaultFontFamily;
                    const textAlign = boothElement.style.textAlign || boothElement.getAttribute('data-text-align') || effectiveSettings.text_align || self.defaultTextAlign;
                    const boxShadow = boothElement.style.boxShadow || boothElement.getAttribute('data-box-shadow') || effectiveSettings.box_shadow || self.defaultBoxShadow;
                    
                    const boothIdInt = parseInt(boothId);
                    if (isNaN(boothIdInt) || boothIdInt <= 0) {
                        console.error('Invalid booth ID:', boothId);
                        return;
                    }
                    
                    const boothDataToSave = {
                        id: boothIdInt,
                        position_x: (isNaN(finalX) || finalX === null || finalX === undefined) ? null : Number(finalX),
                        position_y: (isNaN(finalY) || finalY === null || finalY === undefined) ? null : Number(finalY),
                        width: (isNaN(width) || width === null || width === undefined) ? null : Number(width),
                        height: (isNaN(height) || height === null || height === undefined) ? null : Number(height),
                        rotation: (isNaN(rotation) || rotation === null || rotation === undefined) ? 0 : Number(rotation),
                        z_index: (isNaN(zIndex) || zIndex === null || zIndex === undefined) ? 10 : parseInt(zIndex),
                        font_size: (isNaN(fontSize) || fontSize === null || fontSize === undefined) ? 14 : parseInt(fontSize),
                        border_width: (isNaN(borderWidth) || borderWidth === null || borderWidth === undefined) ? 2 : parseInt(borderWidth),
                        border_radius: (isNaN(borderRadius) || borderRadius === null || borderRadius === undefined) ? 6 : parseInt(borderRadius),
                        opacity: (isNaN(opacity) || opacity === null || opacity === undefined) ? 1.00 : Number(opacity),
                        background_color: backgroundColor ? String(backgroundColor) : null,
                        border_color: borderColor ? String(borderColor) : null,
                        text_color: textColor ? String(textColor) : null,
                        font_weight: fontWeight ? String(fontWeight) : null,
                        font_family: fontFamily ? String(fontFamily) : null,
                        text_align: textAlign ? String(textAlign) : null,
                        box_shadow: boxShadow ? String(boxShadow) : null
                    };
                    
                    boothsToSave.push(boothDataToSave);
                }
                
                // Remove selection state and collect for removal
                boothItem.classList.remove('selected');
                boothsToRemove.push(boothItem);
                addedCount++;
            });
            
            // Remove booths from sidebar after adding to canvas
            boothsToRemove.forEach(function(boothItem) {
                self.removeBoothFromSidebar(boothItem);
            });
            
            // Update "Add Selected" button state
            self.updateZoneAddSelectedButton(zoneName);
            
            // Batch save all booths
            if (boothsToSave.length > 0) {
                console.log('Saving', boothsToSave.length, 'selected booths from Zone', zoneName, 'to database');
                self.saveBoothsBatch(boothsToSave).then(function(result) {
                    console.log('✅ Selected booths from Zone', zoneName, 'saved successfully:', result);
                }).catch(function(error) {
                    console.error('❌ Error saving selected booths from Zone', zoneName, ':', error);
                    showNotification('Error saving selected booths from Zone ' + zoneName + ' to database', 'error');
                });
            }
            
            // Show notification
            if (addedCount > 0) {
                console.log('✅ Successfully added', addedCount, 'selected booth(s) from Zone', zoneName, 'to canvas (stuck together)');
                showNotification(addedCount + ' selected booth' + (addedCount !== 1 ? 's' : '') + ' from Zone ' + zoneName + ' added to canvas (stuck together)' + (skippedCount > 0 ? ' (' + skippedCount + ' already on canvas)' : ''), 'success');
            } else if (skippedCount > 0) {
                showNotification('All selected booths from Zone ' + zoneName + ' are already on canvas', 'info');
            }
            
            // Update booth count
            if (self.updateBoothCount) {
                self.updateBoothCount();
            }
            
            // Save state
            self.saveState();
        });
    },
    
    // Update "Add Selected" button state based on selected booths
    updateZoneAddSelectedButton: function(zoneName) {
        const zoneSection = document.querySelector('[data-zone="' + zoneName + '"]');
        if (!zoneSection) return;
        
        const zoneContent = zoneSection.querySelector('.zone-content');
        if (!zoneContent) return;
        
        const selectedBoothItems = zoneContent.querySelectorAll('.booth-number-item.selected');
        const addSelectedBtn = zoneSection.querySelector('.btn-add-selected-zone');
        
        if (addSelectedBtn) {
            if (selectedBoothItems.length > 0) {
                addSelectedBtn.disabled = false;
                addSelectedBtn.title = 'Add ' + selectedBoothItems.length + ' Selected Booth' + (selectedBoothItems.length !== 1 ? 's' : '') + ' to Canvas (Stick Together)';
            } else {
                addSelectedBtn.disabled = true;
                addSelectedBtn.title = 'Select booths first to add them to canvas';
            }
        }
    },
    
    // Enable click-to-place mode for adding all booths from a zone
    enableClickToPlaceMode: function(zoneName) {
        const self = this;
        const canvas = self.getElement('print');
        if (!canvas) return;
        
        // Find the zone section
        const zoneSection = document.querySelector('[data-zone="' + zoneName + '"]');
        if (!zoneSection) {
            showNotification('Zone ' + zoneName + ' not found', 'error');
            return;
        }
        
        // Get all booth items in this zone
        const zoneContent = zoneSection.querySelector('.zone-content');
        if (!zoneContent) return;
        
        const boothItems = zoneContent.querySelectorAll('.booth-number-item');
        if (boothItems.length === 0) {
            showNotification('No booths in Zone ' + zoneName, 'info');
            return;
        }
        
        // Show notification that click mode is active
        showNotification('Click on the canvas where you want to place all booths from Zone ' + zoneName + ' (' + boothItems.length + ' booths)', 'info');
        
        // Add active class to button for visual feedback
        const clickBtn = document.querySelector('.btn-add-all-zone-click[data-zone="' + zoneName + '"]');
        if (clickBtn) {
            clickBtn.classList.add('active');
        }
        
        // Change cursor to crosshair
        const container = document.getElementById('printContainer');
        if (container) {
            container.style.cursor = 'crosshair';
        }
        
        // Store zone name for the click handler
        self.clickToPlaceZone = zoneName;
        self.clickToPlaceBoothCount = boothItems.length;
        
        // Create one-time click handler
        const handleCanvasClick = function(e) {
            // Only handle clicks on the canvas itself, not on booths
            if (e.target.classList.contains('dropped-booth') || 
                e.target.closest('.dropped-booth') || 
                e.target.closest('.resize-handle') ||
                e.target.closest('.rotate-handle')) {
                return; // Ignore clicks on booths or handles
            }
            
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Remove active state
            if (clickBtn) {
                clickBtn.classList.remove('active');
            }
            if (container) {
                container.style.cursor = '';
            }
            
            // Get click position relative to container
            const containerRect = container ? container.getBoundingClientRect() : canvas.getBoundingClientRect();
            
            // Get current pan/zoom
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
            
            // Convert click position to canvas coordinates
            const clickX = e.clientX - containerRect.left;
            const clickY = e.clientY - containerRect.top;
            
            // Convert to canvas coordinates accounting for pan and zoom
            const canvasX = (clickX - panX) / scale;
            const canvasY = (clickY - panY) / scale;
            
            // Remove click handler (one-time use)
            container.removeEventListener('click', handleCanvasClick, true);
            
            // Clear the zone reference
            self.clickToPlaceZone = null;
            self.clickToPlaceBoothCount = 0;
            
            // Add all booths at the clicked position
            self.addAllZoneToCanvasAtPosition(zoneName, canvasX, canvasY);
        };
        
        // Add click listener to container using capture phase to intercept before panzoom
        container.addEventListener('click', handleCanvasClick, { once: true, capture: true });
        
        // Also allow canceling by pressing Escape
        const cancelHandler = function(e) {
            if (e.key === 'Escape') {
                if (clickBtn) {
                    clickBtn.classList.remove('active');
                }
                if (container) {
                    container.style.cursor = '';
                    container.removeEventListener('click', handleCanvasClick, true);
                }
                document.removeEventListener('keydown', cancelHandler);
                self.clickToPlaceZone = null;
                self.clickToPlaceBoothCount = 0;
                showNotification('Click-to-place mode cancelled', 'info');
            }
        };
        
        document.addEventListener('keydown', cancelHandler);
    },
    
    // Add all booths from a zone at a specific position
    addAllZoneToCanvasAtPosition: function(zoneName, startX, startY) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Find the zone section
        const zoneSection = document.querySelector('[data-zone="' + zoneName + '"]');
        if (!zoneSection) {
            showNotification('Zone ' + zoneName + ' not found', 'error');
            return;
        }
        
        // Get all booth items in this zone
        const zoneContent = zoneSection.querySelector('.zone-content');
        if (!zoneContent) return;
        
        const boothItems = zoneContent.querySelectorAll('.booth-number-item');
        if (boothItems.length === 0) {
            showNotification('No booths in Zone ' + zoneName, 'info');
            return;
        }
        
        // Fetch zone settings first (this will cache them for use in createBoothElement)
        self.getZoneSettings(zoneName, function(zoneSettings) {
            // Use zone settings for spacing if available, otherwise use defaults
            const effectiveSettings = zoneSettings || self.getEffectiveBoothSettings('');
            const spacingX = effectiveSettings.width + 20; // Spacing between booths
            const spacingY = effectiveSettings.height + 20;
            const gridCols = Math.ceil(Math.sqrt(boothItems.length)); // Square-ish grid
            
            let addedCount = 0;
            let skippedCount = 0;
            const boothsToRemove = []; // Collect booths to remove after adding
            const boothsToSave = []; // Collect booth data for batch save
            
            // First pass: Add all booths to canvas and collect items to remove
            Array.from(boothItems).forEach(function(boothItem, index) {
                // Check if booth already exists on canvas
                const boothId = boothItem.getAttribute('data-booth-id');
                const existingBooth = canvas.querySelector('[data-booth-id="' + boothId + '"]');
                if (existingBooth) {
                    skippedCount++;
                    return; // Skip if already on canvas
                }
                
                // Prepare booth data
                const boothData = {
                    id: boothId,
                    number: boothItem.getAttribute('data-booth-number'),
                    status: boothItem.getAttribute('data-booth-status'),
                    clientId: boothItem.getAttribute('data-client-id') || '',
                    userId: boothItem.getAttribute('data-user-id') || '',
                    categoryId: boothItem.getAttribute('data-category-id') || '',
                    subCategoryId: boothItem.getAttribute('data-sub-category-id') || '',
                    assetId: boothItem.getAttribute('data-asset-id') || '',
                    boothTypeId: boothItem.getAttribute('data-booth-type-id') || ''
                };
                
                // Calculate grid position from clicked position
                const col = index % gridCols;
                const row = Math.floor(index / gridCols);
                const x = startX + (col * spacingX);
                const y = startY + (row * spacingY);
                
                // Snap to grid if enabled
                let finalX = x;
                let finalY = y;
                if (self.snapEnabled) {
                    finalX = Math.round(x / self.gridSize) * self.gridSize;
                    finalY = Math.round(y / self.gridSize) * self.gridSize;
                }
                
                // Add booth to canvas (skip individual save)
                self.addBoothToCanvas(boothData, finalX, finalY, true); // true = skip save
                
                // Collect booth data for batch save
                const boothElement = canvas.querySelector('[data-booth-id="' + boothId + '"]');
                if (boothElement) {
                    const width = parseFloat(boothElement.style.width) || effectiveSettings.width;
                    const height = parseFloat(boothElement.style.height) || effectiveSettings.height;
                    const rotation = parseFloat(boothElement.getAttribute('data-rotation')) || effectiveSettings.rotation;
                    const zIndex = parseFloat(boothElement.style.zIndex) || effectiveSettings.zIndex;
                    const fontSize = parseFloat(boothElement.style.fontSize) || effectiveSettings.fontSize;
                    const borderWidth = parseFloat(boothElement.style.borderWidth) || effectiveSettings.borderWidth;
                    const borderRadius = parseFloat(boothElement.style.borderRadius) || effectiveSettings.borderRadius;
                    const opacity = parseFloat(boothElement.style.opacity) || effectiveSettings.opacity;
                    
                    // Get appearance properties
                    const backgroundColor = boothElement.style.backgroundColor || boothElement.getAttribute('data-background-color') || effectiveSettings.background_color || self.defaultBackgroundColor;
                    const borderColor = boothElement.style.borderColor || boothElement.getAttribute('data-border-color') || effectiveSettings.border_color || self.defaultBorderColor;
                    const textColor = boothElement.style.color || boothElement.getAttribute('data-text-color') || effectiveSettings.text_color || self.defaultTextColor;
                    const fontWeight = boothElement.style.fontWeight || boothElement.getAttribute('data-font-weight') || effectiveSettings.font_weight || self.defaultFontWeight;
                    const fontFamily = boothElement.style.fontFamily || boothElement.getAttribute('data-font-family') || effectiveSettings.font_family || self.defaultFontFamily;
                    const textAlign = boothElement.style.textAlign || boothElement.getAttribute('data-text-align') || effectiveSettings.text_align || self.defaultTextAlign;
                    const boxShadow = boothElement.style.boxShadow || boothElement.getAttribute('data-box-shadow') || effectiveSettings.box_shadow || self.defaultBoxShadow;
                    
                    // Ensure boothId is a valid integer
                    const boothIdInt = parseInt(boothId);
                    if (isNaN(boothIdInt) || boothIdInt <= 0) {
                        console.error('Invalid booth ID:', boothId);
                        return;
                    }
                    
                    // Ensure all numeric values are valid numbers (not NaN) and properly formatted
                    const boothDataToSave = {
                        id: boothIdInt,
                        position_x: (isNaN(finalX) || finalX === null || finalX === undefined) ? null : Number(finalX),
                        position_y: (isNaN(finalY) || finalY === null || finalY === undefined) ? null : Number(finalY),
                        width: (isNaN(width) || width === null || width === undefined) ? null : Number(width),
                        height: (isNaN(height) || height === null || height === undefined) ? null : Number(height),
                        rotation: (isNaN(rotation) || rotation === null || rotation === undefined) ? 0 : Number(rotation),
                        z_index: (isNaN(zIndex) || zIndex === null || zIndex === undefined) ? 10 : parseInt(zIndex),
                        font_size: (isNaN(fontSize) || fontSize === null || fontSize === undefined) ? 14 : parseInt(fontSize),
                        border_width: (isNaN(borderWidth) || borderWidth === null || borderWidth === undefined) ? 2 : parseInt(borderWidth),
                        border_radius: (isNaN(borderRadius) || borderRadius === null || borderRadius === undefined) ? 6 : parseInt(borderRadius),
                        opacity: (isNaN(opacity) || opacity === null || opacity === undefined) ? 1.00 : Number(opacity),
                        // Appearance properties - ensure they're strings or null
                        background_color: backgroundColor ? String(backgroundColor) : null,
                        border_color: borderColor ? String(borderColor) : null,
                        text_color: textColor ? String(textColor) : null,
                        font_weight: fontWeight ? String(fontWeight) : null,
                        font_family: fontFamily ? String(fontFamily) : null,
                        text_align: textAlign ? String(textAlign) : null,
                        box_shadow: boxShadow ? String(boxShadow) : null
                    };
                    
                    console.log('Preparing booth data for save (ID:', boothIdInt, '):', boothDataToSave);
                    boothsToSave.push(boothDataToSave);
                }
                
                // Collect booth item to remove (don't remove during iteration)
                boothsToRemove.push(boothItem);
                
                addedCount++;
            });
            
            // Second pass: Remove all booths from sidebar after adding to canvas
            boothsToRemove.forEach(function(boothItem) {
                self.removeBoothFromSidebar(boothItem);
            });
            
            // Batch save all booths at once (much faster than individual saves)
            if (boothsToSave.length > 0) {
                self.saveBoothsBatch(boothsToSave).then(function(result) {
                    console.log('✅ Zone booths saved successfully:', result);
                }).catch(function(error) {
                    console.error('❌ Error saving zone booths:', error);
                });
            }
            
            // Show notification
            if (addedCount > 0) {
                showNotification(addedCount + ' booth' + (addedCount !== 1 ? 's' : '') + ' from Zone ' + zoneName + ' added to canvas at clicked position' + (skippedCount > 0 ? ' (' + skippedCount + ' already on canvas)' : ''), 'success');
            } else if (skippedCount > 0) {
                showNotification('All booths from Zone ' + zoneName + ' are already on canvas', 'info');
            }
            
            // Update booth count
            if (self.updateBoothCount) {
                self.updateBoothCount();
            }
            
            // Save state
            self.saveState();
        });
    },
    
    // Show modal to add new booth to a zone
    showAddBoothModal: function(zoneName) {
        const self = this;
        
        // Set zone name in modal
        document.getElementById('addBoothZoneName').textContent = zoneName;
        
        // Reset form
        document.getElementById('addBoothForm').reset();
        document.getElementById('boothFrom').value = 1;
        document.getElementById('boothTo').value = 1;
        document.getElementById('boothNumberFormat').value = 2;
        document.getElementById('previewGroup').style.display = 'none';
        
        // Show modal
        $('#addBoothModal').modal('show');
        
        // Update preview when values change
        function updatePreview() {
            const from = parseInt(document.getElementById('boothFrom').value) || 1;
            const to = parseInt(document.getElementById('boothTo').value) || 1;
            const format = parseInt(document.getElementById('boothNumberFormat').value) || 2;
            const previewGroup = document.getElementById('previewGroup');
            const preview = document.getElementById('boothPreview');
            
            if (from > to) {
                preview.innerHTML = '<span style="color: red;">⚠️ "From" number must be less than or equal to "To" number</span>';
                previewGroup.style.display = 'block';
                return;
            }
            
            const count = to - from + 1;
            if (count > 100) {
                preview.innerHTML = '<span style="color: red;">⚠️ Maximum 100 booths can be created at once. Your range would create ' + count + ' booths.</span>';
                previewGroup.style.display = 'block';
                return;
            }
            
            // Show preview of first few and last few booths
            let previewText = '<strong>Will create ' + count + ' booth(s):</strong><br>';
            const maxPreview = 10;
            
            if (count <= maxPreview) {
                // Show all
                for (let i = from; i <= to; i++) {
                    const boothNum = zoneName + String(i).padStart(format, '0');
                    previewText += boothNum + (i < to ? ', ' : '');
                }
            } else {
                // Show first few
                for (let i = from; i <= Math.min(from + 4, to); i++) {
                    const boothNum = zoneName + String(i).padStart(format, '0');
                    previewText += boothNum + ', ';
                }
                previewText += '... ';
                // Show last few
                for (let i = Math.max(to - 4, from + 5); i <= to; i++) {
                    const boothNum = zoneName + String(i).padStart(format, '0');
                    previewText += boothNum + (i < to ? ', ' : '');
                }
            }
            
            preview.innerHTML = previewText;
            previewGroup.style.display = 'block';
        }
        
        // Remove previous event listeners and add new ones for preview
        $('#boothFrom, #boothTo, #boothNumberFormat').off('input change').on('input change', updatePreview);
        updatePreview(); // Initial preview
        
        // Handle form submission
        $('#btnAddBoothSubmit').off('click').on('click', function() {
            const from = parseInt(document.getElementById('boothFrom').value) || 1;
            const to = parseInt(document.getElementById('boothTo').value) || 1;
            const format = parseInt(document.getElementById('boothNumberFormat').value) || 2;
            
            if (from < 1 || to < 1) {
                customAlert('Please enter valid numbers (minimum 1)', 'warning');
                return;
            }
            
            if (from > to) {
                customAlert('"From" number must be less than or equal to "To" number', 'warning');
                return;
            }
            
            const count = to - from + 1;
            if (count > 100) {
                customAlert('Maximum 100 booths can be created at once. Your range would create ' + count + ' booths.', 'warning');
                return;
            }
            
            // Disable button and show loading
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Creating ' + count + ' booths...');
            
            // Get current floor plan ID
            const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
            if (!floorPlanId) {
                customAlert('Please select a floor plan first.', 'warning');
                btn.prop('disabled', false);
                btn.html(originalText);
                return;
            }
            
            // Prepare request data
            const requestData = {
                from: from,
                to: to,
                format: format,
                floor_plan_id: floorPlanId
            };
            
            // Create booths
            fetch('/booths/create-in-zone/' + zoneName, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.status === 200) {
                    // Close modal
                    $('#addBoothModal').modal('hide');
                    
                    // Show success message with details
                    let message = data.message;
                    if (data.created && data.created.length > 0) {
                        if (data.created.length <= 10) {
                            // Show all if 10 or fewer
                            message += '<br><strong>Created:</strong> ' + data.created.map(b => b.booth_number).join(', ');
                        } else {
                            // Show first 5 and last 5 if more than 10
                            const first = data.created.slice(0, 5).map(b => b.booth_number).join(', ');
                            const last = data.created.slice(-5).map(b => b.booth_number).join(', ');
                            message += '<br><strong>Created:</strong> ' + first + ' ... ' + last + ' (' + data.created.length + ' total)';
                        }
                    }
                    if (data.skipped && data.skipped.length > 0) {
                        if (data.skipped.length <= 10) {
                            message += '<br><strong>Skipped (already exist):</strong> ' + data.skipped.join(', ');
                        } else {
                            message += '<br><strong>Skipped:</strong> ' + data.skipped.length + ' booths (already exist)';
                        }
                    }
                    customAlert(message, 'success');
                    
                    // Reload the page to show new booths
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    customAlert(data.message || 'Error creating booths', 'error');
                    btn.prop('disabled', false);
                    btn.html(originalText);
                }
            })
            .catch(function(error) {
                console.error('Error creating booths:', error);
                customAlert('Error creating booths: ' + error.message, 'error');
                btn.prop('disabled', false);
                btn.html(originalText);
            });
        });
    },
    
    // Show modal to add a new zone (creates first booth in the zone)
    showAddZoneModal: function() {
        // Reset form
        document.getElementById('addZoneForm').reset();
        document.getElementById('zoneNameInput').value = '';
        
        // Show modal
        $('#addZoneModal').modal('show');
        
        // Handle submit
        $('#btnAddZoneSubmit').off('click').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            
            let zoneName = document.getElementById('zoneNameInput').value.trim().toUpperCase();
            
            if (!zoneName || !/^[A-Z]{1,3}$/.test(zoneName)) {
                customAlert('Please enter a valid zone name (letters only, 1-3 characters).', 'warning');
                return;
            }
            
            // Disable button and show loading
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Creating...');
            
            // Get current floor plan ID
            const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
            if (!floorPlanId) {
                customAlert('Please select a floor plan first.', 'warning');
                btn.prop('disabled', false);
                btn.html(originalText);
                return;
            }
            
            // Create first booth for the zone (01 with 2-digit format)
            const requestData = {
                from: 1,
                to: 1,
                format: 2,
                floor_plan_id: floorPlanId
            };
            
            fetch('/booths/create-in-zone/' + zoneName, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            })
            .then(function(response) {
                // Parse JSON response regardless of status code
                return response.json().then(function(data) {
                    // Check if response was successful
                    if (!response.ok) {
                        // HTTP error status (422, 409, 500, etc.) - use error message from response
                        throw new Error(data.message || 'Failed to create zone: HTTP ' + response.status);
                    }
                    return data; // Return data if response was OK
                });
            })
            .then(function(data) {
                // Check response status code
                if (data.status === 200) {
                    // Success - booth(s) created
                    $('#addZoneModal').modal('hide');
                    
                    let message = data.message;
                    if (data.created && data.created.length > 0) {
                        message += '<br><strong>Created:</strong> ' + data.created.map(b => b.booth_number).join(', ');
                    }
                    if (data.skipped && data.skipped.length > 0) {
                        message += '<br><strong>Skipped (already exist):</strong> ' + data.skipped.slice(0, 5).join(', ') + (data.skipped.length > 5 ? '...' : '');
                    }
                    if (data.errors && data.errors.length > 0) {
                        message += '<br><strong>Errors:</strong> ' + data.errors.map(e => e.error || e).join('; ');
                    }
                    
                    customAlert(message, 'success');
                    
                    // Reload to show new zone immediately with current floor plan
                    setTimeout(function() {
                        const currentFloorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
                        if (currentFloorPlanId) {
                            window.location.href = '{{ route("booths.index") }}?floor_plan_id=' + currentFloorPlanId;
                        } else {
                            window.location.reload();
                        }
                    }, 1200);
                } else {
                    // Error status (409, 422, 500, etc.)
                    let errorMessage = data.message || 'Error creating zone';
                    if (data.errors && data.errors.length > 0) {
                        errorMessage += '<br>Details: ' + JSON.stringify(data.errors);
                    }
                    customAlert(errorMessage, 'error');
                    btn.prop('disabled', false);
                    btn.html(originalText);
                }
            })
            .catch(function(error) {
                console.error('Error creating zone:', error);
                customAlert('Error creating zone: ' + error.message, 'error');
                btn.prop('disabled', false);
                btn.html(originalText);
            });
        });
    },
    
    // Show modal to delete booths from a zone
    showDeleteBoothModal: function(zoneName) {
        const self = this;
        
        // Set zone name in modal
        document.getElementById('deleteBoothZoneName').textContent = zoneName;
        document.getElementById('deleteAllZoneName').textContent = zoneName;
        
        // Reset form and tabs
        document.getElementById('deleteBoothForm').reset();
        $('#deleteOptionTabs a[href="#delete-all"]').tab('show');
        document.getElementById('confirmDeleteAll').checked = false;
        document.getElementById('deleteFrom').value = 1;
        document.getElementById('deleteTo').value = 1;
        document.getElementById('deleteRangePreviewGroup').style.display = 'none';
        
        // Get all booths in this zone
        const zoneSection = document.querySelector('[data-zone="' + zoneName + '"]');
        if (!zoneSection) {
            customAlert('Zone ' + zoneName + ' not found', 'error');
            return;
        }
        
        const zoneContent = zoneSection.querySelector('.zone-content');
        const boothItems = zoneContent ? zoneContent.querySelectorAll('.booth-number-item') : [];
        
        // Set total count for "Delete All"
        document.getElementById('deleteAllCount').value = boothItems.length + ' booth(s)';
        
        // Populate "Delete Specific" list
        const deleteSpecificList = document.getElementById('deleteSpecificBoothsList');
        deleteSpecificList.innerHTML = '';
        
        if (boothItems.length === 0) {
            deleteSpecificList.innerHTML = '<p class="text-muted text-center">No booths in this zone.</p>';
        } else {
            boothItems.forEach(function(boothItem) {
                const boothId = boothItem.getAttribute('data-booth-id');
                const boothNumber = boothItem.getAttribute('data-booth-number');
                const status = boothItem.getAttribute('data-booth-status');
                
                const checkboxDiv = document.createElement('div');
                checkboxDiv.className = 'form-check mb-2';
                checkboxDiv.innerHTML = `
                    <input class="form-check-input delete-booth-checkbox" type="checkbox" value="${boothId}" id="deleteBooth${boothId}">
                    <label class="form-check-label" for="deleteBooth${boothId}">
                        <strong>${boothNumber}</strong>
                        <span class="badge badge-${status == 1 ? 'success' : status == 2 ? 'info' : status == 3 ? 'warning' : 'secondary'} ml-2">${status == 1 ? 'Available' : status == 2 ? 'Confirmed' : status == 3 ? 'Reserved' : 'Hidden'}</span>
                    </label>
                `;
                deleteSpecificList.appendChild(checkboxDiv);
            });
        }
        
        // Select All / Deselect All handlers
        $('#selectAllBooths').off('click').on('click', function() {
            $('.delete-booth-checkbox').prop('checked', true);
        });
        
        $('#deselectAllBooths').off('click').on('click', function() {
            $('.delete-booth-checkbox').prop('checked', false);
        });
        
        // Update preview for range deletion
        function updateDeleteRangePreview() {
            const from = parseInt(document.getElementById('deleteFrom').value) || 1;
            const to = parseInt(document.getElementById('deleteTo').value) || 1;
            const previewGroup = document.getElementById('deleteRangePreviewGroup');
            const preview = document.getElementById('deleteRangePreview');
            
            if (from > to) {
                preview.innerHTML = '<span style="color: red;">⚠️ "From" number must be less than or equal to "To" number</span>';
                previewGroup.style.display = 'block';
                return;
            }
            
            const count = to - from + 1;
            
            // Show preview of first few and last few booths
            let previewText = '<strong>Will delete ' + count + ' booth(s):</strong><br>';
            const maxPreview = 10;
            
            if (count <= maxPreview) {
                // Show all
                for (let i = from; i <= to; i++) {
                    const boothNum = zoneName + String(i).padStart(2, '0');
                    previewText += boothNum + (i < to ? ', ' : '');
                }
            } else {
                // Show first few
                for (let i = from; i <= Math.min(from + 4, to); i++) {
                    const boothNum = zoneName + String(i).padStart(2, '0');
                    previewText += boothNum + ', ';
                }
                previewText += '... ';
                // Show last few
                for (let i = Math.max(to - 4, from + 5); i <= to; i++) {
                    const boothNum = zoneName + String(i).padStart(2, '0');
                    previewText += boothNum + (i < to ? ', ' : '');
                }
            }
            
            preview.innerHTML = previewText;
            previewGroup.style.display = 'block';
        }
        
        // Add event listeners for range preview
        $('#deleteFrom, #deleteTo').off('input change').on('input change', updateDeleteRangePreview);
        
        // Update preview when switching to range tab
        $('#delete-range-tab').off('shown.bs.tab').on('shown.bs.tab', function() {
            updateDeleteRangePreview();
        });
        
        // Show modal
        $('#deleteBoothModal').modal('show');
        
        // Handle form submission
        $('#btnDeleteBoothSubmit').off('click').on('click', function() {
            const activeTab = $('#deleteOptionTabs .nav-link.active').attr('href');
            const btn = $(this);
            const originalText = btn.html();
            
            let requestData = {};
            let confirmMessage = '';
            
            if (activeTab === '#delete-all') {
                // Delete All mode
                if (!document.getElementById('confirmDeleteAll').checked) {
                    customAlert('Please confirm that you want to delete all booths', 'warning');
                    return;
                }
                
                const count = boothItems.length;
                if (count === 0) {
                    customAlert('No booths to delete in this zone', 'info');
                    return;
                }
                
                confirmMessage = 'Are you sure you want to delete ALL ' + count + ' booths from Zone ' + zoneName + '? This action cannot be undone!';
                requestData = { mode: 'all' };
                
            } else if (activeTab === '#delete-specific') {
                // Delete Specific mode
                const selectedBooths = $('.delete-booth-checkbox:checked');
                
                if (selectedBooths.length === 0) {
                    customAlert('Please select at least one booth to delete', 'warning');
                    return;
                }
                
                const boothIds = Array.from(selectedBooths).map(cb => parseInt(cb.value));
                confirmMessage = 'Are you sure you want to delete ' + selectedBooths.length + ' selected booth(s)? This action cannot be undone!';
                requestData = {
                    mode: 'specific',
                    booth_ids: boothIds
                };
                
            } else if (activeTab === '#delete-range') {
                // Delete Range mode
                const from = parseInt(document.getElementById('deleteFrom').value) || 1;
                const to = parseInt(document.getElementById('deleteTo').value) || 1;
                
                if (from < 1 || to < 1) {
                    customAlert('Please enter valid numbers (minimum 1)', 'warning');
                    return;
                }
                
                if (from > to) {
                    customAlert('"From" number must be less than or equal to "To" number', 'warning');
                    return;
                }
                
                const count = to - from + 1;
                confirmMessage = 'Are you sure you want to delete ' + count + ' booths (from ' + zoneName + String(from).padStart(2, '0') + ' to ' + zoneName + String(to).padStart(2, '0') + ')? This action cannot be undone!';
                requestData = {
                    mode: 'range',
                    from: from,
                    to: to
                };
            }
            
            // Show confirmation dialog
            if (!confirm(confirmMessage)) {
                return;
            }
            
            // Disable button and show loading
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
            
            // Delete booths
            fetch('/booths/delete-in-zone/' + zoneName, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(requestData)
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.status === 200) {
                    // Close modal
                    $('#deleteBoothModal').modal('hide');
                    
                    // Show success message with details
                    let message = data.message;
                    if (data.deleted && data.deleted.length > 0) {
                        if (data.deleted.length <= 10) {
                            message += '<br><strong>Deleted:</strong> ' + data.deleted.join(', ');
                        } else {
                            const first = data.deleted.slice(0, 5).join(', ');
                            const last = data.deleted.slice(-5).join(', ');
                            message += '<br><strong>Deleted:</strong> ' + first + ' ... ' + last + ' (' + data.deleted.length + ' total)';
                        }
                    }
                    if (data.errors && data.errors.length > 0) {
                        message += '<br><strong>Errors:</strong> ' + data.errors.length + ' booth(s) failed to delete.';
                    }
                    customAlert(message, 'success');
                    
                    // Reload the page to reflect changes
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                } else {
                    customAlert(data.message || 'Error deleting booths', 'error');
                    btn.prop('disabled', false);
                    btn.html(originalText);
                }
            })
            .catch(function(error) {
                console.error('Error deleting booths:', error);
                customAlert('Error deleting booths: ' + error.message, 'error');
                btn.prop('disabled', false);
                btn.html(originalText);
            });
        });
    },
    
    // Show booking modal for a booth
    showBookBoothModal: function(boothId, boothNumber, boothElement) {
        const self = this;
        
        // Set booth information in modal
        document.getElementById('bookBoothId').value = boothId;
        document.getElementById('bookBoothNumber').textContent = boothNumber;
        
        // Reset form
        document.getElementById('bookBoothForm').reset();
        document.getElementById('bookBoothId').value = boothId;
        document.getElementById('bookBoothError').style.display = 'none';
        
        // Load existing booking data if available
        const clientId = boothElement ? boothElement.getAttribute('data-client-id') : null;
        const status = boothElement ? boothElement.getAttribute('data-booth-status') : '1';
        
        // If booth is already booked, try to load client data
        if (clientId && clientId !== '' && clientId !== 'null') {
            // Fetch client data to populate form
            fetch('/clients/' + clientId, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function(response) {
                    if (response.ok) {
                        return response.json();
                    }
                    return null;
                })
                .then(function(client) {
                    if (client) {
                        document.getElementById('clientName').value = client.name || '';
                        // Support both numeric and string sex values
                        if (client.sex === 1 || client.sex === '1' || client.sex === 'Male' || client.sex === 'male') {
                            document.getElementById('clientSex').value = '1';
                        } else if (client.sex === 2 || client.sex === '2' || client.sex === 'Female' || client.sex === 'female') {
                            document.getElementById('clientSex').value = '2';
                        } else if (client.sex === 3 || client.sex === '3' || client.sex === 'Other' || client.sex === 'other') {
                            document.getElementById('clientSex').value = '3';
                        } else {
                            document.getElementById('clientSex').value = '';
                        }
                        document.getElementById('clientCompany').value = client.company || '';
                        document.getElementById('clientPosition').value = client.position || '';
                        document.getElementById('clientPhone').value = client.phone_number || '';
                        document.getElementById('clientEmail').value = client.email || '';
                        document.getElementById('clientAddress').value = client.address || '';
                        document.getElementById('clientTaxId').value = client.tax_id || '';
                        document.getElementById('clientWebsite').value = client.website || '';
                        document.getElementById('clientNotes').value = client.notes || '';
                    }
                })
                .catch(function(error) {
                    console.log('Could not load client data:', error);
                });
        }
        
        // Set status
        if (status && status !== '1') {
            document.getElementById('bookingStatus').value = status;
        }
        
        // Reset search and form
        $('#clientSearch').val('');
        $('#selectedClientId').val('');
        $('#clientSearchResults').hide();
        $('#btnClearSearch').hide();
        $('#bookBoothForm')[0].reset();
        document.getElementById('bookBoothId').value = boothId;
        
        // Client Search Functionality - Define functions first
        let searchTimeout;
        
        function searchClients(query) {
            if (!query || query.length < 2) {
                $('#clientSearchResults').hide();
                return;
            }
            
            console.log('Searching for clients with query:', query);
            
            $.ajax({
                url: '{{ route("clients.search") }}',
                method: 'GET',
                data: { q: query },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function(clients) {
                    console.log('Search results received:', clients);
                    const resultsDiv = $('#clientSearchResults');
                    const resultsList = $('#clientSearchResultsList');
                    resultsList.empty();
                    
                    if (!clients || clients.length === 0) {
                        resultsList.html('<div class="list-group-item text-muted">No clients found. You can create a new client by filling in the form below.</div>');
                        resultsDiv.show();
                        return;
                    }
                    
                    clients.forEach(function(client) {
                        const item = $('<a href="#" class="list-group-item list-group-item-action"></a>')
                            .html('<div class="d-flex justify-content-between align-items-center">' +
                                '<div>' +
                                '<strong>' + (client.company || client.name) + '</strong>' +
                                (client.company && client.name ? ' - ' + client.name : '') +
                                '<br><small class="text-muted">' +
                                (client.email ? client.email : '') +
                                (client.phone_number ? (client.email ? ' | ' : '') + client.phone_number : '') +
                                '</small>' +
                                '</div>' +
                                '<i class="fas fa-check-circle text-success"></i>' +
                                '</div>')
                            .on('click', function(e) {
                                e.preventDefault();
                                selectClient(client);
                            });
                        resultsList.append(item);
                    });
                    
                    resultsDiv.show();
                    console.log('Search results displayed');
                },
                error: function(xhr, status, error) {
                    console.error('Error searching clients:', error, xhr);
                    const errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error searching clients. Please try again.';
                    $('#clientSearchResultsList').html('<div class="list-group-item text-danger">' + errorMsg + '</div>');
                    $('#clientSearchResults').show();
                }
            });
        }
        
        function selectClient(client) {
            // Set selected client ID
            $('#selectedClientId').val(client.id);
            
            // Fill in form fields
            $('#clientName').val(client.name || '');
            $('#clientCompany').val(client.company || '');
            $('#clientPhone').val(client.phone_number || '');
            $('#clientEmail').val(client.email || '');
            $('#clientAddress').val(client.address || '');
            $('#clientPosition').val(client.position || '');
            $('#clientSex').val(client.sex || '');
            $('#clientTaxId').val(client.tax_id || '');
            $('#clientWebsite').val(client.website || '');
            $('#clientNotes').val(client.notes || '');
            
            // Hide search results and show clear button
            $('#clientSearchResults').hide();
            $('#clientSearch').val(client.company || client.name);
            $('#btnClearSearch').show();
            
            // Show success message
            if (typeof customAlert !== 'undefined') {
                customAlert('Client "' + (client.company || client.name) + '" selected. You can edit the information if needed.', 'success');
            }
        }
        
        // Unbind existing handlers to prevent duplicates
        $('#clientSearch').off('input keyup');
        $('#btnSearchClient').off('click');
        $('#btnClearSearch').off('click');
        
        // Bind event handlers
        $('#clientSearch').on('input keyup', function(e) {
            const query = $(this).val().trim();
            
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                $('#clientSearchResults').hide();
                return;
            }
            
            searchTimeout = setTimeout(function() {
                searchClients(query);
            }, 300);
        });
        
        // Test if handlers are attached (debug)
        console.log('Client search handlers attached');
        
        $('#btnSearchClient').on('click', function() {
            const query = $('#clientSearch').val().trim();
            if (query.length >= 2) {
                searchClients(query);
            }
        });
        
        $('#btnClearSearch').on('click', function() {
            $('#clientSearch').val('');
            $('#selectedClientId').val('');
            $('#clientSearchResults').hide();
            $(this).hide();
            // Clear form fields
            $('#clientName').val('');
            $('#clientCompany').val('');
            $('#clientPhone').val('');
            $('#clientEmail').val('');
            $('#clientAddress').val('');
            $('#clientPosition').val('');
            $('#clientSex').val('');
            $('#clientTaxId').val('');
            $('#clientWebsite').val('');
            $('#clientNotes').val('');
        });
        
        // Show modal
        $('#bookBoothModal').modal('show');
        
        // Handle form submission
        $('#btnBookBoothSubmit').off('click').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            
            // Validate form
            const form = document.getElementById('bookBoothForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Get form data - ALL fields are now collected
            const selectedClientId = document.getElementById('selectedClientId').value;
            const formData = {
                booth_id: parseInt(document.getElementById('bookBoothId').value),
                client_id: selectedClientId ? parseInt(selectedClientId) : null,
                name: document.getElementById('clientName').value.trim(),
                sex: document.getElementById('clientSex').value ? parseInt(document.getElementById('clientSex').value) : null,
                company: document.getElementById('clientCompany').value.trim(),
                position: document.getElementById('clientPosition').value.trim(),
                phone_number: document.getElementById('clientPhone').value.trim(),
                email: document.getElementById('clientEmail').value.trim(),
                address: document.getElementById('clientAddress').value.trim(),
                tax_id: document.getElementById('clientTaxId').value.trim() || null,
                website: document.getElementById('clientWebsite').value.trim() || null,
                notes: document.getElementById('clientNotes').value.trim() || null,
                status: parseInt(document.getElementById('bookingStatus').value)
            };
            
            // Validate ALL required fields (Name, Company, Phone, Email, Address)
            if (!formData.name || !formData.company || !formData.phone_number || !formData.email || !formData.address) {
                const errorDiv = document.getElementById('bookBoothError');
                errorDiv.textContent = 'Please fill in all required fields: Name, Company, Phone Number, Email Address, and Address';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(formData.email)) {
                const errorDiv = document.getElementById('bookBoothError');
                errorDiv.textContent = 'Please enter a valid email address';
                errorDiv.style.display = 'block';
                return;
            }
            
            // Disable button and show loading
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            // Hide error message
            document.getElementById('bookBoothError').style.display = 'none';
            
            // Save booking
            fetch('/booths/book-booth', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.status === 200) {
                    // Close modal
                    $('#bookBoothModal').modal('hide');
                    
                    // Show success message
                    customAlert('Booth ' + boothNumber + ' has been booked successfully!', 'success');
                    
                    // Update booth element on canvas
                    if (boothElement) {
                        // Update status class
                        boothElement.className = 'dropped-booth status-' + formData.status;
                        boothElement.setAttribute('data-booth-status', formData.status);
                        if (data.client_id) {
                            boothElement.setAttribute('data-client-id', data.client_id);
                        }
                        
                        // Update visual appearance based on status
                        // Status colors are already defined in CSS, but we can add a visual indicator
                        boothElement.style.borderWidth = '3px';
                        setTimeout(function() {
                            boothElement.style.borderWidth = '';
                        }, 1000);
                    }
                    
                    // Reload page after a short delay to reflect all changes
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    // Show error
                    const errorDiv = document.getElementById('bookBoothError');
                    errorDiv.textContent = data.message || 'Error booking booth';
                    errorDiv.style.display = 'block';
                    btn.prop('disabled', false);
                    btn.html(originalText);
                }
            })
            .catch(function(error) {
                console.error('Error booking booth:', error);
                const errorDiv = document.getElementById('bookBoothError');
                errorDiv.textContent = 'Error booking booth: ' + error.message;
                errorDiv.style.display = 'block';
                btn.prop('disabled', false);
                btn.html(originalText);
            });
        });
    },
    
    // Show context menu for booth (right-click)
    showBoothContextMenu: function(event, boothId, boothNumber, boothElement) {
        const self = this;
        
        // Remove any existing context menu
        const existingMenu = document.getElementById('boothContextMenu');
        if (existingMenu) {
            existingMenu.remove();
        }
        
        // Create context menu
        const contextMenu = document.createElement('div');
        contextMenu.id = 'boothContextMenu';
        contextMenu.className = 'booth-context-menu';
        contextMenu.innerHTML = `
            <div class="context-menu-item" data-action="set-price">
                <i class="fas fa-dollar-sign"></i> Set Price
            </div>
            <div class="context-menu-item" data-action="book">
                <i class="fas fa-calendar-check"></i> Book Booth
            </div>
        `;
        
        // Position menu at cursor (using fixed positioning)
        const x = event.clientX;
        const y = event.clientY;
        contextMenu.style.position = 'fixed';
        contextMenu.style.left = x + 'px';
        contextMenu.style.top = y + 'px';
        contextMenu.style.zIndex = '10000';
        
        // Add to body
        document.body.appendChild(contextMenu);
        
        // Adjust position if menu goes off screen
        setTimeout(function() {
            const rect = contextMenu.getBoundingClientRect();
            const windowWidth = window.innerWidth;
            const windowHeight = window.innerHeight;
            
            let newX = x;
            let newY = y;
            
            // Adjust horizontal position
            if (rect.right > windowWidth) {
                newX = windowWidth - rect.width - 5;
            }
            if (rect.left < 0) {
                newX = 5;
            }
            
            // Adjust vertical position
            if (rect.bottom > windowHeight) {
                newY = windowHeight - rect.height - 5;
            }
            if (rect.top < 0) {
                newY = 5;
            }
            
            contextMenu.style.left = newX + 'px';
            contextMenu.style.top = newY + 'px';
        }, 0);
        
        // Handle menu item clicks
        contextMenu.querySelectorAll('.context-menu-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                const action = this.getAttribute('data-action');
                
                if (action === 'set-price') {
                    // Get current price
                    let currentPrice = 500; // Default
                    if (typeof window.boothsData !== 'undefined' && Array.isArray(window.boothsData)) {
                        const boothData = window.boothsData.find(b => b.id == boothId);
                        if (boothData && boothData.price !== undefined) {
                            currentPrice = parseFloat(boothData.price) || 500;
                        }
                    }
                    
                    // Show SweetAlert2 dialog for price input
                    Swal.fire({
                        title: 'Set Price for Booth ' + boothNumber,
                        html: '<input type="number" id="boothPriceInput" class="swal2-input" value="' + currentPrice.toFixed(2) + '" min="0" step="0.01" placeholder="Enter price">',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Set Price',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#aaa',
                        focusConfirm: false,
                        preConfirm: function() {
                            const priceInput = document.getElementById('boothPriceInput');
                            const price = parseFloat(priceInput.value);
                            
                            if (isNaN(price) || price < 0) {
                                Swal.showValidationMessage('Please enter a valid price (must be >= 0)');
                                return false;
                            }
                            
                            return price;
                        }
                    }).then(function(result) {
                        if (result.isConfirmed && result.value !== false) {
                            const newPrice = result.value;
                            
                            // Get current position and properties
                            const x = parseFloat(boothElement.style.left) || 0;
                            const y = parseFloat(boothElement.style.top) || 0;
                            const width = parseFloat(boothElement.style.width) || 80;
                            const height = parseFloat(boothElement.style.height) || 50;
                            const rotation = parseFloat(boothElement.getAttribute('data-rotation')) || 0;
                            const zIndex = parseFloat(boothElement.style.zIndex) || 10;
                            const fontSize = parseFloat(boothElement.style.fontSize) || 14;
                            const borderWidth = parseFloat(boothElement.style.borderWidth) || 2;
                            const borderRadius = parseFloat(boothElement.style.borderRadius) || 6;
                            const opacity = parseFloat(boothElement.style.opacity) || 1.00;
                            
                            // Save price using saveBoothPosition
                            self.saveBoothPosition(boothId, x, y, width, height, rotation, zIndex, fontSize, borderWidth, borderRadius, opacity, undefined, undefined, undefined, undefined, undefined, undefined, undefined, newPrice)
                                .then(function(response) {
                                    // Update boothsData if available
                                    if (typeof window.boothsData !== 'undefined' && Array.isArray(window.boothsData)) {
                                        const boothData = window.boothsData.find(b => b.id == boothId);
                                        if (boothData) {
                                            boothData.price = newPrice;
                                        }
                                    }
                                    
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Price Updated',
                                        text: 'Price for Booth ' + boothNumber + ' has been set to $' + newPrice.toFixed(2),
                                        timer: 2000,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'bottom-right'
                                    });
                                })
                                .catch(function(error) {
                                    console.error('Error saving price:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to save price. Please try again.',
                                        timer: 3000,
                                        showConfirmButton: false,
                                        toast: true,
                                        position: 'bottom-right'
                                    });
                                });
                        }
                    });
                } else if (action === 'book') {
                    self.showBookBoothModal(boothId, boothNumber, boothElement);
                }
                
                // Remove menu
                contextMenu.remove();
                document.removeEventListener('click', closeMenu);
                document.removeEventListener('contextmenu', closeMenu);
            });
        });
        
        // Close menu when clicking outside or right-clicking elsewhere
        const closeMenu = function(e) {
            if (contextMenu && !contextMenu.contains(e.target)) {
                contextMenu.remove();
                document.removeEventListener('click', closeMenu);
                document.removeEventListener('contextmenu', closeMenu);
            }
        };
        
        // Use setTimeout to avoid immediate close (browser's default context menu)
        setTimeout(function() {
            document.addEventListener('click', closeMenu, true);
            document.addEventListener('contextmenu', closeMenu, true);
        }, 100);
    },
    
    // Helper: lock a single booth element
    lockBoothElement: function(booth) {
        if (!booth) return;
        booth.classList.add('locked');
        booth.setAttribute('data-locked', 'true');
        // Hide resize and rotate handles
        const handles = booth.querySelectorAll('.resize-handle');
        handles.forEach(function(handle) {
            handle.style.display = 'none';
        });
        const rotateHandle = booth.querySelector('.rotate-handle');
        if (rotateHandle) {
            rotateHandle.style.display = 'none';
        }
    },
    
    // Helper: unlock a single booth element
    unlockBoothElement: function(booth) {
        if (!booth) return;
        booth.classList.remove('locked');
        booth.removeAttribute('data-locked');
        // If currently selected, show handles again
        if (booth.classList.contains('selected')) {
            const handles = booth.querySelectorAll('.resize-handle');
            handles.forEach(function(handle) {
                handle.style.display = 'block';
            });
            const rotateHandle = booth.querySelector('.rotate-handle');
            if (rotateHandle) {
                rotateHandle.style.display = 'flex';
            }
        }
    },
    
    // Open zone settings modal to adjust all booths in a zone
    openZoneSettings: function(zoneName) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Find all booths on canvas that belong to this zone
        const zoneBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        
        if (zoneBooths.length === 0) {
            showNotification('No booths from Zone ' + zoneName + ' found on canvas', 'warning');
            return;
        }
        
        // Get current floor plan ID
        const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
        
        // Load zone settings from database first (floor-plan-specific), then use saved values or fallback to current booth values
        const zoneSettingsUrl = '/booths/zone-settings/' + encodeURIComponent(zoneName) + 
            (floorPlanId ? '?floor_plan_id=' + floorPlanId : '');
        fetch(zoneSettingsUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            let currentWidth, currentHeight, currentRotation, currentZIndex, currentBorderRadius, currentBorderWidth, currentOpacity, currentPrice;
            
            // Use saved zone settings if available, otherwise use current booth values
            if (data.status === 200 && data.settings) {
                // Get current floor plan ID for cache key
                const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
                const cacheKey = floorPlanId ? floorPlanId + '_' + zoneName : 'global_' + zoneName;
                // Update cache with loaded settings (floor-plan-specific key)
                self.zoneSettingsCache[cacheKey] = data.settings;
                
                currentWidth = data.settings.width || self.defaultBoothWidth;
                currentHeight = data.settings.height || self.defaultBoothHeight;
                currentRotation = data.settings.rotation || 0;
                currentZIndex = data.settings.zIndex || 10;
                currentBorderRadius = data.settings.borderRadius || 6;
                currentBorderWidth = data.settings.borderWidth || 2;
                currentOpacity = data.settings.opacity || 1.00;
                currentPrice = data.settings.price || 500;
            } else {
                // Fallback: Get values from first booth
                const firstBooth = zoneBooths[0];
                currentWidth = parseFloat(firstBooth.style.width) || self.defaultBoothWidth;
                currentHeight = parseFloat(firstBooth.style.height) || self.defaultBoothHeight;
                let rotation = parseFloat(firstBooth.getAttribute('data-rotation')) || 0;
                if (!firstBooth.getAttribute('data-rotation')) {
                    const transform = firstBooth.style.transform || '';
                    const match = transform.match(/rotate\(([^)]+)\)/);
                    if (match) {
                        rotation = parseFloat(match[1]) || 0;
                    }
                }
                currentRotation = rotation;
                currentZIndex = parseFloat(firstBooth.style.zIndex) || 10;
                currentBorderRadius = parseFloat(firstBooth.style.borderRadius) || 6;
                currentBorderWidth = parseFloat(firstBooth.style.borderWidth) || 2;
                currentOpacity = parseFloat(firstBooth.style.opacity) || 1.00;
                currentPrice = 500; // Default price if not in settings
            }
            
            self.showZoneSettingsModal(zoneName, zoneBooths.length, {
                width: currentWidth,
                height: currentHeight,
                rotation: currentRotation,
                zIndex: currentZIndex,
                borderRadius: currentBorderRadius,
                borderWidth: currentBorderWidth,
                opacity: currentOpacity,
                price: currentPrice || 500
            });
        })
        .catch(function(error) {
            console.error('Error loading zone settings:', error);
            // Fallback: Use current booth values if database load fails
            const firstBooth = zoneBooths[0];
            const currentWidth = parseFloat(firstBooth.style.width) || self.defaultBoothWidth;
            const currentHeight = parseFloat(firstBooth.style.height) || self.defaultBoothHeight;
            let currentRotation = parseFloat(firstBooth.getAttribute('data-rotation')) || 0;
            if (!firstBooth.getAttribute('data-rotation')) {
                const transform = firstBooth.style.transform || '';
                const match = transform.match(/rotate\(([^)]+)\)/);
                if (match) {
                    currentRotation = parseFloat(match[1]) || 0;
                }
            }
            const currentZIndex = parseFloat(firstBooth.style.zIndex) || 10;
            const currentBorderRadius = parseFloat(firstBooth.style.borderRadius) || 6;
            const currentBorderWidth = parseFloat(firstBooth.style.borderWidth) || 2;
            const currentOpacity = parseFloat(firstBooth.style.opacity) || 1.00;
            const currentPrice = 500; // Default price in fallback case
            
            self.showZoneSettingsModal(zoneName, zoneBooths.length, {
                width: currentWidth,
                height: currentHeight,
                rotation: currentRotation,
                zIndex: currentZIndex,
                borderRadius: currentBorderRadius,
                borderWidth: currentBorderWidth,
                opacity: currentOpacity,
                price: currentPrice
            });
        });
    },
    
    // Show zone settings modal with provided values
    showZoneSettingsModal: function(zoneName, boothCount, settings) {
        const self = this;
        
        // Create settings modal
        var modalHtml = '<div style="text-align: left; max-width: 500px; margin: 0 auto;">';
        modalHtml += '<p style="margin-bottom: 15px; color: #666;">';
        modalHtml += 'Found <strong>' + boothCount + '</strong> booth(s) in Zone ' + zoneName + ' on canvas.';
        modalHtml += '<br><small>Changes will apply to all booths in this zone and be saved for future use.</small>';
        modalHtml += '</p>';
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-arrows-alt-h"></i> Width (px)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneWidth" class="swal2-input" value="' + settings.width + '" min="5" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-arrows-alt-v"></i> Height (px)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneHeight" class="swal2-input" value="' + settings.height + '" min="5" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-redo"></i> Rotation (degrees)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneRotation" class="swal2-input" value="' + settings.rotation + '" min="-360" max="360" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-layer-group"></i> Z-Index';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneZIndex" class="swal2-input" value="' + settings.zIndex + '" min="1" max="1000" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-circle"></i> Border Radius (px)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneBorderRadius" class="swal2-input" value="' + settings.borderRadius + '" min="0" max="50" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-border-style"></i> Border Width (px)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneBorderWidth" class="swal2-input" value="' + settings.borderWidth + '" min="0" max="10" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div style="margin-bottom: 15px;">';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-adjust"></i> Opacity (0.0 - 1.0)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneOpacity" class="swal2-input" value="' + settings.opacity + '" min="0" max="1" step="0.1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '<div style="margin-bottom: 15px;">';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-dollar-sign"></i> Default Price (for new booths in this zone)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zonePrice" class="swal2-input" value="' + (settings.price || 500) + '" min="0" step="0.01" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '<p style="font-size: 11px; color: #999; margin-top: 10px;">';
        modalHtml += '<i class="fas fa-info-circle"></i> All fields are required. Values will be applied to all booths in this zone and saved for future use. Price will be used when creating new booths in this zone.';
        modalHtml += '</p>';
        modalHtml += '</div>';
        
        Swal.fire({
            title: 'Zone ' + zoneName + ' Settings',
            html: modalHtml,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Apply to All Booths',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ff9800',
            width: '600px',
            preConfirm: () => {
                const width = document.getElementById('zoneWidth').value;
                const height = document.getElementById('zoneHeight').value;
                const rotation = document.getElementById('zoneRotation').value;
                const zIndex = document.getElementById('zoneZIndex').value;
                const borderRadius = document.getElementById('zoneBorderRadius').value;
                const borderWidth = document.getElementById('zoneBorderWidth').value;
                const opacity = document.getElementById('zoneOpacity').value;
                const price = document.getElementById('zonePrice').value;
                
                // Validate all fields
                if (!width || !height || rotation === '' || !zIndex || borderRadius === '' || borderWidth === '' || opacity === '' || !price) {
                    Swal.showValidationMessage('Please fill in all fields');
                    return false;
                }
                
                return {
                    width: parseFloat(width),
                    height: parseFloat(height),
                    rotation: parseFloat(rotation),
                    zIndex: parseInt(zIndex),
                    borderRadius: parseFloat(borderRadius),
                    borderWidth: parseFloat(borderWidth),
                    opacity: parseFloat(opacity),
                    price: parseFloat(price)
                };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                self.applyZoneSettings(zoneName, result.value);
            }
        });
    },
    
    // Apply settings to all booths in a zone
    applyZoneSettings: function(zoneName, settings) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Find all booths on canvas that belong to this zone
        const zoneBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        
        if (zoneBooths.length === 0) {
            showNotification('No booths from Zone ' + zoneName + ' found on canvas', 'warning');
            return;
        }
        
        const boothsToSave = [];
        let updatedCount = 0;
        
        // Apply settings to each booth
        zoneBooths.forEach(function(boothElement) {
            // Apply new values
            const width = Math.max(5, settings.width);
            const height = Math.max(5, settings.height);
            let rotation = settings.rotation;
            const zIndex = Math.max(1, Math.min(1000, settings.zIndex));
            const borderRadius = Math.max(0, Math.min(50, settings.borderRadius));
            const borderWidth = Math.max(0, Math.min(10, settings.borderWidth));
            const opacity = Math.max(0, Math.min(1, settings.opacity));
            
            // Normalize rotation to -360 to 360 range
            rotation = rotation % 360;
            if (rotation > 360) rotation -= 360;
            if (rotation < -360) rotation += 360;
            
            // Apply to element
            boothElement.style.width = width + 'px';
            boothElement.style.height = height + 'px';
            boothElement.style.transform = 'rotate(' + rotation + 'deg)';
            boothElement.style.zIndex = zIndex;
            boothElement.style.borderRadius = borderRadius + 'px';
            boothElement.style.borderWidth = borderWidth + 'px';
            boothElement.style.opacity = opacity;
            
            // Update attributes
            boothElement.setAttribute('data-width', width);
            boothElement.setAttribute('data-height', height);
            boothElement.setAttribute('data-rotation', rotation);
            boothElement.setAttribute('data-z-index', zIndex);
            boothElement.setAttribute('data-border-radius', borderRadius);
            boothElement.setAttribute('data-border-width', borderWidth);
            boothElement.setAttribute('data-opacity', opacity);
            
            // Update resize handles and rotation indicator
            self.updateResizeHandlesSize(boothElement);
            self.updateRotationIndicator(boothElement);
            
            // Recalculate font size based on new width
            const userFontSize = parseFloat(boothElement.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const calculatedFontSize = Math.min(userFontSize, Math.max(8, width * 0.45));
            boothElement.style.fontSize = calculatedFontSize + 'px';
            boothElement.setAttribute('data-calculated-font-size', calculatedFontSize);
            
            // Collect booth data for batch save
            const boothId = boothElement.getAttribute('data-booth-id');
            const x = parseFloat(boothElement.style.left) || 0;
            const y = parseFloat(boothElement.style.top) || 0;
            const fontSize = calculatedFontSize;
            
            boothsToSave.push({
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
            
            updatedCount++;
        });
        
        // Get current floor plan ID
        const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
        if (!floorPlanId) {
            customAlert('Please select a floor plan first before saving zone settings.', 'warning');
            return;
        }
        
        // Save zone settings to database (floor-plan-specific)
        fetch('/booths/zone-settings/' + encodeURIComponent(zoneName), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                width: settings.width,
                height: settings.height,
                rotation: settings.rotation,
                zIndex: settings.zIndex,
                borderRadius: settings.borderRadius,
                borderWidth: settings.borderWidth,
                opacity: settings.opacity,
                price: settings.price,
                floor_plan_id: floorPlanId
            })
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.status === 200) {
                // Get current floor plan ID for cache key
                const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
                const cacheKey = floorPlanId ? floorPlanId + '_' + zoneName : 'global_' + zoneName;
                
                // Clear cache so new settings are used immediately (floor-plan-specific)
                delete self.zoneSettingsCache[cacheKey];
                // Update cache with new settings (floor-plan-specific key)
                self.zoneSettingsCache[cacheKey] = {
                    width: settings.width,
                    height: settings.height,
                    rotation: settings.rotation,
                    zIndex: settings.zIndex,
                    borderRadius: settings.borderRadius,
                    borderWidth: settings.borderWidth,
                    opacity: settings.opacity,
                    price: settings.price
                };
            }
        })
        .catch(function(error) {
            console.error('Error saving zone settings to database:', error);
        });
        
        // Batch save all booth changes
        if (boothsToSave.length > 0) {
            self.saveBoothsBatch(boothsToSave).then(function(result) {
                showNotification('Successfully updated ' + updatedCount + ' booth(s) in Zone ' + zoneName + ' with new settings. Settings saved for future use.', 'success');
            }).catch(function(error) {
                console.error('Error saving booth positions:', error);
                showNotification('Settings applied to booths but failed to save positions to database', 'warning');
            });
        }
        
        // Update info toolbar if any of these booths are selected
        if (self.selectedBooths && self.selectedBooths.length > 0) {
            const selectedBooth = self.selectedBooths[0];
            if (selectedBooth && selectedBooth.getAttribute('data-booth-zone') === zoneName) {
                self.updateInfoToolbar(selectedBooth);
            }
        }
        
        // Save state for undo/redo
        self.saveState();
    },
    
    // Clear all booths from a zone (return them to Booth Number Area)
    clearZoneBooths: function(zoneName) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Find all booths on canvas that belong to this zone
        const zoneBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        
        if (zoneBooths.length === 0) {
            showNotification('No booths from Zone ' + zoneName + ' found on canvas', 'info');
            return;
        }
        
        // Confirm action
        Swal.fire({
            title: 'Clear Zone ' + zoneName + '?',
            text: 'This will return ' + zoneBooths.length + ' booth(s) from Zone ' + zoneName + ' back to the Booth Number Area.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Clear Zone',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                self.executeClearZone(zoneName);
            }
        });
    },
    
    // Execute clearing zone booths
    executeClearZone: function(zoneName) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Find all booths on canvas that belong to this zone
        const zoneBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        
        if (zoneBooths.length === 0) return;
        
        const boothsToSave = [];
        let clearedCount = 0;
        
        // Process each booth
        zoneBooths.forEach(function(boothElement) {
            const boothId = boothElement.getAttribute('data-booth-id');
            const boothNumber = boothElement.getAttribute('data-booth-number') || '';
            const boothStatus = boothElement.getAttribute('data-booth-status') || '1';
            const clientId = boothElement.getAttribute('data-client-id') || '';
            const userId = boothElement.getAttribute('data-user-id') || '';
            const categoryId = boothElement.getAttribute('data-category-id') || '';
            const subCategoryId = boothElement.getAttribute('data-sub-category-id') || '';
            const assetId = boothElement.getAttribute('data-asset-id') || '';
            const boothTypeId = boothElement.getAttribute('data-booth-type-id') || '';
            
            // Add booth back to sidebar
            if (boothId && boothNumber) {
                self.addBoothToSidebar({
                    id: boothId,
                    number: boothNumber,
                    status: boothStatus,
                    zone: zoneName,
                    clientId: clientId,
                    userId: userId,
                    categoryId: categoryId,
                    subCategoryId: subCategoryId,
                    assetId: assetId,
                    boothTypeId: boothTypeId
                });
            }
            
            // Prepare data to clear positions in database
            boothsToSave.push({
                id: parseInt(boothId),
                position_x: null,
                position_y: null,
                width: null,
                height: null,
                rotation: null,
                z_index: null,
                font_size: null,
                border_width: null,
                border_radius: null,
                opacity: null
            });
            
            // Remove from canvas
            boothElement.remove();
            clearedCount++;
        });
        
        // Clear selection if any cleared booths were selected
        if (self.selectedBooths) {
            self.selectedBooths = self.selectedBooths.filter(function(booth) {
                return booth.getAttribute('data-booth-zone') !== zoneName;
            });
            if (self.selectedBooths.length === 0) {
                self.updateInfoToolbar(null);
            }
        }
        
        // Batch save to clear positions in database
        if (boothsToSave.length > 0) {
            self.saveBoothsBatch(boothsToSave).then(function(result) {
                showNotification('Successfully cleared ' + clearedCount + ' booth(s) from Zone ' + zoneName + ' and returned to Booth Number Area', 'success');
            }).catch(function(error) {
                console.error('Error clearing zone positions:', error);
                showNotification('Booths removed from canvas but failed to clear positions in database', 'warning');
            });
        }
        
        // Sync sidebar
        self.syncSidebarWithCanvas();
        
        // Update booth count
        if (self.updateBoothCount) {
            self.updateBoothCount();
        }
        
        // Save state for undo/redo
        self.saveState();
    },
    
    // Helper function to calculate bounding box of a rotated rectangle
    getRotatedBoundingBox: function(x, y, width, height, rotation) {
        // Convert rotation to radians
        const angle = (rotation * Math.PI) / 180;
        const cos = Math.cos(angle);
        const sin = Math.sin(angle);
        
        // Calculate center of rectangle
        const centerX = x + width / 2;
        const centerY = y + height / 2;
        
        // Calculate half dimensions
        const halfWidth = width / 2;
        const halfHeight = height / 2;
        
        // Four corners of the rectangle (relative to center)
        const corners = [
            { x: -halfWidth, y: -halfHeight },
            { x: halfWidth, y: -halfHeight },
            { x: halfWidth, y: halfHeight },
            { x: -halfWidth, y: halfHeight }
        ];
        
        // Rotate each corner around the center
        const rotatedCorners = corners.map(function(corner) {
            return {
                x: centerX + corner.x * cos - corner.y * sin,
                y: centerY + corner.x * sin + corner.y * cos
            };
        });
        
        // Find min/max of rotated corners
        let minX = Infinity;
        let minY = Infinity;
        let maxX = -Infinity;
        let maxY = -Infinity;
        
        rotatedCorners.forEach(function(corner) {
            minX = Math.min(minX, corner.x);
            minY = Math.min(minY, corner.y);
            maxX = Math.max(maxX, corner.x);
            maxY = Math.max(maxY, corner.y);
        });
        
        return { minX: minX, minY: minY, maxX: maxX, maxY: maxY };
    },
    
    // Zoom to fit all booths in a zone
    zoomToZone: function(zoneName) {
        const self = this;
        const canvas = document.getElementById('print');
        const container = document.getElementById('printContainer');
        if (!canvas || !container || !self.panzoomInstance) return;
        
        // Find all booths on canvas that belong to this zone
        const zoneBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        
        if (zoneBooths.length === 0) {
            showNotification('No booths from Zone ' + zoneName + ' found on canvas', 'warning');
            return;
        }
        
        // Calculate bounding box of all booths in the zone (accounting for rotation)
        let minX = Infinity;
        let minY = Infinity;
        let maxX = -Infinity;
        let maxY = -Infinity;
        
        zoneBooths.forEach(function(boothElement) {
            const x = parseFloat(boothElement.style.left) || 0;
            const y = parseFloat(boothElement.style.top) || 0;
            const width = parseFloat(boothElement.style.width) || 80;
            const height = parseFloat(boothElement.style.height) || 50;
            
            // Get rotation angle
            let rotation = parseFloat(boothElement.getAttribute('data-rotation')) || 0;
            if (!boothElement.getAttribute('data-rotation')) {
                const transform = boothElement.style.transform || '';
                const match = transform.match(/rotate\(([^)]+)\)/);
                if (match) {
                    rotation = parseFloat(match[1]) || 0;
                }
            }
            
            // Calculate bounding box accounting for rotation
            if (rotation !== 0 && rotation % 360 !== 0) {
                const bbox = self.getRotatedBoundingBox(x, y, width, height, rotation);
                minX = Math.min(minX, bbox.minX);
                minY = Math.min(minY, bbox.minY);
                maxX = Math.max(maxX, bbox.maxX);
                maxY = Math.max(maxY, bbox.maxY);
            } else {
                // No rotation, use simple bounding box
                minX = Math.min(minX, x);
                minY = Math.min(minY, y);
                maxX = Math.max(maxX, x + width);
                maxY = Math.max(maxY, y + height);
            }
        });
        
        // Add padding around the bounding box
        const padding = 50;
        minX -= padding;
        minY -= padding;
        maxX += padding;
        maxY += padding;
        
        // Calculate center and dimensions of the bounding box
        const centerX = (minX + maxX) / 2;
        const centerY = (minY + maxY) / 2;
        const boxWidth = maxX - minX;
        const boxHeight = maxY - minY;
        
        // Get container dimensions
        let containerWidth = container.clientWidth;
        let containerHeight = container.clientHeight;
        
        // Account for sidebar if visible
        const sidebar = document.getElementById('designerSidebar');
        if (sidebar && !sidebar.classList.contains('hidden') && !sidebar.classList.contains('collapsed')) {
            const sidebarWidth = sidebar.offsetWidth || 280;
            containerWidth = containerWidth - sidebarWidth;
        } else if (sidebar && sidebar.classList.contains('collapsed')) {
            containerWidth = containerWidth - 50;
        }
        
        // Ensure we have valid dimensions
        if (containerWidth <= 0) containerWidth = container.clientWidth;
        if (containerHeight <= 0) containerHeight = container.clientHeight;
        
        // Calculate scale to fit the bounding box
        const scaleX = containerWidth / boxWidth;
        const scaleY = containerHeight / boxHeight;
        const fitScale = Math.min(scaleX, scaleY) * 0.9; // 90% to add some padding
        
        // Apply zoom first (similar to fitCanvasToView)
        if (self.panzoomInstance.zoom) {
            self.panzoomInstance.zoom(fitScale, { animate: true });
        }
        
        // Wait for zoom to complete, then center
        setTimeout(function() {
            // Get current transform after zoom
            const transform = self.panzoomInstance.getTransform ? self.panzoomInstance.getTransform() : { x: 0, y: 0, scale: fitScale };
            const currentScale = transform.scale || fitScale;
            
            // Get container dimensions and position
            const containerRect = container.getBoundingClientRect();
            
            // Calculate the center of the visible canvas area (accounting for sidebar)
            // This is where we want to show the booths
            let canvasAreaCenterX = container.clientWidth / 2;
            const canvasAreaCenterY = container.clientHeight / 2;
            
            // Adjust for sidebar if visible - the canvas area is offset
            if (sidebar && !sidebar.classList.contains('hidden') && !sidebar.classList.contains('collapsed')) {
                const sidebarWidth = sidebar.offsetWidth || 280;
                // Canvas area starts after sidebar, so center is: sidebar width + half of remaining width
                canvasAreaCenterX = sidebarWidth + (containerWidth / 2);
            } else if (sidebar && sidebar.classList.contains('collapsed')) {
                canvasAreaCenterX = 50 + (containerWidth / 2);
            }
            
            // Calculate pan position to center the bounding box
            // panX/panY is the offset of the canvas element in the container
            // To show canvas point (centerX, centerY) at container point (canvasAreaCenterX, canvasAreaCenterY):
            // panX = canvasAreaCenterX - (centerX * scale)
            // panY = canvasAreaCenterY - (centerY * scale)
            const panX = canvasAreaCenterX - (centerX * currentScale);
            const panY = canvasAreaCenterY - (centerY * currentScale);
            
            // Apply pan to center using setTransform
            if (self.panzoomInstance.setTransform) {
                self.panzoomInstance.setTransform({ x: panX, y: panY, scale: currentScale });
            } else if (self.panzoomInstance.moveTo) {
                // moveTo moves the canvas so the given point is at viewport center
                // But we need to account for the sidebar offset
                // Try using the center coordinates directly
                self.panzoomInstance.moveTo(centerX, centerY, { animate: true });
            }
            
            // Update zoom level display
            self.zoomLevel = currentScale;
            if ($('#zoomLevel').length) {
                $('#zoomLevel').text(Math.round(currentScale * 100) + '%');
            }
            
            showNotification('Zoomed to fit ' + zoneBooths.length + ' booth(s) from Zone ' + zoneName, 'success');
        }, 300);
    },
    
    // Rotate all booths in a zone by a specified degree
    rotateZoneBooths: function(zoneName) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Find all booths on canvas that belong to this zone
        const zoneBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        
        if (zoneBooths.length === 0) {
            showNotification('No booths from Zone ' + zoneName + ' found on canvas', 'warning');
            return;
        }
        
        // Prompt user for rotation degree
        Swal.fire({
            title: 'Rotate Zone ' + zoneName + ' Booths',
            html: `
                <p>Found <strong>${zoneBooths.length}</strong> booth(s) in Zone ${zoneName} on canvas.</p>
                <p>Enter rotation degree:</p>
                <input type="number" id="rotationDegree" class="swal2-input" 
                       placeholder="0" value="0" min="-360" max="360" step="1">
                <p style="font-size: 12px; color: #666; margin-top: 10px;">
                    <strong>Note:</strong> Enter positive value to rotate clockwise, negative to rotate counter-clockwise.<br>
                    This will be <strong>added</strong> to the current rotation of each booth.
                </p>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Apply Rotation',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ff9800',
            inputValidator: (value) => {
                if (!value && value !== 0) {
                    return 'Please enter a rotation degree';
                }
                const degree = parseFloat(value);
                if (isNaN(degree)) {
                    return 'Please enter a valid number';
                }
                if (degree < -360 || degree > 360) {
                    return 'Rotation must be between -360 and 360 degrees';
                }
            },
            preConfirm: () => {
                const input = document.getElementById('rotationDegree');
                const degree = parseFloat(input.value) || 0;
                return degree;
            }
        }).then((result) => {
            if (result.isConfirmed && result.value !== undefined) {
                const rotationDegree = parseFloat(result.value) || 0;
                self.applyRotationToZoneBooths(zoneName, rotationDegree);
            }
        });
    },
    
    // Apply rotation to all booths in a zone
    applyRotationToZoneBooths: function(zoneName, rotationDegree) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Find all booths on canvas that belong to this zone
        const zoneBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        
        if (zoneBooths.length === 0) {
            showNotification('No booths from Zone ' + zoneName + ' found on canvas', 'warning');
            return;
        }
        
        const boothsToSave = [];
        let rotatedCount = 0;
        
        // Apply rotation to each booth
        zoneBooths.forEach(function(boothElement) {
            // Get current rotation
            let currentRotation = parseFloat(boothElement.getAttribute('data-rotation')) || 0;
            
            // Extract rotation from transform if data-rotation is not set
            if (!boothElement.getAttribute('data-rotation')) {
                const transform = boothElement.style.transform || '';
                const match = transform.match(/rotate\(([^)]+)\)/);
                if (match) {
                    currentRotation = parseFloat(match[1]) || 0;
                }
            }
            
            // Add the rotation degree to current rotation
            let newRotation = currentRotation + rotationDegree;
            
            // Normalize to -360 to 360 range
            newRotation = newRotation % 360;
            if (newRotation > 360) newRotation -= 360;
            if (newRotation < -360) newRotation += 360;
            
            // Apply new rotation
            boothElement.style.transform = 'rotate(' + newRotation + 'deg)';
            boothElement.setAttribute('data-rotation', newRotation);
            
            // Update rotation indicator if it exists
            self.updateRotationIndicator(boothElement);
            
            // Collect booth data for batch save
            const boothId = boothElement.getAttribute('data-booth-id');
            const x = parseFloat(boothElement.style.left) || 0;
            const y = parseFloat(boothElement.style.top) || 0;
            const width = parseFloat(boothElement.style.width) || self.defaultBoothWidth;
            const height = parseFloat(boothElement.style.height) || self.defaultBoothHeight;
            const zIndex = parseFloat(boothElement.style.zIndex) || 10;
            const fontSize = parseFloat(boothElement.style.fontSize) || 14;
            const borderWidth = parseFloat(boothElement.style.borderWidth) || 2;
            const borderRadius = parseFloat(boothElement.style.borderRadius) || 6;
            const opacity = parseFloat(boothElement.style.opacity) || 1.00;
            
            boothsToSave.push({
                id: parseInt(boothId),
                position_x: x,
                position_y: y,
                width: width,
                height: height,
                rotation: newRotation,
                z_index: zIndex,
                font_size: fontSize,
                border_width: borderWidth,
                border_radius: borderRadius,
                opacity: opacity
            });
            
            rotatedCount++;
        });
        
        // Batch save all rotations
        if (boothsToSave.length > 0) {
            self.saveBoothsBatch(boothsToSave).then(function(result) {
                showNotification('Successfully rotated ' + rotatedCount + ' booth(s) in Zone ' + zoneName + ' by ' + rotationDegree + '°', 'success');
            }).catch(function(error) {
                console.error('Error saving rotations:', error);
                showNotification('Rotations applied but failed to save to database', 'warning');
            });
        }
        
        // Update info toolbar if any of these booths are selected
        if (self.selectedBooths && self.selectedBooths.length > 0) {
            const selectedBooth = self.selectedBooths[0];
            if (selectedBooth && selectedBooth.getAttribute('data-booth-zone') === zoneName) {
                self.updateInfoToolbar(selectedBooth);
            }
        }
        
        // Save state for undo/redo
        self.saveState();
    },
    
    // ============================================
    // DESIGN TOOLS SYSTEM
    // ============================================
    
    // Switch between tools
    switchTool: function(tool) {
        const self = this;
        self.currentTool = tool;
        
        // Update button states
        $('.toolbar-btn[data-tool]').removeClass('active');
        $('#btn' + tool.charAt(0).toUpperCase() + tool.slice(1) + 'Tool').addClass('active');
        
        // Update canvas classes and cursor
        const canvas = document.getElementById('print');
        if (canvas) {
            canvas.classList.remove('tool-select', 'tool-pan', 'tool-zoom', 'tool-align', 'tool-distribute', 'tool-measure');
            canvas.classList.add('tool-' + tool);
            
            // Set appropriate cursor
            switch(tool) {
                case 'select':
                    canvas.style.cursor = 'default';
                    break;
                case 'pan':
                    canvas.style.cursor = 'grab';
                    break;
                case 'zoom':
                    canvas.style.cursor = 'zoom-in';
                    break;
                case 'align':
                    canvas.style.cursor = 'crosshair';
                    break;
                case 'distribute':
                    canvas.style.cursor = 'move';
                    break;
                case 'measure':
                    canvas.style.cursor = 'crosshair';
                    break;
            }
        }
        
        // Update Panzoom based on tool
        if (self.panzoomInstance && self.panzoomInstance.setOptions) {
            if (tool === 'pan') {
                // Pan tool - enable panning
                self.panzoomInstance.setOptions({ disablePan: false });
            } else {
                // Other tools - disable panning (can still pan with Space key)
                self.panzoomInstance.setOptions({ disablePan: true });
            }
        }
        
        // Show tool-specific notification
        const toolNames = {
            'select': 'Select Tool',
            'pan': 'Pan Tool',
            'zoom': 'Zoom Tool',
            'align': 'Align Tool',
            'distribute': 'Distribute Tool',
            'measure': 'Measure Tool'
        };
        Swal.fire({
            icon: 'info',
            title: toolNames[tool] + ' activated',
            timer: 1500,
            showConfirmButton: false,
            toast: true,
            position: 'bottom-right'
        });
    },
    
    // Setup tool-specific event handlers
    setupToolHandlers: function() {
        const self = this;
        const canvas = document.getElementById('print');
        const container = document.getElementById('printContainer');
        if (!canvas || !container) return;
        
        // Pan Tool - Enable direct panning
        // Handled by Panzoom when tool is 'pan'
        
        // Zoom Tool - Click to zoom in, Alt+Click to zoom out, Drag to zoom selection
        container.addEventListener('mousedown', function(e) {
            if (self.currentTool === 'zoom' && e.button === 0) {
                e.preventDefault();
                e.stopPropagation();
                
                const canvasRect = canvas.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();
                
                // Get current zoom and pan
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
                
                // Convert click position to canvas coordinates
                const clickX = (e.clientX - canvasRect.left - panX) / scale;
                const clickY = (e.clientY - canvasRect.top - panY) / scale;
                
                // Check if Alt/Option key is pressed (zoom out)
                const isZoomOut = e.altKey || e.metaKey;
                
                // Update cursor
                if (isZoomOut) {
                    canvas.classList.add('zooming-out');
                } else {
                    canvas.classList.remove('zooming-out');
                }
                
                // Start zoom selection or click zoom
                let isDragging = false;
                const startX = e.clientX;
                const startY = e.clientY;
                const startTime = Date.now();
                
                // Capture variables for nested functions
                const capturedClickX = clickX;
                const capturedClickY = clickY;
                const capturedIsZoomOut = isZoomOut;
                
                // Create or get zoom selection element
                let zoomSelection = document.getElementById('zoomSelection');
                if (!zoomSelection) {
                    zoomSelection = document.createElement('div');
                    zoomSelection.className = 'zoom-selection';
                    zoomSelection.id = 'zoomSelection';
                    container.appendChild(zoomSelection);
                }
                
                // Mouse move handler
                const handleZoomMove = function(e) {
                    const moveDistance = Math.abs(e.clientX - startX) + Math.abs(e.clientY - startY);
                    if (moveDistance > 5) {
                        isDragging = true;
                        
                        const currentContainerRect = container.getBoundingClientRect();
                        const currentX = e.clientX - currentContainerRect.left;
                        const currentY = e.clientY - currentContainerRect.top;
                        const startContainerX = startX - currentContainerRect.left;
                        const startContainerY = startY - currentContainerRect.top;
                        
                        const left = Math.min(startContainerX, currentX);
                        const top = Math.min(startContainerY, currentY);
                        const width = Math.abs(currentX - startContainerX);
                        const height = Math.abs(currentY - startContainerY);
                        
                        zoomSelection.style.display = 'block';
                        zoomSelection.style.left = left + 'px';
                        zoomSelection.style.top = top + 'px';
                        zoomSelection.style.width = width + 'px';
                        zoomSelection.style.height = height + 'px';
                        zoomSelection.classList.add('active');
                    }
                };
                
                // Mouse up handler
                const handleZoomUp = function(e) {
                    document.removeEventListener('mousemove', handleZoomMove);
                    document.removeEventListener('mouseup', handleZoomUp);
                    
                    const moveDistance = Math.abs(e.clientX - startX) + Math.abs(e.clientY - startY);
                    const timeDiff = Date.now() - startTime;
                    
                    canvas.classList.remove('zooming-out');
                    
                    // Re-get current transform values in case they changed
                    let currentScale = scale;
                    let currentPanX = panX;
                    let currentPanY = panY;
                    if (self.panzoomInstance) {
                        if (self.panzoomInstance.getScale) {
                            currentScale = self.panzoomInstance.getScale();
                        }
                        if (self.panzoomInstance.getTransform) {
                            const transform = self.panzoomInstance.getTransform();
                            currentPanX = transform.x || 0;
                            currentPanY = transform.y || 0;
                        }
                    }
                    
                    if (isDragging && moveDistance > 10) {
                        // Drag selection - zoom to selected area
                        const containerRect = container.getBoundingClientRect();
                        const endX = e.clientX - containerRect.left;
                        const endY = e.clientY - containerRect.top;
                        const startContainerX = startX - containerRect.left;
                        const startContainerY = startY - containerRect.top;
                        
                        const selectionLeft = Math.min(startContainerX, endX);
                        const selectionTop = Math.min(startContainerY, endY);
                        const selectionWidth = Math.abs(endX - startContainerX);
                        const selectionHeight = Math.abs(endY - startContainerY);
                        
                        if (selectionWidth > 10 && selectionHeight > 10) {
                            const canvasLeft = (selectionLeft - currentPanX) / currentScale;
                            const canvasTop = (selectionTop - currentPanY) / currentScale;
                            const canvasRight = ((selectionLeft + selectionWidth) - currentPanX) / currentScale;
                            const canvasBottom = ((selectionTop + selectionHeight) - currentPanY) / currentScale;
                            
                            self.zoomToSelection(canvasLeft, canvasTop, canvasRight, canvasBottom);
                        }
                    } else if (!isDragging && timeDiff < 300) {
                        // Click zoom - zoom in/out at click point
                        const zoomFactor = capturedIsZoomOut ? 0.8 : 1.25;
                        const newScale = capturedIsZoomOut ? 
                            Math.max(0.1, currentScale * zoomFactor) : 
                            Math.min(5, currentScale * zoomFactor);
                        
                        if (self.panzoomInstance && self.panzoomInstance.zoom) {
                            // Zoom with focal point if supported
                            try {
                                self.panzoomInstance.zoom(newScale, { 
                                    animate: true,
                                    focal: { x: capturedClickX, y: capturedClickY }
                                });
                            } catch (err) {
                                // Fallback if focal point not supported
                                self.panzoomInstance.zoom(newScale, { animate: true });
                            }
                            
                            self.zoomLevel = newScale;
                            const zoomLevelEl = document.getElementById('zoomLevel');
                            if (zoomLevelEl) {
                                zoomLevelEl.textContent = Math.round(newScale * 100) + '%';
                            }
                        }
                    }
                    
                    zoomSelection.style.display = 'none';
                    zoomSelection.classList.remove('active');
                };
                
                document.addEventListener('mousemove', handleZoomMove);
                document.addEventListener('mouseup', handleZoomUp);
            }
        });
        
        // Measure Tool - Click two points to measure distance
        container.addEventListener('mousedown', function(e) {
            if (self.currentTool === 'measure' && e.button === 0) {
                e.preventDefault();
                e.stopPropagation();
                
                const containerRect = container.getBoundingClientRect();
                const mouseX = e.clientX - containerRect.left;
                const mouseY = e.clientY - containerRect.top;
                
                if (!self.measureStartPoint) {
                    // First click - set start point
                    self.measureStartPoint = { x: mouseX, y: mouseY };
                    
                    // Create measure line and label if they don't exist
                    if (!self.measureLine) {
                        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                        svg.style.position = 'absolute';
                        svg.style.top = '0';
                        svg.style.left = '0';
                        svg.style.width = '100%';
                        svg.style.height = '100%';
                        svg.style.pointerEvents = 'none';
                        svg.style.zIndex = '400';
                        svg.id = 'measureSvg';
                        container.appendChild(svg);
                        
                        self.measureLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                        self.measureLine.setAttribute('stroke', '#00ff00');
                        self.measureLine.setAttribute('stroke-width', '2');
                        self.measureLine.setAttribute('stroke-dasharray', '5,5');
                        svg.appendChild(self.measureLine);
                    }
                    
                    if (!self.measureLabel) {
                        self.measureLabel = document.createElement('div');
                        self.measureLabel.className = 'measure-label';
                        container.appendChild(self.measureLabel);
                    }
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'Measure Tool',
                        text: 'Click second point to measure distance',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'bottom-right'
                    });
                } else {
                    // Second click - calculate and display distance
                    const distance = Math.sqrt(
                        Math.pow(mouseX - self.measureStartPoint.x, 2) + 
                        Math.pow(mouseY - self.measureStartPoint.y, 2)
                    );
                    
                    // Convert pixels to real-world units (assuming 1px = 1cm for display)
                    const distanceCm = Math.round(distance);
                    const distanceM = (distanceCm / 100).toFixed(2);
                    
                    // Update label position (midpoint of line)
                    const midX = (self.measureStartPoint.x + mouseX) / 2;
                    const midY = (self.measureStartPoint.y + mouseY) / 2;
                    self.measureLabel.style.left = midX + 'px';
                    self.measureLabel.style.top = (midY - 20) + 'px';
                    self.measureLabel.textContent = distanceCm + 'px (' + distanceM + 'm)';
                    self.measureLabel.style.display = 'block';
                    
                    // Update line
                    self.measureLine.setAttribute('x1', self.measureStartPoint.x);
                    self.measureLine.setAttribute('y1', self.measureStartPoint.y);
                    self.measureLine.setAttribute('x2', mouseX);
                    self.measureLine.setAttribute('y2', mouseY);
                    
                    const distanceText = distanceCm + 'px (' + distanceM + 'm)';
                    Swal.fire({
                        icon: 'success',
                        title: 'Distance Measured',
                        text: distanceText,
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'bottom-right'
                    });
                    
                    // Reset for next measurement
                    self.measureStartPoint = null;
                }
            }
        });
        
        // Clear measure on tool switch or Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && self.currentTool === 'measure') {
                self.clearMeasure();
            }
        });
    },
    
    // Clear measure tool visualization
    clearMeasure: function() {
        const self = this;
        self.measureStartPoint = null;
        if (self.measureLine) {
            self.measureLine.setAttribute('x1', 0);
            self.measureLine.setAttribute('y1', 0);
            self.measureLine.setAttribute('x2', 0);
            self.measureLine.setAttribute('y2', 0);
        }
        if (self.measureLabel) {
            self.measureLabel.style.display = 'none';
        }
    },
    
    // Align Tool - Align selected booths
    alignBooths: function(alignment) {
        const self = this;
        if (!self.selectedBooths || self.selectedBooths.length < 2) {
            showNotification('Please select at least 2 booths to align', 'warning');
            return;
        }
        
        const booths = self.selectedBooths;
        let referenceValue;
        
        if (alignment === 'left' || alignment === 'right') {
            // Align horizontally
            if (alignment === 'left') {
                referenceValue = Math.min(...booths.map(b => parseFloat(b.style.left) || 0));
            } else {
                referenceValue = Math.max(...booths.map(b => {
                    const left = parseFloat(b.style.left) || 0;
                    const width = parseFloat(b.style.width) || 80;
                    return left + width;
                }));
            }
            
            booths.forEach(function(booth) {
                const left = parseFloat(booth.style.left) || 0;
                const width = parseFloat(booth.style.width) || 80;
                if (alignment === 'left') {
                    booth.style.left = referenceValue + 'px';
                } else {
                    booth.style.left = (referenceValue - width) + 'px';
                }
                
                const boothId = booth.getAttribute('data-booth-id');
                const x = parseFloat(booth.style.left);
                const y = parseFloat(booth.style.top) || 0;
                self.saveBoothPosition(parseInt(boothId), x, y);
            });
        } else if (alignment === 'top' || alignment === 'bottom') {
            // Align vertically
            if (alignment === 'top') {
                referenceValue = Math.min(...booths.map(b => parseFloat(b.style.top) || 0));
            } else {
                referenceValue = Math.max(...booths.map(b => {
                    const top = parseFloat(b.style.top) || 0;
                    const height = parseFloat(b.style.height) || 50;
                    return top + height;
                }));
            }
            
            booths.forEach(function(booth) {
                const top = parseFloat(booth.style.top) || 0;
                const height = parseFloat(booth.style.height) || 50;
                if (alignment === 'top') {
                    booth.style.top = referenceValue + 'px';
                } else {
                    booth.style.top = (referenceValue - height) + 'px';
                }
                
                const boothId = booth.getAttribute('data-booth-id');
                const x = parseFloat(booth.style.left) || 0;
                const y = parseFloat(booth.style.top);
                self.saveBoothPosition(parseInt(boothId), x, y);
            });
        }
        
        showNotification('Aligned ' + booths.length + ' booth(s) ' + alignment, 'success');
        self.saveState();
    },
    
    // Distribute Tool - Distribute selected booths evenly
    distributeBooths: function(direction) {
        const self = this;
        if (!self.selectedBooths || self.selectedBooths.length < 3) {
            showNotification('Please select at least 3 booths to distribute', 'warning');
            return;
        }
        
        const booths = self.selectedBooths;
        
        if (direction === 'horizontal') {
            // Sort by X position
            booths.sort(function(a, b) {
                return (parseFloat(a.style.left) || 0) - (parseFloat(b.style.left) || 0);
            });
            
            const firstLeft = parseFloat(booths[0].style.left) || 0;
            const lastBooth = booths[booths.length - 1];
            const lastLeft = parseFloat(lastBooth.style.left) || 0;
            const lastWidth = parseFloat(lastBooth.style.width) || 80;
            const lastRight = lastLeft + lastWidth;
            
            const totalWidth = lastRight - firstLeft;
            const spacing = totalWidth / (booths.length - 1);
            
            booths.forEach(function(booth, index) {
                if (index > 0 && index < booths.length - 1) {
                    const width = parseFloat(booth.style.width) || 80;
                    const newLeft = firstLeft + (spacing * index) - (width / 2);
                    booth.style.left = newLeft + 'px';
                    
                    const boothId = booth.getAttribute('data-booth-id');
                    const x = newLeft;
                    const y = parseFloat(booth.style.top) || 0;
                    self.saveBoothPosition(parseInt(boothId), x, y);
                }
            });
        } else if (direction === 'vertical') {
            // Sort by Y position
            booths.sort(function(a, b) {
                return (parseFloat(a.style.top) || 0) - (parseFloat(b.style.top) || 0);
            });
            
            const firstTop = parseFloat(booths[0].style.top) || 0;
            const lastBooth = booths[booths.length - 1];
            const lastTop = parseFloat(lastBooth.style.top) || 0;
            const lastHeight = parseFloat(lastBooth.style.height) || 50;
            const lastBottom = lastTop + lastHeight;
            
            const totalHeight = lastBottom - firstTop;
            const spacing = totalHeight / (booths.length - 1);
            
            booths.forEach(function(booth, index) {
                if (index > 0 && index < booths.length - 1) {
                    const height = parseFloat(booth.style.height) || 50;
                    const newTop = firstTop + (spacing * index) - (height / 2);
                    booth.style.top = newTop + 'px';
                    
                    const boothId = booth.getAttribute('data-booth-id');
                    const x = parseFloat(booth.style.left) || 0;
                    const y = newTop;
                    self.saveBoothPosition(parseInt(boothId), x, y);
                }
            });
        }
        
        showNotification('Distributed ' + booths.length + ' booth(s) ' + direction + 'ly', 'success');
        self.saveState();
    },
    
    // Get zone from booth number
    getZoneFromBoothNumber: function(boothNumber) {
        if (!boothNumber) return 'OTHER';
        // Extract zone from booth number (first letter or first character)
        const match = boothNumber.match(/^([A-Za-z]+)/);
        if (match) {
            return match[1].toUpperCase();
        }
        // If no letter found, use first character or default to "OTHER"
        return boothNumber.length > 0 ? boothNumber.charAt(0).toUpperCase() : 'OTHER';
    },
    
    // Get zone settings (with caching and fallback to defaults, floor-plan-specific)
    getZoneSettings: function(zoneName, callback) {
        const self = this;
        
        // Get current floor plan ID for cache key
        const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
        const cacheKey = floorPlanId ? floorPlanId + '_' + zoneName : 'global_' + zoneName;
        
        // If already cached for this floor plan, return immediately
        if (self.zoneSettingsCache[cacheKey]) {
            if (callback) {
                callback(self.zoneSettingsCache[cacheKey]);
            }
            return Promise.resolve(self.zoneSettingsCache[cacheKey]);
        }
        
        // If already loading for this floor plan, wait for it
        if (self.zoneSettingsLoading[cacheKey]) {
            return self.zoneSettingsLoading[cacheKey].then(function(settings) {
                if (callback) {
                    callback(settings);
                }
                return settings;
            });
        }
        
        // Fetch from server (floor-plan-specific)
        const zoneSettingsUrl = '/booths/zone-settings/' + encodeURIComponent(zoneName) + 
            (floorPlanId ? '?floor_plan_id=' + floorPlanId : '');
        const loadingPromise = fetch(zoneSettingsUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Failed to load zone settings');
            }
            return response.json();
        })
        .then(function(data) {
            if (data.status === 200 && data.settings) {
                // Cache the settings with floor-plan-specific key
                self.zoneSettingsCache[cacheKey] = data.settings;
                return data.settings;
            } else {
                // Return defaults if no zone settings found for this floor plan
                return null;
            }
        })
        .catch(function(error) {
            console.warn('Failed to load zone settings for ' + zoneName + ' (Floor Plan ' + floorPlanId + '), using defaults:', error);
            return null;
        })
        .finally(function() {
            // Clear loading flag
            delete self.zoneSettingsLoading[cacheKey];
        });
        
        // Store loading promise with floor-plan-specific key
        self.zoneSettingsLoading[cacheKey] = loadingPromise;
        
        if (callback) {
            loadingPromise.then(callback);
        }
        
        return loadingPromise;
    },
    
    // Get effective settings for a booth (zone settings override defaults, floor-plan-specific)
    getEffectiveBoothSettings: function(boothNumber) {
        const self = this;
        const zoneName = self.getZoneFromBoothNumber(boothNumber);
        
        // Get current floor plan ID for cache key
        const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
        const cacheKey = floorPlanId ? floorPlanId + '_' + zoneName : 'global_' + zoneName;
        
        // Start with defaults
        const effectiveSettings = {
            width: self.defaultBoothWidth,
            height: self.defaultBoothHeight,
            rotation: self.defaultBoothRotation,
            zIndex: self.defaultBoothZIndex,
            fontSize: self.defaultBoothFontSize,
            borderWidth: self.defaultBoothBorderWidth,
            borderRadius: self.defaultBoothBorderRadius,
            opacity: self.defaultBoothOpacity
        };
        
        // Override with zone settings if available (floor-plan-specific cache key)
        if (self.zoneSettingsCache[cacheKey]) {
            const zoneSettings = self.zoneSettingsCache[cacheKey];
            if (zoneSettings.width !== undefined) effectiveSettings.width = zoneSettings.width;
            if (zoneSettings.height !== undefined) effectiveSettings.height = zoneSettings.height;
            if (zoneSettings.rotation !== undefined) effectiveSettings.rotation = zoneSettings.rotation;
            if (zoneSettings.zIndex !== undefined) effectiveSettings.zIndex = zoneSettings.zIndex;
            if (zoneSettings.fontSize !== undefined) effectiveSettings.fontSize = zoneSettings.fontSize;
            if (zoneSettings.borderWidth !== undefined) effectiveSettings.borderWidth = zoneSettings.borderWidth;
            if (zoneSettings.borderRadius !== undefined) effectiveSettings.borderRadius = zoneSettings.borderRadius;
            if (zoneSettings.opacity !== undefined) effectiveSettings.opacity = zoneSettings.opacity;
        }
        
        return effectiveSettings;
    },
    
    // Get or create zone section
    getOrCreateZoneSection: function(zoneName) {
        const container = document.getElementById('boothNumbersContainer');
        if (!container) return null;
        
        // Check if zone section already exists
        let zoneSection = container.querySelector('[data-zone="' + zoneName + '"]');
        
        if (!zoneSection) {
            // Create new zone section
            zoneSection = document.createElement('div');
            zoneSection.className = 'zone-section';
            zoneSection.setAttribute('data-zone', zoneName);
            
            // Create zone header
            const zoneHeader = document.createElement('div');
            zoneHeader.className = 'zone-header';
            zoneHeader.setAttribute('data-zone-toggle', zoneName);
            
            const chevron = document.createElement('i');
            chevron.className = 'fas fa-chevron-down zone-chevron';
            
            const zoneNameSpan = document.createElement('span');
            zoneNameSpan.className = 'zone-name';
            zoneNameSpan.textContent = 'Zone ' + zoneName;
            
            const zoneCountSpan = document.createElement('span');
            zoneCountSpan.className = 'zone-count';
            zoneCountSpan.textContent = '(0)';
            
            const zoneHeaderLeft = document.createElement('div');
            zoneHeaderLeft.className = 'zone-header-left';
            zoneHeaderLeft.appendChild(chevron);
            zoneHeaderLeft.appendChild(zoneNameSpan);
            zoneHeaderLeft.appendChild(zoneCountSpan);
            
            const addAllBtn = document.createElement('button');
            addAllBtn.className = 'btn-add-all-zone';
            addAllBtn.setAttribute('data-zone', zoneName);
            addAllBtn.setAttribute('title', 'Add All Booths in Zone ' + zoneName + ' to Canvas');
            addAllBtn.innerHTML = '<i class="fas fa-plus-circle"></i> Add All';
            addAllBtn.onclick = function(e) {
                e.stopPropagation();
                FloorPlanDesigner.addAllZoneToCanvas(zoneName);
            };
            
            // Add "Add Selected" button
            const addSelectedBtn = document.createElement('button');
            addSelectedBtn.className = 'btn-add-selected-zone';
            addSelectedBtn.setAttribute('data-zone', zoneName);
            addSelectedBtn.setAttribute('title', 'Select booths first to add them to canvas');
            addSelectedBtn.innerHTML = '<i class="fas fa-layer-group"></i> Add Selected';
            addSelectedBtn.disabled = true;
            addSelectedBtn.onclick = function(e) {
                e.stopPropagation();
                FloorPlanDesigner.addSelectedZoneBoothsToCanvas(zoneName);
            };
            
            // Add click-to-place button
            const clickToPlaceBtn = document.createElement('button');
            clickToPlaceBtn.className = 'btn-add-all-zone-click';
            clickToPlaceBtn.setAttribute('data-zone', zoneName);
            clickToPlaceBtn.setAttribute('title', 'Add All Booths in Zone ' + zoneName + ' - Click on Canvas to Place');
            clickToPlaceBtn.innerHTML = '<i class="fas fa-crosshairs"></i>';
            clickToPlaceBtn.onclick = function(e) {
                e.stopPropagation();
                FloorPlanDesigner.enableClickToPlaceMode(zoneName);
            };
            
            // Add zone settings button
            const zoneSettingsBtn = document.createElement('button');
            zoneSettingsBtn.className = 'btn-zone-settings';
            zoneSettingsBtn.setAttribute('data-zone', zoneName);
            zoneSettingsBtn.setAttribute('title', 'Zone Settings - Adjust All Booths in Zone ' + zoneName);
            zoneSettingsBtn.innerHTML = '<i class="fas fa-cog"></i>';
            zoneSettingsBtn.onclick = function(e) {
                e.stopPropagation();
                FloorPlanDesigner.openZoneSettings(zoneName);
            };
            
            // Add clear zone button
            const clearZoneBtn = document.createElement('button');
            clearZoneBtn.className = 'btn-zone-clear';
            clearZoneBtn.setAttribute('data-zone', zoneName);
            clearZoneBtn.setAttribute('title', 'Clear Zone ' + zoneName + ' - Return All Booths to Booth Number Area');
            clearZoneBtn.innerHTML = '<i class="fas fa-undo"></i>';
            clearZoneBtn.onclick = function(e) {
                e.stopPropagation();
                FloorPlanDesigner.clearZoneBooths(zoneName);
            };
            
            // Add zoom to zone button
            const zoomToZoneBtn = document.createElement('button');
            zoomToZoneBtn.className = 'btn-zone-zoom';
            zoomToZoneBtn.setAttribute('data-zone', zoneName);
            zoomToZoneBtn.setAttribute('title', 'Zoom to Zone ' + zoneName + ' - Fit All Booths in View');
            zoomToZoneBtn.innerHTML = '<i class="fas fa-search-plus"></i>';
            zoomToZoneBtn.onclick = function(e) {
                e.stopPropagation();
                FloorPlanDesigner.zoomToZone(zoneName);
            };
            
            zoneHeader.appendChild(zoneHeaderLeft);
            zoneHeader.appendChild(addAllBtn);
            zoneHeader.appendChild(addSelectedBtn);
            zoneHeader.appendChild(clickToPlaceBtn);
            zoneHeader.appendChild(zoneSettingsBtn);
            zoneHeader.appendChild(clearZoneBtn);
            zoneHeader.appendChild(zoomToZoneBtn);
            
            // Create zone content
            const zoneContent = document.createElement('div');
            zoneContent.className = 'zone-content';
            zoneContent.id = 'zoneContent' + zoneName;
            
            zoneSection.appendChild(zoneHeader);
            zoneSection.appendChild(zoneContent);
            
            // Insert zone section in alphabetical order
            const allZones = Array.from(container.querySelectorAll('.zone-section'));
            let inserted = false;
            for (let i = 0; i < allZones.length; i++) {
                const existingZone = allZones[i].getAttribute('data-zone');
                if (existingZone > zoneName) {
                    container.insertBefore(zoneSection, allZones[i]);
                    inserted = true;
                    break;
                }
            }
            if (!inserted) {
                container.appendChild(zoneSection);
            }
            
            // Setup toggle handler for new zone
            this.setupZoneToggle(zoneHeader);
        }
        
        return zoneSection;
    },
    
    // Setup zone toggle functionality
    setupZoneToggle: function(zoneHeader) {
        const self = this;
        zoneHeader.addEventListener('click', function() {
            const zoneName = this.getAttribute('data-zone-toggle');
            const zoneSection = this.closest('.zone-section');
            if (zoneSection) {
                zoneSection.classList.toggle('collapsed');
            }
        });
    },
    
    // Update zone count
    updateZoneCount: function(zoneName) {
        const zoneSection = document.querySelector('[data-zone="' + zoneName + '"]');
        if (zoneSection) {
            const zoneContent = zoneSection.querySelector('.zone-content');
            const count = zoneContent ? zoneContent.querySelectorAll('.booth-number-item').length : 0;
            const countSpan = zoneSection.querySelector('.zone-count');
            if (countSpan) {
                countSpan.textContent = '(' + count + ')';
            }
        }
    },
    
    // Add booth back to sidebar
    addBoothToSidebar: function(boothData) {
        const container = document.getElementById('boothNumbersContainer');
        if (!container) return;
        
        // Check if booth already exists in sidebar
        const existingItem = container.querySelector('[data-booth-id="' + boothData.id + '"]');
        if (existingItem) {
            // Already exists, don't add duplicate
            return;
        }
        
        // Get zone from booth number
        const zoneName = this.getZoneFromBoothNumber(boothData.number);
        
        // Get or create zone section
        const zoneSection = this.getOrCreateZoneSection(zoneName);
        if (!zoneSection) return;
        
        const zoneContent = zoneSection.querySelector('.zone-content');
        if (!zoneContent) return;
        
        // Create new booth item element
        const boothItem = document.createElement('div');
        boothItem.className = 'booth-number-item';
        boothItem.setAttribute('draggable', 'true');
        boothItem.setAttribute('data-booth-id', boothData.id);
        boothItem.setAttribute('data-booth-number', boothData.number);
        boothItem.setAttribute('data-booth-status', boothData.status || '1');
        boothItem.setAttribute('data-booth-zone', zoneName);
        boothItem.setAttribute('data-client-id', boothData.clientId || '');
        boothItem.setAttribute('data-user-id', boothData.userId || '');
        boothItem.setAttribute('data-category-id', boothData.categoryId || '');
        boothItem.setAttribute('data-sub-category-id', boothData.subCategoryId || '');
        boothItem.setAttribute('data-asset-id', boothData.assetId || '');
        boothItem.setAttribute('data-booth-type-id', boothData.boothTypeId || '');
        boothItem.textContent = boothData.number;
        
        // Make it draggable
        boothItem.style.pointerEvents = 'auto';
        boothItem.style.userSelect = 'none';
        boothItem.style.webkitUserDrag = 'element';
        boothItem.style.cursor = 'grab';
        
        // Add to zone content (append to maintain order)
        zoneContent.appendChild(boothItem);
        
        // Re-apply drag and drop setup to the new item
        boothItem.setAttribute('draggable', 'true');
        boothItem.draggable = true;
        
        // Update zone count
        this.updateZoneCount(zoneName);
    },
    
    // Add booth to canvas
    addBoothToCanvas: function(boothData, x, y, skipSave) {
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
            
            // Update resize handles size in case booth dimensions changed
            self.updateResizeHandlesSize(existingBooth);
            
            // Get all properties from the element before saving
            const width = parseFloat(existingBooth.style.width) || parseFloat(existingBooth.getAttribute('data-width')) || self.defaultBoothWidth;
            const height = parseFloat(existingBooth.style.height) || parseFloat(existingBooth.getAttribute('data-height')) || self.defaultBoothHeight;
            const rotation = parseFloat(existingBooth.getAttribute('data-rotation')) || parseFloat(existingBooth.style.transform.match(/rotate\(([^)]+)\)/)?.[1]) || self.defaultBoothRotation;
            const zIndex = parseFloat(existingBooth.style.zIndex) || parseFloat(existingBooth.getAttribute('data-z-index')) || self.defaultBoothZIndex;
            const fontSize = parseFloat(existingBooth.style.fontSize) || parseFloat(existingBooth.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const borderWidth = parseFloat(existingBooth.style.borderWidth) || parseFloat(existingBooth.getAttribute('data-border-width')) || self.defaultBoothBorderWidth;
            const borderRadius = parseFloat(existingBooth.style.borderRadius) || parseFloat(existingBooth.getAttribute('data-border-radius')) || self.defaultBoothBorderRadius;
            const opacity = parseFloat(existingBooth.style.opacity) || parseFloat(existingBooth.getAttribute('data-opacity')) || self.defaultBoothOpacity;
            
            // Only save if skipSave is not true
            if (!skipSave) {
            this.saveBoothPosition(boothData.id, x, y, width, height, rotation, zIndex, fontSize, borderWidth, borderRadius, opacity);
            }
            return;
        }
        
        const boothElement = this.createBoothElement(boothData);
        boothElement.style.left = x + 'px';
        boothElement.style.top = y + 'px';
        boothElement.setAttribute('data-x', x);
        boothElement.setAttribute('data-y', y);
        
        canvas.appendChild(boothElement);
        
        // Update booth count
        if (self.updateBoothCount) {
            self.updateBoothCount();
        }
        
        // Verify transform controls exist after appending
        const controlsCheck = boothElement.querySelector('.transform-controls');
        
        this.makeBoothDraggable(boothElement);
        
        // Get all properties from the element after it's created and appended
        // Wait a moment to ensure element is fully rendered
        // Only save if skipSave is not true (for batch operations)
        if (!skipSave) {
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
                
                // Sync sidebar to remove this booth from sidebar
                self.syncSidebarWithCanvas();
        }, 100);
        } else {
            // Still save state even if skipping individual save
            self.saveState();
        }
    },
    
    // Create booth element
    createBoothElement: function(boothData) {
        const self = this;
        const div = document.createElement('div');
        div.className = 'dropped-booth status-' + boothData.status;
        div.setAttribute('data-booth-id', boothData.id);
        div.setAttribute('data-booth-number', boothData.number || '');
        div.setAttribute('data-booth-status', boothData.status || '1');
        const zoneName = boothData.zone || self.getZoneFromBoothNumber(boothData.number || '');
        div.setAttribute('data-booth-zone', zoneName);
        div.setAttribute('data-client-id', boothData.clientId || '');
        div.setAttribute('data-user-id', boothData.userId || '');
        div.setAttribute('data-category-id', boothData.categoryId || '');
        div.setAttribute('data-sub-category-id', boothData.subCategoryId || '');
        div.setAttribute('data-asset-id', boothData.assetId || '');
        div.setAttribute('data-booth-type-id', boothData.boothTypeId || '');
        
        // Get effective settings (zone settings override defaults)
        const effectiveSettings = self.getEffectiveBoothSettings(boothData.number || '');
        
        // Use boothData values if explicitly provided, otherwise use effective settings (zone or defaults)
        const width = boothData.width !== undefined && boothData.width !== null ? boothData.width : effectiveSettings.width;
        const height = boothData.height !== undefined && boothData.height !== null ? boothData.height : effectiveSettings.height;
        const rotation = boothData.rotation !== undefined && boothData.rotation !== null ? boothData.rotation : effectiveSettings.rotation;
        const zIndex = boothData.z_index !== undefined && boothData.z_index !== null ? boothData.z_index : effectiveSettings.zIndex;
        const fontSize = boothData.font_size !== undefined && boothData.font_size !== null ? boothData.font_size : effectiveSettings.fontSize;
        const borderWidth = boothData.border_width !== undefined && boothData.border_width !== null ? boothData.border_width : effectiveSettings.borderWidth;
        const borderRadius = boothData.border_radius !== undefined && boothData.border_radius !== null ? boothData.border_radius : effectiveSettings.borderRadius;
        const opacity = boothData.opacity !== undefined && boothData.opacity !== null ? boothData.opacity : effectiveSettings.opacity;
        
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
        
        // Calculate font size based on booth width to ensure text fits
        // Use width to scale the text size proportionally
        // Calculate font size: use 40-50% of the width, but respect user's fontSize if set
        // If user fontSize is too large for the booth, scale it down
        const calculatedFontSize = Math.min(fontSize, Math.max(8, width * 0.45));
        div.style.fontSize = calculatedFontSize + 'px';
        div.style.fontWeight = this.defaultFontWeight || 'bold';
        
        // Apply new appearance settings
        div.style.backgroundColor = this.defaultBackgroundColor || '#ffffff';
        div.style.borderColor = this.defaultBorderColor || '#007bff';
        div.style.color = this.defaultTextColor || '#000000';
        div.style.fontFamily = this.defaultFontFamily || 'Arial, sans-serif';
        div.style.textAlign = this.defaultTextAlign || 'center';
        div.style.boxShadow = this.defaultBoxShadow || '0 2px 8px rgba(0,0,0,0.2)';
        
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
        
        // Add right-click context menu handler
        div.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const boothId = div.getAttribute('data-booth-id');
            const boothNumber = div.getAttribute('data-booth-number');
            
            // Show context menu
            self.showBoothContextMenu(e, boothId, boothNumber, div);
        });
        
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
        
        // Calculate handle size: 10% of the smallest dimension, with min 8px and max 16px
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
    
    // Update selection bounding box (Photoshop-like) around all selected booths
    updateSelectionBoundingBox: function() {
        const self = this;
        const boundingBox = document.getElementById('selectionBoundingBox');
        if (!boundingBox) return;
        
        if (self.selectedBooths.length === 0) {
            boundingBox.classList.remove('active');
            return;
        }
        
        if (self.selectedBooths.length === 1) {
            // Single selection - hide bounding box (booth handles are shown)
            boundingBox.classList.remove('active');
            return;
        }
        
        // Calculate bounding box for all selected booths
        let minX = Infinity;
        let minY = Infinity;
        let maxX = -Infinity;
        let maxY = -Infinity;
        
        self.selectedBooths.forEach(function(booth) {
            const x = parseFloat(booth.style.left) || 0;
            const y = parseFloat(booth.style.top) || 0;
            const w = parseFloat(booth.style.width) || 80;
            const h = parseFloat(booth.style.height) || 50;
            
            minX = Math.min(minX, x);
            minY = Math.min(minY, y);
            maxX = Math.max(maxX, x + w);
            maxY = Math.max(maxY, y + h);
        });
        
        // Add padding around the bounding box
        const padding = 4;
        boundingBox.style.left = (minX - padding) + 'px';
        boundingBox.style.top = (minY - padding) + 'px';
        boundingBox.style.width = (maxX - minX + padding * 2) + 'px';
        boundingBox.style.height = (maxY - minY + padding * 2) + 'px';
        boundingBox.classList.add('active');
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
            
            // Selection always allowed (no tool restriction)
            
            // Check if Ctrl/Cmd key is pressed for multi-select
            const isMultiSelect = e.ctrlKey || e.metaKey;
            
            if (!isMultiSelect) {
                // Single select - deselect all others
            document.querySelectorAll('.dropped-booth').forEach(function(booth) {
                booth.classList.remove('selected');
                const ctrl = booth.querySelector('.transform-controls');
                if (ctrl) {
                    ctrl.style.display = 'none';
                    ctrl.style.visibility = 'hidden';
                    ctrl.style.opacity = '0';
                }
            });
                self.selectedBooths = [];
            } else {
                // Multi-select - toggle this booth's selection
                if (element.classList.contains('selected')) {
                    // Deselect this booth
                    element.classList.remove('selected');
                    const index = self.selectedBooths.indexOf(element);
                    if (index > -1) {
                        self.selectedBooths.splice(index, 1);
                    }
                    const handles = element.querySelectorAll('.resize-handle');
                    handles.forEach(function(handle) {
                        handle.style.display = 'none';
                    });
                    const rotateHandle = element.querySelector('.rotate-handle');
                    if (rotateHandle) {
                        rotateHandle.style.display = 'none';
                    }
                    // Update bounding box
                    self.updateSelectionBoundingBox();
                    // Update toolbar
                    if (self.selectedBooths.length === 0) {
            self.updateInfoToolbar(null);
                    } else {
                        self.updateInfoToolbar(null); // Show multi-select info
                    }
                    return;
                }
            }
            
            // Add to selection
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
                
                // Show resize handles (only if not locked)
                if (!element.classList.contains('locked')) {
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
                }
                
            // Add to selected booths array
            if (self.selectedBooths.indexOf(element) === -1) {
                self.selectedBooths.push(element);
            }
            
            // Update bounding box
            self.updateSelectionBoundingBox();
            
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
                    if (self.selectedBooths.length === 1) {
                    self.updateInfoToolbar(element);
                    } else {
                        self.updateInfoToolbar(null); // Show multi-select info
                    }
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
            // Check if booth is locked - prevent all interactions if locked
            if (element.classList.contains('locked')) {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            
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
            
            // Dragging always allowed (no tool restriction)
            
            // Check if this booth is selected - if not, only drag this one
            const isSelected = element.classList.contains('selected');
            const isMultiSelect = isSelected && self.selectedBooths.length > 1;
            
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
            
            // Store initial positions of all selected booths for multi-select dragging
            let selectedBoothsInitialPositions = [];
            if (isMultiSelect) {
                self.selectedBooths.forEach(function(selectedBooth) {
                    selectedBoothsInitialPositions.push({
                        element: selectedBooth,
                        initialX: parseFloat(selectedBooth.style.left) || 0,
                        initialY: parseFloat(selectedBooth.style.top) || 0
                    });
                    selectedBooth.style.cursor = 'grabbing';
                    selectedBooth.style.userSelect = 'none';
                    selectedBooth.classList.add('dragging');
                });
            } else {
            element.style.cursor = 'grabbing';
            element.style.userSelect = 'none';
            element.classList.add('dragging');
            }
            
            // Store initial positions for multi-select
            handleMouseMove.selectedBoothsInitialPositions = selectedBoothsInitialPositions;
            handleMouseMove.isMultiSelect = isMultiSelect;
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
        
        // Mouse move handler for smooth dragging (supports single and multi-select)
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
            
            // Check if we're dragging multiple selected booths
            const isMultiSelect = handleMouseMove.isMultiSelect && handleMouseMove.selectedBoothsInitialPositions;
            
            if (isMultiSelect && handleMouseMove.selectedBoothsInitialPositions.length > 0) {
                // Drag all selected booths together
                handleMouseMove.selectedBoothsInitialPositions.forEach(function(boothData) {
                    const boothElement = boothData.element;
                    const boothInitialX = boothData.initialX;
                    const boothInitialY = boothData.initialY;
                    
                    // Calculate new position for this booth
                    let newX = boothInitialX + deltaX;
                    let newY = boothInitialY + deltaY;
                    
                    // Constrain to canvas bounds
                    const elementWidth = parseFloat(boothElement.style.width) || 80;
                    const elementHeight = parseFloat(boothElement.style.height) || 50;
                    const canvasWidth = canvas.offsetWidth;
                    const canvasHeight = canvas.offsetHeight;
                    
                    newX = Math.max(0, Math.min(newX, canvasWidth - elementWidth));
                    newY = Math.max(0, Math.min(newY, canvasHeight - elementHeight));
                    
                    // Snap to grid when dragging (if snap is enabled)
                    if (self.snapEnabled) {
                        newX = Math.round(newX / self.gridSize) * self.gridSize;
                        newY = Math.round(newY / self.gridSize) * self.gridSize;
                    }
                    
                    // Apply new position smoothly
                    boothElement.style.left = newX + 'px';
                    boothElement.style.top = newY + 'px';
                    boothElement.setAttribute('data-x', newX);
                    boothElement.setAttribute('data-y', newY);
                });
                
                // Update bounding box during drag
                self.updateSelectionBoundingBox();
                
                // Update info toolbar with the primary dragged booth
                if (element.classList.contains('selected')) {
                    const anyFieldEditing = document.querySelector('.info-value.info-editable.info-editing');
                    if (!anyFieldEditing) {
                        if (!handleMouseMove.updateScheduled) {
                            handleMouseMove.updateScheduled = true;
                            requestAnimationFrame(function() {
                                if (self.selectedBooths.length === 1) {
                                    self.updateInfoToolbar(element);
                                } else {
                                    self.updateInfoToolbar(null); // Show multi-select info
                                }
                                handleMouseMove.updateScheduled = false;
                            });
                        }
                    }
                }
            } else {
                // Single booth dragging (original behavior)
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
            
                // Snap to grid when dragging (if snap is enabled)
                if (self.snapEnabled) {
            newX = Math.round(newX / self.gridSize) * self.gridSize;
            newY = Math.round(newY / self.gridSize) * self.gridSize;
                }
            
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
            }
        };
        
        // Add mousemove listener to document for smooth dragging even outside element
        document.addEventListener('mousemove', handleMouseMove);
        
        // Initialize mousemove logging flag and update scheduling
        handleMouseMove.logged = false;
        handleMouseMove.updateScheduled = false;
        
        // Mouse up handler - end dragging (supports single and multi-select)
        const handleMouseUp = function(e) {
            if (!isDragging) {
                return;
            }
            
            isDragging = false;
            const isMultiSelect = handleMouseMove.isMultiSelect && handleMouseMove.selectedBoothsInitialPositions;
            
            if (isMultiSelect && handleMouseMove.selectedBoothsInitialPositions) {
                // Final snap to grid for all selected booths (if snap is enabled)
                handleMouseMove.selectedBoothsInitialPositions.forEach(function(boothData) {
                    const boothElement = boothData.element;
                    const currentX = parseFloat(boothElement.style.left) || 0;
                    const currentY = parseFloat(boothElement.style.top) || 0;
                    let snappedX = currentX;
                    let snappedY = currentY;
                    
                    if (self.snapEnabled) {
                        snappedX = Math.round(currentX / self.gridSize) * self.gridSize;
                        snappedY = Math.round(currentY / self.gridSize) * self.gridSize;
                    }
                    
                    boothElement.style.left = snappedX + 'px';
                    boothElement.style.top = snappedY + 'px';
                    boothElement.setAttribute('data-x', snappedX);
                    boothElement.setAttribute('data-y', snappedY);
                    
                    boothElement.style.cursor = 'move';
                    boothElement.style.userSelect = '';
                    boothElement.classList.remove('dragging');
                });
            } else {
            element.style.cursor = 'move';
            element.style.userSelect = '';
            element.classList.remove('dragging');
            
                // Final snap to grid (if snap is enabled)
            const currentX = parseFloat(element.style.left) || 0;
            const currentY = parseFloat(element.style.top) || 0;
                let snappedX = currentX;
                let snappedY = currentY;
                
                if (self.snapEnabled) {
                    snappedX = Math.round(currentX / self.gridSize) * self.gridSize;
                    snappedY = Math.round(currentY / self.gridSize) * self.gridSize;
                }
            
            element.style.left = snappedX + 'px';
            element.style.top = snappedY + 'px';
            element.setAttribute('data-x', snappedX);
            element.setAttribute('data-y', snappedY);
            }
            
            // Clear multi-select dragging state
            handleMouseMove.selectedBoothsInitialPositions = null;
            handleMouseMove.isMultiSelect = false;
            
            // Update bounding box after drag
            self.updateSelectionBoundingBox();
            
            // Get final position relative to canvas
            const canvas = document.getElementById('print');
            const canvasRect = canvas.getBoundingClientRect();
            
            if (isMultiSelect && self.selectedBooths.length > 1) {
                // Save positions for all selected booths
                self.selectedBooths.forEach(function(boothElement) {
                    const boothId = boothElement.getAttribute('data-booth-id');
                    const boothX = parseFloat(boothElement.style.left) || 0;
                    const boothY = parseFloat(boothElement.style.top) || 0;
                    const boothWidth = parseFloat(boothElement.style.width) || 80;
                    const boothHeight = parseFloat(boothElement.style.height) || 50;
                    const boothRotation = parseFloat(boothElement.getAttribute('data-rotation')) || 0;
                    const boothZIndex = parseFloat(boothElement.style.zIndex) || parseFloat(boothElement.getAttribute('data-z-index')) || self.defaultBoothZIndex;
                    const boothFontSize = parseFloat(boothElement.style.fontSize) || parseFloat(boothElement.getAttribute('data-font-size')) || self.defaultBoothFontSize;
                    const boothBorderWidth = parseFloat(boothElement.style.borderWidth) || parseFloat(boothElement.getAttribute('data-border-width')) || self.defaultBoothBorderWidth;
                    const boothBorderRadius = parseFloat(boothElement.style.borderRadius) || parseFloat(boothElement.getAttribute('data-border-radius')) || self.defaultBoothBorderRadius;
                    const boothOpacity = parseFloat(boothElement.style.opacity) || parseFloat(boothElement.getAttribute('data-opacity')) || self.defaultBoothOpacity;
                    const boothBackgroundColor = boothElement.style.backgroundColor || boothElement.getAttribute('data-background-color') || self.defaultBackgroundColor;
                    const boothBorderColor = boothElement.style.borderColor || boothElement.getAttribute('data-border-color') || self.defaultBorderColor;
                    const boothTextColor = boothElement.style.color || boothElement.getAttribute('data-text-color') || self.defaultTextColor;
                    const boothFontWeight = boothElement.style.fontWeight || boothElement.getAttribute('data-font-weight') || self.defaultFontWeight;
                    const boothFontFamily = boothElement.style.fontFamily || boothElement.getAttribute('data-font-family') || self.defaultFontFamily;
                    const boothTextAlign = boothElement.style.textAlign || boothElement.getAttribute('data-text-align') || self.defaultTextAlign;
                    const boothBoxShadow = boothElement.style.boxShadow || boothElement.getAttribute('data-box-shadow') || self.defaultBoxShadow;
                    
                    self.saveBoothPosition(boothId, boothX, boothY, boothWidth, boothHeight, boothRotation, boothZIndex, boothFontSize, boothBorderWidth, boothBorderRadius, boothOpacity, boothBackgroundColor, boothBorderColor, boothTextColor, boothFontWeight, boothFontFamily, boothTextAlign, boothBoxShadow);
                });
            } else {
                // Single booth - save position
                // Use snappedX and snappedY from the earlier snap calculation
                const finalX = parseFloat(element.style.left) || 0;
                const finalY = parseFloat(element.style.top) || 0;
            const width = parseFloat(element.style.width) || 80;
            const height = parseFloat(element.style.height) || 50;
            const rotation = parseFloat(element.getAttribute('data-rotation')) || 0;
                const zIndex = parseFloat(element.style.zIndex) || parseFloat(element.getAttribute('data-z-index')) || self.defaultBoothZIndex;
                const fontSize = parseFloat(element.style.fontSize) || parseFloat(element.getAttribute('data-font-size')) || self.defaultBoothFontSize;
                const borderWidth = parseFloat(element.style.borderWidth) || parseFloat(element.getAttribute('data-border-width')) || self.defaultBoothBorderWidth;
                const borderRadius = parseFloat(element.style.borderRadius) || parseFloat(element.getAttribute('data-border-radius')) || self.defaultBoothBorderRadius;
                const opacity = parseFloat(element.style.opacity) || parseFloat(element.getAttribute('data-opacity')) || self.defaultBoothOpacity;
                const backgroundColor = element.style.backgroundColor || element.getAttribute('data-background-color') || self.defaultBackgroundColor;
                const borderColor = element.style.borderColor || element.getAttribute('data-border-color') || self.defaultBorderColor;
                const textColor = element.style.color || element.getAttribute('data-text-color') || self.defaultTextColor;
                const fontWeight = element.style.fontWeight || element.getAttribute('data-font-weight') || self.defaultFontWeight;
                const fontFamily = element.style.fontFamily || element.getAttribute('data-font-family') || self.defaultFontFamily;
                const textAlign = element.style.textAlign || element.getAttribute('data-text-align') || self.defaultTextAlign;
                const boxShadow = element.style.boxShadow || element.getAttribute('data-box-shadow') || self.defaultBoxShadow;
            
            // Update transform controls with final values (if visible)
            const controls = element.querySelector('.transform-controls');
            if (controls && (controls.style.display === 'flex' || controls.style.display === '')) {
                const xInput = controls.querySelector('.transform-x');
                const yInput = controls.querySelector('.transform-y');
                const wInput = controls.querySelector('.transform-w');
                const hInput = controls.querySelector('.transform-h');
                const rInput = controls.querySelector('.transform-r');
                    if (xInput) xInput.textContent = Math.round(finalX);
                    if (yInput) yInput.textContent = Math.round(finalY);
                if (wInput) wInput.textContent = Math.round(width);
                if (hInput) hInput.textContent = Math.round(height);
                if (rInput) rInput.textContent = Math.round(rotation);
            }
            
            // Save to database
            const boothId = element.getAttribute('data-booth-id');
                self.saveBoothPosition(boothId, finalX, finalY, width, height, rotation, zIndex, fontSize, borderWidth, borderRadius, opacity, backgroundColor, borderColor, textColor, fontWeight, fontFamily, textAlign, boxShadow);
            }
            
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
                // Update font size immediately for visual feedback
                element.style.fontSize = newFontSize + 'px';
                element.style.fontWeight = 'bold';
                element.setAttribute('data-font-size', newFontSize);
                
                // Update info toolbar if this booth is selected
                if (element.classList.contains('selected')) {
                    const infoFontSize = document.getElementById('infoFontSize');
                    if (infoFontSize && !infoFontSize.classList.contains('info-editing') && !infoFontSize.querySelector('input')) {
                        infoFontSize.textContent = Math.round(newFontSize);
                    }
                }
                
                // Debounce database save to prevent excessive API calls
                const boothId = element.getAttribute('data-booth-id');
                self.debounce('fontSize_' + boothId, function() {
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
                }, 500); // Wait 500ms after last wheel event before saving
            }
        }, { passive: false });
    },
    
    // Update properties panel
    // Update Information Toolbar with selected booth data
    updateInfoToolbar: function(element) {
        const self = this; // Ensure self refers to FloorPlanDesigner
        const infoToolbar = self.getElement('infoToolbar');
        if (!infoToolbar) return;
        
        // Safety check: ensure selectedBooths is initialized
        if (!self.selectedBooths || !Array.isArray(self.selectedBooths)) {
            self.selectedBooths = [];
        }
        
        // Check if ANY field is in edit mode - if so, don't update to preserve inputs
        const anyFieldEditing = document.querySelector('.info-value.info-editable.info-editing');
        if (anyFieldEditing) {
            return;
        }
        
        if (!element) {
            // Check if multiple booths are selected
            if (self.selectedBooths.length > 1) {
                // Show multi-select info
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
                
                // Show count or "Multiple" for multi-select
                if (infoX && !infoX.classList.contains('info-editing') && !infoX.querySelector('input')) {
                    infoX.textContent = self.selectedBooths.length + ' selected';
                }
                if (infoY && !infoY.classList.contains('info-editing') && !infoY.querySelector('input')) {
                    infoY.textContent = '';
                }
                if (infoW && !infoW.classList.contains('info-editing') && !infoW.querySelector('input')) {
                    infoW.textContent = '';
                }
                if (infoH && !infoH.classList.contains('info-editing') && !infoH.querySelector('input')) {
                    infoH.textContent = '';
                }
                if (infoR && !infoR.classList.contains('info-editing') && !infoR.querySelector('input')) {
                    infoR.textContent = '';
                }
                if (infoZ && !infoZ.classList.contains('info-editing') && !infoZ.querySelector('input')) {
                    infoZ.textContent = '';
                }
                if (infoFontSize && !infoFontSize.classList.contains('info-editing') && !infoFontSize.querySelector('input')) {
                    infoFontSize.textContent = '';
                }
                if (infoBorderWidth && !infoBorderWidth.classList.contains('info-editing') && !infoBorderWidth.querySelector('input')) {
                    infoBorderWidth.textContent = '';
                }
                if (infoBorderRadius && !infoBorderRadius.classList.contains('info-editing') && !infoBorderRadius.querySelector('input')) {
                    infoBorderRadius.textContent = '';
                }
                if (infoOpacity && !infoOpacity.classList.contains('info-editing') && !infoOpacity.querySelector('input')) {
                    infoOpacity.textContent = '';
                }
                if (infoStatus) {
                    infoStatus.textContent = self.selectedBooths.length + ' booths';
                }
                if (infoCompany) {
                    infoCompany.textContent = 'Multiple';
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
        
        // If not all fields were converted, try again after a short delay
        if (fieldsConverted < editableFields.length) {
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
    },
    
    // Apply value from info toolbar input to booth element(s) - supports multi-select
    applyInfoToolbarValue: function(field, property, value) {
        const self = this;
        
        // Exit edit mode
        field.classList.remove('info-editing');
        
        // Get selected booths
        if (self.selectedBooths.length === 0) {
            // No booth selected, just update display
            const displayValue = this.formatInfoToolbarValue(property, value);
            field.textContent = displayValue;
            return;
        }
        
        const numericValue = parseFloat(value) || 0;
        const isMultiSelect = self.selectedBooths.length > 1;
        
        // Apply value to all selected booths
        self.selectedBooths.forEach(function(element) {
        switch(property) {
                case 'x': {
                    let finalX = numericValue;
                    // Snap to grid (if snap is enabled)
                    if (self.snapEnabled) {
                        finalX = Math.round(numericValue / self.gridSize) * self.gridSize;
                    }
                    element.style.left = finalX + 'px';
                    element.setAttribute('data-x', finalX);
                break;
                }
                case 'y': {
                    let finalY = numericValue;
                    // Snap to grid (if snap is enabled)
                    if (self.snapEnabled) {
                        finalY = Math.round(numericValue / self.gridSize) * self.gridSize;
                    }
                    element.style.top = finalY + 'px';
                    element.setAttribute('data-y', finalY);
                break;
                }
                case 'w': {
                const w = Math.max(5, numericValue);
                element.style.width = w + 'px';
                element.setAttribute('data-width', w);
                    // Recalculate font size based on new width
                    const userFontSize = parseFloat(element.getAttribute('data-font-size')) || self.defaultBoothFontSize;
                    const calculatedFontSize = Math.min(userFontSize, Math.max(8, w * 0.45));
                    element.style.fontSize = calculatedFontSize + 'px';
                    element.setAttribute('data-calculated-font-size', calculatedFontSize);
                    self.updateResizeHandlesSize(element);
                break;
                }
                case 'h': {
                const h = Math.max(5, numericValue);
                element.style.height = h + 'px';
                element.setAttribute('data-height', h);
                    self.updateResizeHandlesSize(element);
                break;
                }
                case 'r': {
                element.style.transform = 'rotate(' + numericValue + 'deg)';
                element.setAttribute('data-rotation', numericValue);
                    self.updateRotationIndicator(element);
                break;
                }
                case 'z': {
                const z = Math.max(1, Math.min(1000, numericValue));
                element.style.zIndex = z;
                break;
                }
                case 'fontsize': {
                    const userFontSize = Math.max(8, Math.min(48, numericValue));
                    element.setAttribute('data-font-size', userFontSize);
                    const w = parseFloat(element.style.width) || 80;
                    const calculatedFontSize = Math.min(userFontSize, Math.max(8, w * 0.45));
                    element.style.fontSize = calculatedFontSize + 'px';
                    element.style.fontWeight = 'bold';
                    element.setAttribute('data-calculated-font-size', calculatedFontSize);
                break;
                }
                case 'borderwidth': {
                const borderWidth = Math.max(0, Math.min(10, numericValue));
                element.style.borderWidth = borderWidth + 'px';
                break;
                }
                case 'borderradius': {
                const borderRadius = Math.max(0, Math.min(50, numericValue));
                element.style.borderRadius = borderRadius + 'px';
                break;
                }
                case 'opacity': {
                const opacity = Math.max(0, Math.min(1, numericValue));
                element.style.opacity = opacity;
                break;
                }
        }
        
        // Save to database with all properties
        const boothId = element.getAttribute('data-booth-id');
        const x = parseFloat(element.style.left) || 0;
        const y = parseFloat(element.style.top) || 0;
        const w = parseFloat(element.style.width) || 80;
        const h = parseFloat(element.style.height) || 50;
        const r = parseFloat(element.getAttribute('data-rotation')) || 0;
            const z = parseFloat(element.style.zIndex) || parseFloat(element.getAttribute('data-z-index')) || self.defaultBoothZIndex;
            const fs = parseFloat(element.style.fontSize) || parseFloat(element.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const bw = parseFloat(element.style.borderWidth) || parseFloat(element.getAttribute('data-border-width')) || self.defaultBoothBorderWidth;
            const br = parseFloat(element.style.borderRadius) || parseFloat(element.getAttribute('data-border-radius')) || self.defaultBoothBorderRadius;
            const op = parseFloat(element.style.opacity) || parseFloat(element.getAttribute('data-opacity')) || self.defaultBoothOpacity;
            const bgColor = element.style.backgroundColor || element.getAttribute('data-background-color') || self.defaultBackgroundColor;
            const borderColor = element.style.borderColor || element.getAttribute('data-border-color') || self.defaultBorderColor;
            const textColor = element.style.color || element.getAttribute('data-text-color') || self.defaultTextColor;
            const fontWeight = element.style.fontWeight || element.getAttribute('data-font-weight') || self.defaultFontWeight;
            const fontFamily = element.style.fontFamily || element.getAttribute('data-font-family') || self.defaultFontFamily;
            const textAlign = element.style.textAlign || element.getAttribute('data-text-align') || self.defaultTextAlign;
            const boxShadow = element.style.boxShadow || element.getAttribute('data-box-shadow') || self.defaultBoxShadow;
            
            self.saveBoothPosition(boothId, x, y, w, h, r, z, fs, bw, br, op, bgColor, borderColor, textColor, fontWeight, fontFamily, textAlign, boxShadow);
        });
        
        // Update field display
        if (isMultiSelect) {
            // Show aggregate value or "Multiple" for multi-select
            field.textContent = self.selectedBooths.length + ' selected';
        } else {
            // Show single value
            const element = self.selectedBooths[0];
            switch(property) {
                case 'x':
                    field.textContent = Math.round(parseFloat(element.style.left) || 0);
                    break;
                case 'y':
                    field.textContent = Math.round(parseFloat(element.style.top) || 0);
                    break;
                case 'w':
                    field.textContent = Math.round(parseFloat(element.style.width) || 80);
                    break;
                case 'h':
                    field.textContent = Math.round(parseFloat(element.style.height) || 50);
                    break;
                case 'r':
                    field.textContent = numericValue + '°';
                    break;
                case 'z':
                    field.textContent = Math.round(parseFloat(element.style.zIndex) || 10);
                    break;
                case 'fontsize':
                    field.textContent = Math.round(parseFloat(element.style.fontSize) || 14);
                    break;
                case 'borderwidth':
                    field.textContent = Math.round(parseFloat(element.style.borderWidth) || 2);
                    break;
                case 'borderradius':
                    field.textContent = Math.round(parseFloat(element.style.borderRadius) || 6);
                    break;
                case 'opacity':
                    field.textContent = parseFloat(element.style.opacity || 1).toFixed(2);
                    break;
            }
        }
        
        // Update bounding box
        self.updateSelectionBoundingBox();
        
        // Update toolbar to reflect all values
        if (isMultiSelect) {
            self.updateInfoToolbar(null); // Update for multi-select
        } else {
            self.updateInfoToolbar(self.selectedBooths[0]);
        }
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
        
        // Get booth price from boothsData
        let boothPrice = 500; // Default price
        if (typeof window.boothsData !== 'undefined' && Array.isArray(window.boothsData)) {
            const boothData = window.boothsData.find(b => b.id == boothId);
            if (boothData && boothData.price !== undefined) {
                boothPrice = parseFloat(boothData.price) || 500;
            }
        }
        
        var propHtml = '';
        propHtml += '<h6 class="mb-3"><i class="fas fa-cube"></i> Booth: ' + boothNumber + '</h6>';
        propHtml += '<div class="mb-3"><strong>Booth Details</strong></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-dollar-sign"></i> Price:</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-price" value="' + boothPrice.toFixed(2) + '" min="0" step="0.01"></div>';
        propHtml += '<div class="mb-3 mt-3"><strong>Position</strong></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-arrows-alt-h"></i> Position X (px):</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-x" value="' + Math.round(x) + '" step="' + self.gridSize + '"></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-arrows-alt-v"></i> Position Y (px):</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-y" value="' + Math.round(y) + '" step="' + self.gridSize + '"></div>';
        propHtml += '<div class="mb-3 mt-3"><strong>Size</strong></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-arrows-alt-h"></i> Width (px):</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-w" value="' + Math.round(width) + '" min="5" step="1"></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-arrows-alt-v"></i> Height (px):</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-h" value="' + Math.round(height) + '" min="5" step="1"></div>';
        propHtml += '<div class="mb-3 mt-3"><strong>Transform</strong></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-redo"></i> Rotation (deg):</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-r" value="' + Math.round(rotation) + '" step="1"></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-layer-group"></i> Z-Index:</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-z" value="' + Math.round(zIndex) + '" min="1" max="1000" step="1"></div>';
        propHtml += '<div class="mb-3 mt-3"><strong>Appearance</strong></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-font"></i> Font Size (px):</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-fontsize" value="' + Math.round(fontSize) + '" min="8" max="48" step="1"></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-border-style"></i> Border Width (px):</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-borderwidth" value="' + Math.round(borderWidth) + '" min="0" max="10" step="1"></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-circle"></i> Border Radius (px):</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-borderradius" value="' + Math.round(borderRadius) + '" min="0" max="50" step="1"></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-adjust"></i> Opacity:</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-opacity" value="' + opacity.toFixed(2) + '" min="0" max="1" step="0.1"></div>';
        
        content.innerHTML = propHtml;
        
        // Helper function to apply grid snapping to position values (only if snap is enabled)
        const snapToGrid = function(value) {
            if (self.snapEnabled) {
            return Math.round(value / self.gridSize) * self.gridSize;
            }
            return value;
        };
        
        // Helper function to save booth position and properties
        const saveBoothProps = function() {
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
            const priceInput = content.querySelector('.prop-price');
            const price = priceInput ? parseFloat(priceInput.value) || null : null;
            // Save with price as last parameter
            self.saveBoothPosition(boothId, x, y, w, h, r, z, fs, bw, br, op, undefined, undefined, undefined, undefined, undefined, undefined, undefined, price);
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
            
            // Recalculate font size based on new width
            const userFontSize = parseFloat(element.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const calculatedFontSize = Math.min(userFontSize, Math.max(8, w * 0.45));
            element.style.fontSize = calculatedFontSize + 'px';
            element.style.fontWeight = 'bold';
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
        addWheelSupport(propW, 10, 5, undefined, function(val) {
            const w = Math.max(5, val);
            element.style.width = w + 'px';
            element.setAttribute('data-width', w);
            
            // Recalculate font size based on new width
            const userFontSize = parseFloat(element.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const calculatedFontSize = Math.min(userFontSize, Math.max(8, w * 0.45));
            element.style.fontSize = calculatedFontSize + 'px';
            element.style.fontWeight = 'bold';
            element.setAttribute('data-calculated-font-size', calculatedFontSize);
            
            self.updateResizeHandlesSize(element);
            self.updateRotationIndicator(element);
            self.updateInfoToolbar(element);
            saveBoothProps();
        });
        
        content.querySelector('.prop-h').addEventListener('change', function() {
            const h = Math.max(5, parseFloat(this.value) || 50);
            element.style.height = h + 'px';
            element.setAttribute('data-height', h);
            
            // Recalculate font size based on new width (height change doesn't affect font size, but update it anyway)
            const w = parseFloat(element.style.width) || 80;
            const userFontSize = parseFloat(element.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const calculatedFontSize = Math.min(userFontSize, Math.max(8, w * 0.45));
            element.style.fontSize = calculatedFontSize + 'px';
            element.style.fontWeight = 'bold';
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
                
                // Calculate actual font size based on booth width
                const w = parseFloat(element.style.width) || 80;
                const calculatedFontSize = Math.min(userFontSize, Math.max(8, w * 0.45));
                element.style.fontSize = calculatedFontSize + 'px';
                element.style.fontWeight = 'bold';
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
        
        // Add event listener for price field
        if (content.querySelector('.prop-price')) {
            content.querySelector('.prop-price').addEventListener('change', function() {
                const price = Math.max(0, parseFloat(this.value) || 500);
                this.value = price.toFixed(2); // Ensure 2 decimal places
                saveBoothProps();
                // Update boothsData if available
                if (typeof window.boothsData !== 'undefined' && Array.isArray(window.boothsData)) {
                    const boothData = window.boothsData.find(b => b.id == boothId);
                    if (boothData) {
                        boothData.price = price;
                    }
                }
            });
            // Add mouse wheel support for Price
            const propPrice = content.querySelector('.prop-price');
            addWheelSupport(propPrice, 10, 0, undefined, null);
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
                // Prevent resize if booth is locked
                if (element.classList.contains('locked')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }
                
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
            
            // Snap to grid (if snap is enabled)
            if (self.snapEnabled) {
            newWidth = Math.round(newWidth / self.gridSize) * self.gridSize;
            newHeight = Math.round(newHeight / self.gridSize) * self.gridSize;
            newLeft = Math.round(newLeft / self.gridSize) * self.gridSize;
            newTop = Math.round(newTop / self.gridSize) * self.gridSize;
            }
            
            // Apply new size and position
            element.style.width = newWidth + 'px';
            element.style.height = newHeight + 'px';
            element.style.left = newLeft + 'px';
            element.style.top = newTop + 'px';
            element.setAttribute('data-width', newWidth);
            element.setAttribute('data-height', newHeight);
            element.setAttribute('data-x', newLeft);
            element.setAttribute('data-y', newTop);
            
            // Recalculate font size based on new width
            const userFontSize = parseFloat(element.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const calculatedFontSize = Math.min(userFontSize, Math.max(8, newWidth * 0.45));
            element.style.fontSize = calculatedFontSize + 'px';
            element.style.fontWeight = 'bold';
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
            // Prevent rotate if booth is locked
            if (element.classList.contains('locked')) {
                e.preventDefault();
                e.stopPropagation();
                return;
            }
            
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
        
        // Get canvas dimensions (image size) - use actual image dimensions for accuracy
        // Prefer data attributes or stored dimensions over offsetWidth/offsetHeight
        const canvasWidth = parseFloat(canvas.getAttribute('data-canvas-width')) || 
                          self.canvasWidth || 
                          (canvas.offsetWidth > 0 ? canvas.offsetWidth : 1200);
        const canvasHeight = parseFloat(canvas.getAttribute('data-canvas-height')) || 
                           self.canvasHeight || 
                           (canvas.offsetHeight > 0 ? canvas.offsetHeight : 800);
        
        // Get container dimensions (viewport size)
        let containerWidth = container.clientWidth;
        let containerHeight = container.clientHeight;
        
        // Account for sidebar width if it's visible (not hidden and not collapsed)
        const sidebar = document.getElementById('designerSidebar');
        if (sidebar && !sidebar.classList.contains('hidden') && !sidebar.classList.contains('collapsed')) {
            const sidebarWidth = sidebar.offsetWidth || 280;
            containerWidth = containerWidth - sidebarWidth;
        } else if (sidebar && sidebar.classList.contains('collapsed')) {
            // If collapsed, account for 50px width
            containerWidth = containerWidth - 50;
        }
        
        // Ensure we have valid dimensions
        if (containerWidth <= 0) containerWidth = container.clientWidth;
        if (containerHeight <= 0) containerHeight = container.clientHeight;
        
        // Calculate scale to fit entire canvas in viewport
        // Use the smaller scale to ensure entire image is visible
        const scaleX = containerWidth / canvasWidth;
        const scaleY = containerHeight / canvasHeight;
        const fitScale = Math.min(scaleX, scaleY) * 0.95; // 95% to add some padding around edges
        
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
            // Account for sidebar offset if visible
            let viewportCenterX = container.clientWidth / 2;
            const viewportCenterY = container.clientHeight / 2;
            
            if (sidebar && !sidebar.classList.contains('hidden') && !sidebar.classList.contains('collapsed')) {
                const sidebarWidth = sidebar.offsetWidth || 280;
                // Adjust center X to account for sidebar, but still center the image in the available space
                viewportCenterX = sidebarWidth + (containerWidth / 2);
            } else if (sidebar && sidebar.classList.contains('collapsed')) {
                // If collapsed, account for 50px width
                viewportCenterX = 50 + (containerWidth / 2);
            }
            
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
            if ($('#zoomLevel').length) {
            $('#zoomLevel').text(Math.round(currentScale * 100) + '%');
            }
        }, animate !== false ? 200 : 50);
    },
    
    // Save booth position, size, and rotation
    saveBoothPosition: function(boothId, x, y, width, height, rotation, zIndex, fontSize, borderWidth, borderRadius, opacity, backgroundColor, borderColor, textColor, fontWeight, fontFamily, textAlign, boxShadow, price) {
        const canvas = document.getElementById('print');
        const boothElement = canvas ? canvas.querySelector('[data-booth-id="' + boothId + '"]') : null;
        
        // Get price from boothsData if not provided
        if (price === undefined && typeof window.boothsData !== 'undefined' && Array.isArray(window.boothsData)) {
            const boothData = window.boothsData.find(b => b.id == boothId);
            if (boothData && boothData.price !== undefined) {
                price = parseFloat(boothData.price);
            }
        }
        
        // Get style properties from element if not provided
        if (boothElement) {
            zIndex = zIndex !== undefined ? zIndex : (parseFloat(boothElement.style.zIndex) || 10);
            fontSize = fontSize !== undefined ? fontSize : (parseFloat(boothElement.style.fontSize) || 14);
            borderWidth = borderWidth !== undefined ? borderWidth : (parseFloat(boothElement.style.borderWidth) || 2);
            borderRadius = borderRadius !== undefined ? borderRadius : (parseFloat(boothElement.style.borderRadius) || 6);
            opacity = opacity !== undefined ? opacity : (parseFloat(boothElement.style.opacity) || 1.00);
            
            // Get appearance properties from element if not provided
            backgroundColor = backgroundColor !== undefined ? backgroundColor : (boothElement.style.backgroundColor || boothElement.getAttribute('data-background-color') || this.defaultBackgroundColor);
            borderColor = borderColor !== undefined ? borderColor : (boothElement.style.borderColor || boothElement.getAttribute('data-border-color') || this.defaultBorderColor);
            textColor = textColor !== undefined ? textColor : (boothElement.style.color || boothElement.getAttribute('data-text-color') || this.defaultTextColor);
            fontWeight = fontWeight !== undefined ? fontWeight : (boothElement.style.fontWeight || boothElement.getAttribute('data-font-weight') || this.defaultFontWeight);
            fontFamily = fontFamily !== undefined ? fontFamily : (boothElement.style.fontFamily || boothElement.getAttribute('data-font-family') || this.defaultFontFamily);
            textAlign = textAlign !== undefined ? textAlign : (boothElement.style.textAlign || boothElement.getAttribute('data-text-align') || this.defaultTextAlign);
            boxShadow = boxShadow !== undefined ? boxShadow : (boothElement.style.boxShadow || boothElement.getAttribute('data-box-shadow') || this.defaultBoxShadow);
        } else {
            zIndex = zIndex || 10;
            fontSize = fontSize || 14;
            borderWidth = borderWidth || 2;
            borderRadius = borderRadius || 6;
            opacity = opacity !== undefined ? opacity : 1.00;
            backgroundColor = backgroundColor || this.defaultBackgroundColor;
            borderColor = borderColor || this.defaultBorderColor;
            textColor = textColor || this.defaultTextColor;
            fontWeight = fontWeight || this.defaultFontWeight;
            fontFamily = fontFamily || this.defaultFontFamily;
            textAlign = textAlign || this.defaultTextAlign;
            boxShadow = boxShadow || this.defaultBoxShadow;
        }
        
        const payload = {
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
        };
        
        // Add price if provided
        if (price !== undefined && price !== null) {
            payload.price = price;
        }
        
        // Add appearance properties if provided
        if (backgroundColor !== undefined && backgroundColor !== null) {
            payload.background_color = backgroundColor;
        }
        if (borderColor !== undefined && borderColor !== null) {
            payload.border_color = borderColor;
        }
        if (textColor !== undefined && textColor !== null) {
            payload.text_color = textColor;
        }
        if (fontWeight !== undefined && fontWeight !== null) {
            payload.font_weight = fontWeight;
        }
        if (fontFamily !== undefined && fontFamily !== null) {
            payload.font_family = fontFamily;
        }
        if (textAlign !== undefined && textAlign !== null) {
            payload.text_align = textAlign;
        }
        if (boxShadow !== undefined && boxShadow !== null) {
            payload.box_shadow = boxShadow;
        }
        
        return fetch('/booths/' + boothId + '/save-position', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
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
    
    // Batch save multiple booths at once (much faster than individual saves)
    saveBoothsBatch: function(boothsData) {
        if (!boothsData || boothsData.length === 0) {
            console.warn('saveBoothsBatch: No booths data provided');
            return Promise.resolve({ saved: 0, total: 0 });
        }
        
        console.log('saveBoothsBatch: Saving', boothsData.length, 'booths:', boothsData);
        
        return fetch('/booths/save-all-positions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                booths: boothsData
            })
        })
        .then(function(response) {
            console.log('saveBoothsBatch: Response status:', response.status, response.statusText);
            if (!response.ok) {
                return response.json().then(function(data) {
                    console.error('saveBoothsBatch: Error response:', data);
                    if (data.errors) {
                        console.error('saveBoothsBatch: Validation errors:', JSON.stringify(data.errors, null, 2));
                        // Log each validation error
                        Object.keys(data.errors).forEach(function(key) {
                            console.error('  -', key + ':', data.errors[key]);
                        });
                    }
                    throw new Error(data.message || 'Failed to save booths');
                });
            }
            return response.json();
        })
        .then(function(data) {
            console.log('✅ Batch saved ' + data.saved + ' out of ' + data.total + ' booth(s)');
            if (data.errors && data.errors.length > 0) {
                console.warn('⚠️ Some booths failed to save:', data.errors);
                data.errors.forEach(function(error) {
                    console.error('Booth ID', error.booth_id, 'error:', error.error);
                });
            }
            if (data.saved === 0 && data.total > 0) {
                console.error('❌ CRITICAL: No booths were saved! Check validation errors above.');
            }
            return data;
        })
        .catch(function(error) {
            console.error('❌ Error batch saving booths:', error);
            console.error('Error details:', error.message, error.stack);
            return { error: error.message, saved: 0, total: boothsData.length };
        });
    },
    
    // Save all booths on the canvas
    saveAllBooths: function() {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) {
            customAlert('Canvas not found!', 'error');
            return;
        }
        
        // Get all booths on the canvas
        const booths = canvas.querySelectorAll('.dropped-booth');
        if (booths.length === 0) {
            customAlert('No booths to save!', 'warning');
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
            
            // Get appearance properties
            const backgroundColor = booth.style.backgroundColor || booth.getAttribute('data-background-color') || self.defaultBackgroundColor;
            const borderColor = booth.style.borderColor || booth.getAttribute('data-border-color') || self.defaultBorderColor;
            const textColor = booth.style.color || booth.getAttribute('data-text-color') || self.defaultTextColor;
            const fontWeight = booth.style.fontWeight || booth.getAttribute('data-font-weight') || self.defaultFontWeight;
            const fontFamily = booth.style.fontFamily || booth.getAttribute('data-font-family') || self.defaultFontFamily;
            const textAlign = booth.style.textAlign || booth.getAttribute('data-text-align') || self.defaultTextAlign;
            const boxShadow = booth.style.boxShadow || booth.getAttribute('data-box-shadow') || self.defaultBoxShadow;
            
            // Get lock state
            const isLocked = booth.classList.contains('locked') || booth.getAttribute('data-locked') === 'true';
            
            // Get price from boothsData if available
            let price = null;
            if (typeof window.boothsData !== 'undefined' && Array.isArray(window.boothsData)) {
                const boothInfo = window.boothsData.find(b => b.id == boothId);
                if (boothInfo && boothInfo.price !== undefined) {
                    price = parseFloat(boothInfo.price) || null;
                }
            }
            
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
                    opacity: opacity,
                    price: price,
                    // Appearance properties
                    background_color: backgroundColor,
                    border_color: borderColor,
                    text_color: textColor,
                    font_weight: fontWeight,
                    font_family: fontFamily,
                    text_align: textAlign,
                    box_shadow: boxShadow,
                    // Lock state
                    is_locked: isLocked ? 1 : 0
                });
            }
        });
        
        if (boothData.length === 0) {
            saveBtn.prop('disabled', false);
            saveBtn.html(originalText);
            customAlert('No booths with valid positions to save!', 'warning');
            return;
        }
        
        // Save both booths and canvas settings
        Promise.all([
            // Save booth positions and properties
            fetch('/booths/save-all-positions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    booths: boothData
                })
            }),
            // Save canvas settings (grid, snap, zoom, pan, etc.)
            self.saveCanvasSettingsToDatabase()
        ])
        .then(function(results) {
            const boothResponse = results[0];
            return boothResponse.json().then(function(data) {
                return { boothData: data, canvasSaved: true };
            });
        })
        .then(function(result) {
            // Success
            saveBtn.prop('disabled', false);
            saveBtn.html('<i class="fas fa-check"></i> Saved!');
            
            // Show success message
            let successMsg = 'Successfully saved ' + result.boothData.saved + ' out of ' + result.boothData.total + ' booth(s)!';
            if (result.canvasSaved) {
                successMsg += '\n\nCanvas settings saved:\n';
                successMsg += '• Grid: ' + (self.gridEnabled ? 'Visible' : 'Hidden') + '\n';
                successMsg += '• Snap to Grid: ' + (self.snapEnabled ? 'Enabled' : 'Disabled') + '\n';
                successMsg += '• Grid Size: ' + self.gridSize + 'px\n';
                successMsg += '• Canvas Size: ' + self.canvasWidth + 'x' + self.canvasHeight + 'px\n';
                if (self.panzoomInstance) {
                    try {
                        const scale = self.panzoomInstance.getScale();
                        if (scale) {
                            successMsg += '• Zoom: ' + Math.round(scale * 100) + '%\n';
                        }
                    } catch (e) {}
                }
            }
            let alertType = 'success';
            if (result.boothData.errors && result.boothData.errors.length > 0) {
                successMsg += '\n\nErrors: ' + result.boothData.errors.length + ' booth(s) failed to save.';
                alertType = 'warning';
                console.warn('Save errors:', result.boothData.errors);
            }
            customAlert(successMsg, alertType);
            
            // Save lock states to localStorage for persistence
            const lockStates = {};
            booths.forEach(function(booth) {
                const boothId = booth.getAttribute('data-booth-id');
                if (boothId) {
                    const isLocked = booth.classList.contains('locked') || booth.getAttribute('data-locked') === 'true';
                    lockStates[boothId] = isLocked;
                }
            });
            try {
                localStorage.setItem('booth_lock_states', JSON.stringify(lockStates));
            } catch (e) {
                console.warn('Could not save lock states to localStorage:', e);
            }
            
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
            customAlert('Error saving booths. Please check the console for details.', 'error');
        });
    },
    
    // Set canvas size
    setCanvasSize: function(width, height) {
        const canvas = document.getElementById('print');
        const container = document.getElementById('printContainer');
        if (!canvas) return;
        
        // Use exact image dimensions for accurate click coordinates
        this.canvasWidth = width;
        this.canvasHeight = height;
        
        // Set canvas dimensions to EXACT image dimensions (not minimum 10000px)
        // This ensures click coordinates are accurate relative to the image
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        canvas.style.minWidth = width + 'px';
        canvas.style.minHeight = height + 'px';
        canvas.style.maxWidth = width + 'px';
        canvas.style.maxHeight = height + 'px';
        canvas.style.flexShrink = '0';
        
        // Ensure background image fills entire canvas and positioned at top-left
        canvas.style.backgroundSize = width + 'px ' + height + 'px';
        canvas.style.backgroundRepeat = 'no-repeat';
        canvas.style.backgroundPosition = 'top left';
        canvas.style.backgroundAttachment = 'local';
        canvas.style.margin = '0';
        canvas.style.display = 'block';
        
        // Set data attributes for reference
        canvas.setAttribute('data-canvas-width', width);
        canvas.setAttribute('data-canvas-height', height);
        
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
        
        // Save canvas settings to database
        const self = this;
        setTimeout(function() {
            self.saveCanvasSettingsToDatabase().catch(function(error) {
                // Silently fail - don't interrupt user workflow
            });
        }, 500); // Debounce saves
    },
    
    // Save canvas settings to database
    // This function saves ALL canvas settings including grid, snap, zoom, pan, dimensions, etc.
    saveCanvasSettingsToDatabase: function() {
        const self = this;
        
        // Get current floor plan ID (floor-plan-specific settings)
        const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
        
        // Prepare settings object - Save EVERYTHING that the canvas has
        const settings = {};
        
        // Include floor_plan_id for floor-plan-specific canvas settings
        if (floorPlanId) {
            settings.floor_plan_id = floorPlanId;
        }
        
        // Canvas dimensions
        if (self.canvasWidth !== undefined && self.canvasWidth !== null) {
            settings.canvas_width = parseInt(self.canvasWidth) || 1200;
        }
        if (self.canvasHeight !== undefined && self.canvasHeight !== null) {
            settings.canvas_height = parseInt(self.canvasHeight) || 800;
        }
        if (self.canvasResolution !== undefined && self.canvasResolution !== null) {
            settings.canvas_resolution = parseInt(self.canvasResolution) || 300;
        }
        
        // Grid settings - Always save current state
        if (self.gridSize !== undefined && self.gridSize !== null) {
            settings.grid_size = parseInt(self.gridSize) || 10;
        }
        // Save grid enabled state (show/hide grid) - Always save current state
        settings.grid_enabled = self.gridEnabled !== undefined ? Boolean(self.gridEnabled) : false;
        // Save snap to grid state - Always save current state
        settings.snap_to_grid = self.snapEnabled !== undefined ? Boolean(self.snapEnabled) : false;
        
        // Get zoom and pan if panzoom is initialized
        if (self.panzoomInstance) {
            try {
                if (self.panzoomInstance.getScale) {
                    const scale = self.panzoomInstance.getScale();
                    if (scale !== undefined && scale !== null && !isNaN(scale)) {
                        settings.zoom_level = parseFloat(scale);
                    }
                }
                if (self.panzoomInstance.getTransform) {
                    const transform = self.panzoomInstance.getTransform();
                    if (transform) {
                        if (transform.x !== undefined && transform.x !== null) {
                            settings.pan_x = parseFloat(transform.x) || 0;
                        }
                        if (transform.y !== undefined && transform.y !== null) {
                            settings.pan_y = parseFloat(transform.y) || 0;
                        }
                    }
                }
            } catch (e) {
                // Panzoom not ready yet, skip zoom/pan
            }
        }
        
        // Get floorplan image path - prefer floor_plans.floor_image over canvas_settings
        // Don't save floorplan_image to canvas_settings - it's already in floor_plans table
        // Canvas settings should only store zoom/pan/grid, not the image path
        // The image path is managed in floor_plans.floor_image and loaded from there
        // This prevents conflicts when switching between floor plans
        
        // Don't save if no valid settings (except floor_plan_id)
        const settingsWithoutFloorPlanId = { ...settings };
        delete settingsWithoutFloorPlanId.floor_plan_id;
        if (Object.keys(settingsWithoutFloorPlanId).length === 0) {
            return Promise.resolve({ status: 200, message: 'No settings to save' });
        }
        
        return fetch('/settings/canvas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(settings)
        })
        .then(function(response) {
            if (!response.ok) {
                return response.json().then(function(data) {
                    throw new Error(data.message || 'Failed to save canvas settings to database');
                });
            }
            return response.json();
        })
        .then(function(data) {
            if (data.status === 200) {
                // Also save to localStorage as backup
                localStorage.setItem('canvasWidth', self.canvasWidth);
                localStorage.setItem('canvasHeight', self.canvasHeight);
                localStorage.setItem('canvasResolution', self.canvasResolution);
                localStorage.setItem('gridSize', self.gridSize);
                localStorage.setItem('gridEnabled', self.gridEnabled);
                localStorage.setItem('snapEnabled', self.snapEnabled);
                console.log('[Canvas Settings] All canvas settings saved:', settings);
                return data;
            }
        })
        .catch(function(error) {
            // Silently fail - don't interrupt user workflow
            // Still save to localStorage as fallback
            localStorage.setItem('canvasWidth', self.canvasWidth);
            localStorage.setItem('canvasHeight', self.canvasHeight);
            localStorage.setItem('canvasResolution', self.canvasResolution);
            localStorage.setItem('gridSize', self.gridSize);
            localStorage.setItem('gridEnabled', self.gridEnabled);
            localStorage.setItem('snapEnabled', self.snapEnabled);
            console.warn('[Canvas Settings] Failed to save to database, saved to localStorage:', error);
        });
    },
    
    // Set grid size
    setGridSize: function(size) {
        this.gridSize = size;
        
        // Update grid overlay CSS
        const gridOverlay = document.getElementById('gridOverlay');
        if (gridOverlay) {
            gridOverlay.style.backgroundSize = size + 'px ' + size + 'px';
        }
        
        // Save to database
        const self = this;
        setTimeout(function() {
            self.saveCanvasSettingsToDatabase().catch(function(error) {
                // Silently fail
            });
        }, 500);
    },
    
    // Load canvas settings from localStorage
    // Load booth default settings from database
    loadBoothSettingsFromDatabase: function() {
        const self = this;
        return fetch('/settings/booth-defaults', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Failed to load settings from database');
            }
            return response.json();
        })
        .then(function(data) {
            if (data.status === 200 && data.data) {
                // Update defaults from database
                self.defaultBoothWidth = data.data.width || 80;
                self.defaultBoothHeight = data.data.height || 50;
                self.defaultBoothRotation = data.data.rotation || 0;
                self.defaultBoothZIndex = data.data.z_index || 10;
                self.defaultBoothFontSize = data.data.font_size || 14;
                self.defaultBoothBorderWidth = data.data.border_width || 2;
                self.defaultBoothBorderRadius = data.data.border_radius || 6;
                self.defaultBoothOpacity = data.data.opacity || 1.00;
                self.defaultBackgroundColor = data.data.background_color || '#ffffff';
                self.defaultBorderColor = data.data.border_color || '#007bff';
                self.defaultTextColor = data.data.text_color || '#000000';
                self.defaultFontWeight = data.data.font_weight || '700';
                self.defaultFontFamily = data.data.font_family || 'Arial, sans-serif';
                self.defaultTextAlign = data.data.text_align || 'center';
                self.defaultBoxShadow = data.data.box_shadow || '0 2px 8px rgba(0,0,0,0.2)';
                
                // Also save to localStorage as cache/fallback
                self.saveBoothSettingsToLocalStorage();
            }
        })
        .catch(function(error) {
            console.warn('Failed to load settings from database, using localStorage fallback:', error);
            // Fallback to localStorage if database fails
            self.loadBoothSettingsFromLocalStorage();
        });
    },
    
    // Load booth default settings from localStorage (fallback)
    loadBoothSettingsFromLocalStorage: function() {
        const savedWidth = localStorage.getItem('defaultBoothWidth');
        const savedHeight = localStorage.getItem('defaultBoothHeight');
        const savedRotation = localStorage.getItem('defaultBoothRotation');
        const savedZIndex = localStorage.getItem('defaultBoothZIndex');
        const savedFontSize = localStorage.getItem('defaultBoothFontSize');
        const savedBorderWidth = localStorage.getItem('defaultBoothBorderWidth');
        const savedBorderRadius = localStorage.getItem('defaultBoothBorderRadius');
        const savedOpacity = localStorage.getItem('defaultBoothOpacity');
        const savedBackgroundColor = localStorage.getItem('defaultBackgroundColor');
        const savedBorderColor = localStorage.getItem('defaultBorderColor');
        const savedTextColor = localStorage.getItem('defaultTextColor');
        const savedFontWeight = localStorage.getItem('defaultFontWeight');
        const savedFontFamily = localStorage.getItem('defaultFontFamily');
        const savedTextAlign = localStorage.getItem('defaultTextAlign');
        const savedBoxShadow = localStorage.getItem('defaultBoxShadow');
        
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
        if (savedBackgroundColor) {
            this.defaultBackgroundColor = savedBackgroundColor;
        }
        if (savedBorderColor) {
            this.defaultBorderColor = savedBorderColor;
        }
        if (savedTextColor) {
            this.defaultTextColor = savedTextColor;
        }
        if (savedFontWeight) {
            this.defaultFontWeight = savedFontWeight;
        }
        if (savedFontFamily) {
            this.defaultFontFamily = savedFontFamily;
        }
        if (savedTextAlign) {
            this.defaultTextAlign = savedTextAlign;
        }
        if (savedBoxShadow) {
            this.defaultBoxShadow = savedBoxShadow;
        }
    },
    
    // Save booth default settings to database
    saveBoothSettingsToDatabase: function() {
        const self = this;
        return fetch('/settings/booth-defaults', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                width: this.defaultBoothWidth,
                height: this.defaultBoothHeight,
                rotation: this.defaultBoothRotation,
                z_index: this.defaultBoothZIndex,
                font_size: this.defaultBoothFontSize,
                border_width: this.defaultBoothBorderWidth,
                border_radius: this.defaultBoothBorderRadius,
                opacity: this.defaultBoothOpacity,
                background_color: this.defaultBackgroundColor,
                border_color: this.defaultBorderColor,
                text_color: this.defaultTextColor,
                font_weight: this.defaultFontWeight,
                font_family: this.defaultFontFamily,
                text_align: this.defaultTextAlign,
                box_shadow: this.defaultBoxShadow
            })
        })
        .then(function(response) {
            if (!response.ok) {
                return response.json().then(function(data) {
                    throw new Error(data.message || 'Failed to save settings to database');
                });
            }
            return response.json();
        })
        .then(function(data) {
            if (data.status === 200) {
                // Also save to localStorage as cache
                self.saveBoothSettingsToLocalStorage();
                return data;
            }
        })
        .catch(function(error) {
            console.error('Failed to save settings to database:', error);
            // Still save to localStorage as fallback
            self.saveBoothSettingsToLocalStorage();
            throw error;
        });
    },
    
    // Save booth default settings to localStorage (cache/fallback)
    saveBoothSettingsToLocalStorage: function() {
        localStorage.setItem('defaultBoothWidth', this.defaultBoothWidth);
        localStorage.setItem('defaultBoothHeight', this.defaultBoothHeight);
        localStorage.setItem('defaultBoothRotation', this.defaultBoothRotation);
        localStorage.setItem('defaultBoothZIndex', this.defaultBoothZIndex);
        localStorage.setItem('defaultBoothFontSize', this.defaultBoothFontSize);
        localStorage.setItem('defaultBoothBorderWidth', this.defaultBoothBorderWidth);
        localStorage.setItem('defaultBoothBorderRadius', this.defaultBoothBorderRadius);
        localStorage.setItem('defaultBoothOpacity', this.defaultBoothOpacity);
        localStorage.setItem('defaultBackgroundColor', this.defaultBackgroundColor);
        localStorage.setItem('defaultBorderColor', this.defaultBorderColor);
        localStorage.setItem('defaultTextColor', this.defaultTextColor);
        localStorage.setItem('defaultFontWeight', this.defaultFontWeight);
        localStorage.setItem('defaultFontFamily', this.defaultFontFamily);
        localStorage.setItem('defaultTextAlign', this.defaultTextAlign);
        localStorage.setItem('defaultBoxShadow', this.defaultBoxShadow);
    },
    
    loadCanvasSettings: function() {
        const self = this;
        
        // Get current floor plan ID (floor-plan-specific settings)
        // CRITICAL: Always get from PHP variable to ensure correct floor plan
        const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
        
        // Debug logging
        console.log('[Canvas Settings] Loading settings for floor plan ID:', floorPlanId);
        
        // Build URL with floor_plan_id query parameter if available
        let canvasSettingsUrl = '/settings/canvas';
        if (floorPlanId) {
            canvasSettingsUrl += '?floor_plan_id=' + floorPlanId;
        }
        
        console.log('[Canvas Settings] Request URL:', canvasSettingsUrl);
        
        // Load from database first, fallback to localStorage
        fetch(canvasSettingsUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Failed to load canvas settings from database');
            }
            return response.json();
        })
        .then(function(data) {
            if (data.status === 200 && data.data) {
                // Load from database
                const settings = data.data;
                
                console.log('[Canvas Settings] Received settings:', {
                    floor_plan_id: settings.floor_plan_id,
                    floorplan_image: settings.floorplan_image,
                    canvas_width: settings.canvas_width,
                    canvas_height: settings.canvas_height
                });
                
                self.canvasWidth = settings.canvas_width || 1200;
                self.canvasHeight = settings.canvas_height || 800;
                self.canvasResolution = settings.canvas_resolution || 300;
                self.setGridSize(settings.grid_size || 10);
                self.gridEnabled = settings.grid_enabled !== undefined ? settings.grid_enabled : true;
                self.snapEnabled = settings.snap_to_grid !== undefined ? settings.snap_to_grid : false;
                
                // Restore grid visibility state in UI
                const gridOverlay = $('#gridOverlay');
                const btnGrid = $('#btnGrid');
                if (self.gridEnabled) {
                    gridOverlay.addClass('visible');
                    btnGrid.addClass('active');
                } else {
                    gridOverlay.removeClass('visible');
                    btnGrid.removeClass('active');
                }
                
                // Restore snap to grid state in UI
                const btnSnap = $('#btnSnap');
                btnSnap.toggleClass('active', self.snapEnabled);
                if (self.snapEnabled) {
                    btnSnap.attr('title', 'Snap to Grid: ON (Click to disable)').css('background', 'rgba(40, 167, 69, 0.3)');
                } else {
                    btnSnap.attr('title', 'Snap to Grid: OFF (Click to enable)').css('background', 'rgba(108, 117, 125, 0.3)');
                }
                
                // Apply saved dimensions
                self.setCanvasSize(self.canvasWidth, self.canvasHeight);
                
                // Restore zoom and pan if available
                if (settings.zoom_level && self.panzoomInstance) {
                    self.panzoomInstance.zoom(settings.zoom_level);
                }
                if (settings.pan_x !== undefined && settings.pan_y !== undefined && self.panzoomInstance) {
                    // Use panzoom's move method instead of moveTo (which doesn't exist)
                    if (self.panzoomInstance.move) {
                        self.panzoomInstance.move(settings.pan_x, settings.pan_y);
                    } else if (self.panzoomInstance.setTransform) {
                        const currentScale = self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : 1;
                        self.panzoomInstance.setTransform({ x: settings.pan_x, y: settings.pan_y, scale: currentScale });
                    }
                }
                
                // CRITICAL: Restore floorplan image from floor_plans.floor_image (source of truth)
                // The settings.floorplan_image comes from floor_plans.floor_image (prioritized in backend)
                // CRITICAL FIX: ALWAYS set the image from settings if it exists - don't be conservative
                const canvas = document.getElementById('print');
                if (canvas) {
                    console.log('[Canvas Settings] Processing floorplan_image:', {
                        floorplan_image: settings.floorplan_image,
                        floor_plan_id_from_settings: settings.floor_plan_id,
                        current_floor_plan_id: floorPlanId,
                        current_floorplanImage: self.floorplanImage,
                        current_floorPlanImageUrl: self.floorPlanImageUrl,
                        current_canvas_background: canvas.style.backgroundImage
                    });
                    
                    // CRITICAL: If settings has floorplan_image and it matches the current floor plan, ALWAYS use it
                    // This ensures the canvas always shows the correct image after upload
                    
                    if (settings.floorplan_image && settings.floor_plan_id === floorPlanId) {
                        // Convert relative path to full URL
                        let imageUrl = settings.floorplan_image;
                        if (!imageUrl.startsWith('http') && !imageUrl.startsWith('/')) {
                            imageUrl = '/' + imageUrl;
                        }
                        if (!imageUrl.startsWith('http')) {
                            imageUrl = window.location.origin + (imageUrl.startsWith('/') ? '' : '/') + imageUrl;
                        }
                        
                        // ALWAYS set the image from settings (don't check if already loaded - force refresh)
                        console.log('[Canvas Settings] ✅ FORCING canvas background image from settings:', imageUrl);
                        canvas.style.backgroundImage = 'url(\'' + imageUrl + '?t=' + Date.now() + '\')';
                        canvas.style.backgroundSize = '100% 100%';
                        canvas.style.backgroundRepeat = 'no-repeat';
                        canvas.style.backgroundPosition = 'top left';
                        canvas.style.backgroundAttachment = 'local';
                        canvas.style.margin = '0';
                        canvas.style.display = 'block';
                        canvas.style.float = 'left';
                        
                        // Store relative path and full URL
                        self.floorplanImage = settings.floorplan_image;
                        self.floorPlanImageUrl = imageUrl;
                        
                        console.log('[Canvas Settings] ✅ Canvas background image SET SUCCESSFULLY from settings');
                    } else if (settings.floorplan_image && settings.floor_plan_id !== floorPlanId) {
                        // Settings for different floor plan - preserve current image
                        console.warn('[Canvas Settings] ⚠️ Floor plan ID mismatch - preserving current image', {
                            settings_floor_plan_id: settings.floor_plan_id,
                            current_floor_plan_id: floorPlanId
                        });
                    } else if (!settings.floorplan_image) {
                        // No image in settings - check if canvas already has image from Blade template
                        console.log('[Canvas Settings] No floorplan_image in settings for floor plan:', floorPlanId);
                        if (canvas.style.backgroundImage && canvas.style.backgroundImage !== 'none') {
                            console.log('[Canvas Settings] ✅ Canvas already has image from Blade template, preserving it');
                            // Ensure variables are set from current floor plan
                            @if(isset($currentFloorPlan) && $currentFloorPlan && $currentFloorPlan->floor_image)
                                @php
                                    $fallbackImageUrl = asset($currentFloorPlan->floor_image);
                                    $fallbackImagePath = $currentFloorPlan->floor_image;
                                @endphp
                                if (!self.floorplanImage) {
                                    self.floorplanImage = '{{ $fallbackImagePath }}';
                                }
                                if (!self.floorPlanImageUrl) {
                                    self.floorPlanImageUrl = '{{ $fallbackImageUrl }}';
                                }
                            @endif
                        } else {
                            console.warn('[Canvas Settings] ⚠️ No image in settings AND canvas has no image - floor plan may not have image uploaded');
                        }
                    }
                }
            }
        })
        .catch(function(error) {
            console.warn('Failed to load canvas settings from database, using localStorage fallback:', error);
            // Fallback to localStorage
            const savedWidth = localStorage.getItem('canvasWidth');
            const savedHeight = localStorage.getItem('canvasHeight');
            const savedResolution = localStorage.getItem('canvasResolution');
            const savedGridSize = localStorage.getItem('gridSize');
            const savedGridEnabled = localStorage.getItem('gridEnabled');
            const savedSnapEnabled = localStorage.getItem('snapEnabled');
            
            if (savedWidth) {
                self.canvasWidth = parseInt(savedWidth);
            }
            if (savedHeight) {
                self.canvasHeight = parseInt(savedHeight);
            }
            if (savedResolution) {
                self.canvasResolution = parseInt(savedResolution);
            }
            if (savedGridSize) {
                self.setGridSize(parseInt(savedGridSize));
            }
            if (savedGridEnabled !== null) {
                self.gridEnabled = savedGridEnabled === 'true';
                // Update UI
                const gridOverlay = $('#gridOverlay');
                const btnGrid = $('#btnGrid');
                if (self.gridEnabled) {
                    gridOverlay.addClass('visible');
                    btnGrid.addClass('active');
                } else {
                    gridOverlay.removeClass('visible');
                    btnGrid.removeClass('active');
                }
            }
            if (savedSnapEnabled !== null) {
                self.snapEnabled = savedSnapEnabled === 'true';
                // Update UI
                const btnSnap = $('#btnSnap');
                btnSnap.toggleClass('active', self.snapEnabled);
                if (self.snapEnabled) {
                    btnSnap.attr('title', 'Snap to Grid: ON (Click to disable)').css('background', 'rgba(40, 167, 69, 0.3)');
                } else {
                    btnSnap.attr('title', 'Snap to Grid: OFF (Click to enable)').css('background', 'rgba(108, 117, 125, 0.3)');
                }
            }
            
            // Apply saved dimensions
            if (savedWidth && savedHeight) {
                self.setCanvasSize(self.canvasWidth, self.canvasHeight);
            }
        });
        
        // Load upload size limit from localStorage (not critical for persistence)
        const savedUploadSizeLimit = localStorage.getItem('uploadSizeLimit');
        if (savedUploadSizeLimit) {
            this.uploadSizeLimit = parseInt(savedUploadSizeLimit);
        }
        
        // Prevent canvas from resizing when browser window resizes
        // This ensures booths stay in their fixed positions
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
        const boothsToAddToSidebar = [];
        
        // Collect all booth data first (for instant removal)
        allBooths.forEach(function(booth) {
            const boothId = booth.getAttribute('data-booth-id');
            const boothNumber = booth.getAttribute('data-booth-number') || booth.textContent.trim();
            const boothStatus = booth.getAttribute('data-booth-status') || '1';
            const clientId = booth.getAttribute('data-client-id') || '';
            const userId = booth.getAttribute('data-user-id') || '';
            const categoryId = booth.getAttribute('data-category-id') || '';
            const subCategoryId = booth.getAttribute('data-sub-category-id') || '';
            const assetId = booth.getAttribute('data-asset-id') || '';
            const boothTypeId = booth.getAttribute('data-booth-type-id') || '';
            
            if (boothId) {
                boothIds.push(boothId);
                
                // Collect booth data for sidebar (will add after removal)
                if (boothNumber) {
                    boothsToAddToSidebar.push({
                        id: boothId,
                        number: boothNumber,
                        status: boothStatus,
                        clientId: clientId,
                        userId: userId,
                        categoryId: categoryId,
                        subCategoryId: subCategoryId,
                        assetId: assetId,
                        boothTypeId: boothTypeId
                    });
                }
            }
        });
        
        // INSTANT: Remove all booths from canvas immediately (visual update)
        allBooths.forEach(function(booth) {
            booth.remove();
        });
        
        // INSTANT: Clear selection and hide properties panel
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
        
        // INSTANT: Add booths back to sidebar (visual update)
        boothsToAddToSidebar.forEach(function(boothData) {
            self.addBoothToSidebar(boothData);
        });
        
        // INSTANT: Update booth count
        if (self.updateBoothCount) {
            self.updateBoothCount();
        }
        
        // INSTANT: Save state for undo/redo
        self.saveState();
        
        console.log('Canvas cleared: ' + boothIds.length + ' booths removed');
        
        // BACKGROUND: Clear positions in database using batch save (non-blocking)
        if (boothIds.length > 0) {
            // Prepare batch data with null positions
            const boothsToClear = boothIds.map(function(boothId) {
                return {
                    id: parseInt(boothId),
                    position_x: null,
                    position_y: null,
                    width: null,
                    height: null,
                    rotation: null,
                    z_index: null,
                    font_size: null,
                    border_width: null,
                    border_radius: null,
                    opacity: null
                };
            });
            
            // Batch save in background (non-blocking)
            self.saveBoothsBatch(boothsToClear).then(function(result) {
                console.log('✅ Cleared positions for ' + result.saved + ' booth(s) in database');
            }).catch(function(error) {
                console.error('⚠️ Error clearing positions:', error);
            });
        }
    },
    
    // Detect existing floorplan image and resize canvas to match its dimensions
    detectAndResizeCanvasToImage: function() {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;

        // Priority 1: Check if we have a floor plan image URL (from floor plan record)
        let imageUrl = null;
        if (self.floorPlanImageUrl) {
            imageUrl = self.floorPlanImageUrl;
        } else {
            // Priority 2: Check if there's a background image on canvas
            const bgImage = canvas.style.backgroundImage;
            if (bgImage && bgImage !== 'none' && bgImage !== '') {
                imageUrl = bgImage.replace(/url\(['"]?([^'"]+)['"]?\)/, '$1');
            } else {
                // Priority 3: Check img element
                const floorplanImg = document.getElementById('floorplanImageElement');
                if (floorplanImg && floorplanImg.src) {
                    imageUrl = floorplanImg.src;
                }
            }
        }
        
        // Also check if img element already has dimensions (quick path - avoids reloading)
        const floorplanImg = document.getElementById('floorplanImageElement');
        if (floorplanImg && floorplanImg.complete && floorplanImg.naturalWidth > 0) {
            const imageWidth = floorplanImg.naturalWidth;
            const imageHeight = floorplanImg.naturalHeight;
            
            if (imageWidth > 0 && imageHeight > 0) {
                // Update canvas size to match image resolution immediately
                self.setCanvasSize(imageWidth, imageHeight);
                
                // Save new canvas size to localStorage
                localStorage.setItem('canvasWidth', imageWidth);
                localStorage.setItem('canvasHeight', imageHeight);
                
                console.log('Canvas resized to image size (from img element):', imageWidth, 'x', imageHeight);
                return; // Already have dimensions, no need to load image again
            }
        }
        
        if (imageUrl) {
            // Create a new image to get dimensions
            const img = new Image();
            img.onload = function() {
                const imageWidth = img.naturalWidth || img.width;
                const imageHeight = img.naturalHeight || img.height;
                
                if (imageWidth > 0 && imageHeight > 0) {
                    // Update canvas size to match image resolution EXACTLY
                    self.setCanvasSize(imageWidth, imageHeight);
                    
                    // Save new canvas size to localStorage
                    localStorage.setItem('canvasWidth', imageWidth);
                    localStorage.setItem('canvasHeight', imageHeight);
                    
                    // Update canvas settings in memory
                    self.canvasWidth = imageWidth;
                    self.canvasHeight = imageHeight;
                    
                    console.log('Canvas resized to image size:', imageWidth, 'x', imageHeight);
                    
                    // Update panzoom after canvas resize
                    if (self.panzoomInstance) {
                        setTimeout(function() {
                            // Refresh panzoom to recognize new canvas size
                            if (self.panzoomInstance.setOptions) {
                                self.panzoomInstance.setOptions({
                                    minScale: 0.1,
                                    maxScale: 5,
                                    contain: 'outside'
                                });
                            }
                        }, 100);
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
            window.boothsData = @json($boothsForJS);
        }
        
        // Use $boothsForJS which has all properties including appearance and positions
        const booths = @json($boothsForJS);
        const self = this;
        
        // Clear any existing booths on canvas first to prevent duplicates
        // This ensures a clean state before loading from database
        const existingBooths = canvas.querySelectorAll('.dropped-booth');
        existingBooths.forEach(function(booth) {
            booth.remove();
        });
        
        booths.forEach(function(booth) {
            if (booth.position_x !== null && booth.position_y !== null) {
                // Check if booth already exists on canvas to prevent duplicates
                const existingBooth = canvas.querySelector('[data-booth-id="' + booth.id + '"]');
                if (existingBooth) {
                    // Booth already exists, just update its position and properties
                    existingBooth.style.left = booth.position_x + 'px';
                    existingBooth.style.top = booth.position_y + 'px';
                    existingBooth.setAttribute('data-x', booth.position_x);
                    existingBooth.setAttribute('data-y', booth.position_y);
                    
                    // Update dimensions if they exist
                    if (booth.width) {
                        existingBooth.style.width = booth.width + 'px';
                        existingBooth.setAttribute('data-width', booth.width);
                    }
                    if (booth.height) {
                        existingBooth.style.height = booth.height + 'px';
                        existingBooth.setAttribute('data-height', booth.height);
                    }
                    // Update rotation if it exists
                    if (booth.rotation !== null && booth.rotation !== undefined) {
                        existingBooth.style.transform = 'rotate(' + booth.rotation + 'deg)';
                        existingBooth.setAttribute('data-rotation', booth.rotation);
                    }
                    // Update style properties if they exist
                    if (booth.z_index) {
                        existingBooth.style.zIndex = booth.z_index;
                        existingBooth.setAttribute('data-z-index', booth.z_index);
                    }
                    if (booth.font_size) {
                        existingBooth.style.fontSize = booth.font_size + 'px';
                        existingBooth.setAttribute('data-font-size', booth.font_size);
                    }
                    if (booth.border_width !== null && booth.border_width !== undefined) {
                        existingBooth.style.borderWidth = booth.border_width + 'px';
                        existingBooth.setAttribute('data-border-width', booth.border_width);
                    }
                    if (booth.border_radius !== null && booth.border_radius !== undefined) {
                        existingBooth.style.borderRadius = booth.border_radius + 'px';
                        existingBooth.setAttribute('data-border-radius', booth.border_radius);
                    }
                    if (booth.opacity !== null && booth.opacity !== undefined) {
                        existingBooth.style.opacity = booth.opacity;
                        existingBooth.setAttribute('data-opacity', booth.opacity);
                    }
                    
                    // Update appearance properties if they exist
                    if (booth.background_color) {
                        existingBooth.style.backgroundColor = booth.background_color;
                        existingBooth.setAttribute('data-background-color', booth.background_color);
                    }
                    if (booth.border_color) {
                        existingBooth.style.borderColor = booth.border_color;
                        existingBooth.setAttribute('data-border-color', booth.border_color);
                    }
                    if (booth.text_color) {
                        existingBooth.style.color = booth.text_color;
                        existingBooth.setAttribute('data-text-color', booth.text_color);
                    }
                    if (booth.font_weight) {
                        existingBooth.style.fontWeight = booth.font_weight;
                        existingBooth.setAttribute('data-font-weight', booth.font_weight);
                    }
                    if (booth.font_family) {
                        existingBooth.style.fontFamily = booth.font_family;
                        existingBooth.setAttribute('data-font-family', booth.font_family);
                    }
                    if (booth.text_align) {
                        existingBooth.style.textAlign = booth.text_align;
                        existingBooth.setAttribute('data-text-align', booth.text_align);
                    }
                    if (booth.box_shadow) {
                        existingBooth.style.boxShadow = booth.box_shadow;
                        existingBooth.setAttribute('data-box-shadow', booth.box_shadow);
                    }
                    
                    // Update resize handles size
                    self.updateResizeHandlesSize(existingBooth);
                    self.updateRotationIndicator(existingBooth);
                    
                    // Restore lock state from localStorage
                    try {
                        const lockStates = JSON.parse(localStorage.getItem('booth_lock_states') || '{}');
                        if (lockStates[booth.id] === true) {
                            existingBooth.classList.add('locked');
                            existingBooth.setAttribute('data-locked', 'true');
                        }
                    } catch (e) {
                        // Ignore localStorage errors
                    }
                    
                    return; // Skip creating a new booth element
                }
                
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
                    boothElement.setAttribute('data-z-index', booth.z_index);
                }
                if (booth.font_size) {
                    boothElement.style.fontSize = booth.font_size + 'px';
                    boothElement.setAttribute('data-font-size', booth.font_size);
                }
                if (booth.border_width !== null && booth.border_width !== undefined) {
                    boothElement.style.borderWidth = booth.border_width + 'px';
                    boothElement.setAttribute('data-border-width', booth.border_width);
                }
                if (booth.border_radius !== null && booth.border_radius !== undefined) {
                    boothElement.style.borderRadius = booth.border_radius + 'px';
                    boothElement.setAttribute('data-border-radius', booth.border_radius);
                }
                if (booth.opacity !== null && booth.opacity !== undefined) {
                    boothElement.style.opacity = booth.opacity;
                    boothElement.setAttribute('data-opacity', booth.opacity);
                }
                
                // Apply saved appearance properties if they exist
                if (booth.background_color) {
                    boothElement.style.backgroundColor = booth.background_color;
                    boothElement.setAttribute('data-background-color', booth.background_color);
                }
                if (booth.border_color) {
                    boothElement.style.borderColor = booth.border_color;
                    boothElement.setAttribute('data-border-color', booth.border_color);
                }
                if (booth.text_color) {
                    boothElement.style.color = booth.text_color;
                    boothElement.setAttribute('data-text-color', booth.text_color);
                }
                if (booth.font_weight) {
                    boothElement.style.fontWeight = booth.font_weight;
                    boothElement.setAttribute('data-font-weight', booth.font_weight);
                }
                if (booth.font_family) {
                    boothElement.style.fontFamily = booth.font_family;
                    boothElement.setAttribute('data-font-family', booth.font_family);
                }
                if (booth.text_align) {
                    boothElement.style.textAlign = booth.text_align;
                    boothElement.setAttribute('data-text-align', booth.text_align);
                }
                if (booth.box_shadow) {
                    boothElement.style.boxShadow = booth.box_shadow;
                    boothElement.setAttribute('data-box-shadow', booth.box_shadow);
                }
                
                canvas.appendChild(boothElement);
                
                // Restore lock state from localStorage for newly created booths
                try {
                    const lockStates = JSON.parse(localStorage.getItem('booth_lock_states') || '{}');
                    if (lockStates[booth.id] === true) {
                        boothElement.classList.add('locked');
                        boothElement.setAttribute('data-locked', 'true');
                    }
                } catch (e) {
                    // Ignore localStorage errors
                }
                
                // Update resize handles size after all styles are applied
                // This ensures handles are properly sized based on the final booth dimensions
                self.updateResizeHandlesSize(boothElement);
                
                self.makeBoothDraggable(boothElement);
            }
            
            // Update booth count after loading all booths
            if (self.updateBoothCount) {
                self.updateBoothCount();
            }
            
            // Sync sidebar after loading positions to remove booths already on canvas
            self.syncSidebarWithCanvas();
        });
    },
    
    // Sync sidebar with canvas - remove booths that are already on canvas
    // This checks both the rendered canvas and database positions to ensure accuracy
    syncSidebarWithCanvas: function() {
        const self = this;
            const canvas = document.getElementById('print');
        if (!canvas) return;
        
        // Get all booth IDs that are currently on the canvas (rendered)
        const canvasBooths = canvas.querySelectorAll('.dropped-booth');
        const boothIdsOnCanvas = new Set();
        
        canvasBooths.forEach(function(boothElement) {
            const boothId = boothElement.getAttribute('data-booth-id');
            if (boothId) {
                boothIdsOnCanvas.add(boothId);
            }
        });
        
        // Also check database positions (from loaded booths data)
        // This ensures booths with saved positions are removed even if not yet rendered
        if (typeof window.boothsData !== 'undefined' && Array.isArray(window.boothsData)) {
            window.boothsData.forEach(function(booth) {
                if (booth.position_x !== null && booth.position_y !== null && booth.id) {
                    boothIdsOnCanvas.add(booth.id.toString());
            }
        });
        }
        
        // Find and remove booths from sidebar that are already on canvas (or have positions in DB)
        const sidebarBooths = document.querySelectorAll('#boothNumbersContainer .booth-number-item');
        let removedCount = 0;
        
        sidebarBooths.forEach(function(boothItem) {
            const boothId = boothItem.getAttribute('data-booth-id');
            if (boothId && boothIdsOnCanvas.has(boothId)) {
                // This booth is on canvas or has a saved position, remove it from sidebar
                self.removeBoothFromSidebar(boothItem);
                removedCount++;
            }
        });
        
        if (removedCount > 0) {
            console.log('✅ Synced sidebar: Removed ' + removedCount + ' booth(s) that are already on canvas or have saved positions');
        }
        
        // Update booth count
        if (self.updateBoothCount) {
            self.updateBoothCount();
        }
    },
    
    // Setup zone booth selection
    setupZoneBoothSelection: function() {
        const self = this;
        
        // Handle booth item click for selection (Ctrl+Click or regular click to toggle)
        $(document).on('click', '.booth-number-item', function(e) {
            // Don't interfere with drag and drop
            if (e.ctrlKey || e.metaKey) {
                // Ctrl+Click: Toggle selection
                e.preventDefault();
                e.stopPropagation();
                $(this).toggleClass('selected');
                
                // Update "Add Selected" button state
                const zoneName = $(this).attr('data-booth-zone');
                if (zoneName) {
                    self.updateZoneAddSelectedButton(zoneName);
                }
            } else if (e.shiftKey) {
                // Shift+Click: Multi-select range (future enhancement)
                e.preventDefault();
                e.stopPropagation();
            } else {
                // Regular click: Toggle selection (but allow drag to work)
                // Only toggle if not dragging
                setTimeout(function() {
                    if (!self.draggedElement || self.draggedElement !== this) {
                        // Check if this is a click (not a drag start)
                        const wasDragging = $(this).hasClass('dragging');
                        if (!wasDragging) {
                            $(this).toggleClass('selected');
                            
                            // Update "Add Selected" button state
                            const zoneName = $(this).attr('data-booth-zone');
                            if (zoneName) {
                                self.updateZoneAddSelectedButton(zoneName);
                            }
                        }
                    }
                }.bind(this), 100);
            }
        });
        
        // Initialize "Add Selected" button states for all zones
        document.querySelectorAll('.zone-section').forEach(function(zoneSection) {
            const zoneName = zoneSection.getAttribute('data-zone');
            if (zoneName) {
                self.updateZoneAddSelectedButton(zoneName);
            }
        });
    },
    
    // Setup toolbar
    setupToolbar: function() {
        const self = this;
        
        // Tool selection (Photoshop-like)
        // Tools removed - clean slate for new development
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
        
        // Center marker toggle
        $('#btnCenter').on('click', function() {
            self.centerMarkerEnabled = !self.centerMarkerEnabled;
            const centerMarker = $('#canvasCenterMarker');
            if (self.centerMarkerEnabled) {
                centerMarker.addClass('visible');
                $(this).addClass('active');
                // Also ensure grid is visible when center marker is on (upgrade as requested)
                if (!self.gridEnabled) {
                    self.gridEnabled = true;
                    $('#gridOverlay').addClass('visible');
                    $('#btnGrid').addClass('active');
                }
            } else {
                centerMarker.removeClass('visible');
                $(this).removeClass('active');
            }
        });
        
        // Initialize grid state on page load
        if (self.gridEnabled) {
            $('#gridOverlay').addClass('visible');
            $('#btnGrid').addClass('active');
        }
        // Initialize center marker state on page load
        if (self.centerMarkerEnabled) {
            $('#canvasCenterMarker').addClass('visible');
            $('#btnCenter').addClass('active');
        }
        
        // Snap to Grid toggle button - Enhanced functionality
        $('#btnSnap').on('click', function() {
            self.snapEnabled = !self.snapEnabled;
            const btn = $(this);
            btn.toggleClass('active', self.snapEnabled);
            
            // Update button title and visual feedback
            if (self.snapEnabled) {
                btn.attr('title', 'Snap to Grid: ON (Click to disable)');
                btn.css('background', 'rgba(40, 167, 69, 0.3)');
                showNotification('Snap to Grid enabled - Booths will align to grid', 'success');
            } else {
                btn.attr('title', 'Snap to Grid: OFF (Click to enable)');
                btn.css('background', 'rgba(108, 117, 125, 0.3)');
                showNotification('Snap to Grid disabled - Free positioning', 'info');
            }
            
            // Save snap preference to localStorage
            localStorage.setItem('snapEnabled', self.snapEnabled);
        });
        
        // Load snap preference from localStorage on page load
        const savedSnapEnabled = localStorage.getItem('snapEnabled');
        if (savedSnapEnabled !== null) {
            self.snapEnabled = savedSnapEnabled === 'true';
            const btnSnap = $('#btnSnap');
            btnSnap.toggleClass('active', self.snapEnabled);
            if (self.snapEnabled) {
                btnSnap.attr('title', 'Snap to Grid: ON (Click to disable)').css('background', 'rgba(40, 167, 69, 0.3)');
            } else {
                btnSnap.attr('title', 'Snap to Grid: OFF (Click to enable)').css('background', 'rgba(108, 117, 125, 0.3)');
            }
        }
        
        // Rotation Controls - Rotate Left (-90 degrees)
        $('#btnRotateLeft').on('click', function() {
            self.rotateSelectedBooths(-90);
        });
        
        // Rotation Controls - Rotate Right (+90 degrees)
        $('#btnRotateRight').on('click', function() {
            self.rotateSelectedBooths(90);
        });
        
        // Function to update booth count badge
        self.updateBoothCount = function() {
            const booths = document.querySelectorAll('.dropped-booth');
            const count = booths.length;
            const badge = document.getElementById('boothCountBadge');
            if (badge) {
                badge.textContent = count;
            }
        };
        
        // Function to flash all booths
        self.flashAllBooths = function() {
            const booths = document.querySelectorAll('.dropped-booth');
            const count = booths.length;
            
            if (count === 0) {
                showNotification('No booths found on canvas', 'info');
                return;
            }
            
            // Add flashing class to all booths
            booths.forEach(function(booth) {
                booth.classList.add('flashing');
            });
            
            // Remove flashing class after animation completes (3 cycles * 0.6s = 1.8s)
            setTimeout(function() {
                booths.forEach(function(booth) {
                    booth.classList.remove('flashing');
                });
            }, 1800);
            
            // Show notification with count
            showNotification(count + ' booth' + (count !== 1 ? 's' : '') + ' found on canvas', 'success');
        };
        
        // Show all booths button - flash effect
        
        // Lock/Unlock dropdown button - only toggle dropdown, don't lock anything
        $('#btnLockBoothsDropdown').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const dropdown = $(this).closest('.dropdown');
            dropdown.toggleClass('show');
        });
        
        // Lock/Unlock dropdown actions
        $('#btnLockSelected').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.dropdown').removeClass('show');
            if (self.selectedBooths.length === 0) {
                customAlert('Please select at least one booth to lock', 'warning');
                return;
            }
            self.selectedBooths.forEach(function(booth) {
                self.lockBoothElement(booth);
            });
            customAlert('Selected booth(s) locked', 'success');
        });
        
        $('#btnUnlockSelected').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.dropdown').removeClass('show');
            if (self.selectedBooths.length === 0) {
                customAlert('Please select at least one booth to unlock', 'warning');
                return;
            }
            self.selectedBooths.forEach(function(booth) {
                self.unlockBoothElement(booth);
            });
            customAlert('Selected booth(s) unlocked', 'success');
        });
        
        $('#btnLockAll').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.dropdown').removeClass('show');
            const canvas = document.getElementById('print');
            if (!canvas) return;
            const booths = canvas.querySelectorAll('.dropped-booth');
            booths.forEach(function(booth) {
                self.lockBoothElement(booth);
            });
            customAlert('All booths locked', 'success');
        });
        
        $('#btnUnlockAll').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('.dropdown').removeClass('show');
            const canvas = document.getElementById('print');
            if (!canvas) return;
            const booths = canvas.querySelectorAll('.dropped-booth');
            booths.forEach(function(booth) {
                self.unlockBoothElement(booth);
            });
            customAlert('All booths unlocked', 'success');
        });
        
        $('#btnShowBooths').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.flashAllBooths();
        });
        
        // Update booth count on page load
        self.updateBoothCount();
        
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
            
            const self = this;
            customConfirm('Are you sure you want to remove the floorplan image? This action cannot be undone.', 'Remove Floorplan', 'Yes, Remove', 'Cancel').then(function(confirmed) {
                if (!confirmed) {
                return;
            }
            
            // Show loading state
                const removeBtn = $('#btnRemoveFloorplan');
            const originalHtml = removeBtn.html();
            removeBtn.html('<i class="fas fa-spinner fa-spin"></i> Removing...');
            removeBtn.css('pointer-events', 'none');
            
            // Get current floor plan ID
            const floorPlanId = @php echo isset($floorPlanId) && $floorPlanId ? (int)$floorPlanId : 'null'; @endphp;
            if (!floorPlanId) {
                customAlert('Please select a floor plan first.', 'warning');
                removeBtn.html(originalHtml);
                removeBtn.css('pointer-events', 'auto');
                return;
            }
            
            // Send AJAX request to remove floorplan
            $.ajax({
                url: '{{ route("booths.remove-floorplan") }}',
                method: 'POST',
                data: {
                    floor_plan_id: floorPlanId
                },
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
                        
                            customAlert('Floorplan removed successfully!', 'success');
                    } else {
                            customAlert('Error: ' + (response.message || 'Failed to remove floorplan'), 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to remove floorplan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                        customAlert(errorMsg, 'error');
                },
                complete: function() {
                    removeBtn.html(originalHtml);
                    removeBtn.css('pointer-events', 'auto');
                }
                });
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
                customAlert('Please select an image file to upload.', 'warning');
                return;
            }
            
            // Create FormData
            const formData = new FormData(form);
            
            // Add floor_plan_id to form data
            const floorPlanId = @if(isset($floorPlanId) && $floorPlanId){{ $floorPlanId }}@else null @endif;
            if (floorPlanId) {
                formData.append('floor_plan_id', floorPlanId);
            } else {
                customAlert('Please select a floor plan first.', 'warning');
                return;
            }
            
            const file = fileInput.files[0];
            
            // Check file size (configurable limit)
            const fileSize = file.size / 1024 / 1024; // Size in MB
            const uploadLimit = self.uploadSizeLimit || 10; // Default to 10MB if not set
            if (uploadLimit > 0 && fileSize > uploadLimit) {
                customAlert('File size exceeds ' + uploadLimit + 'MB limit. Please choose a smaller image.', 'warning');
                return;
            }
            
            // Check file type
            if (!file.type.match('image.*')) {
                customAlert('Please select a valid image file (JPG, PNG, GIF).', 'warning');
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
                        
                        // Store floorplan image path (use relative path from response.image_path)
                        // response.image_path is the relative path: 'images/floor-plans/...'
                        // response.image_url is the full URL: 'http://localhost:8000/images/floor-plans/...'
                        // We use relative path for consistency and to prevent conflicts
                        if (response.image_path) {
                            self.floorplanImage = response.image_path; // Relative path
                        } else if (response.image_url) {
                            // Extract relative path from full URL if image_path not provided
                            const url = new URL(response.image_url);
                            self.floorplanImage = url.pathname.startsWith('/') ? url.pathname.substring(1) : url.pathname;
                        }
                        self.floorPlanImageUrl = response.image_url; // Full URL for display
                        
                        // Save floorplan image path to database (with correct floor_plan_id)
                        self.saveCanvasSettingsToDatabase().then(function() {
                            // Close modal
                            $('#uploadFloorplanModal').modal('hide');
                            customAlert('Floorplan uploaded and saved successfully! Canvas size adjusted to match image dimensions.', 'success');
                        }).catch(function(error) {
                            // Still close modal even if save fails
                            $('#uploadFloorplanModal').modal('hide');
                            customAlert('Floorplan uploaded successfully! (Settings save failed)', 'warning');
                        });
                    } else {
                        customAlert('Error: ' + (response.message || 'Failed to upload floorplan'), 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to upload floorplan.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    customAlert(errorMsg, 'error');
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
            
            // Load new appearance settings
            $('#defaultBackgroundColor').val(self.defaultBackgroundColor);
            $('#defaultBackgroundColorText').val(self.defaultBackgroundColor);
            $('#defaultBorderColor').val(self.defaultBorderColor);
            $('#defaultBorderColorText').val(self.defaultBorderColor);
            $('#defaultTextColor').val(self.defaultTextColor);
            $('#defaultTextColorText').val(self.defaultTextColor);
            $('#defaultFontWeight').val(self.defaultFontWeight);
            $('#defaultFontFamily').val(self.defaultFontFamily);
            $('#defaultTextAlign').val(self.defaultTextAlign);
            $('#defaultBoxShadow').val(self.defaultBoxShadow);
        });
        
        // Sync color picker with text input
        $('#defaultBackgroundColor').on('input', function() {
            $('#defaultBackgroundColorText').val($(this).val());
        });
        $('#defaultBackgroundColorText').on('input', function() {
            const val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $('#defaultBackgroundColor').val(val);
            }
        });
        
        $('#defaultBorderColor').on('input', function() {
            $('#defaultBorderColorText').val($(this).val());
        });
        $('#defaultBorderColorText').on('input', function() {
            const val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $('#defaultBorderColor').val(val);
            }
        });
        
        $('#defaultTextColor').on('input', function() {
            $('#defaultTextColorText').val($(this).val());
        });
        $('#defaultTextColorText').on('input', function() {
            const val = $(this).val();
            if (/^#[0-9A-F]{6}$/i.test(val)) {
                $('#defaultTextColor').val(val);
            }
        });
        
        // Apply Booth Settings
        $('#applyBoothSettings').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            
            // Disable button and show loading
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            self.defaultBoothWidth = parseInt($('#defaultWidth').val()) || 80;
            self.defaultBoothHeight = parseInt($('#defaultHeight').val()) || 50;
            self.defaultBoothRotation = parseInt($('#defaultRotation').val()) || 0;
            self.defaultBoothZIndex = parseInt($('#defaultZIndex').val()) || 10;
            self.defaultBoothFontSize = parseInt($('#defaultFontSize').val()) || 14;
            self.defaultBoothBorderWidth = parseInt($('#defaultBorderWidth').val()) || 2;
            self.defaultBoothBorderRadius = parseInt($('#defaultBorderRadius').val()) || 6;
            self.defaultBoothOpacity = parseFloat($('#defaultOpacity').val()) || 1.00;
            
            // New appearance settings
            self.defaultBackgroundColor = $('#defaultBackgroundColor').val() || '#ffffff';
            self.defaultBorderColor = $('#defaultBorderColor').val() || '#007bff';
            self.defaultTextColor = $('#defaultTextColor').val() || '#000000';
            self.defaultFontWeight = $('#defaultFontWeight').val() || '700';
            self.defaultFontFamily = $('#defaultFontFamily').val() || 'Arial, sans-serif';
            self.defaultTextAlign = $('#defaultTextAlign').val() || 'center';
            self.defaultBoxShadow = $('#defaultBoxShadow').val() || '0 2px 8px rgba(0,0,0,0.2)';
            
            // Save to database
            self.saveBoothSettingsToDatabase()
                .then(function() {
            $('#boothSettingsModal').modal('hide');
                    customAlert('Booth default settings saved to database! New booths will use these settings.', 'success');
                })
                .catch(function(error) {
                    customAlert('Error saving settings: ' + error.message + '\nSettings saved to local storage as fallback.', 'warning');
                })
                .finally(function() {
                    // Re-enable button
                    btn.prop('disabled', false);
                    btn.html(originalText);
                });
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
            const btn = $(this);
            const originalText = btn.html();
            
            // Disable button and show loading
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
            
            const width = parseInt($('#canvasWidth').val()) || 1200;
            const height = parseInt($('#canvasHeight').val()) || 800;
            const resolution = parseInt($('#canvasResolution').val()) || 300;
            const gridSize = parseInt($('#gridSize').val()) || 10;
            const uploadSizeLimit = parseInt($('#uploadSizeLimit').val()) || 10;
            
            self.setCanvasSize(width, height);
            self.canvasResolution = resolution;
            self.setGridSize(gridSize);
            self.uploadSizeLimit = uploadSizeLimit;
            
            // Save to database (setCanvasSize and setGridSize will also trigger saves)
            self.saveCanvasSettingsToDatabase()
                .then(function() {
                    // Also save upload size limit to localStorage (not critical for persistence)
                    localStorage.setItem('uploadSizeLimit', uploadSizeLimit);
                    
                    // Re-enable button
                    btn.prop('disabled', false);
                    btn.html(originalText);
                    
                    $('#canvasSettingsModal').modal('hide');
                    customAlert('Canvas settings saved to database successfully!', 'success');
                })
                .catch(function(error) {
                    // Re-enable button even on error
                    btn.prop('disabled', false);
                    btn.html(originalText);
                    
                    // Still save to localStorage as fallback
                    localStorage.setItem('canvasWidth', width);
                    localStorage.setItem('canvasHeight', height);
                    localStorage.setItem('canvasResolution', resolution);
                    localStorage.setItem('gridSize', gridSize);
                    localStorage.setItem('uploadSizeLimit', uploadSizeLimit);
                    
                    $('#canvasSettingsModal').modal('hide');
                    customAlert('Canvas settings applied (saved locally). Database save failed.', 'warning');
                });
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
        
        // Toggle Booth Numbers Sidebar button
        // Toggle Booth Numbers Sidebar button - use event delegation to ensure it works
        $(document).off('click', '#btnToggleBoothNumbers').on('click', '#btnToggleBoothNumbers', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const sidebar = $('#designerSidebar');
            const btn = $('#btnToggleBoothNumbers');
            
            if (!sidebar.length) {
                console.error('Sidebar element not found!');
                return false;
            }
            
            const isHidden = sidebar.hasClass('hidden');
            
            console.log('Toggle sidebar clicked. Currently hidden:', isHidden);
            console.log('Sidebar element exists:', sidebar.length > 0);
            console.log('Current classes:', sidebar.attr('class'));
            
            if (isHidden) {
                // Show sidebar - just remove the hidden class, CSS will handle the rest
                sidebar.removeClass('hidden');
                
                // Update button state
                btn.css('background', 'rgba(102, 126, 234, 0.5)');
                btn.attr('title', 'Hide Booth Numbers Sidebar');
                btn.find('i').removeClass('fa-th').addClass('fa-th-large');
                
                // Save state
                localStorage.setItem('boothNumbersSidebarVisible', 'true');
                
                console.log('Sidebar shown. New classes:', sidebar.attr('class'));
                console.log('Computed display:', computed.display);
                console.log('Bounding rect:', rect);
            } else {
                // Hide sidebar - just add the hidden class
                sidebar.addClass('hidden');
                
                // Update button state
                btn.css('background', 'rgba(102, 126, 234, 0.3)');
                btn.attr('title', 'Show Booth Numbers Sidebar');
                btn.find('i').removeClass('fa-th-large').addClass('fa-th');
                
                // Save state
                localStorage.setItem('boothNumbersSidebarVisible', 'false');
                
                console.log('Sidebar hidden. New classes:', sidebar.attr('class'));
            }
            
            return false;
        });
        
        // Load sidebar visibility state from localStorage on page load
        // Use setTimeout to ensure DOM is ready
        setTimeout(function() {
            const sidebarVisible = localStorage.getItem('boothNumbersSidebarVisible');
            const sidebar = $('#designerSidebar');
            const toggleBtn = $('#btnToggleBoothNumbers');
            
            if (!sidebar.length) {
                console.error('Sidebar element not found during initialization!');
                return;
            }
            
            if (sidebarVisible === 'false') {
                // Hide sidebar - just add the hidden class
                sidebar.addClass('hidden');
                toggleBtn.css('background', 'rgba(102, 126, 234, 0.3)');
                toggleBtn.attr('title', 'Show Booth Numbers Sidebar');
                toggleBtn.find('i').removeClass('fa-th-large').addClass('fa-th');
                console.log('Sidebar initialized as hidden');
            } else {
                // Default: show sidebar - just remove the hidden class
                sidebar.removeClass('hidden');
                toggleBtn.css('background', 'rgba(102, 126, 234, 0.5)');
                toggleBtn.attr('title', 'Hide Booth Numbers Sidebar');
                toggleBtn.find('i').removeClass('fa-th').addClass('fa-th-large');
                localStorage.setItem('boothNumbersSidebarVisible', 'true');
                console.log('Sidebar initialized as visible');
            }
        }, 100);
        
        // Clear Canvas button
        $('#btnClearCanvas').on('click', function() {
            customConfirm('Are you sure you want to clear all booths from the canvas? This action cannot be undone.', 'Clear Canvas', 'Yes, Clear', 'Cancel').then(function(confirmed) {
                if (confirmed) {
                self.clearCanvas();
            }
            });
        });
        
        // Helper function to get the center point of all booths (or selected booths)
        function getBoothsCenter() {
            const canvas = document.getElementById('print');
            if (!canvas) return null;
            
            // Get selected booths first (priority), or all booths if none selected
            let booths = Array.from(document.querySelectorAll('.dropped-booth.selected'));
            if (booths.length === 0) {
                booths = Array.from(document.querySelectorAll('.dropped-booth'));
            }
            
            if (booths.length === 0) {
                // No booths found, return canvas center
                const canvasWidth = canvas.offsetWidth || self.canvasWidth || 1200;
                const canvasHeight = canvas.offsetHeight || self.canvasHeight || 800;
                return {
                    x: canvasWidth / 2,
                    y: canvasHeight / 2
                };
            }
            
            // Calculate bounding box of all booths (including their full dimensions)
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            let hasValidBooths = false;
            
            booths.forEach(function(booth) {
                const x = parseFloat(booth.style.left) || parseFloat(booth.getAttribute('data-x')) || 0;
                const y = parseFloat(booth.style.top) || parseFloat(booth.getAttribute('data-y')) || 0;
                const width = parseFloat(booth.style.width) || parseFloat(booth.getAttribute('data-width')) || 80;
                const height = parseFloat(booth.style.height) || parseFloat(booth.getAttribute('data-height')) || 50;
                
                // Only include booths with valid positions
                if (!isNaN(x) && !isNaN(y) && x >= 0 && y >= 0) {
                minX = Math.min(minX, x);
                minY = Math.min(minY, y);
                maxX = Math.max(maxX, x + width);
                maxY = Math.max(maxY, y + height);
                    hasValidBooths = true;
                }
            });
            
            // If no valid booths found, return canvas center
            if (!hasValidBooths || minX === Infinity) {
                const canvasWidth = canvas.offsetWidth || self.canvasWidth || 1200;
                const canvasHeight = canvas.offsetHeight || self.canvasHeight || 800;
                return {
                    x: canvasWidth / 2,
                    y: canvasHeight / 2
                };
            }
            
            // Return center of bounding box (where all booths are located)
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
                    
                    // Always use canvas center (crosshairs) as the focal point
                    const canvasCenterX = (self.canvasWidth || 1200) / 2;
                    const canvasCenterY = (self.canvasHeight || 800) / 2;
                    const focalPoint = { x: canvasCenterX, y: canvasCenterY };
                    
                    // Apply zoom with canvas center as focal point
                    if (self.panzoomInstance.zoom && focalPoint) {
                        self.panzoomInstance.zoom(newScale, { 
                            animate: true, 
                            focal: { x: focalPoint.x, y: focalPoint.y }
                        });
                    } else if (self.panzoomInstance.zoom) {
                        // Fallback if no focal point
                        self.panzoomInstance.zoom(newScale, { animate: true });
                    }
                    
                    // Update zoom level display immediately with the new scale
                    self.zoomLevel = newScale;
                    $('#zoomLevel').text(Math.round(newScale * 100) + '%');
                    
                    // Also update after a delay to sync with actual panzoom state
                    setTimeout(function() {
                        if (self.panzoomInstance && self.panzoomInstance.getScale) {
                            const actualScale = self.panzoomInstance.getScale();
                            if (!isNaN(actualScale) && actualScale > 0) {
                                self.zoomLevel = actualScale;
                                $('#zoomLevel').text(Math.round(actualScale * 100) + '%');
                            }
                        }
                    }, 200);
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
                    
                    // Always use canvas center (crosshairs) as the focal point
                    const canvasCenterX = (self.canvasWidth || 1200) / 2;
                    const canvasCenterY = (self.canvasHeight || 800) / 2;
                    const focalPoint = { x: canvasCenterX, y: canvasCenterY };
                    
                    // Apply zoom with canvas center as focal point
                    if (self.panzoomInstance.zoom && focalPoint) {
                        self.panzoomInstance.zoom(newScale, { 
                            animate: true, 
                            focal: { x: focalPoint.x, y: focalPoint.y }
                        });
                    } else if (self.panzoomInstance.zoom) {
                        // Fallback if no focal point
                        self.panzoomInstance.zoom(newScale, { animate: true });
                    }
                    
                    // Update zoom level display immediately with the new scale
                    self.zoomLevel = newScale;
                    $('#zoomLevel').text(Math.round(newScale * 100) + '%');
                    
                    // Also update after a delay to sync with actual panzoom state
                    setTimeout(function() {
                        if (self.panzoomInstance && self.panzoomInstance.getScale) {
                            const actualScale = self.panzoomInstance.getScale();
                            if (!isNaN(actualScale) && actualScale > 0) {
                                self.zoomLevel = actualScale;
                                $('#zoomLevel').text(Math.round(actualScale * 100) + '%');
                            }
                        }
                    }, 200);
                }
            }
        });
        
        // Zoom at canvas center (crosshairs) - Always uses canvas center as focal point
        this.zoomAtCursor = function(scaleMultiplier) {
            const self = this;
            if (!self.panzoomInstance) return;
            
            const canvas = self.getElement('print');
            const container = self.getElement('printContainer');
            if (!canvas || !container) return;
            
            // Get current scale
            const currentScale = self.panzoomInstance.getScale ? self.panzoomInstance.getScale() : 1;
            const newScale = currentScale * scaleMultiplier;
            
            // Get min/max scale limits
            let minScale = 0.1;
            let maxScale = 5;
            if (self.panzoomInstance.getOptions) {
                const options = self.panzoomInstance.getOptions();
                minScale = options.minScale || 0.1;
                maxScale = options.maxScale || 5;
            }
            
            // Clamp to limits
            const clampedScale = Math.max(minScale, Math.min(maxScale, newScale));
            
            // Always use canvas center (crosshairs) as the focal point
            // Get canvas dimensions from stored values or element
            const canvasCenterX = (self.canvasWidth || 1200) / 2;
            const canvasCenterY = (self.canvasHeight || 800) / 2;
            const focalPoint = { x: canvasCenterX, y: canvasCenterY };
            
            // Apply zoom with canvas center as focal point
            if (self.panzoomInstance.zoom && focalPoint) {
                self.panzoomInstance.zoom(clampedScale, { 
                    animate: true, 
                    focal: { x: focalPoint.x, y: focalPoint.y }
                });
            } else if (self.panzoomInstance.zoom) {
                // Fallback if no focal point
                self.panzoomInstance.zoom(clampedScale, { animate: true });
            }
            
            // Update zoom level display
            self.zoomLevel = clampedScale;
            const zoomLevelEl = document.getElementById('zoomLevel');
            if (zoomLevelEl) {
                zoomLevelEl.textContent = Math.round(clampedScale * 100) + '%';
            }
            
            // Sync with actual panzoom state after animation
            setTimeout(function() {
                if (self.panzoomInstance && self.panzoomInstance.getScale) {
                    const actualScale = self.panzoomInstance.getScale();
                    if (!isNaN(actualScale) && actualScale > 0) {
                        self.zoomLevel = actualScale;
                        const zoomLevelEl = document.getElementById('zoomLevel');
                        if (zoomLevelEl) {
                            zoomLevelEl.textContent = Math.round(actualScale * 100) + '%';
                        }
                    }
                }
            }, 200);
        };
        
        $('#zoomReset').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (self.panzoomInstance) {
                self.panzoomInstance.reset();
                self.zoomLevel = 1;
                $('#zoomLevel').text('100%');
                
                // Ensure zoom level is updated
                setTimeout(function() {
                    if (self.panzoomInstance && self.panzoomInstance.getScale) {
                        const scale = self.panzoomInstance.getScale();
                        if (!isNaN(scale) && scale > 0) {
                    self.zoomLevel = scale;
                    $('#zoomLevel').text(Math.round(scale * 100) + '%');
                        } else {
                            // Fallback if getScale returns invalid value
                            self.zoomLevel = 1;
                            $('#zoomLevel').text('100%');
                        }
                    }
                }, 100);
            }
        });
        
        // Fit to Canvas - Center and fit the entire image to show it completely
        $('#zoomFit').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.fitCanvasToView(true); // true = animate
        });
        
        // Sidebar toggle with expand/collapse
        $('#toggleSidebar').on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            const sidebar = $('#designerSidebar');
            const isCollapsed = sidebar.hasClass('collapsed');
            
            if (isCollapsed) {
                // Expand
                sidebar.removeClass('collapsed');
                $(this).find('i').removeClass('fa-chevron-right').addClass('fa-chevron-left');
                $(this).attr('title', 'Collapse Booth Number Area');
                localStorage.setItem('boothNumberAreaCollapsed', 'false');
            } else {
                // Collapse
                sidebar.addClass('collapsed');
                $(this).find('i').removeClass('fa-chevron-left').addClass('fa-chevron-right');
                $(this).attr('title', 'Expand Booth Number Area');
                localStorage.setItem('boothNumberAreaCollapsed', 'true');
            }
        });
        
        // Click on collapsed sidebar header to expand
        $(document).on('click', '.designer-sidebar.collapsed .sidebar-header', function(e) {
            e.stopPropagation();
            e.preventDefault();
            const sidebar = $('#designerSidebar');
            sidebar.removeClass('collapsed');
            $('#toggleSidebar').find('i').removeClass('fa-chevron-right').addClass('fa-chevron-left');
            $('#toggleSidebar').attr('title', 'Collapse Booth Number Area');
            localStorage.setItem('boothNumberAreaCollapsed', 'false');
        });
        
        // Load collapsed state from localStorage
        const boothAreaCollapsed = localStorage.getItem('boothNumberAreaCollapsed');
        if (boothAreaCollapsed === 'true') {
            $('#designerSidebar').addClass('collapsed');
            $('#toggleSidebar').find('i').removeClass('fa-chevron-left').addClass('fa-chevron-right');
            $('#toggleSidebar').attr('title', 'Expand Booth Number Area');
        }
        
        // Setup zone toggles (expand/collapse zones)
        $(document).on('click', '.zone-header', function() {
            const zoneSection = $(this).closest('.zone-section');
            zoneSection.toggleClass('collapsed');
        });
        
        // Toggle "In Stock" filter - Show only booths with status 1 (Available)
        let showInStockOnly = false;
        $('#toggleInStock').on('click', function(e) {
            e.stopPropagation();
            showInStockOnly = !showInStockOnly;
            const btn = $(this);
            
            if (showInStockOnly) {
                btn.addClass('active');
                btn.attr('title', 'Show All Booths');
                btn.find('span').text('In Stock');
                btn.find('i').removeClass('fa-check-circle').addClass('fa-filter');
            } else {
                btn.removeClass('active');
                btn.attr('title', 'Show Only Booths In Stock');
                btn.find('span').text('In Stock');
                btn.find('i').removeClass('fa-filter').addClass('fa-check-circle');
            }
            
            // Filter booth items in sidebar
            filterBoothsInSidebar();
        });
        
        // Function to filter booths in sidebar based on in-stock toggle
        function filterBoothsInSidebar() {
            const boothItems = document.querySelectorAll('#boothNumbersContainer .booth-number-item');
            boothItems.forEach(function(item) {
                const status = parseInt(item.getAttribute('data-booth-status')) || 0;
                // Status 1 = STATUS_AVAILABLE (in stock)
                if (showInStockOnly) {
                    if (status === 1) {
                        item.style.display = '';
                        item.classList.remove('hide-not-in-stock');
                    } else {
                        item.style.display = 'none';
                        item.classList.add('hide-not-in-stock');
                    }
                } else {
                    item.style.display = '';
                    item.classList.remove('hide-not-in-stock');
                }
            });
            
            // Update zone counts after filtering
            const zones = document.querySelectorAll('.zone-section');
            zones.forEach(function(zoneSection) {
                const zoneName = zoneSection.getAttribute('data-zone');
                const zoneContent = zoneSection.querySelector('.zone-content');
                if (zoneContent) {
                    const visibleBooths = Array.from(zoneContent.querySelectorAll('.booth-number-item')).filter(function(item) {
                        return item.style.display !== 'none';
                    }).length;
                    const countSpan = zoneSection.querySelector('.zone-count');
                    if (countSpan) {
                        countSpan.textContent = '(' + visibleBooths + ')';
                    }
                }
            });
        }
        
        // Also filter when search is performed
        $('#boothSearchSidebar').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            const boothItems = document.querySelectorAll('#boothNumbersContainer .booth-number-item');
            
            boothItems.forEach(function(item) {
                const boothNumber = item.getAttribute('data-booth-number') || '';
                const status = parseInt(item.getAttribute('data-booth-status')) || 0;
                
                // Check if matches search term
                const matchesSearch = boothNumber.toLowerCase().includes(searchTerm);
                
                // Check if matches in-stock filter
                const matchesStockFilter = !showInStockOnly || status === 1;
                
                if (matchesSearch && matchesStockFilter) {
                    item.style.display = '';
                    item.classList.remove('hide-not-in-stock');
                } else {
                    item.style.display = 'none';
                    item.classList.add('hide-not-in-stock');
                }
            });
            
            // Update zone counts and show/hide empty zones
            const zones = document.querySelectorAll('.zone-section');
            zones.forEach(function(zoneSection) {
                const zoneContent = zoneSection.querySelector('.zone-content');
                if (zoneContent) {
                    const visibleBooths = Array.from(zoneContent.querySelectorAll('.booth-number-item')).filter(function(item) {
                        return item.style.display !== 'none';
                    });
                    const countSpan = zoneSection.querySelector('.zone-count');
                    if (countSpan) {
                        countSpan.textContent = '(' + visibleBooths.length + ')';
                    }
                    
                    // Show/hide zone based on whether it has visible booths
                    if (visibleBooths.length === 0 && searchTerm.length > 0) {
                        zoneSection.style.display = 'none';
                    } else {
                        zoneSection.style.display = '';
                    }
                }
            });
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
        });
    },
    
    // Rotate selected booths by specified angle (supports single or multiple selection)
    rotateSelectedBooths: function(angle) {
        const self = this;
        
        if (self.selectedBooths.length === 0) {
            showNotification('Please select at least one booth to rotate', 'warning');
            return;
        }
        
        // Rotate all selected booths
        self.selectedBooths.forEach(function(boothElement) {
            // Get current rotation
            const currentRotation = parseFloat(boothElement.getAttribute('data-rotation')) || 
                                   parseFloat(boothElement.style.transform.match(/rotate\(([^)]+)\)/)?.[1]) || 0;
            
            // Calculate new rotation (normalize to 0-360 range)
            let newRotation = (currentRotation + angle) % 360;
            if (newRotation < 0) {
                newRotation += 360;
            }
            
            // Apply rotation
            boothElement.style.transform = 'rotate(' + newRotation + 'deg)';
            boothElement.setAttribute('data-rotation', newRotation);
            
            // Update rotation indicator
            self.updateRotationIndicator(boothElement);
            
            // Save to database
            const boothId = boothElement.getAttribute('data-booth-id');
            const x = parseFloat(boothElement.style.left) || 0;
            const y = parseFloat(boothElement.style.top) || 0;
            const width = parseFloat(boothElement.style.width) || 80;
            const height = parseFloat(boothElement.style.height) || 50;
            const zIndex = parseFloat(boothElement.style.zIndex) || parseFloat(boothElement.getAttribute('data-z-index')) || self.defaultBoothZIndex;
            const fontSize = parseFloat(boothElement.style.fontSize) || parseFloat(boothElement.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const borderWidth = parseFloat(boothElement.style.borderWidth) || parseFloat(boothElement.getAttribute('data-border-width')) || self.defaultBoothBorderWidth;
            const borderRadius = parseFloat(boothElement.style.borderRadius) || parseFloat(boothElement.getAttribute('data-border-radius')) || self.defaultBoothBorderRadius;
            const opacity = parseFloat(boothElement.style.opacity) || parseFloat(boothElement.getAttribute('data-opacity')) || self.defaultBoothOpacity;
            const backgroundColor = boothElement.style.backgroundColor || boothElement.getAttribute('data-background-color') || self.defaultBackgroundColor;
            const borderColor = boothElement.style.borderColor || boothElement.getAttribute('data-border-color') || self.defaultBorderColor;
            const textColor = boothElement.style.color || boothElement.getAttribute('data-text-color') || self.defaultTextColor;
            const fontWeight = boothElement.style.fontWeight || boothElement.getAttribute('data-font-weight') || self.defaultFontWeight;
            const fontFamily = boothElement.style.fontFamily || boothElement.getAttribute('data-font-family') || self.defaultFontFamily;
            const textAlign = boothElement.style.textAlign || boothElement.getAttribute('data-text-align') || self.defaultTextAlign;
            const boxShadow = boothElement.style.boxShadow || boothElement.getAttribute('data-box-shadow') || self.defaultBoxShadow;
            
            self.saveBoothPosition(boothId, x, y, width, height, newRotation, zIndex, fontSize, borderWidth, borderRadius, opacity, backgroundColor, borderColor, textColor, fontWeight, fontFamily, textAlign, boxShadow);
        });
        
        // Update info toolbar
        if (self.selectedBooths.length === 1) {
            self.updateInfoToolbar(self.selectedBooths[0]);
        } else {
            self.updateInfoToolbar(null); // Show multi-select info
        }
        
        // Update bounding box
        self.updateSelectionBoundingBox();
        
        // Show notification
        const rotationText = angle > 0 ? '+' + angle + '°' : angle + '°';
        showNotification(self.selectedBooths.length + ' booth' + (self.selectedBooths.length !== 1 ? 's' : '') + ' rotated ' + rotationText, 'success');
        
        // Save state for undo/redo
        self.saveState();
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
    },
    
    // Zoom to selected area (Photoshop-like zoom selection)
    zoomToSelection: function(startX, startY, endX, endY) {
        const self = this;
        const canvas = document.getElementById('print');
        const container = document.getElementById('printContainer');
        if (!canvas || !container || !self.panzoomInstance) return;
        
        // Get container dimensions
        const containerWidth = container.clientWidth;
        const containerHeight = container.clientHeight;
        
        // Calculate selection rectangle (already in canvas coordinates)
        const selectionLeft = Math.min(startX, endX);
        const selectionTop = Math.min(startY, endY);
        const selectionWidth = Math.abs(endX - startX);
        const selectionHeight = Math.abs(endY - startY);
        
        // Get current transform
        const transform = self.panzoomInstance.getTransform ? self.panzoomInstance.getTransform() : { x: 0, y: 0, scale: 1 };
        const currentScale = transform.scale || 1;
        
        // Calculate the scale needed to fit the selection in the viewport
        // The selection is in canvas coordinates, so we need to account for current scale
        const scaleX = (containerWidth / selectionWidth);
        const scaleY = (containerHeight / selectionHeight);
        const newScale = Math.min(scaleX, scaleY) * 0.95; // 95% to add some padding
        
        // Clamp scale to reasonable limits
        const minScale = 0.1;
        const maxScale = 10;
        const clampedScale = Math.max(minScale, Math.min(maxScale, newScale));
        
        // Calculate the center of the selection in canvas coordinates
        const selectionCenterX = selectionLeft + selectionWidth / 2;
        const selectionCenterY = selectionTop + selectionHeight / 2;
        
        // Calculate container center
        const containerCenterX = containerWidth / 2;
        const containerCenterY = containerHeight / 2;
        
        // Calculate new pan position to center the selection
        // We want: containerCenterX = selectionCenterX * clampedScale + newX
        // Therefore: newX = containerCenterX - selectionCenterX * clampedScale
        const newX = containerCenterX - selectionCenterX * clampedScale;
        const newY = containerCenterY - selectionCenterY * clampedScale;
        
        // Apply zoom and pan
        if (self.panzoomInstance.setTransform) {
            self.panzoomInstance.setTransform({ x: newX, y: newY, scale: clampedScale });
        } else if (self.panzoomInstance.zoom) {
            self.panzoomInstance.zoom(clampedScale, { animate: true });
            setTimeout(function() {
                if (self.panzoomInstance.pan) {
                    self.panzoomInstance.pan(newX, newY, { animate: true });
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
                const target = e.target;
                
                // Don't let Panzoom handle events on booth elements
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
                
                // Don't let Panzoom handle events on UI elements (buttons, inputs, toolbar, etc.)
                if (target.closest('.toolbar') ||
                    target.closest('.toolbar-btn') ||
                    target.closest('.modal') ||
                    target.closest('.dropdown') ||
                    target.closest('.sidebar') ||
                    target.closest('.info-toolbar') ||
                    target.closest('.properties-panel') ||
                    target.closest('.properties-panel-backdrop') ||
                    target.tagName === 'BUTTON' ||
                    target.tagName === 'INPUT' ||
                    target.tagName === 'SELECT' ||
                    target.tagName === 'A' ||
                    target.tagName === 'LABEL' ||
                    target.isContentEditable) {
                    return false; // Don't let Panzoom handle UI elements
                }
                
                // Only allow Panzoom on the canvas itself (for panning/zooming)
                // Check if the target is the canvas or a direct child of canvas (like grid overlay)
                const canvas = document.getElementById('print');
                if (canvas && (target === canvas || target.closest('#print') === canvas || target.id === 'gridOverlay')) {
                    return true; // Allow Panzoom on canvas background
                }
                
                // Default: don't let Panzoom handle it (safer)
                return false;
            }
        });
        
        // Save zoom/pan changes to database (debounced)
        let zoomPanSaveTimeout;
        const saveZoomPan = function() {
            clearTimeout(zoomPanSaveTimeout);
            zoomPanSaveTimeout = setTimeout(function() {
                self.saveCanvasSettingsToDatabase().catch(function(error) {
                    // Silently fail
                });
            }, 1000); // Save 1 second after last change
        };
        
        // Listen to panzoom events to save zoom/pan
        if (this.panzoomInstance) {
            const canvas = document.getElementById('print');
            if (canvas) {
                canvas.addEventListener('panzoomchange', saveZoomPan);
                canvas.addEventListener('panzoomzoom', saveZoomPan);
                canvas.addEventListener('panzoompan', saveZoomPan);
            }
        }
        
        // Track mouse position on canvas for zoom focal point
        const containerForMouseTracking = document.getElementById('printContainer');
        if (containerForMouseTracking) {
            containerForMouseTracking.addEventListener('mousemove', function(e) {
                if (self.panzoomInstance) {
                    const containerRect = containerForMouseTracking.getBoundingClientRect();
                    const canvas = document.getElementById('print');
                    
                    // Get current pan/zoom
                    let scale = 1;
                    let panX = 0;
                    let panY = 0;
                    if (self.panzoomInstance.getScale) {
                        scale = self.panzoomInstance.getScale();
                    }
                    if (self.panzoomInstance.getTransform) {
                        const transform = self.panzoomInstance.getTransform();
                        panX = transform.x || 0;
                        panY = transform.y || 0;
                    }
                    
                    // Convert mouse position to canvas coordinates
                    const mouseX = e.clientX - containerRect.left;
                    const mouseY = e.clientY - containerRect.top;
                    
                    // Convert to canvas coordinates accounting for pan and zoom
                    const canvasX = (mouseX - panX) / scale;
                    const canvasY = (mouseY - panY) / scale;
                    
                    // Store last mouse position in canvas coordinates
                    self.lastMousePosition = { x: canvasX, y: canvasY };
                }
            });
            
            // Also track mouse clicks to update focal point
            containerForMouseTracking.addEventListener('click', function(e) {
                // Only track clicks on the canvas itself, not on booths or UI elements
                if (e.target === canvas || 
                    (e.target === containerForMouseTracking && !e.target.closest('.dropped-booth') && 
                     !e.target.closest('.toolbar') && !e.target.closest('.sidebar'))) {
                    if (self.panzoomInstance) {
                        const containerRect = containerForMouseTracking.getBoundingClientRect();
                        
                        // Get current pan/zoom
                        let scale = 1;
                        let panX = 0;
                        let panY = 0;
                        if (self.panzoomInstance.getScale) {
                            scale = self.panzoomInstance.getScale();
                        }
                        if (self.panzoomInstance.getTransform) {
                            const transform = self.panzoomInstance.getTransform();
                            panX = transform.x || 0;
                            panY = transform.y || 0;
                        }
                        
                        // Convert click position to canvas coordinates
                        const clickX = e.clientX - containerRect.left;
                        const clickY = e.clientY - containerRect.top;
                        
                        // Convert to canvas coordinates accounting for pan and zoom
                        const canvasX = (clickX - panX) / scale;
                        const canvasY = (clickY - panY) / scale;
                        
                        // Store last mouse position in canvas coordinates
                        self.lastMousePosition = { x: canvasX, y: canvasY };
                    }
                }
            });
        }
        
        // Handle zoom events - Panzoom uses events on the element
        // Listen to panzoomzoom event on the canvas
        canvas.addEventListener('panzoomzoom', function(e) {
            let scale = 1;
            if (e.detail && e.detail.scale && !isNaN(e.detail.scale)) {
                scale = e.detail.scale;
            } else if (self.panzoomInstance && self.panzoomInstance.getScale) {
                const instanceScale = self.panzoomInstance.getScale();
                if (!isNaN(instanceScale) && instanceScale > 0) {
                    scale = instanceScale;
                }
            }
            self.zoomLevel = scale;
            $('#zoomLevel').text(Math.round(scale * 100) + '%');
        });
        
        // Also update on panzoomchange (more reliable for all zoom operations)
        canvas.addEventListener('panzoomchange', function(e) {
            let scale = 1;
            if (self.panzoomInstance && self.panzoomInstance.getScale) {
                const instanceScale = self.panzoomInstance.getScale();
                if (!isNaN(instanceScale) && instanceScale > 0) {
                    scale = instanceScale;
                }
            }
            self.zoomLevel = scale;
            $('#zoomLevel').text(Math.round(scale * 100) + '%');
        });
        
        // Listen for wheel zoom events to update display
        canvas.addEventListener('wheel', function(e) {
            if (self.panzoomInstance && self.panzoomInstance.getScale) {
                setTimeout(function() {
                    const scale = self.panzoomInstance.getScale();
                    if (!isNaN(scale) && scale > 0) {
                    self.zoomLevel = scale;
                    $('#zoomLevel').text(Math.round(scale * 100) + '%');
                    }
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
                // Panning is always enabled (n8n-like), but we can still track Space key for cursor
                // Panning is already enabled by default
                e.preventDefault();
            }
        });
        
        document.addEventListener('keyup', function(e) {
            if (e.code === 'Space') {
                spacePressed = false;
                // Panning is always enabled (n8n-like) - no need to disable
            }
        });
        
        // Prevent Panzoom from interfering with booth interactions (n8n-like)
        // DON'T add a handler on canvas for booth elements - let them handle their own events
        // Only handle canvas background clicks
        // Use bubble phase (false) so booth handlers (capture phase) fire first
        // BUT we need to make sure we don't interfere with booth mousedown
        // Reuse containerForMouseTracking variable (already defined above)
        canvas.addEventListener('mousedown', function(e) {
            // CRITICAL: Check for booth elements FIRST, before any other logic
            const target = e.target;
            
            // If clicking on UI elements (buttons, toolbar, etc.), do NOTHING - let their handlers work
            if (target.closest('.toolbar') ||
                target.closest('.toolbar-btn') ||
                target.closest('.modal') ||
                target.closest('.dropdown') ||
                target.closest('.sidebar') ||
                target.closest('.info-toolbar') ||
                target.closest('.properties-panel') ||
                target.tagName === 'BUTTON' ||
                target.tagName === 'INPUT' ||
                target.tagName === 'SELECT' ||
                target.tagName === 'A') {
                return; // Exit immediately - don't interfere with UI element handlers
            }
            
            const isBoothElement = target.closest('.dropped-booth') || 
                                  target.classList.contains('resize-handle') ||
                                  target.classList.contains('rotate-handle') ||
                                  target.closest('.transform-controls') ||
                                  target.closest('.booth-number-item');
            
            // If clicking on a booth element, do NOTHING - let the booth's own handler run
            // The booth handler uses capture phase, so it fires BEFORE this handler
            // But we still need to exit early to avoid any interference
            if (isBoothElement) {
                isInteractingWithBooth = true;
                // CRITICAL: Don't prevent default or stop propagation - let booth handler work
                return; // Exit immediately - don't prevent anything, don't stop propagation
            }
            
            // Continue with canvas background handling (only for non-booth, non-UI elements)
            // Mark that we're not interacting with a booth
                isInteractingWithBooth = false;
            
            // Track click position when Space is held for zoom focal point
            if (self.isSpacePanning && self.panzoomInstance && containerForMouseTracking) {
                const containerRect = containerForMouseTracking.getBoundingClientRect();
                
                // Get current pan/zoom
                let scale = 1;
                let panX = 0;
                let panY = 0;
                if (self.panzoomInstance.getScale) {
                    scale = self.panzoomInstance.getScale();
                }
                if (self.panzoomInstance.getTransform) {
                    const transform = self.panzoomInstance.getTransform();
                    panX = transform.x || 0;
                    panY = transform.y || 0;
                }
                
                // Convert click position to canvas coordinates
                const clickX = e.clientX - containerRect.left;
                const clickY = e.clientY - containerRect.top;
                
                // Convert to canvas coordinates accounting for pan and zoom
                const canvasX = (clickX - panX) / scale;
                const canvasY = (clickY - panY) / scale;
                
                // Store as zoom focal point
                self.zoomFocalPoint = { x: canvasX, y: canvasY };
            }
            
            // Let Panzoom handle canvas background interactions
            // Don't prevent default or stop propagation - let Panzoom work normally
        }, false); // Use bubble phase - booth handlers run first, then canvas handler only for background
        
        // Panning is always enabled (n8n-like) - no need to disable on mouseup
        document.addEventListener('mouseup', function() {
            setTimeout(function() {
                isInteractingWithBooth = false;
            }, 50);
        });
        
        // Update Panzoom panning state when Space is pressed/released
        // Also switch to pan tool when Space is held, and restore previous tool when released
        const spaceKeyHandler = function(e) {
            if (e.code === 'Space') {
                // Don't interfere if user is typing in inputs or if Ctrl/Cmd+Space (zoom selection)
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
                    return;
                }
                
                // Don't interfere with Ctrl+Space (zoom selection)
                if (e.ctrlKey || e.metaKey) {
                    return;
                }
                
                if (e.type === 'keydown' && !e.repeat) {
                    // Prevent default to avoid page scrolling
                    e.preventDefault();
                    
                    // Space pressed - switch to pan tool
                    if (!self.isSpacePanning) {
                        // Save current tool if not already panning
                        if (self.currentTool !== 'pan') {
                            self.previousTool = self.currentTool;
                        }
                        // Switch to pan tool
                        self.isSpacePanning = true;
                        self.switchTool('pan');
                        // Update button state
                        const btn = document.getElementById('btnPanTool');
                        if (btn) {
                            $('.toolbar-btn[data-tool]').removeClass('active');
                            $(btn).addClass('active');
                        }
                    }
                    
                    // Enable panning in Panzoom
                    if (self.panzoomInstance && self.panzoomInstance.setOptions) {
                        self.panzoomInstance.setOptions({ disablePan: false });
                    }
                } else if (e.type === 'keyup') {
                    // Prevent default to avoid page scrolling
                    e.preventDefault();
                    
                    // Space released - restore previous tool
                    if (self.isSpacePanning) {
                        self.isSpacePanning = false;
                        // Note: Keep zoomFocalPoint stored even after Space is released
                        // so zoom operations continue to use the clicked position
                        
                        // Switch back to previous tool
                        if (self.previousTool && self.previousTool !== 'pan') {
                            self.switchTool(self.previousTool);
                            // Update button state
                            const toolBtnId = 'btn' + self.previousTool.charAt(0).toUpperCase() + self.previousTool.slice(1) + 'Tool';
                            const toolBtn = document.getElementById(toolBtnId);
                            if (toolBtn) {
                                $('.toolbar-btn[data-tool]').removeClass('active');
                                $(toolBtn).addClass('active');
                            }
                        }
                    }
                    
                    // Disable panning in Panzoom
                    if (self.panzoomInstance && self.panzoomInstance.setOptions) {
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
            // Don't trigger shortcuts when typing in inputs
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.isContentEditable) {
                return;
            }
            
            // Rotation shortcuts
            if ((e.key === 'r' || e.key === 'R') && self.selectedBooths.length > 0) {
                // Rotate right (+90°) with R key
                if (!e.shiftKey) {
                    self.rotateSelectedBooths(90);
                    e.preventDefault();
                } else {
                    // Rotate left (-90°) with Shift+R
                    self.rotateSelectedBooths(-90);
                    e.preventDefault();
                }
            }
            
            // Delete key
            if (e.key === 'Delete' && self.selectedBooths.length > 0) {
                e.preventDefault(); // Prevent browser default delete behavior
                
                const boothsToSave = []; // Collect booth data for batch save to clear positions
                
                self.selectedBooths.forEach(function(booth) {
                    // Get booth data before removing
                    const boothId = booth.getAttribute('data-booth-id');
                    const boothNumber = booth.getAttribute('data-booth-number') || booth.textContent.trim();
                    const boothStatus = booth.getAttribute('data-booth-status') || '1';
                    const clientId = booth.getAttribute('data-client-id') || '';
                    const userId = booth.getAttribute('data-user-id') || '';
                    const categoryId = booth.getAttribute('data-category-id') || '';
                    const subCategoryId = booth.getAttribute('data-sub-category-id') || '';
                    const assetId = booth.getAttribute('data-asset-id') || '';
                    const boothTypeId = booth.getAttribute('data-booth-type-id') || '';
                    
                    // Add booth back to sidebar before removing from canvas
                    if (boothId && boothNumber) {
                        self.addBoothToSidebar({
                            id: boothId,
                            number: boothNumber,
                            status: boothStatus,
                            clientId: clientId,
                            userId: userId,
                            categoryId: categoryId,
                            subCategoryId: subCategoryId,
                            assetId: assetId,
                            boothTypeId: boothTypeId
                        });
                    }
                    
                    // Prepare data to clear positions in database
                    if (boothId) {
                        boothsToSave.push({
                            id: parseInt(boothId),
                            position_x: null,
                            position_y: null,
                            width: null,
                            height: null,
                            rotation: null,
                            z_index: null,
                            font_size: null,
                            border_width: null,
                            border_radius: null,
                            opacity: null
                        });
                    }
                    
                    booth.remove();
                });
                
                // Batch save to clear positions in database
                if (boothsToSave.length > 0) {
                    self.saveBoothsBatch(boothsToSave).catch(function(error) {
                        console.error('Error clearing booth positions:', error);
                    });
                }
                
                self.selectedBooths = [];
                self.updateInfoToolbar(null);
                self.saveState();
                
                // Update booth count
                if (self.updateBoothCount) {
                    self.updateBoothCount();
                }
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
    
    // Print entire floorplan
    printFloorplan: function() {
        const self = this;
        
        // #region agent log
        fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:9698',message:'printFloorplan() called',data:{timestamp:Date.now()},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H3'})}).catch(()=>{});
        // #endregion
        
        const canvas = self.getElement('print');
        
        // #region agent log
        fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:9700',message:'Canvas check',data:{canvasFound:!!canvas,html2canvasAvailable:typeof html2canvas!=='undefined'},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H3'})}).catch(()=>{});
        // #endregion
        
        if (!canvas) {
            showNotification('Canvas not found!', 'error');
            return;
        }
        
        // Check if html2canvas is available
        if (typeof html2canvas === 'undefined') {
            showNotification('Print library not loaded. Please refresh the page.', 'error');
            return;
        }
        
        // Show loading notification
        Swal.fire({
            title: 'Preparing Print...',
            html: 'Capturing floorplan image...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Get actual canvas dimensions (including scroll area)
        // Use scrollWidth/scrollHeight to get full content size, not just visible area
        const canvasWidth = Math.max(
            canvas.scrollWidth || canvas.offsetWidth || canvas.clientWidth,
            self.canvasWidth || 1200
        );
        const canvasHeight = Math.max(
            canvas.scrollHeight || canvas.offsetHeight || canvas.clientHeight,
            self.canvasHeight || 800
        );
        
        // Temporarily hide UI elements that shouldn't be printed
        const infoToolbar = self.getElement('infoToolbar');
        const designerToolbar = document.querySelector('.designer-toolbar');
        const sidebar = document.getElementById('designerSidebar');
        const selectionBoxes = canvas.querySelectorAll('.selection-box');
        const transformControls = canvas.querySelectorAll('.transform-controls');
        const zoomSelection = canvas.querySelector('.zoom-selection');
        
        // Store original display states and styles
        const originalStates = {
            infoToolbar: infoToolbar ? infoToolbar.style.display : '',
            designerToolbar: designerToolbar ? designerToolbar.style.display : '',
            sidebar: sidebar ? sidebar.style.display : '',
            canvasOverflow: canvas.style.overflow,
            canvasPosition: canvas.style.position
        };
        
        // Hide UI elements
        if (infoToolbar) infoToolbar.style.display = 'none';
        if (designerToolbar) designerToolbar.style.display = 'none';
        if (sidebar) sidebar.style.display = 'none';
        selectionBoxes.forEach(function(box) {
            box.style.display = 'none';
        });
        transformControls.forEach(function(control) {
            control.style.display = 'none';
        });
        if (zoomSelection) zoomSelection.style.display = 'none';
        
        // Ensure canvas is visible and properly sized for capture
        const originalCanvasOverflow = canvas.style.overflow;
        canvas.style.overflow = 'visible';
        
        // Capture the canvas using html2canvas with proper options
        html2canvas(canvas, {
            backgroundColor: '#ffffff',
            scale: 2, // Higher quality for printing (2x resolution)
            logging: false,
            useCORS: true,
            allowTaint: true,
            width: canvasWidth,
            height: canvasHeight,
            windowWidth: canvasWidth,
            windowHeight: canvasHeight,
            scrollX: 0,
            scrollY: 0,
            x: 0,
            y: 0
        }).then(function(canvasImg) {
            // Restore canvas overflow
            canvas.style.overflow = originalCanvasOverflow;
            // Restore UI elements
            if (infoToolbar) infoToolbar.style.display = originalStates.infoToolbar;
            if (designerToolbar) designerToolbar.style.display = originalStates.designerToolbar;
            if (sidebar) sidebar.style.display = originalStates.sidebar;
            selectionBoxes.forEach(function(box) {
                box.style.display = '';
            });
            transformControls.forEach(function(control) {
                control.style.display = '';
            });
            
            // Convert canvas to image data URL
            const imgData = canvasImg.toDataURL('image/png');
            
            // Create a new window for printing
            const printWindow = window.open('', '_blank');
            if (!printWindow) {
                Swal.close();
                showNotification('Please allow popups to print the floorplan.', 'error');
                return;
            }
            
            // Create print-friendly HTML
            // #region agent log
            fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:9809',message:'Creating printHTML',data:{imgDataLength:imgData?imgData.length:0},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H5'})}).catch(()=>{});
            // #endregion
            
            const printDate = new Date().toLocaleString();
            // Build print HTML using array join method to completely avoid string concatenation issues
            var htmlParts = [];
            htmlParts.push('<!DOCTYPE html>');
            htmlParts.push('<html>');
            htmlParts.push('<head>');
            htmlParts.push('<title>Floorplan Print</title>');
            htmlParts.push('<style>');
            htmlParts.push('@media print{@page{margin:0.5cm;size:landscape}body{margin:0;padding:0}img{max-width:100%;height:auto;page-break-inside:avoid}}');
            htmlParts.push('body{margin:0;padding:20px;text-align:center;background:#f5f5f5}');
            htmlParts.push('.print-container{display:inline-block;background:white;padding:20px;box-shadow:0 0 10px rgba(0,0,0,0.1)}');
            htmlParts.push('.print-title{font-family:Arial,sans-serif;font-size:24px;font-weight:bold;margin-bottom:20px;color:#333}');
            htmlParts.push('.print-date{font-family:Arial,sans-serif;font-size:12px;color:#666;margin-bottom:20px}');
            htmlParts.push('img{display:block;margin:0 auto;max-width:100%;height:auto}');
            htmlParts.push('@media print{body{background:white;padding:0}.print-container{box-shadow:none;padding:0}.print-title,.print-date{display:none}}');
            htmlParts.push('</style>');
            htmlParts.push('</head>');
            htmlParts.push('<body>');
            htmlParts.push('<div class="print-container">');
            htmlParts.push('<div class="print-title">Floorplan</div>');
            htmlParts.push('<div class="print-date">Printed on: ');
            htmlParts.push(printDate);
            htmlParts.push('</div>');
            htmlParts.push('<img alt="Floorplan" src="');
            htmlParts.push(imgData);
            htmlParts.push('" />');
            htmlParts.push('</div>');
            htmlParts.push('<' + 'script>');
            htmlParts.push('window.onload=function(){');
            htmlParts.push('setTimeout(function(){window.print()},250);');
            htmlParts.push('};');
            htmlParts.push('<' + '/script>');
            htmlParts.push('</body>');
            htmlParts.push('</html>');
            var printHTML = htmlParts.join('');
            
            // #region agent log
            fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:9923',message:'Before writing to printWindow',data:{printHTMLLength:printHTML?printHTML.length:0,printWindowExists:!!printWindow},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H3'})}).catch(()=>{});
            // #endregion
            
            // Write content to print window
            printWindow.document.write(printHTML);
            printWindow.document.close();
            
            // #region agent log
            fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:9926',message:'After writing to printWindow',data:{timestamp:Date.now()},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H3'})}).catch(()=>{});
            // #endregion
            
            // Close loading notification
            Swal.close();
            
            // Show success notification
            showNotification('Print dialog opened. If it did not open, please check your popup blocker.', 'success');
            
        }).catch(function(error) {
            // #region agent log
            fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:9903',message:'Print error caught',data:{errorMessage:error?error.message:'unknown',errorStack:error?error.stack:'',errorName:error?error.name:''},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H3'})}).catch(()=>{});
            // #endregion
            
            // Restore canvas overflow
            canvas.style.overflow = originalStates.canvasOverflow || '';
            
            // Restore UI elements on error
            if (infoToolbar) infoToolbar.style.display = originalStates.infoToolbar;
            if (designerToolbar) designerToolbar.style.display = originalStates.designerToolbar;
            if (sidebar) sidebar.style.display = originalStates.sidebar;
            selectionBoxes.forEach(function(box) {
                box.style.display = '';
            });
            transformControls.forEach(function(control) {
                control.style.display = '';
            });
            if (zoomSelection) zoomSelection.style.display = '';
            
            Swal.close();
            console.error('Print error:', error);
            showNotification('Failed to capture floorplan for printing. Please try again.', 'error');
        });
    },
    
    // Restore state
    restoreState: function(state) {
        const canvas = this.getElement('print');
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

// Helper function for notifications (using SweetAlert2)
function showNotification(message, type) {
    const iconMap = {
        'success': 'success',
        'error': 'error',
        'warning': 'warning',
        'info': 'info'
    };
    Swal.fire({
        icon: iconMap[type] || 'info',
        title: message,
        timer: type === 'error' ? 4000 : 2000,
        showConfirmButton: false,
        toast: true,
        position: 'bottom-right'
    });
}

// Global error handler
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e);
});

// CRITICAL: Load floor plan image IMMEDIATELY when DOM is ready (before FloorPlanDesigner.init)
// This ensures the canvas automatically shows the correct floor plan image when user clicks "View Booths"
$(document).ready(function() {
    // AUTOMATICALLY load floor plan image if available (highest priority - before anything else)
    @if(isset($currentFloorPlan) && $currentFloorPlan && $currentFloorPlan->floor_image)
        console.log('[Auto-Load Floor Plan] Setting canvas background IMMEDIATELY for floor plan {{ $currentFloorPlan->id }}: {{ $currentFloorPlan->name }}');
        const canvas = document.getElementById('print');
        if (canvas) {
            @php
                $floorPlanImageUrlReady = asset($currentFloorPlan->floor_image);
                $floorPlanImagePathReady = $currentFloorPlan->floor_image;
            @endphp
            const floorPlanImageUrl = '{{ $floorPlanImageUrlReady }}';
            const floorPlanImagePath = '{{ $floorPlanImagePathReady }}';
            
            console.log('[Auto-Load Floor Plan] Image path:', floorPlanImagePath);
            console.log('[Auto-Load Floor Plan] Image URL:', floorPlanImageUrl);
            
            // IMMEDIATELY set canvas background image (automatic load - no delays)
            canvas.style.backgroundImage = 'url(\'' + floorPlanImageUrl + '?t=' + Date.now() + '\')';
            canvas.style.backgroundSize = '100% 100%';
            canvas.style.backgroundRepeat = 'no-repeat';
            canvas.style.backgroundPosition = 'top left';
            canvas.style.backgroundAttachment = 'local';
            canvas.style.margin = '0';
            canvas.style.display = 'block';
            canvas.style.float = 'left';
            
            console.log('[Auto-Load Floor Plan] ✅ Canvas background image set immediately:', canvas.style.backgroundImage);
            
            // Preload image to verify it exists and get dimensions
            const img = new Image();
            img.onload = function() {
                console.log('[Auto-Load Floor Plan] ✅ Image loaded successfully:', {
                    floor_plan_id: {{ $currentFloorPlan->id }},
                    floor_plan_name: '{{ $currentFloorPlan->name }}',
                    image_width: img.naturalWidth || img.width,
                    image_height: img.naturalHeight || img.height,
                    image_url: floorPlanImageUrl
                });
            };
            img.onerror = function() {
                console.error('[Auto-Load Floor Plan] ❌ Failed to load image:', {
                    floor_plan_id: {{ $currentFloorPlan->id }},
                    floor_plan_name: '{{ $currentFloorPlan->name }}',
                    image_url: floorPlanImageUrl,
                    image_path: floorPlanImagePath
                });
            };
            img.src = floorPlanImageUrl;
        } else {
            console.error('[Auto-Load Floor Plan] ❌ Canvas element not found!');
        }
    @else
        console.log('[Auto-Load Floor Plan] No floor plan image to load for current floor plan');
    @endif
    
    // Ensure info toolbar is always visible
    const infoToolbar = document.getElementById('infoToolbar');
    if (infoToolbar) {
        infoToolbar.style.display = 'flex';
        infoToolbar.style.visibility = 'visible';
        infoToolbar.style.opacity = '1';
    }
    
    // Initialize FloorPlanDesigner (image is already set above, this will handle resize and other setup)
    try {
        FloorPlanDesigner.init();
    } catch (error) {
        console.error('Error initializing FloorPlanDesigner:', error);
        customAlert('Error initializing Floor Plan Designer: ' + error.message, 'error');
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
            
            // Update booth count
            if (FloorPlanDesigner.updateBoothCount) {
                FloorPlanDesigner.updateBoothCount();
            }
        }
    });
    
    // Print button handler
    // #region agent log
    const btnPrintElement = $('#btnPrint');
    fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:10032',message:'Print button handler setup',data:{btnPrintFound:btnPrintElement.length>0,jQueryReady:typeof $!=='undefined'},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H1'})}).catch(()=>{});
    // #endregion
    
    $('#btnPrint').on('click', function() {
        // #region agent log
        fetch('http://127.0.0.1:7244/ingest/32c840ca-dc83-4c7d-be79-34d96940ebef',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'booths/index.blade.php:10033',message:'Print button clicked',data:{timestamp:Date.now()},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H1'})}).catch(()=>{});
        // #endregion
        FloorPlanDesigner.printFloorplan();
    });
    
    console.log('✅ Floor Plan Designer ready!');
});

// Floor Plan Switcher
function switchFloorPlan(floorPlanId) {
    if (floorPlanId) {
        // Clear zone settings cache when switching floor plans (zones are floor-plan-specific)
        if (typeof FloorPlanDesigner !== 'undefined' && FloorPlanDesigner.zoneSettingsCache) {
            FloorPlanDesigner.zoneSettingsCache = {};
            FloorPlanDesigner.zoneSettingsLoading = {};
            console.log('Cleared zone settings cache for floor plan switch');
        }
        
        window.location.href = '{{ route("booths.index") }}?floor_plan_id=' + floorPlanId;
    } else {
        window.location.href = '{{ route("booths.index") }}';
    }
}
</script>
@endpush

