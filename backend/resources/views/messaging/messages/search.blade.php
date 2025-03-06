@extends('layouts.dashboard')

@section('title', 'بحث في الرسائل')

@section('page-title', 'بحث في الرسائل')

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
                            <h5 class="mb-0">بحث في محادثة: {{ $conversation->is_group ? $conversation->title : ($otherParticipant->name ?? 'محادثة') }}</h5>
                        </div>
                    </div>
                </x-slot>

                <div class="mb-4">
                    <form id="search-form" method="GET" action="{{ route('messaging.messages.search', $conversation->id) }}">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-query" name="query" placeholder="ابحث عن رسائل..." value="{{ request('query') }}" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="bi bi-search me-1"></i> بحث
                            </button>
                        </div>
                    </form>
                </div>

                <div id="search-results">
                    @if(isset($messages) && count($messages) > 0)
                        <h6 class="mb-3">نتائج البحث ({{ $messages->total() }})</h6>
                        
                        <div class="messages-list">
                            @foreach($messages as $message)
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
                                    <div>
                                        <a href="{{ route('messaging.conversations.show', ['conversation' => $conversation->id, 'message' => $message->id]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i> عرض في المحادثة
                                        </a>
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
                            </div>
                            @endforeach
                        </div>

                        <!-- ترقيم الصفحات -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $messages->appends(['query' => request('query')])->links() }}
                        </div>
                    @elseif(request('query'))
                        <div class="text-center py-5">
                            <img src="{{ asset('images/no-results.svg') }}" alt="لا توجد نتائج" class="img-fluid mb-3" width="120">
                            <h5>لا توجد نتائج</h5>
                            <p class="text-muted">لم يتم العثور على نتائج مطابقة لـ "{{ request('query') }}"</p>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/search.svg') }}" alt="البحث" class="img-fluid mb-3" width="120">
                            <h5>ابحث في المحادثة</h5>
                            <p class="text-muted">اكتب كلمات البحث للعثور على الرسائل</p>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('search-form');
        const searchQuery = document.getElementById('search-query');
        
        // التركيز على حقل البحث عند تحميل الصفحة
        searchQuery.focus();
        
        // إرسال النموذج عند الكتابة بعد تأخير قصير
        let typingTimer;
        const doneTypingInterval = 500; // بالمللي ثانية
        
        searchQuery.addEventListener('keyup', function() {
            clearTimeout(typingTimer);
            if (searchQuery.value.length >= 2) {
                typingTimer = setTimeout(function() {
                    searchForm.submit();
                }, doneTypingInterval);
            }
        });
    });
</script>
@endpush
