@extends('layouts.dashboard')

@section('title', 'الحسابات البنكية')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h4 mb-0">الحسابات البنكية</h2>
                <a href="{{ route('wallet.bank-accounts.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> إضافة حساب بنكي
                </a>
            </div>
            <p class="text-muted">إدارة حساباتك البنكية المستخدمة في عمليات السحب والإيداع.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        @if(count($bankAccounts) > 0)
            @foreach($bankAccounts as $account)
                <div class="col-lg-6 mb-4">
                    <x-card class="h-100 border-0 shadow-sm {{ $account->is_primary ? 'border-primary border-2' : '' }}">
                        <div class="position-relative">
                            @if($account->is_primary)
                                <div class="position-absolute top-0 end-0">
                                    <span class="badge bg-primary mb-2">افتراضي</span>
                                </div>
                            @endif
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="bank-logo me-3">
                                    <i class="bi bi-bank fs-1 text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-1">{{ $account->bank_name }}</h5>
                                    <p class="text-muted mb-0">{{ $account->account_name }}</p>
                                </div>
                            </div>

                            <div class="bank-account-details mb-3">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">رقم الحساب</small>
                                        <span class="font-monospace">
                                            •••• •••• {{ substr($account->account_number, -4) }}
                                        </span>
                                    </div>
                                    
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">العملة</small>
                                        <span>{{ $account->currency->code }}</span>
                                    </div>
                                    
                                    @if($account->iban)
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">IBAN</small>
                                        <span class="font-monospace">{{ substr($account->iban, 0, 4) }} •••• •••• {{ substr($account->iban, -4) }}</span>
                                    </div>
                                    @endif
                                    
                                    @if($account->swift_code)
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">رمز SWIFT</small>
                                        <span class="font-monospace">{{ $account->swift_code }}</span>
                                    </div>
                                    @endif
                                    
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">الدولة</small>
                                        <span>{{ $account->country->name }}</span>
                                    </div>
                                    
                                    @if($account->city)
                                    <div class="col-md-6 mb-2">
                                        <small class="text-muted d-block">المدينة</small>
                                        <span>{{ $account->city->name }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="bank-account-status mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($account->verification_status === 'verified')
                                            <span class="badge bg-success">تم التحقق</span>
                                        @elseif($account->verification_status === 'pending')
                                            <span class="badge bg-warning text-dark">قيد المراجعة</span>
                                        @else
                                            <span class="badge bg-secondary">غير متحقق</span>
                                        @endif
                                    </div>
                                    <div>
                                        <small class="text-muted">تم الإضافة: {{ $account->created_at->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                @if(!$account->is_primary)
                                    <form action="{{ route('wallet.bank-accounts.set-primary', $account->id) }}" method="POST" class="me-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            تعيين كافتراضي
                                        </button>
                                    </form>
                                @endif
                                
                                <div class="dropdown ms-2">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton{{ $account->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $account->id }}">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('wallet.bank-accounts.edit', $account->id) }}">
                                                <i class="bi bi-pencil me-2"></i> تعديل
                                            </a>
                                        </li>
                                        @if($account->verification_status !== 'verified')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('wallet.bank-accounts.verify', $account->id) }}">
                                                    <i class="bi bi-check-circle me-2"></i> تأكيد الحساب
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a class="dropdown-item text-danger btn-delete" href="#" 
                                               data-bs-toggle="modal" 
                                               data-bs-target="#deleteModal" 
                                               data-account-id="{{ $account->id }}"
                                               data-account-name="{{ $account->bank_name }} - {{ substr($account->account_number, -4) }}">
                                                <i class="bi bi-trash me-2"></i> حذف
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </x-card>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <x-card class="border-0 shadow-sm text-center py-5">
                    <div class="py-4">
                        <i class="bi bi-bank text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3">لا توجد حسابات بنكية</h4>
                        <p class="text-muted">لم تقم بإضافة أي حسابات بنكية بعد. أضف حسابك البنكي لتسهيل عمليات السحب.</p>
                        <a href="{{ route('wallet.bank-accounts.create') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-lg me-1"></i> إضافة حساب بنكي
                        </a>
                    </div>
                </x-card>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد حذف الحساب البنكي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في حذف الحساب البنكي: <span id="accountName"></span>؟</p>
                <p class="text-danger">لا يمكن التراجع عن هذا الإجراء.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle delete confirmation
        $('.btn-delete').click(function() {
            const accountId = $(this).data('account-id');
            const accountName = $(this).data('account-name');
            
            $('#accountName').text(accountName);
            $('#deleteForm').attr('action', `/dashboard/wallet/bank-accounts/${accountId}`);
        });
    });
</script>
@endpush
@endsection
