<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>إنشاء حساب جديد - منصة أورا</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #7e22ce;
            --primary-dark: #6b21a8;
            --secondary-color: #06b6d4;
            --accent-color: #f97316;
            --text-color: #1f2937;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --bg-dark: #111827;
            --border-color: #e5e7eb;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        
        .auth-container {
            width: 100%;
            max-width: 1200px;
            display: flex;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            overflow: hidden;
            background-color: #fff;
        }
        
        .auth-banner {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            display: none;
        }
        
        .auth-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('/images/pattern.svg');
            opacity: 0.1;
        }
        
        .auth-banner h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .auth-banner p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }
        
        .auth-banner .features {
            position: relative;
            z-index: 1;
        }
        
        .auth-banner .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .auth-banner .feature-icon {
            width: 32px;
            height: 32px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
        }
        
        .auth-form {
            flex: 1;
            padding: 40px;
            max-width: 550px;
            margin: 0 auto;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-container img {
            width: 120px;
            height: auto;
        }
        
        .auth-heading {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .auth-heading h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 10px;
        }
        
        .auth-heading p {
            color: var(--text-light);
            font-size: 1rem;
        }
        
        .auth-tabs {
            display: flex;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }
        
        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 15px;
            background-color: #fff;
            color: var(--text-light);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .auth-tab.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .form-floating {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-floating > label {
            position: absolute;
            top: 0;
            right: 0;
            padding: 1rem 0.75rem;
            height: 3.5rem;
            pointer-events: none;
            border: 1px solid transparent;
            transform-origin: 100% 0;
            transition: opacity .1s ease-in-out,transform .1s ease-in-out;
            color: var(--text-light);
        }
        
        .form-floating > .form-control {
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
        }
        
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            opacity: .65;
            transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
        }
        
        .form-control {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(126, 34, 206, 0.25);
        }
        
        .form-select {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            height: calc(3.5rem + 2px);
            padding: 1rem 0.75rem;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(126, 34, 206, 0.25);
        }
        
        .user-type-selector {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .user-type-btn {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fff;
        }
        
        .user-type-btn.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .user-type-btn i {
            font-size: 1.5rem;
            margin-bottom: 10px;
            display: block;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .auth-divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
        }
        
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: var(--border-color);
        }
        
        .auth-divider span {
            padding: 0 15px;
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .phone-input-group {
            display: flex;
            align-items: center;
        }
        
        .phone-input-group .country-code {
            width: 120px;
            margin-left: 10px;
        }
        
        .phone-input-group .phone-number {
            flex: 1;
        }
        
        .location-detector {
            background-color: #f9fafb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
        }
        
        .location-detector .location-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .location-detector .location-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(126, 34, 206, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            color: var(--primary-color);
        }
        
        .location-detector .location-text {
            flex: 1;
        }
        
        .location-detector .location-text h5 {
            margin: 0 0 5px;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .location-detector .location-text p {
            margin: 0;
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .location-detector .detect-btn {
            background-color: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .location-detector .detect-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.3);
            color: var(--success-color);
        }
        
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
            color: var(--danger-color);
        }
        
        .alert-warning {
            background-color: rgba(245, 158, 11, 0.1);
            border-color: rgba(245, 158, 11, 0.3);
            color: var(--warning-color);
        }
        
        .invalid-feedback {
            display: block;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: var(--danger-color);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 25px;
            color: var(--text-light);
        }
        
        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .step-progress {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .step-progress::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--border-color);
            z-index: 1;
        }
        
        .step-item {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 30px;
        }
        
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .step-label {
            font-size: 0.8rem;
            color: var(--text-light);
            white-space: nowrap;
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            transition: all 0.3s ease;
        }
        
        .step-item.active .step-circle {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .step-item.active .step-label {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .step-item.completed .step-circle {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }
        
        .step-content {
            display: none;
        }
        
        .step-content.active {
            display: block;
        }
        
        .step-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn-outline-primary {
            background-color: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 30px;
        }
        
        .feature-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .feature-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        
        .feature-card-icon.messaging {
            background-color: rgba(126, 34, 206, 0.1);
            color: var(--primary-color);
        }
        
        .feature-card-icon.payments {
            background-color: rgba(6, 182, 212, 0.1);
            color: var(--secondary-color);
        }
        
        .feature-card-icon.security {
            background-color: rgba(249, 115, 22, 0.1);
            color: var(--accent-color);
        }
        
        .feature-card-icon.commerce {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }
        
        .feature-card h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .feature-card p {
            color: var(--text-light);
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        @media (min-width: 992px) {
            .auth-banner {
                display: flex;
            }
        }
        
        @media (max-width: 767px) {
            .auth-form {
                padding: 30px 20px;
            }
            
            .user-type-selector {
                grid-template-columns: 1fr;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-banner">
            <h2>انضم إلى منصة أورا</h2>
            <p>المنصة المتكاملة للتواصل والمراسلة والتجارة الإلكترونية في السودان. تواصل، تسوق، وأدر أموالك في مكان واحد.</p>
            
            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div>مراسلة فورية بتصميم عصري وسهل الاستخدام</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div>مكالمات صوتية بجودة عالية وبدون انقطاع</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>محفظة إلكترونية لإدارة أموالك وإجراء المعاملات</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>تسوق إلكتروني مع خيارات دفع متعددة وآمنة</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>حماية وتشفير كامل للبيانات والمحادثات</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>دعم المحادثات الجماعية ومشاركة الملفات</div>
                </div>
            </div>
        </div>
        
        <div class="auth-form">
            <div class="logo-container">
                <img src="/images/logo.png" alt="أورا">
            </div>
            
            <div class="auth-heading">
                <h3>إنشاء حساب جديد</h3>
                <p>انضم إلى منصة أورا واستمتع بتجربة تواصل فريدة</p>
            </div>
            
            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="auth-tab">تسجيل الدخول</a>
                <div class="auth-tab active">إنشاء حساب</div>
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
            
            <div class="step-progress">
                <div class="step-item active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">نوع الحساب</div>
                </div>
                <div class="step-item" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">المعلومات الشخصية</div>
                </div>
                <div class="step-item" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">معلومات الاتصال</div>
                </div>
                <div class="step-item" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">التأكيد</div>
                </div>
            </div>
            
            <form class="register-form" method="POST" action="{{ route('register') }}" id="register-form">
                @csrf
                
                <div class="step-content active" data-step="1">
                    <h4 class="mb-4">اختر نوع الحساب</h4>
                    
                    <div class="user-type-selector">
                        <div class="user-type-btn active" data-type="customer">
                            <i class="fas fa-user"></i>
                            <div>عميل</div>
                            <small class="text-muted">للأفراد والاستخدام الشخصي</small>
                        </div>
                        <div class="user-type-btn" data-type="merchant">
                            <i class="fas fa-store"></i>
                            <div>تاجر</div>
                            <small class="text-muted">لأصحاب المتاجر والشركات</small>
                        </div>
                        <div class="user-type-btn" data-type="agent">
                            <i class="fas fa-building"></i>
                            <div>وكيل</div>
                            <small class="text-muted">لوكلاء الخدمات ونقاط البيع</small>
                        </div>
                        <div class="user-type-btn" data-type="messenger">
                            <i class="fas fa-motorcycle"></i>
                            <div>مندوب</div>
                            <small class="text-muted">لخدمات التوصيل والشحن</small>
                        </div>
                        <input type="hidden" name="user_type" id="user_type" value="customer">
                    </div>
                    
                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-card-icon messaging">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h4>تواصل بسهولة</h4>
                            <p>مراسلة فورية مع الأصدقاء والعائلة ومكالمات صوتية عالية الجودة</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-card-icon payments">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <h4>إدارة أموالك</h4>
                            <p>محفظة إلكترونية آمنة لإجراء المعاملات المالية وتحويل الأموال</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-card-icon security">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h4>أمان وخصوصية</h4>
                            <p>تشفير كامل للبيانات والمحادثات مع مصادقة ثنائية لحماية حسابك</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-card-icon commerce">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h4>تسوق إلكتروني</h4>
                            <p>تصفح وشراء المنتجات من المتاجر المحلية مع خيارات دفع متعددة</p>
                        </div>
                    </div>
                    
                    <div class="step-buttons">
                        <div></div>
                        <button type="button" class="btn btn-primary next-step" data-step="1">التالي <i class="fas fa-arrow-left ms-2"></i></button>
                    </div>
                </div>
                
                <div class="step-content" data-step="2">
                    <h4 class="mb-4">المعلومات الشخصية</h4>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder=" " required>
                                <label for="first_name">الاسم الأول</label>
                                @error('first_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder=" " required>
                                <label for="last_name">الاسم الأخير</label>
                                @error('last_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" placeholder=" " required>
                        <label for="username">اسم المستخدم</label>
                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder=" " required>
                        <label for="email">البريد الإلكتروني</label>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder=" " required>
                        <label for="password">كلمة المرور</label>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" placeholder=" " required>
                        <label for="password_confirmation">تأكيد كلمة المرور</label>
                        @error('password_confirmation')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="step-buttons">
                        <button type="button" class="btn btn-outline-primary prev-step" data-step="2"><i class="fas fa-arrow-right me-2"></i> السابق</button>
                        <button type="button" class="btn btn-primary next-step" data-step="2">التالي <i class="fas fa-arrow-left ms-2"></i></button>
                    </div>
                </div>
                
                <div class="step-content" data-step="3">
                    <h4 class="mb-4">معلومات الاتصال</h4>
                    
                    <div class="location-detector">
                        <div class="location-info">
                            <div class="location-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="location-text">
                                <h5>موقعك الحالي</h5>
                                <p id="location-display">
                                    <span id="country-name">{{ $defaultCountry->name ?? 'جاري تحديد الموقع...' }}</span>
                                    <span id="city-name">{{ isset($defaultCity) ? ' - ' . $defaultCity->name : '' }}</span>
                                </p>
                            </div>
                        </div>
                        <input type="hidden" id="country_id" name="country_id" value="{{ old('country_id', $defaultCountry->id ?? '') }}">
                        <input type="hidden" id="city_id" name="city_id" value="{{ old('city_id', $defaultCity->id ?? '') }}">
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', '') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', '') }}">
                        
                        <button type="button" id="detect-location-btn" class="btn btn-sm btn-outline-primary mb-2">
                            <i class="fas fa-location-arrow"></i> تحديد الموقع تلقائياً
                        </button>
                        
                        <button type="button" id="manual-location-btn" class="btn btn-sm btn-outline-secondary mb-2 ms-2">
                            <i class="fas fa-edit"></i> تحديد الموقع يدوياً
                        </button>
                        
                        <div id="location-message" class="alert alert-info mt-2" style="display: none;"></div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <div class="phone-input-group">
                            <div class="form-floating country-code">
                                <select class="form-select @error('country_code') is-invalid @enderror" id="country_code" name="country_code" required>
                                    <option value="+249" {{ old('country_code') == '+249' ? 'selected' : '' }}>+249</option>
                                    <option value="+966" {{ old('country_code') == '+966' ? 'selected' : '' }}>+966</option>
                                </select>
                                <label for="country_code">الرمز</label>
                            </div>
                            <div class="form-floating phone-number">
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder=" " required>
                                <label for="phone">رقم الهاتف</label>
                            </div>
                        </div>
                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}" placeholder=" ">
                        <label for="address">العنوان</label>
                        @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div id="merchant-fields" style="display: none;">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name') }}" placeholder=" ">
                            <label for="business_name">اسم النشاط التجاري</label>
                            @error('business_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="form-floating mb-3">
                            <select class="form-select @error('business_type') is-invalid @enderror" id="business_type" name="business_type">
                                <option value="">اختر نوع النشاط</option>
                                <option value="retail" {{ old('business_type') == 'retail' ? 'selected' : '' }}>تجارة التجزئة</option>
                                <option value="wholesale" {{ old('business_type') == 'wholesale' ? 'selected' : '' }}>تجارة الجملة</option>
                                <option value="services" {{ old('business_type') == 'services' ? 'selected' : '' }}>خدمات</option>
                                <option value="food" {{ old('business_type') == 'food' ? 'selected' : '' }}>مطاعم وأغذية</option>
                                <option value="technology" {{ old('business_type') == 'technology' ? 'selected' : '' }}>تكنولوجيا ومعلومات</option>
                                <option value="other" {{ old('business_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            <label for="business_type">نوع النشاط التجاري</label>
                            @error('business_type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="step-buttons">
                        <button type="button" class="btn btn-outline-primary prev-step" data-step="3"><i class="fas fa-arrow-right me-2"></i> السابق</button>
                        <button type="button" class="btn btn-primary next-step" data-step="3">التالي <i class="fas fa-arrow-left ms-2"></i></button>
                    </div>
                </div>
                
                <div class="step-content" data-step="4">
                    <h4 class="mb-4">تأكيد المعلومات</h4>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> يرجى مراجعة المعلومات التي أدخلتها قبل إكمال عملية التسجيل.
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-3">نوع الحساب</h5>
                        <p id="summary-user-type">عميل</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-3">المعلومات الشخصية</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>الاسم الكامل:</strong> <span id="summary-full-name"></span></p>
                                <p><strong>اسم المستخدم:</strong> <span id="summary-username"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>البريد الإلكتروني:</strong> <span id="summary-email"></span></p>
                                <p><strong>رقم الهاتف:</strong> <span id="summary-phone"></span></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5 class="mb-3">الموقع</h5>
                        <p><strong>البلد:</strong> <span id="summary-country"></span></p>
                        <p><strong>المدينة:</strong> <span id="summary-city"></span></p>
                        <p><strong>العنوان:</strong> <span id="summary-address"></span></p>
                    </div>
                    
                    <div id="summary-merchant-fields" class="mb-4" style="display: none;">
                        <h5 class="mb-3">معلومات النشاط التجاري</h5>
                        <p><strong>اسم النشاط:</strong> <span id="summary-business-name"></span></p>
                        <p><strong>نوع النشاط:</strong> <span id="summary-business-type"></span></p>
                    </div>
                    
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            أوافق على <a href="{{ route('terms') }}" target="_blank">شروط الاستخدام</a> و <a href="{{ route('privacy') }}" target="_blank">سياسة الخصوصية</a>
                        </label>
                        @error('terms')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="step-buttons">
                        <button type="button" class="btn btn-outline-primary prev-step" data-step="4"><i class="fas fa-arrow-right me-2"></i> السابق</button>
                        <button type="submit" class="btn btn-primary">إنشاء الحساب <i class="fas fa-user-plus ms-2"></i></button>
                    </div>
                </div>
            </form>
            
            <div class="auth-footer">
                <p>
                    لديك حساب بالفعل؟ <a href="{{ route('login') }}">تسجيل الدخول</a>
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
            const merchantFields = document.getElementById('merchant-fields');
            const summaryMerchantFields = document.getElementById('summary-merchant-fields');
            const summaryUserType = document.getElementById('summary-user-type');
            
            userTypeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    userTypeButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    userTypeInput.value = this.dataset.type;
                    
                    // Show/hide merchant fields
                    if (this.dataset.type === 'merchant') {
                        merchantFields.style.display = 'block';
                        summaryMerchantFields.style.display = 'block';
                    } else {
                        merchantFields.style.display = 'none';
                        summaryMerchantFields.style.display = 'none';
                    }
                    
                    // Update summary
                    updateSummary();
                });
            });
            
            // Multi-step form
            const stepItems = document.querySelectorAll('.step-item');
            const stepContents = document.querySelectorAll('.step-content');
            const nextButtons = document.querySelectorAll('.next-step');
            const prevButtons = document.querySelectorAll('.prev-step');
            
            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const currentStep = parseInt(this.dataset.step);
                    const nextStep = currentStep + 1;
                    
                    // Validate current step
                    if (validateStep(currentStep)) {
                        // Hide current step
                        document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.remove('active');
                        document.querySelector(`.step-item[data-step="${currentStep}"]`).classList.remove('active');
                        document.querySelector(`.step-item[data-step="${currentStep}"]`).classList.add('completed');
                        
                        // Show next step
                        document.querySelector(`.step-content[data-step="${nextStep}"]`).classList.add('active');
                        document.querySelector(`.step-item[data-step="${nextStep}"]`).classList.add('active');
                        
                        // Update summary if going to step 4
                        if (nextStep === 4) {
                            updateSummary();
                        }
                    }
                });
            });
            
            prevButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const currentStep = parseInt(this.dataset.step);
                    const prevStep = currentStep - 1;
                    
                    // Hide current step
                    document.querySelector(`.step-content[data-step="${currentStep}"]`).classList.remove('active');
                    document.querySelector(`.step-item[data-step="${currentStep}"]`).classList.remove('active');
                    
                    // Show previous step
                    document.querySelector(`.step-content[data-step="${prevStep}"]`).classList.add('active');
                    document.querySelector(`.step-item[data-step="${prevStep}"]`).classList.add('active');
                    document.querySelector(`.step-item[data-step="${prevStep}"]`).classList.remove('completed');
                });
            });
            
            function validateStep(step) {
                let isValid = true;
                
                if (step === 1) {
                    // التحقق من اختيار نوع المستخدم
                    if (!userTypeInput.value) {
                        isValid = false;
                        alert('يرجى اختيار نوع المستخدم');
                    }
                } else if (step === 2) {
                    // التحقق من الحقول المطلوبة في الخطوة 2
                    const requiredFields = ['first_name', 'last_name', 'username', 'email', 'password', 'password_confirmation'];
                    requiredFields.forEach(field => {
                        const input = document.getElementById(field);
                        if (!input.value.trim()) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });
                    
                    // التحقق من تطابق كلمات المرور
                    const password = document.getElementById('password');
                    const confirmPassword = document.getElementById('password_confirmation');
                    if (password.value !== confirmPassword.value) {
                        confirmPassword.classList.add('is-invalid');
                        if (!confirmPassword.nextElementSibling || !confirmPassword.nextElementSibling.classList.contains('invalid-feedback')) {
                            const feedback = document.createElement('div');
                            feedback.classList.add('invalid-feedback');
                            feedback.textContent = 'كلمات المرور غير متطابقة';
                            confirmPassword.parentNode.appendChild(feedback);
                        }
                        isValid = false;
                    }
                } else if (step === 3) {
                    // التحقق من الحقول المطلوبة في الخطوة 3
                    const requiredFields = ['phone'];
                    requiredFields.forEach(field => {
                        const input = document.getElementById(field);
                        if (!input.value.trim()) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            input.classList.remove('is-invalid');
                        }
                    });
                    
                    // التحقق من معلومات الموقع
                    const countryId = document.getElementById('country_id');
                    const cityId = document.getElementById('city_id');
                    
                    // إذا لم يتم تحديد الموقع تلقائياً، تحقق من الاختيار اليدوي
                    if ((!countryId.value || !cityId.value) && document.getElementById('manual-location-selection')) {
                        const manualCountryId = document.querySelector('#manual-location-selection #country_id');
                        const manualCityId = document.querySelector('#manual-location-selection #city_id');
                        
                        if (manualCountryId && !manualCountryId.value) {
                            manualCountryId.classList.add('is-invalid');
                            isValid = false;
                        }
                        
                        if (manualCityId && !manualCityId.value) {
                            manualCityId.classList.add('is-invalid');
                            isValid = false;
                        }
                        
                        // نقل قيم الاختيار اليدوي إلى الحقول الرئيسية
                        if (manualCountryId && manualCountryId.value) {
                            countryId.value = manualCountryId.value;
                        }
                        
                        if (manualCityId && manualCityId.value) {
                            cityId.value = manualCityId.value;
                        }
                    }
                    
                    // التحقق من حقول التاجر إذا تم اختيار نوع المستخدم "تاجر"
                    if (userTypeInput.value === 'merchant') {
                        const merchantRequiredFields = ['business_name', 'business_type'];
                        merchantRequiredFields.forEach(field => {
                            const input = document.getElementById(field);
                            if (!input.value.trim()) {
                                input.classList.add('is-invalid');
                                isValid = false;
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });
                    }
                }
                
                return isValid;
            }
            
            function updateSummary() {
                // Update user type
                const userType = userTypeInput.value;
                let userTypeText = 'عميل';
                if (userType === 'merchant') userTypeText = 'تاجر';
                else if (userType === 'agent') userTypeText = 'وكيل';
                else if (userType === 'messenger') userTypeText = 'مندوب';
                summaryUserType.textContent = userTypeText;
                
                // Update personal info
                document.getElementById('summary-full-name').textContent = `${document.getElementById('first_name').value} ${document.getElementById('last_name').value}`;
                document.getElementById('summary-username').textContent = document.getElementById('username').value;
                document.getElementById('summary-email').textContent = document.getElementById('email').value;
                document.getElementById('summary-phone').textContent = `${document.getElementById('country_code').value} ${document.getElementById('phone').value}`;
                
                // Update location
                document.getElementById('summary-country').textContent = document.getElementById('country-name').textContent;
                document.getElementById('summary-city').textContent = document.getElementById('city-name').textContent.replace(' - ', '');
                document.getElementById('summary-address').textContent = document.getElementById('address').value || 'غير محدد';
                
                // Update merchant fields
                if (userType === 'merchant') {
                    document.getElementById('summary-business-name').textContent = document.getElementById('business_name').value || 'غير محدد';
                    
                    const businessTypeSelect = document.getElementById('business_type');
                    const selectedOption = businessTypeSelect.options[businessTypeSelect.selectedIndex];
                    document.getElementById('summary-business-type').textContent = selectedOption.textContent || 'غير محدد';
                }
            }
            
            // Geolocation
            const detectLocationBtn = document.getElementById('detect-location-btn');
            const manualLocationBtn = document.getElementById('manual-location-btn');
            const countryNameElement = document.getElementById('country-name');
            const cityNameElement = document.getElementById('city-name');
            const countryIdInput = document.getElementById('country_id');
            const cityIdInput = document.getElementById('city_id');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            
            if (detectLocationBtn) {
                detectLocationBtn.addEventListener('click', function() {
                    if (navigator.geolocation) {
                        detectLocationBtn.disabled = true;
                        detectLocationBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري تحديد الموقع...';
                        
                        // إخفاء خيار الاختيار اليدوي إذا كان ظاهرًا
                        if (document.getElementById('manual-location-selection')) {
                            document.getElementById('manual-location-selection').style.display = 'none';
                        }
                        
                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                const latitude = position.coords.latitude;
                                const longitude = position.coords.longitude;
                                
                                latitudeInput.value = latitude;
                                longitudeInput.value = longitude;
                                
                                // استدعاء API لتحويل الإحداثيات إلى معلومات البلد والمدينة
                                fetch(`/aura/backend/public/api/geocode?lat=${latitude}&lon=${longitude}`)
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('فشل في الاتصال بالخادم');
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        if (data.success) {
                                            countryNameElement.textContent = data.country.name;
                                            cityNameElement.textContent = ` - ${data.city.name}`;
                                            countryIdInput.value = data.country.id;
                                            cityIdInput.value = data.city.id;
                                            
                                            detectLocationBtn.disabled = false;
                                            detectLocationBtn.innerHTML = '<i class="fas fa-check"></i> تم تحديد الموقع';
                                            setTimeout(() => {
                                                detectLocationBtn.innerHTML = '<i class="fas fa-location-arrow"></i> تحديد الموقع تلقائياً';
                                            }, 3000);
                                            
                                            // إظهار رسالة نجاح
                                            showLocationMessage('success', 'تم تحديد موقعك بنجاح');
                                        } else {
                                            throw new Error(data.message || 'فشل في تحديد الموقع');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        
                                        // إظهار رسالة الخطأ للمستخدم
                                        showLocationMessage('danger', 'فشل في تحديد الموقع: ' + error.message);
                                        
                                        // إظهار خيار الاختيار اليدوي
                                        showManualLocationSelection();
                                        
                                        detectLocationBtn.disabled = false;
                                        detectLocationBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> حاول مرة أخرى';
                                    });
                            },
                            function(error) {
                                console.error('Geolocation error:', error);
                                countryNameElement.textContent = 'فشل في تحديد الموقع';
                                cityNameElement.textContent = '';
                                
                                detectLocationBtn.disabled = false;
                                detectLocationBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> حاول مرة أخرى';
                            }
                        );
                    } else {
                        countryNameElement.textContent = 'خدمة تحديد الموقع غير مدعومة في متصفحك';
                        cityNameElement.textContent = '';
                    }
                });
            }
            
            // إضافة معالج حدث لزر الاختيار اليدوي
            if (manualLocationBtn) {
                manualLocationBtn.addEventListener('click', function() {
                    // إظهار خيار الاختيار اليدوي
                    showManualLocationSelection();
                    
                    // تحميل قائمة البلدان من الخادم
                    fetch('/aura/backend/public/api/countries')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data) {
                                // الحصول على عنصر القائمة المنسدلة للبلدان
                                const countrySelect = document.querySelector('#manual-location-selection #country_id');
                                if (countrySelect) {
                                    // مسح الخيارات الحالية
                                    countrySelect.innerHTML = '<option value="">اختر بلدك</option>';
                                    
                                    // إضافة البلدان من البيانات
                                    data.data.forEach(country => {
                                        const option = document.createElement('option');
                                        option.value = country.id;
                                        option.textContent = country.name_ar || country.name;
                                        countrySelect.appendChild(option);
                                    });
                                    
                                    // إضافة معالج حدث لتغيير البلد
                                    countrySelect.addEventListener('change', function() {
                                        if (this.value) {
                                            // تحميل قائمة المدن للبلد المحدد
                                            fetch(`/aura/backend/public/api/cities/${this.value}`)
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.success && data.data) {
                                                        // الحصول على عنصر القائمة المنسدلة للمدن
                                                        const citySelect = document.querySelector('#manual-location-selection #city_id');
                                                        if (citySelect) {
                                                            // مسح الخيارات الحالية
                                                            citySelect.innerHTML = '<option value="">اختر مدينتك</option>';
                                                            
                                                            // إضافة المدن من البيانات
                                                            data.data.forEach(city => {
                                                                const option = document.createElement('option');
                                                                option.value = city.id;
                                                                option.textContent = city.name;
                                                                citySelect.appendChild(option);
                                                            });
                                                        }
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Error loading cities:', error);
                                                });
                                        }
                                    });
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error loading countries:', error);
                        });
                });
            }
            
            function showLocationMessage(type, message) {
                const locationMessageElement = document.getElementById('location-message');
                if (locationMessageElement) {
                    locationMessageElement.classList.remove('alert-success', 'alert-danger');
                    locationMessageElement.classList.add(`alert-${type}`);
                    locationMessageElement.textContent = message;
                } else {
                    const messageElement = document.createElement('div');
                    messageElement.id = 'location-message';
                    messageElement.classList.add(`alert-${type}`);
                    messageElement.textContent = message;
                    document.querySelector('.location-detector').appendChild(messageElement);
                }
            }
            
            function showManualLocationSelection() {
                const manualLocationSelectionElement = document.getElementById('manual-location-selection');
                if (manualLocationSelectionElement) {
                    manualLocationSelectionElement.style.display = 'block';
                } else {
                    const manualLocationSelectionHtml = `
                        <div id="manual-location-selection" style="display: block;">
                            <h5 class="mb-3">تحديد الموقع يدوياً</h5>
                            <div class="form-floating mb-3">
                                <select class="form-select" id="country_id" name="country_id">
                                    <option value="">اختر بلدك</option>
                                    <option value="1">السودان</option>
                                    <option value="2">مصر</option>
                                    <option value="3">السعودية</option>
                                </select>
                                <label for="country_id">البلد</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-select" id="city_id" name="city_id">
                                    <option value="">اختر مدينتك</option>
                                    <option value="1">الخرطوم</option>
                                    <option value="2">القاهرة</option>
                                    <option value="3">الرياض</option>
                                </select>
                                <label for="city_id">المدينة</label>
                            </div>
                        </div>
                    `;
                    document.querySelector('.location-detector').insertAdjacentHTML('beforeend', manualLocationSelectionHtml);
                }
            }
            
            // إضافة معالج لإرسال النموذج
            const registerForm = document.getElementById('register-form');
            if (registerForm) {
                registerForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    
                    // التحقق من صحة النموذج قبل الإرسال
                    if (!validateStep(currentStep)) {
                        return false;
                    }
                    
                    // إظهار مؤشر التحميل
                    const submitBtn = document.querySelector('button[type="submit"]');
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التسجيل...';
                    
                    // إرسال النموذج باستخدام Fetch API
                    fetch(registerForm.action, {
                        method: 'POST',
                        body: new FormData(registerForm),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        // تحويل الاستجابة إلى JSON
                        return response.json().then(data => {
                            // إضافة حالة الاستجابة إلى البيانات
                            return { ...data, status: response.status };
                        });
                    })
                    .then(data => {
                        // استعادة زر الإرسال
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        
                        if (data.status >= 200 && data.status < 300) {
                            // نجاح التسجيل
                            showSuccessModal(data.message || 'تم التسجيل بنجاح!', data.redirect || '/aura/backend/public/login');
                        } else {
                            // فشل التسجيل
                            showErrorModal(data.message || data.error || 'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.');
                            
                            // عرض أخطاء التحقق إن وجدت
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    const input = document.getElementById(field);
                                    if (input) {
                                        input.classList.add('is-invalid');
                                        
                                        // إضافة رسالة الخطأ
                                        const feedbackElement = input.nextElementSibling;
                                        if (feedbackElement && feedbackElement.classList.contains('invalid-feedback')) {
                                            feedbackElement.textContent = data.errors[field][0];
                                        } else {
                                            const feedback = document.createElement('div');
                                            feedback.classList.add('invalid-feedback');
                                            feedback.textContent = data.errors[field][0];
                                            input.parentNode.appendChild(feedback);
                                        }
                                    }
                                });
                            }
                        }
                    })
                    .catch(error => {
                        // استعادة زر الإرسال
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText;
                        
                        // عرض رسالة الخطأ
                        showErrorModal('حدث خطأ في الاتصال بالخادم. يرجى التحقق من اتصالك بالإنترنت والمحاولة مرة أخرى.');
                        console.error('Error:', error);
                    });
                });
            }
            
            // دالة لعرض نافذة منبثقة للنجاح
            function showSuccessModal(message, redirectUrl) {
                // إنشاء عناصر النافذة المنبثقة
                const modalHtml = `
                    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="successModalLabel">تم بنجاح</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center mb-4">
                                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                                    </div>
                                    <p class="text-center">${message}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" id="redirectBtn">متابعة</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // إضافة النافذة المنبثقة إلى الصفحة
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                // الحصول على عنصر النافذة المنبثقة
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                
                // عرض النافذة المنبثقة
                successModal.show();
                
                // إضافة معالج حدث لزر المتابعة
                document.getElementById('redirectBtn').addEventListener('click', function() {
                    window.location.href = redirectUrl;
                });
                
                // تحويل تلقائي بعد 3 ثوانٍ
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 3000);
            }
            
            // دالة لعرض نافذة منبثقة للخطأ
            function showErrorModal(message) {
                // إنشاء عناصر النافذة المنبثقة
                const modalHtml = `
                    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="errorModalLabel">خطأ</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center mb-4">
                                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                                    </div>
                                    <p class="text-center">${message}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // إضافة النافذة المنبثقة إلى الصفحة
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                // الحصول على عنصر النافذة المنبثقة
                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                
                // عرض النافذة المنبثقة
                errorModal.show();
            }
        });
    </script>
</body>
</html>