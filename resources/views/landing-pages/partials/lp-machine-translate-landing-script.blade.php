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

    function lpI18nRoot(form) {
        return form.getAttribute('data-lp-i18n-root') || 'visual';
    }

    function lpI18nFieldName(form, loc, key) {
        return lpI18nRoot(form) + '[i18n][' + loc + '][' + key + ']';
    }

    /** Non-English locales allowed for machine translate: checked boxes on main form, or data-lp-enabled-non-en on copy hub. */
    function lpEnabledNonEnLocales(form) {
        var out = [];
        form.querySelectorAll('input[name="enabled_locales[]"]:checked').forEach(function (cb) {
            if (cb.value !== 'en') {
                out.push(cb.value);
            }
        });
        if (out.length) {
            return out;
        }
        try {
            var parsed = JSON.parse(form.getAttribute('data-lp-enabled-non-en') || '[]');
            return Array.isArray(parsed) ? parsed.filter(function (c) { return c && c !== 'en'; }) : [];
        } catch (e) {
            return [];
        }
    }

    function lpSlots() {
        return window.lpLandingTranslateSlots || { tripPhaseSlots: 8, tripSubSlots: 4, promotionTierSlots: 8 };
    }

    function lpTripInput(form, loc, pi, field) {
        return form.querySelector('[name="' + lpI18nRoot(form) + '[i18n][' + loc + '][trip_phases][' + pi + '][' + field + ']"]');
    }

    function lpTripSubInput(form, loc, pi, si, field) {
        return form.querySelector('[name="' + lpI18nRoot(form) + '[i18n][' + loc + '][trip_phases][' + pi + '][subsections][' + si + '][' + field + ']"]');
    }

    function lpCollectTripPhasesJson(form, loc) {
        var s = lpSlots();
        var phases = [];
        for (var pi = 0; pi < s.tripPhaseSlots; pi++) {
            function gv(f) {
                var el = lpTripInput(form, loc, pi, f);
                return el ? String(el.value || '').trim() : '';
            }
            var phase = {
                label: gv('label'),
                date: gv('date'),
                status: gv('status'),
                seats_left: gv('seats_left'),
                intro: gv('intro'),
                feature_image: gv('feature_image'),
                subsections: []
            };
            for (var si = 0; si < s.tripSubSlots; si++) {
                var tEl = lpTripSubInput(form, loc, pi, si, 'title');
                var dEl = lpTripSubInput(form, loc, pi, si, 'detail');
                phase.subsections.push({
                    title: tEl ? String(tEl.value || '').trim() : '',
                    detail: dEl ? String(dEl.value || '').trim() : ''
                });
            }
            phases.push(phase);
        }
        return JSON.stringify(phases);
    }

    function lpTripPhasesHasAnyText(jsonStr) {
        try {
            var a = JSON.parse(jsonStr);
            if (!Array.isArray(a)) {
                return false;
            }
            for (var i = 0; i < a.length; i++) {
                var p = a[i];
                if (!p || typeof p !== 'object') {
                    continue;
                }
                if (String(p.label || '').trim() || String(p.date || '').trim() || String(p.intro || '').trim()) {
                    return true;
                }
                var subs = p.subsections || [];
                for (var j = 0; j < subs.length; j++) {
                    var sub = subs[j] || {};
                    if (String(sub.title || '').trim() || String(sub.detail || '').trim()) {
                        return true;
                    }
                }
            }
        } catch (err) {
            return false;
        }
        return false;
    }

    function lpApplyTripPhasesJson(form, loc, jsonStr) {
        var s = lpSlots();
        var phases;
        try {
            phases = JSON.parse(jsonStr);
        } catch (e) {
            return;
        }
        if (!Array.isArray(phases)) {
            return;
        }
        for (var pi = 0; pi < s.tripPhaseSlots; pi++) {
            var p = phases[pi] || {};
            function setf(f, v) {
                var el = lpTripInput(form, loc, pi, f);
                if (el) {
                    el.value = v != null ? String(v) : '';
                }
            }
            setf('label', p.label || '');
            setf('date', p.date || '');
            setf('status', p.status || '');
            setf('seats_left', p.seats_left || '');
            setf('intro', p.intro || '');
            setf('feature_image', p.feature_image || '');
            var subs = p.subsections || [];
            for (var si = 0; si < s.tripSubSlots; si++) {
                var sub = subs[si] || {};
                var te = lpTripSubInput(form, loc, pi, si, 'title');
                var de = lpTripSubInput(form, loc, pi, si, 'detail');
                if (te) {
                    te.value = sub.title || '';
                }
                if (de) {
                    de.value = sub.detail || '';
                }
            }
        }
    }

    function lpPromoInput(form, loc, field) {
        return form.querySelector('[name="' + lpI18nRoot(form) + '[i18n][' + loc + '][promotion_discounts][' + field + ']"]');
    }

    function lpPromoTierInput(form, loc, ti, field) {
        return form.querySelector('[name="' + lpI18nRoot(form) + '[i18n][' + loc + '][promotion_discounts][tiers][' + ti + '][' + field + ']"]');
    }

    function lpCollectPromotionDiscountsJson(form, loc) {
        var s = lpSlots();
        var baseEl = lpPromoInput(form, loc, 'base_price_text');
        var introEl = lpPromoInput(form, loc, 'intro_text');
        var tiers = [];
        for (var ti = 0; ti < s.promotionTierSlots; ti++) {
            var pEl = lpPromoTierInput(form, loc, ti, 'participants');
            var oEl = lpPromoTierInput(form, loc, ti, 'off_each');
            var lEl = lpPromoTierInput(form, loc, ti, 'label');
            var pn = pEl && pEl.value !== '' ? parseInt(pEl.value, 10) : 0;
            var on = oEl && oEl.value !== '' ? parseInt(oEl.value, 10) : 0;
            if (isNaN(pn)) {
                pn = 0;
            }
            if (isNaN(on)) {
                on = 0;
            }
            tiers.push({
                participants: pn,
                off_each: on,
                label: lEl ? String(lEl.value || '').trim() : ''
            });
        }
        return JSON.stringify({
            base_price_text: baseEl ? String(baseEl.value || '').trim() : '',
            intro_text: introEl ? String(introEl.value || '').trim() : '',
            tiers: tiers
        });
    }

    function lpPromotionHasAnyText(jsonStr) {
        try {
            var o = JSON.parse(jsonStr);
            if (!o || typeof o !== 'object') {
                return false;
            }
            if (String(o.base_price_text || '').trim() || String(o.intro_text || '').trim()) {
                return true;
            }
            var tiers = o.tiers || [];
            for (var i = 0; i < tiers.length; i++) {
                var row = tiers[i] || {};
                if (String(row.label || '').trim()) {
                    return true;
                }
                if ((parseInt(row.participants, 10) > 0 && parseInt(row.off_each, 10) > 0)) {
                    return true;
                }
            }
        } catch (e) {
            return false;
        }
        return false;
    }

    function lpApplyPromotionDiscountsJson(form, loc, jsonStr) {
        var s = lpSlots();
        var o;
        try {
            o = JSON.parse(jsonStr);
        } catch (e) {
            return;
        }
        if (!o || typeof o !== 'object') {
            return;
        }
        var b = lpPromoInput(form, loc, 'base_price_text');
        var introInp = lpPromoInput(form, loc, 'intro_text');
        if (b) {
            b.value = o.base_price_text || '';
        }
        if (introInp) {
            introInp.value = o.intro_text || '';
        }
        var tiers = o.tiers || [];
        for (var ti = 0; ti < s.promotionTierSlots; ti++) {
            var row = tiers[ti] || {};
            var pEl = lpPromoTierInput(form, loc, ti, 'participants');
            var oEl = lpPromoTierInput(form, loc, ti, 'off_each');
            var lEl = lpPromoTierInput(form, loc, ti, 'label');
            if (pEl) {
                pEl.value = row.participants > 0 ? String(row.participants) : '';
            }
            if (oEl) {
                oEl.value = row.off_each > 0 ? String(row.off_each) : '';
            }
            if (lEl) {
                lEl.value = row.label || '';
            }
        }
    }

    function lpGetPromoShowChecked(form, loc) {
        var cb = form.querySelector('#lp-promo-show-' + loc);
        return !!(cb && cb.checked);
    }

    function lpSetPromoShowChecked(form, loc, on) {
        var cb = form.querySelector('#lp-promo-show-' + loc);
        if (cb) {
            cb.checked = !!on;
        }
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
                            if (key === 'trip_phases') {
                                lpApplyTripPhasesJson(form, loc, bag[key] || '[]');
                                return;
                            }
                            if (key === 'promotion_discounts') {
                                lpApplyPromotionDiscountsJson(form, loc, bag[key] || '{}');
                                lpSetPromoShowChecked(form, loc, lpGetPromoShowChecked(form, 'en'));
                                return;
                            }
                            var target = fieldByName(form, lpI18nFieldName(form, loc, key));
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

        var enEl = fieldByName(form, lpI18nFieldName(form, 'en', fieldKey));
        var payloadVal = '';
        if (fieldKey === 'trip_phases') {
            payloadVal = lpCollectTripPhasesJson(form, 'en');
            if (!lpTripPhasesHasAnyText(payloadVal)) {
                alert('Add English trip phase labels, dates, intro, or sub-category text first (English tab).');
                return;
            }
        } else if (fieldKey === 'promotion_discounts') {
            payloadVal = lpCollectPromotionDiscountsJson(form, 'en');
            if (!lpPromotionHasAnyText(payloadVal)) {
                alert('Add English promotion base price, intro, or tier details first (English tab).');
                return;
            }
        } else {
            var enVal = enEl ? String(enEl.value || '').trim() : '';
            if (!enVal) {
                alert('Enter English text for this field in the English tab first.');
                return;
            }
            payloadVal = enEl ? enEl.value : '';
        }

        var enabled = lpEnabledNonEnLocales(form);
        if (enabled.indexOf(targetLocale) === -1) {
            alert('Enable this language under “Languages on public page” first.');
            return;
        }

        var prevHtml = t.innerHTML;
        t.disabled = true;
        t.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        var oneField = {};
        if (fieldKey === 'trip_phases' || fieldKey === 'promotion_discounts') {
            oneField[fieldKey] = payloadVal;
        } else {
            oneField[fieldKey] = enEl.value;
        }

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

        var targets = lpEnabledNonEnLocales(form);

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
            if (key === 'trip_phases') {
                var tripJ = lpCollectTripPhasesJson(form, 'en');
                if (lpTripPhasesHasAnyText(tripJ)) {
                    fields[key] = tripJ;
                    anyNonEmpty = true;
                }
                return;
            }
            if (key === 'promotion_discounts') {
                var promoJ = lpCollectPromotionDiscountsJson(form, 'en');
                if (lpPromotionHasAnyText(promoJ)) {
                    fields[key] = promoJ;
                    anyNonEmpty = true;
                }
                return;
            }
            var el = fieldByName(form, lpI18nFieldName(form, 'en', key));
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
