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
    $sectionBlueprintNormalized = LandingPage::sanitizeSectionBlueprint($visualForm['section_blueprint'] ?? null);
    $sectionBlueprintJsonDefault = json_encode($sectionBlueprintNormalized, JSON_UNESCAPED_SLASHES);
    $sectionBlueprintJson = old('visual.section_blueprint_json', $sectionBlueprintJsonDefault);
    $tabBlueprint = LandingPage::sanitizeSectionBlueprint(json_decode($sectionBlueprintJson, true));
    $sectionLayoutLabels = LandingPage::sectionLayoutLabels();
    $sectionTemplatesForApply = isset($landingPage) && $landingPage
        ? \App\Models\LandingPageSectionTemplate::query()->orderBy('name')->get()
        : collect();
@endphp
<div class="card-body lp-visual-form-root">
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

    <div class="mb-4 lp-landing-visual-builder lp-landing-visual-form-wrap">
        <h5 class="mb-2">Visual page (Canton Fair template)</h5>
        <p class="text-muted small mb-3">Open <strong>Sections &amp; order</strong> to add or reorder blocks (one of each layout). Then open <strong>Section copy</strong>, pick a <strong>language</strong>, and use the <strong>section tabs</strong>—they follow the <em>same order</em> as your section list (after you save or reload the page).</p>
        <input type="hidden" name="template_key" value="canton_fair_visual">

        <ul class="nav nav-tabs flex-wrap border-0 lp-visual-primary-tabs" id="lpVisualPrimaryTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active py-2 px-3" id="lp-tab-shared" data-toggle="tab" href="#lp-pane-shared" role="tab" aria-controls="lp-pane-shared" aria-selected="true"><i class="fas fa-images mr-1" aria-hidden="true"></i>Shared images &amp; hero</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link py-2 px-3" id="lp-tab-sections" data-toggle="tab" href="#lp-pane-sections" role="tab" aria-controls="lp-pane-sections" aria-selected="false"><i class="fas fa-layer-group mr-1" aria-hidden="true"></i>Sections &amp; order</a>
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
                @php
                    $vfLogoApp = is_array($visualForm ?? null) ? $visualForm : [];
                    $oldVisualLogo = old('visual');
                    if (is_array($oldVisualLogo)) {
                        $vfLogoApp = array_merge($vfLogoApp, $oldVisualLogo);
                    }
                    $logoAppearanceForm = \App\Models\LandingPage::normalizeLogoAppearanceFromVisual($vfLogoApp);
                @endphp
                <div class="border-top pt-3 mt-3">
                    <p class="font-weight-bold mb-2">Logo appearance (hero)</p>
                    <p class="text-muted small mb-3">Controls how the logo is sized and styled on the public hero. Uses max width/height so aspect ratio is preserved.</p>
                    <div class="form-row">
                        <div class="form-group col-6 col-md-3">
                            <label for="lp_logo_max_w">Max width (px)</label>
                            <input type="number" class="form-control" id="lp_logo_max_w" name="visual[logo_max_width_px]" min="80" max="420" step="1" value="{{ (int) $logoAppearanceForm['logo_max_width_px'] }}">
                        </div>
                        <div class="form-group col-6 col-md-3">
                            <label for="lp_logo_max_h">Max height (px)</label>
                            <input type="number" class="form-control" id="lp_logo_max_h" name="visual[logo_max_height_px]" min="40" max="200" step="1" value="{{ (int) $logoAppearanceForm['logo_max_height_px'] }}">
                        </div>
                        <div class="form-group col-6 col-md-3">
                            <label for="lp_logo_pad">Padding (px)</label>
                            <input type="number" class="form-control" id="lp_logo_pad" name="visual[logo_padding_px]" min="0" max="40" step="1" value="{{ (int) $logoAppearanceForm['logo_padding_px'] }}">
                        </div>
                        <div class="form-group col-6 col-md-3">
                            <label for="lp_logo_rad">Corner radius (px)</label>
                            <input type="number" class="form-control" id="lp_logo_rad" name="visual[logo_border_radius_px]" min="0" max="48" step="1" value="{{ (int) $logoAppearanceForm['logo_border_radius_px'] }}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-12 col-md-4">
                            <label for="lp_logo_shadow">Shadow / glow</label>
                            <select class="form-control" id="lp_logo_shadow" name="visual[logo_shadow_preset]">
                                @foreach([
                                    'default' => 'Default (bright rim on dark hero)',
                                    'none' => 'None',
                                    'soft' => 'Soft',
                                    'strong' => 'Strong',
                                    'glow' => 'Gold accent glow',
                                ] as $lpShVal => $lpShLabel)
                                    <option value="{{ $lpShVal }}" {{ ($logoAppearanceForm['logo_shadow_preset'] ?? '') === $lpShVal ? 'selected' : '' }}>{{ $lpShLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label for="lp_logo_backdrop">Background plate</label>
                            <select class="form-control" id="lp_logo_backdrop" name="visual[logo_backdrop]">
                                @foreach([
                                    'none' => 'None',
                                    'white' => 'Light (white glass)',
                                    'dark' => 'Dark glass',
                                ] as $lpBdVal => $lpBdLabel)
                                    <option value="{{ $lpBdVal }}" {{ ($logoAppearanceForm['logo_backdrop'] ?? '') === $lpBdVal ? 'selected' : '' }}>{{ $lpBdLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-12 col-md-4">
                            <label for="lp_logo_fx">Motion effect</label>
                            <select class="form-control" id="lp_logo_fx" name="visual[logo_effect]">
                                @foreach([
                                    'none' => 'None',
                                    'gentle_pulse' => 'Gentle pulse',
                                    'soft_ring' => 'Soft ring',
                                ] as $lpFxVal => $lpFxLabel)
                                    <option value="{{ $lpFxVal }}" {{ ($logoAppearanceForm['logo_effect'] ?? '') === $lpFxVal ? 'selected' : '' }}>{{ $lpFxLabel }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Disabled automatically for visitors who prefer reduced motion.</small>
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
            <div class="tab-pane fade p-3" id="lp-pane-sections" role="tabpanel" aria-labelledby="lp-tab-sections" tabindex="0">
                @include('landing-pages.partials.section-management')
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
        <ul class="nav nav-tabs lp-lang-tabs" id="lpLangTabs" role="tablist">
            @foreach($adminLocales as $i => $loc)
                <li class="nav-item" role="presentation">
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
                    $tripActivitySlidesText = old('visual.i18n.'.$loc.'.trip_activity_gallery_slides_text', (string) ($vloc['trip_activity_gallery_slides_text'] ?? ''));
                    $pfx = 'visual[i18n]['.$loc.']';
                @endphp
                <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="pane-{{ $loc }}" role="tabpanel" aria-labelledby="tab-{{ $loc }}">
                    <div class="lp-landing-section-layout">
                        <div class="lp-landing-section-tabs-wrap">
                            <ul class="nav nav-tabs flex-nowrap lp-landing-section-tabs mb-0" id="lp-section-nav-{{ $loc }}" role="tablist" aria-label="Page sections — {{ $localeLabels[$loc] ?? $loc }} (same order as Sections &amp; order)">
                                @foreach($tabBlueprint as $bi => $secRow)
                                    @php
                                        $__layout = (string) ($secRow['layout'] ?? '');
                                        if (! in_array($__layout, LandingPage::SECTION_LAYOUT_KEYS, true)) {
                                            continue;
                                        }
                                        $__secId = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) ($secRow['id'] ?? 'sec'));
                                        $__tabNum = $bi + 1;
                                        $__tabTitle = $sectionLayoutLabels[$__layout] ?? $__layout;
                                    @endphp
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $bi === 0 ? 'active' : '' }}" id="lp-{{ $loc }}-tab-{{ $__secId }}" data-toggle="tab" href="#lp-{{ $loc }}-sec-{{ $__secId }}" role="tab" aria-controls="lp-{{ $loc }}-sec-{{ $__secId }}" aria-selected="{{ $bi === 0 ? 'true' : 'false' }}">{{ $__tabNum }}. {{ $__tabTitle }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="tab-content border border-top-0 rounded-bottom bg-white lp-landing-section-panels" id="lp-section-panels-{{ $loc }}">
                                @foreach($tabBlueprint as $bi => $secRow)
                                    @php
                                        $__layout = (string) ($secRow['layout'] ?? '');
                                        if (! in_array($__layout, LandingPage::SECTION_LAYOUT_KEYS, true)) {
                                            continue;
                                        }
                                        $__secId = preg_replace('/[^a-zA-Z0-9_-]/', '-', (string) ($secRow['id'] ?? 'sec'));
                                        $__previewId = LandingPage::sectionLayoutPreviewSectionId($__layout);
                                    @endphp
                                    <div class="tab-pane fade {{ $bi === 0 ? 'show active' : '' }}" id="lp-{{ $loc }}-sec-{{ $__secId }}" role="tabpanel" aria-labelledby="lp-{{ $loc }}-tab-{{ $__secId }}" tabindex="0">
                                        @include('landing-pages.partials.section-layout-preview', ['section' => $__previewId, 'loc' => $loc])
                                        @include('landing-pages.partials.section-copy-fields.'.$__layout, ['sectionOrdinal' => $bi + 1])
                                    </div>
                                @endforeach
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

    /**
     * Horizontal overflow-x strips (section tabs) often capture vertical wheel in Chrome, which feels like the page "won't scroll" after switching tabs. Forward vertical wheel to the window.
     */
    function lpBindHorizontalStripWheelScrollThrough() {
        document.querySelectorAll('.lp-landing-section-tabs-wrap').forEach(function (el) {
            el.addEventListener('wheel', function (e) {
                if (e.ctrlKey) {
                    return;
                }
                if (Math.abs(e.deltaY) <= Math.abs(e.deltaX)) {
                    return;
                }
                e.preventDefault();
                window.scrollBy(0, e.deltaY);
            }, { passive: false });
        });
    }

    function lpInitLandingVisualFormScripts() {
        initAgendaByDay();
        lpBindHorizontalStripWheelScrollThrough();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', lpInitLandingVisualFormScripts);
    } else {
        lpInitLandingVisualFormScripts();
    }
})();
</script>
@endpush
@endonce
