@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('لوحة تحكم المندوب') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        {{ __('مرحباً بك في لوحة تحكم المندوب!') }}
                    </div>

                    @if(isset($profile) && $profile->verification_status != 'verified')
                        <div class="alert alert-warning">
                            {{ __('حسابك قيد المراجعة من قبل إدارة النظام. سيتم تفعيل الحساب بشكل كامل بعد التحقق من البيانات.') }}
                        </div>
                    @endif

                    <div class="mt-4">
                        <h5>{{ __('معلومات المندوب') }}</h5>
                        <div class="d-flex align-items-center mb-3">
                            <p class="mb-0 me-3"><strong>{{ __('الحالة:') }}</strong></p>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="toggleOnline" 
                                    {{ isset($profile) && $profile->is_online ? 'checked' : '' }}
                                    onchange="updateOnlineStatus(this.checked)">
                                <label class="form-check-label" for="toggleOnline">
                                    {{ isset($profile) && $profile->is_online ? __('متصل') : __('غير متصل') }}
                                </label>
                            </div>
                        </div>
                        <p><strong>{{ __('المنطقة:') }}</strong> {{ isset($profile) && isset($profile->zone) ? $profile->zone->name : __('غير محدد') }}</p>
                        <p>
                            <strong>{{ __('نوع المركبة:') }}</strong> 
                            @if(isset($profile) && isset($profile->vehicle))
                                {{ $profile->vehicle->type }} - {{ $profile->vehicle->model }} ({{ $profile->vehicle->color }})
                            @else
                                {{ __('غير محدد') }}
                            @endif
                        </p>
                        <p>
                            <strong>{{ __('رقم اللوحة:') }}</strong> 
                            @if(isset($profile) && isset($profile->vehicle))
                                {{ $profile->vehicle->plate_number }}
                            @else
                                {{ __('غير محدد') }}
                            @endif
                        </p>
                    </div>

                    <div class="mt-4">
                        <h5>{{ __('الإحصائيات اليومية') }}</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card bg-primary text-white text-center p-3">
                                    <h3>0</h3>
                                    <p class="mb-0">{{ __('الطلبات النشطة') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-success text-white text-center p-3">
                                    <h3>0</h3>
                                    <p class="mb-0">{{ __('الطلبات المكتملة') }}</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-info text-white text-center p-3">
                                    <h3>0</h3>
                                    <p class="mb-0">{{ __('الإيرادات') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5>{{ __('الإجراءات السريعة') }}</h5>
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action">{{ __('طلبات جديدة') }}</a>
                            <a href="{{ route('messenger.profile') }}" class="list-group-item list-group-item-action">{{ __('الملف الشخصي') }}</a>
                            <a href="{{ route('messenger.vehicle') }}" class="list-group-item list-group-item-action">{{ __('معلومات المركبة') }}</a>
                            <a href="{{ route('messenger.statistics') }}" class="list-group-item list-group-item-action">{{ __('الإحصائيات') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateOnlineStatus(isOnline) {
        fetch('{{ route("messenger.toggle-online") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ is_online: isOnline })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector('label[for="toggleOnline"]').textContent = isOnline ? '{{ __("متصل") }}' : '{{ __("غير متصل") }}';
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endpush
@endsection
