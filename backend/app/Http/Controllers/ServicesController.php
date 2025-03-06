<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServicesController extends Controller
{
    /**
     * Display the main services index page
     */
    public function index()
    {
        // Existing services from the view
        $services = [
            [
                'name' => 'المحفظة الإلكترونية',
                'description' => 'محفظة رقمية متكاملة لإدارة أموالك وإجراء المدفوعات بكل سهولة وأمان. حول الأموال، ادفع الفواتير، واستثمر - كل ذلك في مكان واحد.',
                'route' => 'services.wallet',
                'icon' => 'wallet',
                'color' => 'primary'
            ],
            [
                'name' => 'التجارة الإلكترونية',
                'description' => 'منصة تسوق متكاملة تتيح لك شراء وبيع المنتجات بكل سهولة. استفد من العروض الحصرية وأنشئ متجرك الخاص مع حلول دفع آمنة.',
                'route' => 'services.commerce',
                'icon' => 'shopping-cart',
                'color' => 'success'
            ],
            [
                'name' => 'المراسلة والاتصالات',
                'description' => 'منصة تواصل اجتماعي متكاملة مع ميزات محادثة آمنة ومكالمات صوتية ومرئية عالية الجودة. ابق على تواصل مع أصدقائك وعائلتك.',
                'route' => 'services.messaging',
                'icon' => 'comments',
                'color' => 'info'
            ],
            [
                'name' => 'خدمات التوصيل',
                'description' => 'خدمات توصيل سريعة وموثوقة لتوصيل طلباتك من المطاعم والمتاجر المختلفة. تتبع طلباتك في الوقت الفعلي مع تقديرات دقيقة لوقت الوصول.',
                'route' => 'services.delivery',
                'icon' => 'truck',
                'color' => 'warning'
            ],
            [
                'name' => 'المساعد الذكي',
                'description' => 'مساعد شخصي مدعوم بالذكاء الاصطناعي لمساعدتك في إدارة مهامك اليومية وتقديم توصيات شخصية تناسب احتياجاتك واهتماماتك.',
                'route' => 'services.ai-assistant',
                'icon' => 'robot',
                'color' => 'danger'
            ],
            [
                'name' => 'نظام الوكلاء',
                'description' => 'كن وكيلاً معتمداً لخدمات AURA وابدأ بتحقيق دخل إضافي من خلال تقديم خدماتنا للآخرين. استفد من نظام عمولات مجزية وتدريب مستمر.',
                'route' => 'services.agents',
                'icon' => 'user-tie',
                'color' => 'secondary'
            ]
        ];

        return view('services.index', compact('services'));
    }
}
