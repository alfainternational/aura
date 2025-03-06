@extends('layouts.app')

@section('title', 'إعداد المصادقة الثنائية')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <h5 class="mb-0">إعداد المصادقة الثنائية</h5>
                </x-slot>
                
                <div class="setup-process">
                    @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="setup-steps mb-4">
                        <div class="d-flex justify-content-center mb-4">
                            <div class="step-progress">
                                <div class="step-item active">
                                    <div class="step-circle">1</div>
                                    <div class="step-text">تثبيت التطبيق</div>
                                </div>
                                <div class="step-line"></div>
                                <div class="step-item">
                                    <div class="step-circle">2</div>
                                    <div class="step-text">مسح الرمز</div>
                                </div>
                                <div class="step-line"></div>
                                <div class="step-item">
                                    <div class="step-circle">3</div>
                                    <div class="step-text">التحقق</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="setup-content">
                        <div class="setup-step" id="step1">
                            <h5 class="mb-3">الخطوة 1: تثبيت تطبيق المصادقة</h5>
                            <p>قبل البدء، تحتاج إلى تثبيت تطبيق مصادقة على هاتفك الذكي. يمكنك استخدام أحد التطبيقات التالية:</p>
                            
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="auth-app-card text-center p-3 border rounded">
                                        <img src="{{ asset('assets/img/google-authenticator.png') }}" alt="Google Authenticator" class="img-fluid mb-2" style="height: 60px;">
                                        <h6>Google Authenticator</h6>
                                        <div class="mt-2">
                                            <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="btn btn-sm btn-outline-primary">Android</a>
                                            <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank" class="btn btn-sm btn-outline-primary">iOS</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="auth-app-card text-center p-3 border rounded">
                                        <img src="{{ asset('assets/img/microsoft-authenticator.png') }}" alt="Microsoft Authenticator" class="img-fluid mb-2" style="height: 60px;">
                                        <h6>Microsoft Authenticator</h6>
                                        <div class="mt-2">
                                            <a href="https://play.google.com/store/apps/details?id=com.azure.authenticator" target="_blank" class="btn btn-sm btn-outline-primary">Android</a>
                                            <a href="https://apps.apple.com/app/microsoft-authenticator/id983156458" target="_blank" class="btn btn-sm btn-outline-primary">iOS</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="auth-app-card text-center p-3 border rounded">
                                        <img src="{{ asset('assets/img/authy.png') }}" alt="Authy" class="img-fluid mb-2" style="height: 60px;">
                                        <h6>Authy</h6>
                                        <div class="mt-2">
                                            <a href="https://play.google.com/store/apps/details?id=com.authy.authy" target="_blank" class="btn btn-sm btn-outline-primary">Android</a>
                                            <a href="https://apps.apple.com/app/authy/id494168017" target="_blank" class="btn btn-sm btn-outline-primary">iOS</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary next-step" data-step="1">
                                    الخطوة التالية <i class="bi bi-arrow-left ms-1"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="setup-step d-none" id="step2">
                            <h5 class="mb-3">الخطوة 2: مسح رمز QR</h5>
                            <p>افتح تطبيق المصادقة على هاتفك وامسح رمز QR التالي:</p>
                            
                            <div class="qr-container text-center mb-4 p-4 bg-light rounded">
                                {!! $qrCode !!}
                            </div>
                            
                            <div class="manual-setup mb-4">
                                <p class="mb-2">إذا لم تتمكن من مسح الرمز، يمكنك إدخال المفتاح التالي يدويًا:</p>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $user->two_factor_secret }}" id="secretKey" readonly>
                                    <button class="btn btn-outline-secondary" type="button" id="copySecretBtn">
                                        <i class="bi bi-clipboard"></i> نسخ
                                    </button>
                                </div>
                                <small class="text-muted">اسم الحساب: {{ config('app.name') }} ({{ $user->email }})</small>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-outline-secondary prev-step" data-step="2">
                                    <i class="bi bi-arrow-right me-1"></i> الخطوة السابقة
                                </button>
                                <button type="button" class="btn btn-primary next-step" data-step="2">
                                    الخطوة التالية <i class="bi bi-arrow-left ms-1"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="setup-step d-none" id="step3">
                            <h5 class="mb-3">الخطوة 3: التحقق والتفعيل</h5>
                            <p>أدخل رمز التحقق المعروض في تطبيق المصادقة الخاص بك:</p>
                            
                            <form action="{{ route('security.two-factor.enable') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="verification_code" class="form-label">رمز التحقق</label>
                                    <input type="text" class="form-control form-control-lg text-center @error('verification_code') is-invalid @enderror" 
                                        id="verification_code" name="verification_code" placeholder="أدخل الرمز المكون من 6 أرقام" 
                                        maxlength="6" inputmode="numeric" autocomplete="one-time-code" required>
                                    @error('verification_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password" class="form-label">كلمة المرور الحالية</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                        id="password" name="password" placeholder="أدخل كلمة المرور الحالية للتأكيد" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        للتأكد من هويتك، يرجى إدخال كلمة المرور الحالية
                                    </div>
                                </div>
                                
                                <div class="recovery-codes mb-4">
                                    <h6 class="mb-3">رموز الاسترداد</h6>
                                    <p class="text-muted small mb-3">
                                        احتفظ برموز الاسترداد التالية في مكان آمن. يمكنك استخدامها لاستعادة الوصول إلى حسابك في حالة فقدان هاتفك أو تعذر الوصول إلى تطبيق المصادقة.
                                        <strong>لن يتم عرض هذه الرموز مرة أخرى!</strong>
                                    </p>
                                    
                                    <div class="recovery-codes-container bg-light p-3 rounded mb-3">
                                        <div class="row row-cols-1 row-cols-md-2 g-2">
                                            @foreach($recoveryCodes as $code)
                                                <div class="col">
                                                    <code>{{ $code }}</code>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="printRecoveryCodes">
                                            <i class="bi bi-printer me-1"></i> طباعة رموز الاسترداد
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="confirm_backup" name="confirm_backup" required>
                                    <label class="form-check-label" for="confirm_backup">
                                        أؤكد أنني قمت بحفظ رموز الاسترداد في مكان آمن
                                    </label>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary prev-step" data-step="3">
                                        <i class="bi bi-arrow-right me-1"></i> الخطوة السابقة
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-shield-check me-1"></i> تفعيل المصادقة الثنائية
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .step-progress {
        display: flex;
        align-items: center;
        max-width: 600px;
    }
    
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }
    
    .step-item.active .step-circle {
        background-color: #0d6efd;
        color: white;
    }
    
    .step-item.completed .step-circle {
        background-color: #198754;
        color: white;
    }
    
    .step-line {
        flex: 1;
        height: 2px;
        background-color: #e9ecef;
        margin: 0 8px;
    }
    
    .step-item.active ~ .step-line, 
    .step-item.completed ~ .step-line {
        background-color: #0d6efd;
    }
    
    .recovery-codes-container {
        font-family: monospace;
        font-size: 1rem;
    }
    
    #verification_code {
        letter-spacing: 0.5em;
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
    // Navegación entre pasos
    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function() {
            const currentStep = parseInt(this.dataset.step);
            const nextStep = currentStep + 1;
            
            // Ocultar paso actual
            document.getElementById('step' + currentStep).classList.add('d-none');
            
            // Mostrar siguiente paso
            document.getElementById('step' + nextStep).classList.remove('d-none');
            
            // Actualizar indicadores de progreso
            updateStepProgress(nextStep);
        });
    });
    
    document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', function() {
            const currentStep = parseInt(this.dataset.step);
            const prevStep = currentStep - 1;
            
            // Ocultar paso actual
            document.getElementById('step' + currentStep).classList.add('d-none');
            
            // Mostrar paso anterior
            document.getElementById('step' + prevStep).classList.remove('d-none');
            
            // Actualizar indicadores de progreso
            updateStepProgress(prevStep);
        });
    });
    
    function updateStepProgress(activeStep) {
        // Resetear todos los pasos
        document.querySelectorAll('.step-item').forEach((item, index) => {
            item.classList.remove('active', 'completed');
            
            // Marcar pasos completados
            if (index + 1 < activeStep) {
                item.classList.add('completed');
            }
            
            // Marcar paso activo
            if (index + 1 === activeStep) {
                item.classList.add('active');
            }
        });
    }
    
    // Copiar clave secreta
    document.getElementById('copySecretBtn').addEventListener('click', function() {
        const secretKey = document.getElementById('secretKey');
        secretKey.select();
        document.execCommand('copy');
        
        // Cambiar texto del botón temporalmente
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="bi bi-check"></i> تم النسخ';
        
        setTimeout(() => {
            this.innerHTML = originalText;
        }, 2000);
    });
    
    // Formatear código de verificación
    document.getElementById('verification_code').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 6);
    });
    
    // Imprimir códigos de recuperación
    document.getElementById('printRecoveryCodes').addEventListener('click', function() {
        const codesContainer = document.querySelector('.recovery-codes-container').innerHTML;
        const printWindow = window.open('', '_blank');
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>رموز استرداد المصادقة الثنائية - {{ config('app.name') }}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        h1 { font-size: 18px; margin-bottom: 10px; }
                        p { margin-bottom: 20px; }
                        .codes { font-family: monospace; }
                    </style>
                </head>
                <body>
                    <h1>رموز استرداد المصادقة الثنائية - {{ config('app.name') }}</h1>
                    <p>احتفظ بهذه الرموز في مكان آمن. يمكنك استخدامها لاستعادة الوصول إلى حسابك في حالة فقدان هاتفك.</p>
                    <div class="codes">${codesContainer}</div>
                    <p>تاريخ الإنشاء: ${new Date().toLocaleDateString()}</p>
                </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    });
</script>
@endpush
