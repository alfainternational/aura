@extends('layouts.dashboard')

@section('title', 'الملف الشخصي - تاجر')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <x-card class="border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center">
                    <div class="avatar-container me-3">
                        @if ($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="rounded-circle" width="100" height="100">
                        @else
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 100px; height: 100px;">
                                <span class="fs-1">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="user-info">
                        <div class="d-flex align-items-center mb-1">
                            <h3 class="mb-0 me-2">{{ $user->name }}</h3>
                            <span class="badge bg-success">تاجر {{ $stats['rating'] >= 4.5 ? 'مميز' : 'معتمد' }}</span>
                        </div>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                        <div class="d-flex align-items-center mb-1">
                            <div class="me-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($stats['rating']))
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @else
                                        <i class="bi bi-star text-warning"></i>
                                    @endif
                                @endfor
                            </div>
                            <span>{{ number_format($stats['rating'], 1) }} ({{ $user->ratings()->count() }} تقييم)</span>
                        </div>
                        <p class="mb-1">
                            <span class="badge bg-primary">تاجر</span>
                            @if($user->is_verified)
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> موثق</span>
                            @endif
                            @if($user->privacySettings && $user->privacySettings->online_status_visibility !== 'nobody')
                                <span class="badge bg-info"><i class="bi bi-circle-fill text-success me-1 small"></i> متصل</span>
                            @endif
                        </p>
                    </div>
                    <div class="ms-auto">
                        <a href="{{ route('profile.update') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i> تعديل الملف الشخصي
                        </a>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">معلومات الاتصال</h5>
                </x-slot>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                        <div>
                            <p class="mb-0 text-muted">البريد الإلكتروني</p>
                            <p class="mb-0 fw-medium">{{ $user->email }}</p>
                        </div>
                        <span class="badge bg-success rounded-pill">موثق</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                        <div>
                            <p class="mb-0 text-muted">رقم الهاتف</p>
                            <p class="mb-0 fw-medium">{{ $user->phone ?? 'غير محدد' }}</p>
                        </div>
                        @if($user->phone)
                            <span class="badge bg-success rounded-pill">موثق</span>
                        @else
                            <span class="badge bg-secondary rounded-pill">غير محدد</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                        <div>
                            <p class="mb-0 text-muted">عنوان المتجر</p>
                            <p class="mb-0 fw-medium">{{ $user->store->address ?? 'غير محدد' }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                        <div>
                            <p class="mb-0 text-muted">نوع النشاط التجاري</p>
                            <p class="mb-0 fw-medium">{{ $user->store->business_type ?? 'غير محدد' }}</p>
                        </div>
                    </li>
                </ul>
            </x-card>
        </div>

        <div class="col-md-4 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">الإحصائيات</h5>
                </x-slot>
                
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['products'] }}</h3>
                        <p class="text-muted small mb-0">المنتجات</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-success mb-0">{{ $stats['sales'] }}</h3>
                        <p class="text-muted small mb-0">المبيعات</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-info mb-0">{{ number_format($stats['revenue'], 2) }} ج.س</h3>
                        <p class="text-muted small mb-0">الإيرادات</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-warning mb-0">{{ $user->created_at->diffInMonths() + 1 }}</h3>
                        <p class="text-muted small mb-0">شهور النشاط</p>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6 class="mb-2">التقييمات</h6>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2">5 <i class="bi bi-star-fill text-warning"></i></div>
                        <div class="progress flex-grow-1" style="height: 6px">
                            @php
                                $fiveStar = $user->ratings()->where('rating', 5)->count();
                                $totalRatings = $user->ratings()->count() ?: 1;
                                $fiveStarPercent = ($fiveStar / $totalRatings) * 100;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $fiveStarPercent }}%" aria-valuenow="{{ $fiveStarPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="ms-2 text-muted small">{{ $fiveStar }}</div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2">4 <i class="bi bi-star-fill text-warning"></i></div>
                        <div class="progress flex-grow-1" style="height: 6px">
                            @php
                                $fourStar = $user->ratings()->where('rating', 4)->count();
                                $fourStarPercent = ($fourStar / $totalRatings) * 100;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $fourStarPercent }}%" aria-valuenow="{{ $fourStarPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="ms-2 text-muted small">{{ $fourStar }}</div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2">3 <i class="bi bi-star-fill text-warning"></i></div>
                        <div class="progress flex-grow-1" style="height: 6px">
                            @php
                                $threeStar = $user->ratings()->where('rating', 3)->count();
                                $threeStarPercent = ($threeStar / $totalRatings) * 100;
                            @endphp
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $threeStarPercent }}%" aria-valuenow="{{ $threeStarPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="ms-2 text-muted small">{{ $threeStar }}</div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2">2 <i class="bi bi-star-fill text-warning"></i></div>
                        <div class="progress flex-grow-1" style="height: 6px">
                            @php
                                $twoStar = $user->ratings()->where('rating', 2)->count();
                                $twoStarPercent = ($twoStar / $totalRatings) * 100;
                            @endphp
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $twoStarPercent }}%" aria-valuenow="{{ $twoStarPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="ms-2 text-muted small">{{ $twoStar }}</div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2">1 <i class="bi bi-star-fill text-warning"></i></div>
                        <div class="progress flex-grow-1" style="height: 6px">
                            @php
                                $oneStar = $user->ratings()->where('rating', 1)->count();
                                $oneStarPercent = ($oneStar / $totalRatings) * 100;
                            @endphp
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $oneStarPercent }}%" aria-valuenow="{{ $oneStarPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="ms-2 text-muted small">{{ $oneStar }}</div>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-md-4 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">أحدث المبيعات</h5>
                        <a href="#" class="btn btn-sm btn-link">عرض الكل</a>
                    </div>
                </x-slot>
                
                @if($user->sales && $user->sales->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($user->sales->take(5) as $sale)
                            <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0 py-2">
                                <div class="ms-2 me-auto">
                                    <div class="fw-medium">{{ $sale->product->name ?? 'منتج غير معروف' }}</div>
                                    <div class="text-muted small">{{ $sale->created_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <span class="badge bg-success rounded-pill">
                                    {{ number_format($sale->amount, 2) }} ج.س
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-bag text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted">لا توجد مبيعات حتى الآن</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 mb-4">
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">المنتجات المعروضة</h5>
                        <a href="#" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus-lg"></i> إضافة منتج
                        </a>
                    </div>
                </x-slot>
                
                @if($user->products && $user->products->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>السعر</th>
                                    <th>الكمية</th>
                                    <th>التصنيف</th>
                                    <th>الحالة</th>
                                    <th>المبيعات</th>
                                    <th>التقييم</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->products->take(5) as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($product->image)
                                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="rounded me-2" width="40" height="40">
                                                @else
                                                    <div class="rounded bg-light d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="mb-0 fw-medium">{{ $product->name }}</p>
                                                    <p class="mb-0 text-muted small">{{ Str::limit($product->description, 30) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($product->price, 2) }} ج.س</td>
                                        <td>{{ $product->quantity }} {{ $product->unit }}</td>
                                        <td>{{ $product->category->name ?? 'غير مصنف' }}</td>
                                        <td>
                                            @if($product->is_active)
                                                <span class="badge bg-success">متاح</span>
                                            @else
                                                <span class="badge bg-secondary">غير متاح</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->sales_count ?? 0 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-star-fill text-warning me-1"></i>
                                                <span>{{ number_format($product->average_rating ?? 0, 1) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i> عرض</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-pencil me-2"></i> تعديل</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i> حذف</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-outline-primary">عرض جميع المنتجات</a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-box text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted">لا توجد منتجات معروضة حتى الآن</p>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-lg me-1"></i> إضافة منتج جديد
                        </a>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
