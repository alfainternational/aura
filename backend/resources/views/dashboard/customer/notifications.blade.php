@extends('layouts.dashboard')

@section('title', 'الإشعارات - لوحة تحكم العميل')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-2">الإشعارات</h4>
                    <p class="text-muted mb-0">تابع آخر الإشعارات والتنبيهات المتعلقة بحسابك</p>
                </div>
                <div>
                    <a href="{{ route('dashboard.customer.notifications.mark-all-read') }}" class="btn btn-outline-primary me-2">
                        <i class="bi bi-check-all me-1"></i> تعيين الكل كمقروء
                    </a>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="notificationsFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-funnel me-1"></i> تصفية
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsFilterDropdown">
                            <li><a class="dropdown-item {{ $filter == 'all' ? 'active' : '' }}" href="{{ route('dashboard.customer.notifications', ['filter' => 'all']) }}">جميع الإشعارات</a></li>
                            <li><a class="dropdown-item {{ $filter == 'unread' ? 'active' : '' }}" href="{{ route('dashboard.customer.notifications', ['filter' => 'unread']) }}">غير المقروءة</a></li>
                            <li><a class="dropdown-item {{ $filter == 'read' ? 'active' : '' }}" href="{{ route('dashboard.customer.notifications', ['filter' => 'read']) }}">المقروءة</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#filterByTypeModal">حسب النوع</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">تصنيفات الإشعارات</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('dashboard.customer.notifications', ['filter' => 'all']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $filter == 'all' ? 'active' : '' }}">
                            جميع الإشعارات
                            <span class="badge bg-{{ $filter == 'all' ? 'light text-dark' : 'primary' }} rounded-pill">{{ $counters['all'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('dashboard.customer.notifications', ['filter' => 'system']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $filter == 'system' ? 'active' : '' }}">
                            تحديثات النظام
                            <span class="badge bg-{{ $filter == 'system' ? 'light text-dark' : 'secondary' }} rounded-pill">{{ $counters['system'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('dashboard.customer.notifications', ['filter' => 'messages']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $filter == 'messages' ? 'active' : '' }}">
                            الرسائل
                            <span class="badge bg-{{ $filter == 'messages' ? 'light text-dark' : 'info' }} rounded-pill">{{ $counters['messages'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('dashboard.customer.notifications', ['filter' => 'calls']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $filter == 'calls' ? 'active' : '' }}">
                            المكالمات
                            <span class="badge bg-{{ $filter == 'calls' ? 'light text-dark' : 'success' }} rounded-pill">{{ $counters['calls'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('dashboard.customer.notifications', ['filter' => 'alerts']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $filter == 'alerts' ? 'active' : '' }}">
                            التنبيهات
                            <span class="badge bg-{{ $filter == 'alerts' ? 'light text-dark' : 'warning' }} rounded-pill">{{ $counters['alerts'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('dashboard.customer.notifications', ['filter' => 'security']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $filter == 'security' ? 'active' : '' }}">
                            الأمان
                            <span class="badge bg-{{ $filter == 'security' ? 'light text-dark' : 'danger' }} rounded-pill">{{ $counters['security'] ?? 0 }}</span>
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-grid">
                        <a href="{{ route('dashboard.customer.settings') }}#notifications" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-gear me-1"></i> إعدادات الإشعارات
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">
                        @if($filter == 'all')
                            جميع الإشعارات
                        @elseif($filter == 'unread')
                            الإشعارات غير المقروءة
                        @elseif($filter == 'read')
                            الإشعارات المقروءة
                        @elseif($filter == 'system')
                            تحديثات النظام
                        @elseif($filter == 'messages')
                            إشعارات الرسائل
                        @elseif($filter == 'calls')
                            إشعارات المكالمات
                        @elseif($filter == 'alerts')
                            التنبيهات
                        @elseif($filter == 'security')
                            إشعارات الأمان
                        @endif
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="notifications-container">
                        @if(count($notifications) > 0)
                            @foreach($notifications as $notification)
                                <div class="notification-item position-relative p-3 border-bottom {{ !$notification->read_at ? 'bg-light' : '' }}">
                                    <div class="d-flex">
                                        <div class="notification-icon 
                                            @if($notification->type == 'system')
                                                bg-secondary
                                            @elseif($notification->type == 'message')
                                                bg-info
                                            @elseif($notification->type == 'call')
                                                bg-success
                                            @elseif($notification->type == 'alert')
                                                bg-warning
                                            @elseif($notification->type == 'security')
                                                bg-danger
                                            @else
                                                bg-primary
                                            @endif
                                            bg-opacity-10 p-2 rounded me-3">
                                            <i class="bi 
                                                @if($notification->type == 'system')
                                                    bi-gear
                                                @elseif($notification->type == 'message')
                                                    bi-chat-dots
                                                @elseif($notification->type == 'call')
                                                    bi-telephone
                                                @elseif($notification->type == 'alert')
                                                    bi-exclamation-triangle
                                                @elseif($notification->type == 'security')
                                                    bi-shield-lock
                                                @else
                                                    bi-bell
                                                @endif
                                                fs-5
                                                @if($notification->type == 'system')
                                                    text-secondary
                                                @elseif($notification->type == 'message')
                                                    text-info
                                                @elseif($notification->type == 'call')
                                                    text-success
                                                @elseif($notification->type == 'alert')
                                                    text-warning
                                                @elseif($notification->type == 'security')
                                                    text-danger
                                                @else
                                                    text-primary
                                                @endif
                                            "></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0">{{ $notification->title }}</h6>
                                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-2">{{ $notification->message }}</p>
                                            <div>
                                                @if($notification->action_url)
                                                <a href="{{ $notification->action_url }}" class="text-decoration-none btn btn-sm btn-light me-2">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> 
                                                    {{ $notification->action_text ?? 'عرض' }}
                                                </a>
                                                @endif
                                                
                                                @if(!$notification->read_at)
                                                <a href="{{ route('dashboard.customer.notifications.mark-read', $notification->id) }}" class="text-decoration-none btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-check me-1"></i> تعيين كمقروء
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                        <form action="{{ route('dashboard.customer.notifications.delete', $notification->id) }}" method="POST" class="delete-notification-form" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الإشعار؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger position-absolute top-0 end-0 mt-2 me-2" title="حذف">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- حالة عدم وجود إشعارات -->
                            <div class="text-center py-5">
                                <i class="bi bi-bell-slash fs-1 d-block mb-3 text-muted"></i>
                                <h5 class="text-muted mb-3">لا توجد إشعارات {{ $filter != 'all' ? 'في هذا التصنيف' : '' }}</h5>
                                <p class="text-muted">ستظهر هنا الإشعارات والتنبيهات الجديدة عندما تتلقاها</p>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="text-muted small mb-0">إجمالي الإشعارات: {{ $notifications->total() }}</p>
                        {{ $notifications->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة التصفية حسب النوع -->
<div class="modal fade" id="filterByTypeModal" tabindex="-1" aria-labelledby="filterByTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterByTypeModalLabel">تصفية حسب النوع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    <a href="{{ route('dashboard.customer.notifications', ['filter' => 'system']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-gear fs-5 text-secondary"></i>
                            </div>
                            <span>تحديثات النظام</span>
                        </div>
                        <span class="badge bg-secondary rounded-pill">{{ $counters['system'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('dashboard.customer.notifications', ['filter' => 'messages']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-chat-dots fs-5 text-info"></i>
                            </div>
                            <span>الرسائل</span>
                        </div>
                        <span class="badge bg-info rounded-pill">{{ $counters['messages'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('dashboard.customer.notifications', ['filter' => 'calls']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-telephone fs-5 text-success"></i>
                            </div>
                            <span>المكالمات</span>
                        </div>
                        <span class="badge bg-success rounded-pill">{{ $counters['calls'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('dashboard.customer.notifications', ['filter' => 'alerts']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-exclamation-triangle fs-5 text-warning"></i>
                            </div>
                            <span>التنبيهات</span>
                        </div>
                        <span class="badge bg-warning rounded-pill">{{ $counters['alerts'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('dashboard.customer.notifications', ['filter' => 'security']) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-shield-lock fs-5 text-danger"></i>
                            </div>
                            <span>الأمان</span>
                        </div>
                        <span class="badge bg-danger rounded-pill">{{ $counters['security'] ?? 0 }}</span>
                    </a>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>
@endsection