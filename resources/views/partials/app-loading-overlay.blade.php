{{-- Global loading UI — showLoading() / hideLoading() --}}
<div id="loadingOverlay"
     class="loading-overlay"
     role="status"
     aria-live="polite"
     aria-busy="false"
     aria-label="Loading"
     data-default-label="Loading…">
    <div class="loading-overlay__card">
        {{-- Gradient orb: layered blur + cutout (Uiverse-style), see app-loading-overlay.css --}}
        <div class="loading-overlay__orb" aria-hidden="true">
            <div class="loading-overlay__orb-spin">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <p class="loading-overlay__label" id="loadingOverlayLabel">Loading…</p>
        <p class="loading-overlay__hint">Please wait</p>
    </div>
</div>
<script>
(function () {
    'use strict';
    function getOverlay() {
        return document.getElementById('loadingOverlay');
    }
    function getLabelEl() {
        return document.getElementById('loadingOverlayLabel');
    }
    window.showLoading = function (message) {
        var el = getOverlay();
        if (!el) return;
        var label = getLabelEl();
        var def = el.getAttribute('data-default-label') || 'Loading…';
        if (label) {
            label.textContent = (typeof message === 'string' && message.trim() !== '') ? message.trim() : def;
        }
        el.classList.add('active');
        el.setAttribute('aria-busy', 'true');
        document.body.classList.add('app-loading-active');
        var prevOverflow = document.body.getAttribute('data-prev-overflow');
        if (prevOverflow === null) {
            document.body.setAttribute('data-prev-overflow', document.body.style.overflow || '');
        }
        document.body.style.overflow = 'hidden';
    };
    window.hideLoading = function () {
        var el = getOverlay();
        if (!el) return;
        var label = getLabelEl();
        var def = el.getAttribute('data-default-label') || 'Loading…';
        if (label) {
            label.textContent = def;
        }
        el.classList.remove('active');
        el.setAttribute('aria-busy', 'false');
        document.body.classList.remove('app-loading-active');
        var prev = document.body.getAttribute('data-prev-overflow');
        if (prev !== null) {
            document.body.style.overflow = prev;
            document.body.removeAttribute('data-prev-overflow');
        }
    };
})();
// Reset overlay on every paint: prevents a stuck full-screen blocker if navigation was interrupted
// or the page was restored from bfcache after submit (global $('form').submit shows loading).
document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.hideLoading === 'function') {
        window.hideLoading();
    }
});
window.addEventListener('pageshow', function () {
    if (typeof window.hideLoading === 'function') {
        window.hideLoading();
    }
});
</script>
