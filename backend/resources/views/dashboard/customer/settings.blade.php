@extends('layouts.dashboard')

@section('title', 'الإعدادات - لوحة تحكم العميل')

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
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="settings-nav">
                        <div class="nav flex-column nav-pills" id="settings-tab" role="tablist">
                            <button class="nav-link active text-start py-3" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                                <i class="bi bi-gear me-2"></i> إعدادات عامة
                            </button>
                            <button class="nav-link text-start py-3" id="notifications-tab" data-bs-toggle="pill" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false">
                                <i class="bi bi-bell me-2"></i> إعدادات الإشعارات
                            </button>
                            <button class="nav-link text-start py-3" id="privacy-tab" data-bs-toggle="pill" data-bs-target="#privacy" type="button" role="tab" aria-controls="privacy" aria-selected="false">
                                <i class="bi bi-shield-lock me-2"></i> الخصوصية والأمان
                            </button>
                            <button class="nav-link text-start py-3" id="messaging-tab" data-bs-toggle="pill" data-bs-target="#messaging" type="button" role="tab" aria-controls="messaging" aria-selected="false">
                                <i class="bi bi-chat-dots me-2"></i> إعدادات المراسلة
                            </button>
                            <button class="nav-link text-start py-3" id="language-tab" data-bs-toggle="pill" data-bs-target="#language" type="button" role="tab" aria-controls="language" aria-selected="false">
                                <i class="bi bi-translate me-2"></i> اللغة والمنطقة
                            </button>
                            <button class="nav-link text-start py-3" id="accessibility-tab" data-bs-toggle="pill" data-bs-target="#accessibility" type="button" role="tab" aria-controls="accessibility" aria-selected="false">
                                <i class="bi bi-person-badge me-2"></i> إمكانية الوصول
                            </button>
                            <button class="nav-link text-start py-3" id="delete-account-tab" data-bs-toggle="pill" data-bs-target="#delete-account" type="button" role="tab" aria-controls="delete-account" aria-selected="false">
                                <i class="bi bi-trash me-2 text-danger"></i> <span class="text-danger">حذف الحساب</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9">
            <div class="tab-content" id="settings-tabContent">
                <!-- الإعدادات العامة -->
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">إعدادات عامة</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.customer.settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="general">
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">وضع العرض</label>
                                    <div class="theme-options d-flex flex-wrap gap-3">
                                        <div class="form-check theme-option">
                                            <input class="form-check-input d-none" type="radio" name="theme" id="theme-light" value="light" {{ auth()->user()->theme == 'light' ? 'checked' : '' }}>
                                            <label class="form-check-label d-flex flex-column align-items-center border rounded p-3" for="theme-light">
                                                <div class="theme-preview light-theme-preview mb-2"></div>
                                                <span>فاتح</span>
                                            </label>
                                        </div>
                                        <div class="form-check theme-option">
                                            <input class="form-check-input d-none" type="radio" name="theme" id="theme-dark" value="dark" {{ auth()->user()->theme == 'dark' ? 'checked' : '' }}>
                                            <label class="form-check-label d-flex flex-column align-items-center border rounded p-3" for="theme-dark">
                                                <div class="theme-preview dark-theme-preview mb-2"></div>
                                                <span>داكن</span>
                                            </label>
                                        </div>
                                        <div class="form-check theme-option">
                                            <input class="form-check-input d-none" type="radio" name="theme" id="theme-system" value="system" {{ auth()->user()->theme == 'system' ? 'checked' : '' }}>
                                            <label class="form-check-label d-flex flex-column align-items-center border rounded p-3" for="theme-system">
                                                <div class="theme-preview system-theme-preview mb-2"></div>
                                                <span>النظام</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">حالة الاتصال</label>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="show-online-status" name="show_online_status" {{ auth()->user()->show_online_status ? 'checked' : '' }}>
                                        <label class="form-check-label" for="show-online-status">إظهار حالة الاتصال</label>
                                    </div>
                                    <div class="form-text text-muted">
                                        عند تفعيل هذا الخيار، سيتمكن المستخدمون الآخرون من معرفة متى تكون متصلاً.
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">التنبيهات الصوتية</label>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="sound-effects" name="sound_effects" {{ auth()->user()->sound_effects ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sound-effects">تشغيل المؤثرات الصوتية</label>
                                    </div>
                                    <div class="form-text text-muted">
                                        تشغيل المؤثرات الصوتية للإشعارات والرسائل والمكالمات.
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">تنسيق الوقت</label>
                                    <select class="form-select" name="time_format">
                                        <option value="12h" {{ auth()->user()->time_format == '12h' ? 'selected' : '' }}>12 ساعة (مساءً/صباحاً)</option>
                                        <option value="24h" {{ auth()->user()->time_format == '24h' ? 'selected' : '' }}>24 ساعة</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات الإشعارات -->
                <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">إعدادات الإشعارات</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.customer.settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="notifications">
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">الإشعارات المباشرة</label>
                                    <div class="list-group">
                                        <div class="list-group-item border-0 px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">الرسائل الجديدة</h6>
                                                    <p class="text-muted mb-0 small">إشعارات عند استلام رسائل جديدة</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="new-messages" name="notifications[new_messages]" {{ auth()->user()->notification_preferences['new_messages'] ?? true ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">المكالمات الواردة</h6>
                                                    <p class="text-muted mb-0 small">إشعارات عند استلام مكالمات واردة</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="incoming-calls" name="notifications[incoming_calls]" {{ auth()->user()->notification_preferences['incoming_calls'] ?? true ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">المكالمات الفائتة</h6>
                                                    <p class="text-muted mb-0 small">إشعارات عند وجود مكالمات فائتة</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="missed-calls" name="notifications[missed_calls]" {{ auth()->user()->notification_preferences['missed_calls'] ?? true ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">إشعارات عامة</h6>
                                                    <p class="text-muted mb-0 small">تحديثات النظام والإشعارات الإدارية</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="system-notifications" name="notifications[system_notifications]" {{ auth()->user()->notification_preferences['system_notifications'] ?? true ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">إشعارات البريد الإلكتروني</label>
                                    <div class="list-group">
                                        <div class="list-group-item border-0 px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">المكالمات الفائتة</h6>
                                                    <p class="text-muted mb-0 small">تنبيهات بالبريد الإلكتروني للمكالمات الفائتة</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="email-missed-calls" name="email_notifications[missed_calls]" {{ auth()->user()->email_notification_preferences['missed_calls'] ?? false ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">رسائل جديدة أثناء عدم الاتصال</h6>
                                                    <p class="text-muted mb-0 small">تنبيهات بالبريد الإلكتروني عند استلام رسائل جديدة أثناء عدم اتصالك</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="email-offline-messages" name="email_notifications[offline_messages]" {{ auth()->user()->email_notification_preferences['offline_messages'] ?? false ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="list-group-item border-0 px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">تحديثات النظام</h6>
                                                    <p class="text-muted mb-0 small">إشعارات بالبريد الإلكتروني حول تحديثات وميزات النظام الهامة</p>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="email-system-updates" name="email_notifications[system_updates]" {{ auth()->user()->email_notification_preferences['system_updates'] ?? true ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- الخصوصية والأمان -->
                <div class="tab-pane fade" id="privacy" role="tabpanel" aria-labelledby="privacy-tab">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">إعدادات الخصوصية والأمان</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.customer.settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="privacy">
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">من يمكنه رؤية معلوماتي</label>
                                    <div class="mb-3">
                                        <label class="form-label">رقم الهاتف</label>
                                        <select class="form-select" name="privacy[phone_number]">
                                            <option value="everyone" {{ (auth()->user()->privacy_settings['phone_number'] ?? 'contacts') == 'everyone' ? 'selected' : '' }}>الجميع</option>
                                            <option value="contacts" {{ (auth()->user()->privacy_settings['phone_number'] ?? 'contacts') == 'contacts' ? 'selected' : '' }}>جهات الاتصال فقط</option>
                                            <option value="nobody" {{ (auth()->user()->privacy_settings['phone_number'] ?? 'contacts') == 'nobody' ? 'selected' : '' }}>لا أحد</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">الصورة الشخصية</label>
                                        <select class="form-select" name="privacy[profile_photo]">
                                            <option value="everyone" {{ (auth()->user()->privacy_settings['profile_photo'] ?? 'everyone') == 'everyone' ? 'selected' : '' }}>الجميع</option>
                                            <option value="contacts" {{ (auth()->user()->privacy_settings['profile_photo'] ?? 'everyone') == 'contacts' ? 'selected' : '' }}>جهات الاتصال فقط</option>
                                            <option value="nobody" {{ (auth()->user()->privacy_settings['profile_photo'] ?? 'everyone') == 'nobody' ? 'selected' : '' }}>لا أحد</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">آخر ظهور</label>
                                        <select class="form-select" name="privacy[last_seen]">
                                            <option value="everyone" {{ (auth()->user()->privacy_settings['last_seen'] ?? 'contacts') == 'everyone' ? 'selected' : '' }}>الجميع</option>
                                            <option value="contacts" {{ (auth()->user()->privacy_settings['last_seen'] ?? 'contacts') == 'contacts' ? 'selected' : '' }}>جهات الاتصال فقط</option>
                                            <option value="nobody" {{ (auth()->user()->privacy_settings['last_seen'] ?? 'contacts') == 'nobody' ? 'selected' : '' }}>لا أحد</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">المكالمات</label>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="allow-calls" name="privacy[allow_calls]" {{ (auth()->user()->privacy_settings['allow_calls'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow-calls">السماح بالمكالمات</label>
                                    </div>
                                    <div class="form-text text-muted mb-3">
                                        عند تعطيل هذا الخيار، لن يتمكن أي شخص من الاتصال بك.
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">من يمكنه الاتصال بي</label>
                                        <select class="form-select" name="privacy[calls_from]">
                                            <option value="everyone" {{ (auth()->user()->privacy_settings['calls_from'] ?? 'contacts') == 'everyone' ? 'selected' : '' }}>الجميع</option>
                                            <option value="contacts" {{ (auth()->user()->privacy_settings['calls_from'] ?? 'contacts') == 'contacts' ? 'selected' : '' }}>جهات الاتصال فقط</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">المحادثات المجمعة</label>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="read-receipts" name="privacy[read_receipts]" {{ (auth()->user()->privacy_settings['read_receipts'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="read-receipts">إيصالات القراءة</label>
                                    </div>
                                    <div class="form-text text-muted mb-3">
                                        عند تعطيل هذا الخيار، لن يتمكن الآخرون من معرفة ما إذا كنت قد قرأت رسائلهم.
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">المستخدمون المحظورون</label>
                                    <p class="text-muted small mb-2">إدارة قائمة المستخدمين المحظورين</p>
                                    <a href="{{ route('dashboard.customer.blocked') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-shield-x me-1"></i> إدارة المستخدمين المحظورين
                                    </a>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات المراسلة -->
                <div class="tab-pane fade" id="messaging" role="tabpanel" aria-labelledby="messaging-tab">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">إعدادات المراسلة</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.customer.settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="messaging">
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">حجم الخط</label>
                                    <select class="form-select" name="chat_font_size">
                                        <option value="small" {{ auth()->user()->chat_font_size == 'small' ? 'selected' : '' }}>صغير</option>
                                        <option value="medium" {{ auth()->user()->chat_font_size == 'medium' ? 'selected' : '' }}>متوسط</option>
                                        <option value="large" {{ auth()->user()->chat_font_size == 'large' ? 'selected' : '' }}>كبير</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">خلفية المحادثة</label>
                                    <div class="row row-cols-4 g-2 mb-2">
                                        @foreach(['default', 'pattern1', 'pattern2', 'pattern3', 'gradient1', 'gradient2', 'solid1', 'solid2'] as $bg)
                                        <div class="col">
                                            <div class="form-check chat-bg-option">
                                                <input class="form-check-input d-none" type="radio" name="chat_background" id="bg-{{ $bg }}" value="{{ $bg }}" {{ auth()->user()->chat_background == $bg ? 'checked' : '' }}>
                                                <label class="form-check-label" for="bg-{{ $bg }}">
                                                    <div class="chat-bg-preview rounded bg-{{ $bg }}" style="height: 60px;"></div>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="custom-background" name="use_custom_background" {{ auth()->user()->use_custom_background ? 'checked' : '' }}>
                                        <label class="form-check-label" for="custom-background">
                                            استخدام خلفية مخصصة
                                        </label>
                                    </div>
                                    <div class="form-text text-muted mb-3">
                                        يمكنك تحميل صورة خلفية مخصصة من صفحة المحادثة.
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">إعدادات متقدمة</label>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="enter-to-send" name="enter_to_send" {{ auth()->user()->enter_to_send ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enter-to-send">استخدام Enter للإرسال</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="media-auto-download" name="media_auto_download" {{ auth()->user()->media_auto_download ? 'checked' : '' }}>
                                        <label class="form-check-label" for="media-auto-download">تنزيل الوسائط تلقائياً</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="save-media-to-gallery" name="save_media_to_gallery" {{ auth()->user()->save_media_to_gallery ? 'checked' : '' }}>
                                        <label class="form-check-label" for="save-media-to-gallery">حفظ الوسائط في معرض الصور</label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- اللغة والمنطقة -->
                <div class="tab-pane fade" id="language" role="tabpanel" aria-labelledby="language-tab">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">اللغة والمنطقة</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.customer.settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="language">
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">لغة التطبيق</label>
                                    <select class="form-select" name="app_language">
                                        <option value="ar" {{ auth()->user()->app_language == 'ar' ? 'selected' : '' }}>العربية</option>
                                        <option value="en" {{ auth()->user()->app_language == 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">المنطقة الزمنية</label>
                                    <select class="form-select" name="timezone">
                                        <option value="Africa/Khartoum" {{ auth()->user()->timezone == 'Africa/Khartoum' ? 'selected' : '' }}>الخرطوم (GMT+2)</option>
                                        <option value="Africa/Cairo" {{ auth()->user()->timezone == 'Africa/Cairo' ? 'selected' : '' }}>القاهرة (GMT+2)</option>
                                        <option value="Asia/Riyadh" {{ auth()->user()->timezone == 'Asia/Riyadh' ? 'selected' : '' }}>الرياض (GMT+3)</option>
                                        <option value="Asia/Dubai" {{ auth()->user()->timezone == 'Asia/Dubai' ? 'selected' : '' }}>دبي (GMT+4)</option>
                                        <option value="Europe/Istanbul" {{ auth()->user()->timezone == 'Europe/Istanbul' ? 'selected' : '' }}>إسطنبول (GMT+3)</option>
                                        <option value="Europe/London" {{ auth()->user()->timezone == 'Europe/London' ? 'selected' : '' }}>لندن (GMT+0/+1)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">تنسيق التاريخ</label>
                                    <select class="form-select" name="date_format">
                                        <option value="dd/mm/yyyy" {{ auth()->user()->date_format == 'dd/mm/yyyy' ? 'selected' : '' }}>يوم/شهر/سنة (31/12/2023)</option>
                                        <option value="mm/dd/yyyy" {{ auth()->user()->date_format == 'mm/dd/yyyy' ? 'selected' : '' }}>شهر/يوم/سنة (12/31/2023)</option>
                                        <option value="yyyy/mm/dd" {{ auth()->user()->date_format == 'yyyy/mm/dd' ? 'selected' : '' }}>سنة/شهر/يوم (2023/12/31)</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">العملة</label>
                                    <select class="form-select" name="currency">
                                        <option value="SDG" {{ auth()->user()->currency == 'SDG' ? 'selected' : '' }}>جنيه سوداني (ج.س)</option>
                                        <option value="EGP" {{ auth()->user()->currency == 'EGP' ? 'selected' : '' }}>جنيه مصري (ج.م)</option>
                                        <option value="SAR" {{ auth()->user()->currency == 'SAR' ? 'selected' : '' }}>ريال سعودي (ر.س)</option>
                                        <option value="AED" {{ auth()->user()->currency == 'AED' ? 'selected' : '' }}>درهم إماراتي (د.إ)</option>
                                        <option value="USD" {{ auth()->user()->currency == 'USD' ? 'selected' : '' }}>دولار أمريكي ($)</option>
                                        <option value="EUR" {{ auth()->user()->currency == 'EUR' ? 'selected' : '' }}>يورو (€)</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- إمكانية الوصول -->
                <div class="tab-pane fade" id="accessibility" role="tabpanel" aria-labelledby="accessibility-tab">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">إعدادات إمكانية الوصول</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dashboard.customer.settings.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="section" value="accessibility">
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">حجم الخط في التطبيق</label>
                                    <select class="form-select" name="app_font_size">
                                        <option value="small" {{ auth()->user()->app_font_size == 'small' ? 'selected' : '' }}>صغير</option>
                                        <option value="medium" {{ auth()->user()->app_font_size == 'medium' ? 'selected' : '' }}>متوسط</option>
                                        <option value="large" {{ auth()->user()->app_font_size == 'large' ? 'selected' : '' }}>كبير</option>
                                        <option value="x-large" {{ auth()->user()->app_font_size == 'x-large' ? 'selected' : '' }}>كبير جداً</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">نمط التباين</label>
                                    <select class="form-select" name="contrast_mode">
                                        <option value="normal" {{ auth()->user()->contrast_mode == 'normal' ? 'selected' : '' }}>عادي</option>
                                        <option value="high" {{ auth()->user()->contrast_mode == 'high' ? 'selected' : '' }}>تباين عالي</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">تقليل الحركة</label>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="reduce-motion" name="reduce_motion" {{ auth()->user()->reduce_motion ? 'checked' : '' }}>
                                        <label class="form-check-label" for="reduce-motion">تقليل الحركة والرسوم المتحركة</label>
                                    </div>
                                    <div class="form-text text-muted">
                                        يقلل من حركة العناصر في التطبيق والرسوم المتحركة.
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">قارئات الشاشة والتقنيات المساعدة</label>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="screen-reader-support" name="screen_reader_support" {{ auth()->user()->screen_reader_support ? 'checked' : '' }}>
                                        <label class="form-check-label" for="screen-reader-support">تحسين الدعم لقارئات الشاشة</label>
                                    </div>
                                    <div class="form-text text-muted">
                                        تحسين تجربة المستخدم لقارئات الشاشة والتقنيات المساعدة الأخرى.
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-medium">مدة عرض الإشعارات</label>
                                    <select class="form-select" name="notification_duration">
                                        <option value="short" {{ auth()->user()->notification_duration == 'short' ? 'selected' : '' }}>قصيرة (3 ثوان)</option>
                                        <option value="medium" {{ auth()->user()->notification_duration == 'medium' ? 'selected' : '' }}>متوسطة (5 ثوان)</option>
                                        <option value="long" {{ auth()->user()->notification_duration == 'long' ? 'selected' : '' }}>طويلة (8 ثوان)</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- حذف الحساب -->
                <div class="tab-pane fade" id="delete-account" role="tabpanel" aria-labelledby="delete-account-tab">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0 text-danger">حذف الحساب</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <h6 class="alert-heading fw-bold">تحذير: لا يمكن التراجع عن هذا الإجراء</h6>
                                <p class="mb-0">سيؤدي حذف حسابك إلى إزالة جميع بياناتك الشخصية ومحادثاتك وإعداداتك بشكل دائم. لا يمكن استعادة هذه المعلومات بعد الحذف.</p>
                            </div>
                            
                            <div class="my-4">
                                <h6 class="mb-3">قبل حذف حسابك، يرجى الأخذ بعين الاعتبار:</h6>
                                <ul class="text-muted">
                                    <li>سيتم حذف جميع محادثاتك ورسائلك نهائياً.</li>
                                    <li>سيتم حذف معلومات حسابك وملفك الشخصي بالكامل.</li>
                                    <li>لن تتمكن من الوصول إلى أي من خدمات أورا باستخدام هذا الحساب مرة أخرى.</li>
                                    <li>لن يتمكن المستخدمون الآخرون من العثور عليك أو مراسلتك.</li>
                                </ul>
                            </div>
                            
                            <div class="mb-4">
                                <h6 class="mb-3">بدائل لحذف الحساب:</h6>
                                <div class="list-group border-0">
                                    <a href="{{ route('dashboard.customer.settings') }}#privacy" class="list-group-item list-group-item-action border-0 px-0">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-shield-lock fs-4 me-3 text-primary"></i>
                                            <div>
                                                <h6 class="mb-1">تعديل إعدادات الخصوصية</h6>
                                                <p class="text-muted mb-0 small">يمكنك تغيير إعدادات الخصوصية لتحديد من يمكنه التواصل معك ورؤية معلوماتك</p>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="{{ route('dashboard.customer.blocked') }}" class="list-group-item list-group-item-action border-0 px-0">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-x fs-4 me-3 text-primary"></i>
                                            <div>
                                                <h6 class="mb-1">حظر المستخدمين</h6>
                                                <p class="text-muted mb-0 small">يمكنك حظر أي مستخدمين لا ترغب في التواصل معهم</p>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="{{ route('support.contact') }}" class="list-group-item list-group-item-action border-0 px-0">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-headset fs-4 me-3 text-primary"></i>
                                            <div>
                                                <h6 class="mb-1">التواصل مع الدعم</h6>
                                                <p class="text-muted mb-0 small">يمكننا مساعدتك في حل أي مشكلة تواجهها عند استخدام التطبيق</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="border-top pt-4">
                                <p class="fw-medium mb-3">إذا كنت متأكداً من رغبتك في حذف حسابك، يرجى تأكيد ذلك أدناه:</p>
                                
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                    <i class="bi bi-trash me-1"></i> حذف حسابي نهائياً
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة تأكيد حذف الحساب -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAccountModalLabel">تأكيد حذف الحساب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-danger mb-4">هذا إجراء نهائي لا يمكن التراجع عنه. سيتم حذف جميع بياناتك ولن تتمكن من استعادة حسابك.</p>
                
                <form action="{{ route('dashboard.customer.account.delete') }}" method="POST" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')
                    
                    <div class="mb-3">
                        <label for="delete-password" class="form-label">يرجى إدخال كلمة المرور الخاصة بك للتأكيد:</label>
                        <input type="password" class="form-control" id="delete-password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="delete-reason" class="form-label">سبب حذف الحساب (اختياري):</label>
                        <select class="form-select" id="delete-reason" name="delete_reason">
                            <option value="">اختر سبباً...</option>
                            <option value="privacy_concerns">مخاوف تتعلق بالخصوصية</option>
                            <option value="not_useful">التطبيق لا يلبي احتياجاتي</option>
                            <option value="alternate_service">وجدت خدمة بديلة</option>
                            <option value="too_complicated">واجهة المستخدم معقدة</option>
                            <option value="other">سبب آخر</option>
                        </select>
                    </div>
                    
                    <div class="mb-3 d-none" id="other-reason-container">
                        <label for="other-reason" class="form-label">يرجى ذكر السبب:</label>
                        <textarea class="form-control" id="other-reason" name="other_reason" rows="3"></textarea>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirm-delete" name="confirm_delete" required>
                        <label class="form-check-label" for="confirm-delete">
                            أنا أفهم أن هذا الإجراء نهائي وأوافق على حذف حسابي وجميع بياناتي
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-danger">حذف الحساب</button>
            </div>
        </div>
    </div>
</div>

<script>
    // عرض حقل سبب آخر عندما يختار المستخدم "سبب آخر"
    document.addEventListener('DOMContentLoaded', function() {
        const deleteReasonSelect = document.getElementById('delete-reason');
        const otherReasonContainer = document.getElementById('other-reason-container');
        
        if (deleteReasonSelect && otherReasonContainer) {
            deleteReasonSelect.addEventListener('change', function() {
                if (this.value === 'other') {
                    otherReasonContainer.classList.remove('d-none');
                } else {
                    otherReasonContainer.classList.add('d-none');
                }
            });
        }
    });
</script>
@endsection