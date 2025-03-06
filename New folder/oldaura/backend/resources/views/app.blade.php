@extends('layouts.app')

@section('title', 'تطبيق AURA')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <h1 class="fw-bold mb-4">تطبيق AURA</h1>
                <p class="lead">تحكم في حياتك الرقمية من خلال تطبيق واحد متكامل لجميع احتياجاتك اليومية.</p>
            </div>
        </div>
        
        <!-- قسم المعاينة -->
        <div class="row my-5 align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <h2 class="fw-bold mb-4">تطبيق واحد لكل شيء</h2>
                <p>نظام AURA هو أكثر من مجرد تطبيق - إنه نظام بيئي متكامل يجمع بين الخدمات المتنوعة في منصة واحدة سهلة الاستخدام.</p>
                <p>من المدفوعات إلى التسوق، ومن التواصل الاجتماعي إلى خدمات التوصيل، يوفر تطبيق AURA كل ما تحتاجه في تطبيق واحد بتصميم أنيق وواجهة بديهية.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="#" class="btn btn-dark">
                        <img src="{{ asset('assets/images/app-store-badge.png') }}" alt="App Store" height="40">
                    </a>
                    <a href="#" class="btn btn-dark">
                        <img src="{{ asset('assets/images/google-play-badge.png') }}" alt="Google Play" height="40">
                    </a>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <img src="{{ asset('assets/images/app-mockup.svg') }}" alt="AURA App Mockup" class="img-fluid hover-lift" style="max-height: 600px;">
            </div>
        </div>
        
        <!-- مميزات التطبيق -->
        <div class="row my-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">مميزات التطبيق</h2>
                <p class="lead">صمم تطبيق AURA ليقدم تجربة مستخدم استثنائية مع مجموعة من المميزات المتكاملة</p>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-primary text-white mx-auto mb-4">
                            <i class="fas fa-palette"></i>
                        </div>
                        <h3>تصميم عصري</h3>
                        <p>واجهة مستخدم عصرية وبديهية تجعل استخدام التطبيق متعة حقيقية مع تجربة سلسة وممتعة.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-success text-white mx-auto mb-4">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>أداء سريع</h3>
                        <p>تطبيق خفيف وسريع الاستجابة مع زمن تحميل منخفض وأداء سلس حتى على الشبكات البطيئة.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-info text-white mx-auto mb-4">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3>آمن ومحمي</h3>
                        <p>أمان متقدم مع تشفير البيانات والمصادقة متعددة العوامل لحماية بياناتك الشخصية والمالية.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-warning text-white mx-auto mb-4">
                            <i class="fas fa-cog"></i>
                        </div>
                        <h3>قابل للتخصيص</h3>
                        <p>خصص تجربتك في التطبيق وفقًا لتفضيلاتك واحتياجاتك الخاصة مع خيارات تخصيص متنوعة.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-danger text-white mx-auto mb-4">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h3>إشعارات ذكية</h3>
                        <p>نظام إشعارات ذكي يوفر لك التحديثات المهمة دون إزعاج مع إمكانية التخصيص الكامل للإشعارات.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-secondary text-white mx-auto mb-4">
                            <i class="fas fa-sync"></i>
                        </div>
                        <h3>تزامن سحابي</h3>
                        <p>تزامن بياناتك عبر جميع أجهزتك مع النسخ الاحتياطي السحابي للوصول إلى بياناتك في أي وقت ومن أي مكان.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- شاشات التطبيق -->
        <div class="row my-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">استكشف شاشات التطبيق</h2>
                <p class="lead">ألق نظرة على بعض شاشات التطبيق وتعرف على تجربة المستخدم الفريدة</p>
            </div>
            
            <div class="col-12">
                <div class="app-screenshots">
                    <div class="screenshot-carousel d-flex justify-content-between">
                        <div class="screenshot-item">
                            <img src="{{ asset('assets/images/app-mockup.svg') }}" alt="شاشة الرئيسية" class="img-fluid rounded shadow-sm">
                            <p class="mt-2 text-center">شاشة الرئيسية</p>
                        </div>
                        <div class="screenshot-item">
                            <img src="{{ asset('assets/images/app-mockup.svg') }}" alt="المحفظة الإلكترونية" class="img-fluid rounded shadow-sm">
                            <p class="mt-2 text-center">المحفظة الإلكترونية</p>
                        </div>
                        <div class="screenshot-item">
                            <img src="{{ asset('assets/images/app-mockup.svg') }}" alt="التسوق" class="img-fluid rounded shadow-sm">
                            <p class="mt-2 text-center">التسوق</p>
                        </div>
                        <div class="screenshot-item">
                            <img src="{{ asset('assets/images/app-mockup.svg') }}" alt="التواصل الاجتماعي" class="img-fluid rounded shadow-sm">
                            <p class="mt-2 text-center">التواصل الاجتماعي</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- الأسئلة الشائعة -->
        <div class="row my-5">
            <div class="col-lg-8 offset-lg-2">
                <h2 class="fw-bold text-center mb-4">الأسئلة الشائعة</h2>
                
                <div class="accordion shadow-sm" id="appFAQ">
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                على أي أنظمة تشغيل يتوفر تطبيق AURA؟
                            </button>
                        </h3>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#appFAQ">
                            <div class="accordion-body">
                                يتوفر تطبيق AURA حاليًا على أنظمة Android و iOS، مما يتيح لجميع مستخدمي الهواتف الذكية الاستفادة من خدماتنا المتكاملة.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                هل استخدام تطبيق AURA مجاني؟
                            </button>
                        </h3>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#appFAQ">
                            <div class="accordion-body">
                                نعم، تحميل التطبيق واستخدامه مجاني تمامًا. هناك بعض الخدمات التي قد تتطلب رسومًا إضافية، مثل رسوم التحويل أو رسوم التوصيل، ولكن معظم الخدمات الأساسية متاحة مجانًا.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                هل بياناتي آمنة في تطبيق AURA؟
                            </button>
                        </h3>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#appFAQ">
                            <div class="accordion-body">
                                نعم، نحن نأخذ أمان بيانات المستخدمين على محمل الجد. يستخدم تطبيق AURA تقنيات تشفير متقدمة ومصادقة متعددة العوامل لحماية بياناتك الشخصية والمالية. كما أننا نلتزم بأعلى معايير الأمان والخصوصية في الصناعة.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                هل يمكنني استخدام تطبيق AURA في الخارج؟
                            </button>
                        </h3>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#appFAQ">
                            <div class="accordion-body">
                                نعم، يمكنك استخدام تطبيق AURA في أي مكان في العالم طالما لديك اتصال بالإنترنت. ومع ذلك، قد تختلف توفر بعض الخدمات (مثل التوصيل) حسب البلد أو المنطقة. نحن نعمل باستمرار على توسيع نطاق خدماتنا لتشمل المزيد من الدول والمناطق.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                كيف يمكنني الحصول على المساعدة إذا واجهت مشكلة في التطبيق؟
                            </button>
                        </h3>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#appFAQ">
                            <div class="accordion-body">
                                يوفر تطبيق AURA دعمًا على مدار الساعة من خلال ميزة الدردشة المباشرة داخل التطبيق. يمكنك أيضًا الاتصال بنا عبر البريد الإلكتروني أو الهاتف، وسيقوم فريق خدمة العملاء لدينا بالرد عليك في أقرب وقت ممكن. بالإضافة إلى ذلك، لدينا قسم مخصص للأسئلة الشائعة داخل التطبيق يمكن أن يساعدك في حل المشكلات الشائعة.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- دعوة للعمل -->
        <div class="row mt-5">
            <div class="col-lg-8 offset-lg-2">
                <div class="card bg-dark text-white shadow">
                    <div class="card-body p-5 text-center">
                        <h2 class="fw-bold mb-4">جاهز لتجربة مستقبل الخدمات الرقمية؟</h2>
                        <p class="lead mb-4">حمل تطبيق AURA الآن واستمتع بتجربة رقمية متكاملة في تطبيق واحد!</p>
                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="#" class="btn btn-light btn-lg">
                                <i class="fab fa-apple me-2"></i> App Store
                            </a>
                            <a href="#" class="btn btn-light btn-lg">
                                <i class="fab fa-google-play me-2"></i> Google Play
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
