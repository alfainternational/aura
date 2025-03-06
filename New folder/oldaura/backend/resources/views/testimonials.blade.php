@extends('layouts.app')

@section('title', 'آراء العملاء - AURA')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <h1 class="fw-bold mb-4">آراء العملاء</h1>
                <p class="lead">استمع إلى تجارب مستخدمينا الحقيقية وكيف غير نظام AURA حياتهم اليومية.</p>
            </div>
        </div>
        
        <!-- قسم التقييمات -->
        <div class="row mb-5">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/default-avatar.png') }}" alt="صورة المستخدم" class="rounded-circle me-3" width="60">
                                <div>
                                    <h5 class="mb-0">سارة الأحمد</h5>
                                    <p class="text-muted mb-0">طالبة جامعية</p>
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">"أحب كيف يمكنني إدارة كل شيء من خلال تطبيق واحد! أستخدم AURA للتسوق ودفع فواتيري وإرسال المال لأصدقائي. كما أستخدم ميزة تتبع المصروفات التي ساعدتني على توفير الكثير من المال. تطبيق رائع حقًا!"</p>
                        <div class="testimonial-date text-muted">
                            <small>مستخدم منذ عام 2022</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/default-avatar.png') }}" alt="صورة المستخدم" class="rounded-circle me-3" width="60">
                                <div>
                                    <h5 class="mb-0">محمد العلي</h5>
                                    <p class="text-muted mb-0">رجل أعمال</p>
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">"كصاحب عمل صغير، وفر لي AURA الكثير من الوقت والجهد. استخدم منصة التجارة الإلكترونية لعرض منتجاتي، وأستخدم نظام المدفوعات لإدارة المعاملات المالية. كما أن خدمة التوصيل سهلت علي إيصال منتجاتي للعملاء. تحسنت أعمالي بشكل كبير!"</p>
                        <div class="testimonial-date text-muted">
                            <small>مستخدم منذ عام 2021</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/default-avatar.png') }}" alt="صورة المستخدم" class="rounded-circle me-3" width="60">
                                <div>
                                    <h5 class="mb-0">نورة الخالد</h5>
                                    <p class="text-muted mb-0">مصممة جرافيك</p>
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">"أعشق واجهة المستخدم في تطبيق AURA! سهلة الاستخدام وجميلة التصميم. أستخدم المحفظة الإلكترونية بشكل يومي، وميزة التواصل الاجتماعي رائعة للتواصل مع عملائي. المساعد الذكي يساعدني في تنظيم مواعيدي وتذكيري بالمهام المهمة."</p>
                        <div class="testimonial-date text-muted">
                            <small>مستخدمة منذ عام 2023</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/default-avatar.png') }}" alt="صورة المستخدم" class="rounded-circle me-3" width="60">
                                <div>
                                    <h5 class="mb-0">أحمد المحمد</h5>
                                    <p class="text-muted mb-0">معلم</p>
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">"تطبيق AURA سهل الاستخدام للغاية وقد ساعدني في إدارة أموري المالية بشكل أفضل. أستخدم المحفظة الإلكترونية لدفع الفواتير وتحويل الأموال لعائلتي. أتمنى أن تتحسن خدمة التوصيل قليلاً في منطقتي، ولكن بشكل عام تجربة رائعة!"</p>
                        <div class="testimonial-date text-muted">
                            <small>مستخدم منذ عام 2022</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/default-avatar.png') }}" alt="صورة المستخدم" class="rounded-circle me-3" width="60">
                                <div>
                                    <h5 class="mb-0">منيرة السالم</h5>
                                    <p class="text-muted mb-0">ربة منزل</p>
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">"غير تطبيق AURA حياتي! أستخدمه للتسوق وطلب البقالة وأيضًا للتواصل مع عائلتي وأصدقائي. برنامج المكافآت رائع، وقد جمعت الكثير من النقاط واستبدلتها بقسائم شراء. أوصي به بشدة لكل عائلة!"</p>
                        <div class="testimonial-date text-muted">
                            <small>مستخدمة منذ عام 2023</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/default-avatar.png') }}" alt="صورة المستخدم" class="rounded-circle me-3" width="60">
                                <div>
                                    <h5 class="mb-0">فهد العبدالله</h5>
                                    <p class="text-muted mb-0">طالب</p>
                                </div>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text">"كطالب، ساعدني تطبيق AURA على إدارة مصروفي الشهري بشكل أفضل. أستخدم المحفظة الإلكترونية للادخار وتتبع مصاريفي، وميزة الخصومات والعروض وفرت لي الكثير من المال. كما أن خدمة التوصيل سريعة جدًا ومريحة!"</p>
                        <div class="testimonial-date text-muted">
                            <small>مستخدم منذ عام 2022</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم الإحصائيات -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">الأرقام تتحدث عنا</h2>
                <p class="lead">إحصائيات تعكس ثقة المستخدمين في منصة AURA</p>
            </div>
            
            <div class="col-lg-10 offset-lg-1">
                <div class="row g-4 text-center">
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="stat-icon text-primary mb-3">
                                    <i class="fas fa-users fa-3x"></i>
                                </div>
                                <h3 class="display-5 fw-bold">2M+</h3>
                                <p class="text-muted">مستخدم نشط</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="stat-icon text-success mb-3">
                                    <i class="fas fa-shopping-cart fa-3x"></i>
                                </div>
                                <h3 class="display-5 fw-bold">5K+</h3>
                                <p class="text-muted">تاجر شريك</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="stat-icon text-info mb-3">
                                    <i class="fas fa-exchange-alt fa-3x"></i>
                                </div>
                                <h3 class="display-5 fw-bold">50K+</h3>
                                <p class="text-muted">معاملة يومية</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="stat-icon text-warning mb-3">
                                    <i class="fas fa-globe fa-3x"></i>
                                </div>
                                <h3 class="display-5 fw-bold">15+</h3>
                                <p class="text-muted">دولة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم الشهادات الفيديو -->
        <div class="row my-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">شهادات فيديو</h2>
                <p class="lead">استمع إلى قصص نجاح حقيقية من مستخدمي AURA</p>
            </div>
            
            <div class="col-lg-10 offset-lg-1">
                <div class="row g-4">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="video-testimonial position-relative">
                                <img src="{{ asset('assets/images/video-thumbnail.jpg') }}" alt="فيديو شهادة" class="img-fluid w-100 rounded-top">
                                <div class="play-button position-absolute top-50 start-50 translate-middle">
                                    <button class="btn btn-primary rounded-circle p-3">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <h4 class="fw-bold">قصة نجاح: من متجر صغير إلى شركة ناجحة</h4>
                                <p class="text-muted">محمد العلي - صاحب متجر إلكتروني</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="video-testimonial position-relative">
                                <img src="{{ asset('assets/images/video-thumbnail.jpg') }}" alt="فيديو شهادة" class="img-fluid w-100 rounded-top">
                                <div class="play-button position-absolute top-50 start-50 translate-middle">
                                    <button class="btn btn-primary rounded-circle p-3">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <h4 class="fw-bold">كيف ساعدتني AURA في توفير الوقت والمال</h4>
                                <p class="text-muted">سارة الأحمد - طالبة جامعية</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم دعوة للمشاركة -->
        <div class="row mt-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card bg-primary text-white shadow-lg rounded-4">
                    <div class="card-body p-5 text-center">
                        <h2 class="fw-bold mb-4">شارك تجربتك مع AURA</h2>
                        <p class="lead mb-4">هل أنت مستخدم لـ AURA؟ نود سماع قصتك وكيف ساعدك التطبيق في حياتك اليومية.</p>
                        <a href="#" class="btn btn-light btn-lg px-5">شارك تجربتك</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
