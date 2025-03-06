@extends('layouts.dashboard')

@section('title', 'تعديل الحساب البنكي')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">تعديل الحساب البنكي</h5>
                        <a href="{{ route('wallet.bank-accounts.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right me-1"></i> العودة للحسابات البنكية
                        </a>
                    </div>
                </x-slot>

                <form action="{{ route('wallet.bank-accounts.update', $bankAccount->id) }}" method="POST" id="bank-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="bank_name" class="form-label">اسم البنك <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" required>
                        @error('bank_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="account_name" class="form-label">اسم صاحب الحساب <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('account_name') is-invalid @enderror" id="account_name" name="account_name" value="{{ old('account_name', $bankAccount->account_name) }}" required>
                        <small class="form-text text-muted">يجب أن يكون اسم صاحب الحساب مطابقاً للاسم المسجل في البنك</small>
                        @error('account_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="account_number" class="form-label">رقم الحساب <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('account_number') is-invalid @enderror" id="account_number" name="account_number" value="{{ old('account_number', $bankAccount->account_number) }}" required>
                        @error('account_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="iban" class="form-label">رقم IBAN</label>
                        <input type="text" class="form-control @error('iban') is-invalid @enderror" id="iban" name="iban" value="{{ old('iban', $bankAccount->iban) }}">
                        <small class="form-text text-muted">يساعد رقم IBAN في تسريع عمليات التحويل الدولية</small>
                        @error('iban')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="swift_code" class="form-label">رمز SWIFT/BIC</label>
                        <input type="text" class="form-control @error('swift_code') is-invalid @enderror" id="swift_code" name="swift_code" value="{{ old('swift_code', $bankAccount->swift_code) }}">
                        <small class="form-text text-muted">مطلوب للتحويلات الدولية</small>
                        @error('swift_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="branch_name" class="form-label">اسم الفرع</label>
                        <input type="text" class="form-control @error('branch_name') is-invalid @enderror" id="branch_name" name="branch_name" value="{{ old('branch_name', $bankAccount->branch_name) }}">
                        @error('branch_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="branch_code" class="form-label">رمز الفرع</label>
                        <input type="text" class="form-control @error('branch_code') is-invalid @enderror" id="branch_code" name="branch_code" value="{{ old('branch_code', $bankAccount->branch_code) }}">
                        @error('branch_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="bank_address" class="form-label">عنوان البنك</label>
                        <textarea class="form-control @error('bank_address') is-invalid @enderror" id="bank_address" name="bank_address" rows="2">{{ old('bank_address', $bankAccount->bank_address) }}</textarea>
                        @error('bank_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="country_id" class="form-label">الدولة <span class="text-danger">*</span></label>
                        <select class="form-select @error('country_id') is-invalid @enderror" id="country_id" name="country_id" required>
                            <option value="">اختر الدولة</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id', $bankAccount->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('country_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="city_id" class="form-label">المدينة</label>
                        <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id">
                            <option value="">اختر المدينة</option>
                            @if($cities)
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id', $bankAccount->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="currency_id" class="form-label">العملة <span class="text-danger">*</span></label>
                        <select class="form-select @error('currency_id') is-invalid @enderror" id="currency_id" name="currency_id" required>
                            <option value="">اختر العملة</option>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->id }}" {{ old('currency_id', $bankAccount->currency_id) == $currency->id ? 'selected' : '' }}>{{ $currency->name }} ({{ $currency->code }})</option>
                            @endforeach
                        </select>
                        @error('currency_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(!$bankAccount->is_primary)
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input @error('is_primary') is-invalid @enderror" type="checkbox" id="is_primary" name="is_primary" value="1" {{ old('is_primary', $bankAccount->is_primary) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_primary">
                                تعيين كحساب افتراضي
                            </label>
                            @error('is_primary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <small class="form-text text-muted">سيتم استخدام هذا الحساب تلقائياً لعمليات السحب</small>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $bankAccount->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($bankAccount->verification_status !== 'verified')
                    <div class="mb-3">
                        <label for="verification_document" class="form-label">مستند التحقق</label>
                        @if($bankAccount->verification_document)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $bankAccount->verification_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i> عرض المستند الحالي
                                </a>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('verification_document') is-invalid @enderror" id="verification_document" name="verification_document">
                        <small class="form-text text-muted">يرجى إرفاق نسخة من كشف حساب بنكي أو مستند يثبت ملكية الحساب. الصيغ المدعومة: PDF, JPG, PNG. الحد الأقصى للحجم: 2MB</small>
                        @error('verification_document')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back();">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Dynamic Cities based on Country
        $('#country_id').change(function() {
            var countryId = $(this).val();
            if (countryId) {
                $.ajax({
                    url: '/api/countries/' + countryId + '/cities',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#city_id').empty();
                        $('#city_id').append('<option value="">اختر المدينة</option>');
                        $.each(data, function(key, value) {
                            $('#city_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#city_id').empty();
                $('#city_id').append('<option value="">اختر المدينة</option>');
            }
        });

        // Format IBAN
        $('#iban').on('input', function() {
            let value = $(this).val().replace(/\s/g, '').toUpperCase();
            let formattedValue = '';
            
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            
            $(this).val(formattedValue);
        });
    });
</script>
@endpush
@endsection
