@extends('layouts.dashboard')

@section('title', 'الملف الشخصي - عميل')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <x-card class="border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <div class="avatar-container me-3">
                        @if ($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="rounded-circle" width="100" height="100">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 100px; height: 100px;">
                                <span class="fs-1">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="user-info">
                        <div class="d-flex align-items-center mb-1">
                            <h3 class="mb-0 me-2">{{ $user->name }}</h3>
                            <span class="badge bg-success">{{ $stats['membership_level'] }}</span>
                        </div>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                        <p class="mb-1">
                            <span class="badge bg-primary">عميل</span>
                            @if($user->is_verified)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> موثق</span>
                            @endif
                            @if($user->privacySettings && $user->privacySettings->online_status_visibility !== 'nobody')
                                <span class="badge bg-info"><i class="bi bi-circle-fill text-success me-1 small"></i> متصل</span>
                            @endif
                        </p>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('profile.update') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> تعديل الملف الشخصي
                        </a>
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
                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                        <div>
                            <p class="mb-0 text-muted">البريد الإلكتروني</p>
                            <p class="mb-0 fw-medium">{{ $user->email }}</p>
                        </div>
                        <span class="badge bg-success rounded-pill">موثق</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                        <div>
                            <p class="mb-0 text-muted">رقم الهاتف</p>
                            <p class="mb-0 fw-medium">{{ $user->phone ?? 'غير محدد' }}</p>
                        </div>
                        @if($user->phone)
                            <span class="badge bg-success rounded-pill">موثق</span>
                        @else
                            <span class="badge bg-secondary rounded-pill">غير محدد</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                        <div>
                            <p class="mb-0 text-muted">العنوان</p>
                            <p class="mb-0 fw-medium">{{ $user->address ?? 'غير محدد' }}</p>
                        </div>
                    </li>
                </ul>
            </x-card>
        </div>

        <div class="col-md-4 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">الإحصائيات</h5>
                </x-slot>
                
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['transactions'] }}</h3>
                        <p class="text-muted small mb-0">عدد المعاملات</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-success mb-0">{{ number_format($stats['total_spent'], 2) }} ج.س</h3>
                        <p class="text-muted small mb-0">إجمالي الإنفاق</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-info mb-0">{{ $stats['saved_addresses'] }}</h3>
                        <p class="text-muted small mb-0">العناوين المحفوظة</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-warning mb-0">{{ $user->created_at->diffInMonths() + 1 }}</h3>
                        <p class="text-muted small mb-0">شهور العضوية</p>
                    </div>
                </div>
                
                <!-- تقدم مستوى العضوية -->
                <div class="mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small">المستوى الحالي: {{ $stats['membership_level'] }}</span>
                        <span class="text-muted small">المستوى التالي: أوروا {{ $stats['membership_level'] == 'أوروا بيسك' ? 'سيلفر' : ($stats['membership_level'] == 'أوروا سيلفر' ? 'جولد' : ($stats['membership_level'] == 'أوروا جولد' ? 'بلاتينيوم' : 'بلاتينيوم')) }}</span>
                    </div>
                    
                    @php
                        $nextLevel = 0;
                        $currentSpent = $stats['total_spent'];
                        
                        if ($stats['membership_level'] == 'أوروا بيسك') {
                            $nextLevel = 1000;
                            $progress = min(($currentSpent / $nextLevel) * 100, 100);
                        } elseif ($stats['membership_level'] == 'أوروا سيلفر') {
                            $nextLevel = 5000;
                            $progress = min((($currentSpent - 1000) / ($nextLevel - 1000)) * 100, 100);
                        } elseif ($stats['membership_level'] == 'أوروا جولد') {
                            $nextLevel = 10000;
                            $progress = min((($currentSpent - 5000) / ($nextLevel - 5000)) * 100, 100);
                        } else {
                            $progress = 100;
                        }
                    @endphp
                    
                    <div class="progress" style="height: 6px">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    @if($stats['membership_level'] != 'أوروا بلاتينيوم')
                        <p class="text-muted small mt-1 mb-0">{{ number_format($nextLevel - $currentSpent, 2) }} ج.س للمستوى التالي</p>
                    @else
                        <p class="text-muted small mt-1 mb-0">وصلت إلى أعلى مستوى!</p>
                    @endif
                </div>
            </x-card>
        </div>

        <div class="col-md-4 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">المعاملات الأخيرة</h5>
                        <a href="#" class="btn btn-sm btn-link">عرض الكل</a>
                    </div>
                </x-slot>
                
                @if($user->transactions && $user->transactions->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($user->transactions->take(5) as $transaction)
                            <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0 py-2">
                                <div class="ms-2 me-auto">
                                    <div class="fw-medium">{{ $transaction->title }}</div>
                                    <div class="text-muted small">{{ $transaction->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <span class="badge {{ $transaction->type == 'deposit' ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                    {{ $transaction->type == 'deposit' ? '+' : '-' }} {{ number_format($transaction->amount, 2) }} ج.س
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted">لا توجد معاملات حتى الآن</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">العناوين المحفوظة</h5>
                        <a href="#" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i> إضافة عنوان
                        </a>
                    </div>
                </x-slot>
                
                @if($user->addresses && $user->addresses->count() > 0)
                    <div class="row">
                        @foreach($user->addresses as $address)
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3 h-100 position-relative">
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i> تعديل</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2"></i> حذف</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <h6 class="mb-1">{{ $address->title }}</h6>
                                    <p class="mb-2 text-muted">{{ $address->address }}</p>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        <span>{{ $address->city->name ?? '' }}, {{ $address->region ?? '' }}</span>
                                    </div>
                                    
                                    @if($address->is_default)
                                        <span class="badge bg-info mt-2">العنوان الافتراضي</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-geo-alt text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted">لا توجد عناوين محفوظة</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-lg me-1"></i> إضافة عنوان جديد
                        </a>
                    </div>
                @endif
            </x-card>
        </div>
        
        <div class="col-md-6 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">وسائل الدفع</h5>
                        <a href="#" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i> إضافة وسيلة دفع
                        </a>
                    </div>
                </x-slot>
                
                @if($user->paymentMethods && $user->paymentMethods->count() > 0)
                    <div class="row">
                        @foreach($user->paymentMethods as $method)
                            <div class="col-12 mb-3">
                                <div class="border rounded p-3 position-relative">
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i> تعديل</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-trash me-2"></i> حذف</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($method->type == 'card')
                                                <i class="bi bi-credit-card fs-3 text-primary"></i>
                                            @elseif($method->type == 'bank')
                                                <i class="bi bi-bank fs-3 text-primary"></i>
                                            @else
                                                <i class="bi bi-wallet fs-3 text-primary"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $method->title }}</h6>
                                            <p class="mb-0 text-muted">
                                                @if($method->type == 'card')
                                                    **** **** **** {{ substr($method->card_number, -4) }}
                                                @elseif($method->type == 'bank')
                                                    {{ $method->bank_name }} - {{ substr($method->account_number, -4) }}
                                                @else
                                                    {{ $method->wallet_type }} - {{ $method->wallet_number }}
                                                @endif
                                            </p>
                                        </div>
                                        
                                        @if($method->is_default)
                                            <span class="badge bg-info ms-auto">الافتراضي</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-credit-card text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted">لا توجد وسائل دفع محفوظة</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-lg me-1"></i> إضافة وسيلة دفع جديدة
                        </a>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
