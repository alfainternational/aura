<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'btn ' . ($variant ?? 'btn-primary') . ' ' . ($size ?? '') . ' ' . ($class ?? ''),
    ]) }}
>
    @if(isset($icon) && $iconPosition !== 'right')
        <i class="{{ $icon }} me-1"></i>
    @endif
    
    {{ $slot }}
    
    @if(isset($icon) && $iconPosition === 'right')
        <i class="{{ $icon }} ms-1"></i>
    @endif
</button>
