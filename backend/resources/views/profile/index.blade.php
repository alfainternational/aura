@extends('layouts.dashboard')

@section('title', 'الملف الشخصي')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <x-card class="border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <div class="avatar-container me-3">
                        @if ($user->profile && $user->profile->avatar)
                            <img src="{{ asset('storage/' . $user->profile->avatar) }}" alt="{{ $user->name }}" class="rounded-circle" width="100" height="100">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 100px; height: 100px;">
                                <span class="fs-1">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="user-info">
                        <h3 class="mb-1">{{ $user->name }}</h3>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                        <p class="mb-1">
                            <span class="badge bg-primary">{{ $user->roles[0]->name }}</span>
                            @if($user->profile && $user->profile->is_verified)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> موثق</span>
                            @endif
                        </p>
                        <p class="text-muted mb-0">
                            عضو منذ {{ $user->created_at->locale('ar')->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">معلومات الاتصال</h5>
                </x-slot>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-envelope me-2"></i> البريد الإلكتروني</span>
                        <span class="text-muted">{{ $user->email }}</span>
                    </li>
                    @if($user->profile && $user->profile->phone)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-telephone me-2"></i> رقم الهاتف</span>
                        <span class="text-muted">{{ $user->profile->phone }}</span>
                    </li>
                    @endif
                    @if($user->profile && $user->profile->address)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-geo-alt me-2"></i> العنوان</span>
                        <span class="text-muted">{{ $user->profile->address }}</span>
                    </li>
                    @endif
                    @if($user->profile && $user->profile->country_id)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-globe me-2"></i> الدولة</span>
                        <span class="text-muted">{{ $user->profile->country->name }}</span>
                    </li>
                    @endif
                    @if($user->profile && $user->profile->city_id)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-building me-2"></i> المدينة</span>
                        <span class="text-muted">{{ $user->profile->city->name }}</span>
                    </li>
                    @endif
                </ul>

                <div class="d-grid mt-3">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> تعديل المعلومات
                    </a>
                </div>
            </x-card>
        </div>

        <div class="col-md-4 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">الأمان</h5>
                </x-slot>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-shield-lock me-2"></i> كلمة المرور</span>
                        <a href="{{ route('profile.change-password') }}" class="btn btn-sm btn-outline-primary">تغيير</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-phone me-2"></i> المصادقة الثنائية</span>
                        @if(auth()->user()->two_factor_enabled)
                            <span class="badge bg-success">مفعل</span>
                        @else
                            <a href="{{ route('profile.two-factor-auth') }}" class="btn btn-sm btn-outline-primary">تفعيل</a>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-devices me-2"></i> الأجهزة المتصلة</span>
                        <a href="{{ route('profile.devices') }}" class="btn btn-sm btn-outline-primary">عرض</a>
                    </li>
                </ul>
            </x-card>
        </div>

        <div class="col-md-4 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">الإحصائيات</h5>
                </x-slot>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-chat-dots me-2"></i> المحادثات</span>
                        <span class="badge bg-primary rounded-pill">{{ $conversationsCount ?? 0 }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-currency-exchange me-2"></i> المعاملات</span>
                        <span class="badge bg-primary rounded-pill">{{ $transactionsCount ?? 0 }}</span>
                    </li>
                    @if(auth()->user()->hasRole('customer'))
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-box-seam me-2"></i> الطلبات</span>
                        <span class="badge bg-primary rounded-pill">{{ $ordersCount ?? 0 }}</span>
                    </li>
                    @endif
                    @if(auth()->user()->hasRole('merchant'))
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bag me-2"></i> المنتجات</span>
                        <span class="badge bg-primary rounded-pill">{{ $productsCount ?? 0 }}</span>
                    </li>
                    @endif
                </ul>
            </x-card>
        </div>
    </div>
</div>
@endsection
