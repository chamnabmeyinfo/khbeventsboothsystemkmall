{{-- Bootstrap 5 modals for avatar/cover upload (used by React profile shell). --}}
<div class="modal fade" id="avatarUploadModal" tabindex="-1" aria-labelledby="avatarUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="avatarUploadModalLabel">
                    <i class="fas fa-camera me-2 text-primary"></i>{{ __('Upload Avatar') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body pt-2">
                <x-image-upload
                    type="avatar"
                    entity-type="client"
                    entity-id="{{ $client->id }}"
                    current-image="{{ $client->avatar }}"
                    name="{{ $client->name }}"
                />
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="coverUploadModal" tabindex="-1" aria-labelledby="coverUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="coverUploadModalLabel">
                    <i class="fas fa-image me-2 text-primary"></i>{{ __('Upload Cover Image') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body pt-2">
                <x-image-upload
                    type="cover"
                    entity-type="client"
                    entity-id="{{ $client->id }}"
                    current-image="{{ $client->cover_image }}"
                    name="{{ $client->name }}"
                />
            </div>
        </div>
    </div>
</div>
