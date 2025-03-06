@props([
    'conversation' => null, 
    'active' => false
])

<div class="conversation-item d-flex p-3 border-bottom {{ $active ? 'active' : '' }} {{ $conversation->is_pinned ? 'pinned' : '' }}">
    <div class="position-relative">
        @if($conversation->is_group)
            <div class="group-avatar">
                <i class="bi bi-people-fill fs-4"></i>
            </div>
        @else
            <img src="{{ $conversation->participant_photo ?? asset('images/default-avatar.png') }}" alt="{{ $conversation->name }}" class="avatar rounded-circle" style="width: 48px; height: 48px; object-fit: cover;">
            
            @if($conversation->is_online)
                <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-light rounded-circle"></span>
            @endif
        @endif
    </div>
    
    <div class="flex-grow-1 ps-3 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 text-truncate">{{ $conversation->name }}</h6>
            <small class="text-muted">{{ \Carbon\Carbon::parse($conversation->last_message_at)->format('H:i') }}</small>
        </div>
        
        <div class="d-flex justify-content-between align-items-center">
            <p class="mb-0 text-truncate text-muted small" style="max-width: 180px;">
                @if($conversation->last_message_type === 'text')
                    {{ $conversation->last_message }}
                @elseif($conversation->last_message_type === 'image')
                    <i class="bi bi-image me-1"></i> صورة
                @elseif($conversation->last_message_type === 'voice')
                    <i class="bi bi-mic-fill me-1"></i> رسالة صوتية
                @elseif($conversation->last_message_type === 'file')
                    <i class="bi bi-file-earmark me-1"></i> ملف
                @endif
            </p>
            
            <div class="d-flex align-items-center">
                @if($conversation->is_muted)
                    <i class="bi bi-bell-slash-fill text-muted me-2 small"></i>
                @endif
                
                @if($conversation->unread_count > 0)
                    <span class="badge bg-primary rounded-pill">{{ $conversation->unread_count }}</span>
                @endif
            </div>
        </div>
    </div>
</div>