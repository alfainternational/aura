@extends('layouts.admin')

@section('title', 'نظرة عامة - لوحة تحكم المشرف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">نظرة عامة</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">نظرة عامة</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">إجمالي المستخدمين</p>
                            <h4 class="mb-0">{{ $stats['totalUsers'] ?? 0 }}</h4>
                        </div>
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                            <span class="avatar-title">
                                <i class="fas fa-users font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top">
                    <div class="text-center">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-light btn-sm">عرض التفاصيل</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">المستخدمين النشطين</p>
                            <h4 class="mb-0">{{ $stats['activeUsers'] ?? 0 }}</h4>
                        </div>
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-success align-self-center">
                            <span class="avatar-title">
                                <i class="fas fa-user-check font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top">
                    <div class="d-flex">
                        <div class="flex-grow-1 text-center">
                            <a href="#" class="btn btn-outline-light btn-sm">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">طلبات التحقق</p>
                            <h4 class="mb-0">{{ $stats['pendingKyc'] ?? 0 }}</h4>
                        </div>
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-warning align-self-center">
                            <span class="avatar-title">
                                <i class="fas fa-id-card font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top">
                    <div class="text-center">
                        <a href="{{ route('admin.kyc.index') }}" class="btn btn-outline-light btn-sm">عرض التفاصيل</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card mini-stats-wid">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-muted fw-medium">المحادثات النشطة</p>
                            <h4 class="mb-0">{{ $stats['activeConversations'] ?? 0 }}</h4>
                        </div>
                        <div class="mini-stat-icon avatar-sm rounded-circle bg-info align-self-center">
                            <span class="avatar-title">
                                <i class="fas fa-comments font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top">
                    <div class="text-center">
                        <a href="#" class="btn btn-outline-light btn-sm">عرض التفاصيل</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- مخطط نمو المستخدمين -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="card-title">نمو المستخدمين</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="growthChartDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v align-middle"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="growthChartDropdown">
                                    <li><a class="dropdown-item" href="#">اليوم</a></li>
                                    <li><a class="dropdown-item" href="#">هذا الأسبوع</a></li>
                                    <li><a class="dropdown-item" href="#">هذا الشهر</a></li>
                                    <li><a class="dropdown-item" href="#">هذا العام</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="user-growth-chart" class="apex-charts" dir="ltr" style="height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">توزيع المستخدمين</h4>
                    <div id="user-distribution-chart" class="apex-charts" dir="ltr" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- أحدث المستخدمين والنشاطات -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-3">أحدث المستخدمين</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.users') }}" class="btn btn-primary btn-sm">عرض الكل</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>المستخدم</th>
                                    <th>الدور</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestUsers ?? [] as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    {{ substr($user->name ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="font-size-14 mb-0">{{ $user->name ?? 'غير معروف' }}</h5>
                                                <p class="text-muted mb-0">{{ $user->email ?? 'غير معروف' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->role ?? 'غير معروف' }}</td>
                                    <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : 'غير معروف' }}</td>
                                    <td>
                                        @if($user->is_active ?? false)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا يوجد مستخدمين حديثين</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-3">أحدث النشاطات</h4>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="#" class="btn btn-primary btn-sm">عرض الكل</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>المستخدم</th>
                                    <th>النشاط</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestActivities ?? [] as $activity)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-3">
                                                <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                    {{ substr($activity->user->name ?? 'U', 0, 1) }}
                                                </span>
                                            </div>
                                            <span>{{ $activity->user->name ?? 'غير معروف' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $activity->description ?? 'غير معروف' }}</td>
                                    <td>{{ $activity->created_at ? $activity->created_at->diffForHumans() : 'غير معروف' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">لا توجد نشاطات حديثة</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // مخطط نمو المستخدمين
        var userGrowthOptions = {
            series: [{
                name: 'المستخدمين',
                data: @json($userGrowthData ?? [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0])
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
            },
            yaxis: {
                title: {
                    text: 'عدد المستخدمين'
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " مستخدم"
                    }
                }
            }
        };

        var userGrowthChart = new ApexCharts(document.querySelector("#user-growth-chart"), userGrowthOptions);
        userGrowthChart.render();

        // مخطط توزيع المستخدمين
        var userDistributionOptions = {
            series: @json($userDistributionData ?? [25, 15, 10, 30, 20]),
            chart: {
                type: 'donut',
                height: 350
            },
            labels: ['العملاء', 'التجار', 'الوكلاء', 'المندوبين', 'آخرون'],
            colors: ['#3b5de7', '#45cb85', '#eeb902', '#ff715b', '#8c68cd'],
            legend: {
                position: 'bottom'
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " مستخدم"
                    }
                }
            }
        };

        var userDistributionChart = new ApexCharts(document.querySelector("#user-distribution-chart"), userDistributionOptions);
        userDistributionChart.render();
    });
</script>
@endsection
