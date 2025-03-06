@extends('layouts.dashboard')

@section('title', 'المحادثات - لوحة تحكم العميل')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-2">المحادثات</h4>
                    <p class="text-muted mb-0">تواصل مع الأشخاص والمجموعات</p>
                </div>
                <div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                        <i class="bi bi-plus-lg me-2"></i> محادثة جديدة
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control border-0 bg-light" id="searchConversations" placeholder="بحث في المحادثات...">
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="nav nav-pills flex-column conversation-list-container">
                        @if(count($conversations) > 0)
                            @foreach($conversations as $conversation)
                                <a href="{{ route('dashboard.customer.messaging.conversation', $conversation->id) }}" class="nav-link conversation-item d-flex align-items-center py-3 px-3 border-bottom position-relative {{ request()->route('id') == $conversation->id ? 'active' : '' }}">
                                    @if($conversation->is_group)
                                        <div class="conversation-avatar bg-primary bg-opacity-10 text-primary">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                    @else
                                        <div class="conversation-avatar">
                                            @if($conversation->otherParticipant->profile_photo_path)
                                                <img src="{{ asset('storage/' . $conversation->otherParticipant->profile_photo_path) }}" alt="{{ $conversation->otherParticipant->name }}" class="rounded-circle">
                                            @else
                                                <div class="bg-secondary bg-opacity-10 rounded-circle text-secondary d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-person-fill"></i>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="ms-3 flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-1 text-truncate">
                                                @if($conversation->is_group)
                                                    {{ $conversation->name }}
                                                @else
                                                    {{ $conversation->otherParticipant->name }}
                                                @endif
                                            </h6>
                                            <small class="text-muted">{{ $conversation->lastMessage ? $conversation->lastMessage->created_at->diffForHumans(null, false, false, 1) : '' }}</small>
                                        </div>
                                        <p class="mb-0 text-truncate text-muted small">
                                            @if($conversation->lastMessage)
                                                @if($conversation->lastMessage->user_id == auth()->id())
                                                    <span class="text-muted">أنت: </span>
                                                @elseif($conversation->is_group)
                                                    <span class="text-muted">{{ $conversation->lastMessage->user->name }}: </span>
                                                @endif

                                                @if($conversation->lastMessage->type == 'image')
                                                    <i class="bi bi-image me-1"></i> صورة
                                                @elseif($conversation->lastMessage->type == 'file')
                                                    <i class="bi bi-file-earmark me-1"></i> ملف
                                                @elseif($conversation->lastMessage->type == 'audio')
                                                    <i class="bi bi-mic me-1"></i> تسجيل صوتي
                                                @else
                                                    {{ $conversation->lastMessage->content }}
                                                @endif
                                            @else
                                                <span class="text-muted">لا توجد رسائل</span>
                                            @endif
                                        </p>
                                    </div>

                                    @if($conversation->unreadCount > 0)
                                        <div class="badge bg-primary rounded-pill ms-2">{{ $conversation->unreadCount }}</div>
                                    @endif

                                    @if($conversation->is_muted)
                                        <i class="bi bi-bell-slash text-muted position-absolute top-0 end-0 mt-2 me-2 small"></i>
                                    @endif
                                </a>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-chat-square-text fs-1 d-block mb-3 text-muted"></i>
                                <h5 class="text-muted mb-3">لا توجد محادثات</h5>
                                <p class="text-muted">ابدأ محادثة جديدة للتواصل مع الأشخاص والمجموعات</p>
                                <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                                    <i class="bi bi-plus-lg me-2"></i> بدء محادثة
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent py-3">
                    <div class="btn-group w-100" role="group">
                        <button type="button" class="btn btn-outline-secondary active" id="allConversationsBtn">الكل</button>
                        <button type="button" class="btn btn-outline-secondary" id="individualConversationsBtn">الأفراد</button>
                        <button type="button" class="btn btn-outline-secondary" id="groupConversationsBtn">المجموعات</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                @if(isset($selectedConversation))
                    @include('dashboard.customer.messaging.conversation', ['conversation' => $selectedConversation])
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-chat-dots fs-1 d-block mb-3 text-muted"></i>
                        <h5 class="text-muted mb-3">اختر محادثة للبدء</h5>
                        <p class="text-muted">حدد محادثة من القائمة أو ابدأ محادثة جديدة</p>
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                            <i class="bi bi-plus-lg me-2"></i> محادثة جديدة
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal: إنشاء محادثة جديدة -->
<div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newConversationModalLabel">محادثة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="conversationTypeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual-tab-pane" type="button" role="tab" aria-controls="individual-tab-pane" aria-selected="true">فردية</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="group-tab" data-bs-toggle="tab" data-bs-target="#group-tab-pane" type="button" role="tab" aria-controls="group-tab-pane" aria-selected="false">مجموعة</button>
                    </li>
                </ul>
                <div class="tab-content" id="conversationTypeTabsContent">
                    <div class="tab-pane fade show active" id="individual-tab-pane" role="tabpanel" aria-labelledby="individual-tab" tabindex="0">
                        <form action="{{ route('dashboard.customer.messaging.start-conversation') }}" method="POST">
                            @csrf
                            <input type="hidden" name="conversation_type" value="individual">
                            <div class="mb-3">
                                <label for="recipient" class="form-label">اختر شخص</label>
                                <select class="form-select" id="recipient" name="recipient_id" required>
                                    <option value="" selected disabled>اختر شخص...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->country }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">رسالة (اختياري)</label>
                                <textarea class="form-control" id="message" name="message" rows="3" placeholder="اكتب رسالة..."></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">بدء المحادثة</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="group-tab-pane" role="tabpanel" aria-labelledby="group-tab" tabindex="0">
                        <form action="{{ route('dashboard.customer.messaging.start-conversation') }}" method="POST">
                            @csrf
                            <input type="hidden" name="conversation_type" value="group">
                            <div class="mb-3">
                                <label for="group_name" class="form-label">اسم المجموعة</label>
                                <input type="text" class="form-control" id="group_name" name="group_name" required placeholder="اكتب اسم المجموعة...">
                            </div>
                            <div class="mb-3">
                                <label for="participants" class="form-label">المشاركون</label>
                                <select class="form-select" id="participants" name="participants[]" multiple required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->country }})</option>
                                    @endforeach
                                </select>
                                <div class="form-text">يمكنك اختيار أكثر من شخص</div>
                            </div>
                            <div class="mb-3">
                                <label for="group_message" class="form-label">رسالة الترحيب (اختياري)</label>
                                <textarea class="form-control" id="group_message" name="message" rows="3" placeholder="اكتب رسالة ترحيب..."></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">إنشاء المجموعة</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // بحث في المحادثات
        $("#searchConversations").on("keyup", function() {
            let value = $(this).val().toLowerCase();
            $(".conversation-item").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        // تصفية المحادثات
        $("#allConversationsBtn").click(function() {
            $(this).addClass("active");
            $("#individualConversationsBtn, #groupConversationsBtn").removeClass("active");
            $(".conversation-item").show();
        });

        $("#individualConversationsBtn").click(function() {
            $(this).addClass("active");
            $("#allConversationsBtn, #groupConversationsBtn").removeClass("active");
            $(".conversation-item").hide();
            $(".conversation-item:not(:has(.bi-people-fill))").show();
        });

        $("#groupConversationsBtn").click(function() {
            $(this).addClass("active");
            $("#allConversationsBtn, #individualConversationsBtn").removeClass("active");
            $(".conversation-item").hide();
            $(".conversation-item:has(.bi-people-fill)").show();
        });
    });
</script>
@endsection

@section('styles')
<style>
    .conversation-list-container {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .conversation-avatar {
        width: 48px;
        height: 48px;
        overflow: hidden;
        border-radius: 50%;
    }
    
    .conversation-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .conversation-avatar .bi, .conversation-avatar div {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .conversation-item.active {
        background-color: var(--bs-primary);
        color: white;
    }
    
    .conversation-item.active .text-muted {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    
    /* تنسيق scrollbar */
    .conversation-list-container::-webkit-scrollbar {
        width: 5px;
    }
    
    .conversation-list-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .conversation-list-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 5px;
    }
    
    .conversation-list-container::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
</style>
@endsection