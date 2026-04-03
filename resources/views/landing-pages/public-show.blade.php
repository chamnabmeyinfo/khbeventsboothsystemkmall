<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $documentTitle ?? ($landingPage->headline ?: $landingPage->name) }}</title>
    @if($landingPage->use_visual_builder && $landingPage->template_key === 'canton_fair_visual')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    @endif
    @if(!empty($landingPage->css_content))
        <style>{!! $landingPage->css_content !!}</style>
    @endif
</head>
<body>
@php
    $canInlineEdit = request()->boolean('editor') && auth()->check() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin();
@endphp
@if($landingPage->use_visual_builder && $landingPage->template_key === 'canton_fair_visual')
    @include('landing-pages.templates.canton-fair-visual', [
        'landingPage' => $landingPage,
        'visual' => isset($visual) ? (array) $visual : (array) ($landingPage->visual_content ?? []),
        'enabledLocales' => $enabledLocales ?? [],
        'localeLabels' => $localeLabels ?? [],
        'langSwitcherUrls' => $langSwitcherUrls ?? [],
        'currentLocale' => $currentLocale ?? 'en',
    ])
    @include('landing-pages.partials.continue-modal', ['visual' => isset($visual) ? (array) $visual : []])
@else
    {!! $landingPage->html_content !!}
@endif

<form id="landingContinueForm" method="POST" action="{{ route('landing-pages.public.continue', $landingPage) }}" style="display:none;">
    @csrf
    <input type="hidden" name="target" value="{{ $landingPage->redirect_url }}">
</form>

<script>
window.LandingPageConfig = {
    trackingUrl: @json(route('landing-pages.track', $landingPage)),
    leadUrl: @json(route('landing-pages.lead', $landingPage)),
    continueUrl: @json(route('landing-pages.public.continue', $landingPage)),
    csrfToken: @json(csrf_token()),
    inlineEditEnabled: @json($canInlineEdit),
    inlineUpdateUrl: @json($canInlineEdit ? route('landing-pages.visual-inline', $landingPage) : null),
    publishUrl: @json($canInlineEdit ? route('landing-pages.publish', $landingPage) : null),
    setActiveUrl: @json($canInlineEdit ? route('landing-pages.set-active', $landingPage) : null),
    inlineLocale: @json($canInlineEdit ? ($currentLocale ?? 'en') : null),
    useContinueModal: @json($landingPage->use_visual_builder && $landingPage->template_key === 'canton_fair_visual'),
    continueDefaultTarget: @json($landingPage->redirect_url ?? '/login'),
    currentLocale: @json($currentLocale ?? 'en')
};

window.trackLandingEvent = function(eventType, payload) {
    payload = payload || {};
    const body = Object.assign({ event_type: eventType }, payload);
    return fetch(window.LandingPageConfig.trackingUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.LandingPageConfig.csrfToken
        },
        body: JSON.stringify(body)
    }).catch(function() { return null; });
};

window.submitLandingLead = function(payload) {
    return fetch(window.LandingPageConfig.leadUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': window.LandingPageConfig.csrfToken
        },
        body: JSON.stringify(payload || {})
    }).catch(function() { return null; });
};

window._lpContinueTarget = null;

function openLpContinueModal() {
    var el = document.getElementById('lpContinueModal');
    if (!el) return;
    el.removeAttribute('hidden');
    el.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    var n = document.getElementById('lpContinueName');
    if (n) {
        setTimeout(function() { n.focus(); }, 50);
    }
}

function closeLpContinueModal() {
    var el = document.getElementById('lpContinueModal');
    if (!el) return;
    el.setAttribute('hidden', '');
    el.classList.remove('is-open');
    document.body.style.overflow = '';
}

