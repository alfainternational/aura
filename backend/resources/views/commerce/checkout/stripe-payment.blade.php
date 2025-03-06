@extends('layouts.app')

@section('title', 'الدفع عبر Stripe')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">إتمام الدفع الآمن</h4>
                </div>
                <div class="card-body">
                    <div class="order-summary mb-4">
                        <h5>ملخص الطلب #{{ $payment->order->id }}</h5>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>المبلغ الإجمالي:</span>
                            <span>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</span>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-shield-alt"></i>
                        جميع المعاملات آمنة ومشفرة. تفاصيل بطاقتك لا يتم تخزينها مطلقاً.
                    </div>

                    <div id="payment-form-container">
                        <h5 class="mb-3">أدخل تفاصيل البطاقة</h5>
                        <div id="payment-element"></div>
                        <div id="payment-message" class="d-none alert mt-3"></div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button id="submit-payment" class="btn btn-primary btn-lg">
                                <span id="button-text">إتمام الدفع</span>
                                <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                            <a href="{{ route('commerce.payments.methods', ['order_id' => $payment->order_id]) }}" class="btn btn-outline-secondary">تغيير طريقة الدفع</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
#payment-element {
    margin-bottom: 24px;
}

.order-summary {
    background-color: #f9f9f9;
    padding: 15px;
    border-radius: 5px;
}

/* تعديلات على طريقة عرض نموذج Stripe */
.StripeElement {
    box-sizing: border-box;
    height: 40px;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: white;
    box-shadow: 0 1px 3px 0 #e6ebf1;
    -webkit-transition: box-shadow 150ms ease;
    transition: box-shadow 150ms ease;
}

.StripeElement--focus {
    box-shadow: 0 1px 3px 0 #cfd7df;
}

.StripeElement--invalid {
    border-color: #fa755a;
}

.StripeElement--webkit-autofill {
    background-color: #fefde5 !important;
}
</style>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // إعداد Stripe
    const stripe = Stripe('{{ $publicKey }}');
    const clientSecret = '{{ $clientSecret }}';
    
    const options = {
        clientSecret: clientSecret,
        appearance: {
            theme: 'stripe',
            variables: {
                colorPrimary: '#0d6efd',
            },
            labels: 'floating'
        },
        locale: 'ar'
    };

    // إنشاء عناصر الدفع
    const elements = stripe.elements(options);
    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    // عناصر واجهة المستخدم
    const form = document.getElementById('payment-form-container');
    const submitButton = document.getElementById('submit-payment');
    const spinner = document.getElementById('spinner');
    const buttonText = document.getElementById('button-text');
    const paymentMessage = document.getElementById('payment-message');

    // معالجة تقديم النموذج
    submitButton.addEventListener('click', async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const {error} = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: '{{ route("commerce.payments.callback", ["gateway" => "stripe", "payment_id" => $payment->id]) }}',
                },
            });

            if (error) {
                showMessage(error.message, 'danger');
            }
        } catch (e) {
            showMessage('حدث خطأ أثناء معالجة الدفع. يرجى المحاولة مرة أخرى.', 'danger');
        }

        setLoading(false);
    });

    // وظائف مساعدة
    function setLoading(isLoading) {
        if (isLoading) {
            submitButton.disabled = true;
            spinner.classList.remove('d-none');
            buttonText.textContent = 'جارٍ المعالجة...';
        } else {
            submitButton.disabled = false;
            spinner.classList.add('d-none');
            buttonText.textContent = 'إتمام الدفع';
        }
    }

    function showMessage(messageText, type = 'info') {
        paymentMessage.classList.remove('d-none', 'alert-info', 'alert-danger', 'alert-success');
        paymentMessage.classList.add(`alert-${type}`);
        paymentMessage.textContent = messageText;
    }
});
</script>
@endsection
