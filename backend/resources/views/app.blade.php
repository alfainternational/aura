@extends('layouts.app')

@section('content')
<div class="container app-overview-page">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center mb-0">نظرة عامة على أورا</h2>
                </div>
                <div class="card-body">
                    <section class="mb-4">
                        <h3 class="text-primary mb-3 text-center">أورا: منصة التواصل المحلية والآمنة</h3>
                        <p class="lead text-center">
                            تطبيق مصمم خصيصًا للمستخدمين في السودان والمملكة العربية السعودية، 
                            يوفر تجربة تواصل سلسة وموثوقة.
                        </p>
                    </section>

                    <div class="row app-sections">
                        @foreach($appSections as $section)
                            <div class="col-md-4 mb-4">
                                <div class="app-section-card text-center">
                                    <div class="section-icon mb-3">
                                        <i class="fas {{ $section['icon'] }} text-primary"></i>
                                    </div>
                                    <h4 class="section-title">{{ $section['title'] }}</h4>
                                    <p class="section-description mb-3">
                                        {{ $section['description'] }}
                                    </p>
                                    <div class="section-features">
                                        <ul class="list-unstyled">
                                            @foreach($section['features'] as $feature)
                                                <li>
                                                    <i class="fas fa-check-circle text-success ml-2"></i>
                                                    {{ $feature }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <section class="text-center mt-4">
                        <h3 class="text-primary mb-3">الدول المدعومة</h3>
                        <div class="supported-countries">
                            @foreach($supportedCountries['countries'] as $country)
                                <span class="badge bg-secondary mx-2">{{ $country }}</span>
                            @endforeach
                        </div>
                    </section>

                    <section class="text-center mt-4 download-section">
                        <h3 class="text-primary mb-3">حمّل أورا الآن</h3>
                        <p class="lead mb-4">
                            انطلق في رحلة التواصل الآمن والسريع
                        </p>
                        <div class="download-buttons">
                            <a href="#" class="btn btn-outline-primary mx-2">
                                <i class="fab fa-google-play ml-2"></i>
                                متجر جوجل بلاي
                            </a>
                            <a href="#" class="btn btn-outline-dark mx-2">
                                <i class="fab fa-apple ml-2"></i>
                                متجر آبل
                            </a>
                        </div>
                    </section>
                </div>
                <div class="card-footer text-muted text-center">
                    © {{ date('Y') }} أورا - جميع الحقوق محفوظة
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .app-overview-page {
        direction: rtl;
        text-align: right;
    }
    .app-sections {
        display: flex;
        justify-content: center;
    }
    .app-section-card {
        padding: 20px;
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .app-section-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .section-icon i {
        font-size: 3rem;
    }
    .section-title {
        margin-bottom: 15px;
        color: #333;
    }
    .section-description {
        color: #666;
    }
    .section-features ul {
        text-align: right;
        display: inline-block;
    }
    .section-features li {
        margin-bottom: 10px;
    }
    .supported-countries {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }
    .download-buttons {
        display: flex;
        justify-content: center;
    }
</style>
@endpush
