@extends('layouts.admin')

@section('title', 'تفاصيل المستخدم - لوحة تحكم المشرف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">تفاصيل المستخدم</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">المستخدمين</a></li>
                        <li class="breadcrumb-item active">تفاصيل المستخدم</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card overflow-hidden">
                <div class="bg-primary bg-soft">
                    <div class="row">
                        <div class="col-7">
                            <div class="text-primary p-3">
                                <h5 class="text-primary">معلومات المستخدم</h5>
                                <p>{{ $user->role == 'customer' ? 'عميل' : ($user->role == 'merchant' ? 'تاجر' : ($user->role == 'agent' ? 'وكيل' : ($user->role == 'messenger' ? 'مندوب' : 'مشرف'))) }}</p>
                            </div>
                        </div>
                        <div class="col-5 align-self-end">
                            <img src="{{ asset('assets/images/profile-bg.jpg') }}" alt="" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="avatar-md profile-user-wid mb-4">
                                @if($user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="" class="img-thumbnail rounded-circle">
                                @else
                                    <div class="avatar-md">
                                        <span class="avatar-title rounded-circle bg-primary text-white font-size-24">
                                            {{ substr($user->name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <h5 class="font-size-15 text-truncate">{{ $user->name }}</h5>
                            <p class="text-muted mb-0 text-truncate">{{ $user->id }}</p>
                        </div>

                        <div class="col-sm-8">
                            <div class="pt-4">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="font-size-15">{{ $user->created_at->diffForHumans() }}</h5>
                                        <p class="text-muted mb-0">تاريخ التسجيل</p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="font-size-15">
                                            @if($user->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @elseif($user->is_blocked)
                                                <span class="badge bg-danger">محظور</span>
                                            @else
                                                <span class="badge bg-warning">غير نشط</span>
                                            @endif
                                        </h5>
                                        <p class="text-muted mb-0">الحالة</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary waves-effect waves-light btn-sm">تعديل الملف الشخصي <i class="fas fa-edit ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">معلومات الاتصال</h4>

                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">البريد الإلكتروني :</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">رقم الهاتف :</th>
                                    <td>{{ $user->phone ?? 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">الدولة :</th>
                                    <td>{{ $user->country == 'SD' ? 'السودان' : $user->country ?? 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">المدينة :</th>
                                    <td>{{ $user->city ?? 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">العنوان :</th>
                                    <td>{{ $user->address ?? 'غير متوفر' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">معلومات الأمان</h4>

                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">التحقق بخطوتين :</th>
                                    <td>
                                        @if($user->two_factor_enabled)
                                            <span class="badge bg-success">مفعل</span>
                                        @else
                                            <span class="badge bg-danger">غير مفعل</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">التحقق من البريد الإلكتروني :</th>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">مؤكد</span>
                                        @else
                                            <span class="badge bg-danger">غير مؤكد</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">حالة التحقق (KYC) :</th>
                                    <td>
                                        @if($user->kyc_verified)
                                            <span class="badge bg-success">مؤكد</span>
                                        @elseif($user->kyc_status == 'pending')
                                            <span class="badge bg-warning">قيد المراجعة</span>
                                        @elseif($user->kyc_status == 'rejected')
                                            <span class="badge bg-danger">مرفوض</span>
                                        @else
                                            <span class="badge bg-secondary">غير مقدم</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">آخر تسجيل دخول :</th>
                                    <td>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">عنوان IP الأخير :</th>
                                    <td>{{ $user->last_login_ip ?? 'غير متوفر' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="row">
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">المحادثات</p>
                                    <h4 class="mb-0">{{ $stats['conversations'] ?? 0 }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-primary align-self-center">
                                    <span class="avatar-title">
                                        <i class="fas fa-comments font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">الرسائل</p>
                                    <h4 class="mb-0">{{ $stats['messages'] ?? 0 }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-success align-self-center">
                                    <span class="avatar-title">
                                        <i class="fas fa-envelope font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mini-stats-wid">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">المكالمات</p>
                                    <h4 class="mb-0">{{ $stats['calls'] ?? 0 }}</h4>
                                </div>
                                <div class="mini-stat-icon avatar-sm rounded-circle bg-warning align-self-center">
                                    <span class="avatar-title">
                                        <i class="fas fa-phone font-size-24"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($user->role == 'merchant')
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">معلومات المتجر</h4>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">اسم المتجر :</th>
                                    <td>{{ $user->merchant->store_name ?? 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">نوع المتجر :</th>
                                    <td>
                                        @if(isset($user->merchant->store_type))
                                            @if($user->merchant->store_type == 'retail')
                                                تجزئة
                                            @elseif($user->merchant->store_type == 'wholesale')
                                                جملة
                                            @elseif($user->merchant->store_type == 'service')
                                                خدمات
                                            @else
                                                {{ $user->merchant->store_type }}
                                            @endif
                                        @else
                                            غير متوفر
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">تاريخ التسجيل :</th>
                                    <td>{{ isset($user->merchant->created_at) ? $user->merchant->created_at->format('Y-m-d') : 'غير متوفر' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            @if($user->role == 'messenger')
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">معلومات المندوب</h4>
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">نوع المركبة :</th>
                                    <td>
                                        @if(isset($user->messenger->vehicle_type))
                                            @if($user->messenger->vehicle_type == 'motorcycle')
                                                دراجة نارية
                                            @elseif($user->messenger->vehicle_type == 'car')
                                                سيارة
                                            @elseif($user->messenger->vehicle_type == 'bicycle')
                                                دراجة هوائية
                                            @else
                                                {{ $user->messenger->vehicle_type }}
                                            @endif
                                        @else
                                            غير متوفر
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">رقم اللوحة :</th>
                                    <td>{{ $user->messenger->vehicle_plate ?? 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">الحالة :</th>
                                    <td>
                                        @if(isset($user->messenger->status))
                                            @if($user->messenger->status == 'available')
                                                <span class="badge bg-success">متاح</span>
                                            @elseif($user->messenger->status == 'busy')
                                                <span class="badge bg-warning">مشغول</span>
                                            @elseif($user->messenger->status == 'offline')
                                                <span class="badge bg-secondary">غير متصل</span>
                                            @else
                                                <span class="badge bg-info">{{ $user->messenger->status }}</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">غير متوفر</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">سجل النشاطات</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>النشاط</th>
                                    <th>التاريخ</th>
                                    <th>عنوان IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities ?? [] as $activity)
                                <tr>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>{{ $activity->ip_address ?? 'غير متوفر' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">لا توجد نشاطات مسجلة</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if(isset($activities) && count($activities) > 0)
                    <div class="mt-3">
                        {{ $activities->links() }}
                    </div>
                    @endif
                </div>
            </div>

            @if($user->kyc_status)
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <h4 class="card-title flex-grow-1">معلومات التحقق (KYC)</h4>
                        <div class="flex-shrink-0">
                            @if($user->kyc_status == 'pending')
                            <a href="{{ route('admin.kyc.show', $user->kyc->id ?? 0) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i> مراجعة طلب التحقق
                            </a>
                            @endif
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row">حالة التحقق :</th>
                                    <td>
                                        @if($user->kyc_verified)
                                            <span class="badge bg-success">مؤكد</span>
                                        @elseif($user->kyc_status == 'pending')
                                            <span class="badge bg-warning">قيد المراجعة</span>
                                        @elseif($user->kyc_status == 'rejected')
                                            <span class="badge bg-danger">مرفوض</span>
                                        @else
                                            <span class="badge bg-secondary">غير مقدم</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($user->kyc)
                                <tr>
                                    <th scope="row">نوع الهوية :</th>
                                    <td>
                                        @if($user->kyc->id_type == 'national_id')
                                            بطاقة هوية وطنية
                                        @elseif($user->kyc->id_type == 'passport')
                                            جواز سفر
                                        @elseif($user->kyc->id_type == 'driving_license')
                                            رخصة قيادة
                                        @else
                                            {{ $user->kyc->id_type }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">رقم الهوية :</th>
                                    <td>{{ $user->kyc->id_number }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">تاريخ التقديم :</th>
                                    <td>{{ $user->kyc->created_at->format('Y-m-d') }}</td>
                                </tr>
                                @if($user->kyc_status == 'rejected')
                                <tr>
                                    <th scope="row">سبب الرفض :</th>
                                    <td>{{ $user->kyc->rejection_reason ?? 'غير محدد' }}</td>
                                </tr>
                                @endif
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
