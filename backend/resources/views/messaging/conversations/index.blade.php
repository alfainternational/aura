@extends('layouts.dashboard')

@section('title', 'المحادثات')

@section('page-title', 'المحادثات')

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
        <div class="col-md-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">المحادثات ({{ $conversations->total() ?? 0 }})</h5>
                        <div>
                            <a href="{{ route('messaging.conversations.create') }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> محادثة جديدة
                            </a>
                            <a href="{{ route('messaging.contacts.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-person me-1"></i> جهات الاتصال
                            </a>
                        </div>
                    </div>
                </x-slot>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="conversation-search" placeholder="البحث في المحادثات...">
                    <button class="btn btn-outline-secondary" type="button" id="conversation-search-button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                <div class="conversations-list" style="max-height: 600px; overflow-y: auto;">
                    @if(isset($conversations) && $conversations->count() > 0)
                        @foreach($conversations as $conversation)
                        <a href="{{ route('messaging.conversations.show', $conversation->id) }}" 
                           class="conversation-item d-flex justify-content-between align-items-center p-3 mb-2 border-bottom text-decoration-none text-dark rounded {{ request()->route('conversation') == $conversation->id ? 'bg-light' : '' }}">
                            <div class="d-flex align-items-center">
                                @if($conversation->is_group)
                                    <div class="position-relative">
                                        <img src="{{ asset('images/group-avatar.png') }}" 
                                             class="rounded-circle me-2" alt="صورة المجموعة" width="50" height="50">
                                    </div>
                                @else
                                    <div class="position-relative">
                                        <img src="{{ $conversation->otherParticipant->profile_image_url ?? asset('images/default-avatar.png') }}" 
                                             class="rounded-circle me-2" alt="صورة المستخدم" width="50" height="50">
                                        <span class="position-absolute bottom-0 end-0 translate-middle p-1 {{ $conversation->otherParticipant->is_online ?? false ? 'bg-success' : 'bg-secondary' }} border border-light rounded-circle">
                                            <span class="visually-hidden">{{ $conversation->otherParticipant->is_online ?? false ? 'متصل' : 'غير متصل' }}</span>
                                        </span>
                                    </div>
                                @endif
                                <div class="conversation-info">
                                    <h6 class="mb-0">
                                        {{ $conversation->is_group ? $conversation->title : ($conversation->otherParticipant->name ?? 'مستخدم محذوف') }}
                                        @if($conversation->is_muted)
                                            <i class="bi bi-volume-mute-fill text-muted small ms-1" title="تم كتم الإشعارات"></i>
                                        @endif
                                    </h6>
                                    <p class="text-muted small mb-0 text-truncate" style="max-width: 200px;">
                                        @if($conversation->last_message)
                                            @if($conversation->last_message->sender_id == auth()->id())
                                                <span class="text-primary">أنت: </span>
                                            @elseif($conversation->is_group)
                                                <span class="fw-bold">{{ $conversation->last_message->sender->name ?? 'مستخدم' }}: </span>
                                            @endif
                                            
                                            @if($conversation->last_message->type == 'text')
                                                {{ \Illuminate\Support\Str::limit($conversation->last_message->body, 30) }}
                                            @elseif($conversation->last_message->type == 'image')
                                                <i class="bi bi-image me-1"></i> صورة
                                            @elseif($conversation->last_message->type == 'voice')
                                                <i class="bi bi-mic-fill me-1"></i> رسالة صوتية
                                            @endif
                                        @else
                                            <span class="text-muted">لا توجد رسائل</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="conversation-meta text-end">
                                <small class="text-muted d-block">{{ $conversation->last_message ? $conversation->last_message->created_at->format('H:i') : '' }}</small>
                                @if($conversation->unread_count > 0)
                                    <span class="badge rounded-pill bg-primary">{{ $conversation->unread_count }}</span>
                                @endif
                            </div>
                        </a>
                        @endforeach
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $conversations->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/no-conversations.svg') }}" alt="لا توجد محادثات" class="img-fluid mb-3" width="120">
                            <h5>لا توجد محادثات</h5>
                            <p class="text-muted">ابدأ محادثة جديدة للتواصل مع الآخرين</p>
                            <a href="{{ route('messaging.conversations.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> محادثة جديدة
                            </a>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>

        <div class="col-md-8">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">اختر محادثة</h5>
                    </div>
                </x-slot>

                <div class="conversation-placeholder text-center py-5">
                    <img src="{{ asset('images/select-conversation.svg') }}" alt="اختر محادثة" class="img-fluid mb-4" width="200">
                    <h4>اختر محادثة أو ابدأ محادثة جديدة</h4>
                    <p class="text-muted">يمكنك التواصل مع أشخاص آخرين من خلال المحادثات النصية والوسائط</p>
                    <div class="mt-4">
                        <a href="{{ route('messaging.conversations.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> محادثة جديدة
                        </a>
                        <a href="{{ route('messaging.contacts.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-person me-1"></i> إدارة جهات الاتصال
                        </a>
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
        const conversationSearch = document.getElementById('conversation-search');
        const conversationItems = document.querySelectorAll('.conversation-item');
        
        if (conversationSearch) {
            conversationSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                conversationItems.forEach(item => {
                    const title = item.querySelector('.conversation-info h6').textContent.toLowerCase();
                    const lastMessage = item.querySelector('.conversation-info p').textContent.toLowerCase();
                    
                    if (title.includes(searchTerm) || lastMessage.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush