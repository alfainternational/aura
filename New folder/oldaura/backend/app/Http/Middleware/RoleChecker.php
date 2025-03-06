<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class RoleChecker
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
        // Log middleware execution for debugging
        Log::debug('RoleChecker middleware executed', [
            'user_id' => $request->user() ? $request->user()->id : 'Guest',
            'required_role' => $role
        ]);

        // Check if user is logged in
        if (!Auth::check()) {
            Log::warning('User not authenticated, redirecting to login');
            return redirect('login');
        }

        // Convert comma-separated roles to array
        $roles = explode(',', $role);
        
        // Get user type
        $userType = $request->user()->user_type;
        
        // Log the checking process
        Log::info('Checking user role', [
            'user_id' => $request->user()->id,
            'user_type' => $userType,
            'required_roles' => $roles
        ]);
        
        // Check if user has any of the allowed roles
        if (in_array($userType, $roles)) {
            Log::info('User has required role', [
                'user_id' => $request->user()->id,
                'user_type' => $userType,
                'matched_role' => $userType
            ]);
            return $next($request);
        }
        
        // Log the failed check
        Log::warning('User type does not match required role', [
            'user_id' => $request->user()->id,
            'user_type' => $userType,
            'required_roles' => $roles
        ]);
        
        // Redirect based on user type
        return $this->redirectBasedOnUserType($userType);
    }

    /**
     * Redirect user based on their type
     * 
     * @param string $userType
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectBasedOnUserType($userType)
    {
        // Define redirect paths for each user type
        $homeRoutes = [
            'admin' => '/admin/dashboard',
            'customer' => '/customer/dashboard',
            'merchant' => '/merchant/dashboard',
            'agent' => '/agent/dashboard',
            'messenger' => '/messenger/dashboard',
        ];

        // Determine redirect path based on user type
        if (isset($homeRoutes[$userType])) {
            Log::info('Redirecting user to their dashboard', [
                'user_type' => $userType,
                'redirect_path' => $homeRoutes[$userType]
            ]);
            return redirect($homeRoutes[$userType]);
        }

        // If no specific path, redirect to home
        Log::info('No specific redirect path for user type, redirecting to home', [
            'user_type' => $userType
        ]);
        return redirect('/');
    }
}
