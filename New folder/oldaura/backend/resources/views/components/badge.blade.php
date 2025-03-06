<span class="badge bg-{{ $variant ?? 'primary' }} {{ $pill ?? 'rounded-pill' }} {{ $class ?? '' }}">
    @if(isset($icon))
        <i class="{{ $icon }} {{ isset($text) ? 'me-1' : '' }}"></i>
    @endif
    
    {{ $slot }}
</span>
