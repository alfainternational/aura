@extends('layouts.admin')

@section('title', 'لوحة تحكم المسؤول')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title mb-4">لوحة تحكم المسؤول</h1>
                    <p>مرحبًا بك في لوحة تحكم المسؤول الخاصة بنظام Aura</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body">
                                    <h5>المستخدمين</h5>
                                    <h2 class="display-4">{{-- إجمالي المستخدمين --}}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white stretched-link">عرض التفاصيل</a>
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <h5>التجار</h5>
                                    <h2 class="display-4">{{-- إجمالي التجار --}}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white stretched-link">عرض التفاصيل</a>
                                    <i class="fas fa-store"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-warning text-white h-100">
                                <div class="card-body">
                                    <h5>المرسلين</h5>
                                    <h2 class="display-4">{{-- إجمالي المرسلين --}}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white stretched-link">عرض التفاصيل</a>
                                    <i class="fas fa-shipping-fast"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body">
                                    <h5>العملاء</h5>
                                    <h2 class="display-4">{{-- إجمالي العملاء --}}</h2>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <a href="#" class="text-white stretched-link">عرض التفاصيل</a>
                                    <i class="fas fa-users"></i>
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
