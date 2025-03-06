@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('لوحة تحكم الوكيل') }}</div>

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
                        {{ __('مرحباً بك في لوحة تحكم الوكيل!') }}
                    </div>

                    @if(isset($profile) && $profile->verification_status != 'verified')
                        <div class="alert alert-warning">
                            {{ __('حسابك قيد المراجعة من قبل إدارة النظام. سيتم تفعيل الحساب بشكل كامل بعد التحقق من البيانات.') }}
                        </div>
                    @endif

                    <div class="mt-4">
                        <h5>{{ __('معلومات الوكالة') }}</h5>
                        <p><strong>{{ __('نوع الوكالة:') }}</strong> {{ $profile->agent_type ?? 'غير محدد' }}</p>
                        <p><strong>{{ __('منطقة العمل:') }}</strong> {{ $profile->area_of_operation ?? 'غير محدد' }}</p>
                        <p><strong>{{ __('رقم الهوية:') }}</strong> {{ $profile->identification_number ?? 'غير محدد' }}</p>
                        <p><strong>{{ __('الحالة:') }}</strong> 
                            @if(isset($profile))
                                @if($profile->is_online)
                                    <span class="badge bg-success">{{ __('متصل') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('غير متصل') }}</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">{{ __('غير متصل') }}</span>
                            @endif
                        </p>
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
                            <a href="#" class="list-group-item list-group-item-action">{{ __('عرض الطلبات الحالية') }}</a>
                            <a href="#" class="list-group-item list-group-item-action">{{ __('مراجعة الطلبات السابقة') }}</a>
                            <a href="{{ route('agent.settings') }}" class="list-group-item list-group-item-action">{{ __('الإعدادات') }}</a>
                            <a href="{{ route('agent.status') }}" class="list-group-item list-group-item-action">{{ __('تغيير الحالة') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
