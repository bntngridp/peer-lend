<?php

namespace App\Modules\Wallet\Services;

use App\Models\Payment;
use App\Models\User;
use App\Modules\Shared\Services\AuditLogService;
use App\Modules\Shared\Services\NotificationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NowPaymentsService
{
    public function __construct(
        private readonly WalletService $walletService,
        private readonly AuditLogService $auditLogService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Create a NOWPayments Crypto Invoice for Deposit.
     */
    public function createInvoice(User $user, float $amount, string $payCurrency = 'usdttrc20'): array
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'nowpayments',
            'amount'  => $amount,
            'status'  => 'pending',
            'payload' => [
                'type'         => 'crypto_deposit',
                'pay_currency' => strtolower($payCurrency),
            ],
        ]);

        $apiKey = config('nowpayments.api_key');
        $baseUrl = config('nowpayments.base_url');

        $orderId = 'NP-DEP-' . $payment->id;

        $payload = [
            'price_amount'      => (float) $amount,
            'price_currency'    => 'usd',
            'pay_currency'      => strtolower($payCurrency),
            'ipn_callback_url'  => route('payment.nowpayments.ipn'),
            'order_id'          => $orderId,
            'order_description' => 'LendFlow Crypto Deposit #' . substr($payment->id, 0, 8),
            'success_url'       => route('wallet.index', ['status' => 'success']),
            'cancel_url'        => route('wallet.index', ['status' => 'cancelled']),
        ];

        // Mock fallback if using placeholder keys for seamless dev testing
        if (str_contains($apiKey, 'PLACEHOLDER')) {
            Log::info("NOWPayments Mock Invoice created for Payment #{$payment->id}");

            $mockToken = 'np_inv_' . bin2hex(random_bytes(8));
            $mockInvoiceUrl = "https://nowpayments.io/payment/?iid={$mockToken}";

            $payment->update([
                'gateway_ref_id' => $mockToken,
                'payload'        => array_merge($payment->payload ?? [], [
                    'invoice_id'   => $mockToken,
                    'invoice_url'  => $mockInvoiceUrl,
                    'order_id'     => $orderId,
                ]),
            ]);

            return [
                'payment_id'   => $payment->id,
                'invoice_id'   => $mockToken,
                'invoice_url'  => $mockInvoiceUrl,
                'pay_currency' => strtoupper($payCurrency),
                'amount'       => $amount,
            ];
        }

        // Real API Call to NOWPayments
        try {
            $response = Http::withHeaders([
                'x-api-key'    => $apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/invoice", $payload);

            if (!$response->successful()) {
                Log::error('NOWPayments Invoice API call failed: ' . $response->body());
                $payment->update([
                    'status'  => 'failed',
                    'payload' => array_merge($payment->payload ?? [], ['error' => $response->body()]),
                ]);

                throw new \Exception('NOWPayments Gateway Error: ' . ($response->json()['message'] ?? 'Failed to generate crypto invoice.'));
            }

            $responseData = $response->json();
            $invoiceId = (string) ($responseData['id'] ?? $responseData['invoice_id'] ?? '');
            $invoiceUrl = $responseData['invoice_url'] ?? '';

            $payment->update([
                'gateway_ref_id' => $invoiceId,
                'payload'        => array_merge($payment->payload ?? [], $responseData),
            ]);

            return [
                'payment_id'   => $payment->id,
                'invoice_id'   => $invoiceId,
                'invoice_url'  => $invoiceUrl,
                'pay_currency' => strtoupper($payCurrency),
                'amount'       => $amount,
            ];

        } catch (\Throwable $e) {
            Log::error('NOWPayments Exception: ' . $e->getMessage());
            $payment->update([
                'status'  => 'failed',
                'payload' => array_merge($payment->payload ?? [], ['error' => $e->getMessage()]),
            ]);

            throw $e;
        }
    }

    /**
     * Create an Instant NOWPayments Crypto Payout / Withdrawal.
     */
    public function createPayout(User $user, float $amount, string $address, string $currency = 'usdttrc20'): array
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'gateway' => 'nowpayments',
            'amount'  => $amount,
            'status'  => 'pending',
            'payload' => [
                'type'           => 'crypto_withdrawal',
                'crypto_address' => $address,
                'pay_currency'   => strtolower($currency),
            ],
        ]);

        $apiKey = config('nowpayments.api_key');
        $baseUrl = config('nowpayments.base_url');

        $payoutId = 'NP-WD-' . $payment->id;

        // Mock fallback if using placeholder keys for dev testing
        if (str_contains($apiKey, 'PLACEHOLDER')) {
            Log::info("NOWPayments Mock Payout executed for Payment #{$payment->id}");

            $mockPayoutRef = 'np_po_' . bin2hex(random_bytes(8));

            $payment->update([
                'status'         => 'completed',
                'gateway_ref_id' => $mockPayoutRef,
                'payload'        => array_merge($payment->payload ?? [], [
                    'payout_id' => $mockPayoutRef,
                    'status'    => 'FINISHED',
                ]),
            ]);

            // Deduct user wallet
            $this->walletService->withdraw(
                user: $user,
                currencyCode: 'USD',
                amount: $amount,
                paymentGatewayRefId: $mockPayoutRef
            );

            // Send Notification
            $this->notificationService->send(
                userId: $user->id,
                title: 'Penarikan Kripto Berhasil / Crypto Payout Completed',
                message: "Penarikan sebesar {$amount} " . strtoupper($currency) . " ke address {$address} telah sukses diproses via NOWPayments."
            );

            return [
                'payment_id' => $payment->id,
                'payout_id'  => $mockPayoutRef,
                'status'     => 'FINISHED',
            ];
        }

        // Real NOWPayments Payout API Call
        try {
            $payload = [
                'withdrawals' => [
                    [
                        'address'      => $address,
                        'currency'     => strtolower($currency),
                        'amount'       => (float) $amount,
                        'ipn_callback_url' => route('payment.nowpayments.ipn'),
                        'extra_id'     => $payoutId,
                    ],
                ],
            ];

            $response = Http::withHeaders([
                'x-api-key'    => $apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$baseUrl}/payout", $payload);

            if (!$response->successful()) {
                Log::error('NOWPayments Payout API call failed: ' . $response->body());
                $payment->update([
                    'status'  => 'failed',
                    'payload' => array_merge($payment->payload ?? [], ['error' => $response->body()]),
                ]);

                throw new \Exception('NOWPayments Payout Error: ' . ($response->json()['message'] ?? 'Failed to execute crypto payout.'));
            }

            $responseData = $response->json();
            $refId = (string) ($responseData['id'] ?? $responseData['withdrawals'][0]['id'] ?? $payoutId);

            $payment->update([
                'gateway_ref_id' => $refId,
                'payload'        => array_merge($payment->payload ?? [], $responseData),
            ]);

            return [
                'payment_id' => $payment->id,
                'payout_id'  => $refId,
                'status'     => 'PROCESSING',
            ];

        } catch (\Throwable $e) {
            Log::error('NOWPayments Payout Exception: ' . $e->getMessage());
            $payment->update([
                'status'  => 'failed',
                'payload' => array_merge($payment->payload ?? [], ['error' => $e->getMessage()]),
            ]);

            throw $e;
        }
    }

    /**
     * Process NOWPayments Instant Payment Notification (IPN) Webhook.
     */
    public function handleIpnCallback(array $payload, ?string $signature = null): bool
    {
        Log::info('NOWPayments IPN Received:', $payload);

        $orderId = $payload['order_id'] ?? null;
        $paymentStatus = strtolower($payload['payment_status'] ?? $payload['status'] ?? '');
        $invoiceId = (string) ($payload['payment_id'] ?? $payload['invoice_id'] ?? '');

        // Locate Payment record
        $payment = null;
        if ($orderId) {
            $cleanId = str_replace(['NP-DEP-', 'NP-WD-'], '', $orderId);
            $payment = Payment::find($cleanId);
        }

        if (!$payment && $invoiceId) {
            $payment = Payment::where('gateway_ref_id', $invoiceId)->first();
        }

        if (!$payment) {
            Log::warning('NOWPayments IPN: Associated Payment record not found.');
            return false;
        }

        $payment->update([
            'gateway_ref_id' => $invoiceId ?: $payment->gateway_ref_id,
            'payload'        => array_merge($payment->payload ?? [], $payload),
        ]);

        // Process successful payment / deposit
        if (in_array($paymentStatus, ['finished', 'confirmed']) && $payment->status !== 'completed') {
            $payment->update(['status' => 'completed']);
            $user = $payment->user;

            if ($user) {
                $type = $payment->payload['type'] ?? 'crypto_deposit';

                if ($type === 'crypto_deposit') {
                    $this->walletService->deposit($user, 'USD', (float) $payment->amount, $invoiceId);

                    $this->notificationService->send(
                        userId: $user->id,
                        title: 'Deposit Kripto Dikonfirmasi / Crypto Deposit Confirmed',
                        message: "Deposit sebesar $" . number_format($payment->amount, 2) . " via NOWPayments telah berhasil dikreditkan ke dompet Anda."
                    );
                }
            }

            return true;
        }

        if (in_array($paymentStatus, ['failed', 'expired', 'refunded'])) {
            $payment->update(['status' => 'failed']);
            return true;
        }

        return true;
    }

    /**
     * Verify NOWPayments HMAC-SHA512 IPN signature.
     */
    public function verifyIpnSignature(array $payload, string $signature): bool
    {
        $ipnSecret = config('nowpayments.ipn_secret');
        if (empty($ipnSecret) || str_contains($ipnSecret, 'PLACEHOLDER')) {
            return true;
        }

        ksort($payload);
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $computedSignature = hash_hmac('sha512', $jsonPayload, $ipnSecret);

        return hash_equals($computedSignature, strtolower($signature));
    }
}
