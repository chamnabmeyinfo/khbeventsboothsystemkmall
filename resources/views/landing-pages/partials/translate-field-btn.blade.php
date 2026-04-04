@if($locale !== 'en' && !empty($canAutoTranslate))
    <button type="button" class="action-btn action-btn-secondary lp-btn-compact lp-translate-field-btn" data-field-key="{{ $fieldKey }}" data-target-locale="{{ $locale }}" title="Translate this field from English">
        <i class="fas fa-language" aria-hidden="true"></i> From English
    </button>
@endif
