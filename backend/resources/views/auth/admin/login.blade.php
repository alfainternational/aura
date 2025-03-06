@extends('layouts.auth')

@section('title', 'تسجيل دخول المشرف')

@section('content')
<div class="container">
    <div class="login-container admin-login-container">
        <div class="login-header">
            <img src="/images/logo.png" alt="أورا">
            <h3>لوحة تحكم المشرفين</h3>
            <p class="text-light">يرجى تسجيل الدخول للمتابعة</p>
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
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="login-tabs">
            <ul class="nav nav-tabs" id="loginTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-content" type="button" role="tab" aria-controls="password-content" aria-selected="true">
                        <i class="fas fa-key"></i> تسجيل الدخول بكلمة المرور
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="biometric-tab" data-bs-toggle="tab" data-bs-target="#biometric-content" type="button" role="tab" aria-controls="biometric-content" aria-selected="false">
                        <i class="fas fa-fingerprint"></i> تسجيل الدخول بالبصمة
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="tab-content" id="loginTabsContent">
            <!-- Password Login Tab -->
            <div class="tab-pane fade show active" id="password-content" role="tabpanel" aria-labelledby="password-tab">
                <form class="login-form" method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf
                    <input type="hidden" name="user_type" value="admin">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label text-light">البريد الإلكتروني</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="country_id" class="form-label text-light">الدولة</label>
                        <select class="form-select @error('country_id') is-invalid @enderror" id="country_id" name="country_id">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" 
                                    data-code="{{ $country->code }}" 
                                    data-phone-code="{{ $country->phone_code }}"
                                    {{ (old('country_id') == $country->id || ($defaultCountry && $defaultCountry->id == $country->id)) ? 'selected' : '' }}>
                                    {{ $country->name }} ({{ $country->code }})
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
                        <label for="password" class="form-label text-light">كلمة المرور</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-light" for="remember">تذكرني</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-light">نسيت كلمة المرور؟</a>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn login-btn">تسجيل الدخول</button>
                    </div>
                </form>
            </div>
            
            <!-- Biometric Login Tab -->
            <div class="tab-pane fade" id="biometric-content" role="tabpanel" aria-labelledby="biometric-tab">
                <div class="biometric-login-container">
                    <div class="text-center mb-4">
                        <i class="fas fa-fingerprint biometric-icon"></i>
                        <h4 class="text-light">تسجيل الدخول بالبصمة</h4>
                        <p class="text-light">انقر على زر البصمة أدناه لتسجيل الدخول باستخدام بصمة الإصبع</p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="biometric_country_id" class="form-label text-light">الدولة</label>
                        <select class="form-select" id="biometric_country_id">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" 
                                    {{ ($defaultCountry && $defaultCountry->id == $country->id) ? 'selected' : '' }}>
                                    {{ $country->name }} ({{ $country->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="button" id="biometricLoginBtn" class="btn biometric-btn">
                            <i class="fas fa-fingerprint"></i> المصادقة بالبصمة
                        </button>
                    </div>
                    
                    <div id="biometricStatus" class="alert mt-3 d-none"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const passwordInput = this.previousElementSibling;
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });
    
    // Biometric authentication
    document.addEventListener('DOMContentLoaded', function() {
        const biometricLoginBtn = document.getElementById('biometricLoginBtn');
        const biometricStatus = document.getElementById('biometricStatus');
        
        if (biometricLoginBtn) {
            biometricLoginBtn.addEventListener('click', function() {
                // Check if WebAuthn is supported
                if (!window.PublicKeyCredential) {
                    showBiometricStatus('danger', 'المصادقة البيومترية غير مدعومة في هذا المتصفح');
                    return;
                }
                
                // Start biometric authentication
                showBiometricStatus('info', 'جاري التحقق من البصمة...');
                
                // Make request to get challenge
                fetch('{{ route("biometric.authenticate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        country_id: document.getElementById('biometric_country_id').value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        showBiometricStatus('danger', data.message);
                        return;
                    }
                    
                    // Convert base64 challenge to ArrayBuffer
                    const challenge = Uint8Array.from(atob(data.challenge), c => c.charCodeAt(0));
                    
                    // Prepare credentials for WebAuthn
                    const allowCredentials = data.credentials.map(cred => {
                        return {
                            id: Uint8Array.from(atob(cred.id), c => c.charCodeAt(0)),
                            type: cred.type
                        };
                    });
                    
                    // Request authentication
                    return navigator.credentials.get({
                        publicKey: {
                            challenge: challenge,
                            rpId: window.location.hostname,
                            allowCredentials: allowCredentials,
                            userVerification: 'preferred',
                            timeout: 60000
                        }
                    });
                })
                .then(credential => {
                    if (!credential) {
                        throw new Error('المصادقة البيومترية فشلت');
                    }
                    
                    // Prepare credential data for server
                    const authData = new Uint8Array(credential.response.authenticatorData);
                    const clientDataJSON = new Uint8Array(credential.response.clientDataJSON);
                    const signature = new Uint8Array(credential.response.signature);
                    
                    // Convert ArrayBuffers to base64
                    const authDataBase64 = btoa(String.fromCharCode.apply(null, authData));
                    const clientDataJSONBase64 = btoa(String.fromCharCode.apply(null, clientDataJSON));
                    const signatureBase64 = btoa(String.fromCharCode.apply(null, signature));
                    const credentialIdBase64 = btoa(String.fromCharCode.apply(null, new Uint8Array(credential.rawId)));
                    
                    // Send authentication result to server
                    return fetch('{{ route("biometric.authenticate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: credentialIdBase64,
                            authenticatorData: authDataBase64,
                            clientDataJSON: clientDataJSONBase64,
                            signature: signatureBase64,
                            country_id: document.getElementById('biometric_country_id').value
                        })
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showBiometricStatus('success', 'تم تسجيل الدخول بنجاح، جاري التحويل...');
                        window.location.href = data.redirect || '{{ route("admin.dashboard") }}';
                    } else {
                        showBiometricStatus('danger', data.message || 'فشل تسجيل الدخول');
                    }
                })
                .catch(error => {
                    console.error('Biometric authentication error:', error);
                    showBiometricStatus('danger', 'حدث خطأ أثناء المصادقة البيومترية');
                });
            });
        }
        
        function showBiometricStatus(type, message) {
            biometricStatus.textContent = message;
            biometricStatus.className = `alert alert-${type} mt-3`;
        }
    });
</script>
@endsection
