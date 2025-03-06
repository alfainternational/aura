@extends('layouts.dashboard')

@section('title', 'لوحة تحكم العميل')

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
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : asset('assets/images/avatar-placeholder.jpg') }}" 
                                alt="{{ auth()->user()->name }}" 
                                class="rounded-circle" 
                                width="64" height="64">
                        </div>
                        <div>
                            <h5 class="mb-1">مرحباً، {{ auth()->user()->name }}!</h5>
                            <p class="text-muted mb-0">آخر تسجيل دخول: {{ auth()->user()->last_login_at ? \Carbon\Carbon::parse(auth()->user()->last_login_at)->translatedFormat('d M Y، h:i a') : 'لم يتم تسجيل الدخول بعد' }}</p>
                        </div>
                    </div>

                    <div class="alert alert-info d-flex mb-0" role="alert">
                        <i class="bi bi-info-circle fs-4 me-3"></i>
                        <div>
                            <h6 class="alert-heading mb-1">ملاحظة</h6>
                            <p class="mb-0">يمكنك الآن استخدام خدمات المراسلة والاتصال الصوتي بشكل كامل في نظام AURA. اكتشف الميزات الجديدة من خلال قائمة التنقل.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="card-title mb-0">المحادثات النشطة</h6>
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded p-2">
                            <i class="bi bi-chat-dots fs-4"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $activeConversations ?? 0 }}</h3>
                    <p class="text-muted mb-4">محادثات نشطة</p>
                    <a href="{{ route('dashboard.customer.messaging.conversations') }}" class="btn btn-sm btn-outline-primary">عرض المحادثات</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="card-title mb-0">الرسائل الغير مقروءة</h6>
                        <div class="icon-box bg-danger bg-opacity-10 text-danger rounded p-2">
                            <i class="bi bi-envelope fs-4"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $unreadMessages ?? 0 }}</h3>
                    <p class="text-muted mb-4">رسائل جديدة</p>
                    <a href="{{ route('dashboard.customer.messaging.conversations') }}" class="btn btn-sm btn-outline-danger">قراءة الرسائل</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="card-title mb-0">جهات الاتصال</h6>
                        <div class="icon-box bg-success bg-opacity-10 text-success rounded p-2">
                            <i class="bi bi-people fs-4"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $contacts ?? 0 }}</h3>
                    <p class="text-muted mb-4">جهات اتصال نشطة</p>
                    <a href="{{ route('contacts.index') }}" class="btn btn-sm btn-outline-success">إدارة جهات الاتصال</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="card-title mb-0">الإشعارات</h6>
                        <div class="icon-box bg-warning bg-opacity-10 text-warning rounded p-2">
                            <i class="bi bi-bell fs-4"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $notifications ?? 0 }}</h3>
                    <p class="text-muted mb-4">إشعارات غير مقروءة</p>
                    <a href="{{ route('dashboard.customer.notifications') }}" class="btn btn-sm btn-outline-warning">عرض الإشعارات</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">آخر المحادثات</h6>
                        <a href="{{ route('dashboard.customer.messaging.conversations') }}" class="btn btn-sm btn-link text-decoration-none">عرض الكل</a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($recentConversations) && count($recentConversations) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($recentConversations as $conversation)
                                <li class="list-group-item border-0 px-0 py-3">
                                    <a href="{{ route('dashboard.customer.messaging.conversation', $conversation->id) }}" class="text-decoration-none text-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 position-relative">
                                                <img src="{{ $conversation->image ? asset('storage/' . $conversation->image) : asset('assets/images/group-avatar.jpg') }}" 
                                                    alt="{{ $conversation->name }}" 
                                                    class="rounded-circle" 
                                                    width="48" height="48">
                                                @if($conversation->unread_count > 0)
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                        {{ $conversation->unread_count }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0">{{ $conversation->name }}</h6>
                                                    <small class="text-muted">{{ $conversation->last_message_at->diffForHumans() }}</small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <p class="text-muted text-truncate mb-0" style="max-width: 280px;">{{ $conversation->last_message }}</p>
                                                    @if($conversation->is_group)
                                                        <span class="badge bg-info">مجموعة</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-chat-left-text text-muted" style="font-size: 48px;"></i>
                            </div>
                            <h6 class="text-muted">لا توجد محادثات حديثة</h6>
                            <p class="text-muted mb-3">ابدأ محادثة جديدة مع جهات اتصالك</p>
                            <a href="{{ route('dashboard.customer.messaging.conversations') }}" class="btn btn-primary">بدء محادثة جديدة</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">آخر الإشعارات</h6>
                </div>
                <div class="card-body p-0">
                    @if(isset($recentNotifications) && count($recentNotifications) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentNotifications as $notification)
                                <a href="{{ route('dashboard.customer.notifications') }}" class="list-group-item list-group-item-action py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle p-2 bg-{{ $notification->data['type'] ?? 'primary' }} bg-opacity-10 text-{{ $notification->data['type'] ?? 'primary' }}">
                                                <i class="bi bi-{{ $notification->data['icon'] ?? 'bell' }}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">{{ $notification->data['title'] ?? 'إشعار جديد' }}</h6>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-0 small text-muted">{{ $notification->data['message'] ?? '' }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-bell-slash text-muted" style="font-size: 48px;"></i>
                            </div>
                            <h6 class="text-muted">لا توجد إشعارات جديدة</h6>
                            <p class="text-muted">ستظهر الإشعارات الجديدة هنا</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('dashboard.customer.notifications') }}" class="btn btn-sm btn-outline-primary w-100">عرض جميع الإشعارات</a>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">روابط سريعة</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="{{ route('dashboard.customer.messaging.conversations') }}" class="text-decoration-none">
                                <div class="quick-link-card bg-light rounded p-3 text-center">
                                    <i class="bi bi-chat-dots text-primary mb-2" style="font-size: 24px;"></i>
                                    <p class="mb-0 small">المحادثات</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('contacts.index') }}" class="text-decoration-none">
                                <div class="quick-link-card bg-light rounded p-3 text-center">
                                    <i class="bi bi-people text-success mb-2" style="font-size: 24px;"></i>
                                    <p class="mb-0 small">جهات الاتصال</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('profile.index') }}" class="text-decoration-none">
                                <div class="quick-link-card bg-light rounded p-3 text-center">
                                    <i class="bi bi-person text-info mb-2" style="font-size: 24px;"></i>
                                    <p class="mb-0 small">الملف الشخصي</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('dashboard.customer.settings') }}" class="text-decoration-none">
                                <div class="quick-link-card bg-light rounded p-3 text-center">
                                    <i class="bi bi-gear text-secondary mb-2" style="font-size: 24px;"></i>
                                    <p class="mb-0 small">الإعدادات</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .quick-link-card {
        transition: all 0.3s ease;
    }
    .quick-link-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
    }
    .icon-box {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush
