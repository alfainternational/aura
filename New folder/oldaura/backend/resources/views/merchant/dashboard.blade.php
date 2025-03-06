@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('لوحة تحكم التاجر') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        {{ __('مرحباً بك في لوحة تحكم التاجر!') }}
                    </div>

                    @if(isset($profile) && $profile->verification_status != 'verified')
                        <div class="alert alert-warning">
                            {{ __('حسابك قيد المراجعة من قبل إدارة النظام. سيتم تفعيل الحساب بشكل كامل بعد التحقق من البيانات.') }}
                        </div>
                    @endif

                    <div class="mt-4">
                        <h5>{{ __('معلومات المتجر') }}</h5>
                        <p><strong>{{ __('اسم المتجر:') }}</strong> {{ $profile->business_name ?? 'غير محدد' }}</p>
                        <p><strong>{{ __('نوع النشاط:') }}</strong> {{ $profile->business_type ?? 'غير محدد' }}</p>
                        <p><strong>{{ __('رقم الهاتف:') }}</strong> {{ $profile->business_phone ?? 'غير محدد' }}</p>
                        <p><strong>{{ __('البريد الإلكتروني:') }}</strong> {{ $profile->business_email ?? 'غير محدد' }}</p>
                        <p><strong>{{ __('العنوان:') }}</strong> {{ $profile->business_address ?? 'غير محدد' }}</p>
                    </div>

                    <div class="mt-4">
                        <h5>{{ __('الإحصائيات') }}</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card bg-primary text-white text-center p-3">
                                    <h3>0</h3>
                                    <p class="mb-0">{{ __('الطلبات الجديدة') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-success text-white text-center p-3">
                                    <h3>0</h3>
                                    <p class="mb-0">{{ __('الطلبات المكتملة') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-info text-white text-center p-3">
                                    <h3>0</h3>
                                    <p class="mb-0">{{ __('الإيرادات') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>{{ __('الإجراءات السريعة') }}</h5>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action">{{ __('إدارة المنتجات') }}</a>
                            <a href="#" class="list-group-item list-group-item-action">{{ __('إدارة الطلبات') }}</a>
                            <a href="{{ route('merchant.store-settings') }}" class="list-group-item list-group-item-action">{{ __('إعدادات المتجر') }}</a>
                            <a href="#" class="list-group-item list-group-item-action">{{ __('التقارير والإحصائيات') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
