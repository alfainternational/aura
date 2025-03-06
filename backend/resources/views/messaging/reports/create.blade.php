@extends('layouts.dashboard')

@section('title', 'الإبلاغ عن محتوى')

@section('page-title', 'الإبلاغ عن محتوى')

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

    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الإبلاغ عن محتوى</h5>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right me-1"></i> العودة
                        </a>
                    </div>
                </x-slot>

                <div class="report-content">
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>ملاحظة مهمة</h6>
                        <p class="mb-0">يرجى تقديم معلومات دقيقة وكاملة لمساعدتنا في اتخاذ الإجراء المناسب. سيتم التعامل مع جميع البلاغات بسرية تامة.</p>
                    </div>

                    <form action="{{ route('messaging.reports.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        @if(isset($message))
                        <input type="hidden" name="message_id" value="{{ $message->id }}">
                        @endif
                        
                        @if(isset($conversation))
                        <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
                        @endif
                        
                        @if(isset($user))
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        @endif
                        
                        <div class="mb-4">
                            <div class="form-floating">
                                <select class="form-select" id="report-type" name="report_type" required>
                                    <option value="">اختر نوع البلاغ</option>
                                    <option value="message" {{ isset($message) ? 'selected' : '' }}>رسالة محددة</option>
                                    <option value="conversation" {{ isset($conversation) && !isset($message) ? 'selected' : '' }}>محادثة كاملة</option>
                                    <option value="user" {{ isset($user) && !isset($conversation) && !isset($message) ? 'selected' : '' }}>مستخدم</option>
                                </select>
                                <label for="report-type">نوع البلاغ</label>
                            </div>
                        </div>
                        
                        @if(isset($reportTarget))
                        <div class="mb-4">
                            <h6>المحتوى المبلغ عنه:</h6>
                            <div class="reported-content p-3 border rounded bg-light">
                                @if(isset($message))
                                    <div class="d-flex align-items-start mb-2">
                                        <img src="{{ $message->sender->profile_image_url ?? asset('images/default-avatar.png') }}" 
                                             class="rounded-circle me-2" alt="صورة المستخدم" width="30" height="30">
                                        <div>
                                            <strong>{{ $message->sender->name ?? 'مستخدم' }}</strong>
                                            <small class="text-muted ms-2">{{ $message->created_at->format('Y-m-d H:i') }}</small>
                                            
                                            <div class="mt-1 p-2 rounded {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-white border' }}">
                                                @if($message->type == 'text')
                                                    {{ $message->body }}
                                                @elseif($message->type == 'image')
                                                    <img src="{{ asset('storage/' . $message->media_url) }}" alt="صورة" class="img-fluid rounded" style="max-width: 200px;">
                                                @elseif($message->type == 'voice')
                                                    <audio controls class="w-100">
                                                        <source src="{{ asset('storage/' . $message->media_url) }}" type="audio/mpeg">
                                                        المتصفح لا يدعم تشغيل الصوت
                                                    </audio>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @elseif(isset($conversation) && !isset($message))
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $conversation->is_group ? asset('images/group-avatar.png') : ($otherParticipant->profile_image_url ?? asset('images/default-avatar.png')) }}" 
                                             class="rounded-circle me-2" alt="صورة المحادثة" width="40" height="40">
                                        <div>
                                            <h6 class="mb-0">{{ $conversation->is_group ? $conversation->title : ($otherParticipant->name ?? 'مستخدم محذوف') }}</h6>
                                            <small class="text-muted">{{ $conversation->participants_count ?? 0 }} مشاركين • {{ $conversation->messages_count ?? 0 }} رسائل</small>
                                        </div>
                                    </div>
                                @elseif(isset($user) && !isset($conversation) && !isset($message))
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->profile_image_url ?? asset('images/default-avatar.png') }}" 
                                             class="rounded-circle me-2" alt="صورة المستخدم" width="40" height="40">
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->city->name ?? '' }}{{ isset($user->city) && isset($user->country) ? '، ' : '' }}{{ $user->country->name ?? '' }}</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        
                        <div class="mb-4">
                            <div class="form-floating">
                                <select class="form-select" id="report-reason" name="reason" required>
                                    <option value="">اختر سبب البلاغ</option>
                                    <option value="inappropriate_content">محتوى غير لائق</option>
                                    <option value="harassment">تحرش أو مضايقة</option>
                                    <option value="spam">رسائل مزعجة (سبام)</option>
                                    <option value="hate_speech">خطاب كراهية</option>
                                    <option value="violence">تهديد أو تحريض على العنف</option>
                                    <option value="fraud">احتيال أو نصب</option>
                                    <option value="copyright">انتهاك حقوق الملكية الفكرية</option>
                                    <option value="impersonation">انتحال شخصية</option>
                                    <option value="other">أخرى</option>
                                </select>
                                <label for="report-reason">سبب البلاغ</label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-floating">
                                <textarea class="form-control" id="report-details" name="details" style="height: 150px" placeholder="تفاصيل البلاغ" required></textarea>
                                <label for="report-details">تفاصيل البلاغ</label>
                            </div>
                            <small class="text-muted">يرجى وصف المشكلة بالتفصيل وتقديم أي معلومات إضافية تساعدنا في اتخاذ الإجراء المناسب.</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="report-evidence" class="form-label">إرفاق أدلة (اختياري)</label>
                            <input class="form-control" type="file" id="report-evidence" name="evidence[]" multiple accept="image/*, audio/*">
                            <small class="text-muted">يمكنك إرفاق صور أو تسجيلات صوتية كأدلة إضافية (الحد الأقصى: 5 ملفات).</small>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="block-user" name="block_user">
                            <label class="form-check-label" for="block-user">
                                حظر هذا المستخدم
                            </label>
                            <div class="text-muted small">عند تحديد هذا الخيار، سيتم حظر المستخدم تلقائياً ولن يتمكن من التواصل معك.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-flag me-1"></i> إرسال البلاغ
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportTypeSelect = document.getElementById('report-type');
        const reportReasonSelect = document.getElementById('report-reason');
        const fileInput = document.getElementById('report-evidence');
        
        // Validate file uploads
        fileInput.addEventListener('change', function() {
            if (this.files.length > 5) {
                alert('يمكنك تحميل 5 ملفات كحد أقصى');
                this.value = '';
            }
            
            let totalSize = 0;
            for (let i = 0; i < this.files.length; i++) {
                totalSize += this.files[i].size;
            }
            
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (totalSize > maxSize) {
                alert('الحجم الإجمالي للملفات يتجاوز 10 ميجابايت');
                this.value = '';
            }
        });
        
        // Show specific form fields based on report type
        if (reportTypeSelect) {
            reportTypeSelect.addEventListener('change', function() {
                const selectedType = this.value;
                
                // Reset reason selection
                reportReasonSelect.value = '';
                
                // Update reason options based on type
                updateReasonOptions(selectedType);
            });
        }
        
        // Function to update reason options based on report type
        function updateReasonOptions(reportType) {
            const commonOptions = [
                {value: '', text: 'اختر سبب البلاغ'},
                {value: 'inappropriate_content', text: 'محتوى غير لائق'},
                {value: 'harassment', text: 'تحرش أو مضايقة'},
                {value: 'hate_speech', text: 'خطاب كراهية'},
                {value: 'violence', text: 'تهديد أو تحريض على العنف'},
                {value: 'other', text: 'أخرى'}
            ];
            
            let specificOptions = [];
            
            switch (reportType) {
                case 'message':
                    specificOptions = [
                        {value: 'spam', text: 'رسائل مزعجة (سبام)'},
                        {value: 'copyright', text: 'انتهاك حقوق الملكية الفكرية'}
                    ];
                    break;
                case 'conversation':
                    specificOptions = [
                        {value: 'spam', text: 'رسائل مزعجة (سبام)'}
                    ];
                    break;
                case 'user':
                    specificOptions = [
                        {value: 'impersonation', text: 'انتحال شخصية'},
                        {value: 'fraud', text: 'احتيال أو نصب'}
                    ];
                    break;
            }
            
            // Combine common and specific options
            const allOptions = [...commonOptions];
            
            // Add specific options after the first item (the empty placeholder)
            if (specificOptions.length > 0) {
                allOptions.splice(1, 0, ...specificOptions);
            }
            
            // Clear and populate the reason select
            reportReasonSelect.innerHTML = '';
            
            allOptions.forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option.value;
                optionElement.textContent = option.text;
                reportReasonSelect.appendChild(optionElement);
            });
        }
    });
</script>
@endpush
