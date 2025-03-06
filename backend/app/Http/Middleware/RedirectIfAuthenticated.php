<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // إعادة التوجيه بناءً على نوع المستخدم
                $user = Auth::guard($guard)->user();
                
                switch ($user->user_type) {
                    case 'admin':
                        // إذا كان مشرف، توجيهه إلى لوحة تحكم المشرف
                        if ($user->role === 'supervisor') {
                            return redirect()->route('supervisor.dashboard');
                        }
                        // وإلا توجيهه إلى لوحة تحكم المسؤول
                        return redirect()->route('admin.dashboard');
                    case 'merchant':
                        return redirect()->route('merchant.dashboard');
                    case 'agent':
                        return redirect()->route('agent.dashboard');
                    case 'messenger':
                        return redirect()->route('messenger.dashboard');
                    default: // customer
                        return redirect()->route('customer.dashboard');
                }
            }
        }

        return $next($request);
    }
}
