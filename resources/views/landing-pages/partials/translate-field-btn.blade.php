@if($locale !== 'en' && !empty($canAutoTranslate))
    <button type="button" class="btn btn-sm btn-outline-primary lp-translate-field-btn" data-field-key="{{ $fieldKey }}" data-target-locale="{{ $locale }}" title="Translate this field from English">
        <i class="fas fa-language"></i> From English
    </button>
@endif
