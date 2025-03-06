<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check-role:messenger']);
    }

    /**
     * Show current messenger status
     */
    public function index()
    {
        $user = Auth::user();
        return view('messenger.status.index', compact('user'));
    }

    /**
     * Update messenger status
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:available,busy,offline,on_delivery',
            'availability_reason' => 'nullable|string|max:255'
        ]);

        $user = Auth::user();
        $user->status = $validatedData['status'];
        $user->availability_reason = $validatedData['availability_reason'] ?? null;
        $user->save();

        return redirect()->route('messenger.status')
            ->with('success', 'Status updated successfully');
    }

    /**
     * Update messenger location
     */
    public function updateLocation(Request $request)
    {
        $validatedData = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'address' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $user->current_location = json_encode([
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
            'accuracy' => $validatedData['accuracy'] ?? null,
            'address' => $validatedData['address'] ?? null,
            'updated_at' => now()
        ]);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully'
        ]);
    }
}
