@extends('layouts.admin')

@section('title', 'تفاصيل طلب التحقق من الهوية')

@section('styles')
<style>
    .document-preview {
        max-width: 100%;
        max-height: 300px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
    }
    .document-card {
        transition: all 0.3s;
    }
    .document-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6>تفاصيل طلب التحقق من الهوية</h6>
                        <div>
                            <a href="{{ route('admin.kyc.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-arrow-right"></i> العودة للقائمة
                            </a>
                            <a href="{{ route('admin.kyc.download-documents', $verification->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> تنزيل جميع المستندات
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">معلومات المستخدم</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <th class="text-xs" width="150">الاسم</th>
                                        <td class="text-xs">{{ $verification->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">البريد الإلكتروني</th>
                                        <td class="text-xs">{{ $verification->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">اسم المستخدم</th>
                                        <td class="text-xs">{{ $verification->user->username }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">رقم الهاتف</th>
                                        <td class="text-xs">{{ $verification->phone_number }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">تاريخ التسجيل</th>
                                        <td class="text-xs">{{ $verification->user->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">معلومات الهوية</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <th class="text-xs" width="150">الاسم الكامل</th>
                                        <td class="text-xs">{{ $verification->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">تاريخ الميلاد</th>
                                        <td class="text-xs">{{ $verification->date_of_birth }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">الجنسية</th>
                                        <td class="text-xs">{{ $verification->nationality }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">نوع الهوية</th>
                                        <td class="text-xs">
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
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">رقم الهوية</th>
                                        <td class="text-xs">{{ $verification->id_number }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">معلومات الاتصال</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <th class="text-xs" width="150">العنوان</th>
                                        <td class="text-xs">{{ $verification->address }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">المدينة</th>
                                        <td class="text-xs">{{ $verification->city }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">الدولة</th>
                                        <td class="text-xs">{{ $verification->country }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">الرمز البريدي</th>
                                        <td class="text-xs">{{ $verification->postal_code }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">معلومات الطلب</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tr>
                                        <th class="text-xs" width="150">حالة الطلب</th>
                                        <td class="text-xs">
                                            @if($verification->status === 'pending')
                                                <span class="badge bg-warning">قيد المراجعة</span>
                                            @elseif($verification->status === 'approved')
                                                <span class="badge bg-success">تمت الموافقة</span>
                                            @elseif($verification->status === 'rejected')
                                                <span class="badge bg-danger">مرفوض</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">تاريخ التقديم</th>
                                        <td class="text-xs">{{ $verification->submitted_at ? $verification->submitted_at->format('Y-m-d H:i') : 'غير متوفر' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-xs">تاريخ التحقق</th>
                                        <td class="text-xs">{{ $verification->verified_at ? $verification->verified_at->format('Y-m-d H:i') : 'غير متوفر' }}</td>
                                    </tr>
                                    @if($verification->status === 'rejected')
                                    <tr>
                                        <th class="text-xs">سبب الرفض</th>
                                        <td class="text-xs">{{ $verification->rejection_reason }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th class="text-xs">ملاحظات</th>
                                        <td class="text-xs">{{ $verification->notes ?: 'لا توجد ملاحظات' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">المستندات</h6>
                            <div class="row">
                                <div class="col-md-3 mb-4">
                                    <div class="card document-card h-100">
                                        <div class="card-header p-3 text-center">
                                            <h6 class="mb-0">صورة الهوية (الأمام)</h6>
                                        </div>
                                        <div class="card-body p-3 text-center">
                                            @if($verification->id_front_path)
                                                <img src="{{ route('admin.kyc.view-document', ['id' => $verification->id, 'type' => 'id_front']) }}" class="document-preview" alt="ID Front">
                                                <a href="{{ route('admin.kyc.view-document', ['id' => $verification->id, 'type' => 'id_front']) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-search"></i> عرض بالحجم الكامل
                                                </a>
                                            @else
                                                <div class="alert alert-warning">
                                                    المستند غير متوفر
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-4">
                                    <div class="card document-card h-100">
                                        <div class="card-header p-3 text-center">
                                            <h6 class="mb-0">صورة الهوية (الخلف)</h6>
                                        </div>
                                        <div class="card-body p-3 text-center">
                                            @if($verification->id_back_path)
                                                <img src="{{ route('admin.kyc.view-document', ['id' => $verification->id, 'type' => 'id_back']) }}" class="document-preview" alt="ID Back">
                                                <a href="{{ route('admin.kyc.view-document', ['id' => $verification->id, 'type' => 'id_back']) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-search"></i> عرض بالحجم الكامل
                                                </a>
                                            @else
                                                <div class="alert alert-warning">
                                                    المستند غير متوفر
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-4">
                                    <div class="card document-card h-100">
                                        <div class="card-header p-3 text-center">
                                            <h6 class="mb-0">صورة شخصية (سيلفي)</h6>
                                        </div>
                                        <div class="card-body p-3 text-center">
                                            @if($verification->selfie_path)
                                                <img src="{{ route('admin.kyc.view-document', ['id' => $verification->id, 'type' => 'selfie']) }}" class="document-preview" alt="Selfie">
                                                <a href="{{ route('admin.kyc.view-document', ['id' => $verification->id, 'type' => 'selfie']) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-search"></i> عرض بالحجم الكامل
                                                </a>
                                            @else
                                                <div class="alert alert-warning">
                                                    المستند غير متوفر
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-4">
                                    <div class="card document-card h-100">
                                        <div class="card-header p-3 text-center">
                                            <h6 class="mb-0">مستند إضافي</h6>
                                        </div>
                                        <div class="card-body p-3 text-center">
                                            @if($verification->additional_document_path)
                                                <img src="{{ route('admin.kyc.view-document', ['id' => $verification->id, 'type' => 'additional_document']) }}" class="document-preview" alt="Additional Document">
                                                <a href="{{ route('admin.kyc.view-document', ['id' => $verification->id, 'type' => 'additional_document']) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-search"></i> عرض بالحجم الكامل
                                                </a>
                                            @else
                                                <div class="alert alert-warning">
                                                    المستند غير متوفر
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">تحديث حالة الطلب</h6>
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{ route('admin.kyc.update-status', $verification->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="status" class="form-control-label">الحالة</label>
                                                    <select class="form-control" id="status" name="status" required>
                                                        <option value="pending" {{ $verification->status === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                                        <option value="approved" {{ $verification->status === 'approved' ? 'selected' : '' }}>موافقة</option>
                                                        <option value="rejected" {{ $verification->status === 'rejected' ? 'selected' : '' }}>رفض</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="form-group" id="rejection-reason-group" style="{{ $verification->status !== 'rejected' ? 'display: none;' : '' }}">
                                                    <label for="rejection_reason" class="form-control-label">سبب الرفض</label>
                                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3">{{ $verification->rejection_reason }}</textarea>
                                                    <small class="form-text text-muted">يرجى تقديم سبب واضح للرفض ليتمكن المستخدم من تصحيح المشكلة.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="notes" class="form-control-label">ملاحظات إدارية (للاستخدام الداخلي فقط)</label>
                                                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ $verification->notes }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end mt-4">
                                            <button type="submit" class="btn btn-primary">تحديث الحالة</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.getElementById('status');
        const rejectionReasonGroup = document.getElementById('rejection-reason-group');
        const rejectionReasonInput = document.getElementById('rejection_reason');
        
        statusSelect.addEventListener('change', function() {
            if (this.value === 'rejected') {
                rejectionReasonGroup.style.display = 'block';
                rejectionReasonInput.setAttribute('required', 'required');
            } else {
                rejectionReasonGroup.style.display = 'none';
                rejectionReasonInput.removeAttribute('required');
            }
        });
    });
</script>
@endsection
