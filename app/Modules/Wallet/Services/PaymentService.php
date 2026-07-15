<?php

namespace App\Modules\Wallet\Services;

use App\Models\Currency;
use App\Models\Payment;
use App\Models\User;
use App\Modules\Shared\Services\AuditLogService;
use App\Modules\Shared\Services\NotificationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly AuditLogService $auditLogService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Initiate a new Midtrans Snap deposit.
     * Returns the Snap Token and Redirect URL.
     */
    public function initiateDeposit(User $user, float $amount): array
    {
        $idr = Currency::where('code', 'IDR')->firstOrFail();

        // 1. Create a pending Payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'midtrans',
            'amount'  => $amount,
            'status'  => 'pending',
        ]);

        // 2. Call Midtrans Snap API via HTTP client
        $serverKey = config('midtrans.server_key');
        $authHeader = 'Basic ' . base64_encode($serverKey . ':');
        $snapUrl = config('midtrans.snap_url');

        $payload = [
            'transaction_details' => [
                'order_id'     => $payment->id,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => $user->profile?->full_name ?? $user->email,
                'email'      => $user->email,
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => $authHeader,
            ])->post($snapUrl, $payload);

            if (!$response->successful()) {
                Log::error('Midtrans Snap request failed: ' . $response->body());
                $payment->update([
                    'status'  => 'failed',
                    'payload' => ['error' => $response->body()],
                ]);
                throw ValidationException::withMessages([
                    'payment' => ['Failed to contact payment gateway. Please try again later.'],
                ]);
            }

            $responseData = $response->json();
            $snapToken = $responseData['token'] ?? null;
            $redirectUrl = $responseData['redirect_url'] ?? null;

            if (!$snapToken) {
                throw new \Exception('Midtrans Snap did not return a token.');
            }

            // Save payload response & Snap Token
            $payment->update([
                'gateway_ref_id' => $snapToken,
                'payload'        => $responseData,
            ]);

            return [
                'snap_token'   => $snapToken,
                'redirect_url' => $redirectUrl,
                'payment_id'   => $payment->id,
            ];

        } catch (\Throwable $e) {
            Log::error('Deposit initiation failed: ' . $e->getMessage());
            $payment->update([
                'status'  => 'failed',
                'payload' => ['error' => $e->getMessage()],
            ]);
            throw $e;
        }
    }

    /**
     * Handle incoming Midtrans Webhook notifications.
     */
    public function handleWebhook(array $payload): bool
    {
        $orderId     = $payload['order_id'] ?? null;
        $statusCode  = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signature   = $payload['signature_key'] ?? null;
        $status      = $payload['transaction_status'] ?? null;

        if (!$orderId || !$signature || !$statusCode || !$grossAmount) {
            Log::warning('Midtrans Webhook: Missing required parameters in payload.', $payload);
            return false;
        }

        // 1. Verify Midtrans signature key
        $serverKey = config('midtrans.server_key');
        $localSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($localSignature !== $signature) {
            Log::error("Midtrans Webhook signature verification failed for Order {$orderId}. Local signature does not match.");
            return false;
        }

        // 2. Process transaction status
        $newStatus = 'pending';
        if ($status === 'settlement' || ($status === 'capture' && ($payload['fraud_status'] ?? 'accept') === 'accept')) {
            $newStatus = 'success';
        } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
            $newStatus = 'failed';
        }

        // 3. Find and update the Payment record
        return DB::transaction(function () use ($orderId, $newStatus, $payload) {
            $payment = Payment::lockForUpdate()->find($orderId);

            if (!$payment) {
                Log::warning("Midtrans Webhook: Payment record with Order ID {$orderId} not found.");
                return false;
            }

            // If already processed, do nothing (idempotency check)
            if ($payment->status === 'success') {
                return true;
            }

            if ($newStatus === 'success') {
                // Deposit real cash to User wallet
                $idr = Currency::where('code', 'IDR')->firstOrFail();
                
                $walletTx = $this->walletService->deposit(
                    $payment->user,
                    $idr->id,
                    (string)$payment->amount,
                    "Midtrans deposit: Order {$payment->id}"
                );

                $payment->update([
                    'status'                => 'success',
                    'wallet_transaction_id' => $walletTx->id,
                    'payload'               => array_merge($payment->payload ?? [], ['webhook_received' => $payload]),
                ]);

                // Log audit
                $this->auditLogService->log(
                    'payment_webhook_settled',
                    Payment::class,
                    $payment->id,
                    $payment->user,
                    ['amount' => $payment->amount]
                );

                // Notify user
                $this->notificationService->send(
                    $payment->user,
                    'wallet_deposit',
                    'Deposit Berhasil! 💰',
                    "Dana sebesar Rp " . number_format((float)$payment->amount, 0, ',', '.') . " telah berhasil ditambahkan ke wallet kamu.",
                    ['route' => 'wallet.index']
                );

                Log::info("Midtrans Webhook successfully credited Rp " . number_format($payment->amount) . " to User {$payment->user_id}");
            } else {
                $payment->update([
                    'status'  => $newStatus,
                    'payload' => array_merge($payment->payload ?? [], ['webhook_received' => $payload]),
                ]);
            }

            return true;
        });
    }
}
