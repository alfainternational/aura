@extends('layouts.app')

@section('title', 'الإشعارات')

@section('styles')
<style>
    .notification-item {
        transition: all 0.3s ease;
        border-right: 4px solid transparent;
    }
    .notification-item:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
    .notification-item.unread {
        border-right-color: var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    .notification-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .notification-icon.info {
        background-color: rgba(var(--bs-info-rgb), 0.1);
        color: var(--bs-info);
    }
    .notification-icon.success {
        background-color: rgba(var(--bs-success-rgb), 0.1);
        color: var(--bs-success);
    }
    .notification-icon.warning {
        background-color: rgba(var(--bs-warning-rgb), 0.1);
        color: var(--bs-warning);
    }
    .notification-icon.danger {
        background-color: rgba(var(--bs-danger-rgb), 0.1);
        color: var(--bs-danger);
    }
    .notification-time {
        font-size: 0.75rem;
        color: #6c757d;
    }
    .notification-actions {
        visibility: hidden;
        opacity: 0;
        transition: all 0.3s ease;
    }
    .notification-item:hover .notification-actions {
        visibility: visible;
        opacity: 1;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">الإشعارات ({{ $notifications->total() }})</h5>
                    <div>
                        @if($unreadCount > 0)
                            <a href="{{ route('notifications.mark-all-read') }}" class="btn btn-sm btn-outline-primary me-2" onclick="event.preventDefault(); document.getElementById('mark-all-read-form').submit();">
                                <i class="fas fa-check-double"></i> تحديد الكل كمقروء
                            </a>
                            <form id="mark-all-read-form" action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @endif
                        @if($notifications->total() > 0)
                            <a href="{{ route('notifications.destroy-all') }}" class="btn btn-sm btn-outline-danger" onclick="event.preventDefault(); if(confirm('هل أنت متأكد من حذف جميع الإشعارات؟')) document.getElementById('delete-all-form').submit();">
                                <i class="fas fa-trash"></i> حذف الكل
                            </a>
                            <form id="delete-all-form" action="{{ route('notifications.destroy-all') }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item notification-item {{ $notification->read_at ? '' : 'unread' }} p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="notification-icon {{ $notification->type }} me-3">
                                            <i class="fas fa-{{ $notification->icon ?? 'bell' }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-1">{{ $notification->title }}</h6>
                                                <div class="notification-actions">
                                                    @if(!$notification->read_at)
                                                        <a href="{{ route('notifications.mark-read', $notification->id) }}" class="btn btn-sm btn-link text-primary p-0 me-2" title="تحديد كمقروء" onclick="event.preventDefault(); document.getElementById('mark-read-{{ $notification->id }}').submit();">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <form id="mark-read-{{ $notification->id }}" action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="d-none">
                                                            @csrf
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('notifications.destroy', $notification->id) }}" class="btn btn-sm btn-link text-danger p-0" title="حذف" onclick="event.preventDefault(); if(confirm('هل أنت متأكد من حذف هذا الإشعار؟')) document.getElementById('delete-{{ $notification->id }}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <form id="delete-{{ $notification->id }}" action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-none">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </div>
                                            <p class="mb-1">{{ $notification->message }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="notification-time">{{ $notification->created_at->diffForHumans() }}</small>
                                                @if($notification->action_url)
                                                    <a href="{{ $notification->action_url }}" class="btn btn-sm btn-link p-0">
                                                        عرض التفاصيل <i class="fas fa-chevron-left ms-1"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-bell-slash fa-4x text-muted"></i>
                            </div>
                            <h5 class="text-muted">لا توجد إشعارات</h5>
                            <p class="text-muted">ستظهر هنا الإشعارات المهمة والتحديثات الخاصة بحسابك</p>
                        </div>
                    @endif
                </div>
                @if($notifications->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-center">
                            {{ $notifications->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
