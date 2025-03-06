<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Providers\RouteServiceProvider;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $userType
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $userType)
    {
        // تسجيل الدخول إلى الميدلوير للتصحيح
        Log::info('CheckUserType middleware is being called with user type: ' . $userType);
        
        if (!Auth::check()) {
            return redirect('login');
        }

        Log::info('Authenticated user type: ' . $request->user()->user_type);
        
        // التحقق من نوع المستخدم
        if ($request->user()->user_type !== $userType) {
            Log::warning('User type mismatch in CheckUserType: Expected ' . $userType . ', got ' . $request->user()->user_type);
            
            // Redirect based on user type - using the new checkrole middleware instead
            return redirect()->route('login');
        }

        return $next($request);
    }
}
