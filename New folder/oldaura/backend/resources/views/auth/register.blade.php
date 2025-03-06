<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد - منصة أورا</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        .register-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header img {
            width: 100px;
            margin-bottom: 15px;
        }
        .register-header h3 {
            color: #3c4b64;
            font-weight: 600;
        }
        .register-form .form-control {
            height: 50px;
            border-radius: 8px;
        }
        .register-form .form-select {
            height: 50px;
            border-radius: 8px;
        }
        .register-btn {
            height: 50px;
            border-radius: 8px;
            background-color: #4650dd;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }
        .register-btn:hover {
            background-color: #3540c0;
            color: #fff;
        }
        .register-footer {
            text-align: center;
            margin-top: 20px;
        }
        .register-footer a {
            color: #4650dd;
            text-decoration: none;
        }
        .register-footer a:hover {
            text-decoration: underline;
        }
        .auth-options {
            display: flex;
            margin-bottom: 15px;
        }
        .auth-option {
            flex: 1;
            text-align: center;
            padding: 15px;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
        }
        .auth-option.active {
            color: #4650dd;
            border-bottom: 2px solid #4650dd;
        }
        .user-type-container {
            margin-bottom: 30px;
        }
        .user-type-options {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
        }
        .user-type-option {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 25px 15px;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }
        .user-type-option:hover {
            border-color: #4650dd;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(70, 80, 221, 0.1);
        }
        .user-type-option.active {
            border-color: #4650dd;
            background-color: rgba(70, 80, 221, 0.05);
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(70, 80, 221, 0.2);
        }
        .user-type-option i {
            font-size: 40px;
            margin-bottom: 15px;
            color: #4650dd;
            display: block;
        }
        .user-type-option h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .user-type-option p {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 0;
        }
        .steps-container {
            display: none;
        }
        .steps-container.active {
            display: block;
        }
        .steps-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin: 0 10px;
            position: relative;
            z-index: 1;
        }
        .step.active {
            background-color: #4650dd;
            color: #fff;
        }
        .step.completed {
            background-color: #28a745;
            color: #fff;
        }
        .step-connector {
            flex: 1;
            height: 2px;
            background-color: #e9ecef;
            margin-top: 15px;
        }
        .step-connector.active {
            background-color: #4650dd;
        }
        .step-title {
            text-align: center;
            margin-top: 5px;
            font-size: 12px;
            color: #6c757d;
        }
        .registration-note {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-right: 3px solid #4650dd;
        }
        .registration-note p {
            margin-bottom: 0;
            font-size: 14px;
        }
        .registration-note strong {
            color: #4650dd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <img src="/images/logo.png" alt="أورا">
                <h3>إنشاء حساب جديد</h3>
                <p class="text-muted">أنشئ حسابك في منصة أورا للوصول إلى خدماتنا</p>
            </div>
            
            <div class="auth-options">
                <a href="{{ route('login') }}" class="auth-option">تسجيل الدخول</a>
                <div class="auth-option active">إنشاء حساب</div>
            </div>
            
            @if(session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
            @endif
            
            <form class="register-form" method="POST" action="{{ route('register') }}" id="registerForm">
                @csrf
                
                <!-- خطوة 1: اختيار نوع الحساب -->
                <div class="steps-container active" id="step1">
                    <div class="steps-indicator">
                        <div class="step active">1</div>
                        <div class="step-connector"></div>
                        <div class="step">2</div>
                        <div class="step-connector"></div>
                        <div class="step">3</div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <h4>اختر نوع الحساب</h4>
                        <p class="text-muted">حدد نوع الحساب الذي ترغب في إنشائه</p>
                    </div>
                    
                    <div class="user-type-container">
                        <div class="user-type-options">
                            <div class="user-type-option active" data-value="customer">
                                <i class="fas fa-user"></i>
                                <h4>عميل</h4>
                                <p>للمستخدمين الذين يرغبون في التسوق وطلب المنتجات والخدمات.</p>
                            </div>
                            <div class="user-type-option" data-value="merchant">
                                <i class="fas fa-store"></i>
                                <h4>تاجر</h4>
                                <p>لأصحاب المتاجر والشركات لبيع المنتجات والخدمات.</p>
                            </div>
                            <div class="user-type-option" data-value="agent">
                                <i class="fas fa-building"></i>
                                <h4>وكيل</h4>
                                <p>للوكلاء والوسطاء في بيع وتقديم خدمات الشركات.</p>
                            </div>
                            <div class="user-type-option" data-value="messenger">
                                <i class="fas fa-motorcycle"></i>
                                <h4>مندوب</h4>
                                <p>لتوصيل الطلبات والبضائع من المتاجر إلى العملاء.</p>
                            </div>
                        </div>
                        <input type="hidden" name="user_type" id="user_type" value="customer">
                        @error('user_type')
                            <div class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="registration-note" id="customerNote">
                        <p><strong>ملاحظة:</strong> كعميل، ستتمكن من طلب المنتجات والخدمات، وتتبع طلباتك، والاستفادة من العروض الخاصة.</p>
                    </div>
                    
                    <div class="registration-note d-none" id="merchantNote">
                        <p><strong>ملاحظة:</strong> كتاجر، ستحتاج إلى استكمال بيانات إضافية بعد التسجيل تتعلق بنشاطك التجاري. يرجى تجهيز بيانات عملك التجاري.</p>
                    </div>
                    
                    <div class="registration-note d-none" id="agentNote">
                        <p><strong>ملاحظة:</strong> كوكيل، ستحتاج إلى استكمال بيانات إضافية بعد التسجيل بما في ذلك مستندات التعريف ومنطقة عملك.</p>
                    </div>
                    
                    <div class="registration-note d-none" id="messengerNote">
                        <p><strong>ملاحظة:</strong> كمندوب، ستحتاج إلى استكمال معلومات إضافية بعد التسجيل مثل صور الهوية، رخصة القيادة، وتفاصيل المركبة التي ستستخدمها للتوصيل.</p>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn register-btn" id="step1Next">التالي <i class="fas fa-arrow-left ms-2"></i></button>
                    </div>
                </div>
                
                <!-- خطوة 2: المعلومات الشخصية -->
                <div class="steps-container" id="step2">
                    <div class="steps-indicator">
                        <div class="step completed">1</div>
                        <div class="step-connector active"></div>
                        <div class="step active">2</div>
                        <div class="step-connector"></div>
                        <div class="step">3</div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <h4>المعلومات الشخصية</h4>
                        <p class="text-muted">أدخل معلوماتك الشخصية للمتابعة</p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="country_id" class="form-label">الدولة <span class="text-danger">*</span></label>
                        <select class="form-select @error('country_id') is-invalid @enderror" id="country_id" name="country_id" required>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id', $defaultCountry->id ?? '') == $country->id ? 'selected' : '' }}>
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
                    
                    <div class="mb-3">
                        <label for="city_id" class="form-label">المدينة <span class="text-danger">*</span></label>
                        <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id" required>
                            <option value="">اختر المدينة</option>
                        </select>
                        @error('city_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" id="phone_code"></span>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                        </div>
                        @error('phone_number')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" id="step2Back"><i class="fas fa-arrow-right ms-2"></i> السابق</button>
                        <button type="button" class="btn register-btn" id="step2Next">التالي <i class="fas fa-arrow-left ms-2"></i></button>
                    </div>
                </div>
                
                <!-- خطوة 3: معلومات الحساب -->
                <div class="steps-container" id="step3">
                    <div class="steps-indicator">
                        <div class="step completed">1</div>
                        <div class="step-connector active"></div>
                        <div class="step completed">2</div>
                        <div class="step-connector active"></div>
                        <div class="step active">3</div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <h4>معلومات الحساب</h4>
                        <p class="text-muted">أدخل كلمة المرور ووافق على الشروط والأحكام</p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input @error('terms_accepted') is-invalid @enderror" id="terms_accepted" name="terms_accepted" {{ old('terms_accepted') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="terms_accepted">أوافق على <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">شروط الاستخدام</a> و <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">سياسة الخصوصية</a></label>
                        @error('terms_accepted')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" id="step3Back"><i class="fas fa-arrow-right ms-2"></i> السابق</button>
                        <button type="submit" class="btn register-btn">إنشاء حساب</button>
                    </div>
                </div>
            </form>
            
            <div class="register-footer">
                <p class="text-muted">
                    لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a>
                </p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User type selection
            const userTypeOptions = document.querySelectorAll('.user-type-option');
            const userTypeInput = document.getElementById('user_type');
            const customerNote = document.getElementById('customerNote');
            const merchantNote = document.getElementById('merchantNote');
            const agentNote = document.getElementById('agentNote');
            const messengerNote = document.getElementById('messengerNote');
            
            userTypeOptions.forEach(option => {
                option.addEventListener('click', function() {
                    userTypeOptions.forEach(opt => opt.classList.remove('active'));
                    this.classList.add('active');
                    userTypeInput.value = this.dataset.value;
                    
                    // Show/hide user type specific notes
                    customerNote.classList.add('d-none');
                    merchantNote.classList.add('d-none');
                    agentNote.classList.add('d-none');
                    messengerNote.classList.add('d-none');
                    
                    if (this.dataset.value === 'customer') {
                        customerNote.classList.remove('d-none');
                    } else if (this.dataset.value === 'merchant') {
                        merchantNote.classList.remove('d-none');
                    } else if (this.dataset.value === 'agent') {
                        agentNote.classList.remove('d-none');
                    } else if (this.dataset.value === 'messenger') {
                        messengerNote.classList.remove('d-none');
                    }
                });
            });
            
            // Country and city selection
            const countrySelect = document.getElementById('country_id');
            const citySelect = document.getElementById('city_id');
            const phoneCodeSpan = document.getElementById('phone_code');
            
            function updatePhoneCode() {
                const selectedOption = countrySelect.options[countrySelect.selectedIndex];
                const phoneCode = selectedOption.textContent.match(/\(([^)]+)\)/)[1];
                phoneCodeSpan.textContent = phoneCode;
            }
            
            function loadCities() {
                const countryId = countrySelect.value;
                
                if (!countryId) return;
                
                fetch(`/cities-by-country?country_id=${countryId}`)
                    .then(response => response.json())
                    .then(data => {
                        citySelect.innerHTML = '<option value="">اختر المدينة</option>';
                        
                        data.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            
                            if (city.id == "{{ old('city_id') }}") {
                                option.selected = true;
                            }
                            
                            citySelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading cities:', error);
                    });
            }
            
            countrySelect.addEventListener('change', function() {
                loadCities();
                updatePhoneCode();
            });
            
            // Initial load
            if (countrySelect.value) {
                loadCities();
                updatePhoneCode();
            }
            
            // Step navigation
            const step1 = document.getElementById('step1');
            const step2 = document.getElementById('step2');
            const step3 = document.getElementById('step3');
            
            const step1Next = document.getElementById('step1Next');
            const step2Back = document.getElementById('step2Back');
            const step2Next = document.getElementById('step2Next');
            const step3Back = document.getElementById('step3Back');
            
            step1Next.addEventListener('click', function() {
                step1.classList.remove('active');
                step2.classList.add('active');
            });
            
            step2Back.addEventListener('click', function() {
                step2.classList.remove('active');
                step1.classList.add('active');
            });
            
            step2Next.addEventListener('click', function() {
                // Validate step 2 fields
                const name = document.getElementById('name').value;
                const username = document.getElementById('username').value;
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone_number').value;
                const country = document.getElementById('country_id').value;
                const city = document.getElementById('city_id').value;
                
                if (name && username && email && phone && country && city) {
                    step2.classList.remove('active');
                    step3.classList.add('active');
                } else {
                    alert('يرجى إدخال جميع البيانات المطلوبة');
                }
            });
            
            step3Back.addEventListener('click', function() {
                step3.classList.remove('active');
                step2.classList.add('active');
            });
        });
    </script>
</body>
</html>
