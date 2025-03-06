/**
 * نظام تحديد الموقع الجغرافي لمستخدمي تطبيق أورا
 */
const AuraGeolocation = {
    /**
     * الإعدادات الافتراضية
     */
    settings: {
        cookieExpiry: 30, // عدد الأيام لانتهاء صلاحية الكوكيز
        updateInterval: 60 * 60 * 1000, // تحديث الموقع كل ساعة
        requireHighAccuracy: true, // استخدام الدقة العالية (GPS) إذا كان متاحًا
        timeout: 10000, // توقيت الانتظار بالميلي ثانية
    },

    /**
     * تهيئة نظام تحديد الموقع
     * @param {Object} options - خيارات التخصيص
     */
    init: function(options = {}) {
        // دمج الإعدادات المخصصة
        this.settings = { ...this.settings, ...options };

        // التحقق من وجود بيانات الموقع في الكوكيز
        const locationData = this.getLocationFromCookies();
        
        // إذا كانت البيانات موجودة ولكن مر وقت طويل على تحديثها، نحدثها
        if (locationData && this.isLocationStale(locationData.timestamp)) {
            this.requestLocation();
        } 
        // إذا لم تكن هناك بيانات موقع، نطلبها
        else if (!locationData) {
            this.requestLocation();
        }
        
        // إعداد تحديث الموقع تلقائيًا
        setInterval(() => {
            this.requestLocation();
        }, this.settings.updateInterval);
        
        console.log('تم تهيئة نظام تحديد الموقع الجغرافي بنجاح');
    },

    /**
     * طلب موقع المستخدم
     */
    requestLocation: function() {
        if (!navigator.geolocation) {
            console.warn('متصفحك لا يدعم خدمة تحديد الموقع الجغرافي');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            this.handleLocationSuccess.bind(this),
            this.handleLocationError.bind(this),
            {
                enableHighAccuracy: this.settings.requireHighAccuracy,
                timeout: this.settings.timeout,
                maximumAge: 0
            }
        );
    },

    /**
     * معالجة الحصول على الموقع بنجاح
     * @param {Position} position - كائن الموقع
     */
    handleLocationSuccess: function(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        
        // حفظ البيانات في الكوكيز
        this.saveLocationToCookies({
            latitude,
            longitude,
            timestamp: Date.now()
        });
        
        // إرسال البيانات إلى الخادم إذا كان المستخدم مسجل الدخول
        this.sendLocationToServer(latitude, longitude);
        
        // عكس الإحداثيات للحصول على المدينة والبلد
        this.reverseGeocode(latitude, longitude);
        
        console.log('تم تحديد الموقع بنجاح:', latitude, longitude);
    },

    /**
     * معالجة أخطاء الموقع
     * @param {PositionError} error - كائن الخطأ
     */
    handleLocationError: function(error) {
        let errorMessage;
        
        switch(error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = 'تم رفض الوصول إلى الموقع الجغرافي';
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = 'معلومات الموقع غير متاحة';
                break;
            case error.TIMEOUT:
                errorMessage = 'انتهت مهلة طلب الموقع';
                break;
            default:
                errorMessage = 'حدث خطأ غير معروف في تحديد الموقع';
                break;
        }
        
        console.warn('خطأ في تحديد الموقع:', errorMessage);
    },

    /**
     * تحويل الإحداثيات إلى عنوان (البلد والمدينة)
     * @param {number} latitude - خط العرض
     * @param {number} longitude - خط الطول
     */
    reverseGeocode: function(latitude, longitude) {
        // استخدام OpenStreetMap Nominatim API للحصول على معلومات العنوان
        const nominatimUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=10&addressdetails=1`;
        
        fetch(nominatimUrl, {
            headers: {
                'Accept-Language': 'ar', // طلب النتائج باللغة العربية
                'User-Agent': 'Aura Application' // تعريف العميل
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.address) {
                const countryCode = data.address.country_code?.toUpperCase() || '';
                const city = data.address.city || data.address.town || data.address.village || '';
                
                // حفظ المدينة والبلد في الكوكيز
                const locationData = this.getLocationFromCookies() || {};
                locationData.countryCode = countryCode;
                locationData.city = city;
                
                this.saveLocationToCookies(locationData);
                
                // إرسال المدينة والبلد إلى الخادم
                if (countryCode) {
                    this.sendLocationToServer(latitude, longitude, countryCode);
                }
                
                console.log('تم تحديد العنوان:', city, countryCode);
            }
        })
        .catch(error => {
            console.error('خطأ في عكس الإحداثيات:', error);
        });
    },

    /**
     * إرسال بيانات الموقع إلى الخادم
     * @param {number} latitude - خط العرض
     * @param {number} longitude - خط الطول
     * @param {string} countryCode - رمز البلد
     */
    sendLocationToServer: function(latitude, longitude, countryCode = null) {
        // تحقق من تسجيل دخول المستخدم (نتحقق من وجود توكن CSRF)
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            console.log('المستخدم غير مسجل الدخول، لم يتم إرسال بيانات الموقع إلى الخادم');
            return;
        }
        
        // بناء البيانات التي سيتم إرسالها
        const locationData = {
            latitude,
            longitude
        };
        
        if (countryCode) {
            locationData.country_code = countryCode;
        }
        
        // إرسال البيانات إلى الخادم
        fetch('/api/location/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(locationData),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('تم تحديث الموقع في الخادم بنجاح');
            } else {
                console.warn('فشل تحديث الموقع في الخادم:', data.message);
            }
        })
        .catch(error => {
            console.error('خطأ في إرسال بيانات الموقع إلى الخادم:', error);
        });
    },

    /**
     * حفظ بيانات الموقع في الكوكيز
     * @param {Object} locationData - بيانات الموقع
     */
    saveLocationToCookies: function(locationData) {
        const expiryDate = new Date();
        expiryDate.setDate(expiryDate.getDate() + this.settings.cookieExpiry);
        
        document.cookie = `aura_location=${JSON.stringify(locationData)}; expires=${expiryDate.toUTCString()}; path=/; secure; samesite=strict`;
    },

    /**
     * الحصول على بيانات الموقع من الكوكيز
     * @returns {Object|null} - بيانات الموقع أو null إذا لم تكن موجودة
     */
    getLocationFromCookies: function() {
        const cookieValue = document.cookie
            .split('; ')
            .find(row => row.startsWith('aura_location='));
            
        if (cookieValue) {
            try {
                return JSON.parse(cookieValue.split('=')[1]);
            } catch (e) {
                console.error('خطأ في تحليل بيانات الموقع من الكوكيز:', e);
                return null;
            }
        }
        
        return null;
    },

    /**
     * التحقق مما إذا كانت بيانات الموقع قديمة
     * @param {number} timestamp - الطابع الزمني لآخر تحديث
     * @returns {boolean} - هل البيانات قديمة؟
     */
    isLocationStale: function(timestamp) {
        if (!timestamp) return true;
        
        const now = Date.now();
        const elapsedTime = now - timestamp;
        
        // تحقق مما إذا كان قد مر وقت أكثر من الفاصل الزمني المحدد
        return elapsedTime > this.settings.updateInterval;
    },
    
    /**
     * الحصول على الموقع الحالي
     * @returns {Object|null} - بيانات الموقع أو null إذا لم تكن موجودة
     */
    getCurrentLocation: function() {
        return this.getLocationFromCookies();
    }
};

// تهيئة نظام تحديد الموقع عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    AuraGeolocation.init();
});
