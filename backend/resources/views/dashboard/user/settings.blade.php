@extends('layouts.app')

@section('title', 'إعدادات الحساب')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2">إعدادات الحساب</h1>
            <p class="text-muted">تخصيص إعدادات حسابك وتفضيلاتك الشخصية</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4 mb-md-0">
            <!-- قائمة الإعدادات -->
            <x-card class="border-0 shadow-sm">
                <x-list-group class="list-group-flush settings-nav">
                    <a href="#general-settings" class="list-group-item list-group-item-action active d-flex align-items-center">
                        <i class="bi bi-gear me-2"></i> الإعدادات العامة
                    </a>
                    <a href="#notification-settings" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-bell me-2"></i> إعدادات الإشعارات
                    </a>
                    <a href="#privacy-settings" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-shield-lock me-2"></i> الخصوصية والأمان
                    </a>
                    <a href="#appearance-settings" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-palette me-2"></i> المظهر والتخصيص
                    </a>
                    <a href="#language-settings" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-translate me-2"></i> اللغة والمنطقة
                    </a>
                    <a href="#account-settings" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="bi bi-person-gear me-2"></i> إدارة الحساب
                    </a>
                </x-list-group>
            </x-card>
        </div>
        
        <div class="col-md-8">
            <!-- الإعدادات العامة -->
            <section id="general-settings" class="settings-section">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">الإعدادات العامة</h5>
                    </x-slot>
                    
                    <form action="{{ route('user.settings.general.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">وضع العرض الافتراضي</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="default_view" id="defaultViewGrid" value="grid" checked>
                                    <label class="form-check-label" for="defaultViewGrid">
                                        <i class="bi bi-grid me-1"></i> شبكة
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="default_view" id="defaultViewList" value="list">
                                    <label class="form-check-label" for="defaultViewList">
                                        <i class="bi bi-list-ul me-1"></i> قائمة
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enableAnimations" name="enable_animations" checked>
                                <label class="form-check-label" for="enableAnimations">تفعيل التأثيرات الحركية</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="autoRefresh" name="auto_refresh" checked>
                                <label class="form-check-label" for="autoRefresh">تحديث تلقائي للبيانات</label>
                            </div>
                            <div class="form-text">تحديث البيانات تلقائيًا كل فترة زمنية محددة</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="refreshInterval" class="form-label">فترة التحديث التلقائي (بالثواني)</label>
                            <select class="form-select" id="refreshInterval" name="refresh_interval">
                                <option value="30">30 ثانية</option>
                                <option value="60" selected>دقيقة واحدة</option>
                                <option value="300">5 دقائق</option>
                                <option value="600">10 دقائق</option>
                            </select>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </x-card>
            </section>
            
            <!-- إعدادات الإشعارات -->
            <section id="notification-settings" class="settings-section d-none">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">إعدادات الإشعارات</h5>
                    </x-slot>
                    
                    <form action="{{ route('user.settings.notifications.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <h6 class="mb-3">طرق استلام الإشعارات</h6>
                        
                        <div class="table-responsive mb-4">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>نوع الإشعار</th>
                                        <th class="text-center">التطبيق</th>
                                        <th class="text-center">البريد الإلكتروني</th>
                                        <th class="text-center">الرسائل النصية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>تحديثات النظام</td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="systemUpdateApp" name="notifications[system_update][app]" checked>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="systemUpdateEmail" name="notifications[system_update][email]" checked>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="systemUpdateSms" name="notifications[system_update][sms]">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>الرسائل الجديدة</td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="newMessageApp" name="notifications[new_message][app]" checked>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="newMessageEmail" name="notifications[new_message][email]" checked>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="newMessageSms" name="notifications[new_message][sms]" checked>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>تنبيهات الأمان</td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="securityAlertApp" name="notifications[security_alert][app]" checked>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="securityAlertEmail" name="notifications[security_alert][email]" checked>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input class="form-check-input" type="checkbox" id="securityAlertSms" name="notifications[security_alert][sms]" checked>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <h6 class="mb-3">إعدادات إضافية</h6>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enablePushNotifications" name="enable_push_notifications" checked>
                                <label class="form-check-label" for="enablePushNotifications">تفعيل الإشعارات المنبثقة في المتصفح</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enableSoundNotifications" name="enable_sound_notifications" checked>
                                <label class="form-check-label" for="enableSoundNotifications">تفعيل التنبيهات الصوتية</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="digestMode" name="digest_mode">
                                <label class="form-check-label" for="digestMode">وضع الملخص اليومي</label>
                            </div>
                            <div class="form-text">استلام ملخص يومي بدلاً من إشعارات فورية</div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </x-card>
            </section>
            
            <!-- إعدادات الخصوصية والأمان -->
            <section id="privacy-settings" class="settings-section d-none">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">الخصوصية والأمان</h5>
                    </x-slot>
                    
                    <form action="{{ route('user.settings.privacy.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <h6 class="mb-3">إعدادات الخصوصية</h6>
                        
                        <div class="mb-3">
                            <label for="profileVisibility" class="form-label">ظهور الملف الشخصي</label>
                            <select class="form-select" id="profileVisibility" name="profile_visibility">
                                <option value="public">عام - يمكن للجميع رؤية ملفك الشخصي</option>
                                <option value="registered" selected>مسجل - يمكن للمستخدمين المسجلين فقط رؤية ملفك الشخصي</option>
                                <option value="private">خاص - لا يمكن لأحد رؤية ملفك الشخصي</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="activityVisibility" class="form-label">ظهور النشاطات</label>
                            <select class="form-select" id="activityVisibility" name="activity_visibility">
                                <option value="public">عام - يمكن للجميع رؤية نشاطاتك</option>
                                <option value="registered" selected>مسجل - يمكن للمستخدمين المسجلين فقط رؤية نشاطاتك</option>
                                <option value="private">خاص - لا يمكن لأحد رؤية نشاطاتك</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="showOnlineStatus" name="show_online_status" checked>
                                <label class="form-check-label" for="showOnlineStatus">إظهار حالة الاتصال</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="allowSearchEngines" name="allow_search_engines">
                                <label class="form-check-label" for="allowSearchEngines">السماح لمحركات البحث بفهرسة ملفك الشخصي</label>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6 class="mb-3">إعدادات الأمان</h6>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="enableTwoFactor" name="enable_two_factor">
                                <label class="form-check-label" for="enableTwoFactor">تفعيل المصادقة الثنائية</label>
                            </div>
                            <div class="form-text">زيادة أمان حسابك باستخدام رمز إضافي عند تسجيل الدخول</div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="loginNotifications" name="login_notifications" checked>
                                <label class="form-check-label" for="loginNotifications">إشعارات تسجيل الدخول</label>
                            </div>
                            <div class="form-text">تلقي إشعار عند تسجيل الدخول إلى حسابك من جهاز جديد</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('user.password.change') }}" class="btn btn-outline-primary">
                                <i class="bi bi-key me-1"></i> تغيير كلمة المرور
                            </a>
                            <a href="{{ route('user.devices') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-laptop me-1"></i> إدارة الأجهزة المتصلة
                            </a>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </x-card>
            </section>
            
            <!-- إعدادات المظهر والتخصيص -->
            <section id="appearance-settings" class="settings-section d-none">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">المظهر والتخصيص</h5>
                    </x-slot>
                    
                    <form action="{{ route('user.settings.appearance.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="form-label">السمة</label>
                            <div class="d-flex gap-3 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme" id="themeLight" value="light" checked>
                                    <label class="form-check-label" for="themeLight">
                                        <i class="bi bi-sun me-1"></i> فاتح
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme" id="themeDark" value="dark">
                                    <label class="form-check-label" for="themeDark">
                                        <i class="bi bi-moon me-1"></i> داكن
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="theme" id="themeAuto" value="auto">
                                    <label class="form-check-label" for="themeAuto">
                                        <i class="bi bi-circle-half me-1"></i> تلقائي
                                    </label>
                                </div>
                            </div>
                            <div class="form-text">يتبع إعدادات النظام في الوضع التلقائي</div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">اللون الرئيسي</label>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="primary_color" id="colorBlue" value="blue" checked>
                                    <label class="form-check-label color-swatch bg-primary" for="colorBlue" title="أزرق"></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="primary_color" id="colorIndigo" value="indigo">
                                    <label class="form-check-label color-swatch bg-indigo" for="colorIndigo" title="نيلي"></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="primary_color" id="colorPurple" value="purple">
                                    <label class="form-check-label color-swatch bg-purple" for="colorPurple" title="أرجواني"></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="primary_color" id="colorPink" value="pink">
                                    <label class="form-check-label color-swatch bg-pink" for="colorPink" title="وردي"></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="primary_color" id="colorRed" value="red">
                                    <label class="form-check-label color-swatch bg-danger" for="colorRed" title="أحمر"></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="primary_color" id="colorOrange" value="orange">
                                    <label class="form-check-label color-swatch bg-orange" for="colorOrange" title="برتقالي"></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="primary_color" id="colorYellow" value="yellow">
                                    <label class="form-check-label color-swatch bg-warning" for="colorYellow" title="أصفر"></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="primary_color" id="colorGreen" value="green">
                                    <label class="form-check-label color-swatch bg-success" for="colorGreen" title="أخضر"></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="primary_color" id="colorTeal" value="teal">
                                    <label class="form-check-label color-swatch bg-teal" for="colorTeal" title="أزرق مخضر"></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="primary_color" id="colorCyan" value="cyan">
                                    <label class="form-check-label color-swatch bg-info" for="colorCyan" title="سماوي"></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="fontSize" class="form-label">حجم الخط</label>
                            <select class="form-select" id="fontSize" name="font_size">
                                <option value="small">صغير</option>
                                <option value="medium" selected>متوسط</option>
                                <option value="large">كبير</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="compactMode" name="compact_mode">
                                <label class="form-check-label" for="compactMode">الوضع المضغوط</label>
                            </div>
                            <div class="form-text">تقليل المساحات والهوامش لعرض المزيد من المحتوى</div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </x-card>
            </section>
            
            <!-- إعدادات اللغة والمنطقة -->
            <section id="language-settings" class="settings-section d-none">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">اللغة والمنطقة</h5>
                    </x-slot>
                    
                    <form action="{{ route('user.settings.language.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="language" class="form-label">لغة الواجهة</label>
                            <select class="form-select" id="language" name="language">
                                <option value="ar" selected>العربية</option>
                                <option value="en">English (الإنجليزية)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="timezone" class="form-label">المنطقة الزمنية</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="Africa/Khartoum">الخرطوم (GMT+2)</option>
                                <option value="Asia/Riyadh" selected>الرياض (GMT+3)</option>
                                <option value="Asia/Dubai">دبي (GMT+4)</option>
                                <option value="Europe/London">لندن (GMT+0)</option>
                                <option value="America/New_York">نيويورك (GMT-5)</option>
                                <!-- المزيد من المناطق الزمنية -->
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="dateFormat" class="form-label">تنسيق التاريخ</label>
                            <select class="form-select" id="dateFormat" name="date_format">
                                <option value="dd/mm/yyyy" selected>DD/MM/YYYY (31/12/2023)</option>
                                <option value="mm/dd/yyyy">MM/DD/YYYY (12/31/2023)</option>
                                <option value="yyyy-mm-dd">YYYY-MM-DD (2023-12-31)</option>
                                <option value="dd-mm-yyyy">DD-MM-YYYY (31-12-2023)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="timeFormat" class="form-label">تنسيق الوقت</label>
                            <select class="form-select" id="timeFormat" name="time_format">
                                <option value="24h" selected>24 ساعة (14:30)</option>
                                <option value="12h">12 ساعة (2:30 PM)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="firstDayOfWeek" class="form-label">أول يوم في الأسبوع</label>
                            <select class="form-select" id="firstDayOfWeek" name="first_day_of_week">
                                <option value="sunday" selected>الأحد</option>
                                <option value="monday">الإثنين</option>
                                <option value="saturday">السبت</option>
                            </select>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </x-card>
            </section>
            
            <!-- إدارة الحساب -->
            <section id="account-settings" class="settings-section d-none">
                <x-card class="border-0 shadow-sm mb-4">
                    <x-slot name="header">
                        <h5 class="mb-0">إدارة الحساب</h5>
                    </x-slot>
                    
                    <div class="mb-4">
                        <h6>تغيير كلمة المرور</h6>
                        <p class="text-muted small">يُنصح بتغيير كلمة المرور بشكل دوري للحفاظ على أمان حسابك</p>
                        <a href="{{ route('user.password.change') }}" class="btn btn-outline-primary">
                            <i class="bi bi-key me-1"></i> تغيير كلمة المرور
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="mb-4">
                        <h6>تصدير البيانات</h6>
                        <p class="text-muted small">تصدير نسخة من بياناتك الشخصية ونشاطاتك</p>
                        <a href="{{ route('user.data.export') }}" class="btn btn-outline-primary">
                            <i class="bi bi-download me-1"></i> تصدير البيانات
                        </a>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="mb-4">
                        <h6>تعطيل الحساب</h6>
                        <p class="text-muted small">تعطيل حسابك مؤقتًا. يمكنك تفعيله مرة أخرى في أي وقت عن طريق تسجيل الدخول</p>
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#deactivateAccountModal">
                            <i class="bi bi-pause-circle me-1"></i> تعطيل الحساب
                        </button>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div>
                        <h6 class="text-danger">حذف الحساب</h6>
                        <p class="text-muted small">حذف حسابك بشكل نهائي. لا يمكن التراجع عن هذا الإجراء</p>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash me-1"></i> حذف الحساب
                        </button>
                    </div>
                </x-card>
            </section>
        </div>
    </div>
