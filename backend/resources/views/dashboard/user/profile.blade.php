@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2">الملف الشخصي</h1>
            <p class="text-muted">إدارة معلومات حسابك الشخصي وتعديلها</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4 mb-md-0">
            <!-- بطاقة المعلومات الشخصية -->
            <x-card class="border-0 shadow-sm mb-4">
                <div class="text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ asset('assets/images/avatar-placeholder.jpg') }}" alt="صورة المستخدم" class="rounded-circle img-thumbnail" width="120" height="120">
                        <button type="button" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0" data-bs-toggle="modal" data-bs-target="#changeAvatarModal">
                            <i class="bi bi-camera"></i>
                        </button>
                    </div>
                    <h5 class="mb-1">{{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-2">{{ auth()->user()->username }}</p>
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ __('user.regular_user') }}</span>
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#userStatusModal">
                            <i class="bi bi-person-badge me-1"></i> تحديث الحالة
                        </button>
                    </div>
                </div>
                
                <hr>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>
                            <i class="bi bi-envelope text-muted me-2"></i> البريد الإلكتروني
                        </span>
                        <span class="text-muted">{{ auth()->user()->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>
                            <i class="bi bi-telephone text-muted me-2"></i> رقم الهاتف
                        </span>
                        <span class="text-muted">{{ auth()->user()->phone_number ?? 'غير متوفر' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>
                            <i class="bi bi-calendar text-muted me-2"></i> تاريخ الإنضمام
                        </span>
                        <span class="text-muted">{{ auth()->user()->created_at->format('d/m/Y') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>
                            <i class="bi bi-shield-check text-muted me-2"></i> التحقق من KYC
                        </span>
                        <span class="badge {{ auth()->user()->requires_kyc ? 'bg-warning' : 'bg-success' }}">
                            {{ auth()->user()->requires_kyc ? 'مطلوب' : 'تم التحقق' }}
                        </span>
                    </li>
                </ul>
            </x-card>
            
            <!-- روابط سريعة -->
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <h5 class="mb-0">روابط سريعة</h5>
                </x-slot>
                
                <x-list-group class="list-group-flush">
                    <a href="{{ route('user.settings') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-gear text-muted me-2"></i> إعدادات الحساب
                    </a>
                    <a href="{{ route('user.notification-settings') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-bell text-muted me-2"></i> إعدادات الإشعارات
                    </a>
                    <a href="{{ route('user.security') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-shield-lock text-muted me-2"></i> الأمان والخصوصية
                    </a>
                    <a href="{{ route('user.kyc') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-person-badge text-muted me-2"></i> التحقق من الهوية (KYC)
                    </a>
                </x-list-group>
            </x-card>
        </div>
        
        <div class="col-md-8">
            <!-- نموذج تحديث الملف الشخصي -->
            <form action="{{ route('user.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">المعلومات الشخصية</h5>
                    </x-slot>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-form-input
                                id="name"
                                name="name"
                                label="الاسم الكامل"
                                :value="auth()->user()->name"
                                required="true"
                            />
                        </div>
                        
                        <div class="col-md-6">
                            <x-form-input
                                id="username"
                                name="username"
                                label="اسم المستخدم"
                                :value="auth()->user()->username"
                                required="true"
                            />
                        </div>
                        
                        <div class="col-md-6">
                            <x-form-input
                                id="email"
                                name="email"
                                type="email"
                                label="البريد الإلكتروني"
                                :value="auth()->user()->email"
                                required="true"
                                readonly="true"
                                helpText="لا يمكن تغيير البريد الإلكتروني. يرجى التواصل مع الدعم إذا كنت بحاجة لتغييره."
                            />
                        </div>
                        
                        <div class="col-md-6">
                            <x-form-input
                                id="phone_number"
                                name="phone_number"
                                label="رقم الهاتف"
                                :value="auth()->user()->phone_number"
                                placeholder="+966 5xxxxxxxx"
                            />
                        </div>
                        
                        <div class="col-12">
                            <x-form-textarea
                                id="bio"
                                name="bio"
                                label="نبذة شخصية"
                                :value="auth()->user()->bio ?? ''"
                                placeholder="أخبرنا المزيد عنك..."
                                rows="4"
                            />
                        </div>
                    </div>
                    
                    <x-slot name="footer">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> حفظ التغييرات
                            </button>
                        </div>
                    </x-slot>
                </x-card>
            </form>
            
            <!-- معلومات إضافية -->
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <h5 class="mb-0">معلومات الاتصال</h5>
                </x-slot>
                
                <form action="{{ route('user.contact-info.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-form-select
                                id="country"
                                name="country"
                                label="الدولة"
                                :value="auth()->user()->country ?? ''"
                            >
                                <option value="">اختر الدولة</option>
                                <option value="SA" {{ auth()->user()->country === 'SA' ? 'selected' : '' }}>المملكة العربية السعودية</option>
                                <option value="AE" {{ auth()->user()->country === 'AE' ? 'selected' : '' }}>الإمارات العربية المتحدة</option>
                                <option value="EG" {{ auth()->user()->country === 'EG' ? 'selected' : '' }}>مصر</option>
                                <!-- المزيد من الدول -->
                            </x-form-select>
                        </div>
                        
                        <div class="col-md-6">
                            <x-form-input
                                id="city"
                                name="city"
                                label="المدينة"
                                :value="auth()->user()->city ?? ''"
                            />
                        </div>
                        
                        <div class="col-12">
                            <x-form-textarea
                                id="address"
                                name="address"
                                label="العنوان"
                                :value="auth()->user()->address ?? ''"
                                placeholder="أدخل عنوانك الكامل"
                                rows="3"
                            />
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> حفظ معلومات الاتصال
                        </button>
                    </div>
                </form>
            </x-card>
            
            <!-- إعدادات اللغة والمنطقة الزمنية -->
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <h5 class="mb-0">إعدادات التفضيلات</h5>
                </x-slot>
                
                <form action="{{ route('user.preferences.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <x-form-select
                                id="language"
                                name="language"
                                label="اللغة"
                                :value="auth()->user()->language ?? 'ar'"
                            >
                                <option value="ar" {{ (auth()->user()->language ?? 'ar') === 'ar' ? 'selected' : '' }}>العربية</option>
                                <option value="en" {{ (auth()->user()->language ?? 'ar') === 'en' ? 'selected' : '' }}>English</option>
                            </x-form-select>
                        </div>
                        
                        <div class="col-md-6">
                            <x-form-select
                                id="timezone"
                                name="timezone"
                                label="المنطقة الزمنية"
                                :value="auth()->user()->timezone ?? 'Asia/Riyadh'"
                            >
                                <option value="Asia/Riyadh" {{ (auth()->user()->timezone ?? 'Asia/Riyadh') === 'Asia/Riyadh' ? 'selected' : '' }}>الرياض (GMT+3)</option>
                                <option value="Asia/Dubai" {{ (auth()->user()->timezone ?? 'Asia/Riyadh') === 'Asia/Dubai' ? 'selected' : '' }}>دبي (GMT+4)</option>
                                <option value="Africa/Cairo" {{ (auth()->user()->timezone ?? 'Asia/Riyadh') === 'Africa/Cairo' ? 'selected' : '' }}>القاهرة (GMT+2)</option>
                                <!-- المزيد من المناطق الزمنية -->
                            </x-form-select>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> حفظ التفضيلات
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>

<!-- نافذة تغيير الصورة الشخصية -->
<x-modal id="changeAvatarModal" title="تغيير الصورة الشخصية">
    <form action="{{ route('user.avatar.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="text-center mb-3">
            <img src="{{ asset('assets/images/avatar-placeholder.jpg') }}" alt="صورة المستخدم" class="rounded-circle img-thumbnail mb-3" width="150" height="150" id="avatarPreview">
            
            <div class="mb-3">
                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                <div class="form-text">يجب أن تكون الصورة بتنسيق JPG أو PNG وبحجم لا يتجاوز 2MB.</div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-danger" id="removeAvatar">
                <i class="bi bi-trash me-1"></i> إزالة الصورة
            </button>
            <div>
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary">حفظ</button>
            </div>
        </div>
    </form>
</x-modal>

<!-- نافذة تحديث الحالة -->
<x-modal id="userStatusModal" title="تحديث الحالة">
    <form action="{{ route('user.status.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="status" class="form-label">الحالة</label>
            <select class="form-select" id="status" name="status">
                <option value="online">متصل</option>
                <option value="away">غير متواجد</option>
                <option value="busy">مشغول</option>
                <option value="offline">غير متصل</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="status_message" class="form-label">رسالة الحالة (اختياري)</label>
            <input type="text" class="form-control" id="status_message" name="status_message" placeholder="أدخل رسالة الحالة...">
        </div>
        
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-primary">حفظ</button>
        </div>
    </form>
</x-modal>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // معاينة الصورة الشخصية قبل الرفع
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatarPreview');
        
        if (avatarInput && avatarPreview) {
            avatarInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>
@endpush
@endsection
