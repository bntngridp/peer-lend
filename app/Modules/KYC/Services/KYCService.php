<?php

namespace App\Modules\KYC\Services;

use App\Models\KYC;
use App\Models\KYCDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KYCService
{
    /**
     * Submit a new KYC verification request.
     *
     * Saves the photo documents in private storage and puts the request status
     * to 'pending' for admin review. Runs inside a database transaction.
     */
    public function submitKYC(User $user, array $data): KYC
    {
        return DB::transaction(function () use ($user, $data) {
            // Find or create KYC record
            $kyc = KYC::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'status'          => 'pending',
                    'rejected_reason' => null,
                    'reviewed_by'     => null,
                    'reviewed_at'     => null,
                ]
            );

            // Process uploaded documents
            $documentTypes = ['ktp', 'selfie', 'npwp'];
            foreach ($documentTypes as $type) {
                if (isset($data[$type]) && $data[$type] instanceof UploadedFile) {
                    $this->storeDocument($kyc, $data[$type], $type);
                }
            }

            return $kyc;
        });
    }

    /**
     * Upload and store a KYC document to non-public/private storage disk.
     */
    private function storeDocument(KYC $kyc, UploadedFile $file, string $type): KYCDocument
    {
        // Delete old document of same type if exists
        $oldDoc = $kyc->documents()->where('type', $type)->first();
        if ($oldDoc) {
            Storage::disk('local')->delete($oldDoc->file_path);
            $oldDoc->delete();
        }

        // Generate secure random name and store in private kyc folder
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs("kyc/{$type}", $filename, 'local');

        return KYCDocument::create([
            'kyc_id'         => $kyc->id,
            'type'           => $type,
            'file_path'      => $path,
            'storage_driver' => 'local',
        ]);
    }

    /**
     * Approve a pending KYC verification request.
     */
    public function approveKYC(KYC $kyc, User $admin): KYC
    {
        if ($kyc->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => ['Only pending KYC requests can be approved.'],
            ]);
        }

        $kyc->update([
            'status'      => 'approved',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return $kyc;
    }

    /**
     * Reject a pending KYC verification request with a reason.
     */
    public function rejectKYC(KYC $kyc, User $admin, string $reason): KYC
    {
        if ($kyc->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => ['Only pending KYC requests can be rejected.'],
            ]);
        }

        $kyc->update([
            'status'          => 'rejected',
            'rejected_reason' => $reason,
            'reviewed_by'     => $admin->id,
            'reviewed_at'     => now(),
        ]);

        return $kyc;
    }
}
