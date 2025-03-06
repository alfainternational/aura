<div class="toast {{ $class ?? '' }}" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="{{ $autohide ?? 'true' }}" data-bs-delay="{{ $delay ?? 5000 }}">
    <div class="toast-header {{ $headerClass ?? '' }}">
        @if(isset($icon))
            <i class="{{ $icon }} me-2"></i>
        @endif
        
        <strong class="me-auto">{{ $title ?? 'إشعار' }}</strong>
        
        @if(isset($time))
            <small>{{ $time }}</small>
        @endif
        
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    
    <div class="toast-body">
        {{ $slot }}
    </div>
</div>
