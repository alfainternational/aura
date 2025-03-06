@extends('layouts.admin')

@section('title', 'لوحة تحكم المشرف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title mb-4">لوحة تحكم المشرف</h1>
                    <p>مرحبًا بك في لوحة تحكم المشرف الخاصة بنظام Aura</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body">
                                    <h5>المناطق المسؤول عنها</h5>
                                    <h2 class="display-4">{{-- عدد المناطق --}}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white stretched-link">عرض التفاصيل</a>
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <h5>المرسلين</h5>
                                    <h2 class="display-4">{{-- عدد المرسلين --}}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white stretched-link">عرض التفاصيل</a>
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body">
                                    <h5>الطلبات النشطة</h5>
                                    <h2 class="display-4">{{-- عدد الطلبات النشطة --}}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white stretched-link">عرض التفاصيل</a>
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body">
                                    <h5>إحصائيات</h5>
                                    <h2 class="display-4">{{-- إحصائيات أخرى --}}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white stretched-link">عرض التفاصيل</a>
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
