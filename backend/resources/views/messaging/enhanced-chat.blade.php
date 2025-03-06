@extends('layouts.dashboard')

@section('title', 'المحادثات المطورة - AURA')

@section('content')
<div class="enhanced-chat-container">
    <div class="chat-sidebar">
        <div class="user-profile d-flex align-items-center p-3 border-bottom">
            <img src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : asset('images/default-avatar.png') }}" 
                 class="rounded-circle me-2" width="40" height="40" alt="{{ auth()->user()->name }}">
            <div class="user-info">
                <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                <small class="text-success"><i class="bi bi-circle-fill me-1 small"></i>متصل</small>
            </div>
            <div class="ms-auto">
                <button class="btn btn-sm btn-light rounded-circle" id="user-menu-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i>الملف الشخصي</a></li>
                    <li><a class="dropdown-item" href="{{ route('profile.update') }}"><i class="bi bi-gear me-2"></i>الإعدادات</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" id="status-toggle"><i class="bi bi-toggle-on me-2"></i>تغيير الحالة</a></li>
                </ul>
            </div>
        </div>
        
        <div class="search-box p-3">
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0" id="conversation-search" placeholder="بحث...">
            </div>
        </div>
        
        <div class="tabs-container px-3 pt-2">
            <ul class="nav nav-pills nav-fill mb-3" id="chat-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-chats-tab" data-bs-toggle="pill" data-bs-target="#all-chats" type="button" role="tab">
                        <i class="bi bi-chat-dots me-1"></i> الكل
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="personal-tab" data-bs-toggle="pill" data-bs-target="#personal" type="button" role="tab">
                        <i class="bi bi-person me-1"></i> شخصي
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="groups-tab" data-bs-toggle="pill" data-bs-target="#groups" type="button" role="tab">
                        <i class="bi bi-people me-1"></i> مجموعات
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="tab-content flex-grow-1 overflow-auto" id="chat-tabs-content">
            <div class="tab-pane fade show active" id="all-chats" role="tabpanel">
                <div class="conversations-list">
                    @if(isset($conversations) && $conversations->count() > 0)
                        @foreach($conversations as $conversation)
                        <div class="conversation-item p-3 border-bottom {{ isset($activeConversation) && $activeConversation->id == $conversation->id ? 'active' : '' }}" 
                             data-conversation-id="{{ $conversation->id }}">
                            <div class="d-flex">
                                <div class="position-relative">
                                    @if($conversation->is_group)
                                        <div class="group-avatar-container rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 48px; height: 48px;">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                    @else
                                        @php $otherUser = $conversation->otherParticipant; @endphp
                                        <img src="{{ $otherUser->profile_photo_path ? asset('storage/' . $otherUser->profile_photo_path) : asset('images/default-avatar.png') }}" 
                                             class="rounded-circle" width="48" height="48" alt="{{ $otherUser->name }}">
                                        @if($otherUser->is_online)
                                            <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-light rounded-circle">
                                                <span class="visually-hidden">متصل</span>
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                <div class="conversation-info ms-2 flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 conversation-title">
                                            {{ $conversation->is_group ? $conversation->title : $conversation->otherParticipant->name }}
                                        </h6>
                                        <span class="text-muted small">
                                            {{ $conversation->latestMessage ? $conversation->latestMessage->created_at->diffForHumans(null, true) : $conversation->created_at->diffForHumans(null, true) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0 text-truncate text-muted small conversation-latest">
                                            @if($conversation->latestMessage)
                                                @if($conversation->latestMessage->sender_id == auth()->id())
                                                    <span class="text-primary">أنت: </span>
                                                @elseif($conversation->is_group)
                                                    <span class="text-primary">{{ $conversation->latestMessage->sender->name ?? 'مستخدم' }}: </span>
                                                @endif
                                                @if($conversation->latestMessage->type == 'text')
                                                    {{ Str::limit($conversation->latestMessage->body, 30) }}
                                                @elseif($conversation->latestMessage->type == 'image')
                                                    <i class="bi bi-image me-1"></i> صورة
                                                @elseif($conversation->latestMessage->type == 'voice')
                                                    <i class="bi bi-mic me-1"></i> رسالة صوتية
                                                @endif
                                            @else
                                                لا توجد رسائل بعد
                                            @endif
                                        </p>
                                        @if($conversation->unreadCount() > 0)
                                            <span class="badge rounded-pill bg-primary">{{ $conversation->unreadCount() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-chat-square-text text-muted display-4"></i>
                                <p class="text-muted mt-2">لا توجد محادثات</p>
                                <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                                    بدء محادثة جديدة
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="tab-pane fade" id="personal" role="tabpanel">
                <div class="conversations-list">
                    @if(isset($personalConversations) && $personalConversations->count() > 0)
                        @foreach($personalConversations as $conversation)
                        <div class="conversation-item p-3 border-bottom {{ isset($activeConversation) && $activeConversation->id == $conversation->id ? 'active' : '' }}" 
                             data-conversation-id="{{ $conversation->id }}">
                            <!-- نفس محتوى العنصر مثل علامة التبويب "الكل" ولكن فقط للمحادثات الشخصية -->
                            <div class="d-flex">
                                <div class="position-relative">
                                    @php $otherUser = $conversation->otherParticipant; @endphp
                                    <img src="{{ $otherUser->profile_photo_path ? asset('storage/' . $otherUser->profile_photo_path) : asset('images/default-avatar.png') }}" 
                                         class="rounded-circle" width="48" height="48" alt="{{ $otherUser->name }}">
                                    @if($otherUser->is_online)
                                        <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-light rounded-circle">
                                            <span class="visually-hidden">متصل</span>
                                        </span>
                                    @endif
                                </div>
                                <div class="conversation-info ms-2 flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 conversation-title">{{ $otherUser->name }}</h6>
                                        <span class="text-muted small">
                                            {{ $conversation->latestMessage ? $conversation->latestMessage->created_at->diffForHumans(null, true) : $conversation->created_at->diffForHumans(null, true) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0 text-truncate text-muted small conversation-latest">
                                            @if($conversation->latestMessage)
                                                @if($conversation->latestMessage->sender_id == auth()->id())
                                                    <span class="text-primary">أنت: </span>
                                                @endif
                                                @if($conversation->latestMessage->type == 'text')
                                                    {{ Str::limit($conversation->latestMessage->body, 30) }}
                                                @elseif($conversation->latestMessage->type == 'image')
                                                    <i class="bi bi-image me-1"></i> صورة
                                                @elseif($conversation->latestMessage->type == 'voice')
                                                    <i class="bi bi-mic me-1"></i> رسالة صوتية
                                                @endif
                                            @else
                                                لا توجد رسائل بعد
                                            @endif
                                        </p>
                                        @if($conversation->unreadCount() > 0)
                                            <span class="badge rounded-pill bg-primary">{{ $conversation->unreadCount() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-person text-muted display-4"></i>
                                <p class="text-muted mt-2">لا توجد محادثات شخصية</p>
                                <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                                    بدء محادثة جديدة
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="tab-pane fade" id="groups" role="tabpanel">
                <div class="conversations-list">
                    @if(isset($groupConversations) && $groupConversations->count() > 0)
                        @foreach($groupConversations as $conversation)
                        <div class="conversation-item p-3 border-bottom {{ isset($activeConversation) && $activeConversation->id == $conversation->id ? 'active' : '' }}" 
                             data-conversation-id="{{ $conversation->id }}">
                            <!-- نفس محتوى العنصر مثل علامة التبويب "الكل" ولكن فقط للمحادثات الجماعية -->
                            <div class="d-flex">
                                <div class="position-relative">
                                    <div class="group-avatar-container rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 48px; height: 48px;">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                </div>
                                <div class="conversation-info ms-2 flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 conversation-title">{{ $conversation->title }}</h6>
                                        <span class="text-muted small">
                                            {{ $conversation->latestMessage ? $conversation->latestMessage->created_at->diffForHumans(null, true) : $conversation->created_at->diffForHumans(null, true) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="mb-0 text-truncate text-muted small conversation-latest">
                                            @if($conversation->latestMessage)
                                                <span class="text-primary">{{ $conversation->latestMessage->sender_id == auth()->id() ? 'أنت' : ($conversation->latestMessage->sender->name ?? 'مستخدم') }}: </span>
                                                @if($conversation->latestMessage->type == 'text')
                                                    {{ Str::limit($conversation->latestMessage->body, 30) }}
                                                @elseif($conversation->latestMessage->type == 'image')
                                                    <i class="bi bi-image me-1"></i> صورة
                                                @elseif($conversation->latestMessage->type == 'voice')
                                                    <i class="bi bi-mic me-1"></i> رسالة صوتية
                                                @endif
                                            @else
                                                لا توجد رسائل بعد
                                            @endif
                                        </p>
                                        @if($conversation->unreadCount() > 0)
                                            <span class="badge rounded-pill bg-primary">{{ $conversation->unreadCount() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-people text-muted display-4"></i>
                                <p class="text-muted mt-2">لا توجد محادثات جماعية</p>
                                <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                                    إنشاء مجموعة جديدة
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="sidebar-footer p-3 border-top d-flex justify-content-center">
            <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                <i class="bi bi-plus me-1"></i> محادثة جديدة
            </button>
        </div>
    </div>
    
    <div class="chat-main">
        @if(isset($activeConversation))
            <div class="chat-header border-bottom p-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('messaging.conversations.index') }}" class="d-block d-md-none me-3 text-muted">
                        <i class="bi bi-arrow-right fs-5"></i>
                    </a>
                    <div class="position-relative">
                        @if($activeConversation->is_group)
                            <div class="group-avatar-container rounded-circle d-flex align-items-center justify-content-center bg-primary text-white" style="width: 48px; height: 48px;">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        @else
                            @php $otherUser = $activeConversation->otherParticipant; @endphp
                            <img src="{{ $otherUser->profile_photo_path ? asset('storage/' . $otherUser->profile_photo_path) : asset('images/default-avatar.png') }}" 
                                 class="rounded-circle" width="48" height="48" alt="{{ $otherUser->name }}">
                            @if($otherUser->is_online)
                                <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-light rounded-circle">
                                    <span class="visually-hidden">متصل</span>
                                </span>
                            @endif
                        @endif
                    </div>
                    <div class="ms-2 flex-grow-1">
                        <h6 class="mb-0">{{ $activeConversation->is_group ? $activeConversation->title : $activeConversation->otherParticipant->name }}</h6>
                        <small class="text-muted">
                            @if($activeConversation->is_group)
                                {{ $activeConversation->participants_count }} مشاركين
                            @else
                                {{ $activeConversation->otherParticipant->is_online ? 'متصل الآن' : 'آخر ظهور ' . $activeConversation->otherParticipant->last_active_at->diffForHumans() }}
                            @endif
                        </small>
                    </div>
                    <div class="chat-actions">
                        <button class="btn btn-light rounded-circle me-2" id="voice-call-btn" 
                                data-recipient="{{ $activeConversation->is_group ? $activeConversation->id : $activeConversation->otherParticipant->id }}"
                                data-type="{{ $activeConversation->is_group ? 'group' : 'individual' }}">
                            <i class="bi bi-telephone"></i>
                        </button>
                        <button class="btn btn-light rounded-circle me-2" id="search-messages-btn">
                            <i class="bi bi-search"></i>
                        </button>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-light rounded-circle" id="conversation-options" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if($activeConversation->is_group)
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#groupInfoModal">
                                        <i class="bi bi-info-circle me-2"></i>معلومات المجموعة
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                                        <i class="bi bi-person-plus me-2"></i>إضافة مشاركين
                                    </a></li>
                                @else
                                    <li><a class="dropdown-item" href="{{ route('profile.show', $activeConversation->otherParticipant->id) }}">
                                        <i class="bi bi-person me-2"></i>عرض الملف الشخصي
                                    </a></li>
                                @endif
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#mediaModal">
                                    <i class="bi bi-images me-2"></i>الوسائط المشتركة
                                </a></li>
                                <li><a class="dropdown-item" href="#">
                                    <i class="bi bi-pin-angle me-2"></i>الرسائل المثبتة
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item mute-conversation" href="#">
                                    <i class="bi bi-bell-slash me-2"></i>كتم الإشعارات
                                </a></li>
                                <li><a class="dropdown-item text-danger" href="#">
                                    <i class="bi bi-trash me-2"></i>حذف المحادثة
                                </a></li>
                                @if(!$activeConversation->is_group)
                                    <li><a class="dropdown-item text-danger" href="#">
                                        <i class="bi bi-slash-circle me-2"></i>حظر
                                    </a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="chat-body" id="chat-messages-container">
                <div class="messages-wrapper">
                    <div class="date-divider text-center my-3">
                        <span class="date-label px-3 bg-light rounded text-muted small">{{ now()->format('Y-m-d') }}</span>
                    </div>
                    
                    @foreach($messages ?? [] as $message)
                    <div class="message-item {{ $message->sender_id == auth()->id() ? 'outgoing' : 'incoming' }}" id="message-{{ $message->id }}">
                        @if($message->sender_id != auth()->id())
                        <div class="message-avatar">
                            <img src="{{ $message->sender->profile_photo_path ? asset('storage/' . $message->sender->profile_photo_path) : asset('images/default-avatar.png') }}" 
                                class="rounded-circle" width="36" height="36" alt="{{ $message->sender->name }}">
                        </div>
                        @endif
                        
                        <div class="message-content-wrapper">
                            @if($activeConversation->is_group && $message->sender_id != auth()->id())
                            <div class="sender-name small mb-1">{{ $message->sender->name }}</div>
                            @endif
                            
                            <div class="message-content {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                                @if($message->replied_to_id)
                                <div class="replied-message {{ $message->sender_id == auth()->id() ? 'bg-primary-light text-white-50' : 'bg-light-gray text-muted' }} p-2 mb-2 rounded border-start border-3 border-primary">
                                    <small class="d-block mb-1">
                                        <i class="bi bi-reply-fill me-1"></i> رد على {{ $message->repliedTo->sender->name ?? 'مستخدم محذوف' }}
                                    </small>
                                    <div class="text-truncate">{{ $message->repliedTo->body ?? '' }}</div>
                                </div>
                                @endif
                                
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
                                    <img src="{{ asset('storage/' . $message->media_url) }}" alt="صورة" class="img-fluid rounded message-image" style="max-width: 300px;">
                                @elseif($message->type == 'voice')
                                    <div class="voice-message">
                                        <div class="d-flex align-items-center">
                                            <button class="btn btn-sm btn-light play-voice-btn me-2" data-audio-url="{{ asset('storage/' . $message->media_url) }}">
                                                <i class="bi bi-play-fill"></i>
                                            </button>
                                            <div class="voice-waveform flex-grow-1" style="height: 30px; background: rgba(0,0,0,0.1);"></div>
                                            <span class="ms-2 voice-duration small">{{ $message->duration ?? '0:00' }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="message-meta {{ $message->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }} d-flex align-items-center">
                                    <small class="me-auto">{{ $message->created_at->format('H:i') }}</small>
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
                            
                            <div class="message-actions">
                                <div class="btn-group">
                                    <button class="btn btn-sm reply-btn" data-message-id="{{ $message->id }}" data-sender-name="{{ $message->sender->name ?? 'مستخدم محذوف' }}" data-message-text="{{ $message->body }}">
                                        <i class="bi bi-reply"></i>
                                    </button>
                                    <button class="btn btn-sm forward-btn" data-message-id="{{ $message->id }}">
                                        <i class="bi bi-forward"></i>
                                    </button>
                                    <button class="btn btn-sm more-actions-btn" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item copy-message" href="#" data-message-text="{{ $message->body }}">
                                            <i class="bi bi-clipboard me-2"></i>نسخ
                                        </a></li>
                                        @if(!$message->is_pinned)
                                        <li><a class="dropdown-item pin-message" href="#" data-message-id="{{ $message->id }}">
                                            <i class="bi bi-pin-angle me-2"></i>تثبيت
                                        </a></li>
                                        @else
                                        <li><a class="dropdown-item unpin-message" href="#" data-message-id="{{ $message->id }}">
                                            <i class="bi bi-pin-angle-fill me-2"></i>إلغاء التثبيت
                                        </a></li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        @if($message->sender_id == auth()->id())
                                        <li><a class="dropdown-item delete-message text-danger" href="#" data-message-id="{{ $message->id }}">
                                            <i class="bi bi-trash me-2"></i>حذف
                                        </a></li>
                                        @else
                                        <li><a class="dropdown-item report-message text-danger" href="#" data-message-id="{{ $message->id }}">
                                            <i class="bi bi-flag me-2"></i>إبلاغ
                                        </a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <div class="chat-footer border-top p-3">
                <div id="reply-container" class="bg-light p-2 mb-2 rounded border-start border-3 border-primary d-none">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="d-block text-muted mb-1">
                                <i class="bi bi-reply-fill me-1"></i> الرد على <span id="reply-sender-name"></span>
                            </small>
                            <div id="reply-message-text" class="text-truncate small">{{ $message->repliedTo->body ?? '' }}</div>
                                                        </div>
                                                        <button type="button" class="btn btn-sm text-muted" id="cancel-reply-btn">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </div>
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
                                                    <div class="flex-grow-1 message-input-container">
                                                        <textarea class="form-control" id="message-input" name="message" rows="1" placeholder="اكتب رسالة..." style="resize: none;"></textarea>
                                                        <div class="emoji-button">
                                                            <button type="button" class="btn text-muted" id="emoji-button">
                                                                <i class="bi bi-emoji-smile"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="ms-2">
                                                        <button type="submit" class="btn btn-primary rounded-circle" id="send-button">
                                                            <i class="bi bi-send"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div id="voice-recording-container" class="mt-2 p-2 bg-light rounded d-none">
                                                    <div class="d-flex align-items-center">
                                                        <div class="recording-indicator me-2 text-danger">
                                                            <i class="bi bi-record-circle"></i>
                                                            <span>جاري التسجيل...</span>
                                                        </div>
                                                        <div class="recording-time text-muted me-auto">00:00</div>
                                                        <button type="button" class="btn btn-sm btn-outline-danger me-2" id="cancel-recording-btn">
                                                            <i class="bi bi-x-lg me-1"></i> إلغاء
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-success" id="finish-recording-btn">
                                                            <i class="bi bi-check-lg me-1"></i> إنهاء
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @else
                                        <div class="no-conversation-selected d-flex flex-column align-items-center justify-content-center h-100">
                                            <div class="text-center">
                                                <i class="bi bi-chat-text text-muted display-1 mb-3"></i>
                                                <h4>مرحباً بك في المحادثات المطورة</h4>
                                                <p class="text-muted">اختر محادثة من القائمة الجانبية أو قم بإنشاء محادثة جديدة</p>
                                                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                                                    <i class="bi bi-plus me-1"></i> بدء محادثة جديدة
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- مودال إنشاء محادثة جديدة -->
                            <div class="modal fade" id="newConversationModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">محادثة جديدة</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="nav nav-tabs mb-3" id="conversationTabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" type="button" role="tab" aria-controls="individual" aria-selected="true">محادثة فردية</button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="group-tab" data-bs-toggle="tab" data-bs-target="#group" type="button" role="tab" aria-controls="group" aria-selected="false">محادثة جماعية</button>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="conversationTabsContent">
                                                <div class="tab-pane fade show active" id="individual" role="tabpanel" aria-labelledby="individual-tab">
                                                    <form action="{{ route('messaging.conversations.create-individual') }}" method="POST" id="individualConversationForm">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="user_id" class="form-label">اختر مستخدم</label>
                                                            <select class="form-select" id="user_id" name="participants[]" required>
                                                                <option value="">اختر مستخدم...</option>
                                                                <!-- سيتم تحميل المستخدمين عبر Ajax -->
                                                            </select>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="tab-pane fade" id="group" role="tabpanel" aria-labelledby="group-tab">
                                                    <form action="{{ route('messaging.conversations.create-group') }}" method="POST" id="groupConversationForm">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label for="group_title" class="form-label">اسم المجموعة</label>
                                                            <input type="text" class="form-control" id="group_title" name="title" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="group_participants" class="form-label">المشاركين</label>
                                                            <select class="form-select" id="group_participants" name="participants[]" multiple required>
                                                                <!-- سيتم تحميل المستخدمين عبر Ajax -->
                                                            </select>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="button" class="btn btn-primary" id="submitConversationBtn">إنشاء</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- مودال توجيه الرسائل -->
                            <div class="modal fade" id="forwardMessageModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">توجيه الرسالة</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">اختر المحادثات</label>
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                                                    <input type="text" class="form-control" id="forward-search" placeholder="بحث...">
                                                </div>
                                                <div class="conversation-list-container p-2 border rounded" style="max-height: 300px; overflow-y: auto;">
                                                    <div class="form-check mb-2" id="forward-conversations-list">
                                                        <!-- سيتم تحميل المحادثات هنا عبر JavaScript -->
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="forward-message-text" class="form-label">إضافة تعليق (اختياري)</label>
                                                <textarea class="form-control" id="forward-message-text" rows="2"></textarea>
                                            </div>
                                            <input type="hidden" id="forward-message-id" value="">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="button" class="btn btn-primary" id="submit-forward-btn">توجيه</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- مودال بحث الرسائل -->
                            <div class="modal fade" id="searchMessagesModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">بحث في الرسائل</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                                <input type="text" class="form-control" id="message-search-input" placeholder="بحث...">
                                                <button class="btn btn-primary" type="button" id="search-messages-submit">بحث</button>
                                            </div>
                                            <div id="message-search-results" class="mt-3">
                                                <!-- نتائج البحث -->
                                            </div>
                                        </div>
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
                                            @if(isset($activeConversation) && $activeConversation->is_group)
                                            <div class="text-center mb-3">
                                                <div class="group-avatar-container rounded-circle d-flex align-items-center justify-content-center bg-primary text-white mx-auto mb-2" style="width: 80px; height: 80px;">
                                                    <i class="bi bi-people-fill fs-2"></i>
                                                </div>
                                                <h5>{{ $activeConversation->title }}</h5>
                                                <p class="text-muted small">تم الإنشاء {{ $activeConversation->created_at->format('Y-m-d') }}</p>
                                            </div>
                            
                                            <h6 class="mb-3">المشاركون ({{ $activeConversation->participants_count }})</h6>
                                            <ul class="list-group">
                                                @foreach($activeConversation->participants ?? [] as $participant)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $participant->profile_photo_path ? asset('storage/' . $participant->profile_photo_path) : asset('images/default-avatar.png') }}" 
                                                              class="rounded-circle me-2" alt="صورة المستخدم" width="40" height="40">
                                                        <div>
                                                            <h6 class="mb-0">{{ $participant->name }}</h6>
                                                            <small class="text-muted">{{ $participant->is_admin ? 'مسؤول' : 'عضو' }}</small>
                                                        </div>
                                                    </div>
                                                    @if($activeConversation->user_is_admin)
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
                            
                                            <div class="d-grid gap-2 mt-3">
                                                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                                                    <i class="bi bi-plus-circle me-2"></i>إضافة مشاركين
                                                </button>
                                            </div>
                            
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
                            
                            @push('styles')
                            <link rel="stylesheet" href="{{ asset('css/enhanced-messaging.css') }}">
                            @endpush
                            
                            @push('scripts')
                            <script src="{{ asset('js/enhanced-messaging.js') }}"></script>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // تهيئة المحادثة
                                    initEnhancedChat();
                                    
                                    // معالجة النقر على عناصر المحادثة
                                    document.querySelectorAll('.conversation-item').forEach(item => {
                                        item.addEventListener('click', function() {
                                            const conversationId = this.getAttribute('data-conversation-id');
                                            window.location.href = "{{ url('messaging/enhanced-chat') }}/" + conversationId;
                                        });
                                    });
                                    
                                    // البحث في المحادثات
                                    const searchInput = document.getElementById('conversation-search');
                                    if (searchInput) {
                                        searchInput.addEventListener('input', function() {
                                            const searchTerm = this.value.toLowerCase();
                                            document.querySelectorAll('.conversation-item').forEach(item => {
                                                const title = item.querySelector('.conversation-title').textContent.toLowerCase();
                                                const latest = item.querySelector('.conversation-latest').textContent.toLowerCase();
                                                if (title.includes(searchTerm) || latest.includes(searchTerm)) {
                                                    item.style.display = 'block';
                                                } else {
                                                    item.style.display = 'none';
                                                }
                                            });
                                        });
                                    }
                                    
                                    // إرسال الرسائل
                                    const messageForm = document.getElementById('message-form');
                                    if (messageForm) {
                                        messageForm.addEventListener('submit', function(e) {
                                            e.preventDefault();
                                            sendMessage();
                                        });
                                    }
                                    
                                    // الرد على الرسائل
                                    document.querySelectorAll('.reply-btn').forEach(btn => {
                                        btn.addEventListener('click', function() {
                                            const messageId = this.getAttribute('data-message-id');
                                            const senderName = this.getAttribute('data-sender-name');
                                            const messageText = this.getAttribute('data-message-text');
                                            
                                            document.getElementById('reply-container').classList.remove('d-none');
                                            document.getElementById('replied-to-id').value = messageId;
                                            document.getElementById('reply-sender-name').textContent = senderName;
                                            document.getElementById('reply-message-text').textContent = messageText;
                                            
                                            document.getElementById('message-input').focus();
                                        });
                                    });
                                    
                                    // إلغاء الرد
                                    const cancelReplyBtn = document.getElementById('cancel-reply-btn');
                                    if (cancelReplyBtn) {
                                        cancelReplyBtn.addEventListener('click', function() {
                                            document.getElementById('reply-container').classList.add('d-none');
                                            document.getElementById('replied-to-id').value = '';
                                        });
                                    }
                                    
                                    // إضافة الصور
                                    const imageButton = document.getElementById('image-button');
                                    const imageInput = document.getElementById('image-input');
                                    if (imageButton && imageInput) {
                                        imageButton.addEventListener('click', function() {
                                            imageInput.click();
                                        });
                                        
                                        imageInput.addEventListener('change', function() {
                                            if (this.files.length > 0) {
                                                sendImageMessage(this.files[0]);
                                            }
                                        });
                                    }
                                    
                                    // تسجيل الرسائل الصوتية
                                    const voiceButton = document.getElementById('voice-button');
                                    if (voiceButton) {
                                        voiceButton.addEventListener('click', function() {
                                            toggleVoiceRecording();
                                        });
                                    }
                                    
                                    // توجيه الرسائل
                                    document.querySelectorAll('.forward-btn').forEach(btn => {
                                        btn.addEventListener('click', function() {
                                            const messageId = this.getAttribute('data-message-id');
                                            document.getElementById('forward-message-id').value = messageId;
                                            loadConversationsForForward();
                                            
                                            const forwardMessageModal = new bootstrap.Modal(document.getElementById('forwardMessageModal'));
                                            forwardMessageModal.show();
                                        });
                                    });
                                    
                                    // تمرير المحادثة لأسفل
                                    scrollToBottom();
                                });
                                
                                function initEnhancedChat() {
                                    // تعطيل الروابط وزيادة سطور textarea عند الكتابة
                                    const messageInput = document.getElementById('message-input');
                                    if (messageInput) {
                                        messageInput.addEventListener('input', function() {
                                            this.style.height = 'auto';
                                            this.style.height = (this.scrollHeight) + 'px';
                                            if (this.scrollHeight > 150) {
                                                this.style.overflowY = 'auto';
                                            } else {
                                                this.style.overflowY = 'hidden';
                                            }
                                        });
                                    }
                                }
                                
                                function sendMessage() {
                                    const messageInput = document.getElementById('message-input');
                                    const repliedToId = document.getElementById('replied-to-id').value;
                                    
                                    if (!messageInput.value.trim()) return;
                                    
                                    const formData = new FormData();
                                    formData.append('message', messageInput.value);
                                    formData.append('type', 'text');
                                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                                    
                                    if (repliedToId) {
                                        formData.append('replied_to_id', repliedToId);
                                    }
                                    
                                    fetch("{{ isset($activeConversation) ? route('messaging.messages.store', $activeConversation->id) : '#' }}", {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            messageInput.value = '';
                                            messageInput.style.height = 'auto';
                                            
                                            // إضافة الرسالة للمحادثة
                                            appendMessage(data.message);
                                            
                                            // إخفاء منطقة الرد
                                            document.getElementById('reply-container').classList.add('d-none');
                                            document.getElementById('replied-to-id').value = '';
                                            
                                            // تمرير المحادثة لأسفل
                                            scrollToBottom();
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));
                                }
                                
                                function appendMessage(message) {
                                    const messagesWrapper = document.querySelector('.messages-wrapper');
                                    if (!messagesWrapper) return;
                                    
                                    const messageItem = document.createElement('div');
                                    messageItem.className = `message-item ${message.sender_id == {{ auth()->id() }} ? 'outgoing' : 'incoming'}`;
                                    messageItem.id = `message-${message.id}`;
                                    
                                    // بناء محتوى الرسالة حسب النوع
                                    // Note: This is a simplified version, you would need to expand this based on your message structure
                                    let messageHTML = '';
                                    
                                    if (message.sender_id != {{ auth()->id() }}) {
                                        messageHTML += `
                                            <div class="message-avatar">
                                                <img src="${message.sender_photo || '{{ asset("images/default-avatar.png") }}'}" 
                                                    class="rounded-circle" width="36" height="36" alt="${message.sender_name}">
                                            </div>
                                        `;
                                    }
                                    
                                    messageHTML += `
                                        <div class="message-content-wrapper">
                                            ${message.conversation_is_group && message.sender_id != {{ auth()->id() }} ? 
                                                `<div class="sender-name small mb-1">${message.sender_name}</div>` : ''}
                                            
                                            <div class="message-content ${message.sender_id == {{ auth()->id() }} ? 'bg-primary text-white' : 'bg-light'}">
                                                ${message.type == 'text' ? message.body : ''}
                                                
                                                <div class="message-meta ${message.sender_id == {{ auth()->id() }} ? 'text-white-50' : 'text-muted'} d-flex align-items-center">
                                                    <small class="me-auto">${new Date().toLocaleTimeString('ar-SA', {hour: '2-digit', minute:'2-digit'})}</small>
                                                    ${message.sender_id == {{ auth()->id() }} ? '<i class="bi bi-check ms-1"></i>' : ''}
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    
                                    messageItem.innerHTML = messageHTML;
                                    messagesWrapper.appendChild(messageItem);
                                }
                                
                                function scrollToBottom() {
                                    const chatBody = document.getElementById('chat-messages-container');
                                    if (chatBody) {
                                        chatBody.scrollTop = chatBody.scrollHeight;
                                    }
                                }
                            </script>
                            @endpush
                            @endsection