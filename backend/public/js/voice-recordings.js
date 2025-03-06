/**
 * إدارة تسجيلات المكالمات الصوتية
 * 
 * هذا الملف يتعامل مع واجهة المستخدم لعرض وإدارة تسجيلات المكالمات الصوتية
 */
document.addEventListener('DOMContentLoaded', function() {
    // متغيرات عامة
    let currentPage = 1;
    let currentStatus = 'all';
    let currentSearchQuery = '';
    let currentRecordingId = null;
    let currentCallId = null;
    let currentLanguage = 'original';
    let availableLanguages = [
        { code: 'ar', name: 'العربية' },
        { code: 'en', name: 'الإنجليزية' },
        { code: 'fr', name: 'الفرنسية' },
        { code: 'es', name: 'الإسبانية' },
        { code: 'de', name: 'الألمانية' }
    ];

    // العناصر
    const recordingsContainer = document.getElementById('recordings-container');
    const recordingsList = document.getElementById('recordings-list');
    const loadingIndicator = document.getElementById('loading-indicator');
    const noRecordings = document.getElementById('no-recordings');
    const pagination = document.getElementById('pagination');
    const searchInput = document.getElementById('search-recordings');
    const searchButton = document.getElementById('search-button');
    const filterStatusButtons = document.querySelectorAll('.filter-status');
    
    // عناصر النموذج
    const recordingModal = new bootstrap.Modal(document.getElementById('recordingModal'));
    const deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    const recordingDate = document.getElementById('recording-date');
    const recordingDuration = document.getElementById('recording-duration');
    const transcriptStatus = document.getElementById('transcript-status');
    const fileSize = document.getElementById('file-size');
    const audioPlayer = document.getElementById('audio-player');
    const audioSource = document.getElementById('audio-source');
    const transcriptLoading = document.getElementById('transcript-loading');
    const noTranscript = document.getElementById('no-transcript');
    const transcriptContent = document.getElementById('transcript-content');
    const requestTranscriptionBtn = document.getElementById('request-transcription');
    const deleteRecordingBtn = document.getElementById('delete-recording');
    const downloadRecordingBtn = document.getElementById('download-recording');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    const languageDropdown = document.querySelector('.language-dropdown');
    
    // تحميل التسجيلات عند تحميل الصفحة
    loadRecordings();
    
    // إضافة مستمعي الأحداث
    searchButton.addEventListener('click', handleSearch);
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            handleSearch();
        }
    });
    
    filterStatusButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            currentStatus = this.dataset.status;
            currentPage = 1;
            loadRecordings();
            
            // تحديث زر التصفية
            document.getElementById('filterDropdown').textContent = this.textContent;
        });
    });
    
    requestTranscriptionBtn.addEventListener('click', requestTranscription);
    deleteRecordingBtn.addEventListener('click', showDeleteConfirmation);
    confirmDeleteBtn.addEventListener('click', deleteRecording);
    downloadRecordingBtn.addEventListener('click', downloadRecording);
    
    // وظائف التحميل
    function loadRecordings() {
        showLoading();
        
        let url = '/api/messaging/recordings?page=' + currentPage;
        
        if (currentStatus !== 'all') {
            url += '&status=' + currentStatus;
        }
        
        if (currentSearchQuery) {
            url = '/api/messaging/recordings/search?query=' + encodeURIComponent(currentSearchQuery) + '&page=' + currentPage;
        }
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderRecordings(data.data, data.pagination);
            } else {
                showError('حدث خطأ أثناء تحميل التسجيلات');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('حدث خطأ أثناء الاتصال بالخادم');
        })
        .finally(() => {
            hideLoading();
        });
    }
    
    function renderRecordings(recordings, paginationData) {
        if (recordings.length === 0) {
            showNoRecordings();
            return;
        }
        
        recordingsList.innerHTML = '';
        
        recordings.forEach(recording => {
            const recordingCard = createRecordingCard(recording);
            recordingsList.appendChild(recordingCard);
        });
        
        renderPagination(paginationData);
        
        recordingsList.classList.remove('d-none');
        noRecordings.classList.add('d-none');
    }
    
    function createRecordingCard(recording) {
        const card = document.createElement('div');
        card.className = 'card mb-3';
        
        const startDate = new Date(recording.started_at);
        const formattedDate = startDate.toLocaleDateString('ar-SA', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        const duration = recording.duration ? formatDuration(recording.duration) : 'غير متوفر';
        
        let statusBadge = '';
        switch(recording.transcript_status) {
            case 'completed':
                statusBadge = '<span class="badge bg-success">مكتمل</span>';
                break;
            case 'processing':
                statusBadge = '<span class="badge bg-info">قيد المعالجة</span>';
                break;
            case 'pending':
                statusBadge = '<span class="badge bg-warning">في الانتظار</span>';
                break;
            case 'failed':
                statusBadge = '<span class="badge bg-danger">فشل</span>';
                break;
            default:
                statusBadge = '<span class="badge bg-secondary">غير متوفر</span>';
        }
        
        card.innerHTML = `
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="card-title">تسجيل مكالمة ${formattedDate}</h5>
                        <p class="card-text text-muted">المدة: ${duration}</p>
                    </div>
                    <div>
                        ${statusBadge}
                    </div>
                </div>
                <div class="d-flex mt-3">
                    <button class="btn btn-sm btn-primary view-recording" data-recording-id="${recording.id}" data-call-id="${recording.voice_call_id}">
                        <i class="fas fa-eye me-1"></i> عرض التفاصيل
                    </button>
                </div>
            </div>
        `;
        
        // إضافة مستمع الحدث لزر العرض
        card.querySelector('.view-recording').addEventListener('click', function() {
            viewRecording(recording.voice_call_id, recording.id);
        });
        
        return card;
    }
    
    function renderPagination(paginationData) {
        pagination.innerHTML = '';
        
        if (paginationData.total <= paginationData.per_page) {
            return;
        }
        
        // زر السابق
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${paginationData.current_page === 1 ? 'disabled' : ''}`;
        prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
        
        if (paginationData.current_page > 1) {
            prevLi.querySelector('a').addEventListener('click', function(e) {
                e.preventDefault();
                currentPage--;
                loadRecordings();
            });
        }
        
        pagination.appendChild(prevLi);
        
        // أرقام الصفحات
        for (let i = 1; i <= paginationData.last_page; i++) {
            if (
                i === 1 || 
                i === paginationData.last_page || 
                (i >= paginationData.current_page - 2 && i <= paginationData.current_page + 2)
            ) {
                const pageLi = document.createElement('li');
                pageLi.className = `page-item ${i === paginationData.current_page ? 'active' : ''}`;
                pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                
                if (i !== paginationData.current_page) {
                    pageLi.querySelector('a').addEventListener('click', function(e) {
                        e.preventDefault();
                        currentPage = i;
                        loadRecordings();
                    });
                }
                
                pagination.appendChild(pageLi);
            } else if (
                i === paginationData.current_page - 3 || 
                i === paginationData.current_page + 3
            ) {
                const ellipsisLi = document.createElement('li');
                ellipsisLi.className = 'page-item disabled';
                ellipsisLi.innerHTML = `<a class="page-link" href="#">...</a>`;
                pagination.appendChild(ellipsisLi);
            }
        }
        
        // زر التالي
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${paginationData.current_page === paginationData.last_page ? 'disabled' : ''}`;
        nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
        
        if (paginationData.current_page < paginationData.last_page) {
            nextLi.querySelector('a').addEventListener('click', function(e) {
                e.preventDefault();
                currentPage++;
                loadRecordings();
            });
        }
        
        pagination.appendChild(nextLi);
    }
    
    function viewRecording(callId, recordingId) {
        currentCallId = callId;
        currentRecordingId = recordingId;
        currentLanguage = 'original';
        
        // إعادة تعيين النموذج
        resetModal();
        
        // تحميل تفاصيل التسجيل
        fetch(`/api/messaging/voice-calls/${callId}/recordings/${recordingId}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateRecordingDetails(data.data);
                recordingModal.show();
                loadTranscript();
            } else {
                showError('حدث خطأ أثناء تحميل تفاصيل التسجيل');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('حدث خطأ أثناء الاتصال بالخادم');
        });
    }
    
    function populateRecordingDetails(recording) {
        const startDate = new Date(recording.started_at);
        const formattedDate = startDate.toLocaleDateString('ar-SA', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        recordingDate.textContent = formattedDate;
        recordingDuration.textContent = recording.duration ? formatDuration(recording.duration) : 'غير متوفر';
        
        let statusText = '';
        switch(recording.transcript_status) {
            case 'completed':
                statusText = 'مكتمل';
                break;
            case 'processing':
                statusText = 'قيد المعالجة';
                break;
            case 'pending':
                statusText = 'في الانتظار';
                break;
            case 'failed':
                statusText = 'فشل';
                break;
            default:
                statusText = 'غير متوفر';
        }
        
        transcriptStatus.textContent = statusText;
        
        // تعيين مصدر الصوت
        audioSource.src = `/api/messaging/voice-calls/${currentCallId}/recordings/${currentRecordingId}/download`;
        audioPlayer.load();
        
        // تحميل اللغات المتاحة
        populateLanguageDropdown(recording.metadata?.translations || {});
    }
    
    function loadTranscript() {
        transcriptLoading.classList.remove('d-none');
        noTranscript.classList.add('d-none');
        transcriptContent.classList.add('d-none');
        
        let url = `/api/messaging/voice-calls/${currentCallId}/recordings/${currentRecordingId}/transcript`;
        
        if (currentLanguage !== 'original') {
            url += `?language=${currentLanguage}`;
        }
        
        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            transcriptLoading.classList.add('d-none');
            
            if (data.success) {
                transcriptContent.textContent = data.data.transcript;
                transcriptContent.classList.remove('d-none');
            } else {
                noTranscript.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            transcriptLoading.classList.add('d-none');
            noTranscript.classList.remove('d-none');
        });
    }
    
    function populateLanguageDropdown(translations) {
        // إعادة تعيين القائمة المنسدلة
        const items = languageDropdown.querySelectorAll('li');
        for (let i = 2; i < items.length; i++) {
            items[i].remove();
        }
        
        // إضافة اللغات المتاحة
        availableLanguages.forEach(lang => {
            const isAvailable = translations[lang.code] !== undefined;
            
            const li = document.createElement('li');
            li.innerHTML = `<a class="dropdown-item select-language ${isAvailable ? 'available' : ''}" href="#" data-lang="${lang.code}">${lang.name} ${isAvailable ? '<i class="fas fa-check text-success ms-1"></i>' : ''}</a>`;
            
            li.querySelector('a').addEventListener('click', function(e) {
                e.preventDefault();
                currentLanguage = lang.code;
                loadTranscript();
            });
            
            languageDropdown.appendChild(li);
        });
    }
    
    function requestTranscription() {
        fetch(`/api/messaging/recordings/${currentRecordingId}/transcribe`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('تم إرسال طلب النسخ بنجاح');
                noTranscript.classList.add('d-none');
                transcriptLoading.classList.remove('d-none');
                
                // تحديث حالة النسخ
                transcriptStatus.textContent = 'قيد المعالجة';
                
                // إعادة تحميل التسجيلات بعد فترة
                setTimeout(() => {
                    loadRecordings();
                }, 5000);
            } else {
                showError(data.message || 'حدث خطأ أثناء طلب النسخ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('حدث خطأ أثناء الاتصال بالخادم');
        });
    }
    
    function deleteRecording() {
        fetch(`/api/messaging/voice-calls/${currentCallId}/recordings/${currentRecordingId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            deleteConfirmModal.hide();
            recordingModal.hide();
            
            if (data.message) {
                showSuccess('تم حذف التسجيل بنجاح');
                loadRecordings();
            } else {
                showError('حدث خطأ أثناء حذف التسجيل');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            deleteConfirmModal.hide();
            showError('حدث خطأ أثناء الاتصال بالخادم');
        });
    }
    
    function downloadRecording() {
        window.location.href = `/api/messaging/voice-calls/${currentCallId}/recordings/${currentRecordingId}/download`;
    }
    
    function showDeleteConfirmation() {
        deleteConfirmModal.show();
    }
    
    function handleSearch() {
        currentSearchQuery = searchInput.value.trim();
        currentPage = 1;
        loadRecordings();
    }
    
    // وظائف مساعدة
    function showLoading() {
        loadingIndicator.classList.remove('d-none');
        recordingsList.classList.add('d-none');
        noRecordings.classList.add('d-none');
    }
    
    function hideLoading() {
        loadingIndicator.classList.add('d-none');
    }
    
    function showNoRecordings() {
        noRecordings.classList.remove('d-none');
        recordingsList.classList.add('d-none');
    }
    
    function resetModal() {
        recordingDate.textContent = '';
        recordingDuration.textContent = '';
        transcriptStatus.textContent = '';
        fileSize.textContent = '';
        audioSource.src = '';
        audioPlayer.load();
        transcriptContent.textContent = '';
        transcriptContent.classList.add('d-none');
        noTranscript.classList.add('d-none');
        transcriptLoading.classList.add('d-none');
    }
    
    function formatDuration(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }
    
    function showSuccess(message) {
        // يمكن استخدام مكتبة إشعارات مثل toastr أو إنشاء عنصر إشعار مخصص
        alert(message);
    }
    
    function showError(message) {
        // يمكن استخدام مكتبة إشعارات مثل toastr أو إنشاء عنصر إشعار مخصص
        alert(message);
    }
});