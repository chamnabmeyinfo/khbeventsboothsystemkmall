@php
    $v = is_array($visual ?? null) ? $visual : [];
    $modalTitle = $v['continue_modal_title'] ?? 'Before you continue';
    $modalSubtitle = $v['continue_modal_subtitle'] ?? '';
    $tripDatesRaw = is_array($v['trip_dates'] ?? null) ? $v['trip_dates'] : [];
    $tripDates = \App\Models\LandingPage::resolveTripDatesForDisplay($tripDatesRaw);
    $phName = $v['booking_name_placeholder'] ?? 'Full name';
    $phEmail = $v['booking_email_placeholder'] ?? 'Email';
    $phPhone = $v['booking_phone_placeholder'] ?? 'Phone';
    $phTrip = $v['booking_trip_placeholder'] ?? 'Preferred trip date';
    $btnContinue = $v['continue_modal_submit'] ?? 'Continue';
    $btnCancel = $v['continue_modal_cancel'] ?? 'Cancel';
@endphp
<div id="lpContinueModal" class="lp-continue-modal" hidden role="dialog" aria-modal="true" aria-labelledby="lpContinueModalTitle">
    <div class="lp-continue-modal__dialog">
        <button type="button" class="lp-continue-modal__close" id="lpContinueModalClose" aria-label="Close">&times;</button>
        <h2 id="lpContinueModalTitle">{{ $modalTitle }}</h2>
        @if($modalSubtitle !== '')
            <p>{{ $modalSubtitle }}</p>
        @else
            <p>Enter your details. We will save your information, then take you to the next step.</p>
        @endif
        <form id="lpContinueModalForm" novalidate>
            <div class="lp-continue-modal__field">
                <label for="lpContinueName">{{ $phName }} <span class="lp-continue-modal__req" aria-hidden="true">*</span></label>
                <input type="text" id="lpContinueName" name="name" required autocomplete="name" placeholder="{{ $phName }}">
            </div>
            <div class="lp-continue-modal__field">
                <label for="lpContinueEmail">{{ $phEmail }} <span class="lp-continue-modal__req" aria-hidden="true">*</span></label>
                <input type="email" id="lpContinueEmail" name="email" required autocomplete="email" placeholder="{{ $phEmail }}">
            </div>
            <div class="lp-continue-modal__field">
                <label for="lpContinuePhone">{{ $phPhone }}</label>
                <input type="text" id="lpContinuePhone" name="phone" autocomplete="tel" placeholder="{{ $phPhone }}">
            </div>
            <div class="lp-continue-modal__field">
                <label for="lpContinueTrip">{{ $phTrip }}</label>
                <select id="lpContinueTrip" name="tripDate" aria-label="{{ $phTrip }}">
                    <option value="">{{ $phTrip }}</option>
                    @foreach($tripDates as $trip)
                        @php
                            $optPhase = trim((string) ($trip['phase'] ?? ''));
                            $optDate = trim((string) ($trip['date'] ?? ''));
                            $optLabel = $optPhase !== '' ? $optPhase.' — '.$optDate : $optDate;
                        @endphp
                        <option value="{{ $optDate }}">{{ $optLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lp-continue-modal__actions">
                <button type="button" class="lp-continue-modal__btn-secondary" id="lpContinueModalCancel">{{ $btnCancel }}</button>
                <button type="submit" class="lp-continue-modal__btn-primary" id="lpContinueModalSubmit">{{ $btnContinue }}</button>
            </div>
        </form>
    </div>
</div>
