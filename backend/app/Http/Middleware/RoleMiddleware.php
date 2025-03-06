<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
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
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('Unauthenticated access attempt to restricted route');
            return redirect()->route('login');
        }

        // Get user type from authenticated user
        $user = $request->user();
        $userType = $user->user_type;
        
        // Log for debugging
        Log::info('RoleMiddleware check', [
            'user_id' => $user->id,
            'user_type' => $userType,
            'required_role' => $role
        ]);
        
        // Support for multiple roles separated by comma
        $roles = explode(',', $role);
        
        // Check if user type is in the allowed roles
        if (in_array($userType, $roles)) {
            Log::info('Role check passed', [
                'user_id' => $user->id,
                'user_type' => $userType,
                'roles' => $roles
            ]);
            return $next($request);
        }
        
        // If user doesn't have required role, redirect to appropriate dashboard
        Log::warning('User does not have required role', [
            'user_id' => $user->id,
            'user_type' => $userType,
            'required_roles' => $roles
        ]);
        
        // Determine redirect path based on user type
        $redirectPath = '/';
        if (property_exists(RouteServiceProvider::class, 'HOME')) {
            $redirectPath = RouteServiceProvider::HOME;
        }
        
        // Try to redirect to user's dashboard based on their type
        if ($userType && in_array($userType, ['admin', 'customer', 'merchant', 'agent', 'messenger'])) {
            $redirectPath = '/' . $userType . '/dashboard';
        }
        
        return redirect($redirectPath)
            ->with('error', 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
    }
}
