<?php

namespace App\Services;

use App\Models\User;
use App\Models\IdentityVerification;
use Illuminate\Support\Facades\Storage;

class IdentityVerificationService
{
    /**
     * Initiate identity verification process
     */
    public function initiateVerification(
        User $user, 
        string $documentFrontPath, 
        ?string $documentBackPath, 
        string $selfiePath, 
        string $documentType
    ): array {
        // Validate document type
        $allowedDocumentTypes = ['national_id', 'passport'];
        if (!in_array($documentType, $allowedDocumentTypes)) {
            return [
                'status' => 'error',
                'message' => 'نوع المستند غير صالح'
            ];
        }

        // Create verification record
        $verification = IdentityVerification::create([
            'user_id' => $user->id,
            'document_type' => $documentType,
            'document_front_path' => $documentFrontPath,
            'document_back_path' => $documentBackPath,
            'selfie_path' => $selfiePath,
            'status' => 'pending'
        ]);

        // Update user verification status
        $user->update([
            'verification_status' => 'pending'
        ]);

        // Trigger verification processing (could be a queued job)
        $this->processVerification($verification);

        return [
            'status' => 'success',
            'message' => 'تم استلام طلب التحقق من الهوية',
            'verification_id' => $verification->id
        ];
    }

    /**
     * Process verification documents
     */
    private function processVerification(IdentityVerification $verification)
    {
        // In a real-world scenario, this would involve:
        // 1. AI/ML document validation
        // 2. OCR text extraction
        // 3. Facial recognition comparison
        // 4. External API verification

        // Simulated verification logic
        $isValid = $this->simulateDocumentValidation($verification);

        $verification->update([
            'status' => $isValid ? 'approved' : 'rejected'
        ]);

        $verification->user->update([
            'verification_status' => $isValid ? 'verified' : 'rejected'
        ]);
    }

    /**
     * Simulate document validation (mock method)
     */
    private function simulateDocumentValidation(IdentityVerification $verification): bool
    {
        // Basic validation checks
        return 
            Storage::exists($verification->document_front_path) &&
            Storage::exists($verification->selfie_path) &&
            $verification->user->country !== null;
    }

    /**
     * Get verification status for a user
     */
    public function getVerificationStatus(User $user): array
    {
        $latestVerification = IdentityVerification::where('user_id', $user->id)
            ->latest()
            ->first();

        return [
            'status' => $user->verification_status ?? 'not_submitted',
            'document_type' => $latestVerification?->document_type,
            'submitted_at' => $latestVerification?->created_at,
            'last_status' => $latestVerification?->status
        ];
    }
}
