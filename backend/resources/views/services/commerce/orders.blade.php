@extends('layouts.app')

@section('title', 'طلباتي')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold mb-3">طلباتي</h1>
            <p class="lead text-muted">استعراض وتتبع جميع طلباتك السابقة والحالية</p>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-shopping-bag fa-3x mb-3"></i>
                    <h4 class="alert-heading fw-bold">لا توجد طلبات حتى الآن</h4>
                    <p>يبدو أنك لم تقم بأي عمليات شراء بعد. ابدأ بتصفح منتجاتنا!</p>
                    <a href="{{ route('services.commerce.products') }}" class="btn btn-primary mt-3">تصفح المنتجات</a>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($orders as $order)
                <div class="col-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold mb-1">رقم الطلب: #{{ $order->id }}</h5>
                                <small class="text-muted">{{ $order->created_at->format('Y-m-d H:i') }}</small>
                            </div>
                            <span class="badge 
                                @switch($order->status)
                                    @case('pending') bg-warning @break
                                    @case('processing') bg-info @break
                                    @case('shipped') bg-primary @break
                                    @case('delivered') bg-success @break
                                    @case('cancelled') bg-danger @break
                                @endswitch
                            ">
                                {{ __('orders.status.' . $order->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="fw-bold mb-3">المنتجات</h6>
                                    <div class="list-group">
                                        @foreach($order->products as $product)
                                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $product->image_url }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-1 fw-bold">{{ $product->name }}</h6>
                                                        <small class="text-muted">الكمية: {{ $product->pivot->quantity }}</small>
                                                    </div>
                                                </div>
                                                <span class="text-primary fw-bold">{{ $product->pivot->price }} ج.س</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3">ملخص الطلب</h6>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>المجموع الفرعي</span>
                                                <span>{{ $order->subtotal }} ج.س</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>الضريبة</span>
                                                <span>{{ $order->tax }} ج.س</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between fw-bold">
                                                <span>المجموع الكلي</span>
                                                <span>{{ $order->total }} ج.س</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between">
                            <a href="{{ route('services.commerce.orders.track', $order->id) }}" class="btn btn-sm btn-outline-primary">تتبع الطلب</a>
                            @if($order->status === 'pending')
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal{{ $order->id }}">إلغاء الطلب</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
