@extends('layouts.dashboard')

@section('title', 'إنشاء محادثة جديدة')

@section('page-title', 'إنشاء محادثة جديدة')

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
        <div class="col-12">
            <x-card class="shadow-sm border-0">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">إنشاء محادثة جديدة</h5>
                        <a href="{{ route('messaging.conversations.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right me-1"></i> العودة
                        </a>
                    </div>
                </x-slot>

                <form action="{{ route('messaging.conversations.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="is_group" name="is_group">
                        <label class="form-check-label" for="is_group">إنشاء محادثة جماعية</label>
                    </div>
                    
                    <div id="group-details" class="mb-4" style="display: none;">
                        <div class="mb-3">
                            <label for="group_title" class="form-label">عنوان المجموعة</label>
                            <input type="text" class="form-control" id="group_title" name="group_title" placeholder="أدخل عنوان المجموعة">
                        </div>
                        
                        <div class="mb-3">
                            <label for="group_description" class="form-label">وصف المجموعة</label>
                            <textarea class="form-control" id="group_description" name="group_description" rows="3" placeholder="أدخل وصف المجموعة (اختياري)"></textarea>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">المشاركون</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="search_contacts" placeholder="البحث عن جهات الاتصال...">
                            <button class="btn btn-outline-secondary" type="button" id="search_button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        
                        <div id="search-results" class="mb-3" style="max-height: 300px; overflow-y: auto;">
                            <!-- نتائج البحث ستظهر هنا -->
                        </div>
                        
                        <div id="selected-contacts" class="mb-3">
                            <h6>المشاركون المختارون</h6>
                            <div class="selected-contacts-list">
                                <!-- المستخدمون المختارون سيظهرون هنا -->
                                <div class="text-muted text-center py-3" id="no-contacts-selected">
                                    لم يتم اختيار أي مشاركين
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="create-conversation-button" disabled>
                            إنشاء المحادثة
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>

