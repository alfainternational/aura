@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center mb-5">{{ __('الأسئلة الشائعة') }}</h1>
            
            <div class="accordion" id="faqAccordion">
                <!-- Question 1 -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingOne">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-right text-dark" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <i class="fas fa-question-circle me-2"></i> {{ __('كيف يمكنني إنشاء حساب جديد؟') }}
                            </button>
                        </h2>
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#faqAccordion">
                        <div class="card-body">
                            {{ __('يمكنك إنشاء حساب جديد عن طريق النقر على زر "تسجيل" في أعلى الصفحة، ثم إدخال بياناتك الشخصية المطلوبة، بما في ذلك الاسم الكامل وعنوان البريد الإلكتروني ورقم الهاتف وكلمة المرور. بعد ذلك، ستحتاج إلى تأكيد بريدك الإلكتروني قبل أن تتمكن من استخدام جميع ميزات التطبيق.') }}
                        </div>
                    </div>
                </div>
                
                <!-- Question 2 -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingTwo">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-right text-dark collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <i class="fas fa-question-circle me-2"></i> {{ __('كيف يمكنني تغيير كلمة المرور الخاصة بي؟') }}
                            </button>
                        </h2>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                        <div class="card-body">
                            {{ __('لتغيير كلمة المرور الخاصة بك، قم بتسجيل الدخول إلى حسابك، ثم انتقل إلى لوحة التحكم. اضغط على رابط "تغيير كلمة المرور" في قسم الإجراءات السريعة. ستحتاج إلى إدخال كلمة المرور الحالية أولاً، ثم إدخال كلمة المرور الجديدة وتأكيدها.') }}
                        </div>
                    </div>
                </div>
                
                <!-- Question 3 -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingThree">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-right text-dark collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <i class="fas fa-question-circle me-2"></i> {{ __('ما هي الدول والمدن المدعومة في التطبيق؟') }}
                            </button>
                        </h2>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
                        <div class="card-body">
                            {{ __('حالياً، يدعم التطبيق عدة مدن في السودان، بما في ذلك الخرطوم وأم درمان وبحري والعديد من المدن الأخرى. نحن نعمل باستمرار على توسيع نطاق خدماتنا لتشمل المزيد من المدن والدول. يمكنك التحقق من توفر الخدمة في منطقتك عن طريق تحديث موقعك في التطبيق.') }}
                        </div>
                    </div>
                </div>
                
                <!-- Question 4 -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingFour">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-right text-dark collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                <i class="fas fa-question-circle me-2"></i> {{ __('كيف يمكنني العثور على متاجر ووكلاء قريبين مني؟') }}
                            </button>
                        </h2>
                    </div>
                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#faqAccordion">
                        <div class="card-body">
                            {{ __('يمكنك العثور على المتاجر والوكلاء القريبين منك عن طريق تفعيل خدمة تحديد الموقع في جهازك. بمجرد تفعيل الخدمة، سيقوم التطبيق تلقائياً بعرض المتاجر والوكلاء القريبين منك في لوحة التحكم الخاصة بك. يمكنك أيضاً استخدام خاصية البحث للعثور على متاجر أو وكلاء محددين في منطقتك.') }}
                        </div>
                    </div>
                </div>
                
                <!-- Question 5 -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingFive">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-right text-dark collapsed" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                <i class="fas fa-question-circle me-2"></i> {{ __('ما هي خيارات الدفع المتاحة؟') }}
                            </button>
                        </h2>
                    </div>
                    <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#faqAccordion">
                        <div class="card-body">
                            {{ __('يقدم التطبيق عدة خيارات للدفع، بما في ذلك الدفع عند الاستلام، والدفع ببطاقات الائتمان/الخصم، والمحافظ الإلكترونية مثل محفظتك الرقمية في التطبيق. تختلف خيارات الدفع المتاحة حسب الموقع والخدمة التي تستخدمها. يمكنك الاطلاع على جميع خيارات الدفع المتاحة عند إجراء عملية شراء.') }}
                        </div>
                    </div>
                </div>
                
                <!-- Question 6 -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingSix">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-right text-dark collapsed" type="button" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                <i class="fas fa-question-circle me-2"></i> {{ __('كيف يمكنني تعديل معلوماتي الشخصية؟') }}
                            </button>
                        </h2>
                    </div>
                    <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#faqAccordion">
                        <div class="card-body">
                            {{ __('يمكنك تعديل معلوماتك الشخصية بسهولة من خلال الانتقال إلى لوحة التحكم الخاصة بك، ثم النقر على رابط "تعديل الملف الشخصي" في قسم الإجراءات السريعة. هناك يمكنك تحديث معلوماتك مثل الاسم ورقم الهاتف والعنوان وغيرها من المعلومات الشخصية.') }}
                        </div>
                    </div>
                </div>
                
                <!-- Question 7 -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingSeven">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-right text-dark collapsed" type="button" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                <i class="fas fa-question-circle me-2"></i> {{ __('كيف يمكنني الاتصال بدعم العملاء؟') }}
                            </button>
                        </h2>
                    </div>
                    <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#faqAccordion">
                        <div class="card-body">
                            {{ __('يمكنك الاتصال بفريق دعم العملاء لدينا من خلال عدة طرق. يمكنك استخدام نموذج الاتصال الموجود في صفحة "اتصل بنا"، أو إرسال بريد إلكتروني مباشرة إلى support@aura.com، أو الاتصال بنا على الرقم المخصص لخدمة العملاء. نحن نسعى دائماً لتقديم الدعم على مدار الساعة طوال أيام الأسبوع.') }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5">
                <p>{{ __('هل لديك سؤال آخر؟') }}</p>
                <a href="{{ route('contact') }}" class="btn btn-primary">{{ __('اتصل بنا') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
