@extends('admin.layouts.master')

@section('title', 'إدارة بوابات الدفع')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1><i class="fas fa-credit-card"></i> إدارة بوابات الدفع</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                <li class="breadcrumb-item active">بوابات الدفع</li>
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
        <div class="col-md-12">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-wallet"></i>
                        تفعيل بوابات الدفع
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.payment.gateways.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="callout callout-info">
                                    <h5><i class="fas fa-info"></i> ملاحظة:</h5>
                                    <p>قم بتحديد بوابات الدفع التي تريد تفعيلها في النظام، وحدد البوابة الافتراضية التي سيتم استخدامها.</p>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="default_gateway">بوابة الدفع الافتراضية</label>
                                    <select name="default_gateway" id="default_gateway" class="form-control @error('default_gateway') is-invalid @enderror">
                                        @foreach($gateways as $key => $gateway)
                                            <option value="{{ $key }}" @if($default_gateway == $key) selected @endif>
                                                {{ $gateway['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('default_gateway')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            @foreach($gateways as $key => $gateway)
                                <div class="col-md-6">
                                    <div class="card card-outline @if($gateway['active']) card-success @else card-secondary @endif mb-4">
                                        <div class="card-header">
                                            <h3 class="card-title">
                                                <i class="fas {{ $gateway['icon'] }}"></i>
                                                {{ $gateway['name'] }}
                                            </h3>
                                            <div class="card-tools">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input gateway-toggle" 
                                                        id="gateway_{{ $key }}" name="gateways[]" value="{{ $key }}"
                                                        @if($gateway['active']) checked @endif>
                                                    <label class="custom-control-label" for="gateway_{{ $key }}"></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p>{{ $gateway['description'] }}</p>
                                            
                                            @if($gateway['internal'])
                                                <span class="badge badge-success">بوابة داخلية</span>
                                            @else
                                                <span class="badge badge-warning">بوابة خارجية</span>
                                            @endif
                                            
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" 
                                                    data-target="#configModal{{ $key }}">
                                                    <i class="fas fa-cog"></i> إعدادات البوابة
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ التغييرات
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals para la configuración de cada pasarela -->
@foreach($gateways as $key => $gateway)
<div class="modal fade" id="configModal{{ $key }}" tabindex="-1" role="dialog" aria-labelledby="configModalLabel{{ $key }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.payment.gateways.config', $key) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-header">
                    <h5 class="modal-title" id="configModalLabel{{ $key }}">
                        <i class="fas {{ $gateway['icon'] }}"></i>
                        إعدادات {{ $gateway['name'] }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @switch($key)
                        @case('aura_wallet')
                            <div class="form-group">
                                <label for="transaction_fee_percentage">نسبة رسوم التحويل (%)</label>
                                <input type="number" min="0" max="100" step="0.01" 
                                    class="form-control @error('transaction_fee_percentage') is-invalid @enderror" 
                                    id="transaction_fee_percentage" name="transaction_fee_percentage" 
                                    value="{{ $gateway['config_items']['transaction_fee_percentage'] }}">
                                @error('transaction_fee_percentage')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">نسبة الرسوم التي سيتم خصمها من كل عملية دفع (0% يعني لا توجد رسوم)</small>
                            </div>
                            @break
                            
                        @case('cod')
                            <div class="form-group">
                                <label for="minimum_order_value">الحد الأدنى لقيمة الطلب</label>
                                <input type="number" min="0" step="0.01" 
                                    class="form-control @error('minimum_order_value') is-invalid @enderror" 
                                    id="minimum_order_value" name="minimum_order_value" 
                                    value="{{ $gateway['config_items']['minimum_order_value'] }}">
                                @error('minimum_order_value')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="maximum_order_value">الحد الأقصى لقيمة الطلب</label>
                                <input type="number" min="0" step="0.01" 
                                    class="form-control @error('maximum_order_value') is-invalid @enderror" 
                                    id="maximum_order_value" name="maximum_order_value" 
                                    value="{{ $gateway['config_items']['maximum_order_value'] }}">
                                @error('maximum_order_value')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            @break
                            
                        @case('paypal')
                            <div class="form-group">
                                <label for="client_id">Client ID</label>
                                <input type="text" class="form-control @error('client_id') is-invalid @enderror" 
                                    id="client_id" name="client_id" 
                                    value="{{ $gateway['config_items']['client_id'] }}">
                                @error('client_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="client_secret">Client Secret</label>
                                <input type="password" class="form-control @error('client_secret') is-invalid @enderror" 
                                    id="client_secret" name="client_secret" 
                                    value="{{ $gateway['config_items']['client_secret'] }}">
                                @error('client_secret')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="environment">البيئة</label>
                                <select name="environment" id="environment" class="form-control @error('environment') is-invalid @enderror">
                                    <option value="sandbox" @if($gateway['config_items']['environment'] == 'sandbox') selected @endif>
                                        Sandbox (اختبار)
                                    </option>
                                    <option value="production" @if($gateway['config_items']['environment'] == 'production') selected @endif>
                                        Production (إنتاج)
                                    </option>
                                </select>
                                @error('environment')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            @break
                            
                        @case('stripe')
                            <div class="form-group">
                                <label for="public_key">Public Key</label>
                                <input type="text" class="form-control @error('public_key') is-invalid @enderror" 
                                    id="public_key" name="public_key" 
                                    value="{{ $gateway['config_items']['public_key'] }}">
                                @error('public_key')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="secret_key">Secret Key</label>
                                <input type="password" class="form-control @error('secret_key') is-invalid @enderror" 
                                    id="secret_key" name="secret_key" 
                                    value="{{ $gateway['config_items']['secret_key'] }}">
                                @error('secret_key')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            @break
                            
                        @case('myfatoorah')
                            <div class="form-group">
                                <label for="api_key">API Key</label>
                                <input type="password" class="form-control @error('api_key') is-invalid @enderror" 
                                    id="api_key" name="api_key" 
                                    value="{{ $gateway['config_items']['api_key'] }}">
                                @error('api_key')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="base_url">API URL</label>
                                <input type="url" class="form-control @error('base_url') is-invalid @enderror" 
                                    id="base_url" name="base_url" 
                                    value="{{ $gateway['config_items']['base_url'] }}">
                                @error('base_url')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    استخدم https://apitest.myfatoorah.com للاختبار أو https://api.myfatoorah.com للإنتاج
                                </small>
                            </div>
                            @break
                            
                        @default
                            <div class="alert alert-info">
                                لا توجد إعدادات إضافية لهذه البوابة
                            </div>
                    @endswitch
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('styles')
<style>
    .gateway-toggle:checked + .custom-control-label::before {
        background-color: #28a745;
        border-color: #28a745;
    }
</style>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.gateway-toggle').change(function() {
            let cardElement = $(this).closest('.card');
            if ($(this).is(':checked')) {
                cardElement.removeClass('card-secondary').addClass('card-success');
            } else {
                cardElement.removeClass('card-success').addClass('card-secondary');
            }
        });
    });
</script>
@endsection
