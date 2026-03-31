{{--
  Module display customize form (all modules). Used on Global Settings tab and Booth list settings modal.
  @param string $moduleDisplayFormId
  @param string $moduleDisplayContainerId
  @param bool $useGlobalResetOnclick  When true, reset button calls loadModuleDisplaySettings() (settings page only).
--}}
@php
    $formId = $moduleDisplayFormId ?? 'moduleDisplayForm';
    $containerId = $moduleDisplayContainerId ?? 'moduleDisplayContainer';
    $useGlobal = $useGlobalResetOnclick ?? false;
@endphp
<form id="{{ $formId }}">
    @csrf
    <div class="row g-3" id="{{ $containerId }}">
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading module settings...</p>
        </div>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i>Save Module Display Settings
        </button>
        <button type="button" class="btn btn-secondary ms-2" id="{{ $formId }}_resetBtn"
            @if($useGlobal)
                onclick="loadModuleDisplaySettings()"
            @endif
        >
            <i class="fas fa-sync-alt me-2"></i>Reset to Defaults
        </button>
    </div>
</form>
