@props([
    'message' => null,
    'isOutgoing' => false,
    'showSender' => false,
])

<div class="message-content {{ $isOutgoing ? 'bg-primary text-white' : 'bg-light' }}">
    @if($showSender && $message->sender_name)
        <div class="sender-name small mb-1">{{ $message->sender_name }}</div>
    @endif
    
    @if($message->replied_to)
        <div class="replied-message {{ $isOutgoing ? 'bg-primary-light text-white-50' : 'bg-light-gray text-muted' }} p-2 mb-2 rounded border-start border-3 border-info">
            <small class="d-block mb-1">
                <i class="bi bi-reply-fill me-1"></i> {{ $message->replied_to->sender_name ?? 'مستخدم' }}
            </small>
            <div class="replied-text text-truncate">{{ Str::limit($message->replied_to->body, 50) }}</div>
        </div>
    @endif
    
    @if($message->forwarded_from)
        <div class="forwarded-message {{ $isOutgoing ? 'bg-primary-light text-white-50' : 'bg-light-gray text-muted' }} p-2 mb-2 rounded border-start border-3 border-info">
            <small class="d-block mb-1">
                <i class="bi bi-forward-fill me-1"></i> تم توجيهها من {{ $message->forwarded_from->sender_name ?? 'مستخدم' }}
            </small>
        </div>
    @endif
    
    @if($message->type === 'text')
        <div class="message-text">{{ $message->body }}</div>
    @elseif($message->type === 'image')
        <img src="{{ $message->media_url }}" alt="صورة" class="img-fluid rounded message-image" onclick="openImageViewer('{{ $message->media_url }}')">
    @elseif($message->type === 'voice')
        <div class="voice-message">
            <div class="d-flex align-items-center">
                <button class="btn btn-sm {{ $isOutgoing ? 'btn-light' : 'btn-primary' }} play-voice-btn me-2" data-audio-url="{{ $message->media_url }}">
                    <i class="bi bi-play-fill"></i>
                </button>
                <div class="voice-waveform flex-grow-1" style="height: 30px; background: rgba(0,0,0,0.1);"></div>
                <span class="ms-2 voice-duration small">{{ $message->duration ?? '0:00' }}</span>
            </div>
        </div>
    @elseif($message->type === 'file')
        <div class="file-attachment">
            <div class="d-flex align-items-center">
                <i class="bi bi-file-earmark me-2 fs-4"></i>
                <div>
                    <div class="file-name">{{ $message->file_name ?? 'ملف' }}</div>
                    <small class="text-{{ $isOutgoing ? 'white-50' : 'muted' }}">{{ $message->file_size ?? '0 KB' }}</small>
                </div>
                <a href="{{ $message->media_url }}" class="btn btn-sm {{ $isOutgoing ? 'btn-light' : 'btn-primary' }} ms-auto" download>
                    <i class="bi bi-download"></i>
                </a>
            </div>
        </div>
    @endif
    
    <div class="message-meta {{ $isOutgoing ? 'text-white-50' : 'text-muted' }} d-flex align-items-center">
        <small class="me-auto">{{ \Carbon\Carbon::parse($message->created_at)->format('H:i') }}</small>
        @if($isOutgoing)
            @if($message->read_at)
                <i class="bi bi-check2-all ms-1"></i>
            @elseif($message->delivered_at)
                <i class="bi bi-check2 ms-1"></i>
            @else
                <i class="bi bi-check ms-1"></i>
            @endif
        @endif
    </div>
</div>