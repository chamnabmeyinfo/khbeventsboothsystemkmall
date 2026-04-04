<section class="lv-section lv-section--faq" data-lp-section="faq">
    <div class="lv-container">
        <h2 data-lv-key="faq_title">{{ $faqTitle }}</h2>
        @foreach($faqItems as $idx => $faq)
            <div class="lv-card" @if($idx > 0) style="margin-top:12px" @endif>
                <strong class="lv-faq-q">{{ $faq['question'] ?? '' }}</strong>
                <div class="lv-faq-a">{{ $faq['answer'] ?? '' }}</div>
            </div>
        @endforeach
        <div class="lv-card lv-contact-bar" style="margin-top:14px;text-align:center;">
            @foreach($contactPhones as $p)
                <a href="tel:{{ preg_replace('/\\s+/', '', $p) }}" style="margin:0 8px;display:inline-block;">{{ $p }}</a>
            @endforeach
        </div>
    </div>
</section>