</div>

<!-- نافذة تعطيل الحساب -->
<x-modal id="deactivateAccountModal" title="تعطيل الحساب">
    <div class="text-center mb-4">
        <i class="bi bi-exclamation-triangle text-warning fs-1 d-block mb-3"></i>
        <h5>هل أنت متأكد من رغبتك في تعطيل حسابك؟</h5>
        <p class="text-muted">عند تعطيل حسابك، لن تتمكن من الوصول إلى خدماتنا حتى تقوم بتسجيل الدخول مرة أخرى.</p>
    </div>
    
    <form action="{{ route('user.account.deactivate') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="deactivationReason" class="form-label">سبب التعطيل (اختياري)</label>
            <select class="form-select" id="deactivationReason" name="deactivation_reason">
                <option value="">اختر سببًا...</option>
                <option value="temporary">تعطيل مؤقت فقط</option>
                <option value="not_useful">لا أجد الخدمة مفيدة</option>
                <option value="another_account">لدي حساب آخر</option>
                <option value="privacy">مخاوف تتعلق بالخصوصية</option>
                <option value="other">سبب آخر</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="deactivationFeedback" class="form-label">ملاحظات إضافية (اختياري)</label>
            <textarea class="form-control" id="deactivationFeedback" name="deactivation_feedback" rows="3" placeholder="أخبرنا بالمزيد عن سبب مغادرتك..."></textarea>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="confirmDeactivation" name="confirm_deactivation" required>
                <label class="form-check-label" for="confirmDeactivation">
                    أؤكد رغبتي في تعطيل حسابي
                </label>
            </div>
        </div>
        
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-warning">تعطيل الحساب</button>
        </div>
    </form>
