@extends('layouts.app')

@section('title', 'الإشعارات والتنبيهات')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-2">الإشعارات والتنبيهات</h1>
                    <p class="text-muted">تابع آخر الإشعارات والتنبيهات المتعلقة بحسابك</p>
                </div>
                <div>
                    <a href="{{ route('user.notifications.mark-all-read') }}" class="btn btn-outline-primary me-2">
                        <i class="bi bi-check-all me-1"></i> تعيين الكل كمقروء
                    </a>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="notificationsFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-funnel me-1"></i> تصفية
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsFilterDropdown">
                            <li><a class="dropdown-item active" href="#">جميع الإشعارات</a></li>
                            <li><a class="dropdown-item" href="#">غير المقروءة</a></li>
                            <li><a class="dropdown-item" href="#">المقروءة</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#">حسب النوع</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <h5 class="mb-0">تصنيفات الإشعارات</h5>
                </x-slot>
                
                <x-list-group class="list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center active">
                        جميع الإشعارات
                        <span class="badge bg-primary rounded-pill">0</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        تحديثات النظام
                        <span class="badge bg-secondary rounded-pill">0</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        الرسائل
                        <span class="badge bg-info rounded-pill">0</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        المعاملات
                        <span class="badge bg-success rounded-pill">0</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        التنبيهات
                        <span class="badge bg-warning rounded-pill">0</span>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        الأمان
                        <span class="badge bg-danger rounded-pill">0</span>
                    </a>
                </x-list-group>
                
                <x-slot name="footer">
                    <div class="d-grid">
                        <a href="{{ route('user.notification-settings') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-gear me-1"></i> إعدادات الإشعارات
                        </a>
                    </div>
                </x-slot>
            </x-card>
        </div>
        
        <div class="col-md-8">
            <x-card class="border-0 shadow-sm">
                <div class="notifications-container">
                    <!-- حالة عدم وجود إشعارات -->
                    <div class="text-center py-5">
                        <i class="bi bi-bell-slash fs-1 d-block mb-3 text-muted"></i>
                        <h5 class="text-muted mb-3">لا توجد إشعارات حالياً</h5>
                        <p class="text-muted">ستظهر هنا الإشعارات والتنبيهات الجديدة عندما تتلقاها</p>
                    </div>
                    
                    <!-- نموذج لإشعارات (معطل حالياً - سيتم استخدامه عندما تكون هناك إشعارات) -->
                    <div class="notification-item d-none">
                        <div class="d-flex position-relative p-3 border-bottom">
                            <div class="notification-icon bg-primary bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-bell fs-5 text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0">عنوان الإشعار</h6>
                                    <small class="text-muted">منذ 5 دقائق</small>
                                </div>
                                <p class="mb-1">محتوى الإشعار يظهر هنا. يمكن أن يكون نصاً طويلاً أو قصيراً حسب نوع الإشعار.</p>
                                <div>
                                    <a href="#" class="text-decoration-none small me-3">
                                        <i class="bi bi-eye me-1"></i> عرض
                                    </a>
                                    <a href="#" class="text-decoration-none text-muted small">
                                        <i class="bi bi-check me-1"></i> تعيين كمقروء
                                    </a>
                                </div>
                            </div>
                            <a href="#" class="text-danger position-absolute top-0 end-0 mt-2 me-2" title="حذف">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <x-slot name="footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="text-muted small mb-0">إجمالي الإشعارات: 0</p>
                        <nav aria-label="تصفح الإشعارات">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="السابق">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="التالي">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>
@endsection
