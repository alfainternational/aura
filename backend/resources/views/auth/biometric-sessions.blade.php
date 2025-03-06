@extends('layouts.app')

@section('title', 'إدارة أجهزة البصمة')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-fingerprint me-2"></i> إدارة أجهزة البصمة</h5>
                    <a href="{{ route('biometric.register') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-plus me-1"></i> إضافة جهاز جديد
                    </a>
                </div>
                
                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success m-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger m-3">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(count($sessions) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>الجهاز</th>
                                        <th>آخر استخدام</th>
                                        <th>تاريخ التسجيل</th>
                                        <th>الحالة</th>
                                        <th>خيارات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessions as $session)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="device-icon me-3">
                                                        <i class="fas fa-{{ Str::contains(strtolower($session->device_name), 'iphone') ? 'apple' : 'mobile-alt' }} text-secondary"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $session->device_name }}</strong>
                                                        <div class="text-muted small">{{ $session->device_id }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($session->last_used_at)
                                                    <span title="{{ $session->last_used_at }}">{{ $session->last_used_at->diffForHumans() }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span title="{{ $session->created_at }}">{{ $session->created_at->diffForHumans() }}</span>
                                            </td>
                                            <td>
                                                @if($session->is_active)
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-secondary">غير نشط</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('biometric.session.delete', $session->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الجهاز؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash-alt"></i> حذف
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center p-5">
                            <div class="mb-4">
                                <i class="fas fa-fingerprint fa-4x text-muted"></i>
                            </div>
                            <h5>لا توجد أجهزة مسجلة</h5>
                            <p class="text-muted">لم تقم بتسجيل أي جهاز للمصادقة البيومترية حتى الآن.</p>
                            <a href="{{ route('biometric.register') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus me-2"></i> إضافة جهاز جديد
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> معلومات عن المصادقة البيومترية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-shield-alt text-primary me-2"></i> الأمان العالي</h6>
                            <p class="text-muted">المصادقة البيومترية أكثر أماناً من كلمات المرور التقليدية وتحمي حسابك من هجمات التصيد.</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-bolt text-primary me-2"></i> سرعة الوصول</h6>
                            <p class="text-muted">تسجيل الدخول بالبصمة أسرع وأسهل من كتابة كلمة المرور في كل مرة.</p>
                        </div>
                        <div class="col-md-6 mt-3">
                            <h6><i class="fas fa-lock text-primary me-2"></i> الخصوصية</h6>
                            <p class="text-muted">بيانات البصمة الخاصة بك لا تغادر جهازك أبداً، ونحن نستخدم فقط المعرفات الآمنة.</p>
                        </div>
                        <div class="col-md-6 mt-3">
                            <h6><i class="fas fa-mobile-alt text-primary me-2"></i> متعدد الأجهزة</h6>
                            <p class="text-muted">يمكنك تسجيل عدة أجهزة للوصول إلى حسابك، مما يوفر المرونة والراحة.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .device-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        border-radius: 50%;
    }
    
    .device-icon i {
        font-size: 1.25rem;
    }
</style>
@endsection
