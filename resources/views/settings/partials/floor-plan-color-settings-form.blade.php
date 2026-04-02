{{--
  Floor plan & public view color settings (accent + booked tick). Global Settings → Color tab.
  @param string $formId
  @param string $idPrefix  Prefix for HTML ids only.
--}}
@php
    $p = $idPrefix ?? '';
@endphp
<form id="{{ $formId }}" action="{{ route('settings.public-view.save') }}" method="POST" class="settings-form-ajax {{ $extraFormClass ?? '' }}">
    @csrf
    <input type="hidden" name="floor_plan_color_scope" value="1">
    <div class="mb-3">
        <label class="form-label" for="{{ $p }}public_view_button_color">Public view button color</label>
        <div class="input-group" style="max-width: 320px;">
            <input type="color" class="form-control form-control-color" name="public_view_button_color" id="{{ $p }}public_view_button_color" value="{{ old('public_view_button_color', $publicViewButtonColor ?? '#28a745') }}" title="Primary button color">
            <input type="text" class="form-control" value="{{ old('public_view_button_color', $publicViewButtonColor ?? '#28a745') }}" id="{{ $p }}public_view_button_color_hex" maxlength="7" pattern="#[0-9A-Fa-f]{6}" placeholder="#28a745" aria-label="Button color hex" autocomplete="off">
        </div>
        <small class="text-muted d-block mt-1">Accent for primary actions on the public floor plan (Book this booth, toolbar controls, booking modal, help headings). Booth status colors on the map are configured below.</small>
    </div>
    <div class="mb-3">
        <input type="hidden" name="booth_booked_show_tick" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="booth_booked_show_tick" id="{{ $p }}booth_booked_show_tick" value="1" {{ ($showBookedTick ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $p }}booth_booked_show_tick">
                <strong>Show tick sign on booked booths (canvas and public view)</strong>
            </label>
        </div>
        <small class="text-muted d-block mt-1">When enabled, a check (✓) appears on booked booths on the canvas designer and on the public floor plan view.</small>
    </div>
    <div class="mb-3 ps-3 border-start border-2 border-secondary">
        <h6 class="mb-2">Booked tick appearance</h6>
        <div class="mb-3">
            <label class="form-label" for="{{ $p }}tick_floor_plan_id">Apply to floor plan</label>
            <select class="form-select" name="tick_floor_plan_id" id="{{ $p }}tick_floor_plan_id" style="max-width: 320px;">
                <option value="">Default (all floor plans)</option>
                @foreach($floorPlans ?? [] as $fp)
                    <option value="{{ $fp->id }}" {{ (isset($tickFloorPlanId) && (string)$tickFloorPlanId === (string)$fp->id) ? 'selected' : '' }}>{{ $fp->name }}{{ $fp->is_default ? ' (default)' : '' }}</option>
                @endforeach
            </select>
            <small class="text-muted d-block mt-1">Choose &quot;Default&quot; to set global tick style (used when a floor plan has no own settings). Choose a floor plan to set tick style for that floor plan only.</small>
        </div>
        <div class="row g-3">
            <div class="col-md-6 col-lg-4">
                <label class="form-label" for="{{ $p }}booth_booked_tick_color">Tick color (font)</label>
                <div class="input-group">
                    <input type="color" class="form-control form-control-color" name="booth_booked_tick_color" id="{{ $p }}booth_booked_tick_color" value="{{ old('booth_booked_tick_color', $bookedTickColor ?? '#28a745') }}" title="Tick color">
                    <input type="text" class="form-control" value="{{ old('booth_booked_tick_color', $bookedTickColor ?? '#28a745') }}" id="{{ $p }}booth_booked_tick_color_hex" maxlength="7" pattern="#[0-9A-Fa-f]{6}" placeholder="#28a745" aria-label="Hex color">
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label" for="{{ $p }}booth_booked_tick_font_size">Font size</label>
                <select class="form-select" name="booth_booked_tick_font_size" id="{{ $p }}booth_booked_tick_font_size">
                    <option value="small" {{ ($bookedTickFontSize ?? 'medium') === 'small' ? 'selected' : '' }}>Small</option>
                    <option value="medium" {{ ($bookedTickFontSize ?? 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="large" {{ ($bookedTickFontSize ?? 'medium') === 'large' ? 'selected' : '' }}>Large</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label" for="{{ $p }}booth_booked_tick_size_mode">Overall size</label>
                <select class="form-select" name="booth_booked_tick_size_mode" id="{{ $p }}booth_booked_tick_size_mode">
                    <option value="fixed" {{ ($bookedTickSizeMode ?? 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed (use Box size / Font size below)</option>
                    <option value="relative" {{ ($bookedTickSizeMode ?? 'fixed') === 'relative' ? 'selected' : '' }}>Relative (% of booth width &amp; height)</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-4" id="{{ $p }}booth_booked_tick_relative_wrap">
                <label class="form-label" for="{{ $p }}booth_booked_tick_relative_percent">Tick size (% of booth width &amp; height)</label>
                <select class="form-select" name="booth_booked_tick_relative_percent" id="{{ $p }}booth_booked_tick_relative_percent">
                    <option value="8" {{ ($bookedTickRelativePercent ?? '12') === '8' ? 'selected' : '' }}>8%</option>
                    <option value="10" {{ ($bookedTickRelativePercent ?? '12') === '10' ? 'selected' : '' }}>10%</option>
                    <option value="12" {{ ($bookedTickRelativePercent ?? '12') === '12' ? 'selected' : '' }}>12%</option>
                    <option value="15" {{ ($bookedTickRelativePercent ?? '12') === '15' ? 'selected' : '' }}>15%</option>
                    <option value="20" {{ ($bookedTickRelativePercent ?? '12') === '20' ? 'selected' : '' }}>20%</option>
                </select>
                <small class="text-muted">Tick box is this % of booth width and % of booth height.</small>
            </div>
            <div class="col-md-6 col-lg-4" id="{{ $p }}booth_booked_tick_fixed_wrap">
                <label class="form-label" for="{{ $p }}booth_booked_tick_size">Box size</label>
                <select class="form-select" name="booth_booked_tick_size" id="{{ $p }}booth_booked_tick_size">
                    <option value="small" {{ ($bookedTickSize ?? 'medium') === 'small' ? 'selected' : '' }}>Small</option>
                    <option value="medium" {{ ($bookedTickSize ?? 'medium') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="large" {{ ($bookedTickSize ?? 'medium') === 'large' ? 'selected' : '' }}>Large</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label" for="{{ $p }}booth_booked_tick_shape">Shape</label>
                <select class="form-select" name="booth_booked_tick_shape" id="{{ $p }}booth_booked_tick_shape">
                    <option value="round" {{ ($bookedTickShape ?? 'round') === 'round' ? 'selected' : '' }}>Round</option>
                    <option value="square" {{ ($bookedTickShape ?? 'round') === 'square' ? 'selected' : '' }}>Square</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label" for="{{ $p }}booth_booked_tick_position">Position</label>
                <select class="form-select" name="booth_booked_tick_position" id="{{ $p }}booth_booked_tick_position">
                    <option value="top-right" {{ ($bookedTickPosition ?? 'top-right') === 'top-right' ? 'selected' : '' }}>Top right (outside)</option>
                    <option value="top-left" {{ ($bookedTickPosition ?? 'top-right') === 'top-left' ? 'selected' : '' }}>Top left (outside)</option>
                    <option value="bottom-right" {{ ($bookedTickPosition ?? 'top-right') === 'bottom-right' ? 'selected' : '' }}>Bottom right (outside)</option>
                    <option value="bottom-left" {{ ($bookedTickPosition ?? 'top-right') === 'bottom-left' ? 'selected' : '' }}>Bottom left (outside)</option>
                    <option value="inside" {{ ($bookedTickPosition ?? 'top-right') === 'inside' ? 'selected' : '' }}>Inside booth (top-right)</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label" for="{{ $p }}booth_booked_tick_animation">Animation</label>
                <select class="form-select" name="booth_booked_tick_animation" id="{{ $p }}booth_booked_tick_animation">
                    <option value="pulse" {{ ($bookedTickAnimation ?? 'pulse') === 'pulse' ? 'selected' : '' }}>Pulse</option>
                    <option value="none" {{ ($bookedTickAnimation ?? 'pulse') === 'none' ? 'selected' : '' }}>None</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label">Background</label>
                <div class="mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="booth_booked_tick_bg_none" id="{{ $p }}booth_booked_tick_bg_none" value="1" {{ empty($bookedTickBgColor ?? '') ? 'checked' : '' }}>
                        <label class="form-check-label" for="{{ $p }}booth_booked_tick_bg_none">No background (transparent)</label>
                    </div>
                </div>
                <div class="input-group" id="{{ $p }}booth_booked_tick_bg_wrap">
                    <input type="color" class="form-control form-control-color" name="booth_booked_tick_bg_color" id="{{ $p }}booth_booked_tick_bg_color" value="{{ old('booth_booked_tick_bg_color', !empty($bookedTickBgColor) ? $bookedTickBgColor : '#ffffff') }}" title="Tick background">
                    <input type="text" class="form-control" value="{{ old('booth_booked_tick_bg_color', !empty($bookedTickBgColor) ? $bookedTickBgColor : '#ffffff') }}" id="{{ $p }}booth_booked_tick_bg_color_hex" maxlength="7" pattern="#[0-9A-Fa-f]{6}" placeholder="#ffffff" aria-label="Background hex">
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label" for="{{ $p }}booth_booked_tick_border_width">Border width</label>
                <select class="form-select" name="booth_booked_tick_border_width" id="{{ $p }}booth_booked_tick_border_width">
                    <option value="0" {{ ($bookedTickBorderWidth ?? '0') === '0' ? 'selected' : '' }}>None</option>
                    <option value="1" {{ ($bookedTickBorderWidth ?? '0') === '1' ? 'selected' : '' }}>1px</option>
                    <option value="2" {{ ($bookedTickBorderWidth ?? '0') === '2' ? 'selected' : '' }}>2px</option>
                    <option value="3" {{ ($bookedTickBorderWidth ?? '0') === '3' ? 'selected' : '' }}>3px</option>
                </select>
            </div>
            <div class="col-md-6 col-lg-4">
                <label class="form-label" for="{{ $p }}booth_booked_tick_border_color">Border color</label>
                <div class="input-group">
                    <input type="color" class="form-control form-control-color" name="booth_booked_tick_border_color" id="{{ $p }}booth_booked_tick_border_color" value="{{ old('booth_booked_tick_border_color', $bookedTickBorderColor ?? '#ffffff') }}" title="Border color">
                    <input type="text" class="form-control" value="{{ old('booth_booked_tick_border_color', $bookedTickBorderColor ?? '#ffffff') }}" id="{{ $p }}booth_booked_tick_border_color_hex" maxlength="7" pattern="#[0-9A-Fa-f]{6}" placeholder="#ffffff" aria-label="Border hex">
                </div>
            </div>
        </div>
        <small class="text-muted d-block mt-1">Customize how the booked tick looks on canvas and public view. Colors must be hex (e.g. #28a745).</small>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>Save floor plan colors &amp; tick
        </button>
        <button type="button" class="btn btn-outline-warning" id="{{ $p }}btnRestoreFloorPlanColorDefaults">
            <i class="fas fa-undo me-1"></i>Restore defaults
        </button>
    </div>
    <p class="text-muted small mt-2 mb-0">Restore applies to the <strong>public button color</strong> and <strong>booked tick</strong> for the floor plan selected above (or global default).</p>
</form>
