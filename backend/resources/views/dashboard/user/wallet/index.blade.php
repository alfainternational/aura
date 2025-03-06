@extends('layouts.dashboard')

@section('title', 'المحفظة الإلكترونية')

@section('page-title', 'المحفظة الإلكترونية')

@section('content')
<div class="container-fluid py-4">
    <!-- رسائل النجاح والخطأ -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- بطاقة الرصيد -->
        <div class="col-lg-4 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <div class="text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-wallet2 fs-1 text-primary"></i>
                    </div>
                    <h5 class="mb-1">رصيد المحفظة</h5>
                    <h2 class="mb-3">{{ number_format($wallet->balance, 2) }} {{ $wallet->currency }}</h2>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#depositModal">
                            <i class="bi bi-plus-circle me-1"></i> إيداع
                        </button>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                            <i class="bi bi-dash-circle me-1"></i> سحب
                        </button>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- إحصائيات المحفظة -->
        <div class="col-lg-8 mb-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <h5 class="mb-0">إحصائيات المحفظة</h5>
                </x-slot>

                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <div class="mb-2">
                                <i class="bi bi-arrow-down-circle fs-3 text-success"></i>
                            </div>
                            <h6 class="text-muted mb-1">إجمالي الإيداعات</h6>
                            <h4>{{ number_format($stats['total_deposits'] ?? 0, 2) }} {{ $wallet->currency }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <div class="mb-2">
                                <i class="bi bi-arrow-up-circle fs-3 text-danger"></i>
                            </div>
                            <h6 class="text-muted mb-1">إجمالي السحوبات</h6>
                            <h4>{{ number_format($stats['total_withdrawals'] ?? 0, 2) }} {{ $wallet->currency }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <div class="mb-2">
                                <i class="bi bi-hourglass-split fs-3 text-warning"></i>
                            </div>
                            <h6 class="text-muted mb-1">معاملات معلقة</h6>
                            <h4>{{ $stats['pending_transactions'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <div class="row">
        <!-- المعاملات الأخيرة -->
        <div class="col-lg-8 mb-4">
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">آخر المعاملات</h5>
                        <a href="{{ route('dashboard.transactions.index') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                    </div>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">المعرف</th>
                                <th scope="col">النوع</th>
                                <th scope="col">المبلغ</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">التاريخ</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->id }}</td>
                                <td>
                                    @if($transaction->type == 'deposit')
                                    <span class="badge bg-success">إيداع</span>
                                    @elseif($transaction->type == 'withdrawal')
                                    <span class="badge bg-danger">سحب</span>
                                    @elseif($transaction->type == 'transfer')
                                    <span class="badge bg-info">تحويل</span>
                                    @elseif($transaction->type == 'payment')
                                    <span class="badge bg-warning">دفع</span>
                                    @else
                                    <span class="badge bg-secondary">{{ $transaction->type }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($transaction->type, ['deposit', 'refund', 'commission']))
                                    <span class="text-success">+{{ number_format($transaction->amount, 2) }}</span>
                                    @else
                                    <span class="text-danger">-{{ number_format($transaction->amount, 2) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->status == 'completed')
                                    <span class="badge bg-success">مكتملة</span>
                                    @elseif($transaction->status == 'pending')
                                    <span class="badge bg-warning">معلقة</span>
                                    @elseif($transaction->status == 'failed')
                                    <span class="badge bg-danger">فاشلة</span>
                                    @elseif($transaction->status == 'cancelled')
                                    <span class="badge bg-secondary">ملغاة</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('dashboard.transactions.show', $transaction->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($transaction->status == 'pending')
                                    <form action="{{ route('dashboard.transactions.cancel', $transaction->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من إلغاء هذه المعاملة؟')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 text-muted"></i>
                                    <p class="text-muted">لا توجد معاملات حتى الآن</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- الحسابات البنكية -->
        <div class="col-lg-4 mb-4">
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الحسابات البنكية</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addBankAccountModal">
                            <i class="bi bi-plus"></i> إضافة حساب
                        </button>
                    </div>
                </x-slot>

                <div class="bank-accounts-container">
                    @forelse($bankAccounts as $account)
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">{{ $account->bank_name }}</h6>
                                <div>
                                    @if($account->is_primary)
                                    <span class="badge bg-primary">أساسي</span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-muted mb-1">{{ $account->account_holder }}</p>
                            <p class="text-muted mb-1">{{ substr_replace($account->account_number, '****', 4, 8) }}</p>
                            <div class="d-flex justify-content-end mt-3">
                                @if(!$account->is_primary)
                                <form action="{{ route('dashboard.wallet.set-primary-bank-account', $account->id) }}" method="POST" class="me-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        تعيين كأساسي
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('dashboard.wallet.delete-bank-account', $account->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الحساب البنكي؟')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="bi bi-credit-card fs-3 d-block mb-2 text-muted"></i>
                        <p class="text-muted">لم تقم بإضافة أي حسابات بنكية بعد</p>
                    </div>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>
</div>

<!-- Modal: إيداع -->
<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dashboard.wallet.deposit') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="depositModalLabel">إيداع في المحفظة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">المبلغ</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="amount" name="amount" min="1" step="0.01" required>
                            <span class="input-group-text">{{ $wallet->currency }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="bank_account_id" class="form-label">الحساب البنكي</label>
                        <select class="form-select" id="bank_account_id" name="bank_account_id" required>
                            @forelse($bankAccounts as $account)
                            <option value="{{ $account->id }}" {{ $account->is_primary ? 'selected' : '' }}>
                                {{ $account->bank_name }} - {{ substr_replace($account->account_number, '****', 4, 8) }}
                            </option>
                            @empty
                            <option value="" disabled>لا توجد حسابات بنكية</option>
                            @endforelse
                        </select>
                        @if($bankAccounts->isEmpty())
                        <div class="form-text text-danger">يجب إضافة حساب بنكي أولاً</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف (اختياري)</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" {{ $bankAccounts->isEmpty() ? 'disabled' : '' }}>تأكيد الإيداع</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: سحب -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dashboard.wallet.withdraw') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawModalLabel">سحب من المحفظة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="withdraw_amount" class="form-label">المبلغ</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="withdraw_amount" name="amount" min="1" max="{{ $wallet->balance }}" step="0.01" required>
                            <span class="input-group-text">{{ $wallet->currency }}</span>
                        </div>
                        <div class="form-text">الرصيد المتاح: {{ number_format($wallet->balance, 2) }} {{ $wallet->currency }}</div>
                    </div>
                    <div class="mb-3">
                        <label for="withdraw_bank_account_id" class="form-label">الحساب البنكي</label>
                        <select class="form-select" id="withdraw_bank_account_id" name="bank_account_id" required>
                            @forelse($bankAccounts as $account)
                            <option value="{{ $account->id }}" {{ $account->is_primary ? 'selected' : '' }}>
                                {{ $account->bank_name }} - {{ substr_replace($account->account_number, '****', 4, 8) }}
                            </option>
                            @empty
                            <option value="" disabled>لا توجد حسابات بنكية</option>
                            @endforelse
                        </select>
                        @if($bankAccounts->isEmpty())
                        <div class="form-text text-danger">يجب إضافة حساب بنكي أولاً</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="withdraw_description" class="form-label">الوصف (اختياري)</label>
                        <textarea class="form-control" id="withdraw_description" name="description" rows="2"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <small>ملاحظة: سيتم تطبيق رسوم بنسبة 1% على مبلغ السحب.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary" {{ $bankAccounts->isEmpty() || $wallet->balance <= 0 ? 'disabled' : '' }}>تأكيد السحب</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: إضافة حساب بنكي -->
<div class="modal fade" id="addBankAccountModal" tabindex="-1" aria-labelledby="addBankAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dashboard.wallet.add-bank-account') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addBankAccountModalLabel">إضافة حساب بنكي</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">اسم البنك</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="account_holder" class="form-label">اسم صاحب الحساب</label>
                        <input type="text" class="form-control" id="account_holder" name="account_holder" required>
                    </div>
                    <div class="mb-3">
                        <label for="account_number" class="form-label">رقم الحساب</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="branch_code" class="form-label">رمز الفرع (اختياري)</label>
                        <input type="text" class="form-control" id="branch_code" name="branch_code">
                    </div>
                    <div class="mb-3">
                        <label for="iban" class="form-label">رقم الآيبان (اختياري)</label>
                        <input type="text" class="form-control" id="iban" name="iban">
                    </div>
                    <div class="mb-3">
                        <label for="swift_code" class="form-label">رمز السويفت (اختياري)</label>
                        <input type="text" class="form-control" id="swift_code" name="swift_code">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة الحساب</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // تحديث الحد الأقصى للسحب عند فتح النافذة
    $('#withdrawModal').on('show.bs.modal', function() {
        const maxBalance = {{ $wallet->balance }};
        $('#withdraw_amount').attr('max', maxBalance);
    });
</script>
@endsection
