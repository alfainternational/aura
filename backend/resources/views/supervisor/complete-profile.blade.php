@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('استكمال بيانات المشرف') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('supervisor.store-profile') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <label for="phone_number" class="col-md-4 col-form-label text-md-end">{{ __('رقم الهاتف') }}</label>
                            <div class="col-md-6">
                                <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number', auth()->user()->phone_number) }}" required>
                                @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="department" class="col-md-4 col-form-label text-md-end">{{ __('القسم') }}</label>
                            <div class="col-md-6">
                                <select id="department" class="form-control @error('department') is-invalid @enderror" name="department" required>
                                    <option value="">{{ __('-- اختر القسم --') }}</option>
                                    <option value="delivery" {{ old('department') == 'delivery' ? 'selected' : '' }}>{{ __('قسم التوصيل') }}</option>
                                    <option value="merchants" {{ old('department') == 'merchants' ? 'selected' : '' }}>{{ __('قسم التجار') }}</option>
                                    <option value="customer_service" {{ old('department') == 'customer_service' ? 'selected' : '' }}>{{ __('خدمة العملاء') }}</option>
                                    <option value="quality" {{ old('department') == 'quality' ? 'selected' : '' }}>{{ __('قسم الجودة') }}</option>
                                </select>
                                @error('department')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="position" class="col-md-4 col-form-label text-md-end">{{ __('المنصب') }}</label>
                            <div class="col-md-6">
                                <input id="position" type="text" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ old('position') }}" required>
                                @error('position')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="zones" class="col-md-4 col-form-label text-md-end">{{ __('المناطق المسؤول عنها') }}</label>
                            <div class="col-md-6">
                                <select id="zones" class="form-control @error('zones') is-invalid @enderror" name="zones[]" multiple required>
                                    <!-- هنا سيتم وضع المناطق ديناميكياً من قاعدة البيانات -->
                                    <option value="1">{{ __('منطقة 1') }}</option>
                                    <option value="2">{{ __('منطقة 2') }}</option>
                                    <option value="3">{{ __('منطقة 3') }}</option>
                                    <option value="4">{{ __('منطقة 4') }}</option>
                                </select>
                                <small class="form-text text-muted">{{ __('يمكنك اختيار أكثر من منطقة باستخدام زر Ctrl أو Command') }}</small>
                                @error('zones')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="birth_date" class="col-md-4 col-form-label text-md-end">{{ __('تاريخ الميلاد') }}</label>
                            <div class="col-md-6">
                                <input id="birth_date" type="date" class="form-control @error('birth_date') is-invalid @enderror" name="birth_date" value="{{ old('birth_date', auth()->user()->birth_date) }}">
                                @error('birth_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="gender" class="col-md-4 col-form-label text-md-end">{{ __('الجنس') }}</label>
                            <div class="col-md-6">
                                <select id="gender" class="form-control @error('gender') is-invalid @enderror" name="gender">
                                    <option value="">{{ __('-- اختر --') }}</option>
                                    <option value="male" {{ old('gender', auth()->user()->gender) == 'male' ? 'selected' : '' }}>{{ __('ذكر') }}</option>
                                    <option value="female" {{ old('gender', auth()->user()->gender) == 'female' ? 'selected' : '' }}>{{ __('أنثى') }}</option>
                                    <option value="other" {{ old('gender', auth()->user()->gender) == 'other' ? 'selected' : '' }}>{{ __('آخر') }}</option>
                                </select>
                                @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="profile_image" class="col-md-4 col-form-label text-md-end">{{ __('الصورة الشخصية') }}</label>
                            <div class="col-md-6">
                                <input id="profile_image" type="file" class="form-control @error('profile_image') is-invalid @enderror" name="profile_image" accept="image/*">
                                @error('profile_image')
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
