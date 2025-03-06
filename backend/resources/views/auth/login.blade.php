<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>تسجيل الدخول - منصة أورا</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth-style.css') }}">
</head>
<body>
    <div class="auth-container">
        <div class="auth-banner">
            <h2>مرحباً بك في منصة أورا</h2>
            <p>المنصة الأولى للتواصل والمراسلة الآمنة في السودان. تواصل مع أصدقائك وعائلتك بكل سهولة وأمان.</p>
            
            <div class="feature-cards">
                <div class="feature-card">
                    <div class="feature-card-icon wallet">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h4>المحفظة الإلكترونية</h4>
                    <p>إدارة أموالك وإجراء المدفوعات بسهولة وأمان</p>
                </div>
                <div class="feature-card">
                    <div class="feature-card-icon ecommerce">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h4>التجارة الإلكترونية</h4>
                    <p>تسوق أو بيع المنتجات عبر منصتنا</p>
                </div>
                <div class="feature-card">
                    <div class="feature-card-icon messaging">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h4>المراسلة الفورية</h4>
                    <p>تواصل مع الأصدقاء والعائلة بخصوصية تامة</p>
                </div>
            </div>
        </div>
        
        <div class="auth-form">
            <div class="logo-container">
                <img src="/images/logo.png" alt="أورا">
            </div>
            
            <div class="form-title">
                <h3>تسجيل الدخول</h3>
                <p>أدخل بياناتك للوصول إلى حسابك</p>
            </div>
            
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="user-types">
                <div class="user-type-btn active" data-type="customer">
                    <div class="user-type-icon"><i class="fas fa-user"></i></div>
                    <div>مستخدم</div>
                </div>
                <div class="user-type-btn" data-type="merchant">
                    <div class="user-type-icon"><i class="fas fa-store"></i></div>
                    <div>تاجر</div>
                </div>
                <div class="user-type-btn" data-type="agent">
                    <div class="user-type-icon"><i class="fas fa-user-tie"></i></div>
                    <div>وكيل</div>
                </div>
                <div class="user-type-btn" data-type="messenger">
                    <div class="user-type-icon"><i class="fas fa-motorcycle"></i></div>
                    <div>مندوب توصيل</div>
                </div>
            </div>
            
            <form class="login-form" method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="user_type" id="user_type" value="customer">
                
                <div class="location-detector">
                    <div class="location-info">
                        <div class="location-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="location-text">
                            <h5>موقعك الحالي</h5>
                            <p id="location-display">لم يتم تحديد الموقع بعد</p>
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <input type="hidden" name="country" id="country">
                            <input type="hidden" name="city" id="city">
                            <input type="hidden" name="country_id" id="country_id">
                        </div>
                    </div>
                    <div class="location-actions">
                        <button type="button" id="detect-location" class="btn btn-outline-primary">
                            <i class="fas fa-location-arrow"></i> تحديد موقعي
                        </button>
                    </div>
                    <div id="location-message" style="display: none;" class="alert mt-2 mb-0"></div>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder=" " required>
                    <label for="phone">رقم الهاتف</label>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-floating mb-3">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder=" " required>
                    <label for="password">كلمة المرور</label>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">تذكرني</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="link-primary">نسيت كلمة المرور؟</a>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">تسجيل الدخول</button>
                
                <button type="button" id="biometric-login" class="btn btn-outline-primary w-100 py-2 mb-3">
                    <i class="fas fa-fingerprint me-2"></i> تسجيل الدخول ببصمة الإصبع
                </button>
            </form>
            
            <div class="divider">أو</div>
            
            <div class="social-buttons">
                <a href="{{ route('login.google') }}" class="btn-social">
                    <i class="fab fa-google"></i> Google
                </a>
                <a href="{{ route('login.facebook') }}" class="btn-social">
                    <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="{{ route('login.twitter') }}" class="btn-social">
                    <i class="fab fa-twitter"></i> Twitter
                </a>
            </div>
            
            <div class="auth-footer">
                <p>
                    ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a>
                </p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Type Selection
            const userTypeButtons = document.querySelectorAll('.user-type-btn');
            const userTypeInput = document.getElementById('user_type');
            
            userTypeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    userTypeButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    userTypeInput.value = this.getAttribute('data-type');
                });
            });
            
            // Geolocation
            const detectLocationBtn = document.getElementById('detect-location');
            const locationDisplay = document.getElementById('location-display');
            
            if (detectLocationBtn) {
                detectLocationBtn.addEventListener('click', function() {
                    if (navigator.geolocation) {
                        detectLocationBtn.disabled = true;
                        detectLocationBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري تحديد الموقع...';
                        
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                const latitude = position.coords.latitude;
                                const longitude = position.coords.longitude;
                                
                                document.getElementById('latitude').value = latitude;
                                document.getElementById('longitude').value = longitude;
                                
                                // إرسال الإحداثيات إلى الخادم للحصول على اسم البلد والمدينة
                                fetch(`{{ route('api.validate-location') }}?lat=${latitude}&lon=${longitude}`, {
                                    headers: {
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        document.getElementById('country').value = data.country;
                                        document.getElementById('city').value = data.city;
                                        document.getElementById('country_id').value = data.country_id;
                                        
                                        locationDisplay.textContent = `${data.city}, ${data.country}`;
                                        showLocationMessage('success', `تم تحديد موقعك بنجاح: ${data.city}, ${data.country}`);
                                    } else {
                                        showLocationMessage('danger', data.message || 'لم نتمكن من تحديد موقعك بدقة.');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    showLocationMessage('danger', 'حدث خطأ أثناء تحديد موقعك.');
                                })
                                .finally(() => {
                                    detectLocationBtn.disabled = false;
                                    detectLocationBtn.innerHTML = '<i class="fas fa-location-arrow"></i> تحديد موقعي';
                                });
                            },
                            function(error) {
                                detectLocationBtn.disabled = false;
                                detectLocationBtn.innerHTML = '<i class="fas fa-location-arrow"></i> تحديد موقعي';
                                
                                let errorMessage = 'حدث خطأ أثناء تحديد موقعك.';
                                if (error.code === 1) {
                                    errorMessage = 'لم يتم السماح بالوصول إلى موقعك. الرجاء السماح بذلك من إعدادات المتصفح.';
                                } else if (error.code === 2) {
                                    errorMessage = 'تعذر تحديد موقعك. الرجاء المحاولة مرة أخرى.';
                                } else if (error.code === 3) {
                                    errorMessage = 'انتهت مهلة تحديد الموقع. الرجاء المحاولة مرة أخرى.';
                                }
                                
                                showLocationMessage('danger', errorMessage);
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    } else {
                        showLocationMessage('danger', 'متصفحك لا يدعم تحديد الموقع.');
                    }
                });
            }
            
            // تظهر رسائل الموقع
            function showLocationMessage(type, message) {
                const locationMessageElement = document.getElementById('location-message');
                if (locationMessageElement) {
                    locationMessageElement.classList.remove('alert-success', 'alert-danger');
                    locationMessageElement.classList.add(`alert-${type}`);
                    locationMessageElement.innerHTML = message;
                    locationMessageElement.style.display = 'block';
                }
            }
            
            // تسجيل الدخول ببصمة الإصبع
            const biometricLoginBtn = document.getElementById('biometric-login');
            
            if (biometricLoginBtn && window.PublicKeyCredential) {
                biometricLoginBtn.addEventListener('click', function() {
                    authenticateWithBiometric();
                });
            } else if (biometricLoginBtn) {
                biometricLoginBtn.disabled = true;
                biometricLoginBtn.innerHTML = '<i class="fas fa-fingerprint me-2"></i> غير متاح على هذا الجهاز';
            }
            
            function authenticateWithBiometric() {
                const countryId = document.getElementById('country_id').value;
                
                // First, get the challenge from the server
                fetch('{{ route("biometric.authentication.start") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        country_id: countryId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Convert base64 to ArrayBuffer
                        const challenge = Uint8Array.from(atob(data.challenge), c => c.charCodeAt(0)).buffer;
                        
                        // Create PublicKeyCredentialRequestOptions
                        const options = {
                            challenge: challenge,
                            allowCredentials: data.allowCredentials.map(cred => {
                                return {
                                    id: Uint8Array.from(atob(cred.id), c => c.charCodeAt(0)).buffer,
                                    type: 'public-key'
                                };
                            }),
                            timeout: 60000,
                            userVerification: 'preferred'
                        };
                        
                        // Start the WebAuthn authentication
                        return navigator.credentials.get({
                            publicKey: options
                        });
                    } else {
                        throw new Error(data.message || 'لم نتمكن من بدء عملية المصادقة.');
                    }
                })
                .then(credential => {
                    // Prepare the credential for the server
                    const authData = {
                        id: btoa(String.fromCharCode.apply(null, new Uint8Array(credential.rawId))),
                        clientDataJSON: btoa(String.fromCharCode.apply(null, new Uint8Array(credential.response.clientDataJSON))),
                        authenticatorData: btoa(String.fromCharCode.apply(null, new Uint8Array(credential.response.authenticatorData))),
                        signature: btoa(String.fromCharCode.apply(null, new Uint8Array(credential.response.signature)))
                    };
                    
                    // Send the credential to the server
                    return fetch('{{ route("biometric.authentication.finish") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            credential: authData,
                            country_id: countryId
                        })
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect || '{{ route("home") }}';
                    } else {
                        showLocationMessage('danger', data.message || 'فشلت عملية المصادقة ببصمة الإصبع.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showLocationMessage('danger', 'حدث خطأ أثناء عملية المصادقة ببصمة الإصبع.');
                });
            }
        });
    </script>
</body>
</html>