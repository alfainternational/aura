<footer class="site-footer bg-dark text-white py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-logo mb-3">
                    <img src="{{ asset('assets/images/aura-logo-white.svg') }}" alt="AURA" height="40">
                </div>
                <p class="mb-4">AURA هو نظام بيئي رقمي متكامل يجمع بين المدفوعات الإلكترونية، منصة التواصل الاجتماعي، التجارة الإلكترونية، والخدمات اللوجستية في منصة واحدة.</p>
                <h5 class="text-white mb-3">تواصل معنا</h5>
                <div class="social-links d-flex gap-2">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                        <i class="icon-facebook"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                        <i class="icon-twitter"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                        <i class="icon-instagram"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                        <i class="icon-linkedin"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                        <i class="icon-youtube"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <h5 class="text-white mb-3">روابط سريعة</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('home') }}" class="text-white-50 d-block py-1">الرئيسية</a></li>
                    <li><a href="{{ route('about') }}" class="text-white-50 d-block py-1">عن AURA</a></li>
                    <li><a href="{{ route('services.index') }}" class="text-white-50 d-block py-1">خدماتنا</a></li>
                    <li><a href="{{ route('features') }}" class="text-white-50 d-block py-1">المميزات</a></li>
                    <li><a href="{{ route('app') }}" class="text-white-50 d-block py-1">التطبيق</a></li>
                    <li><a href="{{ route('testimonials') }}" class="text-white-50 d-block py-1">آراء العملاء</a></li>
                    <li><a href="{{ route('login') }}" class="text-white-50 d-block py-1">تسجيل الدخول</a></li>
                    <li><a href="{{ route('register') }}" class="text-white-50 d-block py-1">إنشاء حساب</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4">
                <h5 class="text-white mb-3">خدماتنا</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="{{ route('services.wallet') }}" class="text-white-50 d-block py-1">المحفظة الإلكترونية</a></li>
                    <li><a href="{{ route('services.commerce') }}" class="text-white-50 d-block py-1">التجارة الإلكترونية</a></li>
                    <li><a href="{{ route('services.messaging') }}" class="text-white-50 d-block py-1">المراسلة والاتصالات</a></li>
                    <li><a href="{{ route('services.delivery') }}" class="text-white-50 d-block py-1">خدمات التوصيل</a></li>
                    <li><a href="{{ route('services.ai-assistant') }}" class="text-white-50 d-block py-1">المساعد الذكي</a></li>
                    <li><a href="{{ route('services.agents') }}" class="text-white-50 d-block py-1">الوكلاء</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4">
                <h5 class="text-white mb-3">النشرة البريدية</h5>
                <p class="text-white-50 mb-3">اشترك في نشرتنا البريدية للحصول على آخر الأخبار والتحديثات.</p>
                <form class="mb-3">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="بريدك الإلكتروني" aria-label="Email" aria-describedby="newsletter-btn">
                        <button class="btn btn-primary" type="button" id="newsletter-btn">اشتراك</button>
                    </div>
                </form>
                <h5 class="text-white mb-3">حمّل التطبيق</h5>
                <div class="app-badges d-flex flex-wrap gap-2">
                    <a href="#" class="app-badge">
                        <img src="{{ asset('assets/images/google-play-badge.png') }}" alt="Google Play" height="40">
                    </a>
                    <a href="#" class="app-badge">
                        <img src="{{ asset('assets/images/app-store-badge.png') }}" alt="App Store" height="40">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom bg-darker py-3 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-white-50 mb-0">&copy; {{ date('Y') }} AURA. جميع الحقوق محفوظة.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <ul class="list-inline mb-0">
                        <li class="list-inline-item">
                            <a href="{{ route('privacy') }}" class="text-white-50">سياسة الخصوصية</a>
                        </li>
                        <li class="list-inline-item mx-3">
                            <a href="{{ route('terms') }}" class="text-white-50">شروط الاستخدام</a>
                        </li>
                        <li class="list-inline-item">
                            <a href="{{ route('cookies') }}" class="text-white-50">سياسة ملفات تعريف الارتباط</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
