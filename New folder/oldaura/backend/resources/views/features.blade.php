@extends('layouts.app')

@section('title', 'مميزات AURA')

@section('content')
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 offset-lg-2 text-center">
                <h1 class="fw-bold mb-4">مميزات AURA</h1>
                <p class="lead">استكشف المميزات الفريدة التي تجعل AURA النظام البيئي الرقمي الأمثل لتلبية احتياجاتك اليومية.</p>
            </div>
        </div>
        
        <!-- المميزات الرئيسية -->
        <div class="row mb-5">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-primary text-white mb-3">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h3 class="card-title">محفظة إلكترونية متكاملة</h3>
                        <p class="card-text">محفظة رقمية تتيح لك إجراء المدفوعات وتحويل الأموال وإدارة ميزانيتك بسهولة وأمان.</p>
                        <ul class="list-unstyled mt-3">
                            <li><i class="fas fa-check text-success me-2"></i> تحويلات فورية بين المستخدمين</li>
                            <li><i class="fas fa-check text-success me-2"></i> دفع الفواتير وشحن الرصيد</li>
                            <li><i class="fas fa-check text-success me-2"></i> تتبع المصروفات والميزانية</li>
                            <li><i class="fas fa-check text-success me-2"></i> برنامج مكافآت ونقاط ولاء</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-success text-white mb-3">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3 class="card-title">منصة تسوق متكاملة</h3>
                        <p class="card-text">منصة تجارة إلكترونية متكاملة تتيح لك الشراء والبيع بكل سهولة وأمان.</p>
                        <ul class="list-unstyled mt-3">
                            <li><i class="fas fa-check text-success me-2"></i> متاجر متنوعة في مكان واحد</li>
                            <li><i class="fas fa-check text-success me-2"></i> دفع آمن وسهل</li>
                            <li><i class="fas fa-check text-success me-2"></i> إمكانية إنشاء متجرك الخاص</li>
                            <li><i class="fas fa-check text-success me-2"></i> عروض وخصومات حصرية</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-info text-white mb-3">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3 class="card-title">تواصل اجتماعي</h3>
                        <p class="card-text">منصة تواصل اجتماعي تتيح لك البقاء على تواصل مع أصدقائك وعائلتك ومشاركة لحظاتك معهم.</p>
                        <ul class="list-unstyled mt-3">
                            <li><i class="fas fa-check text-success me-2"></i> مراسلة فورية مشفرة</li>
                            <li><i class="fas fa-check text-success me-2"></i> مكالمات صوتية ومرئية</li>
                            <li><i class="fas fa-check text-success me-2"></i> مشاركة الصور والفيديو</li>
                            <li><i class="fas fa-check text-success me-2"></i> مجموعات اهتمام مشتركة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-warning text-white mb-3">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h3 class="card-title">خدمات التوصيل</h3>
                        <p class="card-text">خدمات توصيل سريعة وموثوقة لتوصيل مشترياتك وطلباتك إلى باب منزلك.</p>
                        <ul class="list-unstyled mt-3">
                            <li><i class="fas fa-check text-success me-2"></i> توصيل الطعام من المطاعم</li>
                            <li><i class="fas fa-check text-success me-2"></i> توصيل البقالة والاحتياجات اليومية</li>
                            <li><i class="fas fa-check text-success me-2"></i> شحن الطرود بين المدن</li>
                            <li><i class="fas fa-check text-success me-2"></i> تتبع الطلبات في الوقت الفعلي</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-danger text-white mb-3">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3 class="card-title">مساعد ذكي</h3>
                        <p class="card-text">مساعد شخصي مدعوم بالذكاء الاصطناعي لمساعدتك في إدارة مهامك اليومية.</p>
                        <ul class="list-unstyled mt-3">
                            <li><i class="fas fa-check text-success me-2"></i> إدارة المواعيد والتذكيرات</li>
                            <li><i class="fas fa-check text-success me-2"></i> اقتراحات شخصية للتسوق</li>
                            <li><i class="fas fa-check text-success me-2"></i> مساعدة في الاستفسارات العامة</li>
                            <li><i class="fas fa-check text-success me-2"></i> تحليل الإنفاق وتقديم نصائح مالية</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="feature-icon bg-secondary text-white mb-3">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3 class="card-title">نظام الوكلاء</h3>
                        <p class="card-text">نظام وكلاء متكامل يتيح للأفراد العمل كوكلاء لخدمات AURA وتحقيق دخل إضافي.</p>
                        <ul class="list-unstyled mt-3">
                            <li><i class="fas fa-check text-success me-2"></i> التسجيل السهل كوكيل</li>
                            <li><i class="fas fa-check text-success me-2"></i> عمولات مجزية على الخدمات</li>
                            <li><i class="fas fa-check text-success me-2"></i> تدريب وتأهيل مستمر</li>
                            <li><i class="fas fa-check text-success me-2"></i> لوحة تحكم خاصة للوكلاء</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- المميزات الحصرية -->
        <div class="row my-5">
            <div class="col-12 text-center mb-5">
                <h2 class="fw-bold">ميزات حصرية في AURA</h2>
                <p class="lead">ما يميزنا عن غيرنا ويجعل تجربتك معنا فريدة من نوعها</p>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm bg-primary text-white">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-4">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-circle fa-stack-2x text-white-50"></i>
                                    <i class="fas fa-lock fa-stack-1x text-primary"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="fw-bold">أمان متطور</h3>
                                <p>نظام أمان متطور يضمن حماية بياناتك ومعاملاتك المالية من خلال تقنيات التشفير المتقدمة والمصادقة متعددة العوامل.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm bg-success text-white">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-4">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-circle fa-stack-2x text-white-50"></i>
                                    <i class="fas fa-sync fa-stack-1x text-success"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="fw-bold">تكامل شامل</h3>
                                <p>تكامل فريد بين جميع الخدمات يتيح لك تجربة سلسة ومتكاملة دون الحاجة للتنقل بين التطبيقات المختلفة.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm bg-info text-white">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-4">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-circle fa-stack-2x text-white-50"></i>
                                    <i class="fas fa-bolt fa-stack-1x text-info"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="fw-bold">أداء متفوق</h3>
                                <p>تم تصميم النظام ليعمل بسرعة وكفاءة عالية حتى على الاتصالات البطيئة، مما يضمن تجربة سلسة في جميع الظروف.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm bg-warning text-white">
                    <div class="card-body p-4">
                        <div class="d-flex">
                            <div class="me-4">
                                <span class="fa-stack fa-2x">
                                    <i class="fas fa-circle fa-stack-2x text-white-50"></i>
                                    <i class="fas fa-trophy fa-stack-1x text-warning"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="fw-bold">برنامج مكافآت فريد</h3>
                                <p>نظام مكافآت متطور يمنح المستخدمين نقاط على كل معاملة يمكن استبدالها بقسائم شراء وخصومات حصرية.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- المقارنة التنافسية -->
        <div class="row my-5">
            <div class="col-lg-10 offset-lg-1">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="fw-bold text-center mb-4">لماذا AURA أفضل الخيارات؟</h2>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered compare-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">الميزات</th>
                                        <th scope="col" class="text-center">AURA</th>
                                        <th scope="col" class="text-center">المنافسون</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>خدمات متكاملة في منصة واحدة</td>
                                        <td class="text-center text-success"><i class="fas fa-check-circle fa-lg"></i></td>
                                        <td class="text-center text-danger"><i class="fas fa-times-circle fa-lg"></i></td>
                                    </tr>
                                    <tr>
                                        <td>محفظة إلكترونية شاملة</td>
                                        <td class="text-center text-success"><i class="fas fa-check-circle fa-lg"></i></td>
                                        <td class="text-center text-secondary"><i class="fas fa-minus-circle fa-lg"></i></td>
                                    </tr>
                                    <tr>
                                        <td>نظام مكافآت متكامل</td>
                                        <td class="text-center text-success"><i class="fas fa-check-circle fa-lg"></i></td>
                                        <td class="text-center text-secondary"><i class="fas fa-minus-circle fa-lg"></i></td>
                                    </tr>
                                    <tr>
                                        <td>مساعد ذكي مدعوم بالذكاء الاصطناعي</td>
                                        <td class="text-center text-success"><i class="fas fa-check-circle fa-lg"></i></td>
                                        <td class="text-center text-danger"><i class="fas fa-times-circle fa-lg"></i></td>
                                    </tr>
                                    <tr>
                                        <td>دعم فني على مدار الساعة</td>
                                        <td class="text-center text-success"><i class="fas fa-check-circle fa-lg"></i></td>
                                        <td class="text-center text-secondary"><i class="fas fa-minus-circle fa-lg"></i></td>
                                    </tr>
                                    <tr>
                                        <td>نظام وكلاء لتحقيق دخل إضافي</td>
                                        <td class="text-center text-success"><i class="fas fa-check-circle fa-lg"></i></td>
                                        <td class="text-center text-danger"><i class="fas fa-times-circle fa-lg"></i></td>
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
                <div class="card bg-dark text-white shadow">
                    <div class="card-body p-5 text-center">
                        <h2 class="fw-bold mb-4">جاهز للانضمام إلى مستقبل الخدمات الرقمية؟</h2>
                        <p class="lead mb-4">ابدأ رحلتك مع AURA اليوم واستمتع بتجربة رقمية متكاملة تلبي جميع احتياجاتك.</p>
                        <a href="#" class="btn btn-light btn-lg px-5">سجل الآن</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
