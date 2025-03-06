@extends('layouts.dashboard')

@section('title', 'الأجهزة المتصلة')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الأجهزة المتصلة</h5>
                        <a href="{{ route('profile.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right me-1"></i> العودة للملف الشخصي
                        </a>
                    </div>
                </x-slot>

                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    يمكنك مراجعة الأجهزة التي قمت بتسجيل الدخول منها مؤخراً. إذا لاحظت نشاطًا غير معتاد، يمكنك تسجيل الخروج من هذه الأجهزة.
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col">الجهاز</th>
                                <th scope="col">المتصفح</th>
                                <th scope="col">الموقع</th>
                                <th scope="col">آخر نشاط</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($sessions) > 0)
                                @foreach($sessions as $session)
                                    <tr class="{{ $session->is_current ? 'table-primary' : '' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if(str_contains(strtolower($session->device), 'mobile'))
                                                        <i class="bi bi-phone fs-4"></i>
                                                    @elseif(str_contains(strtolower($session->device), 'tablet'))
                                                        <i class="bi bi-tablet fs-4"></i>
                                                    @else
                                                        <i class="bi bi-laptop fs-4"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="d-block">{{ $session->device }}</span>
                                                    <small class="text-muted">{{ $session->platform }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $session->browser }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('assets/img/flags/' . strtolower($session->country_code) . '.svg') }}" width="18" class="me-2" alt="{{ $session->country }}">
                                                <span>{{ $session->city }}, {{ $session->country }}</span>
                                            </div>
                                            <small class="text-muted">{{ $session->ip_address }}</small>
                                        </td>
                                        <td>{{ $session->last_active_at->diffForHumans() }}</td>
                                        <td>
                                            @if($session->is_current)
                                                <span class="badge bg-success">الجلسة الحالية</span>
                                            @else
                                                @if($session->is_suspicious)
                                                    <span class="badge bg-danger">مشبوه</span>
                                                @else
                                                    <span class="badge bg-secondary">غير نشط</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if(!$session->is_current)
                                                <form action="{{ route('profile.logout-device', $session->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من رغبتك في تسجيل الخروج من هذا الجهاز؟');">
                                                        <i class="bi bi-box-arrow-right"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="my-3">
                                            <i class="bi bi-device-hdd text-muted" style="font-size: 48px;"></i>
                                        </div>
                                        <p class="mb-0">لا توجد جلسات نشطة متاحة.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <form action="{{ route('profile.logout-all-devices') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من رغبتك في تسجيل الخروج من جميع الأجهزة الأخرى؟');">
                            <i class="bi bi-box-arrow-right me-2"></i> تسجيل الخروج من جميع الأجهزة الأخرى
                        </button>
                    </form>
                </div>
            </x-card>

            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <h5 class="mb-0">سجل تسجيل الدخول</h5>
                </x-slot>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col">الوقت</th>
                                <th scope="col">الجهاز</th>
                                <th scope="col">الموقع</th>
                                <th scope="col">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($loginHistory) > 0)
                                @foreach($loginHistory as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    @if(str_contains(strtolower($log->device), 'mobile'))
                                                        <i class="bi bi-phone fs-4"></i>
                                                    @elseif(str_contains(strtolower($log->device), 'tablet'))
                                                        <i class="bi bi-tablet fs-4"></i>
                                                    @else
                                                        <i class="bi bi-laptop fs-4"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="d-block">{{ $log->browser }}</span>
                                                    <small class="text-muted">{{ $log->device }} ({{ $log->platform }})</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('assets/img/flags/' . strtolower($log->country_code) . '.svg') }}" width="18" class="me-2" alt="{{ $log->country }}">
                                                <span>{{ $log->city }}, {{ $log->country }}</span>
                                            </div>
                                            <small class="text-muted">{{ $log->ip_address }}</small>
                                        </td>
                                        <td>
                                            @if($log->status === 'success')
                                                <span class="badge bg-success">ناجح</span>
                                            @else
                                                <span class="badge bg-danger">فاشل</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <p class="mb-0">لا يوجد سجل تسجيل دخول متاح.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if($loginHistory instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="d-flex justify-content-center mt-3">
                        {{ $loginHistory->links() }}
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
