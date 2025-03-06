<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ترجمات التجارة الإلكترونية
    |--------------------------------------------------------------------------
    |
    | الترجمات المتعلقة بصفحات وميزات التجارة الإلكترونية
    |
    */

    'order_status' => [
        'pending' => 'قيد الانتظار',
        'processing' => 'قيد المعالجة',
        'confirmed' => 'تم التأكيد',
        'shipped' => 'تم الشحن',
        'delivered' => 'تم التوصيل',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
        'refunded' => 'مسترجع',
        'failed' => 'فشل',
    ],
    
    'payment_status' => [
        'pending' => 'قيد الانتظار',
        'processing' => 'قيد المعالجة',
        'completed' => 'مكتمل',
        'failed' => 'فشل',
        'cancelled' => 'ملغي',
        'refunded' => 'مسترجع',
        'partially_refunded' => 'مسترجع جزئياً',
    ],
    
    'payment_methods' => [
        'stripe' => 'بطاقة ائتمان (Stripe)',
        'paypal' => 'PayPal',
        'myfatoorah' => 'My Fatoorah',
        'cod' => 'الدفع عند الاستلام',
        'credit_card' => 'بطاقة ائتمان',
        'mada' => 'مدى',
        'apple_pay' => 'Apple Pay',
        'bank_transfer' => 'تحويل بنكي',
    ],
    
    'shipping_methods' => [
        'standard' => 'توصيل قياسي',
        'express' => 'توصيل سريع',
        'same_day' => 'توصيل في نفس اليوم',
    ],
    
    'product_categories' => [
        'electronics' => 'إلكترونيات',
        'clothing' => 'ملابس',
        'home' => 'المنزل والأثاث',
        'beauty' => 'الجمال والعناية الشخصية',
        'sports' => 'رياضة',
        'books' => 'كتب',
        'toys' => 'ألعاب',
        'jewelry' => 'مجوهرات',
        'groceries' => 'بقالة',
        'other' => 'أخرى',
    ],
    
    'validation' => [
        'coupon' => [
            'invalid' => 'كود الخصم غير صالح',
            'expired' => 'انتهت صلاحية كود الخصم',
            'used' => 'لقد استخدمت هذا الكود بالفعل',
            'minimum_order' => 'الحد الأدنى للطلب لاستخدام هذا الكود هو :amount',
            'not_applicable' => 'لا يمكن تطبيق الكود على هذا الطلب',
        ],
    ],
    
    'installments' => [
        'title' => 'الدفع بالتقسيط',
        'monthly_payment' => 'القسط الشهري',
        'interest_rate' => 'معدل الفائدة',
        'total_amount' => 'المبلغ الإجمالي',
        'duration' => 'المدة',
        'months' => 'شهر | أشهر',
    ],
    
    'notifications' => [
        'order_confirmed' => 'تم تأكيد طلبك رقم #:id',
        'order_shipped' => 'تم شحن طلبك رقم #:id',
        'order_delivered' => 'تم توصيل طلبك رقم #:id',
        'payment_received' => 'تم استلام الدفع لطلبك رقم #:id',
        'payment_failed' => 'فشل الدفع لطلبك رقم #:id',
    ],
    
    'cart' => [
        'empty' => 'سلة التسوق فارغة',
        'items_count' => 'عدد المنتجات: :count',
        'subtotal' => 'المجموع الفرعي',
        'shipping' => 'الشحن',
        'tax' => 'الضريبة',
        'discount' => 'الخصم',
        'total' => 'المجموع الكلي',
        'proceed_to_checkout' => 'متابعة الشراء',
        'apply_coupon' => 'تطبيق كود الخصم',
        'remove_coupon' => 'إزالة كود الخصم',
    ],
    
    'checkout' => [
        'title' => 'إتمام الطلب',
        'shipping_address' => 'عنوان الشحن',
        'billing_address' => 'عنوان الفوترة',
        'payment_method' => 'طريقة الدفع',
        'review_order' => 'مراجعة الطلب',
        'complete_order' => 'إتمام الطلب',
        'agree_terms' => 'أوافق على الشروط والأحكام',
    ],
];
