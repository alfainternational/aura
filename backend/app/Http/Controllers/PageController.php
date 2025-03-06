<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show the app download/information page
     */
    public function app()
    {
        return view('pages.app', [
            'platforms' => [
                [
                    'name' => 'iOS',
                    'icon' => 'fab fa-apple',
                    'link' => '#',
                    'available' => true
                ],
                [
                    'name' => 'Android',
                    'icon' => 'fab fa-android',
                    'link' => '#',
                    'available' => true
                ],
                [
                    'name' => 'Web App',
                    'icon' => 'fas fa-globe',
                    'link' => route('welcome'),
                    'available' => true
                ]
            ],
            'features' => [
                'آمن وموثوق',
                'سهل الاستخدام',
                'متوافق مع الأجهزة المختلفة',
                'تحديثات مستمرة'
            ]
        ]);
    }

    /**
     * Show the about page
     */
    public function about()
    {
        return view('pages.about', [
            'mission' => 'توفير حلول رقمية متكاملة وآمنة للمستخدمين في السودان',
            'values' => [
                'الشفافية',
                'الابتكار',
                'الموثوقية',
                'خدمة العملاء'
            ],
            'team' => [
                [
                    'name' => 'محمد أحمد',
                    'role' => 'المؤسس والرئيس التنفيذي',
                    'image' => 'path/to/image'
                ]
                // Add more team members
            ]
        ]);
    }

    /**
     * Show the features page
     */
    public function features()
    {
        return view('pages.features', [
            'mainFeatures' => [
                [
                    'name' => 'المحفظة الإلكترونية',
                    'description' => 'إدارة الأموال بسهولة وأمان',
                    'icon' => 'wallet'
                ],
                [
                    'name' => 'التجارة الإلكترونية',
                    'description' => 'تسوق وبيع المنتجات بكل سهولة',
                    'icon' => 'shopping-cart'
                ],
                [
                    'name' => 'المراسلة والاتصالات',
                    'description' => 'تواصل آمن وسريع',
                    'icon' => 'comments'
                ]
            ]
        ]);
    }

    /**
     * Show the contact page
     */
    public function contact()
    {
        return view('pages.contact', [
            'contactMethods' => [
                [
                    'type' => 'هاتف',
                    'value' => '+249 123 456 789',
                    'icon' => 'phone'
                ],
                [
                    'type' => 'بريد إلكتروني',
                    'value' => 'support@aura.sd',
                    'icon' => 'envelope'
                ],
                [
                    'type' => 'العنوان',
                    'value' => 'الخرطوم، السودان',
                    'icon' => 'map-marker-alt'
                ]
            ]
        ]);
    }

    /**
     * Show the testimonials page
     */
    public function testimonials()
    {
        return view('pages.testimonials', [
            'testimonials' => [
                [
                    'name' => 'أحمد محمد',
                    'role' => 'تاجر',
                    'text' => 'AURA غيرت طريقة عملي تماماً، أصبحت أدير أعمالي بسهولة وأمان.',
                    'rating' => 5
                ],
                [
                    'name' => 'فاطمة عبدالله',
                    'role' => 'طالبة',
                    'text' => 'تطبيق رائع وسهل الاستخدام، ساعدني في إدارة مصروفاتي بشكل أفضل.',
                    'rating' => 4
                ]
                // Add more testimonials
            ]
        ]);
    }

    /**
     * Show the cookies policy page
     */
    public function cookies()
    {
        return view('pages.cookies', [
            'lastUpdated' => '2 مارس 2025',
            'contactInfo' => [
                'company' => 'Aura Technologies',
                'address' => 'الخرطوم، السودان',
                'email' => 'privacy@aura.com',
                'phone' => '+249 123 456789'
            ]
        ]);
    }
}
