@extends('layouts.dashboard')

@section('title', 'جهات الاتصال')

@section('page-title', 'جهات الاتصال')

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
            <x-card class="border-0 shadow-sm">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">البحث عن جهات اتصال</h5>
                    </div>
                </x-slot>

                <form action="{{ route('messaging.contacts.search') }}" method="GET" class="mb-0">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" placeholder="اسم المستخدم" value="{{ request('name') }}">
                                <label for="name">اسم المستخدم</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="country" name="country">
                                    <option value="">جميع الدول</option>
                                    @foreach($countries ?? [] as $country)
                                    <option value="{{ $country->id }}" {{ request('country') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                <label for="country">الدولة</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="city" name="city">
                                    <option value="">جميع المدن</option>
                                    @foreach($cities ?? [] as $city)
                                    <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                <label for="city">المدينة</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary h-100 w-100">بحث</button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="col-md-4">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">جهات الاتصال ({{ $contacts->total() ?? 0 }})</h5>
                        <a href="{{ route('messaging.contacts.blocked') }}" class="btn btn-sm btn-outline-danger">
                            المستخدمون المحظورون
                        </a>
                    </div>
                </x-slot>

                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="contact-search" placeholder="البحث في جهات الاتصال...">
                    <button class="btn btn-outline-secondary" type="button" id="contact-search-button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                <div class="contacts-list" style="max-height: 400px; overflow-y: auto;">
                    @if(isset($contacts) && $contacts->count() > 0)
                        @foreach($contacts as $contact)
                        <div class="contact-item d-flex justify-content-between align-items-center p-2 mb-2 border-bottom contact-item-{{ $contact->id }}">
                            <div class="d-flex align-items-center">
                                <div class="position-relative">
                                    <img src="{{ $contact->profile_image_url ?? asset('images/default-avatar.png') }}" 
                                         class="rounded-circle me-2" alt="صورة المستخدم" width="40" height="40">
                                    <span class="position-absolute bottom-0 end-0 translate-middle p-1 {{ $contact->is_online ? 'bg-success' : 'bg-secondary' }} border border-light rounded-circle">
                                        <span class="visually-hidden">{{ $contact->is_online ? 'متصل' : 'غير متصل' }}</span>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $contact->name }}</h6>
                                    <small class="text-muted">{{ $contact->city->name ?? '' }}{{ isset($contact->city) && isset($contact->country) ? '، ' : '' }}{{ $contact->country->name ?? '' }}</small>
                                </div>
                            </div>
                            <div class="contact-actions">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('messaging.contacts.show', $contact->id) }}"><i class="bi bi-person-badge me-2"></i>عرض الملف الشخصي</a></li>
                                        <li><a class="dropdown-item" href="{{ route('messaging.conversations.create', ['user' => $contact->id]) }}"><i class="bi bi-chat-text me-2"></i>بدء محادثة</a></li>
                                        <li><a class="dropdown-item" href="{{ route('messaging.voice-calls.start', ['user' => $contact->id]) }}"><i class="bi bi-telephone me-2"></i>اتصال صوتي</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('messaging.contacts.block', $contact->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-slash-circle me-2"></i>حظر</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $contacts->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/no-contacts.svg') }}" alt="لا توجد جهات اتصال" class="img-fluid mb-3" width="120">
                            <h5>لا توجد جهات اتصال</h5>
                            <p class="text-muted">قم بالبحث عن مستخدمين وإضافتهم إلى جهات الاتصال</p>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>

        <div class="col-md-8">
            <x-card class="border-0 shadow-sm h-100">
                <x-slot name="header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">نتائج البحث</h5>
                    </div>
                </x-slot>

                <div class="search-results" style="min-height: 400px;">
                    @if(isset($searchResults) && $searchResults->count() > 0)
                        @foreach($searchResults as $user)
                        <div class="user-item d-flex justify-content-between align-items-center p-3 mb-3 border rounded">
                            <div class="d-flex align-items-center">
                                <div class="position-relative">
                                    <img src="{{ $user->profile_image_url ?? asset('images/default-avatar.png') }}" 
                                         class="rounded-circle me-3" alt="صورة المستخدم" width="50" height="50">
                                    <span class="position-absolute bottom-0 end-0 translate-middle p-1 {{ $user->is_online ? 'bg-success' : 'bg-secondary' }} border border-light rounded-circle">
                                        <span class="visually-hidden">{{ $user->is_online ? 'متصل' : 'غير متصل' }}</span>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <div class="small text-muted">
                                        <i class="bi bi-geo-alt-fill"></i> {{ $user->city->name ?? '' }}{{ isset($user->city) && isset($user->country) ? '، ' : '' }}{{ $user->country->name ?? '' }}
                                    </div>
                                    @if($user->bio)
                                    <p class="small text-truncate mt-1" style="max-width: 300px;">{{ $user->bio }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="user-actions">
                                @if($user->is_contact)
                                <button class="btn btn-sm btn-success" disabled>
                                    <i class="bi bi-person-check-fill me-1"></i> في جهات الاتصال
                                </button>
                                @elseif($user->is_blocked)
                                <button class="btn btn-sm btn-danger" disabled>
                                    <i class="bi bi-slash-circle me-1"></i> محظور
                                </button>
                                @else
                                <form action="{{ route('messaging.contacts.add', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="bi bi-person-plus me-1"></i> إضافة
                                    </button>
                                </form>
                                @endif
                                <a href="{{ route('messaging.contacts.show', $user->id) }}" class="btn btn-sm btn-light ms-1">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                        @endforeach
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $searchResults->links() }}
                        </div>
                    @elseif(request()->has('name') || request()->has('country') || request()->has('city'))
                        <div class="text-center py-5">
                            <img src="{{ asset('images/no-results.svg') }}" alt="لا توجد نتائج" class="img-fluid mb-3" width="150">
                            <h5>لا توجد نتائج</h5>
                            <p class="text-muted">جرب تغيير معايير البحث للعثور على المزيد من المستخدمين</p>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/search-users.svg') }}" alt="ابحث عن مستخدمين" class="img-fluid mb-3" width="150">
                            <h5>ابحث عن مستخدمين</h5>
                            <p class="text-muted">استخدم نموذج البحث للعثور على مستخدمين وإضافتهم إلى جهات الاتصال</p>
                        </div>
                    @endif
                </div>
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
                <!-- محتوى الملف الشخصي سيتم تحميله هنا -->
                <div class="text-center py-3">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .contact-item {
        transition: all 0.2s ease;
        border-radius: 8px;
    }
    
    .contact-item:hover {
        background-color: #f8f9fa;
    }
    
    .user-item {
        transition: all 0.2s ease;
    }
    
    .user-item:hover {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dynamic city dropdown based on country selection
        const countrySelect = document.getElementById('country');
        const citySelect = document.getElementById('city');
        
        if (countrySelect && citySelect) {
            countrySelect.addEventListener('change', function() {
                const countryId = this.value;
                
                // Reset city dropdown
                citySelect.innerHTML = '<option value="">جميع المدن</option>';
                
                if (countryId) {
                    // Show loading
                    citySelect.disabled = true;
                    
                    // Fetch cities for selected country
                    fetch(`/api/countries/${countryId}/cities`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.id;
                                option.textContent = city.name;
                                citySelect.appendChild(option);
                            });
                            
                            citySelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching cities:', error);
                            citySelect.disabled = false;
                        });
                }
            });
        }
        
        // Search within contacts list
        const contactSearchInput = document.getElementById('contact-search');
        const contactSearchButton = document.getElementById('contact-search-button');
        
        if (contactSearchInput && contactSearchButton) {
            function searchContacts() {
                const searchTerm = contactSearchInput.value.toLowerCase();
                const contactItems = document.querySelectorAll('.contact-item');
                
                contactItems.forEach(item => {
                    const contactName = item.querySelector('h6').textContent.toLowerCase();
                    const contactLocation = item.querySelector('small') ? item.querySelector('small').textContent.toLowerCase() : '';
                    
                    if (contactName.includes(searchTerm) || contactLocation.includes(searchTerm)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
            
            contactSearchButton.addEventListener('click', searchContacts);
            contactSearchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    searchContacts();
                }
            });
        }
    });
</script>
@endpush
