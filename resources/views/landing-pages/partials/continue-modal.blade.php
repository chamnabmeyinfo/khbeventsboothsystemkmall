@php
    $v = is_array($visual ?? null) ? $visual : [];
    $modalTitle = $v['continue_modal_title'] ?? 'Before you continue';
    $modalSubtitle = $v['continue_modal_subtitle'] ?? '';
    $tripDates = is_array($v['trip_dates'] ?? null) ? $v['trip_dates'] : [];
    $phName = $v['booking_name_placeholder'] ?? 'Full name';
    $phEmail = $v['booking_email_placeholder'] ?? 'Email';
    $phPhone = $v['booking_phone_placeholder'] ?? 'Phone';
    $phTrip = $v['booking_trip_placeholder'] ?? 'Preferred trip date';
    $btnContinue = $v['continue_modal_submit'] ?? 'Continue';
    $btnCancel = $v['continue_modal_cancel'] ?? 'Cancel';
@endphp
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400&family=Fraunces:opsz,wght@9..144,600&display=swap');
    #lpContinueModal.lp-continue-modal[hidden]{display:none !important}
    #lpContinueModal.lp-continue-modal.is-open{display:flex !important}
    .lp-continue-modal{
        position:fixed;inset:0;z-index:10050;display:none;align-items:center;justify-content:center;padding:16px;box-sizing:border-box;
        background:rgba(15,23,42,.58);backdrop-filter:blur(4px);
        font-family:"DM Sans",Hanuman,"Noto Sans Khmer","Khmer OS Battambang",system-ui,sans-serif;
    }
    .lp-continue-modal__dialog{
        position:relative;width:min(440px,100%);max-height:min(90vh,720px);overflow:auto;
        background:#fff;border-radius:20px;border:1px solid rgba(15,23,42,.08);
        box-shadow:0 24px 48px rgba(15,23,42,.18),0 0 0 1px rgba(255,255,255,.06) inset;
        padding:26px 22px 22px;
    }
    .lp-continue-modal__dialog h2{margin:0 0 10px;font-size:1.35rem;color:#0f172a;font-family:Fraunces,"DM Sans",serif;font-weight:600;letter-spacing:-0.02em}
    .lp-continue-modal__dialog p{margin:0 0 18px;font-size:0.95rem;color:#64748b;line-height:1.5}
    .lp-continue-modal__dialog label{display:block;font-weight:600;font-size:0.8rem;color:#334155;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
    .lp-continue-modal__dialog input,.lp-continue-modal__dialog select{
        width:100%;min-height:48px;border:1px solid #cbd5e1;border-radius:12px;padding:12px 14px;font-size:16px;box-sizing:border-box;
        transition:border-color .15s,box-shadow .15s;
    }
    .lp-continue-modal__dialog input:focus,.lp-continue-modal__dialog select:focus{
        outline:0;border-color:#c41e1e;box-shadow:0 0 0 3px rgba(196,30,30,.2);
    }
    .lp-continue-modal__field{margin-bottom:14px}
    .lp-continue-modal__actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:22px;justify-content:flex-end}
    .lp-continue-modal__actions button{min-height:48px;border-radius:999px;padding:12px 22px;font-weight:700;cursor:pointer;border:0;font-size:0.95rem}
    .lp-continue-modal__btn-primary{background:linear-gradient(135deg,#c41e1e 0%,#8b1414 100%);color:#fff;box-shadow:0 6px 20px rgba(196,30,30,.35)}
    .lp-continue-modal__btn-primary:hover{filter:brightness(1.05)}
    .lp-continue-modal__btn-primary:focus{outline:3px solid #c9a227;outline-offset:2px}
    .lp-continue-modal__btn-secondary{background:#f1f5f9;color:#0f172a}
    .lp-continue-modal__btn-secondary:hover{background:#e2e8f0}
    .lp-continue-modal__close{position:absolute;top:10px;right:10px;width:44px;height:44px;border:0;background:transparent;cursor:pointer;font-size:1.5rem;line-height:1;color:#64748b;border-radius:12px;padding:0}
    .lp-continue-modal__close:hover{background:#f1f5f9;color:#0f172a}
    .lp-continue-modal__close:focus{outline:2px solid #c41e1e;outline-offset:2px}
</style>
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
                <label for="lpContinueName">{{ $phName }} <span style="color:#b91c1c">*</span></label>
                <input type="text" id="lpContinueName" name="name" required autocomplete="name" placeholder="{{ $phName }}">
            </div>
            <div class="lp-continue-modal__field">
                <label for="lpContinueEmail">{{ $phEmail }} <span style="color:#b91c1c">*</span></label>
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
                        <option value="{{ $trip['date'] ?? '' }}">{{ $trip['date'] ?? '' }}</option>
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
