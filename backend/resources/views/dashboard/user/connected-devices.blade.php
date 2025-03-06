@extends('layouts.dashboard')

@section('title', 'الأجهزة المتصلة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">الأجهزة المتصلة</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('user.security') }}">الأمان</a></li>
                        <li class="breadcrumb-item active">الأجهزة المتصلة</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-soft-primary">
                    <h5 class="card-title mb-0">الجهاز الحالي</h5>
                </div>
                <div class="card-body">
                    @if($currentDevice)
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-circle avatar-md bg-soft-primary">
                                    <i class="mdi mdi-{{ $currentDevice->device_type == 'هاتف محمول' ? 'cellphone' : ($currentDevice->device_type == 'جهاز لوحي' ? 'tablet' : 'laptop') }} font-size-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="font-size-16 mb-1">{{ $currentDevice->device_name }}</h5>
                                <p class="text-muted mb-0">{{ $currentDevice->browser }} على {{ $currentDevice->operating_system }}</p>
                                <p class="text-muted mb-0">
                                    <small>آخر نشاط: {{ $currentDevice->last_active_at->diffForHumans() }}</small>
                                </p>
                                <p class="text-muted mb-0">
                                    <small>{{ $currentDevice->ip_address }} • {{ $currentDevice->location ?? 'موقع غير معروف' }}</small>
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-success">الجهاز الحالي</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center">
                            <p class="mb-0">لا يوجد معلومات عن الجهاز الحالي</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">الأجهزة الأخرى المتصلة</h5>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#logoutAllDevicesModal">
                        <i class="mdi mdi-logout-variant me-1"></i> تسجيل الخروج من جميع الأجهزة
                    </button>
                </div>
                <div class="card-body">
                    @if(count($otherDevices) > 0)
                        <div class="table-responsive">
                            <table class="table table-centered table-nowrap mb-0">
                                <thead>
                                    <tr>
                                        <th>الجهاز</th>
                                        <th>آخر نشاط</th>
                                        <th>الموقع</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otherDevices as $device)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-xs me-3">
                                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                            <i class="mdi mdi-{{ $device->device_type == 'هاتف محمول' ? 'cellphone' : ($device->device_type == 'جهاز لوحي' ? 'tablet' : 'laptop') }}"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h5 class="font-size-14 mb-1">{{ $device->device_name }}</h5>
                                                        <p class="text-muted mb-0">{{ $device->browser }} على {{ $device->operating_system }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $device->last_active_at->diffForHumans() }}</td>
                                            <td>{{ $device->location ?? 'موقع غير معروف' }} ({{ $device->ip_address }})</td>
                                            <td>
                                                @if($device->is_trusted)
                                                    <span class="badge bg-soft-success text-success">جهاز موثوق</span>
                                                @else
                                                    <span class="badge bg-soft-warning text-warning">غير موثوق</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="mdi mdi-dots-horizontal font-size-18"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <form action="{{ route('user.devices.toggle-trust', $device->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    @if($device->is_trusted)
                                                                        <i class="mdi mdi-shield-off-outline text-danger me-2"></i> إلغاء الثقة
                                                                    @else
                                                                        <i class="mdi mdi-shield-check-outline text-success me-2"></i> تعيين كجهاز موثوق
                                                                    @endif
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('user.devices.logout', $device->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="mdi mdi-logout-variant text-danger me-2"></i> تسجيل الخروج
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="avatar-md mx-auto mb-4">
                                <div class="avatar-title rounded-circle bg-light">
                                    <i class="mdi mdi-devices text-primary font-size-24"></i>
                                </div>
                            </div>
                            <h5>لا توجد أجهزة أخرى متصلة</h5>
                            <p class="text-muted">لم يتم تسجيل دخولك من أي أجهزة أخرى.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات هامة</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <h5 class="alert-heading"><i class="mdi mdi-information-outline me-2"></i> نصائح للحفاظ على أمان حسابك</h5>
                        <ul class="mb-0">
                            <li>قم بتسجيل الخروج دائمًا عند استخدام أجهزة عامة.</li>
                            <li>لا تقم بتعيين الأجهزة العامة كأجهزة موثوقة.</li>
                            <li>إذا لاحظت أي نشاط مشبوه، قم بتسجيل الخروج من جميع الأجهزة وتغيير كلمة المرور الخاصة بك.</li>
                            <li>قم بتفعيل المصادقة الثنائية للحصول على حماية إضافية.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal تسجيل الخروج من جميع الأجهزة -->
<div class="modal fade" id="logoutAllDevicesModal" tabindex="-1" aria-labelledby="logoutAllDevicesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutAllDevicesModalLabel">تسجيل الخروج من جميع الأجهزة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في تسجيل الخروج من جميع الأجهزة الأخرى؟</p>
                <p class="text-danger">سيتم إنهاء جميع جلسات تسجيل الدخول الحالية باستثناء الجهاز الذي تستخدمه حاليًا.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('user.devices.logout-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">تسجيل الخروج من جميع الأجهزة</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