<!-- مودال عرض الملف الشخصي -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">الملف الشخصي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img src="" id="profile-image" class="rounded-circle mb-3" alt="صورة المستخدم" width="100" height="100">
                    <h5 id="profile-name"></h5>
                    <p class="text-muted" id="profile-bio"></p>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="text-center">
                            <h6>المحادثات</h6>
                            <h5 id="profile-conversations-count">0</h5>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <h6>المدينة</h6>
                            <h5 id="profile-city">-</h5>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .contact-item {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }
    
    .contact-item:hover {
        border-color: #dee2e6;
        background-color: #f8f9fa;
    }
    
    .contact-item.selected {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
    }
    
    .selected-contact-badge {
        background-color: #e9ecef;
        border-radius: 30px;
        padding: 5px 10px;
        margin-right: 5px;
        margin-bottom: 5px;
        display: inline-block;
    }
    
    .badge-remove {
        cursor: pointer;
        margin-right: 5px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isGroupCheckbox = document.getElementById('is_group');
        const groupDetailsSection = document.getElementById('group-details');
        const searchInput = document.getElementById('search_contacts');
        const searchButton = document.getElementById('search_button');
        const searchResults = document.getElementById('search-results');
        const selectedContactsList = document.querySelector('.selected-contacts-list');
        const noContactsSelected = document.getElementById('no-contacts-selected');
        const createButton = document.getElementById('create-conversation-button');
        const selectedContacts = new Set();
        
        // Toggle group details section
        isGroupCheckbox.addEventListener('change', function() {
            if (this.checked) {
                groupDetailsSection.style.display = 'block';
            } else {
                groupDetailsSection.style.display = 'none';
            }
        });
        
        // Search contacts
        function searchContacts() {
            const query = searchInput.value.trim();
            if (query.length < 2) return;
            
            // Show loading indicator
            searchResults.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"></div> جاري البحث...</div>';
            
            // Perform AJAX request
            fetch(`{{ route('messaging.contacts.search') }}?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.contacts.length === 0) {
                        searchResults.innerHTML = '<div class="text-muted text-center py-3">لم يتم العثور على نتائج</div>';
                        return;
                    }
                    
                    let resultsHtml = '';
                    data.contacts.forEach(contact => {
                        const isSelected = selectedContacts.has(contact.id);
                        resultsHtml += `
                            <div class="contact-item d-flex justify-content-between align-items-center ${isSelected ? 'selected' : ''}" data-id="${contact.id}" data-name="${contact.name}">
                                <div class="d-flex align-items-center">
                                    <img src="${contact.profile_image_url || '{{ asset("images/default-avatar.png") }}'}" 
                                         class="rounded-circle me-2" alt="صورة المستخدم" width="40" height="40">
                                    <div>
                                        <h6 class="mb-0">${contact.name}</h6>
                                        <small class="text-muted">${contact.city || ''}</small>
                                    </div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm ${isSelected ? 'btn-danger remove-contact' : 'btn-primary add-contact'}">
                                        <i class="bi ${isSelected ? 'bi-dash' : 'bi-plus'}"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light view-profile" data-id="${contact.id}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                    });
                    
                    searchResults.innerHTML = resultsHtml;
                    
                    // Add event listeners to buttons
                    document.querySelectorAll('.add-contact').forEach(button => {
                        button.addEventListener('click', function(e) {
                            const contactItem = this.closest('.contact-item');
                            const contactId = contactItem.dataset.id;
                            const contactName = contactItem.dataset.name;
                            
                            // Add to selected contacts
                            addSelectedContact(contactId, contactName);
                            
                            // Update UI
                            contactItem.classList.add('selected');
                            this.classList.remove('btn-primary', 'add-contact');
                            this.classList.add('btn-danger', 'remove-contact');
                            this.innerHTML = '<i class="bi bi-dash"></i>';
                            
                            // Re-add event listener
                            this.removeEventListener('click', arguments.callee);
                            this.addEventListener('click', function() {
                                removeSelectedContact(contactId);
                                
                                contactItem.classList.remove('selected');
                                this.classList.remove('btn-danger', 'remove-contact');
                                this.classList.add('btn-primary', 'add-contact');
                                this.innerHTML = '<i class="bi bi-plus"></i>';
                            });
                        });
                    });
                    
                    document.querySelectorAll('.remove-contact').forEach(button => {
                        button.addEventListener('click', function() {
                            const contactItem = this.closest('.contact-item');
                            const contactId = contactItem.dataset.id;
                            
                            // Remove from selected contacts
                            removeSelectedContact(contactId);
                            
                            // Update UI
                            contactItem.classList.remove('selected');
                            this.classList.remove('btn-danger', 'remove-contact');
                            this.classList.add('btn-primary', 'add-contact');
                            this.innerHTML = '<i class="bi bi-plus"></i>';
                            
                            // Re-add event listener
                            this.removeEventListener('click', arguments.callee);
                            this.addEventListener('click', function() {
                                const contactName = contactItem.dataset.name;
                                addSelectedContact(contactId, contactName);
                                
                                contactItem.classList.add('selected');
                                this.classList.remove('btn-primary', 'add-contact');
                                this.classList.add('btn-danger', 'remove-contact');
                                this.innerHTML = '<i class="bi bi-dash"></i>';
                            });
                        });
                    });
                    
                    document.querySelectorAll('.view-profile').forEach(button => {
                        button.addEventListener('click', function() {
                            const contactId = this.dataset.id;
                            showContactProfile(contactId);
                        });
                    });
                })
                .catch(error => {
                    console.error('Error searching contacts:', error);
                    searchResults.innerHTML = '<div class="text-danger text-center py-3">حدث خطأ أثناء البحث</div>';
                });
        }
        
        searchButton.addEventListener('click', searchContacts);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchContacts();
            }
        });
        
        // Add selected contact
        function addSelectedContact(id, name) {
            selectedContacts.add(id);
            updateSelectedContactsUI();
            
            // Create hidden input for form submission
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'participants[]';
            hiddenInput.value = id;
            hiddenInput.id = `participant-${id}`;
            document.querySelector('form').appendChild(hiddenInput);
            
            // Enable create button if at least one contact is selected
            createButton.disabled = false;
        }
        
        // Remove selected contact
        function removeSelectedContact(id) {
            selectedContacts.delete(id);
            updateSelectedContactsUI();
            
            // Remove hidden input
            const hiddenInput = document.getElementById(`participant-${id}`);
            if (hiddenInput) {
                hiddenInput.remove();
            }
            
            // Disable create button if no contacts are selected
            if (selectedContacts.size === 0) {
                createButton.disabled = true;
            }
        }
        
        // Update selected contacts UI
        function updateSelectedContactsUI() {
            if (selectedContacts.size === 0) {
                noContactsSelected.style.display = 'block';
                selectedContactsList.innerHTML = '';
                return;
            }
            
            noContactsSelected.style.display = 'none';
            
            let contactsHtml = '';
            document.querySelectorAll('.contact-item').forEach(item => {
                const id = item.dataset.id;
                const name = item.dataset.name;
                
                if (selectedContacts.has(id)) {
                    contactsHtml += `
                        <div class="selected-contact-badge">
                            ${name}
                            <span class="badge-remove" data-id="${id}">×</span>
                        </div>
                    `;
                }
            });
            
            selectedContactsList.innerHTML = contactsHtml;
            
            // Add event listeners to remove badges
            document.querySelectorAll('.badge-remove').forEach(badge => {
                badge.addEventListener('click', function() {
                    const contactId = this.dataset.id;
                    removeSelectedContact(contactId);
                    
                    // Update the search results UI if the contact is visible there
                    const contactItem = document.querySelector(`.contact-item[data-id="${contactId}"]`);
                    if (contactItem) {
                        contactItem.classList.remove('selected');
                        const addButton = contactItem.querySelector('.remove-contact');
                        if (addButton) {
                            addButton.classList.remove('btn-danger', 'remove-contact');
                            addButton.classList.add('btn-primary', 'add-contact');
                            addButton.innerHTML = '<i class="bi bi-plus"></i>';
                        }
                    }
                });
            });
        }
        
        // Show contact profile
        function showContactProfile(contactId) {
            // Fetch contact details via AJAX
            fetch(`{{ route('messaging.contacts.get') }}/${contactId}`)
                .then(response => response.json())
                .then(data => {
                    const contact = data.contact;
                    
                    // Update modal with contact details
                    document.getElementById('profile-image').src = contact.profile_image_url || '{{ asset("images/default-avatar.png") }}';
                    document.getElementById('profile-name').textContent = contact.name;
                    document.getElementById('profile-bio').textContent = contact.bio || 'لا يوجد نبذة تعريفية';
                    document.getElementById('profile-conversations-count').textContent = contact.conversations_count || 0;
                    document.getElementById('profile-city').textContent = contact.city || '-';
                    
                    // Show modal
                    const profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
                    profileModal.show();
                })
                .catch(error => {
                    console.error('Error fetching contact profile:', error);
                    alert('حدث خطأ أثناء تحميل بيانات المستخدم');
                });
        }
    });
</script>
@endpush