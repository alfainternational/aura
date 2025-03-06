<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            if ($request->user() && $request->user()->user_type === 'admin') {
                // إذا كان المستخدم مسؤولاً، توجيهه إلى لوحة التحكم الرئيسية
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->route('login');
        }

        return $next($request);
    }
}
