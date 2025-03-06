@extends('landing')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-right">
            <h1 class="display-4 mb-4">{{ $pageTitle }}</h1>
            
            <div class="about-content">
                <p class="lead text-muted mb-4">
                    أورا هي منصة متكاملة للخدمات الذكية، مصممة خصيصًا للمستخدمين في السودان والمملكة العربية السعودية
                </p>
                
                <div class="supported-regions mt-4">
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
