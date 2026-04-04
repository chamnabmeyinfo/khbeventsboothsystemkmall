<section class="lv-section lv-section--booking" data-lp-section="booking">
    <div class="lv-container lv-grid">
        <div>
            <h2 data-lv-key="booking_title">{{ $bookingTitle }}</h2>
            <div class="lv-booking">
                <form id="lvBookingForm">
                    <input type="text" name="name" placeholder="{{ $bookingNamePh }}" required>
                    <input type="email" name="email" placeholder="{{ $bookingEmailPh }}" required>
                    <input type="text" name="phone" placeholder="{{ $bookingPhonePh }}">
                    <select name="tripDate" aria-label="{{ $bookingTripPh }}">
                        <option value="">{{ $bookingTripPh }}</option>
                        @foreach($tripDates as $trip)
                            @php
                                $optPhase = trim((string) ($trip['phase'] ?? ''));
                                $optDate = trim((string) ($trip['date'] ?? ''));
                                $optLabel = $optPhase !== '' ? $optPhase.' — '.$optDate : $optDate;
                            @endphp
                            <option value="{{ $optDate }}">{{ $optLabel }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="lv-btn" data-lv-key="booking_submit_text">{{ $bookingSubmitText }}</button>
                </form>
            </div>
        </div>
        <div class="lv-image" data-lv-image-key="why_image" data-lv-image-current="{{ $whyImage }}" style="background-image:url('{{ $whyImage }}')"></div>
    </div>
</section>
