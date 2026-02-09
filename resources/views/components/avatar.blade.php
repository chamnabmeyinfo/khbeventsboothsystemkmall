@php
    // Default sizes: xs (16-20px), sm (24-32px), md (40-48px), lg (64-96px), xl (128px+)
    $sizeMap = [
        'xs' => '20px',
        'sm' => '32px',
        'md' => '48px',
        'lg' => '96px',
        'xl' => '128px',
    ];
    
    $size = $size ?? 'md';
    // If size contains 'px', use it directly; otherwise use sizeMap
    if (strpos($size, 'px') !== false) {
        $sizePx = $size;
        $sizeNum = intval(str_replace('px', '', $size));
    } else {
        $sizePx = $sizeMap[$size] ?? $sizeMap['md'];
        $sizeNum = intval($sizePx);
    }
    
    // Get initials or icon
    $initials = $initials ?? '';
    $name = $name ?? '';
    if (empty($initials) && !empty($name)) {
        $parts = explode(' ', $name);
        $initials = '';
        foreach ($parts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }
    }
    if (empty($initials)) {
        $initials = '?';
    }
    
    // Get avatar URL (normalize path so images load properly)
    $avatarUrl = \App\Helpers\AssetHelper::imageUrl($avatar ?? $image ?? null);
    
    // Type color for default avatar
    $type = $type ?? 'default';
    $typeColors = [
        'admin' => ['bg' => 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)', 'text' => '#fff'],
        'user' => ['bg' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'text' => '#fff'],
        'client' => ['bg' => 'linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%)', 'text' => '#fff'],
        'category' => ['bg' => 'linear-gradient(135deg, #30cfd0 0%, #330867 100%)', 'text' => '#fff'],
        'default' => ['bg' => 'linear-gradient(135deg, #718096 0%, #4a5568 100%)', 'text' => '#fff'],
    ];
    $colors = $typeColors[$type] ?? $typeColors['default'];
    
    // Shape: circle or square
    $shape = $shape ?? 'circle';
    $borderRadius = $shape === 'circle' ? '50%' : '12px';
    
    // Clickable
    $clickable = $clickable ?? false;
    $onClick = $onClick ?? null;
@endphp

<div class="avatar-wrapper {{ $clickable ? 'avatar-clickable' : '' }}" 
     style="width: {{ $sizePx }}; height: {{ $sizePx }}; position: relative; display: inline-flex; align-items: center; justify-content: center;"
     @if($onClick) onclick="{{ $onClick }}" @endif
     data-size="{{ $size }}"
     data-shape="{{ $shape }}">
    @if($avatarUrl)
        <img src="{{ $avatarUrl }}" 
             alt="{{ $name ?? 'Avatar' }}"
             class="avatar-image"
             style="width: 100%; height: 100%; object-fit: cover; border-radius: {{ $borderRadius }}; border: 2px solid rgba(255, 255, 255, 0.2); box-shadow: 0 2px 8px rgba(0,0,0,0.15);"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="avatar-placeholder" style="display: none; width: 100%; height: 100%; border-radius: {{ $borderRadius }}; background: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; align-items: center; justify-content: center; font-weight: 600; font-size: {{ $sizeNum * 0.4 }}px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
            {{ $initials }}
        </div>
    @else
        <div class="avatar-placeholder" 
             style="width: 100%; height: 100%; border-radius: {{ $borderRadius }}; background: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: {{ $sizeNum * 0.4 }}px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); border: 2px solid rgba(255, 255, 255, 0.2);">
            @if($icon ?? false)
                <i class="{{ $icon }}" style="font-size: {{ $sizeNum * 0.5 }}px;"></i>
            @else
                {{ $initials }}
            @endif
        </div>
    @endif
    
    @if($badge ?? false)
        <div class="avatar-badge" style="position: absolute; bottom: 0; right: 0; width: {{ $sizeNum * 0.3 }}px; height: {{ $sizeNum * 0.3 }}px; border-radius: 50%; background: {{ $badge['color'] ?? '#28a745' }}; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></div>
    @endif
</div>

@once
@push('styles')
<style>
    .avatar-clickable {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .avatar-clickable:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    
    .avatar-image {
        transition: opacity 0.3s;
    }
    
    .avatar-wrapper {
        flex-shrink: 0;
    }
</style>
@endpush
@endonce

