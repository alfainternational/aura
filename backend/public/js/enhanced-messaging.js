/**
 * ملف جافاسكريبت للمحادثات المطورة
 */

// متغيرات عالمية
let mediaRecorder;
let audioChunks = [];
let isRecording = false;
let recordingTimer;
let recordingDuration = 0;
let currentAudioPlayer;

/**
 * تهيئة صفحة المحادثة المطورة
 */
function initEnhancedChat() {
    // إعداد واجهة المستخدم المتقدمة
    setupUI();
    
    // إعداد المستمعين للأحداث
    setupEventListeners();
    
    // إعداد وظائف البث المباشر للرسائل
    setupRealtimeListeners();
}

/**
 * إعداد واجهة المستخدم
 */
function setupUI() {
    // تمديد ارتفاع مربع النص حسب المحتوى
    autoResizeTextarea();
    
    // تهيئة القوائم المنسدلة
    initializeSelects();
    
    // تحميل المستخدمين للمحادثات الجديدة
    loadUsersForConversations();
}

/**
 * تمديد ارتفاع مربع النص حسب المحتوى
 */
function autoResizeTextarea() {
    const messageInput = document.getElementById('message-input');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if (this.scrollHeight > 150) {
                this.style.overflowY = 'auto';
            } else {
                this.style.overflowY = 'hidden';
            }
        });
    }
}

/**
 * تهيئة القوائم المنسدلة
 */
function initializeSelects() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('#user_id, #group_participants').select2({
            placeholder: 'اختر المستخدمين',
            dir: 'rtl',
            language: 'ar'
        });
    }
}

/**
 * تحميل المستخدمين للمحادثات الجديدة
 */
function loadUsersForConversations() {
    fetch('/api/users/list')
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                const userIdSelect = document.getElementById('user_id');
                const groupParticipantsSelect = document.getElementById('group_participants');
                
                data.data.forEach(user => {
                    const option = new Option(user.name, user.id);
                    
                    if (userIdSelect) {
                        userIdSelect.appendChild(option.cloneNode(true));
                    }
                    
                    if (groupParticipantsSelect) {
                        groupParticipantsSelect.appendChild(option.cloneNode(true));
                    }
                });
            }
        })
        .catch(error => console.error('Error loading users:', error));
}

/**
 * إعداد المستمعين للأحداث
 */
function setupEventListeners() {
    // زر إنشاء محادثة جديدة
    const submitConversationBtn = document.getElementById('submitConversationBtn');
    if (submitConversationBtn) {
        submitConversationBtn.addEventListener('click', createNewConversation);
    }
    
    // نموذج إرسال الرسائل
    const messageForm = document.getElementById('message-form');
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }
    
    // البحث في المحادثات
    const conversationSearch = document.getElementById('conversation-search');
    if (conversationSearch) {
        conversationSearch.addEventListener('input', searchConversations);
    }
    
    // المحادثات
    setupConversationClickListeners();
    
    // الرد على الرسائل
    setupReplyListeners();
    
    // توجيه الرسائل
    setupForwardListeners();
    
    // تسجيل الرسائل الصوتية
    setupVoiceRecordingListeners();
    
    // رفع الصور
    setupImageUploadListeners();
    
    // زر البحث في الرسائل
    const searchMessagesBtn = document.getElementById('search-messages-btn');
    if (searchMessagesBtn) {
        searchMessagesBtn.addEventListener('click', function() {
            const searchModal = new bootstrap.Modal(document.getElementById('searchMessagesModal'));
            searchModal.show();
        });
    }
    
    // زر المكالمة الصوتية
    const voiceCallBtn = document.getElementById('voice-call-btn');
    if (voiceCallBtn) {
        voiceCallBtn.addEventListener('click', initiateVoiceCall);
    }
    
    // زر تغيير الحالة
    const statusToggle = document.getElementById('status-toggle');
    if (statusToggle) {
        statusToggle.addEventListener('click', toggleOnlineStatus);
    }
}

/**
 * إعداد مستمعين الرد على الرسائل
 */
function setupReplyListeners() {
    // أزرار الرد
    document.querySelectorAll('.reply-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const messageId = this.getAttribute('data-message-id');
            const senderName = this.getAttribute('data-sender-name');
            const messageText = this.getAttribute('data-message-text');
            
            document.getElementById('reply-container').classList.remove('d-none');
            document.getElementById('replied-to-id').value = messageId;
            document.getElementById('reply-sender-name').textContent = senderName;
            document.getElementById('reply-message-text').textContent = messageText;
            
            document.getElementById('message-input').focus();
        });
    });
    
    // زر إلغاء الرد
    const cancelReplyBtn = document.getElementById('cancel-reply-btn');
    if (cancelReplyBtn) {
        cancelReplyBtn.addEventListener('click', function() {
            document.getElementById('reply-container').classList.add('d-none');
            document.getElementById('replied-to-id').value = '';
        });
    }
}

