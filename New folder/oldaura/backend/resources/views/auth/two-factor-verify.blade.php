@extends('layouts.auth')

@section('title', 'التحقق من المصادقة الثنائية')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-sm-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/img/2fa-icon.svg') }}" alt="المصادقة الثنائية" class="img-fluid mb-3" style="height: 80px;">
                        <h4>التحقق من المصادقة الثنائية</h4>
                        <p class="text-muted">أدخل رمز التحقق من تطبيق المصادقة الخاص بك</p>
                    </div>

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="verification-form">
                        <form action="{{ route('two-factor.validate') }}" method="POST" id="twoFactorForm">
                            @csrf
                            <div class="mb-4">
                                <label for="code" class="form-label">رمز التحقق</label>
                                <div class="verification-code-input">
                                    <input type="text" class="form-control form-control-lg text-center @error('code') is-invalid @enderror" 
                                        id="code" name="code" placeholder="أدخل الرمز المكون من 6 أرقام" 
                                        maxlength="6" inputmode="numeric" autocomplete="one-time-code" autofocus>
                                </div>
                                <div class="form-text text-center">
                                    <div id="codeTimer" class="mb-1">
                                        ينتهي الرمز خلال <span id="countdown">30</span> ثانية
                                    </div>
                                    <small>افتح تطبيق المصادقة على هاتفك للحصول على رمز التحقق</small>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember_device" name="remember_device" value="1">
                                <label class="form-check-label" for="remember_device">تذكر هذا الجهاز لمدة 30 يوم</label>
                                <div class="form-text">
                                    <small>لن يُطلب منك إدخال رمز التحقق مرة أخرى عند تسجيل الدخول من هذا الجهاز</small>
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-shield-check me-2"></i> تحقق وتسجيل الدخول
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <p class="mb-2">
                                <a href="#" data-bs-toggle="collapse" data-bs-target="#recoveryCodeForm" aria-expanded="false">
                                    لا يمكنك الوصول إلى تطبيق المصادقة؟
                                </a>
                            </p>
                            <div class="collapse" id="recoveryCodeForm">
                                <div class="card card-body bg-light mb-3">
                                    <h6 class="mb-3">استخدام رمز الاسترداد</h6>
                                    <p class="text-muted small mb-3">
                                        إذا فقدت الوصول إلى تطبيق المصادقة، يمكنك استخدام أحد رموز الاسترداد التي تم توفيرها لك عند إعداد المصادقة الثنائية.
                                        <strong>ملاحظة: سيتم استهلاك رمز الاسترداد بعد استخدامه.</strong>
                                    </p>
                                    <form action="{{ route('two-factor.validate-recovery') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <input type="text" class="form-control @error('recovery_code') is-invalid @enderror" 
                                                id="recovery_code" name="recovery_code" placeholder="أدخل رمز الاسترداد">
                                            @error('recovery_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-outline-primary">
                                            استخدام رمز الاسترداد
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <a href="{{ route('login') }}" class="btn btn-link">
                                <i class="bi bi-arrow-right me-1"></i> العودة إلى تسجيل الدخول
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .verification-code-input input {
        letter-spacing: 0.5em;
        font-weight: bold;
    }
    
    .verification-code-input {
        position: relative;
    }
    
    .verification-code-input::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(to right, #007bff, #6610f2);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .verification-code-input:focus-within::after {
        transform: scaleX(1);
    }
</style>
@endpush

@push('scripts')
<script>
    // Formatear el campo de código de verificación
    document.getElementById('code').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 6);
        
        // Enviar automáticamente cuando se ingresan 6 dígitos
        if (e.target.value.length === 6) {
            setTimeout(() => {
                document.getElementById('twoFactorForm').submit();
            }, 300);
        }
    });

    // Temporizador de cuenta regresiva para el código
    let countdown = 30;
    const countdownElement = document.getElementById('countdown');
    
    function updateCountdown() {
        countdownElement.textContent = countdown;
        
        if (countdown <= 0) {
            countdown = 30;
        } else {
            countdown--;
            setTimeout(updateCountdown, 1000);
        }
    }
    
    updateCountdown();
</script>
@endpush
