@extends('layouts.dashboard')

@section('title', 'المحادثة')

@section('page-title', isset($conversation) && $conversation->is_group ? $conversation->title : ($otherParticipant->name ?? 'محادثة'))

@section('content')
<div class="container-fluid py-4">
    <!-- رسائل النجاح والخطأ -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <x-card class="border-0 shadow-sm conversation-container" style="height: calc(100vh - 200px);">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('messaging.conversations.index') }}" class="me-3">
                                <i class="bi bi-arrow-right fs-4"></i>
                            </a>
                            <div class="position-relative">
                                <img src="{{ isset($conversation) && $conversation->is_group ? asset('images/group-avatar.png') : ($otherParticipant->profile_image_url ?? asset('images/default-avatar.png')) }}" 
                                     class="rounded-circle" alt="صورة المحادثة" width="40" height="40">
                                @if(isset($otherParticipant) && !$conversation->is_group)
                                <span class="position-absolute bottom-0 end-0 translate-middle p-1 {{ $otherParticipant->is_online ? 'bg-success' : 'bg-secondary' }} border border-light rounded-circle">
                                    <span class="visually-hidden">{{ $otherParticipant->is_online ? 'متصل' : 'غير متصل' }}</span>
                                </span>
                                @endif
                            </div>
                            <div class="ms-2">
                                <h6 class="mb-0">{{ isset($conversation) && $conversation->is_group ? $conversation->title : ($otherParticipant->name ?? 'مستخدم محذوف') }}</h6>
                                @if(isset($otherParticipant) && !$conversation->is_group)
                                <small class="text-muted">{{ $otherParticipant->is_online ? 'متصل الآن' : 'آخر ظهور ' . $otherParticipant->last_active_at->diffForHumans() }}</small>
                                @else
                                <small class="text-muted">{{ isset($conversation) ? $conversation->participants_count . ' مشاركين' : '' }}</small>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    @if(isset($conversation) && $conversation->is_group)
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#groupInfoModal"><i class="bi bi-info-circle me-2"></i>معلومات المجموعة</a></li>
                                    @else
                                    <li><a class="dropdown-item" href="{{ route('messaging.contacts.show', $otherParticipant->id ?? 0) }}"><i class="bi bi-person me-2"></i>عرض الملف الشخصي</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#mediaModal"><i class="bi bi-images me-2"></i>الوسائط المشتركة</a></li>
                                    <li><a class="dropdown-item" href="{{ route('messaging.messages.search', $conversation->id ?? 0) }}"><i class="bi bi-search me-2"></i>البحث في الرسائل</a></li>
                                    <li><a class="dropdown-item" href="{{ route('messaging.messages.pinned', $conversation->id ?? 0) }}"><i class="bi bi-pin-angle me-2"></i>الرسائل المثبتة</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#reportModal"><i class="bi bi-flag me-2"></i>إبلاغ</a></li>
                                    <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#blockModal"><i class="bi bi-slash-circle me-2"></i>حظر</a></li>
                                </ul>
                            </div>
                            <a href="{{ route('messaging.voice-calls.start', ['user' => $otherParticipant->id ?? 0]) }}" class="btn btn-sm btn-primary ms-2">
                                <i class="bi bi-telephone"></i>
                            </a>
                        </div>
                    </div>
                </x-slot>

                <div class="conversation-messages" id="messages-container">
                    <div class="text-center text-muted my-3">
                        <small>{{ isset($conversation) ? $conversation->created_at->format('Y-m-d') : now()->format('Y-m-d') }}</small>
                    </div>
                    
                    @if(isset($messages))
                    @forelse($messages as $message)
                    <div class="message-wrapper {{ $message->sender_id == auth()->id() ? 'mine' : 'theirs' }}" id="message-{{ $message->id }}">
                        <div class="message {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                            <!-- إذا كانت الرسالة رداً على رسالة أخرى -->
                            @if($message->replied_to_id)
                            <div class="replied-message {{ $message->sender_id == auth()->id() ? 'bg-primary-light text-white-50' : 'bg-light-gray text-muted' }} p-2 mb-2 rounded border-start border-3 border-primary">
                                <small class="d-block mb-1">
                                    <i class="bi bi-reply-fill me-1"></i> رد على {{ $message->repliedTo->sender->name ?? 'مستخدم محذوف' }}
                                </small>
                                <div class="text-truncate">{{ $message->repliedTo->message ?? '' }}</div>
                            </div>
                            @endif

                            <!-- إذا كانت الرسالة موجهة من رسالة أخرى -->
                            @if($message->forwarded_from_id)
                            <div class="forwarded-message {{ $message->sender_id == auth()->id() ? 'bg-primary-light text-white-50' : 'bg-light-gray text-muted' }} p-2 mb-2 rounded border-start border-3 border-info">
                                <small class="d-block mb-1">
                                    <i class="bi bi-forward-fill me-1"></i> تم توجيهها من {{ $message->forwardedFrom->sender->name ?? 'مستخدم محذوف' }}
                                </small>
                            </div>
                            @endif

                            @if($message->type == 'text')
                                {{ $message->body }}
                            @elseif($message->type == 'image')
                                <img src="{{ asset('storage/' . $message->media_url) }}" alt="صورة" class="img-fluid rounded" style="max-width: 300px;">
                            @elseif($message->type == 'voice')
                                <audio controls class="w-100">
                                    <source src="{{ asset('storage/' . $message->media_url) }}" type="audio/mpeg">
                                    المتصفح لا يدعم تشغيل الصوت
                                </audio>
                            @endif

                            <!-- إذا كانت الرسالة مثبتة -->
                            @if($message->is_pinned)
                            <div class="pinned-badge mt-1 {{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                <small><i class="bi bi-pin-angle-fill me-1"></i> مثبتة</small>
                            </div>
                            @endif

                            <div class="message-info {{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}">
                                <small>{{ $message->created_at->format('H:i') }}</small>
                                @if($message->sender_id == auth()->id())
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

                        <!-- قائمة خيارات الرسالة -->
                        <div class="message-actions {{ $message->sender_id == auth()->id() ? 'text-end' : 'text-start' }} mt-1">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-sm btn-light reply-btn" 
                                        data-message-id="{{ $message->id }}" 
                                        data-sender-name="{{ $message->sender->name ?? 'مستخدم محذوف' }}" 
                                        data-message-text="{{ $message->message ?? '' }}">
                                    <i class="bi bi-reply"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light forward-btn" 
                                        data-message-id="{{ $message->id }}">
                                    <i class="bi bi-forward"></i>
                                </button>
                                @if((!$conversation->is_group) || ($conversation->is_group && auth()->user()->isAdminInConversation($conversation)))
                                    @if(!$message->is_pinned)
                                    <button type="button" class="btn btn-sm btn-light pin-btn" 
                                            data-message-id="{{ $message->id }}">
                                        <i class="bi bi-pin-angle"></i>
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-sm btn-light unpin-btn" 
                                            data-message-id="{{ $message->id }}">
                                        <i class="bi bi-pin-angle-fill text-warning"></i>
                                    </button>
                                    @endif
                                @endif
                                @if($message->sender_id == auth()->id())
                                <button type="button" class="btn btn-sm btn-light delete-btn" 
                                        data-message-id="{{ $message->id }}">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                                @endif
                            </div>
                        </div>

                        @if($conversation->is_group && $message->sender_id != auth()->id())
                        <div class="sender-name text-muted">
                            <small>{{ $message->sender->name ?? 'مستخدم محذوف' }}</small>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <img src="{{ asset('images/start-chat.svg') }}" alt="ابدأ المحادثة" class="img-fluid mb-3" width="150">
                        <h5>ابدأ المحادثة الآن</h5>
                        <p class="text-muted">أرسل رسالة للبدء في المحادثة</p>
                    </div>
                    @endforelse
                    @endif
                </div>

                <x-slot name="footer">
                    <div id="reply-container" class="bg-light p-2 mb-2 rounded border-start border-3 border-primary d-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="d-block text-muted mb-1">
                                    <i class="bi bi-reply-fill me-1"></i> الرد على <span id="reply-sender-name"></span>
                                </small>
                                <div id="reply-message-text" class="text-truncate small"></div>
                            </div>
                            <button type="button" class="btn btn-sm text-muted" id="cancel-reply-btn">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <form id="message-form" class="message-input-form">
                        @csrf
                        <input type="hidden" id="replied-to-id" name="replied_to_id" value="">
                        <div class="d-flex align-items-end">
                            <div class="message-attachments me-2">
                                <button type="button" class="btn btn-light rounded-circle" id="image-button">
                                    <i class="bi bi-image"></i>
                                </button>
                                <input type="file" id="image-input" name="image" accept="image/*" style="display: none;">
                            </div>
                            <div class="message-voice me-2">
                                <button type="button" class="btn btn-light rounded-circle" id="voice-button">
                                    <i class="bi bi-mic"></i>
                                </button>
                            </div>
                            <div class="flex-grow-1">
                                <textarea class="form-control" id="message-input" name="message" rows="1" placeholder="اكتب رسالة..." style="resize: none;"></textarea>
                            </div>
                            <div class="ms-2">
                                <button type="submit" class="btn btn-primary rounded-circle" id="send-button">
                                    <i class="bi bi-send"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>

