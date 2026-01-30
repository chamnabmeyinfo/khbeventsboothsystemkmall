<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Public View - {{ $floorPlan->name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <script src="{{ asset('vendor/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('vendor/panzoom/panzoom.min.js') }}"></script>
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
            padding: 8px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            z-index: 1000;
            position: relative;
            flex-wrap: wrap;
            gap: 10px;
            min-height: 48px;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            flex-wrap: wrap;
        }
        
        .header-title-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header-title-section h1 {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            line-height: 1;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .header-title-section h1 i {
            font-size: 1rem;
        }
        
        .project-name {
            font-size: 0.75rem;
            opacity: 0.85;
            font-weight: 400;
            margin-left: 4px;
        }
        
        .header-stats {
            display: flex;
            gap: 8px;
        }
        
        .stat-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 10px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            backdrop-filter: blur(10px);
        }
        
        .stat-badge i {
            font-size: 0.7rem;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        /* Status Legend - Minimal Compact Layout */
        .status-legend-inline {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            padding: 4px 10px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            flex-wrap: wrap;
        }
        
        .legend-label {
            color: white;
            font-weight: 500;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
        }
        
        .legend-label i {
            font-size: 0.7rem;
        }
        
        .legend-items-horizontal {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .legend-item-horizontal {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .legend-color {
            width: 14px;
            height: 14px;
            border-radius: 3px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            flex-shrink: 0;
        }
        
        .legend-text {
            color: white;
            font-weight: 400;
            font-size: 0.7rem;
            white-space: nowrap;
        }
        
        @media (max-width: 768px) {
            .public-header {
                padding: 6px 12px;
                gap: 8px;
            }
            
            .header-title-section h1 {
                font-size: 1rem;
            }
            
            .status-legend-inline {
                padding: 3px 8px;
                gap: 6px;
            }
            
            .legend-label {
                font-size: 0.65rem;
            }
            
            .legend-items-horizontal {
                gap: 6px;
            }
            
            .legend-color {
                width: 12px;
                height: 12px;
            }
            
            .legend-text {
                font-size: 0.65rem;
            }
            
            .zoom-controls-simple {
                padding: 3px 6px;
                gap: 4px;
            }
            
            .zoom-btn-simple {
                width: 22px;
                height: 22px;
            }
        }
        
        @media (max-width: 480px) {
            .public-header {
                padding: 6px 10px;
                min-height: 44px;
            }
            
            .header-left {
                gap: 8px;
            }
            
            .header-title-section h1 {
                font-size: 0.9rem;
            }
            
            .project-name {
                display: none;
            }
            
            .status-legend-inline {
                width: 100%;
                justify-content: center;
                margin-top: 4px;
            }
        }
        
        /* Minimal Zoom Controls */
        .header-action-link, .header-action-hint {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            color: white;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            transition: background 0.2s;
        }
        .header-action-link:hover {
            background: rgba(255, 255, 255, 0.35);
            color: white;
        }
        .header-action-text { white-space: nowrap; }
        @media (max-width: 575.98px) {
            .header-action-text { display: none; }
        }
        .btn-book-booth-public {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-book-booth-public:hover { color: white; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(40, 167, 69, 0.5); }
        /* Right-click context menu (public view - Create Booking) */
        .public-view-context-menu {
            display: none;
            position: fixed;
            z-index: 10001;
            min-width: 200px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            padding: 6px 0;
            list-style: none;
            margin: 0;
            border: 1px solid #e9ecef;
        }
        .public-view-context-menu.active { display: block; }
        .public-view-context-menu li { margin: 0; padding: 0; }
        .public-view-context-menu a,
        .public-view-context-menu button {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 16px;
            border: none;
            background: none;
            text-align: left;
            font-size: 0.95rem;
            color: #495057;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }
        .public-view-context-menu a:hover,
        .public-view-context-menu button:hover {
            background: #f0f4ff;
            color: #667eea;
        }
        .public-view-context-menu a i,
        .public-view-context-menu button i { width: 20px; text-align: center; }
        .zoom-controls-simple {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.15);
            padding: 4px 8px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .zoom-label {
            color: white;
            font-weight: 500;
            font-size: 0.7rem;
            margin-right: 2px;
        }
        
        .zoom-btn-simple {
            background: rgba(255, 255, 255, 0.25);
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 0.7rem;
            padding: 0;
        }
        
        .zoom-btn-simple:hover {
            background: rgba(255, 255, 255, 0.4);
        }
        
        .zoom-level-display {
            color: white;
            font-size: 0.7rem;
            font-weight: 500;
            min-width: 35px;
            text-align: center;
        }
        
        /* Help Button - Icon Only, Minimal */
        .help-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: all 0.2s;
            backdrop-filter: blur(10px);
            padding: 0;
        }
        
        .help-btn:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        
        /* Help Modal */
        .help-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 10002;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease;
        }
        
        .help-modal.active {
            display: flex;
        }
        
        .help-modal-content {
            background: white;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 12px 48px rgba(0,0,0,0.3);
            animation: slideUp 0.3s ease;
        }
        
        .help-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .help-modal-header h3 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
        }
        
        .help-modal-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 1.2rem;
        }
        
        .help-modal-close:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1);
        }
        
        .help-modal-body {
            padding: 25px;
        }
        
        .help-section {
            margin-bottom: 25px;
        }
        
        .help-section:last-child {
            margin-bottom: 0;
        }
        
        .help-section h4 {
            color: #667eea;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .help-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .help-section ul li {
            padding: 10px 0;
            padding-left: 25px;
            position: relative;
            color: #495057;
            line-height: 1.6;
        }
        
        .help-section ul li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .help-legend {
            margin-top: 15px;
        }
        
        .help-legend-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .help-legend-item:last-child {
            border-bottom: none;
        }
        
        .help-legend-color {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: 2px solid rgba(0,0,0,0.1);
            flex-shrink: 0;
        }
        
        @media (max-width: 768px) {
            .public-header {
                padding: 15px 20px;
            }
            
            .header-left {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .status-legend-inline {
                width: 100%;
            }
            
            .header-title-section h1 {
                font-size: 1.4rem;
            }
            
            .header-right {
                width: 100%;
                justify-content: space-between;
            }
            
            .zoom-controls-simple {
                flex: 1;
                justify-content: center;
            }
        }
        
        .canvas-container {
            width: 100%;
            height: calc(100vh - 48px);
            overflow: hidden;
            position: relative;
            background: #e9ecef;
        }
        
        /* Welcome Message */
        .welcome-message {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.95);
            padding: 15px 25px;
            border-radius: 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            z-index: 999;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            color: #495057;
            animation: slideDown 0.5s ease;
            max-width: 90%;
        }
        
        .welcome-message.hidden {
            display: none;
        }
        
        .welcome-close {
            background: transparent;
            border: none;
            color: #6c757d;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
        }
        
        .welcome-close:hover {
            background: #f0f0f0;
            color: #495057;
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
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), 
                        box-shadow 0.4s cubic-bezier(0.34, 1.56, 0.64, 1),
                        border-width 0.3s ease;
            user-select: none;
            pointer-events: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transform-origin: center center;
            overflow: visible;
            /* Rotation will be stored in CSS variable and preserved during shake */
            --booth-rotation: 0deg;
            --dock-scale: 1;
            --dock-lift: 0px;
        }
        
        /* Booked booth glow effect and icon */
        .dropped-booth.booked {
            box-shadow: 
                0 2px 8px rgba(0,0,0,0.15),
                0 0 12px rgba(40, 167, 69, 0.4),
                0 0 20px rgba(40, 167, 69, 0.2) !important;
            animation: bookedGlow 2s ease-in-out infinite;
        }
        
        .dropped-booth.booked::after {
            content: '\f00c';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            top: -6px;
            right: -6px;
            width: 18px;
            height: 18px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.5);
            z-index: 100;
            border: 2px solid white;
            animation: bookedIconPulse 2s ease-in-out infinite;
        }
        
        @keyframes bookedGlow {
            0%, 100% {
                box-shadow: 
                    0 2px 8px rgba(0,0,0,0.15),
                    0 0 12px rgba(40, 167, 69, 0.4),
                    0 0 20px rgba(40, 167, 69, 0.2);
            }
            50% {
                box-shadow: 
                    0 2px 8px rgba(0,0,0,0.15),
                    0 0 16px rgba(40, 167, 69, 0.6),
                    0 0 28px rgba(40, 167, 69, 0.3);
            }
        }
        
        @keyframes bookedIconPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 2px 8px rgba(40, 167, 69, 0.5);
            }
            50% {
                transform: scale(1.1);
                box-shadow: 0 3px 12px rgba(40, 167, 69, 0.7);
            }
        }
        
        .dropped-booth:hover {
            transform: scale(calc(1.5 * var(--dock-scale))) translateY(calc(-20px + var(--dock-lift))) rotate(var(--booth-rotation));
            box-shadow: 0 12px 40px rgba(0,0,0,0.5), 0 0 20px rgba(0,0,0,0.2);
            z-index: 1000 !important;
            border-width: 3px;
        }
        
        /* Ripple effect for nearby booths */
        .dropped-booth.dock-ripple-1 {
            transform: scale(1.15) rotate(var(--booth-rotation));
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        .dropped-booth.dock-ripple-2 {
            transform: scale(1.08) rotate(var(--booth-rotation));
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        .dropped-booth.dock-ripple-3 {
            transform: scale(1.04) rotate(var(--booth-rotation));
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        
        /* Mobile touch improvements */
        @media (hover: none) and (pointer: coarse) {
            .dropped-booth:active {
                transform: scale(1.05);
                box-shadow: 0 4px 16px rgba(0,0,0,0.3);
            }
        }
        
        /* Dynamic status colors - will be generated from database settings */
        /* Status colors only apply if booth doesn't have custom colors */
        @foreach($statusSettings ?? [] as $status)
        .dropped-booth.status-{{ $status->status_code }}:not(.has-custom-colors) {
            background: {{ $status->status_color }} !important;
            border-color: {{ $status->border_color ?? $status->status_color }} !important;
            border-width: {{ $status->border_width ?? 2 }}px !important;
            border-style: {{ $status->border_style ?? 'solid' }} !important;
            border-radius: {{ $status->border_radius ?? 4 }}px !important;
            color: {{ $status->text_color }} !important;
        }
        @endforeach
        
        /* Custom colors override - when booth has custom colors, they take priority */
        .dropped-booth.has-custom-colors {
            /* Custom colors are applied via inline styles with !important */
        }
        
        /* Fallback colors if no custom statuses - only apply if booth doesn't have custom colors */
        @if(empty($statusSettings))
        .dropped-booth.status-1:not(.has-custom-colors) {
            background: rgba(40, 167, 69, 0.9);
            border-color: #28a745;
            color: white;
        }
        
        .dropped-booth.status-2:not(.has-custom-colors) {
            background: rgba(13, 202, 240, 0.9);
            border-color: #0dcaf0;
            color: white;
        }
        
        .dropped-booth.status-3:not(.has-custom-colors) {
            background: rgba(255, 193, 7, 0.9);
            border-color: #ffc107;
            color: #333;
        }
        
        .dropped-booth.status-4:not(.has-custom-colors) {
            background: rgba(108, 117, 125, 0.7);
            border-color: #6c757d;
            color: white;
        }
        
        .dropped-booth.status-5:not(.has-custom-colors) {
            background: rgba(33, 37, 41, 0.9);
            border-color: #212529;
            color: white;
        }
        @endif
        
        .booth-tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.95);
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            font-size: 0.9rem;
            pointer-events: none;
            z-index: 10000;
            display: none;
            max-width: 380px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.6);
            line-height: 1.7;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .booth-tooltip img {
            width: 100%;
            height: auto;
            border-radius: 6px;
            margin-bottom: 8px;
        }
        
        .booth-tooltip .tooltip-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 12px;
            color: #fff;
            border-bottom: 2px solid rgba(255,255,255,0.25);
            padding-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .booth-tooltip .tooltip-row {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            gap: 12px;
        }
        
        .booth-tooltip .tooltip-label {
            color: rgba(255,255,255,0.7);
            font-weight: 500;
        }
        
        .booth-tooltip .tooltip-value {
            color: #fff;
            font-weight: 600;
            text-align: right;
        }
        
        .booth-tooltip .tooltip-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .booth-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 10001;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.2s ease;
        }
        
        .booth-modal.active {
            display: flex;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .booth-modal-content {
            background: white;
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 12px 48px rgba(0,0,0,0.3);
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .booth-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .booth-modal-header h3 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 700;
        }
        
        .booth-modal-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 1.3rem;
        }
        
        .booth-modal-close:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1);
        }
        
        .booth-modal-body {
            padding: 30px;
        }

        /* Public view: interactive booking-only popup (same flow as /books/create) */
        .public-booking-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 10002; justify-content: center; align-items: center; animation: fadeIn 0.2s ease; padding: 16px; box-sizing: border-box; }
        .public-booking-modal.active { display: flex; }
        .public-booking-modal .modal-content-inner { background: white; border-radius: 16px; width: 100%; max-width: 560px; max-height: 92vh; overflow: hidden; box-shadow: 0 12px 48px rgba(0,0,0,0.3); animation: slideUp 0.3s ease; display: flex; flex-direction: column; }
        .public-booking-modal .modal-header-inner { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 16px 20px; border-radius: 16px 16px 0 0; flex-shrink: 0; display: flex; justify-content: space-between; align-items: center; }
        .public-booking-modal .modal-header-inner h3 { margin: 0; font-size: 1.2rem; font-weight: 700; }
        .public-booking-modal .modal-body-inner { padding: 20px; overflow-y: auto; flex: 1; min-height: 0; }
        .public-booking-modal .btn-close-inner { background: rgba(255,255,255,0.2); border: none; color: white; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
        .public-booking-modal .btn-close-inner:hover { background: rgba(255,255,255,0.3); }
        .public-booking-modal .bf-section { margin-bottom: 20px; }
        .public-booking-modal .bf-section-title { font-size: 0.85rem; font-weight: 700; color: #667eea; margin-bottom: 10px; display: flex; align-items: center; gap: 6px; }
        .public-booking-modal .bf-form-row { display: flex; gap: 12px; flex-wrap: wrap; }
        .public-booking-modal .bf-form-row .bf-field { flex: 1; min-width: 140px; }
        .public-booking-modal .bf-field { margin-bottom: 12px; }
        .public-booking-modal .bf-field label { display: block; font-weight: 600; color: #495057; margin-bottom: 4px; font-size: 0.85rem; }
        .public-booking-modal .bf-field input, .public-booking-modal .bf-field select, .public-booking-modal .bf-field textarea { width: 100%; padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box; }
        .public-booking-modal .bf-field .req { color: #dc3545; }
        .public-booking-modal .bf-section-title-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 10px; flex-wrap: wrap; }
        .public-booking-modal .bf-section-title-row .bf-section-title { margin-bottom: 0; }
        .public-booking-modal .bf-booths-view-switcher { display: flex; gap: 4px; }
        .public-booking-modal .bf-view-btn { width: 32px; height: 32px; border: 1px solid #dee2e6; background: #fff; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; color: #6c757d; font-size: 0.85rem; transition: all 0.2s; }
        .public-booking-modal .bf-view-btn:hover { background: #f8f9fa; color: #667eea; border-color: #667eea; }
        .public-booking-modal .bf-view-btn.active { background: #667eea; color: #fff; border-color: #667eea; }
        .public-booking-modal .bf-booths { max-height: 220px; overflow-y: auto; border: 1px solid #e9ecef; border-radius: 8px; padding: 10px; background: #f8f9fa; }
        .public-booking-modal .bf-booth-item { display: flex; align-items: center; gap: 8px; padding: 6px 0; cursor: pointer; }
        .public-booking-modal .bf-booth-item input { width: auto; margin: 0; flex-shrink: 0; }
        .public-booking-modal .bf-booth-item label { margin: 0; font-weight: 500; cursor: pointer; flex: 1; }
        .public-booking-modal .bf-booth-item-info { font-size: 0.75rem; color: #6c757d; margin-top: 2px; }
        .public-booking-modal .bf-booths.view-list .bf-booth-item { flex-wrap: wrap; }
        .public-booking-modal .bf-booths.view-list .bf-booth-item .bf-booth-item-info { flex: 1 1 100%; padding-left: 28px; }
        .public-booking-modal .bf-booths.view-large { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; }
        .public-booking-modal .bf-booths.view-large .bf-booth-item { flex-direction: column; align-items: stretch; padding: 10px; background: #fff; border: 1px solid #e9ecef; border-radius: 8px; min-height: 70px; }
        .public-booking-modal .bf-booths.view-large .bf-booth-item label { display: flex; flex-direction: column; align-items: center; text-align: center; }
        .public-booking-modal .bf-booths.view-large .bf-booth-item .bf-booth-item-primary { font-weight: 700; font-size: 1rem; color: #212529; }
        .public-booking-modal .bf-booths.view-large .bf-booth-item .bf-booth-item-info { margin-top: 4px; font-size: 0.7rem; }
        .public-booking-modal .bf-booths.view-medium { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; }
        .public-booking-modal .bf-booths.view-medium .bf-booth-item { flex-direction: column; align-items: center; padding: 8px; background: #fff; border: 1px solid #e9ecef; border-radius: 6px; min-height: 56px; }
        .public-booking-modal .bf-booths.view-medium .bf-booth-item label { text-align: center; font-size: 0.8rem; }
        .public-booking-modal .bf-booths.view-medium .bf-booth-item .bf-booth-item-primary { font-weight: 600; }
        .public-booking-modal .bf-booths.view-medium .bf-booth-item .bf-booth-item-info { font-size: 0.65rem; margin-top: 2px; }
        .public-booking-modal .bf-booths.view-detail .bf-booth-item { flex-wrap: wrap; padding: 8px 10px; background: #fff; border: 1px solid #e9ecef; border-radius: 6px; margin-bottom: 6px; }
        .public-booking-modal .bf-booths.view-detail .bf-booth-item:last-child { margin-bottom: 0; }
        .public-booking-modal .bf-booths.view-detail .bf-booth-item label { flex: 1 1 100%; display: block; }
        .public-booking-modal .bf-booths.view-detail .bf-booth-item .bf-booth-item-primary { font-weight: 700; font-size: 0.9rem; }
        .public-booking-modal .bf-booths.view-detail .bf-booth-item .bf-booth-item-info { flex: 1 1 100%; padding-left: 28px; font-size: 0.75rem; line-height: 1.4; }
        /* Client search & selected (same style as /books/create) */
        .public-booking-modal .bf-client-search-wrap { position: relative; margin-bottom: 10px; }
        .public-booking-modal .bf-client-search-input { width: 100%; padding: 10px 12px 10px 36px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box; }
        .public-booking-modal .bf-client-search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none; }
        .public-booking-modal .bf-client-results { position: absolute; left: 0; right: 0; top: 100%; margin-top: 4px; background: #fff; border: 1px solid #dee2e6; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.12); max-height: 200px; overflow-y: auto; z-index: 100; }
        .public-booking-modal .bf-client-result-item { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
        .public-booking-modal .bf-client-result-item:last-child { border-bottom: none; }
        .public-booking-modal .bf-client-result-item:hover { background: #f8f9fa; }
        .public-booking-modal .bf-client-result-item .name { font-weight: 600; color: #212529; }
        .public-booking-modal .bf-client-result-item .meta { font-size: 0.8rem; color: #6c757d; }
        .public-booking-modal .bf-btn-new-client { margin-top: 8px; padding: 8px 14px; background: #e9ecef; border: 1px solid #dee2e6; border-radius: 8px; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
        .public-booking-modal .bf-btn-new-client:hover { background: #dee2e6; }
        .public-booking-modal .bf-client-selected { display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px; margin-bottom: 10px; }
        .public-booking-modal .bf-client-selected-info { display: flex; align-items: center; gap: 12px; }
        .public-booking-modal .bf-client-avatar { width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; flex-shrink: 0; }
        .public-booking-modal .bf-client-name { font-weight: 600; color: #212529; }
        .public-booking-modal .bf-client-meta { font-size: 0.8rem; color: #6c757d; }
        .public-booking-modal .bf-btn-change { padding: 6px 12px; font-size: 0.8rem; background: #fff; border: 1px solid #dee2e6; border-radius: 6px; cursor: pointer; }
        .public-booking-modal .bf-btn-change:hover { background: #f8f9fa; }
        .public-booking-modal .bf-new-client-form { margin-top: 14px; padding-top: 14px; border-top: 1px solid #e9ecef; }
        .public-booking-modal .bf-new-client-form-full .bf-new-client-form-scroll { max-height: 320px; overflow-y: auto; padding-right: 6px; margin-bottom: 14px; }
        .public-booking-modal .bf-form-subsection { margin-bottom: 16px; }
        .public-booking-modal .bf-form-subsection:last-child { margin-bottom: 0; }
        .public-booking-modal .bf-form-subtitle { font-size: 0.8rem; font-weight: 700; color: #6c757d; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
        .public-booking-modal .bf-new-client-actions { margin-top: 12px; padding-top: 12px; border-top: 1px solid #e9ecef; }
        .public-booking-modal .bf-alert { padding: 10px 12px; border-radius: 8px; margin-bottom: 12px; font-size: 0.85rem; display: none; }
        .public-booking-modal .bf-alert.show { display: block; }
        .public-booking-modal .bf-alert-error { background: #f8d7da; color: #721c24; }
        .public-booking-modal .bf-footer { padding: 16px 20px; border-top: 1px solid #e9ecef; background: #f8f9fa; border-radius: 0 0 16px 16px; display: flex; justify-content: flex-end; gap: 10px; flex-shrink: 0; }
        .public-booking-modal .bf-btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .public-booking-modal .bf-btn-secondary { background: #fff; color: #495057; border: 1px solid #dee2e6; }
        .public-booking-modal .bf-btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .public-booking-modal .bf-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        
        .booth-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 14px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .booth-detail-row:last-child {
            border-bottom: none;
        }
        
        .booth-detail-label {
            color: #6c757d;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .booth-detail-value {
            color: #212529;
            font-weight: 500;
            text-align: right;
            max-width: 60%;
            word-wrap: break-word;
            font-size: 0.95rem;
        }
        
        .booth-detail-value.status-badge {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 25px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
            font-size: 0.9rem;
            font-weight: 600;
            color: white;
        }
        
        .status-1 { background: #28a745; }
        .status-2 { background: #0dcaf0; }
        .status-3 { background: #ffc107; color: #333 !important; }
        .status-4 { background: #6c757d; }
        .status-5 { background: #212529; }
        
        .booth-detail-section {
            margin-bottom: 25px;
        }
        
        .booth-detail-section:last-child {
            margin-bottom: 0;
        }
        
        .booth-detail-section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .booth-detail-section-content {
            color: #495057;
            line-height: 1.7;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <!-- Public Header - Minimal Design -->
    <div class="public-header">
        <div class="header-left">
            <div class="header-title-section">
                <h1><i class="fas fa-map"></i>{{ $floorPlan->name }}</h1>
                @if($floorPlan->project_name)
                    <span class="project-name">• {{ $floorPlan->project_name }}</span>
                @endif
            </div>
            <div class="header-stats">
                <div class="stat-badge">
                    <i class="fas fa-store"></i>
                    <span><strong>{{ $booths->count() }}</strong></span>
                </div>
            </div>
            
            <!-- Status Legend - Compact -->
            <div class="status-legend-inline">
                <div class="legend-label">
                    <i class="fas fa-info-circle"></i>
                    <span>Status:</span>
                </div>
                <div class="legend-items-horizontal">
                    @forelse($statusSettings ?? [] as $status)
                        <div class="legend-item-horizontal">
                            <span class="legend-color" style="background: {{ $status->status_color }};"></span>
                            <span class="legend-text">{{ $status->status_name }}</span>
                        </div>
                    @empty
                        {{-- Fallback to defaults if no custom statuses --}}
                        <div class="legend-item-horizontal">
                            <span class="legend-color" style="background: #28a745;"></span>
                            <span class="legend-text">Available</span>
                        </div>
                        <div class="legend-item-horizontal">
                            <span class="legend-color" style="background: #0dcaf0;"></span>
                            <span class="legend-text">Confirmed</span>
                        </div>
                        <div class="legend-item-horizontal">
                            <span class="legend-color" style="background: #ffc107;"></span>
                            <span class="legend-text">Reserved</span>
                        </div>
                        <div class="legend-item-horizontal">
                            <span class="legend-color" style="background: #6c757d;"></span>
                            <span class="legend-text">Hidden</span>
                        </div>
                        <div class="legend-item-horizontal">
                            <span class="legend-color" style="background: #212529;"></span>
                            <span class="legend-text">Paid</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="header-right">
            @if($canSwitchToCanvasDesign ?? false)
                <a href="{{ route('booths.index', ['view' => 'canvas', 'floor_plan_id' => $floorPlan->id]) }}" class="header-action-link" title="Switch back to Canvas Design">
                    <i class="fas fa-pencil-ruler"></i><span class="header-action-text">Canvas Design</span>
                </a>
            @endif
            @if($authUser ?? null)
                <a href="{{ route('books.index') }}" class="header-action-link" title="My Bookings">
                    <i class="fas fa-calendar-check"></i><span class="header-action-text">My Bookings</span>
                </a>
                @if($canCreateBookingOnPublicView ?? false)
                    <span class="header-action-hint" title="You can create a booking from this page">Create booking</span>
                @endif
            @else
                <a href="{{ url('/login?redirect=' . urlencode(request()->fullUrl())) }}" class="header-action-link" title="Login to create bookings">
                    <i class="fas fa-sign-in-alt"></i><span class="header-action-text">Login</span>
                </a>
            @endif
            <!-- Simple Zoom Controls -->
            <div class="zoom-controls-simple">
                <div class="zoom-label">Zoom:</div>
                <button class="zoom-btn-simple" id="zoomOut" title="Zoom Out">
                    <i class="fas fa-minus"></i>
                </button>
                <span class="zoom-level-display" id="zoomLevel">100%</span>
                <button class="zoom-btn-simple" id="zoomIn" title="Zoom In">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="zoom-btn-simple" id="zoomFit" title="Fit to Screen">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
            
            <!-- Help Button - Icon Only -->
            <button class="help-btn" onclick="showHelp()" title="How to use this map">
                <i class="fas fa-question-circle"></i>
            </button>
        </div>
    </div>
    
    <!-- Help Modal -->
    <div class="help-modal" id="helpModal" onclick="closeHelp(event)">
        <div class="help-modal-content" onclick="event.stopPropagation()">
            <div class="help-modal-header">
                <h3><i class="fas fa-question-circle mr-2"></i>How to Use This Floor Plan</h3>
                <button class="help-modal-close" onclick="closeHelp()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="help-modal-body">
                <div class="help-section">
                    <h4><i class="fas fa-mouse-pointer text-primary"></i> Viewing Booths</h4>
                    <ul>
                        <li><strong>Hover</strong> over any booth to see quick information</li>
                        <li><strong>Click</strong> on a booth to see full details</li>
                        <li>Each booth is color-coded by its status (see legend above)</li>
                    </ul>
                </div>
                <div class="help-section">
                    <h4><i class="fas fa-hand-paper text-primary"></i> Moving Around</h4>
                    <ul>
                        <li><strong>Click and drag</strong> the map to move around</li>
                        <li>Use your <strong>mouse wheel</strong> to zoom in and out</li>
                        <li>On mobile, <strong>pinch to zoom</strong> and <strong>drag</strong> to move</li>
                    </ul>
                </div>
                <div class="help-section">
                    <h4><i class="fas fa-search-plus text-primary"></i> Zoom Controls</h4>
                    <ul>
                        <li><strong>+</strong> button: Zoom in for closer view</li>
                        <li><strong>-</strong> button: Zoom out to see more</li>
                        <li><strong>Fit</strong> button: Automatically fit the entire map to your screen</li>
                    </ul>
                </div>
                <div class="help-section">
                    <h4><i class="fas fa-palette text-primary"></i> Understanding Colors</h4>
                    <p>Each booth has a color that shows its availability:</p>
                    <div class="help-legend">
                        <div class="help-legend-item">
                            <span class="help-legend-color" style="background: #28a745;"></span>
                            <span>Green = Available for booking</span>
                        </div>
                        <div class="help-legend-item">
                            <span class="help-legend-color" style="background: #0dcaf0;"></span>
                            <span>Blue = Confirmed booking</span>
                        </div>
                        <div class="help-legend-item">
                            <span class="help-legend-color" style="background: #ffc107;"></span>
                            <span>Yellow = Reserved</span>
                        </div>
                        <div class="help-legend-item">
                            <span class="help-legend-color" style="background: #6c757d;"></span>
                            <span>Gray = Hidden</span>
                        </div>
                        <div class="help-legend-item">
                            <span class="help-legend-color" style="background: #212529;"></span>
                            <span>Black = Paid</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Welcome Message -->
    <div class="welcome-message" id="welcomeMessage">
        <i class="fas fa-info-circle text-primary"></i>
        <span><strong>Tip:</strong> Hover over booths to see details, or click for full information. Drag the map to explore!</span>
        <button class="welcome-close" onclick="closeWelcome()" title="Close">
            <i class="fas fa-times"></i>
        </button>
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
    
    <!-- Booth Detail Modal -->
    <div class="booth-modal" id="boothModal">
        <div class="booth-modal-content">
            <div class="booth-modal-header">
                <h3 id="modalBoothNumber">Booth Details</h3>
                <button class="booth-modal-close" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="booth-modal-body" id="modalBody">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Right-click context menu (Create Booking for logged-in user) -->
    <ul class="public-view-context-menu" id="publicViewContextMenu" role="menu"></ul>

    <!-- Full booking form popup (same form as /books/create, POST to /books) -->
    <div class="public-booking-modal" id="publicBookingModal">
        <div class="modal-content-inner">
            <div class="modal-header-inner">
                <h3 id="publicBookingModalTitle"><i class="fas fa-calendar-plus"></i> Create Booking</h3>
                <button type="button" class="btn-close-inner" id="publicBookingModalClose" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('books.store') }}" method="POST" id="bookingForm" class="public-booking-form">
                @csrf
                <input type="hidden" name="clientid" id="publicBookingClientId" required>
                <div class="modal-body-inner">
                    <div id="publicBookingError" class="bf-alert bf-alert-error" role="alert"></div>

                    <div class="bf-section" id="publicBookingClientBlock">
                        <div class="bf-section-title"><i class="fas fa-user"></i> Client <span class="req">*</span></div>
                        <div id="publicSelectedClientUI" style="display: none;">
                            <div class="bf-client-selected">
                                <div class="bf-client-selected-info">
                                    <span class="bf-client-avatar" id="publicUiClientInitial">—</span>
                                    <div>
                                        <div class="bf-client-name" id="publicUiClientName"></div>
                                        <div class="bf-client-meta" id="publicUiClientDetails"></div>
                                    </div>
                                </div>
                                <button type="button" class="bf-btn-change" id="publicBtnChangeClient">Change</button>
                            </div>
                        </div>
                        <div id="publicSearchClientUI">
                            <div class="bf-client-search-wrap">
                                <i class="fas fa-search bf-client-search-icon"></i>
                                <input type="text" id="publicClientSearch" class="bf-client-search-input" placeholder="Search by name or company..." autocomplete="off">
                                <div id="publicInlineClientResults" class="bf-client-results" style="display: none;">
                                    <div id="publicInlineClientResultsList"></div>
                                </div>
                            </div>
                            <button type="button" class="bf-btn-new-client" id="publicBtnNewClient"><i class="fas fa-user-plus"></i> New client</button>
                        </div>
                        <div id="publicNewClientForm" class="bf-new-client-form bf-new-client-form-full" style="display: none;">
                            <div class="bf-section-title"><i class="fas fa-user-plus"></i> Create new client (full form)</div>
                            <div class="bf-new-client-form-scroll">
                                <div class="bf-form-subsection">
                                    <div class="bf-form-subtitle"><i class="fas fa-user"></i> Basic</div>
                                    <div class="bf-form-row">
                                        <div class="bf-field"><label>Full Name</label><input type="text" name="name" id="newClientName" placeholder="Full name"></div>
                                        <div class="bf-field"><label>Gender</label><select name="sex" id="newClientSex"><option value="">—</option><option value="1">Male</option><option value="2">Female</option><option value="3">Other</option></select></div>
                                    </div>
                                </div>
                                <div class="bf-form-subsection">
                                    <div class="bf-form-subtitle"><i class="fas fa-building"></i> Company</div>
                                    <div class="bf-form-row">
                                        <div class="bf-field"><label>Company Name</label><input type="text" name="company" id="newClientCompany" placeholder="Company name"></div>
                                        <div class="bf-field"><label>Company Name (Khmer)</label><input type="text" name="company_name_khmer" id="newClientCompanyKhmer" placeholder="Khmer"></div>
                                    </div>
                                    <div class="bf-field"><label>Position/Title</label><input type="text" name="position" id="newClientPosition" placeholder="Position or title"></div>
                                </div>
                                <div class="bf-form-subsection">
                                    <div class="bf-form-subtitle"><i class="fas fa-phone"></i> Contact</div>
                                    <div class="bf-form-row">
                                        <div class="bf-field"><label>Phone Number</label><input type="text" name="phone_number" id="newClientPhone" placeholder="Phone"></div>
                                        <div class="bf-field"><label>Phone 1</label><input type="text" name="phone_1" id="newClientPhone1" placeholder="Primary phone"></div>
                                    </div>
                                    <div class="bf-form-row">
                                        <div class="bf-field"><label>Phone 2</label><input type="text" name="phone_2" id="newClientPhone2" placeholder="Secondary phone"></div>
                                        <div class="bf-field"><label>Email</label><input type="email" name="email" id="newClientEmail" placeholder="Email"></div>
                                    </div>
                                    <div class="bf-form-row">
                                        <div class="bf-field"><label>Email 1</label><input type="email" name="email_1" id="newClientEmail1" placeholder="Primary email"></div>
                                        <div class="bf-field"><label>Email 2</label><input type="email" name="email_2" id="newClientEmail2" placeholder="Secondary email"></div>
                                    </div>
                                    <div class="bf-field"><label>Address</label><textarea name="address" id="newClientAddress" rows="2" placeholder="Street, city, country"></textarea></div>
                                </div>
                                <div class="bf-form-subsection">
                                    <div class="bf-form-subtitle"><i class="fas fa-info-circle"></i> Additional</div>
                                    <div class="bf-form-row">
                                        <div class="bf-field"><label>Tax ID</label><input type="text" name="tax_id" id="newClientTaxId" placeholder="Tax ID or business registration"></div>
                                        <div class="bf-field"><label>Website</label><input type="url" name="website" id="newClientWebsite" placeholder="https://..."></div>
                                    </div>
                                    <div class="bf-field"><label>Notes</label><textarea name="notes" id="newClientNotes" rows="2" placeholder="Additional notes"></textarea></div>
                                </div>
                            </div>
                            <div class="bf-form-row bf-new-client-actions">
                                <button type="button" class="bf-btn bf-btn-secondary" id="publicNewClientCancel">Cancel</button>
                                <button type="button" class="bf-btn bf-btn-primary" id="publicNewClientSave"><i class="fas fa-save"></i> Save &amp; Select</button>
                            </div>
                        </div>
                    </div>

                    <div class="bf-section">
                        <div class="bf-section-title-row">
                            <div class="bf-section-title"><i class="fas fa-th-large"></i> Booths <span class="req">*</span></div>
                            <div class="bf-booths-view-switcher" role="group" aria-label="Booth list view style">
                                <button type="button" class="bf-view-btn active" data-view="list" title="List View"><i class="fas fa-list"></i></button>
                                <button type="button" class="bf-view-btn" data-view="large" title="Large Icon View"><i class="fas fa-th-large"></i></button>
                                <button type="button" class="bf-view-btn" data-view="medium" title="Medium Icon View"><i class="fas fa-th"></i></button>
                                <button type="button" class="bf-view-btn" data-view="detail" title="Detail View"><i class="fas fa-align-left"></i></button>
                            </div>
                        </div>
                        <div id="publicBookingBoothList" class="bf-booths view-list"></div>
                    </div>

                    <div class="bf-section">
                        <div class="bf-section-title"><i class="fas fa-tag"></i> Details</div>
                        <div class="bf-form-row">
                            <div class="bf-field"><label>Type</label><select name="type" id="publicBookingType"><option value="1">Regular (Reserved)</option><option value="2">Special (Confirmed)</option><option value="3">Temporary (Short-term)</option></select></div>
                            <div class="bf-field"><label>Date &amp; time</label><input type="datetime-local" name="date_book" id="publicBookingDateBook" value="{{ now()->format('Y-m-d\TH:i') }}"></div>
                        </div>
                        <div class="bf-field"><label>Notes</label><textarea name="notes" id="publicBookingNotes" rows="2" placeholder="Optional notes..."></textarea></div>
                    </div>
                </div>
                <div class="bf-footer">
                    <button type="button" class="bf-btn bf-btn-secondary" id="publicBookingCancel">Cancel</button>
                    <button type="submit" class="bf-btn bf-btn-primary" id="publicBookingSubmit"><i class="fas fa-save"></i> Create booking</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Function to calculate contrasting text color (black or white) based on background
        // This function is used globally for status badges in tooltips and modals
        function getContrastColor(hexColor) {
            // Remove # if present
            hexColor = hexColor.replace('#', '');
            
            // Convert to RGB
            const r = parseInt(hexColor.substr(0, 2), 16);
            const g = parseInt(hexColor.substr(2, 2), 16);
            const b = parseInt(hexColor.substr(4, 2), 16);
            
            // Calculate luminance (relative brightness)
            const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
            
            // Return black for light colors, white for dark colors
            return luminance > 0.5 ? '#000000' : '#ffffff';
        }

        // Public view actions (from server: allow logged-in user to create booking)
        window.publicViewCanCreateBooking = @json($canCreateBookingOnPublicView ?? false);
        window.publicViewFloorPlanId = @json($floorPlan->id ?? null);
        window.publicViewBooksCreateUrl = @json(route('books.create'));
        window.publicViewBooksStoreUrl = @json(route('books.store'));
        window.publicViewClientsSearchUrl = @json(route('clients.search'));
        window.publicViewClientsStoreUrl = @json(route('clients.store'));

        // Right-click context menu (Create Booking)
        const publicViewContextMenu = document.getElementById('publicViewContextMenu');
        function showPublicViewContextMenu(x, y, items) {
            if (!items || items.length === 0) return;
            publicViewContextMenu.innerHTML = '';
            items.forEach(function(item) {
                const li = document.createElement('li');
                li.setAttribute('role', 'menuitem');
                if (item.url) {
                    const a = document.createElement('a');
                    a.href = item.url;
                    a.innerHTML = (item.icon ? '<i class="fas ' + item.icon + '"></i>' : '') + '<span>' + item.label + '</span>';
                    a.addEventListener('click', function(e) { e.preventDefault(); window.location.href = item.url; });
                    li.appendChild(a);
                } else if (item.action) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.innerHTML = (item.icon ? '<i class="fas ' + item.icon + '"></i>' : '') + '<span>' + item.label + '</span>';
                    btn.addEventListener('click', function() { hidePublicViewContextMenu(); item.action(); });
                    li.appendChild(btn);
                }
                publicViewContextMenu.appendChild(li);
            });
            publicViewContextMenu.style.left = x + 'px';
            publicViewContextMenu.style.top = y + 'px';
            publicViewContextMenu.classList.add('active');
            const rect = publicViewContextMenu.getBoundingClientRect();
            if (rect.right > window.innerWidth) publicViewContextMenu.style.left = (window.innerWidth - rect.width - 8) + 'px';
            if (rect.bottom > window.innerHeight) publicViewContextMenu.style.top = (window.innerHeight - rect.height - 8) + 'px';
        }
        function hidePublicViewContextMenu() {
            publicViewContextMenu.classList.remove('active');
        }
        document.addEventListener('click', function() { hidePublicViewContextMenu(); });
        document.addEventListener('contextmenu', function(e) {
            if (!publicViewContextMenu.contains(e.target)) hidePublicViewContextMenu();
        });

        // Full booking form popup (same as /books/create: POST to /books, client + booths + details)
        const publicBookingModalEl = document.getElementById('publicBookingModal');
        const publicBookingModalTitle = document.getElementById('publicBookingModalTitle');
        const publicBookingFormEl = document.getElementById('bookingForm');
        const publicBookingBoothList = document.getElementById('publicBookingBoothList');
        const publicBookingErrorEl = document.getElementById('publicBookingError');
        const publicBookingSubmitBtn = document.getElementById('publicBookingSubmit');
        const publicBookingClientIdEl = document.getElementById('publicBookingClientId');
        const publicSelectedClientUI = document.getElementById('publicSelectedClientUI');
        const publicSearchClientUI = document.getElementById('publicSearchClientUI');
        const publicNewClientForm = document.getElementById('publicNewClientForm');

        var publicBookingBoothView = 'list';
        var publicBookingBoothAvailable = [];
        var publicBookingBoothPreselectedId = null;

        function publicBoothInfoLine(b, view) {
            var parts = [];
            if (view === 'list') {
                if (b.category) parts.push(b.category);
                if (b.area_sqm != null && b.area_sqm !== '') parts.push(b.area_sqm + ' m²');
                return parts.length ? parts.join(' · ') : 'Available';
            }
            if (b.price != null && b.price !== '') parts.push('$' + parseFloat(b.price).toFixed(2));
            if (b.category) parts.push(b.category);
            if (b.area_sqm != null && b.area_sqm !== '') parts.push(b.area_sqm + ' m²');
            if (view === 'detail' && b.booth_type) parts.push(b.booth_type);
            return parts.length ? parts.join(' · ') : 'Available';
        }

        function renderPublicBookingBoothList() {
            publicBookingBoothList.innerHTML = '';
            publicBookingBoothList.className = 'bf-booths view-' + publicBookingBoothView;
            if (!publicBookingBoothAvailable.length) {
                publicBookingBoothList.innerHTML = '<p class="text-muted small mb-0">No available booths.</p>';
                return;
            }
            publicBookingBoothAvailable.forEach(function(b) {
                var id = 'pb_booth_' + b.id;
                var checked = (publicBookingBoothPreselectedId && b.id == publicBookingBoothPreselectedId) ? ' checked' : '';
                var num = (b.booth_number || b.id);
                var info = publicBoothInfoLine(b, publicBookingBoothView);
                var div = document.createElement('div');
                div.className = 'bf-booth-item';
                var labelHtml = '';
                if (publicBookingBoothView === 'list') {
                    labelHtml = 'Booth ' + num + (b.price != null && b.price !== '' ? ' — $' + parseFloat(b.price).toFixed(2) : '') + '<span class="bf-booth-item-info">' + info.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>';
                } else if (publicBookingBoothView === 'large' || publicBookingBoothView === 'medium') {
                    labelHtml = '<span class="bf-booth-item-primary">Booth ' + num + '</span><span class="bf-booth-item-info">' + info.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>';
                } else {
                    labelHtml = '<span class="bf-booth-item-primary">Booth ' + num + '</span><span class="bf-booth-item-info">' + info.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>';
                }
                div.innerHTML = '<input type="checkbox" name="booth_ids[]" value="' + b.id + '" id="' + id + '"' + checked + '><label for="' + id + '">' + labelHtml + '</label>';
                publicBookingBoothList.appendChild(div);
            });
        }

        function openPublicBookingModal(boothId, boothNumber) {
            if (!publicBookingFormEl) return;
            publicBookingFormEl.reset();
            publicBookingClientIdEl.value = '';
            publicSelectedClientUI.style.display = 'none';
            publicSearchClientUI.style.display = 'block';
            publicNewClientForm.style.display = 'none';
            document.getElementById('publicClientSearch').value = '';
            document.getElementById('publicInlineClientResults').style.display = 'none';
            document.getElementById('publicInlineClientResultsList').innerHTML = '';
            if (document.getElementById('publicBookingDateBook')) document.getElementById('publicBookingDateBook').value = '{{ now()->format('Y-m-d\TH:i') }}';
            publicBookingErrorEl.classList.remove('show');
            publicBookingErrorEl.textContent = '';
            publicBookingModalTitle.innerHTML = (boothId && boothNumber)
                ? '<i class="fas fa-calendar-plus"></i> Create Booking for Booth ' + (boothNumber || boothId)
                : '<i class="fas fa-calendar-plus"></i> Create Booking';
            publicBookingBoothAvailable = [];
            publicBookingBoothPreselectedId = boothId || null;
            if (typeof booths !== 'undefined' && booths.length) {
                publicBookingBoothAvailable = booths.filter(function(b) {
                    return b.status == 1 && b.position_x != null && b.position_y != null;
                });
            }
            document.querySelectorAll('.bf-booths-view-switcher .bf-view-btn').forEach(function(btn) {
                btn.classList.toggle('active', btn.getAttribute('data-view') === publicBookingBoothView);
            });
            renderPublicBookingBoothList();
            publicBookingModalEl.classList.add('active');
        }

        document.querySelectorAll('.bf-booths-view-switcher .bf-view-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var view = this.getAttribute('data-view');
                if (!view) return;
                publicBookingBoothView = view;
                document.querySelectorAll('.bf-booths-view-switcher .bf-view-btn').forEach(function(b) { b.classList.remove('active'); });
                this.classList.add('active');
                renderPublicBookingBoothList();
            });
        });

        function closePublicBookingModal() {
            publicBookingModalEl.classList.remove('active');
        }

        function selectClientPublic(c) {
            publicBookingClientIdEl.value = c.id;
            document.getElementById('publicUiClientName').textContent = c.company || c.name || '—';
            document.getElementById('publicUiClientDetails').textContent = [c.email, c.phone_number].filter(Boolean).join(' · ') || '—';
            var initial = (c.company || c.name || '—').toString().charAt(0).toUpperCase();
            document.getElementById('publicUiClientInitial').textContent = initial === ' ' ? '—' : initial;
            publicSearchClientUI.style.display = 'none';
            publicSelectedClientUI.style.display = 'block';
            publicNewClientForm.style.display = 'none';
            document.getElementById('publicInlineClientResults').style.display = 'none';
        }

        document.getElementById('publicBookingModalClose').addEventListener('click', function() { closePublicBookingModal(); });
        document.getElementById('publicBookingCancel').addEventListener('click', function() { closePublicBookingModal(); });
        publicBookingModalEl.addEventListener('click', function(e) {
            if (e.target === publicBookingModalEl) closePublicBookingModal();
        });

        document.getElementById('publicBtnChangeClient').addEventListener('click', function() {
            publicBookingClientIdEl.value = '';
            publicSelectedClientUI.style.display = 'none';
            publicSearchClientUI.style.display = 'block';
        });

        var publicClientSearchTimer;
        document.getElementById('publicClientSearch').addEventListener('input', function() {
            var q = this.value.trim();
            clearTimeout(publicClientSearchTimer);
            var resultsEl = document.getElementById('publicInlineClientResults');
            var listEl = document.getElementById('publicInlineClientResultsList');
            if (q.length < 2) { resultsEl.style.display = 'none'; return; }
            publicClientSearchTimer = setTimeout(function() {
                fetch(window.publicViewClientsSearchUrl + '?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        listEl.innerHTML = '';
                        if (res && res.length > 0) {
                            res.forEach(function(c) {
                                var item = document.createElement('div');
                                item.className = 'bf-client-result-item';
                                item.innerHTML = '<div class="name">' + (c.company || c.name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div><div class="meta">' + (c.phone_number || c.email || '').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>';
                                item.addEventListener('click', function() { selectClientPublic(c); });
                                listEl.appendChild(item);
                            });
                        } else {
                            listEl.innerHTML = '<div class="bf-client-result-item"><div class="meta text-center">No clients found</div></div>';
                        }
                        resultsEl.style.display = 'block';
                    })
                    .catch(function() { listEl.innerHTML = '<div class="bf-client-result-item"><div class="meta text-center">Search failed</div></div>'; resultsEl.style.display = 'block'; });
            }, 300);
        });
        document.addEventListener('click', function(e) {
            if (!publicBookingModalEl.contains(e.target) || (!e.target.closest('#publicClientSearch') && !e.target.closest('#publicInlineClientResults')))
                document.getElementById('publicInlineClientResults').style.display = 'none';
        });

        function clearPublicNewClientForm() {
            var ids = ['newClientName', 'newClientSex', 'newClientCompany', 'newClientCompanyKhmer', 'newClientPosition', 'newClientPhone', 'newClientPhone1', 'newClientPhone2', 'newClientEmail', 'newClientEmail1', 'newClientEmail2', 'newClientAddress', 'newClientTaxId', 'newClientWebsite', 'newClientNotes'];
            ids.forEach(function(id) {
                var el = document.getElementById(id);
                if (el) el.value = '';
            });
        }
        document.getElementById('publicBtnNewClient').addEventListener('click', function() {
            publicSearchClientUI.style.display = 'none';
            publicNewClientForm.style.display = 'block';
            clearPublicNewClientForm();
        });
        document.getElementById('publicNewClientCancel').addEventListener('click', function() {
            publicNewClientForm.style.display = 'none';
            publicSearchClientUI.style.display = 'block';
        });
        document.getElementById('publicNewClientSave').addEventListener('click', function() {
            var name = document.getElementById('newClientName') ? document.getElementById('newClientName').value.trim() : '';
            var company = document.getElementById('newClientCompany') ? document.getElementById('newClientCompany').value.trim() : '';
            var phone = document.getElementById('newClientPhone') ? document.getElementById('newClientPhone').value.trim() : '';
            var phone1 = document.getElementById('newClientPhone1') ? document.getElementById('newClientPhone1').value.trim() : '';
            if (!name && !company) {
                publicBookingErrorEl.textContent = 'Please enter at least Name or Company for the new client.';
                publicBookingErrorEl.classList.add('show');
                return;
            }
            if (!phone && !phone1) {
                publicBookingErrorEl.textContent = 'Please enter at least one phone number (Phone or Phone 1).';
                publicBookingErrorEl.classList.add('show');
                return;
            }
            var btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            var fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '');
            var fields = [
                { id: 'newClientName', key: 'name' }, { id: 'newClientSex', key: 'sex' }, { id: 'newClientCompany', key: 'company' },
                { id: 'newClientCompanyKhmer', key: 'company_name_khmer' }, { id: 'newClientPosition', key: 'position' },
                { id: 'newClientPhone', key: 'phone_number' }, { id: 'newClientPhone1', key: 'phone_1' }, { id: 'newClientPhone2', key: 'phone_2' },
                { id: 'newClientEmail', key: 'email' }, { id: 'newClientEmail1', key: 'email_1' }, { id: 'newClientEmail2', key: 'email_2' },
                { id: 'newClientAddress', key: 'address' }, { id: 'newClientTaxId', key: 'tax_id' }, { id: 'newClientWebsite', key: 'website' }, { id: 'newClientNotes', key: 'notes' }
            ];
            fields.forEach(function(f) {
                var el = document.getElementById(f.id);
                if (el) fd.append(f.key, el.value || '');
            });
            fetch(window.publicViewClientsStoreUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.status === 'success' && data.client) { selectClientPublic(data.client); publicBookingErrorEl.classList.remove('show'); publicNewClientForm.style.display = 'none'; publicSearchClientUI.style.display = 'block'; clearPublicNewClientForm(); }
                    else { publicBookingErrorEl.textContent = (data.message || 'Failed to create client.') + (data.errors ? ' ' + Object.values(data.errors).flat().join(' ') : ''); publicBookingErrorEl.classList.add('show'); }
                })
                .catch(function() { publicBookingErrorEl.textContent = 'Failed to create client.'; publicBookingErrorEl.classList.add('show'); })
                .finally(function() { btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Save & Select'; });
        });

        publicBookingFormEl.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!publicBookingClientIdEl.value) {
                publicBookingErrorEl.textContent = 'Please select or add a client.';
                publicBookingErrorEl.classList.add('show');
                return;
            }
            var checked = publicBookingFormEl.querySelectorAll('input[name="booth_ids[]"]:checked');
            if (!checked.length) {
                publicBookingErrorEl.textContent = 'Please select at least one booth.';
                publicBookingErrorEl.classList.add('show');
                return;
            }
            publicBookingErrorEl.classList.remove('show');
            var formData = new FormData(publicBookingFormEl);
            publicBookingSubmitBtn.disabled = true;
            publicBookingSubmitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
            fetch(window.publicViewBooksStoreUrl, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json().catch(function() { return {}; }); })
            .then(function(data) {
                if (data.success === true) {
                    closePublicBookingModal();
                    alert(data.message || 'Booking created successfully.');
                    window.location.reload();
                } else {
                    publicBookingErrorEl.textContent = (data.message || data.errors ? (typeof data.errors === 'object' ? Object.values(data.errors).flat().join(' ') : '') : 'Failed to create booking.') || 'Failed to create booking.';
                    publicBookingErrorEl.classList.add('show');
                }
            })
            .catch(function(err) {
                publicBookingErrorEl.textContent = 'Error: ' + (err.message || 'Failed to create booking.');
                publicBookingErrorEl.classList.add('show');
            })
            .finally(function() {
                publicBookingSubmitBtn.disabled = false;
                publicBookingSubmitBtn.innerHTML = '<i class="fas fa-save"></i> Create booking';
            });
        });
        
        let panzoomInstance;
        let zoomLevel = 1;
        const canvas = document.getElementById('print');
        const container = document.getElementById('printContainer');
        
        // Initialize Panzoom with touch support
        if (canvas && typeof Panzoom !== 'undefined') {
            panzoomInstance = Panzoom(canvas, {
                maxScale: 5,
                minScale: 0.01,
                contain: 'outside',
                disablePan: false,
                disableZoom: false,
                // Enable touch gestures for mobile
                touchAction: 'none',
            });
            
            // Enable pinch zoom on mobile
            container.addEventListener('wheel', panzoomInstance.zoomWithWheel);
            
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
                // Check if booth is booked (has client_id or status != 1)
                const isBooked = (booth.client_id && booth.client_id !== null) || (booth.status != 1);
                boothElement.className = 'dropped-booth status-' + booth.status + (isBooked ? ' booked' : '');
                boothElement.setAttribute('data-booth-id', booth.id);
                boothElement.setAttribute('data-booth-number', booth.booth_number);
                boothElement.textContent = booth.booth_number;
                
                // Set position
                boothElement.style.left = booth.position_x + 'px';
                boothElement.style.top = booth.position_y + 'px';
                
                // Set dimensions
                if (booth.width) boothElement.style.width = booth.width + 'px';
                if (booth.height) boothElement.style.height = booth.height + 'px';
                
                // Set rotation - store in CSS variable and apply to transform
                const rotation = booth.rotation || 0;
                boothElement.style.setProperty('--booth-rotation', rotation + 'deg');
                boothElement.style.transform = 'rotate(' + rotation + 'deg)';
                
                // Apply subtle shake animation that preserves rotation
                applyShakeAnimation(boothElement, rotation);
                
                // Set z-index
                if (booth.z_index) boothElement.style.zIndex = booth.z_index;
                
                // Set appearance - custom colors override status colors
                // IMPORTANT: Custom colors ONLY apply when status is "Available" (status code 1)
                const boothStatus = booth.status || '1';
                const isAvailable = (boothStatus === '1' || boothStatus === 1);
                const hasCustomColors = isAvailable && (booth.background_color || booth.border_color || booth.text_color);
                
                if (hasCustomColors) {
                    boothElement.classList.add('has-custom-colors');
                }
                
                // Apply custom colors with !important to override status color CSS (ONLY for Available status)
                if (booth.background_color && isAvailable) {
                    boothElement.style.setProperty('background-color', booth.background_color, 'important');
                }
                if (booth.border_color && isAvailable) {
                    boothElement.style.setProperty('border-color', booth.border_color, 'important');
                }
                if (booth.text_color && isAvailable) {
                    boothElement.style.setProperty('color', booth.text_color, 'important');
                }
                
                // Apply other appearance properties
                if (booth.font_size) boothElement.style.fontSize = booth.font_size + 'px';
                if (booth.border_width) boothElement.style.borderWidth = booth.border_width + 'px';
                if (booth.border_radius) boothElement.style.borderRadius = booth.border_radius + 'px';
                if (booth.opacity !== null) boothElement.style.opacity = booth.opacity;
                
                // Status labels, descriptions, and colors from database
                @php
                    $statusLabelsArray = isset($statusSettings) && $statusSettings->count() > 0 
                        ? $statusSettings->pluck('status_name', 'status_code')->toArray() 
                        : [1 => 'Available', 2 => 'Confirmed', 3 => 'Reserved', 4 => 'Hidden', 5 => 'Paid'];
                    
                    $statusDescriptionsArray = [];
                    if (isset($statusSettings) && $statusSettings->count() > 0) {
                        foreach ($statusSettings as $status) {
                            $statusDescriptionsArray[$status->status_code] = $status->description ?? '';
                        }
                    } else {
                        // Fallback defaults
                        $statusDescriptionsArray = [
                            1 => 'This booth is available for booking',
                            2 => 'This booth has been confirmed',
                            3 => 'This booth is reserved',
                            4 => 'This booth is hidden',
                            5 => 'Payment has been received for this booth'
                        ];
                    }
                    
                    // Convert statusColors from nested array format to simple color strings
                    $statusColorsArray = [];
                    if (isset($statusColors) && !empty($statusColors)) {
                        foreach ($statusColors as $code => $colorData) {
                            $statusColorsArray[$code] = is_array($colorData) ? ($colorData['background'] ?? '#6c757d') : $colorData;
                        }
                    } else {
                        // Fallback defaults
                        $statusColorsArray = [1 => '#28a745', 2 => '#0dcaf0', 3 => '#ffc107', 4 => '#6c757d', 5 => '#212529'];
                    }
                @endphp
                const statusLabels = @json($statusLabelsArray);
                const statusDescriptions = @json($statusDescriptionsArray);
                const statusColors = @json($statusColorsArray);
                
                // Enhanced tooltip on hover
                const tooltip = document.getElementById('boothTooltip');
                let tooltipTimeout;
                
                // macOS dock-style animation with ripple effect
                boothElement.addEventListener('mouseenter', function(e) {
                    clearTimeout(tooltipTimeout);
                    const rect = this.getBoundingClientRect();
                    const statusLabel = statusLabels[booth.status] || 'Unknown';
                    const statusColor = statusColors[booth.status] || '#6c757d';
                    
                    // Calculate position for ripple effect (dock-style animation)
                    const hoveredX = parseFloat(this.style.left) + (parseFloat(this.style.width) || 80) / 2;
                    const hoveredY = parseFloat(this.style.top) + (parseFloat(this.style.height) || 50) / 2;
                    
                    // Find and animate nearby booths (ripple effect)
                    const allBooths = document.querySelectorAll('.dropped-booth');
                    allBooths.forEach(function(otherBooth) {
                        if (otherBooth === boothElement) return; // Skip self
                        
                        const otherX = parseFloat(otherBooth.style.left) + (parseFloat(otherBooth.style.width) || 80) / 2;
                        const otherY = parseFloat(otherBooth.style.top) + (parseFloat(otherBooth.style.height) || 50) / 2;
                        
                        // Calculate distance
                        const distance = Math.sqrt(Math.pow(hoveredX - otherX, 2) + Math.pow(hoveredY - otherY, 2));
                        
                        // Apply ripple effect based on distance (max 200px for effect)
                        if (distance < 200) {
                            const rippleIntensity = 1 - (distance / 200); // 1 at 0px, 0 at 200px
                            
                            if (rippleIntensity > 0.5) {
                                otherBooth.classList.add('dock-ripple-1');
                            } else if (rippleIntensity > 0.25) {
                                otherBooth.classList.add('dock-ripple-2');
                            } else {
                                otherBooth.classList.add('dock-ripple-3');
                            }
                        }
                    });
                    
                    let tooltipHTML = '<div class="tooltip-title"><i class="fas fa-store mr-1"></i>Booth ' + booth.booth_number + '</div>';
                    
                    // Customer / Client: show logo when available, else placeholder when booth is booked (has company)
                    if (booth.client_logo) {
                        tooltipHTML += '<div class="booth-tooltip-customer-logo" style="margin-bottom: 12px; text-align: center; padding: 10px; background: rgba(255,255,255,0.05); border-radius: 8px;"><img src="' + booth.client_logo + '" alt="' + (booth.company || booth.client_name || 'Customer').replace(/"/g, '&quot;') + '" style="max-width: 100%; max-height: 140px; border-radius: 6px; object-fit: contain; box-shadow: 0 4px 12px rgba(0,0,0,0.3); background: rgba(255,255,255,0.95); padding: 12px;"></div>';
                    } else if (booth.company || booth.client_name) {
                        var customerLabel = booth.company || booth.client_name || 'Customer';
                        var initial = (customerLabel || '?').toString().charAt(0).toUpperCase();
                        tooltipHTML += '<div class="booth-tooltip-customer-placeholder" style="margin-bottom: 12px; text-align: center; padding: 12px; background: rgba(255,255,255,0.08); border-radius: 8px;"><span style="display: inline-flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 700; font-size: 1.4rem;">' + initial + '</span><div style="margin-top: 8px; font-weight: 600; color: rgba(255,255,255,0.95);">' + (customerLabel || '').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div></div>';
                    }
                    // Booth image (only when no customer logo and no booked customer)
                    if (!booth.client_logo && !(booth.company || booth.client_name) && booth.booth_image) {
                        tooltipHTML += '<div style="margin-bottom: 12px; text-align: center;"><img src="' + booth.booth_image + '" alt="Booth Preview" style="max-width: 100%; max-height: 140px; border-radius: 6px; object-fit: cover; box-shadow: 0 2px 6px rgba(0,0,0,0.2);"></div>';
                    }
                    
                    // Key information in organized sections
                    tooltipHTML += '<div style="margin-bottom: 8px;">';
                    
                    // Booth type
                    if (booth.booth_type) {
                        tooltipHTML += '<div class="tooltip-row"><span class="tooltip-label"><i class="fas fa-tag mr-1"></i>Type:</span><span class="tooltip-value"><strong>' + booth.booth_type + '</strong></span></div>';
                    }
                    
                    // Status with description - calculate contrasting text color
                    const statusDescription = statusDescriptions[booth.status] || '';
                    const contrastColor = getContrastColor(statusColor);
                    tooltipHTML += '<div class="tooltip-row"><span class="tooltip-label"><i class="fas fa-info-circle mr-1"></i>Status:</span><span class="tooltip-value"><span class="tooltip-status" style="background: ' + statusColor + '; color: ' + contrastColor + ';">' + statusLabel + '</span></span></div>';
                    if (statusDescription) {
                        tooltipHTML += '<div style="margin-top: 4px; padding-left: 20px; font-size: 0.85rem; color: rgba(255,255,255,0.85); font-style: italic;">' + statusDescription + '</div>';
                    }
                    
                    // Price removed from public view
                    
                    tooltipHTML += '</div>';
                    
                    // Specifications section
                    if (booth.area_sqm || booth.capacity || booth.electricity_power) {
                        tooltipHTML += '<div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.15);">';
                        
                        if (booth.area_sqm) {
                            tooltipHTML += '<div class="tooltip-row"><span class="tooltip-label"><i class="fas fa-ruler-combined mr-1"></i>Area:</span><span class="tooltip-value">' + parseFloat(booth.area_sqm).toFixed(2) + ' m²</span></div>';
                        }
                        
                        if (booth.capacity) {
                            tooltipHTML += '<div class="tooltip-row"><span class="tooltip-label"><i class="fas fa-users mr-1"></i>Capacity:</span><span class="tooltip-value">' + booth.capacity + ' people</span></div>';
                        }
                        
                        if (booth.electricity_power) {
                            tooltipHTML += '<div class="tooltip-row"><span class="tooltip-label"><i class="fas fa-plug mr-1"></i>Power:</span><span class="tooltip-value">' + booth.electricity_power + '</span></div>';
                        }
                        
                        tooltipHTML += '</div>';
                    }
                    
                    // Company & Category section
                    if (booth.company || booth.category) {
                        tooltipHTML += '<div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.15);">';
                        
                        if (booth.company) {
                            tooltipHTML += '<div class="tooltip-row"><span class="tooltip-label"><i class="fas fa-building mr-1"></i>Company:</span><span class="tooltip-value">' + booth.company + '</span></div>';
                        }
                        
                        // Client logo shown above if available, so no need to show again here
                        
                        if (booth.category) {
                            tooltipHTML += '<div class="tooltip-row"><span class="tooltip-label"><i class="fas fa-folder mr-1"></i>Category:</span><span class="tooltip-value">' + booth.category + '</span></div>';
                        }
                        
                        tooltipHTML += '</div>';
                    }
                    
                    // Description preview
                    if (booth.description) {
                        const shortDesc = booth.description.length > 80 ? booth.description.substring(0, 80) + '...' : booth.description;
                        tooltipHTML += '<div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 0.85rem; color: rgba(255,255,255,0.9); line-height: 1.4;">' + shortDesc + '</div>';
                    }
                    
                    tooltipHTML += '<div style="margin-top: 12px; padding-top: 10px; border-top: 2px solid rgba(255,255,255,0.3); font-size: 0.8rem; color: rgba(255,255,255,0.9); text-align: center; font-weight: 600;"><i class="fas fa-hand-pointer mr-1"></i>Click to see full details</div>';
                    
                    tooltip.innerHTML = tooltipHTML;
                    tooltip.style.display = 'block';
                    
                    // Adjust max-width based on content (logo or image needs more space)
                    if (booth.client_logo || booth.booth_image) {
                        tooltip.style.maxWidth = '360px';
                    } else {
                        tooltip.style.maxWidth = '320px';
                    }
                    
                    // Position tooltip relative to viewport
                    updateTooltipPosition(e, tooltip, rect);
                });
                
                boothElement.addEventListener('mousemove', function(e) {
                    if (tooltip.style.display === 'block') {
                        const rect = this.getBoundingClientRect();
                        updateTooltipPosition(e, tooltip, rect);
                    }
                });
                
                boothElement.addEventListener('mouseleave', function() {
                    tooltipTimeout = setTimeout(function() {
                        tooltip.style.display = 'none';
                    }, 100);
                    
                    // Remove ripple effects from all booths (dock animation cleanup)
                    const allBooths = document.querySelectorAll('.dropped-booth');
                    allBooths.forEach(function(otherBooth) {
                        otherBooth.classList.remove('dock-ripple-1', 'dock-ripple-2', 'dock-ripple-3');
                    });
                });
                
                // Function to update tooltip position
                function updateTooltipPosition(e, tooltip, rect) {
                    const tooltipRect = tooltip.getBoundingClientRect();
                    let left = e.clientX - (tooltipRect.width / 2);
                    let top = rect.top - tooltipRect.height - 15;
                    
                    // Adjust if tooltip goes off screen horizontally
                    if (left < 10) left = 10;
                    if (left + tooltipRect.width > window.innerWidth - 10) {
                        left = window.innerWidth - tooltipRect.width - 10;
                    }
                    
                    // Adjust if tooltip goes off screen vertically
                    if (top < 10) {
                        top = rect.bottom + 15;
                    }
                    
                    tooltip.style.left = left + 'px';
                    tooltip.style.top = top + 'px';
                    tooltip.style.transform = 'none';
                }
                
                // Add title attribute for native browser tooltip with status text
                const statusLabel = statusLabels[booth.status] || 'Unknown';
                const statusDescription = statusDescriptions[booth.status] || '';
                if (statusDescription) {
                    boothElement.setAttribute('title', statusLabel + ': ' + statusDescription);
                } else {
                    boothElement.setAttribute('title', statusLabel);
                }
                
                // Click to show detailed modal
                boothElement.addEventListener('click', function(e) {
                    e.stopPropagation();
                    showBoothModal(booth, statusLabels, statusColors, statusDescriptions);
                });

                // Right-click context menu (Create Booking for logged-in user)
                boothElement.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const items = [];
                    const isAvailableForBooking = (booth.status == 1 || booth.status == 4);
                    if (window.publicViewCanCreateBooking && isAvailableForBooking) {
                        items.push({
                            label: 'Create Booking for Booth ' + booth.booth_number,
                            icon: 'fa-calendar-plus',
                            action: function() { hidePublicViewContextMenu(); openPublicBookingModal(booth.id, booth.booth_number); }
                        });
                    }
                    items.push({
                        label: 'View Booth Details',
                        icon: 'fa-info-circle',
                        action: function() { hidePublicViewContextMenu(); showBoothModal(booth, statusLabels, statusColors, statusDescriptions); }
                    });
                    showPublicViewContextMenu(e.clientX, e.clientY, items);
                });
                
                canvas.appendChild(boothElement);
            }
        });

        // Right-click on canvas (empty area): show Create Booking when logged-in user can create
        if (container && window.publicViewCanCreateBooking) {
            container.addEventListener('contextmenu', function(e) {
                if (e.target.closest('.dropped-booth')) return;
                e.preventDefault();
                showPublicViewContextMenu(e.clientX, e.clientY, [
                    { label: 'Create Booking', icon: 'fa-calendar-plus', action: function() { hidePublicViewContextMenu(); openPublicBookingModal(null, null); } }
                ]);
            });
        }
        
        // Function to apply shake animation while preserving rotation
        function applyShakeAnimation(element, rotation) {
            const shakeOffsets = [
                { x: -0.5, y: 0.3 },
                { x: 0.4, y: -0.2 },
                { x: -0.3, y: 0.4 },
                { x: 0.5, y: -0.3 },
                { x: -0.2, y: 0.2 },
                { x: 0.3, y: -0.4 },
                { x: -0.4, y: 0.3 },
                { x: 0.2, y: -0.2 },
                { x: -0.3, y: 0.2 },
                { x: 0, y: 0 }
            ];
            
            let currentIndex = 0;
            const rotationDeg = rotation || 0;
            
            // Store original transform for hover
            element.style.setProperty('--booth-rotation', rotationDeg + 'deg');
            
            function animate() {
                if (element.parentElement && !element.matches(':hover')) {
                    const offset = shakeOffsets[currentIndex];
                    element.style.transform = 'translate(' + offset.x + 'px, ' + offset.y + 'px) rotate(' + rotationDeg + 'deg)';
                    currentIndex = (currentIndex + 1) % shakeOffsets.length;
                }
            }
            
            // Start animation with 300ms interval (3s total cycle)
            const intervalId = setInterval(animate, 300);
            
            // Stop animation on hover
            element.addEventListener('mouseenter', function() {
                element.style.transform = 'scale(1.08) rotate(' + rotationDeg + 'deg)';
            });
            
            element.addEventListener('mouseleave', function() {
                animate(); // Resume animation
            });
            
            // Clean up interval when element is removed
            const observer = new MutationObserver(function(mutations) {
                if (!element.parentElement) {
                    clearInterval(intervalId);
                    observer.disconnect();
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
        
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
        
        // Legend is now always visible, no toggle needed
        
        // Help modal functions
        function showHelp() {
            document.getElementById('helpModal').classList.add('active');
        }
        
        function closeHelp(event) {
            if (!event || event.target.id === 'helpModal') {
                document.getElementById('helpModal').classList.remove('active');
            }
        }
        
        // Welcome message functions
        function closeWelcome() {
            const welcomeMsg = document.getElementById('welcomeMessage');
            if (welcomeMsg) {
                welcomeMsg.classList.add('hidden');
                // Hide after 5 seconds automatically
                setTimeout(function() {
                    if (welcomeMsg) {
                        welcomeMsg.style.display = 'none';
                    }
                }, 500);
            }
        }
        
        // Auto-hide welcome message after 8 seconds
        setTimeout(function() {
            const welcomeMsg = document.getElementById('welcomeMessage');
            if (welcomeMsg && !welcomeMsg.classList.contains('hidden')) {
                welcomeMsg.style.opacity = '0';
                welcomeMsg.style.transition = 'opacity 0.5s ease';
                setTimeout(function() {
                    if (welcomeMsg) {
                        welcomeMsg.style.display = 'none';
                    }
                }, 500);
            }
        }, 8000);
        
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
        
        // Show booth detail modal
        function showBoothModal(booth, statusLabels, statusColors, statusDescriptions) {
            const modal = document.getElementById('boothModal');
            const modalBody = document.getElementById('modalBody');
            const modalBoothNumber = document.getElementById('modalBoothNumber');
            
            modalBoothNumber.textContent = 'Booth ' + booth.booth_number;
            
            const statusLabel = statusLabels[booth.status] || 'Unknown';
            const statusColor = statusColors[booth.status] || '#6c757d';
            const statusDescription = (statusDescriptions && statusDescriptions[booth.status]) ? statusDescriptions[booth.status] : '';
            const contrastColor = getContrastColor(statusColor);
            
            let html = '';
            
            // Customer / Client: show logo when available, else placeholder when booth is booked
            if (booth.client_logo) {
                html += '<div class="booth-detail-section booth-detail-customer-logo" style="margin-bottom: 25px; text-align: center;">';
                html += '<div style="display: inline-block; width: 160px; height: 160px; border-radius: 50%; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.15); background: rgba(0,0,0,0.02); border: 3px solid rgba(102,126,234,0.2);">';
                html += '<img src="' + booth.client_logo + '" alt="' + (booth.company || booth.client_name || 'Customer').replace(/"/g, '&quot;') + '" style="width: 100%; height: 100%; object-fit: cover;">';
                html += '</div>';
                html += '</div>';
            } else if (booth.company || booth.client_name) {
                var customerLabel = booth.company || booth.client_name || 'Customer';
                var initial = (customerLabel || '?').toString().charAt(0).toUpperCase();
                html += '<div class="booth-detail-section booth-detail-customer-placeholder" style="margin-bottom: 25px; text-align: center; padding: 24px; background: linear-gradient(135deg, rgba(102,126,234,0.08) 0%, rgba(118,75,162,0.08) 100%); border-radius: 12px; border: 1px solid rgba(102,126,234,0.2);">';
                html += '<span style="display: inline-flex; align-items: center; justify-content: center; width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 700; font-size: 1.8rem;">' + initial + '</span>';
                html += '<div style="margin-top: 12px; font-weight: 700; font-size: 1.1rem; color: #212529;">' + (customerLabel || '').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</div>';
                html += '</div>';
            }
            // Booth Image Preview (only when no customer and no logo)
            if (!booth.client_logo && !(booth.company || booth.client_name) && booth.booth_image) {
                html += '<div class="booth-detail-section" style="margin-bottom: 25px;">';
                html += '<img src="' + booth.booth_image + '" alt="Booth Preview" style="width: 100%; max-height: 350px; object-fit: cover; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.15);">';
                html += '</div>';
            }
            
            // Basic Information Section
            html += '<div class="booth-detail-section">';
            html += '<div class="booth-detail-section-title"><i class="fas fa-info-circle"></i> Basic Information</div>';
            
            html += '<div class="booth-detail-row">';
            html += '<span class="booth-detail-label"><i class="fas fa-hashtag"></i> Booth Number:</span>';
            html += '<span class="booth-detail-value"><strong style="font-size: 1.3rem; color: #667eea;">' + booth.booth_number + '</strong></span>';
            html += '</div>';
            
            if (booth.booth_type) {
                html += '<div class="booth-detail-row">';
                html += '<span class="booth-detail-label"><i class="fas fa-tag"></i> Booth Type:</span>';
                html += '<span class="booth-detail-value"><strong style="color: #667eea;">' + booth.booth_type + '</strong></span>';
                html += '</div>';
            }
            
            html += '<div class="booth-detail-row">';
            html += '<span class="booth-detail-label"><i class="fas fa-info-circle"></i> Status:</span>';
            html += '<span class="booth-detail-value status-badge" style="background: ' + statusColor + '; color: ' + contrastColor + ';">' + statusLabel + '</span>';
            html += '</div>';
            
            if (statusDescription) {
                html += '<div class="booth-detail-row" style="margin-top: 8px;">';
                html += '<span class="booth-detail-label"></span>';
                html += '<span class="booth-detail-value" style="font-size: 0.9rem; color: #6c757d; font-style: italic;">' + statusDescription + '</span>';
                html += '</div>';
            }
            
            html += '</div>';
            
            // Specifications Section
            let hasSpecs = false;
            let specsHTML = '<div class="booth-detail-section">';
            specsHTML += '<div class="booth-detail-section-title"><i class="fas fa-clipboard-list"></i> Specifications</div>';
            
            if (booth.area_sqm) {
                specsHTML += '<div class="booth-detail-row">';
                specsHTML += '<span class="booth-detail-label"><i class="fas fa-ruler-combined"></i> Area:</span>';
                specsHTML += '<span class="booth-detail-value">' + parseFloat(booth.area_sqm).toFixed(2) + ' m²</span>';
                specsHTML += '</div>';
                hasSpecs = true;
            }
            
            if (booth.capacity) {
                specsHTML += '<div class="booth-detail-row">';
                specsHTML += '<span class="booth-detail-label"><i class="fas fa-users"></i> Capacity:</span>';
                specsHTML += '<span class="booth-detail-value">' + booth.capacity + ' people</span>';
                specsHTML += '</div>';
                hasSpecs = true;
            }
            
            if (booth.electricity_power) {
                specsHTML += '<div class="booth-detail-row">';
                specsHTML += '<span class="booth-detail-label"><i class="fas fa-bolt"></i> Electricity Power:</span>';
                specsHTML += '<span class="booth-detail-value">' + booth.electricity_power + '</span>';
                specsHTML += '</div>';
                hasSpecs = true;
            }
            
            specsHTML += '</div>';
            if (hasSpecs) {
                html += specsHTML;
            }
            
            if (booth.description) {
                html += '<div class="booth-detail-section">';
                html += '<div class="booth-detail-section-title"><i class="fas fa-align-left"></i> Description</div>';
                html += '<div class="booth-detail-section-content">' + booth.description.replace(/\n/g, '<br>') + '</div>';
                html += '</div>';
            }
            
            if (booth.features) {
                html += '<div class="booth-detail-section">';
                html += '<div class="booth-detail-section-title"><i class="fas fa-star"></i> Features</div>';
                html += '<div class="booth-detail-section-content">' + booth.features.replace(/\n/g, '<br>') + '</div>';
                html += '</div>';
            }
            
            if (booth.company || booth.category || booth.sub_category) {
                html += '<div class="booth-detail-section">';
                html += '<div class="booth-detail-section-title"><i class="fas fa-building"></i> Company & Category</div>';
                
                if (booth.company) {
                    html += '<div class="booth-detail-row">';
                    html += '<span class="booth-detail-label"><i class="fas fa-building"></i> Company:</span>';
                    html += '<span class="booth-detail-value">' + booth.company + '</span>';
                    html += '</div>';
                }
                if (booth.client_name) {
                    html += '<div class="booth-detail-row">';
                    html += '<span class="booth-detail-label"><i class="fas fa-user"></i> Contact:</span>';
                    html += '<span class="booth-detail-value">' + booth.client_name.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>';
                    html += '</div>';
                }
                if (booth.category) {
                    html += '<div class="booth-detail-row">';
                    html += '<span class="booth-detail-label"><i class="fas fa-folder"></i> Category:</span>';
                    html += '<span class="booth-detail-value">' + booth.category + '</span>';
                    html += '</div>';
                }
                
                if (booth.sub_category) {
                    html += '<div class="booth-detail-row">';
                    html += '<span class="booth-detail-label"><i class="fas fa-tag"></i> Sub-Category:</span>';
                    html += '<span class="booth-detail-value">' + booth.sub_category + '</span>';
                    html += '</div>';
                }
                
                html += '</div>';
            }
            
            // Dimensions & Technical Details Section
            if (booth.width || booth.height) {
                html += '<div class="booth-detail-section">';
                html += '<div class="booth-detail-section-title"><i class="fas fa-ruler-combined"></i> Dimensions & Technical Details</div>';
                
                if (booth.width && booth.height) {
                    html += '<div class="booth-detail-row">';
                    html += '<span class="booth-detail-label">Canvas Size:</span>';
                    html += '<span class="booth-detail-value">' + booth.width + ' × ' + booth.height + ' px</span>';
                    html += '</div>';
                }
                
                html += '</div>';
            }
            
            if (booth.notes) {
                html += '<div class="booth-detail-section">';
                html += '<div class="booth-detail-section-title"><i class="fas fa-sticky-note"></i> Additional Notes</div>';
                html += '<div class="booth-detail-section-content" style="font-style: italic; color: #6c757d;">' + booth.notes.replace(/\n/g, '<br>') + '</div>';
                html += '</div>';
            }

            // Book this booth (only when logged in with permission and booth is available or hidden) - opens inline form
            const isAvailableForBooking = (booth.status == 1 || booth.status == 4);
            if (window.publicViewCanCreateBooking && isAvailableForBooking) {
                html += '<div class="booth-detail-section" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef;">';
                html += '<button type="button" class="btn-book-booth-public" data-booth-id="' + booth.id + '" data-booth-number="' + (booth.booth_number || '') + '"><i class="fas fa-calendar-plus"></i> Book this booth</button>';
                html += '<p class="text-muted small mt-2 mb-0">Opens the booking form on this page.</p>';
                html += '</div>';
            }
            
            modalBody.innerHTML = html;
            modal.classList.add('active');

            // Book this booth button: open inline booking form
            const bookBtn = modalBody.querySelector('.btn-book-booth-public');
            if (bookBtn && bookBtn.dataset.boothId && typeof openPublicBookingModal === 'function') {
                bookBtn.addEventListener('click', function() {
                    closeBoothModal();
                    openPublicBookingModal(parseInt(bookBtn.dataset.boothId, 10), bookBtn.dataset.boothNumber || '');
                });
            }
            
            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeBoothModal();
                }
            });
        }
        
        // Close booth modal
        function closeBoothModal() {
            const modal = document.getElementById('boothModal');
            modal.classList.remove('active');
        }
        
        // Close button event
        document.getElementById('closeModal').addEventListener('click', closeBoothModal);
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBoothModal();
            }
        });
    </script>
</body>
</html>
