<?php

namespace App\Modules\KYC\Services;

use App\Models\KYC;
use App\Models\KYCDocument;
use App\Models\User;
use App\Modules\Shared\Services\AuditLogService;
use App\Modules\Shared\Services\NotificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KYCService
{
    public function __construct(
        private readonly OCRService          $ocrService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Submit a new KYC verification request.
     *
     * Saves the photo documents in private storage and puts the request status
     * to 'pending' or 'rejected' based on OCR validation. Runs inside a database transaction.
     */
    public function submitKYC(User $user, array $data): KYC
    {
        return DB::transaction(function () use ($user, $data) {
            // Run simulated OCR check on KTP card
            $ocrResult = $this->ocrService->parseKTP($data['ktp'], $user->profile->full_name);

            $status = 'pending';
            $rejectedReason = null;

            // Name matching check
            if (strtoupper(trim($ocrResult['full_name'])) !== strtoupper(trim($user->profile->full_name))) {
                $status = 'rejected';
                $rejectedReason = "OCR Validation failed: KTP name '{$ocrResult['full_name']}' does not match profile name '{$user->profile->full_name}'.";
            }

            // Find or create KYC record
            $kyc = KYC::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nik'             => $ocrResult['nik'],
                    'status'          => $status,
                    'rejected_reason' => $rejectedReason,
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

            // Write Audit Log
            app(\App\Modules\Shared\Services\AuditLogService::class)->log(
                'kyc_submit',
                KYC::class,
                $kyc->id,
                $user,
                ['status' => $kyc->status, 'nik' => $ocrResult['nik']]
            );

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

        app(AuditLogService::class)->log(
            'kyc_approve',
            KYC::class,
            $kyc->id,
            $admin
        );

        $this->notificationService->notifyKycApproved($kyc->user);

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

        app(AuditLogService::class)->log(
            'kyc_reject',
            KYC::class,
            $kyc->id,
            $admin,
            ['reason' => $reason]
        );

        $this->notificationService->notifyKycRejected($kyc->user, $reason);

        return $kyc;
    }
}
