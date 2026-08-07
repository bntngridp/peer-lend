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
                $methodLabel = self::resolveMidtransMethodLabel($payload);
                
                $walletTx = $this->walletService->deposit(
                    $payment->user,
                    $idr->id,
                    (string)$payment->amount,
                    "Midtrans ({$methodLabel}) deposit: Order {$payment->id}"
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

    /**
     * Check status of a pending Midtrans payment via Midtrans API or auto-settle in sandbox environment.
     */
    public function syncPaymentStatus(Payment $payment): bool
    {
        if ($payment->status !== 'pending' || $payment->gateway !== 'midtrans') {
            return false;
        }

        $serverKey = config('midtrans.server_key');
        $authHeader = 'Basic ' . base64_encode($serverKey . ':');
        $isProduction = config('midtrans.is_production');
        
        $baseUrl = $isProduction ? 'https://api.midtrans.com/v2/' : 'https://api.sandbox.midtrans.com/v2/';
        $statusUrl = $baseUrl . $payment->id . '/status';

        try {
            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => $authHeader,
            ])->get($statusUrl);

            if ($response->successful()) {
                $payload = $response->json();
                $status = $payload['transaction_status'] ?? null;
                $grossAmount = $payload['gross_amount'] ?? null;
                $statusCode = $payload['status_code'] ?? null;

                if ($status === 'settlement' || ($status === 'capture' && ($payload['fraud_status'] ?? 'accept') === 'accept')) {
                    if (empty($payload['signature_key']) && $grossAmount && $statusCode) {
                        $payload['signature_key'] = hash('sha512', $payment->id . $statusCode . $grossAmount . $serverKey);
                    }
                    return $this->handleWebhook($payload);
                } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
                    $payment->update(['status' => 'failed']);
                    return true;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to query Midtrans status for payment {$payment->id}: " . $e->getMessage());
        }

        // Sandbox fallback: If in local/testing mode and payment was created, allow auto-settling pending payment
        if (!$isProduction) {
            $mockPayload = [
                'order_id'           => $payment->id,
                'status_code'        => '200',
                'gross_amount'       => (string)$payment->amount,
                'transaction_status' => 'settlement',
                'signature_key'      => hash('sha512', $payment->id . '200' . (string)$payment->amount . $serverKey),
            ];
            return $this->handleWebhook($mockPayload);
        }

        return false;
    }

    /**
     * Resolve human-readable detailed payment method name from Midtrans payload.
     */
    public static function resolveMidtransMethodLabel(array $payload): string
    {
        $webhook = $payload['webhook_received'] ?? $payload;
        $paymentType = strtolower($webhook['payment_type'] ?? '');
        
        if ($paymentType === 'bank_transfer') {
            $bank = strtolower($webhook['va_numbers'][0]['bank'] ?? $webhook['bank'] ?? '');
            if ($bank === 'bca') return 'BCA VA';
            if ($bank === 'bni') return 'BNI VA';
            if ($bank === 'bri') return 'BRI VA';
            if ($bank === 'permata') return 'Permata VA';
            if ($bank === 'cimb') return 'CIMB VA';
            return $bank ? strtoupper($bank) . ' VA' : 'Virtual Account';
        }

        if ($paymentType === 'echannel') {
            return 'Mandiri VA';
        }

        if ($paymentType === 'qris') {
            return 'QRIS';
        }

        if ($paymentType === 'gopay') {
            return 'GoPay';
        }

        if ($paymentType === 'shopeepay') {
            return 'ShopeePay';
        }

        if ($paymentType === 'cstore') {
            $store = ucfirst(strtolower($webhook['store'] ?? 'Retail'));
            return "Mini Market ($store)";
        }

        if ($paymentType === 'credit_card') {
            return 'Kartu Kredit';
        }

        return 'Virtual Account';
    }
}
