@extends('landing')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-right">
            <h1 class="display-4 mb-4">شروط الاستخدام</h1>
            
            <div class="terms-content">
                <p class="lead text-muted mb-4">
                    يرجى قراءة شروط الاستخدام بعناية قبل استخدام منصة أورا
                </p>
                
                <div class="terms-sections">
                    <h3 class="text-primary mb-3">الشروط الأساسية</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-primary ml-2"></i>
                            يجب أن يكون عمرك 18 عامًا أو أكثر
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-primary ml-2"></i>
                            الالتزام بسياسات الاستخدام المقبول
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-primary ml-2"></i>
                            احترام خصوصية المستخدمين الآخرين
                        </li>
                    </ul>
                </div>
                
                <div class="supported-regions mt-5">
                    <h4 class="mb-3">المناطق المدعومة</h4>
                    <div class="region-badges">
                        @foreach($supportedCountries['countries'] as $country)
                            <span class="badge badge-secondary mx-1">{{ $country }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