window.landingContinue = function(targetUrl) {
    var form = document.getElementById('landingContinueForm');
    if (!form) return;
    var fallback = (window.LandingPageConfig && window.LandingPageConfig.continueDefaultTarget) ? window.LandingPageConfig.continueDefaultTarget : '/login';
    var finalTarget = targetUrl || fallback;
    if (window.LandingPageConfig && window.LandingPageConfig.inlineEditEnabled) {
        form.querySelector('input[name="target"]').value = finalTarget;
        form.submit();
        return;
    }
    if (window.LandingPageConfig && window.LandingPageConfig.useContinueModal && document.getElementById('lpContinueModal')) {
        window._lpContinueTarget = finalTarget;
        form.querySelector('input[name="target"]').value = finalTarget;
        openLpContinueModal();
        return;
    }
    form.querySelector('input[name="target"]').value = finalTarget;
    form.submit();
};

(function() {
    var modal = document.getElementById('lpContinueModal');
    if (!modal) return;
    var modalForm = document.getElementById('lpContinueModalForm');
    var cancelBtn = document.getElementById('lpContinueModalCancel');
    var closeBtn = document.getElementById('lpContinueModalClose');
    modal.addEventListener('click', function(ev) {
        if (ev.target === modal) {
            closeLpContinueModal();
        }
    });
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() { closeLpContinueModal(); });
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', function() { closeLpContinueModal(); });
    }
    document.addEventListener('keydown', function(ev) {
        if (ev.key === 'Escape' && modal.classList.contains('is-open')) {
            closeLpContinueModal();
        }
    });
    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!modalForm.checkValidity()) {
                modalForm.reportValidity();
                return;
            }
            var submitBtn = document.getElementById('lpContinueModalSubmit');
            if (submitBtn) {
                submitBtn.disabled = true;
            }
            var payload = {
                lead_name: (modalForm.name && modalForm.name.value) ? modalForm.name.value.trim() : '',
                lead_email: (modalForm.email && modalForm.email.value) ? modalForm.email.value.trim() : '',
                lead_phone: (modalForm.phone && modalForm.phone.value) ? modalForm.phone.value.trim() : '',
                source: 'continue-modal',
                meta: {
                    tripDate: (modalForm.tripDate && modalForm.tripDate.value) ? modalForm.tripDate.value : '',
                    locale: (window.LandingPageConfig && window.LandingPageConfig.currentLocale) ? window.LandingPageConfig.currentLocale : 'en'
                }
            };
            var p = (typeof submitLandingLead === 'function') ? submitLandingLead(payload) : Promise.resolve(null);
            p.catch(function() { return null; }).then(function() {
                if (typeof trackLandingEvent === 'function') {
                    trackLandingEvent('lead_submit', { cta_label: 'ContinueModal', source: 'continue-modal' });
                }
            }).finally(function() {
                closeLpContinueModal();
                var contForm = document.getElementById('landingContinueForm');
                var t = window._lpContinueTarget || ((window.LandingPageConfig && window.LandingPageConfig.continueDefaultTarget) ? window.LandingPageConfig.continueDefaultTarget : '/login');
                if (contForm) {
                    contForm.querySelector('input[name="target"]').value = t;
                    contForm.submit();
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            });
        });
    }
})();

