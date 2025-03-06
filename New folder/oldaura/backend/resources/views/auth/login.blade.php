<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - منصة أورا</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
        }
        .login-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header img {
            width: 100px;
            margin-bottom: 15px;
        }
        .login-header h3 {
            color: #3c4b64;
            font-weight: 600;
        }
        .login-form .form-control {
            height: 50px;
            border-radius: 8px;
        }
        .login-btn {
            height: 50px;
            border-radius: 8px;
            background-color: #4650dd;
            color: #fff;
            font-weight: 600;
            font-size: 16px;
        }
        .login-btn:hover {
            background-color: #3540c0;
            color: #fff;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        .auth-options {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }
        .auth-option {
            flex: 1;
            text-align: center;
            padding: 12px;
            background-color: #f1f3f9;
            color: #6c757d;
            cursor: pointer;
            text-decoration: none;
        }
        .auth-option.active {
            background-color: #4650dd;
            color: #fff;
            font-weight: 600;
        }
        .user-type-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        .user-type-btn {
            flex: 1;
            min-width: calc(50% - 10px);
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            background-color: #f1f3f9;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s;
        }
        .user-type-btn.active {
            background-color: #4650dd;
            color: #fff;
        }
        .user-type-btn i {
            margin-left: 5px;
        }
        .biometric-login {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        .biometric-login:before, .biometric-login:after {
            content: "";
            display: block;
            width: 40%;
            height: 1px;
            background: #e0e0e0;
            position: absolute;
            top: 50%;
        }
        .biometric-login:before {
            left: 0;
        }
        .biometric-login:after {
            right: 0;
        }
        .biometric-login span {
            display: inline-block;
            padding: 0 15px;
            background: #fff;
            position: relative;
            z-index: 1;
            color: #6c757d;
        }
        .fingerprint-btn {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: #f1f3f9;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 15px auto;
            cursor: pointer;
            transition: all 0.3s;
        }
        .fingerprint-btn:hover {
            background-color: #e9ecef;
        }
        .fingerprint-btn i {
            font-size: 30px;
            color: #4650dd;
        }
        .country-selector {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <img src="/images/logo.png" alt="أورا">
                <h3>تسجيل الدخول</h3>
                @if(isset($userType) && $userType === 'admin')
                <p class="text-muted">تسجيل دخول المشرف</p>
                @else
                <p class="text-muted">قم بتسجيل الدخول للوصول إلى حسابك</p>
                @endif
            </div>
            
            <div class="auth-options">
                <div class="auth-option active">تسجيل الدخول</div>
                @if(!isset($userType) || $userType !== 'admin')
                <a href="{{ route('register') }}" class="auth-option">إنشاء حساب</a>
                @endif
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
            
            @if(!isset($userType) || $userType !== 'admin')
            <div class="user-type-selector">
                <div class="user-type-btn active" data-type="customer">
                    <i class="fas fa-user"></i> عميل
                </div>
                <div class="user-type-btn" data-type="merchant">
                    <i class="fas fa-store"></i> تاجر
                </div>
                <div class="user-type-btn" data-type="agent">
                    <i class="fas fa-building"></i> وكيل
                </div>
                <div class="user-type-btn" data-type="messenger">
                    <i class="fas fa-motorcycle"></i> مندوب
                </div>
            </div>
            @endif
            
            <form class="login-form" method="POST" action="{{ isset($userType) && $userType === 'admin' ? route('admin.login.submit') : route('login') }}">
                @csrf
                @if(isset($userType) && $userType === 'admin')
                <input type="hidden" name="user_type" value="admin">
                @else
                <input type="hidden" name="user_type" id="user_type" value="customer">
                @endif
                
                <div class="country-selector mb-3">
                    <label for="country_id" class="form-label">الدولة</label>
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
                    <label for="login" class="form-label">البريد الإلكتروني أو اسم المستخدم أو رقم الهاتف</label>
                    <input type="text" class="form-control @error('login') is-invalid @enderror @error('email') is-invalid @enderror" id="login" name="login" value="{{ old('login') }}" required autofocus>
                    @error('login')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">تذكرني</label>
                    </div>
                    <a href="{{ route('password.request') }}" class="small text-decoration-none">نسيت كلمة المرور؟</a>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn login-btn">تسجيل الدخول</button>
                </div>
            </form>
            
            @if(session('biometric_auth_available'))
            <div class="biometric-login">
                <span>أو</span>
            </div>
            
            <div class="text-center">
                <p class="mb-2">تسجيل الدخول السريع</p>
                <button type="button" id="biometric-login-btn" class="fingerprint-btn">
                    <i class="fas fa-fingerprint"></i>
                </button>
            </div>
            @endif
            
            <div class="login-footer">
                <p class="text-muted">
                    ليس لديك حساب؟ <a href="{{ route('register') }}">إنشاء حساب جديد</a>
                </p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userTypeButtons = document.querySelectorAll('.user-type-btn');
            const userTypeInput = document.getElementById('user_type');
            
            userTypeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    userTypeButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    userTypeInput.value = this.dataset.type;
                });
            });
            
            // Biometric authentication
            const biometricBtn = document.getElementById('biometric-login-btn');
            if (biometricBtn) {
                biometricBtn.addEventListener('click', function() {
                    if (window.PublicKeyCredential) {
                        // Check if the browser supports WebAuthn
                        authenticateWithBiometric();
                    } else {
                        alert('المصادقة البيومترية غير مدعومة في هذا المتصفح.');
                    }
                });
            }
            
            function authenticateWithBiometric() {
                const countryId = document.getElementById('country_id').value;
                
                // First, get the challenge from the server
                fetch('{{ route("biometric.authenticate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ country_id: countryId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Convert base64 to array buffer
                        const challenge = Uint8Array.from(atob(data.challenge), c => c.charCodeAt(0));
                        
                        // Create the credential options
                        const options = {
                            publicKey: {
                                challenge: challenge,
                                timeout: 60000,
                                userVerification: 'preferred',
                                allowCredentials: data.credentials.map(cred => ({
                                    id: Uint8Array.from(atob(cred.id), c => c.charCodeAt(0)),
                                    type: 'public-key'
                                }))
                            }
                        };
                        
                        // Request the credential
                        return navigator.credentials.get(options);
                    } else {
                        throw new Error(data.message || 'فشل في بدء المصادقة البيومترية');
                    }
                })
                .then(credential => {
                    // Convert the credential to a format the server can understand
                    const authData = {
                        id: btoa(String.fromCharCode.apply(null, new Uint8Array(credential.rawId))),
                        clientDataJSON: btoa(String.fromCharCode.apply(null, new Uint8Array(credential.response.clientDataJSON))),
                        authenticatorData: btoa(String.fromCharCode.apply(null, new Uint8Array(credential.response.authenticatorData))),
                        signature: btoa(String.fromCharCode.apply(null, new Uint8Array(credential.response.signature))),
                        country_id: countryId
                    };
                    
                    // Send the credential to the server
                    return fetch('{{ route("biometric.authenticate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(authData)
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message || 'فشل في المصادقة البيومترية');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('حدث خطأ أثناء المصادقة البيومترية: ' + error.message);
                });
            }
        });
    </script>
</body>
</html>
