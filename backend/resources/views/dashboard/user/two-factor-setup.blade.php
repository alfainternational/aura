@extends('layouts.app')

@section('title', 'إعداد المصادقة الثنائية')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">إعداد المصادقة الثنائية</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="bi bi-info-circle-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">تعزيز أمان حسابك</h5>
                                <p class="mb-0">المصادقة الثنائية توفر طبقة إضافية من الحماية لحسابك. عند تفعيلها، ستحتاج إلى إدخال رمز مؤقت من تطبيق المصادقة على هاتفك بالإضافة إلى كلمة المرور عند تسجيل الدخول.</p>
                            </div>
                        </div>
                    </div>

                    <div class="setup-steps mt-4">
                        <div class="step-1 mb-4">
                            <h5>الخطوة 1: تثبيت تطبيق المصادقة</h5>
                            <p>قم بتثبيت أحد تطبيقات المصادقة التالية على هاتفك:</p>
                            <div class="row text-center">
                                <div class="col-md-4 mb-3">
                                    <div class="authenticator-app p-3 border rounded">
                                        <img src="{{ asset('assets/img/google-authenticator.png') }}" alt="Google Authenticator" class="img-fluid mb-2" style="height: 60px;">
                                        <h6>Google Authenticator</h6>
                                        <div class="mt-2">
                                            <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="bi bi-android2"></i> Android
                                            </a>
                                            <a href="https://apps.apple.com/app/google-authenticator/id388497605" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-apple"></i> iOS
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="authenticator-app p-3 border rounded">
                                        <img src="{{ asset('assets/img/authy.png') }}" alt="Authy" class="img-fluid mb-2" style="height: 60px;">
                                        <h6>Authy</h6>
                                        <div class="mt-2">
                                            <a href="https://play.google.com/store/apps/details?id=com.authy.authy" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="bi bi-android2"></i> Android
                                            </a>
                                            <a href="https://apps.apple.com/app/authy/id494168017" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-apple"></i> iOS
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="authenticator-app p-3 border rounded">
                                        <img src="{{ asset('assets/img/microsoft-authenticator.png') }}" alt="Microsoft Authenticator" class="img-fluid mb-2" style="height: 60px;">
                                        <h6>Microsoft Authenticator</h6>
                                        <div class="mt-2">
                                            <a href="https://play.google.com/store/apps/details?id=com.azure.authenticator" target="_blank" class="btn btn-sm btn-outline-primary me-1">
                                                <i class="bi bi-android2"></i> Android
                                            </a>
                                            <a href="https://apps.apple.com/app/microsoft-authenticator/id983156458" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-apple"></i> iOS
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step-2 mb-4">
                            <h5>الخطوة 2: مسح رمز QR</h5>
                            <p>افتح تطبيق المصادقة على هاتفك وامسح رمز QR التالي:</p>
                            <div class="row">
                                <div class="col-md-6 offset-md-3">
                                    <div class="qr-code-container text-center p-4 border rounded bg-light">
                                        <div class="qr-code mb-3">
                                            {!! $qrCode !!}
                                        </div>
                                        <div class="manual-key">
                                            <p class="mb-1 text-muted">أو أدخل هذا المفتاح يدويًا:</p>
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="{{ $user->two_factor_secret }}" readonly id="secretKey">
                                                <button class="btn btn-outline-secondary" type="button" onclick="copySecretKey()">
                                                    <i class="bi bi-clipboard"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="step-3 mb-4">
                            <h5>الخطوة 3: التحقق من الإعداد</h5>
                            <p>أدخل الرمز المؤقت الذي يظهر في تطبيق المصادقة للتحقق من الإعداد:</p>
                            <form action="{{ route('user.two-factor.enable') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="verification_code" class="form-label">رمز التحقق</label>
                                            <input type="text" class="form-control @error('verification_code') is-invalid @enderror" id="verification_code" name="verification_code" placeholder="أدخل الرمز المكون من 6 أرقام" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
                                            @error('verification_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">كلمة المرور الحالية</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="أدخل كلمة المرور الحالية للتأكيد">
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <a href="{{ route('user.security') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-right me-1"></i> العودة
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-shield-check me-1"></i> تفعيل المصادقة الثنائية
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="step-4">
                            <h5>الخطوة 4: رموز الاسترداد</h5>
                            <p>احتفظ برموز الاسترداد التالية في مكان آمن. يمكنك استخدامها للوصول إلى حسابك في حالة فقدان هاتفك أو عدم تمكنك من الوصول إلى تطبيق المصادقة:</p>
                            <div class="recovery-codes p-3 border rounded bg-light mb-3">
                                <div class="row">
                                    @foreach(array_chunk($recoveryCodes, 4) as $chunk)
                                        <div class="col-md-6">
                                            <ul class="list-unstyled mb-0">
                                                @foreach($chunk as $code)
                                                    <li class="mb-2 font-monospace">{{ wordwrap($code, 5, ' ', true) }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-outline-primary" onclick="printRecoveryCodes()">
                                    <i class="bi bi-printer me-1"></i> طباعة رموز الاسترداد
                                </button>
                                <button type="button" class="btn btn-outline-primary ms-2" onclick="downloadRecoveryCodes()">
                                    <i class="bi bi-download me-1"></i> تنزيل رموز الاسترداد
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copySecretKey() {
        const secretKey = document.getElementById('secretKey');
        secretKey.select();
        document.execCommand('copy');
        alert('تم نسخ المفتاح السري');
    }

    function printRecoveryCodes() {
        const content = document.querySelector('.recovery-codes').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>رموز استرداد المصادقة الثنائية - {{ config('app.name') }}</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        h1 { font-size: 18px; margin-bottom: 20px; }
                        ul { list-style-type: none; padding: 0; }
                        li { margin-bottom: 10px; font-family: monospace; }
                        .footer { margin-top: 30px; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <h1>رموز استرداد المصادقة الثنائية - {{ config('app.name') }}</h1>
                    <p>احتفظ بهذه الرموز في مكان آمن. يمكنك استخدامها للوصول إلى حسابك في حالة فقدان هاتفك.</p>
                    ${content}
                    <div class="footer">
                        <p>تم إنشاء هذه الرموز في: ${new Date().toLocaleString()}</p>
                        <p>البريد الإلكتروني: {{ $user->email }}</p>
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }

    function downloadRecoveryCodes() {
        const codes = [
            @foreach($recoveryCodes as $code)
                "{{ $code }}",
            @endforeach
        ];
        
        const content = "رموز استرداد المصادقة الثنائية - {{ config('app.name') }}\n" +
                      "احتفظ بهذه الرموز في مكان آمن\n" +
                      "تم إنشاء هذه الرموز في: " + new Date().toLocaleString() + "\n" +
                      "البريد الإلكتروني: {{ $user->email }}\n\n" +
                      codes.join("\n");
        
        const blob = new Blob([content], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'recovery-codes-{{ config('app.name') }}.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    // Formatear el campo de código de verificación
    document.getElementById('verification_code').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '').substring(0, 6);
    });
</script>
@endpush
