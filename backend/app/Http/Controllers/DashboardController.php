<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * General dashboard index
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirect to appropriate dashboard based on user type
        return match($user->user_type) {
            'customer' => redirect()->route('customer.dashboard'),
            'merchant' => redirect()->route('merchant.dashboard'),
            'agent' => redirect()->route('agent.dashboard'),
            'messenger' => redirect()->route('messenger.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'supervisor' => redirect()->route('supervisor.dashboard'),
            default => redirect()->route('home')
        };
    }

    /**
     * Customer dashboard
     */
    public function customerDashboard()
    {
        $user = Auth::user();
        return view('dashboard.customer', compact('user'));
    }

    /**
     * Merchant dashboard
     */
    public function merchantDashboard()
    {
        $user = Auth::user();
        return view('dashboard.merchant', compact('user'));
    }

    /**
     * Agent dashboard
     */
    public function agentDashboard()
    {
        $user = Auth::user();
        return view('dashboard.agent', compact('user'));
    }

    /**
     * Messenger dashboard
     */
    public function messengerDashboard()
    {
        $user = Auth::user();
        return view('dashboard.messenger', compact('user'));
    }

    /**
     * Admin dashboard
     */
    public function adminDashboard()
    {
        $user = Auth::user();
        return view('dashboard.admin', compact('user'));
    }

    /**
     * Supervisor dashboard
     */
    public function supervisorDashboard()
    {
        $user = Auth::user();
        return view('dashboard.supervisor', compact('user'));
    }
}
