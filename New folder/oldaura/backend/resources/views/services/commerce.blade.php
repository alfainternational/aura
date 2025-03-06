@extends('layouts.app')

@section('title', 'التجارة الإلكترونية - AURA')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="service-icon bg-success text-white mx-auto mb-4">
                    <i class="fas fa-shopping-cart fa-3x"></i>
                </div>
                <h1 class="fw-bold mb-4">التجارة الإلكترونية</h1>
                <p class="lead">منصة تسوق متكاملة تتيح لك شراء وبيع المنتجات بكل سهولة. استفد من العروض الحصرية وأنشئ متجرك الخاص مع حلول دفع آمنة.</p>
            </div>
        </div>
        
        <!-- مميزات التجارة الإلكترونية -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4">مميزات منصة التجارة الإلكترونية</h2>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">إنشاء متجرك الخاص</h5>
                                        <p>أنشئ متجرك الإلكتروني الخاص بك في دقائق وابدأ ببيع منتجاتك للعملاء في جميع أنحاء العالم.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-tags"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">عروض وخصومات حصرية</h5>
                                        <p>استفد من العروض والخصومات الحصرية المقدمة من المتاجر الشريكة على المنصة.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-money-check-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">حلول دفع متكاملة</h5>
                                        <p>تكامل سلس مع المحفظة الإلكترونية وخيارات دفع متعددة لتسهيل عملية الشراء.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-truck"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">توصيل سريع</h5>
                                        <p>خدمات توصيل سريعة ومضمونة لجميع طلباتك مع إمكانية تتبع الشحنة في الوقت الفعلي.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">تقييمات ومراجعات</h5>
                                        <p>اطلع على تقييمات ومراجعات المنتجات من المستخدمين الآخرين لاتخاذ قرار شراء مستنير.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-chart-bar"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">تحليلات المبيعات</h5>
                                        <p>للتجار: تحليلات مفصلة للمبيعات والعملاء لمساعدتك على تنمية نشاطك التجاري.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- أقسام التسوق -->
        <div class="row mb-5">
            <div class="col-lg-10 offset-lg-1">
                <h2 class="fw-bold text-center mb-5">اكتشف أقسام التسوق</h2>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card bg-light border-0 h-100 text-center">
                            <div class="card-body p-4">
                                <div class="category-icon bg-success text-white mx-auto mb-3">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <h4 class="fw-bold">الأزياء والملابس</h4>
                                <p>أحدث صيحات الموضة من الملابس والأحذية والإكسسوارات للرجال والنساء والأطفال.</p>
                                <a href="#" class="btn btn-sm btn-outline-success mt-3">تسوق الآن</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-light border-0 h-100 text-center">
                            <div class="card-body p-4">
                                <div class="category-icon bg-success text-white mx-auto mb-3">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <h4 class="fw-bold">الإلكترونيات</h4>
                                <p>أحدث الأجهزة الإلكترونية والهواتف الذكية والحواسيب وملحقاتها بأفضل الأسعار.</p>
                                <a href="#" class="btn btn-sm btn-outline-success mt-3">تسوق الآن</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-light border-0 h-100 text-center">
                            <div class="card-body p-4">
                                <div class="category-icon bg-success text-white mx-auto mb-3">
                                    <i class="fas fa-home"></i>
                                </div>
                                <h4 class="fw-bold">المنزل والأثاث</h4>
                                <p>كل ما يلزم منزلك من أثاث وديكورات وأدوات منزلية بتصاميم عصرية وجودة عالية.</p>
                                <a href="#" class="btn btn-sm btn-outline-success mt-3">تسوق الآن</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- بدء البيع -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="fw-bold mb-4">ابدأ البيع على منصة AURA</h2>
                                <p class="mb-4">انضم إلى آلاف التجار الناجحين على منصتنا وابدأ في بيع منتجاتك لملايين المستخدمين. نوفر لك كل الأدوات التي تحتاجها لبدء وتنمية نشاطك التجاري.</p>
                                <ul class="list-unstyled mb-4">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> إعداد متجرك في دقائق</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> عمولات تنافسية</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> دعم فني على مدار الساعة</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> أدوات تسويقية متقدمة</li>
                                </ul>
                                <a href="#" class="btn btn-success">ابدأ البيع الآن</a>
                            </div>
                            <div class="col-md-6 d-flex justify-content-center">
                                <div class="seller-illustration">
                                    <i class="fas fa-store fa-10x text-success opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- دعوة للعمل -->
        <div class="row mt-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="bg-light rounded-4 p-5 text-center">
                    <h2 class="fw-bold mb-4">جاهز للتسوق مع AURA؟</h2>
                    <p class="lead mb-4">ابدأ تجربة تسوق فريدة مع آلاف المنتجات والعروض الحصرية على منصة AURA للتجارة الإلكترونية.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-success btn-lg">تسوق الآن</a>
                        <a href="#" class="btn btn-outline-success btn-lg">استكشف العروض</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
