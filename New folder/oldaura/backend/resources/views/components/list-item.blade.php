<div class="list-group-item {{ isset($active) && $active ? 'active' : '' }} {{ $class ?? '' }}">
    @if(isset($title))
        <div class="d-flex w-100 justify-content-between">
            <h5 class="mb-1">{{ $title }}</h5>
            @if(isset($badge))
                <span class="badge {{ $badgeClass ?? 'bg-primary' }}">{{ $badge }}</span>
            @endif
        </div>
    @endif
    
    @if(isset($subtitle))
        <p class="mb-1">{{ $subtitle }}</p>
    @endif
    
    {{ $slot }}
    
    @if(isset($footer))
        <small class="text-muted">{{ $footer }}</small>
    @endif
</div>
