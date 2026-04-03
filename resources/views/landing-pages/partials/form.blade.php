@php
    use App\Models\LandingPage;
    $localeLabels = config('landing_locales.allowed');
    if (! is_array($localeLabels) || $localeLabels === []) {
        $localeLabels = [
            'en' => 'English',
            'km' => 'ខ្មែរ (Khmer)',
            'zh' => '中文 (Chinese)',
        ];
    }
    $adminLocales = array_keys($localeLabels);
    $visualForm = LandingPage::visualContentForAdminForm($landingPage ?? null);
    if (old('visual') && is_array(old('visual'))) {
        $ov = old('visual');
        if (! empty($ov['i18n']) && is_array($ov['i18n'])) {
            foreach ($ov['i18n'] as $lc => $data) {
                if (is_array($data)) {
                    $visualForm['i18n'][$lc] = array_merge($visualForm['i18n'][$lc] ?? [], $data);
                }
            }
        }
        if (isset($ov['hero_cta_target'])) {
            $visualForm['hero_cta_target'] = $ov['hero_cta_target'];
        }
        if (array_key_exists('hero_background_video', $ov)) {
            $visualForm['hero_background_video'] = $ov['hero_background_video'];
        }
    }
    $defaultLocale = old('default_locale', optional($landingPage)->default_locale ?? 'en');
    $enabledLocalesInput = old('enabled_locales', optional($landingPage)->enabled_locales ?? ['en']);
    if (! is_array($enabledLocalesInput)) {
        $enabledLocalesInput = ['en'];
    }
    foreach ($adminLocales as $al) {
        $visualForm['i18n'][$al] = $visualForm['i18n'][$al] ?? [];
    }
    $heroCtaShared = old('visual.hero_cta_target', $visualForm['hero_cta_target'] ?? '/login');
    $heroVideoUrl = old('visual.hero_background_video', $visualForm['hero_background_video'] ?? '');
    $heroGalleryPaths = old('visual.hero_background_images');
    if (! is_array($heroGalleryPaths)) {
        $heroGalleryPaths = \App\Models\LandingPage::heroBackgroundImagePathsForDisplay($visualForm ?? []);
    }
    $showOnceMode = old('show_once_mode', optional($landingPage)->show_once_mode ?? 'cookie_once');
    $canAutoTranslate = isset($landingPage) && $landingPage;
