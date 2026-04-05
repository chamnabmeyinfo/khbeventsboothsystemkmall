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
    $sectionBlueprintForManagement = old('visual.section_blueprint');
    if (! is_array($sectionBlueprintForManagement)) {
        $sectionBlueprintForManagement = $sectionBlueprintNormalized;
    } else {
        $sectionBlueprintForManagement = LandingPage::sanitizeSectionBlueprint($sectionBlueprintForManagement);
    }
    $tabBlueprint = $sectionBlueprintForManagement;
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
        @include('landing-pages.partials.lp-public-copy-workspace', ['lpI18nInputRoot' => 'visual'])
    </div>
</div>
@include('landing-pages.partials.lp-landing-copy-form-scripts')
