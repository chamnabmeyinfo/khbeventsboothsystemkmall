@php $ord = (int) ($sectionOrdinal ?? 1); @endphp
<div class="card card-outline-secondary mb-3">
    <div class="card-header py-2">
        <strong class="d-block">{{ $ord }}. FAQ &amp; contact</strong>
        <small class="text-muted">Stacked Q&amp;A cards, then a single contact bar with phone links (one number per line).</small>
    </div>
    <div class="card-body">
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                <label class="mb-0">FAQ Section Title</label>
                @include('landing-pages.partials.translate-field-btn', ['locale' => $loc, 'fieldKey' => 'faq_title', 'canAutoTranslate' => $canAutoTranslate])
            </div>
            <input type="text" name="{{ $pfx }}[faq_title]" class="form-control" value="{{ old(($lpI18nOldKeyPrefix ?? 'visual.i18n').'.'.$loc.'.faq_title', $vloc['faq_title'] ?? '') }}">
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