/**
 * إعداد مستمعين توجيه الرسائل
 */
function setupForwardListeners() {
    // أزرار التوجيه
    document.querySelectorAll('.forward-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const messageId = this.getAttribute('data-message-id');
            document.getElementById('forward-message-id').value = messageId;
            loadConversationsForForward();
            
            const forwardMessageModal = new bootstrap.Modal(document.getElementById('forwardMessageModal'));
            forwardMessageModal.show();
        });
    });
    
    // زر تأكيد التوجيه
    const submitForwardBtn = document.getElementById('submit-forward-btn');
    if (submitForwardBtn) {
        submitForwardBtn.addEventListener('click', forwardMessage);
    }
}

/**
 * تحميل المحادثات لتوجيه الرسائل
 */
function loadConversationsForForward() {
    const forwardConversationsList = document.getElementById('forward-conversations-list');
    if (!forwardConversationsList) return;
    
    forwardConversationsList.innerHTML = '<div class="text-center py-2"><div class="spinner-border spinner-border-sm" role="status"></div></div>';
    
    fetch('/api/conversations/list')
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                forwardConversationsList.innerHTML = '';
                
                data.data.forEach(conversation => {
                    const conversationItem = document.createElement('div');
                    conversationItem.className = 'form-check mb-2';
                    
                    const title = conversation.is_group ? conversation.title : 
                                 (conversation.other_participant ? conversation.other_participant.name : 'مستخدم محذوف');
                    
                    conversationItem.innerHTML = `
                        <input class="form-check-input" type="checkbox" value="${conversation.id}" id="forward-conversation-${conversation.id}">
                        <label class="form-check-label d-flex align-items-center" for="forward-conversation-${conversation.id}">
                            ${conversation.is_group ? 
                              '<div class="group-icon me-2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;"><i class="bi bi-people-fill small"></i></div>' : 
                              `<img src="${conversation.other_participant && conversation.other_participant.profile_photo_path ? 
                                        '/storage/' + conversation.other_participant.profile_photo_path : 
                                        '/images/default-avatar.png'}" class="rounded-circle me-2" width="32" height="32" alt="">`}
                            ${title}
                        </label>
                    `;
                    
                    forwardConversationsList.appendChild(conversationItem);
                });
                
                // إضافة مستمع بحث المحادثات
                const forwardSearch = document.getElementById('forward-search');
                if (forwardSearch) {
                    forwardSearch.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase();
                        document.querySelectorAll('#forward-conversations-list .form-check').forEach(item => {
                            const label = item.querySelector('label').textContent.toLowerCase();
                            item.style.display = label.includes(searchTerm) ? 'block' : 'none';
                        });
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error loading conversations:', error);
            forwardConversationsList.innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء تحميل المحادثات</div>';
        });
}

/**
 * توجيه الرسالة للمحادثات المحددة
 */
function forwardMessage() {
    const messageId = document.getElementById('forward-message-id').value;
    const comment = document.getElementById('forward-message-text').value;
    const selectedConversations = [];
    
    document.querySelectorAll('#forward-conversations-list input[type="checkbox"]:checked').forEach(checkbox => {
        selectedConversations.push(checkbox.value);
    });
    
    if (selectedConversations.length === 0) {
        alert('الرجاء اختيار محادثة واحدة على الأقل');
        return;
    }
    
    const formData = new FormData();
    formData.append('message_id', messageId);
    formData.append('conversations', JSON.stringify(selectedConversations));
    formData.append('comment', comment);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch('/api/messages/forward', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // إغلاق المودال
            bootstrap.Modal.getInstance(document.getElementById('forwardMessageModal')).hide();
            
            // إظهار رسالة نجاح
            showNotification('تم توجيه الرسالة بنجاح', 'success');
        } else {
            showNotification(data.message || 'حدث خطأ أثناء توجيه الرسالة', 'error');
        }
    })
    .catch(error => {
        console.error('Error forwarding message:', error);
        showNotification('حدث خطأ أثناء توجيه الرسالة', 'error');
    });
}

/**
 * إعداد مستمعين التسجيل الصوتي
 */
function setupVoiceRecordingListeners() {
    const voiceButton = document.getElementById('voice-button');
    const cancelRecordingBtn = document.getElementById('cancel-recording-btn');
    const finishRecordingBtn = document.getElementById('finish-recording-btn');
    
    if (voiceButton) {
        voiceButton.addEventListener('click', toggleVoiceRecording);
    }
    
    if (cancelRecordingBtn) {
        cancelRecordingBtn.addEventListener('click', cancelVoiceRecording);
    }
    
    if (finishRecordingBtn) {
        finishRecordingBtn.addEventListener('click', finishVoiceRecording);
    }
    
    // تشغيل الرسائل الصوتية
    document.querySelectorAll('.play-voice-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const audioUrl = this.getAttribute('data-audio-url');
            playVoiceMessage(this, audioUrl);
        });
    });
}

/**
 * تبديل حالة تسجيل الصوت
 */
function toggleVoiceRecording() {
    if (!isRecording) {
        startVoiceRecording();
    } else {
        finishVoiceRecording();
    }
}

/**
 * بدء تسجيل صوتي
 */
function startVoiceRecording() {
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                // إظهار حاوية التسجيل
                document.getElementById('voice-recording-container').classList.remove('d-none');
                
                // إنشاء مسجل الوسائط
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                
                // إضافة مستمع لبيانات الصوت
                mediaRecorder.addEventListener('dataavailable', e => {
                    audioChunks.push(e.data);
                });
                
                // بدء التسجيل
                mediaRecorder.start();
                isRecording = true;
                
                // بدء مؤقت التسجيل
                startRecordingTimer();
                
                // إضافة مستمع لإيقاف التسجيل عند الخروج من الصفحة
                window.addEventListener('beforeunload', cancelVoiceRecording);
            })
            .catch(error => {
                console.error('Error accessing microphone:', error);
                showNotification('لا يمكن الوصول إلى الميكروفون. الرجاء التحقق من صلاحيات الوصول.', 'error');
            });
    } else {
        showNotification('متصفحك لا يدعم تسجيل الصوت', 'error');
    }
}

/**
 * تحديث مؤقت التسجيل
 */
function startRecordingTimer() {
    const recordingTimeElement = document.querySelector('.recording-time');
    recordingDuration = 0;
    
    if (recordingTimeElement) {
        recordingTimer = setInterval(() => {
            recordingDuration++;
            const minutes = Math.floor(recordingDuration / 60);
            const seconds = recordingDuration % 60;
            recordingTimeElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            // الحد الأقصى للتسجيل هو 5 دقائق
            if (recordingDuration >= 300) {
                finishVoiceRecording();
            }
        }, 1000);
    }
}

/**
 * إلغاء التسجيل الصوتي
 */
function cancelVoiceRecording() {
    if (isRecording) {
        clearInterval(recordingTimer);
        
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
        }
        
        document.getElementById('voice-recording-container').classList.add('d-none');
        isRecording = false;
        audioChunks = [];
        
        window.removeEventListener('beforeunload', cancelVoiceRecording);
    }
}

/**
 * إنهاء التسجيل الصوتي وإرساله
 */
function finishVoiceRecording() {
    if (isRecording) {
        clearInterval(recordingTimer);
        
        mediaRecorder.addEventListener('stop', () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/mpeg' });
            sendVoiceMessage(audioBlob, recordingDuration);
        });
        
        mediaRecorder.stop();
        mediaRecorder.stream.getTracks().forEach(track => track.stop());
        
        document.getElementById('voice-recording-container').classList.add('d-none');
        isRecording = false;
        
        window.removeEventListener('beforeunload', cancelVoiceRecording);
    }
}

/**
 * تشغيل رسالة صوتية
 */
function playVoiceMessage(button, audioUrl) {
    // إيقاف أي صوت حالي
    if (currentAudioPlayer) {
        currentAudioPlayer.pause();
        currentAudioPlayer = null;
        
        // إعادة تعيين أيقونات جميع الأزرار
        document.querySelectorAll('.play-voice-btn i').forEach(icon => {
            icon.className = 'bi bi-play-fill';
        });
    }
    
    // تشغيل الصوت الجديد
    const audio = new Audio(audioUrl);
    currentAudioPlayer = audio;
    
    // تغيير أيقونة الزر إلى إيقاف مؤقت
    const icon = button.querySelector('i');
    icon.className = 'bi bi-pause-fill';
    
    audio.play();
    
    // إضافة مستمعين للصوت
    audio.addEventListener('ended', () => {
        icon.className = 'bi bi-play-fill';
        currentAudioPlayer = null;
    });
    
    audio.addEventListener('pause', () => {
        icon.className = 'bi bi-play-fill';
    });
    
    // إضافة مستمع نقر للإيقاف المؤقت
    button.addEventListener('click', function togglePlay(e) {
        if (currentAudioPlayer && currentAudioPlayer.paused) {
            currentAudioPlayer.play();
            icon.className = 'bi bi-pause-fill';
        } else if (currentAudioPlayer) {
            currentAudioPlayer.pause();
            icon.className = 'bi bi-play-fill';
        }
        // هذا لمنع تنفيذ وظيفة الزر الأصلية مرة أخرى
        e.stopPropagation();
        
        // إزالة المستمع بعد النقر لمنع إضافة مستمعين متعددة
        button.removeEventListener('click', togglePlay);
    }, { once: true });
}

/**
 * إعداد مستمعين رفع الصور
 */
function setupImageUploadListeners() {
    const imageButton = document.getElementById('image-button');
    const imageInput = document.getElementById('image-input');
    
    if (imageButton && imageInput) {
        imageButton.addEventListener('click', function() {
            imageInput.click();
        });
        
        imageInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                sendImageMessage(this.files[0]);
            }
        });
    }
}

/**
 * إنشاء محادثة جديدة
 */
function createNewConversation() {
    const activeTab = document.querySelector('#conversationTabs .nav-link.active');
    let form;
    
    if (activeTab.id === 'individual-tab') {
        form = document.getElementById('individualConversationForm');
    } else {
        form = document.getElementById('groupConversationForm');
    }
    
    if (form) {
        form.submit();
    }
}

/**
 * البحث في المحادثات
 */
function searchConversations() {
    const searchTerm = this.value.toLowerCase();
    
    document.querySelectorAll('.conversation-item').forEach(item => {
        const title = item.querySelector('.conversation-title').textContent.toLowerCase();
        const latest = item.querySelector('.conversation-latest').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || latest.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

/**
 * إعداد مستمعين نقر المحادثات
 */
function setupConversationClickListeners() {
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            const conversationId = this.getAttribute('data-conversation-id');
            window.location.href = `/messaging/enhanced-chat/${conversationId}`;
        });
    });
}

/**
 * إعداد مستمعي البث المباشر للرسائل
 */
function setupRealtimeListeners() {
    // استخدام بوشر أو سوكت.أيو للاستماع للرسائل الجديدة
    // هذا مجرد مثال، يجب تكييفه وفقًا لنظام البث الخاص بك
    
    if (typeof window.Echo !== 'undefined') {
        // استخدام Laravel Echo
        const conversationId = document.querySelector('[data-conversation-id]')?.getAttribute('data-conversation-id');
        
        if (conversationId) {
            window.Echo.private(`conversation.${conversationId}`)
                .listen('NewMessage', (e) => {
                    // التحقق مما إذا كانت الرسالة موجودة بالفعل
                    if (!document.getElementById(`message-${e.message.id}`)) {
                        appendMessage(e.message);
                        scrollToBottom();
                        
                        // تحديث حالة القراءة إذا كانت الرسالة من مستخدم آخر
                        if (e.message.sender_id !== window.userId) {
                            updateMessageReadStatus(e.message.id);
                        }
                    }
                })
                .listen('MessageRead', (e) => {
                    // تحديث حالة القراءة للرسائل
                    updateMessageReadIndicators(e.message_ids);
                });
        }
    }
}

/**
 * إرسال رسالة نصية
 */
function sendMessage() {
    const messageInput = document.getElementById('message-input');
    const repliedToId = document.getElementById('replied-to-id').value;
    
    if (!messageInput.value.trim()) return;
    
    const formData = new FormData();
    formData.append('message', messageInput.value);
    formData.append('type', 'text');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    if (repliedToId) {
        formData.append('replied_to_id', repliedToId);
    }
    
    const conversationId = window.location.pathname.split('/').pop();
    
    fetch(`/messaging/messages/${conversationId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageInput.value = '';
            messageInput.style.height = 'auto';
            
            // إعادة تعيين مربع الرد
            document.getElementById('reply-container').classList.add('d-none');
            document.getElementById('replied-to-id').value = '';
            
            // في حالة عدم استخدام البث المباشر، نضيف الرسالة يدويًا
            if (typeof window.Echo === 'undefined') {
                appendMessage(data.message);
                scrollToBottom();
            }
        } else {
            showNotification(data.message || 'حدث خطأ أثناء إرسال الرسالة', 'error');
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        showNotification('حدث خطأ أثناء إرسال الرسالة', 'error');
    });
}