if (window.LandingPageConfig.inlineEditEnabled) {
    (function() {
        var dirty = {};
        var imageDirty = {};
        var historyStack = [];
        var imageHistoryStack = [];
        var editingLocked = false;
        var lastSavedSnapshot = {};

        var toolbar = document.createElement('div');
        toolbar.style.position = 'fixed';
        toolbar.style.right = '12px';
        toolbar.style.top = '12px';
        toolbar.style.zIndex = '99999';
        toolbar.style.background = 'rgba(17,24,39,0.95)';
        toolbar.style.color = '#fff';
        toolbar.style.padding = '10px';
        toolbar.style.borderRadius = '10px';
        toolbar.style.boxShadow = '0 10px 30px rgba(0,0,0,0.25)';
        toolbar.style.width = '240px';
        toolbar.innerHTML = ''
          + '<div style="font-size:12px;margin-bottom:6px;">Inline Edit Mode</div>'
          + '<div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">'
          + '<button id="lpLockBtn" style="min-height:34px;padding:6px 8px;border:0;border-radius:8px;background:#6b7280;color:#fff;cursor:pointer;">Lock</button>'
          + '<button id="lpUndoBtn" style="min-height:34px;padding:6px 8px;border:0;border-radius:8px;background:#374151;color:#fff;cursor:pointer;">Undo</button>'
          + '<button id="lpResetBtn" style="min-height:34px;padding:6px 8px;border:0;border-radius:8px;background:#b45309;color:#fff;cursor:pointer;">Reset</button>'
          + '<button id="lpSaveInlineBtn" style="min-height:34px;padding:6px 8px;border:0;border-radius:8px;background:#2563eb;color:#fff;cursor:pointer;">Save</button>'
          + '<button id="lpPublishBtn" style="grid-column:1/3;min-height:36px;padding:6px 8px;border:0;border-radius:8px;background:#059669;color:#fff;cursor:pointer;">Publish Live</button>'
          + '</div>'
          + '<div style="font-size:11px;margin-top:6px;opacity:.85;">Alt+Click image to edit URL</div>';
        document.body.appendChild(toolbar);

        function setEditState(isLocked) {
            editingLocked = isLocked;
            document.querySelectorAll('[data-lv-key]').forEach(function(el) {
                el.setAttribute('contenteditable', isLocked ? 'false' : 'true');
                el.style.outline = isLocked ? 'none' : '1px dashed rgba(37,99,235,.45)';
                el.style.cursor = isLocked ? 'default' : 'text';
            });
            document.querySelectorAll('[data-lv-image-key]').forEach(function(el) {
                el.style.outline = isLocked ? 'none' : '1px dashed rgba(245,158,11,.45)';
                el.style.cursor = isLocked ? 'default' : 'pointer';
            });
            var lockBtn = document.getElementById('lpLockBtn');
            if (lockBtn) {
                lockBtn.textContent = isLocked ? 'Unlock' : 'Lock';
                lockBtn.style.background = isLocked ? '#1d4ed8' : '#6b7280';
            }
        }

        document.querySelectorAll('[data-lv-key]').forEach(function(el) {
            var key = el.getAttribute('data-lv-key');
            if (!key) return;
            lastSavedSnapshot[key] = el.innerText.trim();
            el.setAttribute('contenteditable', 'true');
            el.style.outline = '1px dashed rgba(37,99,235,.45)';
            el.style.cursor = 'text';
            el.addEventListener('beforeinput', function() {
                historyStack.push({
                    key: key,
                    previousValue: el.innerText.trim()
                });
            });
            el.addEventListener('input', function() {
                dirty[key] = el.innerText.trim();
            });
        });

        document.querySelectorAll('[data-lv-image-key]').forEach(function(el) {
            var key = el.getAttribute('data-lv-image-key');
            if (!key) return;
            lastSavedSnapshot[key] = el.getAttribute('data-lv-image-current') || '';
            el.style.outline = '1px dashed rgba(245,158,11,.45)';
            el.style.cursor = 'pointer';
            el.title = 'Alt+Click to edit image URL';
            el.addEventListener('click', function(ev) {
                if (editingLocked || !ev.altKey) return;
                ev.preventDefault();
                var current = el.getAttribute('data-lv-image-current') || '';
                var next = window.prompt('Set image URL/path for '+ key, current);
                if (next === null) return;
                imageHistoryStack.push({
                    key: key,
                    previousValue: current
                });
                imageDirty[key] = next;
                if (el.tagName.toLowerCase() === 'img') {
                    el.setAttribute('src', next);
                } else {
                    el.style.backgroundImage = "url('" + next.replace(/'/g, "\\'") + "')";
                }
                el.setAttribute('data-lv-image-current', next);
            });
        });

        var lockBtn = document.getElementById('lpLockBtn');
        if (lockBtn) {
            lockBtn.addEventListener('click', function() {
                setEditState(!editingLocked);
            });
        }

        var undoBtn = document.getElementById('lpUndoBtn');
        if (undoBtn) {
            undoBtn.addEventListener('click', function() {
                var imageStep = imageHistoryStack.pop();
                if (imageStep) {
                    var imageEl = document.querySelector('[data-lv-image-key="' + imageStep.key + '"]');
                    if (imageEl) {
                        if (imageEl.tagName.toLowerCase() === 'img') {
                            imageEl.setAttribute('src', imageStep.previousValue);
                        } else {
                            imageEl.style.backgroundImage = "url('" + imageStep.previousValue.replace(/'/g, "\\'") + "')";
                        }
                        imageEl.setAttribute('data-lv-image-current', imageStep.previousValue);
                        imageDirty[imageStep.key] = imageStep.previousValue;
                    }
                    return;
                }
                var step = historyStack.pop();
                if (!step) {
                    alert('Nothing to undo.');
                    return;
                }
                var target = document.querySelector('[data-lv-key="' + step.key + '"]');
                if (!target) return;
                target.innerText = step.previousValue || '';
                dirty[step.key] = target.innerText.trim();
            });
        }

        var resetBtn = document.getElementById('lpResetBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                Object.keys(lastSavedSnapshot).forEach(function(key) {
                    var txt = document.querySelector('[data-lv-key="' + key + '"]');
                    if (txt) {
                        txt.innerText = lastSavedSnapshot[key] || '';
                    }
                    var img = document.querySelector('[data-lv-image-key="' + key + '"]');
                    if (img) {
                        var snap = lastSavedSnapshot[key] || '';
                        if (img.tagName.toLowerCase() === 'img') {
                            img.setAttribute('src', snap);
                        } else {
                            img.style.backgroundImage = "url('" + snap.replace(/'/g, "\\'") + "')";
                        }
                        img.setAttribute('data-lv-image-current', snap);
                    }
                });
                dirty = {};
                imageDirty = {};
                historyStack = [];
                imageHistoryStack = [];
            });
        }

        var saveBtn = document.getElementById('lpSaveInlineBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                var fields = Object.assign({}, dirty, imageDirty);
                if (Object.keys(fields).length === 0) {
                    alert('No changes to save.');
                    return;
                }
                saveBtn.disabled = true;
                saveBtn.textContent = 'Saving...';
                fetch(window.LandingPageConfig.inlineUpdateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.LandingPageConfig.csrfToken
                    },
                    body: JSON.stringify({
                        fields: fields,
                        locale: window.LandingPageConfig.inlineLocale || 'en'
                    })
                }).then(function(r) { return r.json(); })
                  .then(function(data) {
                      if (!data || data.ok !== true) {
                          throw new Error((data && data.message) ? data.message : 'Save failed');
                      }
                      Object.keys(fields).forEach(function(k) { lastSavedSnapshot[k] = fields[k]; });
                      dirty = {};
                      imageDirty = {};
                      historyStack = [];
                      imageHistoryStack = [];
                      alert('Preview changes saved.');
                  })
                  .catch(function(err) {
                      alert('Save failed: ' + (err && err.message ? err.message : 'Unknown error'));
                  })
                  .finally(function() {
                      saveBtn.disabled = false;
                      saveBtn.textContent = 'Save Changes';
                  });
            });
        }

        var publishBtn = document.getElementById('lpPublishBtn');
        if (publishBtn) {
            publishBtn.addEventListener('click', function() {
                publishBtn.disabled = true;
                publishBtn.textContent = 'Publishing...';
                fetch(window.LandingPageConfig.publishUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.LandingPageConfig.csrfToken
                    }
                }).then(function() {
                    return fetch(window.LandingPageConfig.setActiveUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': window.LandingPageConfig.csrfToken
                        }
                    });
                }).then(function() {
                    alert('Landing page published and set active.');
                }).catch(function(err) {
                    alert('Publish failed: ' + (err && err.message ? err.message : 'Unknown error'));
                }).finally(function() {
                    publishBtn.disabled = false;
                    publishBtn.textContent = 'Publish Live';
                });
            });
        }

        setEditState(false);
    })();
}
</script>

@if($landingPage->allow_inline_scripts && !empty($landingPage->js_content))
    <script>{!! $landingPage->js_content !!}</script>
@endif
</body>
</html>
