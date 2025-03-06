@extends('layouts.app')

@section('title', 'المراسلة والاتصالات - AURA')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="service-icon bg-info text-white mx-auto mb-4">
                    <i class="fas fa-comments fa-3x"></i>
                </div>
                <h1 class="fw-bold mb-4">المراسلة والاتصالات</h1>
                <p class="lead">منصة تواصل اجتماعي متكاملة مع ميزات محادثة آمنة ومكالمات صوتية ومرئية عالية الجودة. ابق على تواصل مع أصدقائك وعائلتك.</p>
            </div>
        </div>
        
        <!-- مميزات المراسلة والاتصالات -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4">مميزات منصة المراسلة والاتصالات</h2>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-info text-white me-3">
                                        <i class="fas fa-comment-dots"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">مراسلة فورية</h5>
                                        <p>محادثات نصية فورية مع الأصدقاء والعائلة والزملاء مع دعم الوسائط المتعددة.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-info text-white me-3">
                                        <i class="fas fa-video"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">مكالمات فيديو HD</h5>
                                        <p>مكالمات فيديو عالية الجودة مع ما يصل إلى 50 شخصًا في نفس الوقت.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-info text-white me-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">مجموعات ومجتمعات</h5>
                                        <p>إنشاء مجموعات للعائلة والأصدقاء أو مجتمعات حول اهتماماتك المشتركة.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-info text-white me-3">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">تشفير من طرف لطرف</h5>
                                        <p>جميع المحادثات والمكالمات مشفرة من طرف لطرف لضمان الخصوصية والأمان.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-info text-white me-3">
                                        <i class="fas fa-share-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">مشاركة الملفات</h5>
                                        <p>مشاركة الملفات والصور والفيديو بسهولة مع تخزين سحابي مجاني يصل إلى 10GB.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-info text-white me-3">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">شبكة اجتماعية</h5>
                                        <p>اكتشف أصدقاء جدد وشارك المنشورات والتحديثات على صفحتك الشخصية.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- شاشات التطبيق -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">تجربة مستخدم سلسة عبر جميع الأجهزة</h2>
                <p class="lead">استمتع بتجربة AURA للمراسلة والاتصالات على الهاتف المحمول والحاسوب واللوحي الإلكتروني</p>
            </div>
            
            <div class="col-lg-10 offset-lg-1">
                <div class="app-screenshots text-center">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow-sm hover-lift">
                                <div class="card-body">
                                    <div class="app-screenshot bg-light p-4 rounded-3 mb-3">
                                        <i class="fas fa-mobile-alt fa-5x text-info opacity-50"></i>
                                    </div>
                                    <h5 class="fw-bold">تطبيق الجوال</h5>
                                    <p>متاح على Android و iOS مع واجهة سهلة الاستخدام وإشعارات فورية.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow-sm hover-lift">
                                <div class="card-body">
                                    <div class="app-screenshot bg-light p-4 rounded-3 mb-3">
                                        <i class="fas fa-desktop fa-5x text-info opacity-50"></i>
                                    </div>
                                    <h5 class="fw-bold">تطبيق سطح المكتب</h5>
                                    <p>تطبيق متكامل لنظام Windows و Mac مع مزامنة لحظية لرسائلك.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card border-0 shadow-sm hover-lift">
                                <div class="card-body">
                                    <div class="app-screenshot bg-light p-4 rounded-3 mb-3">
                                        <i class="fas fa-globe fa-5x text-info opacity-50"></i>
                                    </div>
                                    <h5 class="fw-bold">الإصدار الويب</h5>
                                    <p>استخدم المنصة مباشرة من أي متصفح دون الحاجة لتثبيت أي برامج.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- مقارنة الباقات -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="fw-bold text-center mb-5">باقات الاتصالات</h2>
                        
                        <div class="table-responsive">
                            <table class="table table-hover pricing-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th>الميزة</th>
                                        <th class="text-center">الباقة المجانية</th>
                                        <th class="text-center">الباقة الأساسية</th>
                                        <th class="text-center">الباقة المتقدمة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>المراسلة الفورية</td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>المكالمات الصوتية</td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>مكالمات الفيديو</td>
                                        <td class="text-center">1:1 فقط</td>
                                        <td class="text-center">حتى 10 أشخاص</td>
                                        <td class="text-center">حتى 50 شخص</td>
                                    </tr>
                                    <tr>
                                        <td>مساحة تخزين سحابية</td>
                                        <td class="text-center">2GB</td>
                                        <td class="text-center">10GB</td>
                                        <td class="text-center">50GB</td>
                                    </tr>
                                    <tr>
                                        <td>عدد المجموعات</td>
                                        <td class="text-center">10</td>
                                        <td class="text-center">غير محدود</td>
                                        <td class="text-center">غير محدود</td>
                                    </tr>
                                    <tr>
                                        <td>حجم الملفات المرسلة</td>
                                        <td class="text-center">حتى 100MB</td>
                                        <td class="text-center">حتى 1GB</td>
                                        <td class="text-center">حتى 5GB</td>
                                    </tr>
                                    <tr>
                                        <td>دعم أولوي</td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>الثمن الشهري</td>
                                        <td class="text-center">مجاناً</td>
                                        <td class="text-center">$4.99</td>
                                        <td class="text-center">$9.99</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td class="text-center"><a href="#" class="btn btn-sm btn-outline-info">البدء مجاناً</a></td>
                                        <td class="text-center"><a href="#" class="btn btn-sm btn-info">اشترك الآن</a></td>
                                        <td class="text-center"><a href="#" class="btn btn-sm btn-info">اشترك الآن</a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- دعوة للعمل -->
        <div class="row mt-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="bg-light rounded-4 p-5 text-center">
                    <h2 class="fw-bold mb-4">جاهز للتواصل مع AURA؟</h2>
                    <p class="lead mb-4">انضم إلى ملايين المستخدمين واستمتع بتجربة تواصل سلسة وآمنة مع منصة AURA للمراسلة والاتصالات.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-info btn-lg">تنزيل التطبيق</a>
                        <a href="#" class="btn btn-outline-info btn-lg">تصفح المزيد</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
