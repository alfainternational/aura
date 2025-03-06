@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <div class="product-gallery">
                <img src="{{ $product->image_url }}" class="img-fluid rounded shadow-sm main-image" alt="{{ $product->name }}">
                @if($product->additional_images)
                    <div class="thumbnail-gallery mt-3 d-flex">
                        @foreach($product->additional_images as $image)
                            <img src="{{ $image }}" class="img-thumbnail me-2" style="width: 80px; height: 80px; object-fit: cover;">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="product-details">
                <h1 class="fw-bold mb-3">{{ $product->name }}</h1>
                <div class="d-flex align-items-center mb-3">
                    <div class="text-warning me-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $product->average_rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                    </div>
                    <small class="text-muted">({{ $product->reviews_count }} تقييم)</small>
                </div>
                <div class="price-section mb-3">
                    <h3 class="text-primary fw-bold">{{ $product->price }} ج.س</h3>
                    @if($product->discount_price)
                        <small class="text-muted text-decoration-line-through me-2">{{ $product->original_price }} ج.س</small>
                        <span class="badge bg-success">خصم {{ $product->discount_percentage }}%</span>
                    @endif
                </div>
                <p class="text-muted mb-4">{{ $product->description }}</p>

                <div class="product-meta mb-4">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>الفئة:</strong> {{ $product->category->name }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>الحالة:</strong> 
                            <span class="{{ $product->stock > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $product->stock > 0 ? 'متوفر' : 'غير متوفر' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="purchase-actions">
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="input-group mb-3">
                            <button class="btn btn-outline-secondary" type="button" id="decreaseQuantity">-</button>
                            <input type="number" class="form-control text-center" name="quantity" value="1" min="1" max="{{ $product->stock }}" id="productQuantity">
                            <button class="btn btn-outline-secondary" type="button" id="increaseQuantity">+</button>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" {{ $product->stock == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-shopping-cart me-2"></i> 
                            {{ $product->stock > 0 ? 'أضف إلى السلة' : 'نفد من المخزون' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">التفاصيل</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">التقييمات ({{ $product->reviews_count }})</button>
                </li>
            </ul>
            <div class="tab-content" id="productTabsContent">
                <div class="tab-pane fade show active" id="details" role="tabpanel">
                    <div class="card border-top-0">
                        <div class="card-body">
                            {!! $product->full_description !!}
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="card border-top-0">
                        <div class="card-body">
                            @forelse($product->reviews as $review)
                                <div class="review mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="fw-bold mb-0">{{ $review->user->name }}</h6>
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-muted">{{ $review->comment }}</p>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-comment-slash fa-3x mb-3"></i>
                                    <p>لا توجد تقييمات حتى الآن</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('productQuantity');
    const decreaseBtn = document.getElementById('decreaseQuantity');
    const increaseBtn = document.getElementById('increaseQuantity');

    decreaseBtn.addEventListener('click', function() {
        if (quantityInput.value > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    });

    increaseBtn.addEventListener('click', function() {
        if (quantityInput.value < {{ $product->stock }}) {
            quantityInput.value = parseInt(quantityInput.value) + 1;
        }
    });
});
</script>
@endpush
@endsection
