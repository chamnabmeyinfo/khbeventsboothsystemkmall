<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Public View - {{ $floorPlan->name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            overflow: hidden;
        }
        
        .public-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 1000;
            position: relative;
        }
        
        .public-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        .public-header .header-info {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 0.9rem;
        }
        
        .zoom-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 12px;
            border-radius: 8px;
        }
        
        .zoom-btn {
            background: rgba(255, 255, 255, 0.3);
            border: none;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        
        .zoom-btn:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        
        .zoom-level {
            color: white;
            font-weight: 600;
            min-width: 50px;
            text-align: center;
        }
        
        .canvas-container {
            width: 100%;
            height: calc(100vh - 70px);
            overflow: hidden;
            position: relative;
            background: #e9ecef;
        }
        
        .floorplan-canvas {
            position: relative;
            background-size: 100% 100% !important;
            background-repeat: no-repeat !important;
            background-position: top left !important;
            cursor: grab;
            min-width: 10000px;
            min-height: 10000px;
            width: 10000px;
            height: 10000px;
            background-attachment: local;
        }
        
        .floorplan-canvas:active {
            cursor: grabbing;
        }
        
        .dropped-booth {
            position: absolute;
            border: 2px solid;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            user-select: none;
            pointer-events: auto;
        }
        
        .dropped-booth:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 1000 !important;
        }
        
        .dropped-booth.status-1 {
            background: rgba(40, 167, 69, 0.9);
            border-color: #28a745;
            color: white;
        }
        
        .dropped-booth.status-2 {
            background: rgba(13, 202, 240, 0.9);
            border-color: #0dcaf0;
            color: white;
        }
        
        .dropped-booth.status-3 {
            background: rgba(255, 193, 7, 0.9);
            border-color: #ffc107;
            color: #333;
        }
        
        .dropped-booth.status-4 {
            background: rgba(108, 117, 125, 0.7);
            border-color: #6c757d;
            color: white;
        }
        
        .dropped-booth.status-5 {
            background: rgba(33, 37, 41, 0.9);
            border-color: #212529;
            color: white;
        }
        
        .booth-tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            pointer-events: none;
            z-index: 10000;
            display: none;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
    <!-- Public Header -->
    <div class="public-header">
        <div>
            <h1><i class="fas fa-map mr-2"></i>{{ $floorPlan->name }}</h1>
            @if($floorPlan->project_name)
                <div style="font-size: 0.85rem; opacity: 0.9; margin-top: 4px;">
                    {{ $floorPlan->project_name }}
                </div>
            @endif
        </div>
        <div class="header-info">
            <div>
                <i class="fas fa-store mr-1"></i>
                <strong>{{ $booths->count() }}</strong> Booths
            </div>
            <div class="zoom-controls">
                <button class="zoom-btn" id="zoomOut" title="Zoom Out">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="zoom-level" id="zoomLevel">100%</span>
                <button class="zoom-btn" id="zoomIn" title="Zoom In">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="zoom-btn" id="zoomFit" title="Fit to View">
                    <i class="fas fa-expand-arrows-alt"></i>
                </button>
                <button class="zoom-btn" id="zoomReset" title="Reset Zoom">
                    <i class="fas fa-home"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Canvas Container -->
    <div class="canvas-container" id="printContainer">
        <div id="print" class="floorplan-canvas"
             style="@if($floorImage && $floorImageExists)
             background-image: url('{{ $floorImageUrl }}'); background-size: 100% 100%; background-repeat: no-repeat; background-position: top left; background-attachment: local;
             @else
             background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
             @endif">
            @if($floorImage && $floorImageExists)
                <img src="{{ $floorImageUrl }}" 
                     id="floorplanImageElement"
                     alt="Floor Plan Map"
                     style="display: none;"
                     onerror="console.error('Failed to load floor plan image');"/>
            @endif
        </div>
    </div>
    
    <!-- Booth Tooltip -->
    <div class="booth-tooltip" id="boothTooltip"></div>
    
    <script>
        let panzoomInstance;
        let zoomLevel = 1;
        const canvas = document.getElementById('print');
        const container = document.getElementById('printContainer');
        
        // Initialize Panzoom
        if (canvas && typeof Panzoom !== 'undefined') {
            panzoomInstance = Panzoom(canvas, {
                maxScale: 5,
                minScale: 0.01, // Allow zooming out much further (1% instead of 10%)
                contain: 'outside',
                disablePan: false,
                disableZoom: false,
            });
            
            // Update zoom level display
            canvas.addEventListener('panzoomzoom', function(e) {
                if (e.detail && e.detail.scale) {
                    zoomLevel = e.detail.scale;
                    document.getElementById('zoomLevel').textContent = Math.round(zoomLevel * 100) + '%';
                }
            });
        }
        
        // Load booths from database
        const booths = @json($boothsForJS);
        const canvasWidth = {{ $canvasWidth }};
        const canvasHeight = {{ $canvasHeight }};
        
        // Set canvas size
        if (canvas) {
            canvas.style.width = canvasWidth + 'px';
            canvas.style.height = canvasHeight + 'px';
            canvas.style.minWidth = canvasWidth + 'px';
            canvas.style.minHeight = canvasHeight + 'px';
        }
        
        // Load booth positions
        booths.forEach(function(booth) {
            if (booth.position_x !== null && booth.position_y !== null) {
                const boothElement = document.createElement('div');
                boothElement.className = 'dropped-booth status-' + booth.status;
                boothElement.setAttribute('data-booth-id', booth.id);
                boothElement.setAttribute('data-booth-number', booth.booth_number);
                boothElement.textContent = booth.booth_number;
                
                // Set position
                boothElement.style.left = booth.position_x + 'px';
                boothElement.style.top = booth.position_y + 'px';
                
                // Set dimensions
                if (booth.width) boothElement.style.width = booth.width + 'px';
                if (booth.height) boothElement.style.height = booth.height + 'px';
                
                // Set rotation
                if (booth.rotation) {
                    boothElement.style.transform = 'rotate(' + booth.rotation + 'deg)';
                }
                
                // Set z-index
                if (booth.z_index) boothElement.style.zIndex = booth.z_index;
                
                // Set appearance
                if (booth.background_color) boothElement.style.backgroundColor = booth.background_color;
                if (booth.border_color) boothElement.style.borderColor = booth.border_color;
                if (booth.text_color) boothElement.style.color = booth.text_color;
                if (booth.font_size) boothElement.style.fontSize = booth.font_size + 'px';
                if (booth.border_width) boothElement.style.borderWidth = booth.border_width + 'px';
                if (booth.border_radius) boothElement.style.borderRadius = booth.border_radius + 'px';
                if (booth.opacity !== null) boothElement.style.opacity = booth.opacity;
                
                // Add tooltip on hover
                const tooltip = document.getElementById('boothTooltip');
                boothElement.addEventListener('mouseenter', function(e) {
                    const rect = this.getBoundingClientRect();
                    tooltip.textContent = 'Booth ' + booth.booth_number + 
                        (booth.company ? ' - ' + booth.company : '') +
                        (booth.price ? ' - $' + booth.price : '');
                    tooltip.style.display = 'block';
                    tooltip.style.left = (rect.left + rect.width / 2) + 'px';
                    tooltip.style.top = (rect.top - 35) + 'px';
                    tooltip.style.transform = 'translateX(-50%)';
                });
                
                boothElement.addEventListener('mouseleave', function() {
                    tooltip.style.display = 'none';
                });
                
                boothElement.addEventListener('mousemove', function(e) {
                    if (tooltip.style.display === 'block') {
                        const rect = this.getBoundingClientRect();
                        tooltip.style.left = (e.clientX) + 'px';
                        tooltip.style.top = (rect.top - 35) + 'px';
                    }
                });
                
                canvas.appendChild(boothElement);
            }
        });
        
        // Zoom controls
        document.getElementById('zoomIn').addEventListener('click', function() {
            if (panzoomInstance) {
                const currentScale = panzoomInstance.getScale ? panzoomInstance.getScale() : zoomLevel;
                const newScale = Math.min(currentScale * 1.2, 5); // Max 500%
                panzoomInstance.zoom(newScale);
            }
        });
        
        document.getElementById('zoomOut').addEventListener('click', function() {
            if (panzoomInstance) {
                // Zoom directly to minimum scale (0.01 = 1%)
                const minScale = 0.01;
                panzoomInstance.zoom(minScale, { animate: false });
                
                // Update zoom level display
                zoomLevel = minScale;
                document.getElementById('zoomLevel').textContent = Math.round(minScale * 100) + '%';
            }
        });
        
        document.getElementById('zoomReset').addEventListener('click', function() {
            if (panzoomInstance) {
                panzoomInstance.reset();
                zoomLevel = 1;
                document.getElementById('zoomLevel').textContent = '100%';
            }
        });
        
        // Function to calculate content bounds (canvas + all booths)
        function calculateContentBounds() {
            let minX = 0;
            let minY = 0;
            let maxX = canvasWidth || 1200;
            let maxY = canvasHeight || 800;
            
            // Check all booths to find the actual content bounds
            const boothElements = canvas.querySelectorAll('.dropped-booth');
            if (boothElements.length > 0) {
                boothElements.forEach(function(booth) {
                    const left = parseFloat(booth.style.left) || 0;
                    const top = parseFloat(booth.style.top) || 0;
                    const width = parseFloat(booth.offsetWidth) || 50;
                    const height = parseFloat(booth.offsetHeight) || 50;
                    
                    minX = Math.min(minX, left);
                    minY = Math.min(minY, top);
                    maxX = Math.max(maxX, left + width);
                    maxY = Math.max(maxY, top + height);
                });
            }
            
            // Ensure minimum bounds
            const width = Math.max(maxX - minX, canvasWidth || 1200);
            const height = Math.max(maxY - minY, canvasHeight || 800);
            
            return {
                minX: minX,
                minY: minY,
                maxX: maxX,
                maxY: maxY,
                width: width,
                height: height
            };
        }
        
        document.getElementById('zoomFit').addEventListener('click', function() {
            if (panzoomInstance && canvas && container) {
                const containerWidth = container.clientWidth;
                const containerHeight = container.clientHeight;
                
                // Ensure we have valid dimensions
                if (containerWidth <= 0 || containerHeight <= 0) {
                    console.warn('Invalid container dimensions');
                    return;
                }
                
                // Calculate actual content bounds
                const bounds = calculateContentBounds();
                const contentWidth = Math.max(bounds.width || canvasWidth, 100); // Minimum 100px
                const contentHeight = Math.max(bounds.height || canvasHeight, 100); // Minimum 100px
                
                // Add padding (5% on each side)
                const padding = 0.05;
                const availableWidth = containerWidth * (1 - padding * 2);
                const availableHeight = containerHeight * (1 - padding * 2);
                
                // Calculate scale to fit content
                const scaleX = availableWidth / contentWidth;
                const scaleY = availableHeight / contentHeight;
                let fitScale = Math.min(scaleX, scaleY);
                
                // Clamp scale to minScale and maxScale limits
                const minScale = 0.01;
                const maxScale = 5;
                fitScale = Math.max(minScale, Math.min(maxScale, fitScale));
                
                // Calculate center position of content
                const contentCenterX = bounds.minX + (contentWidth / 2);
                const contentCenterY = bounds.minY + (contentHeight / 2);
                
                // Viewport center
                const viewportCenterX = containerWidth / 2;
                const viewportCenterY = containerHeight / 2;
                
                // Calculate pan to center the content
                // panX = viewportCenterX - (contentCenterX * fitScale)
                const panX = viewportCenterX - (contentCenterX * fitScale);
                const panY = viewportCenterY - (contentCenterY * fitScale);
                
                // Apply zoom first, then pan
                if (panzoomInstance.zoom) {
                    panzoomInstance.zoom(fitScale, { animate: false });
                }
                
                // Wait a bit for zoom to apply, then set pan position
                setTimeout(function() {
                    // Get current scale after zoom (might be clamped)
                    const currentTransform = panzoomInstance.getTransform ? panzoomInstance.getTransform() : { scale: fitScale, x: 0, y: 0 };
                    const currentScale = currentTransform.scale || fitScale;
                    
                    // Recalculate pan with actual scale
                    const actualPanX = viewportCenterX - (contentCenterX * currentScale);
                    const actualPanY = viewportCenterY - (contentCenterY * currentScale);
                    
                    // Apply transform with fallback methods
                    if (panzoomInstance.setTransform) {
                        panzoomInstance.setTransform({ 
                            x: actualPanX, 
                            y: actualPanY, 
                            scale: currentScale 
                        });
                    } else if (panzoomInstance.pan) {
                        panzoomInstance.pan(actualPanX, actualPanY, { animate: false });
                    } else if (panzoomInstance.moveTo) {
                        panzoomInstance.moveTo(contentCenterX, contentCenterY, { animate: false });
                    }
                    
                    // Update zoom level display
                    zoomLevel = currentScale;
                    document.getElementById('zoomLevel').textContent = Math.round(currentScale * 100) + '%';
                }, 100);
            }
        });
        
        // Auto-fit on load - wait for all booths to be rendered
        window.addEventListener('load', function() {
            setTimeout(function() {
                // Ensure all booths are rendered before fitting
                const zoomFitBtn = document.getElementById('zoomFit');
                if (zoomFitBtn) {
                    zoomFitBtn.click();
                }
            }, 500);
        });
        
        // Fit on resize with debouncing
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                const zoomFitBtn = document.getElementById('zoomFit');
                if (zoomFitBtn) {
                    zoomFitBtn.click();
                }
            }, 300);
        });
    </script>
</body>
</html>
