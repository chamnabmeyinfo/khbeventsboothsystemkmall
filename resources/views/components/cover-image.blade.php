@php
    // Get cover URL
    $coverUrl = $cover ?? $image ?? null;
    if ($coverUrl && !filter_var($coverUrl, FILTER_VALIDATE_URL)) {
        $coverUrl = asset($coverUrl);
    }
    
    // Default gradient
    $defaultGradient = $gradient ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    
    // Height
    $height = $height ?? '300px';
    
    // Overlay content
    $showOverlay = $showOverlay ?? false;
    $overlayContent = $overlayContent ?? null;
    
    // Clickable for upload
    $clickable = $clickable ?? false;
    $uploadAction = $uploadAction ?? null;
@endphp

<div class="cover-image-wrapper" 
     style="position: relative; width: 100%; height: {{ $height }}; border-radius: {{ $radius ?? '16px' }}; overflow: hidden; box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);">
    @if($coverUrl)
        <img src="{{ $coverUrl }}" 
             alt="Cover Image"
             class="cover-image"
             style="width: 100%; height: 100%; object-fit: cover;"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="cover-placeholder" style="display: none; width: 100%; height: 100%; background: {{ $defaultGradient }}; align-items: center; justify-content: center;">
            <i class="fas fa-image fa-3x" style="color: rgba(255,255,255,0.3);"></i>
        </div>
    @else
        <div class="cover-placeholder" 
             style="width: 100%; height: 100%; background: {{ $defaultGradient }}; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-image fa-3x" style="color: rgba(255,255,255,0.3);"></i>
        </div>
    @endif
    
    @if($showOverlay || $overlayContent)
    <div class="cover-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.5) 100%); display: flex; align-items: flex-end; padding: 24px;">
        {!! $overlayContent ?? '' !!}
    </div>
    @endif
    
    @if($clickable && $uploadAction)
    <div class="cover-upload-overlay" 
         style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; cursor: pointer;"
         onclick="{{ $uploadAction }}">
        <div style="text-align: center; color: white;">
            <i class="fas fa-camera fa-2x mb-2"></i>
            <p class="mb-0">Change Cover</p>
        </div>
    </div>
    @endif
</div>

@once
@push('styles')
<style>
    .cover-image-wrapper {
        transition: all 0.3s;
    }
    
    .cover-image-wrapper:hover .cover-upload-overlay {
        display: flex !important;
    }
    
    .cover-image {
        transition: transform 0.3s;
    }
    
    .cover-image-wrapper:hover .cover-image {
        transform: scale(1.02);
    }
</style>
@endpush
@endonce
