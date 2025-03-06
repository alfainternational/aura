@extends('layouts.app')

@section('title', 'تسجيل الدخول بالبصمة')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-fingerprint me-2"></i> تسجيل الدخول بالبصمة</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="biometric-animation mb-3">
                            <i class="fas fa-fingerprint fa-5x text-primary pulse-animation"></i>
                        </div>
                        <h4 class="mb-3">تسجيل الدخول السريع بالبصمة</h4>
                        <p class="text-muted">استخدم بصمة الإصبع أو التعرف على الوجه للدخول إلى حسابك بأمان.</p>
                    </div>

                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" id="username" class="form-control" placeholder="اسم المستخدم أو البريد الإلكتروني" value="{{ old('username') }}">
                        </div>
                        <div class="form-text">أدخل اسم المستخدم أو البريد الإلكتروني للحساب الخاص بك</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button id="biometric-login-button" class="btn btn-primary btn-lg">
                            <i class="fas fa-fingerprint me-2"></i> تسجيل الدخول بالبصمة
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-keyboard me-2"></i> تسجيل الدخول بكلمة المرور
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for authentication process -->
<div class="modal fade" id="authentication-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div id="authentication-progress">
                    <div class="mb-4">
                        <i class="fas fa-fingerprint fa-4x text-primary pulse-animation"></i>
                    </div>
                    <h4>جاري التحقق من البصمة...</h4>
                    <p class="text-muted">يرجى استخدام بصمة الإصبع أو التعرف على الوجه عندما يطلب منك المتصفح ذلك.</p>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
                
                <div id="authentication-success" style="display: none;">
                    <div class="mb-4 text-success">
                        <i class="fas fa-check-circle fa-4x"></i>
                    </div>
                    <h4>تم تسجيل الدخول بنجاح!</h4>
                    <p>جاري تحويلك إلى لوحة التحكم...</p>
                </div>
                
                <div id="authentication-error" style="display: none;">
                    <div class="mb-4 text-danger">
                        <i class="fas fa-exclamation-circle fa-4x"></i>
                    </div>
                    <h4>فشل تسجيل الدخول</h4>
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
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle login button click
        document.getElementById('biometric-login-button').addEventListener('click', function() {
            const username = document.getElementById('username').value.trim();
            if (!username) {
                alert('يرجى إدخال اسم المستخدم أو البريد الإلكتروني');
                return;
            }
            
            startAuthentication(username);
        });
    });
    
    // Web Authentication API implementation
    function startAuthentication(username) {
        // Check if WebAuthn is supported
        if (!window.PublicKeyCredential) {
            alert('متصفحك لا يدعم تقنية المصادقة البيومترية. يرجى استخدام متصفح حديث أو تسجيل الدخول بكلمة المرور.');
            return;
        }
        
        // Show modal
        const authModal = new bootstrap.Modal(document.getElementById('authentication-modal'));
        authModal.show();
        
        // Reset modal state
        document.getElementById('authentication-progress').style.display = 'block';
        document.getElementById('authentication-success').style.display = 'none';
        document.getElementById('authentication-error').style.display = 'none';
        
        // Get authentication options from server
        fetch('{{ route("biometric.authentication.start") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                username: username
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status !== 'success') {
                throw new Error(data.message || 'Unknown error');
            }
            
            // Prepare the options for the WebAuthn API
            const getOptions = preparePublicKeyOptions(data.options);
            
            // Get credentials
            return navigator.credentials.get({
                publicKey: getOptions
            });
        })
        .then(credential => {
            // Format the response from WebAuthn API
            const response = formatAuthenticationResponse(credential);
            
            // Send response to server to complete authentication
            return fetch('{{ route("biometric.authentication.complete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    credential: response
                })
            });
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Show success message
                document.getElementById('authentication-progress').style.display = 'none';
                document.getElementById('authentication-success').style.display = 'block';
                
                // Redirect to dashboard
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("dashboard") }}';
                }, 1500);
            } else {
                throw new Error(data.message || 'فشل تسجيل الدخول بالبصمة');
            }
        })
        .catch(error => {
            console.error('Authentication error:', error);
            
            // Show error message
            document.getElementById('authentication-progress').style.display = 'none';
            document.getElementById('authentication-error').style.display = 'block';
            document.getElementById('error-message').textContent = error.message || 'حدث خطأ أثناء التحقق من البصمة، يرجى المحاولة مرة أخرى.';
        });
    }
    
    // Helper function to prepare PublicKey options
    function preparePublicKeyOptions(options) {
        // Convert base64 strings to ArrayBuffer
        if (options.challenge) {
            options.challenge = base64ToArrayBuffer(options.challenge);
        }
        
        if (options.allowCredentials) {
            options.allowCredentials = options.allowCredentials.map(credential => {
                return {
                    ...credential,
                    id: base64ToArrayBuffer(credential.id)
                };
            });
        }
        
        return options;
    }
    
    // Helper function to format authentication response
    function formatAuthenticationResponse(credential) {
        const { id, rawId, response, type } = credential;
        
        return {
            id: id,
            rawId: arrayBufferToBase64(rawId),
            response: {
                clientDataJSON: arrayBufferToBase64(response.clientDataJSON),
                authenticatorData: arrayBufferToBase64(response.authenticatorData),
                signature: arrayBufferToBase64(response.signature),
                userHandle: response.userHandle ? arrayBufferToBase64(response.userHandle) : null
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
