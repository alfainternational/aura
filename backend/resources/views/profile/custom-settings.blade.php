@extends('layouts.app')

@section('title', 'إعدادات المستخدم المخصصة')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/custom-profiles.css') }}">
<style>
    .settings-card {
        border-radius: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    
    .settings-card:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    
    .settings-header {
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    
    .settings-section {
        margin-bottom: 30px;
    }
    
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
    }
    
    .color-option {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
        cursor: pointer;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #ddd;
    }
    
    .color-option.active {
        box-shadow: 0 0 0 2px #007bff;
    }
    
    .language-option {
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .language-option:hover {
        background-color: #f8f9fa;
    }
    
    .language-option.active {
        border-color: #007bff;
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .language-flag {
        width: 24px;
        height: 24px;
        margin-right: 10px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- رأس الصفحة -->
            <div class="settings-header mb-4">
                <h2 class="mb-0">إعدادات المستخدم المخصصة</h2>
                <p class="text-muted">تخصيص تجربتك في تطبيق أورا</p>
            </div>
            
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- بطاقات الإعدادات -->
            <div class="row">
                <!-- إعدادات الواجهة -->
                <div class="col-md-6 mb-4">
                    <div class="card settings-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="fas fa-palette me-2"></i>
                                إعدادات الواجهة
                            </h4>
                            
                            <form action="{{ route('profile.update-interface-settings') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">الوضع المظلم</h5>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="darkModeToggle" name="dark_mode" value="1" {{ auth()->user()->settings->dark_mode ? 'checked' : '' }}>
                                        <label class="form-check-label" for="darkModeToggle">تفعيل الوضع المظلم</label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="dark_mode_preference" id="darkModeSystem" value="system" {{ auth()->user()->settings->dark_mode_preference == 'system' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="darkModeSystem">
                                            اتباع إعدادات النظام
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="dark_mode_preference" id="darkModeManual" value="manual" {{ auth()->user()->settings->dark_mode_preference == 'manual' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="darkModeManual">
                                            تحديد يدوي
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">لون التطبيق الأساسي</h5>
                                    <div class="d-flex flex-wrap">
                                        <div class="color-option {{ auth()->user()->settings->theme_color == 'default' ? 'active' : '' }}" style="background-color: #007bff;" data-color="default" onclick="selectColor(this, 'default')"></div>
                                        <div class="color-option {{ auth()->user()->settings->theme_color == 'green' ? 'active' : '' }}" style="background-color: #28a745;" data-color="green" onclick="selectColor(this, 'green')"></div>
                                        <div class="color-option {{ auth()->user()->settings->theme_color == 'purple' ? 'active' : '' }}" style="background-color: #6f42c1;" data-color="purple" onclick="selectColor(this, 'purple')"></div>
                                        <div class="color-option {{ auth()->user()->settings->theme_color == 'orange' ? 'active' : '' }}" style="background-color: #fd7e14;" data-color="orange" onclick="selectColor(this, 'orange')"></div>
                                        <div class="color-option {{ auth()->user()->settings->theme_color == 'pink' ? 'active' : '' }}" style="background-color: #e83e8c;" data-color="pink" onclick="selectColor(this, 'pink')"></div>
                                        <div class="color-option {{ auth()->user()->settings->theme_color == 'teal' ? 'active' : '' }}" style="background-color: #20c997;" data-color="teal" onclick="selectColor(this, 'teal')"></div>
                                    </div>
                                    <input type="hidden" name="theme_color" id="themeColorInput" value="{{ auth()->user()->settings->theme_color }}">
                                </div>
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">حجم الخط</h5>
                                    <select class="form-select" name="font_size">
                                        <option value="small" {{ auth()->user()->settings->font_size == 'small' ? 'selected' : '' }}>صغير</option>
                                        <option value="medium" {{ auth()->user()->settings->font_size == 'medium' ? 'selected' : '' }}>متوسط</option>
                                        <option value="large" {{ auth()->user()->settings->font_size == 'large' ? 'selected' : '' }}>كبير</option>
                                    </select>
                                </div>
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">تأثيرات الحركة</h5>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="animationsToggle" name="animations_enabled" value="1" {{ auth()->user()->settings->animations_enabled ? 'checked' : '' }}>
                                        <label class="form-check-label" for="animationsToggle">تفعيل تأثيرات الحركة</label>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">حفظ إعدادات الواجهة</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات اللغة والتوطين -->
                <div class="col-md-6 mb-4">
                    <div class="card settings-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="fas fa-language me-2"></i>
                                اللغة والتوطين
                            </h4>
                            
                            <form action="{{ route('profile.update-language-settings') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">لغة التطبيق</h5>
                                    <div class="language-option {{ auth()->user()->settings->language == 'ar' ? 'active' : '' }}" onclick="selectLanguage(this, 'ar')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/flags/ar.png') }}" alt="Arabic" class="language-flag">
                                            <span>العربية</span>
                                        </div>
                                    </div>
                                    <div class="language-option {{ auth()->user()->settings->language == 'en' ? 'active' : '' }}" onclick="selectLanguage(this, 'en')">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/flags/en.png') }}" alt="English" class="language-flag">
                                            <span>English</span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="language" id="languageInput" value="{{ auth()->user()->settings->language }}">
                                </div>
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">تنسيق التاريخ والوقت</h5>
                                    <select class="form-select" name="date_format">
                                        <option value="default" {{ auth()->user()->settings->date_format == 'default' ? 'selected' : '' }}>الافتراضي (DD/MM/YYYY)</option>
                                        <option value="us" {{ auth()->user()->settings->date_format == 'us' ? 'selected' : '' }}>الأمريكي (MM/DD/YYYY)</option>
                                        <option value="iso" {{ auth()->user()->settings->date_format == 'iso' ? 'selected' : '' }}>ISO (YYYY-MM-DD)</option>
                                    </select>
                                    
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="use24HourTime" name="use_24_hour_time" value="1" {{ auth()->user()->settings->use_24_hour_time ? 'checked' : '' }}>
                                        <label class="form-check-label" for="use24HourTime">
                                            استخدام نظام 24 ساعة
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">المنطقة الزمنية</h5>
                                    <select class="form-select" name="timezone">
                                        @foreach($timezones as $tz)
                                            <option value="{{ $tz }}" {{ auth()->user()->settings->timezone == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">حفظ إعدادات اللغة</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات الإشعارات -->
                <div class="col-md-6 mb-4">
                    <div class="card settings-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="fas fa-bell me-2"></i>
                                إعدادات الإشعارات
                            </h4>
                            
                            <form action="{{ route('profile.update-notification-settings') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">الإشعارات العامة</h5>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="enableNotifications" name="notifications_enabled" value="1" {{ auth()->user()->settings->notifications_enabled ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enableNotifications">تفعيل الإشعارات</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="enableSounds" name="notification_sounds" value="1" {{ auth()->user()->settings->notification_sounds ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enableSounds">تفعيل أصوات الإشعارات</label>
                                    </div>
                                </div>
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">إشعارات المحادثات</h5>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="newMessageNotifications" name="new_message_notifications" value="1" {{ auth()->user()->settings->new_message_notifications ? 'checked' : '' }}>
                                        <label class="form-check-label" for="newMessageNotifications">رسائل جديدة</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="groupMessageNotifications" name="group_message_notifications" value="1" {{ auth()->user()->settings->group_message_notifications ? 'checked' : '' }}>
                                        <label class="form-check-label" for="groupMessageNotifications">رسائل المجموعات</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="mentionNotifications" name="mention_notifications" value="1" {{ auth()->user()->settings->mention_notifications ? 'checked' : '' }}>
                                        <label class="form-check-label" for="mentionNotifications">الإشارة إليك في الرسائل</label>
                                    </div>
                                </div>
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">إشعارات المكالمات</h5>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="callNotifications" name="call_notifications" value="1" {{ auth()->user()->settings->call_notifications ? 'checked' : '' }}>
                                        <label class="form-check-label" for="callNotifications">مكالمات واردة</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="missedCallNotifications" name="missed_call_notifications" value="1" {{ auth()->user()->settings->missed_call_notifications ? 'checked' : '' }}>
                                        <label class="form-check-label" for="missedCallNotifications">مكالمات فائتة</label>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">حفظ إعدادات الإشعارات</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- إعدادات الخصوصية -->
                <div class="col-md-6 mb-4">
                    <div class="card settings-card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="fas fa-shield-alt me-2"></i>
                                إعدادات الخصوصية
                            </h4>
                            
                            <form action="{{ route('profile.update-privacy-settings') }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">الرؤية والوصول</h5>
                                    <div class="mb-3">
                                        <label class="form-label">من يمكنه رؤية حالة اتصالك؟</label>
                                        <select class="form-select" name="online_status_visibility">
                                            <option value="everyone" {{ auth()->user()->settings->online_status_visibility == 'everyone' ? 'selected' : '' }}>الجميع</option>
                                            <option value="contacts" {{ auth()->user()->settings->online_status_visibility == 'contacts' ? 'selected' : '' }}>جهات الاتصال فقط</option>
                                            <option value="nobody" {{ auth()->user()->settings->online_status_visibility == 'nobody' ? 'selected' : '' }}>لا أحد</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">من يمكنه الاتصال بك؟</label>
                                        <select class="form-select" name="call_privacy">
                                            <option value="everyone" {{ auth()->user()->settings->call_privacy == 'everyone' ? 'selected' : '' }}>الجميع</option>
                                            <option value="contacts" {{ auth()->user()->settings->call_privacy == 'contacts' ? 'selected' : '' }}>جهات الاتصال فقط</option>
                                            <option value="nobody" {{ auth()->user()->settings->call_privacy == 'nobody' ? 'selected' : '' }}>لا أحد</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">إعدادات القراءة</h5>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="readReceiptsEnabled" name="read_receipts" value="1" {{ auth()->user()->settings->read_receipts ? 'checked' : '' }}>
                                        <label class="form-check-label" for="readReceiptsEnabled">إظهار إشعارات القراءة</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="typingIndicatorsEnabled" name="typing_indicators" value="1" {{ auth()->user()->settings->typing_indicators ? 'checked' : '' }}>
                                        <label class="form-check-label" for="typingIndicatorsEnabled">إظهار مؤشر الكتابة</label>
                                    </div>
                                </div>
                                
                                <div class="settings-section">
                                    <h5 class="mb-3">الأمان المتقدم</h5>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="twoFactorEnabled" name="two_factor_enabled" value="1" {{ auth()->user()->two_factor_enabled ? 'checked' : '' }}>
                                        <label class="form-check-label" for="twoFactorEnabled">تفعيل المصادقة الثنائية</label>
                                    </div>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="loginNotificationsEnabled" name="login_notifications" value="1" {{ auth()->user()->settings->login_notifications ? 'checked' : '' }}>
                                        <label class="form-check-label" for="loginNotificationsEnabled">إشعارات تسجيل الدخول</label>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">حفظ إعدادات الخصوصية</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectColor(element, color) {
        // إزالة الفئة النشطة من جميع العناصر
        document.querySelectorAll('.color-option').forEach(el => {
            el.classList.remove('active');
        });
        
        // إضافة الفئة النشطة للعنصر المحدد
        element.classList.add('active');
        
        // تحديث قيمة الإدخال المخفي
        document.getElementById('themeColorInput').value = color;
    }
    
    function selectLanguage(element, language) {
        // إزالة الفئة النشطة من جميع العناصر
        document.querySelectorAll('.language-option').forEach(el => {
            el.classList.remove('active');
        });
        
        // إضافة الفئة النشطة للعنصر المحدد
        element.classList.add('active');
        
        // تحديث قيمة الإدخال المخفي
        document.getElementById('languageInput').value = language;
    }
    
    // تفعيل التبديل بين الوضع المظلم
    document.getElementById('darkModeToggle').addEventListener('change', function() {
        if (this.checked) {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
    });
</script>
@endsection