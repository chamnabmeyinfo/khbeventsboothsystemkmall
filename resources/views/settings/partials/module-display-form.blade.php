{{--
  Module display customize form (all modules). Used on Global Settings tab and Booth list settings modal.
  @param string $moduleDisplayFormId
  @param string $moduleDisplayContainerId
  @param bool $useGlobalResetOnclick  When true, reload button calls loadModuleDisplaySettings() (settings page only).
--}}
@php
    $formId = $moduleDisplayFormId ?? 'moduleDisplayForm';
    $containerId = $moduleDisplayContainerId ?? 'moduleDisplayContainer';
    $useGlobal = $useGlobalResetOnclick ?? false;
@endphp
<form id="{{ $formId }}">
    @csrf
    <div class="row g-3" id="{{ $containerId }}">
        <div class="col-12">
            <div class="settings-module-loading text-center py-5">
                <div class="spinner-border settings-module-loading__spinner" role="status" aria-label="Loading">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted mb-0">Loading module settings…</p>
            </div>
        </div>
    </div>
    <div class="mt-4 d-flex flex-wrap gap-2">
        <button type="submit" class="action-btn action-btn-primary">
            <i class="fas fa-save" aria-hidden="true"></i>Save module display settings
        </button>
        <button type="button" class="action-btn action-btn-secondary" id="{{ $formId }}_resetBtn"
            @if($useGlobal)
                onclick="loadModuleDisplaySettings()"
                title="Fetch saved module visibility from the server and discard unsaved changes on this form. Does not change the database until you save."
            @endif
        >
            <i class="fas fa-sync-alt" aria-hidden="true"></i>Reload saved
        </button>
    </div>
</form>
