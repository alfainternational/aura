@extends('layouts.app')

@section('title', 'تطبيق AURA')

@section('content')
<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-start mb-4 mb-lg-0">
            <h1 class="display-4 fw-bold mb-4">AURA في جهازك</h1>
            <p class="lead text-muted mb-4">
                استمتع بتجربة AURA المتكاملة على جميع الأجهزة. تواصل، تسوق، وأدر أموالك بسهولة أينما كنت.
            </p>
            
            <div class="mb-4">
                <h4 class="fw-bold mb-3">منصات متاحة</h4>
                <div class="d-flex justify-content-center justify-content-lg-start">
                    @foreach($platforms as $platform)
                        <a href="{{ $platform['link'] }}" 
                           class="btn btn-{{ $platform['available'] ? 'outline-primary' : 'secondary' }} me-3 mb-2 {{ $platform['available'] ? '' : 'disabled' }}">
                            <i class="{{ $platform['icon'] }} me-2"></i>
                            {{ $platform['name'] }}
                            @if(!$platform['available'])
                                <small class="text-muted">(قريباً)</small>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
            
            <div>
                <h4 class="fw-bold mb-3">مميزات التطبيق</h4>
                <ul class="list-unstyled">
                    @foreach($features as $feature)
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        
        <div class="col-lg-6 text-center">
            <div class="app-mockup position-relative">
                <img src="{{ asset('images/app-mockup.png') }}" class="img-fluid" alt="AURA App Mockup">
                <div class="device-frame position-absolute top-50 start-50 translate-middle">
                    <div class="screen-content">
                        <!-- Placeholder for app screenshot -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12 text-center">
            <div class="alert alert-light border-0 shadow-sm" role="alert">
                <h4 class="alert-heading fw-bold mb-3">
                    <i class="fas fa-shield-alt text-success me-2"></i>
                    خصوصية وأمان مضمونان
                </h4>
                <p class="mb-0">
                    نلتزم بحماية بياناتك الشخصية وضمان أعلى معايير الأمان في جميع تطبيقاتنا.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.app-mockup {
    max-width: 400px;
    margin: 0 auto;
}
.device-frame {
    width: 240px;
    height: 480px;
    background-color: #f0f0f0;
    border-radius: 20px;
    overflow: hidden;
}
.screen-content {
    width: 100%;
    height: 100%;
    background-color: #ffffff;
}
</style>
@endpush