<!-- مودال معلومات المجموعة -->
<div class="modal fade" id="groupInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">معلومات المجموعة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(isset($conversation) && $conversation->is_group)
                <div class="text-center mb-3">
                    <img src="{{ asset('images/group-avatar.png') }}" class="rounded-circle mb-2" alt="صورة المجموعة" width="80" height="80">
                    <h5>{{ $conversation->title }}</h5>
                    <p class="text-muted">تم الإنشاء {{ $conversation->created_at->format('Y-m-d') }}</p>
                </div>

                <h6 class="mb-3">المشاركون ({{ $conversation->participants_count }})</h6>
                <ul class="list-group">
                    @foreach($conversation->participants ?? [] as $participant)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ $participant->profile_image_url ?? asset('images/default-avatar.png') }}" 
                                 class="rounded-circle me-2" alt="صورة المستخدم" width="40" height="40">
                            <div>
                                <h6 class="mb-0">{{ $participant->name }}</h6>
                                <small class="text-muted">{{ $participant->is_admin ? 'مسؤول' : 'عضو' }}</small>
                            </div>
                        </div>
                        @if($conversation->user_is_admin)
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">{{ $participant->is_admin ? 'إزالة كمسؤول' : 'تعيين كمسؤول' }}</a></li>
                                <li><a class="dropdown-item text-danger" href="#">إزالة من المجموعة</a></li>
                            </ul>
                        </div>
                        @endif
                    </li>
                    @endforeach
                </ul>

                @if($conversation->user_is_admin)
                <div class="d-grid gap-2 mt-3">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                        <i class="bi bi-plus-circle me-2"></i>إضافة مشاركين
                    </button>
                </div>
                @endif

                <div class="d-grid gap-2 mt-4">
                    <button class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-left me-2"></i>مغادرة المجموعة
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- مودال الوسائط المشتركة -->
<div class="modal fade" id="mediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">الوسائط المشتركة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="mediaTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="images-tab" data-bs-toggle="tab" data-bs-target="#images-tab-pane" type="button" role="tab" aria-controls="images-tab-pane" aria-selected="true">الصور</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="voice-tab" data-bs-toggle="tab" data-bs-target="#voice-tab-pane" type="button" role="tab" aria-controls="voice-tab-pane" aria-selected="false">الصوتيات</button>
                    </li>
                </ul>
                <div class="tab-content" id="mediaTabContent">
                    <div class="tab-pane fade show active" id="images-tab-pane" role="tabpanel" aria-labelledby="images-tab" tabindex="0">
                        <div class="row g-2">
                            @foreach($mediaMessages['images'] ?? [] as $mediaMessage)
                            <div class="col-4">
                                <a href="{{ asset('storage/' . $mediaMessage->media_url) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $mediaMessage->media_url) }}" alt="صورة" class="img-fluid rounded">
                                </a>
                            </div>
                            @endforeach
                            
                            @if(empty($mediaMessages['images'] ?? []))
                            <div class="col-12 text-center py-4">
                                <p class="text-muted">لا توجد صور مشتركة</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane fade" id="voice-tab-pane" role="tabpanel" aria-labelledby="voice-tab" tabindex="0">
                        <div class="list-group">
                            @foreach($mediaMessages['voice'] ?? [] as $mediaMessage)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <audio controls class="w-100">
                                        <source src="{{ asset('storage/' . $mediaMessage->media_url) }}" type="audio/mpeg">
                                        المتصفح لا يدعم تشغيل الصوت
                                    </audio>
                                    <small class="text-muted ms-2">{{ $mediaMessage->created_at->format('Y-m-d') }}</small>
                                </div>
                            </div>
                            @endforeach
                            
                            @if(empty($mediaMessages['voice'] ?? []))
                            <div class="text-center py-4">
                                <p class="text-muted">لا توجد رسائل صوتية</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- مودال الإبلاغ -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إبلاغ عن محتوى</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('messaging.reports.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $conversation->id ?? '' }}">
                    
                    <div class="mb-3">
                        <label for="report-reason" class="form-label">سبب الإبلاغ</label>
                        <select class="form-select" id="report-reason" name="reason" required>
                            <option value="">اختر سبب الإبلاغ</option>
                            <option value="inappropriate_content">محتوى غير لائق</option>
                            <option value="harassment">تحرش أو مضايقة</option>
                            <option value="spam">رسائل مزعجة (سبام)</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="report-details" class="form-label">تفاصيل إضافية</label>
                        <textarea class="form-control" id="report-details" name="details" rows="3" placeholder="يرجى وصف المشكلة بالتفصيل"></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger">إرسال البلاغ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- مودال الحظر -->
<div class="modal fade" id="blockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">حظر المستخدم</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في حظر هذا المستخدم؟ لن يتمكن من إرسال رسائل لك.</p>
                <form action="{{ route('messaging.block-user', $otherParticipant->id ?? 0) }}" method="POST">
                    @csrf
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger">تأكيد الحظر</button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .conversation-container {
        display: flex;
        flex-direction: column;
    }
    
    .conversation-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    
    .message-wrapper {
        display: flex;
        flex-direction: column;
        margin-bottom: 1rem;
        max-width: 70%;
    }
    
    .message-wrapper.mine {
        align-self: flex-end;
        align-items: flex-end;
    }
    
    .message-wrapper.theirs {
        align-self: flex-start;
        align-items: flex-start;
    }
    
    .message {
        border-radius: 1.3rem;
        padding: 0.8rem 1rem;
        position: relative;
    }
    
    .message-wrapper.mine .message {
        border-bottom-right-radius: 0.3rem;
    }
    
    .message-wrapper.theirs .message {
        border-bottom-left-radius: 0.3rem;
    }
    
    .message-info {
        margin-top: 0.3rem;
        font-size: 0.75rem;
    }
    
    .sender-name {
        margin-top: 0.2rem;
        margin-left: 0.5rem;
    }
    
    .message-actions {
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    
    .message-wrapper:hover .message-actions {
        opacity: 1;
    }
    
    .replied-message, .forwarded-message {
        font-size: 0.9rem;
        max-width: 100%;
    }
    
    .bg-primary-light {
        background-color: rgba(13, 110, 253, 0.2);
    }
    
    .bg-light-gray {
        background-color: rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.getElementById('messages-container');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const imageButton = document.getElementById('image-button');
        const imageInput = document.getElementById('image-input');
        const voiceButton = document.getElementById('voice-button');
        const replyContainer = document.getElementById('reply-container');
        const replySenderName = document.getElementById('reply-sender-name');
        const replyMessageText = document.getElementById('reply-message-text');
        const repliedToId = document.getElementById('replied-to-id');
        const cancelReplyBtn = document.getElementById('cancel-reply-btn');
        
        // تفعيل زر الرد على الرسائل
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', function() {
                const messageId = this.getAttribute('data-message-id');
                const senderName = this.getAttribute('data-sender-name');
                const messageText = this.getAttribute('data-message-text');
                
                repliedToId.value = messageId;
                replySenderName.textContent = senderName;
                replyMessageText.textContent = messageText;
                replyContainer.classList.remove('d-none');
                
                // تركيز على حقل الإدخال
                messageInput.focus();
            });
        });
        
        // إلغاء الرد
        cancelReplyBtn.addEventListener('click', function() {
            repliedToId.value = '';
            replyContainer.classList.add('d-none');
        });
        
        // تفعيل زر توجيه الرسائل
        document.querySelectorAll('.forward-btn').forEach(button => {
            button.addEventListener('click', function() {
                const messageId = this.getAttribute('data-message-id');
                window.location.href = `{{ url('messaging/messages') }}/${messageId}/forward`;
            });
        });
        
        // تفعيل زر تثبيت الرسائل
        document.querySelectorAll('.pin-btn').forEach(button => {
            button.addEventListener('click', function() {
                const messageId = this.getAttribute('data-message-id');
                
                fetch(`{{ url('messaging/messages') }}/${messageId}/pin`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => response.json())
                  .then(data => {
                      if (data.message) {
                          // تحديث واجهة المستخدم
                          this.innerHTML = '<i class="bi bi-pin-angle-fill text-warning"></i>';
                          this.classList.remove('pin-btn');
                          this.classList.add('unpin-btn');
                          
                          // إضافة شارة التثبيت للرسالة
                          const messageElement = document.getElementById(`message-${messageId}`);
                          const messageInfoElement = messageElement.querySelector('.message-info');
                          const pinnedBadge = document.createElement('div');
                          pinnedBadge.className = messageElement.classList.contains('mine') ? 'pinned-badge mt-1 text-white-50' : 'pinned-badge mt-1 text-muted';
                          pinnedBadge.innerHTML = '<small><i class="bi bi-pin-angle-fill me-1"></i> مثبتة</small>';
                          messageInfoElement.parentNode.insertBefore(pinnedBadge, messageInfoElement);
                      }
                  });
            });
        });
        
        // تفعيل زر إلغاء تثبيت الرسائل
        document.querySelectorAll('.unpin-btn').forEach(button => {
            button.addEventListener('click', function() {
                const messageId = this.getAttribute('data-message-id');
                
                fetch(`{{ url('messaging/messages') }}/${messageId}/unpin`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => response.json())
                  .then(data => {
                      if (data.message) {
                          // تحديث واجهة المستخدم
                          this.innerHTML = '<i class="bi bi-pin-angle"></i>';
                          this.classList.remove('unpin-btn');
                          this.classList.add('pin-btn');
                          
                          // إزالة شارة التثبيت من الرسالة
                          const messageElement = document.getElementById(`message-${messageId}`);
                          const pinnedBadge = messageElement.querySelector('.pinned-badge');
                          if (pinnedBadge) {
                              pinnedBadge.remove();
                          }
                      }
                  });
            });
        });
        
        // تفعيل زر حذف الرسائل
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('هل أنت متأكد من حذف هذه الرسالة؟')) {
                    const messageId = this.getAttribute('data-message-id');
                    
                    fetch(`{{ url('messaging/messages') }}/${messageId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).then(response => response.json())
                      .then(data => {
                          if (data.success) {
                              // إزالة الرسالة من واجهة المستخدم
                              const messageElement = document.getElementById(`message-${messageId}`);
                              messageElement.remove();
                          }
                      });
                }
            });
        });
        
        // Scroll to bottom of messages
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Auto-resize textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        
        // Handle image button click
        imageButton.addEventListener('click', function() {
            imageInput.click();
        });
        
        // Handle image selection
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Submit the form with the image
                messageForm.submit();
            }
        });
        
        // Recording functionality for voice button
        let mediaRecorder;
        let audioChunks = [];
        let isRecording = false;
        
        voiceButton.addEventListener('click', function() {
            if (!isRecording) {
                // Start recording
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then(stream => {
                        mediaRecorder = new MediaRecorder(stream);
                        mediaRecorder.start();
                        
                        mediaRecorder.addEventListener('dataavailable', event => {
                            audioChunks.push(event.data);
                        });
                        
                        mediaRecorder.addEventListener('stop', () => {
                            const audioBlob = new Blob(audioChunks, { type: 'audio/mp3' });
                            const formData = new FormData();
                            formData.append('voice', audioBlob);
                            
                            // Send audio via AJAX
                            fetch('{{ route("messaging.conversations.send-voice", $conversation->id ?? 0) }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            }).then(response => {
                                // Handle response
                                audioChunks = [];
                                isRecording = false;
                                voiceButton.classList.remove('btn-danger');
                                voiceButton.innerHTML = '<i class="bi bi-mic"></i>';
                            });
                        });
                        
                        isRecording = true;
                        voiceButton.classList.add('btn-danger');
                        voiceButton.innerHTML = '<i class="bi bi-stop-fill"></i>';
                    });
            } else {
                // Stop recording
                mediaRecorder.stop();
            }
        });
        
        // Submit message form
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (messageInput.value.trim() !== '') {
                const formData = {
                    message: messageInput.value,
                };
                
                // إضافة معرف الرسالة المرد عليها إذا كان موجوداً
                if (repliedToId.value) {
                    formData.replied_to_id = repliedToId.value;
                }
                
                fetch('{{ route("messaging.conversations.send-message", $conversation->id ?? 0) }}', {
                    method: 'POST',
                    body: JSON.stringify(formData),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }).then(response => {
                    // Clear input after sending
                    messageInput.value = '';
                    messageInput.style.height = 'auto';
                    
                    // إعادة تعيين حالة الرد
                    repliedToId.value = '';
                    replyContainer.classList.add('d-none');
                });
            }
        });
    });
</script>
@endpush