<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireKycVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // التحقق من أن المستخدم قد أكمل عملية التحقق من الهوية
        if (!$user || $user->kyc_status !== 'approved') {
            // إذا كان المستخدم في صفحة التحقق من الهوية، نسمح له بالمتابعة
            if ($request->is('user/kyc*')) {
                return $next($request);
            }

            // إعادة توجيه المستخدم إلى صفحة التحقق من الهوية مع رسالة
            return redirect()->route('user.kyc')->with('warning', 'يجب عليك إكمال عملية التحقق من الهوية للوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
