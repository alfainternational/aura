@props([
    'type' => 'image',
    'url' => null,
    'filename' => null,
    'filesize' => null,
    'duration' => null,
])

<div class="attachment-preview mb-3">
    @if($type === 'image')
        <div class="position-relative">
            <img src="{{ $url }}" alt="معاينة الصورة" class="img-fluid rounded attachment-preview-img">
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-attachment">
                <i class="bi bi-x"></i>
            </button>
        </div>
    @elseif($type === 'voice')
        <div class="voice-attachment p-2 border rounded d-flex align-items-center">
            <i class="bi bi-mic-fill me-2 text-primary"></i>
            <div class="flex-grow-1">
                <div>تسجيل صوتي</div>
                <small class="text-muted">{{ $duration ?? '0:00' }}</small>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary me-1 play-voice-preview" data-audio-url="{{ $url }}">
                    <i class="bi bi-play-fill"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger remove-attachment">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        </div>
    @elseif($type === 'file')
        <div class="file-attachment p-2 border rounded d-flex align-items-center">
            <i class="bi bi-file-earmark me-2 text-primary"></i>
            <div class="flex-grow-1">
                <div>{{ $filename ?? 'ملف' }}</div>
                <small class="text-muted">{{ $filesize ?? '0 KB' }}</small>
            </div>
            <button type="button" class="btn btn-sm btn-danger remove-attachment">
                <i class="bi bi-x"></i>
            </button>
        </div>
    @endif
</div>