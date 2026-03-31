{{--
  Upload control form (Global Settings). Reused on /settings and in Booth list settings modal.
  @param string $formId  e.g. uploadControlForm
  @param string $idPrefix  Prefix for HTML ids only (names unchanged). Use '' on settings page; e.g. boothsEmbed_ in modal.
  @param array $uploadSettings
--}}
@php
    $p = $idPrefix ?? '';
@endphp
<form id="{{ $formId }}" action="{{ route('settings.upload-control.save') }}" method="POST" class="settings-form-ajax {{ $extraFormClass ?? '' }}">
    @csrf
    <div class="mb-4">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="uploads_enabled" id="{{ $p }}uploads_enabled" value="1" {{ ($uploadSettings['uploads_enabled'] ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $p }}uploads_enabled">
                <strong>Allow file uploads</strong>
            </label>
        </div>
        <small class="text-muted d-block mt-1">When disabled, all users cannot upload files.</small>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label" for="{{ $p }}uploads_max_size_mb">Global max size (MB)</label>
            <input type="number" class="form-control" name="uploads_max_size_mb" id="{{ $p }}uploads_max_size_mb" value="{{ old('uploads_max_size_mb', $uploadSettings['uploads_max_size_mb'] ?? '') }}" min="0" max="100" step="0.5" placeholder="10">
            <small class="text-muted">Default for all uploads. Override per context below.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="{{ $p }}uploads_allowed_extensions">Global allowed extensions</label>
            <input type="text" class="form-control" name="uploads_allowed_extensions" id="{{ $p }}uploads_allowed_extensions" value="{{ old('uploads_allowed_extensions', $uploadSettings['uploads_allowed_extensions'] ?? '') }}" placeholder="jpg, png, gif, pdf">
            <small class="text-muted">Comma-separated. Empty = use context defaults.</small>
        </div>
    </div>
    <hr class="my-4">
    <h6 class="mb-3">Per-context limits (override global)</h6>
    <div class="looker-table-container">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Context</th>
                    <th style="width: 140px;">Max size (MB)</th>
                    <th>Allowed extensions</th>
                </tr>
            </thead>
            <tbody>
                @foreach(['floor_plan' => 'Floor plan images', 'booth' => 'Booth images', 'avatar' => 'Avatars', 'cover' => 'Cover images', 'document' => 'HR documents', 'training_certificate' => 'Training certificates', 'company_logo' => 'Company logo/favicon'] as $ctx => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm" name="uploads_{{ $ctx }}_max_size_mb" value="{{ old("uploads_{$ctx}_max_size_mb", $uploadSettings["uploads_{$ctx}_max_size_mb"] ?? '') }}" min="0" max="100" step="0.5" placeholder="—">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="uploads_{{ $ctx }}_allowed_extensions" value="{{ old("uploads_{$ctx}_allowed_extensions", $uploadSettings["uploads_{$ctx}_allowed_extensions"] ?? '') }}" placeholder="e.g. jpg, png, gif">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>Save
        </button>
    </div>
</form>
