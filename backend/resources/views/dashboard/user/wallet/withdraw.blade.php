@extends('layouts.dashboard')

@section('title', 'سحب من المحفظة')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">سحب مبلغ من المحفظة</h5>
                        <a href="{{ route('wallet.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right me-1"></i> العودة للمحفظة
                        </a>
                    </div>
                </x-slot>

                <div class="alert alert-info mb-4" role="alert">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="bi bi-info-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="alert-heading">معلومات السحب</h5>
                            <p class="mb-0">
                                يمكنك سحب مبلغ من محفظتك إلى حسابك البنكي المسجل.
                                سيتم معالجة طلب السحب خلال 1-3 أيام عمل.
                                الحد الأدنى للسحب هو {{ $minWithdrawalAmount }} {{ $defaultCurrency->code }}.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="wallet-balance-box mb-4 p-3 border rounded bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5">الرصيد المتاح للسحب:</span>
                        <span class="fs-4 fw-bold text-primary">{{ number_format($wallet->balance, 2) }} {{ $defaultCurrency->code }}</span>
                    </div>
                </div>

                <form action="{{ route('wallet.withdraw.store') }}" method="POST" id="withdraw-form">
                    @csrf

                    <div class="mb-3">
                        <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" min="{{ $minWithdrawalAmount }}" max="{{ $wallet->balance }}" step="0.01" value="{{ old('amount') }}" required>
                            <span class="input-group-text">{{ $defaultCurrency->code }}</span>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">
                            الحد الأدنى للسحب: {{ $minWithdrawalAmount }} {{ $defaultCurrency->code }}
                            @if($withdrawalFee > 0)
                                | رسوم السحب: {{ $withdrawalFee }}{{ $withdrawalFeeType == 'percentage' ? '%' : ' ' . $defaultCurrency->code }}
                            @endif
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="bank_account_id" class="form-label">الحساب البنكي <span class="text-danger">*</span></label>
                        <select class="form-select @error('bank_account_id') is-invalid @enderror" id="bank_account_id" name="bank_account_id" required>
                            <option value="">اختر الحساب البنكي</option>
                            @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>{{ $account->bank_name }} - {{ substr($account->account_number, -4) }}</option>
                            @endforeach
                            <option value="new">إضافة حساب بنكي جديد</option>
                        </select>
                        @error('bank_account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="new-bank-account-fields" class="d-none">
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">اسم البنك <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name') }}">
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="account_name" class="form-label">اسم صاحب الحساب <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('account_name') is-invalid @enderror" id="account_name" name="account_name" value="{{ old('account_name') }}">
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="account_number" class="form-label">رقم الحساب <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number" value="{{ old('account_number') }}">
                            @error('account_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="iban" class="form-label">رقم IBAN</label>
                            <input type="text" class="form-control @error('iban') is-invalid @enderror" id="iban" name="iban" value="{{ old('iban') }}">
                            @error('iban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="swift_code" class="form-label">رمز SWIFT</label>
                            <input type="text" class="form-control @error('swift_code') is-invalid @enderror" id="swift_code" name="swift_code" value="{{ old('swift_code') }}">
                            @error('swift_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="bank_address" class="form-label">عنوان البنك</label>
                            <input type="text" class="form-control @error('bank_address') is-invalid @enderror" id="bank_address" name="bank_address" value="{{ old('bank_address') }}">
                            @error('bank_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('save_bank_account') is-invalid @enderror" type="checkbox" id="save_bank_account" name="save_bank_account" value="1" {{ old('save_bank_account') ? 'checked' : '' }}>
                                <label class="form-check-label" for="save_bank_account">
                                    حفظ معلومات الحساب البنكي للاستخدام مستقبلاً
                                </label>
                                @error('save_bank_account')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="withdrawal-summary" class="d-none mb-4">
                        <h6 class="mb-3">ملخص السحب:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>المبلغ المطلوب:</td>
                                        <td class="text-end fw-bold" id="requested-amount">0.00 {{ $defaultCurrency->code }}</td>
                                    </tr>
                                    <tr>
                                        <td>رسوم السحب:</td>
                                        <td class="text-end text-danger" id="withdrawal-fee">0.00 {{ $defaultCurrency->code }}</td>
                                    </tr>
                                    <tr class="table-light">
                                        <td>إجمالي المبلغ المسحوب من الرصيد:</td>
                                        <td class="text-end fw-bold" id="total-amount">0.00 {{ $defaultCurrency->code }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back();">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تأكيد السحب</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // التعامل مع إضافة حساب بنكي جديد
        $('#bank_account_id').change(function() {
            if ($(this).val() === 'new') {
                $('#new-bank-account-fields').removeClass('d-none');
            } else {
                $('#new-bank-account-fields').addClass('d-none');
            }
        });

        // حساب رسوم السحب وتحديث ملخص السحب
        $('#amount').on('input', function() {
            const amount = parseFloat($(this).val()) || 0;
            const withdrawalFeeType = '{{ $withdrawalFeeType }}';
            const withdrawalFee = parseFloat('{{ $withdrawalFee }}');
            let feeAmount = 0;
            
            if (withdrawalFeeType === 'percentage') {
                feeAmount = amount * (withdrawalFee / 100);
            } else {
                feeAmount = withdrawalFee;
            }
            
            const totalAmount = amount + feeAmount;
            
            // تحديث ملخص السحب
            $('#requested-amount').text(amount.toFixed(2) + ' {{ $defaultCurrency->code }}');
            $('#withdrawal-fee').text(feeAmount.toFixed(2) + ' {{ $defaultCurrency->code }}');
            $('#total-amount').text(totalAmount.toFixed(2) + ' {{ $defaultCurrency->code }}');
            
            if (amount > 0) {
                $('#withdrawal-summary').removeClass('d-none');
            } else {
                $('#withdrawal-summary').addClass('d-none');
            }
        });

        // التحقق من صحة النموذج عند التقديم
        $('#withdraw-form').submit(function(e) {
            const amount = parseFloat($('#amount').val()) || 0;
            const minAmount = parseFloat('{{ $minWithdrawalAmount }}');
            const maxAmount = parseFloat('{{ $wallet->balance }}');
            const bankAccountId = $('#bank_account_id').val();
            
            if (amount < minAmount) {
                e.preventDefault();
                alert('الحد الأدنى للسحب هو ' + minAmount + ' {{ $defaultCurrency->code }}');
                return false;
            }
            
            if (amount > maxAmount) {
                e.preventDefault();
                alert('المبلغ المطلوب أكبر من الرصيد المتاح');
                return false;
            }
            
            if (bankAccountId === 'new') {
                const bankName = $('#bank_name').val();
                const accountName = $('#account_name').val();
                const accountNumber = $('#account_number').val();
                
                if (!bankName) {
                    e.preventDefault();
                    alert('يرجى إدخال اسم البنك');
                    return false;
                }
                
                if (!accountName) {
                    e.preventDefault();
                    alert('يرجى إدخال اسم صاحب الحساب');
                    return false;
                }
                
                if (!accountNumber) {
                    e.preventDefault();
                    alert('يرجى إدخال رقم الحساب');
                    return false;
                }
            } else if (!bankAccountId || bankAccountId === '') {
                e.preventDefault();
                alert('يرجى اختيار حساب بنكي');
                return false;
            }
        });
    });
</script>
@endpush
@endsection
