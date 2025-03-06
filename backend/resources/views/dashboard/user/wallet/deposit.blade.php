@extends('layouts.dashboard')

@section('title', 'إيداع في المحفظة')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">إيداع مبلغ في المحفظة</h5>
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
                            <h5 class="alert-heading">معلومات الإيداع</h5>
                            <p class="mb-0">
                                يمكنك إيداع مبلغ في محفظتك من خلال التحويل البنكي أو بطاقة الائتمان.
                                بعد تأكيد الإيداع، سيتم إضافة المبلغ إلى رصيد محفظتك خلال 24 ساعة عمل.
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('wallet.deposit.store') }}" method="POST" id="deposit-form">
                    @csrf

                    <div class="mb-3">
                        <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" min="10" step="0.01" value="{{ old('amount') }}" required>
                            <span class="input-group-text">{{ $defaultCurrency->code }}</span>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">الحد الأدنى للإيداع: 10 {{ $defaultCurrency->code }}</small>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                            <option value="">اختر طريقة الدفع</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" data-type="{{ $method->type }}" {{ old('payment_method') == $method->id ? 'selected' : '' }}>{{ $method->name }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- حقول بطاقة الائتمان -->
                    <div id="credit-card-fields" class="payment-fields d-none">
                        <div class="mb-3">
                            <label for="card_number" class="form-label">رقم البطاقة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('card_number') is-invalid @enderror" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX">
                            @error('card_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="card_expiry" class="form-label">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('card_expiry') is-invalid @enderror" id="card_expiry" name="card_expiry" placeholder="MM/YY">
                                @error('card_expiry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="card_cvv" class="form-label">رمز التحقق CVV <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('card_cvv') is-invalid @enderror" id="card_cvv" name="card_cvv" placeholder="XXX">
                                @error('card_cvv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="card_holder" class="form-label">اسم حامل البطاقة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('card_holder') is-invalid @enderror" id="card_holder" name="card_holder">
                            @error('card_holder')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- حقول التحويل البنكي -->
                    <div id="bank-transfer-fields" class="payment-fields d-none">
                        <div class="alert alert-success mb-4">
                            <h5 class="alert-heading">معلومات الحساب البنكي</h5>
                            <p>يرجى استخدام المعلومات التالية لإجراء التحويل البنكي:</p>
                            <ul class="mb-0">
                                <li>اسم البنك: {{ $companyBankInfo->bank_name }}</li>
                                <li>اسم الحساب: {{ $companyBankInfo->account_name }}</li>
                                <li>رقم الحساب: {{ $companyBankInfo->account_number }}</li>
                                <li>رقم IBAN: {{ $companyBankInfo->iban }}</li>
                                <li>رمز SWIFT: {{ $companyBankInfo->swift_code }}</li>
                            </ul>
                        </div>

                        <div class="mb-3">
                            <label for="bank_account_id" class="form-label">حسابك البنكي (المحول منه) <span class="text-danger">*</span></label>
                            <select class="form-select @error('bank_account_id') is-invalid @enderror" id="bank_account_id" name="bank_account_id">
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
                            <label for="transfer_receipt" class="form-label">إيصال التحويل <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('transfer_receipt') is-invalid @enderror" id="transfer_receipt" name="transfer_receipt">
                            <small class="form-text text-muted">الصيغ المدعومة: JPG, PNG, PDF. الحد الأقصى للحجم: 2MB</small>
                            @error('transfer_receipt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="transfer_reference" class="form-label">رقم مرجع التحويل</label>
                            <input type="text" class="form-control @error('transfer_reference') is-invalid @enderror" id="transfer_reference" name="transfer_reference" value="{{ old('transfer_reference') }}">
                            @error('transfer_reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back();">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تأكيد الإيداع</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // إظهار/إخفاء حقول طرق الدفع بناءً على الاختيار
        $('#payment_method').change(function() {
            const selectedOption = $(this).find('option:selected');
            const paymentType = selectedOption.data('type');
            
            // إخفاء جميع الحقول
            $('.payment-fields').addClass('d-none');
            
            // إظهار الحقول المناسبة
            if (paymentType === 'credit_card') {
                $('#credit-card-fields').removeClass('d-none');
            } else if (paymentType === 'bank_transfer') {
                $('#bank-transfer-fields').removeClass('d-none');
            }
        });

        // التعامل مع إضافة حساب بنكي جديد
        $('#bank_account_id').change(function() {
            if ($(this).val() === 'new') {
                $('#new-bank-account-fields').removeClass('d-none');
            } else {
                $('#new-bank-account-fields').addClass('d-none');
            }
        });

        // تنسيق حقول البطاقة
        $('#card_number').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            let formattedValue = '';
            
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            
            $(this).val(formattedValue);
        });

        $('#card_expiry').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            
            $(this).val(value);
        });

        $('#card_cvv').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            $(this).val(value.substring(0, 4));
        });

        // التحقق من صحة النموذج عند التقديم
        $('#deposit-form').submit(function(e) {
            const paymentMethod = $('#payment_method').find('option:selected').data('type');
            
            if (paymentMethod === 'credit_card') {
                const cardNumber = $('#card_number').val().replace(/\s/g, '');
                const cardExpiry = $('#card_expiry').val();
                const cardCvv = $('#card_cvv').val();
                const cardHolder = $('#card_holder').val();
                
                if (!cardNumber || cardNumber.length < 16) {
                    e.preventDefault();
                    alert('يرجى إدخال رقم بطاقة صحيح');
                    return false;
                }
                
                if (!cardExpiry || cardExpiry.length < 5) {
                    e.preventDefault();
                    alert('يرجى إدخال تاريخ انتهاء صحيح');
                    return false;
                }
                
                if (!cardCvv || cardCvv.length < 3) {
                    e.preventDefault();
                    alert('يرجى إدخال رمز CVV صحيح');
                    return false;
                }
                
                if (!cardHolder) {
                    e.preventDefault();
                    alert('يرجى إدخال اسم حامل البطاقة');
                    return false;
                }
            } else if (paymentMethod === 'bank_transfer') {
                const bankAccountId = $('#bank_account_id').val();
                const transferReceipt = $('#transfer_receipt').val();
                
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
                
                if (!transferReceipt) {
                    e.preventDefault();
                    alert('يرجى إرفاق إيصال التحويل');
                    return false;
                }
            }
        });
    });
</script>
@endpush
@endsection
