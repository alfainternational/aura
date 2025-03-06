@extends('layouts.admin')

@section('title', 'مراجعة طلب التحقق من الهوية')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>مراجعة طلب التحقق من الهوية #{{ $verification->id }}</h6>
                    <a href="{{ route('admin.kyc.index') }}" class="btn btn-sm btn-outline-primary">العودة للقائمة</a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6 class="text-white mb-1">
                                    <i class="fas fa-info-circle me-1"></i> 
                                    معلومات الطلب
                                </h6>
                                <ul class="mb-0 ps-4">
                                    <li>تاريخ التقديم: {{ $verification->created_at->format('Y-m-d H:i') }}</li>
                                    <li>الحالة: <span class="badge bg-warning">قيد المراجعة</span></li>
                                    <li>مدة الانتظار: {{ $verification->created_at->diffForHumans() }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-primary">
                                <h6 class="text-white mb-1">
                                    <i class="fas fa-user-circle me-1"></i> 
                                    معلومات المستخدم
                                </h6>
                                <ul class="mb-0 ps-4">
                                    <li>الاسم: {{ $verification->user->name }}</li>
                                    <li>البريد الإلكتروني: {{ $verification->user->email }}</li>
                                    <li>رقم الهاتف: {{ $verification->user->phone ?? 'غير متوفر' }}</li>
                                    <li>تاريخ التسجيل: {{ $verification->user->created_at->format('Y-m-d') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">معلومات الهوية</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>الاسم الكامل</th>
                                            <td>{{ $verification->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>رقم الهوية</th>
                                            <td>{{ $verification->id_number }}</td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ الميلاد</th>
                                            <td>{{ $verification->date_of_birth }}</td>
                                        </tr>
                                        <tr>
                                            <th>نوع الهوية</th>
                                            <td>{{ $verification->id_type }}</td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ الإصدار</th>
                                            <td>{{ $verification->issue_date }}</td>
                                        </tr>
                                        <tr>
                                            <th>تاريخ الانتهاء</th>
                                            <td>{{ $verification->expiry_date }}</td>
                                        </tr>
                                        <tr>
                                            <th>العنوان</th>
                                            <td>{{ $verification->address }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">المستندات المرفقة</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header p-2">
                                            <h6 class="mb-0">صورة الهوية (الوجه الأمامي)</h6>
                                        </div>
                                        <div class="card-body p-2 text-center">
                                            @if($verification->id_front_path)
                                                <a href="{{ route('admin.kyc.view-document', [$verification->id, 'id_front']) }}" target="_blank">
                                                    <img src="{{ route('admin.kyc.view-document', [$verification->id, 'id_front']) }}" class="img-fluid mb-2 border" style="max-height: 150px;">
                                                </a>
                                                <div>
                                                    <a href="{{ route('admin.kyc.view-document', [$verification->id, 'id_front']) }}" class="btn btn-sm btn-info" target="_blank">
                                                        <i class="fas fa-search-plus me-1"></i> تكبير
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-danger">غير متوفر</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header p-2">
                                            <h6 class="mb-0">صورة الهوية (الوجه الخلفي)</h6>
                                        </div>
                                        <div class="card-body p-2 text-center">
                                            @if($verification->id_back_path)
                                                <a href="{{ route('admin.kyc.view-document', [$verification->id, 'id_back']) }}" target="_blank">
                                                    <img src="{{ route('admin.kyc.view-document', [$verification->id, 'id_back']) }}" class="img-fluid mb-2 border" style="max-height: 150px;">
                                                </a>
                                                <div>
                                                    <a href="{{ route('admin.kyc.view-document', [$verification->id, 'id_back']) }}" class="btn btn-sm btn-info" target="_blank">
                                                        <i class="fas fa-search-plus me-1"></i> تكبير
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-danger">غير متوفر</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header p-2">
                                            <h6 class="mb-0">صورة شخصية (سيلفي)</h6>
                                        </div>
                                        <div class="card-body p-2 text-center">
                                            @if($verification->selfie_path)
                                                <a href="{{ route('admin.kyc.view-document', [$verification->id, 'selfie']) }}" target="_blank">
                                                    <img src="{{ route('admin.kyc.view-document', [$verification->id, 'selfie']) }}" class="img-fluid mb-2 border" style="max-height: 150px;">
                                                </a>
                                                <div>
                                                    <a href="{{ route('admin.kyc.view-document', [$verification->id, 'selfie']) }}" class="btn btn-sm btn-info" target="_blank">
                                                        <i class="fas fa-search-plus me-1"></i> تكبير
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-danger">غير متوفر</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header p-2">
                                            <h6 class="mb-0">مستند إضافي</h6>
                                        </div>
                                        <div class="card-body p-2 text-center">
                                            @if($verification->additional_document_path)
                                                <a href="{{ route('admin.kyc.view-document', [$verification->id, 'additional_document']) }}" target="_blank">
                                                    <img src="{{ route('admin.kyc.view-document', [$verification->id, 'additional_document']) }}" class="img-fluid mb-2 border" style="max-height: 150px;">
                                                </a>
                                                <div>
                                                    <a href="{{ route('admin.kyc.view-document', [$verification->id, 'additional_document']) }}" class="btn btn-sm btn-info" target="_blank">
                                                        <i class="fas fa-search-plus me-1"></i> تكبير
                                                    </a>
                                                </div>
                                            @else
                                                <div class="text-secondary">لم يتم تقديم مستند إضافي</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 text-center mt-3">
                                    <a href="{{ route('admin.kyc.download-documents', $verification->id) }}" class="btn btn-success">
                                        <i class="fas fa-download me-1"></i> تنزيل جميع المستندات
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row">
                        <div class="col-12">
                            <h6 class="mb-3">تحديث حالة الطلب</h6>
                            <form action="{{ route('admin.kyc.updateStatus', $verification->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">القرار</label>
                                    <div class="d-flex">
                                        <div class="form-check me-4">
                                            <input class="form-check-input" type="radio" name="status" id="status-approved" value="approved" required>
                                            <label class="form-check-label" for="status-approved">
                                                <span class="text-success"><i class="fas fa-check-circle me-1"></i> موافقة</span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="status-rejected" value="rejected">
                                            <label class="form-check-label" for="status-rejected">
                                                <span class="text-danger"><i class="fas fa-times-circle me-1"></i> رفض</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="rejection-section" class="mb-3" style="display: none;">
                                    <label class="form-label">سبب الرفض</label>
                                    <select class="form-select" name="rejection_reason" id="rejection-reason">
                                        <option value="">-- اختر سبب الرفض --</option>
                                        <option value="المستندات غير واضحة">المستندات غير واضحة</option>
                                        <option value="المعلومات غير مطابقة">المعلومات غير مطابقة للهوية</option>
                                        <option value="المستندات منتهية الصلاحية">المستندات منتهية الصلاحية</option>
                                        <option value="مستندات غير مكتملة">مستندات غير مكتملة</option>
                                        <option value="صورة شخصية غير مطابقة">صورة شخصية غير مطابقة للهوية</option>
                                        <option value="مستندات مزورة">مستندات يشتبه في كونها مزورة</option>
                                        <option value="أخرى">سبب آخر...</option>
                                    </select>
                                </div>
                                
                                <div id="other-reason-section" class="mb-3" style="display: none;">
                                    <label class="form-label">سبب الرفض (آخر)</label>
                                    <input type="text" name="other_reason" class="form-control" placeholder="أدخل سبب الرفض بالتفصيل">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">ملاحظات إدارية (اختياري)</label>
                                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="أدخل أي ملاحظات إضافية (لن تظهر للمستخدم)"></textarea>
                                </div>
                                
                                <div class="text-center">
                                    <button type="submit" class="btn btn-lg btn-primary">
                                        <i class="fas fa-save me-1"></i> حفظ القرار
                                    </button>
                                </div>
                            </form>
                        </div>
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
        const statusRadios = document.querySelectorAll('input[name="status"]');
        const rejectionSection = document.getElementById('rejection-section');
        const rejectionReason = document.getElementById('rejection-reason');
        const otherReasonSection = document.getElementById('other-reason-section');
        
        // عند تغيير حالة الطلب
        statusRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'rejected') {
                    rejectionSection.style.display = 'block';
                    rejectionReason.setAttribute('required', 'required');
                } else {
                    rejectionSection.style.display = 'none';
                    rejectionReason.removeAttribute('required');
                    otherReasonSection.style.display = 'none';
                }
            });
        });
        
        // عند تغيير سبب الرفض
        rejectionReason.addEventListener('change', function() {
            if (this.value === 'أخرى') {
                otherReasonSection.style.display = 'block';
            } else {
                otherReasonSection.style.display = 'none';
            }
        });
    });
</script>
@endpush
