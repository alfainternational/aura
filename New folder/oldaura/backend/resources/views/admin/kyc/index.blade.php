@extends('layouts.admin')

@section('title', 'إدارة طلبات التحقق من الهوية')

@section('content')
<div class="container-fluid py-4">
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
                                    <th class="text-secondary opacity-7"></th>
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
                                            <span class="badge badge-sm bg-warning">قيد المراجعة</span>
                                        @elseif($verification->status === 'approved')
                                            <span class="badge badge-sm bg-success">تمت الموافقة</span>
                                        @elseif($verification->status === 'rejected')
                                            <span class="badge badge-sm bg-danger">مرفوض</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $verification->submitted_at ? $verification->submitted_at->format('Y-m-d H:i') : 'غير متوفر' }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $verification->verified_at ? $verification->verified_at->format('Y-m-d H:i') : 'غير متوفر' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('admin.kyc.show', $verification->id) }}" class="btn btn-sm btn-info me-2" title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($verification->status === 'pending')
                                                <a href="{{ route('admin.kyc.review', $verification->id) }}" class="btn btn-sm btn-warning" title="مراجعة وتحديث الحالة">
                                                    <i class="fas fa-check-circle"></i> مراجعة
                                                </a>
                                            @endif
                                        </div>
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
</div>
@endsection
