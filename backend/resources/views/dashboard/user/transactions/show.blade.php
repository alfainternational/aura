@extends('layouts.dashboard')

@section('title', 'تفاصيل المعاملة')

@section('page-title', 'تفاصيل المعاملة')

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
        <div class="col-lg-8 mx-auto">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">معاملة #{{ $transaction->id }}</h5>
                    <div>
                        <a href="{{ route('dashboard.transactions.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-left me-1"></i> العودة للقائمة
                        </a>
                        @if($transaction->status == 'pending')
                        <form action="{{ route('dashboard.transactions.cancel', $transaction->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('هل أنت متأكد من إلغاء هذه المعاملة؟')">
                                <i class="bi bi-x-circle me-1"></i> إلغاء المعاملة
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="transaction-status text-center mb-4">
                        @if($transaction->status == 'completed')
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success fs-1"></i>
                        </div>
                        <h4 class="text-success">تمت المعاملة بنجاح</h4>
                        @elseif($transaction->status == 'pending')
                        <div class="mb-3">
                            <i class="bi bi-hourglass-split text-warning fs-1"></i>
                        </div>
                        <h4 class="text-warning">المعاملة قيد المعالجة</h4>
                        @elseif($transaction->status == 'failed')
                        <div class="mb-3">
                            <i class="bi bi-x-circle-fill text-danger fs-1"></i>
                        </div>
                        <h4 class="text-danger">فشلت المعاملة</h4>
                        @elseif($transaction->status == 'cancelled')
                        <div class="mb-3">
                            <i class="bi bi-slash-circle-fill text-secondary fs-1"></i>
                        </div>
                        <h4 class="text-secondary">تم إلغاء المعاملة</h4>
                        @endif
                        <p class="text-muted">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">نوع المعاملة</h6>
                                <p class="fs-5">
                                    @if($transaction->type == 'deposit')
                                    <span class="badge bg-success">إيداع</span>
                                    @elseif($transaction->type == 'withdrawal')
                                    <span class="badge bg-danger">سحب</span>
                                    @elseif($transaction->type == 'transfer')
                                    <span class="badge bg-info">تحويل</span>
                                    @elseif($transaction->type == 'payment')
                                    <span class="badge bg-warning">دفع</span>
                                    @elseif($transaction->type == 'refund')
                                    <span class="badge bg-success">استرداد</span>
                                    @elseif($transaction->type == 'commission')
                                    <span class="badge bg-primary">عمولة</span>
                                    @else
                                    <span class="badge bg-secondary">{{ $transaction->type }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">المبلغ</h6>
                                <p class="fs-5">
                                    @if(in_array($transaction->type, ['deposit', 'refund', 'commission']))
                                    <span class="text-success">+{{ number_format($transaction->amount, 2) }} {{ $transaction->wallet->currency }}</span>
                                    @else
                                    <span class="text-danger">-{{ number_format($transaction->amount, 2) }} {{ $transaction->wallet->currency }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">الرسوم</h6>
                                <p>{{ number_format($transaction->fee, 2) }} {{ $transaction->wallet->currency }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">الرصيد بعد المعاملة</h6>
                                <p>{{ number_format($transaction->balance_after, 2) }} {{ $transaction->wallet->currency }}</p>
                            </div>
                        </div>
                    </div>

                    @if($transaction->description)
                    <div class="mb-4">
                        <h6 class="text-muted mb-1">الوصف</h6>
                        <p>{{ $transaction->description }}</p>
                    </div>
                    @endif

                    @if($transaction->reference_id)
                    <div class="mb-4">
                        <h6 class="text-muted mb-1">رقم المرجع</h6>
                        <p>{{ $transaction->reference_id }}</p>
                    </div>
                    @endif

                    @if($transaction->meta_data)
                    <div class="mb-4">
                        <h6 class="text-muted mb-1">معلومات إضافية</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <pre class="mb-0">{{ json_encode($transaction->meta_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($transaction->bank_account)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">معلومات الحساب البنكي</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-1"><strong>البنك:</strong> {{ $transaction->bank_account->bank->name }}</p>
                                <p class="mb-1"><strong>صاحب الحساب:</strong> {{ $transaction->bank_account->account_name }}</p>
                                <p class="mb-1"><strong>رقم الحساب:</strong> {{ substr_replace($transaction->bank_account->account_number, '****', 4, 8) }}</p>
                                @if($transaction->bank_account->meta_data && isset($transaction->bank_account->meta_data['iban']))
                                <p class="mb-1"><strong>رقم الآيبان:</strong> {{ substr_replace($transaction->bank_account->meta_data['iban'], '****', 8, 12) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="transaction-timeline mt-5">
                        <h6 class="text-muted mb-3">سجل المعاملة</h6>
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">تم إنشاء المعاملة</h6>
                                    <p class="text-muted small">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>

                            @if($transaction->status == 'completed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">تم اكتمال المعاملة</h6>
                                    <p class="text-muted small">{{ $transaction->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                            @elseif($transaction->status == 'failed')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">فشلت المعاملة</h6>
                                    <p class="text-muted small">{{ $transaction->updated_at->format('Y-m-d H:i:s') }}</p>
                                    @if($transaction->meta_data && isset($transaction->meta_data['error_message']))
                                    <p class="text-danger">{{ $transaction->meta_data['error_message'] }}</p>
                                    @endif
                                </div>
                            </div>
                            @elseif($transaction->status == 'cancelled')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-secondary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-0">تم إلغاء المعاملة</h6>
                                    <p class="text-muted small">{{ $transaction->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 1.5rem;
        margin-left: 1rem;
    }

    .timeline:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 2px;
        background-color: #e9ecef;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }

    .timeline-marker {
        position: absolute;
        left: -1.5rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #e9ecef;
    }

    .timeline-content {
        padding-left: 0.5rem;
    }
</style>
@endsection
