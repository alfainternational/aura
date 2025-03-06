<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\ConnectedDevice;
use Illuminate\Support\Facades\Cookie;

class TwoFactorVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // إذا كان المستخدم غير مسجل دخوله، نسمح بالمرور
        if (!$user) {
            return $next($request);
        }

        // إذا لم تكن المصادقة الثنائية مفعلة للمستخدم، نسمح بالمرور
        if (!$user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        // التحقق من وجود جلسة مصادقة ثنائية صالحة
        if ($user->hasValidTwoFactorSession()) {
            return $next($request);
        }

        // التحقق من وجود كوكي للجهاز الموثوق
        $deviceToken = Cookie::get('trusted_device');
        
        if ($deviceToken && $user->isTrustedDevice($deviceToken)) {
            // إنشاء جلسة مصادقة ثنائية جديدة
            $user->createTwoFactorSession();
            
            // تحديث آخر نشاط للجهاز
            $device = $user->connectedDevices()
                ->where('device_token', $deviceToken)
                ->first();
                
            if ($device) {
                $device->markAsActive();
            }
            
            return $next($request);
        }

        // حفظ URL الحالي في الجلسة للعودة إليه بعد التحقق
        if (!$request->is('two-factor/*')) {
            session(['url.intended' => $request->fullUrl()]);
        }
        
        // إعادة توجيه المستخدم إلى صفحة التحقق من المصادقة الثنائية
        return redirect()->route('two-factor.form');
    }
}
