@extends('layouts.app')

@section('title', 'التجارة الإلكترونية')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2 text-center mb-5">
            <h1 class="fw-bold mb-4">منصة التجارة الإلكترونية</h1>
            <p class="lead">استكشف عالم التسوق الرقمي مع AURA - منصة متكاملة للبيع والشراء بكل سهولة وأمان</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="service-icon bg-success text-white mb-3 rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <h3 class="fw-bold mb-3">المتاجر</h3>
                    <p>استعرض مجموعة واسعة من المتاجر والمنتجات المتاحة على منصتنا</p>
                    <a href="{{ route('services.commerce.products') }}" class="btn btn-outline-success mt-3">تصفح المنتجات</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="service-icon bg-primary text-white mb-3 rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-tags fa-2x"></i>
                    </div>
                    <h3 class="fw-bold mb-3">الفئات</h3>
                    <p>اكتشف مختلف فئات المنتجات والعثور على ما يناسب احتياجاتك</p>
                    <a href="{{ route('services.commerce.categories') }}" class="btn btn-outline-primary mt-3">استعراض الفئات</a>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="service-icon bg-info text-white mb-3 rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-receipt fa-2x"></i>
                    </div>
                    <h3 class="fw-bold mb-3">طلباتي</h3>
                    <p>تتبع وإدارة جميع طلباتك السابقة والحالية بسهولة</p>
                    <a href="{{ route('services.commerce.orders') }}" class="btn btn-outline-info mt-3">عرض الطلبات</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <div class="alert alert-light border-0 shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-shield-alt text-success me-3 fa-2x"></i>
                    <div>
                        <h4 class="alert-heading fw-bold">تسوق بأمان</h4>
                        <p class="mb-0">نضمن لك تجربة تسوق آمنة وموثوقة مع حماية كاملة للمعلومات والمدفوعات</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
