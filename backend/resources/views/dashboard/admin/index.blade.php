@extends('layouts.admin')

@section('title', 'لوحة تحكم المشرف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">لوحة تحكم المشرف</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">لوحة التحكم</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md flex-shrink-0">
                            <span class="avatar-title bg-soft-primary rounded-circle">
                                <i class="fas fa-users font-size-24 text-primary"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="font-size-16 mb-0">المستخدمين</h5>
                            <h2 class="mb-0">{{ $totalUsers ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md flex-shrink-0">
                            <span class="avatar-title bg-soft-success rounded-circle">
                                <i class="fas fa-store font-size-24 text-success"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="font-size-16 mb-0">التجار</h5>
                            <h2 class="mb-0">{{ $totalMerchants ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md flex-shrink-0">
                            <span class="avatar-title bg-soft-warning rounded-circle">
                                <i class="fas fa-user-tie font-size-24 text-warning"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="font-size-16 mb-0">الوكلاء</h5>
                            <h2 class="mb-0">{{ $totalAgents ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-md flex-shrink-0">
                            <span class="avatar-title bg-soft-info rounded-circle">
                                <i class="fas fa-motorcycle font-size-24 text-info"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="font-size-16 mb-0">المندوبين</h5>
                            <h2 class="mb-0">{{ $totalMessengers ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">إحصائيات المستخدمين</h4>
                    <div id="user-statistics-chart" style="height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">طلبات التحقق الأخيرة</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>المستخدم</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                    <th>الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentKycRequests ?? [] as $request)
                                <tr>
                                    <td>{{ $request->user->name ?? 'غير معروف' }}</td>
                                    <td>{{ $request->type ?? 'غير معروف' }}</td>
                                    <td>
                                        @if($request->status == 'pending')
                                            <span class="badge bg-warning">قيد الانتظار</span>
                                        @elseif($request->status == 'approved')
                                            <span class="badge bg-success">تم الموافقة</span>
                                        @elseif($request->status == 'rejected')
                                            <span class="badge bg-danger">مرفوض</span>
                                        @else
                                            <span class="badge bg-secondary">غير معروف</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.kyc.show', $request->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد طلبات تحقق حديثة</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">المستخدمين المسجلين حديثاً</h4>
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الدور</th>
                                    <th>تاريخ التسجيل</th>
                                    <th>الحالة</th>
                                    <th>الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers ?? [] as $user)
                                <tr>
                                    <td>{{ $user->name ?? 'غير معروف' }}</td>
                                    <td>{{ $user->email ?? 'غير معروف' }}</td>
                                    <td>{{ $user->role ?? 'غير معروف' }}</td>
                                    <td>{{ $user->created_at ? $user->created_at->format('Y-m-d') : 'غير معروف' }}</td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا يوجد مستخدمين مسجلين حديثاً</td>
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
        var options = {
            series: [{
                name: 'المستخدمين',
                data: @json($userStats ?? [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0])
            }],
            chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '45%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
            },
            yaxis: {
                title: {
                    text: 'عدد المستخدمين'
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + " مستخدم"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#user-statistics-chart"), options);
        chart.render();
    });
</script>
@endsection
