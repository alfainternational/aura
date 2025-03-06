@extends('layouts.dashboard')

@section('title', 'الملف الشخصي - مرسال')

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
                            <span class="badge bg-success">{{ $stats['level'] }}</span>
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
                            <span>{{ number_format($stats['rating'], 1) }} ({{ $user->reviews_count ?? 0 }} تقييم)</span>
                        </div>
                        <p class="mb-1">
                            <span class="badge bg-primary">مرسال</span>
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
                            <p class="mb-0 text-muted">العنوان</p>
                            <p class="mb-0 fw-medium">{{ $user->address ?? 'غير محدد' }}</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                        <div>
                            <p class="mb-0 text-muted">وسيلة التوصيل</p>
                            <p class="mb-0 fw-medium">{{ $user->delivery_vehicle ?? 'غير محدد' }}</p>
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
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total_deliveries'] }}</h3>
                        <p class="text-muted small mb-0">عمليات التوصيل</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-success mb-0">{{ $stats['completed_deliveries'] }}</h3>
                        <p class="text-muted small mb-0">عمليات مكتملة</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-info mb-0">{{ number_format($stats['on_time_percentage'], 1) }}%</h3>
                        <p class="text-muted small mb-0">نسبة الالتزام بالوقت</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-warning mb-0">{{ number_format($stats['avg_delivery_time'], 1) }} دقيقة</h3>
                        <p class="text-muted small mb-0">متوسط وقت التوصيل</p>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6 class="mb-2">التقييمات</h6>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2">5 <i class="bi bi-star-fill text-warning"></i></div>
                        <div class="progress flex-grow-1" style="height: 6px">
                            @php
                                $fiveStar = $user->reviews()->where('rating', 5)->count();
                                $totalReviews = $user->reviews()->count() ?: 1;
                                $fiveStarPercent = ($fiveStar / $totalReviews) * 100;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $fiveStarPercent }}%" aria-valuenow="{{ $fiveStarPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="ms-2 text-muted small">{{ $fiveStar }}</div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2">4 <i class="bi bi-star-fill text-warning"></i></div>
                        <div class="progress flex-grow-1" style="height: 6px">
                            @php
                                $fourStar = $user->reviews()->where('rating', 4)->count();
                                $fourStarPercent = ($fourStar / $totalReviews) * 100;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $fourStarPercent }}%" aria-valuenow="{{ $fourStarPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="ms-2 text-muted small">{{ $fourStar }}</div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2">3 <i class="bi bi-star-fill text-warning"></i></div>
                        <div class="progress flex-grow-1" style="height: 6px">
                            @php
                                $threeStar = $user->reviews()->where('rating', 3)->count();
                                $threeStarPercent = ($threeStar / $totalReviews) * 100;
                            @endphp
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $threeStarPercent }}%" aria-valuenow="{{ $threeStarPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="ms-2 text-muted small">{{ $threeStar }}</div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2">2 <i class="bi bi-star-fill text-warning"></i></div>
                        <div class="progress flex-grow-1" style="height: 6px">
                            @php
                                $twoStar = $user->reviews()->where('rating', 2)->count();
                                $twoStarPercent = ($twoStar / $totalReviews) * 100;
                            @endphp
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $twoStarPercent }}%" aria-valuenow="{{ $twoStarPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="ms-2 text-muted small">{{ $twoStar }}</div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-2">1 <i class="bi bi-star-fill text-warning"></i></div>
                        <div class="progress flex-grow-1" style="height: 6px">
                            @php
                                $oneStar = $user->reviews()->where('rating', 1)->count();
                                $oneStarPercent = ($oneStar / $totalReviews) * 100;
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
                        <h5 class="mb-0">الطلبات الحالية</h5>
                        <a href="#" class="btn btn-sm btn-link">عرض الكل</a>
                    </div>
                </x-slot>
                
                @if($user->activeOrders && $user->activeOrders->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($user->activeOrders->take(5) as $order)
                            <li class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0">طلب #{{ $order->id }}</h6>
                                    @if($order->status == 'assigned')
                                        <span class="badge bg-warning">تم التعيين</span>
                                    @elseif($order->status == 'picked_up')
                                        <span class="badge bg-info">تم الاستلام</span>
                                    @elseif($order->status == 'in_transit')
                                        <span class="badge bg-primary">جاري التوصيل</span>
                                    @endif
                                </div>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $order->pickup_address ? Str::limit($order->pickup_address, 25) : 'غير محدد' }}
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    {{ $order->delivery_address ? Str::limit($order->delivery_address, 25) : 'غير محدد' }}
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <i class="bi bi-clock me-1"></i> {{ $order->expected_delivery_time ? $order->expected_delivery_time->format('H:i') : 'غير محدد' }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-person me-1"></i> {{ $order->customer ? $order->customer->name : 'غير محدد' }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-box text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted">لا توجد طلبات حالية</p>
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
                        <h5 class="mb-0">سجل التوصيل</h5>
                        <div>
                            <select class="form-select form-select-sm me-2" style="width: auto; display: inline-block;">
                                <option>جميع الطلبات</option>
                                <option>طلبات مكتملة</option>
                                <option>طلبات تحت التنفيذ</option>
                                <option>طلبات ملغاة</option>
                            </select>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-file-earmark-arrow-down me-1"></i> تصدير التقرير
                            </a>
                        </div>
                    </div>
                </x-slot>
                
                @if($user->deliveries && $user->deliveries->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>المتجر</th>
                                    <th>المسافة</th>
                                    <th>وقت التعيين</th>
                                    <th>وقت التسليم</th>
                                    <th>الحالة</th>
                                    <th>التقييم</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->deliveries->take(5) as $delivery)
                                    <tr>
                                        <td><strong>#{{ $delivery->id }}</strong></td>
                                        <td>{{ $delivery->customer->name ?? 'غير محدد' }}</td>
                                        <td>{{ $delivery->store->name ?? 'غير محدد' }}</td>
                                        <td>{{ number_format($delivery->distance ?? 0, 1) }} كم</td>
                                        <td>{{ $delivery->assigned_at ? $delivery->assigned_at->format('Y-m-d H:i') : 'غير محدد' }}</td>
                                        <td>{{ $delivery->delivered_at ? $delivery->delivered_at->format('Y-m-d H:i') : 'غير محدد' }}</td>
                                        <td>
                                            @if($delivery->status == 'assigned')
                                                <span class="badge bg-warning">تم التعيين</span>
                                            @elseif($delivery->status == 'picked_up')
                                                <span class="badge bg-info">تم الاستلام</span>
                                            @elseif($delivery->status == 'in_transit')
                                                <span class="badge bg-primary">جاري التوصيل</span>
                                            @elseif($delivery->status == 'delivered')
                                                <span class="badge bg-success">تم التوصيل</span>
                                            @elseif($delivery->status == 'cancelled')
                                                <span class="badge bg-danger">ملغي</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $delivery->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($delivery->review)
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-star-fill text-warning me-1"></i>
                                                    <span>{{ number_format($delivery->review->rating, 1) }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">غير مقيم</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i> التفاصيل</a></li>
                                                    @if(!in_array($delivery->status, ['delivered', 'cancelled']))
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-check-circle me-2"></i> تحديث الحالة</a></li>
                                                    @endif
                                                    <li><a class="dropdown-item" href="#"><i class="bi bi-chat-dots me-2"></i> الرسائل</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-outline-primary">عرض جميع الطلبات</a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-box-seam text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted">لا توجد عمليات توصيل مسجلة</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">حالة التوصيل اليومية</h5>
                </x-slot>
                
                <div class="row text-center">
                    <div class="col-4 mb-3">
                        <div class="p-3 rounded bg-light">
                            <h3 class="fw-bold text-primary mb-0">{{ $stats['today_deliveries'] ?? 0 }}</h3>
                            <p class="text-muted small mb-0">طلبات اليوم</p>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <div class="p-3 rounded bg-light">
                            <h3 class="fw-bold text-success mb-0">{{ $stats['today_completed'] ?? 0 }}</h3>
                            <p class="text-muted small mb-0">طلبات مكتملة</p>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <div class="p-3 rounded bg-light">
                            <h3 class="fw-bold text-warning mb-0">{{ $stats['today_pending'] ?? 0 }}</h3>
                            <p class="text-muted small mb-0">طلبات جارية</p>
                        </div>
                    </div>
                </div>
                
                <!-- مخطط نشاط التوصيل -->
                <div class="mt-3">
                    <h6 class="mb-2">نشاط التوصيل اليومي</h6>
                    <canvas id="deliveryActivityChart" height="200"></canvas>
                </div>
            </x-card>
        </div>
        
        <div class="col-md-6 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">الأرباح والعمولات</h5>
                </x-slot>
                
                <div class="row text-center">
                    <div class="col-4 mb-3">
                        <div class="p-3 rounded bg-light">
                            <h3 class="fw-bold text-primary mb-0">{{ number_format($stats['today_earnings'] ?? 0, 2) }}</h3>
                            <p class="text-muted small mb-0">أرباح اليوم</p>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <div class="p-3 rounded bg-light">
                            <h3 class="fw-bold text-success mb-0">{{ number_format($stats['week_earnings'] ?? 0, 2) }}</h3>
                            <p class="text-muted small mb-0">أرباح الأسبوع</p>
                        </div>
                    </div>
                    <div class="col-4 mb-3">
                        <div class="p-3 rounded bg-light">
                            <h3 class="fw-bold text-info mb-0">{{ number_format($stats['month_earnings'] ?? 0, 2) }}</h3>
                            <p class="text-muted small mb-0">أرباح الشهر</p>
                        </div>
                    </div>
                </div>
                
                <!-- مخطط الأرباح الشهرية -->
                <div class="mt-3">
                    <h6 class="mb-2">الأرباح الشهرية</h6>
                    <canvas id="monthlyEarningsChart" height="200"></canvas>
                </div>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // مخطط نشاط التوصيل
        const deliveryActivityCtx = document.getElementById('deliveryActivityChart').getContext('2d');
        const deliveryHours = ['6 ص', '8 ص', '10 ص', '12 م', '2 م', '4 م', '6 م', '8 م', '10 م'];
        const deliveryData = [0, 2, 5, 3, 4, 6, 3, 2, 1]; // هذه بيانات تجريبية، يجب استبدالها بالبيانات الفعلية
        
        new Chart(deliveryActivityCtx, {
            type: 'line',
            data: {
                labels: deliveryHours,
                datasets: [{
                    label: 'عدد عمليات التوصيل',
                    data: deliveryData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // مخطط الأرباح الشهرية
        const monthlyEarningsCtx = document.getElementById('monthlyEarningsChart').getContext('2d');
        const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'];
        const earningsData = [1200, 1900, 2300, 1800, 2500, 3000]; // هذه بيانات تجريبية، يجب استبدالها بالبيانات الفعلية
        
        new Chart(monthlyEarningsCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'الأرباح الشهرية',
                    data: earningsData,
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: '#22c55e',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection