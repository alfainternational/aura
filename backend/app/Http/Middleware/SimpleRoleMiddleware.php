<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimpleRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = $request->user();
        
        if ($user->user_type == $role) {
            return $next($request);
        }
        
        return redirect('/')->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة.');
    }
}
