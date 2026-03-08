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
            analysis.suggestedFix = '🔧 Fix: Check for unclosed brackets, quotes, or template literal issues. Look for Blade syntax conflicts (use {{ }} for literal braces).';
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

function hideCanvasLoader() {
    var el = document.getElementById('canvasLoadingOverlay');
    if (el) el.classList.add('canvas-loaded');
}

// Global State
window.floorPlanIdForCanvas = FPD.floorPlanId;
window.canvasTextItems = FPD.canvasTextItems;

const FloorPlanDesigner = {
    // State
    draggedElement: null,
    draggedBoothData: null,
    selectedBooths: [],
    history: [],
    historyIndex: -1,
    gridEnabled: false, // Grid visibility (can be toggled)
    snapEnabled: true,
    gridSize: 1, // Grid size in px (default 1px)
    centerMarkerEnabled: false, // Show/hide canvas center marker
    zoomLevel: 1,
    panzoomInstance: null,
    canvasWidth: FPD.canvasWidth, // Floor plan canvas width or default
    canvasHeight: FPD.canvasHeight, // Floor plan canvas height or default
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
    
    // Status colors cache (loaded from database)
    statusColorsCache: null,
    
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
        // #endregion
        
        return element;
    },
    
    // Initialize
    init: function() {
        const self = this;
        
        // Check if user can edit canvas (from backend)
        self.canEditCanvas = FPD.canEditCanvas;
        
        // #region agent log
        // #endregion
        
        // Cache frequently used DOM elements
        self._cachedElements.canvas = document.getElementById('print');
        self._cachedElements.container = document.getElementById('printContainer');
        self._cachedElements.infoToolbar = document.getElementById('infoToolbar');
        self._cachedElements.floorplanImage = document.getElementById('floorplanImageElement');
        
        // #region agent log
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
            } else if (e.key === 't' || e.key === 'T') {
                e.preventDefault();
                if (!self.isSpacePanning && self.currentTool !== 'text') {
                    self.previousTool = self.currentTool;
                }
                self.switchTool('text');
                const btn = document.getElementById('btnTextTool');
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
        if (FPD.currentFloorPlan) {
            if (FPD.canvasWidth && FPD.canvasHeight) {
                // Use floor plan canvas dimensions as defaults
                self.canvasWidth = FPD.canvasWidth;
                self.canvasHeight = FPD.canvasHeight;
            }
            
            if (FPD.currentFloorPlan && FPD.currentFloorPlan.floor_image) {
                // CRITICAL: Always use floor_plans.floor_image as source of truth
                // Store floor plan image URL for immediate use - ensure absolute URL
                self.floorPlanImageUrl = FPD.floorPlanImageUrl;
                self.floorplanImage = FPD.floorPlanImagePath;
                
                // IMMEDIATELY set canvas background from floor plan (highest priority)
                // This ensures the image loads automatically when user clicks "View Booths"
                const canvas = document.getElementById('print');
                if (canvas) {
                    console.log('[Floor Plan] Loading image for floor plan ' + FPD.currentFloorPlan.id + ': ' + FPD.currentFloorPlan.name);
                    console.log('[Floor Plan] Image path:', self.floorplanImage);
                    console.log('[Floor Plan] Image URL:', self.floorPlanImageUrl);
                    
                    // Always set the background image from current floor plan (automatic load)
                    // Use absolute URL to ensure it works for all users
                    const imageUrl = self.floorPlanImageUrl.startsWith('http') 
                        ? self.floorPlanImageUrl 
                        : window.location.origin + (self.floorPlanImageUrl.startsWith('/') ? '' : '/') + self.floorPlanImageUrl;
                    
                    canvas.style.backgroundImage = 'url(\'' + imageUrl + '?t=' + Date.now() + '\')';
                    canvas.style.backgroundSize = '100% 100%';
                    canvas.style.backgroundRepeat = 'no-repeat';
                    canvas.style.backgroundPosition = 'top left';
                    canvas.style.backgroundAttachment = 'local';
                    canvas.style.margin = '0';
                    canvas.style.display = 'block';
                    canvas.style.float = 'left';
                    
                    console.log('[Floor Plan] Canvas background image set:', canvas.style.backgroundImage);
                    
                    // Verify image loads successfully
                    const testImg = new Image();
                    testImg.onload = function() {
                        console.log('[Floor Plan] ✅ Image verified and loaded successfully');
                    };
                    testImg.onerror = function() {
                        console.error('[Floor Plan] ❌ Image failed to load, trying alternative URL');
                        // Try alternative URL construction
                        const altUrl = window.location.origin + '/' + self.floorplanImage.replace(/^\//, '');
                        canvas.style.backgroundImage = 'url(\'' + altUrl + '?t=' + Date.now() + '\')';
                        console.log('[Floor Plan] Retry with alternative URL:', altUrl);
                    };
                    testImg.src = imageUrl;
                }
            } else {
                console.log('[Floor Plan] No image for floor plan ' + FPD.currentFloorPlan.id + ': ' + FPD.currentFloorPlan.name);
            }
        }
        
        // Load booth default settings and status colors from database first, then setup
        Promise.all([
            this.loadBoothSettingsFromDatabase(),
            this.loadStatusColors()
        ]).then(function() {
            self.setupDragAndDrop();
            self.setupToolbar();
            self.setupTextFormatPanel();
            self.setupCanvas();
            self.setupKeyboard();
            self.setupZoomSelection(); // Setup Photoshop-like zoom selection (Ctrl+Space)
            
            // AUTOMATICALLY load floor plan image and resize canvas to match image resolution
            if (FPD.currentFloorPlan && FPD.currentFloorPlan.floor_image) {
                // CRITICAL: Floor plan has image - automatically load it and resize canvas
                // This ensures the canvas loads the correct image when user clicks "View Booths"
                const canvas = document.getElementById('print');
                if (canvas && self.floorPlanImageUrl) {
                    console.log('[Floor Plan Auto-Load] Starting automatic image load for floor plan ' + FPD.currentFloorPlan.id);
                    
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
                    // Remove crossOrigin if it causes issues - let browser handle it naturally
                    // img.crossOrigin = 'anonymous'; // Only use if CORS is actually needed
                    
                    // Ensure absolute URL
                    const imageUrlToLoad = self.floorPlanImageUrl.startsWith('http') 
                        ? self.floorPlanImageUrl 
                        : window.location.origin + (self.floorPlanImageUrl.startsWith('/') ? '' : '/') + self.floorPlanImageUrl;
                    
                    img.onload = function() {
                        const imageWidth = img.naturalWidth || img.width;
                        const imageHeight = img.naturalHeight || img.height;
                        
                        console.log('[Floor Plan Auto-Load] Image loaded successfully:', {
                            floor_plan_id: FPD.currentFloorPlan.id,
                            floor_plan_name: FPD.currentFloorPlan.name,
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
                                    setTimeout(function() {
                                        self.fitCanvasToView(false);
                                        setTimeout(hideCanvasLoader, 150);
                                    }, 100);
                                }, 100);
                            } else {
                                setTimeout(hideCanvasLoader, 200);
                            }
                        }
                    };
                    
                    img.onerror = function() {
                        console.error('[Floor Plan Auto-Load] Failed to load floor plan image:', {
                            floor_plan_id: FPD.currentFloorPlan.id,
                            floor_plan_name: FPD.currentFloorPlan.name,
                            image_url: imageUrlToLoad,
                            image_path: self.floorplanImage,
                            original_url: self.floorPlanImageUrl
                        });
                        
                        // Try alternative URL formats
                        const alternatives = [
                            window.location.origin + '/' + self.floorplanImage.replace(/^\//, ''),
                            '/' + self.floorplanImage.replace(/^\//, ''),
                            self.floorplanImage.startsWith('/') ? self.floorplanImage : '/' + self.floorplanImage
                        ];
                        
                        let retryCount = 0;
                        const tryNextAlternative = function() {
                            if (retryCount < alternatives.length) {
                                const altUrl = alternatives[retryCount];
                                console.log('[Floor Plan Auto-Load] Retrying with alternative URL:', altUrl);
                                img.src = altUrl + '?t=' + Date.now();
                                retryCount++;
                            } else {
                                console.error('[Floor Plan Auto-Load] All image URL attempts failed');
                                // Fallback to floor plan canvas dimensions if image fails to load
                                if (!localStorage.getItem('canvasWidth') || !localStorage.getItem('canvasHeight')) {
                                    self.setCanvasSize(self.canvasWidth, self.canvasHeight);
                                }
                                // Show user-friendly error
                                customAlert('Floor plan image could not be loaded. Please check if the image file exists.', 'warning');
                                hideCanvasLoader();
                            }
                        };
                        
                        // Retry with alternatives
                        img.onerror = tryNextAlternative;
                        tryNextAlternative();
                    };
                    
                    // AUTOMATICALLY load the image immediately
                    img.src = imageUrlToLoad;
                    
                    // Load canvas settings AFTER image is loaded (zoom/pan/grid - but don't override image)
                    // The image is already set from floor_plans.floor_image, so loadCanvasSettings won't clear it
                    setTimeout(function() {
                        console.log('[Floor Plan Auto-Load] Loading canvas settings for floor plan ' + FPD.currentFloorPlan.id);
                        self.loadCanvasSettings();
                    }, 300); // Small delay to ensure image starts loading first
                } else {
                    console.warn('[Floor Plan Auto-Load] Canvas or image URL not found', {
                        canvas_exists: !!canvas,
                        floorPlanImageUrl: self.floorPlanImageUrl
                    });
                    // Fallback: load canvas settings
                    self.loadCanvasSettings();
                    setTimeout(hideCanvasLoader, 300);
                }
            } else {
                // No floor plan image from Blade - load canvas settings (may have image from canvas_settings)
                console.log('[Floor Plan Auto-Load] No image for current floor plan, loading canvas settings');
                self.loadCanvasSettings();
                // Ensure canvas has a fixed size (in case no saved settings exist)
                if (!localStorage.getItem('canvasWidth') || !localStorage.getItem('canvasHeight')) {
                    self.setCanvasSize(self.canvasWidth, self.canvasHeight);
                }
                setTimeout(hideCanvasLoader, 600);
            }
            
            // Check if there's an existing floorplan image and resize canvas to match (fallback for other cases)
            // Only if we haven't already loaded from Blade template
            if (!FPD.currentFloorPlan || !FPD.currentFloorPlan.floor_image) {
                self.detectAndResizeCanvasToImage();
            }
        
            self.loadSavedPositions();
            // After loading positions, sync sidebar to remove booths already on canvas
            setTimeout(function() {
                self.syncSidebarWithCanvas();
            }, 500); // Small delay to ensure all booths are loaded
            
            // Save initial state for undo/redo so we have a snapshot to undo back to
            setTimeout(function() {
                self.saveState();
            }, 700);
            
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
            self.setupTextFormatPanel();
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
            setTimeout(hideCanvasLoader, 600);
        });
        
        // Auto-fit canvas to show entire image on EVERY page load
        // Always fit to canvas to show 100% of the floorplan
        const fitOnLoad = function() {
            if (self.panzoomInstance) {
                const canvas = self.getElement('print');
                const container = self.getElement('printContainer');
                
                if (canvas && container) {
                    // Always fit canvas to view - no conditions
                    self.fitCanvasToView(false);
                } else {
                    // Elements not ready, wait a bit more
                    setTimeout(fitOnLoad, 200);
                }
            } else {
                // Panzoom not ready, wait a bit more
                setTimeout(fitOnLoad, 200);
            }
        };
        
        // Start fitting immediately and retry if needed
        setTimeout(fitOnLoad, 100);
        
        // Also fit after image loads (if there's an image)
        const floorplanImg = self.getElement('floorplanImageElement');
        if (floorplanImg) {
            if (floorplanImg.complete) {
                // Image already loaded, fit now
                setTimeout(function() {
                    if (self.panzoomInstance) {
                        self.fitCanvasToView(false);
                    }
                }, 300);
            } else {
                // Wait for image to load
                floorplanImg.addEventListener('load', function() {
                    setTimeout(function() {
                        if (self.panzoomInstance) {
                            self.fitCanvasToView(false);
                        }
                    }, 300);
                }, { once: true });
            }
        }
        
        // Fit canvas on window resize (debounced) - separate from the resize handler above
        let fitResizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(fitResizeTimeout);
            fitResizeTimeout = setTimeout(function() {
                if (self.panzoomInstance) {
                    self.fitCanvasToView(false);
                }
            }, 300);
        });
    },
    
    // Setup Drag and Drop
    setupDragAndDrop: function() {
        const self = this;
        const canvas = self.getElement('print');
        
        if (!canvas) {
            return;
        }
        
        // If user cannot edit canvas, disable drag and drop
        if (!self.canEditCanvas) {
            console.log('[Canvas Permissions] Drag and drop disabled - user does not have canvas edit permission');
            // Make booth items non-draggable
            const boothItems = document.querySelectorAll('.booth-number-item');
            boothItems.forEach(function(item) {
                item.draggable = false;
                item.style.cursor = 'default';
                item.style.opacity = '0.8';
            });
            return; // Exit early - no drag and drop setup
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
            const floorPlanId = FPD.floorPlanId;
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
        document.getElementById('zoneAboutInput').value = '';
        
        // Show modal
        $('#addZoneModal').modal('show');
        
        // Handle submit
        $('#btnAddZoneSubmit').off('click').on('click', function() {
            const btn = $(this);
            const originalText = btn.html();
            
            let zoneName = document.getElementById('zoneNameInput').value.trim().toUpperCase();
            let zoneAbout = document.getElementById('zoneAboutInput').value.trim();
            
            if (!zoneName || !/^[A-Z]{1,3}$/.test(zoneName)) {
                customAlert('Please enter a valid zone name (letters only, 1-3 characters).', 'warning');
                return;
            }
            
            // Disable button and show loading
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Creating...');
            
            // Get current floor plan ID
            const floorPlanId = FPD.floorPlanId;
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
                floor_plan_id: floorPlanId,
                zone_about: zoneAbout
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
                        const currentFloorPlanId = FPD.floorPlanId;
                        if (currentFloorPlanId) {
                            window.location.href = FPD.routes.boothsIndex + '?view=canvas&floor_plan_id=' + currentFloorPlanId;
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
        
        // Reset form and tabs (Bootstrap 5: use data-bs-toggle tab or Tab API)
        document.getElementById('deleteBoothForm').reset();
        var deleteAllTabEl = document.querySelector('#deleteOptionTabs a[href="#delete-all"]');
        if (deleteAllTabEl && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(deleteAllTabEl).show();
        } else {
            $('#deleteOptionTabs .nav-link').removeClass('active');
            $('#deleteOptionTabsContent .tab-pane').removeClass('show active');
            $('#delete-all-tab').addClass('active');
            $('#delete-all').addClass('show active');
        }
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
            
            // Get current floor plan ID (not required for "By Booth ID(s)" tab)
            const floorPlanId = FPD.floorPlanId;
            
            let requestData = {};
            let confirmMessage = '';
            let deleteByIds = false;
            
            if (activeTab === '#delete-all') {
                if (!floorPlanId) {
                    customAlert('Floor plan ID is required to delete booths', 'error');
                    return;
                }
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
                
                confirmMessage = 'Are you sure you want to delete ALL ' + count + ' booths from Zone ' + zoneName + ' in this floor plan? This action cannot be undone!';
                requestData = { 
                    mode: 'all',
                    floor_plan_id: floorPlanId
                };
                
            } else if (activeTab === '#delete-specific') {
                if (!floorPlanId) {
                    customAlert('Floor plan ID is required to delete booths', 'error');
                    return;
                }
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
                    booth_ids: boothIds,
                    floor_plan_id: floorPlanId
                };
                
            } else if (activeTab === '#delete-by-ids') {
                // Delete by booth ID(s) from any zone / any floor plan
                const raw = (document.getElementById('deleteByBoothIdsInput').value || '').trim();
                if (!raw) {
                    customAlert('Please enter at least one booth ID (comma-separated)', 'warning');
                    return;
                }
                const boothIds = raw.split(/[\s,]+/).map(function(s) { return parseInt(s, 10); }).filter(function(n) { return !isNaN(n) && n > 0; });
                if (boothIds.length === 0) {
                    customAlert('Please enter valid booth ID(s) (numbers, comma-separated)', 'warning');
                    return;
                }
                confirmMessage = 'Are you sure you want to delete ' + boothIds.length + ' booth(s) by ID from any zone/floor plan? This cannot be undone!';
                requestData = { booth_ids: boothIds };
                deleteByIds = true;
                
            } else if (activeTab === '#delete-range') {
                if (!floorPlanId) {
                    customAlert('Floor plan ID is required to delete booths', 'error');
                    return;
                }
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
                confirmMessage = 'Are you sure you want to delete ' + count + ' booths (from ' + zoneName + String(from).padStart(2, '0') + ' to ' + zoneName + String(to).padStart(2, '0') + ') in this floor plan? This action cannot be undone!';
                requestData = {
                    mode: 'range',
                    from: from,
                    to: to,
                    floor_plan_id: floorPlanId
                };
            }
            
            // Show confirmation dialog
            if (!confirm(confirmMessage)) {
                return;
            }
            
            // Disable button and show loading
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
            
            var deleteUrl = deleteByIds ? '/booths/delete-by-ids' : ('/booths/delete-in-zone/' + zoneName);
            // Delete booths
            fetch(deleteUrl, {
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
                if (data.status === 200 || data.status === 206) {
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
                    customAlert(message, data.status === 206 ? 'warning' : 'success');
                    
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
                url: FPD.routes.clientsSearch,
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
                        const isBooked = (data.client_id && data.client_id !== null) || (formData.status != 1);
                        boothElement.className = 'dropped-booth status-' + formData.status + (isBooked ? ' booked' : '');
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
            <div class="context-menu-item context-menu-subheader" style="pointer-events: none; font-weight: 600; color: #667eea;">
                <i class="fas fa-edit"></i> Edit Booth
            </div>
            <div class="context-menu-item" data-action="update-booth-info" title="Basic info, details, content, media (same as Edit at /booths)">
                <i class="fas fa-info-circle"></i> Update Booth Info
            </div>
            <div class="context-menu-item" data-action="update-booth-appearance" title="Position, size, colors on canvas">
                <i class="fas fa-paint-brush"></i> Update Booth Appearance
            </div>
            <div class="context-menu-divider"></div>
            <div class="context-menu-item" data-action="set-colors" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 600;">
                <i class="fas fa-palette"></i> Set Individual Colors
            </div>
            <div class="context-menu-divider"></div>
            <div class="context-menu-item" data-action="set-price">
                <i class="fas fa-dollar-sign"></i> Set Price
            </div>
            <div class="context-menu-item" data-action="book">
                <i class="fas fa-calendar-check"></i> Book Booth
            </div>
            <div class="context-menu-divider"></div>
            <div class="context-menu-item" data-action="delete-booth" style="color: #dc3545;">
                <i class="fas fa-trash-alt"></i> Delete Booth(s)
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
                
                if (action === 'update-booth-info') {
                    // Open Update Booth Info modal on canvas (no redirect): fetch booth data and populate form
                    self.openBoothInfoModal(boothId);
                } else if (action === 'update-booth-appearance') {
                    // Select this booth and open Properties panel (position, size, colors on canvas)
                    self.selectedBooths.forEach(function(booth) {
                        booth.classList.remove('selected');
                    });
                    self.selectedBooths = [boothElement];
                    boothElement.classList.add('selected');
                    self.updateInfoToolbar(boothElement);
                    if (!boothElement.classList.contains('locked')) {
                        var handles = boothElement.querySelectorAll('.resize-handle');
                        handles.forEach(function(h) { h.style.display = 'block'; });
                        self.updateResizeHandlesSize(boothElement);
                        var rotateHandle = boothElement.querySelector('.rotate-handle');
                        if (rotateHandle) rotateHandle.style.display = 'flex';
                        self.updateRotationIndicator(boothElement);
                    }
                    self.updateSelectionBoundingBox();
                    self.updatePropertiesPanel(boothElement);
                    var panel = document.getElementById('propertiesPanel');
                    var backdrop = document.getElementById('propertiesPanelBackdrop');
                    if (panel) panel.classList.add('active');
                    if (backdrop) backdrop.classList.add('active');
                } else if (action === 'set-price') {
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
                } else if (action === 'set-colors') {
                    // Open individual booth color picker
                    self.openBoothColorPicker(boothId, boothNumber, boothElement);
                } else if (action === 'book') {
                    self.showBookBoothModal(boothId, boothNumber, boothElement);
                } else if (action === 'delete-booth') {
                    self.deleteBoothsFromCanvas(boothId, boothNumber, boothElement);
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
    
    // Delete booth(s) from canvas: check bookings first, then delete if clear (procedure-first)
    deleteBoothsFromCanvas: function(boothId, boothNumber, boothElement) {
        const self = this;
        // Determine booth IDs to delete: all selected if multiple selected and this booth is in selection, else just this booth
        let boothIdsToDelete = [];
        const isInSelection = self.selectedBooths && self.selectedBooths.indexOf(boothElement) !== -1;
        if (isInSelection && self.selectedBooths.length > 1) {
            boothIdsToDelete = self.selectedBooths.map(function(el) { return parseInt(el.getAttribute('data-booth-id'), 10); }).filter(function(id) { return !isNaN(id); });
        } else {
            boothIdsToDelete = [parseInt(boothId, 10)];
        }
        if (boothIdsToDelete.length === 0) return;
        
        // Step 1: Check if any booth has an active booking (procedure: resolve first before delete)
        fetch('/booths/check-bookings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ booth_ids: boothIdsToDelete })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.status !== 200) {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to check bookings.' });
                return;
            }
            if (data.booths_with_bookings && data.booths_with_bookings.length > 0) {
                var list = data.booths_with_bookings.map(function(b) {
                    return b.booth_number + ' (Booking #' + b.book_id + ', ' + (b.client_company || 'Unknown') + ')';
                }).join('<br>');
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot delete: active bookings',
                    html: 'The following booth(s) have active bookings. Please cancel or reassign the booking first before deleting.<br><br><div style="text-align: left; max-height: 200px; overflow-y: auto;">' + list + '</div><br><small>Go to Bookings to manage or cancel bookings.</small>',
                    confirmButtonText: 'Go to Bookings',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#3085d6'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        window.location.href = FPD.urls.books;
                    }
                });
                return;
            }
            // Step 2: All clear — confirm then delete
            var count = boothIdsToDelete.length;
            Swal.fire({
                title: 'Delete ' + count + ' booth(s)?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                confirmButtonColor: '#dc3545',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                fetch('/booths/delete-by-ids', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ booth_ids: boothIdsToDelete })
                })
                .then(function(res) { return res.json(); })
                .then(function(delData) {
                    if (delData.status !== 200 && delData.status !== 206) {
                        Swal.fire({ icon: 'error', title: 'Error', text: delData.message || 'Failed to delete.' });
                        return;
                    }
                    // Remove booth elements from canvas and sidebar
                    boothIdsToDelete.forEach(function(id) {
                        var canvasBooth = document.querySelector('#print .dropped-booth[data-booth-id="' + id + '"]');
                        if (canvasBooth) {
                            if (self.selectedBooths) {
                                var idx = self.selectedBooths.indexOf(canvasBooth);
                                if (idx !== -1) self.selectedBooths.splice(idx, 1);
                            }
                            canvasBooth.remove();
                        }
                        var sidebarItem = document.querySelector('.booth-number-item[data-booth-id="' + id + '"]');
                        if (sidebarItem) sidebarItem.remove();
                    });
                    self.updateSelectionBoundingBox && self.updateSelectionBoundingBox();
                    self.saveState && self.saveState();
                    var msg = (delData.deleted && delData.deleted.length) ? delData.deleted.length + ' booth(s) deleted.' : count + ' booth(s) deleted.';
                    if (delData.booked_booths_skipped && delData.booked_booths_skipped.length > 0) {
                        msg += ' ' + delData.booked_booths_skipped.length + ' booth(s) with bookings were skipped.';
                    }
                    Swal.fire({ icon: 'success', title: 'Deleted', text: msg, timer: 3000, showConfirmButton: false });
                })
                .catch(function(err) {
                    console.error('Delete booths error:', err);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete booths.' });
                });
            });
        })
        .catch(function(err) {
            console.error('Check bookings error:', err);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to check bookings.' });
        });
    },
    
    // Open Update Booth Info modal on canvas: fetch booth data and populate form (no redirect)
    openBoothInfoModal: function(boothId) {
        const self = this;
        const modal = document.getElementById('canvasBoothInfoModal');
        const form = document.getElementById('canvasBoothInfoForm');
        if (!modal || !form) return;
        
        $('#canvasModalTitleText').text('Update Booth Info');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(modal).show();
        } else {
            $('#canvasBoothInfoModal').modal('show');
        }
        
        const modalBody = $('#canvasBoothInfoModal .modal-body');
        modalBody.css('position', 'relative');
        if (!document.getElementById('canvasBoothInfoLoading')) {
            modalBody.prepend('<div id="canvasBoothInfoLoading" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.9); z-index: 1000; display: flex; align-items: center; justify-content: center; border-radius: 12px;"><div class="text-center"><div class="spinner-border text-primary mb-2" role="status"></div><p class="text-muted">Loading booth data...</p></div></div>');
        }
        
        const url = FPD.urls.booths + '/' + boothId + '?json=1';
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : ''
            },
            credentials: 'same-origin'
        })
        .then(function(response) {
            const ct = response.headers.get('content-type');
            if (!ct || !ct.includes('application/json')) {
                return response.text().then(function(t) { throw new Error('Expected JSON'); });
            }
            if (!response.ok) {
                return response.json().then(function(err) { throw new Error(err.message || 'HTTP ' + response.status); });
            }
            return response.json();
        })
        .then(function(data) {
            $('#canvasBoothInfoLoading').remove();
            if (data.error) throw new Error(data.message || data.error);
            
            $('#canvasBoothId').val(data.id || '');
            $('#canvas_booth_number').val(data.booth_number || '');
            $('#canvas_floor_plan_id').val(data.floor_plan_id || '');
            $('#canvas_booth_type_id').val(data.booth_type_id || '');
            $('#canvas_type').val(data.type || '1');
            $('#canvas_price').val(data.price || '');
            $('#canvas_status').val(data.status || '1');
            $('#canvas_client_id').val(data.client_id || '');
            $('#canvas_category_id').val(data.category_id || '');
            $('#canvas_area_sqm').val(data.area_sqm || '');
            $('#canvas_capacity').val(data.capacity || '');
            $('#canvas_electricity_power').val(data.electricity_power || '');
            $('#canvas_description').val(data.description || '');
            $('#canvas_features').val(data.features || '');
            $('#canvas_notes').val(data.notes || '');
            
            if (data.booth_image) {
                $('#canvasImagePreview').attr('src', data.booth_image);
                $('#canvasImagePreviewContainer').show();
                $('#canvasImageUploadArea').hide();
            } else {
                $('#canvasImagePreviewContainer').hide();
                $('#canvasImageUploadArea').show();
            }
            
            var basicTab = document.getElementById('canvas-basic-tab');
            if (basicTab && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                bootstrap.Tab.getOrCreateInstance(basicTab).show();
            } else {
                $('#canvas-basic-tab').tab('show');
            }
            var bsModal = (typeof bootstrap !== 'undefined' && bootstrap.Modal) ? bootstrap.Modal.getOrCreateInstance(modal) : null;
            if (bsModal && !modal.classList.contains('show')) bsModal.show();
            else if (!modal.classList.contains('show')) $('#canvasBoothInfoModal').modal('show');
        })
        .catch(function(err) {
            $('#canvasBoothInfoLoading').remove();
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modal).hide();
            } else {
                $('#canvasBoothInfoModal').modal('hide');
            }
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Failed to load booth data', text: err.message || 'Please try again.' });
            } else {
                alert('Failed to load booth data: ' + (err.message || 'Please try again.'));
            }
        });
    },
    
    clearCanvasBoothImagePreview: function() {
        $('#canvas_booth_image').val('');
        $('#canvasImagePreview').attr('src', '');
        $('#canvasImagePreviewContainer').hide();
        $('#canvasImageUploadArea').show();
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
        const floorPlanId = FPD.floorPlanId;
        
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
            let currentBackgroundColor, currentBorderColor, currentTextColor, currentFontWeight, currentFontFamily, currentTextAlign, currentBoxShadow;
            
            // Use saved zone settings if available, otherwise use current booth values
            if (data.status === 200 && data.settings) {
                // Get current floor plan ID for cache key
                const floorPlanId = FPD.floorPlanId;
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
                // Appearance/Color fields
                currentBackgroundColor = data.settings.background_color || data.settings.backgroundColor || '';
                currentBorderColor = data.settings.border_color || data.settings.borderColor || '';
                currentTextColor = data.settings.text_color || data.settings.textColor || '';
                currentFontWeight = data.settings.font_weight || data.settings.fontWeight || '';
                currentFontFamily = data.settings.font_family || data.settings.fontFamily || '';
                currentTextAlign = data.settings.text_align || data.settings.textAlign || '';
                currentBoxShadow = data.settings.box_shadow || data.settings.boxShadow || '';
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
                currentBorderRadius = parseFloat(firstBooth.style.borderRadius) || parseFloat(firstBooth.getAttribute('data-border-radius')) || 6;
                currentBorderWidth = parseFloat(firstBooth.style.borderWidth) || parseFloat(firstBooth.getAttribute('data-border-width')) || 2;
                currentOpacity = parseFloat(firstBooth.style.opacity) || parseFloat(firstBooth.getAttribute('data-opacity')) || 1.00;
                currentPrice = 500; // Default price if not in settings
                // Get appearance from first booth (check both style and data attributes)
                currentBackgroundColor = firstBooth.style.backgroundColor || firstBooth.getAttribute('data-background-color') || '';
                currentBorderColor = firstBooth.style.borderColor || firstBooth.getAttribute('data-border-color') || '';
                currentTextColor = firstBooth.style.color || firstBooth.getAttribute('data-text-color') || '';
                currentFontWeight = firstBooth.style.fontWeight || firstBooth.getAttribute('data-font-weight') || '';
                currentFontFamily = firstBooth.style.fontFamily || firstBooth.getAttribute('data-font-family') || '';
                currentTextAlign = firstBooth.style.textAlign || firstBooth.getAttribute('data-text-align') || '';
                currentBoxShadow = firstBooth.style.boxShadow || firstBooth.getAttribute('data-box-shadow') || '';
            }
            
            self.showZoneSettingsModal(zoneName, zoneBooths.length, {
                width: currentWidth,
                height: currentHeight,
                rotation: currentRotation,
                zIndex: currentZIndex,
                borderRadius: currentBorderRadius,
                borderWidth: currentBorderWidth,
                opacity: currentOpacity,
                price: currentPrice || 500,
                background_color: currentBackgroundColor,
                border_color: currentBorderColor,
                text_color: currentTextColor,
                font_weight: currentFontWeight,
                font_family: currentFontFamily,
                text_align: currentTextAlign,
                box_shadow: currentBoxShadow
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
            const currentBorderRadius = parseFloat(firstBooth.style.borderRadius) || parseFloat(firstBooth.getAttribute('data-border-radius')) || 6;
            const currentBorderWidth = parseFloat(firstBooth.style.borderWidth) || parseFloat(firstBooth.getAttribute('data-border-width')) || 2;
            const currentOpacity = parseFloat(firstBooth.style.opacity) || parseFloat(firstBooth.getAttribute('data-opacity')) || 1.00;
            const currentPrice = 500; // Default price in fallback case
            // Get appearance from first booth (check both style and data attributes)
            const currentBackgroundColor = firstBooth.style.backgroundColor || firstBooth.getAttribute('data-background-color') || '';
            const currentBorderColor = firstBooth.style.borderColor || firstBooth.getAttribute('data-border-color') || '';
            const currentTextColor = firstBooth.style.color || firstBooth.getAttribute('data-text-color') || '';
            const currentFontWeight = firstBooth.style.fontWeight || firstBooth.getAttribute('data-font-weight') || '';
            const currentFontFamily = firstBooth.style.fontFamily || firstBooth.getAttribute('data-font-family') || '';
            const currentTextAlign = firstBooth.style.textAlign || firstBooth.getAttribute('data-text-align') || '';
            const currentBoxShadow = firstBooth.style.boxShadow || firstBooth.getAttribute('data-box-shadow') || '';
            
            self.showZoneSettingsModal(zoneName, zoneBooths.length, {
                width: currentWidth,
                height: currentHeight,
                rotation: currentRotation,
                zIndex: currentZIndex,
                borderRadius: currentBorderRadius,
                borderWidth: currentBorderWidth,
                opacity: currentOpacity,
                price: currentPrice,
                background_color: currentBackgroundColor,
                border_color: currentBorderColor,
                text_color: currentTextColor,
                font_weight: currentFontWeight,
                font_family: currentFontFamily,
                text_align: currentTextAlign,
                box_shadow: currentBoxShadow
            });
        });
    },
    
    // Show zone settings modal with separated Shape/Layout and Appearance groups
    showZoneSettingsModal: function(zoneName, boothCount, settings) {
        const self = this;
        
        // Create settings modal with two separate groups
        var modalHtml = '<div style="text-align: left; max-width: 700px; margin: 0 auto;">';
        modalHtml += '<p style="margin-bottom: 20px; color: #666; padding: 12px; background: #f8f9fa; border-radius: 8px;">';
        modalHtml += 'Found <strong>' + boothCount + '</strong> booth(s) in Zone ' + zoneName + '.';
        modalHtml += '<br><small><strong>Instant preview:</strong> Changing any number or color applies to all booths in this zone immediately. Use Save to persist.</small>';
        modalHtml += '</p>';
        
        // Group 0: Zone General Information (Zone About)
        modalHtml += '<div style="background: #fff; border: 2px solid #10b981; border-radius: 12px; padding: 20px; margin-bottom: 20px;">';
        modalHtml += '<h4 style="margin: 0 0 15px 0; color: #10b981; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">';
        modalHtml += '<i class="fas fa-info-circle"></i> Zone Information';
        modalHtml += '</h4>';
        modalHtml += '<div style="margin-bottom: 15px;">';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-file-alt"></i> Zone About / Description';
        modalHtml += '</label>';
        modalHtml += '<textarea id="zoneAbout" class="swal2-textarea" placeholder="Describe this zone, its features, location, benefits, etc." style="width: 100%; margin: 0; padding: 12px; border-radius: 8px; border: 1px solid #ddd; min-height: 100px;">' + (settings.zone_about || '') + '</textarea>';
        modalHtml += '<small style="color: #999; display: block; margin-top: 5px;">This information helps users understand the zone features when booking.</small>';
        modalHtml += '</div>';
        modalHtml += '<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">';
        modalHtml += '<button id="saveZoneInfoBtn" class="swal2-confirm swal2-styled" style="background-color: #10b981; width: 100%; padding: 12px; font-weight: 600;">';
        modalHtml += '<i class="fas fa-save mr-2"></i>Save Zone Information';
        modalHtml += '</button>';
        modalHtml += '</div>';
        modalHtml += '</div>'; // End Zone Information group
        
        // Group 1: Shape & Layout Settings (W, H, R, Z)
        modalHtml += '<div style="background: #fff; border: 2px solid #667eea; border-radius: 12px; padding: 20px; margin-bottom: 20px;">';
        modalHtml += '<h4 style="margin: 0 0 15px 0; color: #667eea; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">';
        modalHtml += '<i class="fas fa-ruler-combined"></i> Shape & Layout Settings';
        modalHtml += '</h4>';
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-arrows-alt-h"></i> Width (W)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneWidth" class="swal2-input" value="' + settings.width + '" min="5" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-arrows-alt-v"></i> Height (H)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneHeight" class="swal2-input" value="' + settings.height + '" min="5" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-redo"></i> Rotation (R)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneRotation" class="swal2-input" value="' + settings.rotation + '" min="-360" max="360" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-layer-group"></i> Z-Index (Z)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneZIndex" class="swal2-input" value="' + settings.zIndex + '" min="1" max="1000" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">';
        modalHtml += '<button id="saveShapeLayoutBtn" class="swal2-confirm swal2-styled" style="background-color: #667eea; width: 100%; padding: 12px; font-weight: 600;">';
        modalHtml += '<i class="fas fa-save mr-2"></i>Save Shape & Layout';
        modalHtml += '</button>';
        modalHtml += '</div>';
        modalHtml += '</div>'; // End Shape & Layout group
        
        // Group 2: Appearance & Style Settings
        modalHtml += '<div style="background: #fff; border: 2px solid #f6c23e; border-radius: 12px; padding: 20px; margin-bottom: 20px;">';
        modalHtml += '<h4 style="margin: 0 0 15px 0; color: #f6c23e; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">';
        modalHtml += '<i class="fas fa-palette"></i> Appearance & Style Settings';
        modalHtml += '</h4>';
        
        // Colors
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-fill-drip"></i> Background Color';
        modalHtml += '</label>';
        modalHtml += '<div style="display: flex; gap: 8px; align-items: center;">';
        modalHtml += '<input type="color" id="zoneAppearanceBackgroundColor" value="' + (settings.background_color || '#ffffff') + '" style="width: 60px; height: 38px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">';
        modalHtml += '<input type="text" id="zoneAppearanceBackgroundColorText" class="swal2-input" value="' + (settings.background_color || '') + '" placeholder="#ffffff" style="flex: 1;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-border-all"></i> Border Color';
        modalHtml += '</label>';
        modalHtml += '<div style="display: flex; gap: 8px; align-items: center;">';
        modalHtml += '<input type="color" id="zoneAppearanceBorderColor" value="' + (settings.border_color || '#007bff') + '" style="width: 60px; height: 38px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">';
        modalHtml += '<input type="text" id="zoneAppearanceBorderColorText" class="swal2-input" value="' + (settings.border_color || '') + '" placeholder="#007bff" style="flex: 1;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div style="margin-bottom: 15px;">';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-font"></i> Text Color';
        modalHtml += '</label>';
        modalHtml += '<div style="display: flex; gap: 8px; align-items: center;">';
        modalHtml += '<input type="color" id="zoneAppearanceTextColor" value="' + (settings.text_color || '#000000') + '" style="width: 60px; height: 38px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">';
        modalHtml += '<input type="text" id="zoneAppearanceTextColorText" class="swal2-input" value="' + (settings.text_color || '') + '" placeholder="#000000" style="flex: 1;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        
        // Border & Opacity
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-circle"></i> Border Radius';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneAppearanceBorderRadius" class="swal2-input" value="' + settings.borderRadius + '" min="0" max="50" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-border-style"></i> Border Width';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneAppearanceBorderWidth" class="swal2-input" value="' + settings.borderWidth + '" min="0" max="10" step="1" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div style="margin-bottom: 15px;">';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-adjust"></i> Opacity (0.0 - 1.0)';
        modalHtml += '</label>';
        modalHtml += '<input type="number" id="zoneAppearanceOpacity" class="swal2-input" value="' + settings.opacity + '" min="0" max="1" step="0.1" style="width: 100%;">';
        modalHtml += '</div>';
        
        // Font Settings
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-bold"></i> Font Weight';
        modalHtml += '</label>';
        modalHtml += '<select id="zoneAppearanceFontWeight" class="swal2-input" style="width: 100%;">';
        modalHtml += '<option value="">Use Default</option>';
        modalHtml += '<option value="300"' + (settings.font_weight === '300' ? ' selected' : '') + '>Light (300)</option>';
        modalHtml += '<option value="400"' + (settings.font_weight === '400' ? ' selected' : '') + '>Normal (400)</option>';
        modalHtml += '<option value="600"' + (settings.font_weight === '600' ? ' selected' : '') + '>Semi-Bold (600)</option>';
        modalHtml += '<option value="700"' + (settings.font_weight === '700' ? ' selected' : '') + '>Bold (700)</option>';
        modalHtml += '<option value="800"' + (settings.font_weight === '800' ? ' selected' : '') + '>Extra Bold (800)</option>';
        modalHtml += '</select>';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-align-center"></i> Text Align';
        modalHtml += '</label>';
        modalHtml += '<select id="zoneAppearanceTextAlign" class="swal2-input" style="width: 100%;">';
        modalHtml += '<option value="">Use Default</option>';
        modalHtml += '<option value="left"' + (settings.text_align === 'left' ? ' selected' : '') + '>Left</option>';
        modalHtml += '<option value="center"' + (settings.text_align === 'center' ? ' selected' : '') + '>Center</option>';
        modalHtml += '<option value="right"' + (settings.text_align === 'right' ? ' selected' : '') + '>Right</option>';
        modalHtml += '</select>';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div style="margin-bottom: 15px;">';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-font"></i> Font Family';
        modalHtml += '</label>';
        modalHtml += '<input type="text" id="zoneAppearanceFontFamily" class="swal2-input" value="' + (settings.font_family || '') + '" placeholder="Arial, sans-serif" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '<div style="margin-bottom: 15px;">';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-shadow"></i> Box Shadow';
        modalHtml += '</label>';
        modalHtml += '<input type="text" id="zoneAppearanceBoxShadow" class="swal2-input" value="' + (settings.box_shadow || '') + '" placeholder="0 2px 8px rgba(0,0,0,0.2)" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e0e0e0;">';
        modalHtml += '<button id="saveAppearanceBtn" class="swal2-confirm swal2-styled" style="background-color: #f6c23e; width: 100%; padding: 12px; font-weight: 600;">';
        modalHtml += '<i class="fas fa-save mr-2"></i>Save Appearance & Style';
        modalHtml += '</button>';
        modalHtml += '</div>';
        modalHtml += '</div>'; // End Appearance & Style group
        
        modalHtml += '<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 20px; margin-bottom: 20px;">';
        modalHtml += '<button id="saveAllZoneBtn" class="swal2-confirm swal2-styled" style="background: #fff; color: #667eea; width: 100%; padding: 14px; font-weight: 700; border: 2px solid #fff;">';
        modalHtml += '<i class="fas fa-save mr-2"></i>Save All Zone Settings';
        modalHtml += '</button>';
        modalHtml += '<p style="font-size: 11px; color: rgba(255,255,255,0.9); margin: 10px 0 0 0;">Saves Zone Info, Shape & Layout, and Appearance & Style for Zone ' + zoneName + '.</p>';
        modalHtml += '</div>';
        
        modalHtml += '<p style="font-size: 11px; color: #999; margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 6px;">';
        modalHtml += '<i class="fas fa-info-circle"></i> <strong>Note:</strong> Changes apply instantly on canvas. Use the Save buttons above to persist. "Save All" persists everything for this zone.';
        modalHtml += '</p>';
        modalHtml += '</div>';
        
        Swal.fire({
            title: 'Zone ' + zoneName + ' Settings',
            html: modalHtml,
            icon: 'info',
            showCancelButton: true,
            cancelButtonText: 'Close',
            confirmButtonText: false,
            width: '750px',
            didOpen: () => {
                // Hide default confirm button
                const confirmBtn = Swal.getConfirmButton();
                if (confirmBtn) confirmBtn.style.display = 'none';
                
                // Sync color pickers with text inputs
                const bgColorPicker = document.getElementById('zoneAppearanceBackgroundColor');
                const bgColorText = document.getElementById('zoneAppearanceBackgroundColorText');
                const borderColorPicker = document.getElementById('zoneAppearanceBorderColor');
                const borderColorText = document.getElementById('zoneAppearanceBorderColorText');
                const textColorPicker = document.getElementById('zoneAppearanceTextColor');
                const textColorText = document.getElementById('zoneAppearanceTextColorText');
                
                if (bgColorPicker && bgColorText) {
                    bgColorPicker.addEventListener('input', function() {
                        bgColorText.value = this.value;
                    });
                    bgColorText.addEventListener('input', function() {
                        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                            bgColorPicker.value = this.value;
                        }
                    });
                }
                if (borderColorPicker && borderColorText) {
                    borderColorPicker.addEventListener('input', function() {
                        borderColorText.value = this.value;
                    });
                    borderColorText.addEventListener('input', function() {
                        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                            borderColorPicker.value = this.value;
                        }
                    });
                }
                if (textColorPicker && textColorText) {
                    textColorPicker.addEventListener('input', function() {
                        textColorText.value = this.value;
                    });
                    textColorText.addEventListener('input', function() {
                        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                            textColorPicker.value = this.value;
                        }
                    });
                }
                
                // Live preview: read shape from form and apply to canvas
                function readShapeFromForm() {
                    const w = document.getElementById('zoneWidth');
                    const h = document.getElementById('zoneHeight');
                    const r = document.getElementById('zoneRotation');
                    const z = document.getElementById('zoneZIndex');
                    return {
                        width: w ? (parseFloat(w.value) || 80) : 80,
                        height: h ? (parseFloat(h.value) || 50) : 50,
                        rotation: r ? (parseFloat(r.value) || 0) : 0,
                        zIndex: z ? (parseInt(z.value, 10) || 10) : 10
                    };
                }
                function readAppearanceFromForm() {
                    const bgText = document.getElementById('zoneAppearanceBackgroundColorText');
                    const borderText = document.getElementById('zoneAppearanceBorderColorText');
                    const textColorEl = document.getElementById('zoneAppearanceTextColorText');
                    const bgPicker = document.getElementById('zoneAppearanceBackgroundColor');
                    const borderPicker = document.getElementById('zoneAppearanceBorderColor');
                    const textPicker = document.getElementById('zoneAppearanceTextColor');
                    const fontWeightEl = document.getElementById('zoneAppearanceFontWeight');
                    const fontFamilyEl = document.getElementById('zoneAppearanceFontFamily');
                    const textAlignEl = document.getElementById('zoneAppearanceTextAlign');
                    const boxShadowEl = document.getElementById('zoneAppearanceBoxShadow');
                    const borderRadiusEl = document.getElementById('zoneAppearanceBorderRadius');
                    const borderWidthEl = document.getElementById('zoneAppearanceBorderWidth');
                    const opacityEl = document.getElementById('zoneAppearanceOpacity');
                    return {
                        background_color: (bgText && bgText.value.trim()) || (bgPicker && bgPicker.value) || '',
                        border_color: (borderText && borderText.value.trim()) || (borderPicker && borderPicker.value) || '',
                        text_color: (textColorEl && textColorEl.value.trim()) || (textPicker && textPicker.value) || '',
                        font_weight: (fontWeightEl && fontWeightEl.value) ? (fontWeightEl.value === '' ? null : fontWeightEl.value) : null,
                        font_family: (fontFamilyEl && fontFamilyEl.value.trim()) || null,
                        text_align: (textAlignEl && textAlignEl.value) ? (textAlignEl.value === '' ? null : textAlignEl.value) : null,
                        box_shadow: (boxShadowEl && boxShadowEl.value.trim()) || null,
                        borderRadius: borderRadiusEl ? (parseFloat(borderRadiusEl.value) || 0) : 0,
                        borderWidth: borderWidthEl ? (parseFloat(borderWidthEl.value) || 2) : 2,
                        opacity: opacityEl ? (parseFloat(opacityEl.value) || 1) : 1
                    };
                }
                function applyShapeLive() {
                    self.applyZoneShapeLayoutToCanvas(zoneName, readShapeFromForm());
                }
                function applyAppearanceLive() {
                    self.applyZoneAppearanceToCanvas(zoneName, readAppearanceFromForm());
                }
                [ 'zoneWidth', 'zoneHeight', 'zoneRotation', 'zoneZIndex' ].forEach(function(id) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', applyShapeLive);
                        el.addEventListener('change', applyShapeLive);
                    }
                });
                [ 'zoneAppearanceBackgroundColor', 'zoneAppearanceBackgroundColorText', 'zoneAppearanceBorderColor', 'zoneAppearanceBorderColorText', 'zoneAppearanceTextColor', 'zoneAppearanceTextColorText', 'zoneAppearanceFontWeight', 'zoneAppearanceFontFamily', 'zoneAppearanceTextAlign', 'zoneAppearanceBoxShadow', 'zoneAppearanceBorderRadius', 'zoneAppearanceBorderWidth', 'zoneAppearanceOpacity' ].forEach(function(id) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', applyAppearanceLive);
                        el.addEventListener('change', applyAppearanceLive);
                    }
                });
                
                // Save Zone Information button
                document.getElementById('saveZoneInfoBtn').addEventListener('click', function() {
                    const zoneAbout = document.getElementById('zoneAbout').value.trim();
                    self.saveZoneInfoSettings(zoneName, { zone_about: zoneAbout });
                });
                
                // Save Shape & Layout button
                document.getElementById('saveShapeLayoutBtn').addEventListener('click', function() {
                    const width = document.getElementById('zoneWidth').value;
                    const height = document.getElementById('zoneHeight').value;
                    const rotation = document.getElementById('zoneRotation').value;
                    const zIndex = document.getElementById('zoneZIndex').value;
                    
                    if (!width || !height || rotation === '' || !zIndex) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please fill in all Shape & Layout fields (Width, Height, Rotation, Z-Index)'
                        });
                        return;
                    }
                    
                    const shapeSettings = {
                        width: parseFloat(width),
                        height: parseFloat(height),
                        rotation: parseFloat(rotation),
                        zIndex: parseInt(zIndex)
                    };
                    
                    self.saveZoneShapeLayoutSettings(zoneName, shapeSettings);
                });
                
                // Save Appearance & Style button
                document.getElementById('saveAppearanceBtn').addEventListener('click', function() {
                    const bgText = document.getElementById('zoneAppearanceBackgroundColorText');
                    const borderText = document.getElementById('zoneAppearanceBorderColorText');
                    const textColorEl = document.getElementById('zoneAppearanceTextColorText');
                    const backgroundColor = (bgText && bgText.value.trim()) || (document.getElementById('zoneAppearanceBackgroundColor') && document.getElementById('zoneAppearanceBackgroundColor').value) || null;
                    const borderColor = (borderText && borderText.value.trim()) || (document.getElementById('zoneAppearanceBorderColor') && document.getElementById('zoneAppearanceBorderColor').value) || null;
                    const textColor = (textColorEl && textColorEl.value.trim()) || (document.getElementById('zoneAppearanceTextColor') && document.getElementById('zoneAppearanceTextColor').value) || null;
                    const fontWeightEl = document.getElementById('zoneAppearanceFontWeight');
                    const fontWeight = (fontWeightEl && fontWeightEl.value.trim()) ? (fontWeightEl.value === 'Use Default' || fontWeightEl.value === '' ? null : fontWeightEl.value) : null;
                    const fontFamilyEl = document.getElementById('zoneAppearanceFontFamily');
                    const fontFamily = (fontFamilyEl && fontFamilyEl.value.trim()) ? (fontFamilyEl.value === 'Use Default' || fontFamilyEl.value === '' ? null : fontFamilyEl.value) : null;
                    const textAlignEl = document.getElementById('zoneAppearanceTextAlign');
                    const textAlign = (textAlignEl && textAlignEl.value.trim()) ? (textAlignEl.value === 'Use Default' || textAlignEl.value === '' ? null : textAlignEl.value) : null;
                    const boxShadow = document.getElementById('zoneAppearanceBoxShadow') ? document.getElementById('zoneAppearanceBoxShadow').value.trim() || null : null;
                    const borderRadiusEl = document.getElementById('zoneAppearanceBorderRadius');
                    const borderWidthEl = document.getElementById('zoneAppearanceBorderWidth');
                    const opacityEl = document.getElementById('zoneAppearanceOpacity');
                    const borderRadius = borderRadiusEl ? (parseFloat(borderRadiusEl.value) || 0) : 0;
                    const borderWidth = borderWidthEl ? (parseFloat(borderWidthEl.value) || 0) : 2;
                    const opacity = opacityEl ? (parseFloat(opacityEl.value) || 1) : 1;
                    
                    const appearanceSettings = {
                        background_color: backgroundColor,
                        border_color: borderColor,
                        text_color: textColor,
                        font_weight: fontWeight,
                        font_family: fontFamily,
                        text_align: textAlign,
                        box_shadow: boxShadow,
                        borderRadius: borderRadius,
                        borderWidth: borderWidth,
                        opacity: opacity
                    };
                    
                    self.saveZoneAppearanceSettings(zoneName, appearanceSettings);
                });
                
                // Save All Zone Settings (Info + Shape + Appearance in one)
                const saveAllZoneBtn = document.getElementById('saveAllZoneBtn');
                if (saveAllZoneBtn) {
                    saveAllZoneBtn.addEventListener('click', function() {
                        const zoneAbout = document.getElementById('zoneAbout') ? document.getElementById('zoneAbout').value.trim() : '';
                        const shape = readShapeFromForm();
                        const appearance = readAppearanceFromForm();
                        self.saveAllZoneSettings(zoneName, {
                            zone_about: zoneAbout,
                            width: shape.width,
                            height: shape.height,
                            rotation: shape.rotation,
                            zIndex: shape.zIndex,
                            borderRadius: appearance.borderRadius,
                            borderWidth: appearance.borderWidth,
                            opacity: appearance.opacity,
                            background_color: appearance.background_color,
                            border_color: appearance.border_color,
                            text_color: appearance.text_color,
                            font_weight: appearance.font_weight,
                            font_family: appearance.font_family,
                            text_align: appearance.text_align,
                            box_shadow: appearance.box_shadow
                        });
                    });
                }
            }
        });
    },
    
    // Apply zone shape/layout to canvas only (instant preview, no save)
    applyZoneShapeLayoutToCanvas: function(zoneName, shapeSettings) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        const canvasBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        const width = Math.max(5, parseFloat(shapeSettings.width) || 80);
        const height = Math.max(5, parseFloat(shapeSettings.height) || 50);
        const rotation = (parseFloat(shapeSettings.rotation) || 0) % 360;
        const zIndex = Math.max(1, Math.min(1000, parseInt(shapeSettings.zIndex, 10) || 10));
        canvasBooths.forEach(function(boothElement) {
            boothElement.style.width = width + 'px';
            boothElement.style.height = height + 'px';
            boothElement.style.transform = 'rotate(' + rotation + 'deg)';
            boothElement.style.zIndex = zIndex;
            boothElement.setAttribute('data-width', width);
            boothElement.setAttribute('data-height', height);
            boothElement.setAttribute('data-rotation', rotation);
            boothElement.setAttribute('data-z-index', zIndex);
            if (self.updateResizeHandlesSize) self.updateResizeHandlesSize(boothElement);
            if (self.updateRotationIndicator) self.updateRotationIndicator(boothElement);
            const userFontSize = parseFloat(boothElement.getAttribute('data-font-size')) || self.defaultBoothFontSize || 14;
            const calculatedFontSize = Math.min(userFontSize, Math.max(8, width * 0.45));
            boothElement.style.fontSize = calculatedFontSize + 'px';
            boothElement.setAttribute('data-calculated-font-size', calculatedFontSize);
        });
    },
    
    // Apply zone appearance/style to canvas only (instant preview, no save)
    applyZoneAppearanceToCanvas: function(zoneName, appearanceSettings) {
        const canvas = document.getElementById('print');
        if (!canvas) return;
        const canvasBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        canvasBooths.forEach(function(boothElement) {
            boothElement.classList.add('has-custom-colors');
            boothElement.style.backgroundColor = appearanceSettings.background_color || '';
            boothElement.style.borderColor = appearanceSettings.border_color || '';
            boothElement.style.color = appearanceSettings.text_color || '';
            boothElement.style.fontWeight = appearanceSettings.font_weight || '';
            boothElement.style.fontFamily = appearanceSettings.font_family || '';
            boothElement.style.textAlign = appearanceSettings.text_align || '';
            boothElement.style.boxShadow = appearanceSettings.box_shadow || '';
            const br = appearanceSettings.borderRadius != null ? parseFloat(appearanceSettings.borderRadius) : 0;
            boothElement.style.borderRadius = br + 'px';
            const bw = appearanceSettings.borderWidth != null ? parseFloat(appearanceSettings.borderWidth) : 2;
            boothElement.style.borderWidth = bw + 'px';
            boothElement.style.borderStyle = 'solid';
            const op = appearanceSettings.opacity != null ? parseFloat(appearanceSettings.opacity) : 1;
            boothElement.style.opacity = op;
            boothElement.setAttribute('data-background-color', appearanceSettings.background_color || '');
            boothElement.setAttribute('data-border-color', appearanceSettings.border_color || '');
            boothElement.setAttribute('data-text-color', appearanceSettings.text_color || '');
            boothElement.setAttribute('data-font-weight', appearanceSettings.font_weight || '');
            boothElement.setAttribute('data-font-family', appearanceSettings.font_family || '');
            boothElement.setAttribute('data-text-align', appearanceSettings.text_align || '');
            boothElement.setAttribute('data-box-shadow', appearanceSettings.box_shadow || '');
            boothElement.setAttribute('data-border-radius', br);
            boothElement.setAttribute('data-border-width', bw);
            boothElement.setAttribute('data-opacity', op);
        });
    },
    
    // Save Zone Shape & Layout Settings (W, H, R, Z only) — applies to entire zone (sidebar + canvas)
    saveZoneShapeLayoutSettings: function(zoneName, shapeSettings) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        const sidebarContainer = document.getElementById('boothNumbersContainer');
        const sidebarBooths = sidebarContainer ? sidebarContainer.querySelectorAll('.booth-number-item[data-booth-zone="' + zoneName + '"]') : [];
        const canvasBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        const canvasBoothIds = new Set();
        canvasBooths.forEach(function(booth) {
            const boothId = booth.getAttribute('data-booth-id');
            if (boothId) canvasBoothIds.add(boothId);
        });
        
        const boothsToSave = [];
        let updatedCount = 0;
        const width = Math.max(5, shapeSettings.width);
        const height = Math.max(5, shapeSettings.height);
        const rotation = shapeSettings.rotation % 360;
        const zIndex = Math.max(1, Math.min(1000, shapeSettings.zIndex));
        
        // 1) Sidebar booths (not on canvas): save shape to DB only
        sidebarBooths.forEach(function(boothItem) {
            const boothId = boothItem.getAttribute('data-booth-id');
            if (!boothId || canvasBoothIds.has(boothId)) return;
            boothsToSave.push({
                id: parseInt(boothId),
                width: width,
                height: height,
                rotation: rotation,
                z_index: zIndex
            });
            updatedCount++;
        });
        
        // 2) Canvas booths: apply to DOM and save to DB (entire zone)
        canvasBooths.forEach(function(boothElement) {
            const boothId = boothElement.getAttribute('data-booth-id');
            if (!boothId) return;
            boothElement.style.width = width + 'px';
            boothElement.style.height = height + 'px';
            boothElement.style.transform = 'rotate(' + rotation + 'deg)';
            boothElement.style.zIndex = zIndex;
            boothElement.setAttribute('data-width', width);
            boothElement.setAttribute('data-height', height);
            boothElement.setAttribute('data-rotation', rotation);
            boothElement.setAttribute('data-z-index', zIndex);
            self.updateResizeHandlesSize(boothElement);
            self.updateRotationIndicator(boothElement);
            const userFontSize = parseFloat(boothElement.getAttribute('data-font-size')) || self.defaultBoothFontSize;
            const calculatedFontSize = Math.min(userFontSize, Math.max(8, width * 0.45));
            boothElement.style.fontSize = calculatedFontSize + 'px';
            boothElement.setAttribute('data-calculated-font-size', calculatedFontSize);
            const x = parseFloat(boothElement.style.left) || 0;
            const y = parseFloat(boothElement.style.top) || 0;
            boothsToSave.push({
                id: parseInt(boothId),
                position_x: x,
                position_y: y,
                width: width,
                height: height,
                rotation: rotation,
                z_index: zIndex,
                font_size: calculatedFontSize,
                border_width: boothElement.getAttribute('data-border-width') || 2,
                border_radius: boothElement.getAttribute('data-border-radius') || 6,
                opacity: boothElement.getAttribute('data-opacity') || 1,
                background_color: boothElement.style.backgroundColor || boothElement.getAttribute('data-background-color') || null,
                border_color: boothElement.style.borderColor || boothElement.getAttribute('data-border-color') || null,
                text_color: boothElement.style.color || boothElement.getAttribute('data-text-color') || null,
                font_weight: boothElement.getAttribute('data-font-weight') || null,
                font_family: boothElement.style.fontFamily || boothElement.getAttribute('data-font-family') || null,
                text_align: boothElement.getAttribute('data-text-align') || null,
                box_shadow: boothElement.style.boxShadow || boothElement.getAttribute('data-box-shadow') || null
            });
            updatedCount++;
        });
        
        if (updatedCount === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Booths in Zone',
                text: 'No booths from Zone ' + zoneName + ' found. Settings will be saved for future booths added to this zone.'
            });
            return;
        }
        
        // Get current floor plan ID
        const floorPlanId = FPD.floorPlanId;
        if (!floorPlanId) {
            Swal.fire({
                icon: 'warning',
                title: 'Floor Plan Required',
                text: 'Please select a floor plan first before saving zone settings.'
            });
            return;
        }
        
        // Load existing zone settings to preserve appearance settings
        const zoneSettingsUrl = '/booths/zone-settings/' + encodeURIComponent(zoneName) + 
            (floorPlanId ? '?floor_plan_id=' + floorPlanId : '');
        
        fetch(zoneSettingsUrl)
            .then(response => response.json())
            .then(function(data) {
                const existingSettings = data.settings || {};
                
                // Merge shape settings with existing appearance settings
                const settingsToSave = {
                    width: shapeSettings.width,
                    height: shapeSettings.height,
                    rotation: shapeSettings.rotation,
                    zIndex: shapeSettings.zIndex,
                    // Preserve existing appearance settings
                    borderRadius: existingSettings.borderRadius || 6,
                    borderWidth: existingSettings.borderWidth || 2,
                    opacity: existingSettings.opacity || 1.0,
                    price: existingSettings.price || 500,
                    background_color: existingSettings.background_color || null,
                    border_color: existingSettings.border_color || null,
                    text_color: existingSettings.text_color || null,
                    font_weight: existingSettings.font_weight || null,
                    font_family: existingSettings.font_family || null,
                    text_align: existingSettings.text_align || null,
                    box_shadow: existingSettings.box_shadow || null,
                    floor_plan_id: floorPlanId
                };
                
                // Save to database
                return fetch('/booths/zone-settings/' + encodeURIComponent(zoneName), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(settingsToSave)
                });
            })
            .then(response => response.json())
            .then(function(data) {
                if (data.status === 200) {
                    // Update cache
                    const cacheKey = floorPlanId ? floorPlanId + '_' + zoneName : 'global_' + zoneName;
                    if (self.zoneSettingsCache[cacheKey]) {
                        self.zoneSettingsCache[cacheKey].width = shapeSettings.width;
                        self.zoneSettingsCache[cacheKey].height = shapeSettings.height;
                        self.zoneSettingsCache[cacheKey].rotation = shapeSettings.rotation;
                        self.zoneSettingsCache[cacheKey].zIndex = shapeSettings.zIndex;
                    } else {
                        self.zoneSettingsCache[cacheKey] = {
                            width: shapeSettings.width,
                            height: shapeSettings.height,
                            rotation: shapeSettings.rotation,
                            zIndex: shapeSettings.zIndex
                        };
                    }
                }
                
                // Save booth updates to database
                if (boothsToSave.length > 0) {
                    return self.saveBoothsBatch(boothsToSave);
                }
            })
            .then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Shape & Layout applied to entire Zone ' + zoneName + ' — ' + updatedCount + ' booth(s) updated.',
                    timer: 3000,
                    showConfirmButton: false
                });
                // Save state for undo/redo
                self.saveState();
            })
            .catch(function(error) {
                console.error('Error saving zone shape/layout settings:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save settings. Please try again.'
                });
            });
    },
    
    // Save Zone Appearance & Style Settings — applies to entire zone (sidebar + canvas)
    saveZoneAppearanceSettings: function(zoneName, appearanceSettings) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        const sidebarContainer = document.getElementById('boothNumbersContainer');
        const sidebarBooths = sidebarContainer ? sidebarContainer.querySelectorAll('.booth-number-item[data-booth-zone="' + zoneName + '"]') : [];
        const canvasBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        const canvasBoothIds = new Set();
        canvasBooths.forEach(function(booth) {
            const boothId = booth.getAttribute('data-booth-id');
            if (boothId) canvasBoothIds.add(boothId);
        });
        
        const boothsToSave = [];
        let updatedCount = 0;
        const boothDataAppearance = {
            background_color: appearanceSettings.background_color,
            border_color: appearanceSettings.border_color,
            text_color: appearanceSettings.text_color,
            font_weight: appearanceSettings.font_weight,
            font_family: appearanceSettings.font_family,
            text_align: appearanceSettings.text_align,
            box_shadow: appearanceSettings.box_shadow,
            border_radius: appearanceSettings.borderRadius,
            border_width: appearanceSettings.borderWidth,
            opacity: appearanceSettings.opacity
        };
        
        // 1) Sidebar booths (not on canvas): save appearance to DB only
        sidebarBooths.forEach(function(boothItem) {
            const boothId = boothItem.getAttribute('data-booth-id');
            if (!boothId || canvasBoothIds.has(boothId)) return;
            boothsToSave.push(Object.assign({ id: parseInt(boothId) }, boothDataAppearance));
            updatedCount++;
        });
        
        // 2) Canvas booths: apply to DOM and save to DB (entire zone)
        canvasBooths.forEach(function(boothElement) {
            const boothId = boothElement.getAttribute('data-booth-id');
            if (!boothId) return;
            // So status-based !important rules don't override inline styles
            boothElement.classList.add('has-custom-colors');
            boothElement.style.backgroundColor = appearanceSettings.background_color || '';
            boothElement.style.borderColor = appearanceSettings.border_color || '';
            boothElement.style.color = appearanceSettings.text_color || '';
            boothElement.style.fontWeight = appearanceSettings.font_weight || '';
            boothElement.style.fontFamily = appearanceSettings.font_family || '';
            boothElement.style.textAlign = appearanceSettings.text_align || '';
            boothElement.style.boxShadow = appearanceSettings.box_shadow || '';
            boothElement.style.borderRadius = (appearanceSettings.borderRadius != null ? parseFloat(appearanceSettings.borderRadius) : 0) + 'px';
            const borderWidthPx = appearanceSettings.borderWidth != null ? parseFloat(appearanceSettings.borderWidth) : 2;
            boothElement.style.borderWidth = borderWidthPx + 'px';
            boothElement.style.borderStyle = 'solid';
            boothElement.style.opacity = appearanceSettings.opacity != null ? parseFloat(appearanceSettings.opacity) : 1;
            boothElement.setAttribute('data-background-color', appearanceSettings.background_color || '');
            boothElement.setAttribute('data-border-color', appearanceSettings.border_color || '');
            boothElement.setAttribute('data-text-color', appearanceSettings.text_color || '');
            boothElement.setAttribute('data-font-weight', appearanceSettings.font_weight || '');
            boothElement.setAttribute('data-font-family', appearanceSettings.font_family || '');
            boothElement.setAttribute('data-text-align', appearanceSettings.text_align || '');
            boothElement.setAttribute('data-box-shadow', appearanceSettings.box_shadow || '');
            boothElement.setAttribute('data-border-radius', appearanceSettings.borderRadius || 0);
            boothElement.setAttribute('data-border-width', appearanceSettings.borderWidth || 0);
            boothElement.setAttribute('data-opacity', appearanceSettings.opacity != null ? appearanceSettings.opacity : 1);
            const x = parseFloat(boothElement.style.left) || 0;
            const y = parseFloat(boothElement.style.top) || 0;
            boothsToSave.push({
                id: parseInt(boothId),
                position_x: x,
                position_y: y,
                width: parseInt(boothElement.getAttribute('data-width'), 10) || parseInt(boothElement.style.width, 10) || 80,
                height: parseInt(boothElement.getAttribute('data-height'), 10) || parseInt(boothElement.style.height, 10) || 50,
                rotation: parseFloat(boothElement.getAttribute('data-rotation')) || 0,
                z_index: parseInt(boothElement.getAttribute('data-z-index'), 10) || 10,
                font_size: parseFloat(boothElement.getAttribute('data-calculated-font-size')) || 14,
                border_width: appearanceSettings.borderWidth,
                border_radius: appearanceSettings.borderRadius,
                opacity: appearanceSettings.opacity != null ? appearanceSettings.opacity : 1,
                background_color: appearanceSettings.background_color,
                border_color: appearanceSettings.border_color,
                text_color: appearanceSettings.text_color,
                font_weight: appearanceSettings.font_weight,
                font_family: appearanceSettings.font_family,
                text_align: appearanceSettings.text_align,
                box_shadow: appearanceSettings.box_shadow
            });
            updatedCount++;
        });
        
        if (updatedCount === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No Booths in Zone',
                text: 'No booths from Zone ' + zoneName + ' found. Settings will be saved for future booths added to this zone.'
            });
            return;
        }
        
        // Get current floor plan ID
        const floorPlanId = FPD.floorPlanId;
        if (!floorPlanId) {
            Swal.fire({
                icon: 'warning',
                title: 'Floor Plan Required',
                text: 'Please select a floor plan first before saving zone settings.'
            });
            return;
        }
        
        // Load existing zone settings to preserve shape/layout settings
        const zoneSettingsUrl = '/booths/zone-settings/' + encodeURIComponent(zoneName) + 
            (floorPlanId ? '?floor_plan_id=' + floorPlanId : '');
        
        fetch(zoneSettingsUrl)
            .then(response => response.json())
            .then(function(data) {
                const existingSettings = data.settings || {};
                
                // Merge appearance settings with existing shape/layout settings
                const settingsToSave = {
                    // Preserve existing shape/layout settings
                    width: existingSettings.width || 80,
                    height: existingSettings.height || 50,
                    rotation: existingSettings.rotation || 0,
                    zIndex: existingSettings.zIndex || 10,
                    // Update appearance settings
                    borderRadius: appearanceSettings.borderRadius,
                    borderWidth: appearanceSettings.borderWidth,
                    opacity: appearanceSettings.opacity,
                    price: existingSettings.price || 500,
                    background_color: appearanceSettings.background_color,
                    border_color: appearanceSettings.border_color,
                    text_color: appearanceSettings.text_color,
                    font_weight: appearanceSettings.font_weight,
                    font_family: appearanceSettings.font_family,
                    text_align: appearanceSettings.text_align,
                    box_shadow: appearanceSettings.box_shadow,
                    floor_plan_id: floorPlanId
                };
                
                // Save to database
                return fetch('/booths/zone-settings/' + encodeURIComponent(zoneName), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(settingsToSave)
                });
            })
            .then(response => response.json())
            .then(function(data) {
                if (data.status === 200) {
                    // Update cache
                    const cacheKey = floorPlanId ? floorPlanId + '_' + zoneName : 'global_' + zoneName;
                    if (self.zoneSettingsCache[cacheKey]) {
                        self.zoneSettingsCache[cacheKey].background_color = appearanceSettings.background_color;
                        self.zoneSettingsCache[cacheKey].border_color = appearanceSettings.border_color;
                        self.zoneSettingsCache[cacheKey].text_color = appearanceSettings.text_color;
                        self.zoneSettingsCache[cacheKey].font_weight = appearanceSettings.font_weight;
                        self.zoneSettingsCache[cacheKey].font_family = appearanceSettings.font_family;
                        self.zoneSettingsCache[cacheKey].text_align = appearanceSettings.text_align;
                        self.zoneSettingsCache[cacheKey].box_shadow = appearanceSettings.box_shadow;
                        self.zoneSettingsCache[cacheKey].borderRadius = appearanceSettings.borderRadius;
                        self.zoneSettingsCache[cacheKey].borderWidth = appearanceSettings.borderWidth;
                        self.zoneSettingsCache[cacheKey].opacity = appearanceSettings.opacity;
                    } else {
                        self.zoneSettingsCache[cacheKey] = {
                            background_color: appearanceSettings.background_color,
                            border_color: appearanceSettings.border_color,
                            text_color: appearanceSettings.text_color,
                            font_weight: appearanceSettings.font_weight,
                            font_family: appearanceSettings.font_family,
                            text_align: appearanceSettings.text_align,
                            box_shadow: appearanceSettings.box_shadow,
                            borderRadius: appearanceSettings.borderRadius,
                            borderWidth: appearanceSettings.borderWidth,
                            opacity: appearanceSettings.opacity
                        };
                    }
                }
                
                // Save booth updates to database
                if (boothsToSave.length > 0) {
                    return self.saveBoothsBatch(boothsToSave);
                }
            })
            .then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Appearance & Style applied to entire Zone ' + zoneName + ' — ' + updatedCount + ' booth(s) updated.',
                    timer: 3000,
                    showConfirmButton: false
                });
                // Save state for undo/redo
                self.saveState();
            })
            .catch(function(error) {
                console.error('Error saving zone appearance settings:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save settings. Please try again.'
                });
            });
    },
    
    // Save all zone settings in one go (Info + Shape + Appearance) — applies to entire zone
    saveAllZoneSettings: function(zoneName, fullSettings) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        const floorPlanId = FPD.floorPlanId;
        if (!floorPlanId) {
            Swal.fire({
                icon: 'warning',
                title: 'Floor Plan Required',
                text: 'Please select a floor plan first before saving zone settings.'
            });
            return;
        }
        
        const sidebarContainer = document.getElementById('boothNumbersContainer');
        const sidebarBooths = sidebarContainer ? sidebarContainer.querySelectorAll('.booth-number-item[data-booth-zone="' + zoneName + '"]') : [];
        const canvasBooths = canvas.querySelectorAll('.dropped-booth[data-booth-zone="' + zoneName + '"]');
        const canvasBoothIds = new Set();
        canvasBooths.forEach(function(booth) {
            const boothId = booth.getAttribute('data-booth-id');
            if (boothId) canvasBoothIds.add(boothId);
        });
        
        // Apply to DOM so canvas reflects current form (in case live preview missed)
        self.applyZoneShapeLayoutToCanvas(zoneName, {
            width: fullSettings.width,
            height: fullSettings.height,
            rotation: fullSettings.rotation,
            zIndex: fullSettings.zIndex
        });
        self.applyZoneAppearanceToCanvas(zoneName, {
            background_color: fullSettings.background_color,
            border_color: fullSettings.border_color,
            text_color: fullSettings.text_color,
            font_weight: fullSettings.font_weight,
            font_family: fullSettings.font_family,
            text_align: fullSettings.text_align,
            box_shadow: fullSettings.box_shadow,
            borderRadius: fullSettings.borderRadius,
            borderWidth: fullSettings.borderWidth,
            opacity: fullSettings.opacity
        });
        
        const boothsToSave = [];
        const width = Math.max(5, parseFloat(fullSettings.width) || 80);
        const height = Math.max(5, parseFloat(fullSettings.height) || 50);
        const rotation = (parseFloat(fullSettings.rotation) || 0) % 360;
        const zIndex = Math.max(1, Math.min(1000, parseInt(fullSettings.zIndex, 10) || 10));
        
        sidebarBooths.forEach(function(boothItem) {
            const boothId = boothItem.getAttribute('data-booth-id');
            if (!boothId || canvasBoothIds.has(boothId)) return;
            boothsToSave.push({
                id: parseInt(boothId),
                width: width,
                height: height,
                rotation: rotation,
                z_index: zIndex,
                border_width: fullSettings.borderWidth,
                border_radius: fullSettings.borderRadius,
                opacity: fullSettings.opacity != null ? fullSettings.opacity : 1,
                background_color: fullSettings.background_color,
                border_color: fullSettings.border_color,
                text_color: fullSettings.text_color,
                font_weight: fullSettings.font_weight,
                font_family: fullSettings.font_family,
                text_align: fullSettings.text_align,
                box_shadow: fullSettings.box_shadow
            });
        });
        
        canvasBooths.forEach(function(boothElement) {
            const boothId = boothElement.getAttribute('data-booth-id');
            if (!boothId) return;
            const x = parseFloat(boothElement.style.left) || 0;
            const y = parseFloat(boothElement.style.top) || 0;
            boothsToSave.push({
                id: parseInt(boothId),
                position_x: x,
                position_y: y,
                width: parseInt(boothElement.getAttribute('data-width'), 10) || width,
                height: parseInt(boothElement.getAttribute('data-height'), 10) || height,
                rotation: parseFloat(boothElement.getAttribute('data-rotation')) || rotation,
                z_index: parseInt(boothElement.getAttribute('data-z-index'), 10) || zIndex,
                font_size: parseFloat(boothElement.getAttribute('data-calculated-font-size')) || 14,
                border_width: fullSettings.borderWidth,
                border_radius: fullSettings.borderRadius,
                opacity: fullSettings.opacity != null ? fullSettings.opacity : 1,
                background_color: fullSettings.background_color,
                border_color: fullSettings.border_color,
                text_color: fullSettings.text_color,
                font_weight: fullSettings.font_weight,
                font_family: fullSettings.font_family,
                text_align: fullSettings.text_align,
                box_shadow: fullSettings.box_shadow
            });
        });
        
        const settingsToPost = {
            floor_plan_id: floorPlanId,
            zone_about: fullSettings.zone_about || null,
            width: width,
            height: height,
            rotation: rotation,
            zIndex: zIndex,
            borderRadius: fullSettings.borderRadius != null ? fullSettings.borderRadius : 6,
            borderWidth: fullSettings.borderWidth != null ? fullSettings.borderWidth : 2,
            opacity: fullSettings.opacity != null ? fullSettings.opacity : 1,
            background_color: fullSettings.background_color || null,
            border_color: fullSettings.border_color || null,
            text_color: fullSettings.text_color || null,
            font_weight: fullSettings.font_weight || null,
            font_family: fullSettings.font_family || null,
            text_align: fullSettings.text_align || null,
            box_shadow: fullSettings.box_shadow || null
        };
        
        Swal.showLoading();
        
        fetch('/booths/zone-settings/' + encodeURIComponent(zoneName), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(settingsToPost)
        })
        .then(response => response.json())
        .then(function(data) {
            if (data.status !== 200) throw new Error(data.message || 'Failed to save zone settings');
            const cacheKey = floorPlanId + '_' + zoneName;
            self.zoneSettingsCache[cacheKey] = Object.assign(self.zoneSettingsCache[cacheKey] || {}, {
                zone_about: fullSettings.zone_about,
                width: width,
                height: height,
                rotation: rotation,
                zIndex: zIndex,
                borderRadius: fullSettings.borderRadius,
                borderWidth: fullSettings.borderWidth,
                opacity: fullSettings.opacity,
                background_color: fullSettings.background_color,
                border_color: fullSettings.border_color,
                text_color: fullSettings.text_color,
                font_weight: fullSettings.font_weight,
                font_family: fullSettings.font_family,
                text_align: fullSettings.text_align,
                box_shadow: fullSettings.box_shadow
            });
            if (boothsToSave.length > 0) {
                return self.saveBoothsBatch(boothsToSave);
            }
        })
        .then(function() {
            Swal.fire({
                icon: 'success',
                title: 'All Settings Saved',
                text: 'Zone ' + zoneName + ' — Info, Shape & Layout, and Appearance saved. ' + boothsToSave.length + ' booth(s) updated.',
                timer: 3000,
                showConfirmButton: false
            });
            self.saveState();
        })
        .catch(function(error) {
            console.error('Error saving all zone settings:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to save settings. Please try again.'
            });
        });
    },
    
    // Save Zone General Information (Zone About)
    saveZoneInfoSettings: function(zoneName, infoSettings) {
        const self = this;
        const floorPlanId = FPD.floorPlanId;
        
        if (!floorPlanId) {
            Swal.fire({ icon: 'warning', title: 'Floor Plan Required', text: 'Please select a floor plan first.' });
            return;
        }
        
        const saveUrl = '/booths/zone-settings/' + encodeURIComponent(zoneName);
        const requestData = {
            floor_plan_id: floorPlanId,
            zone_about: infoSettings.zone_about
        };
        
        Swal.showLoading();
        
        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 200) {
                // Update cache
                const cacheKey = floorPlanId + '_' + zoneName;
                if (!self.zoneSettingsCache[cacheKey]) {
                    self.zoneSettingsCache[cacheKey] = {};
                }
                self.zoneSettingsCache[cacheKey].zone_about = infoSettings.zone_about;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Information Saved',
                    text: 'Zone ' + zoneName + ' information has been updated successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                throw new Error(data.message || 'Error saving information');
            }
        })
        .catch(error => {
            console.error('Error saving zone info:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save zone information: ' + error.message });
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
            
            // Get current appearance values from element (preserve existing appearance, don't overwrite)
            const backgroundColor = boothElement.style.backgroundColor || boothElement.getAttribute('data-background-color') || null;
            const borderColor = boothElement.style.borderColor || boothElement.getAttribute('data-border-color') || null;
            const textColor = boothElement.style.color || boothElement.getAttribute('data-text-color') || null;
            const fontWeight = boothElement.style.fontWeight || boothElement.getAttribute('data-font-weight') || null;
            const fontFamily = boothElement.style.fontFamily || boothElement.getAttribute('data-font-family') || null;
            const textAlign = boothElement.style.textAlign || boothElement.getAttribute('data-text-align') || null;
            const boxShadow = boothElement.style.boxShadow || boothElement.getAttribute('data-box-shadow') || null;
            
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
                opacity: opacity,
                // Preserve existing appearance (don't overwrite)
                background_color: backgroundColor,
                border_color: borderColor,
                text_color: textColor,
                font_weight: fontWeight,
                font_family: fontFamily,
                text_align: textAlign,
                box_shadow: boxShadow
            });
            
            updatedCount++;
        });
        
        // Get current floor plan ID
        const floorPlanId = FPD.floorPlanId;
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
                const floorPlanId = FPD.floorPlanId;
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
    
    // Open zone appearance settings - redirects to unified modal
    openZoneAppearanceSettings: function(zoneName) {
        // Redirect to the unified zone settings modal (which has both groups)
        this.openZoneSettings(zoneName);
    },
    
    // Show zone appearance settings modal (colors/appearance only)
    showZoneAppearanceModal: function(zoneName, boothCount, settings) {
        const self = this;
        
        // Create appearance-only settings modal
        var modalHtml = '<div style="text-align: left; max-width: 500px; margin: 0 auto;">';
        modalHtml += '<p style="margin-bottom: 15px; color: #666;">';
        modalHtml += 'Found <strong>' + boothCount + '</strong> booth(s) in Zone ' + zoneName + ' on canvas.';
        modalHtml += '<br><small><i class="fas fa-info-circle"></i> This will <strong>only</strong> change colors and appearance. Size, rotation, and dimensions will remain unchanged.</small>';
        modalHtml += '</p>';
        
        // Appearance/Color Customization Section
        modalHtml += '<div style="margin-top: 10px; padding-top: 15px; border-top: 2px solid #e0e0e0;">';
        modalHtml += '<h4 style="margin-bottom: 15px; color: #667eea; font-size: 1rem;"><i class="fas fa-palette mr-2"></i>Appearance & Colors (Zone-Specific)</h4>';
        modalHtml += '<p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">Customize colors and appearance for all booths in this zone. Leave empty to use booth defaults.</p>';
        
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-fill-drip"></i> Background Color';
        modalHtml += '</label>';
        modalHtml += '<div style="display: flex; gap: 8px; align-items: center;">';
        modalHtml += '<input type="color" id="zoneAppearanceBackgroundColor" value="' + (settings.background_color || '#ffffff') + '" style="width: 60px; height: 38px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">';
        modalHtml += '<input type="text" id="zoneAppearanceBackgroundColorText" class="swal2-input" value="' + (settings.background_color || '') + '" placeholder="#ffffff or rgb()" style="flex: 1;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-border-all"></i> Border Color';
        modalHtml += '</label>';
        modalHtml += '<div style="display: flex; gap: 8px; align-items: center;">';
        modalHtml += '<input type="color" id="zoneAppearanceBorderColor" value="' + (settings.border_color || '#007bff') + '" style="width: 60px; height: 38px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">';
        modalHtml += '<input type="text" id="zoneAppearanceBorderColorText" class="swal2-input" value="' + (settings.border_color || '') + '" placeholder="#007bff or rgb()" style="flex: 1;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '</div>';
        
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-font"></i> Text Color';
        modalHtml += '</label>';
        modalHtml += '<div style="display: flex; gap: 8px; align-items: center;">';
        modalHtml += '<input type="color" id="zoneAppearanceTextColor" value="' + (settings.text_color || '#000000') + '" style="width: 60px; height: 38px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;">';
        modalHtml += '<input type="text" id="zoneAppearanceTextColorText" class="swal2-input" value="' + (settings.text_color || '') + '" placeholder="#000000 or rgb()" style="flex: 1;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-bold"></i> Font Weight';
        modalHtml += '</label>';
        modalHtml += '<select id="zoneAppearanceFontWeight" class="swal2-input" style="width: 100%;">';
        modalHtml += '<option value="">Use Default</option>';
        modalHtml += '<option value="300"' + (settings.font_weight === '300' ? ' selected' : '') + '>Light (300)</option>';
        modalHtml += '<option value="400"' + (settings.font_weight === '400' ? ' selected' : '') + '>Normal (400)</option>';
        modalHtml += '<option value="600"' + (settings.font_weight === '600' ? ' selected' : '') + '>Semi-Bold (600)</option>';
        modalHtml += '<option value="700"' + (settings.font_weight === '700' ? ' selected' : '') + '>Bold (700)</option>';
        modalHtml += '<option value="800"' + (settings.font_weight === '800' ? ' selected' : '') + '>Extra Bold (800)</option>';
        modalHtml += '</select>';
        modalHtml += '</div>';
        modalHtml += '</div>';
        
        modalHtml += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-align-center"></i> Text Align';
        modalHtml += '</label>';
        modalHtml += '<select id="zoneAppearanceTextAlign" class="swal2-input" style="width: 100%;">';
        modalHtml += '<option value="">Use Default</option>';
        modalHtml += '<option value="left"' + (settings.text_align === 'left' ? ' selected' : '') + '>Left</option>';
        modalHtml += '<option value="center"' + (settings.text_align === 'center' ? ' selected' : '') + '>Center</option>';
        modalHtml += '<option value="right"' + (settings.text_align === 'right' ? ' selected' : '') + '>Right</option>';
        modalHtml += '</select>';
        modalHtml += '</div>';
        modalHtml += '<div>';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-font"></i> Font Family';
        modalHtml += '</label>';
        modalHtml += '<input type="text" id="zoneAppearanceFontFamily" class="swal2-input" value="' + (settings.font_family || '') + '" placeholder="Arial, sans-serif" style="width: 100%;">';
        modalHtml += '</div>';
        modalHtml += '</div>';
        
        modalHtml += '<div style="margin-bottom: 15px;">';
        modalHtml += '<label style="display: block; margin-bottom: 5px; font-weight: 600; color: #333;">';
        modalHtml += '<i class="fas fa-shadow"></i> Box Shadow';
        modalHtml += '</label>';
        modalHtml += '<input type="text" id="zoneAppearanceBoxShadow" class="swal2-input" value="' + (settings.box_shadow || '') + '" placeholder="0 2px 8px rgba(0,0,0,0.2)" style="width: 100%;">';
        modalHtml += '</div>';
        
        modalHtml += '</div>'; // End appearance section
        
        modalHtml += '<p style="font-size: 11px; color: #999; margin-top: 10px;">';
        modalHtml += '<i class="fas fa-info-circle"></i> All fields are optional - leave empty to use booth defaults. Settings are saved per floor plan and zone. Size and rotation will NOT be changed.';
        modalHtml += '</p>';
        modalHtml += '</div>';
        
        Swal.fire({
            title: 'Zone ' + zoneName + ' Appearance',
            html: modalHtml,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Apply Appearance Only',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#667eea',
            width: '700px',
            didOpen: () => {
                // Sync color pickers with text inputs
                const bgColorPicker = document.getElementById('zoneAppearanceBackgroundColor');
                const bgColorText = document.getElementById('zoneAppearanceBackgroundColorText');
                const borderColorPicker = document.getElementById('zoneAppearanceBorderColor');
                const borderColorText = document.getElementById('zoneAppearanceBorderColorText');
                const textColorPicker = document.getElementById('zoneAppearanceTextColor');
                const textColorText = document.getElementById('zoneAppearanceTextColorText');
                
                // Update text when color picker changes
                if (bgColorPicker && bgColorText) {
                    bgColorPicker.addEventListener('input', function() {
                        bgColorText.value = this.value;
                    });
                    bgColorText.addEventListener('input', function() {
                        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                            bgColorPicker.value = this.value;
                        }
                    });
                }
                if (borderColorPicker && borderColorText) {
                    borderColorPicker.addEventListener('input', function() {
                        borderColorText.value = this.value;
                    });
                    borderColorText.addEventListener('input', function() {
                        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                            borderColorPicker.value = this.value;
                        }
                    });
                }
                if (textColorPicker && textColorText) {
                    textColorPicker.addEventListener('input', function() {
                        textColorText.value = this.value;
                    });
                    textColorText.addEventListener('input', function() {
                        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
                            textColorPicker.value = this.value;
                        }
                    });
                }
            },
            preConfirm: () => {
                // Get color/appearance values (all optional)
                const backgroundColor = document.getElementById('zoneAppearanceBackgroundColorText').value.trim() || null;
                const borderColor = document.getElementById('zoneAppearanceBorderColorText').value.trim() || null;
                const textColor = document.getElementById('zoneAppearanceTextColorText').value.trim() || null;
                const fontWeight = document.getElementById('zoneAppearanceFontWeight').value.trim() || null;
                const fontFamily = document.getElementById('zoneAppearanceFontFamily').value.trim() || null;
                const textAlign = document.getElementById('zoneAppearanceTextAlign').value.trim() || null;
                const boxShadow = document.getElementById('zoneAppearanceBoxShadow').value.trim() || null;
                
                return {
                    background_color: backgroundColor,
                    border_color: borderColor,
                    text_color: textColor,
                    font_weight: fontWeight,
                    font_family: fontFamily,
                    text_align: textAlign,
                    box_shadow: boxShadow
                };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                self.applyZoneAppearanceSettings(zoneName, result.value);
            }
        });
    },
    
    // Apply appearance settings only (doesn't affect size/rotation) - DEPRECATED, use saveZoneAppearanceSettings instead
    applyZoneAppearanceSettings: function(zoneName, appearanceSettings) {
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
        
        // Apply appearance settings to each booth (preserve size/rotation)
        zoneBooths.forEach(function(boothElement) {
            // Get current size/rotation values (preserve them)
            const width = parseFloat(boothElement.style.width) || parseFloat(boothElement.getAttribute('data-width')) || self.defaultBoothWidth;
            const height = parseFloat(boothElement.style.height) || parseFloat(boothElement.getAttribute('data-height')) || self.defaultBoothHeight;
            const rotation = parseFloat(boothElement.getAttribute('data-rotation')) || 0;
            const zIndex = parseFloat(boothElement.style.zIndex) || 10;
            const borderRadius = parseFloat(boothElement.style.borderRadius) || 6;
            const borderWidth = parseFloat(boothElement.style.borderWidth) || 2;
            const opacity = parseFloat(boothElement.style.opacity) || 1.0;
            const fontSize = parseFloat(boothElement.style.fontSize) || self.defaultBoothFontSize;
            
            // Apply appearance/color settings if provided
            if (appearanceSettings.background_color) {
                boothElement.style.backgroundColor = appearanceSettings.background_color;
                boothElement.setAttribute('data-background-color', appearanceSettings.background_color);
            }
            if (appearanceSettings.border_color) {
                boothElement.style.borderColor = appearanceSettings.border_color;
                boothElement.setAttribute('data-border-color', appearanceSettings.border_color);
            }
            if (appearanceSettings.text_color) {
                boothElement.style.color = appearanceSettings.text_color;
                boothElement.setAttribute('data-text-color', appearanceSettings.text_color);
            }
            if (appearanceSettings.font_weight) {
                boothElement.style.fontWeight = appearanceSettings.font_weight;
                boothElement.setAttribute('data-font-weight', appearanceSettings.font_weight);
            }
            if (appearanceSettings.font_family) {
                boothElement.style.fontFamily = appearanceSettings.font_family;
                boothElement.setAttribute('data-font-family', appearanceSettings.font_family);
            }
            if (appearanceSettings.text_align) {
                boothElement.style.textAlign = appearanceSettings.text_align;
                boothElement.setAttribute('data-text-align', appearanceSettings.text_align);
            }
            if (appearanceSettings.box_shadow) {
                boothElement.style.boxShadow = appearanceSettings.box_shadow;
                boothElement.setAttribute('data-box-shadow', appearanceSettings.box_shadow);
            }
            
            // Collect booth data for batch save (preserve all existing values)
            const boothId = boothElement.getAttribute('data-booth-id');
            const x = parseFloat(boothElement.style.left) || 0;
            const y = parseFloat(boothElement.style.top) || 0;
            
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
                opacity: opacity,
                // Apply new appearance settings
                background_color: appearanceSettings.background_color || boothElement.style.backgroundColor || boothElement.getAttribute('data-background-color') || null,
                border_color: appearanceSettings.border_color || boothElement.style.borderColor || boothElement.getAttribute('data-border-color') || null,
                text_color: appearanceSettings.text_color || boothElement.style.color || boothElement.getAttribute('data-text-color') || null,
                font_weight: appearanceSettings.font_weight || boothElement.style.fontWeight || boothElement.getAttribute('data-font-weight') || null,
                font_family: appearanceSettings.font_family || boothElement.style.fontFamily || boothElement.getAttribute('data-font-family') || null,
                text_align: appearanceSettings.text_align || boothElement.style.textAlign || boothElement.getAttribute('data-text-align') || null,
                box_shadow: appearanceSettings.box_shadow || boothElement.style.boxShadow || boothElement.getAttribute('data-box-shadow') || null
            });
            
            updatedCount++;
        });
        
        // Get current floor plan ID
        const floorPlanId = FPD.floorPlanId;
        if (!floorPlanId) {
            customAlert('Please select a floor plan first before saving zone appearance settings.', 'warning');
            return;
        }
        
        // Load existing zone settings to preserve size/rotation values
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
            // Get existing zone settings to preserve size/rotation
            const existingSettings = (data.status === 200 && data.settings) ? data.settings : {};
            
            // Save zone appearance settings to database (preserve size/rotation, only update appearance)
            fetch('/booths/zone-settings/' + encodeURIComponent(zoneName), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    // Preserve existing size/rotation settings
                    width: existingSettings.width || 80,
                    height: existingSettings.height || 50,
                    rotation: existingSettings.rotation || 0,
                    zIndex: existingSettings.zIndex || existingSettings.z_index || 10,
                    borderRadius: existingSettings.borderRadius || existingSettings.border_radius || 6,
                    borderWidth: existingSettings.borderWidth || existingSettings.border_width || 2,
                    opacity: existingSettings.opacity || 1.0,
                    price: existingSettings.price || 500,
                    floor_plan_id: floorPlanId,
                    // Update appearance settings only
                    background_color: appearanceSettings.background_color || null,
                    border_color: appearanceSettings.border_color || null,
                    text_color: appearanceSettings.text_color || null,
                    font_weight: appearanceSettings.font_weight || null,
                    font_family: appearanceSettings.font_family || null,
                    text_align: appearanceSettings.text_align || null,
                    box_shadow: appearanceSettings.box_shadow || null
                })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data.status === 200) {
                    // Get current floor plan ID for cache key
                    const floorPlanId = FPD.floorPlanId;
                    const cacheKey = floorPlanId ? floorPlanId + '_' + zoneName : 'global_' + zoneName;
                    
                    // Update cache with new appearance settings (preserve size/rotation)
                    if (self.zoneSettingsCache[cacheKey]) {
                        self.zoneSettingsCache[cacheKey].background_color = appearanceSettings.background_color;
                        self.zoneSettingsCache[cacheKey].border_color = appearanceSettings.border_color;
                        self.zoneSettingsCache[cacheKey].text_color = appearanceSettings.text_color;
                        self.zoneSettingsCache[cacheKey].font_weight = appearanceSettings.font_weight;
                        self.zoneSettingsCache[cacheKey].font_family = appearanceSettings.font_family;
                        self.zoneSettingsCache[cacheKey].text_align = appearanceSettings.text_align;
                        self.zoneSettingsCache[cacheKey].box_shadow = appearanceSettings.box_shadow;
                    } else {
                        self.zoneSettingsCache[cacheKey] = {
                            width: existingSettings.width || 80,
                            height: existingSettings.height || 50,
                            rotation: existingSettings.rotation || 0,
                            zIndex: existingSettings.zIndex || 10,
                            borderRadius: existingSettings.borderRadius || 6,
                            borderWidth: existingSettings.borderWidth || 2,
                            opacity: existingSettings.opacity || 1.0,
                            price: existingSettings.price || 500,
                            background_color: appearanceSettings.background_color,
                            border_color: appearanceSettings.border_color,
                            text_color: appearanceSettings.text_color,
                            font_weight: appearanceSettings.font_weight,
                            font_family: appearanceSettings.font_family,
                            text_align: appearanceSettings.text_align,
                            box_shadow: appearanceSettings.box_shadow
                        };
                    }
                }
            })
            .catch(function(error) {
                console.error('Error saving zone appearance settings to database:', error);
            });
        })
        .catch(function(error) {
            console.error('Error loading existing zone settings:', error);
        });
        
        // Batch save all booth changes
        if (boothsToSave.length > 0) {
            self.saveBoothsBatch(boothsToSave).then(function(result) {
                showNotification('Successfully updated appearance for ' + updatedCount + ' booth(s) in Zone ' + zoneName + '. Size and rotation unchanged.', 'success');
            }).catch(function(error) {
                console.error('Error saving booth appearance:', error);
                showNotification('Appearance applied to booths but failed to save to database', 'warning');
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
            canvas.classList.remove('tool-select', 'tool-pan', 'tool-zoom', 'tool-align', 'tool-distribute', 'tool-measure', 'tool-text');
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
                case 'text':
                    canvas.style.cursor = 'text';
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
            'measure': 'Measure Tool',
            'text': 'Text Tool'
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
        
        // Text Tool - Click on canvas to add a text label
        container.addEventListener('mousedown', function(e) {
            if (self.currentTool === 'text' && e.button === 0) {
                if (e.target.classList.contains('dropped-booth') || e.target.closest('.dropped-booth') ||
                    e.target.classList.contains('canvas-text') || e.target.closest('.canvas-text')) {
                    return;
                }
                e.preventDefault();
                e.stopPropagation();
                const canvasRect = canvas.getBoundingClientRect();
                const containerRect = container.getBoundingClientRect();
                let scale = 1, panX = 0, panY = 0;
                if (self.panzoomInstance) {
                    if (self.panzoomInstance.getScale) scale = self.panzoomInstance.getScale();
                    if (self.panzoomInstance.getTransform) {
                        const t = self.panzoomInstance.getTransform();
                        panX = t.x || 0; panY = t.y || 0;
                    }
                }
                const clickX = e.clientX - containerRect.left;
                const clickY = e.clientY - containerRect.top;
                const canvasX = (clickX - panX) / scale;
                const canvasY = (clickY - panY) / scale;
                self.createCanvasTextElement(canvasX, canvasY);
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
        const floorPlanId = FPD.floorPlanId;
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
        const floorPlanId = FPD.floorPlanId;
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
            opacity: self.defaultBoothOpacity,
            // Appearance defaults
            background_color: self.defaultBackgroundColor,
            border_color: self.defaultBorderColor,
            text_color: self.defaultTextColor,
            font_weight: self.defaultFontWeight,
            font_family: self.defaultFontFamily,
            text_align: self.defaultTextAlign,
            box_shadow: self.defaultBoxShadow
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
            // Override appearance with zone settings (zone-specific customization)
            if (zoneSettings.background_color !== undefined && zoneSettings.background_color !== null) effectiveSettings.background_color = zoneSettings.background_color;
            if (zoneSettings.border_color !== undefined && zoneSettings.border_color !== null) effectiveSettings.border_color = zoneSettings.border_color;
            if (zoneSettings.text_color !== undefined && zoneSettings.text_color !== null) effectiveSettings.text_color = zoneSettings.text_color;
            if (zoneSettings.font_weight !== undefined && zoneSettings.font_weight !== null) effectiveSettings.font_weight = zoneSettings.font_weight;
            if (zoneSettings.font_family !== undefined && zoneSettings.font_family !== null) effectiveSettings.font_family = zoneSettings.font_family;
            if (zoneSettings.text_align !== undefined && zoneSettings.text_align !== null) effectiveSettings.text_align = zoneSettings.text_align;
            if (zoneSettings.box_shadow !== undefined && zoneSettings.box_shadow !== null) effectiveSettings.box_shadow = zoneSettings.box_shadow;
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
            
            const selectAllZoneBtn = document.createElement('button');
            selectAllZoneBtn.className = 'btn-zone-select-all';
            selectAllZoneBtn.setAttribute('data-zone', zoneName);
            selectAllZoneBtn.setAttribute('title', 'Select All Booths from Zone ' + zoneName + ' on Canvas');
            selectAllZoneBtn.innerHTML = '<i class="fas fa-mouse-pointer"></i>';
            selectAllZoneBtn.onclick = function(e) {
                e.stopPropagation();
                FloorPlanDesigner.selectAllBoothsByZone(zoneName);
            };
            
            const zoneHeaderActions = document.createElement('div');
            zoneHeaderActions.className = 'zone-header-actions';
            zoneHeaderActions.appendChild(addAllBtn);
            zoneHeaderActions.appendChild(addSelectedBtn);
            zoneHeaderActions.appendChild(clickToPlaceBtn);
            zoneHeaderActions.appendChild(zoneSettingsBtn);
            zoneHeaderActions.appendChild(clearZoneBtn);
            zoneHeaderActions.appendChild(zoomToZoneBtn);
            zoneHeaderActions.appendChild(selectAllZoneBtn);
            
            zoneHeader.appendChild(zoneHeaderLeft);
            zoneHeader.appendChild(zoneHeaderActions);
            
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
            
            // Apply button visibility settings to newly created zone
            if (typeof window.applyZoneButtonVisibility === 'function') {
                window.applyZoneButtonVisibility();
            }
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
        
        // Add booked class if booth has client_id or status != 1
        const isBooked = (boothData.clientId && boothData.clientId !== '') || (boothData.status != '1' && boothData.status != 1);
        if (isBooked) {
            boothItem.classList.add('booked');
        }
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
                const boothId = parseInt(boothData.id);
                if (isNaN(boothId)) {
                    console.error('Invalid booth ID:', boothData.id);
                    showNotification('Error: Invalid booth ID. Cannot save position.', 'error');
                    return;
                }
                this.saveBoothPosition(boothId, x, y, width, height, rotation, zIndex, fontSize, borderWidth, borderRadius, opacity)
                    .then(function() {
                        console.log('✅ Booth position updated successfully');
                    })
                    .catch(function(error) {
                        console.error('❌ Failed to update booth position:', error);
                        showNotification('Failed to save booth position: ' + (error.message || 'Unknown error'), 'error');
                    });
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
            // Ensure booth ID is valid
            if (!boothData.id) {
                console.error('Cannot save booth: Missing booth ID', boothData);
                showNotification('Error: Booth ID is missing. Cannot save position.', 'error');
                return;
            }
            
            setTimeout(function() {
                const width = parseFloat(boothElement.style.width) || parseFloat(boothElement.getAttribute('data-width')) || self.defaultBoothWidth;
                const height = parseFloat(boothElement.style.height) || parseFloat(boothElement.getAttribute('data-height')) || self.defaultBoothHeight;
                const rotation = parseFloat(boothElement.getAttribute('data-rotation')) || self.defaultBoothRotation;
                const zIndex = parseFloat(boothElement.style.zIndex) || parseFloat(boothElement.getAttribute('data-z-index')) || self.defaultBoothZIndex;
                const fontSize = parseFloat(boothElement.style.fontSize) || parseFloat(boothElement.getAttribute('data-font-size')) || self.defaultBoothFontSize;
                const borderWidth = parseFloat(boothElement.style.borderWidth) || parseFloat(boothElement.getAttribute('data-border-width')) || self.defaultBoothBorderWidth;
                const borderRadius = parseFloat(boothElement.style.borderRadius) || parseFloat(boothElement.getAttribute('data-border-radius')) || self.defaultBoothBorderRadius;
                const opacity = parseFloat(boothElement.style.opacity) || parseFloat(boothElement.getAttribute('data-opacity')) || self.defaultBoothOpacity;
                
                // Convert booth ID to integer if it's a string
                const boothId = parseInt(boothData.id);
                if (isNaN(boothId)) {
                    console.error('Invalid booth ID:', boothData.id);
                    showNotification('Error: Invalid booth ID. Cannot save position.', 'error');
                    return;
                }
                
                // Ensure all values are properly parsed and validated before saving
                const validatedWidth = (width !== undefined && !isNaN(width)) ? parseFloat(width) : null;
                const validatedHeight = (height !== undefined && !isNaN(height)) ? parseFloat(height) : null;
                const validatedRotation = (rotation !== undefined && !isNaN(rotation)) ? parseFloat(rotation) : 0;
                const validatedZIndex = (zIndex !== undefined && !isNaN(zIndex)) ? Math.max(1, Math.min(1000, parseInt(zIndex))) : 10;
                const validatedFontSize = (fontSize !== undefined && !isNaN(fontSize)) ? Math.max(8, Math.min(48, parseInt(fontSize))) : 14;
                const validatedBorderWidth = (borderWidth !== undefined && !isNaN(borderWidth)) ? Math.max(0, Math.min(10, parseInt(borderWidth))) : 2;
                const validatedBorderRadius = (borderRadius !== undefined && !isNaN(borderRadius)) ? Math.max(0, Math.min(50, parseInt(borderRadius))) : 6;
                const validatedOpacity = (opacity !== undefined && !isNaN(opacity)) ? Math.max(0, Math.min(1, parseFloat(opacity))) : 1.00;
                
                console.log('Saving booth position:', boothId, 'at', x, y);
                self.saveBoothPosition(boothId, x, y, validatedWidth, validatedHeight, validatedRotation, validatedZIndex, validatedFontSize, validatedBorderWidth, validatedBorderRadius, validatedOpacity)
                    .then(function() {
                        console.log('✅ Booth position saved successfully');
                        // Sync sidebar to remove this booth from sidebar
                        self.syncSidebarWithCanvas();
                    })
                    .catch(function(error) {
                        console.error('❌ Failed to save booth position:', error);
                        showNotification('Failed to save booth position: ' + (error.message || 'Unknown error'), 'error');
                    });
                self.saveState();
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
        const boothStatus = boothData.status || '1';
        const isBooked = (boothData.clientId && boothData.clientId !== '') || (boothStatus != '1' && boothStatus != 1);
        div.className = 'dropped-booth status-' + boothStatus + (isBooked ? ' booked' : '');
        div.setAttribute('data-booth-id', boothData.id);
        div.setAttribute('data-booth-number', boothData.number || '');
        div.setAttribute('data-booth-status', boothStatus);
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
        let borderWidth = boothData.border_width !== undefined && boothData.border_width !== null ? boothData.border_width : effectiveSettings.borderWidth;
        let borderRadius = boothData.border_radius !== undefined && boothData.border_radius !== null ? boothData.border_radius : effectiveSettings.borderRadius;
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
        
        // Get status colors from cache (loaded from database)
        const statusColors = self.getStatusColors();
        
        // Get status-based colors (these will override custom colors)
        // boothStatus already declared at the top of createBoothElement function
        const statusColor = statusColors[boothStatus] || statusColors[1] || { bg: '#28a745', border: '#28a745', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 };
        
        // Check if booth has custom colors (individual booth colors override status colors)
        // Custom colors are stored in boothData or will be loaded from database
        // IMPORTANT: Custom colors ONLY apply when status is "Available" (status code 1)
        const isAvailable = (boothStatus === '1' || boothStatus === 1);
        const customBgColor = isAvailable ? boothData.background_color : null;
        const customBorderColor = isAvailable ? boothData.border_color : null;
        const customTextColor = isAvailable ? boothData.text_color : null;
        
        // Apply appearance settings (use zone settings if available, otherwise booth data, then defaults)
        // Custom booth colors take priority over status colors ONLY for Available status, then status colors, then defaults
        const backgroundColor = customBgColor || (statusColor.background || statusColor.bg); // Custom color takes priority only if Available
        const borderColor = customBorderColor || statusColor.border; // Custom color takes priority only if Available
        // Override borderWidth and borderRadius with status color values if available
        borderWidth = statusColor.border_width !== undefined ? statusColor.border_width : borderWidth;
        const borderStyle = statusColor.border_style || 'solid';
        borderRadius = statusColor.border_radius !== undefined ? statusColor.border_radius : borderRadius;
        const textColor = customTextColor || statusColor.text; // Custom color takes priority only if Available
        const fontWeight = boothData.font_weight || effectiveSettings.font_weight || this.defaultFontWeight || 'bold';
        const fontFamily = boothData.font_family || effectiveSettings.font_family || this.defaultFontFamily || 'Arial, sans-serif';
        const textAlign = boothData.text_align || effectiveSettings.text_align || this.defaultTextAlign || 'center';
        const boxShadow = boothData.box_shadow || effectiveSettings.box_shadow || this.defaultBoxShadow || '0 2px 8px rgba(0,0,0,0.2)';
        
        // Apply colors - use !important if custom colors are set to override status colors (ONLY for Available status)
        if (customBgColor && isAvailable) {
            div.style.setProperty('background-color', backgroundColor, 'important');
            div.classList.add('has-custom-colors');
        } else {
            div.style.backgroundColor = backgroundColor;
        }
        
        if (customBorderColor && isAvailable) {
            div.style.setProperty('border-color', borderColor, 'important');
            div.classList.add('has-custom-colors');
        } else {
            div.style.borderColor = borderColor;
        }
        
        div.style.borderWidth = borderWidth + 'px';
        div.style.borderStyle = borderStyle;
        div.style.borderRadius = borderRadius + 'px';
        
        if (customTextColor && isAvailable) {
            div.style.setProperty('color', textColor, 'important');
            div.classList.add('has-custom-colors');
        } else {
            div.style.color = textColor;
        }
        div.style.fontWeight = fontWeight;
        div.style.fontFamily = fontFamily;
        div.style.textAlign = textAlign;
        div.style.boxShadow = boxShadow;
        
        // Store appearance in data attributes for persistence
        if (backgroundColor) div.setAttribute('data-background-color', backgroundColor);
        if (borderColor) div.setAttribute('data-border-color', borderColor);
        if (textColor) div.setAttribute('data-text-color', textColor);
        if (fontWeight) div.setAttribute('data-font-weight', fontWeight);
        if (fontFamily) div.setAttribute('data-font-family', fontFamily);
        if (textAlign) div.setAttribute('data-text-align', textAlign);
        if (boxShadow) div.setAttribute('data-box-shadow', boxShadow);
        
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
        
        // Add right-click context menu handler with long-press detection (2 seconds for color settings)
        let rightClickStartTime = 0;
        let rightClickTimer = null;
        let isRightClickHeld = false;
        let progressIndicator = null;
        let originalTransform = '';
        
        div.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const boothId = div.getAttribute('data-booth-id');
            const boothNumber = div.getAttribute('data-booth-number');
            
            // Store original transform
            originalTransform = div.style.transform || '';
            
            // Start timer for long-press detection (2 seconds)
            rightClickStartTime = Date.now();
            isRightClickHeld = false;
            
            // Show visual feedback that right-click is being held
            div.style.opacity = '0.8';
            div.style.transform = originalTransform + (originalTransform ? ' ' : '') + 'scale(1.08)';
            
            // Create a progress indicator
            progressIndicator = document.createElement('div');
            progressIndicator.className = 'right-click-progress';
            div.appendChild(progressIndicator);
            
            // Timer for 2 seconds - show color picker directly
            rightClickTimer = setTimeout(function() {
                isRightClickHeld = true;
                
                // Clean up visual feedback
                div.style.opacity = '';
                div.style.transform = originalTransform;
                if (progressIndicator && progressIndicator.parentElement) {
                    progressIndicator.remove();
                }
                
                // Open color picker directly after 2 seconds
                self.openBoothColorPicker(boothId, boothNumber, div);
            }, 2000);
            
            // Clean up on mouse release
            const cleanup = function() {
                if (rightClickTimer) {
                    clearTimeout(rightClickTimer);
                    rightClickTimer = null;
                }
                
                // Restore original appearance
                div.style.opacity = '';
                div.style.transform = originalTransform;
                if (progressIndicator && progressIndicator.parentElement) {
                    progressIndicator.remove();
                }
                
                // If NOT held for 2 seconds, show regular context menu
                if (!isRightClickHeld) {
                    // Show context menu immediately (quick right-click)
                    self.showBoothContextMenu(e, boothId, boothNumber, div);
                }
                
                document.removeEventListener('mouseup', cleanup);
                document.removeEventListener('contextmenu', cleanup);
                document.removeEventListener('mousemove', cleanup);
            };
            
            document.addEventListener('mouseup', cleanup);
            document.addEventListener('contextmenu', cleanup);
            document.addEventListener('mousemove', cleanup);
        });
        
        return div;
    },
    
    // Create a text box on the canvas (PowerPoint-like: resize, rotate, font, background, border)
    createCanvasTextElement: function(x, y) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        const wrap = document.createElement('div');
        wrap.className = 'canvas-text';
        wrap.setAttribute('data-x', x);
        wrap.setAttribute('data-y', y);
        wrap.setAttribute('data-width', 160);
        wrap.setAttribute('data-height', 60);
        wrap.setAttribute('data-rotation', 0);
        wrap.setAttribute('data-font-size', 14);
        wrap.setAttribute('data-font-family', 'Arial, sans-serif');
        wrap.setAttribute('data-color', '#333333');
        wrap.setAttribute('data-background-color', 'rgba(255,255,255,0.95)');
        wrap.setAttribute('data-border-width', 1);
        wrap.setAttribute('data-border-color', '#667eea');
        wrap.setAttribute('data-border-radius', 6);
        wrap.setAttribute('data-text-align', 'left');
        wrap.setAttribute('data-opacity', '1');
        wrap.setAttribute('data-z-index', '50');
        wrap.style.left = x + 'px';
        wrap.style.top = y + 'px';
        wrap.style.position = 'absolute';
        wrap.style.width = '160px';
        wrap.style.height = '60px';
        wrap.style.minWidth = '40px';
        wrap.style.minHeight = '24px';
        wrap.style.transform = 'rotate(0deg)';
        wrap.style.transformOrigin = 'center center';
        wrap.style.zIndex = '50';
        wrap.style.cursor = 'move';
        wrap.style.boxSizing = 'border-box';
        const inner = document.createElement('div');
        inner.className = 'canvas-text-inner';
        inner.contentEditable = 'true';
        inner.setAttribute('data-placeholder', 'Type here...');
        inner.textContent = '';
        inner.style.width = '100%';
        inner.style.height = '100%';
        inner.style.boxSizing = 'border-box';
        inner.style.padding = '6px 10px';
        inner.style.margin = '0';
        inner.style.background = 'rgba(255,255,255,0.95)';
        inner.style.border = '1px solid #667eea';
        inner.style.borderRadius = '6px';
        inner.style.fontSize = '14px';
        inner.style.fontFamily = 'Arial, sans-serif';
        inner.style.color = '#333333';
        inner.style.textAlign = 'left';
        inner.style.outline = 'none';
        inner.style.wordWrap = 'break-word';
        inner.style.whiteSpace = 'pre-wrap';
        inner.style.overflow = 'auto';
        inner.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
        wrap.appendChild(inner);
        ['nw','ne','sw','se','n','s','w','e'].forEach(function(c) {
            const h = document.createElement('div');
            h.className = 'resize-handle ' + c;
            h.style.display = 'none';
            wrap.appendChild(h);
        });
        const rot = document.createElement('div');
        rot.className = 'rotate-handle';
        rot.style.display = 'none';
        wrap.appendChild(rot);
        canvas.appendChild(wrap);
        self.updateCanvasTextHandlesSize(wrap);
        self.makeCanvasTextDraggable(wrap);
        self.setupResizeHandlesForCanvasText(wrap);
        self.setupRotateHandleForCanvasText(wrap);
        inner.addEventListener('blur', function() { self.saveCanvasTextToServer(); });
        inner.focus();
        showNotification('Text box added. Drag to move; resize/rotate when selected. Use toolbar or Format for font, colors, border.', 'success');
        var selfRef = self;
        setTimeout(function() { selfRef.saveCanvasTextToServer(); }, 400);
    },
    
    loadCanvasTextItemsOnCanvas: function() {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas || !window.canvasTextItems || !Array.isArray(window.canvasTextItems) || window.canvasTextItems.length === 0) return;
        window.canvasTextItems.forEach(function(item) {
            const wrap = document.createElement('div');
            wrap.className = 'canvas-text';
            const x = item.x != null ? Number(item.x) : 0;
            const y = item.y != null ? Number(item.y) : 0;
            const w = item.w != null ? Number(item.w) : 160;
            const h = item.h != null ? Number(item.h) : 60;
            const r = item.r != null ? Number(item.r) : 0;
            const z = item.z != null ? Number(item.z) : 50;
            wrap.setAttribute('data-x', x);
            wrap.setAttribute('data-y', y);
            wrap.setAttribute('data-width', w);
            wrap.setAttribute('data-height', h);
            wrap.setAttribute('data-rotation', r);
            wrap.style.left = x + 'px';
            wrap.style.top = y + 'px';
            wrap.style.position = 'absolute';
            wrap.style.width = w + 'px';
            wrap.style.height = h + 'px';
            wrap.style.minWidth = '40px';
            wrap.style.minHeight = '24px';
            wrap.style.transform = 'rotate(' + r + 'deg)';
            wrap.style.transformOrigin = 'center center';
            wrap.style.zIndex = z;
            wrap.style.cursor = 'move';
            wrap.style.boxSizing = 'border-box';
            const inner = document.createElement('div');
            inner.className = 'canvas-text-inner';
            inner.contentEditable = 'true';
            inner.setAttribute('data-placeholder', 'Type here...');
            inner.textContent = item.text || '';
            inner.style.width = '100%';
            inner.style.height = '100%';
            inner.style.boxSizing = 'border-box';
            inner.style.padding = '6px 10px';
            inner.style.margin = '0';
            inner.style.background = item.backgroundColor || 'rgba(255,255,255,0.95)';
            inner.style.border = (item.borderWidth != null ? item.borderWidth : 1) + 'px ' + (item.borderStyle || 'solid') + ' ' + (item.borderColor || '#667eea');
            inner.style.borderRadius = (item.borderRadius != null ? item.borderRadius : 6) + 'px';
            inner.style.fontSize = (item.fontSize != null ? item.fontSize : 14) + 'px';
            inner.style.fontFamily = item.fontFamily || 'Arial, sans-serif';
            inner.style.color = item.color || '#333333';
            inner.style.textAlign = item.textAlign || 'left';
            inner.style.fontWeight = item.fontWeight || 'normal';
            inner.style.fontStyle = item.fontStyle || 'normal';
            inner.style.textDecoration = item.textDecoration || 'none';
            if (item.letterSpacing) inner.style.letterSpacing = item.letterSpacing;
            if (item.lineHeight) inner.style.lineHeight = item.lineHeight;
            if (item.boxShadow) inner.style.boxShadow = item.boxShadow;
            if (item.textShadow) inner.style.textShadow = item.textShadow;
            inner.style.outline = 'none';
            inner.style.wordWrap = 'break-word';
            inner.style.whiteSpace = 'pre-wrap';
            inner.style.overflow = 'auto';
            inner.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
            if (item.opacity != null) inner.style.opacity = item.opacity;
            wrap.appendChild(inner);
            ['nw','ne','sw','se','n','s','w','e'].forEach(function(c) {
                const hEl = document.createElement('div');
                hEl.className = 'resize-handle ' + c;
                hEl.style.display = 'none';
                wrap.appendChild(hEl);
            });
            const rot = document.createElement('div');
            rot.className = 'rotate-handle';
            rot.style.display = 'none';
            wrap.appendChild(rot);
            canvas.appendChild(wrap);
            self.updateCanvasTextHandlesSize(wrap);
            self.makeCanvasTextDraggable(wrap);
            self.setupResizeHandlesForCanvasText(wrap);
            self.setupRotateHandleForCanvasText(wrap);
            inner.addEventListener('blur', function() { self.saveCanvasTextToServer(); });
        });
    },
    
    getCanvasTextItemsFromDom: function() {
        const canvas = document.getElementById('print');
        if (!canvas) return [];
        const items = [];
        canvas.querySelectorAll('.canvas-text').forEach(function(wrap) {
            const inner = wrap.querySelector('.canvas-text-inner');
            const r = parseFloat(wrap.getAttribute('data-rotation')) || parseFloat((wrap.style.transform || '').match(/rotate\(([^)]+)\)/)?.[1]) || 0;
            const borderStyle = inner && inner.style.borderStyle ? inner.style.borderStyle : 'solid';
            const borderColor = inner && inner.style.borderColor ? inner.style.borderColor : '';
            items.push({
                x: parseFloat(wrap.style.left) || 0,
                y: parseFloat(wrap.style.top) || 0,
                w: parseFloat(wrap.style.width) || 160,
                h: parseFloat(wrap.style.height) || 60,
                r: r,
                z: parseFloat(wrap.style.zIndex) || 50,
                text: inner ? (inner.innerText || inner.textContent || '').trim() : '',
                fontSize: inner ? (parseFloat(inner.style.fontSize) || 14) : 14,
                borderWidth: inner ? (parseFloat(inner.style.borderWidth) || 1) : 1,
                borderRadius: inner ? (parseFloat(inner.style.borderRadius) || 6) : 6,
                opacity: inner ? (parseFloat(inner.style.opacity) || 1) : 1,
                backgroundColor: inner ? (inner.style.backgroundColor || '') : '',
                color: inner ? (inner.style.color || '') : '',
                fontFamily: inner ? (inner.style.fontFamily || '') : '',
                textAlign: inner ? (inner.style.textAlign || 'left') : 'left',
                fontWeight: inner ? (inner.style.fontWeight || 'normal') : 'normal',
                fontStyle: inner ? (inner.style.fontStyle || 'normal') : 'normal',
                textDecoration: inner ? (inner.style.textDecoration || 'none') : 'none',
                letterSpacing: inner ? (inner.style.letterSpacing || '') : '',
                lineHeight: inner ? (inner.style.lineHeight || '') : '',
                borderColor: borderColor,
                borderStyle: borderStyle,
                boxShadow: inner ? (inner.style.boxShadow || '') : '',
                textShadow: inner ? (inner.style.textShadow || '') : ''
            });
        });
        return items;
    },
    
    saveCanvasTextToServer: function() {
        const self = this;
        let floorPlanId = window.floorPlanIdForCanvas;
        if (!floorPlanId && typeof URLSearchParams !== 'undefined') {
            const params = new URLSearchParams(window.location.search);
            const fromUrl = params.get('floor_plan_id');
            if (fromUrl) floorPlanId = parseInt(fromUrl, 10);
        }
        if (!floorPlanId) return Promise.resolve();
        const items = self.getCanvasTextItemsFromDom();
        const token = document.querySelector('meta[name="csrf-token"]');
        const csrf = token ? token.getAttribute('content') : '';
        return fetch('/booths/canvas-text', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ floor_plan_id: floorPlanId, items: items })
        }).then(function(r) {
            if (!r.ok) throw new Error('Save failed');
            return r.json();
        }).catch(function(err) {
            console.warn('Canvas text save failed:', err);
        });
    },
    
    updateCanvasTextHandlesSize: function(wrap) {
        const w = parseFloat(wrap.style.width) || 160;
        const h = parseFloat(wrap.style.height) || 60;
        const minDim = Math.min(w, h);
        const size = Math.max(8, Math.min(14, minDim * 0.12));
        const offset = size / 2;
        wrap.querySelectorAll('.resize-handle').forEach(function(handle) {
            const c = handle.className.split(' ')[1];
            handle.style.width = size + 'px';
            handle.style.height = size + 'px';
            handle.style.borderWidth = '1px';
            if (c === 'nw') { handle.style.top = -offset + 'px'; handle.style.left = -offset + 'px'; }
            else if (c === 'ne') { handle.style.top = -offset + 'px'; handle.style.right = -offset + 'px'; }
            else if (c === 'sw') { handle.style.bottom = -offset + 'px'; handle.style.left = -offset + 'px'; }
            else if (c === 'se') { handle.style.bottom = -offset + 'px'; handle.style.right = -offset + 'px'; }
            else if (c === 'n') { handle.style.top = -offset + 'px'; handle.style.left = '50%'; handle.style.marginLeft = -offset + 'px'; }
            else if (c === 's') { handle.style.bottom = -offset + 'px'; handle.style.left = '50%'; handle.style.marginLeft = -offset + 'px'; }
            else if (c === 'w') { handle.style.left = -offset + 'px'; handle.style.top = '50%'; handle.style.marginTop = -offset + 'px'; }
            else if (c === 'e') { handle.style.right = -offset + 'px'; handle.style.top = '50%'; handle.style.marginTop = -offset + 'px'; }
        });
        const rh = wrap.querySelector('.rotate-handle');
        if (rh) {
            const rs = Math.max(14, Math.min(22, minDim * 0.2));
            rh.style.width = rs + 'px';
            rh.style.height = rs + 'px';
            rh.style.marginLeft = (-rs/2) + 'px';
            rh.style.top = (-rs - 4) + 'px';
        }
    },
    
    setupResizeHandlesForCanvasText: function(wrap) {
        const self = this;
        let isResizing = false, startX, startY, startW, startH, startLeft, startTop, handleClass = null;
        wrap.querySelectorAll('.resize-handle').forEach(function(handle) {
            handle.addEventListener('mousedown', function(e) {
                e.preventDefault();
                e.stopPropagation();
                isResizing = true;
                handleClass = handle.className.split(' ')[1];
                startX = e.clientX;
                startY = e.clientY;
                startW = parseFloat(wrap.style.width) || 160;
                startH = parseFloat(wrap.style.height) || 60;
                startLeft = parseFloat(wrap.style.left) || 0;
                startTop = parseFloat(wrap.style.top) || 0;
                document.addEventListener('mousemove', onResize);
                document.addEventListener('mouseup', onResizeEnd);
            });
        });
        function onResize(e) {
            if (!isResizing || !handleClass) return;
            let scale = 1;
            if (self.panzoomInstance && self.panzoomInstance.getScale) scale = self.panzoomInstance.getScale();
            const dx = (e.clientX - startX) / scale;
            const dy = (e.clientY - startY) / scale;
            let nw = startW, nh = startH, nL = startLeft, nT = startTop;
            if (handleClass.includes('e')) nw = Math.max(40, startW + dx);
            if (handleClass.includes('w')) { nw = Math.max(40, startW - dx); nL = startLeft + dx; }
            if (handleClass.includes('s')) nh = Math.max(24, startH + dy);
            if (handleClass.includes('n')) { nh = Math.max(24, startH - dy); nT = startTop + dy; }
            wrap.style.width = nw + 'px';
            wrap.style.height = nh + 'px';
            wrap.style.left = nL + 'px';
            wrap.style.top = nT + 'px';
            wrap.setAttribute('data-width', nw);
            wrap.setAttribute('data-height', nh);
            wrap.setAttribute('data-x', nL);
            wrap.setAttribute('data-y', nT);
            self.updateCanvasTextHandlesSize(wrap);
            if (self.selectedCanvasText === wrap) self.updateInfoToolbar(wrap);
        }
        function onResizeEnd() {
            isResizing = false;
            handleClass = null;
            document.removeEventListener('mousemove', onResize);
            document.removeEventListener('mouseup', onResizeEnd);
        }
    },
    
    setupRotateHandleForCanvasText: function(wrap) {
        const self = this;
        const rot = wrap.querySelector('.rotate-handle');
        if (!rot) return;
        rot.addEventListener('mousedown', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const rect = wrap.getBoundingClientRect();
            const cx = rect.left + rect.width / 2;
            const cy = rect.top + rect.height / 2;
            let startAngle = Math.atan2(e.clientY - cy, e.clientX - cx);
            let currentRotation = parseFloat(wrap.getAttribute('data-rotation')) || parseFloat((wrap.style.transform || '').match(/rotate\(([^)]+)\)/)?.[1]) || 0;
            function onMove(ev) {
                const angle = Math.atan2(ev.clientY - cy, ev.clientX - cx);
                currentRotation += (angle - startAngle) * 180 / Math.PI;
                startAngle = angle;
                wrap.style.transform = 'rotate(' + currentRotation + 'deg)';
                wrap.setAttribute('data-rotation', currentRotation);
                if (self.selectedCanvasText === wrap) self.updateInfoToolbar(wrap);
            }
            function onUp() {
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
            }
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        });
    },
    
    // Make canvas text wrapper draggable; on select show handles and update toolbar
    makeCanvasTextDraggable: function(wrap) {
        const self = this;
        const inner = wrap.querySelector('.canvas-text-inner');
        let isDragging = false, startX, startY, startLeft, startTop;
        function showHandles(show) {
            wrap.querySelectorAll('.resize-handle').forEach(function(h) { h.style.display = show ? 'block' : 'none'; });
            const r = wrap.querySelector('.rotate-handle');
            if (r) r.style.display = show ? 'flex' : 'none';
        }
        wrap.addEventListener('mousedown', function(e) {
            if (e.target.classList.contains('resize-handle') || e.target.classList.contains('rotate-handle')) return;
            if (e.button !== 0) return;
            if (e.target === inner && document.activeElement === inner && window.getSelection().toString().length > 0) return;
            e.preventDefault();
            e.stopPropagation();
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            startLeft = parseFloat(wrap.style.left) || 0;
            startTop = parseFloat(wrap.style.top) || 0;
            self.selectedCanvasText = wrap;
            self.selectedBooths = [];
            document.querySelectorAll('.canvas-text').forEach(function(t) { t.classList.remove('canvas-text-selected'); });
            wrap.classList.add('canvas-text-selected');
            showHandles(true);
            self.updateInfoToolbar(wrap);
            const onMove = function(e) {
                if (!isDragging) return;
                let scale = 1;
                if (self.panzoomInstance && self.panzoomInstance.getScale) scale = self.panzoomInstance.getScale();
                wrap.style.left = (startLeft + (e.clientX - startX) / scale) + 'px';
                wrap.style.top = (startTop + (e.clientY - startY) / scale) + 'px';
            };
            const onUp = function() {
                isDragging = false;
                document.removeEventListener('mousemove', onMove);
                document.removeEventListener('mouseup', onUp);
            };
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
        });
        wrap.addEventListener('click', function(e) {
            if (e.target.classList.contains('resize-handle') || e.target.classList.contains('rotate-handle')) return;
            e.stopPropagation();
            self.selectedCanvasText = wrap;
            self.selectedBooths = [];
            document.querySelectorAll('.canvas-text').forEach(function(t) { t.classList.remove('canvas-text-selected'); });
            wrap.classList.add('canvas-text-selected');
            showHandles(true);
            self.updateInfoToolbar(wrap);
        });
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
    
    // Select all booths on the canvas
    selectAllBoothsOnCanvas: function() {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        const allBooths = canvas.querySelectorAll('.dropped-booth');
        if (allBooths.length === 0) {
            showNotification('No booths on canvas to select', 'info');
            return;
        }
        // Clear current selection
        if (self.selectedBooths && self.selectedBooths.length > 0) {
            self.selectedBooths.forEach(function(booth) {
                booth.classList.remove('selected');
            });
        }
        self.selectedBooths = Array.from(allBooths);
        self.selectedBooths.forEach(function(booth) {
            booth.classList.add('selected');
        });
        self.updateSelectionBoundingBox();
        self.updateInfoToolbar && self.updateInfoToolbar(null);
        showNotification(allBooths.length + ' booth(s) selected', 'success');
    },
    
    // When user clicks .btn-zone-select-all for a zone: select ALL booth IDs on the canvas
    // that belong to that zone. Uses data-booth-zone when set; otherwise derives zone from booth number.
    selectAllBoothsByZone: function(zoneName) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas) return;
        zoneName = String(zoneName || '').trim();
        const zoneNameNorm = zoneName.toUpperCase();
        const allCanvasBooths = canvas.querySelectorAll('.dropped-booth');
        const zoneBooths = [];
        for (let i = 0; i < allCanvasBooths.length; i++) {
            const booth = allCanvasBooths[i];
            const attrZone = (booth.getAttribute('data-booth-zone') || '').trim().toUpperCase();
            const boothNumber = (booth.getAttribute('data-booth-number') || booth.textContent || '').trim();
            const derivedZone = (self.getZoneFromBoothNumber(boothNumber) || '').toUpperCase();
            if (attrZone === zoneNameNorm || derivedZone === zoneNameNorm) {
                zoneBooths.push(booth);
            }
        }
        if (zoneBooths.length === 0) {
            showNotification('No booths from Zone ' + zoneName + ' on canvas to select', 'info');
            return;
        }
        if (self.selectedBooths && self.selectedBooths.length > 0) {
            self.selectedBooths.forEach(function(booth) {
                booth.classList.remove('selected');
                const ctrl = booth.querySelector('.transform-controls');
                if (ctrl) {
                    ctrl.style.display = 'none';
                    ctrl.style.visibility = 'hidden';
                    ctrl.style.opacity = '0';
                }
                const handles = booth.querySelectorAll('.resize-handle');
                handles.forEach(function(h) { h.style.display = 'none'; });
                const rot = booth.querySelector('.rotate-handle');
                if (rot) rot.style.display = 'none';
            });
        }
        self.selectedBooths = Array.from(zoneBooths);
        self.selectedBooths.forEach(function(booth) {
            booth.classList.add('selected');
        });
        self.updateSelectionBoundingBox();
        if (self.selectedBooths.length === 1) {
            self.updateInfoToolbar(self.selectedBooths[0]);
            const el = self.selectedBooths[0];
            const controls = el.querySelector('.transform-controls');
            if (controls) {
                controls.style.display = '';
                controls.style.visibility = '';
                controls.style.opacity = '';
            }
            if (!el.classList.contains('locked')) {
                const handles = el.querySelectorAll('.resize-handle');
                handles.forEach(function(h) { h.style.display = 'block'; });
                self.updateResizeHandlesSize(el);
                const rotateHandle = el.querySelector('.rotate-handle');
                if (rotateHandle) rotateHandle.style.display = 'flex';
                self.updateRotationIndicator(el);
            }
        } else {
            self.updateInfoToolbar(null);
            self.selectedBooths.forEach(function(booth) {
                const ctrl = booth.querySelector('.transform-controls');
                if (ctrl) {
                    ctrl.style.display = 'none';
                    ctrl.style.visibility = 'hidden';
                    ctrl.style.opacity = '0';
                }
                const handles = booth.querySelectorAll('.resize-handle');
                handles.forEach(function(h) { h.style.display = 'none'; });
                const rot = booth.querySelector('.rotate-handle');
                if (rot) rot.style.display = 'none';
            });
        }
        showNotification(zoneBooths.length + ' booth(s) from Zone ' + zoneName + ' selected', 'success');
    },
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
        // If user cannot edit canvas, disable dragging
        if (!this.canEditCanvas) {
            if (element) {
                element.style.cursor = 'default';
                element.draggable = false;
            }
            return;
        }
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
            
            // Ctrl + drag: temporary lock - do not move booth, let canvas pan (avoid accidental move)
            if (e.ctrlKey) {
                return; // Do not start booth drag; do not stopPropagation so Panzoom can handle pan
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
                    // Get current rotation to preserve it during drag
                    const currentRotation = parseFloat(selectedBooth.getAttribute('data-rotation')) || 
                                          parseFloat(selectedBooth.style.transform.match(/rotate\(([^)]+)\)/)?.[1]) || 0;
                    
                    selectedBoothsInitialPositions.push({
                        element: selectedBooth,
                        initialX: parseFloat(selectedBooth.style.left) || 0,
                        initialY: parseFloat(selectedBooth.style.top) || 0,
                        rotation: currentRotation
                    });
                    selectedBooth.style.cursor = 'grabbing';
                    selectedBooth.style.userSelect = 'none';
                    // Preserve rotation when adding dragging class
                    if (currentRotation !== 0) {
                        selectedBooth.style.transform = 'rotate(' + currentRotation + 'deg) scale(1.02)';
                    } else {
                        selectedBooth.style.transform = 'scale(1.02)';
                    }
                    selectedBooth.classList.add('dragging');
                });
            } else {
                // Get current rotation to preserve it during drag
                const currentRotation = parseFloat(element.getAttribute('data-rotation')) || 
                                      parseFloat(element.style.transform.match(/rotate\(([^)]+)\)/)?.[1]) || 0;
                
                element.style.cursor = 'grabbing';
                element.style.userSelect = 'none';
                // Preserve rotation when adding dragging class
                if (currentRotation !== 0) {
                    element.style.transform = 'rotate(' + currentRotation + 'deg) scale(1.02)';
                } else {
                    element.style.transform = 'scale(1.02)';
                }
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
                    
                    // Preserve rotation during drag (combine with scale)
                    const rotation = boothData.rotation || parseFloat(boothElement.getAttribute('data-rotation')) || 0;
                    if (rotation !== 0) {
                        boothElement.style.transform = 'rotate(' + rotation + 'deg) scale(1.02)';
                    } else {
                        boothElement.style.transform = 'scale(1.02)';
                    }
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
            
            // Preserve rotation during drag (combine with scale)
            const currentRotation = parseFloat(element.getAttribute('data-rotation')) || 
                                  parseFloat(element.style.transform.match(/rotate\(([^)]+)\)/)?.[1]) || 0;
            if (currentRotation !== 0) {
                element.style.transform = 'rotate(' + currentRotation + 'deg) scale(1.02)';
            } else {
                element.style.transform = 'scale(1.02)';
            }
            
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
                    
                    // Restore rotation transform (remove scale, keep rotation)
                    const rotation = boothData.rotation || parseFloat(boothElement.getAttribute('data-rotation')) || 0;
                    if (rotation !== 0) {
                        boothElement.style.transform = 'rotate(' + rotation + 'deg)';
                    } else {
                        boothElement.style.transform = '';
                    }
                    
                    boothElement.style.cursor = 'move';
                    boothElement.style.userSelect = '';
                    boothElement.classList.remove('dragging');
                });
            } else {
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
            
            // Restore rotation transform (remove scale, keep rotation)
            const rotation = parseFloat(element.getAttribute('data-rotation')) || 
                           parseFloat(element.style.transform.match(/rotate\(([^)]+)\)/)?.[1]) || 0;
            if (rotation !== 0) {
                element.style.transform = 'rotate(' + rotation + 'deg)';
            } else {
                element.style.transform = '';
            }
            
            element.style.cursor = 'move';
            element.style.userSelect = '';
            element.classList.remove('dragging');
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
        
        const textFormatPanel = document.getElementById('textFormatPanel');
        if (textFormatPanel) textFormatPanel.style.display = 'none';
        
        // Canvas text box selected: show position, size, rotation, font, border, opacity + format panel
        if (element && element.classList.contains('canvas-text')) {
            const inner = element.querySelector('.canvas-text-inner');
            const x = Math.round(parseFloat(element.style.left) || 0);
            const y = Math.round(parseFloat(element.style.top) || 0);
            const w = Math.round(parseFloat(element.style.width) || 160);
            const h = Math.round(parseFloat(element.style.height) || 60);
            const r = Math.round(parseFloat(element.getAttribute('data-rotation')) || parseFloat((element.style.transform || '').match(/rotate\(([^)]+)\)/)?.[1]) || 0);
            const z = Math.round(parseFloat(element.style.zIndex) || 50);
            const fs = inner ? Math.round(parseFloat(inner.style.fontSize) || 14) : 14;
            const bw = inner ? Math.round(parseFloat(inner.style.borderWidth) || 1) : 1;
            const br = inner ? Math.round(parseFloat(inner.style.borderRadius) || 6) : 6;
            const op = inner ? parseFloat(inner.style.opacity) || 1 : 1;
            const set = function(id, text) {
                const el = document.getElementById(id);
                if (el && !el.classList.contains('info-editing') && !el.querySelector('input')) el.textContent = text;
            };
            set('infoX', x);
            set('infoY', y);
            set('infoW', w);
            set('infoH', h);
            set('infoR', r + '°');
            set('infoZ', z);
            set('infoFontSize', fs);
            set('infoBorderWidth', bw);
            set('infoBorderRadius', br);
            set('infoOpacity', op.toFixed(2));
            set('infoStatus', 'Text');
            set('infoCompany', '-');
            if (infoToolbar) {
                infoToolbar.style.display = 'flex';
                infoToolbar.style.visibility = 'visible';
                infoToolbar.style.opacity = '1';
            }
            if (textFormatPanel && inner) {
                textFormatPanel.style.display = 'flex';
                var fp = document.getElementById('textFormatFontFamily');
                if (fp) fp.value = inner.style.fontFamily || 'Arial, sans-serif';
                var fsEl = document.getElementById('textFormatFontSize');
                if (fsEl) fsEl.value = Math.round(parseFloat(inner.style.fontSize) || 14);
                document.getElementById('textFormatBold').classList.toggle('active', (inner.style.fontWeight === 'bold' || inner.style.fontWeight === '700'));
                document.getElementById('textFormatItalic').classList.toggle('active', inner.style.fontStyle === 'italic');
                document.getElementById('textFormatUnderline').classList.toggle('active', (inner.style.textDecoration || '').indexOf('underline') !== -1);
                document.getElementById('textFormatStrike').classList.toggle('active', (inner.style.textDecoration || '').indexOf('line-through') !== -1);
                document.querySelectorAll('.text-format-align').forEach(function(btn) {
                    btn.classList.toggle('active', (btn.getAttribute('data-align') || '') === (inner.style.textAlign || 'left'));
                });
                var tc = document.getElementById('textFormatColor');
                if (tc) tc.value = (inner.style.color || '#333333').replace(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/, function(m,r,g,b){ return '#' + [r,g,b].map(function(x){ return ('0'+parseInt(x,10).toString(16)).slice(-2); }).join(''); }) || '#333333';
                var tb = document.getElementById('textFormatBg');
                if (tb && inner.style.backgroundColor) {
                    var bg = inner.style.backgroundColor;
                    if (bg.indexOf('rgb') === 0) {
                        var match = bg.match(/rgb\((\d+),\s*(\d+),\s*(\d+)/);
                        if (match) tb.value = '#' + [match[1],match[2],match[3]].map(function(x){ return ('0'+parseInt(x,10).toString(16)).slice(-2); }).join('');
                    } else tb.value = bg;
                }
                var tbc = document.getElementById('textFormatBorderColor');
                if (tbc && inner.style.borderColor) tbc.value = inner.style.borderColor.replace(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/, function(m,r,g,b){ return '#' + [r,g,b].map(function(x){ return ('0'+parseInt(x,10).toString(16)).slice(-2); }).join(''); }) || '#667eea';
                var tbw = document.getElementById('textFormatBorderWidth');
                if (tbw) tbw.value = Math.round(parseFloat(inner.style.borderWidth) || 1);
                var tbs = document.getElementById('textFormatBorderStyle');
                if (tbs) tbs.value = (inner.style.borderStyle || 'solid');
                var tbr = document.getElementById('textFormatBorderRadius');
                if (tbr) tbr.value = Math.round(parseFloat(inner.style.borderRadius) || 6);
                var tbox = document.getElementById('textFormatBoxShadow');
                if (tbox) tbox.value = inner.style.boxShadow || '';
                var tts = document.getElementById('textFormatTextShadow');
                if (tts) tts.value = inner.style.textShadow || '';
                var tls = document.getElementById('textFormatLetterSpacing');
                if (tls) tls.value = inner.style.letterSpacing || '';
                var tlh = document.getElementById('textFormatLineHeight');
                if (tlh) tlh.value = inner.style.lineHeight || '';
            }
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
        
        // Get selected element (booth or canvas text) for reading current values
        const boothElement = self.selectedBooths.length > 0 ? self.selectedBooths[0] : (self.selectedCanvasText || null);
        
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
            
            // First, try to get from selected element (booth or canvas text) if available
            if (boothElement) {
                const inner = boothElement.classList.contains('canvas-text') ? boothElement.querySelector('.canvas-text-inner') : null;
                const el = inner && (property === 'fontsize' || property === 'borderwidth' || property === 'borderradius' || property === 'opacity') ? inner : boothElement;
                switch(property) {
                    case 'x':
                        currentValue = (parseFloat(boothElement.style.left) || 0).toString();
                        break;
                    case 'y':
                        currentValue = (parseFloat(boothElement.style.top) || 0).toString();
                        break;
                    case 'w':
                        currentValue = (parseFloat(boothElement.style.width) || (boothElement.classList.contains('canvas-text') ? 160 : 80)).toString();
                        break;
                    case 'h':
                        currentValue = (parseFloat(boothElement.style.height) || (boothElement.classList.contains('canvas-text') ? 60 : 50)).toString();
                        break;
                    case 'r':
                        currentValue = (parseFloat(boothElement.getAttribute('data-rotation')) || parseFloat((boothElement.style.transform || '').match(/rotate\(([^)]+)\)/)?.[1]) || 0).toString();
                        break;
                    case 'z':
                        currentValue = (parseFloat(boothElement.style.zIndex) || (boothElement.classList.contains('canvas-text') ? 50 : 10)).toString();
                        break;
                    case 'fontsize':
                        currentValue = (parseFloat(el.style.fontSize) || 14).toString();
                        break;
                    case 'borderwidth':
                        currentValue = (parseFloat(el.style.borderWidth) || (boothElement.classList.contains('canvas-text') ? 1 : 2)).toString();
                        break;
                    case 'borderradius':
                        currentValue = (parseFloat(el.style.borderRadius) || 6).toString();
                        break;
                    case 'opacity':
                        currentValue = (parseFloat(el.style.opacity) || 1).toFixed(2);
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
                    input.step = self.gridSize || 1;
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
                input.step = self.gridSize || 1;
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
    
    // Apply value from info toolbar input to booth element(s) or canvas text - supports multi-select
    applyInfoToolbarValue: function(field, property, value) {
        const self = this;
        
        // Exit edit mode
        field.classList.remove('info-editing');
        
        // Apply to selected canvas text when no booth selected
        if (self.selectedCanvasText && self.selectedBooths.length === 0) {
            const wrap = self.selectedCanvasText;
            const inner = wrap.querySelector('.canvas-text-inner');
            const num = parseFloat(value) || 0;
            const setWrap = function(style, v) { wrap.style[style] = v; };
            const setInner = function(style, v) { if (inner) inner.style[style] = v; };
            switch (property) {
                case 'x': wrap.style.left = num + 'px'; wrap.setAttribute('data-x', num); break;
                case 'y': wrap.style.top = num + 'px'; wrap.setAttribute('data-y', num); break;
                case 'w': wrap.style.width = Math.max(40, num) + 'px'; wrap.setAttribute('data-width', Math.max(40, num)); self.updateCanvasTextHandlesSize(wrap); break;
                case 'h': wrap.style.height = Math.max(24, num) + 'px'; wrap.setAttribute('data-height', Math.max(24, num)); self.updateCanvasTextHandlesSize(wrap); break;
                case 'r': wrap.style.transform = 'rotate(' + num + 'deg)'; wrap.setAttribute('data-rotation', num); break;
                case 'z': wrap.style.zIndex = Math.max(1, Math.min(1000, num)); break;
                case 'fontsize': setInner('fontSize', Math.max(8, Math.min(72, num)) + 'px'); if (inner) wrap.setAttribute('data-font-size', num); break;
                case 'borderwidth': setInner('borderWidth', Math.max(0, Math.min(10, num)) + 'px'); if (inner) wrap.setAttribute('data-border-width', num); break;
                case 'borderradius': setInner('borderRadius', Math.max(0, Math.min(50, num)) + 'px'); if (inner) wrap.setAttribute('data-border-radius', num); break;
                case 'opacity': setInner('opacity', Math.max(0, Math.min(1, num))); break;
            }
            field.textContent = property === 'r' ? value + '°' : (property === 'opacity' ? parseFloat(value).toFixed(2) : value);
            self.updateInfoToolbar(wrap);
            self.saveCanvasTextToServer();
            return;
        }
        
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
        const self = this; // FloorPlanDesigner - do not use global 'self'
        const panel = document.getElementById('propertiesPanel');
        const content = document.getElementById('propertiesContent');
        
        if (!panel || !content) {
            return;
        }
        
        const boothId = element.getAttribute('data-booth-id');
        const boothNumber = element.textContent.trim();
        const safeNum = function(v, def) { const n = parseFloat(v); return (n !== n || !Number.isFinite(n)) ? (def !== undefined ? def : 0) : n; };
        const x = safeNum(element.style.left, 0);
        const y = safeNum(element.style.top, 0);
        const width = safeNum(element.style.width, 80);
        const height = safeNum(element.style.height, 50);
        const rotation = safeNum(element.getAttribute('data-rotation'), 0);
        
        // Get booth data attributes for additional properties
        const status = element.getAttribute('data-status') || element.className.match(/status-(\d+)/) ? element.className.match(/status-(\d+)/)[1] : '1';
        const fontSize = safeNum(element.style.fontSize, 14);
        const borderWidth = safeNum(element.style.borderWidth, 2);
        const borderRadius = safeNum(element.style.borderRadius, 6);
        const opacity = safeNum(element.style.opacity, 1);
        const zIndex = safeNum(element.style.zIndex, 10);
        
        // Get booth price from boothsData
        let boothPrice = 500; // Default price
        if (typeof window.boothsData !== 'undefined' && Array.isArray(window.boothsData)) {
            const boothData = window.boothsData.find(b => b.id == boothId);
            if (boothData && boothData.price !== undefined) {
                boothPrice = safeNum(boothData.price, 500);
            }
        }
        
        var propHtml = '';
        propHtml += '<h6 class="mb-3"><i class="fas fa-cube"></i> Booth: ' + boothNumber + '</h6>';
        propHtml += '<div class="mb-3"><strong>Booth Details</strong></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-dollar-sign"></i> Price:</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-price" value="' + (Number.isFinite(boothPrice) ? boothPrice.toFixed(2) : '0.00') + '" min="0" step="0.01"></div>';
        propHtml += '<div class="mb-3 mt-3"><strong>Position</strong></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-arrows-alt-h"></i> Position X (px):</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-x" value="' + Math.round(x) + '" step="' + (Number(self.gridSize) || 1) + '"></div>';
        propHtml += '<div class="mb-2"><label class="form-label small"><i class="fas fa-arrows-alt-v"></i> Position Y (px):</label>';
        propHtml += '<input type="number" class="form-control form-control-sm prop-y" value="' + Math.round(y) + '" step="' + (Number(self.gridSize) || 1) + '"></div>';
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
        propHtml += '<input type="number" class="form-control form-control-sm prop-opacity" value="' + (Number.isFinite(opacity) ? opacity.toFixed(2) : '1.00') + '" min="0" max="1" step="0.1"></div>';
        
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
            // Parse and validate zIndex (must be integer 1-1000)
            if (zIndex === undefined) {
                const parsedZ = parseInt(boothElement.style.zIndex) || parseInt(boothElement.getAttribute('data-z-index')) || 10;
                zIndex = isNaN(parsedZ) ? 10 : Math.max(1, Math.min(1000, parsedZ));
            }
            
            // Parse and validate fontSize (must be integer 8-48)
            if (fontSize === undefined) {
                const parsedFS = parseInt(boothElement.style.fontSize) || parseInt(boothElement.getAttribute('data-font-size')) || 14;
                fontSize = isNaN(parsedFS) ? 14 : Math.max(8, Math.min(48, parsedFS));
            }
            
            // Parse and validate borderWidth (must be integer 0-10)
            if (borderWidth === undefined) {
                const parsedBW = parseInt(boothElement.style.borderWidth) || parseInt(boothElement.getAttribute('data-border-width')) || 2;
                borderWidth = isNaN(parsedBW) ? 2 : Math.max(0, Math.min(10, parsedBW));
            }
            
            // Parse and validate borderRadius (must be integer 0-50)
            if (borderRadius === undefined) {
                const parsedBR = parseInt(boothElement.style.borderRadius) || parseInt(boothElement.getAttribute('data-border-radius')) || 6;
                borderRadius = isNaN(parsedBR) ? 6 : Math.max(0, Math.min(50, parsedBR));
            }
            
            // Parse and validate opacity (must be numeric 0-1)
            if (opacity === undefined) {
                const parsedOp = parseFloat(boothElement.style.opacity) || parseFloat(boothElement.getAttribute('data-opacity')) || 1.00;
                opacity = isNaN(parsedOp) ? 1.00 : Math.max(0, Math.min(1, parsedOp));
            }
            
            // Get appearance properties from element if not provided
            backgroundColor = backgroundColor !== undefined ? backgroundColor : (boothElement.style.backgroundColor || boothElement.getAttribute('data-background-color') || this.defaultBackgroundColor);
            borderColor = borderColor !== undefined ? borderColor : (boothElement.style.borderColor || boothElement.getAttribute('data-border-color') || this.defaultBorderColor);
            textColor = textColor !== undefined ? textColor : (boothElement.style.color || boothElement.getAttribute('data-text-color') || this.defaultTextColor);
            fontWeight = fontWeight !== undefined ? fontWeight : (boothElement.style.fontWeight || boothElement.getAttribute('data-font-weight') || this.defaultFontWeight);
            fontFamily = fontFamily !== undefined ? fontFamily : (boothElement.style.fontFamily || boothElement.getAttribute('data-font-family') || this.defaultFontFamily);
            textAlign = textAlign !== undefined ? textAlign : (boothElement.style.textAlign || boothElement.getAttribute('data-text-align') || this.defaultTextAlign);
            boxShadow = boxShadow !== undefined ? boxShadow : (boothElement.style.boxShadow || boothElement.getAttribute('data-box-shadow') || this.defaultBoxShadow);
        } else {
            // Ensure values are properly validated and within ranges
            zIndex = (zIndex !== undefined && !isNaN(zIndex)) ? Math.max(1, Math.min(1000, parseInt(zIndex))) : 10;
            fontSize = (fontSize !== undefined && !isNaN(fontSize)) ? Math.max(8, Math.min(48, parseInt(fontSize))) : 14;
            borderWidth = (borderWidth !== undefined && !isNaN(borderWidth)) ? Math.max(0, Math.min(10, parseInt(borderWidth))) : 2;
            borderRadius = (borderRadius !== undefined && !isNaN(borderRadius)) ? Math.max(0, Math.min(50, parseInt(borderRadius))) : 6;
            opacity = (opacity !== undefined && !isNaN(opacity)) ? Math.max(0, Math.min(1, parseFloat(opacity))) : 1.00;
            backgroundColor = backgroundColor || this.defaultBackgroundColor;
            borderColor = borderColor || this.defaultBorderColor;
            textColor = textColor || this.defaultTextColor;
            fontWeight = fontWeight || this.defaultFontWeight;
            fontFamily = fontFamily || this.defaultFontFamily;
            textAlign = textAlign || this.defaultTextAlign;
            boxShadow = boxShadow || this.defaultBoxShadow;
        }
        
        // Ensure all numeric values are properly converted and validated
        const payload = {
            position_x: (x !== undefined && !isNaN(x)) ? parseFloat(x) : null,
            position_y: (y !== undefined && !isNaN(y)) ? parseFloat(y) : null,
            width: (width !== undefined && !isNaN(width)) ? parseFloat(width) : null,
            height: (height !== undefined && !isNaN(height)) ? parseFloat(height) : null,
            rotation: (rotation !== undefined && !isNaN(rotation)) ? parseFloat(rotation) : 0,
            z_index: parseInt(zIndex), // Already validated above
            font_size: parseInt(fontSize), // Already validated above
            border_width: parseInt(borderWidth), // Already validated above
            border_radius: parseInt(borderRadius), // Already validated above
            opacity: parseFloat(opacity) // Already validated above
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
            if (data.error) {
                console.error('❌ Error saving booth position:', data.error);
                throw new Error(data.error);
            }
            console.log('✅ Booth position saved successfully:', boothId, data);
            return data;
        }).catch(function(error) {
            console.error('❌ Error saving booth position:', error);
            // Re-throw error so calling code can handle it
            throw error;
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
        const floorPlanId = FPD.floorPlanId;
        
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
            settings.grid_size = parseInt(self.gridSize) || 1;
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
    
    // Load status colors from database
    loadStatusColors: function() {
        const self = this;
        
        // Return cached if available
        if (self.statusColorsCache) {
            return Promise.resolve(self.statusColorsCache);
        }
        
        return fetch('/settings/booth-statuses/colors', {
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
            if (data.status === 200 && data.data) {
                // Convert to format expected by JavaScript
                const colors = {};
                Object.keys(data.data).forEach(function(statusCode) {
                    const status = data.data[statusCode];
                    colors[parseInt(statusCode)] = {
                        background: status.background,
                        bg: status.background, // Keep for backward compatibility
                        border: status.border || status.background,
                        border_width: status.border_width || 2,
                        border_style: status.border_style || 'solid',
                        border_radius: status.border_radius || 4,
                        text: status.text
                    };
                });
                self.statusColorsCache = colors;
                return colors;
            }
            // Fallback to defaults
            return {
                1: { background: '#28a745', bg: '#28a745', border: '#28a745', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 },
                2: { background: '#0dcaf0', bg: '#0dcaf0', border: '#0dcaf0', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 },
                3: { background: '#ffc107', bg: '#ffc107', border: '#ffc107', text: '#333333', border_width: 2, border_style: 'solid', border_radius: 4 },
                4: { background: '#6c757d', bg: '#6c757d', border: '#6c757d', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 },
                5: { background: '#212529', bg: '#212529', border: '#212529', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 }
            };
        })
        .catch(function(error) {
            console.error('Error loading status colors:', error);
            // Return defaults on error
            return {
                1: { bg: '#28a745', border: '#28a745', text: '#ffffff' },
                2: { bg: '#0dcaf0', border: '#0dcaf0', text: '#ffffff' },
                3: { bg: '#ffc107', border: '#ffc107', text: '#333333' },
                4: { bg: '#6c757d', border: '#6c757d', text: '#ffffff' },
                5: { bg: '#212529', border: '#212529', text: '#ffffff' }
            };
        });
    },
    
    // Get status colors (returns cached or loads if needed)
    getStatusColors: function() {
        const self = this;
        if (self.statusColorsCache) {
            return self.statusColorsCache;
        }
            // Return defaults if not loaded yet
        return {
            1: { background: '#28a745', bg: '#28a745', border: '#28a745', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 },
            2: { background: '#0dcaf0', bg: '#0dcaf0', border: '#0dcaf0', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 },
            3: { background: '#ffc107', bg: '#ffc107', border: '#ffc107', text: '#333333', border_width: 2, border_style: 'solid', border_radius: 4 },
            4: { background: '#6c757d', bg: '#6c757d', border: '#6c757d', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 },
            5: { background: '#212529', bg: '#212529', border: '#212529', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 }
        };
    },
    
    // Refresh status colors cache (call after status settings are updated)
    refreshStatusColors: function() {
        const self = this;
        self.statusColorsCache = null;
        return self.loadStatusColors();
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
        const floorPlanId = FPD.floorPlanId;
        
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
                self.setGridSize(settings.grid_size || 1);
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
                
                // CRITICAL: Don't restore zoom/pan from settings - always fit to view for consistency
                // This ensures all users see the same canvas layout regardless of who last saved settings
                // Instead, always fit canvas to view after loading settings
                setTimeout(function() {
                    if (self.panzoomInstance) {
                        // Always fit canvas to view to ensure consistent display for all users
                        self.fitCanvasToView(false);
                    }
                }, 200);
                
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
                        // Convert relative path to full URL - ensure absolute URL
                        let imageUrl = settings.floorplan_image;
                        if (!imageUrl.startsWith('http')) {
                            if (!imageUrl.startsWith('/')) {
                                imageUrl = '/' + imageUrl;
                            }
                            imageUrl = window.location.origin + imageUrl;
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
                        
                        // Verify image loads
                        const verifyImg = new Image();
                        verifyImg.onload = function() {
                            console.log('[Canvas Settings] ✅ Image verified and loaded successfully');
                        };
                        verifyImg.onerror = function() {
                            console.error('[Canvas Settings] ❌ Image failed to load, trying alternatives');
                            // Try alternative URL formats
                            const alternatives = [
                                window.location.origin + '/' + settings.floorplan_image.replace(/^\//, ''),
                                '/' + settings.floorplan_image.replace(/^\//, ''),
                                settings.floorplan_image.startsWith('/') ? settings.floorplan_image : '/' + settings.floorplan_image
                            ];
                            let retryIdx = 0;
                            const tryAlt = function() {
                                if (retryIdx < alternatives.length) {
                                    const altUrl = alternatives[retryIdx];
                                    console.log('[Canvas Settings] Retrying with alternative URL:', altUrl);
                                    canvas.style.backgroundImage = 'url(\'' + altUrl + '?t=' + Date.now() + '\')';
                                    verifyImg.src = altUrl;
                                    retryIdx++;
                                }
                            };
                            verifyImg.onerror = tryAlt;
                            tryAlt();
                        };
                        verifyImg.src = imageUrl;
                        
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
                            if (FPD.currentFloorPlan && FPD.currentFloorPlan.floor_image) {
                                if (!self.floorplanImage) {
                                    self.floorplanImage = FPD.floorPlanImagePath;
                                }
                                if (!self.floorPlanImageUrl) {
                                    self.floorPlanImageUrl = FPD.floorPlanImageUrl;
                                }
                            }
                        } else {
                            console.warn('[Canvas Settings] ⚠️ No image in settings AND canvas has no image - floor plan may not have image uploaded');
                        }
                    }
                }
            }
        })
        .catch(function(error) {
            console.warn('Failed to load canvas settings from database, using defaults:', error);
            // CRITICAL: Don't use localStorage for canvas settings - it causes different users to see different layouts
            // Always use database values or floor plan defaults to ensure consistency across all users
            // Only use localStorage for booth default settings (width, height, etc.) which are user preferences
            
            // Use floor plan dimensions from PHP if available, otherwise use defaults
            const floorPlanWidth = FPD.canvasWidth;
            const floorPlanHeight = FPD.canvasHeight;
            
            if (floorPlanWidth && floorPlanHeight) {
                self.canvasWidth = floorPlanWidth;
                self.canvasHeight = floorPlanHeight;
            } else {
                // Use defaults
                self.canvasWidth = 1200;
                self.canvasHeight = 800;
            }
            
            self.canvasResolution = 300;
            self.setGridSize(10);
            self.gridEnabled = true;
            self.snapEnabled = false;
            
            // Update UI
            const gridOverlay = $('#gridOverlay');
            const btnGrid = $('#btnGrid');
            gridOverlay.addClass('visible');
            btnGrid.addClass('active');
            
            // Update UI for snap to grid
            const btnSnap = $('#btnSnap');
            btnSnap.toggleClass('active', self.snapEnabled);
            if (self.snapEnabled) {
                btnSnap.attr('title', 'Snap to Grid: ON (Click to disable)').css('background', 'rgba(40, 167, 69, 0.3)');
            } else {
                btnSnap.attr('title', 'Snap to Grid: OFF (Click to enable)').css('background', 'rgba(108, 117, 125, 0.3)');
            }
            
            // Apply saved dimensions
            self.setCanvasSize(self.canvasWidth, self.canvasHeight);
            
            // CRITICAL: Always fit canvas to view for consistency across all users
            // Don't restore zoom/pan from localStorage - it causes different users to see different layouts
            setTimeout(function() {
                if (self.panzoomInstance) {
                    self.fitCanvasToView(false);
                }
            }, 200);
        });
        
        // Load upload size limit from localStorage (not critical for persistence)
        const savedUploadSizeLimit = localStorage.getItem('uploadSizeLimit');
        if (savedUploadSizeLimit) {
            this.uploadSizeLimit = parseInt(savedUploadSizeLimit);
        }
        
        // Prevent canvas from resizing when browser window resizes
        // This ensures booths stay in their fixed positions
        // Also refit canvas to viewport on window resize
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
                    
                    // Always refit canvas to viewport after window resize
                    if (self.panzoomInstance) {
                        setTimeout(function() {
                            self.fitCanvasToView(false);
                        }, 100);
                    }
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
            window.boothsData = FPD.boothsData;
        }
        if (typeof window.floorPlanIdForCanvas === 'undefined') {
            window.floorPlanIdForCanvas = FPD.floorPlanId;
        }
        if (typeof window.canvasTextItems === 'undefined') {
            window.canvasTextItems = FPD.canvasTextItems;
        }
        
        // Clear any existing canvas text so we can re-load from saved data
        canvas.querySelectorAll('.canvas-text').forEach(function(el) { el.remove(); });
        
        // CRITICAL: Always use database values from $boothsForJS - never use localStorage for positions
        // This ensures all users see the exact same booth arrangement as defined in the database
        const booths = FPD.boothsData;
        const self = this;
        
        console.log('[Load Positions] Loading booth positions from database for', booths.length, 'booths');
        
        // Clear any existing booths on canvas first to prevent duplicates
        // This ensures a clean state before loading from database
        const existingBooths = canvas.querySelectorAll('.dropped-booth');
        existingBooths.forEach(function(booth) {
            booth.remove();
        });
        
        booths.forEach(function(booth) {
            if (booth.position_x !== null && booth.position_y !== null) {
                // Declare boothStatus once for this iteration
                const boothStatus = booth.status || 1;
                
                // Check if booth already exists on canvas to prevent duplicates
                const existingBooth = canvas.querySelector('[data-booth-id="' + booth.id + '"]');
                if (existingBooth) {
                    // Booth already exists, just update its position and properties
                    existingBooth.style.left = booth.position_x + 'px';
                    existingBooth.style.top = booth.position_y + 'px';
                    existingBooth.setAttribute('data-x', booth.position_x);
                    existingBooth.setAttribute('data-y', booth.position_y);
                    const existingZone = self.getZoneFromBoothNumber(booth.booth_number || '');
                    existingBooth.setAttribute('data-booth-zone', existingZone);
                    
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
                    
                    // Get status colors from cache (loaded from database)
                    const statusColors = self.getStatusColors();
                    
                    // Apply status-based colors (custom booth colors override status colors)
                    const statusColor = statusColors[boothStatus] || statusColors[1] || { background: '#28a745', border: '#28a745', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 };
                    
                    // Check if booth has custom colors (individual booth colors override status colors)
                    const customBgColor = booth.background_color;
                    const customBorderColor = booth.border_color;
                    const customTextColor = booth.text_color;
                    
                    // Apply colors: custom colors take priority over status colors
                    // IMPORTANT: Custom colors ONLY apply when status is "Available" (status code 1)
                    const isAvailable = (boothStatus === '1' || boothStatus === 1);
                    const effectiveCustomBgColor = isAvailable ? customBgColor : null;
                    const effectiveCustomBorderColor = isAvailable ? customBorderColor : null;
                    const effectiveCustomTextColor = isAvailable ? customTextColor : null;
                    
                    // Use !important for custom colors to override status color CSS (ONLY for Available status)
                    if (effectiveCustomBgColor && isAvailable) {
                        existingBooth.style.setProperty('background-color', effectiveCustomBgColor, 'important');
                        existingBooth.setAttribute('data-background-color', effectiveCustomBgColor);
                        existingBooth.classList.add('has-custom-colors');
                    } else {
                        existingBooth.style.backgroundColor = statusColor.background || statusColor.bg;
                        existingBooth.setAttribute('data-background-color', statusColor.background || statusColor.bg);
                        // Don't remove has-custom-colors here - check all colors first
                    }
                    
                    if (effectiveCustomBorderColor && isAvailable) {
                        existingBooth.style.setProperty('border-color', effectiveCustomBorderColor, 'important');
                        existingBooth.setAttribute('data-border-color', effectiveCustomBorderColor);
                        existingBooth.classList.add('has-custom-colors');
                    } else {
                        existingBooth.style.borderColor = statusColor.border;
                        existingBooth.setAttribute('data-border-color', statusColor.border);
                    }
                    
                    if (effectiveCustomTextColor && isAvailable) {
                        existingBooth.style.setProperty('color', effectiveCustomTextColor, 'important');
                        existingBooth.setAttribute('data-text-color', effectiveCustomTextColor);
                        existingBooth.classList.add('has-custom-colors');
                    } else {
                        existingBooth.style.color = statusColor.text;
                        existingBooth.setAttribute('data-text-color', statusColor.text);
                    }
                    
                    // Remove has-custom-colors class if no custom colors are set or status is not Available
                    if (!effectiveCustomBgColor && !effectiveCustomBorderColor && !effectiveCustomTextColor) {
                        existingBooth.classList.remove('has-custom-colors');
                    }
                    
                    // Prefer saved booth border_radius/border_width over status (avoids overwriting zone/appearance settings on refresh)
                    var hasSavedBorder = (booth.border_radius !== null && booth.border_radius !== undefined) || (booth.border_width !== null && booth.border_width !== undefined);
                    if (hasSavedBorder) {
                        existingBooth.classList.add('has-custom-colors');
                        existingBooth.style.borderWidth = (booth.border_width !== null && booth.border_width !== undefined ? booth.border_width : (statusColor.border_width || 2)) + 'px';
                        existingBooth.style.borderStyle = 'solid';
                        existingBooth.style.borderRadius = (booth.border_radius !== null && booth.border_radius !== undefined ? booth.border_radius : (statusColor.border_radius || 4)) + 'px';
                        existingBooth.setAttribute('data-border-width', booth.border_width !== null && booth.border_width !== undefined ? booth.border_width : (statusColor.border_width || 2));
                        existingBooth.setAttribute('data-border-radius', booth.border_radius !== null && booth.border_radius !== undefined ? booth.border_radius : (statusColor.border_radius || 4));
                    } else {
                        existingBooth.style.borderWidth = (statusColor.border_width || 2) + 'px';
                        existingBooth.style.borderStyle = statusColor.border_style || 'solid';
                        existingBooth.style.borderRadius = (statusColor.border_radius || 4) + 'px';
                    }
                    
                    // Update status class
                    existingBooth.className = existingBooth.className.replace(/status-\d+/, 'status-' + boothStatus);
                    if (!existingBooth.className.includes('status-' + boothStatus)) {
                        existingBooth.className += ' status-' + boothStatus;
                    }
                    
                    // Add/remove booked class based on client_id or status
                    const isBooked = (booth.client_id && booth.client_id !== null) || (boothStatus != 1);
                    if (isBooked) {
                        existingBooth.classList.add('booked');
                    } else {
                        existingBooth.classList.remove('booked');
                    }
                    
                    // Still apply other appearance properties if they exist (font-weight, font-family, etc.)
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
                
                const zoneFromNumber = self.getZoneFromBoothNumber(booth.booth_number || '');
                const extendedBoothData = {
                    id: boothData.id,
                    number: boothData.number,
                    status: boothData.status,
                    zone: zoneFromNumber,
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
                    y: booth.position_y,
                    // Include custom colors if they exist
                    background_color: booth.background_color || null,
                    border_color: booth.border_color || null,
                    text_color: booth.text_color || null
                };
                const boothElement = self.createBoothElement(extendedBoothData);
                
                // Declare isBooked once for this iteration (createBoothElement already handles booked class, but we'll ensure it's correct)
                const isBooked = (booth.client_id && booth.client_id !== null) || (booth.status != 1);
                if (isBooked) {
                    boothElement.classList.add('booked');
                } else {
                    boothElement.classList.remove('booked');
                }
                
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
                
                // Get status colors from cache (loaded from database)
                const statusColors = self.getStatusColors();
                
                // Apply status-based colors (these override custom background/border colors)
                // boothStatus already declared at the top of the forEach loop
                const statusColor = statusColors[boothStatus] || statusColors[1] || { background: '#28a745', border: '#28a745', text: '#ffffff', border_width: 2, border_style: 'solid', border_radius: 4 };
                
                // Check if booth has custom colors (individual booth colors override status colors)
                // IMPORTANT: Custom colors ONLY apply when status is "Available" (status code 1)
                const isAvailable = (boothStatus === '1' || boothStatus === 1);
                const customBgColor = isAvailable ? booth.background_color : null;
                const customBorderColor = isAvailable ? booth.border_color : null;
                const customTextColor = isAvailable ? booth.text_color : null;
                
                // Apply colors: custom colors take priority over status colors ONLY for Available status
                // Use !important for custom colors to override status color CSS
                if (customBgColor && isAvailable) {
                    boothElement.style.setProperty('background-color', customBgColor, 'important');
                    boothElement.setAttribute('data-background-color', customBgColor);
                    boothElement.classList.add('has-custom-colors');
                } else {
                    boothElement.style.backgroundColor = statusColor.background || statusColor.bg;
                    boothElement.setAttribute('data-background-color', statusColor.background || statusColor.bg);
                }
                
                if (customBorderColor && isAvailable) {
                    boothElement.style.setProperty('border-color', customBorderColor, 'important');
                    boothElement.setAttribute('data-border-color', customBorderColor);
                    boothElement.classList.add('has-custom-colors');
                } else {
                    boothElement.style.borderColor = statusColor.border;
                    boothElement.setAttribute('data-border-color', statusColor.border);
                }
                
                if (customTextColor && isAvailable) {
                    boothElement.style.setProperty('color', customTextColor, 'important');
                    boothElement.setAttribute('data-text-color', customTextColor);
                    boothElement.classList.add('has-custom-colors');
                } else {
                    boothElement.style.color = statusColor.text;
                    boothElement.setAttribute('data-text-color', statusColor.text);
                }
                
                // Remove has-custom-colors class if no custom colors are set or status is not Available
                if (!customBgColor && !customBorderColor && !customTextColor) {
                    boothElement.classList.remove('has-custom-colors');
                }
                
                // Prefer saved booth border_radius/border_width over status (avoids overwriting zone/appearance settings on refresh)
                var hasSavedBorderNew = (booth.border_radius !== null && booth.border_radius !== undefined) || (booth.border_width !== null && booth.border_width !== undefined);
                if (hasSavedBorderNew) {
                    boothElement.classList.add('has-custom-colors');
                    boothElement.style.borderWidth = (booth.border_width !== null && booth.border_width !== undefined ? booth.border_width : (statusColor.border_width || 2)) + 'px';
                    boothElement.style.borderStyle = 'solid';
                    boothElement.style.borderRadius = (booth.border_radius !== null && booth.border_radius !== undefined ? booth.border_radius : (statusColor.border_radius || 4)) + 'px';
                    boothElement.setAttribute('data-border-width', booth.border_width !== null && booth.border_width !== undefined ? booth.border_width : (statusColor.border_width || 2));
                    boothElement.setAttribute('data-border-radius', booth.border_radius !== null && booth.border_radius !== undefined ? booth.border_radius : (statusColor.border_radius || 4));
                } else {
                    boothElement.style.borderWidth = (statusColor.border_width || 2) + 'px';
                    boothElement.style.borderStyle = statusColor.border_style || 'solid';
                    boothElement.style.borderRadius = (statusColor.border_radius || 4) + 'px';
                }
                
                    // Store status colors in data attributes (when not using custom)
                    if (!customBgColor) boothElement.setAttribute('data-background-color', statusColor.bg);
                    if (!customBorderColor) boothElement.setAttribute('data-border-color', statusColor.border);
                    boothElement.setAttribute('data-text-color', customTextColor || statusColor.text);
                    
                    // isBooked already declared earlier in this block, just ensure booked class is applied
                    if (isBooked) {
                        boothElement.classList.add('booked');
                    } else {
                        boothElement.classList.remove('booked');
                    }
                
                // Still apply other appearance properties if they exist (font-weight, font-family, etc.)
                if (booth.font_weight) {
                    boothElement.style.fontWeight = booth.font_weight;
                    boothElement.setAttribute('data-font-weight', booth.font_weight);
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
        
        // Load canvas text items (labels) from saved data
        self.loadCanvasTextItemsOnCanvas();
        
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
        
        // Function to update booth count badge and info toolbar
        self.updateBoothCount = function() {
            const booths = document.querySelectorAll('.dropped-booth');
            const count = booths.length;
            const badge = document.getElementById('boothCountBadge');
            if (badge) {
                badge.textContent = count;
            }
            const infoBooths = document.getElementById('infoBoothsOnCanvas');
            if (infoBooths) {
                infoBooths.textContent = count;
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
            const floorPlanId = FPD.floorPlanId;
            if (!floorPlanId) {
                customAlert('Please select a floor plan first.', 'warning');
                removeBtn.html(originalHtml);
                removeBtn.css('pointer-events', 'auto');
                return;
            }
            
            // Send AJAX request to remove floorplan
            $.ajax({
                url: FPD.routes.boothsRemoveFloorplan,
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
            
            // Add floor_plan_id to form data (from URL param or current floor plan)
            const floorPlanId = FPD.floorPlanId;
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
                    let errorMsg = 'The floorplan image failed to upload.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors && xhr.responseJSON.errors.floorplan_image) {
                            errorMsg = xhr.responseJSON.errors.floorplan_image[0] || errorMsg;
                        } else if (xhr.responseJSON.errors && typeof xhr.responseJSON.errors === 'object') {
                            const firstError = Object.values(xhr.responseJSON.errors).flat()[0];
                            if (firstError) errorMsg = firstError;
                        } else if (xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                    }
                    console.warn('[Upload Floorplan] 422/Error:', xhr.status, xhr.responseJSON || xhr.responseText);
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
            const gridSize = parseInt($('#gridSize').val()) || 1;
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
        
        // Setup zone toggles (expand/collapse zones) - only toggle when click is not on a button
        $(document).on('click', '.zone-header', function(e) {
            if ($(e.target).closest('button').length) return;
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
        // Zone Controls Panel Toggle
        $('#zoneControlsToggle').on('click', function() {
            $('.zone-controls-panel').toggleClass('collapsed');
        });
        
        // Load saved button visibility settings from localStorage
        function loadZoneButtonSettings() {
            const savedSettings = localStorage.getItem('zoneButtonSettings');
            if (savedSettings) {
                try {
                    const settings = JSON.parse(savedSettings);
                    $('.zone-control-checkbox').each(function() {
                        const buttonType = $(this).data('button');
                        if (settings.hasOwnProperty(buttonType)) {
                            $(this).prop('checked', settings[buttonType]);
                        }
                    });
                    applyZoneButtonVisibility();
                } catch (e) {
                    console.error('Error loading zone button settings:', e);
                }
            }
        }
        
        // Apply button visibility based on checkbox states
        function applyZoneButtonVisibility() {
            const settings = {};
            const buttonMap = {
                'add-all': '.btn-add-all-zone',
                'add-selected': '.btn-add-selected-zone',
                'add-click': '.btn-add-all-zone-click',
                'settings': '.btn-zone-settings',
                'appearance': '.btn-zone-appearance',
                'clear': '.btn-zone-clear',
                'zoom': '.btn-zone-zoom',
                'add-new': '.btn-zone-add-new',
                'delete': '.btn-zone-delete'
            };
            
            $('.zone-control-checkbox').each(function() {
                const buttonType = $(this).data('button');
                const isVisible = $(this).is(':checked');
                settings[buttonType] = isVisible;
                
                const buttonClass = buttonMap[buttonType];
                if (buttonClass) {
                    if (isVisible) {
                        $(buttonClass).show();
                    } else {
                        $(buttonClass).hide();
                    }
                }
            });
            
            // Save settings to localStorage
            localStorage.setItem('zoneButtonSettings', JSON.stringify(settings));
        }
        
        // Make applyZoneButtonVisibility globally accessible for dynamic zone creation
        window.applyZoneButtonVisibility = applyZoneButtonVisibility;
        
        // Handle checkbox changes
        $(document).on('change', '.zone-control-checkbox', function() {
            applyZoneButtonVisibility();
        });
        
        // Initialize on page load
        loadZoneButtonSettings();
        
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
        
        // Update Booth Info modal: submit via AJAX (no redirect), stay on canvas
        $(document).on('submit', '#canvasBoothInfoForm', function(e) {
            e.preventDefault();
            var form = this;
            var boothId = document.getElementById('canvasBoothId').value;
            if (!boothId) {
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Booth ID missing.' });
                return;
            }
            var formData = new FormData(form);
            formData.append('_method', 'PUT');
            var url = FPD.urls.booths + '/' + boothId;
            var submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            }
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    if (response.ok && (data.success !== false)) {
                        var canvasModalEl = document.getElementById('canvasBoothInfoModal');
                        if (canvasModalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            bootstrap.Modal.getOrCreateInstance(canvasModalEl).hide();
                        } else {
                            $('#canvasBoothInfoModal').modal('hide');
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({ icon: 'success', title: 'Saved', text: 'Booth updated successfully.', timer: 2000, showConfirmButton: false, toast: true, position: 'bottom-right' });
                        } else {
                            alert('Booth updated successfully.');
                        }
                        var newNumber = document.getElementById('canvas_booth_number').value;
                        var boothEl = document.querySelector('.dropped-booth[data-booth-id="' + boothId + '"]');
                        if (boothEl && newNumber) {
                            boothEl.textContent = newNumber;
                        }
                        if (typeof window.boothsData !== 'undefined' && Array.isArray(window.boothsData)) {
                            var b = window.boothsData.find(function(x) { return x.id == boothId; });
                            if (b) {
                                b.booth_number = newNumber;
                                b.price = document.getElementById('canvas_price').value;
                                b.status = document.getElementById('canvas_status').value;
                            }
                        }
                    } else {
                        var msg = data.message || 'Update failed';
                        if (data.errors) msg = Object.values(data.errors).flat().join(' ');
                        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        else alert(msg);
                    }
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Booth';
                    }
                }).catch(function() {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Booth';
                    }
                    if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save booth.' });
                });
            })
            .catch(function(err) {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Save Booth';
                }
                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Failed to save booth.' });
            });
            return false;
        });
        
        // Canvas booth image preview on file select
        $(document).on('change', '#canvas_booth_image', function() {
            var input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#canvasImagePreview').attr('src', e.target.result);
                    $('#canvasImagePreviewContainer').show();
                    $('#canvasImageUploadArea').hide();
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
        $(document).on('click', '#canvasRemoveImageBtn', function() {
            if (typeof FloorPlanDesigner !== 'undefined' && FloorPlanDesigner.clearCanvasBoothImagePreview) {
                FloorPlanDesigner.clearCanvasBoothImagePreview();
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
    
    // Wire up text-format panel controls to the currently-selected canvas text element
    setupTextFormatPanel: function() {
        var self = this;
        function getInner() {
            var w = self.selectedCanvasText;
            return w ? w.querySelector('.canvas-text-inner') : null;
        }
        function markDirty() { self.saveState && self.saveState(); }

        var fontFamily = document.getElementById('textFormatFontFamily');
        var fontSize   = document.getElementById('textFormatFontSize');
        var bold       = document.getElementById('textFormatBold');
        var italic     = document.getElementById('textFormatItalic');
        var underline  = document.getElementById('textFormatUnderline');
        var strike     = document.getElementById('textFormatStrike');
        var color      = document.getElementById('textFormatColor');
        var bg         = document.getElementById('textFormatBg');
        var borderClr  = document.getElementById('textFormatBorderColor');
        var borderW    = document.getElementById('textFormatBorderWidth');
        var borderS    = document.getElementById('textFormatBorderStyle');
        var borderR    = document.getElementById('textFormatBorderRadius');
        var boxShadow  = document.getElementById('textFormatBoxShadow');
        var textShadow = document.getElementById('textFormatTextShadow');
        var letterSp   = document.getElementById('textFormatLetterSpacing');
        var lineH      = document.getElementById('textFormatLineHeight');

        if (fontFamily) fontFamily.addEventListener('change', function() { var el = getInner(); if (el) { el.style.fontFamily = this.value; markDirty(); } });
        if (fontSize)   fontSize.addEventListener('input',  function() { var el = getInner(); if (el) { el.style.fontSize = this.value + 'px'; markDirty(); } });
        if (bold) bold.addEventListener('click', function() { var el = getInner(); if (el) { var isBold = el.style.fontWeight === 'bold' || el.style.fontWeight === '700'; el.style.fontWeight = isBold ? 'normal' : 'bold'; this.classList.toggle('active', !isBold); markDirty(); } });
        if (italic) italic.addEventListener('click', function() { var el = getInner(); if (el) { var isIt = el.style.fontStyle === 'italic'; el.style.fontStyle = isIt ? 'normal' : 'italic'; this.classList.toggle('active', !isIt); markDirty(); } });
        if (underline) underline.addEventListener('click', function() { var el = getInner(); if (el) { var dec = el.style.textDecoration || ''; var has = dec.indexOf('underline') !== -1; el.style.textDecoration = has ? dec.replace('underline', '').trim() : (dec + ' underline').trim(); this.classList.toggle('active', !has); markDirty(); } });
        if (strike) strike.addEventListener('click', function() { var el = getInner(); if (el) { var dec = el.style.textDecoration || ''; var has = dec.indexOf('line-through') !== -1; el.style.textDecoration = has ? dec.replace('line-through', '').trim() : (dec + ' line-through').trim(); this.classList.toggle('active', !has); markDirty(); } });

        document.querySelectorAll('.text-format-align').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var el = getInner(); if (!el) return;
                el.style.textAlign = this.getAttribute('data-align') || 'left';
                document.querySelectorAll('.text-format-align').forEach(function(b) { b.classList.remove('active'); });
                this.classList.add('active');
                markDirty();
            });
        });

        if (color)     color.addEventListener('input',  function() { var el = getInner(); if (el) { el.style.color = this.value; markDirty(); } });
        if (bg)        bg.addEventListener('input',     function() { var el = getInner(); if (el) { el.style.backgroundColor = this.value; markDirty(); } });
        if (borderClr) borderClr.addEventListener('input', function() { var el = getInner(); if (el) { el.style.borderColor = this.value; markDirty(); } });
        if (borderW)   borderW.addEventListener('input',   function() { var el = getInner(); if (el) { el.style.borderWidth = this.value + 'px'; markDirty(); } });
        if (borderS)   borderS.addEventListener('change',  function() { var el = getInner(); if (el) { el.style.borderStyle = this.value; markDirty(); } });
        if (borderR)   borderR.addEventListener('input',   function() { var el = getInner(); if (el) { el.style.borderRadius = this.value + 'px'; markDirty(); } });
        if (boxShadow) boxShadow.addEventListener('change', function() { var el = getInner(); if (el) { el.style.boxShadow = this.value; markDirty(); } });
        if (textShadow) textShadow.addEventListener('change', function() { var el = getInner(); if (el) { el.style.textShadow = this.value; markDirty(); } });
        if (letterSp)  letterSp.addEventListener('input',  function() { var el = getInner(); if (el) { el.style.letterSpacing = this.value; markDirty(); } });
        if (lineH)     lineH.addEventListener('input',     function() { var el = getInner(); if (el) { el.style.lineHeight = this.value; markDirty(); } });
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
            exclude: ['.dropped-booth', '.resize-handle', '.rotate-handle', '.transform-controls', '.booth-number-item', '.canvas-text'],
            // Also exclude by checking the target in event handlers
            handleStartEvent: function(e) {
                const target = e.target;
                
                // Ctrl + drag: temporary lock - pan canvas only, do not move booths (avoid accidental booth move)
                if (e.ctrlKey) {
                    return true; // Let Panzoom handle pan; booth drag will not start
                }
                
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
        
        // Ctrl + Mouse wheel = zoom in/out at cursor position (zoom follows mouse)
        const container = document.getElementById('printContainer');
        if (container) {
            container.addEventListener('wheel', function(e) {
                if (!e.ctrlKey || !self.panzoomInstance) return;
                e.preventDefault();
                e.stopPropagation();
                
                let currentScale = 1;
                if (self.panzoomInstance.getScale) currentScale = self.panzoomInstance.getScale();
                // Gentler step (1.08) for smoother feel; short animation for fluid transition
                const zoomFactor = 1.08;
                const newScale = e.deltaY < 0
                    ? Math.min(5, currentScale * zoomFactor)
                    : Math.max(0.1, currentScale / zoomFactor);
                
                try {
                    if (self.panzoomInstance.zoomToPoint) {
                        self.panzoomInstance.zoomToPoint(newScale, {
                            clientX: e.clientX,
                            clientY: e.clientY
                        }, { animate: true, duration: 100 });
                    } else if (self.panzoomInstance.zoom) {
                        self.panzoomInstance.zoom(newScale, { animate: true, duration: 100 });
                    }
                } catch (err) {
                    if (self.panzoomInstance.zoom) {
                        self.panzoomInstance.zoom(newScale, { animate: true, duration: 100 });
                    }
                }
                
                self.zoomLevel = newScale;
                const zoomLevelEl = document.getElementById('zoomLevel');
                if (zoomLevelEl) zoomLevelEl.textContent = Math.round(newScale * 100) + '%';
            }, { passive: false, capture: true });
        }
        
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
        
        // Click on canvas background to deselect booths and canvas text
        canvas.addEventListener('click', function(e) {
            // Only deselect if clicking directly on canvas (not on booths or canvas-text)
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
                
                // Deselect canvas text: remove selection class and hide handles
                if (self.selectedCanvasText) {
                    self.selectedCanvasText.classList.remove('canvas-text-selected');
                    const wrap = self.selectedCanvasText;
                    const handles = wrap.querySelectorAll('.resize-handle, .rotate-handle');
                    handles.forEach(function(h) { h.style.display = 'none'; });
                    self.selectedCanvasText = null;
                }
                
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
                
                // Check if panzoom instance exists and has required methods
                if (!self.panzoomInstance) {
                    return;
                }
                
                canvas.style.cursor = 'grabbing';
                let startX = e.clientX;
                let startY = e.clientY;
                
                // Safely get transform - check if method exists first
                let startPanX = 0;
                let startPanY = 0;
                if (self.panzoomInstance.getTransform) {
                    const transform = self.panzoomInstance.getTransform();
                    startPanX = transform.x || 0;
                    startPanY = transform.y || 0;
                } else if (self.panzoomInstance.getPan) {
                    // Fallback: try getPan method if available
                    const pan = self.panzoomInstance.getPan();
                    if (pan) {
                        startPanX = pan.x || 0;
                        startPanY = pan.y || 0;
                    }
                }
                
                const onMove = function(e) {
                    if (!self.panzoomInstance) {
                        return;
                    }
                    const deltaX = e.clientX - startX;
                    const deltaY = e.clientY - startY;
                    if (self.panzoomInstance.pan) {
                        self.panzoomInstance.pan(startPanX + deltaX, startPanY + deltaY, { relative: false });
                    }
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
            
            // Delete key - canvas text (no booth selected)
            if (e.key === 'Delete' && self.selectedCanvasText && self.selectedBooths.length === 0) {
                e.preventDefault();
                const wrap = self.selectedCanvasText;
                wrap.remove();
                self.selectedCanvasText = null;
                self.updateInfoToolbar(null);
                self.saveCanvasTextToServer();
                // Hide resize/rotate handles (handles are children of wrap, already removed)
                return;
            }
            
            // Delete key - booths
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
    
    // Save state for undo/redo (full booth position/size/rotation)
    saveState: function() {
        const canvas = document.getElementById('print');
        if (!canvas) return;
        
        const self = this;
        const booths = canvas.querySelectorAll('.dropped-booth');
        const state = [];
        
        booths.forEach(function(booth) {
            const id = booth.getAttribute('data-booth-id');
            const x = parseFloat(booth.style.left);
            const y = parseFloat(booth.style.top);
            const w = parseFloat(booth.style.width);
            const h = parseFloat(booth.style.height);
            const rot = parseFloat(booth.getAttribute('data-rotation')) ||
                parseFloat((booth.style.transform || '').match(/rotate\(([^)]+)\)/)?.[1]) || 0;
            
            state.push({
                id: id,
                x: !isNaN(x) ? x : 0,
                y: !isNaN(y) ? y : 0,
                width: !isNaN(w) ? w : (parseFloat(booth.offsetWidth) || 80),
                height: !isNaN(h) ? h : (parseFloat(booth.offsetHeight) || 50),
                rotation: !isNaN(rot) ? rot : 0
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
        
        this.saveCanvasTextToServer();
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
        // #endregion
        
        const canvas = self.getElement('print');
        
        // #region agent log
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
        const canvasTextHandles = canvas.querySelectorAll('.canvas-text .resize-handle, .canvas-text .rotate-handle');
        const canvasTextSelected = canvas.querySelectorAll('.canvas-text-selected');
        
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
        canvasTextHandles.forEach(function(h) {
            h.style.setProperty('display', 'none', 'important');
        });
        canvasTextSelected.forEach(function(el) {
            el.classList.remove('canvas-text-selected');
        });
        
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
            // Restore canvas-text selection and handles
            canvasTextSelected.forEach(function(el) {
                el.classList.add('canvas-text-selected');
            });
            canvasTextHandles.forEach(function(h) {
                h.style.removeProperty('display');
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
            // #endregion
            
            // Write content to print window
            printWindow.document.write(printHTML);
            printWindow.document.close();
            
            // #region agent log
            // #endregion
            
            // Close loading notification
            Swal.close();
            
            // Show success notification
            showNotification('Print dialog opened. If it did not open, please check your popup blocker.', 'success');
            
        }).catch(function(error) {
            // #region agent log
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
            canvasTextSelected.forEach(function(el) {
                el.classList.add('canvas-text-selected');
            });
            canvasTextHandles.forEach(function(h) {
                h.style.removeProperty('display');
            });
            
            Swal.close();
            console.error('Print error:', error);
            showNotification('Failed to capture floorplan for printing. Please try again.', 'error');
        });
    },
    
    // Restore state (apply saved positions/sizes/rotations and persist to server)
    restoreState: function(state) {
        const canvas = this.getElement('print');
        if (!canvas || !state || !Array.isArray(state)) return;
        
        const self = this;
        state.forEach(function(saved) {
            const boothId = saved.id;
            const booth = canvas.querySelector('.dropped-booth[data-booth-id="' + boothId + '"]');
            if (!booth) return;
            
            const x = typeof saved.x === 'number' ? saved.x : parseFloat(saved.x) || 0;
            const y = typeof saved.y === 'number' ? saved.y : parseFloat(saved.y) || 0;
            const w = typeof saved.width === 'number' ? saved.width : (parseFloat(saved.width) || 80);
            const h = typeof saved.height === 'number' ? saved.height : (parseFloat(saved.height) || 50);
            const rot = typeof saved.rotation === 'number' ? saved.rotation : (parseFloat(saved.rotation) || 0);
            
            booth.style.left = x + 'px';
            booth.style.top = y + 'px';
            booth.style.width = w + 'px';
            booth.style.height = h + 'px';
            booth.setAttribute('data-x', x);
            booth.setAttribute('data-y', y);
            booth.setAttribute('data-rotation', rot);
            if (rot !== 0) {
                booth.style.transform = 'rotate(' + rot + 'deg)';
            } else {
                booth.style.transform = '';
            }
            
            // Persist to server (saveBoothPosition reads zIndex, fontSize, etc. from element)
            self.saveBoothPosition(boothId, x, y, w, h, rot);
        });
        
        // Update selection box and info toolbar if needed
        if (this.updateSelectionBoundingBox) this.updateSelectionBoundingBox();
        if (this.selectedBooths && this.selectedBooths.length === 1 && this.updateInfoToolbar) {
            this.updateInfoToolbar(this.selectedBooths[0]);
        }
    },
    
    // Open individual booth color picker modal
    openBoothColorPicker: function(boothId, boothNumber, boothElement) {
        const self = this;
        const canvas = document.getElementById('print');
        if (!canvas || !boothElement) return;
        
        // Get current colors from booth element
        // Check data attributes first (these contain the actual saved colors)
        let currentBgColor = boothElement.getAttribute('data-background-color') || boothElement.style.backgroundColor || '#ffffff';
        let currentBorderColor = boothElement.getAttribute('data-border-color') || boothElement.style.borderColor || '#007bff';
        let currentTextColor = boothElement.getAttribute('data-text-color') || boothElement.style.color || '#000000';
        
        // Convert RGB to hex if needed
        if (currentBgColor && currentBgColor.startsWith('rgb')) {
            currentBgColor = self.rgbToHex(currentBgColor);
        }
        if (currentBorderColor && currentBorderColor.startsWith('rgb')) {
            currentBorderColor = self.rgbToHex(currentBorderColor);
        }
        if (currentTextColor && currentTextColor.startsWith('rgb')) {
            currentTextColor = self.rgbToHex(currentTextColor);
        }
        
        // Ensure hex format
        if (!currentBgColor.startsWith('#')) {
            currentBgColor = '#ffffff';
        }
        if (!currentBorderColor.startsWith('#')) {
            currentBorderColor = '#007bff';
        }
        if (!currentTextColor.startsWith('#')) {
            currentTextColor = '#000000';
        }
        
        // Set modal values
        $('#colorPickerBoothId').val(boothId);
        $('#colorPickerBoothNumber').text(boothNumber);
        $('#boothBackgroundColor').val(currentBgColor);
        $('#boothBackgroundColorText').val(currentBgColor);
        $('#boothBorderColor').val(currentBorderColor);
        $('#boothBorderColorText').val(currentBorderColor);
        $('#boothTextColor').val(currentTextColor);
        $('#boothTextColorText').val(currentTextColor);
        
        // Store booth element reference
        $('#boothColorPickerModal').data('booth-element', boothElement);
        
        // Show modal
        $('#boothColorPickerModal').modal('show');
    },
    
    // Helper: Convert RGB to Hex
    rgbToHex: function(rgb) {
        if (!rgb || rgb === 'transparent') return '#ffffff';
        if (rgb.startsWith('#')) return rgb;
        
        const result = rgb.match(/\d+/g);
        if (!result || result.length < 3) return '#ffffff';
        
        const r = parseInt(result[0]);
        const g = parseInt(result[1]);
        const b = parseInt(result[2]);
        
        return '#' + [r, g, b].map(x => {
            const hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
    },
    
    // Save individual booth colors
    saveBoothColors: function(boothId, backgroundColor, borderColor, textColor) {
        const self = this;
        const canvas = document.getElementById('print');
        const boothElement = canvas ? canvas.querySelector('[data-booth-id="' + boothId + '"]') : null;
        
        if (!boothElement) {
            showNotification('Booth element not found on canvas', 'error');
            return Promise.reject('Booth element not found');
        }
        
        // Get current booth properties
        const x = parseFloat(boothElement.style.left) || 0;
        const y = parseFloat(boothElement.style.top) || 0;
        const width = parseFloat(boothElement.style.width) || 80;
        const height = parseFloat(boothElement.style.height) || 50;
        const rotation = parseFloat(boothElement.getAttribute('data-rotation')) || 0;
        const zIndex = parseFloat(boothElement.style.zIndex) || 10;
        const fontSize = parseFloat(boothElement.style.fontSize) || 14;
        const borderWidth = parseFloat(boothElement.style.borderWidth) || 2;
        const borderRadius = parseFloat(boothElement.style.borderRadius) || 6;
        const opacity = parseFloat(boothElement.style.opacity) || 1.0;
        
        // Update booth element colors immediately (visual feedback)
        // Use !important to override status color CSS rules
        if (backgroundColor && backgroundColor !== '' && backgroundColor !== null) {
            boothElement.style.setProperty('background-color', backgroundColor, 'important');
            boothElement.setAttribute('data-background-color', backgroundColor);
            boothElement.classList.add('has-custom-colors');
        } else {
            boothElement.style.removeProperty('background-color');
            boothElement.removeAttribute('data-background-color');
        }
        
        if (borderColor && borderColor !== '' && borderColor !== null) {
            boothElement.style.setProperty('border-color', borderColor, 'important');
            boothElement.setAttribute('data-border-color', borderColor);
            boothElement.classList.add('has-custom-colors');
        } else {
            boothElement.style.removeProperty('border-color');
            boothElement.removeAttribute('data-border-color');
        }
        
        if (textColor && textColor !== '' && textColor !== null) {
            boothElement.style.setProperty('color', textColor, 'important');
            boothElement.setAttribute('data-text-color', textColor);
            boothElement.classList.add('has-custom-colors');
        } else {
            boothElement.style.removeProperty('color');
            boothElement.removeAttribute('data-text-color');
        }
        
        // If all colors are cleared, remove the custom colors class
        if ((!backgroundColor || backgroundColor === '' || backgroundColor === null) && 
            (!borderColor || borderColor === '' || borderColor === null) && 
            (!textColor || textColor === '' || textColor === null)) {
            boothElement.classList.remove('has-custom-colors');
        }
        
        // Save to database
        return self.saveBoothPosition(
            boothId, 
            x, 
            y, 
            width, 
            height, 
            rotation, 
            zIndex, 
            fontSize, 
            borderWidth, 
            borderRadius, 
            opacity, 
            backgroundColor || null, 
            borderColor || null, 
            textColor || null
        );
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

// Individual Booth Color Picker Modal Handlers
$(document).ready(function() {
    // Sync color picker inputs
    $('#boothBackgroundColor').on('input', function() {
        $('#boothBackgroundColorText').val($(this).val());
    });
    $('#boothBackgroundColorText').on('input', function() {
        const val = $(this).val();
        if (/^#[0-9A-F]{6}$/i.test(val)) {
            $('#boothBackgroundColor').val(val);
        }
    });
    
    $('#boothBorderColor').on('input', function() {
        $('#boothBorderColorText').val($(this).val());
    });
    $('#boothBorderColorText').on('input', function() {
        const val = $(this).val();
        if (/^#[0-9A-F]{6}$/i.test(val)) {
            $('#boothBorderColor').val(val);
        }
    });
    
    $('#boothTextColor').on('input', function() {
        $('#boothTextColorText').val($(this).val());
    });
    $('#boothTextColorText').on('input', function() {
        const val = $(this).val();
        if (/^#[0-9A-F]{6}$/i.test(val)) {
            $('#boothTextColor').val(val);
        }
    });
    
    // Clear color buttons
    $('#clearBoothBackgroundColor').on('click', function() {
        $('#boothBackgroundColor').val('#ffffff');
        $('#boothBackgroundColorText').val('#ffffff');
    });
    
    $('#clearBoothBorderColor').on('click', function() {
        $('#boothBorderColor').val('#007bff');
        $('#boothBorderColorText').val('#007bff');
    });
    
    $('#clearBoothTextColor').on('click', function() {
        $('#boothTextColor').val('#000000');
        $('#boothTextColorText').val('#000000');
    });
    
    // Reset to status colors
    $('#resetBoothColors').on('click', function() {
        const boothId = $('#colorPickerBoothId').val();
        const boothElement = $('#boothColorPickerModal').data('booth-element');
        
        if (!boothElement) return;
        
        // Get status colors
        const statusColors = FloorPlanDesigner.getStatusColors();
        const boothStatus = boothElement.getAttribute('data-booth-status') || '1';
        const statusColor = statusColors[boothStatus] || statusColors[1] || { bg: '#28a745', border: '#28a745', text: '#ffffff' };
        
        const bgColor = statusColor.background || statusColor.bg || '#28a745';
        const borderColor = statusColor.border || '#28a745';
        const textColor = statusColor.text || '#ffffff';
        
        // Update inputs
        $('#boothBackgroundColor').val(bgColor);
        $('#boothBackgroundColorText').val(bgColor);
        $('#boothBorderColor').val(borderColor);
        $('#boothBorderColorText').val(borderColor);
        $('#boothTextColor').val(textColor);
        $('#boothTextColorText').val(textColor);
        
        // Save with null values to clear custom colors (will use status colors)
        const btn = $(this);
        btn.prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Resetting...');
        
        FloorPlanDesigner.saveBoothColors(boothId, null, null, null)
            .then(function() {
                // Remove custom colors class and let status colors apply
                boothElement.classList.remove('has-custom-colors');
                boothElement.removeAttribute('data-background-color');
                boothElement.removeAttribute('data-border-color');
                boothElement.removeAttribute('data-text-color');
                
                // Reload the page or refresh booth to apply status colors
                showNotification('Booth colors reset to status colors!', 'success');
                $('#boothColorPickerModal').modal('hide');
                
                // Reload booth from database to get status colors
                location.reload();
            })
            .catch(function(error) {
                console.error('Error resetting booth colors:', error);
                showNotification('Failed to reset booth colors: ' + (error.message || 'Unknown error'), 'error');
            })
            .finally(function() {
                btn.prop('disabled', false);
                btn.html('<i class="fas fa-undo"></i> Reset to Status Colors');
            });
    });
    
    // Save booth colors
    $('#saveBoothColors').on('click', function() {
        const boothId = $('#colorPickerBoothId').val();
        const backgroundColor = $('#boothBackgroundColor').val();
        const borderColor = $('#boothBorderColor').val();
        const textColor = $('#boothTextColor').val();
        
        if (!boothId) {
            showNotification('Booth ID is missing', 'error');
            return;
        }
        
        const btn = $(this);
        btn.prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        FloorPlanDesigner.saveBoothColors(boothId, backgroundColor, borderColor, textColor)
            .then(function() {
                showNotification('Booth colors saved successfully!', 'success');
                $('#boothColorPickerModal').modal('hide');
            })
            .catch(function(error) {
                console.error('Error saving booth colors:', error);
                showNotification('Failed to save booth colors: ' + (error.message || 'Unknown error'), 'error');
            })
            .finally(function() {
                btn.prop('disabled', false);
                btn.html('<i class="fas fa-save"></i> Save Colors');
            });
    });
});

// CRITICAL: Load floor plan image IMMEDIATELY when DOM is ready (before FloorPlanDesigner.init)
// This ensures the canvas automatically shows the correct floor plan image when user clicks "View Booths"
$(document).ready(function() {
    // AUTOMATICALLY load floor plan image if available (highest priority - before anything else)
        if (FPD.currentFloorPlan && FPD.currentFloorPlan.floor_image) {
        console.log('[Auto-Load Floor Plan] Setting canvas background IMMEDIATELY for floor plan ' + FPD.currentFloorPlan.id + ': ' + FPD.currentFloorPlan.name);
        const canvas = document.getElementById('print');
        if (canvas) {
            const floorPlanImagePath = FPD.floorPlanImagePath;
            let floorPlanImageUrl = FPD.floorPlanImageUrl;
            
            // Ensure absolute URL in JavaScript too
            if (!floorPlanImageUrl.startsWith('http')) {
                floorPlanImageUrl = window.location.origin + (floorPlanImageUrl.startsWith('/') ? '' : '/') + floorPlanImageUrl;
            }
            
            console.log('[Auto-Load Floor Plan] Image path:', floorPlanImagePath);
            console.log('[Auto-Load Floor Plan] Image URL (absolute):', floorPlanImageUrl);
            
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
                    floor_plan_id: FPD.currentFloorPlan.id,
                    floor_plan_name: FPD.currentFloorPlan.name,
                    image_width: img.naturalWidth || img.width,
                    image_height: img.naturalHeight || img.height,
                    image_url: floorPlanImageUrl
                });
            };
            img.onerror = function() {
                console.error('[Auto-Load Floor Plan] ❌ Failed to load image, trying alternatives:', {
                    floor_plan_id: FPD.currentFloorPlan.id,
                    floor_plan_name: FPD.currentFloorPlan.name,
                    image_url: floorPlanImageUrl,
                    image_path: floorPlanImagePath
                });
                
                // Try alternative URL formats
                const alternatives = [
                    window.location.origin + '/' + floorPlanImagePath.replace(/^\//, ''),
                    '/' + floorPlanImagePath.replace(/^\//, ''),
                    floorPlanImagePath.startsWith('/') ? floorPlanImagePath : '/' + floorPlanImagePath
                ];
                
                let retryIndex = 0;
                const tryAlternative = function() {
                    if (retryIndex < alternatives.length) {
                        const altUrl = alternatives[retryIndex];
                        console.log('[Auto-Load Floor Plan] Retrying with alternative URL:', altUrl);
                        canvas.style.backgroundImage = 'url(\'' + altUrl + '?t=' + Date.now() + '\')';
                        img.src = altUrl;
                        retryIndex++;
                    } else {
                        console.error('[Auto-Load Floor Plan] ❌ All image URL attempts failed');
                    }
                };
                
                img.onerror = tryAlternative;
                tryAlternative();
            };
            img.src = floorPlanImageUrl;
        } else {
            console.error('[Auto-Load Floor Plan] ❌ Canvas element not found!');
        }
    } else {
        console.log('[Auto-Load Floor Plan] No floor plan image to load for current floor plan');
    }
    
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
        if (FloorPlanDesigner.selectedCanvasText && FloorPlanDesigner.selectedBooths.length === 0) {
            FloorPlanDesigner.selectedCanvasText.remove();
            FloorPlanDesigner.selectedCanvasText = null;
            FloorPlanDesigner.updateInfoToolbar(null);
            FloorPlanDesigner.saveCanvasTextToServer();
            return;
        }
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
    // #endregion
    
    $('#btnPrint').on('click', function() {
        // #region agent log
        // #endregion
        FloorPlanDesigner.printFloorplan();
    });
    
    // Preview button handler - opens public view in new tab
    $('#btnPreview').on('click', function() {
        const floorPlanId = FPD.floorPlanId;
        
        if (!floorPlanId) {
            alert('Please select a floor plan first.');
            return;
        }
        
        const previewUrl = FPD.routes.floorPlansPublic.replace(':id', floorPlanId);
        window.open(previewUrl, '_blank');
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
        
        window.location.href = FPD.routes.boothsIndex + '?view=canvas&floor_plan_id=' + floorPlanId;
    } else {
        window.location.href = FPD.routes.boothsIndex + '?view=canvas';
    }
}
