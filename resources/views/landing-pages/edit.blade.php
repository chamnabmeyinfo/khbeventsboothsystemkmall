@extends('layouts.adminlte')

@include('landing-pages.partials.admin-looker-setup')

@section('title', 'Edit Landing Page')
@section('page-title', 'Edit Landing Page')
@section('breadcrumb', 'Landing Pages / Edit')

@section('content')
<div class="looker-dashboard">
    <header class="looker-header">
        <div class="looker-header-title">
            <h1>Edit: {{ $landingPage->name }}</h1>
            <p>Update settings, visuals, and multilingual copy.</p>
        </div>
        <div class="looker-actions flex-wrap">
            <a href="{{ route('landing-pages.reporting', $landingPage) }}" class="action-btn action-btn-secondary">
                <i class="fas fa-chart-line" aria-hidden="true"></i> Leads ({{ (int) ($landingPage->leads_count ?? 0) }})
            </a>
            <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Back to list
            </a>
        </div>
    </header>

    <div class="canvas-panel lp-landing-form-shell">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-edit" aria-hidden="true"></i> Page configuration</h2>
        </div>
        <form id="lpLandingPageAdminForm" action="{{ route('landing-pages.update', $landingPage) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')
            @include('landing-pages.partials.form', ['landingPage' => $landingPage])
            <div class="card-footer lp-sticky-card-footer lp-form-footer-actions">
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="fas fa-save" aria-hidden="true"></i> Update
                </button>
                <a href="{{ route('landing-pages.index') }}" class="action-btn action-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var translateFromEnglishUrl = @json(route('landing-pages.translate-from-english', $landingPage));
    var fieldKeys = @json(\App\Services\LandingTextTranslationService::visualI18nFieldKeys());
    var tokenEl = document.querySelector('meta[name="csrf-token"]');
    var csrf = tokenEl ? tokenEl.getAttribute('content') : '';

    function fieldByName(form, name) {
        for (var i = 0; i < form.elements.length; i++) {
            if (form.elements[i].name === name) {
                return form.elements[i];
            }
        }
        return null;
    }

    function showTranslateError(res) {
        var msg = 'Translation failed.';
        var d = res.data;
        if (d && d.message) {
            msg = d.message;
        } else if (d && d.errors) {
            var keys = Object.keys(d.errors);
            if (keys.length) {
                var first = d.errors[keys[0]];
                if (Array.isArray(first) && first[0]) {
                    msg = first[0];
                }
            }
        }
        alert(msg);
    }

    function postTranslate(form, fields, targetLocales, onDone) {
        fetch(translateFromEnglishUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                fields: fields,
                target_locales: targetLocales
            })
        })
            .then(function (r) {
                return r.json().then(function (data) {
                    return { ok: r.ok, status: r.status, data: data };
                });
            })
            .then(function (res) {
                if (res.ok && res.data && res.data.ok && res.data.locales) {
                    Object.keys(res.data.locales).forEach(function (loc) {
                        var bag = res.data.locales[loc];
                        Object.keys(bag).forEach(function (key) {
                            var target = fieldByName(form, 'visual[i18n][' + loc + '][' + key + ']');
                            if (target) {
                                target.value = bag[key] || '';
                                target.dispatchEvent(new Event('input', { bubbles: true }));
                                target.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                            if (key === 'trip_activity_gallery_slides_text') {
                                var tripFlag = form.querySelector('input[data-lp-trip-from-ta="' + loc + '"]');
                                if (tripFlag) {
                                    tripFlag.value = '1';
                                }
                            }
                        });
                    });
                } else {
                    showTranslateError(res);
                }
            })
            .catch(function () {
                alert('Network error. Please try again.');
            })
            .finally(function () {
                if (typeof onDone === 'function') {
                    onDone();
                }
            });
    }

    document.addEventListener('click', function (e) {
        var t = e.target.closest('.lp-translate-field-btn');
        if (!t) {
            return;
        }
        e.preventDefault();
        var form = t.closest('form');
        if (!form) {
            return;
        }
        var fieldKey = t.getAttribute('data-field-key');
        var targetLocale = t.getAttribute('data-target-locale');
        if (!fieldKey || !targetLocale) {
            return;
        }
        if (fieldKeys.indexOf(fieldKey) === -1) {
            return;
        }

        if (fieldKey === 'trip_activity_gallery_slides_text' && typeof window.lpSyncTripSlidesForLocale === 'function') {
            var enTaFlag = form.querySelector('input[data-lp-trip-from-ta="en"]');
            if (enTaFlag && String(enTaFlag.value || '') !== '1') {
                window.lpSyncTripSlidesForLocale(form, 'en');
            }
        }

        var enEl = fieldByName(form, 'visual[i18n][en][' + fieldKey + ']');
        var enVal = enEl ? String(enEl.value || '').trim() : '';
        if (!enVal) {
            alert('Enter English text for this field in the English tab first.');
            return;
        }

        var enabled = [];
        form.querySelectorAll('input[name="enabled_locales[]"]:checked').forEach(function (cb) {
            if (cb.value !== 'en') {
                enabled.push(cb.value);
            }
        });
        if (enabled.indexOf(targetLocale) === -1) {
            alert('Enable this language under “Languages on public page” first.');
            return;
        }

        var prevHtml = t.innerHTML;
        t.disabled = true;
        t.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        var oneField = {};
        oneField[fieldKey] = enEl.value; // raw value for API (not trimmed)

        postTranslate(form, oneField, [targetLocale], function () {
            t.disabled = false;
            t.innerHTML = prevHtml;
        });
    });

    var btn = document.getElementById('lpTranslateFromEnglishBtn');
    if (!btn) {
        return;
    }

    btn.addEventListener('click', function () {
        var form = btn.closest('form');
        if (!form) {
            return;
        }

        var targets = [];
        form.querySelectorAll('input[name="enabled_locales[]"]:checked').forEach(function (cb) {
            if (cb.value !== 'en') {
                targets.push(cb.value);
            }
        });

        if (targets.length === 0) {
            alert('Enable at least one language other than English under “Languages on public page”, then try again.');
            return;
        }

        if (typeof window.lpSyncTripSlidesForLocale === 'function') {
            var enTaFlagAll = form.querySelector('input[data-lp-trip-from-ta="en"]');
            if (enTaFlagAll && String(enTaFlagAll.value || '') !== '1') {
                window.lpSyncTripSlidesForLocale(form, 'en');
            }
        }

        var fields = {};
        var anyNonEmpty = false;
        fieldKeys.forEach(function (key) {
            var el = fieldByName(form, 'visual[i18n][en][' + key + ']');
            var v = el ? (el.value || '') : '';
            fields[key] = v;
            if (String(v).trim()) {
                anyNonEmpty = true;
            }
        });

        if (!anyNonEmpty) {
            alert('Please enter your English copy in the English tab first, then click “Update all languages from English”.');
            return;
        }

        var prevHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Translating…';

        postTranslate(form, fields, targets, function () {
            btn.disabled = false;
            btn.innerHTML = prevHtml;
        });
    });
})();
</script>
@endpush
