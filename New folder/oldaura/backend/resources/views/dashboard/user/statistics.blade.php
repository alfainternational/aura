@extends('layouts.app')

@section('title', 'الإحصائيات والمؤشرات')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2">الإحصائيات والمؤشرات</h1>
            <p class="text-muted">مؤشرات وإحصائيات حسابك لمساعدتك على متابعة نشاطاتك</p>
        </div>
    </div>

    <!-- مؤشرات عامة -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <x-card class="border-0 shadow-sm h-100">
                <div class="d-flex flex-column align-items-center">
                    <div class="mb-3">
                        <div class="chart-circle" data-value="0" data-thickness="10" data-color="primary">
                            <div class="chart-circle-value">0%</div>
                        </div>
                    </div>
                    <h6 class="mb-1">اكتمال الملف الشخصي</h6>
                    <p class="text-muted small mb-0">أكمل ملفك الشخصي</p>
                </div>
            </x-card>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <x-card class="border-0 shadow-sm h-100">
                <div class="text-center">
                    <h3 class="mb-1">0</h3>
                    <h6 class="text-muted mb-1">عدد الزيارات</h6>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: 30%"></div>
                    </div>
                    <p class="text-muted small mt-2 mb-0">زيادة بنسبة 0% عن الشهر الماضي</p>
                </div>
            </x-card>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <x-card class="border-0 shadow-sm h-100">
                <div class="text-center">
                    <h3 class="mb-1">0</h3>
                    <h6 class="text-muted mb-1">المشاركات</h6>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-warning" style="width: 40%"></div>
                    </div>
                    <p class="text-muted small mt-2 mb-0">زيادة بنسبة 0% عن الشهر الماضي</p>
                </div>
            </x-card>
        </div>
        <div class="col-md-3">
            <x-card class="border-0 shadow-sm h-100">
                <div class="text-center">
                    <h3 class="mb-1">0</h3>
                    <h6 class="text-muted mb-1">المتابعون</h6>
                    <div class="progress" style="height: 4px;">
                        <div class="progress-bar bg-danger" style="width: 60%"></div>
                    </div>
                    <p class="text-muted small mt-2 mb-0">زيادة بنسبة 0% عن الشهر الماضي</p>
                </div>
            </x-card>
        </div>
    </div>

    <!-- الرسم البياني الرئيسي -->
    <div class="row mb-4">
        <div class="col-12">
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">نشاط الحساب</h5>
                        <div>
                            <select class="form-select form-select-sm">
                                <option value="30">آخر 30 يوم</option>
                                <option value="60">آخر 60 يوم</option>
                                <option value="90">آخر 90 يوم</option>
                                <option value="all">كل الفترة</option>
                            </select>
                        </div>
                    </div>
                </x-slot>
                
                <div id="activity-chart" style="height: 300px;">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="text-center">
                            <i class="bi bi-bar-chart fs-3 d-block mb-2 text-muted"></i>
                            <p class="text-muted">لا توجد بيانات كافية لعرض الرسم البياني</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
    
    <!-- إحصائيات إضافية -->
    <div class="row">
        <div class="col-md-6 mb-4 mb-md-0">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">التفاعلات حسب النوع</h5>
                </x-slot>
                
                <div id="interactions-chart" style="height: 250px;">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="text-center">
                            <i class="bi bi-pie-chart fs-3 d-block mb-2 text-muted"></i>
                            <p class="text-muted">لا توجد بيانات كافية لعرض الرسم البياني</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
        
        <div class="col-md-6">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">أوقات النشاط</h5>
                </x-slot>
                
                <div id="activity-time-chart" style="height: 250px;">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="text-center">
                            <i class="bi bi-clock fs-3 d-block mb-2 text-muted"></i>
                            <p class="text-muted">لا توجد بيانات كافية لعرض الرسم البياني</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // يمكن إضافة مكتبات الرسوم البيانية مثل Chart.js أو ApexCharts هنا
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة الرسوم البيانية عندما تتوفر البيانات
    });
</script>
@endpush
@endsection
