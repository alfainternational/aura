@extends('layouts.admin')

@section('title', 'تحليل الذكاء الاصطناعي للتحقق من الهوية')

@section('content')
<div class="container-fluid">
    <!-- بطاقة تفاصيل المستخدم -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-user-check me-2"></i> تحليل الذكاء الاصطناعي للتحقق من الهوية
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold">معلومات المستخدم</h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="150">اسم المستخدم:</th>
                            <td>{{ $verification->user->name }}</td>
                        </tr>
                        <tr>
                            <th>البريد الإلكتروني:</th>
                            <td>{{ $verification->user->email }}</td>
                        </tr>
                        <tr>
                            <th>رقم الهاتف:</th>
                            <td>{{ $verification->user->phone_number ?? 'غير متوفر' }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ التسجيل:</th>
                            <td>{{ $verification->user->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">معلومات التحقق</h6>
                    <table class="table table-sm">
                        <tr>
                            <th width="150">الاسم الكامل:</th>
                            <td>{{ $verification->full_name }}</td>
                        </tr>
                        <tr>
                            <th>رقم الهوية:</th>
                            <td>{{ $verification->id_number }}</td>
                        </tr>
                        <tr>
                            <th>نوع المستند:</th>
                            <td>{{ $verification->id_type ?? 'بطاقة هوية' }}</td>
                        </tr>
                        <tr>
                            <th>تاريخ التقديم:</th>
                            <td>{{ $verification->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ملخص نتائج التحليل -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">ملخص نتائج التحليل</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 text-center mb-3">
                                    <div class="p-3 rounded 
                                        @if(isset($aiResults['overall_score']) && $aiResults['overall_score'] >= 80)
                                            bg-success-subtle text-success
                                        @elseif(isset($aiResults['overall_score']) && $aiResults['overall_score'] >= 60)
                                            bg-warning-subtle text-warning
                                        @else
                                            bg-danger-subtle text-danger
                                        @endif">
                                        <h3 class="mb-0">{{ $aiResults['overall_score'] ?? 'N/A' }}</h3>
                                        <small>الدرجة الكلية</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="p-3 rounded 
                                        @if(isset($aiResults['document_score']) && $aiResults['document_score'] >= 80)
                                            bg-success-subtle text-success
                                        @elseif(isset($aiResults['document_score']) && $aiResults['document_score'] >= 60)
                                            bg-warning-subtle text-warning
                                        @else
                                            bg-danger-subtle text-danger
                                        @endif">
                                        <h3 class="mb-0">{{ $aiResults['document_score'] ?? 'N/A' }}</h3>
                                        <small>صحة المستند</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="p-3 rounded 
                                        @if(isset($aiResults['face_match_score']) && $aiResults['face_match_score'] >= 80)
                                            bg-success-subtle text-success
                                        @elseif(isset($aiResults['face_match_score']) && $aiResults['face_match_score'] >= 60)
                                            bg-warning-subtle text-warning
                                        @else
                                            bg-danger-subtle text-danger
                                        @endif">
                                        <h3 class="mb-0">{{ $aiResults['face_match_score'] ?? 'N/A' }}</h3>
                                        <small>مطابقة الوجه</small>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center mb-3">
                                    <div class="p-3 rounded 
                                        @if(isset($aiResults['risk_score']) && $aiResults['risk_score'] <= 20)
                                            bg-success-subtle text-success
                                        @elseif(isset($aiResults['risk_score']) && $aiResults['risk_score'] <= 50)
                                            bg-warning-subtle text-warning
                                        @else
                                            bg-danger-subtle text-danger
                                        @endif">
                                        <h3 class="mb-0">{{ $aiResults['risk_score'] ?? 'N/A' }}</h3>
                                        <small>مستوى المخاطر</small>
                                    </div>
                                </div>
                            </div>
                            <div class="alert 
                                @if(isset($aiResults['recommendation']) && $aiResults['recommendation'] == 'approve')
                                    alert-success
                                @elseif(isset($aiResults['recommendation']) && $aiResults['recommendation'] == 'manual_review')
                                    alert-warning
                                @else
                                    alert-danger
                                @endif mt-3">
                                <strong>توصية النظام:</strong> 
                                @if(isset($aiResults['recommendation']))
                                    @if($aiResults['recommendation'] == 'approve')
                                        <span class="badge bg-success">الموافقة على الطلب</span>
                                    @elseif($aiResults['recommendation'] == 'manual_review')
                                        <span class="badge bg-warning">مراجعة يدوية مطلوبة</span>
                                    @else
                                        <span class="badge bg-danger">رفض الطلب</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">غير متوفر</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- تفاصيل التحليل -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">تحليل المستند</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($aiResults['details']['document_analysis']))
                                <ul class="list-group list-group-flush">
                                    @foreach($aiResults['details']['document_analysis'] as $key => $value)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                            @if(is_bool($value))
                                                @if($value)
                                                    <span class="badge bg-success rounded-pill">نعم</span>
                                                @else
                                                    <span class="badge bg-danger rounded-pill">لا</span>
                                                @endif
                                            @else
                                                <span class="badge bg-primary rounded-pill">{{ $value }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="alert alert-info">لا توجد بيانات تحليل للمستند</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">مطابقة الوجه والتحقق من الحيوية</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($aiResults['details']['face_analysis']))
                                <ul class="list-group list-group-flush">
                                    @foreach($aiResults['details']['face_analysis'] as $key => $value)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                            @if(is_bool($value))
                                                @if($value)
                                                    <span class="badge bg-success rounded-pill">نعم</span>
                                                @else
                                                    <span class="badge bg-danger rounded-pill">لا</span>
                                                @endif
                                            @elseif(is_numeric($value))
                                                <span class="badge bg-primary rounded-pill">{{ $value }}%</span>
                                            @else
                                                <span class="badge bg-primary rounded-pill">{{ $value }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="alert alert-info">لا توجد بيانات تحليل للوجه</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">تحليل المخاطر</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($aiResults['details']['risk_analysis']))
                                <ul class="list-group list-group-flush">
                                    @foreach($aiResults['details']['risk_analysis'] as $key => $value)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                            @if(is_bool($value))
                                                @if($value)
                                                    <span class="badge bg-danger rounded-pill">نعم</span>
                                                @else
                                                    <span class="badge bg-success rounded-pill">لا</span>
                                                @endif
                                            @elseif(is_numeric($value) && strpos($key, 'score') !== false)
                                                @if($value <= 20)
                                                    <span class="badge bg-success rounded-pill">{{ $value }}</span>
                                                @elseif($value <= 50)
                                                    <span class="badge bg-warning rounded-pill">{{ $value }}</span>
                                                @else
                                                    <span class="badge bg-danger rounded-pill">{{ $value }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-primary rounded-pill">{{ $value }}</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="alert alert-info">لا توجد بيانات تحليل للمخاطر</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h6 class="mb-0">ملاحظات إضافية</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($aiResults['notes']) && !empty($aiResults['notes']))
                                <div class="alert alert-info">
                                    {{ $aiResults['notes'] }}
                                </div>
                            @else
                                <div class="alert alert-info">لا توجد ملاحظات إضافية</div>
                            @endif

                            @if(isset($aiResults['warnings']) && !empty($aiResults['warnings']))
                                <h6 class="mt-3 text-danger">تحذيرات:</h6>
                                <ul class="list-group">
                                    @foreach($aiResults['warnings'] as $warning)
                                        <li class="list-group-item list-group-item-danger">{{ $warning }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="{{ route('admin.kyc.show', $verification->id) }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> العودة للتفاصيل
                    </a>
                    
                    @if($verification->status == 'pending')
                        <a href="{{ route('admin.kyc.approve', $verification->id) }}" class="btn btn-success me-2" 
                           onclick="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                            <i class="fas fa-check"></i> الموافقة على الطلب
                        </a>
                        
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times"></i> رفض الطلب
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة رفض الطلب -->
@if($verification->status == 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.kyc.reject', $verification->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">رفض طلب التحقق من الهوية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">سبب الرفض</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                        <small class="text-muted">سيتم إرسال هذا السبب للمستخدم</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الرفض</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // يمكن إضافة أي سكربتات إضافية هنا
    });
</script>
@endsection
