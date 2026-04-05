@php
    $ord = (int) ($sectionOrdinal ?? 1);
    $slideRowsOld = old('visual.i18n.'.$loc.'.trip_activity_slide_keep');
    if (is_array($slideRowsOld) && $slideRowsOld !== []) {
        $tripActivitySlideRows = [];
        foreach ($slideRowsOld as $row) {
            if (! is_array($row)) {
                continue;
            }
            $p = \App\Models\LandingPage::sanitizeTripPhaseFeatureImagePath((string) ($row['path'] ?? ''));
            if ($p === '') {
                continue;
            }
            $tripActivitySlideRows[] = [
                'path' => $p,
                'caption' => (string) ($row['caption'] ?? ''),
                'headline' => (string) ($row['headline'] ?? ''),
                'title' => (string) ($row['title'] ?? ''),
            ];
        }
    } else {
        $tripActivitySlideRows = \App\Models\LandingPage::parseTripActivityGallerySlideRows($tripActivitySlidesText);
    }
    $tripActivitySlideRows = array_values($tripActivitySlideRows);
@endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. Trip activity — photo slider</strong>
        <small class="text-muted">Slideshow on the public page. Section order comes from <strong>Sections &amp; order</strong>. Upload images here — they are saved under <code>public/images/landing-pages/{{ old('slug', optional($landingPage ?? null)->slug ?? 'your-slug') }}/</code> automatically (no manual paths needed).</small>
    </div>
    <div class="card-body">
        <input type="hidden" name="{{ $pfx }}[trip_activity_slides_rebuild]" value="1">
        <input type="hidden" name="{{ $pfx }}[trip_activity_from_textarea]" value="{{ old('visual.i18n.'.$loc.'.trip_activity_from_textarea', '0') }}" data-lp-trip-from-ta="{{ $loc }}">

        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0" for="lp-trip-title-{{ $loc }}">Section title</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_activity_gallery_title', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" id="lp-trip-title-{{ $loc }}" name="{{ $pfx }}[trip_activity_gallery_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.trip_activity_gallery_title', $vloc['trip_activity_gallery_title'] ?? '') }}" placeholder="Trip highlights" maxlength="255">
        </div>

        <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <span class="mb-0 font-weight-bold">Slides</span>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_activity_gallery_slides_text', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <p class="small text-muted mb-2">Per slide: optional <strong>headline</strong> (small line), <strong>title</strong> (main line), and <strong>caption</strong> (short description). Shown on the public slider overlay. Leave all slides removed and no new uploads to hide this block.</p>

            @forelse($tripActivitySlideRows as $idx => $slide)
                <div class="lp-trip-slide-row card mb-2 p-2 border" data-locale="{{ $loc }}" data-lp-trip-slide-row>
                    <div class="row align-items-start">
                        <div class="col-md-4 col-lg-3 mb-2 mb-md-0">
                            <img src="{{ asset($slide['path']) }}" alt="" class="img-fluid rounded border bg-light" style="max-height: 140px; width: 100%; object-fit: cover;">
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <input type="hidden" name="{{ $pfx }}[trip_activity_slide_keep][{{ $idx }}][path]" value="{{ e($slide['path']) }}" data-lp-trip-slide-path autocomplete="off">
                            <div class="form-group mb-2">
                                <label class="small mb-0 font-weight-bold" for="lp-trip-hl-{{ $loc }}-{{ $idx }}">Slide headline <span class="text-muted font-weight-normal">(optional, small text)</span></label>
                                <input type="text" id="lp-trip-hl-{{ $loc }}-{{ $idx }}" class="form-control form-control-sm" name="{{ $pfx }}[trip_activity_slide_keep][{{ $idx }}][headline]" value="{{ e($slide['headline'] ?? '') }}" maxlength="200" data-lp-trip-slide-headline placeholder="e.g. Day 2 · Morning" autocomplete="off">
                            </div>
                            <div class="form-group mb-2">
                                <label class="small mb-0 font-weight-bold" for="lp-trip-st-{{ $loc }}-{{ $idx }}">Slide title <span class="text-muted font-weight-normal">(optional, prominent)</span></label>
                                <input type="text" id="lp-trip-st-{{ $loc }}-{{ $idx }}" class="form-control form-control-sm" name="{{ $pfx }}[trip_activity_slide_keep][{{ $idx }}][title]" value="{{ e($slide['title'] ?? '') }}" maxlength="255" data-lp-trip-slide-title placeholder="e.g. Factory visit" autocomplete="off">
                            </div>
                            <div class="form-group mb-2">
                                <label class="small mb-0" for="lp-trip-cap-{{ $loc }}-{{ $idx }}">Caption <span class="text-muted">(optional)</span></label>
                                <input type="text" id="lp-trip-cap-{{ $loc }}-{{ $idx }}" class="form-control form-control-sm" name="{{ $pfx }}[trip_activity_slide_keep][{{ $idx }}][caption]" value="{{ e($slide['caption']) }}" maxlength="500" data-lp-trip-slide-caption placeholder="Short supporting line" autocomplete="off">
                            </div>
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input lp-trip-slide-remove" id="lp-trip-rm-{{ $loc }}-{{ $idx }}" name="{{ $pfx }}[trip_activity_slide_keep][{{ $idx }}][remove]" value="1">
                                <label class="custom-control-label small" for="lp-trip-rm-{{ $loc }}-{{ $idx }}">Remove this slide</label>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted small mb-2">No slides yet. Use <strong>Add images</strong> below.</p>
            @endforelse

            <div class="form-group mb-3">
                <label class="d-block" for="lp-trip-new-{{ $loc }}">Add images</label>
                <input type="file" id="lp-trip-new-{{ $loc }}" name="visual_trip_activity_new_slides[{{ $loc }}][]" class="form-control-file" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                <small class="text-muted d-block mt-1">JPG, PNG, GIF, or WebP. Max 8 MB per file. Multiple selection allowed; new files are added after existing slides.</small>
            </div>

            <details class="mb-0">
                <summary class="small text-muted" style="cursor: pointer;">Advanced: edit as text (path per line)</summary>
                <label for="lp-trip-ta-{{ $loc }}" class="sr-only">Trip activity slides — raw lines for translation tools</label>
                <textarea id="lp-trip-ta-{{ $loc }}" name="{{ $pfx }}[trip_activity_gallery_slides_text]" class="form-control font-monospace mt-2" rows="6" maxlength="16000" aria-describedby="lp-trip-ta-hint-{{ $loc }}" data-lp-trip-slides-textarea data-locale="{{ $loc }}">{{ old('visual.i18n.'.$loc.'.trip_activity_gallery_slides_text', $tripActivitySlidesText) }}</textarea>
                <small id="lp-trip-ta-hint-{{ $loc }}" class="text-muted d-block mt-1">Synced from the list above when you save. One slide per line: <code>path|caption|headline|title</code> (omit tail segments if empty; legacy <code>path|caption</code> still works). Do not use <code>|</code> inside the text fields.</small>
            </details>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    function fieldByName(form, name) {
        var el = form.elements.namedItem(name);
        if (el && el.length && !el.tagName) {
            return el[0];
        }
        return el;
    }
    function syncTripSlidesForLocale(form, loc) {
        var ta = fieldByName(form, 'visual[i18n][' + loc + '][trip_activity_gallery_slides_text]');
        if (!ta || !ta.getAttribute('data-lp-trip-slides-textarea')) {
            return;
        }
        var rowEls = form.querySelectorAll('.lp-trip-slide-row[data-locale="' + loc + '"]');
        if (rowEls.length === 0) {
            return;
        }
        var lines = [];
        rowEls.forEach(function (row) {
            var rm = row.querySelector('.lp-trip-slide-remove');
            if (rm && rm.checked) {
                return;
            }
            var pathInp = row.querySelector('input[data-lp-trip-slide-path]');
            if (!pathInp || !String(pathInp.value || '').trim()) {
                return;
            }
            var path = String(pathInp.value || '').trim();
            var capInp = row.querySelector('input[data-lp-trip-slide-caption]');
            var hlInp = row.querySelector('input[data-lp-trip-slide-headline]');
            var tiInp = row.querySelector('input[data-lp-trip-slide-title]');
            var cap = capInp ? String(capInp.value || '').trim() : '';
            var hl = hlInp ? String(hlInp.value || '').trim() : '';
            var ti = tiInp ? String(tiInp.value || '').trim() : '';
            lines.push([path, cap, hl, ti].join('|'));
        });
        ta.value = lines.join('\n');
    }
    function syncAllTripSlides(form) {
        var locs = {};
        form.querySelectorAll('.lp-trip-slide-row[data-locale]').forEach(function (row) {
            var loc = row.getAttribute('data-locale');
            if (loc) {
                locs[loc] = true;
            }
        });
        form.querySelectorAll('textarea[data-lp-trip-slides-textarea][data-locale]').forEach(function (ta) {
            var loc = ta.getAttribute('data-locale');
            if (loc) {
                locs[loc] = true;
            }
        });
        Object.keys(locs).forEach(function (loc) {
            syncTripSlidesForLocale(form, loc);
        });
    }
    window.lpSyncTripSlidesForLocale = syncTripSlidesForLocale;

    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('lpLandingPageAdminForm');
        if (!form) {
            return;
        }
        form.addEventListener('submit', function () {
            form.querySelectorAll('input[data-lp-trip-from-ta]').forEach(function (flag) {
                if (String(flag.value || '') === '1') {
                    return;
                }
                var loc = flag.getAttribute('data-lp-trip-from-ta');
                if (loc) {
                    syncTripSlidesForLocale(form, loc);
                }
            });
        });
        form.addEventListener('input', function (e) {
            if (e.target.matches && e.target.matches('textarea[data-lp-trip-slides-textarea]')) {
                var loc = e.target.getAttribute('data-locale');
                var flag = loc ? form.querySelector('input[data-lp-trip-from-ta="' + loc + '"]') : null;
                if (flag) {
                    flag.value = '1';
                }
                return;
            }
            if (e.target.closest('.lp-trip-slide-row')) {
                var row = e.target.closest('.lp-trip-slide-row');
                var loc = row.getAttribute('data-locale');
                var flag = loc ? form.querySelector('input[data-lp-trip-from-ta="' + loc + '"]') : null;
                if (flag) {
                    flag.value = '0';
                }
                if (loc) {
                    syncTripSlidesForLocale(form, loc);
                }
            }
        });
        form.addEventListener('change', function (e) {
            if (e.target.closest('.lp-trip-slide-row')) {
                var row = e.target.closest('.lp-trip-slide-row');
                var loc = row.getAttribute('data-locale');
                var flag = loc ? form.querySelector('input[data-lp-trip-from-ta="' + loc + '"]') : null;
                if (flag) {
                    flag.value = '0';
                }
                if (loc) {
                    syncTripSlidesForLocale(form, loc);
                }
            }
        });
    });
})();
</script>
@endpush
@endonce
