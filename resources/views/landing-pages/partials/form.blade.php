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

    <div class="form-group">
        <label>HTML Content <span class="text-danger">*</span></label>
        <textarea name="html_content" class="form-control" rows="14" required>{{ old('html_content', optional($landingPage)->html_content) }}</textarea>
    </div>

    <div class="form-group">
        <label>CSS Content (optional)</label>
        <textarea name="css_content" class="form-control" rows="6">{{ old('css_content', optional($landingPage)->css_content) }}</textarea>
    </div>

    <div class="form-group">
        <label>JavaScript Content (optional)</label>
        <textarea name="js_content" class="form-control" rows="6">{{ old('js_content', optional($landingPage)->js_content) }}</textarea>
        <small class="text-muted">Ignored when "Allow inline JavaScript" is disabled.</small>
    </div>
</div>
