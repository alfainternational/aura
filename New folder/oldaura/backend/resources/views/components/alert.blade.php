<div class="alert alert-{{ $variant ?? 'info' }} {{ isset($dismissible) && $dismissible ? 'alert-dismissible fade show' : '' }} {{ $class ?? '' }}" role="alert">
    @if(isset($icon))
        <i class="{{ $icon }} me-2"></i>
    @endif
    
    @if(isset($title))
        <strong>{{ $title }}</strong>
    @endif
    
    {{ $slot }}
    
    @if(isset($dismissible) && $dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    @endif
</div>
