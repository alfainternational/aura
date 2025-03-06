<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Providers\RouteServiceProvider;

class CheckAllUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $userType): Response
    {
        try {
            $user = $request->user();
            
            // التحقق من تسجيل الدخول
            if (!$user) {
                Log::info('التحقق من نوع المستخدم: لم يتم تسجيل الدخول');
                return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول للوصول إلى هذه الصفحة.');
            }
            
            // التحقق من نوع المستخدم
            if ($user->user_type !== $userType) {
                Log::info('التحقق من نوع المستخدم: نوع المستخدم غير متطابق', [
                    'user_id' => $user->id,
                    'current_type' => $user->user_type,
                    'required_type' => $userType
                ]);
                
                // التوجيه حسب نوع المستخدم الحالي
                return redirect(RouteServiceProvider::getHomeRoute($user))
                    ->with('error', 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
            }
            
            // التحقق من حالة التحقق KYC للمستخدم
            if ($user->requires_kyc && !$user->is_verified) {
                $currentRoute = $request->route()->getName();
                
                // قائمة المسارات المسموحة حتى بدون KYC
                $allowedRoutes = [
                    $userType . '.complete-profile',
                    $userType . '.dashboard',
                    $userType . '.verification',
                    $userType . '.profile',
                ];
                
                if (!in_array($currentRoute, $allowedRoutes)) {
                    Log::info('التحقق من KYC: لم يتم اكتمال التحقق', [
                        'user_id' => $user->id,
                        'user_type' => $user->user_type,
                        'is_verified' => $user->is_verified
                    ]);
                    
                    // التوجيه إلى صفحة استكمال بيانات KYC
                    return redirect()->route($userType . '.verification')
                        ->with('warning', 'يجب عليك استكمال عملية التحقق للوصول إلى هذه الميزة.');
                }
            }
            
            // إذا اجتاز جميع الفحوصات، استمر في معالجة الطلب
            Log::info('التحقق من نوع المستخدم: نجاح', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'required_type' => $userType
            ]);
            
            return $next($request);
        } catch (\Exception $e) {
            Log::error('خطأ في التحقق من نوع المستخدم', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('home')
                ->with('error', 'حدث خطأ أثناء التحقق من صلاحياتك. يرجى المحاولة مرة أخرى.');
        }
    }
}
