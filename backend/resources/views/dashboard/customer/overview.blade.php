@extends('layouts.dashboard')

@section('title', 'نظرة عامة - لوحة تحكم العميل')

@section('content')
<div class="container-fluid py-4">
    <!-- رسائل النجاح والخطأ -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h5 class="card-title">نظرة عامة على النشاط</h5>
                            <p class="text-muted">ملخص نشاطك خلال الشهر الحالي</p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-md-end">
                                <div class="dropdown me-2">
                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="periodDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        الشهر الحالي
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="periodDropdown">
                                        <li><a class="dropdown-item" href="#">اليوم</a></li>
                                        <li><a class="dropdown-item" href="#">هذا الأسبوع</a></li>
                                        <li><a class="dropdown-item active" href="#">الشهر الحالي</a></li>
                                        <li><a class="dropdown-item" href="#">آخر 3 شهور</a></li>
                                        <li><a class="dropdown-item" href="#">السنة الحالية</a></li>
                                    </ul>
                                </div>
                                <button class="btn btn-outline-primary">
                                    <i class="bi bi-download me-1"></i> تصدير
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top">
                    <div class="row">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="activity-summary">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <div class="activity-stat text-center">
                                            <div class="stat-value text-primary fw-bold fs-1 mb-2">{{ $messageSent ?? 245 }}</div>
                                            <div class="stat-label text-muted">رسائل مرسلة</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="activity-stat text-center">
                                            <div class="stat-value text-success fw-bold fs-1 mb-2">{{ $messageReceived ?? 312 }}</div>
                                            <div class="stat-label text-muted">رسائل مستلمة</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mt-4">
                                    <div class="col-6">
                                        <div class="activity-stat text-center">
                                            <div class="stat-value text-warning fw-bold fs-1 mb-2">{{ $callsMade ?? 18 }}</div>
                                            <div class="stat-label text-muted">مكالمات صادرة</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="activity-stat text-center">
                                            <div class="stat-value text-info fw-bold fs-1 mb-2">{{ $callsReceived ?? 24 }}</div>
                                            <div class="stat-label text-muted">مكالمات واردة</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="chart-container" style="height: 300px;">
                                <canvas id="activityChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">أكثر جهات الاتصال نشاطاً</h6>
                </div>
                <div class="card-body">
                    @if(isset($topContacts) && count($topContacts) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($topContacts as $index => $contact)
                                <li class="list-group-item px-0 py-3 border-0 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="contact-rank me-3 bg-{{ ['primary', 'success', 'info', 'warning', 'danger'][($index) % 5] }} rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                            <span>{{ $index + 1 }}</span>
                                        </div>
                                        <div class="contact-avatar me-3">
                                            <img src="{{ asset('storage/' . ($contact->avatar ?? 'avatars/default.jpg')) }}" alt="{{ $contact->name }}" class="rounded-circle" width="40" height="40">
                                        </div>
                                        <div class="contact-details flex-grow-1">
                                            <h6 class="mb-1">{{ $contact->name }}</h6>
                                            <p class="text-muted mb-0 small">{{ $contact->message_count ?? 0 }} رسالة</p>
                                        </div>
                                        <div class="contact-action">
                                            <a href="{{ route('dashboard.customer.messaging.conversation', $contact->conversation_id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-chat-dots"></i>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="bi bi-people text-muted" style="font-size: 48px;"></i>
                            </div>
                            <h6 class="text-muted">لا توجد جهات اتصال نشطة</h6>
                            <p class="text-muted">ابدأ محادثة مع جهات اتصالك لتظهر هنا</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">محادثات نشطة</h6>
                </div>
                <div class="card-body">
                    @if(isset($activeConversationsList) && count($activeConversationsList) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($activeConversationsList as $conversation)
                                <li class="list-group-item px-0 py-3 border-0 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="conversation-avatar me-3 position-relative">
                                            <img src="{{ $conversation->image ? asset('storage/' . $conversation->image) : asset('assets/images/group-avatar.jpg') }}" 
                                                alt="{{ $conversation->name }}" 
                                                class="rounded-circle" 
                                                width="40" height="40">
                                            @if($conversation->is_group)
                                                <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-info" style="font-size: 0.6rem;">
                                                    <i class="bi bi-people-fill"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="conversation-details flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0">{{ Str::limit($conversation->name, 20) }}</h6>
                                                <small class="text-muted">{{ $conversation->last_message_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="text-muted mb-0 text-truncate small" style="max-width: 220px;">
                                                {{ $conversation->last_message ?? 'لا توجد رسائل' }}
                                            </p>
                                        </div>
                                        <div class="conversation-action ms-2">
                                            <a href="{{ route('dashboard.customer.messaging.conversation', $conversation->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-chat-dots"></i>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="bi bi-chat-square-text text-muted" style="font-size: 48px;"></i>
                            </div>
                            <h6 class="text-muted">لا توجد محادثات نشطة</h6>
                            <p class="text-muted">ابدأ محادثة جديدة من قسم المراسلة</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent text-center">
                    <a href="{{ route('dashboard.customer.messaging.conversations') }}" class="btn btn-sm btn-outline-primary">عرض جميع المحادثات</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title mb-0">المكالمات الأخيرة</h6>
                </div>
                <div class="card-body">
                    @if(isset($recentCalls) && count($recentCalls) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($recentCalls as $call)
                                <li class="list-group-item px-0 py-3 border-0 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="call-icon me-3">
                                            @if($call->direction == 'outgoing')
                                                <div class="rounded-circle bg-{{ $call->status == 'completed' ? 'success' : 'danger' }} bg-opacity-10 p-2">
                                                    <i class="bi bi-telephone-outbound text-{{ $call->status == 'completed' ? 'success' : 'danger' }} fs-5"></i>
                                                </div>
                                            @else
                                                <div class="rounded-circle bg-{{ $call->status == 'completed' ? 'info' : 'warning' }} bg-opacity-10 p-2">
                                                    <i class="bi bi-telephone-inbound text-{{ $call->status == 'completed' ? 'info' : 'warning' }} fs-5"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="call-details flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h6 class="mb-0">{{ $call->participant_name }}</h6>
                                                <span class="badge bg-{{ $call->status == 'missed' ? 'danger' : ($call->status == 'rejected' ? 'warning' : 'success') }}">
                                                    {{ $call->status == 'completed' ? 'مكتملة' : ($call->status == 'missed' ? 'فائتة' : 'مرفوضة') }}
                                                </span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="text-muted mb-0 small">
                                                    <i class="bi bi-clock me-1"></i> 
                                                    {{ $call->duration ? gmdate('i:s', $call->duration) : '--:--' }}
                                                </p>
                                                <small class="text-muted">{{ $call->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        <div class="call-action ms-2">
                                            <button class="btn btn-sm btn-outline-primary" onclick="initiateCall('{{ $call->participant_id }}')">
                                                <i class="bi bi-telephone"></i>
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="bi bi-telephone text-muted" style="font-size: 48px;"></i>
                            </div>
                            <h6 class="text-muted">لا توجد مكالمات حديثة</h6>
                            <p class="text-muted">ابدأ مكالمة صوتية من قسم المراسلة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // معلومات الرسم البياني
        const ctx = document.getElementById('activityChart').getContext('2d');
        
        // أسبوع سابق، هذا الأسبوع - بيانات تجريبية
        const activityData = {
            labels: ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
            datasets: [
                {
                    label: 'رسائل مرسلة',
                    data: [12, 19, 10, 15, 20, 25, 18],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'رسائل مستلمة',
                    data: [15, 25, 12, 18, 24, 27, 20],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'المكالمات',
                    data: [3, 2, 5, 1, 4, 6, 2],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        };
        
        // إنشاء الرسم البياني
        const activityChart = new Chart(ctx, {
            type: 'line',
            data: activityData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });

        // دالة لبدء مكالمة - يمكن تنفيذها في المشروع الفعلي
        window.initiateCall = function(userId) {
            alert('بدء مكالمة مع المستخدم: ' + userId);
            // هنا يمكن إضافة الكود الخاص ببدء المكالمة
        };
    });
</script>
@endpush

@push('styles')
<style>
    .contact-rank {
        width: 28px;
        height: 28px;
        font-size: 0.8rem;
    }
</style>
@endpush