/**
 * إرسال رسالة صوتية
 */
function sendVoiceMessage(audioBlob, duration) {
    const formData = new FormData();
    formData.append('audio', audioBlob, 'voice-message.mp3');
    formData.append('duration', duration);
    formData.append('type', 'voice');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const conversationId = window.location.pathname.split('/').pop();
    
    fetch(`/messaging/messages/${conversationId}/voice`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // في حالة عدم استخدام البث المباشر، نضيف الرسالة يدويًا
            if (typeof window.Echo === 'undefined') {
                appendMessage(data.message);
                scrollToBottom();
            }
        } else {
            showNotification(data.message || 'حدث خطأ أثناء إرسال الرسالة الصوتية', 'error');
        }
    })
    .catch(error => {
        console.error('Error sending voice message:', error);
        showNotification('حدث خطأ أثناء إرسال الرسالة الصوتية', 'error');
    });
}

/**
 * إرسال رسالة صورة
 */
function sendImageMessage(imageFile) {
    const formData = new FormData();
    formData.append('image', imageFile);
    formData.append('type', 'image');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    const conversationId = window.location.pathname.split('/').pop();
    
    fetch(`/messaging/messages/${conversationId}/image`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // في حالة عدم استخدام البث المباشر، نضيف الرسالة يدويًا
            if (typeof window.Echo === 'undefined') {
                appendMessage(data.message);
                scrollToBottom();
            }
            
            // إعادة تعيين مدخل الصورة
            document.getElementById('image-input').value = '';
        } else {
            showNotification(data.message || 'حدث خطأ أثناء إرسال الصورة', 'error');
        }
    })
    .catch(error => {
        console.error('Error sending image:', error);
        showNotification('حدث خطأ أثناء إرسال الصورة', 'error');
    });
}

