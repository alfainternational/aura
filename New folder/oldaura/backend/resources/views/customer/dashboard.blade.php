@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map-container {
            height: 300px;
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
        }
        .location-card {
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        .location-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .agent-item, .store-item {
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-right: 4px solid #28a745;
        }
        .store-item {
            border-right-color: #007bff;
        }
        .section-title {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #333;
        }
        .badge-distance {
            background-color: #6c757d;
            color: white;
            padding: 5px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('لوحة تحكم العميل') }}</h5>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="alert alert-success">
                        {{ __('مرحباً بك') }} {{ auth()->user()->name }}! {{ __('نحن سعداء برؤيتك مجدداً') }}
                    </div>

                    <!-- موقعك الحالي -->
                    <h4 class="section-title">{{ __('موقعك الحالي') }}</h4>
                    <div id="map-container">
                        <!-- سيتم تحميل الخريطة هنا باستخدام JavaScript -->
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="location-card card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('المدينة') }}</h5>
                                    <p class="card-text" id="current-city">{{ auth()->user()->city ? auth()->user()->city->name : __('غير معروف') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="location-card card">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('الدولة') }}</h5>
                                    <p class="card-text" id="current-country">{{ auth()->user()->country ? auth()->user()->country->name : __('غير معروف') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- الوكلاء القريبون -->
                    <h4 class="section-title">{{ __('الوكلاء القريبون منك') }}</h4>
                    <div id="nearby-agents">
                        <div class="empty-state">
                            <i class="fas fa-user-tie"></i>
                            <p>{{ __('يتم تحميل الوكلاء القريبين منك...') }}</p>
                        </div>
                    </div>

                    <!-- المتاجر القريبة -->
                    <h4 class="section-title">{{ __('المتاجر القريبة منك') }}</h4>
                    <div id="nearby-stores">
                        <div class="empty-state">
                            <i class="fas fa-store"></i>
                            <p>{{ __('يتم تحميل المتاجر القريبة منك...') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- معلومات المستخدم وروابط سريعة -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">{{ __('المعلومات الشخصية') }}</h5>
                </div>
                
                <div class="card-body">
                    <p><strong>{{ __('الاسم:') }}</strong> {{ auth()->user()->name }}</p>
                    <p><strong>{{ __('اسم المستخدم:') }}</strong> {{ auth()->user()->username }}</p>
                    <p><strong>{{ __('البريد الإلكتروني:') }}</strong> {{ auth()->user()->email }}</p>
                    <p><strong>{{ __('رقم الهاتف:') }}</strong> {{ auth()->user()->phone_number ?? __('غير محدد') }}</p>
                </div>
            </div>

            <!-- الإجراءات السريعة -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">{{ __('الإجراءات السريعة') }}</h5>
                </div>
                
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-box me-2"></i> {{ __('الطلبات السابقة') }}
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-map-marker-alt me-2"></i> {{ __('العناوين المحفوظة') }}
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-edit me-2"></i> {{ __('تعديل الملف الشخصي') }}
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-key me-2"></i> {{ __('تغيير كلمة المرور') }}
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-bell me-2"></i> {{ __('إعدادات الإشعارات') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- روابط مفيدة -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">{{ __('روابط مفيدة') }}</h5>
                </div>
                
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('contact') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-envelope me-2"></i> {{ __('اتصل بنا') }}
                        </a>
                        <a href="{{ route('faq') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-question-circle me-2"></i> {{ __('الأسئلة الشائعة') }}
                        </a>
                        <a href="{{ route('terms') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-contract me-2"></i> {{ __('شروط الاستخدام') }}
                        </a>
                        <a href="{{ route('privacy') }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-shield me-2"></i> {{ __('سياسة الخصوصية') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="{{ asset('js/geolocation.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة الخريطة
            let map = null;
            let userMarker = null;
            let agentsMarkers = [];
            let storesMarkers = [];
            
            // إحداثيات افتراضية للخرطوم، السودان إذا لم يكن هناك موقع للمستخدم
            const defaultLat = 15.5007;
            const defaultLng = 32.5599;
            
            // إنشاء الخريطة
            function initMap(latitude, longitude) {
                if (map === null) {
                    map = L.map('map-container').setView([latitude, longitude], 13);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                } else {
                    map.setView([latitude, longitude], 13);
                }
                
                // إضافة علامة موقع المستخدم
                if (userMarker) {
                    userMarker.setLatLng([latitude, longitude]);
                } else {
                    userMarker = L.marker([latitude, longitude], {
                        title: '{{ __("موقعك الحالي") }}'
                    }).addTo(map);
                }
                
                userMarker.bindPopup('{{ __("أنت هنا") }}').openPopup();
            }
            
            // تحديث معلومات المدينة والبلد
            function updateLocationInfo(city, country) {
                document.getElementById('current-city').textContent = city || '{{ __("غير معروف") }}';
                document.getElementById('current-country').textContent = country || '{{ __("غير معروف") }}';
            }
            
            // جلب الوكلاء القريبين
            function fetchNearbyAgents() {
                const agentsContainer = document.getElementById('nearby-agents');
                
                // محاكاة الحصول على البيانات من الخادم
                fetch('/api/nearby-agents')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            let agentsHtml = '';
                            
                            data.data.forEach(agent => {
                                // إضافة علامة للوكيل على الخريطة
                                const agentMarker = L.marker([agent.latitude, agent.longitude], {
                                    title: agent.name,
                                    icon: L.divIcon({
                                        className: 'agent-marker',
                                        html: '<i class="fas fa-user-tie fa-2x text-success"></i>',
                                        iconSize: [25, 41],
                                        iconAnchor: [12, 41]
                                    })
                                }).addTo(map);
                                
                                agentMarker.bindPopup(`
                                    <strong>${agent.name}</strong><br>
                                    ${agent.address || ''}<br>
                                    <a href="tel:${agent.phone_number}">${agent.phone_number}</a>
                                `);
                                
                                agentsMarkers.push(agentMarker);
                                
                                // إضافة بطاقة الوكيل
                                agentsHtml += `
                                    <div class="agent-item">
                                        <h5>${agent.name} <span class="badge badge-distance float-end">${agent.distance} كم</span></h5>
                                        <p>${agent.address || ''}</p>
                                        <p><i class="fas fa-phone-alt me-2"></i> ${agent.phone_number}</p>
                                        <button class="btn btn-sm btn-outline-success float-end">التواصل</button>
                                    </div>
                                `;
                            });
                            
                            agentsContainer.innerHTML = agentsHtml;
                        } else {
                            agentsContainer.innerHTML = `
                                <div class="empty-state">
                                    <i class="fas fa-user-tie"></i>
                                    <p>{{ __('لا يوجد وكلاء بالقرب منك حالياً') }}</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في جلب الوكلاء القريبين:', error);
                        agentsContainer.innerHTML = `
                            <div class="alert alert-warning">
                                {{ __('حدث خطأ أثناء تحميل الوكلاء القريبين. يرجى المحاولة مرة أخرى لاحقاً.') }}
                            </div>
                        `;
                    });
            }
            
            // جلب المتاجر القريبة
            function fetchNearbyStores() {
                const storesContainer = document.getElementById('nearby-stores');
                
                // محاكاة الحصول على البيانات من الخادم
                fetch('/api/nearby-stores')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            let storesHtml = '';
                            
                            data.data.forEach(store => {
                                // إضافة علامة للمتجر على الخريطة
                                const storeMarker = L.marker([store.latitude, store.longitude], {
                                    title: store.name,
                                    icon: L.divIcon({
                                        className: 'store-marker',
                                        html: '<i class="fas fa-store fa-2x text-primary"></i>',
                                        iconSize: [25, 41],
                                        iconAnchor: [12, 41]
                                    })
                                }).addTo(map);
                                
                                storeMarker.bindPopup(`
                                    <strong>${store.name}</strong><br>
                                    ${store.address || ''}<br>
                                    <a href="tel:${store.phone_number}">${store.phone_number}</a>
                                `);
                                
                                storesMarkers.push(storeMarker);
                                
                                // إضافة بطاقة المتجر
                                storesHtml += `
                                    <div class="store-item">
                                        <h5>${store.name} <span class="badge badge-distance float-end">${store.distance} كم</span></h5>
                                        <p>${store.address || ''}</p>
                                        <p><i class="fas fa-phone-alt me-2"></i> ${store.phone_number}</p>
                                        <button class="btn btn-sm btn-outline-primary float-end">زيارة المتجر</button>
                                    </div>
                                `;
                            });
                            
                            storesContainer.innerHTML = storesHtml;
                        } else {
                            storesContainer.innerHTML = `
                                <div class="empty-state">
                                    <i class="fas fa-store"></i>
                                    <p>{{ __('لا توجد متاجر بالقرب منك حالياً') }}</p>
                                </div>
                            `;
                        }
                    })
                    .catch(error => {
                        console.error('خطأ في جلب المتاجر القريبة:', error);
                        storesContainer.innerHTML = `
                            <div class="alert alert-warning">
                                {{ __('حدث خطأ أثناء تحميل المتاجر القريبة. يرجى المحاولة مرة أخرى لاحقاً.') }}
                            </div>
                        `;
                    });
            }
            
            // التحقق من وجود نظام تحديد المواقع
            if (window.AuraGeolocation) {
                const locationData = AuraGeolocation.getCurrentLocation();
                
                if (locationData && locationData.latitude && locationData.longitude) {
                    // استخدام موقع المستخدم الحالي
                    initMap(locationData.latitude, locationData.longitude);
                    updateLocationInfo(locationData.city, locationData.country);
                } else {
                    // استخدام الموقع الافتراضي
                    initMap(defaultLat, defaultLng);
                }
                
                // طلب تحديث الموقع
                AuraGeolocation.requestLocation();
                
                // الاستماع لتحديثات الموقع
                document.addEventListener('aura-location-updated', function(e) {
                    const newLocation = e.detail;
                    if (newLocation && newLocation.latitude && newLocation.longitude) {
                        initMap(newLocation.latitude, newLocation.longitude);
                        updateLocationInfo(newLocation.city, newLocation.country);
                    }
                });
            } else {
                // استخدام الموقع الافتراضي إذا لم يكن هناك نظام تحديد المواقع
                initMap(defaultLat, defaultLng);
                console.warn('نظام تحديد المواقع غير متاح');
            }
            
            // جلب الوكلاء والمتاجر القريبة
            fetchNearbyAgents();
            fetchNearbyStores();
        });
    </script>
@endsection
