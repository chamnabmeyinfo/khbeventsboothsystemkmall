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
    $showOnceMode = old('show_once_mode', optional($landingPage)->show_once_mode ?? 'cookie_once');
    $canAutoTranslate = isset($landingPage) && $landingPage;
@endphp
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
        <label>Redirect URL after Continue <span class="text-danger">*</span></label>
        <input type="text" name="redirect_url" class="form-control" required value="{{ old('redirect_url', optional($landingPage)->redirect_url ?? '/login') }}" placeholder="/login or https://example.com">
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

    <div class="border rounded p-3 mb-4">
        <h5 class="mb-3">Page content</h5>
        <p class="text-muted small mb-3">Shared images and button link apply to every language. Use a tab for each language&rsquo;s text.</p>
        <input type="hidden" name="template_key" value="canton_fair_visual">

        <div class="form-group">
            <label>Hero CTA target (shared)</label>
            <input type="text" name="visual[hero_cta_target]" class="form-control" value="{{ $heroCtaShared }}" placeholder="/login">
        </div>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Logo Image</label>
                    <input type="file" name="visual_logo_image" class="form-control-file" accept="image/*">
                    @if(!empty($visualForm['logo_image']))<small class="text-muted d-block mt-1">Current: {{ $visualForm['logo_image'] }}</small>@endif
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Hero Background Image</label>
                    <input type="file" name="visual_hero_background_image" class="form-control-file" accept="image/*">
                    @if(!empty($visualForm['hero_background_image']))<small class="text-muted d-block mt-1">Current: {{ $visualForm['hero_background_image'] }}</small>@endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>About Section Image</label>
                    <input type="file" name="visual_about_image" class="form-control-file" accept="image/*">
                    @if(!empty($visualForm['about_image']))<small class="text-muted d-block mt-1">Current: {{ $visualForm['about_image'] }}</small>@endif
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Why Choose Section Image</label>
                    <input type="file" name="visual_why_image" class="form-control-file" accept="image/*">
                    @if(!empty($visualForm['why_image']))<small class="text-muted d-block mt-1">Current: {{ $visualForm['why_image'] }}</small>@endif
                </div>
            </div>
        </div>

        <h6 class="mt-4 mb-2">Text by language</h6>
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
                    $tripDatesText = old('visual.i18n.'.$loc.'.trip_dates_text', collect($vloc['trip_dates'] ?? [])->map(fn ($row) => trim(($row['date'] ?? '').'|'.($row['status'] ?? '').'|'.($row['seats_left'] ?? '')))->implode("\n"));
                    $faqItemsText = old('visual.i18n.'.$loc.'.faq_items_text', collect($vloc['faq_items'] ?? [])->map(fn ($row) => trim(($row['question'] ?? '').'|'.($row['answer'] ?? '')))->implode("\n"));
                    $contactPhonesText = old('visual.i18n.'.$loc.'.contact_phones_text', collect($vloc['contact_phones'] ?? [])->implode("\n"));
                    $pfx = 'visual[i18n]['.$loc.']';
                @endphp
                <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}" id="pane-{{ $loc }}" role="tabpanel" aria-labelledby="tab-{{ $loc }}">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Hero Title</label>
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
                            <label class="mb-0">Hero Subtitle</label>
                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'hero_subtitle', 'canAutoTranslate' => $canAutoTranslate])
                        </div>
                        <textarea name="{{ $pfx }}[hero_subtitle]" class="form-control" rows="3">{{ old('visual.i18n.'.$loc.'.hero_subtitle', $vloc['hero_subtitle'] ?? '') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">About Title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'about_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[about_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.about_title', $vloc['about_title'] ?? '') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Package Title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'package_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[package_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.package_title', $vloc['package_title'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                            <label class="mb-0">About text (main paragraph)</label>
                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'about_text_en', 'canAutoTranslate' => $canAutoTranslate])
                        </div>
                        <textarea name="{{ $pfx }}[about_text_en]" class="form-control" rows="3">{{ old('visual.i18n.'.$loc.'.about_text_en', $vloc['about_text_en'] ?? '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                            <label class="mb-0">About text (highlight box)</label>
                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'about_text_kh', 'canAutoTranslate' => $canAutoTranslate])
                        </div>
                        <textarea name="{{ $pfx }}[about_text_kh]" class="form-control" rows="3">{{ old('visual.i18n.'.$loc.'.about_text_kh', $vloc['about_text_kh'] ?? '') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Package Price</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'package_price', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[package_price]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.package_price', $vloc['package_price'] ?? '$499') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Booking Section Title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'booking_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[booking_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.booking_title', $vloc['booking_title'] ?? '') }}">
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">FAQ Section Title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'faq_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[faq_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.faq_title', $vloc['faq_title'] ?? '') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Trip dates section title</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_section_title', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[trip_section_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.trip_section_title', $vloc['trip_section_title'] ?? '') }}" placeholder="Choose Your Trip Date">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">&ldquo;Per person&rdquo; label</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'per_person_label', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[per_person_label]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.per_person_label', $vloc['per_person_label'] ?? '') }}" placeholder="per person">
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-group">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                                    <label class="mb-0">Seats suffix</label>
                                    @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'seats_left_suffix', 'canAutoTranslate' => $canAutoTranslate])
                                </div>
                                <input type="text" name="{{ $pfx }}[seats_left_suffix]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.seats_left_suffix', $vloc['seats_left_suffix'] ?? '') }}" placeholder="seats left">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                            <label class="mb-0">Hero Stats (one per line: value|label)</label>
                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'hero_stats_text', 'canAutoTranslate' => $canAutoTranslate])
                        </div>
                        <textarea name="{{ $pfx }}[hero_stats_text]" class="form-control" rows="4" placeholder="200+|Countries|globe&#10;70,000+|Exhibition Booths|store&#10;500,000+|Annual Visitors|users">{{ $heroStatsText }}</textarea>
                        <small class="text-muted d-block mt-1">Format per line: <code>value|label|icon</code> (icon keys: globe, store, users, plane, hotel, etc.).</small>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                            <label class="mb-0">Package Items (one per line)</label>
                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'package_items_text', 'canAutoTranslate' => $canAutoTranslate])
                        </div>
                        <textarea name="{{ $pfx }}[package_items_text]" class="form-control" rows="10" placeholder="Round-trip flight tickets|plane&#10;3 nights / 4 days hotel accommodation|hotel">{{ $packageItemsText }}</textarea>
                        <small class="text-muted d-block mt-1">One benefit per line. Optional: <code>text|icon</code> (e.g. <code>|plane</code>).</small>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                            <label class="mb-0">Trip Dates (one per line: date|status|seats_left)</label>
                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_dates_text', 'canAutoTranslate' => $canAutoTranslate])
                        </div>
                        <textarea name="{{ $pfx }}[trip_dates_text]" class="form-control" rows="4" placeholder="17 - 20 October 2025|Available|18&#10;25 - 28 October 2025|Almost Full|7">{{ $tripDatesText }}</textarea>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                            <label class="mb-0">FAQ Items (one per line: question|answer)</label>
                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'faq_items_text', 'canAutoTranslate' => $canAutoTranslate])
                        </div>
                        <textarea name="{{ $pfx }}[faq_items_text]" class="form-control" rows="5" placeholder="Do I need visa for China?|Yes, and we provide guidance.">{{ $faqItemsText }}</textarea>
                    </div>
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                            <label class="mb-0">Contact Phones (one per line)</label>
                            @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'contact_phones_text', 'canAutoTranslate' => $canAutoTranslate])
                        </div>
                        <textarea name="{{ $pfx }}[contact_phones_text]" class="form-control" rows="3" placeholder="060 815 515&#10;010 94 76 40">{{ $contactPhonesText }}</textarea>
                    </div>
                    <p class="text-muted small mb-2">Booking form labels (this language)</p>
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
            @endforeach
        </div>
    </div>
</div>
