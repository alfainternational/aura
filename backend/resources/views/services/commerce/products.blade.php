@extends('layouts.app')

@section('title', 'منتجات التجارة الإلكترونية')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold mb-3">المنتجات المتاحة</h1>
            <p class="lead text-muted">استكشف مجموعتنا المتنوعة من المنتجات</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-3">فلترة المنتجات</h4>
                    <form>
                        <div class="mb-3">
                            <label class="form-label">الفئة</label>
                            <select class="form-select">
                                <option>جميع الفئات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">نطاق السعر</label>
                            <div class="d-flex">
                                <input type="number" class="form-control me-2" placeholder="من">
                                <input type="number" class="form-control" placeholder="إلى">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">تطبيق الفلترة</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="row">
                @foreach($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}">
                            <div class="card-body">
                                <h5 class="card-title fw-bold">{{ $product->name }}</h5>
                                <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0 text-primary">{{ $product->price }} ج.س</span>
                                    <a href="{{ route('product.details', $product->id) }}" class="btn btn-sm btn-outline-primary">التفاصيل</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
