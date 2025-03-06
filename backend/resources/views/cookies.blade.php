@extends('layouts.app')

@section('content')
<div class="container cookies-page">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center mb-0">سياسة الكوكيز</h2>
                </div>
                <div class="card-body">
                    <section class="mb-4">
                        <h3 class="text-primary mb-3 text-center">الشفافية في استخدام الكوكيز</h3>
                        <p class="lead text-center">
                            نحن نلتزم بالشفافية التامة في كيفية استخدامنا للكوكيز لتحسين تجربتك في أورا.
                        </p>
                    </section>

                    <section class="cookies-sections mb-4">
                        @foreach($cookiesSections as $section)
                            <div class="cookies-section mb-3">
                                <h4 class="text-primary">{{ $section['title'] }}</h4>
                                <p>{{ $section['content'] }}</p>
                            </div>
                        @endforeach
                    </section>

                    <section class="cookies-types mb-4">
                        <h3 class="text-primary mb-3">أنواع الكوكيز</h3>
                        <div class="row">
                            @foreach($cookiesTypes as $category => $types)
                                <div class="col-md-6">
                                    <h4 class="mb-3">{{ $category }}</h4>
                                    <ul class="list-group">
                                        @foreach($types as $type)
                                            <li class="list-group-item {{ $category == 'كوكيز أساسية' ? 'list-group-item-success' : 'list-group-item-info' }}">
                                                {{ $type }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </section>

                    <section class="text-center mt-4 browser-settings">
                        <h3 class="text-primary mb-3">التحكم في الكوكيز</h3>
                        <p class="lead mb-4">
                            يمكنك التحكم في إعدادات الكوكيز من خلال متصفحك. راجع إعدادات الخصوصية للتفاصيل.
                        </p>
                        <div class="browser-icons">
                            <i class="fab fa-chrome mx-2 fa-2x text-muted"></i>
                            <i class="fab fa-firefox mx-2 fa-2x text-muted"></i>
                            <i class="fab fa-safari mx-2 fa-2x text-muted"></i>
                            <i class="fab fa-edge mx-2 fa-2x text-muted"></i>
                        </div>
                    </section>

                    <section class="text-center mt-4 contact-section">
                        <h3 class="text-primary mb-3">استفسارات الكوكيز</h3>
                        <p class="lead mb-4">
                            إذا كانت لديك أي أسئلة أو استفسارات حول سياسة الكوكيز، يمكنك التواصل معنا.
                        </p>
                        <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">
                            تواصل معنا
                        </a>
                    </section>

                    <section class="text-center mt-4 supported-countries">
                        <h3 class="text-primary mb-3">الدول المدعومة</h3>
                        <div>
                            @foreach($supportedCountries['countries'] as $country)
                                <span class="badge bg-secondary mx-2">{{ $country }}</span>
                            @endforeach
                        </div>
                    </section>
                </div>
                <div class="card-footer text-muted text-center">
                    © {{ date('Y') }} أورا - سياسة الكوكيز
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .cookies-page {
        direction: rtl;
        text-align: right;
    }
    .cookies-sections {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
    }
    .cookies-section {
        margin-bottom: 20px;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 15px;
    }
    .cookies-section:last-child {
        border-bottom: none;
    }
    .cookies-types .list-group-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cookies-types .list-group-item-success {
        background-color: rgba(25, 135, 84, 0.1);
    }
    .cookies-types .list-group-item-info {
        background-color: rgba(13, 110, 253, 0.1);
    }
    .browser-settings .browser-icons {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    .supported-countries {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }
</style>
@endpush
