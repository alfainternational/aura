<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function __construct()
    {
        // Allow guests to access login form and login action
        $this->middleware(['guest'])->except(['logout']);
        
        // Require admin authentication for logout
        $this->middleware(['auth', 'check-role:admin'])->only(['logout']);
    }

    /**
     * Show the admin login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.admin.login');
    }

    /**
     * Handle an admin login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt to log in with admin role
        if (Auth::attempt($credentials + ['user_type' => 'admin'], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the admin out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Show the password reset request form.
     *
     * @return \Illuminate\View\View
     */
    public function showPasswordResetForm()
    {
        return view('auth.admin.passwords.email');
    }

    /**
     * Send a password reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Implement password reset link sending logic
        // This could use Laravel's built-in password broker
        // Ensure only admin users can reset their password
        
        return back()->with('status', trans('passwords.sent'));
    }

    /**
     * Show the password reset confirmation form.
     *
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showPasswordResetConfirmForm($token)
    {
        return view('auth.admin.passwords.reset', ['token' => $token]);
    }

    /**
     * Reset the password for the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Implement password reset logic
        // Ensure only admin users can reset their password
        
        return redirect()->route('admin.login')->with('status', trans('passwords.reset'));
    }
}
