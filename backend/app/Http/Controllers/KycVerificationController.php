<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KycVerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check-role:admin']);
    }

    /**
     * KYC Verification Dashboard
     */
    public function index(Request $request)
    {
        $query = KycVerification::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        $verifications = $query->paginate(20);
        
        return view('admin.kyc.index', compact('verifications'));
    }

    /**
     * Pending KYC Verifications
     */
    public function pendingVerifications()
    {
        $pendingVerifications = KycVerification::where('status', 'pending')->paginate(20);
        
        return view('admin.kyc.pending', compact('pendingVerifications'));
    }

    /**
     * Show KYC Verification Details
     */
    public function showDetails(KycVerification $verification)
    {
        return view('admin.kyc.details', compact('verification'));
    }

    /**
     * Approve KYC Verification
     */
    public function approve(KycVerification $verification)
    {
        $verification->status = 'approved';
        $verification->approved_by = Auth::id();
        $verification->save();

        // Update user's KYC status
        $verification->user->kyc_verified = true;
        $verification->user->save();

        return redirect()->route('admin.kyc.index')
            ->with('success', 'KYC Verification Approved');
    }

    /**
     * Reject KYC Verification
     */
    public function reject(Request $request, KycVerification $verification)
    {
        $validatedData = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $verification->status = 'rejected';
        $verification->rejected_by = Auth::id();
        $verification->rejection_reason = $validatedData['rejection_reason'];
        $verification->save();

        return redirect()->route('admin.kyc.index')
            ->with('error', 'KYC Verification Rejected');
    }
}
