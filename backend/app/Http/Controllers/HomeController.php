<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\LocationService;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    protected $locationService;

    /**
     * Create a new controller instance.
     */
    public function __construct(LocationService $locationService)
    {
        $this->middleware('auth')->except(['landing', 'about', 'contact', 'submitContact', 'features', 'appOverview', 'testimonials', 'privacyPolicy', 'termsOfService', 'cookiesPolicy', 'faq']);
        $this->locationService = $locationService;
    }

    /**
     * Landing page for non-authenticated users
     */
    public function landing()
    {
        $supportedLocations = $this->locationService->getSupportedLocations();
        return view('landing', [
            'supportedCountries' => $supportedLocations['countries']
        ]);
    }

    /**
     * Show the about page
     */
    public function about()
    {
        $supportedLocations = $this->locationService->getSupportedLocations();
        return view('home.about', [
            'pageTitle' => 'نبذة عن أورا',
            'supportedCountries' => $supportedLocations['countries']
        ]);
    }

    /**
     * Show the contact page
     */
    public function contact()
    {
        return view('home.contact', [
            'pageTitle' => 'اتصل بنا',
            'supportedCountries' => $this->locationService->getSupportedCountries()
        ]);
    }

    /**
     * Handle contact form submission
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:1000'
        ]);

        // Log contact submission
        Log::info('Contact form submitted', $validated);

        // In a real-world scenario, you might:
        // 1. Send an email to support
        // 2. Store the message in the database
        // 3. Trigger notifications

        return redirect()->route('contact')
            ->with('status', 'تم استلام رسالتك بنجاح. سنقوم بالرد عليك قريبًا.');
    }

    /**
     * Show the features page
     */
    public function features()
    {
        $features = [
            [
                'icon' => 'fa-comment-alt',
                'title' => 'المراسلة المباشرة',
                'description' => 'تواصل بسهولة مع رسائل نصية وصور مباشرة. دعم المحادثات الفردية والجماعية.'
            ],
            [
                'icon' => 'fa-phone',
                'title' => 'مكالمات صوتية',
                'description' => 'مكالمات صوتية واضحة وموثوقة مع جودة عالية وأمان كامل.'
            ],
            [
                'icon' => 'fa-lock',
                'title' => 'خصوصية وأمان',
                'description' => 'حماية بيانات المستخدم من خلال التشفير المتقدم والتحقق من الهوية.'
            ],
            [
                'icon' => 'fa-map-marker-alt',
                'title' => 'دعم محلي',
                'description' => 'مصمم خصيصًا للمستخدمين في السودان والمملكة العربية السعودية.'
            ],
            [
                'icon' => 'fa-user-shield',
                'title' => 'إدارة المستخدمين',
                'description' => 'لوحة تحكم إدارية متقدمة للتحكم في حسابات المستخدمين.'
            ]
        ];

        return view('home.features', [
            'pageTitle' => 'مميزات أورا',
            'features' => $features,
            'supportedCountries' => $this->locationService->getSupportedCountries()
        ]);
    }

    /**
     * Show the app overview page
     */
    public function appOverview()
    {
        $appSections = [
            [
                'icon' => 'fa-comments',
                'title' => 'المراسلة',
                'description' => 'تواصل بسهولة مع رسائل نصية وصور. دعم المحادثات الفردية والجماعية.',
                'features' => [
                    'محادثات فردية',
                    'محادثات جماعية',
                    'مشاركة الصور',
                    'حذف الرسائل'
                ]
            ],
            [
                'icon' => 'fa-phone-alt',
                'title' => 'المكالمات الصوتية',
                'description' => 'مكالمات صوتية واضحة وموثوقة مع جودة عالية.',
                'features' => [
                    'مكالمات فردية',
                    'جودة صوت عالية',
                    'سرعة اتصال',
                    'أمان كامل'
                ]
            ],
            [
                'icon' => 'fa-user-shield',
                'title' => 'الخصوصية والأمان',
                'description' => 'حماية بيانات المستخدم من خلال التشفير المتقدم والتحقق من الهوية.',
                'features' => [
                    'التحقق الثنائي',
                    'تشفير المحادثات',
                    'التحكم في الخصوصية',
                    'حماية البيانات'
                ]
            ]
        ];

        return view('home.app', [
            'pageTitle' => 'نظرة عامة على تطبيق أورا',
            'appSections' => $appSections,
            'supportedCountries' => $this->locationService->getSupportedCountries()
        ]);
    }

    /**
     * Show the testimonials page
     */
    public function testimonials()
    {
        $testimonials = [
            [
                'name' => 'محمد أحمد',
                'location' => 'الخرطوم، السودان',
                'avatar' => 'male-1.jpg',
                'quote' => 'أورا غيرت طريقة تواصلي تمامًا. التطبيق سريع وآمن، وأشعر بالراحة عند استخدامه.',
                'rating' => 5
            ],
            [
                'name' => 'فاطمة محمد',
                'location' => 'جدة، المملكة العربية السعودية',
                'avatar' => 'female-1.jpg',
                'quote' => 'كتطبيق محلي، أورا يفهم احتياجاتنا بشكل مذهل. المكالمات الصوتية والمراسلة رائعة.',
                'rating' => 5
            ],
            [
                'name' => 'عبدالله سليمان',
                'location' => 'الرياض، المملكة العربية السعودية',
                'avatar' => 'male-2.jpg',
                'quote' => 'الأمان والخصوصية هما أهم ما يميز أورا. أثق تمامًا في حماية بياناتي.',
                'rating' => 4
            ]
        ];

        return view('home.testimonials', [
            'pageTitle' => 'آراء المستخدمين',
            'testimonials' => $testimonials,
            'supportedCountries' => $this->locationService->getSupportedCountries()
        ]);
    }

    /**
     * Show the privacy policy page
     */
    public function privacyPolicy()
    {
        $privacyPolicySections = [
            [
                'title' => 'جمع المعلومات الشخصية',
                'content' => 'نجمع فقط المعلومات الضرورية للتواصل والتحقق من الهوية. تشمل هذه المعلومات اسمك، رقم هاتفك، وموقعك الجغرافي.'
            ],
            [
                'title' => 'استخدام البيانات',
                'content' => 'نستخدم بياناتك حصريًا لتوفير خدمات التواصل والمكالمات الصوتية. لن نشارك معلوماتك مع أطراف ثالثة دون موافقتك الصريحة.'
            ],
            [
                'title' => 'الأمان والتشفير',
                'content' => 'نطبق أعلى معايير الأمان لحماية بياناتك. جميع الرسائل والمكالمات مشفرة من طرف إلى طرف.'
            ],
            [
                'title' => 'حقوق المستخدم',
                'content' => 'يمكنك في أي وقت طلب حذف حسابك وجميع بياناتك. نحترم خصوصيتك ونمنحك التحكم الكامل في معلوماتك.'
            ]
        ];

        $dataCollectionDetails = [
            'المعلومات المطلوبة' => [
                'الاسم الكامل',
                'رقم الهاتف',
                'البريد الإلكتروني',
                'الموقع الجغرافي'
            ],
            'المعلومات التي لا نجمعها' => [
                'معلومات التواصل الاجتماعي',
                'تفاصيل شخصية غير ضرورية',
                'معلومات الدفع',
                'بيانات التتبع الشخصية'
            ]
        ];

        return view('home.privacy', [
            'pageTitle' => 'سياسة الخصوصية',
            'privacyPolicySections' => $privacyPolicySections,
            'dataCollectionDetails' => $dataCollectionDetails,
            'supportedCountries' => $this->locationService->getSupportedCountries()
        ]);
    }

    /**
     * Show the terms of service page
     */
    public function termsOfService()
    {
        $termsSections = [
            [
                'title' => 'نطاق الخدمة',
                'content' => 'أورا هي منصة للتواصل المحلي تركز حصريًا على المراسلة والمكالمات الصوتية في السودان والمملكة العربية السعودية. نحن نقدم خدمات التواصل الآمنة والموثوقة.'
            ],
            [
                'title' => 'شروط الاستخدام',
                'content' => 'يجب أن يكون المستخدمون من السودان أو المملكة العربية السعودية. يلتزم المستخدمون باستخدام التطبيق بشكل أخلاقي وقانوني، مع احترام خصوصية الآخرين.'
            ],
            [
                'title' => 'المسؤولية القانونية',
                'content' => 'نحن غير مسؤولين عن أي محتوى يتم تبادله بين المستخدمين. كل مستخدم مسؤول عن محتوى رسائله والتزامه بالقوانين المحلية.'
            ],
            [
                'title' => 'الأمان والخصوصية',
                'content' => 'نلتزم بحماية بيانات المستخدمين من خلال التشفير من طرف إلى طرف والتحقق الثنائي. نحافظ على سرية المعلومات ولا نشارك البيانات مع أطراف خارجية.'
            ]
        ];

        $userRights = [
            'الحق في الخصوصية' => [
                'حماية البيانات الشخصية',
                'التحكم في المعلومات الشخصية',
                'حذف الحساب في أي وقت'
            ],
            'التزامات المستخدم' => [
                'استخدام التطبيق بشكل قانوني',
                'احترام خصوصية المستخدمين الآخرين',
                'عدم نشر محتوى مسيء أو غير قانوني'
            ]
        ];

        return view('home.terms', [
            'pageTitle' => 'شروط الخدمة',
            'termsSections' => $termsSections,
            'userRights' => $userRights,
            'supportedCountries' => $this->locationService->getSupportedCountries()
        ]);
    }

    /**
     * Show the cookies policy page
     */
    public function cookiesPolicy()
    {
        $cookiesSections = [
            [
                'title' => 'ما هي الكوكيز؟',
                'content' => 'الكوكيز هي ملفات صغيرة يتم تخزينها على جهازك لتحسين تجربة المستخدم وتوفير وظائف أساسية في تطبيق أورا.'
            ],
            [
                'title' => 'أنواع الكوكيز التي نستخدمها',
                'content' => 'نستخدم كوكيز أساسية للحفاظ على أمان حسابك وتسجيل الدخول، وكوكيز أداء لتحليل استخدام التطبيق وتحسين تجربتك.'
            ],
            [
                'title' => 'إدارة الكوكيز',
                'content' => 'يمكنك التحكم في إعدادات الكوكيز من خلال متصفحك. ومع ذلك، قد يؤثر تعطيل الكوكيز على بعض وظائف التطبيق.'
            ],
            [
                'title' => 'سياسة الخصوصية',
                'content' => 'نحترم خصوصيتك ونلتزم بعدم مشاركة أي معلومات شخصية مستمدة من الكوكيز مع أطراف خارجية.'
            ]
        ];

        $cookiesTypes = [
            'كوكيز أساسية' => [
                'تأمين تسجيل الدخول',
                'الحفاظ على حالة الجلسة',
                'إدارة الإعدادات الشخصية'
            ],
            'كوكيز الأداء' => [
                'تحليل استخدام التطبيق',
                'تحسين تجربة المستخدم',
                'قياس الأداء والتفاعل'
            ]
        ];

        return view('home.cookies', [
            'pageTitle' => 'سياسة الكوكيز',
            'cookiesSections' => $cookiesSections,
            'cookiesTypes' => $cookiesTypes,
            'supportedCountries' => $this->locationService->getSupportedCountries()
        ]);
    }

    /**
     * Show the FAQ page
     */
    public function faq()
    {
        $faqCategories = [
            [
                'title' => 'المراسلة والتواصل',
                'questions' => [
                    [
                        'question' => 'هل يمكنني إرسال الصور في المحادثات؟',
                        'answer' => 'نعم، يدعم أورا مشاركة الصور بسهولة في المحادثات الفردية والجماعية.'
                    ],
                    [
                        'question' => 'هل يمكنني حذف الرسائل؟',
                        'answer' => 'بالتأكيد، يمكنك حذف الرسائل في أي وقت للمحادثات الفردية والجماعية.'
                    ]
                ]
            ],
            [
                'title' => 'المكالمات الصوتية',
                'questions' => [
                    [
                        'question' => 'هل المكالمات الصوتية مجانية؟',
                        'answer' => 'نعم، جميع المكالمات الصوتية داخل التطبيق مجانية تمامًا.'
                    ],
                    [
                        'question' => 'كيف أجري مكالمة صوتية؟',
                        'answer' => 'اختر المستخدم من قائمة جهات الاتصال واضغط على أيقونة المكالمة الصوتية.'
                    ]
                ]
            ],
            [
                'title' => 'الأمان والخصوصية',
                'questions' => [
                    [
                        'question' => 'هل بياناتي آمنة؟',
                        'answer' => 'نعم، نستخدم التشفير من طرف إلى طرف لحماية جميع رسائلك ومكالماتك.'
                    ],
                    [
                        'question' => 'من يمكنه استخدام أورا؟',
                        'answer' => 'حاليًا، التطبيق مخصص للمستخدمين في السودان والمملكة العربية السعودية.'
                    ]
                ]
            ]
        ];

        $supportedFeatures = [
            'المراسلة الفردية' => 'محادثات خاصة مع مستخدم واحد',
            'المراسلة الجماعية' => 'إنشاء مجموعات للتواصل',
            'مشاركة الصور' => 'إرسال وتبادل الصور بسهولة',
            'المكالمات الصوتية' => 'مكالمات صوتية مجانية',
            'الأمان' => 'تشفير من طرف إلى طرف'
        ];

        return view('home.faq', [
            'pageTitle' => 'الأسئلة الشائعة',
            'faqCategories' => $faqCategories,
            'supportedFeatures' => $supportedFeatures,
            'supportedCountries' => $this->locationService->getSupportedCountries()
        ]);
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        return view('home.index', [
            'user' => $user,
            'recentContacts' => $user->recentContacts(),
            'unreadMessages' => $user->unreadMessages(),
            'pendingCalls' => $user->pendingCalls()
        ]);
    }
}
