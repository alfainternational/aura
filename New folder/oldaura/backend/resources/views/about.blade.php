@extends('layouts.app')

@section('title', 'عن AURA - النظام البيئي الرقمي المتكامل')

@section('content')
    <div class="container py-5">
        <!-- قسم عنوان الصفحة -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <h1 class="fw-bold mb-4">عن AURA</h1>
                <p class="lead">نظام بيئي رقمي متكامل يهدف إلى توفير مجموعة شاملة من الخدمات الرقمية في منصة واحدة</p>
            </div>
        </div>
        
        <!-- قسم القصة والرؤية -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <div class="row">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h2 class="fw-bold mb-4">قصتنا</h2>
                                <p>تأسست AURA في عام 2021 كاستجابة للحاجة المتزايدة لتوفير حلول رقمية متكاملة تسهل الحياة اليومية للأفراد والشركات.</p>
                                <p>بدأت رحلتنا كمنصة للمدفوعات الإلكترونية، ثم توسعنا تدريجياً لنشمل خدمات التجارة الإلكترونية، والتواصل الاجتماعي، وخدمات التوصيل، والمزيد من الخدمات التي تلبي احتياجات المستخدمين في عالم رقمي متطور.</p>
                                <p>اليوم، أصبحت AURA منصة متكاملة تخدم أكثر من 2 مليون مستخدم نشط وتتعامل مع أكثر من 50 ألف معاملة يومياً.</p>
                            </div>
                            <div class="col-md-6">
                                <h2 class="fw-bold mb-4">رؤيتنا</h2>
                                <p>نسعى في AURA لأن نصبح النظام البيئي الرقمي الرائد في المنطقة، ونهدف إلى تمكين الأفراد والشركات من الوصول إلى عالم من الفرص الرقمية بسهولة وأمان.</p>
                                <p>رؤيتنا هي خلق عالم تكون فيه جميع الخدمات الرقمية متاحة للجميع، بغض النظر عن خلفياتهم أو مواقعهم، وتوفير تجربة سلسة ومتكاملة تتيح للمستخدمين إدارة حياتهم الرقمية بكفاءة وفعالية.</p>
                                <p>نؤمن بقوة التكنولوجيا في تغيير حياة الناس للأفضل، ونلتزم بتطوير حلول مبتكرة تلبي احتياجات المستخدمين المتغيرة.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم قيمنا -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">قيمنا</h2>
                <p class="lead">المبادئ التي توجه عملنا في AURA وتشكل ثقافتنا وطريقة خدمة عملائنا</p>
            </div>
            
            <div class="col-lg-10 offset-lg-1">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="value-icon text-primary mx-auto mb-3">
                                    <i class="fas fa-shield-alt fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">الأمان والثقة</h4>
                                <p>نضع أمان بيانات المستخدمين على رأس أولوياتنا ونعمل باستمرار على تعزيز أنظمة الحماية والخصوصية.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="value-icon text-primary mx-auto mb-3">
                                    <i class="fas fa-lightbulb fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">الابتكار المستمر</h4>
                                <p>نسعى دائماً لابتكار حلول جديدة وتطوير خدماتنا الحالية لتلبية احتياجات المستخدمين المتغيرة.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="value-icon text-primary mx-auto mb-3">
                                    <i class="fas fa-users fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">التركيز على العميل</h4>
                                <p>نضع احتياجات المستخدمين في صميم كل ما نقوم به، ونسعى لتوفير تجربة استثنائية لكل عميل.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="value-icon text-primary mx-auto mb-3">
                                    <i class="fas fa-globe fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">الشمولية</h4>
                                <p>نسعى لتوفير خدماتنا للجميع بغض النظر عن مواقعهم أو خلفياتهم، ونعمل على تقليل الفجوة الرقمية.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="value-icon text-primary mx-auto mb-3">
                                    <i class="fas fa-handshake fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">الشراكة والتعاون</h4>
                                <p>نؤمن بقوة الشراكات والتعاون مع مختلف الجهات لتوفير خدمات متكاملة تلبي احتياجات المستخدمين.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 text-center">
                            <div class="card-body p-4">
                                <div class="value-icon text-primary mx-auto mb-3">
                                    <i class="fas fa-chart-line fa-3x"></i>
                                </div>
                                <h4 class="fw-bold">النمو المستدام</h4>
                                <p>نلتزم بتحقيق نمو مستدام يراعي احتياجات المجتمع والبيئة، ويساهم في تحقيق أهداف التنمية المستدامة.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم فريق العمل -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">فريق القيادة</h2>
                <p class="lead">نخبة من الخبراء والمتخصصين الذين يقودون AURA نحو تحقيق رؤيتها</p>
            </div>
            
            <div class="col-lg-10 offset-lg-1">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="team-member-img mb-3">
                                    <img src="{{ asset('assets/images/team/ceo.jpg') }}" alt="CEO" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <h4 class="fw-bold">أحمد محمد</h4>
                                <p class="text-muted">الرئيس التنفيذي والمؤسس</p>
                                <p>رائد أعمال ومتخصص في مجال التكنولوجيا المالية مع خبرة تزيد عن 15 عاماً في قطاع التكنولوجيا.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="team-member-img mb-3">
                                    <img src="{{ asset('assets/images/team/cto.jpg') }}" alt="CTO" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <h4 class="fw-bold">سارة علي</h4>
                                <p class="text-muted">المدير التقني</p>
                                <p>متخصصة في علوم الحاسوب والذكاء الاصطناعي مع خبرة واسعة في تطوير المنصات الرقمية المتكاملة.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body p-4">
                                <div class="team-member-img mb-3">
                                    <img src="{{ asset('assets/images/team/coo.jpg') }}" alt="COO" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <h4 class="fw-bold">عمر خالد</h4>
                                <p class="text-muted">مدير العمليات</p>
                                <p>خبير في إدارة العمليات والاستراتيجيات مع سجل حافل في توسيع نطاق الشركات الناشئة وتحسين كفاءة العمليات.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم الإنجازات -->
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5">
                        <h2 class="fw-bold mb-4 text-center">إنجازاتنا</h2>
                        
                        <div class="timeline">
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-date text-primary fw-bold me-3">2021</div>
                                    <div>
                                        <h5 class="fw-bold">تأسيس AURA</h5>
                                        <p>إطلاق المنصة كخدمة للمدفوعات الإلكترونية في 3 مدن رئيسية.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-date text-primary fw-bold me-3">2022</div>
                                    <div>
                                        <h5 class="fw-bold">توسع الخدمات والمستخدمين</h5>
                                        <p>إضافة خدمات التجارة الإلكترونية والمراسلة، والوصول إلى 500 ألف مستخدم.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item mb-4 pb-4 border-bottom">
                                <div class="d-flex">
                                    <div class="timeline-date text-primary fw-bold me-3">2023</div>
                                    <div>
                                        <h5 class="fw-bold">إطلاق خدمات التوصيل والمساعد الذكي</h5>
                                        <p>توسيع نطاق الخدمات وتجاوز مليون مستخدم مع تغطية 10 دول.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="timeline-date text-primary fw-bold me-3">2024</div>
                                    <div>
                                        <h5 class="fw-bold">توسع إقليمي وعالمي</h5>
                                        <p>الوصول إلى أكثر من 2 مليون مستخدم و5000 تاجر شريك في 15 دولة.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم الشركاء -->
        <div class="row mb-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold">شركاؤنا</h2>
                <p class="lead">نفخر بالعمل مع مجموعة من الشركات والمؤسسات الرائدة</p>
            </div>
            
            <div class="col-lg-10 offset-lg-1">
                <div class="partners-grid">
                    <div class="row row-cols-2 row-cols-md-4 g-4">
                        <div class="col">
                            <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center p-4">
                                <img src="{{ asset('assets/images/partners/partner1.png') }}" alt="Partner 1" class="img-fluid" style="max-height: 80px;">
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center p-4">
                                <img src="{{ asset('assets/images/partners/partner2.png') }}" alt="Partner 2" class="img-fluid" style="max-height: 80px;">
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center p-4">
                                <img src="{{ asset('assets/images/partners/partner3.png') }}" alt="Partner 3" class="img-fluid" style="max-height: 80px;">
                            </div>
                        </div>
                        <div class="col">
                            <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center p-4">
                                <img src="{{ asset('assets/images/partners/partner4.png') }}" alt="Partner 4" class="img-fluid" style="max-height: 80px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- قسم التواصل -->
        <div class="row mt-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="bg-light rounded-4 p-5 text-center">
                    <h2 class="fw-bold mb-4">تواصل معنا</h2>
                    <p class="lead mb-4">نحن هنا للإجابة على استفساراتك ومساعدتك في أي وقت</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="#" class="btn btn-primary btn-lg">اتصل بنا</a>
                        <a href="#" class="btn btn-outline-primary btn-lg">الوظائف المتاحة</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
