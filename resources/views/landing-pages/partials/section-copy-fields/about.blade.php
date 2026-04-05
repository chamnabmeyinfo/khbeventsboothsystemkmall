@php $ord = (int) ($sectionOrdinal ?? 1); @endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. About</strong>
        <small class="text-muted">Two columns on large screens: shared <strong>About image</strong> (left) + title, main paragraph, and a highlighted callout box. Use the highlight for a second language or key message.</small>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">About Title</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'about_title', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[about_title]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.about_title', $vloc['about_title'] ?? '') }}">
        </div>
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">About text (main paragraph)</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'about_text_en', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <textarea name="{{ $pfx }}[about_text_en]" class="form-control" rows="3">{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.about_text_en', $vloc['about_text_en'] ?? '') }}</textarea>
        </div>
        <div class="form-group mb-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">About text (highlight box)</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'about_text_kh', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <textarea name="{{ $pfx }}[about_text_kh]" class="form-control" rows="3">{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.about_text_kh', $vloc['about_text_kh'] ?? '') }}</textarea>
        </div>
    </div>
</div>
