<header class="site-header">
    <div class="header-top bg-light py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center small">
                        <div class="me-3">
                            <i class="icon-envelope-alt me-1 text-primary"></i>
                            <a href="mailto:support@aura.com" class="text-muted">support@aura.com</a>
                        </div>
                        <div>
                            <i class="icon-phone-alt me-1 text-primary"></i>
                            <a href="tel:+249123456789" class="text-muted">+249 123 456 789</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-md-end small">
                        <div class="me-3">
                            <a href="{{ route('about') }}" class="text-muted">من نحن</a>
                        </div>
                        <div class="me-3">
                            <a href="{{ route('contact') }}" class="text-muted">تواصل معنا</a>
                        </div>
                        <!-- زر تبديل الوضع المظلم/الفاتح -->
                        <div class="me-3">
                            <button id="themeToggleBtn" class="btn btn-sm btn-link text-muted p-0" title="تبديل المظهر">
                                <i id="themeIcon" class="fas fa-sun"></i>
                            </button>
                        </div>
                        <div class="dropdown language-selector">
                            <button class="btn btn-sm btn-link text-muted dropdown-toggle p-0" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-globe me-1"></i> العربية
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                <li><a class="dropdown-item active" href="#">العربية</a></li>
                                <li><a class="dropdown-item" href="#">English</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/aura-logo.svg') }}" alt="AURA" height="40">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">الرئيسية</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('services.*') ? 'active' : '' }}" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            خدماتنا
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                            <li><a class="dropdown-item" href="{{ route('services.wallet') }}">المحفظة الإلكترونية</a></li>
                            <li><a class="dropdown-item" href="{{ route('services.commerce') }}">التجارة الإلكترونية</a></li>
                            <li><a class="dropdown-item" href="{{ route('services.messaging') }}">المراسلة والاتصالات</a></li>
                            <li><a class="dropdown-item" href="{{ route('services.delivery') }}">خدمات التوصيل</a></li>
                            <li><a class="dropdown-item" href="{{ route('services.ai-assistant') }}">المساعد الذكي</a></li>
                            <li><a class="dropdown-item" href="{{ route('services.agents') }}">الوكلاء</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('services.index') }}">جميع الخدمات</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="infoDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            معلومات
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="infoDropdown">
                            <li><a href="{{ route('about') }}" class="dropdown-item">من نحن</a></li>
                            <li><a href="{{ route('features') }}" class="dropdown-item">المميزات</a></li>
                            <li><a href="{{ route('app') }}" class="dropdown-item">التطبيق</a></li>
                            <li><a href="{{ route('contact') }}" class="dropdown-item">اتصل بنا</a></li>
                            <li><a href="{{ route('testimonials') }}" class="dropdown-item">آراء العملاء</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="d-flex">
                    @auth
                        @php
                            $dashboardRoute = Auth::user()->user_type . '.dashboard';
                        @endphp
                        <a href="{{ route($dashboardRoute) }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-tachometer-alt me-1"></i> لوحة التحكم
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-out-alt me-1"></i> تسجيل الخروج
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">تسجيل الدخول</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">إنشاء حساب</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
