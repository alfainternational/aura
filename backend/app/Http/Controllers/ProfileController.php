<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Services\LocationService;
use App\Services\IdentityVerificationService;

class ProfileController extends Controller
{
    protected $locationService;
    protected $identityVerificationService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(
        LocationService $locationService, 
        IdentityVerificationService $identityVerificationService
    ) {
        $this->middleware('auth');
        $this->locationService = $locationService;
        $this->identityVerificationService = $identityVerificationService;
    }

    /**
     * Display user profile
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('profile.index', [
            'user' => $user,
            'location' => $this->locationService->getUserLocation($user),
            'verificationStatus' => $this->identityVerificationService->getVerificationStatus($user)
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|unique:users,phone,' . $user->id,
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'profile_picture' => 'sometimes|image|max:2048',
            'country' => 'sometimes|string',
            'city' => 'sometimes|string'
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Remove old profile picture
            if ($user->profile_picture) {
                Storage::delete($user->profile_picture);
            }

            // Store new profile picture
            $validatedData['profile_picture'] = $request->file('profile_picture')
                ->store('profile_pictures', 'public');
        }

        // Update user location if provided
        if (isset($validatedData['country']) && isset($validatedData['city'])) {
            $this->locationService->updateUserLocation(
                $user, 
                $validatedData['country'], 
                $validatedData['city']
            );
        }

        // Update user profile
        $user->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user
        ]);
    }

    /**
     * Verify user identity
     */
    public function verifyIdentity(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'document_type' => 'required|in:national_id,passport',
            'document_front' => 'required|image|max:5120',
            'document_back' => 'sometimes|image|max:5120',
            'selfie' => 'required|image|max:5120'
        ]);

        // Store verification documents
        $documentFrontPath = $request->file('document_front')
            ->store('identity_verification', 'private');
        
        $documentBackPath = $request->hasFile('document_back')
            ? $request->file('document_back')->store('identity_verification', 'private')
            : null;
        
        $selfiePath = $request->file('selfie')
            ->store('identity_verification', 'private');

        // Initiate verification process
        $verificationResult = $this->identityVerificationService->initiateVerification(
            $user,
            $documentFrontPath,
            $documentBackPath,
            $selfiePath,
            $validatedData['document_type']
        );

        return response()->json([
            'status' => $verificationResult['status'],
            'message' => $verificationResult['message']
        ]);
    }
}
