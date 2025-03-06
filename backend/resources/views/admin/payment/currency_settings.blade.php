@extends('admin.layouts.master')

@section('title', 'إعدادات العملة والبلد')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-money-bill-wave"></i> إعدادات العملة والبلد</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.payment.gateways') }}">بوابات الدفع</a></li>
                <li class="breadcrumb-item active">إعدادات العملة</li>
            </ol>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-check"></i> نجاح!</h5>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-ban"></i> خطأ!</h5>
        {{ session('error') }}
    </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-globe"></i>
                        البلد الافتراضي
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.country.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="default_country">البلد الافتراضي</label>
                            <select name="default_country" id="default_country" class="form-control">
                                @foreach($countries as $country)
                                <option value="{{ $country->code }}" 
                                    {{ $country->is_default ? 'selected' : '' }}>
                                    {{ $country->name }} ({{ $country->code }})
                                </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                سيتم استخدام هذا البلد كإعداد افتراضي في التطبيق.
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ إعدادات البلد
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-money-bill-wave"></i>
                        العملة الافتراضية
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>العملة الافتراضية الحالية:</strong> @defaultCurrency
                        <br>
                        <strong>رمز العملة:</strong> @defaultCurrencySymbol
                    </div>
                    
                    <p>العملة الافتراضية يتم اختيارها تلقائيًا بناءً على البلد الافتراضي.</p>
                    
                    <form action="{{ route('admin.currency.update') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label>تحديث جميع البيانات المالية لاستخدام العملة الجديدة</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="update_all_data" name="update_all_data" value="1">
                                <label class="custom-control-label" for="update_all_data">تحديث جميع البيانات إلى العملة الافتراضية (قد يستغرق بعض الوقت)</label>
                            </div>
                            <small class="form-text text-muted">
                                هذا سيقوم بتحديث العملة في جميع الطلبات والمحافظ وبيانات المعاملات الموجودة.
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-sync"></i> تحديث بيانات العملة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i>
                        إعدادات عرض الأسعار
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">مثال على عرض الأسعار</h5>
                                    <div class="price-examples mt-3">
                                        <p><strong>سعر عادي:</strong> @currency(1000)</p>
                                        <p><strong>سعر مع خصم:</strong> <s>@currency(1000)</s> @currency(800)</p>
                                        <p><strong>سعر منخفض:</strong> @currency(10)</p>
                                        <p><strong>سعر مرتفع:</strong> @currency(99999)</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>ملاحظة:</strong>
                                تغيير العملة الافتراضية قد يتطلب تعديل أسعار المنتجات بما يتناسب مع العملة الجديدة. تأكد من إبلاغ التجار بهذا التغيير.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // عند تغيير البلد الافتراضي
    $('#default_country').change(function() {
        const selectedCountry = $(this).val();
        // يمكن إضافة أي منطق إضافي هنا إذا لزم الأمر
    });
    
    // عند تحديد تحديث جميع البيانات
    $('#update_all_data').change(function() {
        if($(this).is(':checked')) {
            if(!confirm('هذه العملية قد تستغرق وقتًا طويلًا حسب حجم البيانات. هل أنت متأكد من المتابعة؟')) {
                $(this).prop('checked', false);
            }
        }
    });
});
</script>
@endsection