</x-modal>

<!-- نافذة حذف الحساب -->
<x-modal id="deleteAccountModal" title="حذف الحساب">
    <div class="text-center mb-4">
        <i class="bi bi-exclamation-triangle text-danger fs-1 d-block mb-3"></i>
        <h5>هل أنت متأكد من رغبتك في حذف حسابك؟</h5>
        <p class="text-muted">هذا الإجراء نهائي ولا يمكن التراجع عنه. سيتم حذف جميع بياناتك بشكل دائم.</p>
    </div>
    
    <form action="{{ route('user.account.delete') }}" method="POST">
        @csrf
        @method('DELETE')
        
        <div class="mb-3">
            <label for="deletionReason" class="form-label">سبب الحذف (اختياري)</label>
            <select class="form-select" id="deletionReason" name="deletion_reason">
                <option value="">اختر سببًا...</option>
                <option value="not_useful">لا أجد الخدمة مفيدة</option>
                <option value="data_privacy">مخاوف تتعلق بالخصوصية والبيانات</option>
                <option value="another_service">أستخدم خدمة أخرى</option>
                <option value="too_complicated">الخدمة معقدة جدًا</option>
                <option value="other">سبب آخر</option>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="deletionFeedback" class="form-label">ملاحظات إضافية (اختياري)</label>
            <textarea class="form-control" id="deletionFeedback" name="deletion_feedback" rows="3" placeholder="أخبرنا بالمزيد عن سبب مغادرتك..."></textarea>
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">كلمة المرور الحالية</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <div class="form-text">يرجى إدخال كلمة المرور الحالية للتأكيد</div>
        </div>
        
        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="confirmDeletion" name="confirm_deletion" required>
                <label class="form-check-label" for="confirmDeletion">
                    أؤكد رغبتي في حذف حسابي نهائيًا وأدرك أن هذا الإجراء لا يمكن التراجع عنه
                </label>
            </div>
        </div>
        
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">إلغاء</button>
            <button type="submit" class="btn btn-danger">حذف الحساب نهائيًا</button>
        </div>
    </form>
