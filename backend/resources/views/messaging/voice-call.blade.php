@extends('layouts.app')

@section('title', 'مكالمة صوتية')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/enhanced-messaging.css') }}">
<style>
    .call-container {
        height: calc(100vh - 100px);
        background-color: #f8f9fa;
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }
    
    .call-header {
        background-color: #0d6efd;
        color: white;
        padding: 1rem;
    }
    
    .call-body {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: calc(100% - 130px);
    }
    
    .call-status {
        font-size: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .call-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }
    
    .call-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }
    
    .call-button {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    .avatar-pulse {
        animation: pulse 2s infinite;
        position: relative;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7);
        }
        70% {
            box-shadow: 0 0 0 20px rgba(13, 110, 253, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(13, 110, 253, 0);
        }
    }
    
    .timer {
        font-size: 1.25rem;
        letter-spacing: 2px;
        margin-bottom: 2rem;
    }
    
    .call-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1rem;
        text-align: center;
    }
    
    .volume-controls {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="call-container">
                <div class="call-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $recipient->name ?? 'مستخدم' }}</h5>
                        <small id="call-connection-status">جاري الاتصال...</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link text-white minimize-call-btn">
                            <i class="bi bi-dash-lg"></i>
                        </button>
                    </div>
                </div>
                
                <div class="call-body">
                    <div class="avatar-pulse">
                        <img src="{{ $recipient->profile_photo ?? asset('images/default-avatar.png') }}" class="call-avatar" alt="{{ $recipient->name ?? 'مستخدم' }}">
                    </div>
                    
                    <div class="call-status" id="call-status">جاري الاتصال...</div>
                    
                    <div class="timer d-none" id="call-timer">00:00</div>
                    
                    <div class="call-actions">
                        <button class="call-button btn-light" id="mute-btn">
                            <i class="bi bi-mic-fill"></i>
                        </button>
                        
                        <button class="call-button btn-danger" id="end-call-btn">
                            <i class="bi bi-telephone-x-fill"></i>
                        </button>
                        
                        <button class="call-button btn-light" id="speaker-btn">
                            <i class="bi bi-volume-up-fill"></i>
                        </button>
                    </div>
                </div>
                
                <div class="call-footer">
                    <div class="volume-controls">
                        <div>
                            <label for="mic-volume" class="form-label small">مستوى الميكروفون</label>
                            <input type="range" class="form-range" id="mic-volume" min="0" max="100" value="100">
                        </div>
                        <div>
                            <label for="speaker-volume" class="form-label small">مستوى الصوت</label>
                            <input type="range" class="form-range" id="speaker-volume" min="0" max="100" value="100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- مودال إشعار المكالمة الواردة -->
<div class="modal fade" id="incomingCallModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="avatar-pulse mb-3">
                    <img src="{{ asset('images/default-avatar.png') }}" id="caller-avatar" class="call-avatar" alt="المتصل">
                </div>
                <h5 class="mb-1" id="caller-name">مستخدم</h5>
                <p class="text-muted mb-4">يتصل بك...</p>
                
                <div class="d-flex justify-content-center gap-3">
                    <button class="btn btn-danger px-4" id="reject-call-btn">
                        <i class="bi bi-telephone-x-fill me-2"></i> رفض
                    </button>
                    <button class="btn btn-success px-4" id="accept-call-btn">
                        <i class="bi bi-telephone-fill me-2"></i> قبول
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/peerjs/1.4.7/peerjs.min.js"></script>
<script src="{{ asset('js/voice-call.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تهيئة المكالمة
        const callData = {
            callId: '{{ $callId ?? '' }}',
            callType: '{{ $callType ?? 'voice' }}',
            recipientId: '{{ $recipient->id ?? '' }}',
            isInitiator: {{ $isInitiator ?? 'false' }}
        };
        
        if (window.initializeVoiceCall) {
            window.initializeVoiceCall(callData);
        }
    });
</script>
@endsection