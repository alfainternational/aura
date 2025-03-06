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
        <div class="col-md-12">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">المحادثات</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                            <i class="fas fa-plus me-1"></i> محادثة جديدة
                        </button>
                    </div>
                </x-slot>
                
                <div class="conversations-list">
                    @if(isset($conversations) && $conversations->count() > 0)
                        <div class="row">
                            @foreach($conversations as $conversation)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <a href="{{ route('messaging.conversations.show', $conversation->id) }}" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                @if($conversation->is_group)
                                                    <div class="avatar-group me-2">
                                                        @foreach($conversation->activeUsers->take(3) as $participant)
                                                        <img src="{{ $participant->profile_image ? asset('storage/' . $participant->profile_image) : asset('images/default-avatar.png') }}" 
                                                             class="avatar avatar-sm rounded-circle" alt="{{ $participant->name }}">
                                                        @endforeach
                                                    </div>
                                                    <h6 class="mb-0">{{ $conversation->title ?? 'مجموعة' }}</h6>
                                                @else
                                                    @php $otherUser = $conversation->activeUsers->first(); @endphp
                                                    @if($otherUser)
                                                    <img src="{{ $otherUser->profile_image ? asset('storage/' . $otherUser->profile_image) : asset('images/default-avatar.png') }}" 
                                                         class="avatar avatar-md rounded-circle me-2" alt="{{ $otherUser->name }}">
                                                    <h6 class="mb-0">{{ $otherUser->name }}</h6>
                                                    @else
                                                    <img src="{{ asset('images/default-avatar.png') }}" class="avatar avatar-md rounded-circle me-2" alt="مستخدم غير متاح">
                                                    <h6 class="mb-0">مستخدم غير متاح</h6>
                                                    @endif
                                                @endif
                                                
                                                @if($conversation->unreadCount() > 0)
                                                <span class="badge bg-primary ms-2">{{ $conversation->unreadCount() }}</span>
                                                @endif
                                            </div>
                                            
                                            <p class="text-muted small mb-2 text-truncate">
                                                @if($conversation->latestMessage)
                                                    @if($conversation->latestMessage->sender_id == auth()->id())
                                                        <span class="text-primary">أنت: </span>
                                                    @elseif($conversation->is_group)
                                                        <span class="text-primary">{{ optional($conversation->latestMessage->sender)->name ?? 'مستخدم' }}: </span>
                                                    @endif
                                                    {{ Str::limit($conversation->latestMessage->content, 50) }}
                                                @else
                                                    لا توجد رسائل بعد
                                                @endif
                                            </p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    @if($conversation->latestMessage)
                                                        {{ $conversation->latestMessage->created_at->diffForHumans() }}
                                                    @else
                                                        {{ $conversation->created_at->diffForHumans() }}
                                                    @endif
                                                </small>
                                                
                                                @if($conversation->is_group)
                                                <small class="text-muted">{{ $conversation->activeUsers->count() }} مشارك</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/no-conversations.svg') }}" alt="لا توجد محادثات" class="img-fluid mb-3" width="120">
                            <h5>لا توجد محادثات</h5>
                            <p class="text-muted">ابدأ محادثة جديدة للتواصل مع الآخرين</p>
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                                <i class="fas fa-plus me-1"></i> بدء محادثة جديدة
                            </button>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>

<!-- Modal: إنشاء محادثة جديدة -->
<div class="modal fade" id="newConversationModal" tabindex="-1" aria-labelledby="newConversationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newConversationModalLabel">محادثة جديدة</h5>
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
                                <label for="group_title" class="form-label">عنوان المجموعة</label>
                                <input type="text" class="form-control" id="group_title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="group_participants" class="form-label">المشاركين</label>
                                <select class="form-select" id="group_participants" name="participants[]" multiple required>
                                    <!-- سيتم تحميل المستخدمين عبر Ajax -->
                                </select>
                                <div class="form-text">يمكنك اختيار أكثر من مستخدم</div>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // تحميل المستخدمين للمحادثات الفردية
        $.ajax({
            url: '{{ route("api.users.list") }}',
            type: 'GET',
            success: function(response) {
                if (response.data) {
                    $.each(response.data, function(index, user) {
                        $('#user_id, #group_participants').append(
                            $('<option></option>').val(user.id).text(user.name)
                        );
                    });
                }
            }
        });
        
        // تفعيل Select2 للاختيارات المتعددة
        $('#group_participants').select2({
            placeholder: 'اختر المشاركين',
            width: '100%'
        });
        
        // إرسال النموذج المناسب
        $('#submitConversationBtn').click(function() {
            if ($('#individual-tab').hasClass('active')) {
                $('#individualConversationForm').submit();
            } else {
                $('#groupConversationForm').submit();
            }
        });
    });
</script>
@endpush
