@extends('layouts.admin')

@section('title', 'طلبات التحقق - لوحة تحكم المشرف')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">طلبات التحقق (KYC)</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active">طلبات التحقق</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <h4 class="card-title flex-grow-1">قائمة طلبات التحقق</h4>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 col-sm-12 mb-2">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="بحث...">
                                <button class="btn btn-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">كل الحالات</option>
                                <option value="pending">قيد المراجعة</option>
                                <option value="approved">مقبول</option>
                                <option value="rejected">مرفوض</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-2">
                            <select class="form-select" id="idTypeFilter">
                                <option value="">كل أنواع الهوية</option>
                                <option value="national_id">بطاقة هوية وطنية</option>
                                <option value="passport">جواز سفر</option>
                                <option value="driving_license">رخصة قيادة</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="kyc-table" class="table table-centered table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>المستخدم</th>
                                    <th>نوع الهوية</th>
                                    <th>رقم الهوية</th>
                                    <th>تاريخ التقديم</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kycRequests ?? [] as $kyc)
                                <tr>
                                    <td>{{ $kyc->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs me-2">
                                                @if($kyc->user->profile_photo_path)
                                                    <img src="{{ asset('storage/' . $kyc->user->profile_photo_path) }}" alt="" class="rounded-circle avatar-xs">
                                                @else
                                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                                        {{ substr($kyc->user->name ?? 'U', 0, 1) }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <h5 class="font-size-14 mb-0">{{ $kyc->user->name }}</h5>
                                                <p class="text-muted mb-0">{{ $kyc->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
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
                                    <td>{{ $kyc->id_number }}</td>
                                    <td>{{ $kyc->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        @if($kyc->status == 'pending')
                                            <span class="badge bg-warning">قيد المراجعة</span>
                                        @elseif($kyc->status == 'approved')
                                            <span class="badge bg-success">مقبول</span>
                                        @elseif($kyc->status == 'rejected')
                                            <span class="badge bg-danger">مرفوض</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $kyc->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('admin.kyc.show', $kyc->id) }}"><i class="fas fa-eye me-2"></i>عرض التفاصيل</a></li>
                                                @if($kyc->status == 'pending')
                                                    <li><a class="dropdown-item text-success" href="#" onclick="approveKyc({{ $kyc->id }})"><i class="fas fa-check-circle me-2"></i>قبول</a></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="rejectKyc({{ $kyc->id }})"><i class="fas fa-times-circle me-2"></i>رفض</a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد طلبات تحقق</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-sm-6">
                            <div>
                                <p class="mb-sm-0">عرض {{ $kycRequests->firstItem() ?? 0 }} إلى {{ $kycRequests->lastItem() ?? 0 }} من {{ $kycRequests->total() ?? 0 }} طلب</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="float-sm-end">
                                {{ $kycRequests->links() }}
                            </div>
                        </div>
                    </div>
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
                <form id="approveForm" method="POST" action="">
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
            <form id="rejectForm" method="POST" action="">
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

@section('scripts')
<script>
    // تصفية طلبات التحقق
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterKycRequests();
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        filterKycRequests();
    });

    document.getElementById('idTypeFilter').addEventListener('change', function() {
        filterKycRequests();
    });

    function filterKycRequests() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const idTypeFilter = document.getElementById('idTypeFilter').value;
        
        const rows = document.querySelectorAll('#kyc-table tbody tr');
        
        rows.forEach(row => {
            const userName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const userEmail = row.querySelector('td:nth-child(2) p').textContent.toLowerCase();
            const idType = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const idNumber = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            const status = row.querySelector('td:nth-child(6) .badge').textContent.toLowerCase();
            
            const matchesSearch = userName.includes(searchTerm) || userEmail.includes(searchTerm) || idNumber.includes(searchTerm);
            const matchesStatus = !statusFilter || status.includes(statusFilter === 'pending' ? 'قيد المراجعة' : (statusFilter === 'approved' ? 'مقبول' : (statusFilter === 'rejected' ? 'مرفوض' : '')));
            const matchesIdType = !idTypeFilter || idType.includes(idTypeFilter === 'national_id' ? 'بطاقة هوية وطنية' : (idTypeFilter === 'passport' ? 'جواز سفر' : (idTypeFilter === 'driving_license' ? 'رخصة قيادة' : '')));
            
            if (matchesSearch && matchesStatus && matchesIdType) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // قبول طلب التحقق
    function approveKyc(kycId) {
        if (!kycId) return;
        
        const approveForm = document.getElementById('approveForm');
        approveForm.action = `/admin/kyc/${kycId}/approve`;
        
        const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
        approveModal.show();
    }

    // رفض طلب التحقق
    function rejectKyc(kycId) {
        if (!kycId) return;
        
        const rejectForm = document.getElementById('rejectForm');
        rejectForm.action = `/admin/kyc/${kycId}/reject`;
        
        const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
        rejectModal.show();
    }
</script>
@endsection
