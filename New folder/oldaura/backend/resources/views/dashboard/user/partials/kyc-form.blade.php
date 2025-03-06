<x-card class="border-0 shadow-sm mb-4">
    <x-slot name="header">
        <h5 class="mb-0">نموذج التحقق من الهوية</h5>
    </x-slot>
    
    <form action="{{ route('user.kyc.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- الخطوة 1: المعلومات الشخصية -->
        <div class="kyc-step" id="step-1">
            <h5 class="border-bottom pb-2 mb-4">الخطوة 1: المعلومات الشخصية</h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="full_name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="{{ old('full_name', auth()->user()->full_name) }}" required>
                    @error('full_name')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="date_of_birth" class="form-label">تاريخ الميلاد <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', auth()->user()->date_of_birth) }}" required>
                    @error('date_of_birth')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nationality" class="form-label">الجنسية <span class="text-danger">*</span></label>
                    <select class="form-select" id="nationality" name="nationality" required>
                        <option value="">اختر الجنسية...</option>
                        <option value="SA" {{ old('nationality', auth()->user()->nationality) == 'SA' ? 'selected' : '' }}>المملكة العربية السعودية</option>
                        <option value="SD" {{ old('nationality', auth()->user()->nationality) == 'SD' ? 'selected' : '' }}>السودان</option>
                        <option value="KW" {{ old('nationality', auth()->user()->nationality) == 'KW' ? 'selected' : '' }}>الكويت</option>
                        <option value="BH" {{ old('nationality', auth()->user()->nationality) == 'BH' ? 'selected' : '' }}>البحرين</option>
                        <option value="QA" {{ old('nationality', auth()->user()->nationality) == 'QA' ? 'selected' : '' }}>قطر</option>
                        <option value="OM" {{ old('nationality', auth()->user()->nationality) == 'OM' ? 'selected' : '' }}>عمان</option>
                        <option value="EG" {{ old('nationality', auth()->user()->nationality) == 'EG' ? 'selected' : '' }}>مصر</option>
                        <option value="JO" {{ old('nationality', auth()->user()->nationality) == 'JO' ? 'selected' : '' }}>الأردن</option>
                        <!-- يمكن إضافة المزيد من الدول حسب الحاجة -->
                    </select>
                    @error('nationality')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label">الجنس <span class="text-danger">*</span></label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="">اختر الجنس...</option>
                        <option value="male" {{ old('gender', auth()->user()->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ old('gender', auth()->user()->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                    @error('gender')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-primary next-step" data-step="1">
                    التالي <i class="bi bi-arrow-left ms-1"></i>
                </button>
            </div>
        </div>
        
        <!-- الخطوة 2: معلومات الاتصال -->
        <div class="kyc-step d-none" id="step-2">
            <h5 class="border-bottom pb-2 mb-4">الخطوة 2: معلومات الاتصال</h5>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="phone_number" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}" required>
                    <div class="form-text">يرجى إدخال رقم الهاتف مع رمز الدولة، مثال: +966501234567</div>
                    @error('phone_number')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">العنوان <span class="text-danger">*</span></label>
                <textarea class="form-control" id="address" name="address" rows="2" required>{{ old('address', auth()->user()->address) }}</textarea>
                @error('address')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="city" class="form-label">المدينة <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city', auth()->user()->city) }}" required>
                    @error('city')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="country" class="form-label">الدولة <span class="text-danger">*</span></label>
                    <select class="form-select" id="country" name="country" required>
                        <option value="">اختر الدولة...</option>
                        <option value="SA" {{ old('country', auth()->user()->country) == 'SA' ? 'selected' : '' }}>المملكة العربية السعودية</option>
                        <option value="AE" {{ old('country', auth()->user()->country) == 'AE' ? 'selected' : '' }}>الإمارات العربية المتحدة</option>
                        <option value="KW" {{ old('country', auth()->user()->country) == 'KW' ? 'selected' : '' }}>الكويت</option>
                        <option value="BH" {{ old('country', auth()->user()->country) == 'BH' ? 'selected' : '' }}>البحرين</option>
                        <option value="QA" {{ old('country', auth()->user()->country) == 'QA' ? 'selected' : '' }}>قطر</option>
                        <option value="OM" {{ old('country', auth()->user()->country) == 'OM' ? 'selected' : '' }}>عمان</option>
                        <option value="EG" {{ old('country', auth()->user()->country) == 'EG' ? 'selected' : '' }}>مصر</option>
                        <option value="JO" {{ old('country', auth()->user()->country) == 'JO' ? 'selected' : '' }}>الأردن</option>
                        <!-- يمكن إضافة المزيد من الدول حسب الحاجة -->
                    </select>
                    @error('country')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary prev-step" data-step="2">
                    <i class="bi bi-arrow-right me-1"></i> السابق
                </button>
                <button type="button" class="btn btn-primary next-step" data-step="2">
                    التالي <i class="bi bi-arrow-left ms-1"></i>
                </button>
            </div>
        </div>
        
        <!-- الخطوة 3: وثائق الهوية -->
        <div class="kyc-step d-none" id="step-3">
            <h5 class="border-bottom pb-2 mb-4">الخطوة 3: وثائق الهوية</h5>
            
            <div class="mb-4">
                <label for="id_type" class="form-label">نوع الهوية <span class="text-danger">*</span></label>
                <select class="form-select" id="id_type" name="id_type" required>
                    <option value="">اختر نوع الهوية...</option>
                    <option value="national_id" {{ old('id_type') == 'national_id' ? 'selected' : '' }}>بطاقة الهوية الوطنية</option>
                    <option value="passport" {{ old('id_type') == 'passport' ? 'selected' : '' }}>جواز السفر</option>
                    <option value="residence" {{ old('id_type') == 'residence' ? 'selected' : '' }}>الإقامة</option>
                </select>
                @error('id_type')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="id_number" class="form-label">رقم الهوية <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="id_number" name="id_number" value="{{ old('id_number') }}" required>
                @error('id_number')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="id_expiry" class="form-label">تاريخ انتهاء الهوية <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="id_expiry" name="id_expiry" value="{{ old('id_expiry') }}" required>
                @error('id_expiry')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label for="id_front" class="form-label">صورة الهوية (الوجه الأمامي) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="id_front" name="id_front" accept="image/*" required>
                    <div class="form-text">الصيغ المقبولة: JPG، PNG، PDF. الحد الأقصى للحجم: 5 ميجابايت</div>
                    @error('id_front')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-4">
                    <label for="id_back" class="form-label">صورة الهوية (الوجه الخلفي) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="id_back" name="id_back" accept="image/*" required>
                    <div class="form-text">الصيغ المقبولة: JPG، PNG، PDF. الحد الأقصى للحجم: 5 ميجابايت</div>
                    @error('id_back')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-4">
                <label for="selfie" class="form-label">صورة شخصية (سيلفي) <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="selfie" name="selfie" accept="image/*" required>
                <div class="form-text">يرجى التقاط صورة واضحة لوجهك مع إمكانية رؤية ملامح الوجه بوضوح</div>
                @error('selfie')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary prev-step" data-step="3">
                    <i class="bi bi-arrow-right me-1"></i> السابق
                </button>
                <button type="button" class="btn btn-primary next-step" data-step="3">
                    التالي <i class="bi bi-arrow-left ms-1"></i>
                </button>
            </div>
        </div>
        
        <!-- الخطوة 4: التحقق النهائي -->
        <div class="kyc-step d-none" id="step-4">
            <h5 class="border-bottom pb-2 mb-4">الخطوة 4: التحقق النهائي</h5>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle-fill me-2"></i> يرجى مراجعة جميع المعلومات المقدمة قبل الإرسال. بعد الإرسال، لن تتمكن من تعديل المعلومات حتى تتم مراجعتها.
            </div>
            
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirm_accuracy" name="confirm_accuracy" required>
                    <label class="form-check-label" for="confirm_accuracy">
                        أؤكد أن جميع المعلومات والوثائق المقدمة صحيحة ودقيقة
                    </label>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms_agreement" name="terms_agreement" required>
                    <label class="form-check-label" for="terms_agreement">
                        أوافق على <a href="{{ route('terms') }}" target="_blank">شروط وأحكام</a> عملية التحقق من الهوية وأفهم أن تقديم معلومات غير صحيحة قد يؤدي إلى رفض الطلب
                    </label>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="data_processing" name="data_processing" required>
                    <label class="form-check-label" for="data_processing">
                        أوافق على معالجة بياناتي الشخصية وفقًا <a href="{{ route('privacy') }}" target="_blank">لسياسة الخصوصية</a>
                    </label>
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-outline-secondary prev-step" data-step="4">
                    <i class="bi bi-arrow-right me-1"></i> السابق
                </button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check2-circle me-1"></i> إرسال طلب التحقق
                </button>
            </div>
        </div>
    </form>
</x-card>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // التنقل بين خطوات نموذج التحقق
        const nextButtons = document.querySelectorAll('.next-step');
        const prevButtons = document.querySelectorAll('.prev-step');
        const kycSteps = document.querySelectorAll('.kyc-step');
        
        // زر الانتقال للخطوة التالية
        nextButtons.forEach(button => {
            button.addEventListener('click', function() {
                const currentStep = parseInt(this.getAttribute('data-step'));
                const nextStep = currentStep + 1;
                
                // التحقق من صحة الحقول في الخطوة الحالية
                if (validateStep(currentStep)) {
                    // إخفاء الخطوة الحالية
                    document.getElementById('step-' + currentStep).classList.add('d-none');
                    
                    // إظهار الخطوة التالية
                    document.getElementById('step-' + nextStep).classList.remove('d-none');
                }
            });
        });
        
        // زر الرجوع للخطوة السابقة
        prevButtons.forEach(button => {
            button.addEventListener('click', function() {
                const currentStep = parseInt(this.getAttribute('data-step'));
                const prevStep = currentStep - 1;
                
                // إخفاء الخطوة الحالية
                document.getElementById('step-' + currentStep).classList.add('d-none');
                
                // إظهار الخطوة السابقة
                document.getElementById('step-' + prevStep).classList.remove('d-none');
            });
        });
        
        // التحقق من صحة الحقول في كل خطوة
        function validateStep(step) {
            const currentStep = document.getElementById('step-' + step);
            const requiredFields = currentStep.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                alert('يرجى ملء جميع الحقول المطلوبة');
            }
            
            return isValid;
        }
    });
</script>
@endpush
