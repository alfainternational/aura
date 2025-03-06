<div class="spinner-container {{ $containerClass ?? 'd-flex justify-content-center align-items-center' }}" style="{{ isset($fullscreen) && $fullscreen ? 'position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(255, 255, 255, 0.8); z-index: 9999;' : '' }}">
    <div class="spinner-{{ $type ?? 'border' }} {{ $class ?? 'text-primary' }}" role="status" style="{{ isset($size) ? "width: {$size}rem; height: {$size}rem;" : '' }}">
        <span class="visually-hidden">جاري التحميل...</span>
    </div>
    
    @if(isset($text))
        <span class="ms-2">{{ $text }}</span>
    @endif
</div>
