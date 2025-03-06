@extends('landing')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-right">
            <h1 class="display-4 mb-4">آراء المستخدمين</h1>
            
            <div class="testimonials-content">
                <p class="lead text-muted mb-4">
                    سمعة أورا من خلال عيون مستخدمينا
                </p>
                
                @php
                $testimonials = [
                    [
                        'name' => 'محمد أحمد',
                        'location' => 'الخرطوم، السودان',
                        'quote' => 'أورا غيّر طريقة تواصلي تمامًا. الأمان والسرعة في أعلى مستوياتها.',
                        'avatar' => 'https://randomuser.me/api/portraits/men/1.jpg'
                    ],
                    [
                        'name' => 'فاطمة محمد',
                        'location' => 'جدة، المملكة العربية السعودية',
                        'quote' => 'التشفير والخصوصية في أورا جعلتني أشعر بالأمان عند التواصل مع أصدقائي.',
                        'avatar' => 'https://randomuser.me/api/portraits/women/2.jpg'
                    ]
                ];
                @endphp

                <div class="testimonial-grid row">
                    @foreach($testimonials as $testimonial)
                        <div class="col-md-6 mb-4">
                            <div class="card testimonial-card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <img src="{{ $testimonial['avatar'] }}" 
                                         alt="{{ $testimonial['name'] }}" 
                                         class="rounded-circle mb-3" 
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                    <blockquote class="blockquote">
                                        <p class="mb-3">
                                            <i class="fas fa-quote-right text-primary ml-2"></i>
                                            {{ $testimonial['quote'] }}
                                        </p>
                                        <footer class="blockquote-footer">
                                            {{ $testimonial['name'] }}
                                            <cite title="Location">من {{ $testimonial['location'] }}</cite>
                                        </footer>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
