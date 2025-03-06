@extends('layouts.app')

@section('title', 'شبكة الوكلاء - AURA')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="service-icon bg-success text-white mx-auto mb-4">
                    <i class="fas fa-user-tie fa-3x"></i>
                </div>
                <h1 class="fw-bold mb-4">شبكة وكلاء AURA</h1>
                <p class="lead">شبكة واسعة من الوكلاء المعتمدين في جميع أنحاء المنطقة لمساعدتك في إجراء معاملاتك المالية وتقديم خدمات AURA المختلفة بكل سهولة وأمان.</p>
            </div>
        </div>
        
        <!-- مميزات شبكة الوكلاء -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4">مميزات شبكة الوكلاء</h2>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">انتشار واسع</h5>
                                        <p>شبكة من أكثر من 5000 وكيل معتمد منتشرين في جميع أنحاء المنطقة لتكون قريباً دائماً.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-handshake"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">معتمدون وموثوقون</h5>
                                        <p>جميع وكلائنا معتمدون ومدربون لتقديم خدمات ذات جودة عالية وبأمان تام.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">المعاملات المالية</h5>
                                        <p>إيداع وسحب النقود من محفظتك الإلكترونية وإرسال واستلام الحوالات المالية بسهولة.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">تسجيل المستخدمين</h5>
                                        <p>يمكن للوكلاء مساعدتك في فتح حسابات جديدة والتحقق من الهوية وتفعيل الخدمات.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-headset"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">الدعم الفني</h5>
                                        <p>يقدم الوكلاء المساعدة الفنية وحل المشكلات البسيطة التي قد تواجهك أثناء استخدام الخدمات.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-success text-white me-3">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">خدمات متنوعة</h5>
                                        <p>يقدم الوكلاء مجموعة متنوعة من الخدمات تشمل دفع الفواتير، شحن الرصيد، التحويلات وغيرها.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- أنواع الوكلاء -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">أنواع وكلاء AURA</h2>
                <p class="lead">تضم شبكتنا عدة فئات من الوكلاء المعتمدين لتلبية مختلف احتياجاتك</p>
            </div>
            
            <div class="col-lg-10 offset-lg-1">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="agent-icon bg-success text-white mx-auto mb-4 rounded-circle">
                                    <i class="fas fa-store fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">وكلاء التجزئة</h4>
                                <p>متاجر ومحلات معتمدة في الأحياء والمجمعات التجارية تقدم خدمات AURA الأساسية.</p>
                                <span class="badge bg-success mb-3">+3500 وكيل</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="agent-icon bg-success text-white mx-auto mb-4 rounded-circle">
                                    <i class="fas fa-building fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">وكلاء رئيسيون</h4>
                                <p>مراكز خدمة متكاملة توفر جميع خدمات AURA مع فريق متخصص لمساعدتك.</p>
                                <span class="badge bg-success mb-3">+500 وكيل</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="agent-icon bg-success text-white mx-auto mb-4 rounded-circle">
                                    <i class="fas fa-user-tie fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">وكلاء متجولون</h4>
                                <p>وكلاء يصلون إليك أينما كنت لتوفير خدمات AURA في المناطق النائية وحسب الطلب.</p>
                                <span class="badge bg-success mb-3">+1000 وكيل</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- عملية البحث عن وكيل -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4 text-center">كيفية العثور على وكيل</h2>
                        
                        <div class="timeline">
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-success text-white">1</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">فتح تطبيق AURA</h5>
                                        <p>افتح تطبيق AURA على هاتفك الذكي وانتقل إلى خيار "الوكلاء" في القائمة الرئيسية.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-success text-white">2</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">تحديد موقعك</h5>
                                        <p>اسمح للتطبيق بتحديد موقعك الحالي أو أدخل موقعاً محدداً للبحث فيه عن وكلاء.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-success text-white">3</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">اختيار نوع الخدمة</h5>
                                        <p>حدد نوع الخدمة التي تبحث عنها، مثل الإيداع، السحب، تسجيل حساب جديد، إلخ.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-success text-white">4</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">اختيار الوكيل المناسب</h5>
                                        <p>تصفح قائمة الوكلاء القريبين منك، شاهد التقييمات والمراجعات، واختر الوكيل المناسب.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- خريطة الوكلاء -->
        <div class="row mb-5">
            <div class="col-lg-10 offset-lg-1 text-center">
                <h2 class="fw-bold mb-4">ابحث عن أقرب وكيل</h2>
                <p class="lead mb-5">استخدم الخريطة التفاعلية للعثور على أقرب وكلاء AURA في منطقتك</p>
                
                <div class="agent-map bg-light rounded-3 p-5 mb-4">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="text-center">
                            <i class="fas fa-map-marked-alt fa-6x text-success opacity-25 mb-4"></i>
                            <h5 class="fw-bold mb-3">خريطة الوكلاء</h5>
                            <p>الخريطة التفاعلية تعرض وكلاء AURA حول موقعك الحالي.</p>
                            <a href="#" class="btn btn-success mt-3">فتح الخريطة التفاعلية</a>
                        </div>
                    </div>
                </div>
                
                <div class="search-form">
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="أدخل الموقع للبحث عن وكلاء...">
                                <button class="btn btn-success" type="button">بحث</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- كن وكيلاً -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="bg-light rounded-4 p-5">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h2 class="fw-bold mb-4">انضم لشبكة وكلاء AURA</h2>
                            <p class="mb-4">هل ترغب في أن تكون جزءًا من شبكة وكلاء AURA المتنامية؟ سواء كنت تمتلك متجرًا أو ترغب في العمل كوكيل متجول، نحن نرحب بك لتقديم خدماتنا للمزيد من العملاء.</p>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> عمولات مجزية على كل معاملة</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> تدريب مجاني على الخدمات والأنظمة</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> دعم فني متواصل على مدار الساعة</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> مواد تسويقية وترويجية مجانية</li>
                            </ul>
                            <a href="#" class="btn btn-success">قدم طلبك الآن</a>
                        </div>
                        <div class="col-md-5 d-flex justify-content-center">
                            <div class="agent-illustration">
                                <i class="fas fa-handshake fa-10x text-success opacity-25"></i>
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
                    <h2 class="fw-bold mb-4">جاهز للوصول إلى شبكة وكلاء AURA؟</h2>
                    <p class="lead mb-4">حمل تطبيق AURA الآن واستفد من شبكة واسعة من الوكلاء لإجراء معاملاتك بسهولة!</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-success btn-lg">حمل التطبيق</a>
                        <a href="#" class="btn btn-outline-success btn-lg">ابحث عن وكيل</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
