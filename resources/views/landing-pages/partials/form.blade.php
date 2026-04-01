<div class="card-body">
    @php
        $visual = old('visual', is_array(optional($landingPage)->visual_content) ? optional($landingPage)->visual_content : []);
        $useVisualBuilder = old('use_visual_builder', optional($landingPage)->use_visual_builder);
    @endphp
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

    <div class="row">
        <div class="col-12 col-md-4">
            <div class="form-group">
                <label>Show Once Mode</label>
                <select name="show_once_mode" class="form-control">
                    @php($mode = old('show_once_mode', optional($landingPage)->show_once_mode ?? 'cookie_once'))
                    <option value="cookie_once" {{ $mode === 'cookie_once' ? 'selected' : '' }}>Cookie Once</option>
                    <option value="session_once" {{ $mode === 'session_once' ? 'selected' : '' }}>Session Once</option>
                    <option value="entry_url_once" {{ $mode === 'entry_url_once' ? 'selected' : '' }}>Entry URL Once</option>
                </select>
            </div>
        </div>
        <div class="col-12 col-md-8 d-flex align-items-center">
            <div class="form-check mr-4">
                <input type="checkbox" name="use_visual_builder" id="use_visual_builder" class="form-check-input" value="1" {{ $useVisualBuilder ? 'checked' : '' }}>
                <label for="use_visual_builder" class="form-check-label">Use Visual Builder</label>
            </div>
            <div class="form-check mr-4">
                <input type="checkbox" name="allow_inline_scripts" id="allow_inline_scripts" class="form-check-input" value="1" {{ old('allow_inline_scripts', optional($landingPage)->allow_inline_scripts) ? 'checked' : '' }}>
                <label for="allow_inline_scripts" class="form-check-label">Allow inline JavaScript</label>
            </div>
            <div class="form-check mr-4">
                <input type="checkbox" name="is_published" id="is_published" class="form-check-input" value="1" {{ old('is_published', optional($landingPage)->is_published) ? 'checked' : '' }}>
                <label for="is_published" class="form-check-label">Published</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', optional($landingPage)->is_active) ? 'checked' : '' }}>
                <label for="is_active" class="form-check-label">Active</label>
            </div>
        </div>
    </div>

    <div class="border rounded p-3 mb-4">
        <h5 class="mb-3">Visual Builder (Canton Fair Template)</h5>
        <input type="hidden" name="template_key" value="canton_fair_visual">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Hero Title</label>
                    <input type="text" name="visual[hero_title]" class="form-control" value="{{ $visual['hero_title'] ?? '' }}">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Hero CTA Text</label>
                    <input type="text" name="visual[hero_cta_text]" class="form-control" value="{{ $visual['hero_cta_text'] ?? '' }}" placeholder="Reserve Your Seat Now">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Hero Subtitle</label>
            <textarea name="visual[hero_subtitle]" class="form-control" rows="3">{{ $visual['hero_subtitle'] ?? '' }}</textarea>
        </div>
        <div class="form-group">
            <label>Hero CTA Target</label>
            <input type="text" name="visual[hero_cta_target]" class="form-control" value="{{ $visual['hero_cta_target'] ?? '/login' }}" placeholder="/login">
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>About Title</label>
                    <input type="text" name="visual[about_title]" class="form-control" value="{{ $visual['about_title'] ?? '' }}">
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Package Title</label>
                    <input type="text" name="visual[package_title]" class="form-control" value="{{ $visual['package_title'] ?? '' }}">
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>About Text (EN)</label>
            <textarea name="visual[about_text_en]" class="form-control" rows="3">{{ $visual['about_text_en'] ?? '' }}</textarea>
        </div>
        <div class="form-group">
            <label>About Text (KH)</label>
            <textarea name="visual[about_text_kh]" class="form-control" rows="3">{{ $visual['about_text_kh'] ?? '' }}</textarea>
        </div>
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label>Package Price</label>
                    <input type="text" name="visual[package_price]" class="form-control" value="{{ $visual['package_price'] ?? '$499' }}">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label>Booking Section Title</label>
                    <input type="text" name="visual[booking_title]" class="form-control" value="{{ $visual['booking_title'] ?? '' }}">
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label>FAQ Section Title</label>
                    <input type="text" name="visual[faq_title]" class="form-control" value="{{ $visual['faq_title'] ?? '' }}">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Logo Image</label>
                    <input type="file" name="visual_logo_image" class="form-control-file" accept="image/*">
                    @if(!empty($visual['logo_image']))<small class="text-muted d-block mt-1">Current: {{ $visual['logo_image'] }}</small>@endif
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Hero Background Image</label>
                    <input type="file" name="visual_hero_background_image" class="form-control-file" accept="image/*">
                    @if(!empty($visual['hero_background_image']))<small class="text-muted d-block mt-1">Current: {{ $visual['hero_background_image'] }}</small>@endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>About Section Image</label>
                    <input type="file" name="visual_about_image" class="form-control-file" accept="image/*">
                    @if(!empty($visual['about_image']))<small class="text-muted d-block mt-1">Current: {{ $visual['about_image'] }}</small>@endif
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label>Why Choose Section Image</label>
                    <input type="file" name="visual_why_image" class="form-control-file" accept="image/*">
                    @if(!empty($visual['why_image']))<small class="text-muted d-block mt-1">Current: {{ $visual['why_image'] }}</small>@endif
                </div>
            </div>
        </div>
    </div>

    <div class="border rounded p-3">
        <h5 class="mb-3">Advanced HTML/CSS/JS (Optional / Hybrid)</h5>
        <div class="form-group">
            <label>HTML Content</label>
            <textarea name="html_content" class="form-control" rows="12">{{ old('html_content', optional($landingPage)->html_content) }}</textarea>
        </div>

        <div class="form-group">
            <label>CSS Content</label>
            <textarea name="css_content" class="form-control" rows="6">{{ old('css_content', optional($landingPage)->css_content) }}</textarea>
        </div>

        <div class="form-group">
            <label>JavaScript Content</label>
            <textarea name="js_content" class="form-control" rows="6">{{ old('js_content', optional($landingPage)->js_content) }}</textarea>
            <small class="text-muted">Ignored when "Allow inline JavaScript" is disabled and visual builder is off.</small>
        </div>
    </div>
</div>
