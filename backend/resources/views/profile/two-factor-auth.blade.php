@extends('layouts.dashboard')

@section('title', 'المصادقة الثنائية')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">المصادقة الثنائية</h5>
                        <a href="{{ route('profile.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right me-1"></i> العودة للملف الشخصي
                        </a>
                    </div>
                </x-slot>

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            تساعدك المصادقة الثنائية في حماية حسابك من خلال طلب عامل أمان إضافي عند تسجيل الدخول.
                        </div>

                        @if(!auth()->user()->two_factor_enabled)
                            <div class="text-center my-4">
                                <img src="{{ asset('assets/img/2fa-illustration.svg') }}" alt="المصادقة الثنائية" class="img-fluid mb-3" style="max-height: 200px;">
                                <h4>لم تقم بتفعيل المصادقة الثنائية</h4>
                                <p class="text-muted">قم بتفعيل المصادقة الثنائية لتأمين حسابك بشكل أفضل</p>
                            </div>

                            <form action="{{ route('profile.enable-two-factor') }}" method="POST">
                                @csrf
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-shield-lock me-2"></i> تفعيل المصادقة الثنائية
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="text-center my-4">
                                <div class="mb-3 bg-success-subtle p-3 rounded-circle d-inline-block">
                                    <i class="bi bi-shield-check text-success" style="font-size: 64px;"></i>
                                </div>
                                <h4>المصادقة الثنائية مفعلة</h4>
                                <p class="text-muted">تم تأمين حسابك بنجاح باستخدام المصادقة الثنائية</p>
                            </div>

                            @if($qrCode)
                                <div class="mb-4">
                                    <h5>مسح رمز QR</h5>
                                    <p class="text-muted">
                                        قم بمسح رمز QR هذا باستخدام تطبيق المصادقة (مثل Google Authenticator) على هاتفك الذكي.
                                    </p>
                                    
                                    <div class="d-flex justify-content-center my-3">
                                        <div class="qr-code-container border p-3 rounded">
                                            {!! $qrCode !!}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($recoveryCodes)
                                <div class="mb-4">
                                    <h5>رموز الاسترداد</h5>
                                    <p class="text-muted">
                                        احفظ رموز الاسترداد هذه في مكان آمن. يمكن استخدامها للوصول إلى حسابك في حالة فقدان جهازك.
                                    </p>
                                    
                                    <div class="recovery-codes bg-light p-3 rounded mb-3">
                                        <div class="d-flex flex-wrap">
                                            @foreach($recoveryCodes as $code)
                                                <div class="recovery-code p-2 bg-white rounded m-1 border">
                                                    <code>{{ $code }}</code>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <button id="copyRecoveryCodes" class="btn btn-outline-secondary">
                                            <i class="bi bi-clipboard me-2"></i> نسخ رموز الاسترداد
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <hr class="my-4">

                            <form action="{{ route('profile.disable-two-factor') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في تعطيل المصادقة الثنائية؟ سيؤدي ذلك إلى تقليل أمان حسابك.');">
                                @csrf
                                @method('DELETE')
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-shield-x me-2"></i> تعطيل المصادقة الثنائية
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyButton = document.getElementById('copyRecoveryCodes');
        if (copyButton) {
            copyButton.addEventListener('click', function() {
                const recoveryCodes = document.querySelectorAll('.recovery-code code');
                let codeText = '';
                
                recoveryCodes.forEach(code => {
                    codeText += code.textContent.trim() + '\n';
                });
                
                navigator.clipboard.writeText(codeText).then(function() {
                    copyButton.innerHTML = '<i class="bi bi-check2 me-2"></i> تم النسخ بنجاح';
                    
                    setTimeout(function() {
                        copyButton.innerHTML = '<i class="bi bi-clipboard me-2"></i> نسخ رموز الاسترداد';
                    }, 3000);
                });
            });
        }
    });
</script>
@endpush
@endsection
