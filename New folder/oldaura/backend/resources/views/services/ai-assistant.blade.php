@extends('layouts.app')

@section('title', 'المساعد الذكي - AURA')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="service-icon bg-primary text-white mx-auto mb-4">
                    <i class="fas fa-robot fa-3x"></i>
                </div>
                <h1 class="fw-bold mb-4">المساعد الذكي AURA</h1>
                <p class="lead">مساعدك الشخصي المدعوم بالذكاء الاصطناعي الذي يتعلم تفضيلاتك ويساعدك على إدارة حياتك اليومية بكفاءة وذكاء.</p>
            </div>
        </div>
        
        <!-- مميزات المساعد الذكي -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4">مميزات المساعد الذكي</h2>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-brain"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">ذكاء اصطناعي متطور</h5>
                                        <p>مدعوم بأحدث تقنيات الذكاء الاصطناعي وتعلم الآلة لفهم احتياجاتك وتفضيلاتك.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">إدارة المهام</h5>
                                        <p>ينظم مهامك ومواعيدك ويذكرك بها في الوقت المناسب حسب أولوياتك.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-shopping-basket"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">اقتراحات التسوق</h5>
                                        <p>يقدم اقتراحات شخصية للتسوق بناءً على عاداتك السابقة واحتياجاتك الحالية.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">إدارة المالية</h5>
                                        <p>يساعدك على تتبع مصروفاتك، وضع ميزانية، وتقديم نصائح لتوفير المال.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-language"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">التفاعل الطبيعي</h5>
                                        <p>يفهم اللغة العربية بشكل طبيعي ويتفاعل معك بمحادثات سلسة كأنه إنسان حقيقي.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="feature-icon bg-primary text-white me-3">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold">خصوصية آمنة</h5>
                                        <p>معلوماتك محمية بتشفير من طرف لطرف ولا يتم مشاركتها مع أي جهة ثالثة.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- كيف يعمل المساعد الذكي -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4 text-center">كيف يعمل المساعد الذكي؟</h2>
                        
                        <div class="timeline">
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-primary text-white">1</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">يتعلم منك</h5>
                                        <p>يقوم المساعد الذكي بتحليل سلوكك وتفضيلاتك من خلال تفاعلاتك السابقة لفهم احتياجاتك بشكل أفضل.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-primary text-white">2</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">يتوقع احتياجاتك</h5>
                                        <p>بناءً على عاداتك اليومية، يمكنه توقع احتياجاتك المستقبلية وتقديم اقتراحات استباقية.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-primary text-white">3</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">يتكامل مع الخدمات الأخرى</h5>
                                        <p>يعمل بسلاسة مع جميع خدمات AURA الأخرى مثل المحفظة الإلكترونية، التسوق، وخدمات التوصيل.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="timeline-icon bg-primary text-white">4</div>
                                    <div class="ms-3">
                                        <h5 class="fw-bold">يتكيف مع تغيراتك</h5>
                                        <p>يواصل التعلم والتكيف مع تغير احتياجاتك وتفضيلاتك مع مرور الوقت.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- حالات استخدام المساعد الذكي -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">استخدامات المساعد الذكي</h2>
                <p class="lead">اكتشف كيف يمكن للمساعد الذكي AURA مساعدتك في مختلف جوانب حياتك اليومية</p>
            </div>
            
            <div class="col-lg-10 offset-lg-1">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center hover-lift">
                            <div class="card-body p-4">
                                <div class="use-case-icon bg-light text-primary rounded-circle mx-auto mb-4">
                                    <i class="fas fa-calendar-alt fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">إدارة الجدول الزمني</h4>
                                <p>"أذكرني بموعد الطبيب غداً في الساعة 4 مساءً"</p>
                                <p>"نظم جدول اجتماعاتي لهذا الأسبوع"</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center hover-lift">
                            <div class="card-body p-4">
                                <div class="use-case-icon bg-light text-primary rounded-circle mx-auto mb-4">
                                    <i class="fas fa-utensils fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">اقتراحات الطعام</h4>
                                <p>"ما هي وصفات العشاء السريعة للعائلة الليلة؟"</p>
                                <p>"اطلب وجبتي المفضلة من المطعم القريب"</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center hover-lift">
                            <div class="card-body p-4">
                                <div class="use-case-icon bg-light text-primary rounded-circle mx-auto mb-4">
                                    <i class="fas fa-wallet fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">النصائح المالية</h4>
                                <p>"كم أنفقت على المطاعم هذا الشهر؟"</p>
                                <p>"ساعدني في وضع ميزانية للسفر الشهر القادم"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- باقات المساعد الذكي -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="fw-bold text-center mb-5">باقات المساعد الذكي</h2>
                        
                        <div class="table-responsive">
                            <table class="table table-hover pricing-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th>الميزة</th>
                                        <th class="text-center">الباقة المجانية</th>
                                        <th class="text-center">الباقة المتقدمة</th>
                                        <th class="text-center">الباقة الاحترافية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>التفاعل اليومي</td>
                                        <td class="text-center">50 رسالة</td>
                                        <td class="text-center">غير محدود</td>
                                        <td class="text-center">غير محدود</td>
                                    </tr>
                                    <tr>
                                        <td>تكامل مع الخدمات</td>
                                        <td class="text-center">2 خدمات</td>
                                        <td class="text-center">جميع الخدمات</td>
                                        <td class="text-center">جميع الخدمات</td>
                                    </tr>
                                    <tr>
                                        <td>التخصيص المتقدم</td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>اقتراحات استباقية</td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>تحليلات مفصلة</td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                    </tr>
                                    <tr>
                                        <td>الأولوية في المساعدة</td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
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
                                        <td class="text-center"><a href="#" class="btn btn-sm btn-outline-primary">البدء مجاناً</a></td>
                                        <td class="text-center"><a href="#" class="btn btn-sm btn-primary">اشترك الآن</a></td>
                                        <td class="text-center"><a href="#" class="btn btn-sm btn-primary">اشترك الآن</a></td>
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
                    <h2 class="fw-bold mb-4">جرب مساعدنا الذكي اليوم!</h2>
                    <p class="lead mb-4">احصل على مساعد شخصي افتراضي يتعلم تفضيلاتك ويبسط حياتك اليومية بذكاء وفعالية.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-primary btn-lg">ابدأ مجاناً</a>
                        <a href="#" class="btn btn-outline-primary btn-lg">عرض توضيحي</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
