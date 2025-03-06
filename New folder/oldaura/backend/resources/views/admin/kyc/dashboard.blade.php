@extends('layouts.admin')

@section('title', 'لوحة معلومات التحقق من الهوية')

@section('content')
<div class="container-fluid py-4">
    <!-- بطاقات الإحصائيات -->
    @include('admin.kyc.partials.stats-cards')

    <div class="row mb-4">
        <!-- الرسم البياني الشهري -->
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6>إحصائيات طلبات التحقق من الهوية</h6>
                    <p class="text-sm">
                        <i class="fa fa-arrow-up text-success"></i>
                        <span class="font-weight-bold">إحصائيات آخر 6 أشهر</span>
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-kyc-monthly" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- آخر الطلبات -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>آخر طلبات التحقق من الهوية</h6>
                </div>
                <div class="card-body p-3">
                    <div class="timeline timeline-one-side">
                        @forelse($latestVerifications as $verification)
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    @if($verification->status == 'approved')
                                        <i class="fas fa-check text-success text-gradient"></i>
                                    @elseif($verification->status == 'rejected')
                                        <i class="fas fa-times text-danger text-gradient"></i>
                                    @else
                                        <i class="fas fa-clock text-warning text-gradient"></i>
                                    @endif
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $verification->user->name }}</h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        {{ $verification->created_at->format('d M H:i') }}
                                    </p>
                                    <p class="text-sm mt-3 mb-2">
                                        {{ $verification->user->email }}
                                    </p>
                                    <a href="{{ route('admin.kyc.show', $verification->id) }}" class="btn btn-sm btn-info">
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p>لا توجد طلبات تحقق من الهوية حتى الآن</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- طلبات قيد المراجعة -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>طلبات التحقق قيد المراجعة</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">المستخدم</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">نوع الهوية</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">تاريخ الطلب</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingVerifications as $verification)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ $verification->user->profile_photo_url ?? asset('img/default-avatar.png') }}" class="avatar avatar-sm me-3" alt="{{ $verification->user->name }}">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $verification->user->name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $verification->user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                @switch($verification->id_type)
                                                    @case('national_id')
                                                        بطاقة هوية وطنية
                                                        @break
                                                    @case('passport')
                                                        جواز سفر
                                                        @break
                                                    @case('residence')
                                                        إقامة
                                                        @break
                                                    @default
                                                        {{ $verification->id_type }}
                                                @endswitch
                                            </p>
                                            <p class="text-xs text-secondary mb-0">{{ $verification->id_number }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $verification->created_at->format('Y-m-d H:i') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('admin.kyc.show', $verification->id) }}" class="btn btn-link text-dark px-3 mb-0">
                                                <i class="fas fa-eye text-dark me-2" aria-hidden="true"></i>عرض
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <p class="mb-0">لا توجد طلبات قيد المراجعة حاليًا</p>
                                        </td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("chart-kyc-monthly").getContext("2d");
        
        var gradientStroke1 = ctx.createLinearGradient(0, 230, 0, 50);
        gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
        gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');
        
        var gradientStroke2 = ctx.createLinearGradient(0, 230, 0, 50);
        gradientStroke2.addColorStop(1, 'rgba(45, 206, 137, 0.2)');
        gradientStroke2.addColorStop(0.2, 'rgba(45, 206, 137, 0.0)');
        gradientStroke2.addColorStop(0, 'rgba(45, 206, 137, 0)');
        
        var gradientStroke3 = ctx.createLinearGradient(0, 230, 0, 50);
        gradientStroke3.addColorStop(1, 'rgba(251, 99, 64, 0.2)');
        gradientStroke3.addColorStop(0.2, 'rgba(251, 99, 64, 0.0)');
        gradientStroke3.addColorStop(0, 'rgba(251, 99, 64, 0)');
        
        var gradientStroke4 = ctx.createLinearGradient(0, 230, 0, 50);
        gradientStroke4.addColorStop(1, 'rgba(255, 193, 7, 0.2)');
        gradientStroke4.addColorStop(0.2, 'rgba(255, 193, 7, 0.0)');
        gradientStroke4.addColorStop(0, 'rgba(255, 193, 7, 0)');
        
        new Chart(ctx, {
            type: "line",
            data: {
                labels: @json($monthlyStats['labels']),
                datasets: [
                    {
                        label: "إجمالي الطلبات",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#5e72e4",
                        backgroundColor: gradientStroke1,
                        borderWidth: 3,
                        fill: true,
                        data: @json($monthlyStats['datasets']['total']),
                        maxBarThickness: 6
                    },
                    {
                        label: "تمت الموافقة",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#2dce89",
                        backgroundColor: gradientStroke2,
                        borderWidth: 3,
                        fill: true,
                        data: @json($monthlyStats['datasets']['approved']),
                        maxBarThickness: 6
                    },
                    {
                        label: "مرفوضة",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#fb6340",
                        backgroundColor: gradientStroke3,
                        borderWidth: 3,
                        fill: true,
                        data: @json($monthlyStats['datasets']['rejected']),
                        maxBarThickness: 6
                    },
                    {
                        label: "قيد المراجعة",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 0,
                        borderColor: "#ffc107",
                        backgroundColor: gradientStroke4,
                        borderWidth: 3,
                        fill: true,
                        data: @json($monthlyStats['datasets']['pending']),
                        maxBarThickness: 6
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#344767',
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: false,
                            drawOnChartArea: false,
                            drawTicks: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            display: true,
                            color: '#344767',
                            padding: 10,
                            font: {
                                size: 11,
                                family: "Open Sans",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });
    });
</script>
@endpush
