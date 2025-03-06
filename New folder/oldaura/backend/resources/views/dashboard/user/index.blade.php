@extends('layouts.dashboard')

@section('title', 'لوحة التحكم')

@section('page-title', 'لوحة التحكم')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">مرحباً {{ auth()->user()->name }}</h1>
            <p class="text-muted">آخر تسجيل دخول: {{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d/m/Y الساعة h:i A') : 'أول مرة' }}</p>
        </div>
    </div>

    <!-- تنبيه التحقق من الهوية KYC -->
    @include('dashboard.user.partials.kyc-alert')

    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <x-card class="h-100 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">الطلبات</h6>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-box fs-3 text-primary"></i>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <x-card class="h-100 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">الرسائل</h6>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-chat-dots fs-3 text-success"></i>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <x-card class="h-100 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">الإشعارات</h6>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="bi bi-bell fs-3 text-warning"></i>
                    </div>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card class="h-100 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">المفضلة</h6>
                        <h3 class="mb-0">0</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                        <i class="bi bi-heart fs-3 text-danger"></i>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- المحتوى الرئيسي -->
    <div class="row">
        <div class="col-md-8 mb-4 mb-md-0">
            <x-card class="shadow-sm border-0">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">آخر النشاطات</h5>
                        <a href="#" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                    </div>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">النشاط</th>
                                <th scope="col">التاريخ</th>
                                <th scope="col">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="bi bi-calendar-check fs-3 d-block mb-2 text-muted"></i>
                                    <p class="text-muted">لم يتم تسجيل أي نشاطات بعد</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <div class="col-md-4">
            <x-card class="shadow-sm border-0 mb-4">
                <x-slot name="header">
                    <h5 class="mb-0">ملفك الشخصي</h5>
                </x-slot>

                <div class="text-center mb-3">
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('assets/images/avatar-placeholder.jpg') }}" alt="صورة المستخدم" class="rounded-circle" width="100" height="100">
                        <div class="position-absolute bottom-0 end-0">
                            <a href="{{ route('user.profile') }}" class="btn btn-sm btn-primary rounded-circle">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </div>
                    <h5 class="mt-3 mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-3">{{ auth()->user()->email }}</p>

                    <div class="d-grid">
                        <a href="{{ route('user.profile') }}" class="btn btn-outline-primary">تعديل الملف الشخصي</a>
                    </div>
                </div>
            </x-card>

            <x-card class="shadow-sm border-0">
                <x-slot name="header">
                    <h5 class="mb-0">الإشعارات الأخيرة</h5>
                </x-slot>

                <div class="notifications-container">
                    <div class="text-center py-4">
                        <i class="bi bi-bell-slash fs-3 d-block mb-2 text-muted"></i>
                        <p class="text-muted">لا توجد إشعارات جديدة</p>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="d-grid">
                        <a href="{{ route('user.notifications') }}" class="btn btn-sm btn-outline-primary">عرض كل الإشعارات</a>
                    </div>
                </x-slot>
            </x-card>
        </div>
    </div>
</div>
@endsection
