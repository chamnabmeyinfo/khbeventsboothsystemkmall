@php
    $type = $type ?? 'avatar'; // avatar or cover
    $entityType = $entityType ?? 'user';
    $entityId = $entityId ?? null;
    $currentImage = $currentImage ?? null;
    $label = $label ?? ($type === 'avatar' ? 'Upload Avatar' : 'Upload Cover Image');
    $sizeLimit = $sizeLimit ?? ($type === 'avatar' ? '5MB' : '10MB');
    $allowedTypes = $allowedTypes ?? 'JPEG, JPG, PNG, GIF';
    $enableCrop = $enableCrop ?? ($type === 'avatar');
    $previewSize = $previewSize ?? ($type === 'avatar' ? '200px' : '100%');
@endphp

<div class="image-upload-component" data-type="{{ $type }}" data-entity-type="{{ $entityType }}" data-entity-id="{{ $entityId }}">
    <!-- Current Image Preview -->
    <div class="current-image-preview mb-3" style="{{ $currentImage ? '' : 'display: none;' }}">
        <div class="text-center">
            <div style="display: inline-block; position: relative;">
                @if($type === 'avatar')
                    <x-avatar :avatar="$currentImage" :name="$name ?? ''" :size="'lg'" :type="$entityType" />
                @else
                    <x-cover-image :cover="$currentImage" :height="'200px'" />
                @endif
                @if($currentImage)
                <button type="button" class="btn btn-sm btn-danger remove-image-btn" style="position: absolute; top: -8px; right: -8px; border-radius: 50%; width: 28px; height: 28px; padding: 0; line-height: 1;">
                    <i class="fas fa-times"></i>
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Upload Area -->
    <div class="upload-area {{ $currentImage ? 'has-image' : '' }}" 
         style="border: 2px dashed #cbd5e0; border-radius: 12px; padding: 32px; text-align: center; transition: all 0.3s; background: rgba(248, 249, 250, 0.5); cursor: pointer;"
         id="uploadArea_{{ $type }}_{{ $entityId }}">
        <input type="file" 
               class="image-file-input d-none" 
               accept="image/*" 
               id="fileInput_{{ $type }}_{{ $entityId }}"
               data-type="{{ $type }}"
               data-entity-type="{{ $entityType }}"
               data-entity-id="{{ $entityId }}">
        
        <div class="upload-content">
            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
            <p class="mb-2">
                <strong>Click to upload</strong> or drag and drop
            </p>
            <p class="text-muted small mb-0">
                {{ $allowedTypes }} (Max: {{ $sizeLimit }})
            </p>
        </div>
        
        <!-- Preview Container -->
        <div class="preview-container mt-3" style="display: none;">
            <div class="preview-wrapper" style="display: inline-block; position: relative;">
                <img id="previewImage_{{ $type }}_{{ $entityId }}" 
                     src="" 
                     alt="Preview"
                     style="max-width: {{ $previewSize }}; max-height: {{ $previewSize }}; border-radius: {{ $type === 'avatar' ? '50%' : '12px' }}; border: 2px solid #ddd; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                @if($enableCrop && $type === 'avatar')
                <div class="crop-controls mt-3">
                    <button type="button" class="btn btn-sm btn-primary crop-btn">
                        <i class="fas fa-crop mr-1"></i>Crop Image
                    </button>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Progress Bar -->
        <div class="upload-progress mt-3" style="display: none;">
            <div class="progress" style="height: 8px; border-radius: 4px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: 0%;"
                     aria-valuenow="0" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                </div>
            </div>
            <small class="text-muted mt-1 d-block">Uploading...</small>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="upload-actions mt-3" style="display: none;">
        <button type="button" class="btn btn-primary btn-sm save-image-btn">
            <i class="fas fa-save mr-1"></i>Save Image
        </button>
        <button type="button" class="btn btn-secondary btn-sm cancel-upload-btn">
            <i class="fas fa-times mr-1"></i>Cancel
        </button>
    </div>
</div>

@once
@push('styles')
<style>
    .upload-area {
        transition: all 0.3s;
    }
    
    .upload-area:hover {
        border-color: #667eea !important;
        background: rgba(102, 126, 234, 0.05) !important;
    }
    
    .upload-area.dragover {
        border-color: #667eea !important;
        background: rgba(102, 126, 234, 0.1) !important;
        transform: scale(1.02);
    }
    
    .upload-area.has-image {
        padding: 16px;
    }
    
    .preview-container img {
        transition: all 0.3s;
    }
</style>
@endpush
@endonce

