@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('استكمال بيانات المتجر') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('merchant.store-profile') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <label for="store_name" class="col-md-4 col-form-label text-md-end">{{ __('اسم المتجر') }}</label>
                            <div class="col-md-6">
                                <input id="store_name" type="text" class="form-control @error('store_name') is-invalid @enderror" name="store_name" value="{{ old('store_name') }}" required>
                                @error('store_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="store_phone" class="col-md-4 col-form-label text-md-end">{{ __('رقم هاتف المتجر') }}</label>
                            <div class="col-md-6">
                                <input id="store_phone" type="text" class="form-control @error('store_phone') is-invalid @enderror" name="store_phone" value="{{ old('store_phone') }}" required>
                                @error('store_phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="store_address" class="col-md-4 col-form-label text-md-end">{{ __('عنوان المتجر') }}</label>
                            <div class="col-md-6">
                                <input id="store_address" type="text" class="form-control @error('store_address') is-invalid @enderror" name="store_address" value="{{ old('store_address') }}" required>
                                @error('store_address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="store_description" class="col-md-4 col-form-label text-md-end">{{ __('وصف المتجر') }}</label>
                            <div class="col-md-6">
                                <textarea id="store_description" class="form-control @error('store_description') is-invalid @enderror" name="store_description" rows="3">{{ old('store_description') }}</textarea>
                                @error('store_description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="business_type" class="col-md-4 col-form-label text-md-end">{{ __('نوع النشاط التجاري') }}</label>
                            <div class="col-md-6">
                                <select id="business_type" class="form-control @error('business_type') is-invalid @enderror" name="business_type" required>
                                    <option value="">{{ __('اختر نوع النشاط') }}</option>
                                    <option value="restaurant">{{ __('مطعم') }}</option>
                                    <option value="grocery">{{ __('بقالة') }}</option>
                                    <option value="pharmacy">{{ __('صيدلية') }}</option>
                                    <option value="electronics">{{ __('إلكترونيات') }}</option>
                                    <option value="clothing">{{ __('ملابس') }}</option>
                                    <option value="other">{{ __('أخرى') }}</option>
                                </select>
                                @error('business_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="logo" class="col-md-4 col-form-label text-md-end">{{ __('شعار المتجر') }}</label>
                            <div class="col-md-6">
                                <input id="logo" type="file" class="form-control @error('logo') is-invalid @enderror" name="logo" accept="image/*">
                                @error('logo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="commercial_register" class="col-md-4 col-form-label text-md-end">{{ __('رقم السجل التجاري') }}</label>
                            <div class="col-md-6">
                                <input id="commercial_register" type="text" class="form-control @error('commercial_register') is-invalid @enderror" name="commercial_register" value="{{ old('commercial_register') }}">
                                @error('commercial_register')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="tax_number" class="col-md-4 col-form-label text-md-end">{{ __('الرقم الضريبي') }}</label>
                            <div class="col-md-6">
                                <input id="tax_number" type="text" class="form-control @error('tax_number') is-invalid @enderror" name="tax_number" value="{{ old('tax_number') }}">
                                @error('tax_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('حفظ البيانات') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
