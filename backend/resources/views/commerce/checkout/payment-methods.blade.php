@extends('layouts.app')

@section('title', 'طرق الدفع')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">اختر طريقة الدفع</h4>
                </div>
                <div class="card-body">
                    <div class="order-summary mb-4">
                        <h5>ملخص الطلب #{{ $order->id }}</h5>
                        <div class="d-flex justify-content-between">
                            <span>إجمالي المنتجات:</span>
                            <span>@currency($order->subtotal)</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="d-flex justify-content-between text-success">
                            <span>الخصم:</span>
                            <span>-@currency($order->discount)</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <span>رسوم التوصيل:</span>
                            <span>@currency($order->shipping_cost)</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>الضريبة:</span>
                            <span>@currency($order->tax)</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>المجموع الكلي:</span>
                            <span>@currency($order->total)</span>
                        </div>
                    </div>

                    <div class="alert alert-warning mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>ملاحظة مهمة:</strong> أورا لن تكون الضامن لحقوق البائع والمشتري في حال استخدام أي وسيلة سداد غير محفظة أورا. ننصح باستخدام محفظة أورا للحصول على حماية كاملة للمعاملة.
                    </div>

                    <form action="{{ route('commerce.payments.create', ['order_id' => $order->id]) }}" method="POST" id="payment-form">
                        @csrf
                        <div class="payment-methods">
                            <h5 class="mb-3">طرق الدفع المتاحة</h5>
                            
                            @forelse($availableGateways as $key => $gateway)
                                <div class="card mb-3 payment-method-card {{ $key == 'aura_wallet' ? 'border-primary' : '' }}">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input payment-gateway-input" 
                                                type="radio" 
                                                name="gateway" 
                                                id="gateway-{{ $key }}" 
                                                value="{{ $key }}" 
                                                {{ $key == 'aura_wallet' ? 'checked' : '' }}
                                                data-has-options="{{ !empty($gateway['options']) }}"
                                            >
                                            <label class="form-check-label d-flex justify-content-between w-100" for="gateway-{{ $key }}">
                                                <span>{{ $gateway['name'] }}</span>
                                                <div class="payment-icons">
                                                    @if($key == 'stripe')
                                                        <i class="fab fa-cc-visa"></i>
                                                        <i class="fab fa-cc-mastercard"></i>
                                                        <i class="fab fa-cc-amex"></i>
                                                    @elseif($key == 'paypal')
                                                        <i class="fab fa-paypal"></i>
                                                    @elseif($key == 'myfatoorah')
                                                        <img src="{{ asset('images/myfatoorah-logo.png') }}" alt="My Fatoorah" height="24">
                                                    @elseif($key == 'aura_wallet')
                                                        <i class="fas fa-wallet text-primary"></i>
                                                        <span class="badge bg-success ms-1">موصى به</span>
                                                    @elseif($key == 'cod')
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                        
                                        @if(!empty($gateway['options']))
                                            <div class="payment-options mt-3 {{ $key == 'aura_wallet' ? '' : 'd-none' }}" id="options-{{ $key }}">
                                                <div class="row">
                                                    @foreach($gateway['options'] as $optionKey => $optionLabel)
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-check">
                                                                <input 
                                                                    class="form-check-input" 
                                                                    type="radio" 
                                                                    name="payment_method" 
                                                                    id="method-{{ $key }}-{{ $optionKey }}" 
                                                                    value="{{ $optionKey }}"
                                                                    {{ $loop->first ? 'checked' : '' }}
                                                                >
                                                                <label class="form-check-label" for="method-{{ $key }}-{{ $optionKey }}">
                                                                    {{ $optionLabel }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($key == 'aura_wallet')
                                            <div class="alert alert-success mt-3" id="info-{{ $key }}">
                                                <i class="fas fa-shield-alt"></i>
                                                الدفع عبر محفظة أورا يوفر حماية كاملة للمشتري والبائع. 
                                                @if(isset($user_wallet) && $user_wallet)
                                                    <div class="mt-2">
                                                        <span class="fw-bold">رصيد المحفظة:</span> 
                                                        @currency($user_wallet->balance)
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        @if($key == 'cod')
                                            <div class="alert alert-warning mt-3 d-none" id="info-{{ $key }}">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                <strong>تنبيه:</strong> الدفع عند الاستلام - أورا لن تكون ضامنة لحقوق البائع أو المشتري عند استخدام هذه الطريقة. استخدم محفظة أورا للحصول على حماية كاملة.
                                            </div>
                                        @endif
                                        
                                        @if($gateway['supports_installments'])
                                            <div class="installments-option mt-3 {{ $key == 'aura_wallet' ? '' : 'd-none' }}" id="installments-{{ $key }}">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="use_installments" id="use-installments-{{ $key }}" value="1">
                                                    <label class="form-check-label" for="use-installments-{{ $key }}">
                                                        الدفع بالتقسيط 
                                                        <span class="badge bg-info">متوفر</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-warning">
                                    لا توجد طرق دفع متاحة حالياً. يرجى التواصل مع الدعم الفني.
                                </div>
                            @endforelse
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">المتابعة للدفع</button>
                            <a href="{{ route('commerce.checkout.index') }}" class="btn btn-outline-secondary">الرجوع للسلة</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // التبديل بين خيارات طرق الدفع
    const gatewayInputs = document.querySelectorAll('.payment-gateway-input');
    
    gatewayInputs.forEach(input => {
        input.addEventListener('change', function() {
            // إخفاء جميع خيارات الدفع
            document.querySelectorAll('.payment-options, .installments-option, .alert[id^="info-"]').forEach(el => {
                el.classList.add('d-none');
            });
            
            // إظهار خيارات طريقة الدفع المحددة
            const gatewayId = this.value;
            const optionsDiv = document.getElementById(`options-${gatewayId}`);
            const installmentsDiv = document.getElementById(`installments-${gatewayId}`);
            const infoDiv = document.getElementById(`info-${gatewayId}`);
            
            if (optionsDiv) {
                optionsDiv.classList.remove('d-none');
            }
            
            if (installmentsDiv) {
                installmentsDiv.classList.remove('d-none');
            }
            
            if (infoDiv) {
                infoDiv.classList.remove('d-none');
            }
        });
    });
});
</script>
@endsection

@section('styles')
<style>
.payment-method-card {
    border: 1px solid #ddd;
    transition: all 0.3s;
}

.payment-method-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.payment-method-card.border-primary {
    border-width: 2px;
    box-shadow: 0 0.25rem 0.75rem rgba(0, 123, 255, 0.2);
}

.form-check-input:checked ~ .form-check-label {
    font-weight: bold;
}

.payment-icons {
    font-size: 1.5rem;
    color: #666;
}

.payment-icons i, .payment-icons img {
    margin-left: 5px;
}

.order-summary {
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 5px;
}
</style>
@endsection