</x-modal>

@push('styles')
<style>
    .settings-section:not(.d-none) {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .color-swatch {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-block;
        cursor: pointer;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .bg-indigo {
        background-color: #6610f2;
    }
    
    .bg-purple {
        background-color: #6f42c1;
    }
    
    .bg-pink {
        background-color: #d63384;
    }
    
    .bg-orange {
        background-color: #fd7e14;
    }
    
    .bg-teal {
        background-color: #20c997;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // التبديل بين أقسام الإعدادات
        const settingsLinks = document.querySelectorAll('.settings-nav a');
        const settingsSections = document.querySelectorAll('.settings-section');
        
        settingsLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // إزالة الفئة النشطة من جميع الروابط
                settingsLinks.forEach(item => {
                    item.classList.remove('active');
                });
                
                // إضافة الفئة النشطة للرابط المحدد
                this.classList.add('active');
                
                // إخفاء جميع الأقسام
                settingsSections.forEach(section => {
                    section.classList.add('d-none');
                });
                
                // إظهار القسم المطلوب
                const targetId = this.getAttribute('href').substring(1);
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.remove('d-none');
                }
            });
        });
        
        // التعامل مع تغيير السمة
        const themeRadios = document.querySelectorAll('input[name="theme"]');
        themeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // هنا يمكن إضافة كود لتطبيق السمة المحددة
                console.log('تم تغيير السمة إلى: ' + this.value);
            });
        });
    });
</script>
@endpush
@endsection