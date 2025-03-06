@extends('landing')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12 text-right">
            <h1 class="display-4 mb-4">الأسئلة الشائعة</h1>
            
            <div class="faq-content">
                <p class="lead text-muted mb-4">
                    أجوبة على الأسئلة الأكثر شيوعًا حول منصة أورا
                </p>
                
                @php
                $faqCategories = [
                    [
                        'title' => 'المراسلة والتواصل',
                        'questions' => [
                            [
                                'question' => 'هل يمكنني إرسال الصور في المحادثات؟',
                                'answer' => 'نعم، يدعم أورا مشاركة الصور بسهولة في المحادثات الفردية والجماعية.'
                            ],
                            [
                                'question' => 'هل المحادثات آمنة وخاصة؟',
                                'answer' => 'بالتأكيد. جميع المحادثات مشفرة بأعلى معايير الأمان لضمان خصوصية تامة.'
                            ]
                        ]
                    ],
                    [
                        'title' => 'المكالمات الصوتية',
                        'questions' => [
                            [
                                'question' => 'هل أستطيع إجراء مكالمات صوتية؟',
                                'answer' => 'نعم، يوفر أورا خدمة المكالمات الصوتية بجودة عالية.'
                            ]
                        ]
                    ]
                ];
                @endphp

                <div class="faq-accordion">
                    @foreach($faqCategories as $category)
                        <div class="card mb-3">
                            <div class="card-header" id="heading{{ $loop->index }}">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{ $loop->index }}">
                                        {{ $category['title'] }}
                                    </button>
                                </h5>
                            </div>

                            <div id="collapse{{ $loop->index }}" class="collapse {{ $loop->first ? 'show' : '' }}">
                                <div class="card-body">
                                    @foreach($category['questions'] as $qa)
                                        <div class="faq-item mb-3">
                                            <h6 class="text-primary">{{ $qa['question'] }}</h6>
                                            <p>{{ $qa['answer'] }}</p>
                                        </div>
                                    @endforeach
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
