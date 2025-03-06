/**
 * مكتبة مكالمات الصوت لأورا
 * تعتمد على مكتبة PeerJS للاتصالات من نظير إلى نظير
 */
(function() {
    // المتغيرات العامة
    let peer = null;
    let currentCall = null;
    let localStream = null;
    let remoteStream = null;
    let callTimer = null;
    let callDuration = 0;
    let isMuted = false;
    let isSpeakerOn = true;
    let isCallActive = false;
    let remoteAudio = null;
    
    // الإعدادات الافتراضية
    const defaultConfig = {
        host: '/',
        port: 443,
        path: '/peerjs',
        secure: true,
        config: {
            iceServers: [
                { urls: "stun:stun.l.google.com:19302" },
                { urls: "stun:stun1.l.google.com:19302" },
                { urls: "stun:stun2.l.google.com:19302" }
            ]
        }
    };
    
    /**
     * تهيئة المكالمة الصوتية
     * @param {Object|string} options - معرف المستقبل أو كائن بيانات المكالمة
     * @param {string} callType - نوع المكالمة (صوت/فيديو)
     */
    function initializeVoiceCall(options, callType = 'voice') {
        // تهيئة PeerJS
        setupPeerConnection();
        
        let callData = {};
        
        // التحقق من نوع المعلمات
        if (typeof options === 'string') {
            callData = {
                recipientId: options,
                callType: callType,
                isInitiator: true
            };
        } else if (typeof options === 'object') {
            callData = options;
        } else {
            console.error('معلمات غير صالحة لتهيئة المكالمة');
            return;
        }
        
        // إعداد مستمعي الأحداث لعناصر UI
        setupUIEventListeners();
        
        // إذا كان هذا المتصل الأولي، ابدأ المكالمة
        if (callData.isInitiator) {
            startCall(callData.recipientId, callData.callType);
        }
        
        // لاستقبال المكالمات الواردة
        listenForIncomingCalls();
    }
    
    /**
     * إعداد اتصال PeerJS
     */
    function setupPeerConnection() {
        // استخدام معرف المستخدم الحالي
        const userId = window.userId || 'user_' + Math.floor(Math.random() * 10000);
        
        // تهيئة Peer 
        peer = new Peer(userId, defaultConfig);
        
        peer.on('open', (id) => {
            console.log('تم الاتصال بمعرف: ' + id);
        });
        
        peer.on('error', (err) => {
            console.error('خطأ في الاتصال:', err);
            showNotification('حدث خطأ في الاتصال. الرجاء المحاولة مرة أخرى.', 'error');
            endCall();
        });
    }
    
    /**
     * الاستماع للمكالمات الواردة
     */
    function listenForIncomingCalls() {
        peer.on('call', (call) => {
            // حفظ معلومات المكالمة الحالية
            currentCall = call;
            
            // عرض مودال المكالمة الواردة
            const incomingCallModal = new bootstrap.Modal(document.getElementById('incomingCallModal'));
            incomingCallModal.show();
            
            // تعيين معلومات المتصل
            document.getElementById('caller-name').textContent = call.metadata?.callerName || 'مستخدم';
            
            if (call.metadata?.callerAvatar) {
                document.getElementById('caller-avatar').src = call.metadata.callerAvatar;
            }
            
            // تشغيل نغمة الرنين
            playRingtone('incoming');
            
            // مستمع لزر قبول المكالمة
            document.getElementById('accept-call-btn').onclick = function() {
                incomingCallModal.hide();
                stopRingtone();
                
                // طلب الوصول إلى الميكروفون
                navigator.mediaDevices.getUserMedia({ audio: true, video: false })
                    .then((stream) => {
                        localStream = stream;
                        call.answer(stream);
                        handleCallStream(call);
                        updateCallStatus('متصل');
                        startCallTimer();
                        isCallActive = true;
                    })
                    .catch((err) => {
                        console.error('خطأ في الوصول إلى الميكروفون:', err);
                        showNotification('لا يمكن الوصول إلى الميكروفون', 'error');
                        endCall();
                    });
            };
            
            // مستمع لزر رفض المكالمة
            document.getElementById('reject-call-btn').onclick = function() {
                incomingCallModal.hide();
                stopRingtone();
                call.close();
                sendCallDeclined(call.peer);
            };
        });
    }
    
    /**
     * بدء مكالمة جديدة
     * @param {string} recipientId - معرف المستلم
     * @param {string} callType - نوع المكالمة
     */
    function startCall(recipientId, callType = 'voice') {
        updateCallStatus('جاري الاتصال...');
        
        // طلب الوصول إلى الميكروفون
        navigator.mediaDevices.getUserMedia({ audio: true, video: false })
            .then((stream) => {
                localStream = stream;
                
                // الحصول على معلومات المستخدم الحالي
                const callerData = {
                    callerName: window.userName || 'مستخدم',
                    callerAvatar: window.userAvatar || null,
                    callType: callType
                };
                
                // إجراء المكالمة
                const call = peer.call(recipientId, stream, { metadata: callerData });
                currentCall = call;
                
                // تشغيل نغمة الرنين
                playRingtone('outgoing');
                
                // معالجة تدفق المكالمة
                handleCallStream(call);
                
                // إرسال إشعار إلى الخادم بأن هناك مكالمة واردة
                notifyIncomingCall(recipientId, callType);
            })
            .catch((err) => {
                console.error('خطأ في الوصول إلى الميكروفون:', err);
                showNotification('لا يمكن الوصول إلى الميكروفون', 'error');
                updateCallStatus('فشل في بدء المكالمة');
            });
    }
    
    /**
     * معالجة تدفق المكالمة
     * @param {Object} call - كائن المكالمة
     */
    function handleCallStream(call) {
        call.on('stream', (stream) => {
            // إيقاف نغمة الرنين
            stopRingtone();
            
            // حفظ التدفق البعيد
            remoteStream = stream;
            
            // إنشاء عنصر الصوت وإضافته للصفحة
            remoteAudio = document.createElement('audio');
            remoteAudio.srcObject = stream;
            remoteAudio.autoplay = true;
            document.body.appendChild(remoteAudio);
            
            // تحديث واجهة المستخدم
            updateCallStatus('متصل');
            startCallTimer();
            isCallActive = true;
            
            // إيقاف تأثير النبض
            document.querySelector('.avatar-pulse')?.classList.remove('avatar-pulse');
        });
        
        call.on('close', () => {
            endCall();
        });
        
        call.on('error', (err) => {
            console.error('خطأ في المكالمة:', err);
            showNotification('حدث خطأ أثناء المكالمة', 'error');
            endCall();
        });
    }
    
    /**
     * إنهاء المكالمة الحالية
     */
    function endCall() {
        isCallActive = false;
        
        // إيقاف نغمة الرنين
        stopRingtone();
        
        // إيقاف مؤقت المكالمة
        stopCallTimer();
        
        if (currentCall) {
            currentCall.close();
            currentCall = null;
        }
        
        // إغلاق التدفقات
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
            localStream = null;
        }
        
        if (remoteAudio) {
            remoteAudio.srcObject = null;
            remoteAudio.remove();
            remoteAudio = null;
        }
        
        // تحديث واجهة المستخدم
        updateCallStatus('تم إنهاء المكالمة');
        
        // إرسال إشعار إلى الخادم بانتهاء المكالمة
        if (currentCall) {
            notifyCallEnded(currentCall.peer);
        }
        
        // إعادة توجيه المستخدم بعد ثانيتين
        setTimeout(() => {
            window.location.href = '/messaging';
        }, 2000);
    }
    
    /**
     * كتم/إلغاء كتم الميكروفون
     */
    function toggleMute() {
        if (!localStream) return;
        
        isMuted = !isMuted;
        
        localStream.getAudioTracks().forEach(track => {
            track.enabled = !isMuted;
        });
        
        // تحديث زر كتم الصوت
        const muteBtn = document.getElementById('mute-btn');
        if (muteBtn) {
            if (isMuted) {
                muteBtn.innerHTML = '<i class="bi bi-mic-mute-fill"></i>';
                muteBtn.classList.add('btn-danger');
                muteBtn.classList.remove('btn-light');
            } else {
                muteBtn.innerHTML = '<i class="bi bi-mic-fill"></i>';
                muteBtn.classList.remove('btn-danger');
                muteBtn.classList.add('btn-light');
            }
        }
    }
    
    /**
     * تبديل وضع السماعة
     */
    function toggleSpeaker() {
        isSpeakerOn = !isSpeakerOn;
        
        if (remoteAudio) {
            // ضبط مستوى الصوت
            remoteAudio.volume = isSpeakerOn ? 1.0 : 0.2;
        }
        
        // تحديث زر السماعة
        const speakerBtn = document.getElementById('speaker-btn');
        if (speakerBtn) {
            if (isSpeakerOn) {
                speakerBtn.innerHTML = '<i class="bi bi-volume-up-fill"></i>';
            } else {
                speakerBtn.innerHTML = '<i class="bi bi-volume-down-fill"></i>';
            }
        }
    }
    
    /**
     * بدء مؤقت المكالمة
     */
    function startCallTimer() {
        callDuration = 0;
        const timerElement = document.getElementById('call-timer');
        if (timerElement) {
            timerElement.classList.remove('d-none');
        }
        
        callTimer = setInterval(() => {
            callDuration++;
            updateTimerDisplay();
        }, 1000);
    }
    
    /**
     * إيقاف مؤقت المكالمة
     */
    function stopCallTimer() {
        if (callTimer) {
            clearInterval(callTimer);
            callTimer = null;
        }
    }
    
    /**
     * تحديث عرض المؤقت
     */
    function updateTimerDisplay() {
        const timerElement = document.getElementById('call-timer');
        if (!timerElement) return;
        
        const minutes = Math.floor(callDuration / 60);
        const seconds = callDuration % 60;
        
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    
    /**
     * تشغيل نغمة الرنين
     * @param {string} type - نوع النغمة (واردة/صادرة)
     */
    function playRingtone(type) {
        // تأكد من عدم وجود نغمة رنين حالية
        stopRingtone();
        
        // إنشاء عنصر صوت جديد
        const audio = new Audio();
        audio.id = 'ringtone-audio';
        audio.loop = true;
        
        // تحديد مسار الملف حسب نوع النغمة
        if (type === 'incoming') {
            audio.src = '/audio/incoming-call.mp3';
        } else {
            audio.src = '/audio/outgoing-call.mp3';
        }
        
        // تشغيل النغمة
        audio.play().catch(err => console.error('فشل تشغيل نغمة الرنين:', err));
        
        // إضافة للمستند
        document.body.appendChild(audio);
    }
    
    /**
     * إيقاف نغمة الرنين
     */
    function stopRingtone() {
        const audio = document.getElementById('ringtone-audio');
        if (audio) {
            audio.pause();
            audio.remove();
        }
    }
    
    /**
     * تحديث حالة المكالمة
     * @param {string} status - حالة المكالمة النصية
     */
    function updateCallStatus(status) {
        const statusElement = document.getElementById('call-status');
        if (statusElement) {
            statusElement.textContent = status;
        }
        
        const connectionStatusElement = document.getElementById('call-connection-status');
        if (connectionStatusElement) {
            connectionStatusElement.textContent = status;
        }
    }
    
    /**
     * إشعار المستلم بمكالمة واردة
     * @param {string} recipientId - معرف المستلم
     * @param {string} callType - نوع المكالمة
     */
    function notifyIncomingCall(recipientId, callType) {
        const formData = new FormData();
        formData.append('recipient_id', recipientId);
        formData.append('call_type', callType);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
        
        fetch('/messaging/voice-call/notify', {
            method: 'POST',
            body: formData
        })
        .catch(err => console.error('فشل إشعار المستلم بالمكالمة:', err));
    }
    
    /**
     * إشعار برفض المكالمة
     * @param {string} userId - معرف المستخدم
     */
    function sendCallDeclined(userId) {
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
        
        fetch('/messaging/voice-call/decline', {
            method: 'POST',
            body: formData
        })
        .catch(err => console.error('فشل إرسال إشعار رفض المكالمة:', err));
    }
    
    /**
     * إشعار بانتهاء المكالمة
     * @param {string} userId - معرف المستخدم
     */
    function notifyCallEnded(userId) {
        const formData = new FormData();
        formData.append('user_id', userId);
        formData.append('duration', callDuration);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));
        
        fetch('/messaging/voice-call/end', {
            method: 'POST',
            body: formData
        })
        .catch(err => console.error('فشل إرسال إشعار انتهاء المكالمة:', err));
    }
    
    /**
     * إعداد مستمعي أحداث واجهة المستخدم
     */
    function setupUIEventListeners() {
        // زر إنهاء المكالمة
        const endCallBtn = document.getElementById('end-call-btn');
        if (endCallBtn) {
            endCallBtn.addEventListener('click', endCall);
        }
        
        // زر كتم الصوت
        const muteBtn = document.getElementById('mute-btn');
        if (muteBtn) {
            muteBtn.addEventListener('click', toggleMute);
        }
        
        // زر تبديل السماعة
        const speakerBtn = document.getElementById('speaker-btn');
        if (speakerBtn) {
            speakerBtn.addEventListener('click', toggleSpeaker);
        }
        
        // مستوى صوت الميكروفون
        const micVolumeControl = document.getElementById('mic-volume');
        if (micVolumeControl) {
            micVolumeControl.addEventListener('input', function() {
                if (localStream) {
                    const gainNode = new GainNode(new AudioContext(), { gain: this.value / 100 });
                    const source = new MediaStreamAudioSourceNode(gainNode.context, { mediaStream: localStream });
                    source.connect(gainNode);
                }
            });
        }
        
        // مستوى صوت السماعة
        const speakerVolumeControl = document.getElementById('speaker-volume');
        if (speakerVolumeControl) {
            speakerVolumeControl.addEventListener('input', function() {
                if (remoteAudio) {
                    remoteAudio.volume = this.value / 100;
                }
            });
        }
        
        // زر تصغير المكالمة
        const minimizeCallBtn = document.querySelector('.minimize-call-btn');
        if (minimizeCallBtn) {
            minimizeCallBtn.addEventListener('click', function() {
                // يمكن تنفيذ منطق تصغير نافذة المكالمة هنا
                showNotification('هذه الميزة غير متوفرة حاليًا', 'info');
            });
        }
    }
    
    /**
     * عرض إشعار للمستخدم
     * @param {string} message - نص الإشعار
     * @param {string} type - نوع الإشعار (info, success, error)
     */
    function showNotification(message, type = 'info') {
        // استخدام showNotification العالمية من enhanced-messaging.js إذا كانت متوفرة
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
            return;
        }
        
        // التنفيذ المحلي إذا لم تكن الدالة العالمية متوفرة
        alert(message);
    }
    
    // تصدير الدوال العامة
    window.initializeVoiceCall = initializeVoiceCall;
    window.endCall = endCall;
    
})(); // نهاية IIFE