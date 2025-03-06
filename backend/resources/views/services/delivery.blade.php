@extends('layouts.app')

@section('title', 'خدمات التوصيل - AURA')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="service-icon bg-warning text-white mx-auto mb-4">
                    <i class="fas fa-truck fa-3x"></i>
                </div>
                <h1 class="fw-bold mb-4">خدمات التوصيل</h1>
                <p class="lead">خدمات توصيل سريعة وموثوقة لتوصيل طلباتك من المطاعم والمتاجر المختلفة. تتبع طلباتك في الوقت الفعلي مع تقديرات دقيقة لوقت الوصول.</p>
            </div>
        </div>
        
        <!-- مميزات خدمات التوصيل -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4">مميزات خدمات التوصيل</h2>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-warning text-white me-3">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">توصيل سريع</h5>
                                        <p>خدمة توصيل سريعة تضمن وصول طلباتك في أقل وقت ممكن مع خيار التوصيل الفوري.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-warning text-white me-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">تتبع مباشر</h5>
                                        <p>تتبع طلبك في الوقت الفعلي على الخريطة منذ لحظة قبول الطلب حتى التسليم.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-warning text-white me-3">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">تنوع المتاجر</h5>
                                        <p>طلب من آلاف المطاعم والمتاجر المحلية المتنوعة، من البقالة إلى الملابس والإلكترونيات.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-warning text-white me-3">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">جدولة التوصيل</h5>
                                        <p>إمكانية جدولة طلبات التوصيل في الوقت الذي يناسبك سواء كان اليوم أو في المستقبل.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-warning text-white me-3">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">عروض حصرية</h5>
                                        <p>استمتع بعروض وخصومات حصرية على خدمات التوصيل بشكل مستمر ومتجدد.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-warning text-white me-3">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">موثوقية وأمان</h5>
                                        <p>مندوبي توصيل تم التحقق منهم بعناية لضمان موثوقية الخدمة وأمان طلباتك.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- أنواع التوصيل -->
        <div class="row mb-5">
            <div class="col-lg-10 offset-lg-1">
                <h2 class="fw-bold text-center mb-5">أنواع خدمات التوصيل</h2>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card bg-light border-0 h-100 text-center">
                            <div class="card-body p-4">
                                <div class="delivery-icon bg-warning text-white mx-auto mb-3">
                                    <i class="fas fa-utensils"></i>
                                </div>
                                <h4 class="fw-bold">توصيل الطعام</h4>
                                <p>استمتع بأطباقك المفضلة من مطاعمك المحلية المفضلة، موصلة إلى باب منزلك في أقصر وقت ممكن.</p>
                                <a href="#" class="btn btn-sm btn-outline-warning mt-3">اطلب الآن</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-light border-0 h-100 text-center">
                            <div class="card-body p-4">
                                <div class="delivery-icon bg-warning text-white mx-auto mb-3">
                                    <i class="fas fa-shopping-basket"></i>
                                </div>
                                <h4 class="fw-bold">توصيل البقالة</h4>
                                <p>احصل على احتياجاتك اليومية من البقالة والخضروات والفواكه الطازجة من أقرب المتاجر إليك.</p>
                                <a href="#" class="btn btn-sm btn-outline-warning mt-3">اطلب الآن</a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card bg-light border-0 h-100 text-center">
                            <div class="card-body p-4">
                                <div class="delivery-icon bg-warning text-white mx-auto mb-3">
                                    <i class="fas fa-gift"></i>
                                </div>
                                <h4 class="fw-bold">توصيل الهدايا</h4>
                                <p>أرسل الهدايا والمفاجآت لأحبائك في المناسبات الخاصة مع خيارات تغليف وبطاقات تهنئة مجانية.</p>
                                <a href="#" class="btn btn-sm btn-outline-warning mt-3">اطلب الآن</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- كيفية الطلب -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4 text-center">كيفية طلب التوصيل</h2>
                        
                        <div class="timeline">
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-warning text-white">1</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">اختر المتجر أو المطعم</h5>
                                        <p>تصفح المتاجر والمطاعم المتاحة في منطقتك واختر ما يناسب احتياجاتك.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-warning text-white">2</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">اختر المنتجات</h5>
                                        <p>أضف المنتجات المطلوبة إلى سلة التسوق وراجع طلبك قبل التأكيد.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-warning text-white">3</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">حدد موقع التوصيل</h5>
                                        <p>أدخل عنوان التوصيل بدقة أو استخدم خدمة تحديد الموقع التلقائية.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-warning text-white">4</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">اختر طريقة الدفع وتتبع طلبك</h5>
                                        <p>أكمل عملية الدفع باستخدام محفظة AURA أو أي طريقة دفع أخرى، ثم تتبع طلبك مباشرة.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- اشتراكات التوصيل -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="bg-light rounded-4 p-5">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="fw-bold mb-4">اشتراك AURA للتوصيل</h2>
                            <p class="mb-4">وفر المزيد مع اشتراك AURA للتوصيل الذي يتيح لك توصيل غير محدود بدون رسوم طوال الشهر. يبدأ الاشتراك من 9.99$ فقط شهرياً.</p>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> توصيل غير محدود</li>
                                <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> أولوية في التوصيل</li>
                                <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> عروض حصرية للمشتركين</li>
                                <li class="mb-2"><i class="fas fa-check text-warning me-2"></i> استرجاع سهل بدون رسوم إضافية</li>
                            </ul>
                            <a href="#" class="btn btn-warning">اشترك الآن</a>
                        </div>
                        <div class="col-md-6 d-flex justify-content-center">
                            <div class="subscription-illustration">
                                <i class="fas fa-truck-loading fa-10x text-warning opacity-25"></i>
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
                    <h2 class="fw-bold mb-4">جاهز لتجربة خدمات توصيل AURA؟</h2>
                    <p class="lead mb-4">حمل تطبيق AURA الآن واحصل على خصم 50% على أول 3 طلبات توصيل!</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-warning btn-lg">حمل التطبيق</a>
                        <a href="#" class="btn btn-outline-warning btn-lg">تصفح المتاجر</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
