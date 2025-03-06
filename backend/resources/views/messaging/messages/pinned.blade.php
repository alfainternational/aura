@extends('layouts.dashboard')

@section('title', 'الرسائل المثبتة')

@section('page-title', 'الرسائل المثبتة')

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
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{ route('messaging.conversations.show', $conversation->id) }}" class="me-3">
                                <i class="bi bi-arrow-right fs-4"></i>
                            </a>
                            <h5 class="mb-0">الرسائل المثبتة في: {{ $conversation->is_group ? $conversation->title : ($otherParticipant->name ?? 'محادثة') }}</h5>
                        </div>
                    </div>
                </x-slot>

                <div id="pinned-messages">
                    @if(isset($pinnedMessages) && count($pinnedMessages) > 0)
                        <div class="messages-list">
                            @foreach($pinnedMessages as $message)
                            <div class="message-item p-3 mb-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $message->sender->profile_image_url ?? asset('images/default-avatar.png') }}" 
                                             class="rounded-circle me-2" alt="صورة المستخدم" width="30" height="30">
                                        <div>
                                            <span class="fw-bold">{{ $message->sender->name ?? 'مستخدم محذوف' }}</span>
                                            <small class="text-muted ms-2">{{ $message->created_at->format('Y-m-d H:i') }}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <span class="badge bg-warning me-2" title="تم التثبيت بواسطة {{ $message->pinnedBy->name ?? 'مستخدم محذوف' }}">
                                            <i class="bi bi-pin-angle-fill me-1"></i> مثبتة
                                        </span>
                                        <a href="{{ route('messaging.conversations.show', ['conversation' => $conversation->id, 'message' => $message->id]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i> عرض في المحادثة
                                        </a>
                                        @if($conversation->is_group && auth()->user()->isAdminInConversation($conversation) || !$conversation->is_group)
                                        <form action="{{ route('messaging.messages.unpin', $message->id) }}" method="POST" class="ms-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('هل أنت متأكد من إلغاء تثبيت هذه الرسالة؟')">
                                                <i class="bi bi-pin-angle me-1"></i> إلغاء التثبيت
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>

                                @if($message->replied_to_id)
                                <div class="replied-message bg-light p-2 mb-2 rounded border-start border-3 border-primary">
                                    <small class="d-block text-muted mb-1">
                                        <i class="bi bi-reply-fill me-1"></i> رد على {{ $message->repliedTo->sender->name ?? 'مستخدم محذوف' }}
                                    </small>
                                    <div class="text-truncate">{{ $message->repliedTo->message ?? '' }}</div>
                                </div>
                                @endif

                                @if($message->forwarded_from_id)
                                <div class="forwarded-message bg-light p-2 mb-2 rounded border-start border-3 border-info">
                                    <small class="d-block text-muted mb-1">
                                        <i class="bi bi-forward-fill me-1"></i> تم توجيهها من {{ $message->forwardedFrom->sender->name ?? 'مستخدم محذوف' }}
                                    </small>
                                </div>
                                @endif

                                <div class="message-content">
                                    @if($message->message)
                                        <p class="mb-2">{{ $message->message }}</p>
                                    @endif

                                    @if($message->hasAttachment())
                                        <div class="attachment mt-2">
                                            @if($message->attachment_type == 'image')
                                                <img src="{{ asset('storage/' . $message->attachment_path) }}" 
                                                     class="img-fluid rounded" alt="صورة مرفقة" style="max-height: 200px;">
                                            @else
                                                <div class="d-flex align-items-center p-2 border rounded">
                                                    <i class="bi bi-file-earmark fs-4 me-2"></i>
                                                    <div>
                                                        <span class="d-block">{{ $message->attachment_name }}</span>
                                                        <small class="text-muted">{{ round($message->attachment_size / 1024, 2) }} KB</small>
                                                    </div>
                                                    <a href="{{ asset('storage/' . $message->attachment_path) }}" 
                                                       class="btn btn-sm btn-outline-primary ms-auto" download>
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mt-2 text-muted">
                                    <small>
                                        <i class="bi bi-pin-angle-fill me-1"></i> 
                                        تم التثبيت بواسطة {{ $message->pinnedBy->name ?? 'مستخدم محذوف' }} 
                                        {{ $message->pinned_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/pin.svg') }}" alt="لا توجد رسائل مثبتة" class="img-fluid mb-3" width="120">
                            <h5>لا توجد رسائل مثبتة</h5>
                            <p class="text-muted">لم يتم تثبيت أي رسائل في هذه المحادثة بعد</p>
                            <a href="{{ route('messaging.conversations.show', $conversation->id) }}" class="btn btn-primary mt-2">
                                <i class="bi bi-chat-dots me-1"></i> العودة إلى المحادثة
                            </a>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
