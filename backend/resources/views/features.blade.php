@extends('landing')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-right">
            <h1 class="display-4 mb-4">مميزات أورا</h1>
            
            <div class="features-content">
                <p class="lead text-muted mb-4">
                    منصة متكاملة للتواصل الآمن والخدمات الذكية
                </p>
                
                <div class="features-sections">
                    @php
                    $features = [
                        [
                            'icon' => 'comments',
                            'title' => 'المراسلة المباشرة',
                            'description' => 'محادثات نصية وصورية مشفرة بأعلى معايير الأمان',
                            'details' => [
                                'محادثات فردية وجماعية',
                                'مشاركة الصور بسهولة',
                                'حذف الرسائل',
                                'حالة الرسائل (مرسلة، موصلة، مقروءة)'
                            ]
                        ],
                        [
                            'icon' => 'phone',
                            'title' => 'المكالمات الصوتية',
                            'description' => 'مكالمات صوتية عالية الجودة وآمنة',
                            'details' => [
                                'مكالمات واحد إلى واحد',
                                'جودة صوت عالية',
                                'اتصال سريع وموثوق'
                            ]
                        ]
                    ];
                    @endphp

                    <div class="row">
                        @foreach($features as $feature)
                            <div class="col-md-6 mb-4">
                                <div class="feature-card card border-0 shadow-sm h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-{{ $feature['icon'] }} fa-3x text-primary mb-3"></i>
                                        <h4 class="feature-title mb-3">{{ $feature['title'] }}</h4>
                                        <p class="feature-description text-muted mb-4">
                                            {{ $feature['description'] }}
                                        </p>
                                        <ul class="feature-details list-unstyled text-right">
                                            @foreach($feature['details'] as $detail)
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-primary ml-2"></i>
                                                    {{ $detail }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
