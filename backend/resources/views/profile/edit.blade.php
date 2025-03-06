@extends('layouts.dashboard')

@section('title', 'تعديل الملف الشخصي')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">تعديل الملف الشخصي</h5>
                        <a href="{{ route('profile.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right me-1"></i> العودة للملف الشخصي
                        </a>
                    </div>
                </x-slot>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="d-flex flex-column align-items-center">
                                <div class="avatar-container mb-3">
                                    @if ($user->profile && $user->profile->avatar)
                                        <img src="{{ asset('storage/' . $user->profile->avatar) }}" alt="{{ $user->name }}" class="rounded-circle" width="150" height="150" id="avatar-preview">
                                    @else
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white" style="width: 150px; height: 150px;" id="avatar-placeholder">
                                            <span class="fs-1">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        <img src="" alt="{{ $user->name }}" class="rounded-circle d-none" width="150" height="150" id="avatar-preview">
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="avatar" class="btn btn-outline-primary">
                                        <i class="bi bi-camera me-1"></i> تغيير الصورة
                                    </label>
                                    <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                                </div>
                                @if ($user->profile && $user->profile->avatar)
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" value="1" id="remove_avatar" name="remove_avatar">
                                    <label class="form-check-label" for="remove_avatar">
                                        إزالة الصورة الحالية
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-8 mb-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">الاسم الكامل <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->profile->phone ?? '') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="date_of_birth" class="form-label">تاريخ الميلاد</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->profile->date_of_birth ?? '') }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">الجنس</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                        <option value="">اختر الجنس</option>
                                        <option value="male" {{ old('gender', $user->profile->gender ?? '') == 'male' ? 'selected' : '' }}>ذكر</option>
                                        <option value="female" {{ old('gender', $user->profile->gender ?? '') == 'female' ? 'selected' : '' }}>أنثى</option>
                                        <option value="other" {{ old('gender', $user->profile->gender ?? '') == 'other' ? 'selected' : '' }}>أخرى</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="country_id" class="form-label">الدولة</label>
                                    <select class="form-select @error('country_id') is-invalid @enderror" id="country_id" name="country_id">
                                        <option value="">اختر الدولة</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id', $user->profile->country_id ?? '') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="city_id" class="form-label">المدينة</label>
                                    <select class="form-select @error('city_id') is-invalid @enderror" id="city_id" name="city_id">
                                        <option value="">اختر المدينة</option>
                                        @if($cities)
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}" {{ old('city_id', $user->profile->city_id ?? '') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="address" class="form-label">العنوان</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->profile->address ?? '') }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="bio" class="form-label">نبذة مختصرة</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-outline-secondary me-2">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#avatar-placeholder').addClass('d-none');
                $('#avatar-preview').removeClass('d-none').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

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

        // Handle avatar removal checkbox
        $('#remove_avatar').change(function() {
            if($(this).is(':checked')) {
                $('#avatar').prop('disabled', true);
            } else {
                $('#avatar').prop('disabled', false);
            }
        });
    });
</script>
@endpush
@endsection
