@extends('layouts.dashboard')

@section('title', 'التحقق من الهوية KYC')

@section('page-title', 'التحقق من الهوية (KYC)')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <p class="text-muted">أكمل عملية التحقق من هويتك للوصول إلى جميع ميزات المنصة</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
            <!-- حالة التحقق -->
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <h5 class="mb-0">حالة التحقق</h5>
                </x-slot>
                
                <div class="p-3">
                    @if($kycStatus === 'approved')
                        <div class="text-center mb-3">
                            <div class="verification-icon mb-3">
                                <i class="bi bi-check-circle-fill text-success fs-1"></i>
                            </div>
                            <h5 class="mb-2">تم التحقق</h5>
                            <p class="text-muted mb-0">تم التحقق من هويتك بنجاح</p>
                            <p class="small text-muted mt-2">تاريخ التحقق: {{ $kycVerifiedAt ? $kycVerifiedAt->format('Y-m-d') : 'غير متوفر' }}</p>
                        </div>
                    @elseif($kycStatus === 'pending')
                        <div class="text-center mb-3">
                            <div class="verification-icon mb-3">
                                <i class="bi bi-hourglass-split text-warning fs-1"></i>
                            </div>
                            <h5 class="mb-2">قيد المراجعة</h5>
                            <p class="text-muted mb-0">تم استلام طلب التحقق الخاص بك وهو قيد المراجعة حاليًا</p>
                            <p class="small text-muted mt-2">تاريخ التقديم: {{ $kycSubmittedAt ? $kycSubmittedAt->format('Y-m-d') : 'غير متوفر' }}</p>
                        </div>
                    @elseif($kycStatus === 'rejected')
                        <div class="text-center mb-3">
                            <div class="verification-icon mb-3">
                                <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                            </div>
                            <h5 class="mb-2">مرفوض</h5>
                            <p class="text-muted mb-0">تم رفض طلب التحقق الخاص بك. يرجى مراجعة السبب وإعادة التقديم</p>
                            <p class="small text-muted mt-2">سبب الرفض: {{ $kycRejectionReason ?: 'لم يتم تحديد سبب' }}</p>
                        </div>
                    @else
                        <div class="text-center mb-3">
                            <div class="verification-icon mb-3">
                                <i class="bi bi-exclamation-circle text-muted fs-1"></i>
                            </div>
                            <h5 class="mb-2">غير مكتمل</h5>
                            <p class="text-muted mb-0">لم تكمل عملية التحقق من الهوية بعد</p>
                        </div>
                    @endif
                    
                    <div class="verification-steps mt-4">
                        <h6 class="mb-3">خطوات التحقق:</h6>
                        
                        <div class="step d-flex align-items-center mb-3">
                            <div class="step-icon me-3">
                                <span class="badge rounded-pill {{ $user->kyc_step >= 1 ? 'bg-success' : 'bg-secondary' }}">1</span>
                            </div>
                            <div class="step-content">
                                <h6 class="mb-0">المعلومات الشخصية</h6>
                                <p class="text-muted small mb-0">{{ $user->kyc_step >= 1 ? 'مكتمل' : 'غير مكتمل' }}</p>
                            </div>
                        </div>
                        
                        <div class="step d-flex align-items-center mb-3">
                            <div class="step-icon me-3">
                                <span class="badge rounded-pill {{ $user->kyc_step >= 2 ? 'bg-success' : 'bg-secondary' }}">2</span>
                            </div>
                            <div class="step-content">
                                <h6 class="mb-0">معلومات الاتصال</h6>
                                <p class="text-muted small mb-0">{{ $user->kyc_step >= 2 ? 'مكتمل' : 'غير مكتمل' }}</p>
                            </div>
                        </div>
                        
                        <div class="step d-flex align-items-center mb-3">
                            <div class="step-icon me-3">
                                <span class="badge rounded-pill {{ $user->kyc_step >= 3 ? 'bg-success' : 'bg-secondary' }}">3</span>
                            </div>
                            <div class="step-content">
                                <h6 class="mb-0">وثائق الهوية</h6>
                                <p class="text-muted small mb-0">{{ $user->kyc_step >= 3 ? 'مكتمل' : 'غير مكتمل' }}</p>
                            </div>
                        </div>
                        
                        <div class="step d-flex align-items-center">
                            <div class="step-icon me-3">
                                <span class="badge rounded-pill {{ $user->kyc_step >= 4 ? 'bg-success' : 'bg-secondary' }}">4</span>
                            </div>
                            <div class="step-content">
                                <h6 class="mb-0">التحقق النهائي</h6>
                                <p class="text-muted small mb-0">{{ $user->kyc_step >= 4 ? 'مكتمل' : 'غير مكتمل' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
            
            <!-- معلومات التحقق -->
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <h5 class="mb-0">لماذا التحقق من الهوية؟</h5>
                </x-slot>
                
                <div class="p-3">
                    <div class="mb-3">
                        <h6><i class="bi bi-shield-check text-primary me-2"></i> الأمان</h6>
                        <p class="text-muted small">يساعد التحقق من الهوية في حماية حسابك ومعاملاتك من الاحتيال</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="bi bi-unlock text-primary me-2"></i> الوصول الكامل</h6>
                        <p class="text-muted small">الوصول إلى جميع ميزات المنصة بما في ذلك المعاملات ذات القيمة العالية</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6><i class="bi bi-check2-circle text-primary me-2"></i> الامتثال</h6>
                        <p class="text-muted small">الامتثال للوائح المالية والقانونية المحلية والدولية</p>
                    </div>
                    
                    <div>
                        <h6><i class="bi bi-people text-primary me-2"></i> الثقة</h6>
                        <p class="text-muted small">بناء الثقة مع المستخدمين الآخرين على المنصة</p>
                    </div>
                </div>
            </x-card>
        </div>
        
        <div class="col-lg-8">
            @if($kycStatus === 'approved')
                <!-- تم التحقق بالفعل -->
                <x-card class="border-0 shadow-sm">
                    <x-slot name="header">
                        <h5 class="mb-0">معلومات التحقق</h5>
                    </x-slot>
                    
                    <div class="p-3">
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle-fill me-2"></i> تم التحقق من هويتك بنجاح. يمكنك الآن الوصول إلى جميع ميزات المنصة.
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <h6>المعلومات الشخصية</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td class="text-muted">الاسم الكامل</td>
                                        <td>{{ $user->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">تاريخ الميلاد</td>
                                        <td>{{ $user->date_of_birth }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">الجنسية</td>
                                        <td>{{ $user->nationality }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6>معلومات الاتصال</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td class="text-muted">البريد الإلكتروني</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">رقم الهاتف</td>
                                        <td>{{ $user->phone_number }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">العنوان</td>
                                        <td>{{ $user->address }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <p class="text-muted small">إذا كنت بحاجة إلى تحديث معلوماتك، يرجى التواصل مع فريق الدعم.</p>
                        </div>
                    </div>
                </x-card>
            @elseif($kycStatus === 'pending')
                <!-- قيد المراجعة -->
                <x-card class="border-0 shadow-sm">
                    <x-slot name="header">
                        <h5 class="mb-0">طلب التحقق قيد المراجعة</h5>
                    </x-slot>
                    
                    <div class="p-3">
                        <div class="alert alert-warning">
                            <i class="bi bi-hourglass-split me-2"></i> طلب التحقق الخاص بك قيد المراجعة حاليًا. سنخطرك عبر البريد الإلكتروني بمجرد اكتمال المراجعة.
                        </div>
                        
                        <div class="mt-4">
                            <h6>الوقت المتوقع للمراجعة</h6>
                            <p>عادة ما تستغرق عملية المراجعة من 1-3 أيام عمل. نقدر صبرك.</p>
                            
                            <h6 class="mt-4">هل تحتاج إلى مساعدة؟</h6>
                            <p>إذا كان لديك أي أسئلة أو استفسارات حول عملية التحقق، يرجى التواصل مع فريق الدعم.</p>
                            
                            <div class="d-grid mt-4">
                                <a href="{{ route('contact') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-headset me-2"></i> التواصل مع الدعم
                                </a>
                            </div>
                        </div>
                    </div>
                </x-card>
            @elseif($kycStatus === 'rejected')
                <!-- تم الرفض -->
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">طلب التحقق مرفوض</h5>
                    </x-slot>
                    
                    <div class="p-3">
                        <div class="alert alert-danger">
                            <i class="bi bi-x-circle-fill me-2"></i> تم رفض طلب التحقق الخاص بك. يرجى مراجعة السبب أدناه وإعادة التقديم.
                        </div>
                        
                        <div class="mt-4">
                            <h6>سبب الرفض</h6>
                            <p>{{ $kycRejectionReason ?: 'لم يتم تحديد سبب محدد. يرجى التأكد من أن جميع المعلومات والوثائق المقدمة صحيحة وواضحة.' }}</p>
                            
                            <div class="d-grid mt-4">
                                <a href="#kyc-form" class="btn btn-primary">
                                    <i class="bi bi-arrow-repeat me-2"></i> إعادة التقديم
                                </a>
                            </div>
                        </div>
                    </div>
                </x-card>
                
                <!-- نموذج إعادة التقديم -->
                <div id="kyc-form">
                    @include('dashboard.user.partials.kyc-form')
                </div>
            @else
                <!-- نموذج التحقق -->
                <div id="kyc-form">
                    @include('dashboard.user.partials.kyc-form')
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
