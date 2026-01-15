/**
 * Performance Optimizer
 * Optimizes loading and interactions for smooth UX
 */

(function() {
    'use strict';
    
    // Detect device type
    const deviceType = (function() {
        const width = window.innerWidth;
        if (width <= 768) return 'mobile';
        if (width <= 1024) return 'tablet';
        return 'desktop';
    })();
    
    // Intersection Observer for lazy loading
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    img.classList.add('loaded');
                    observer.unobserve(img);
                }
            }
        });
    }, {
        rootMargin: '50px'
    });
    
    // Lazy load images
    function initLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => imageObserver.observe(img));
    }
    
    // Preload critical resources
    function preloadCritical() {
        if (deviceType === 'mobile') {
            // Preload mobile-specific assets
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'style';
            link.href = '/css/mobile-design-system.css';
            document.head.appendChild(link);
        }
    }
    
    // Optimize animations based on device
    function optimizeAnimations() {
        if (deviceType === 'mobile') {
            // Reduce animation complexity on mobile
            document.documentElement.style.setProperty('--animation-duration', '0.2s');
        } else {
            document.documentElement.style.setProperty('--animation-duration', '0.3s');
        }
    }
    
    // Debounce resize events
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // Re-optimize on resize
            optimizeAnimations();
        }, 250);
    });
    
    // Request idle callback for non-critical tasks
    if ('requestIdleCallback' in window) {
        requestIdleCallback(function() {
            initLazyLoading();
            // Load non-critical resources
        }, { timeout: 2000 });
    } else {
        // Fallback
        setTimeout(initLazyLoading, 1000);
    }
    
    // Immediate optimizations
    optimizeAnimations();
    preloadCritical();
    
    // Performance monitoring
    if ('PerformanceObserver' in window) {
        try {
            const perfObserver = new PerformanceObserver((list) => {
                for (const entry of list.getEntries()) {
                    if (entry.entryType === 'largest-contentful-paint') {
                        console.log('LCP:', entry.renderTime || entry.loadTime);
                    }
                }
            });
            perfObserver.observe({ entryTypes: ['largest-contentful-paint', 'layout-shift'] });
        } catch (e) {
            // Ignore errors
        }
    }
    
    // Expose device type
    window.deviceType = deviceType;
})();
