<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KHB Booths Booking System')</title>
    
    @php
        try {
            $cdnSettings = \App\Models\Setting::getCDNSettings();
            $useCDN = $cdnSettings['use_cdn'] ?? true; // Default to true (CDN enabled)
        } catch (\Exception $e) {
            // If settings table doesn't exist or error occurs, default to CDN enabled
            $useCDN = true;
        }
    @endphp
    
    {{-- Performance Optimizations: Resource Hints --}}
    @if($useCDN)
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="dns-prefetch" href="{{ url('/') }}">
    @else
    <link rel="preconnect" href="{{ url('/') }}">
    <link rel="dns-prefetch" href="{{ url('/') }}">
    @endif
    
    @if($useCDN)
    {{-- CDN CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @else
    {{-- Critical CSS: Preload essential stylesheets --}}
    <link rel="preload" href="{{ asset('vendor/bootstrap5/css/bootstrap.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ asset('vendor/fontawesome/css/all.min.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('vendor/bootstrap5/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    </noscript>
    
    {{-- Device-Optimized Performance CSS --}}
    <link rel="stylesheet" href="{{ asset('css/device-optimized.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tablet-optimized.css') }}">
    <link rel="stylesheet" href="{{ asset('css/desktop-optimized.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modern-design-system.css') }}">
    <link rel="stylesheet" href="{{ asset('css/global-ux-consistency.css') }}">
    @endif
    
    {{-- Conditional CSS Loading: Mobile vs Desktop --}}
    <script>
        (function() {
            var isMobile = window.innerWidth <= 768;
            var isTablet = window.innerWidth > 768 && window.innerWidth <= 1024;
            
            // Load mobile CSS only on mobile devices
            if (isMobile) {
                var mobileCSS = document.createElement('link');
                mobileCSS.rel = 'stylesheet';
                mobileCSS.href = '{{ asset('css/mobile-design-system.css') }}';
                mobileCSS.media = 'only x';
                mobileCSS.onload = function() { this.media = 'all'; };
                document.head.appendChild(mobileCSS);
                
                var mobileEnhancements = document.createElement('link');
                mobileEnhancements.rel = 'stylesheet';
                mobileEnhancements.href = '{{ asset('css/global-mobile-enhancements.css') }}';
                mobileEnhancements.media = 'only x';
                mobileEnhancements.onload = function() { this.media = 'all'; };
                document.head.appendChild(mobileEnhancements);
            }
        })();
    </script>
    
    @if(!$useCDN)
    {{-- Performance Optimizer - Load early --}}
    <script src="{{ asset('js/performance-optimizer.js') }}" defer></script>
    @endif
    
    @stack('styles')
</head>
<body>
<script>
// Send screen width to server for device detection
(function() {
    const screenWidth = window.innerWidth || screen.width;
    const isMobile = screenWidth <= 768;
    const isTablet = screenWidth > 768 && screenWidth <= 1024;
    
    // Store in sessionStorage for subsequent requests
    sessionStorage.setItem('screen_width', screenWidth);
    sessionStorage.setItem('is_mobile', isMobile);
    sessionStorage.setItem('is_tablet', isTablet);
    
    // Set cookie for server-side detection (expires in 1 hour)
    const expires = new Date();
    expires.setTime(expires.getTime() + (60 * 60 * 1000)); // 1 hour
    document.cookie = 'screen_width=' + screenWidth + ';expires=' + expires.toUTCString() + ';path=/';
    
    // If on mobile and not already on mobile view, redirect with screen_width parameter
    if (isMobile && !window.location.search.includes('screen_width') && !window.location.search.includes('mobile_view')) {
        const url = new URL(window.location.href);
        url.searchParams.set('screen_width', screenWidth);
        url.searchParams.set('mobile_view', '1');
        // Only redirect if we're not already on a mobile view route
        if (!url.pathname.includes('-mobile') && !document.querySelector('.modern-mobile-header')) {
            // Don't redirect immediately - let the page load first, then check
            setTimeout(function() {
                if (!document.querySelector('.modern-mobile-header') && !document.querySelector('.mobile-booking-container')) {
                    window.location.href = url.toString();
                }
            }, 100);
        }
    }
    
    // Intercept fetch requests to add header
    if (window.fetch) {
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            if (!args[1]) args[1] = {};
            args[1].headers = args[1].headers || {};
            if (typeof args[1].headers === 'object' && !(args[1].headers instanceof Headers)) {
                args[1].headers['X-Screen-Width'] = screenWidth;
            }
            return originalFetch.apply(this, args);
        };
    }
    
    // Also intercept XMLHttpRequest
    if (window.XMLHttpRequest) {
        const originalOpen = XMLHttpRequest.prototype.open;
        const originalSetRequestHeader = XMLHttpRequest.prototype.setRequestHeader;
        XMLHttpRequest.prototype.open = function(method, url, ...rest) {
            this._url = url;
            return originalOpen.apply(this, [method, url, ...rest]);
        };
        XMLHttpRequest.prototype.setRequestHeader = function(name, value) {
            if (this._url && !this._screenWidthSent) {
                try {
                    originalSetRequestHeader.call(this, 'X-Screen-Width', screenWidth);
                    this._screenWidthSent = true;
                } catch(e) {}
            }
            return originalSetRequestHeader.apply(this, arguments);
        };
    }
})();
</script>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary d-none d-md-block">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-calendar-alt me-2"></i>KHB Booths
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('booths.index') }}">Booths</a>
                    </li>
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('booths.my-booths') }}">My Booths</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('clients.index') }}">Clients</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('books.index') }}">Bookings</a>
                    </li>
                    @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.index') }}">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('settings.index') }}">Settings</a>
                    </li>
                    @endif
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i>{{ auth()->user()->username }}
                            @if(auth()->user()->isAdmin())
                                <span class="badge bg-danger ms-1">Admin</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-4" id="main-content" style="min-height: calc(100vh - 60px);">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any() && !request()->routeIs('login'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

