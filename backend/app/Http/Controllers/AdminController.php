<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Middleware to ensure admin access
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        $userStats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'user_types' => User::groupBy('user_type')
                ->select('user_type', \DB::raw('count(*) as count'))
                ->get()
        ];

        $locationStats = [
            'total_locations' => Location::count(),
            'active_locations' => Location::where('status', 'active')->count(),
            'countries' => Location::distinct('country')->count()
        ];

        return view('admin.dashboard', [
            'userStats' => $userStats,
            'locationStats' => $locationStats
        ]);
    }

    /**
     * User management page
     */
    public function userManagement(Request $request)
    {
        $users = User::query()
            ->when($request->input('search'), function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($request->input('user_type'), function($query, $userType) {
                return $query->where('user_type', $userType);
            })
            ->when($request->input('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->paginate(50);

        $userTypes = User::distinct('user_type')->pluck('user_type');
        $statuses = ['active', 'suspended', 'inactive'];

        return view('admin.users.index', [
            'users' => $users,
            'userTypes' => $userTypes,
            'statuses' => $statuses
        ]);
    }

    /**
     * Location management page
     */
    public function locationManagement(Request $request)
    {
        $locations = Location::query()
            ->when($request->input('search'), function($query, $search) {
                return $query->where('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            })
            ->when($request->input('country'), function($query, $country) {
                return $query->where('country', $country);
            })
            ->when($request->input('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->paginate(50);

        $countries = Location::distinct('country')->pluck('country');
        $statuses = ['active', 'restricted'];

        return view('admin.locations.index', [
            'locations' => $locations,
            'countries' => $countries,
            'statuses' => $statuses
        ]);
    }

    /**
     * Update user status
     */
    public function updateUserStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,inactive'
        ]);

        $user = User::findOrFail($id);

        // Prevent changing status of the current admin
        if ($user->id === Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot change status of current admin'
            ], 403);
        }

        $user->update([
            'status' => $request->input('status')
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User status updated successfully',
            'user' => $user
        ]);
    }
}
