@extends('layouts.dashboard')

@section('title', 'تغيير كلمة المرور')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <x-card class="border-0 shadow-sm mb-4">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">تغيير كلمة المرور</h5>
                        <a href="{{ route('profile.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right me-1"></i> العودة للملف الشخصي
                        </a>
                    </div>
                </x-slot>

                <form action="{{ route('profile.update-password') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        كلمة المرور يجب أن تحتوي على الأقل على 8 أحرف وتتضمن حرف كبير وحرف صغير ورقم ورمز خاص.
                    </div>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">كلمة المرور الحالية <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="password-strength mt-2">
                            <div class="progress" style="height: 5px;">
                                <div id="password-strength-meter" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small id="password-strength-text" class="text-muted">قوة كلمة المرور</small>
                                <small id="password-strength-label" class="text-muted">ضعيفة</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور الجديدة <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="reset" class="btn btn-outline-secondary me-2">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Toggle password visibility
        $('.toggle-password').click(function() {
            const targetId = $(this).data('target');
            const passwordInput = $(`#${targetId}`);
            const icon = $(this).find('i');
            
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('bi-eye').addClass('bi-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('bi-eye-slash').addClass('bi-eye');
            }
        });

        // Password strength meter
        $('#password').on('input', function() {
            const password = $(this).val();
            let strength = 0;
            let label = 'ضعيفة';
            let color = 'danger';

            if (password.length >= 8) {
                strength += 25;
            }
            
            if (password.match(/[A-Z]/)) {
                strength += 25;
            }
            
            if (password.match(/[0-9]/)) {
                strength += 25;
            }
            
            if (password.match(/[^A-Za-z0-9]/)) {
                strength += 25;
            }

            if (strength >= 75) {
                label = 'قوية';
                color = 'success';
            } else if (strength >= 50) {
                label = 'متوسطة';
                color = 'warning';
            } else if (strength >= 25) {
                label = 'ضعيفة';
                color = 'danger';
            } else {
                label = 'ضعيفة جداً';
                color = 'danger';
            }

            $('#password-strength-meter')
                .css('width', strength + '%')
                .removeClass('bg-danger bg-warning bg-success')
                .addClass('bg-' + color);
            
            $('#password-strength-label').text(label);
        });
    });
</script>
@endpush
@endsection
