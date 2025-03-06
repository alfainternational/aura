@extends('layouts.dashboard')

@section('title', 'تفاصيل المعاملة')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">تفاصيل المعاملة #{{ $transaction->reference_id }}</h2>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-right me-1"></i> العودة للمعاملات
                </a>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="transaction-status text-center py-4 mb-4">
                        @if($transaction->status == 'completed')
                            <div class="mb-3">
                                <span class="status-icon bg-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-check-lg text-white fs-3"></i>
                                </span>
                            </div>
                            <h3 class="text-success mb-1">مكتملة</h3>
                        @elseif($transaction->status == 'pending')
                            <div class="mb-3">
                                <span class="status-icon bg-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-hourglass-split text-white fs-3"></i>
                                </span>
                            </div>
                            <h3 class="text-warning mb-1">قيد المعالجة</h3>
                        @elseif($transaction->status == 'failed')
                            <div class="mb-3">
                                <span class="status-icon bg-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-x-lg text-white fs-3"></i>
                                </span>
                            </div>
                            <h3 class="text-danger mb-1">فشلت</h3>
                        @elseif($transaction->status == 'cancelled')
                            <div class="mb-3">
                                <span class="status-icon bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="bi bi-dash-circle text-white fs-3"></i>
                                </span>
                            </div>
                            <h3 class="text-secondary mb-1">ملغية</h3>
                        @endif
                        <p class="text-muted mb-0">{{ $transaction->description }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <h5 class="border-bottom pb-2 mb-3">معلومات المعاملة</h5>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">نوع المعاملة:</span>
                                    <strong>
                                        @if($transaction->type == 'deposit')
                                            <span class="badge bg-primary">إيداع</span>
                                        @elseif($transaction->type == 'withdrawal')
                                            <span class="badge bg-info">سحب</span>
                                        @elseif($transaction->type == 'transfer')
                                            <span class="badge bg-secondary">تحويل</span>
                                        @elseif($transaction->type == 'payment')
                                            <span class="badge bg-success">دفع</span>
                                        @else
                                            <span class="badge bg-dark">{{ $transaction->type }}</span>
                                        @endif
                                    </strong>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">رقم المرجع:</span>
                                    <strong>{{ $transaction->reference_id }}</strong>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">التاريخ:</span>
                                    <strong>{{ $transaction->created_at->format('Y-m-d h:i A') }}</strong>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">حالة المعاملة:</span>
                                    <strong>
                                        @if($transaction->status == 'completed')
                                            <span class="text-success">مكتملة</span>
                                        @elseif($transaction->status == 'pending')
                                            <span class="text-warning">قيد المعالجة</span>
                                        @elseif($transaction->status == 'failed')
                                            <span class="text-danger">فشلت</span>
                                        @elseif($transaction->status == 'cancelled')
                                            <span class="text-secondary">ملغية</span>
                                        @endif
                                    </strong>
                                </div>
                            </div>
                            
                            @if($transaction->updated_at->ne($transaction->created_at))
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">آخر تحديث:</span>
                                    <strong>{{ $transaction->updated_at->format('Y-m-d h:i A') }}</strong>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="col-lg-6">
                            <h5 class="border-bottom pb-2 mb-3">تفاصيل المبالغ</h5>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">المبلغ:</span>
                                    <strong>{{ number_format($transaction->amount, 2) }} {{ $transaction->wallet->currency }}</strong>
                                </div>
                            </div>
                            @if($transaction->fee > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">الرسوم:</span>
                                    <strong>{{ number_format($transaction->fee, 2) }} {{ $transaction->wallet->currency }}</strong>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">المبلغ الإجمالي:</span>
                                    <strong>{{ number_format($transaction->amount + $transaction->fee, 2) }} {{ $transaction->wallet->currency }}</strong>
                                </div>
                            </div>
                            @endif
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">الرصيد بعد المعاملة:</span>
                                    <strong>{{ number_format($transaction->balance_after, 2) }} {{ $transaction->wallet->currency }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($transaction->metadata['bank_account_id']) || isset($transaction->metadata['bank_details']))
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">معلومات الحساب البنكي</h5>
                            
                            @if(isset($transaction->metadata['bank_account_id']) && is_numeric($transaction->metadata['bank_account_id']))
                                @php
                                    $bankAccount = \App\Models\BankAccount::find($transaction->metadata['bank_account_id']);
                                @endphp
                                
                                @if($bankAccount)
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">اسم البنك:</span>
                                                <strong>{{ $bankAccount->bank_name }}</strong>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">اسم صاحب الحساب:</span>
                                                <strong>{{ $bankAccount->account_name }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">رقم الحساب:</span>
                                                <strong>{{ $bankAccount->account_number }}</strong>
                                            </div>
                                        </div>
                                        @if($bankAccount->iban)
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">رقم الآيبان:</span>
                                                <strong>{{ $bankAccount->iban }}</strong>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif
                            @elseif(isset($transaction->metadata['bank_details']))
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">اسم البنك:</span>
                                                <strong>{{ $transaction->metadata['bank_details']['bank_name'] }}</strong>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">اسم صاحب الحساب:</span>
                                                <strong>{{ $transaction->metadata['bank_details']['account_name'] }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">رقم الحساب:</span>
                                                <strong>{{ $transaction->metadata['bank_details']['account_number'] }}</strong>
                                            </div>
                                        </div>
                                        @if(isset($transaction->metadata['bank_details']['iban']))
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">رقم الآيبان:</span>
                                                <strong>{{ $transaction->metadata['bank_details']['iban'] }}</strong>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($transaction->type == 'deposit' && isset($transaction->metadata['transfer_receipt']))
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="border-bottom pb-2 mb-3">إيصال التحويل</h5>
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $transaction->metadata['transfer_receipt']) }}" alt="إيصال التحويل" class="img-fluid mb-2" style="max-height: 300px;">
                                <div>
                                    <a href="{{ asset('storage/' . $transaction->metadata['transfer_receipt']) }}" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="bi bi-arrows-fullscreen me-1"></i> عرض بالحجم الكامل
                                    </a>
                                    <a href="{{ asset('storage/' . $transaction->metadata['transfer_receipt']) }}" class="btn btn-sm btn-outline-secondary" download>
                                        <i class="bi bi-download me-1"></i> تنزيل
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($transaction->status == 'pending')
                    <div class="row">
                        <div class="col-12">
                            <div class="border-top pt-3 mt-3 d-flex justify-content-end">
                                @if($transaction->type == 'deposit')
                                <form action="{{ route('transactions.cancel', $transaction->id) }}" method="POST" class="me-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('هل أنت متأكد من إلغاء هذه المعاملة؟')">
                                        <i class="bi bi-x-circle me-1"></i> إلغاء المعاملة
                                    </button>
                                </form>
                                @elseif($transaction->type == 'withdrawal')
                                <form action="{{ route('transactions.cancel', $transaction->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('هل أنت متأكد من إلغاء طلب السحب هذا؟ سيتم إعادة المبلغ إلى رصيد محفظتك.')">
                                        <i class="bi bi-x-circle me-1"></i> إلغاء السحب
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- سجل الأحداث للمعاملة -->
            @if(count($transaction->logs ?? []) > 0)
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">سجل الأحداث</h5>
                </div>
                <div class="card-body">
                    <div class="transaction-timeline">
                        @foreach($transaction->logs as $log)
                        <div class="timeline-item pb-3 mb-3 border-bottom">
                            <div class="d-flex">
                                <div class="timeline-icon me-3">
                                    @if($log->action == 'created')
                                        <span class="badge bg-primary rounded-pill"><i class="bi bi-plus-lg"></i></span>
                                    @elseif($log->action == 'status_updated')
                                        <span class="badge bg-info rounded-pill"><i class="bi bi-arrow-repeat"></i></span>
                                    @elseif($log->action == 'completed')
                                        <span class="badge bg-success rounded-pill"><i class="bi bi-check-lg"></i></span>
                                    @elseif($log->action == 'failed')
                                        <span class="badge bg-danger rounded-pill"><i class="bi bi-x-lg"></i></span>
                                    @elseif($log->action == 'cancelled')
                                        <span class="badge bg-secondary rounded-pill"><i class="bi bi-dash-circle"></i></span>
                                    @else
                                        <span class="badge bg-dark rounded-pill"><i class="bi bi-activity"></i></span>
                                    @endif
                                </div>
                                <div class="timeline-content flex-grow-1">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong>{{ $log->description }}</strong>
                                        <small class="text-muted">{{ $log->created_at->format('Y-m-d h:i A') }}</small>
                                    </div>
                                    @if($log->metadata)
                                    <div class="small text-muted">
                                        @if(isset($log->metadata['old_status']) && isset($log->metadata['new_status']))
                                            تم تغيير الحالة من: <span class="badge bg-secondary">{{ $log->metadata['old_status'] }}</span>
                                            إلى: <span class="badge bg-primary">{{ $log->metadata['new_status'] }}</span>
                                        @endif
                                        
                                        @if(isset($log->metadata['note']))
                                            <div class="mt-1">{{ $log->metadata['note'] }}</div>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.status-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    border-radius: 50%;
}
.timeline-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}
</style>
@endsection