@extends('layouts.adminlte')

@section('title', 'Edit Landing Page')
@section('page-title', 'Edit Landing Page')
@section('breadcrumb', 'Landing Pages / Edit')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-2"></i>Edit Landing Page: {{ $landingPage->name }}</h3>
        </div>
        <form action="{{ route('landing-pages.update', $landingPage) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('landing-pages.partials.form', ['landingPage' => $landingPage])
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Update</button>
                <a href="{{ route('landing-pages.index') }}" class="btn btn-default">Cancel</a>
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
