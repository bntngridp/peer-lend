<?php

namespace App\Modules\Wallet\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Wallet\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Modules\Wallet\Services\XenditService;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly XenditService $xenditService
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
}
