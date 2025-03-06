@extends('landing')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-right">
            <h1 class="display-4 mb-4">سياسة الخصوصية</h1>
            
            <div class="privacy-content">
                <p class="lead text-muted mb-4">
                    نحن في أورا نلتزم بحماية خصوصية وأمن معلوماتك الشخصية
                </p>
                
                <div class="privacy-sections">
                    <h3 class="text-primary mb-3">المعلومات التي نجمعها</h3>
                    <ul class="list-unstyled">
                        <li class="mb-2">اسمك</li>
                        <li class="mb-2">رقم الهاتف</li>
                        <li class="mb-2">البريد الإلكتروني</li>
                    </ul>
                </div>
                
                <div class="supported-regions mt-5">
                    <h4 class="mb-3">المناطق المدعومة</h4>
                    <div class="region-badges">
                        @foreach($supportedCountries as $country)
                            <span class="badge badge-secondary mx-1">{{ $country }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
