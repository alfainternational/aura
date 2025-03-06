@extends('layouts.app')

@section('title', 'تم تأكيد الطلب بنجاح')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <div class="success-animation">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                            </svg>
                        </div>
                    </div>
                    
                    <h2 class="mb-3">تم تأكيد طلبك بنجاح!</h2>
                    <p class="lead mb-4">شكراً لك على الطلب. تم استلام طلبك وسيتم معالجته قريباً.</p>
                    
                    <div class="order-details text-start bg-light p-4 rounded mb-4">
                        <h5 class="mb-3">تفاصيل الطلب</h5>
                        <div class="row mb-2">
                            <div class="col-6">رقم الطلب:</div>
                            <div class="col-6 fw-bold">{{ $order->id }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">تاريخ الطلب:</div>
                            <div class="col-6">{{ $order->created_at->format('Y-m-d h:i A') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">حالة الطلب:</div>
                            <div class="col-6">
                                <span class="badge bg-success">{{ __('commerce.order_status.' . $order->status) }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">طريقة الدفع:</div>
                            <div class="col-6">{{ $order->payment->payment_method ?? __('commerce.payment_methods.' . $order->payment->gateway) }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">المبلغ الإجمالي:</div>
                            <div class="col-6 fw-bold">{{ number_format($order->total, 2) }} {{ $order->currency }}</div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        تم إرسال تفاصيل الطلب إلى بريدك الإلكتروني {{ $order->user->email }}.
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('commerce.orders.show', ['id' => $order->id]) }}" class="btn btn-primary">
                            <i class="fas fa-receipt me-2"></i>
                            عرض تفاصيل الطلب
                        </a>
                        <a href="{{ route('commerce.products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-shopping-bag me-2"></i>
                            متابعة التسوق
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">منتجات قد تعجبك</h5>
                    <div class="recommended-products row row-cols-2 row-cols-md-4 g-3" id="recommendations-container">
                        <div class="col skeleton-loading">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="ratio ratio-1x1 skeleton"></div>
                                <div class="card-body">
                                    <div class="skeleton skeleton-text"></div>
                                    <div class="skeleton skeleton-text"></div>
                                    <div class="skeleton skeleton-price mt-2"></div>
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

@section('styles')
<style>
/* Success Animation */
.success-animation {
    margin: 20px auto;
    width: 80px;
    height: 80px;
}

.checkmark {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: block;
    stroke-width: 2;
    stroke: #4bb71b;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #4bb71b;
    animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
}

.checkmark__circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke-miterlimit: 10;
    stroke: #4bb71b;
    fill: none;
    animation: stroke .6s cubic-bezier(0.650, 0.000, 0.450, 1.000) forwards;
}

.checkmark__check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke .3s cubic-bezier(0.650, 0.000, 0.450, 1.000) .8s forwards;
}

@keyframes stroke {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes scale {
    0%, 100% {
        transform: none;
    }
    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}

@keyframes fill {
    100% {
        box-shadow: inset 0px 0px 0px 30px #4bb71b;
    }
}

/* Order details */
.order-details {
    border-right: 4px solid #28a745;
}

/* Skeleton Loading */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

.skeleton-text {
    height: 12px;
    margin-bottom: 8px;
    border-radius: 2px;
}

.skeleton-price {
    height: 20px;
    width: 60%;
    border-radius: 2px;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }
    100% {
        background-position: -200% 0;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get recommended products based on this order
    fetchRecommendations();
    
    function fetchRecommendations() {
        fetch(`{{ route('commerce.recommendations.similar', ['product_id' => $order->items->first()->product_id ?? 0]) }}?limit=4`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.products.length > 0) {
                    displayRecommendations(data.products);
                } else {
                    // If no specific recommendations, fetch trending products
                    return fetch(`{{ route('commerce.recommendations.trending') }}?limit=4`);
                }
            })
            .then(response => {
                if (response) return response.json();
            })
            .then(data => {
                if (data && data.success && data.products.length > 0) {
                    displayRecommendations(data.products);
                } else {
                    document.querySelector('#recommendations-container').innerHTML = 
                        '<div class="col-12 text-center py-3">لا توجد منتجات موصى بها حالياً</div>';
                }
            })
            .catch(error => {
                console.error('Error fetching recommendations:', error);
                document.querySelector('#recommendations-container').innerHTML = 
                    '<div class="col-12 text-center py-3">لا توجد منتجات موصى بها حالياً</div>';
            });
    }
    
    function displayRecommendations(products) {
        const container = document.querySelector('#recommendations-container');
        container.innerHTML = '';
        
        products.forEach(product => {
            const imageUrl = product.images && product.images.length > 0 
                ? product.images[0].url 
                : '{{ asset("images/placeholder.jpg") }}';
                
            container.innerHTML += `
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <a href="{{ route('commerce.products.show', ['slug' => '']) }}/${product.slug}">
                            <img src="${imageUrl}" class="card-img-top" alt="${product.name}">
                        </a>
                        <div class="card-body">
                            <h6 class="card-title mb-1">
                                <a href="{{ route('commerce.products.show', ['slug' => '']) }}/${product.slug}" class="text-decoration-none text-dark">
                                    ${product.name}
                                </a>
                            </h6>
                            <p class="card-text small text-muted">${product.category ? product.category.name : ''}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-bold text-primary">${product.price} ${product.currency || 'SAR'}</span>
                                <a href="{{ route('commerce.cart.add', ['id' => '']) }}/${product.id}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-cart-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
});
</script>
@endsection
