@extends('layouts.dashboard')

@section('title', 'توجيه رسالة')

@section('page-title', 'توجيه رسالة')

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
                            <a href="{{ route('messaging.conversations.show', $message->conversation_id) }}" class="me-3">
                                <i class="bi bi-arrow-right fs-4"></i>
                            </a>
                            <h5 class="mb-0">توجيه رسالة</h5>
                        </div>
                    </div>
                </x-slot>

                <div class="row">
                    <div class="col-md-6">
                        <div class="original-message p-3 mb-4 border rounded">
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ $message->sender->profile_image_url ?? asset('images/default-avatar.png') }}" 
                                     class="rounded-circle me-2" alt="صورة المستخدم" width="30" height="30">
                                <div>
                                    <span class="fw-bold">{{ $message->sender->name ?? 'مستخدم محذوف' }}</span>
                                    <small class="text-muted ms-2">{{ $message->created_at->format('Y-m-d H:i') }}</small>
                                </div>
                            </div>

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
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="mb-3">اختر محادثة للتوجيه إليها:</h6>
                        
                        <form id="forward-form" action="{{ route('messaging.messages.forward', $message->id) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <input type="text" class="form-control" id="conversation-search" 
                                       placeholder="البحث في المحادثات..." autocomplete="off">
                            </div>
                            
                            <div class="conversations-list mb-4" style="max-height: 300px; overflow-y: auto;">
                                @if(isset($conversations) && $conversations->count() > 0)
                                    @foreach($conversations as $conversation)
                                    <div class="form-check conversation-item p-3 mb-2 border rounded">
                                        <input class="form-check-input" type="radio" name="conversation_id" 
                                               id="conversation-{{ $conversation->id }}" value="{{ $conversation->id }}"
                                               {{ $conversation->id == $message->conversation_id ? 'disabled' : '' }}>
                                        <label class="form-check-label d-flex align-items-center" for="conversation-{{ $conversation->id }}">
                                            @if($conversation->is_group)
                                                <img src="{{ asset('images/group-avatar.png') }}" 
                                                     class="rounded-circle me-2" alt="صورة المجموعة" width="40" height="40">
                                                <div>
                                                    <span class="d-block">{{ $conversation->title }}</span>
                                                    <small class="text-muted">{{ $conversation->participants_count }} مشاركين</small>
                                                </div>
                                            @else
                                                <img src="{{ $conversation->otherParticipant->profile_image_url ?? asset('images/default-avatar.png') }}" 
                                                     class="rounded-circle me-2" alt="صورة المستخدم" width="40" height="40">
                                                <div>
                                                    <span class="d-block">{{ $conversation->otherParticipant->name ?? 'مستخدم محذوف' }}</span>
                                                    <small class="text-muted">
                                                        {{ $conversation->otherParticipant->is_online ?? false ? 'متصل الآن' : 'آخر ظهور ' . ($conversation->otherParticipant->last_active_at ? $conversation->otherParticipant->last_active_at->diffForHumans() : 'غير معروف') }}
                                                    </small>
                                                </div>
                                            @endif
                                            
                                            @if($conversation->id == $message->conversation_id)
                                                <span class="badge bg-secondary ms-auto">المحادثة الحالية</span>
                                            @endif
                                        </label>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-muted">لا توجد محادثات متاحة للتوجيه</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="forward-button" disabled>
                                    <i class="bi bi-forward me-1"></i> توجيه الرسالة
                                </button>
                                <a href="{{ route('messaging.conversations.show', $message->conversation_id) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i> إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const forwardForm = document.getElementById('forward-form');
        const forwardButton = document.getElementById('forward-button');
        const conversationSearch = document.getElementById('conversation-search');
        const conversationItems = document.querySelectorAll('.conversation-item');
        const conversationRadios = document.querySelectorAll('input[name="conversation_id"]');
        
        // تفعيل زر التوجيه عند اختيار محادثة
        conversationRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                forwardButton.disabled = !this.checked || this.disabled;
            });
        });
        
        // البحث في المحادثات
        conversationSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            conversationItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // تأكيد التوجيه
        forwardForm.addEventListener('submit', function(e) {
            const selectedConversation = document.querySelector('input[name="conversation_id"]:checked');
            
            if (!selectedConversation || selectedConversation.disabled) {
                e.preventDefault();
                alert('الرجاء اختيار محادثة صالحة للتوجيه');
            }
        });
    });
</script>
@endpush
