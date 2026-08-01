<?php

namespace App\Modules\Wallet\Services;

use App\Models\Payment;
use App\Models\User;
use App\Modules\Shared\Services\AuditLogService;
use App\Modules\Shared\Services\NotificationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class XenditService
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly AuditLogService $auditLogService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Create an outbound disbursement (withdrawal) via Xendit API.
     */
    public function createDisbursement(
        User $user,
        float $amount,
        string $bankCode,
        string $accountNumber,
        string $accountHolderName
    ): array {
        // 1. Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'xendit',
            'amount'  => $amount,
            'status'  => 'pending',
            'payload' => [
                'type'                => 'withdrawal',
                'bank_code'           => strtoupper($bankCode),
                'account_number'      => $accountNumber,
                'account_holder_name' => $accountHolderName,
            ],
        ]);

        $secretKey = config('xendit.secret_key');
        $disbursementUrl = config('xendit.disbursement_url');

        $externalId = 'WD-' . $payment->id;

        $payload = [
            'external_id'          => $externalId,
            'amount'               => (int) $amount,
            'bank_code'            => strtoupper($bankCode),
            'account_holder_name'  => $accountHolderName,
            'account_number'       => $accountNumber,
            'description'          => 'LendFlow Wallet Withdrawal #' . substr($payment->id, 0, 8),
        ];

        // If placeholder key, execute instant mock disbursement for seamless testing
        if (str_contains($secretKey, 'placeholderkey')) {
            Log::info("Xendit Mock Disbursement executed for Payment #{$payment->id}");

            $mockResponse = [
                'id'                  => 'disb_' . bin2hex(random_bytes(8)),
                'external_id'          => $externalId,
                'status'              => 'COMPLETED',
                'amount'              => (int) $amount,
                'bank_code'           => strtoupper($bankCode),
                'account_holder_name' => $accountHolderName,
                'account_number'      => $accountNumber,
                'is_instant'          => true,
            ];

            $payment->update([
                'status'         => 'completed',
                'gateway_ref_id' => $mockResponse['id'],
                'payload'        => array_merge($payment->payload ?? [], $mockResponse),
            ]);

            // Deduct user wallet balance
            $idr = \App\Models\Currency::where('code', 'IDR')->firstOrFail();
            $this->walletService->withdraw(
                user: $user,
                currencyId: $idr->id,
                amount: (string) $amount,
                description: "Xendit payout: {$mockResponse['id']}"
            );

            // Send notification
            $this->notificationService->send(
                $user,
                'wallet_withdrawal',
                'Penarikan Dana Berhasil / Withdrawal Completed',
                "Penarikan sebesar Rp " . number_format($amount, 0, ',', '.') . " ke rekening {$bankCode} - {$accountNumber} berhasil diproses via Xendit."
            );

            return [
                'disbursement_id' => $mockResponse['id'],
                'status'          => 'COMPLETED',
                'payment'         => $payment,
            ];
        }

        // Real API Call to Xendit
        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($disbursementUrl, $payload);

            if (!$response->successful()) {
                Log::error('Xendit Disbursement API call failed: ' . $response->body());
                $payment->update([
                    'status'  => 'failed',
                    'payload' => array_merge($payment->payload ?? [], ['error' => $response->body()]),
                ]);

                throw new \Exception('Xendit Payout Failed: ' . ($response->json()['message'] ?? 'Gateway Error'));
            }

            $responseData = $response->json();
            $disbursementId = $responseData['id'] ?? null;
            $status = strtoupper($responseData['status'] ?? 'PENDING');

            $payment->update([
                'gateway_ref_id' => $disbursementId,
                'status'         => strtolower($status) === 'completed' ? 'completed' : 'pending',
                'payload'        => array_merge($payment->payload ?? [], $responseData),
            ]);

            if ($status === 'COMPLETED') {
                $this->walletService->withdraw($user, 'IDR', $amount, $disbursementId);
            }

            return [
                'disbursement_id' => $disbursementId,
                'status'          => $status,
                'payment'         => $payment,
            ];

        } catch (\Throwable $e) {
            Log::error('Xendit Disbursement Exception: ' . $e->getMessage());
            $payment->update([
                'status'  => 'failed',
                'payload' => array_merge($payment->payload ?? [], ['error' => $e->getMessage()]),
            ]);

            throw $e;
        }
    }

    /**
     * Process Xendit Payout Webhook Callback.
     */
    public function handleWebhook(array $payload): bool
    {
        $externalId = $payload['external_id'] ?? null;
        $status = strtoupper($payload['status'] ?? '');
        $disbursementId = $payload['id'] ?? null;

        if (!$externalId) {
            return false;
        }

        $paymentId = str_replace('WD-', '', $externalId);
        $payment = Payment::find($paymentId);

        if (!$payment) {
            Log::warning("Xendit Webhook: Payment #{$paymentId} not found.");
            return false;
        }

        $payment->update([
            'gateway_ref_id' => $disbursementId ?? $payment->gateway_ref_id,
            'payload'        => array_merge($payment->payload ?? [], $payload),
        ]);

        if ($status === 'COMPLETED' && $payment->status !== 'completed') {
            $payment->update(['status' => 'completed']);
            $user = $payment->user;

            if ($user) {
                $idr = \App\Models\Currency::where('code', 'IDR')->firstOrFail();
                $this->walletService->withdraw($user, $idr->id, (string) $payment->amount, "Xendit withdrawal: {$disbursementId}");

                $this->notificationService->send(
                    $user,
                    'wallet_withdrawal',
                    'Penarikan Dana Berhasil / Withdrawal Confirmed',
                    "Penarikan sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " telah sukses ditransfer ke rekening bank Anda."
                );
            }

            return true;
        }

        if ($status === 'FAILED') {
            $payment->update(['status' => 'failed']);
            return true;
        }

        return true;
    }
}
