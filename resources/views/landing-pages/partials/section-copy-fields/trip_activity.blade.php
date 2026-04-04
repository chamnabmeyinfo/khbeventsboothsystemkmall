@php $ord = (int) ($sectionOrdinal ?? 1); @endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. Trip activity — photo slider</strong>
        <small class="text-muted">Horizontal slideshow on the public page. Position in <strong>Sections &amp; order</strong> sets where it appears. One image per line: site path (under <code>images/landing-pages/…</code>), optional caption after <code>|</code>. Same path rules as phase feature images.</small>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Section title</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_activity_gallery_title', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[trip_activity_gallery_title]" class="form-control" value="{{ old('visual.i18n.'.$loc.'.trip_activity_gallery_title', $vloc['trip_activity_gallery_title'] ?? '') }}" placeholder="Trip highlights" maxlength="255">
        </div>
        <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">Slides (one per line)</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'trip_activity_gallery_slides_text', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <textarea name="{{ $pfx }}[trip_activity_gallery_slides_text]" class="form-control" rows="6" placeholder="images/landing-pages/your-slug/activity-1.jpg|Factory visit&#10;images/landing-pages/your-slug/activity-2.jpg|Team dinner">{{ $tripActivitySlidesText }}</textarea>
            <small class="text-muted d-block mt-1">Leave empty to hide this block on the public page. Format: <code>path|caption</code> or path only.</small>
        </div>
    </div>
</div>
