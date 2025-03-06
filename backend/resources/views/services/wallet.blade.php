@extends('layouts.app')

@section('title', 'المحفظة الإلكترونية - AURA')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="service-icon bg-primary text-white mx-auto mb-4">
                    <i class="fas fa-wallet fa-3x"></i>
                </div>
                <h1 class="fw-bold mb-4">المحفظة الإلكترونية</h1>
                <p class="lead">محفظة رقمية متكاملة لإدارة أموالك وإجراء المدفوعات بكل سهولة وأمان. حول الأموال، ادفع الفواتير، واستثمر - كل ذلك في مكان واحد.</p>
            </div>
        </div>
        
        <!-- مميزات المحفظة -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4">مميزات المحفظة الإلكترونية</h2>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">تحويل الأموال فوري</h5>
                                        <p>أرسل واستقبل الأموال فوراً مع أي شخص في أي مكان بالعالم بأقل رسوم تحويل.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">دفع الفواتير</h5>
                                        <p>ادفع جميع فواتيرك (كهرباء، ماء، إنترنت، هاتف) من مكان واحد بنقرة زر.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">بطاقة افتراضية</h5>
                                        <p>احصل على بطاقة افتراضية للتسوق عبر الإنترنت بأمان تام وتحكم كامل في سقف الإنفاق.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">تتبع المصاريف</h5>
                                        <p>تحليلات مفصلة لمصاريفك لمساعدتك على إدارة ميزانيتك بشكل فعال.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">رصيد مكافآت</h5>
                                        <p>اكسب نقاط على كل عملية واستبدلها بعروض حصرية من متاجرنا الشريكة.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">أمان متطور</h5>
                                        <p>تأمين حسابك بالمصادقة ثنائية العوامل وإشعارات فورية لكل حركة على حسابك.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- كيفية استخدام المحفظة -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4">كيفية استخدام المحفظة الإلكترونية</h2>
                        
                        <div class="timeline">
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-primary text-white">1</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">إنشاء حساب</h5>
                                        <p>قم بإنشاء حساب في تطبيق AURA وأكمل عملية التحقق من هويتك بسهولة.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-primary text-white">2</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">شحن المحفظة</h5>
                                        <p>اشحن محفظتك عبر التحويل البنكي أو بطاقة الائتمان أو عبر نقاط الشحن المتاحة.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-primary text-white">3</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">استخدام المحفظة</h5>
                                        <p>استخدم رصيدك للتسوق، دفع الفواتير، التحويل للأصدقاء، أو حتى الاستثمار.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-primary text-white">4</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">الاستفادة من المكافآت</h5>
                                        <p>اكسب وإستبدل نقاط المكافآت واستمتع بالعروض الحصرية المقدمة لمستخدمي المحفظة.</p>
                                    </div>
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
                    <h2 class="fw-bold mb-4">جاهز للبدء مع المحفظة الإلكترونية؟</h2>
                    <p class="lead mb-4">احصل على محفظتك الإلكترونية الآن وابدأ في الاستفادة من المزايا الفريدة التي تقدمها AURA.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-primary btn-lg">فتح محفظة جديدة</a>
                        <a href="#" class="btn btn-outline-primary btn-lg">معرفة المزيد</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
