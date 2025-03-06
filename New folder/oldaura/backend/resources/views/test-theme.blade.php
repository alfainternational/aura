@extends('layouts.app-new')

@section('title', 'اختبار الألوان والقوالب - AURA')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-center mb-5">
            <h1 class="display-4 fw-bold">اختبار نظام الألوان والقوالب</h1>
            <p class="lead">هذه الصفحة مخصصة لاختبار نظام الألوان والوضع الليلي/النهاري في منصة AURA</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title">تعليمات الاستخدام</h2>
                    <p class="card-text">
                        يمكنك تغيير وضع العرض (الليلي/النهاري) باستخدام الزر في أعلى الصفحة. كما يمكنك اختيار لون القالب من خلال أيقونة الألوان في أسفل يسار الشاشة.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-primary">عناصر التصميم</h3>
                    <p class="card-text">فيما يلي مجموعة من عناصر التصميم الأساسية للتحقق من توافقها مع الوضع الليلي/النهاري:</p>
                    
                    <h5 class="mt-4">الألوان الأساسية</h5>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <div class="p-3 bg-primary text-white rounded">أساسي</div>
                        <div class="p-3 bg-secondary text-white rounded">ثانوي</div>
                        <div class="p-3 bg-success text-white rounded">نجاح</div>
                        <div class="p-3 bg-danger text-white rounded">خطأ</div>
                        <div class="p-3 bg-warning text-dark rounded">تحذير</div>
                        <div class="p-3 bg-info text-white rounded">معلومات</div>
                    </div>
                    
                    <h5 class="mt-4">النصوص</h5>
                    <p class="text-primary">نص بلون أساسي</p>
                    <p class="text-secondary">نص بلون ثانوي</p>
                    <p class="text-success">نص بلون النجاح</p>
                    <p class="text-danger">نص بلون الخطأ</p>
                    <p class="text-warning">نص بلون التحذير</p>
                    <p class="text-info">نص بلون المعلومات</p>
                    <p class="text-muted">نص بلون محايد</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-primary">أزرار وعناصر تفاعلية</h3>
                    <p class="card-text">فيما يلي مجموعة من الأزرار والعناصر التفاعلية للتحقق من توافقها مع الوضع الليلي/النهاري:</p>
                    
                    <h5 class="mt-4">أزرار أساسية</h5>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button class="btn btn-primary">أساسي</button>
                        <button class="btn btn-secondary">ثانوي</button>
                        <button class="btn btn-success">نجاح</button>
                        <button class="btn btn-danger">خطأ</button>
                        <button class="btn btn-warning">تحذير</button>
                        <button class="btn btn-info">معلومات</button>
                    </div>
                    
                    <h5 class="mt-4">أزرار مخططة</h5>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <button class="btn btn-outline-primary">أساسي</button>
                        <button class="btn btn-outline-secondary">ثانوي</button>
                        <button class="btn btn-outline-success">نجاح</button>
                        <button class="btn btn-outline-danger">خطأ</button>
                        <button class="btn btn-outline-warning">تحذير</button>
                        <button class="btn btn-outline-info">معلومات</button>
                    </div>
                    
                    <h5 class="mt-4">عناصر أخرى</h5>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="switchExample">
                        <label class="form-check-label" for="switchExample">مفتاح تبديل</label>
                    </div>
                    
                    <div class="progress mb-3">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75%</div>
                    </div>
                    
                    <div class="alert alert-primary" role="alert">
                        هذا تنبيه باللون الأساسي للتطبيق.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
