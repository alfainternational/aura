@extends('layouts.dashboard')

@section('title', 'المعاملات')

@section('page-title', 'المعاملات')

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
        <div class="col-12 mb-4">
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">فلترة المعاملات</h5>
                        <a href="{{ route('dashboard.transactions.export') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-file-excel me-1"></i> تصدير
                        </a>
                    </div>
                </x-slot>

                <form action="{{ route('dashboard.transactions.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="type" class="form-label">نوع المعاملة</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">الكل</option>
                                <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>إيداع</option>
                                <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>سحب</option>
                                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>تحويل</option>
                                <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>دفع</option>
                                <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>استرداد</option>
                                <option value="commission" {{ request('type') == 'commission' ? 'selected' : '' }}>عمولة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">الكل</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i> بحث
                            </button>
                            <a href="{{ route('dashboard.transactions.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> إعادة ضبط
                            </a>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="col-12">
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <h5 class="mb-0">قائمة المعاملات</h5>
                </x-slot>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">المعرف</th>
                                <th scope="col">النوع</th>
                                <th scope="col">المبلغ</th>
                                <th scope="col">الرسوم</th>
                                <th scope="col">الرصيد بعد</th>
                                <th scope="col">الحالة</th>
                                <th scope="col">التاريخ</th>
                                <th scope="col">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
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
                                    @elseif($transaction->type == 'refund')
                                    <span class="badge bg-success">استرداد</span>
                                    @elseif($transaction->type == 'commission')
                                    <span class="badge bg-primary">عمولة</span>
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
                                <td>{{ number_format($transaction->fee, 2) }}</td>
                                <td>{{ number_format($transaction->balance_after, 2) }}</td>
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
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 text-muted"></i>
                                    <p class="text-muted">لا توجد معاملات متطابقة مع معايير البحث</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
