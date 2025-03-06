@extends('layouts.app')

@section('title', 'الأمان والخصوصية')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2">الأمان والخصوصية</h1>
            <p class="text-muted">إدارة إعدادات الأمان والخصوصية لحسابك</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4 mb-md-0">
            <!-- قائمة الأمان -->
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <h5 class="mb-0">قائمة الأمان</h5>
                </x-slot>
                
                <x-list-group class="list-group-flush">
                    <a href="#password-section" class="list-group-item list-group-item-action d-flex align-items-center active">
                        <i class="bi bi-key me-2"></i> كلمة المرور
                    </a>
                    <a href="#two-factor-section" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-shield-lock me-2"></i> المصادقة الثنائية
                    </a>
                    <a href="#login-history-section" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-clock-history me-2"></i> سجل تسجيل الدخول
                    </a>
                    <a href="#connected-devices-section" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-laptop me-2"></i> الأجهزة المتصلة
                    </a>
                    <a href="#privacy-section" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-eye-slash me-2"></i> إعدادات الخصوصية
                    </a>
                </x-list-group>
            </x-card>
            
            <!-- نصائح الأمان -->
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <h5 class="mb-0">نصائح الأمان</h5>
                </x-slot>
                
                <div class="p-1">
                    <div class="d-flex align-items-start mb-3">
                        <i class="bi bi-shield-check text-success fs-4 me-2"></i>
                        <div>
                            <h6 class="mb-1">استخدم كلمة مرور قوية</h6>
                            <p class="text-muted small mb-0">استخدم مزيجًا من الأحرف والأرقام والرموز</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3">
                        <i class="bi bi-shield-check text-success fs-4 me-2"></i>
                        <div>
                            <h6 class="mb-1">فعّل المصادقة الثنائية</h6>
                            <p class="text-muted small mb-0">لحماية إضافية، قم بتفعيل المصادقة الثنائية</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-3">
                        <i class="bi bi-shield-check text-success fs-4 me-2"></i>
                        <div>
                            <h6 class="mb-1">تحقق من الأجهزة المتصلة</h6>
                            <p class="text-muted small mb-0">راجع قائمة الأجهزة المتصلة بحسابك بانتظام</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start">
                        <i class="bi bi-shield-check text-success fs-4 me-2"></i>
                        <div>
                            <h6 class="mb-1">تحديث معلومات الاتصال</h6>
                            <p class="text-muted small mb-0">تأكد من تحديث بريدك الإلكتروني ورقم هاتفك</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
        
        <div class="col-md-8">
            <!-- قسم كلمة المرور -->
            <section id="password-section" class="security-section">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">تغيير كلمة المرور</h5>
                    </x-slot>
                    
                    <form action="{{ route('user.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">كلمة المرور الحالية</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور الجديدة</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="form-text">يجب أن تتكون كلمة المرور من 8 أحرف على الأقل وتحتوي على حرف كبير وحرف صغير ورقم ورمز خاص</div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="password-strength mb-3">
                            <label class="form-label">قوة كلمة المرور</label>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 0%" id="password-strength-bar"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="text-muted small">ضعيفة</span>
                                <span class="text-muted small">متوسطة</span>
                                <span class="text-muted small">قوية</span>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> تحديث كلمة المرور
                            </button>
                        </div>
                    </form>
                </x-card>
            </section>
            
            <!-- قسم المصادقة الثنائية -->
            <section id="two-factor-section" class="security-section d-none">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">المصادقة الثنائية</h5>
                    </x-slot>
                    
                    <div class="two-factor-status mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="me-3">
                                <i class="bi bi-shield-lock fs-1 {{ auth()->user()->two_factor_enabled ? 'text-success' : 'text-muted' }}"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">المصادقة الثنائية {{ auth()->user()->two_factor_enabled ? 'مفعلة' : 'غير مفعلة' }}</h6>
                                <p class="text-muted mb-0">{{ auth()->user()->two_factor_enabled ? 'حسابك محمي بطبقة أمان إضافية.' : 'قم بتفعيل المصادقة الثنائية لتعزيز أمان حسابك.' }}</p>
                            </div>
                        </div>
                        
                        @if(auth()->user()->two_factor_enabled)
                            <div class="mb-3">
                                <p>تم تفعيل المصادقة الثنائية لحسابك. في كل مرة تقوم فيها بتسجيل الدخول من جهاز غير موثوق، سيُطلب منك إدخال رمز تحقق.</p>
                                
                                <div class="alert alert-info">
                                    <h6 class="alert-heading"><i class="bi bi-info-circle me-1"></i> رموز الاسترداد</h6>
                                    <p class="mb-0">احتفظ برموز الاسترداد التالية في مكان آمن. يمكنك استخدامها لاستعادة الوصول إلى حسابك في حالة فقدان جهازك.</p>
                                </div>
                                
                                @if(isset($recoveryCodes) && count($recoveryCodes) > 0)
                                    <div class="recovery-codes mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">رموز الاسترداد</h6>
                                            <form action="{{ route('security.two-factor.regenerate-codes') }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-arrow-repeat me-1"></i> إعادة إنشاء الرموز
                                                </button>
                                            </form>
                                        </div>
                                        <div class="recovery-codes-container bg-light p-3 rounded">
                                            <div class="row row-cols-1 row-cols-md-2 g-2">
                                                @foreach($recoveryCodes as $code)
                                                    <div class="col">
                                                        <code>{{ $code }}</code>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <form action="{{ route('security.two-factor.disable') }}" method="POST" class="mt-3">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="disable_2fa_password" class="form-label">كلمة المرور الحالية</label>
                                        <input type="password" class="form-control" id="disable_2fa_password" name="password" required>
                                        @error('disable_2fa_password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-shield-x me-1"></i> تعطيل المصادقة الثنائية
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="mb-3">
                                <p>المصادقة الثنائية هي طبقة إضافية من الأمان لحسابك. عند تفعيلها، ستحتاج إلى إدخال رمز تحقق من تطبيق المصادقة بالإضافة إلى كلمة المرور عند تسجيل الدخول.</p>
                                
                                <div class="alert alert-warning">
                                    <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-1"></i> تنبيه هام</h6>
                                    <p class="mb-0">ستحتاج إلى تثبيت تطبيق مصادقة مثل Google Authenticator أو Microsoft Authenticator على هاتفك لاستخدام هذه الميزة.</p>
                                </div>
                                
                                <a href="{{ route('security.two-factor.setup') }}" class="btn btn-primary">
                                    <i class="bi bi-shield-check me-1"></i> تفعيل المصادقة الثنائية
                                </a>
                            </div>
                        @endif
                    </div>
                    
                    <hr>
                    
                    <div class="trusted-devices">
                        <h6 class="mb-3">الأجهزة الموثوقة</h6>
                        <p class="text-muted">يمكنك تعيين أجهزة معينة كأجهزة موثوقة. لن يُطلب منك إدخال رمز المصادقة الثنائية عند تسجيل الدخول من هذه الأجهزة.</p>
                        
                        <a href="{{ route('security.devices') }}" class="btn btn-outline-primary">
                            <i class="bi bi-laptop me-1"></i> إدارة الأجهزة الموثوقة
                        </a>
                    </div>
                </x-card>
            </section>
            
            <!-- قسم سجل تسجيل الدخول -->
            <section id="login-history-section" class="security-section d-none">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">سجل تسجيل الدخول</h5>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshLoginHistory">
                                <i class="bi bi-arrow-clockwise me-1"></i> تحديث
                            </button>
                        </div>
                    </x-slot>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>التاريخ والوقت</th>
                                    <th>الجهاز</th>
                                    <th>المتصفح</th>
                                    <th>الموقع</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ now()->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <i class="bi bi-laptop me-1"></i> الجهاز الحالي
                                    </td>
                                    <td>Chrome 98.0</td>
                                    <td>الرياض، المملكة العربية السعودية</td>
                                    <td><span class="badge bg-success">ناجح</span></td>
                                </tr>
                                <!-- يمكن إضافة المزيد من السجلات هنا -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <p class="text-muted small">يتم عرض آخر 10 عمليات تسجيل دخول فقط</p>
                    </div>
                </x-card>
            </section>
            
            <!-- قسم الأجهزة المتصلة -->
            <section id="connected-devices-section" class="security-section d-none">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">الأجهزة المتصلة</h5>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="refreshDevices">
                                <i class="bi bi-arrow-clockwise me-1"></i> تحديث
                            </button>
                        </div>
                    </x-slot>
                    
                    <div class="devices-list">
                        <div class="device-item p-3 border-bottom position-relative">
                            <div class="d-flex">
                                <div class="device-icon me-3">
                                    <i class="bi bi-laptop fs-3"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">الجهاز الحالي <span class="badge bg-primary ms-2">حالي</span></h6>
                                            <p class="text-muted small mb-1">Chrome على Windows</p>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                                                <i class="bi bi-x-circle me-1"></i> إنهاء الجلسة
                                            </button>
                                        </div>
                                    </div>
                                    <div class="device-details">
                                        <span class="text-muted small me-3">
                                            <i class="bi bi-geo-alt me-1"></i> الرياض، المملكة العربية السعودية
                                        </span>
                                        <span class="text-muted small me-3">
                                            <i class="bi bi-calendar me-1"></i> آخر نشاط: {{ now()->format('Y-m-d H:i') }}
                                        </span>
                                        <span class="text-muted small">
                                            <i class="bi bi-clock me-1"></i> تسجيل الدخول: {{ now()->subHours(2)->format('Y-m-d H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- يمكن إضافة المزيد من الأجهزة هنا -->
                    </div>
                    
                    <div class="p-3">
                        <div class="d-grid">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutAllDevicesModal">
                                <i class="bi bi-power me-1"></i> تسجيل الخروج من جميع الأجهزة الأخرى
                            </button>
                        </div>
                    </div>
                </x-card>
            </section>
            
            <!-- قسم إعدادات الخصوصية -->
            <section id="privacy-section" class="security-section d-none">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">إعدادات الخصوصية</h5>
                    </x-slot>
                    
                    <form action="{{ route('user.privacy.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <h6 class="mb-3">خصوصية الملف الشخصي</h6>
                        
                        <div class="mb-3">
                            <label for="profile_visibility" class="form-label">من يمكنه رؤية ملفك الشخصي؟</label>
                            <select class="form-select" id="profile_visibility" name="profile_visibility">
                                <option value="public">الجميع</option>
                                <option value="registered" selected>المستخدمون المسجلون فقط</option>
                                <option value="private">أنا فقط</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="activity_visibility" class="form-label">من يمكنه رؤية نشاطاتك؟</label>
                            <select class="form-select" id="activity_visibility" name="activity_visibility">
                                <option value="public">الجميع</option>
                                <option value="registered" selected>المستخدمون المسجلون فقط</option>
                                <option value="private">أنا فقط</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="show_online_status" name="show_online_status" checked>
                                <label class="form-check-label" for="show_online_status">إظهار حالة الاتصال</label>
                            </div>
                            <div class="form-text">السماح للآخرين بمعرفة ما إذا كنت متصلاً حالياً</div>
                        </div>
                        
                        <h6 class="mb-3">البيانات والخصوصية</h6>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="allow_search_engines" name="allow_search_engines">
                                <label class="form-check-label" for="allow_search_engines">السماح لمحركات البحث بفهرسة ملفك الشخصي</label>
                            </div>
                            <div class="form-text">السماح لمحركات البحث مثل Google بعرض ملفك الشخصي في نتائج البحث</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="data_collection" name="data_collection" checked>
                                <label class="form-check-label" for="data_collection">جمع بيانات الاستخدام</label>
                            </div>
                            <div class="form-text">السماح لنا بجمع بيانات استخدامك لتحسين خدماتنا</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="personalized_ads" name="personalized_ads" checked>
                                <label class="form-check-label" for="personalized_ads">الإعلانات المخصصة</label>
                            </div>
                            <div class="form-text">السماح بعرض إعلانات مخصصة بناءً على اهتماماتك</div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('user.data.export') }}" class="btn btn-outline-primary">
                                <i class="bi bi-download me-1"></i> تصدير بياناتي
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </x-card>
            </section>
        </div>
    </div>
</div>

<!-- نافذة تسجيل الخروج من جميع الأجهزة -->
<x-modal id="logoutAllDevicesModal" title="تسجيل الخروج من جميع الأجهزة">
    <div class="text-center mb-4">
        <i class="bi bi-exclamation-triangle text-warning fs-1 d-block mb-3"></i>
        <h5>هل أنت متأكد من رغبتك في تسجيل الخروج من جميع الأجهزة الأخرى؟</h5>
        <p class="text-muted">سيتم إنهاء جميع جلسات تسجيل الدخول النشطة باستثناء الجلسة الحالية.</p>
    </div>
    
    <form action="{{ route('user.devices.logout-all') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label for="password_confirm" class="form-label">كلمة المرور</label>
            <input type="password" class="form-control" id="password_confirm" name="password" required>
            <div class="form-text">يرجى إدخال كلمة المرور الحالية للتأكيد</div>
        </div>
        
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-danger">تسجيل الخروج من جميع الأجهزة</button>
        </div>
    </form>
</x-modal>

@push('styles')
<style>
    .security-section:not(.d-none) {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // التبديل بين أقسام الأمان
        const securityLinks = document.querySelectorAll('.list-group a');
        const securitySections = document.querySelectorAll('.security-section');
        
        securityLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // إزالة الفئة النشطة من جميع الروابط
                securityLinks.forEach(item => {
                    item.classList.remove('active');
                });
                
                // إضافة الفئة النشطة للرابط المحدد
                this.classList.add('active');
                
                // إخفاء جميع الأقسام
                securitySections.forEach(section => {
                    section.classList.add('d-none');
                });
                
                // إظهار القسم المطلوب
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.remove('d-none');
                }
            });
        });
        
        // مقياس قوة كلمة المرور
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('password-strength-bar');
        
        if (passwordInput && strengthBar) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                if (password.length >= 8) strength += 20;
                if (password.match(/[a-z]+/)) strength += 20;
                if (password.match(/[A-Z]+/)) strength += 20;
                if (password.match(/[0-9]+/)) strength += 20;
                if (password.match(/[^a-zA-Z0-9]+/)) strength += 20;
                
                strengthBar.style.width = strength + '%';
                
                if (strength < 40) {
                    strengthBar.className = 'progress-bar bg-danger';
                } else if (strength < 80) {
                    strengthBar.className = 'progress-bar bg-warning';
                } else {
                    strengthBar.className = 'progress-bar bg-success';
                }
            });
        }
    });
</script>
@endpush
@endsection