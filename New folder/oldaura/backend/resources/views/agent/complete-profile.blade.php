@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('استكمال بيانات الوكيل') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('agent.store-profile') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <label for="agency_name" class="col-md-4 col-form-label text-md-end">{{ __('اسم الوكالة') }}</label>
                            <div class="col-md-6">
                                <input id="agency_name" type="text" class="form-control @error('agency_name') is-invalid @enderror" name="agency_name" value="{{ old('agency_name') }}" required>
                                @error('agency_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="agency_phone" class="col-md-4 col-form-label text-md-end">{{ __('رقم هاتف الوكالة') }}</label>
                            <div class="col-md-6">
                                <input id="agency_phone" type="text" class="form-control @error('agency_phone') is-invalid @enderror" name="agency_phone" value="{{ old('agency_phone') }}" required>
                                @error('agency_phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="agency_address" class="col-md-4 col-form-label text-md-end">{{ __('عنوان الوكالة') }}</label>
                            <div class="col-md-6">
                                <input id="agency_address" type="text" class="form-control @error('agency_address') is-invalid @enderror" name="agency_address" value="{{ old('agency_address') }}" required>
                                @error('agency_address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="agency_description" class="col-md-4 col-form-label text-md-end">{{ __('وصف الوكالة') }}</label>
                            <div class="col-md-6">
                                <textarea id="agency_description" class="form-control @error('agency_description') is-invalid @enderror" name="agency_description" rows="3">{{ old('agency_description') }}</textarea>
                                @error('agency_description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="zone_id" class="col-md-4 col-form-label text-md-end">{{ __('منطقة العمل') }}</label>
                            <div class="col-md-6">
                                <select id="zone_id" class="form-control @error('zone_id') is-invalid @enderror" name="zone_id" required>
                                    <option value="">{{ __('اختر منطقة العمل') }}</option>
                                    <!-- هنا سيتم وضع المناطق ديناميكياً من قاعدة البيانات -->
                                    <option value="1">{{ __('منطقة 1') }}</option>
                                    <option value="2">{{ __('منطقة 2') }}</option>
                                    <option value="3">{{ __('منطقة 3') }}</option>
                                </select>
                                @error('zone_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="logo" class="col-md-4 col-form-label text-md-end">{{ __('شعار الوكالة') }}</label>
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
                            <label for="license_number" class="col-md-4 col-form-label text-md-end">{{ __('رقم الترخيص') }}</label>
                            <div class="col-md-6">
                                <input id="license_number" type="text" class="form-control @error('license_number') is-invalid @enderror" name="license_number" value="{{ old('license_number') }}">
                                @error('license_number')
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
