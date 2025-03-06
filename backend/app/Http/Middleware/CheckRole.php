<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Log middleware execution for debugging
        Log::debug('CheckRole middleware executed', [
            'user_id' => $request->user() ? $request->user()->id : 'Guest',
            'required_roles' => $roles
        ]);

        // Check if user is logged in
        if (!Auth::check()) {
            Log::warning('User not authenticated, redirecting to login');
            return redirect('login');
        }

        // Get user type
        $userType = $request->user()->user_type;
        
        // Log the checking process
        Log::info('Checking user roles', [
            'user_id' => $request->user()->id,
            'user_type' => $userType,
            'required_roles' => $roles
        ]);
        
        // Fix the roles array if it comes as nested array
        if (isset($roles[0]) && is_array($roles[0])) {
            $roles = $roles[0];
        }
        
        // If roles parameter contains comma-separated values, split it
        if (count($roles) === 1 && strpos($roles[0], ',') !== false) {
            $roles = explode(',', $roles[0]);
        }
        
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
        Log::warning('User type does not match any allowed roles', [
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

        // Check if RouteServiceProvider has home routes defined
        if (property_exists(RouteServiceProvider::class, 'HOME_ROUTES')) {
            $homeRoutes = array_merge($homeRoutes, RouteServiceProvider::$HOME_ROUTES);
        }

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