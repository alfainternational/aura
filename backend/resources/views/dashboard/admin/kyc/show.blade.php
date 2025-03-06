@extends('layouts.admin')

@section('title', 'تفاصيل طلب التحقق - لوحة تحكم المشرف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">تفاصيل طلب التحقق</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.kyc.index') }}">طلبات التحقق</a></li>
                        <li class="breadcrumb-item active">تفاصيل الطلب</li>
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
                                <p>{{ $kyc->user->role == 'customer' ? 'عميل' : ($kyc->user->role == 'merchant' ? 'تاجر' : ($kyc->user->role == 'agent' ? 'وكيل' : ($kyc->user->role == 'messenger' ? 'مندوب' : 'مشرف'))) }}</p>
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
                                @if($kyc->user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $kyc->user->profile_photo_path) }}" alt="" class="img-thumbnail rounded-circle">
                                @else
                                    <div class="avatar-md">
                                        <span class="avatar-title rounded-circle bg-primary text-white font-size-24">
                                            {{ substr($kyc->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <h5 class="font-size-15 text-truncate">{{ $kyc->user->name }}</h5>
                            <p class="text-muted mb-0 text-truncate">{{ $kyc->user->id }}</p>
                        </div>

                        <div class="col-sm-8">
                            <div class="pt-4">
                                <div class="row">
                                    <div class="col-6">
                                        <h5 class="font-size-15">{{ $kyc->user->created_at->diffForHumans() }}</h5>
                                        <p class="text-muted mb-0">تاريخ التسجيل</p>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="font-size-15">
                                            @if($kyc->user->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @elseif($kyc->user->is_blocked)
                                                <span class="badge bg-danger">محظور</span>
                                            @else
                                                <span class="badge bg-warning">غير نشط</span>
                                            @endif
                                        </h5>
                                        <p class="text-muted mb-0">الحالة</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('admin.users.show', $kyc->user->id) }}" class="btn btn-primary waves-effect waves-light btn-sm">عرض الملف الشخصي <i class="fas fa-user ms-1"></i></a>
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
                                    <td>{{ $kyc->user->email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">رقم الهاتف :</th>
                                    <td>{{ $kyc->user->phone ?? 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">الدولة :</th>
                                    <td>{{ $kyc->user->country == 'SD' ? 'السودان' : $kyc->user->country ?? 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">المدينة :</th>
                                    <td>{{ $kyc->user->city ?? 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">العنوان :</th>
                                    <td>{{ $kyc->user->address ?? 'غير متوفر' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <h4 class="card-title flex-grow-1">معلومات طلب التحقق</h4>
                        <div class="flex-shrink-0">
                            <span class="badge bg-{{ $kyc->status == 'pending' ? 'warning' : ($kyc->status == 'approved' ? 'success' : 'danger') }} font-size-12">
                                {{ $kyc->status == 'pending' ? 'قيد المراجعة' : ($kyc->status == 'approved' ? 'مقبول' : 'مرفوض') }}
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive mb-4">
                        <table class="table table-nowrap mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%;">رقم الطلب :</th>
                                    <td>{{ $kyc->id }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">نوع الهوية :</th>
                                    <td>
                                        @if($kyc->id_type == 'national_id')
                                            بطاقة هوية وطنية
                                        @elseif($kyc->id_type == 'passport')
                                            جواز سفر
                                        @elseif($kyc->id_type == 'driving_license')
                                            رخصة قيادة
                                        @else
                                            {{ $kyc->id_type }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">رقم الهوية :</th>
                                    <td>{{ $kyc->id_number }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">تاريخ الإصدار :</th>
                                    <td>{{ $kyc->issue_date ? \Carbon\Carbon::parse($kyc->issue_date)->format('Y-m-d') : 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">تاريخ الانتهاء :</th>
                                    <td>{{ $kyc->expiry_date ? \Carbon\Carbon::parse($kyc->expiry_date)->format('Y-m-d') : 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">تاريخ التقديم :</th>
                                    <td>{{ $kyc->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                @if($kyc->status == 'rejected')
                                <tr>
                                    <th scope="row">سبب الرفض :</th>
                                    <td>{{ $kyc->rejection_reason ?? 'غير محدد' }}</td>
                                </tr>
                                @endif
                                @if($kyc->status == 'approved')
                                <tr>
                                    <th scope="row">تاريخ الموافقة :</th>
                                    <td>{{ $kyc->approved_at ? \Carbon\Carbon::parse($kyc->approved_at)->format('Y-m-d H:i:s') : 'غير متوفر' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row">تمت الموافقة بواسطة :</th>
                                    <td>{{ $kyc->approved_by_name ?? 'غير متوفر' }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <h5 class="card-title mb-3">صور الوثائق</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-transparent border-bottom">
                                    <h5 class="card-title mb-0">صورة الهوية (الوجه الأمامي)</h5>
                                </div>
                                <div class="card-body">
                                    @if($kyc->id_front_path)
                                        <a href="{{ asset('storage/' . $kyc->id_front_path) }}" data-lightbox="id-images" data-title="صورة الهوية - الوجه الأمامي">
                                            <img src="{{ asset('storage/' . $kyc->id_front_path) }}" alt="صورة الهوية - الوجه الأمامي" class="img-fluid rounded">
                                        </a>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            لا توجد صورة للوجه الأمامي للهوية
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-transparent border-bottom">
                                    <h5 class="card-title mb-0">صورة الهوية (الوجه الخلفي)</h5>
                                </div>
                                <div class="card-body">
                                    @if($kyc->id_back_path)
                                        <a href="{{ asset('storage/' . $kyc->id_back_path) }}" data-lightbox="id-images" data-title="صورة الهوية - الوجه الخلفي">
                                            <img src="{{ asset('storage/' . $kyc->id_back_path) }}" alt="صورة الهوية - الوجه الخلفي" class="img-fluid rounded">
                                        </a>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            لا توجد صورة للوجه الخلفي للهوية
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-transparent border-bottom">
                                    <h5 class="card-title mb-0">صورة شخصية مع الهوية</h5>
                                </div>
                                <div class="card-body">
                                    @if($kyc->selfie_with_id_path)
                                        <a href="{{ asset('storage/' . $kyc->selfie_with_id_path) }}" data-lightbox="id-images" data-title="صورة شخصية مع الهوية">
                                            <img src="{{ asset('storage/' . $kyc->selfie_with_id_path) }}" alt="صورة شخصية مع الهوية" class="img-fluid rounded">
                                        </a>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            لا توجد صورة شخصية مع الهوية
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border">
                                <div class="card-header bg-transparent border-bottom">
                                    <h5 class="card-title mb-0">وثائق إضافية</h5>
                                </div>
                                <div class="card-body">
                                    @if($kyc->additional_document_path)
                                        <a href="{{ asset('storage/' . $kyc->additional_document_path) }}" data-lightbox="id-images" data-title="وثائق إضافية">
                                            <img src="{{ asset('storage/' . $kyc->additional_document_path) }}" alt="وثائق إضافية" class="img-fluid rounded">
                                        </a>
                                    @else
                                        <div class="alert alert-warning mb-0">
                                            لا توجد وثائق إضافية
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($kyc->status == 'pending')
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="fas fa-times-circle me-1"></i> رفض
                                </button>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <i class="fas fa-check-circle me-1"></i> قبول
                                </button>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة تأكيد القبول -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">تأكيد قبول طلب التحقق</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من رغبتك في قبول طلب التحقق هذا؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.kyc.approve', $kyc->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">قبول</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- نافذة تأكيد الرفض -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">رفض طلب التحقق</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kyc.reject', $kyc->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">سبب الرفض</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">رفض</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script>
    // تهيئة مكتبة Lightbox لعرض الصور
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': "صورة %1 من %2"
    });
</script>
@endsection
