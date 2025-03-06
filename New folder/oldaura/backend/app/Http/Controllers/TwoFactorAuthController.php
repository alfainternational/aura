<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ConnectedDevice;
use App\Models\TwoFactorSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class TwoFactorAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['showVerifyForm', 'validateCode', 'validateRecoveryCode']);
    }

    /**
     * عرض صفحة إعداد المصادقة الثنائية
     */
    public function setup()
    {
        $user = Auth::user();
        
        // التحقق من عدم تفعيل المصادقة الثنائية مسبقًا
        if ($user->two_factor_enabled) {
            return redirect()->route('user.security')->with('info', 'المصادقة الثنائية مفعلة بالفعل');
        }
        
        // إنشاء مفتاح سري جديد إذا لم يكن موجودًا
        if (!$user->two_factor_secret) {
            $google2fa = new Google2FA();
            $user->two_factor_secret = $google2fa->generateSecretKey();
            $user->save();
        }
        
        // إنشاء رمز QR
        $qrCode = $this->generateQrCode($user);
        
        // إنشاء رموز استرداد إذا لم تكن موجودة
        if (!$user->two_factor_recovery_codes) {
            $recoveryCodes = $this->generateRecoveryCodes();
            $user->two_factor_recovery_codes = json_encode($recoveryCodes);
            $user->save();
        } else {
            $recoveryCodes = json_decode($user->two_factor_recovery_codes);
        }
        
        return view('dashboard.user.two-factor-setup', compact('user', 'qrCode', 'recoveryCodes'));
    }
    
    /**
     * تفعيل المصادقة الثنائية
     */
    public function enable(Request $request)
    {
        $user = Auth::user();
        
        // التحقق من عدم تفعيل المصادقة الثنائية مسبقًا
        if ($user->two_factor_enabled) {
            return redirect()->route('user.security')->with('info', 'المصادقة الثنائية مفعلة بالفعل');
        }
        
        $request->validate([
            'verification_code' => 'required|string|size:6',
            'password' => 'required|string',
        ]);
        
        // التحقق من كلمة المرور
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->route('user.two-factor.setup')
                ->withErrors(['password' => 'كلمة المرور غير صحيحة'])
                ->withInput();
        }
        
        // التحقق من صحة الرمز
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->verification_code);
        
        if (!$valid) {
            return redirect()->route('user.two-factor.setup')
                ->withErrors(['verification_code' => 'رمز التحقق غير صحيح'])
                ->withInput();
        }
        
        // تفعيل المصادقة الثنائية
        $user->two_factor_enabled = true;
        $user->two_factor_confirmed_at = now();
        $user->save();
        
        // تسجيل الجهاز الحالي كجهاز موثوق
        $this->registerTrustedDevice($user);
        
        // إضافة سجل نشاط
        $this->logSecurityActivity($user, 'تفعيل المصادقة الثنائية', 'تم تفعيل المصادقة الثنائية بنجاح');
        
        // إضافة إشعار للمستخدم
        $user->addNotification(
            'تفعيل المصادقة الثنائية',
            'تم تفعيل المصادقة الثنائية لحسابك بنجاح. تأكد من الاحتفاظ برموز الاسترداد في مكان آمن.',
            'success',
            'shield-check'
        );
        
        return redirect()->route('user.security')->with('success', 'تم تفعيل المصادقة الثنائية بنجاح. تأكد من الاحتفاظ برموز الاسترداد في مكان آمن.');
    }
    
    /**
     * تعطيل المصادقة الثنائية
     */
    public function disable(Request $request)
    {
        $user = Auth::user();
        
        // التحقق من تفعيل المصادقة الثنائية
        if (!$user->two_factor_enabled) {
            return redirect()->route('user.security')->with('info', 'المصادقة الثنائية غير مفعلة');
        }
        
        $request->validate([
            'password' => 'required|string',
        ]);
        
        // التحقق من كلمة المرور
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->route('user.security')
                ->withErrors(['disable_2fa_password' => 'كلمة المرور غير صحيحة'])
                ->withInput();
        }
        
        // تعطيل المصادقة الثنائية
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();
        
        // إضافة سجل نشاط
        $this->logSecurityActivity($user, 'تعطيل المصادقة الثنائية', 'تم تعطيل المصادقة الثنائية');
        
        // إضافة إشعار للمستخدم
        $user->addNotification(
            'تعطيل المصادقة الثنائية',
            'تم تعطيل المصادقة الثنائية لحسابك. نوصي بتفعيلها مرة أخرى لتعزيز أمان حسابك.',
            'warning',
            'shield-exclamation'
        );
        
        return redirect()->route('user.security')->with('success', 'تم تعطيل المصادقة الثنائية بنجاح');
    }
    
    /**
     * عرض صفحة التحقق من المصادقة الثنائية
     */
    public function showVerifyForm()
    {
        if (!session('auth.two_factor.required')) {
            return redirect()->route('login');
        }
        
        return view('auth.two-factor-verify');
    }
    
    /**
     * التحقق من رمز المصادقة الثنائية
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'remember_device' => 'nullable|boolean',
        ]);
        
        $user = User::find(session('auth.two_factor.user_id'));
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // التحقق من صحة الرمز
        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);
        
        if (!$valid) {
            return redirect()->route('two-factor.form')
                ->withErrors(['code' => 'رمز التحقق غير صحيح'])
                ->withInput();
        }
        
        // تسجيل الدخول
        Auth::loginUsingId($user->id);
        
        // تسجيل الجهاز كجهاز موثوق إذا تم طلب ذلك
        if ($request->remember_device) {
            $deviceId = $this->registerTrustedDevice($user, 30); // 30 يوم
            
            // إنشاء جلسة مصادقة ثنائية
            $twoFactorSession = new TwoFactorSession();
            $twoFactorSession->user_id = $user->id;
            $twoFactorSession->session_id = session()->getId();
            $twoFactorSession->ip_address = request()->ip();
            $twoFactorSession->user_agent = request()->userAgent();
            $twoFactorSession->verified_at = now();
            $twoFactorSession->expires_at = now()->addDays(30);
            $twoFactorSession->save();
            
            // إضافة كوكي للجهاز الموثوق
            Cookie::queue('trusted_device', $deviceId, 43200); // 30 يوم
        }
        
        // وضع علامة في الجلسة أن المصادقة الثنائية تمت
        Session::put('two_factor_verified', true);
        
        // إضافة سجل نشاط
        $this->logSecurityActivity($user, 'تسجيل دخول', 'تم تسجيل الدخول باستخدام المصادقة الثنائية');
        
        // إزالة بيانات المصادقة الثنائية من الجلسة
        session()->forget('auth.two_factor.required');
        session()->forget('auth.two_factor.user_id');
        
        return redirect()->intended(route('home'));
    }
    
    /**
     * التحقق من رمز الاسترداد
     */
    public function validateRecoveryCode(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);
        
        $user = User::find(session('auth.two_factor.user_id'));
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // التحقق من صحة رمز الاسترداد
        $recoveryCodes = json_decode($user->two_factor_recovery_codes, true);
        $recoveryCode = str_replace(' ', '', $request->recovery_code);
        
        if (!in_array($recoveryCode, $recoveryCodes)) {
            return redirect()->route('two-factor.form')
                ->withErrors(['recovery_code' => 'رمز الاسترداد غير صحيح'])
                ->withInput();
        }
        
        // إزالة رمز الاسترداد المستخدم
        $recoveryCodes = array_diff($recoveryCodes, [$recoveryCode]);
        $user->two_factor_recovery_codes = json_encode(array_values($recoveryCodes));
        $user->save();
        
        // تسجيل الدخول
        Auth::loginUsingId($user->id);
        
        // وضع علامة في الجلسة أن المصادقة الثنائية تمت
        Session::put('two_factor_verified', true);
        
        // إضافة سجل نشاط
        $this->logSecurityActivity($user, 'استخدام رمز استرداد', 'تم تسجيل الدخول باستخدام رمز استرداد');
        
        // إضافة إشعار للمستخدم
        $user->addNotification(
            'استخدام رمز استرداد',
            'تم استخدام أحد رموز الاسترداد الخاصة بك لتسجيل الدخول. يرجى إنشاء رموز استرداد جديدة.',
            'warning',
            'shield-exclamation'
        );
        
        // إزالة بيانات المصادقة الثنائية من الجلسة
        session()->forget('auth.two_factor.required');
        session()->forget('auth.two_factor.user_id');
        
        return redirect()->intended(route('home'))->with('warning', 'تم استخدام رمز الاسترداد. يرجى إنشاء رموز استرداد جديدة.');
    }
    
    /**
     * إعادة إنشاء رموز الاسترداد
     */
    public function regenerateRecoveryCodes()
    {
        $user = Auth::user();
        
        // التحقق من تفعيل المصادقة الثنائية
        if (!$user->two_factor_enabled) {
            return redirect()->route('user.security')->with('info', 'المصادقة الثنائية غير مفعلة');
        }
        
        // إنشاء رموز استرداد جديدة
        $recoveryCodes = $this->generateRecoveryCodes();
        $user->two_factor_recovery_codes = json_encode($recoveryCodes);
        $user->save();
        
        // إضافة سجل نشاط
        $this->logSecurityActivity($user, 'إعادة إنشاء رموز الاسترداد', 'تم إعادة إنشاء رموز الاسترداد');
        
        // إضافة إشعار للمستخدم
        $user->addNotification(
            'إعادة إنشاء رموز الاسترداد',
            'تم إعادة إنشاء رموز الاسترداد الخاصة بك. تأكد من الاحتفاظ بها في مكان آمن.',
            'info',
            'key'
        );
        
        return redirect()->route('user.security')->with('success', 'تم إعادة إنشاء رموز الاسترداد بنجاح. تأكد من الاحتفاظ بها في مكان آمن.');
    }
    
    /**
     * إنشاء رمز QR
     */
    private function generateQrCode($user)
    {
        $google2fa = new Google2FA();
        $companyName = config('app.name', 'Aura');
        $qrCodeUrl = $google2fa->getQRCodeUrl($companyName, $user->email, $user->two_factor_secret);
        
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($qrCodeUrl);
        
        return $qrCode;
    }
    
    /**
     * إنشاء رموز استرداد
     */
    private function generateRecoveryCodes($count = 8)
    {
        $recoveryCodes = [];
        
        for ($i = 0; $i < $count; $i++) {
            $recoveryCodes[] = Str::random(10);
        }
        
        return $recoveryCodes;
    }
    
    /**
     * تسجيل الجهاز الحالي كجهاز موثوق
     */
    private function registerTrustedDevice($user, $expiresInDays = 30)
    {
        $agent = request()->userAgent();
        $ip = request()->ip();
        $deviceId = md5($agent . $ip . $user->id . Str::random(5));
        
        // إضافة الجهاز إلى قائمة الأجهزة الموثوقة للمستخدم
        $user->addTrustedDevice($deviceId, $expiresInDays);
        
        // التحقق من وجود الجهاز في قائمة الأجهزة المتصلة
        $device = ConnectedDevice::where('user_id', $user->id)
            ->where('device_id', $deviceId)
            ->first();
        
        if (!$device) {
            // إنشاء سجل جديد للجهاز
            $device = new ConnectedDevice();
            $device->user_id = $user->id;
            $device->device_id = $deviceId;
            $device->name = $this->getBrowserName($agent) . ' على ' . $this->getDeviceType($agent);
            $device->ip_address = $ip;
            $device->user_agent = $agent;
            $device->is_trusted = true;
            $device->trusted_at = now();
        } else {
            // تحديث الجهاز الموجود
            $device->is_trusted = true;
            $device->trusted_at = now();
            $device->ip_address = $ip;
        }
        
        $device->last_activity = now();
        $device->save();
        
        return $deviceId;
    }
    
    /**
     * تسجيل نشاط أمني
     */
    private function logSecurityActivity($user, $action, $description)
    {
        // يمكن تنفيذ هذا لاحقًا لتسجيل أنشطة الأمان
        // على سبيل المثال، إنشاء جدول security_activities وتسجيل الأنشطة فيه
    }
    
    /**
     * الحصول على اسم المتصفح
     */
    private function getBrowserName($userAgent)
    {
        if (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident') !== false) {
            return 'Internet Explorer';
        } else {
            return 'متصفح غير معروف';
        }
    }
    
    /**
     * الحصول على نوع الجهاز
     */
    private function getDeviceType($userAgent)
    {
        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
            return 'هاتف محمول';
        } elseif (strpos($userAgent, 'Tablet') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'جهاز لوحي';
        } else {
            return 'كمبيوتر';
        }
    }
}