@endphp
@once
@push('styles')
<style>
    /* Sticky save/cancel bar on landing page create & edit (card-footer in parent view) */
    .card-footer.lp-sticky-card-footer {
        position: sticky;
        bottom: 0;
        z-index: 1020;
        background-color: #fff;
        border-top: 1px solid rgba(0, 0, 0, 0.125);
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
    }
    /* Landing page admin: primary + section tab folders (single responsive template) */
    .lp-visual-primary-tabs .nav-link {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
    }
    .lp-visual-primary-tabs .nav-link:focus {
        outline: 2px solid #007bff;
        outline-offset: 2px;
    }
    .lp-visual-primary-panels {
        border-top-left-radius: 0;
    }
    /* Horizontal section tabs (matches language + primary tabs; scroll on narrow viewports) */
    .lp-landing-section-tabs-wrap {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        margin-bottom: -1px;
        border-bottom: 1px solid #dee2e6;
    }
    .lp-landing-section-tabs {
        flex-wrap: nowrap;
        border-bottom: 0;
        min-height: 44px;
    }
    .lp-landing-section-tabs .nav-item {
        flex-shrink: 0;
    }
    .lp-landing-section-tabs .nav-link {
        white-space: nowrap;
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
    .lp-landing-section-tabs .nav-link:focus {
        outline: 2px solid #007bff;
        outline-offset: -2px;
        z-index: 1;
    }
    .lp-landing-section-panels {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }
    /* Agenda: per-day tabs (admin); same 3 columns as public table */
    .lp-agenda-by-day-editor .lp-agenda-admin-nav{border-bottom:1px solid #dee2e6}
    .lp-agenda-by-day-editor .lp-agenda-admin-content{border:1px solid #dee2e6;border-top:0;border-radius:0 0 .25rem .25rem;padding:.75rem;background:#fff}
    .lp-agenda-by-day-editor .lp-agenda-add-day-li .btn{min-height:38px}
</style>
@endpush
@endonce
<div class="card-body">
    <div class="row">
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', optional($landingPage)->name) }}">
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="form-group">
                <label>Slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" required value="{{ old('slug', optional($landingPage)->slug) }}" placeholder="e.g. events-april-offer">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-4">
            <div class="form-group">
                <label>Industry</label>
                <input type="text" name="industry" class="form-control" value="{{ old('industry', optional($landingPage)->industry) }}" placeholder="Events, Booths, Trips, Real Estate...">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                <label>Headline</label>
                <input type="text" name="headline" class="form-control" value="{{ old('headline', optional($landingPage)->headline) }}">
                <small class="text-muted">Browser tab title falls back to hero title if empty.</small>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                <label>Priority</label>
                <input type="number" min="1" max="9999" name="priority" class="form-control" value="{{ old('priority', optional($landingPage)->priority ?? 100) }}">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>Redirect URL after Continue</label>
        <input type="text" name="redirect_url" class="form-control" value="{{ old('redirect_url', optional($landingPage)->redirect_url ?? '') }}" placeholder="/l/{{ optional($landingPage)->slug ?? 'your-slug' }}/thank-you">
        <small class="text-muted d-block mt-1">After visitors submit the lead form or continue, they go here. Leave empty to use the built-in <strong>Thank you</strong> page for this page, or enter any path or full URL (e.g. <code>/login</code>, <code>https://example.com</code>).</small>
    </div>

    <input type="hidden" name="use_visual_builder" value="1">
    <input type="hidden" name="allow_inline_scripts" value="1">

    <div class="row">
        <div class="col-12 col-md-4">
            <div class="form-group">
                <label>Show Once Mode</label>
                <select name="show_once_mode" class="form-control">
                    <option value="cookie_once" {{ $showOnceMode === 'cookie_once' ? 'selected' : '' }}>Cookie Once</option>
                    <option value="session_once" {{ $showOnceMode === 'session_once' ? 'selected' : '' }}>Session Once</option>
                    <option value="entry_url_once" {{ $showOnceMode === 'entry_url_once' ? 'selected' : '' }}>Entry URL Once</option>
                </select>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="form-group">
                <label>Default language <span class="text-danger">*</span></label>
                <select name="default_locale" class="form-control" required>
                    @foreach($localeLabels as $code => $label)
                        <option value="{{ $code }}" {{ (string) $defaultLocale === (string) $code ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Shown on first visit (visitor can switch).</small>
            </div>
        </div>
        <div class="col-12 col-md-4 d-flex align-items-center flex-wrap">
            <div class="form-check mr-4 mb-2">
                <input type="checkbox" name="is_published" id="is_published" class="form-check-input" value="1" {{ old('is_published', optional($landingPage)->is_published) ? 'checked' : '' }}>
                <label for="is_published" class="form-check-label">Published</label>
            </div>
            <div class="form-check mb-2">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', optional($landingPage)->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">Active</label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="d-block">Languages on public page <span class="text-danger">*</span></label>
        @foreach($localeLabels as $code => $label)
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="enabled_locales[]" id="enabled_loc_{{ $code }}" value="{{ $code }}" {{ in_array($code, $enabledLocalesInput, true) ? 'checked' : '' }}>
                <label class="form-check-label" for="enabled_loc_{{ $code }}">{{ $label }}</label>
            </div>
        @endforeach
        <small class="text-muted d-block mt-1">Only checked languages appear in the visitor language switcher. All tabs below are saved so you can prepare copy before enabling a language.</small>
    </div>

    <div class="mb-4 lp-landing-visual-builder">
        <h5 class="mb-2">Visual page (Canton Fair template)</h5>
        <p class="text-muted small mb-3">Open <strong>Shared images &amp; hero</strong> for logos and media. Open <strong>Section copy</strong>, pick a <strong>language</strong> tab, then use the <strong>section tabs</strong> (1–9) to edit each block—less scrolling, clearer focus.</p>
        <input type="hidden" name="template_key" value="canton_fair_visual">

        <ul class="nav nav-tabs flex-wrap border-0 lp-visual-primary-tabs" id="lpVisualPrimaryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active py-2 px-3" id="lp-tab-shared" data-toggle="tab" href="#lp-pane-shared" role="tab" aria-controls="lp-pane-shared" aria-selected="true"><i class="fas fa-images mr-1" aria-hidden="true"></i>Shared images &amp; hero</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-2 px-3" id="lp-tab-copy" data-toggle="tab" href="#lp-pane-copy" role="tab" aria-controls="lp-pane-copy" aria-selected="false"><i class="fas fa-language mr-1" aria-hidden="true"></i>Section copy (by language)</a>
            </li>
        </ul>
        <div class="tab-content border rounded-bottom bg-white lp-visual-primary-panels">
            <div class="tab-pane fade show active p-3" id="lp-pane-shared" role="tabpanel" aria-labelledby="lp-tab-shared" tabindex="0">
        <div class="card card-outline-primary mb-3">
            <div class="card-header py-2">
                <strong class="d-block">Shared: brand, hero &amp; CTA link</strong>
                <small class="text-muted">Logo and hero backgrounds power the <em>Hero</em> section. Add multiple hero images for a timed slideshow (6s per image, then 10s video, repeating every 45s). Optional video plays behind the scrim; the first hero image is the poster/fallback. The CTA button uses the same destination for all languages.</small>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Hero CTA target (shared)</label>
                    <input type="text" name="visual[hero_cta_target]" class="form-control" value="{{ $heroCtaShared }}" placeholder="/login">
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-md-0">
                            <label>Logo image</label>
                            <input type="file" name="visual_logo_image" class="form-control-file" accept="image/*">
                            @if(!empty($visualForm['logo_image']))<small class="text-muted d-block mt-1">Current: {{ $visualForm['logo_image'] }}</small>@endif
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-group mb-0">
                            <label>Hero background image</label>
                            <input type="file" name="visual_hero_background_image" class="form-control-file" accept="image/*">
                            @if(!empty($visualForm['hero_background_image']))<small class="text-muted d-block mt-1">Current: {{ $visualForm['hero_background_image'] }}</small>@endif
                        </div>
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label class="d-block">Additional hero slideshow images (optional)</label>
                    <small class="text-muted d-block mb-2">Up to 10 images total (including the primary hero image above). Order is left to right in the list below. Public page: each image shows 6 seconds, then your hero video 10 seconds, in a 45-second repeating loop.</small>
                    <input type="file" name="visual_hero_background_images[]" class="form-control-file" accept="image/*" multiple>
                    @foreach($heroGalleryPaths as $gp)
                        <input type="hidden" name="visual[hero_background_images][]" value="{{ $gp }}">
                    @endforeach
                    @if(count($heroGalleryPaths) > 0)
                        <small class="text-muted d-block mt-2">{{ count($heroGalleryPaths) }} image(s) saved in order (first = primary).</small>
                    @endif
                </div>
                <div class="form-group mb-0 mt-3 pt-3 border-top">
                    <label class="d-block">Hero background video (optional)</label>
                    <small class="text-muted d-block mb-2">Upload MP4/WebM (max 50&nbsp;MB), or paste a <strong>YouTube</strong> link (<code>https://youtu.be/…</code> or <code>youtube.com/watch?v=…</code>), or a <strong>direct</strong> MP4/WebM file URL. YouTube uses an embedded player (not the HTML5 video tag). The hero image above is the poster/fallback.</small>
                    <input type="file" name="visual_hero_background_video" class="form-control-file" accept="video/mp4,video/webm,.mp4,.webm">
                    <label class="small text-muted d-block mt-2 mb-0">Or video URL</label>
                    <input type="text" name="visual[hero_background_video]" class="form-control mt-1" value="{{ $heroVideoUrl }}" placeholder="YouTube URL, https://…/video.mp4, or images/landing-pages/…/file.mp4" autocomplete="off">
                    @if(!empty($visualForm['hero_background_video']))<small class="text-muted d-block mt-1">Saved: {{ $visualForm['hero_background_video'] }}</small>@endif
                    <div class="form-check mt-2">
                        <input type="checkbox" class="form-check-input" name="clear_hero_background_video" id="clear_hero_background_video" value="1" {{ old('clear_hero_background_video') ? 'checked' : '' }}>
                        <label class="form-check-label" for="clear_hero_background_video">Remove hero background video</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline-secondary mb-3">
            <div class="card-header py-2">
                <strong class="d-block">Shared: About section image</strong>
                <small class="text-muted">Left column image beside the <em>About</em> text block (two-column layout on desktop).</small>
            </div>
            <div class="card-body py-3">
                <div class="form-group mb-0">
                    <label>About section image</label>
                    <input type="file" name="visual_about_image" class="form-control-file" accept="image/*">
                    @if(!empty($visualForm['about_image']))<small class="text-muted d-block mt-1">Current: {{ $visualForm['about_image'] }}</small>@endif
                </div>
            </div>
        </div>

        <div class="card card-outline-secondary mb-3">
            <div class="card-header py-2">
                <strong class="d-block">Shared: Booking / &ldquo;Why&rdquo; section image</strong>
                <small class="text-muted">Right column image next to the booking form (paired layout).</small>
            </div>
            <div class="card-body py-3">
                <div class="form-group mb-0">
                    <label>Why / booking side image</label>
                    <input type="file" name="visual_why_image" class="form-control-file" accept="image/*">
                    @if(!empty($visualForm['why_image']))<small class="text-muted d-block mt-1">Current: {{ $visualForm['why_image'] }}</small>@endif
                </div>
            </div>
        </div>

            </div>
            <div class="tab-pane fade p-3" id="lp-pane-copy" role="tabpanel" aria-labelledby="lp-tab-copy" tabindex="0">
        <h6 class="mt-0 mb-2">Text by language</h6>
        @if(!empty($canAutoTranslate))
            <div class="alert alert-info border mb-3 d-flex flex-column flex-md-row align-items-md-center justify-content-md-between">
                <div class="mb-2 mb-md-0 pr-md-3">
                    <strong>Translate from English</strong>
                    <p class="small mb-0 text-muted">Write and edit copy in the <strong>English</strong> tab first. Use <strong>Update all languages from English</strong> for a full pass, or <strong>From English</strong> next to each field on non-English tabs to translate that field only. Review all tabs before publishing.</p>
                </div>
                <button type="button" class="btn btn-primary text-nowrap" id="lpTranslateFromEnglishBtn">
                    <i class="fas fa-language"></i> Update all languages from English
                </button>
            </div>
        @endif
        <ul class="nav nav-tabs" id="lpLangTabs" role="tablist">
            @foreach($adminLocales as $i => $loc)
                <li class="nav-item">
                    <a class="nav-link {{ $i === 0 ? 'active' : '' }}" id="tab-{{ $loc }}" data-toggle="tab" href="#pane-{{ $loc }}" role="tab" aria-controls="pane-{{ $loc }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}">{{ $localeLabels[$loc] ?? strtoupper($loc) }}</a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content border border-top-0 p-3 rounded-bottom bg-white">
            @foreach($adminLocales as $i => $loc)
                @php
                    $vloc = $visualForm['i18n'][$loc] ?? [];
                    $heroStatsText = old('visual.i18n.'.$loc.'.hero_stats_text', collect($vloc['hero_stats'] ?? [])->map(fn ($row) => trim(($row['value'] ?? '').'|'.($row['label'] ?? '').'|'.($row['icon'] ?? '')))->implode("\n"));
                    $packageItemsText = old('visual.i18n.'.$loc.'.package_items_text', collect($vloc['package_items'] ?? [])->map(function ($row) {
                        if (is_string($row)) {
                            return $row;
                        }
                        if (! is_array($row)) {
                            return '';
                        }
                        $t = trim((string) ($row['text'] ?? ''));
                        $i = trim((string) ($row['icon'] ?? ''));

                        return $i !== '' ? $t.'|'.$i : $t;
                    })->implode("\n"));
                    $tripFromDb = collect($vloc['trip_dates'] ?? [])->map(fn ($row) => trim(($row['phase'] ?? '').'|'.($row['date'] ?? '').'|'.($row['status'] ?? '').'|'.($row['seats_left'] ?? '')))->implode("\n");
                    $tripDemoPrefill = ($landingPage === null && $tripFromDb === '') ? \App\Models\LandingPage::defaultTripDatesText() : '';
                    $tripDatesText = old('visual.i18n.'.$loc.'.trip_dates_text', $tripFromDb !== '' ? $tripFromDb : $tripDemoPrefill);
                    $tripPhasesJsonOld = old('visual.i18n.'.$loc.'.trip_phases_json');
                    if ($tripPhasesJsonOld !== null) {
                        $tripPhasesJson = $tripPhasesJsonOld;
                    } elseif (isset($vloc['trip_phases']) && is_array($vloc['trip_phases']) && $vloc['trip_phases'] !== []) {
                        $enc = json_encode($vloc['trip_phases'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        $tripPhasesJson = $enc !== false ? $enc : '[]';
                    } else {
                        $flatRows = \App\Models\LandingPage::parseTripDateRowsFromText($tripDatesText, []);
                        $defaultFlat = \App\Models\LandingPage::defaultTripDateRows();
                        if ($flatRows === [] || json_encode($flatRows) === json_encode($defaultFlat)) {
                            $tripPhasesJson = \App\Models\LandingPage::defaultTripPhasesJson();
                        } else {
                            $tp = \App\Models\LandingPage::tripPhasesFromFlatTripDates($flatRows);
                            $enc = json_encode($tp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            $tripPhasesJson = $enc !== false ? $enc : '[]';
                        }
                    }
                    $promotionDiscountsJsonOld = old('visual.i18n.'.$loc.'.promotion_discounts_json');
                    if ($promotionDiscountsJsonOld !== null) {
                        $promotionDiscountsJson = $promotionDiscountsJsonOld;
                    } elseif (($vloc['promotion_discounts_show'] ?? true) === false) {
                        $promotionDiscountsJson = '';
                    } elseif (isset($vloc['promotion_discounts']) && is_array($vloc['promotion_discounts']) && $vloc['promotion_discounts'] !== []) {
                        $enc = json_encode($vloc['promotion_discounts'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        $promotionDiscountsJson = $enc !== false ? $enc : \App\Models\LandingPage::defaultPromotionDiscountsJson();
                    } else {
                        $promotionDiscountsJson = \App\Models\LandingPage::defaultPromotionDiscountsJson();
                    }
                    $agendaFromDb = collect($vloc['agenda_items'] ?? [])->map(fn ($row) => trim(($row['slot'] ?? '').'|'.($row['activity'] ?? '').'|'.($row['detail'] ?? '')))->implode("\n");
                    $agendaDemoPrefill = ($landingPage === null && $agendaFromDb === '') ? LandingPage::defaultDemoAgendaItemsText($loc) : '';
                    $agendaItemsText = old('visual.i18n.'.$loc.'.agenda_items_text', $agendaFromDb !== '' ? $agendaFromDb : $agendaDemoPrefill);
                    $oldAgendaText = old('visual.i18n.'.$loc.'.agenda_items_text');
                    $agendaDaysForForm = [];
                    if ($oldAgendaText !== null && (string) $oldAgendaText !== '') {
                        $agendaDaysForForm = \App\Models\LandingPage::buildAgendaDaysFromItems(
                            \App\Models\LandingPage::parseAgendaItemsFromText((string) $oldAgendaText),
                            $loc
                        );
                    } elseif (! empty($vloc['agenda_days']) && is_array($vloc['agenda_days'])) {
                        $agendaDaysForForm = \App\Models\LandingPage::sanitizeAgendaDays($vloc['agenda_days']);
                    } elseif (! empty($vloc['agenda_items']) && is_array($vloc['agenda_items'])) {
                        $agendaDaysForForm = \App\Models\LandingPage::buildAgendaDaysFromItems($vloc['agenda_items'], $loc);
                    } else {
                        $agendaDaysForForm = \App\Models\LandingPage::buildAgendaDaysFromItems(
                            \App\Models\LandingPage::parseAgendaItemsFromText((string) $agendaItemsText),
                            $loc
                        );
                    }
                    if ($agendaDaysForForm === []) {
                        $agendaDaysForForm = [['day' => 1, 'label' => $loc === 'zh' ? '第1天' : 'Day 1', 'rows' => []]];
                    }
                    $agendaDayRowTexts = [];
                    foreach ($agendaDaysForForm as $di => $dayBlock) {
                        $agendaDayRowTexts[$di] = collect($dayBlock['rows'] ?? [])->map(function ($r) {
                            if (! is_array($r)) {
                                return '';
                            }

                            return trim(($r['slot'] ?? '').'|'.($r['activity'] ?? '').'|'.($r['detail'] ?? ''));
                        })->filter(fn ($l) => $l !== '')->implode("\n");
                    }
                    $faqItemsText = old('visual.i18n.'.$loc.'.faq_items_text', collect($vloc['faq_items'] ?? [])->map(fn ($row) => trim(($row['question'] ?? '').'|'.($row['answer'] ?? '')))->implode("\n"));
                    $contactPhonesText = old('visual.i18n.'.$loc.'.contact_phones_text', collect($vloc['contact_phones'] ?? [])->implode("\n"));
                    $pfx = 'visual[i18n]['.$loc.']';
                @endphp
                <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="pane-{{ $loc }}" role="tabpanel" aria-labelledby="tab-{{ $loc }}">
                    <div class="lp-landing-section-layout">
                        <div class="lp-landing-section-tabs-wrap">
                            <ul class="nav nav-tabs flex-nowrap lp-landing-section-tabs mb-0" id="lp-section-nav-{{ $loc }}" role="tablist" aria-label="Page sections — {{ $localeLabels[$loc] ?? $loc }}">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="lp-{{ $loc }}-t1" data-toggle="tab" href="#lp-{{ $loc }}-s1" role="tab" aria-controls="lp-{{ $loc }}-s1" aria-selected="true">1. Hero</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="lp-{{ $loc }}-t2" data-toggle="tab" href="#lp-{{ $loc }}-s2" role="tab" aria-controls="lp-{{ $loc }}-s2" aria-selected="false">2. About</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="lp-{{ $loc }}-t3" data-toggle="tab" href="#lp-{{ $loc }}-s3" role="tab" aria-controls="lp-{{ $loc }}-s3" aria-selected="false">3. Package</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="lp-{{ $loc }}-t4" data-toggle="tab" href="#lp-{{ $loc }}-s4" role="tab" aria-controls="lp-{{ $loc }}-s4" aria-selected="false">4. Promotion</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="lp-{{ $loc }}-t5" data-toggle="tab" href="#lp-{{ $loc }}-s5" role="tab" aria-controls="lp-{{ $loc }}-s5" aria-selected="false">5. Trip dates</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="lp-{{ $loc }}-t6" data-toggle="tab" href="#lp-{{ $loc }}-s6" role="tab" aria-controls="lp-{{ $loc }}-s6" aria-selected="false">6. Agenda</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="lp-{{ $loc }}-t7" data-toggle="tab" href="#lp-{{ $loc }}-s7" role="tab" aria-controls="lp-{{ $loc }}-s7" aria-selected="false">7. Booking</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="lp-{{ $loc }}-t8" data-toggle="tab" href="#lp-{{ $loc }}-s8" role="tab" aria-controls="lp-{{ $loc }}-s8" aria-selected="false">8. FAQ</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="lp-{{ $loc }}-t9" data-toggle="tab" href="#lp-{{ $loc }}-s9" role="tab" aria-controls="lp-{{ $loc }}-s9" aria-selected="false">9. Terms</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content border border-top-0 rounded-bottom bg-white lp-landing-section-panels" id="lp-section-panels-{{ $loc }}">

                    {{-- Section: Hero (full-bleed image + headline + CTA + stats) --}}
                    <div class="tab-pane fade show active" id="lp-{{ $loc }}-s1" role="tabpanel" aria-labelledby="lp-{{ $loc }}-t1" tabindex="0">
                    <div class="card card-outline-secondary mb-3">
                        <div class="card-header py-2">
                            <strong class="d-block">1. Hero</strong>
                            <small class="text-muted">Full-width banner: dark overlay on the shared background image, optional logo, headline, subtitle, primary CTA, then a row of stat cards. Keep copy scannable; stats use <code>value|label|icon</code> per line.</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Hero title</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'hero_title', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[hero_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.hero_title', $vloc['hero_title'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Hero CTA Text</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'hero_cta_text', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[hero_cta_text]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.hero_cta_text', $vloc['hero_cta_text'] ?? '') }}" placeholder="Reserve Your Seat Now">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Hero subtitle</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'hero_subtitle', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <textarea name="{{ $pfx }}[hero_subtitle]" class="form-control" rows="3">{{ old('visual.i18n.'.$loc.'.hero_subtitle', $vloc['hero_subtitle'] ?? '') }}</textarea>
                            </div>
                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Hero stats</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'hero_stats_text', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <textarea name="{{ $pfx }}[hero_stats_text]" class="form-control" rows="4" placeholder="200+|Countries|globe&#10;70,000+|Exhibition Booths|store&#10;500,000+|Annual Visitors|users">{{ $heroStatsText }}</textarea>
                                <small class="text-muted d-block mt-1">One stat per line: <code>value|label|icon</code> (icon keys: globe, store, users, plane, hotel, etc.).</small>
                            </div>
                        </div>
                    </div>
                    </div>

                    {{-- Section: About --}}
                    <div class="tab-pane fade" id="lp-{{ $loc }}-s2" role="tabpanel" aria-labelledby="lp-{{ $loc }}-t2" tabindex="0">
                    <div class="card card-outline-secondary mb-3">
                        <div class="card-header py-2">
                            <strong class="d-block">2. About</strong>
                            <small class="text-muted">Two columns on large screens: shared <strong>About image</strong> (left) + title, main paragraph, and a highlighted callout box. Use the highlight for a second language or key message.</small>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">About Title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'about_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[about_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.about_title', $vloc['about_title'] ?? '') }}">
                            </div>
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">About text (main paragraph)</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'about_text_en', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <textarea name="{{ $pfx }}[about_text_en]" class="form-control" rows="3">{{ old('visual.i18n.'.$loc.'.about_text_en', $vloc['about_text_en'] ?? '') }}</textarea>
                            </div>
                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">About text (highlight box)</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'about_text_kh', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <textarea name="{{ $pfx }}[about_text_kh]" class="form-control" rows="3">{{ old('visual.i18n.'.$loc.'.about_text_kh', $vloc['about_text_kh'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                    </div>

                    {{-- Section: Package & pricing --}}
                    <div class="tab-pane fade" id="lp-{{ $loc }}-s3" role="tabpanel" aria-labelledby="lp-{{ $loc }}-t3" tabindex="0">
                    <div class="card card-outline-secondary mb-3">
                        <div class="card-header py-2">
                            <strong class="d-block">3. Package &amp; pricing</strong>
                            <small class="text-muted">Warm gradient section: section title, responsive grid of benefit rows with optional icons, then a large red price panel with price + &ldquo;per person&rdquo; line.</small>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Package Title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'package_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[package_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.package_title', $vloc['package_title'] ?? '') }}">
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Package price</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'package_price', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[package_price]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.package_price', $vloc['package_price'] ?? '$499') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">&ldquo;Per person&rdquo; label</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'per_person_label', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[per_person_label]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.per_person_label', $vloc['per_person_label'] ?? '') }}" placeholder="per person">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Package items (benefits)</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'package_items_text', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <textarea name="{{ $pfx }}[package_items_text]" class="form-control" rows="10" placeholder="Round-trip flight tickets|plane&#10;3 nights / 4 days hotel accommodation|hotel">{{ $packageItemsText }}</textarea>
                                <small class="text-muted d-block mt-1">One benefit per line. Optional: <code>text|icon</code> (e.g. <code>text|plane</code>).</small>
                            </div>
                        </div>
                    </div>
                    </div>

                    {{-- Section: Promotion discounts --}}
                    <div class="tab-pane fade" id="lp-{{ $loc }}-s4" role="tabpanel" aria-labelledby="lp-{{ $loc }}-t4" tabindex="0">
                    <div class="card card-outline-secondary mb-3">
                        <div class="card-header py-2">
                            <strong class="d-block">4. Promotion discounts</strong>
                            <small class="text-muted">Shown below the package price block: base rate card plus one card per tier. Edit JSON for copy and numbers.</small>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Promotion section title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'promotion_section_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[promotion_section_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.promotion_section_title', $vloc['promotion_section_title'] ?? '') }}" placeholder="Group promotion discounts">
                            </div>
                            <div class="form-group mb-0">
                                <label class="mb-1">Promotion discounts (JSON)</label>
                                <textarea name="{{ $pfx }}[promotion_discounts_json]" class="form-control font-monospace" rows="14" spellcheck="false">{{ $promotionDiscountsJson }}</textarea>
                                <small class="text-muted d-block mt-1">Object with <code>base_price_text</code>, optional <code>intro_text</code>, and <code>tiers</code> (each: <code>participants</code>, <code>off_each</code>, optional <code>label</code>). Or submit a JSON array of tiers only. Clear this field and save to hide the promotion section on the public page.</small>
                            </div>
                        </div>
                    </div>
                    </div>

                    {{-- Section: Trip dates --}}
                    <div class="tab-pane fade" id="lp-{{ $loc }}-s5" role="tabpanel" aria-labelledby="lp-{{ $loc }}-t5" tabindex="0">
                    <div class="card card-outline-secondary mb-3">
                        <div class="card-header py-2">
                            <strong class="d-block">5. Trip dates</strong>
                            <small class="text-muted">Centered title, then one card per phase with date, status, seats, optional intro, and sub-categories (<code>subsections</code> with title + detail). Use <strong>Trip phases (JSON)</strong> for full content; <strong>Trip date rows</strong> stays as a quick pipe list and is synced from JSON when JSON is saved.</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-8">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Trip dates section title</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_section_title', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[trip_section_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.trip_section_title', $vloc['trip_section_title'] ?? '') }}" placeholder="Choose Your Trip Date">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Seats suffix</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'seats_left_suffix', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[seats_left_suffix]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.seats_left_suffix', $vloc['seats_left_suffix'] ?? '') }}" placeholder="seats left">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Per-phase register button label</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_phase_register_cta', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[trip_phase_register_cta]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.trip_phase_register_cta', $vloc['trip_phase_register_cta'] ?? '') }}" placeholder="Register for this trip" maxlength="120">
                                        <small class="text-muted d-block mt-1">Shown on each phase card; opens the registration popup.</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Register modal title</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_phase_modal_title', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[trip_phase_modal_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.trip_phase_modal_title', $vloc['trip_phase_modal_title'] ?? '') }}" placeholder="Complete your registration" maxlength="255">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="mb-0">Trip phases (JSON)</label>
                                <textarea name="{{ $pfx }}[trip_phases_json]" class="form-control font-monospace" rows="14" spellcheck="false" placeholder='[{"label":"Phase I","date":"…","feature_image":"images/landing-pages/your-slug/phase1.jpg","status":"…","seats_left":"…","intro":"…","subsections":[{"title":"…","detail":"…"}]}]'>{{ $tripPhasesJson }}</textarea>
                                <small class="text-muted d-block mt-1">Array of objects: <code>label</code>, <code>date</code>, <code>status</code>, <code>seats_left</code>, optional <code>intro</code>, optional <code>feature_image</code> (site path like <code>images/landing-pages/your-slug/photo.jpg</code> after you upload to that folder), and <code>subsections</code> as <code>[{&quot;title&quot;,&quot;detail&quot;}, …]</code>. Invalid JSON cannot be saved. Clear the field to drop rich content and use only the pipe list below.</small>
                            </div>
                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Trip date rows (pipe list)</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_dates_text', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <textarea name="{{ $pfx }}[trip_dates_text]" class="form-control" rows="5" placeholder="Phase I|15 - 19 April 2026|Available|18&#10;Phase II|23 - 27 April 2026|Available|22&#10;Phase III|1 - 5 May 2026|Available|25">{{ $tripDatesText }}</textarea>
                                <small class="text-muted d-block mt-1">When you save with valid JSON above, these rows are overwritten from the JSON. Edit the pipe list alone if you are not using JSON.</small>
                            </div>
                        </div>
                    </div>
                    </div>

                    {{-- Section: Agenda --}}
                    <div class="tab-pane fade" id="lp-{{ $loc }}-s6" role="tabpanel" aria-labelledby="lp-{{ $loc }}-t6" tabindex="0">
                    <div class="card card-outline-secondary mb-3">
                        <div class="card-header py-2">
                            <strong class="d-block">6. Agenda</strong>
                            <small class="text-muted">Shown after trip dates as a <strong>table</strong> on the public page. Each line is one row: <code>slot|activity|detail</code> (detail can be empty). Optional column titles below override the default English headers.</small>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Agenda section title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'agenda_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[agenda_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.agenda_title', $vloc['agenda_title'] ?? '') }}" placeholder="Business Tour Itinerary">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="small text-muted mb-0">Table column: time / slot</label>
                                    <input type="text" name="{{ $pfx }}[agenda_hdr_slot]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.agenda_hdr_slot', $vloc['agenda_hdr_slot'] ?? '') }}" placeholder="Time / slot" maxlength="120">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small text-muted mb-0">Table column: activity</label>
                                    <input type="text" name="{{ $pfx }}[agenda_hdr_activity]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.agenda_hdr_activity', $vloc['agenda_hdr_activity'] ?? '') }}" placeholder="Activity" maxlength="120">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="small text-muted mb-0">Table column: details</label>
                                    <input type="text" name="{{ $pfx }}[agenda_hdr_detail]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.agenda_hdr_detail', $vloc['agenda_hdr_detail'] ?? '') }}" placeholder="Details" maxlength="120">
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0" for="lp-agenda-items-text-{{ $loc }}">Agenda by day</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'agenda_items_text', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <p class="small text-muted mb-2">Use a <strong>tab per day</strong> (Day 1, Day 2, …). Each tab uses the same columns as the public page: <code>time/slot|activity|details</code> — you can omit the &ldquo;Day 1 ·&rdquo; prefix in the first column; it is added when saving. <strong>Add day</strong> adds another tab.</p>
                                <div class="lp-agenda-by-day-editor mb-2" id="lp-agenda-days-editor-{{ $loc }}" data-locale="{{ $loc }}">
                                    <ul class="nav nav-tabs flex-wrap lp-agenda-admin-nav mb-0" role="tablist">
                                        @foreach($agendaDaysForForm as $di => $dayBlock)
                                            <li class="nav-item lp-agenda-day-li" role="presentation">
                                                <a class="nav-link py-2 px-3 {{ $di === 0 ? 'active' : '' }}" id="lp-agenda-{{ $loc }}-nav-{{ $di }}" data-toggle="tab" href="#lp-agenda-{{ $loc }}-pane-{{ $di }}" role="tab" aria-controls="lp-agenda-{{ $loc }}-pane-{{ $di }}" aria-selected="{{ $di === 0 ? 'true' : 'false' }}">{{ $dayBlock['label'] ?? ('Day '.($di + 1)) }}</a>
                                            </li>
                                        @endforeach
                                        <li class="nav-item lp-agenda-add-day-li align-self-center ml-1" role="presentation">
                                            <button type="button" class="btn btn-sm btn-outline-secondary lp-agenda-add-day" data-locale="{{ $loc }}">+ Add day</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content lp-agenda-admin-content">
                                        @foreach($agendaDaysForForm as $di => $dayBlock)
                                            <div class="tab-pane fade lp-agenda-day-pane {{ $di === 0 ? 'show active' : '' }}" id="lp-agenda-{{ $loc }}-pane-{{ $di }}" role="tabpanel" aria-labelledby="lp-agenda-{{ $loc }}-nav-{{ $di }}">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                                    <span class="small text-muted mb-0">One row per line: <code>time or slot|activity|details</code></span>
                                                    @if(count($agendaDaysForForm) > 1)
                                                        <button type="button" class="btn btn-sm btn-outline-danger lp-agenda-remove-day" data-locale="{{ $loc }}">Remove this day</button>
                                                    @endif
                                                </div>
                                                <textarea class="form-control font-monospace lp-agenda-day-rows" rows="8" placeholder="06:00|Airport meet|&#10;12:00|Canton Fair|Halls 1–3">{{ $agendaDayRowTexts[$di] ?? '' }}</textarea>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <textarea name="{{ $pfx }}[agenda_items_text]" class="d-none lp-agenda-items-hidden" id="lp-agenda-items-text-{{ $loc }}" autocomplete="off">{{ $agendaItemsText }}</textarea>
                                <small class="text-muted d-block mt-1">Saved data is merged into one list for the API; the public page shows <strong>Day 1 / Day 2</strong> tabs when there are multiple days.</small>
                            </div>
                        </div>
                    </div>
                    </div>

                    {{-- Section: Booking --}}
                    <div class="tab-pane fade" id="lp-{{ $loc }}-s7" role="tabpanel" aria-labelledby="lp-{{ $loc }}-t7" tabindex="0">
                    <div class="card card-outline-secondary mb-3">
                        <div class="card-header py-2">
                            <strong class="d-block">7. Booking</strong>
                            <small class="text-muted">Split layout: form + fields on one side, shared <strong>Why / booking image</strong> on the other. Set the section heading and all form control labels for this language.</small>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Booking Section Title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[booking_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.booking_title', $vloc['booking_title'] ?? '') }}">
                            </div>
                            <p class="text-muted small mb-2">Form labels (this language)</p>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Name placeholder</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_name_placeholder', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[booking_name_placeholder]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.booking_name_placeholder', $vloc['booking_name_placeholder'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Email placeholder</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_email_placeholder', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[booking_email_placeholder]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.booking_email_placeholder', $vloc['booking_email_placeholder'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Phone placeholder</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_phone_placeholder', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[booking_phone_placeholder]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.booking_phone_placeholder', $vloc['booking_phone_placeholder'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Trip dropdown label</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_trip_placeholder', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[booking_trip_placeholder]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.booking_trip_placeholder', $vloc['booking_trip_placeholder'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                            <label class="mb-0">Submit button</label>
                                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_submit_text', 'canAutoTranslate' => $canAutoTranslate])
                                        </div>
                                        <input type="text" name="{{ $pfx }}[booking_submit_text]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.booking_submit_text', $vloc['booking_submit_text'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    {{-- Section: FAQ & contact --}}
                    <div class="tab-pane fade" id="lp-{{ $loc }}-s8" role="tabpanel" aria-labelledby="lp-{{ $loc }}-t8" tabindex="0">
                    <div class="card card-outline-secondary mb-3">
                        <div class="card-header py-2">
                            <strong class="d-block">8. FAQ &amp; contact</strong>
                            <small class="text-muted">Stacked Q&amp;A cards, then a single contact bar with phone links (one number per line).</small>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">FAQ Section Title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'faq_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[faq_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.faq_title', $vloc['faq_title'] ?? '') }}">
                            </div>
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">FAQ Items (one per line: question|answer)</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'faq_items_text', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <textarea name="{{ $pfx }}[faq_items_text]" class="form-control" rows="5" placeholder="Do I need visa for China?|Yes, and we provide guidance.">{{ $faqItemsText }}</textarea>
                            </div>
                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Contact Phones (one per line)</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'contact_phones_text', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <textarea name="{{ $pfx }}[contact_phones_text]" class="form-control" rows="3" placeholder="060 815 515&#10;010 94 76 40">{{ $contactPhonesText }}</textarea>
                            </div>
                        </div>
                    </div>
                    </div>

                    {{-- Section: Terms & Conditions --}}
                    <div class="tab-pane fade" id="lp-{{ $loc }}-s9" role="tabpanel" aria-labelledby="lp-{{ $loc }}-t9" tabindex="0">
                    <div class="card card-outline-secondary mb-3">
                        <div class="card-header py-2">
                            <strong class="d-block">9. Terms &amp; Conditions</strong>
                            <small class="text-muted">Always shown after FAQ on the public page. Add your legal or policy copy below; the section heading still appears if the body is empty.</small>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Terms section title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'terms_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[terms_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.terms_title', $vloc['terms_title'] ?? '') }}" placeholder="Terms &amp; Conditions">
                            </div>
                            <div class="form-group mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Terms &amp; Conditions text</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'terms_text', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <textarea name="{{ $pfx }}[terms_text]" class="form-control" rows="8" placeholder="Booking rules, cancellation policy, liability, visas, etc.">{{ old('visual.i18n.'.$loc.'.terms_text', $vloc['terms_text'] ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                    </div>

                            </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@once
@push('scripts')
<script>
(function () {
    var parseAgendaDaysUrl = @json(route('landing-pages.parse-agenda-days'));
    var adminLocales = @json($adminLocales);
    var tokenEl = document.querySelector('meta[name="csrf-token"]');
    var csrf = tokenEl ? tokenEl.getAttribute('content') : '';

    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function lpAgendaTabLabel(locale, index0) {
        var n = index0 + 1;
        return locale === 'zh' ? ('第' + n + '天') : ('Day ' + n);
    }

    function lpAgendaSlotPrefix(locale, index0) {
        var n = index0 + 1;
        if (locale === 'zh') {
            return '第' + n + '天 · ';
        }
        return 'Day ' + n + ' · ';
    }

    function lpAgendaSlotHasDayPrefix(s) {
        s = (s || '').trim();
        if (/^day\s*\d+/i.test(s)) {
            return true;
        }
        if (/^第\d+天/.test(s)) {
            return true;
        }
        if (/^第[一二三四五六七八九十]+天/.test(s)) {
            return true;
        }
        return false;
    }

    function lpAgendaSerializeLocale(locale) {
        var root = document.getElementById('lp-agenda-days-editor-' + locale);
        var hidden = document.getElementById('lp-agenda-items-text-' + locale);
        if (!root || !hidden) {
            return;
        }
        var panes = root.querySelectorAll('.lp-agenda-day-pane');
        var lines = [];
        panes.forEach(function (pane, idx) {
            var prefix = lpAgendaSlotPrefix(locale, idx);
            var ta = pane.querySelector('.lp-agenda-day-rows');
            if (!ta) {
                return;
            }
            (ta.value || '').split(/\r?\n/).forEach(function (line) {
                line = line.trim();
                if (!line) {
                    return;
                }
                var parts = line.split('|');
                var slot = (parts[0] || '').trim();
                var activity = (parts[1] || '').trim();
                var detail = (parts[2] || '').trim();
                if (!slot && !activity && !detail) {
                    return;
                }
                if (!lpAgendaSlotHasDayPrefix(slot)) {
                    slot = slot ? (prefix + slot) : prefix.replace(/\s+$/,'');
                }
                lines.push(slot + '|' + activity + '|' + detail);
            });
        });
        hidden.value = lines.join('\n');
    }

    function lpAgendaEnsureRemoveButtons(locale) {
        var root = document.getElementById('lp-agenda-days-editor-' + locale);
        if (!root) {
            return;
        }
        var panes = root.querySelectorAll('.lp-agenda-day-pane');
        var show = panes.length > 1;
        panes.forEach(function (pane) {
            var flex = pane.querySelector('.d-flex.justify-content-between');
            var btn = pane.querySelector('.lp-agenda-remove-day');
            if (show && !btn && flex) {
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'btn btn-sm btn-outline-danger lp-agenda-remove-day';
                b.setAttribute('data-locale', locale);
                b.textContent = 'Remove this day';
                flex.appendChild(b);
            }
            if (!show && btn) {
                btn.remove();
            }
        });
    }

    function lpAgendaRebuildFromGroups(locale, groups) {
        var root = document.getElementById('lp-agenda-days-editor-' + locale);
        if (!root || !groups || !groups.length) {
            return;
        }
        var nav = root.querySelector('.lp-agenda-admin-nav');
        var content = root.querySelector('.lp-agenda-admin-content');
        var addLi = nav.querySelector('.lp-agenda-add-day-li');
        nav.querySelectorAll('.lp-agenda-day-li').forEach(function (li) { li.remove(); });
        content.innerHTML = '';
        groups.forEach(function (g, di) {
            var label = g.label || lpAgendaTabLabel(locale, di);
            var li = document.createElement('li');
            li.className = 'nav-item lp-agenda-day-li';
            li.setAttribute('role', 'presentation');
            var a = document.createElement('a');
            a.className = 'nav-link py-2 px-3' + (di === 0 ? ' active' : '');
            a.id = 'lp-agenda-' + locale + '-nav-' + di;
            a.setAttribute('data-toggle', 'tab');
            a.href = '#lp-agenda-' + locale + '-pane-' + di;
            a.setAttribute('role', 'tab');
            a.setAttribute('aria-controls', 'lp-agenda-' + locale + '-pane-' + di);
            a.setAttribute('aria-selected', di === 0 ? 'true' : 'false');
            a.textContent = label;
            li.appendChild(a);
            nav.insertBefore(li, addLi);

            var rowLines = (g.rows || []).map(function (r) {
                return [r.slot || '', r.activity || '', r.detail || ''].join('|');
            }).join('\n');

            var pane = document.createElement('div');
            pane.className = 'tab-pane fade lp-agenda-day-pane' + (di === 0 ? ' show active' : '');
            pane.id = 'lp-agenda-' + locale + '-pane-' + di;
            pane.setAttribute('role', 'tabpanel');
            pane.setAttribute('aria-labelledby', 'lp-agenda-' + locale + '-nav-' + di);

            var flex = document.createElement('div');
            flex.className = 'd-flex justify-content-between align-items-center flex-wrap gap-2 mb-2';
            var hint = document.createElement('span');
            hint.className = 'small text-muted mb-0';
            hint.innerHTML = 'One row per line: <code>time or slot|activity|details</code>';
            flex.appendChild(hint);
            if (groups.length > 1) {
                var rb = document.createElement('button');
                rb.type = 'button';
                rb.className = 'btn btn-sm btn-outline-danger lp-agenda-remove-day';
                rb.setAttribute('data-locale', locale);
                rb.textContent = 'Remove this day';
                flex.appendChild(rb);
            }
            var ta = document.createElement('textarea');
            ta.className = 'form-control font-monospace lp-agenda-day-rows';
            ta.rows = 8;
            ta.placeholder = '06:00|Airport meet|\n12:00|Canton Fair|Halls 1–3';
            ta.value = rowLines;
            pane.appendChild(flex);
            pane.appendChild(ta);
            content.appendChild(pane);
        });
        if (window.jQuery) {
            jQuery(nav.querySelector('.lp-agenda-day-li a')).tab('show');
        }
        lpAgendaSerializeLocale(locale);
    }

    function lpAgendaFetchAndRebuild(locale) {
        var hidden = document.getElementById('lp-agenda-items-text-' + locale);
        if (!hidden) {
            return;
        }
        fetch(parseAgendaDaysUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ text: hidden.value || '', locale: locale })
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data && data.groups && data.groups.length) {
                    lpAgendaRebuildFromGroups(locale, data.groups);
                }
            })
            .catch(function () {});
    }

    function initAgendaByDay() {
        adminLocales.forEach(function (loc) {
            lpAgendaSerializeLocale(loc);
        });
        var form = document.getElementById('lpLandingPageAdminForm');
        if (form) {
            form.addEventListener('submit', function () {
                adminLocales.forEach(function (loc) {
                    lpAgendaSerializeLocale(loc);
                });
            });
        }
        document.addEventListener('input', function (e) {
            if (e.target.classList && e.target.classList.contains('lp-agenda-day-rows')) {
                var root = e.target.closest('.lp-agenda-by-day-editor');
                if (root) {
                    lpAgendaSerializeLocale(root.getAttribute('data-locale'));
                }
            }
            if (e.target.classList && e.target.classList.contains('lp-agenda-items-hidden')) {
                var loc = e.target.id.replace('lp-agenda-items-text-', '');
                lpAgendaFetchAndRebuild(loc);
            }
        });

        document.addEventListener('click', function (e) {
            var add = e.target.closest('.lp-agenda-add-day');
            if (add) {
                e.preventDefault();
                var root = add.closest('.lp-agenda-by-day-editor');
                var locale = root.getAttribute('data-locale');
                var nav = root.querySelector('.lp-agenda-admin-nav');
                var content = root.querySelector('.lp-agenda-admin-content');
                var addLi = nav.querySelector('.lp-agenda-add-day-li');
                var n = root.querySelectorAll('.lp-agenda-day-pane').length;
                var li = document.createElement('li');
                li.className = 'nav-item lp-agenda-day-li';
                li.setAttribute('role', 'presentation');
                var a = document.createElement('a');
                a.className = 'nav-link py-2 px-3';
                a.id = 'lp-agenda-' + locale + '-nav-' + n;
                a.setAttribute('data-toggle', 'tab');
                a.href = '#lp-agenda-' + locale + '-pane-' + n;
                a.setAttribute('role', 'tab');
                a.textContent = lpAgendaTabLabel(locale, n);
                li.appendChild(a);
                nav.insertBefore(li, addLi);
                var pane = document.createElement('div');
                pane.className = 'tab-pane fade lp-agenda-day-pane';
                pane.id = 'lp-agenda-' + locale + '-pane-' + n;
                pane.setAttribute('role', 'tabpanel');
                var flex = document.createElement('div');
                flex.className = 'd-flex justify-content-between align-items-center flex-wrap gap-2 mb-2';
                var hintAdd = document.createElement('span');
                hintAdd.className = 'small text-muted mb-0';
                hintAdd.innerHTML = 'One row per line: <code>time or slot|activity|details</code>';
                var rb = document.createElement('button');
                rb.type = 'button';
                rb.className = 'btn btn-sm btn-outline-danger lp-agenda-remove-day';
                rb.setAttribute('data-locale', locale);
                rb.textContent = 'Remove this day';
                flex.appendChild(hintAdd);
                flex.appendChild(rb);
                var ta = document.createElement('textarea');
                ta.className = 'form-control font-monospace lp-agenda-day-rows';
                ta.rows = 8;
                ta.placeholder = '06:00|Airport meet|\n12:00|Canton Fair|Halls 1–3';
                pane.appendChild(flex);
                pane.appendChild(ta);
                content.appendChild(pane);
                lpAgendaEnsureRemoveButtons(locale);
                if (window.jQuery) {
                    jQuery(a).tab('show');
                }
                lpAgendaSerializeLocale(locale);
                return;
            }
            var rem = e.target.closest('.lp-agenda-remove-day');
            if (rem) {
                e.preventDefault();
                var pane = rem.closest('.lp-agenda-day-pane');
                var root = rem.closest('.lp-agenda-by-day-editor');
                var locale = root.getAttribute('data-locale');
                var panes = root.querySelectorAll('.lp-agenda-day-pane');
                if (panes.length <= 1) {
                    return;
                }
                var href = '#' + pane.id;
                var link = root.querySelector('a[href="' + href + '"]');
                if (link && link.closest('li')) {
                    link.closest('li').remove();
                }
                pane.remove();
                lpAgendaEnsureRemoveButtons(locale);
                var first = root.querySelector('.lp-agenda-day-li a');
                if (first && window.jQuery) {
                    jQuery(first).tab('show');
                }
                lpAgendaSerializeLocale(locale);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAgendaByDay);
    } else {
        initAgendaByDay();
    }
})();
</script>
@endpush
@endonce
