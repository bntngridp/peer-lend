<?php

namespace App\Modules\Wallet\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Wallet\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService
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
}
