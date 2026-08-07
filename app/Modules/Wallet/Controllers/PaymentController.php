<?php

namespace App\Modules\Wallet\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Wallet\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Modules\Wallet\Services\XenditService;
use App\Modules\Wallet\Services\NowPaymentsService;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly XenditService $xenditService,
        private readonly NowPaymentsService $nowPaymentsService
    ) {}

    /**
     * Initiate deposit and return Midtrans Snap token.
     * 
     * POST /wallet/deposit/initiate
     */
    public function initiateDeposit(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:10000', 'max:100000000'], // Min Rp 10k, Max Rp 100jt
        ]);

        try {
            $data = $this->paymentService->initiateDeposit(
                Auth::user(),
                (float) $request->amount
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Deposit initiated successfully.',
                'data'    => $data,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Initiate Crypto Deposit via NOWPayments.
     * 
     * POST /wallet/crypto/deposit/initiate
     */
    public function initiateCryptoDeposit(Request $request): JsonResponse
    {
        $request->validate([
            'amount'       => ['required', 'numeric', 'min:5', 'max:100000'],
            'pay_currency' => ['required', 'string', 'max:20'],
        ]);

        try {
            $data = $this->nowPaymentsService->createInvoice(
                user: Auth::user(),
                amount: (float) $request->amount,
                payCurrency: $request->pay_currency
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Crypto deposit invoice created via NOWPayments.',
                'data'    => $data,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Initiate automated withdrawal via Xendit Payout API.
     * 
     * POST /wallet/withdraw/initiate
     */
    public function initiateWithdrawal(Request $request): JsonResponse
    {
        $request->validate([
            'amount'              => ['required', 'numeric', 'min:50000', 'max:100000000'],
            'bank_code'           => ['required', 'string', 'max:20'],
            'account_number'      => ['required', 'string', 'max:30'],
            'account_holder_name' => ['required', 'string', 'max:100'],
        ]);

        try {
            $data = $this->xenditService->createDisbursement(
                user: Auth::user(),
                amount: (float) $request->amount,
                bankCode: $request->bank_code,
                accountNumber: $request->account_number,
                accountHolderName: $request->account_holder_name
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Penarikan dana via Xendit berhasil diajukan.',
                'data'    => $data,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Initiate Instant Crypto Withdrawal via NOWPayments.
     * 
     * POST /wallet/crypto/withdraw/initiate
     */
    public function initiateCryptoWithdrawal(Request $request): JsonResponse
    {
        $request->validate([
            'amount'   => ['required', 'numeric', 'min:10', 'max:100000'],
            'address'  => ['required', 'string', 'max:120'],
            'currency' => ['required', 'string', 'max:20'],
        ]);

        try {
            $data = $this->nowPaymentsService->createPayout(
                user: Auth::user(),
                amount: (float) $request->amount,
                address: $request->address,
                currency: $request->currency
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Crypto payout initiated via NOWPayments.',
                'data'    => $data,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Process Midtrans webhook notification callback.
     * 
     * POST /api/payment/webhook
     */
    public function webhook(Request $request): JsonResponse
    {
        $processed = $this->paymentService->handleWebhook($request->all());

        if (!$processed) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Webhook verification failed or could not be processed.',
            ], 400);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Webhook processed successfully.',
        ]);
    }

    /**
     * Process Xendit Payout webhook notification callback.
     * 
     * POST /api/payment/xendit/webhook
     */
    public function xenditWebhook(Request $request): JsonResponse
    {
        $processed = $this->xenditService->handleWebhook($request->all());

        if (!$processed) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Xendit Webhook processing failed.',
            ], 400);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Xendit Webhook processed successfully.',
        ]);
    }

    /**
     * Process NOWPayments IPN webhook notification callback.
     * 
     * POST /api/payment/nowpayments/ipn
     */
    public function nowpaymentsIpn(Request $request): JsonResponse
    {
        $signature = $request->header('x-nowpayments-sig');
        
        $valid = $this->nowPaymentsService->verifyIpnSignature($request->all(), $signature ?? '');
        if (!$valid) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid IPN HMAC signature.',
            ], 400);
        }

        $processed = $this->nowPaymentsService->handleIpnCallback($request->all(), $signature);

        if (!$processed) {
            return response()->json([
                'status'  => 'error',
                'message' => 'NOWPayments IPN processing failed.',
            ], 400);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'NOWPayments IPN processed successfully.',
        ]);
    }

    /**
     * Confirm/Sync deposit status from frontend onSuccess JS callback or manual check.
     * 
     * POST /wallet/deposit/confirm
     */
    public function confirmDeposit(Request $request): JsonResponse
    {
        $request->validate([
            'payment_id' => ['nullable', 'string'],
            'order_id'   => ['nullable', 'string'],
        ]);

        $paymentId = $request->payment_id ?? $request->order_id;
        $user = Auth::user();

        if ($paymentId) {
            $payment = \App\Models\Payment::where('id', $paymentId)
                ->where('user_id', $user->id)
                ->first();

            if ($payment) {
                $this->paymentService->syncPaymentStatus($payment);
            }
        } else {
            $pendingPayments = \App\Models\Payment::where('user_id', $user->id)
                ->where('status', 'pending')
                ->get();

            foreach ($pendingPayments as $p) {
                $this->paymentService->syncPaymentStatus($p);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Deposit status checked and synced.',
        ]);
    }
}
