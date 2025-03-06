<div class="card shadow-sm {{ $class ?? '' }}">
    @if(isset($header))
        <div class="card-header {{ $headerClass ?? 'bg-white' }}">
            {{ $header }}
        </div>
    @endif
    
    <div class="card-body {{ $bodyClass ?? '' }}">
        @if(isset($title))
            <h5 class="card-title">{{ $title }}</h5>
        @endif
        
        @if(isset($subtitle))
            <h6 class="card-subtitle mb-2 text-muted">{{ $subtitle }}</h6>
        @endif
        
        <div class="card-text">
            {{ $slot }}
        </div>
    </div>
    
    @if(isset($footer))
        <div class="card-footer {{ $footerClass ?? 'bg-white' }}">
            {{ $footer }}
        </div>
    @endif
</div>
