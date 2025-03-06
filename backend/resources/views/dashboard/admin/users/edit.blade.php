@extends('layouts.admin')

@section('title', 'تعديل المستخدم - لوحة تحكم المشرف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">تعديل المستخدم</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">المستخدمين</a></li>
                        <li class="breadcrumb-item active">تعديل المستخدم</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <h4 class="card-title flex-grow-1">معلومات المستخدم</h4>
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm me-2">
                                <i class="fas fa-eye me-1"></i> عرض الملف الشخصي
                            </a>
                        </div>
                    </div>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">الدور <span class="text-danger">*</span></label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="">اختر الدور</option>
                                        <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>عميل</option>
                                        <option value="merchant" {{ old('role', $user->role) == 'merchant' ? 'selected' : '' }}>تاجر</option>
                                        <option value="agent" {{ old('role', $user->role) == 'agent' ? 'selected' : '' }}>وكيل</option>
                                        <option value="messenger" {{ old('role', $user->role) == 'messenger' ? 'selected' : '' }}>مندوب</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>مشرف</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">كلمة المرور <small class="text-muted">(اتركها فارغة إذا لم ترغب في تغييرها)</small></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="merchant-fields" style="display: {{ $user->role == 'merchant' ? 'flex' : 'none' }};">
                            <div class="col-12">
                                <h5 class="mt-3 mb-3">معلومات التاجر</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="store_name" class="form-label">اسم المتجر</label>
                                    <input type="text" class="form-control" id="store_name" name="store_name" value="{{ old('store_name', $user->merchant->store_name ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="store_type" class="form-label">نوع المتجر</label>
                                    <select class="form-select" id="store_type" name="store_type">
                                        <option value="">اختر نوع المتجر</option>
                                        <option value="retail" {{ old('store_type', $user->merchant->store_type ?? '') == 'retail' ? 'selected' : '' }}>تجزئة</option>
                                        <option value="wholesale" {{ old('store_type', $user->merchant->store_type ?? '') == 'wholesale' ? 'selected' : '' }}>جملة</option>
                                        <option value="service" {{ old('store_type', $user->merchant->store_type ?? '') == 'service' ? 'selected' : '' }}>خدمات</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" id="messenger-fields" style="display: {{ $user->role == 'messenger' ? 'flex' : 'none' }};">
                            <div class="col-12">
                                <h5 class="mt-3 mb-3">معلومات المندوب</h5>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vehicle_type" class="form-label">نوع المركبة</label>
                                    <select class="form-select" id="vehicle_type" name="vehicle_type">
                                        <option value="">اختر نوع المركبة</option>
                                        <option value="motorcycle" {{ old('vehicle_type', $user->messenger->vehicle_type ?? '') == 'motorcycle' ? 'selected' : '' }}>دراجة نارية</option>
                                        <option value="car" {{ old('vehicle_type', $user->messenger->vehicle_type ?? '') == 'car' ? 'selected' : '' }}>سيارة</option>
                                        <option value="bicycle" {{ old('vehicle_type', $user->messenger->vehicle_type ?? '') == 'bicycle' ? 'selected' : '' }}>دراجة هوائية</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vehicle_plate" class="form-label">رقم اللوحة</label>
                                    <input type="text" class="form-control" id="vehicle_plate" name="vehicle_plate" value="{{ old('vehicle_plate', $user->messenger->vehicle_plate ?? '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="country" class="form-label">الدولة</label>
                                    <select class="form-select" id="country" name="country">
                                        <option value="">اختر الدولة</option>
                                        <option value="SD" {{ old('country', $user->country ?? '') == 'SD' ? 'selected' : '' }}>السودان</option>
                                        <!-- يمكن إضافة المزيد من الدول هنا -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">المدينة</label>
                                    <select class="form-select" id="city" name="city">
                                        <option value="">اختر المدينة</option>
                                        <!-- سيتم تحميل المدن بناءً على الدولة المختارة -->
                                        @if(old('country', $user->country ?? '') == 'SD')
                                            @php
                                                $sudanCities = [
                                                    'الخرطوم', 'أم درمان', 'بحري', 'بورتسودان', 'ود مدني', 'الأبيض', 'نيالا',
                                                    'الفاشر', 'كسلا', 'القضارف', 'عطبرة', 'دنقلا', 'الدمازين', 'سنار', 'كوستي'
                                                ];
                                            @endphp
                                            @foreach($sudanCities as $city)
                                                <option value="{{ $city }}" {{ old('city', $user->city ?? '') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="profile_photo" class="form-label">صورة الملف الشخصي</label>
                                    <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                                    @if($user->profile_photo_path)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="صورة الملف الشخصي" class="rounded avatar-lg">
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" id="remove_photo" name="remove_photo" value="1">
                                                <label class="form-check-label" for="remove_photo">
                                                    إزالة الصورة الحالية
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label d-block">الحالة</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_active" id="active" value="1" {{ old('is_active', $user->is_active) == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="active">نشط</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_active" id="inactive" value="0" {{ old('is_active', $user->is_active) == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="inactive">غير نشط</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label d-block">حالة الحظر</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_blocked" id="not_blocked" value="0" {{ old('is_blocked', $user->is_blocked) == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="not_blocked">غير محظور</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_blocked" id="blocked" value="1" {{ old('is_blocked', $user->is_blocked) == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="blocked">محظور</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">العنوان</label>
                                    <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <a href="{{ route('admin.users') }}" class="btn btn-secondary me-2">إلغاء</a>
                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // إظهار/إخفاء حقول التاجر والمندوب بناءً على الدور المحدد
        const roleSelect = document.getElementById('role');
        const merchantFields = document.getElementById('merchant-fields');
        const messengerFields = document.getElementById('messenger-fields');

        roleSelect.addEventListener('change', function() {
            if (this.value === 'merchant') {
                merchantFields.style.display = 'flex';
                messengerFields.style.display = 'none';
            } else if (this.value === 'messenger') {
                merchantFields.style.display = 'none';
                messengerFields.style.display = 'flex';
            } else {
                merchantFields.style.display = 'none';
                messengerFields.style.display = 'none';
            }
        });

        // تحميل المدن بناءً على الدولة المختارة
        const countrySelect = document.getElementById('country');
        const citySelect = document.getElementById('city');
        const currentCity = "{{ old('city', $user->city ?? '') }}";

        countrySelect.addEventListener('change', function() {
            citySelect.innerHTML = '<option value="">اختر المدينة</option>';
            
            if (this.value === 'SD') {
                const sudanCities = [
                    'الخرطوم', 'أم درمان', 'بحري', 'بورتسودان', 'ود مدني', 'الأبيض', 'نيالا',
                    'الفاشر', 'كسلا', 'القضارف', 'عطبرة', 'دنقلا', 'الدمازين', 'سنار', 'كوستي'
                ];
                
                sudanCities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    if (city === currentCity) {
                        option.selected = true;
                    }
                    citySelect.appendChild(option);
                });
            }
            // يمكن إضافة المزيد من الدول والمدن هنا
        });

        // إظهار/إخفاء كلمة المرور
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
        
        const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
        const passwordConfirmation = document.getElementById('password_confirmation');
        
        togglePasswordConfirmation.addEventListener('click', function() {
            const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmation.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
</script>
@endsection
