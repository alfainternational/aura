@extends('layouts.dashboard')

@section('title', 'الملف الشخصي - وكيل')

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
                            <span class="badge bg-success">{{ $stats['agent_level'] }}</span>
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
                            <span class="badge bg-primary">وكيل</span>
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
                            <p class="mb-0 text-muted">مجال العمل</p>
                            <p class="mb-0 fw-medium">{{ $user->agent_specialization ?? 'غير محدد' }}</p>
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
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total_tasks'] }}</h3>
                        <p class="text-muted small mb-0">المهام الكلية</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-success mb-0">{{ $stats['completed_tasks'] }}</h3>
                        <p class="text-muted small mb-0">المهام المكتملة</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-info mb-0">{{ number_format($stats['success_rate'], 1) }}%</h3>
                        <p class="text-muted small mb-0">معدل النجاح</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="fw-bold text-warning mb-0">{{ number_format($stats['avg_response_time'], 1) }} دقيقة</h3>
                        <p class="text-muted small mb-0">متوسط وقت الرد</p>
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
                        <h5 class="mb-0">المهام الحالية</h5>
                        <a href="#" class="btn btn-sm btn-link">عرض الكل</a>
                    </div>
                </x-slot>
                
                @if($user->tasks && $user->tasks->where('status', '!=', 'completed')->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($user->tasks->where('status', '!=', 'completed')->take(5) as $task)
                            <li class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0">{{ $task->title }}</h6>
                                    @if($task->status == 'pending')
                                        <span class="badge bg-warning">قيد الانتظار</span>
                                    @elseif($task->status == 'in_progress')
                                        <span class="badge bg-info">قيد التنفيذ</span>
                                    @elseif($task->status == 'on_hold')
                                        <span class="badge bg-secondary">معلق</span>
                                    @endif
                                </div>
                                <p class="text-muted small mb-1">{{ Str::limit($task->description, 60) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar me-1"></i> {{ $task->due_date->format('Y-m-d') }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-person me-1"></i> {{ $task->customer->name ?? 'غير محدد' }}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-clipboard-check text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted">لا توجد مهام حالية</p>
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
                        <h5 class="mb-0">سجل المهام</h5>
                        <div>
                            <select class="form-select form-select-sm me-2" style="width: auto; display: inline-block;">
                                <option>جميع المهام</option>
                                <option>المهام المكتملة</option>
                                <option>المهام قيد التنفيذ</option>
                                <option>المهام المعلقة</option>
                            </select>
                            <a href="#" class="btn btn-sm btn-outline-primary">تصدير التقرير</a>
                        </div>
                    </div>
                </x-slot>
                
                @if($user->tasks && $user->tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>رقم المهمة</th>
                                    <th>عنوان المهمة</th>
                                    <th>العميل</th>
                                    <th>تاريخ البدء</th>
                                    <th>تاريخ الاستحقاق</th>
                                    <th>الحالة</th>
                                    <th>التقييم</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->tasks->take(5) as $task)
                                    <tr>
                                        <td><strong>#{{ $task->id }}</strong></td>
                                        <td>{{ $task->title }}</td>
                                        <td>{{ $task->customer->name ?? 'غير محدد' }}</td>
                                        <td>{{ $task->created_at->format('Y-m-d') }}</td>
                                        <td>{{ $task->due_date->format('Y-m-d') }}</td>
                                        <td>
                                            @if($task->status == 'pending')
                                                <span class="badge bg-warning">قيد الانتظار</span>
                                            @elseif($task->status == 'in_progress')
                                                <span class="badge bg-info">قيد التنفيذ</span>
                                            @elseif($task->status == 'completed')
                                                <span class="badge bg-success">مكتمل</span>
                                            @elseif($task->status == 'on_hold')
                                                <span class="badge bg-secondary">معلق</span>
                                            @elseif($task->status == 'canceled')
                                                <span class="badge bg-danger">ملغي</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($task->rating)
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-star-fill text-warning me-1"></i>
                                                    <span>{{ number_format($task->rating, 1) }}</span>
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
                                                    @if($task->status != 'completed' && $task->status != 'canceled')
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
                        <a href="#" class="btn btn-outline-primary">عرض جميع المهام</a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-clipboard text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted">لا توجد مهام مسجلة</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</div>
@endsection
