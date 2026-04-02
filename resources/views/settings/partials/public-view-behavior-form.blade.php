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
    <div class="mb-3">
        <input type="hidden" name="public_view_allow_create_booking" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="public_view_allow_create_booking" id="{{ $p }}public_view_allow_create_booking" value="1" {{ ($publicViewAllowCreate ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $p }}public_view_allow_create_booking">
                <strong>Allow create booking on public view</strong>
            </label>
        </div>
        <small class="text-muted d-block mt-1">When enabled, logged-in users with &quot;Create Bookings&quot; permission can create a booking from the public floor plan page (e.g. Sales can book a booth from the public link).</small>
    </div>
    <div class="mb-3">
        <input type="hidden" name="public_view_restrict_crud_to_own_booking" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="public_view_restrict_crud_to_own_booking" id="{{ $p }}public_view_restrict_crud_to_own_booking" value="1" {{ ($publicViewRestrictOwn ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $p }}public_view_restrict_crud_to_own_booking">
                <strong>Restrict booking CRUD to own bookings (non-admin)</strong>
            </label>
        </div>
        <small class="text-muted d-block mt-1">When enabled, users who are not Administrators can only view, edit, update, and delete <strong>their own</strong> bookings (bookings they created). Sales can only manage their own; they cannot edit or delete other sales&#39; bookings. Administrators can always manage all bookings.</small>
    </div>
    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save me-1"></i>Save public view options
    </button>
</form>
