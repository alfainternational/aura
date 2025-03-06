<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AdminProfile;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Country; // Add this line

class AdminAuthController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    public function __construct()
    {
        // Apply authentication middleware to all methods except login-related ones
        $this->middleware(['auth', 'check-role:admin'])->except(['showLoginForm', 'login']);
    }
    
    /**
     * Display the admin profile completion page
     * 
     * @return \Illuminate\Http\Response
     */
    public function completeProfile()
    {
        try {
            $user = Auth::user();
            $profile = $user->adminProfile;
            
            if ($profile) {
                Log::info('Admin redirected from complete profile to dashboard', ['admin_id' => $user->id]);
                return redirect()->route('admin.dashboard');
            }
            
            // Get departments list for dropdown
            $departments = $this->getDepartmentsList();
            
            // Get available permissions
            $availablePermissions = $this->getAvailablePermissions();
            
            return view('admin.complete-profile', compact('user', 'departments', 'availablePermissions'));
        } catch (\Exception $e) {
            Log::error('Error displaying admin profile completion page', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while loading the profile completion page. Please try again.');
        }
    }
    
    /**
     * Get all available departments for dropdown
     * 
     * @return array
     */
    private function getDepartmentsList()
    {
        // This could be fetched from a database table in production
        return [
            'IT' => 'IT Department',
            'Finance' => 'Finance Department',
            'Operations' => 'Operations Department',
            'Customer Support' => 'Customer Support Department',
            'Marketing' => 'Marketing Department',
            'HR' => 'Human Resources Department',
        ];
    }
    
    /**
     * Get all available permissions for admin
     * 
     * @return array
     */
    private function getAvailablePermissions()
    {
        // This could be fetched from a permissions table in production
        return [
            'users.view' => 'View Users',
            'users.create' => 'Create Users',
            'users.edit' => 'Edit Users',
            'users.delete' => 'Delete Users',
            'kyc.view' => 'View KYC Verifications',
            'kyc.approve' => 'Approve KYC Verifications',
            'kyc.reject' => 'Reject KYC Verifications',
            'reports.view' => 'View Reports',
            'settings.edit' => 'Edit System Settings',
            'logs.view' => 'View System Logs',
        ];
    }

    /**
     * Store admin profile information
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeProfile(Request $request)
    {
        try {
            $request->validate([
                'phone_number' => 'required|string|max:20',
                'position' => 'required|string|max:100',
                'department' => 'required|string|max:100',
                'office_address' => 'nullable|string|max:255',
                'office_phone' => 'nullable|string|max:20',
                'emergency_contact' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'profile_image' => 'nullable|image|max:2048',
                'permissions' => 'nullable|array',
            ]);

            $user = Auth::user();
            
            // Check if profile already exists
            if ($user->adminProfile) {
                Log::warning('Admin attempted to create duplicate profile', ['admin_id' => $user->id]);
                return redirect()->route('admin.dashboard')
                    ->with('info', 'Your profile has already been completed.');
            }
            
            // Update user data
            $user->phone_number = $request->phone_number;
            $user->birth_date = $request->birth_date;
            $user->gender = $request->gender;
            
            // Handle profile image
            if ($request->hasFile('profile_image')) {
                $imagePath = $request->file('profile_image')->store('profile_images', 'public');
                $user->profile_image = $imagePath;
            }
            
            $user->save();
            
            // Process permissions with validation
            $availablePermissions = array_keys($this->getAvailablePermissions());
            $permissions = $request->has('permissions') ? $request->permissions : [];
            
            // Only allow valid permissions
            $validPermissions = array_intersect($permissions, $availablePermissions);
            
            // Create admin profile
            $profile = new AdminProfile([
                'position' => $request->position,
                'department' => $request->department,
                'office_address' => $request->office_address,
                'office_phone' => $request->office_phone,
                'emergency_contact' => $request->emergency_contact,
                'permissions' => json_encode($validPermissions),
                'last_active_at' => now(),
            ]);
            
            $user->adminProfile()->save($profile);
            
            // Log the profile creation
            Log::info('Admin profile created successfully', ['admin_id' => $user->id]);
            
            return redirect()->route('admin.dashboard')
                ->with('success', 'Profile completed successfully');
        } catch (\Exception $e) {
            Log::error('Error storing admin profile', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while saving your profile. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display admin dashboard
     * 
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();
            $profile = $user->adminProfile;
            
            if (!$profile) {
                Log::info('Admin redirected to complete profile', ['admin_id' => $user->id]);
                return redirect()->route('admin.complete-profile');
            }
            
            // Update last active timestamp
            $profile->last_active_at = now();
            $profile->save();
            
            // Get recent notifications
            $notifications = $user->notifications()->latest()->take(5)->get();
            
            // Get system stats for admin
            $stats = $this->getDashboardStats();
            
            return view('admin.dashboard', compact('user', 'profile', 'notifications', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error displaying admin dashboard', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while loading the dashboard. Please try again.');
        }
    }
    
    /**
     * Get dashboard statistics for admin
     *
     * @return array
     */
    private function getDashboardStats()
    {
        return [
            'users_count' => User::count(),
            'admins_count' => User::where('user_type', 'admin')->count(),
            'customers_count' => User::where('user_type', 'customer')->count(),
            'merchants_count' => User::where('user_type', 'merchant')->count(),
            'messengers_count' => User::where('user_type', 'messenger')->count(),
            'pending_kyc' => DB::table('kyc_verifications')->where('status', 'pending')->count(),
            'active_users_today' => User::whereDate('last_login_at', Carbon::today())->count(),
            'new_users_this_week' => User::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
        ];
    }

    /**
     * Display admin profile page
     * 
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        try {
            $user = Auth::user();
            $profile = $user->adminProfile;
            
            if (!$profile) {
                return redirect()->route('admin.complete-profile');
            }
            
            // Get login history
            $loginHistory = LoginHistory::where('user_id', $user->id)
                ->orderBy('login_at', 'desc')
                ->take(10)
                ->get();
            
            $departments = $this->getDepartmentsList();
            
            return view('admin.profile', compact('user', 'profile', 'departments', 'loginHistory'));
        } catch (\Exception $e) {
            Log::error('Error displaying admin profile', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while loading your profile. Please try again.');
        }
    }

    /**
     * Update admin profile information
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $profile = $user->adminProfile;
            
            if (!$profile) {
                return redirect()->route('admin.complete-profile');
            }
            
            $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'position' => 'required|string|max:100',
                'department' => 'required|string|max:100',
                'office_address' => 'nullable|string|max:255',
                'office_phone' => 'nullable|string|max:20',
                'emergency_contact' => 'nullable|string|max:20',
                'birth_date' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'profile_image' => 'nullable|image|max:2048',
            ]);

            // Update user data
            $user->name = $request->name;
            $user->phone_number = $request->phone_number;
            $user->birth_date = $request->birth_date;
            $user->gender = $request->gender;

            // Handle profile image
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                
                $imagePath = $request->file('profile_image')->store('profile_images', 'public');
                $user->profile_image = $imagePath;
            }

            $user->save();
            
            // Update admin profile
            $profile->position = $request->position;
            $profile->department = $request->department;
            $profile->office_address = $request->office_address;
            $profile->office_phone = $request->office_phone;
            $profile->emergency_contact = $request->emergency_contact;
            $profile->last_active_at = now();
            $profile->save();

            // Log the profile update
            Log::info('Admin profile updated', ['admin_id' => $user->id]);
            
            return back()->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating admin profile', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while updating your profile. Please try again.')
                ->withInput();
        }
    }

    /**
     * Display permissions management page
     * 
     * @return \Illuminate\Http\Response
     */
    public function permissions()
    {
        try {
            $user = Auth::user();
            $profile = $user->adminProfile;
            
            if (!$profile) {
                return redirect()->route('admin.complete-profile');
            }
            
            $currentPermissions = json_decode($profile->permissions ?: '[]', true);
            $availablePermissions = $this->getAvailablePermissions();
            
            // Group permissions by category for better UI organization
            $groupedPermissions = [];
            foreach ($availablePermissions as $key => $value) {
                $category = explode('.', $key)[0];
                if (!isset($groupedPermissions[$category])) {
                    $groupedPermissions[$category] = [];
                }
                $groupedPermissions[$category][$key] = $value;
            }
            
            return view('admin.permissions', compact('user', 'profile', 'currentPermissions', 'groupedPermissions'));
        } catch (\Exception $e) {
            Log::error('Error displaying admin permissions page', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while loading the permissions page. Please try again.');
        }
    }

    /**
     * Update admin permissions
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePermissions(Request $request)
    {
        try {
            $request->validate([
                'permissions' => 'nullable|array',
            ]);

            $user = Auth::user();
            $profile = $user->adminProfile;
            
            if (!$profile) {
                return redirect()->route('admin.complete-profile');
            }
            
            // Validate permissions against available ones
            $availablePermissions = array_keys($this->getAvailablePermissions());
            $permissions = $request->has('permissions') ? $request->permissions : [];
            
            // Only allow valid permissions
            $validPermissions = array_intersect($permissions, $availablePermissions);
            
            // Update permissions
            $profile->permissions = json_encode($validPermissions);
            $profile->save();
            
            // Log the permissions update
            Log::info('Admin permissions updated', [
                'admin_id' => $user->id,
                'permissions_count' => count($validPermissions)
            ]);
            
            return back()->with('success', 'Permissions updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating admin permissions', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while updating permissions. Please try again.');
        }
    }
    
    /**
     * Display security settings page
     * 
     * @return \Illuminate\Http\Response
     */
    public function security()
    {
        try {
            $user = Auth::user();
            $profile = $user->adminProfile;
            
            if (!$profile) {
                return redirect()->route('admin.complete-profile');
            }
            
            // Get login history for security tab
            $loginHistory = LoginHistory::where('user_id', $user->id)
                ->orderBy('login_at', 'desc')
                ->paginate(10);
                
            // Get active sessions if you have a sessions table
            $activeSessions = []; // Placeholder for active sessions data
            
            return view('admin.security', compact('user', 'profile', 'loginHistory', 'activeSessions'));
        } catch (\Exception $e) {
            Log::error('Error displaying admin security page', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while loading the security page. Please try again.');
        }
    }
    
    /**
     * Change admin password
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            $user = Auth::user();
            
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect');
            }
            
            // Update password
            $user->password = Hash::make($request->password);
            $user->password_changed_at = now();
            $user->save();
            
            // Log password change for security
            Log::info('Admin password changed', ['admin_id' => $user->id]);
            
            return back()->with('success', 'Password changed successfully');
        } catch (\Exception $e) {
            Log::error('Error changing admin password', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while changing your password. Please try again.');
        }
    }
    
    /**
     * Toggle two-factor authentication
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleTwoFactor(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Check if 2FA is being enabled or disabled
            $enable2FA = $request->has('enable') && $request->enable == 1;
            
            if ($enable2FA) {
                // Logic to enable 2FA
                // This would involve generating a secret, showing QR code, etc.
                $user->two_factor_enabled = true;
                // $user->two_factor_secret = $secretKey;
            } else {
                // Logic to disable 2FA
                $user->two_factor_enabled = false;
                // $user->two_factor_secret = null;
            }
            
            $user->save();
            
            // Log 2FA status change
            Log::info('Admin changed 2FA status', [
                'admin_id' => $user->id, 
                'enabled' => $enable2FA
            ]);
            
            $message = $enable2FA ? 
                'Two-factor authentication has been enabled' : 
                'Two-factor authentication has been disabled';
                
            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error toggling admin 2FA', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return back()->with('error', 'An error occurred while updating two-factor authentication. Please try again.');
        }
    }
    
    /**
     * Show the application login form for admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // Get active countries for the view
        $countries = Country::where('is_active', true)->get();
        
        // Default country (Sudan)
        $defaultCountry = Country::where('code', 'SD')->first();

        return view('auth.admin.login', [
            'countries' => $countries,
            'defaultCountry' => $defaultCountry
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            // Log successful login
            Log::info('Admin login successful', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        // Log failed login attempt
        Log::warning('Admin login failed', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return Auth::attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['user_type'] = 'admin';
        $credentials['is_active'] = true;
        
        return $credentials;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }
    
    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}
