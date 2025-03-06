@extends('layouts.app')

@section('title', 'إكمال الملف الشخصي')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">إكمال الملف الشخصي</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <p class="text-muted">مرحبًا {{ $user->name }}، يرجى إكمال معلومات ملفك الشخصي للمتابعة</p>
                        
                        <!-- مؤشر التقدم -->
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPercentage }}%;" 
                                 aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <p class="small text-muted">اكتمال الملف الشخصي: {{ $completionPercentage }}%</p>
                    </div>
                    
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profile-form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="from_complete_profile" value="1">
                        
                        <div class="mb-4 text-center">
                            <div class="profile-image-container mb-3">
                                <img src="{{ $user->profile_image ? asset('storage/' . $user->profile_image) : asset('assets/images/default-avatar.png') }}" 
                                     alt="صورة الملف الشخصي" class="rounded-circle" id="profile-image-preview" style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            <div class="mb-3">
                                <label for="profile_image" class="form-label">صورة الملف الشخصي</label>
                                <input type="file" class="form-control @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept="image/*">
                                @error('profile_image')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">اختر صورة شخصية بحجم لا يتجاوز 2 ميجابايت</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="country_id" class="form-label">الدولة <span class="text-danger">*</span></label>
                            <select class="form-select @error('country_id') is-invalid @enderror" id="country_id" name="country_id" required>
                                <option value="">-- اختر الدولة --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id', $user->country_id) == $country->id ? 'selected' : '' }}
                                            data-phone-code="{{ $country->phone_code }}">
                                        {{ $country->name }} ({{ $country->phone_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle me-1"></i> سيتم تعيين المدينة تلقائيًا بناءً على الدولة المختارة.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" id="phone_code"></span>
                                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" 
                                       id="phone_number" name="phone_number" 
                                       value="{{ old('phone_number', $user->phone_number) }}" 
                                       required pattern="[0-9]+" 
                                       placeholder="أدخل رقم الهاتف بدون رمز الدولة">
                                @error('phone_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">أدخل رقم الهاتف بدون رمز الدولة (أرقام فقط)</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">إعدادات الخصوصية</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="privacy_settings[show_online_status]" id="show_online_status" value="1" {{ old('privacy_settings.show_online_status', $user->privacy_settings['show_online_status'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_online_status">
                                            إظهار حالة الاتصال للآخرين
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="privacy_settings[show_read_receipts]" id="show_read_receipts" value="1" {{ old('privacy_settings.show_read_receipts', $user->privacy_settings['show_read_receipts'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show_read_receipts">
                                            إظهار إشعارات القراءة
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">إعدادات الإشعارات</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="notification_settings[message_notifications]" id="message_notifications" value="1" {{ old('notification_settings.message_notifications', $user->notification_settings['message_notifications'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="message_notifications">
                                            إشعارات الرسائل الجديدة
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="notification_settings[call_notifications]" id="call_notifications" value="1" {{ old('notification_settings.call_notifications', $user->notification_settings['call_notifications'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="call_notifications">
                                            إشعارات المكالمات الواردة
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">حفظ المعلومات والمتابعة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Country selection
        const countrySelect = document.getElementById('country_id');
        const phoneCodeSpan = document.getElementById('phone_code');
        const phoneNumberInput = document.getElementById('phone_number');
        const profileForm = document.getElementById('profile-form');
        
        // تحديث رمز الهاتف عند تغيير الدولة
        function updatePhoneCode() {
            const selectedOption = countrySelect.options[countrySelect.selectedIndex];
            if (selectedOption.value) {
                const phoneCode = selectedOption.getAttribute('data-phone-code');
                phoneCodeSpan.textContent = phoneCode;
            } else {
                phoneCodeSpan.textContent = '';
            }
        }
        
        // تحديث رمز الهاتف عند تحميل الصفحة
        updatePhoneCode();
        
        // تحديث رمز الهاتف عند تغيير الدولة
        countrySelect.addEventListener('change', updatePhoneCode);
        
        // التحقق من صحة رقم الهاتف
        phoneNumberInput.addEventListener('input', function() {
            // إزالة أي أحرف غير رقمية
            this.value = this.value.replace(/\D/g, '');
        });
        
        // إضافة مستمعي الأحداث للخانات المطلوبة
        document.querySelectorAll('#profile-form input[required], #profile-form select[required]').forEach(element => {
            element.addEventListener('change', function() {
                if (this.value) {
                    this.classList.remove('is-invalid');
                } else {
                    this.classList.add('is-invalid');
                }
            });
        });
        
        // التحقق من صحة النموذج قبل الإرسال
        profileForm.addEventListener('submit', function(e) {
            console.log('Form submission attempted');
            let isValid = true;
            
            // التحقق من اختيار الدولة
            if (!countrySelect.value) {
                countrySelect.classList.add('is-invalid');
                isValid = false;
                console.log('Country validation failed');
            } else {
                countrySelect.classList.remove('is-invalid');
            }
            
            // التحقق من إدخال رقم الهاتف
            if (!phoneNumberInput.value) {
                phoneNumberInput.classList.add('is-invalid');
                isValid = false;
                console.log('Phone validation failed');
            } else {
                phoneNumberInput.classList.remove('is-invalid');
            }
            
            if (!isValid) {
                e.preventDefault();
                console.log('Form submission prevented due to validation errors');
                
                // إضافة تنبيه للمستخدم
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
                    <strong>تنبيه!</strong> يرجى إكمال جميع الحقول المطلوبة.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                profileForm.insertBefore(alertDiv, profileForm.firstChild);
            } else {
                console.log('Form validation passed, submitting...');
            }
        });
        
        // معاينة صورة الملف الشخصي
        const profileImageInput = document.getElementById('profile_image');
        const profileImagePreview = document.getElementById('profile-image-preview');
        
        profileImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection
