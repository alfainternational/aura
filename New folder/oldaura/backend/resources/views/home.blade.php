@extends('layouts.app')

@section('title', 'AURA - النظام البيئي الرقمي المتكامل')

@section('content')
    <!-- قسم الهيرو -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content animate-on-scroll">
                        <h1>نظام AURA البيئي المتكامل</h1>
                        <p>منصة واحدة شاملة تجمع بين المدفوعات الإلكترونية، التواصل الاجتماعي، التجارة الإلكترونية والخدمات اللوجستية</p>
                        <div class="hero-buttons">
                            <a href="#features" class="btn btn-primary">استكشف المميزات</a>
                            <a href="#download" class="btn btn-outline">تحميل التطبيق</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image animate-on-scroll">
                        <img src="{{ asset('assets/images/hero-img.svg') }}" alt="AURA App" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم المميزات الرئيسية -->
    <section class="section bg-light" id="features">
        <div class="container">
            <div class="section-title">
                <h2>مميزات النظام</h2>
                <p>AURA يقدم مجموعة متكاملة من الخدمات الرقمية التي تلبي احتياجاتك اليومية في عالم رقمي متطور</p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <i class="icon-wallet"></i>
                        </div>
                        <h3>المحفظة الإلكترونية</h3>
                        <p>أرسل واستقبل الأموال، وادفع فواتيرك، واشحن رصيدك بكل سهولة وأمان من خلال محفظة AURA الإلكترونية.</p>
                        <a href="{{ route('services.wallet') }}" class="btn btn-link">اقرأ المزيد <i class="icon-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <i class="icon-comments"></i>
                        </div>
                        <h3>المراسلة والاتصالات</h3>
                        <p>تواصل مع أصدقائك وعائلتك عبر الرسائل النصية والصوتية والمكالمات المرئية باستخدام تقنية مشفرة.</p>
                        <a href="{{ route('services.messaging') }}" class="btn btn-link">اقرأ المزيد <i class="icon-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <i class="icon-shopping-cart"></i>
                        </div>
                        <h3>التجارة الإلكترونية</h3>
                        <p>تسوق عبر الإنترنت من مجموعة واسعة من المنتجات والخدمات، وبيع منتجاتك الخاصة بكل سهولة.</p>
                        <a href="{{ route('services.commerce') }}" class="btn btn-link">اقرأ المزيد <i class="icon-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <i class="icon-truck"></i>
                        </div>
                        <h3>خدمات التوصيل</h3>
                        <p>خدمات توصيل سريعة وموثوقة للطعام والبقالة والطرود، مع تتبع مباشر لحالة طلبك في الوقت الفعلي.</p>
                        <a href="{{ route('services.delivery') }}" class="btn btn-link">اقرأ المزيد <i class="icon-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <i class="icon-robot"></i>
                        </div>
                        <h3>المساعد الذكي</h3>
                        <p>مساعد شخصي مدعوم بالذكاء الاصطناعي يساعدك في إدارة مهامك اليومية وتقديم توصيات مخصصة.</p>
                        <a href="{{ route('services.ai-assistant') }}" class="btn btn-link">اقرأ المزيد <i class="icon-arrow-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="feature-card animate-on-scroll">
                        <div class="feature-icon">
                            <i class="icon-user-tie"></i>
                        </div>
                        <h3>شبكة الوكلاء</h3>
                        <p>شبكة واسعة من الوكلاء المعتمدين لتسهيل المعاملات المالية والخدمات في جميع أنحاء المنطقة.</p>
                        <a href="{{ route('services.agents') }}" class="btn btn-link">اقرأ المزيد <i class="icon-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم كيف يعمل النظام -->
    <section class="section" id="how-it-works">
        <div class="container">
            <div class="section-title">
                <h2>كيف يعمل النظام؟</h2>
                <p>استمتع بتجربة رقمية سلسة من خلال هذه الخطوات البسيطة</p>
            </div>

            <div class="how-it-works">
                <div class="step animate-on-scroll">
                    <div class="step-number">1</div>
                    <h3>إنشاء حساب</h3>
                    <p>قم بإنشاء حساب جديد عبر تطبيق AURA أو الموقع الإلكتروني في دقائق معدودة.</p>
                </div>

                <div class="step animate-on-scroll">
                    <div class="step-number">2</div>
                    <h3>تفعيل المحفظة</h3>
                    <p>فعّل محفظتك الإلكترونية وأضف رصيدًا من خلال العديد من طرق الدفع المتاحة.</p>
                </div>

                <div class="step animate-on-scroll">
                    <div class="step-number">3</div>
                    <h3>استكشف الخدمات</h3>
                    <p>تصفح مجموعة متنوعة من الخدمات المتاحة واختر ما يناسب احتياجاتك.</p>
                </div>

                <div class="step animate-on-scroll">
                    <div class="step-number">4</div>
                    <h3>استمتع بالتجربة</h3>
                    <p>استمتع بتجربة رقمية متكاملة وآمنة تجمع كل ما تحتاجه في مكان واحد.</p>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('features') }}" class="btn btn-primary">اكتشف المزيد من المميزات</a>
            </div>
        </div>
    </section>

    <!-- قسم الإحصائيات -->
    <section class="section bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-box p-4 rounded bg-white shadow-sm animate-on-scroll">
                        <h2 class="display-4 fw-bold text-primary">+2M</h2>
                        <p class="mb-0 text-secondary">مستخدم نشط</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-box p-4 rounded bg-white shadow-sm animate-on-scroll">
                        <h2 class="display-4 fw-bold text-primary">+50K</h2>
                        <p class="mb-0 text-secondary">معاملة يومية</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-box p-4 rounded bg-white shadow-sm animate-on-scroll">
                        <h2 class="display-4 fw-bold text-primary">+5K</h2>
                        <p class="mb-0 text-secondary">تاجر معتمد</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="stat-box p-4 rounded bg-white shadow-sm animate-on-scroll">
                        <h2 class="display-4 fw-bold text-primary">+15</h2>
                        <p class="mb-0 text-secondary">دولة مدعومة</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- قسم التحميل والتنزيل -->
    <section class="section" id="download">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="download-content animate-on-scroll">
                        <h2>حمّل تطبيق AURA الآن</h2>
                        <p>تطبيق AURA متاح الآن على متاجر التطبيقات الرئيسية. قم بتحميل التطبيق واستمتع بتجربة رقمية فريدة تمكنك من إدارة حياتك اليومية بسهولة وأمان.</p>
                        <div class="download-badges">
                            <a href="#" class="btn btn-dark btn-lg mb-2 mb-md-0 me-md-2">
                                <i class="fab fa-apple"></i> App Store
                            </a>
                            <a href="#" class="btn btn-dark btn-lg">
                                <i class="fab fa-google-play"></i> Google Play
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="download-image animate-on-scroll">
                        <img src="{{ asset('assets/images/app-screens.png') }}" alt="AURA App Screens" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    // أي سكريبت خاص بصفحة الرئيسية
    $(document).ready(function() {
        // تفعيل التأثيرات البصرية عند التمرير
        const animateElements = document.querySelectorAll('.animate-on-scroll');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, { threshold: 0.1 });
        
        animateElements.forEach(element => {
            observer.observe(element);
        });
    });
</script>
@endsection
