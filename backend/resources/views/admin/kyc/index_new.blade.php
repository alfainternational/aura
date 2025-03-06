@extends('layouts.admin')

@section('title', 'إدارة طلبات التحقق من الهوية')

@section('content')
<div class="container-fluid py-4">
    <!-- إحصائيات سريعة -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">إجمالي الطلبات</p>
                                <h5 class="font-weight-bolder">{{ $stats['total'] ?? 0 }}</h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">+{{ $stats['new_today'] ?? 0 }}</span>
                                    اليوم
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                <i class="fas fa-users text-lg opacity-10" aria-hidden="true"></i>
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
                                <h5 class="font-weight-bolder">{{ $stats['pending'] ?? 0 }}</h5>
                                <p class="mb-0">
                                    <span class="text-warning text-sm font-weight-bolder">{{ $stats['pending_percent'] ?? 0 }}%</span>
                                    من الإجمالي
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                <i class="fas fa-hourglass-half text-lg opacity-10" aria-hidden="true"></i>
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
                                <h5 class="font-weight-bolder">{{ $stats['approved'] ?? 0 }}</h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">{{ $stats['approved_percent'] ?? 0 }}%</span>
                                    من الإجمالي
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
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
                                <p class="text-sm mb-0 text-uppercase font-weight-bold">مرفوضة</p>
                                <h5 class="font-weight-bolder">{{ $stats['rejected'] ?? 0 }}</h5>
                                <p class="mb-0">
                                    <span class="text-danger text-sm font-weight-bolder">{{ $stats['rejected_percent'] ?? 0 }}%</span>
                                    من الإجمالي
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                <i class="fas fa-times text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6>طلبات التحقق من الهوية (KYC)</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-check text-info" aria-hidden="true"></i>
                                <span class="font-weight-bold ms-1">إجمالي الطلبات:</span> {{ $verifications->total() }}
                            </p>
                        </div>
                        <div class="col-lg-6 col-5 my-auto text-end">
                            <form action="{{ route('admin.kyc.index') }}" method="GET" class="d-flex justify-content-end">
                                <div class="input-group me-2" style="width: 250px;">
                                    <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                                    <input type="text" class="form-control" name="search" placeholder="بحث..." value="{{ request('search') }}">
                                </div>
                                <select class="form-select me-2" name="status" style="width: 150px;" onchange="this.form.submit()">
                                    <option value="">جميع الحالات</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">تصفية</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">المستخدم</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">نوع الهوية</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الحالة</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">تاريخ التقديم</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">تاريخ التحقق</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($verifications as $verification)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="{{ $verification->user->profile_photo_url ?? asset('img/default-avatar.png') }}" class="avatar avatar-sm me-3" alt="user image">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $verification->user->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $verification->user->email }}</p>
                                                <p class="text-xs text-secondary mb-0">ID: {{ $verification->user->id }}</p>
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
                                    <td class="align-middle text-center text-sm">
                                        @if($verification->status === 'pending')
                                            <span class="badge badge-sm bg-gradient-warning">قيد المراجعة</span>
                                        @elseif($verification->status === 'approved')
                                            <span class="badge badge-sm bg-gradient-success">تمت الموافقة</span>
                                        @elseif($verification->status === 'rejected')
                                            <span class="badge badge-sm bg-gradient-danger">مرفوض</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $verification->submitted_at ? $verification->submitted_at->format('Y-m-d H:i') : 'غير متوفر' }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $verification->verified_at ? $verification->verified_at->format('Y-m-d H:i') : 'غير متوفر' }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.kyc.show', $verification->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($verification->status === 'pending')
                                        <a href="{{ route('admin.kyc.review', $verification->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="مراجعة الطلب">
                                            <i class="fas fa-check-circle"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-secondary mb-0">لا توجد طلبات تحقق من الهوية</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $verifications->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- آخر الطلبات المعتمدة -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6">
                            <h6 class="mb-0">آخر الطلبات المعتمدة</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">المستخدم</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">تاريخ الموافقة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestApproved ?? [] as $approved)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="{{ $approved->user->profile_photo_url ?? asset('img/default-avatar.png') }}" class="avatar avatar-sm me-3" alt="user image">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $approved->user->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $approved->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $approved->verified_at ? $approved->verified_at->format('Y-m-d H:i') : 'غير متوفر' }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        <p class="text-secondary mb-0">لا توجد طلبات معتمدة حديثاً</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- آخر الطلبات المرفوضة -->
        <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-lg-6">
                            <h6 class="mb-0">آخر الطلبات المرفوضة</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">المستخدم</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">سبب الرفض</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestRejected ?? [] as $rejected)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <img src="{{ $rejected->user->profile_photo_url ?? asset('img/default-avatar.png') }}" class="avatar avatar-sm me-3" alt="user image">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $rejected->user->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $rejected->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $rejected->rejection_reason ?? 'غير محدد' }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        <p class="text-secondary mb-0">لا توجد طلبات مرفوضة حديثاً</p>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تفعيل tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
