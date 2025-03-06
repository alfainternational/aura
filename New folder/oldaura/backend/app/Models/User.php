<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'user_type',
        'role',
        'permissions',
        'phone_number',
        'profile_image',
        'birth_date',
        'gender',
        'password',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'biometric_enabled',
        'biometric_credentials',
        'biometric_registered_at',
        'trusted_devices',
        'is_active',
        'last_login_at',
        'is_verified',
        'verification_code',
        'phone_verified_at',
        'requires_kyc',
        'latitude',
        'longitude',
        'country_id',
        'city_id',
        'last_location_update',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'verification_code',
        'trusted_devices',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'permissions' => 'json',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'biometric_enabled' => 'boolean',
            'biometric_credentials' => 'json',
            'biometric_registered_at' => 'datetime',
            'trusted_devices' => 'json',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'requires_kyc' => 'boolean',
            'last_login_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
            'last_location_update' => 'datetime',
        ];
    }

    /**
     * العلاقة مع ملف تعريف المسؤول
     */
    public function adminProfile()
    {
        return $this->hasOne(AdminProfile::class);
    }

    /**
     * العلاقة مع ملف تعريف المشرف
     */
    public function supervisorProfile()
    {
        return $this->hasOne(SupervisorProfile::class);
    }

    /**
     * العلاقة مع ملف تعريف التاجر
     */
    public function merchantProfile()
    {
        return $this->hasOne(MerchantProfile::class);
    }

    /**
     * العلاقة مع ملف تعريف الوكيل
     */
    public function agentProfile()
    {
        return $this->hasOne(AgentProfile::class);
    }

    /**
     * العلاقة مع ملف تعريف المندوب
     */
    public function messengerProfile()
    {
        return $this->hasOne(MessengerProfile::class);
    }

    /**
     * العلاقة مع ملف تعريف العميل
     */
    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    /**
     * العلاقة مع طلبات التحقق KYC
     */
    public function kycVerifications()
    {
        return $this->hasMany(KycVerification::class);
    }

    /**
     * الحصول على آخر طلب تحقق KYC
     */
    public function latestKycVerification()
    {
        return $this->kycVerifications()->latest()->first();
    }

    /**
     * التحقق من حالة KYC للمستخدم
     */
    public function getKycStatusAttribute()
    {
        $latestVerification = $this->latestKycVerification();
        
        if (!$latestVerification) {
            return 'pending';
        }
        
        return $latestVerification->status;
    }
    
    /**
     * التحقق مما إذا كان المستخدم قد تم التحقق من هويته
     */
    public function isKycVerified()
    {
        return $this->kyc_status === 'approved';
    }

    /**
     * العلاقة مع سجل تسجيل الدخول
     */
    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    /**
     * العلاقة مع الأجهزة المتصلة
     */
    public function connectedDevices()
    {
        return $this->hasMany(ConnectedDevice::class);
    }

    /**
     * العلاقة مع جلسات المصادقة الثنائية
     */
    public function twoFactorSessions()
    {
        return $this->hasMany(TwoFactorSession::class);
    }

    /**
     * العلاقة مع الإشعارات
     */
    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    /**
     * الحصول على الإشعارات غير المقروءة
     */
    public function unreadNotifications()
    {
        return $this->notifications()->unread()->latest();
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة
     */
    public function unreadNotificationsCount()
    {
        return $this->unreadNotifications()->count();
    }

    /**
     * إضافة إشعار جديد للمستخدم
     */
    public function addNotification($title, $message, $type = 'info', $icon = null, $actionUrl = null, $data = null)
    {
        return $this->notifications()->create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'action_url' => $actionUrl,
            'data' => $data,
        ]);
    }

    /**
     * تشفير سر المصادقة الثنائية
     */
    public function setTwoFactorSecretAttribute($value)
    {
        $this->attributes['two_factor_secret'] = $value ? Crypt::encrypt($value) : null;
    }

    /**
     * فك تشفير سر المصادقة الثنائية
     */
    public function getTwoFactorSecretAttribute($value)
    {
        return $value ? Crypt::decrypt($value) : null;
    }

    /**
     * تشفير رموز استرداد المصادقة الثنائية
     */
    public function setTwoFactorRecoveryCodesAttribute($value)
    {
        $this->attributes['two_factor_recovery_codes'] = $value ? Crypt::encrypt($value) : null;
    }

    /**
     * فك تشفير رموز استرداد المصادقة الثنائية
     */
    public function getTwoFactorRecoveryCodesAttribute($value)
    {
        return $value ? Crypt::decrypt($value) : null;
    }

    /**
     * التحقق ما إذا كان المستخدم قد قام بتفعيل المصادقة الثنائية
     */
    public function hasTwoFactorEnabled()
    {
        return $this->two_factor_enabled && $this->two_factor_confirmed_at !== null;
    }

    /**
     * التحقق ما إذا كان المستخدم لديه جلسة مصادقة ثنائية صالحة
     */
    public function hasValidTwoFactorSession()
    {
        $device = $this->currentDevice();
        
        if (!$device) {
            return false;
        }
        
        // التحقق من وجود جلسة صالحة للجهاز الحالي
        $session = $this->twoFactorSessions()
            ->where('ip_address', request()->ip())
            ->where('user_agent', request()->userAgent())
            ->where('expires_at', '>', now())
            ->first();
            
        return $session !== null;
    }
    
    /**
     * إنشاء جلسة مصادقة ثنائية جديدة
     */
    public function createTwoFactorSession($expiresInMinutes = 60 * 24 * 7) // 7 days by default
    {
        // إزالة أي جلسات قديمة للجهاز الحالي
        $this->twoFactorSessions()
            ->where('ip_address', request()->ip())
            ->where('user_agent', request()->userAgent())
            ->delete();
            
        // إنشاء جلسة جديدة
        return $this->twoFactorSessions()->create([
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ]);
    }
    
    /**
     * إنشاء رموز استرداد جديدة
     */
    public function generateRecoveryCodes($count = 8)
    {
        $recoveryCodes = [];
        
        for ($i = 0; $i < $count; $i++) {
            $recoveryCodes[] = strtoupper(substr(md5(uniqid()), 0, 10));
        }
        
        $this->two_factor_recovery_codes = json_encode($recoveryCodes);
        $this->save();
        
        return $recoveryCodes;
    }
    
    /**
     * التحقق من صحة رمز الاسترداد
     */
    public function validateRecoveryCode($code)
    {
        $recoveryCodes = json_decode($this->two_factor_recovery_codes, true) ?? [];
        
        // البحث عن الرمز في القائمة
        $index = array_search($code, $recoveryCodes);
        
        if ($index !== false) {
            // إزالة الرمز المستخدم
            unset($recoveryCodes[$index]);
            $this->two_factor_recovery_codes = json_encode(array_values($recoveryCodes));
            $this->save();
            
            return true;
        }
        
        return false;
    }

    /**
     * إضافة جهاز موثوق للمستخدم
     */
    public function addTrustedDevice($deviceId, $expiresInDays = 30)
    {
        $trustedDevices = $this->trusted_devices ?? [];
        
        // إزالة الأجهزة المنتهية الصلاحية
        $trustedDevices = array_filter($trustedDevices, function($device) {
            return $device['expires_at'] > now()->timestamp;
        });
        
        // إضافة الجهاز الجديد
        $trustedDevices[] = [
            'id' => $deviceId,
            'created_at' => now()->timestamp,
            'expires_at' => now()->addDays($expiresInDays)->timestamp,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
        
        $this->trusted_devices = $trustedDevices;
        return $this->save();
    }

    /**
     * التحقق ما إذا كان الجهاز موثوق للمستخدم
     */
    public function isTrustedDevice($deviceId)
    {
        $trustedDevices = $this->trusted_devices ?? [];
        
        foreach ($trustedDevices as $device) {
            if ($device['id'] === $deviceId && $device['expires_at'] > now()->timestamp) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * إزالة جهاز موثوق للمستخدم
     */
    public function removeTrustedDevice($deviceId)
    {
        $trustedDevices = $this->trusted_devices ?? [];
        
        $trustedDevices = array_filter($trustedDevices, function($device) use ($deviceId) {
            return $device['id'] !== $deviceId;
        });
        
        $this->trusted_devices = $trustedDevices;
        return $this->save();
    }

    /**
     * إزالة جميع الأجهزة الموثوقة للمستخدم
     */
    public function removeAllTrustedDevices()
    {
        $this->trusted_devices = [];
        return $this->save();
    }

    /**
     * الحصول على الجهاز الحالي
     */
    public function currentDevice()
    {
        return $this->connectedDevices()->where('is_current_device', true)->first();
    }

    /**
     * عناوين المستخدم
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }
    
    /**
     * العلاقة مع البلد
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    /**
     * العلاقة مع المدينة
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * العلاقة مع جلسات المصادقة البيومترية
     */
    public function biometricSessions()
    {
        return $this->hasMany(BiometricSession::class);
    }

    /**
     * التحقق مما إذا كان المستخدم قد قام بتفعيل المصادقة البيومترية
     */
    public function hasBiometricEnabled()
    {
        return $this->biometric_enabled;
    }

    /**
     * الحصول على جلسات المصادقة البيومترية النشطة
     */
    public function getActiveBiometricSessions()
    {
        return $this->biometricSessions()->where('is_active', true)->get();
    }

    /**
     * إضافة جلسة مصادقة بيومترية جديدة
     */
    public function addBiometricSession($deviceId, $deviceName, $credentialId, $publicKey, $deviceInfo = null)
    {
        return $this->biometricSessions()->create([
            'device_id' => $deviceId,
            'device_name' => $deviceName,
            'credential_id' => $credentialId,
            'public_key' => $publicKey,
            'device_info' => $deviceInfo,
            'last_used_at' => now(),
        ]);
    }

    /**
     * Get the biometric credentials for the user.
     */
    public function biometricCredentials()
    {
        return $this->hasMany(BiometricCredential::class);
    }
    
    /**
     * التحقق ما إذا كان المستخدم من نوع معين
     */
    public function isUserType($type)
    {
        return $this->user_type === $type;
    }
    
    /**
     * التحقق ما إذا كان المستخدم أكمل بيانات KYC
     */
    public function hasCompletedKYC()
    {
        return $this->is_verified && !$this->requires_kyc;
    }
    
    /**
     * تحديث موقع المستخدم
     */
    public function updateLocation($latitude, $longitude, $countryId = null, $cityId = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        
        if ($countryId) {
            $this->country_id = $countryId;
        }
        
        if ($cityId) {
            $this->city_id = $cityId;
        }
        
        $this->last_location_update = now();
        return $this->save();
    }
    
    /**
     * الحصول على المتاجر القريبة من المستخدم
     */
    public function getNearbyMerchants($radius = 10, $limit = 10)
    {
        if (!$this->latitude || !$this->longitude) {
            return [];
        }
        
        return User::where('user_type', 'merchant')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) < ?", 
                [$this->latitude, $this->longitude, $this->latitude, $radius])
            ->limit($limit)
            ->get();
    }
    
    /**
     * الحصول على الوكلاء القريبين من المستخدم
     */
    public function getNearbyAgents($radius = 10, $limit = 10)
    {
        if (!$this->latitude || !$this->longitude) {
            return [];
        }
        
        return User::where('user_type', 'agent')
            ->where('is_active', true)
            ->where('is_verified', true)
            ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) < ?", 
                [$this->latitude, $this->longitude, $this->latitude, $radius])
            ->limit($limit)
            ->get();
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }
}
