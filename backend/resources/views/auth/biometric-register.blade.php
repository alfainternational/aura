@extends('layouts.app')

@section('title', 'تسجيل البصمة')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-fingerprint me-2"></i> تسجيل البصمة</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="biometric-animation mb-3">
                            <i class="fas fa-fingerprint fa-5x text-primary pulse-animation"></i>
                        </div>
                        <h4 class="mb-3">إضافة البصمة لتسجيل الدخول بسهولة وأمان</h4>
                        <p class="text-muted">إضافة البصمة سيسمح لك بالدخول إلى حسابك بسرعة وأمان عالي دون الحاجة لإدخال كلمة المرور.</p>
                    </div>

                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5>كيف يعمل هذا؟</h5>
                                <p class="mb-0">عند النقر على "تسجيل البصمة"، سيطلب منك المتصفح تأكيد هويتك باستخدام البصمة أو ميزة التعرف على الوجه المتوفرة على جهازك.</p>
                            </div>
                        </div>
                    </div>

                    <div class="device-info mb-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-mobile-alt"></i></span>
                            <input type="text" id="device-name" class="form-control" placeholder="اسم الجهاز (مثال: هاتفي الشخصي)" value="{{ $deviceName ?? '' }}">
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button id="register-button" class="btn btn-primary btn-lg">
                            <i class="fas fa-fingerprint me-2"></i> تسجيل البصمة
                        </button>
                        <a href="{{ route('user.settings.security') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> العودة للإعدادات
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i> الأجهزة المسجلة</h5>
                </div>
                <div class="card-body p-0">
                    <div id="sessions-container">
                        <div class="text-center p-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جاري التحميل...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for registration process -->
