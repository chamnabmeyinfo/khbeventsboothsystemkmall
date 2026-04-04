{{--
  Public floor plan behavior only (no colors). Used on /settings Public View Actions card.
  @param string $formId
  @param string $idPrefix
--}}
@php
    $p = $idPrefix ?? '';
@endphp
<form id="{{ $formId }}" action="{{ route('settings.public-view.save') }}" method="POST" class="settings-form-ajax {{ $extraFormClass ?? '' }}">
    @csrf
    <input type="hidden" name="pv_behavior_scope" value="1">
    <div class="settings-mini-panel mb-3">
        <input type="hidden" name="public_view_allow_create_booking" value="0">
        <div class="form-check form-switch d-flex align-items-start gap-3">
            <input class="form-check-input settings-form-switch flex-shrink-0" type="checkbox" name="public_view_allow_create_booking" id="{{ $p }}public_view_allow_create_booking" value="1" {{ ($publicViewAllowCreate ?? true) ? 'checked' : '' }}>
            <div>
                <label class="form-check-label fw-semibold" for="{{ $p }}public_view_allow_create_booking">Allow create booking on public view</label>
                <p class="text-muted small mb-0 mt-1">When on, users with <strong>Create Bookings</strong> can create a booking from the public floor plan (e.g. sales using the public link).</p>
            </div>
        </div>
    </div>
    <div class="settings-mini-panel mb-4">
        <input type="hidden" name="public_view_restrict_crud_to_own_booking" value="0">
        <div class="form-check form-switch d-flex align-items-start gap-3">
            <input class="form-check-input settings-form-switch flex-shrink-0" type="checkbox" name="public_view_restrict_crud_to_own_booking" id="{{ $p }}public_view_restrict_crud_to_own_booking" value="1" {{ ($publicViewRestrictOwn ?? true) ? 'checked' : '' }}>
            <div>
                <label class="form-check-label fw-semibold" for="{{ $p }}public_view_restrict_crud_to_own_booking">Restrict booking CRUD to own bookings (non-admin)</label>
                <p class="text-muted small mb-0 mt-1">Non-admins only manage <strong>their own</strong> bookings. Administrators always see all bookings.</p>
            </div>
        </div>
    </div>
    <button type="submit" class="action-btn action-btn-primary">
        <i class="fas fa-save" aria-hidden="true"></i>Save public view options
    </button>
</form>
