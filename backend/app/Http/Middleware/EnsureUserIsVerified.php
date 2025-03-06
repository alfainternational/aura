<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsVerified
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
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // التحقق من أن المستخدم تم التحقق منه
        if (!$user->is_verified) {
            return redirect()->route('verification.notice')->with('message', 'يجب التحقق من هويتك قبل الوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
