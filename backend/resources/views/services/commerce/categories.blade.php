@extends('layouts.app')

@section('title', 'فئات المنتجات')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="fw-bold mb-3">فئات المنتجات</h1>
            <p class="lead text-muted">استكشف الفئات المختلفة للمنتجات المتاحة</p>
        </div>
    </div>

    <div class="row">
        @foreach($categories as $category)
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title fw-bold mb-0">{{ $category->name }}</h4>
                            <span class="badge bg-primary">{{ $category->products_count }} منتج</span>
                        </div>
                        <p class="card-text text-muted">{{ Str::limit($category->description, 100) }}</p>
                        <a href="{{ route('services.commerce.products', ['category' => $category->id]) }}" class="btn btn-outline-primary">عرض المنتجات</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $categories->links() }}
    </div>
</div>
@endsection