@if($useCDN)
{{-- CDN JavaScript --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom@4.5.1/dist/panzoom.min.js"></script>
@else
{{-- Performance: Preload critical JavaScript with high priority --}}
<link rel="preload" href="{{ asset('vendor/jquery/jquery-3.7.0.min.js') }}" as="script" fetchpriority="high">
<link rel="preload" href="{{ asset('vendor/bootstrap5/js/bootstrap.bundle.min.js') }}" as="script" fetchpriority="high">
    
{{-- Critical JavaScript: Load with defer (non-blocking) --}}
<script src="{{ asset('vendor/jquery/jquery-3.7.0.min.js') }}" defer></script>
<script src="{{ asset('vendor/bootstrap5/js/bootstrap.bundle.min.js') }}" defer></script>

{{-- Performance Optimizer - Load early --}}
<script src="{{ asset('js/performance-optimizer.js') }}" defer></script>
@endif
    
{{-- Non-Critical JavaScript: Lazy load only when needed --}}
<script>
    (function() {
        'use strict';
        
        function loadScript(src, callback) {
            var script = document.createElement('script');
            script.src = src;
            script.async = true;
            if (callback) script.onload = callback;
            document.head.appendChild(script);
        }
        
        // Wait for DOM and jQuery to be ready
        function whenReady(callback) {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', callback);
            } else {
                callback();
            }
        }
        
        whenReady(function() {
            // Wait for jQuery
            var checkJQuery = setInterval(function() {
                if (typeof jQuery !== 'undefined') {
                    clearInterval(checkJQuery);
                    
                    // Load SweetAlert2 (lightweight, commonly used)
                    loadScript('{{ asset('vendor/sweetalert2/js/sweetalert2.min.js') }}');
                    
                    // Load Panzoom only if needed (check for panzoom elements)
                    if (document.querySelector('[data-panzoom], .panzoom, canvas, svg')) {
                        loadScript('{{ asset('vendor/panzoom/panzoom.min.js') }}');
                    }
                }
            }, 50);
        });
    })();
</script>
    
    <!-- Custom Notification System -->
    <script>
    // Custom notification functions to replace browser alerts
    window.showNotification = function(message, type = 'info', title = null) {
        const config = {
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: type === 'error' ? 5000 : 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        };

        const icons = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info',
            'question': 'question'
        };

        return Swal.fire({
            ...config,
            icon: icons[type] || 'info',
            title: title || (type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : 'Info'),
            text: message
        });
    };

    // Copy text to clipboard
    window.copyToClipboard = function(text) {
        // Try modern clipboard API first
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text).then(function() {
                return true;
            }).catch(function(err) {
                console.error('Failed to copy using clipboard API:', err);
                return fallbackCopyToClipboard(text);
            });
        } else {
            // Fallback for older browsers
            return fallbackCopyToClipboard(text);
        }
    };

    // Fallback copy method for older browsers
    function fallbackCopyToClipboard(text) {
        return new Promise(function(resolve, reject) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                if (successful) {
                    resolve(true);
                } else {
                    reject(new Error('Copy command failed'));
                }
            } catch (err) {
                document.body.removeChild(textArea);
                reject(err);
            }
        });
    }

    // Replace alert() with custom notification
    window.customAlert = function(message, type = 'info', title = null) {
        const isError = type === 'error';
        const fullMessage = title ? (title + '\n\n' + message) : message;
        
        return Swal.fire({
            icon: type === 'error' ? 'error' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info',
            title: title || (type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : type === 'warning' ? 'Warning!' : 'Information'),
            html: message + (isError ? '<br><br><button id="copyErrorBtn" class="btn btn-sm btn-outline-light mt-2" style="font-size: 12px;"><i class="fas fa-copy me-1"></i>Copy Error Message</button>' : ''),
            confirmButtonText: 'OK',
            confirmButtonColor: type === 'error' ? '#dc3545' : type === 'success' ? '#28a745' : type === 'warning' ? '#ffc107' : '#007bff',
            buttonsStyling: true,
            customClass: {
                popup: 'custom-swal-popup'
            },
            didOpen: function() {
                // Add copy button functionality for error messages
                if (isError) {
                    const copyBtn = document.getElementById('copyErrorBtn');
                    if (copyBtn) {
                        copyBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            copyToClipboard(fullMessage).then(function() {
                                // Show success feedback
                                const originalHtml = copyBtn.innerHTML;
                                copyBtn.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
                                copyBtn.classList.remove('btn-outline-light');
                                copyBtn.classList.add('btn-success');
                                copyBtn.disabled = true;
                                
                                // Reset after 2 seconds
                                setTimeout(function() {
                                    copyBtn.innerHTML = originalHtml;
                                    copyBtn.classList.remove('btn-success');
                                    copyBtn.classList.add('btn-outline-light');
                                    copyBtn.disabled = false;
                                }, 2000);
                            }).catch(function(err) {
                                console.error('Failed to copy:', err);
                                // Show error feedback
                                const originalHtml = copyBtn.innerHTML;
                                copyBtn.innerHTML = '<i class="fas fa-times me-1"></i>Failed';
                                copyBtn.classList.remove('btn-outline-light');
                                copyBtn.classList.add('btn-danger');
                                
                                setTimeout(function() {
                                    copyBtn.innerHTML = originalHtml;
                                    copyBtn.classList.remove('btn-danger');
                                    copyBtn.classList.add('btn-outline-light');
                                }, 2000);
                            });
                        });
                    }
                }
            }
        });
    };

    // Replace confirm() with custom confirmation dialog
    window.customConfirm = function(message, title = 'Confirm Action', confirmText = 'Yes', cancelText = 'No') {
        return Swal.fire({
            title: title,
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            buttonsStyling: true,
            reverseButtons: true,
            customClass: {
                popup: 'custom-swal-popup'
            }
        }).then((result) => {
            return result.isConfirmed;
        });
    };

    // Standalone copy function that can be used anywhere
    // Usage: copyToClipboard('text to copy').then(() => console.log('Copied!'));
    window.copyTextToClipboard = function(text, showNotification = true) {
        return copyToClipboard(text).then(function() {
            if (showNotification) {
                showNotification('Text copied to clipboard!', 'success');
            }
            return true;
        }).catch(function(err) {
            console.error('Failed to copy:', err);
            if (showNotification) {
                showNotification('Failed to copy text. Please try manually.', 'error');
            }
            return false;
        });
    };

    // Override native alert and confirm (optional - can be removed if you prefer explicit calls)
    // Uncomment these if you want to automatically replace all alert() and confirm() calls
    /*
    window.alert = function(message) {
        return window.customAlert(message, 'info');
    };
    
    window.confirm = function(message) {
        return window.customConfirm(message, 'Confirm', 'OK', 'Cancel');
    };
    */
    </script>
    
    <style>
    .custom-swal-popup {
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    
    #copyErrorBtn {
        transition: all 0.3s ease;
    }
    
    #copyErrorBtn:hover {
        transform: scale(1.05);
    }
    
    /* Make sure copy button is visible in error modals */
    .swal2-popup .btn-outline-light {
        border-color: rgba(255, 255, 255, 0.5);
        color: #fff;
    }
    
    .swal2-popup .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.8);
    }
    </style>
    
    @stack('scripts')
    
    <style>
    /* Mobile Overrides - Force Mobile App Design - COMPLETE OLD UI REMOVAL */
    @media (max-width: 768px) {
        /* COMPLETELY HIDE navbar on mobile */
        nav.navbar,
        .navbar,
        .navbar-expand-lg,
        .navbar-brand,
        .navbar-nav,
        .navbar-toggler,
        .navbar-collapse {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            opacity: 0 !important;
            position: absolute !important;
            left: -9999px !important;
            width: 0 !important;
        }
        
        /* Remove ALL main padding on mobile (except login page) */
        main.container-fluid:not(:has(.login-container)),
        #main-content:not(:has(.login-container)),
        main:not(:has(.login-container)) {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            width: 100% !important;
        }
        
        /* Full width content */
        body {
            overflow-x: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        
        /* Hide any desktop-specific elements */
        .sidebar,
        .content-wrapper,
        .main-sidebar,
        .content-header,
        .breadcrumb,
        .page-header {
            display: none !important;
            visibility: hidden !important;
        }
    }
    
    /* Additional mobile view detection - hide old UI when mobile view class exists */
    html.mobile-view nav.navbar,
    html.mobile-view .navbar,
    body.mobile-view nav.navbar,
    body.mobile-view .navbar,
    .mobile-view nav.navbar,
    .mobile-view .navbar {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    /* Login page specific - ensure main doesn't interfere */
    body:has(.login-container) main.container-fluid {
        padding: 0 !important;
        margin: 0 !important;
        min-height: 100vh !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    </style>
</body>
</html>

