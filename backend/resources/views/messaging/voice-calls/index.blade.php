@extends('layouts.dashboard')

@section('title', 'مكالمة صوتية')

@section('page-title', 'مكالمة صوتية مع ' . ($contact->name ?? 'مستخدم'))

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
            <x-card class="border-0 shadow-sm text-center voice-call-container">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-light">
                            <i class="bi bi-arrow-right"></i> عودة
                        </a>
                        <h5 class="mb-0">مكالمة صوتية</h5>
                        <div></div> <!-- للمحافظة على التوازن -->
                    </div>
                </x-slot>

                <div class="voice-call-content py-5">
                    <div class="user-avatar mb-4">
                        <img src="{{ $contact->profile_image_url ?? asset('images/default-avatar.png') }}" 
                             class="rounded-circle img-thumbnail" alt="صورة المستخدم" width="150" height="150">
                    </div>
                    
                    <h4 class="mb-2">{{ $contact->name ?? 'مستخدم' }}</h4>
                    <p class="text-muted mb-4" id="call-status">جاري الاتصال...</p>
                    
                    <div class="call-timer mb-4 d-none" id="call-timer">
                        <h5><span id="minutes">00</span>:<span id="seconds">00</span></h5>
                    </div>
                    
                    <div class="call-controls">
                        <button class="btn btn-light rounded-circle mx-2 control-btn" id="mute-btn" title="كتم الصوت">
                            <i class="bi bi-mic-fill"></i>
                        </button>
                        <button class="btn btn-danger rounded-circle mx-2 control-btn" id="end-call-btn" title="إنهاء المكالمة">
                            <i class="bi bi-telephone-x-fill"></i>
                        </button>
                        <button class="btn btn-light rounded-circle mx-2 control-btn" id="speaker-btn" title="مكبر الصوت">
                            <i class="bi bi-volume-up-fill"></i>
                        </button>
                    </div>
                </div>

                <x-slot name="footer">
                    <div class="call-status-footer">
                        <small class="text-muted" id="call-quality">جودة الاتصال: جيدة</small>
                    </div>
                </x-slot>
            </x-card>
        </div>
    </div>
    
    <!-- مودال انتهاء المكالمة -->
    <div class="modal fade" id="callEndedModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">انتهت المكالمة</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-telephone-x display-1 text-danger"></i>
                    </div>
                    <h5 class="mb-3">انتهت المكالمة مع <span id="call-contact-name">{{ $contact->name ?? 'مستخدم' }}</span></h5>
                    <p class="mb-1">مدة المكالمة: <span id="call-duration">00:00</span></p>
                    <p class="text-muted" id="call-end-reason">تم إنهاء المكالمة من قبلك</p>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('messaging.conversations.show', $conversation->id ?? 0) }}" class="btn btn-primary">العودة للمحادثة</a>
                    <a href="{{ route('messaging.contacts.index') }}" class="btn btn-outline-secondary">جهات الاتصال</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- مودال رفض المكالمة -->
    <div class="modal fade" id="callDeclinedModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تم رفض المكالمة</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-telephone-x display-1 text-danger"></i>
                    </div>
                    <h5 class="mb-3">تم رفض المكالمة من قبل <span>{{ $contact->name ?? 'مستخدم' }}</span></h5>
                    <p class="text-muted">قد يكون المستخدم مشغولاً الآن، يمكنك المحاولة مرة أخرى لاحقاً</p>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('messaging.conversations.show', $conversation->id ?? 0) }}" class="btn btn-primary">العودة للمحادثة</a>
                    <a href="{{ route('messaging.contacts.index') }}" class="btn btn-outline-secondary">جهات الاتصال</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- مودال فشل المكالمة -->
    <div class="modal fade" id="callFailedModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">فشل الاتصال</h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
                    </div>
                    <h5 class="mb-3">فشل الاتصال بـ <span>{{ $contact->name ?? 'مستخدم' }}</span></h5>
                    <p class="text-muted">تعذر إجراء الاتصال، يرجى التحقق من اتصالك بالإنترنت والمحاولة مرة أخرى</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="retry-call-btn">إعادة المحاولة</button>
                    <a href="{{ route('messaging.conversations.show', $conversation->id ?? 0) }}" class="btn btn-outline-secondary">العودة للمحادثة</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .voice-call-container {
        min-height: 500px;
    }
    
    .user-avatar img {
        border: 5px solid #f8f9fa;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .call-controls {
        margin-top: 2rem;
    }
    
    .control-btn {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    
    .control-btn:hover {
        transform: scale(1.1);
    }
    
    #end-call-btn {
        width: 70px;
        height: 70px;
    }
    
    .muted {
        background-color: #dc3545 !important;
        color: white !important;
    }
    
    .speaker-on {
        background-color: #0d6efd !important;
        color: white !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const callStatus = document.getElementById('call-status');
        const callTimer = document.getElementById('call-timer');
        const minutes = document.getElementById('minutes');
        const seconds = document.getElementById('seconds');
        const muteBtn = document.getElementById('mute-btn');
        const speakerBtn = document.getElementById('speaker-btn');
        const endCallBtn = document.getElementById('end-call-btn');
        const callQuality = document.getElementById('call-quality');
        const callDuration = document.getElementById('call-duration');
        const callEndReason = document.getElementById('call-end-reason');
        const retryCallBtn = document.getElementById('retry-call-btn');
        
        let callEndedModal, callDeclinedModal, callFailedModal;
        let timerInterval;
        let totalSeconds = 0;
        let isMuted = false;
        let isSpeakerOn = false;
        
        // Initialize Bootstrap modals
        callEndedModal = new bootstrap.Modal(document.getElementById('callEndedModal'));
        callDeclinedModal = new bootstrap.Modal(document.getElementById('callDeclinedModal'));
        callFailedModal = new bootstrap.Modal(document.getElementById('callFailedModal'));
        
        // Simulate call connection
        setTimeout(() => {
            // Randomly choose to connect, decline, or fail (for demo purposes)
            const outcome = Math.random();
            
            if (outcome < 0.8) { // 80% chance to connect
                callStatus.textContent = 'متصل';
                callTimer.classList.remove('d-none');
                startTimer();
                
                // Simulate quality changes
                simulateQualityChanges();
            } else if (outcome < 0.9) { // 10% chance to decline
                callDeclinedModal.show();
            } else { // 10% chance to fail
                callFailedModal.show();
            }
        }, 3000);
        
        // Start timer function
        function startTimer() {
            timerInterval = setInterval(() => {
                totalSeconds++;
                updateTimerDisplay();
            }, 1000);
        }
        
        // Update timer display
        function updateTimerDisplay() {
            const mins = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
            const secs = (totalSeconds % 60).toString().padStart(2, '0');
            minutes.textContent = mins;
            seconds.textContent = secs;
        }
        
        // Simulate call quality changes
        function simulateQualityChanges() {
            const qualities = ['ممتازة', 'جيدة', 'متوسطة', 'ضعيفة'];
            
            setInterval(() => {
                const randomQuality = qualities[Math.floor(Math.random() * qualities.length)];
                callQuality.textContent = `جودة الاتصال: ${randomQuality}`;
                
                if (randomQuality === 'ضعيفة') {
                    callQuality.classList.add('text-danger');
                    callQuality.classList.remove('text-muted');
                } else {
                    callQuality.classList.remove('text-danger');
                    callQuality.classList.add('text-muted');
                }
            }, 10000); // Change every 10 seconds
        }
        
        // Handle mute button
        muteBtn.addEventListener('click', function() {
            isMuted = !isMuted;
            if (isMuted) {
                this.classList.add('muted');
                this.innerHTML = '<i class="bi bi-mic-mute-fill"></i>';
            } else {
                this.classList.remove('muted');
                this.innerHTML = '<i class="bi bi-mic-fill"></i>';
            }
        });
        
        // Handle speaker button
        speakerBtn.addEventListener('click', function() {
            isSpeakerOn = !isSpeakerOn;
            if (isSpeakerOn) {
                this.classList.add('speaker-on');
            } else {
                this.classList.remove('speaker-on');
            }
        });
        
        // Handle end call button
        endCallBtn.addEventListener('click', function() {
            endCall('تم إنهاء المكالمة من قبلك');
        });
        
        // Handle retry call button
        if (retryCallBtn) {
            retryCallBtn.addEventListener('click', function() {
                location.reload();
            });
        }
        
        // End call function
        function endCall(reason) {
            clearInterval(timerInterval);
            
            // Update call end modal
            const mins = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
            const secs = (totalSeconds % 60).toString().padStart(2, '0');
            callDuration.textContent = `${mins}:${secs}`;
            callEndReason.textContent = reason;
            
            // Show modal
            callEndedModal.show();
        }
        
        // Simulate random call end after some time (for demo purposes)
        setTimeout(() => {
            const shouldEnd = Math.random() < 0.3; // 30% chance to end randomly
            if (shouldEnd) {
                endCall('تم إنهاء المكالمة من قبل المستخدم الآخر');
            }
        }, 60000); // After 1 minute
    });
</script>
@endpush
