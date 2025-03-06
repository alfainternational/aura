<div class="sidebar">
    <div class="sidebar-inner">
        <!-- لوجو للسايدبار المتنقل -->
        <div class="sidebar-logo d-flex align-items-center justify-content-between p-4 border-bottom">
            <a href="{{ route('home') }}" class="d-flex align-items-center">
                <img src="{{ asset('assets/images/aura-logo.svg') }}" alt="AURA" height="40">
            </a>
            <button type="button" class="btn-close sidebar-close d-lg-none"></button>
        </div>

        <!-- معلومات المستخدم المسجل الدخول -->
        @auth
        <div class="sidebar-user p-4 border-bottom">
            <div class="d-flex align-items-center">
                <div class="sidebar-user-avatar me-3">
                    <img src="{{ asset('assets/images/default-avatar.png') }}" alt="User" class="rounded-circle" width="50" height="50">
                </div>
                <div class="sidebar-user-info">
                    <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                    <p class="small text-muted mb-0">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
        @endauth

        <!-- روابط التنقل الرئيسية -->
        <div class="sidebar-navigation p-4">
            <h6 class="sidebar-heading text-uppercase text-muted mb-3 fs-6">التنقل</h6>
            <ul class="sidebar-menu list-unstyled mb-4">
                <li class="sidebar-menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-home me-3"></i>
                        <span>الرئيسية</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">
                        <div class="nav-link-icon"><i class="fas fa-info-circle"></i></div>
                        من نحن
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('features') }}">
                        <div class="nav-link-icon"><i class="fas fa-star"></i></div>
                        المميزات
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('app') }}">
                        <div class="nav-link-icon"><i class="fas fa-mobile-alt"></i></div>
                        التطبيق
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('testimonials') }}">
                        <div class="nav-link-icon"><i class="fas fa-comment"></i></div>
                        آراء العملاء
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact') }}">
                        <div class="nav-link-icon"><i class="fas fa-envelope"></i></div>
                        تواصل معنا
                    </a>
                </li>
            </ul>

            <!-- روابط قانونية -->
            <h6 class="sidebar-heading text-uppercase text-muted mb-3 fs-6">روابط قانونية</h6>
            <ul class="sidebar-menu list-unstyled mb-4">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('privacy') }}">
                        <div class="nav-link-icon"><i class="fas fa-shield-alt"></i></div>
                        سياسة الخصوصية
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('terms') }}">
                        <div class="nav-link-icon"><i class="fas fa-balance-scale"></i></div>
                        الشروط والأحكام
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cookies') }}">
                        <div class="nav-link-icon"><i class="fas fa-cookie-bite"></i></div>
                        سياسة الكوكيز
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('faq') }}">
                        <div class="nav-link-icon"><i class="fas fa-question-circle"></i></div>
                        الأسئلة الشائعة
                    </a>
                </li>
            </ul>

            <!-- روابط الخدمات -->
            <h6 class="sidebar-heading text-uppercase text-muted mb-3 fs-6">خدماتنا</h6>
            <ul class="sidebar-menu list-unstyled mb-4">
                <li class="sidebar-menu-item {{ request()->routeIs('services.index') ? 'active' : '' }}">
                    <a href="{{ route('services.index') }}" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-th-large me-3"></i>
                        <span>جميع الخدمات</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->routeIs('services.wallet') ? 'active' : '' }}">
                    <a href="{{ route('services.wallet') }}" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-wallet me-3"></i>
                        <span>المحفظة الإلكترونية</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->routeIs('services.commerce') ? 'active' : '' }}">
                    <a href="{{ route('services.commerce') }}" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-shopping-cart me-3"></i>
                        <span>التجارة الإلكترونية</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->routeIs('services.messaging') ? 'active' : '' }}">
                    <a href="{{ route('services.messaging') }}" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-comments me-3"></i>
                        <span>المراسلة والاتصالات</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->routeIs('services.delivery') ? 'active' : '' }}">
                    <a href="{{ route('services.delivery') }}" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-truck me-3"></i>
                        <span>خدمات التوصيل</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->routeIs('services.ai-assistant') ? 'active' : '' }}">
                    <a href="{{ route('services.ai-assistant') }}" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-robot me-3"></i>
                        <span>المساعد الذكي</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ request()->routeIs('services.agents') ? 'active' : '' }}">
                    <a href="{{ route('services.agents') }}" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-user-tie me-3"></i>
                        <span>الوكلاء</span>
                    </a>
                </li>
            </ul>

            <!-- روابط أخرى -->
            <h6 class="sidebar-heading text-uppercase text-muted mb-3 fs-6">روابط أخرى</h6>
            <ul class="sidebar-menu list-unstyled mb-4">
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-headset me-3"></i>
                        <span>الدعم الفني</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-file-alt me-3"></i>
                        <span>المدونة</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link d-flex align-items-center py-2">
                        <i class="icon-phone-alt me-3"></i>
                        <span>اتصل بنا</span>
                    </a>
                </li>
            </ul>

            <!-- أزرار تسجيل الدخول/الخروج -->
            @guest
            <div class="sidebar-cta p-4 bg-light rounded-3 mb-4">
                <h6 class="mb-3">انضم إلى AURA اليوم</h6>
                <p class="small text-muted mb-3">استمتع بتجربة رقمية متكاملة مع جميع الخدمات التي تحتاجها في مكان واحد.</p>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-primary">إنشاء حساب</a>
                    <a href="#" class="btn btn-outline-primary">تسجيل الدخول</a>
                </div>
            </div>
            @else
            <div class="sidebar-cta p-4 bg-light rounded-3 mb-4">
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-danger">تسجيل الخروج</a>
                </div>
            </div>
            @endguest
        </div>
    </div>
</div>
