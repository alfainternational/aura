@extends('layouts.app')

@section('content')
<div class="container-fluid ai-enhanced-dashboard">
    <div class="row">
        {{-- لوحة تحكم المستخدم الذكية --}}
        <div class="col-md-4">
            <div class="card ai-user-insights">
                <div class="card-header">
                    <h3 class="card-title">{{ __('لوحة التحكم الذكية') }}</h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">AI Powered</span>
                    </div>
                </div>
                <div class="card-body">
                    {{-- معلومات المستخدم الذكية --}}
                    <div class="user-profile-summary">
                        <div class="profile-header">
                            <img src="{{ auth()->user()->profile_image }}" class="img-circle elevation-2" alt="User Image">
                            <h4>{{ auth()->user()->name }}</h4>
                            <p>{{ __('مستوى الخصوصية') }}: <span class="badge badge-success">{{ __('عالي') }}</span></p>
                        </div>

                        <div class="ai-insights">
                            <h5>{{ __('رؤى ذكية') }}</h5>
                            <ul class="list-unstyled">
                                <li>
                                    <i class="fas fa-chart-line"></i> 
                                    {{ __('نشاطك الشهري') }}: 
                                    <span class="badge badge-info">{{ $userActivityLevel }}</span>
                                </li>
                                <li>
                                    <i class="fas fa-comments"></i> 
                                    {{ __('كفاءة التواصل') }}: 
                                    <span class="badge badge-warning">{{ $communicationEfficiency }}%</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- لوحة المحادثات والاتصالات --}}
        <div class="col-md-8">
            <div class="card ai-communications">
                <div class="card-header">
                    <h3 class="card-title">{{ __('محادثاتك وتواصلك') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- إحصائيات المراسلة --}}
                        <div class="col-md-6">
                            <h5>{{ __('المراسلة') }}</h5>
                            <div class="progress-group">
                                {{ __('الرسائل المرسلة') }}
                                <span class="float-right"><b>{{ $sentMessages }}</b>/{{ $totalMessages }}</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-primary" style="width: {{ ($sentMessages / $totalMessages) * 100 }}%"></div>
                                </div>
                            </div>
                            <div class="progress-group">
                                {{ __('المكالمات الصوتية') }}
                                <span class="float-right"><b>{{ $voiceCalls }}</b>/{{ $totalCommunications }}</span>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" style="width: {{ ($voiceCalls / $totalCommunications) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- المحادثات الذكية --}}
                        <div class="col-md-6">
                            <h5>{{ __('المحادثات الذكية') }}</h5>
                            <div class="ai-chat-insights">
                                <ul class="list-unstyled">
                                    @foreach($aiConversations as $conversation)
                                        <li>
                                            <span class="badge badge-secondary">{{ $conversation->type }}</span>
                                            {{ $conversation->summary }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- قسم التوصيات والاقتراحات --}}
    <div class="row">
        <div class="col-12">
            <div class="card ai-recommendations">
                <div class="card-header">
                    <h3 class="card-title">{{ __('توصيات ذكية') }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($aiRecommendations as $recommendation)
                            <div class="col-md-4">
                                <div class="card card-recommendation">
                                    <div class="card-body">
                                        <h5>{{ $recommendation->title }}</h5>
                                        <p>{{ $recommendation->description }}</p>
                                        <a href="{{ $recommendation->link }}" class="btn btn-sm btn-primary">{{ __('المزيد') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // تحميل البيانات الديناميكية للوحة التحكم الذكية
    document.addEventListener('DOMContentLoaded', function() {
        // تحديث المعلومات باستخدام AJAX
        fetchAIDashboardData();
    });
</script>
@endpush
