@extends('layouts.auth')

@section('title', 'التحقق بخطوتين - أورا')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card auth-card">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <a href="{{ route('home') }}" class="d-block auth-logo">
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="أورا" height="60" class="logo logo-dark">
                            <img src="{{ asset('assets/images/logo-light.png') }}" alt="أورا" height="60" class="logo logo-light">
                        </a>
                    </div>

                    <div class="text-center mb-4">
                        <h4 class="mb-2">التحقق بخطوتين</h4>
                        <p class="text-muted">يرجى إدخال رمز التحقق للمتابعة</p>
                    </div>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="mb-3">
                        <p class="text-muted mb-3">
                            {{ __('يرجى تأكيد الوصول إلى حسابك عن طريق إدخال رمز المصادقة المقدم من تطبيق المصادقة الخاص بك.') }}
                        </p>

                        <form method="POST" action="{{ route('two-factor.login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="code" class="form-label">{{ __('رمز التحقق') }}</label>
                                <input id="code" type="text" class="form-control" name="code" autofocus autocomplete="one-time-code" placeholder="أدخل رمز التحقق من تطبيق المصادقة">
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    {{ __('تحقق') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="text-muted mb-0">أو</p>
                    </div>

                    <div class="mt-3">
                        <p class="text-muted mb-3">
                            {{ __('لم تتمكن من الوصول إلى تطبيق المصادقة الخاص بك؟ يمكنك استخدام أحد رموز الاسترداد الطارئة.') }}
                        </p>

                        <form method="POST" action="{{ route('two-factor.login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="recovery_code" class="form-label">{{ __('رمز الاسترداد') }}</label>
                                <input id="recovery_code" type="text" class="form-control" name="recovery_code" autocomplete="one-time-code" placeholder="أدخل رمز الاسترداد">
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-outline-primary waves-effect waves-light">
                                    {{ __('استخدام رمز الاسترداد') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="mb-0">
                            <a href="{{ route('login') }}" class="fw-medium text-primary">
                                {{ __('العودة إلى تسجيل الدخول') }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
