<div class="row mb-4">
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">إجمالي الطلبات</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $stats['total'] }}
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                            <i class="fas fa-id-card text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">قيد المراجعة</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $stats['pending'] }}
                                @if($stats['pending'] > 0)
                                    <span class="text-danger text-sm font-weight-bolder">تحتاج إلى مراجعة</span>
                                @endif
                            </h5>
                            <p class="mb-0 text-xs">
                                <span class="text-info">{{ $stats['pending_percent'] }}%</span> من إجمالي الطلبات
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                            <i class="fas fa-clock text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">تمت الموافقة</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $stats['approved'] }}
                                <span class="text-success text-sm font-weight-bolder">{{ number_format(($stats['approved'] / max($stats['total'], 1)) * 100, 1) }}%</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                            <i class="fas fa-check text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">جديدة اليوم</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $stats['new_today'] }}
                            </h5>
                            <p class="mb-0 text-xs">
                                <span class="text-success">{{ $stats['completed_today'] }}</span> تم مراجعتها اليوم
                            </p>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                            <i class="fas fa-calendar-day text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body p-3">
                <div class="row">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-uppercase font-weight-bold">مرفوضة</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $stats['rejected'] }}
                                <span class="text-danger text-sm font-weight-bolder">{{ number_format(($stats['rejected'] / max($stats['total'], 1)) * 100, 1) }}%</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                            <i class="fas fa-times text-lg opacity-10" aria-hidden="true"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
