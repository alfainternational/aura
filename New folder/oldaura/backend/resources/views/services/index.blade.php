@extends('layouts.app')

@section('title', 'خدمات AURA')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <h1 class="fw-bold mb-4">خدمات نظام AURA</h1>
                <p class="lead">استكشف باقة متكاملة من الخدمات الرقمية المصممة لتلبية احتياجاتك اليومية في منصة واحدة.</p>
            </div>
        </div>
        
        <!-- قسم الخدمات الرئيسية -->
        <div class="row mb-5">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-0 hover-lift">
                    <div class="card-body p-4">
                        <div class="service-icon bg-primary text-white mb-4">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-3">المحفظة الإلكترونية</h3>
                        <p>محفظة رقمية متكاملة لإدارة أموالك وإجراء المدفوعات بكل سهولة وأمان. حول الأموال، ادفع الفواتير، واستثمر - كل ذلك في مكان واحد.</p>
                        <a href="{{ route('services.wallet') }}" class="btn btn-outline-primary mt-3">استكشف الخدمة <i class="fas fa-arrow-left me-2"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-0 hover-lift">
                    <div class="card-body p-4">
                        <div class="service-icon bg-success text-white mb-4">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-3">التجارة الإلكترونية</h3>
                        <p>منصة تسوق متكاملة تتيح لك شراء وبيع المنتجات بكل سهولة. استفد من العروض الحصرية وأنشئ متجرك الخاص مع حلول دفع آمنة.</p>
                        <a href="{{ route('services.commerce') }}" class="btn btn-outline-success mt-3">استكشف الخدمة <i class="fas fa-arrow-left me-2"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-0 hover-lift">
                    <div class="card-body p-4">
                        <div class="service-icon bg-info text-white mb-4">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-3">المراسلة والاتصالات</h3>
                        <p>منصة تواصل اجتماعي متكاملة مع ميزات محادثة آمنة ومكالمات صوتية ومرئية عالية الجودة. ابق على تواصل مع أصدقائك وعائلتك.</p>
                        <a href="{{ route('services.messaging') }}" class="btn btn-outline-info mt-3">استكشف الخدمة <i class="fas fa-arrow-left me-2"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-0 hover-lift">
                    <div class="card-body p-4">
                        <div class="service-icon bg-warning text-white mb-4">
                            <i class="fas fa-truck fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-3">خدمات التوصيل</h3>
                        <p>خدمات توصيل سريعة وموثوقة لتوصيل طلباتك من المطاعم والمتاجر المختلفة. تتبع طلباتك في الوقت الفعلي مع تقديرات دقيقة لوقت الوصول.</p>
                        <a href="{{ route('services.delivery') }}" class="btn btn-outline-warning mt-3">استكشف الخدمة <i class="fas fa-arrow-left me-2"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-0 hover-lift">
                    <div class="card-body p-4">
                        <div class="service-icon bg-danger text-white mb-4">
                            <i class="fas fa-robot fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-3">المساعد الذكي</h3>
                        <p>مساعد شخصي مدعوم بالذكاء الاصطناعي لمساعدتك في إدارة مهامك اليومية وتقديم توصيات شخصية تناسب احتياجاتك واهتماماتك.</p>
                        <a href="{{ route('services.ai-assistant') }}" class="btn btn-outline-danger mt-3">استكشف الخدمة <i class="fas fa-arrow-left me-2"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-0 hover-lift">
                    <div class="card-body p-4">
                        <div class="service-icon bg-secondary text-white mb-4">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-3">نظام الوكلاء</h3>
                        <p>كن وكيلاً معتمداً لخدمات AURA وابدأ بتحقيق دخل إضافي من خلال تقديم خدماتنا للآخرين. استفد من نظام عمولات مجزية وتدريب مستمر.</p>
                        <a href="{{ route('services.agents') }}" class="btn btn-outline-secondary mt-3">استكشف الخدمة <i class="fas fa-arrow-left me-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم المميزات المشتركة -->
        <div class="row my-5">
            <div class="col-lg-8 offset-lg-2 text-center mb-5">
                <h2 class="fw-bold">ما يميز خدمات AURA</h2>
                <p class="lead">تتميز جميع خدماتنا بمجموعة من المزايا المشتركة التي تجعل تجربتك مع AURA فريدة من نوعها</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon-circle bg-primary text-white mx-auto mb-3">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4 class="fw-bold mb-3">أمان متطور</h4>
                        <p>جميع خدماتنا مؤمنة بأحدث تقنيات التشفير والمصادقة متعددة العوامل لحماية بياناتك ومعاملاتك.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon-circle bg-success text-white mx-auto mb-3">
                            <i class="fas fa-sync"></i>
                        </div>
                        <h4 class="fw-bold mb-3">تكامل سلس</h4>
                        <p>جميع خدماتنا متكاملة بشكل سلس مع بعضها البعض، مما يتيح لك تجربة استخدام موحدة وسهلة.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card bg-light border-0 h-100">
                    <div class="card-body p-4 text-center">
                        <div class="feature-icon-circle bg-info text-white mx-auto mb-3">
                            <i class="fas fa-star"></i>
                        </div>
                        <h4 class="fw-bold mb-3">مكافآت موحدة</h4>
                        <p>اكسب نقاط المكافآت من جميع خدماتنا واستبدلها في أي من خدمات AURA المختلفة.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم الأسعار -->
        <div class="row my-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">باقات الاشتراك</h2>
                <p class="lead">اختر الباقة المناسبة لاحتياجاتك واستمتع بخدمات AURA المتكاملة</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="text-primary fw-bold">الباقة المجانية</h3>
                            <h1 class="display-4 fw-bold">$0</h1>
                            <p class="text-muted">شهرياً</p>
                        </div>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> المحفظة الإلكترونية الأساسية</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> التسوق الإلكتروني</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> المراسلة الأساسية</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-times text-danger me-2"></i> خدمات التوصيل</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-times text-danger me-2"></i> المساعد الذكي</li>
                        </ul>
                        <div class="d-grid">
                            <a href="#" class="btn btn-outline-primary">البدء مجاناً</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-primary border-2 shadow h-100">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <span class="badge bg-warning">الأكثر شعبية</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="text-primary fw-bold">الباقة الأساسية</h3>
                            <h1 class="display-4 fw-bold">$9.99</h1>
                            <p class="text-muted">شهرياً</p>
                        </div>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> المحفظة الإلكترونية المتقدمة</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> التسوق مع خصومات حصرية</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> المراسلة والمكالمات المرئية</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> خدمات التوصيل مع خصم 10%</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-times text-danger me-2"></i> المساعد الذكي المتقدم</li>
                        </ul>
                        <div class="d-grid">
                            <a href="#" class="btn btn-primary">اشترك الآن</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h3 class="text-primary fw-bold">الباقة المتكاملة</h3>
                            <h1 class="display-4 fw-bold">$19.99</h1>
                            <p class="text-muted">شهرياً</p>
                        </div>
                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> جميع ميزات المحفظة الإلكترونية</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> التسوق مع خصومات VIP</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> المراسلة غير المحدودة</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> خدمات التوصيل المجانية</li>
                            <li class="list-group-item border-0 ps-0"><i class="fas fa-check text-success me-2"></i> المساعد الذكي المتقدم</li>
                        </ul>
                        <div class="d-grid">
                            <a href="#" class="btn btn-outline-primary">اشترك الآن</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم دعوة للعمل -->
        <div class="row mt-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="bg-light rounded-4 p-5 text-center">
                    <h2 class="fw-bold mb-4">جاهز للانضمام إلى نظام AURA الرقمي؟</h2>
                    <p class="lead mb-4">ابدأ رحلتك مع AURA اليوم واستمتع بتجربة رقمية متكاملة تجمع كل ما تحتاجه في مكان واحد.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-primary btn-lg">إنشاء حساب مجاني</a>
                        <a href="#" class="btn btn-outline-primary btn-lg">تحميل التطبيق</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