<div class="modal fade" id="registration-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div id="registration-progress">
                    <div class="mb-4">
                        <i class="fas fa-fingerprint fa-4x text-primary pulse-animation"></i>
                    </div>
                    <h4>جاري تسجيل البصمة...</h4>
                    <p class="text-muted">يرجى استخدام بصمة الإصبع أو التعرف على الوجه عندما يطلب منك المتصفح ذلك.</p>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                
                <div id="registration-success" style="display: none;">
                    <div class="mb-4 text-success">
                        <i class="fas fa-check-circle fa-4x"></i>
                    </div>
                    <h4>تم تسجيل البصمة بنجاح!</h4>
                    <p>يمكنك الآن استخدام البصمة لتسجيل الدخول بسهولة وأمان.</p>
                    <button type="button" class="btn btn-success mt-3" data-bs-dismiss="modal">حسناً</button>
                </div>
                
                <div id="registration-error" style="display: none;">
                    <div class="mb-4 text-danger">
                        <i class="fas fa-exclamation-circle fa-4x"></i>
                    </div>
                    <h4>حدث خطأ أثناء التسجيل</h4>
                    <p id="error-message">يرجى المحاولة مرة أخرى.</p>
                    <button type="button" class="btn btn-danger mt-3" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .pulse-animation {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            opacity: 0.7;
        }
        50% {
            transform: scale(1.05);
            opacity: 1;
        }
        100% {
            transform: scale(0.95);
            opacity: 0.7;
        }
    }
    
    .device-row {
        transition: all 0.3s ease;
    }
    
    .device-row:hover {
        background-color: rgba(0,0,0,0.03);
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Load registered devices
        loadRegisteredDevices();
        
        // Handle registration button click
        document.getElementById('register-button').addEventListener('click', function() {
            const deviceName = document.getElementById('device-name').value.trim();
            if (!deviceName) {
                alert('يرجى إدخال اسم الجهاز');
                return;
            }
            
            startRegistration(deviceName);
        });
    });
    
    // Load registered biometric devices
    function loadRegisteredDevices() {
        const sessionsContainer = document.getElementById('sessions-container');
        
        fetch('{{ route("biometric.sessions.list") }}')
            .then(response => response.json())
            .then(data => {
                if (data.sessions && data.sessions.length > 0) {
                    let html = '<ul class="list-group list-group-flush">';
                    data.sessions.forEach(session => {
                        html += `
                            <li class="list-group-item device-row">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-${session.device_name.toLowerCase().includes('iphone') ? 'apple' : 'mobile-alt'} me-3 text-secondary"></i>
                                            <div>
                                                <h6 class="mb-1">${session.device_name}</h6>
                                                <span class="text-muted small">آخر استخدام: ${session.last_used_at}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="{{ route("biometric.session.delete", "") }}/${session.id}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الجهاز؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        `;
                    });
                    html += '</ul>';
                    sessionsContainer.innerHTML = html;
                } else {
                    sessionsContainer.innerHTML = `
                        <div class="p-4 text-center">
                            <p class="text-muted">لا توجد أجهزة مسجلة حالياً</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                sessionsContainer.innerHTML = `
                    <div class="p-4 text-center">
                        <p class="text-danger">حدث خطأ أثناء تحميل الأجهزة المسجلة</p>
                    </div>
                `;
                console.error('Error loading sessions:', error);
            });
    }
    
    // Web Authentication API implementation
    function startRegistration(deviceName) {
        // Show modal
        const registrationModal = new bootstrap.Modal(document.getElementById('registration-modal'));
        registrationModal.show();
        
        // Reset modal state
        document.getElementById('registration-progress').style.display = 'block';
        document.getElementById('registration-success').style.display = 'none';
        document.getElementById('registration-error').style.display = 'none';
        
        // Get registration options from server
        fetch('{{ route("biometric.registration.start") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                device_name: deviceName
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                throw new Error(data.message || 'Unknown error');
            }
            
            // Prepare the options for the WebAuthn API
            const createOptions = preparePublicKeyOptions(data.options);
            
            // Create credentials
            return navigator.credentials.create({
                publicKey: createOptions
            });
        })
        .then(credential => {
            // Format the response from WebAuthn API
            const response = formatCredentialResponse(credential);
            
            // Send response to server to complete registration
            return fetch('{{ route("biometric.registration.complete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    credential: response,
                    device_name: deviceName
                })
            });
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success message
                document.getElementById('registration-progress').style.display = 'none';
                document.getElementById('registration-success').style.display = 'block';
                
                // Reload the devices list
                loadRegisteredDevices();
            } else {
                throw new Error(data.message || 'Failed to register biometric credentials');
            }
        })
        .catch(error => {
            console.error('Registration error:', error);
            
            // Show error message
            document.getElementById('registration-progress').style.display = 'none';
            document.getElementById('registration-error').style.display = 'block';
            document.getElementById('error-message').textContent = error.message || 'حدث خطأ أثناء تسجيل البصمة، يرجى المحاولة مرة أخرى.';
        });
    }
    
    // Helper function to prepare PublicKey options
    function preparePublicKeyOptions(options) {
        // Convert base64 strings to ArrayBuffer
        if (options.challenge) {
            options.challenge = base64ToArrayBuffer(options.challenge);
        }
        
        if (options.user && options.user.id) {
            options.user.id = base64ToArrayBuffer(options.user.id);
        }
        
        if (options.excludeCredentials) {
            options.excludeCredentials = options.excludeCredentials.map(credential => {
                return {
                    ...credential,
                    id: base64ToArrayBuffer(credential.id)
                };
            });
        }
        
        return options;
    }
    
    // Helper function to format credential response
    function formatCredentialResponse(credential) {
        const { id, rawId, response, type } = credential;
        
        return {
            id: id,
            rawId: arrayBufferToBase64(rawId),
            response: {
                clientDataJSON: arrayBufferToBase64(response.clientDataJSON),
                attestationObject: arrayBufferToBase64(response.attestationObject)
            },
            type: type
        };
    }
    
    // Helper function to convert ArrayBuffer to Base64
    function arrayBufferToBase64(buffer) {
        const binary = String.fromCharCode.apply(null, new Uint8Array(buffer));
        return window.btoa(binary);
    }
    
    // Helper function to convert Base64 to ArrayBuffer
    function base64ToArrayBuffer(base64) {
        const binary = window.atob(base64);
        const buffer = new ArrayBuffer(binary.length);
        const bytes = new Uint8Array(buffer);
        
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        
        return buffer;
    }
</script>
@endsection