/**
 * إضافة رسالة جديدة إلى المحادثة
 */
function appendMessage(message) {
    const messagesWrapper = document.querySelector('.messages-wrapper');
    if (!messagesWrapper) return;
    
    const messageItem = document.createElement('div');
    messageItem.className = `message-item ${message.sender_id == window.userId ? 'outgoing' : 'incoming'}`;
    messageItem.id = `message-${message.id}`;
    
    let messageHTML = '';
    
    // إضافة صورة المرسل للرسائل الواردة
    if (message.sender_id != window.userId) {
        messageHTML += `
            <div class="message-avatar">
                <img src="${message.sender_photo || '/images/default-avatar.png'}" 
                    class="rounded-circle" width="36" height="36" alt="${message.sender_name}">
            </div>
        `;
    }
    
    messageHTML += `<div class="message-content-wrapper">`;
    
    // إضافة اسم المرسل في المحادثات الجماعية
    if (message.conversation_is_group && message.sender_id != window.userId) {
        messageHTML += `<div class="sender-name small mb-1">${message.sender_name}</div>`;
    }
    
    // محتوى الرسالة
    messageHTML += `<div class="message-content ${message.sender_id == window.userId ? 'bg-primary text-white' : 'bg-light'}">`;
    
    // الرد على رسالة
    if (message.replied_to) {
        messageHTML += `
            <div class="replied-message ${message.sender_id == window.userId ? 'bg-primary-light text-white-50' : 'bg-light-gray text-muted'} p-2 mb-2 rounded border-start border-3 border-primary">
                <small class="d-block mb-1">
                    <i class="bi bi-reply-fill me-1"></i> رد على ${message.replied_to.sender_name || 'مستخدم محذوف'}
                </small>
                <div class="text-truncate">${message.replied_to.body || ''}</div>
            </div>
        `;
    }
    
    // توجيه من رس    // توجيه من رسالة أخرى
        if (message.forwarded_from) {
            messageHTML += `
                <div class="forwarded-message ${message.sender_id == window.userId ? 'bg-primary-light text-white-50' : 'bg-light-gray text-muted'} p-2 mb-2 rounded border-start border-3 border-info">
                    <small class="d-block mb-1">
                        <i class="bi bi-forward-fill me-1"></i> تم توجيهها من ${message.forwarded_from.sender_name || 'مستخدم محذوف'}
                    </small>
                </div>
            `;
        }
        
        // محتوى الرسالة حسب النوع
        if (message.type === 'text') {
            messageHTML += message.body;
        } else if (message.type === 'image') {
            messageHTML += `<img src="${message.media_url}" alt="صورة" class="img-fluid rounded message-image" style="max-width: 300px;">`;
        } else if (message.type === 'voice') {
            messageHTML += `
                <div class="voice-message">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-light play-voice-btn me-2" data-audio-url="${message.media_url}">
                            <i class="bi bi-play-fill"></i>
                        </button>
                        <div class="voice-waveform flex-grow-1" style="height: 30px; background: rgba(0,0,0,0.1);"></div>
                        <span class="ms-2 voice-duration small">${message.duration || '0:00'}</span>
                    </div>
                </div>
            `;
        }
        
        // تذييل الرسالة مع الوقت وحالة التوصيل
        messageHTML += `
            <div class="message-meta ${message.sender_id == window.userId ? 'text-white-50' : 'text-muted'} d-flex align-items-center">
                <small class="me-auto">${new Date(message.created_at || Date.now()).toLocaleTimeString('ar-SA', {hour: '2-digit', minute:'2-digit'})}</small>
                ${message.sender_id == window.userId ? getMessageStatusIcon(message) : ''}
            </div>
        `;
        
        messageHTML += `</div>`; // نهاية message-content
        
        // أزرار التفاعل مع الرسالة
        messageHTML += `
            <div class="message-actions">
                <div class="btn-group">
                    <button class="btn btn-sm reply-btn" data-message-id="${message.id}" data-sender-name="${message.sender_name || ''}" data-message-text="${message.type === 'text' ? message.body : ''}">
                        <i class="bi bi-reply"></i>
                    </button>
                    <button class="btn btn-sm forward-btn" data-message-id="${message.id}">
                        <i class="bi bi-forward"></i>
                    </button>
                    <button class="btn btn-sm more-actions-btn" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        ${message.type === 'text' ? `
                            <li><a class="dropdown-item copy-message" href="#" data-message-text="${message.body}">
                                <i class="bi bi-clipboard me-2"></i>نسخ
                            </a></li>
                        ` : ''}
                        <li><a class="dropdown-item ${message.is_pinned ? 'unpin-message' : 'pin-message'}" href="#" data-message-id="${message.id}">
                            <i class="bi bi-${message.is_pinned ? 'pin-angle-fill' : 'pin-angle'} me-2"></i>${message.is_pinned ? 'إلغاء التثبيت' : 'تثبيت'}
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        ${message.sender_id == window.userId ? `
                            <li><a class="dropdown-item delete-message text-danger" href="#" data-message-id="${message.id}">
                                <i class="bi bi-trash me-2"></i>حذف
                            </a></li>
                        ` : `
                            <li><a class="dropdown-item report-message text-danger" href="#" data-message-id="${message.id}">
                                <i class="bi bi-flag me-2"></i>إبلاغ
                            </a></li>
                        `}
                    </ul>
                </div>
            </div>
        `;
        
        messageHTML += `</div>`; // نهاية message-content-wrapper
        
        messageItem.innerHTML = messageHTML;
        messagesWrapper.appendChild(messageItem);
        
        // إضافة مستمعين للأحداث للرسالة الجديدة
        setupMessageEventListeners(messageItem);
    }
    
    /**
     * إعداد مستمعين للأحداث للرسالة
     */
    function setupMessageEventListeners(messageItem) {
        // مستمع زر الرد
        const replyBtn = messageItem.querySelector('.reply-btn');
        if (replyBtn) {
            replyBtn.addEventListener('click', function() {
                const messageId = this.getAttribute('data-message-id');
                const senderName = this.getAttribute('data-sender-name');
                const messageText = this.getAttribute('data-message-text');
                
                document.getElementById('reply-container').classList.remove('d-none');
                document.getElementById('replied-to-id').value = messageId;
                document.getElementById('reply-sender-name').textContent = senderName;
                document.getElementById('reply-message-text').textContent = messageText;
                
                document.getElementById('message-input').focus();
            });
        }
        
        // مستمع زر التوجيه
        const forwardBtn = messageItem.querySelector('.forward-btn');
        if (forwardBtn) {
            forwardBtn.addEventListener('click', function() {
                const messageId = this.getAttribute('data-message-id');
                document.getElementById('forward-message-id').value = messageId;
                loadConversationsForForward();
                
                const forwardMessageModal = new bootstrap.Modal(document.getElementById('forwardMessageModal'));
                forwardMessageModal.show();
            });
        }
        
        // مستمع زر النسخ
        const copyBtn = messageItem.querySelector('.copy-message');
        if (copyBtn) {
            copyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const messageText = this.getAttribute('data-message-text');
                navigator.clipboard.writeText(messageText)
                    .then(() => showNotification('تم نسخ النص بنجاح', 'success'))
                    .catch(() => showNotification('فشل نسخ النص', 'error'));
            });
        }
        
        // مستمع زر التثبيت
        const pinBtn = messageItem.querySelector('.pin-message, .unpin-message');
        if (pinBtn) {
            pinBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const messageId = this.getAttribute('data-message-id');
                const isPinned = this.classList.contains('unpin-message');
                togglePinMessage(messageId, !isPinned);
            });
        }
        
        // مستمع زر الحذف
        const deleteBtn = messageItem.querySelector('.delete-message');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const messageId = this.getAttribute('data-message-id');
                if (confirm('هل أنت متأكد من حذف هذه الرسالة؟')) {
                    deleteMessage(messageId);
                }
            });
        }
        
        // مستمع زر الإبلاغ
        const reportBtn = messageItem.querySelector('.report-message');
        if (reportBtn) {
            reportBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const messageId = this.getAttribute('data-message-id');
                reportMessage(messageId);
            });
        }
        
        // مستمع زر تشغيل الصوت
        const playVoiceBtn = messageItem.querySelector('.play-voice-btn');
        if (playVoiceBtn) {
            playVoiceBtn.addEventListener('click', function() {
                const audioUrl = this.getAttribute('data-audio-url');
                playVoiceMessage(this, audioUrl);
            });
        }
    }
    
    /**
     * الحصول على أيقونة حالة الرسالة
     */
    function getMessageStatusIcon(message) {
        if (message.read_at) {
            return '<i class="bi bi-check2-all ms-1"></i>';
        } else if (message.delivered_at) {
            return '<i class="bi bi-check2 ms-1"></i>';
        } else {
            return '<i class="bi bi-check ms-1"></i>';
        }
    }
    
    /**
     * تحديث مؤشرات قراءة الرسائل
     */
    function updateMessageReadIndicators(messageIds) {
        if (!Array.isArray(messageIds)) return;
        
        messageIds.forEach(id => {
            const message = document.getElementById(`message-${id}`);
            if (message) {
                const statusIcon = message.querySelector('.message-meta i');
                if (statusIcon) {
                    statusIcon.className = 'bi bi-check2-all ms-1';
                }
            }
        });
    }
    
    /**
     * تمرير المحادثة للأسفل
     */
    function scrollToBottom() {
        const chatBody = document.getElementById('chat-messages-container');
        if (chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    }
    
    /**
     * تبديل تثبيت الرسالة
     */
    function togglePinMessage(messageId, shouldPin) {
        const formData = new FormData();
        formData.append('message_id', messageId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch(`/messaging/messages/${shouldPin ? 'pin' : 'unpin'}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`تم ${shouldPin ? 'تثبيت' : 'إلغاء تثبيت'} الرسالة بنجاح`, 'success');
                
                // تحديث واجهة المستخدم
                const message = document.getElementById(`message-${messageId}`);
                if (message) {
                    const pinBtn = message.querySelector('.pin-message, .unpin-message');
                    if (pinBtn) {
                        if (shouldPin) {
                            pinBtn.classList.remove('pin-message');
                            pinBtn.classList.add('unpin-message');
                            pinBtn.innerHTML = '<i class="bi bi-pin-angle-fill me-2"></i>إلغاء التثبيت';
                        } else {
                            pinBtn.classList.remove('unpin-message');
                            pinBtn.classList.add('pin-message');
                            pinBtn.innerHTML = '<i class="bi bi-pin-angle me-2"></i>تثبيت';
                        }
                    }
                }
            } else {
                showNotification(data.message || 'حدث خطأ', 'error');
            }
        })
        .catch(error => {
            console.error('Error toggling pin status:', error);
            showNotification('حدث خطأ أثناء تغيير حالة التثبيت', 'error');
        });
    }
    
    /**
     * حذف رسالة
     */
    function deleteMessage(messageId) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'DELETE');
        
        fetch(`/messaging/messages/${messageId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // حذف الرسالة من واجهة المستخدم
                const message = document.getElementById(`message-${messageId}`);
                if (message) {
                    message.remove();
                }
                
                showNotification('تم حذف الرسالة بنجاح', 'success');
            } else {
                showNotification(data.message || 'حدث خطأ أثناء حذف الرسالة', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting message:', error);
            showNotification('حدث خطأ أثناء حذف الرسالة', 'error');
        });
    }
    
    /**
     * الإبلاغ عن رسالة
     */
    function reportMessage(messageId) {
        const reason = prompt('الرجاء تحديد سبب الإبلاغ:');
        if (!reason) return;
        
        const formData = new FormData();
        formData.append('message_id', messageId);
        formData.append('reason', reason);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/messaging/messages/report', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('تم الإبلاغ عن الرسالة بنجاح', 'success');
            } else {
                showNotification(data.message || 'حدث خطأ أثناء الإبلاغ عن الرسالة', 'error');
            }
        })
        .catch(error => {
            console.error('Error reporting message:', error);
            showNotification('حدث خطأ أثناء الإبلاغ عن الرسالة', 'error');
        });
    }
    
    /**
     * تحديث حالة قراءة الرسالة
     */
    function updateMessageReadStatus(messageId) {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        const conversationId = window.location.pathname.split('/').pop();
        
        fetch(`/messaging/conversations/${conversationId}/mark-read`, {
            method: 'POST',
            body: formData
        })
        .catch(error => console.error('Error marking messages as read:', error));
    }
    
    /**
     * بدء مكالمة صوتية
     */
    function initiateVoiceCall() {
        const recipientId = this.getAttribute('data-recipient');
        const callType = this.getAttribute('data-type');
        
        // تأكد من تحميل وظائف المكالمات
        if (typeof initializeVoiceCall !== 'function') {
            showNotification('جاري تحميل وظائف المكالمات...', 'info');
            
            // تحميل ملف المكالمات ديناميكيًا
            const script = document.createElement('script');
            script.src = '/js/voice-call.js';
            script.onload = function() {
                if (typeof initializeVoiceCall === 'function') {
                    initializeVoiceCall(recipientId, callType);
                } else {
                    showNotification('فشل تحميل وظائف المكالمات', 'error');
                }
            };
            script.onerror = function() {
                showNotification('فشل تحميل وظائف المكالمات', 'error');
            };
            document.head.appendChild(script);
        } else {
            initializeVoiceCall(recipientId, callType);
        }
    }
    
    /**
     * تغيير حالة الاتصال
     */
    function toggleOnlineStatus() {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        fetch('/api/users/toggle-online-status', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusIcon = document.querySelector('.user-info small i');
                if (statusIcon) {
                    if (data.is_online) {
                        statusIcon.className = 'bi bi-circle-fill me-1 small';
                        statusIcon.parentElement.className = 'text-success';
                        statusIcon.parentElement.textContent = ' متصل';
                        statusIcon.parentElement.prepend(statusIcon);
                    } else {
                        statusIcon.className = 'bi bi-circle me-1 small';
                        statusIcon.parentElement.className = 'text-muted';
                        statusIcon.parentElement.textContent = ' غير متصل';
                        statusIcon.parentElement.prepend(statusIcon);
                    }
                }
                
                showNotification(`تم تغيير حالتك إلى ${data.is_online ? 'متصل' : 'غير متصل'}`, 'success');
            } else {
                showNotification(data.message || 'حدث خطأ أثناء تغيير الحالة', 'error');
            }
        })
        .catch(error => {
            console.error('Error toggling online status:', error);
            showNotification('حدث خطأ أثناء تغيير الحالة', 'error');
        });
    }
    
    /**
     * عرض إشعار للمستخدم
     */
    function showNotification(message, type = 'info') {
        // التحقق مما إذا كان تواست موجودًا
        if (typeof bootstrap !== 'undefined' && typeof bootstrap.Toast !== 'undefined') {
            // إنشاء عنصر التواست
            const toastContainer = document.getElementById('toast-container');
            let container = toastContainer;
            
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(container);
            }
            
            const toastId = 'toast-' + Date.now();
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center border-0 text-white bg-${type === 'error' ? 'danger' : (type === 'success' ? 'success' : 'primary')}`;
            toastEl.id = toastId;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            
            toastEl.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            container.appendChild(toastEl);
            
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3000
            });
            
            toast.show();
            
            // تنظيف بعد الإخفاء
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        } else {
            // استخدام التنبيه العادي إذا لم يكن بوتستراب متاحًا
            if (type === 'error') {
                console.error(message);
            } else {
                console.log(message);
            }
            alert(message);
        }
    }
    
    // تصدير الدوال العامة
    window.sendMessage = sendMessage;
    window.toggleVoiceRecording = toggleVoiceRecording;
    window.initiateVoiceCall = initiateVoiceCall;
    window.scrollToBottom = scrollToBottom;

// إزالة القوس والقوس المنحني هنا