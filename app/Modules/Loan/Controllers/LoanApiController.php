<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoanCategory;
use App\Models\LoanRequest;
use App\Models\Setting;
use App\Modules\Loan\Services\LoanRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoanApiController extends Controller
{
    public function __construct(
        private readonly LoanRequestService $loanRequestService
    ) {}
    /**
     * Apply for a new loan via REST API.
     * Reuses LoanRequestService to ensure identical credit scoring & business logic.
     *
     * POST /api/v1/loans/apply
     */
    public function apply(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Guard: Only borrowers with approved KYC can apply
        if (!$user->kyc || $user->kyc->status !== 'approved') {
            return response()->json([
                'status'  => 'error',
                'message' => 'KYC verification is required before applying for a loan.',
                'errors'  => [['field' => 'kyc', 'message' => 'KYC not approved']],
            ], 403);
        }

        // Validate input (mirrors CreateLoanRequest form request)
        $minAmount = (int) Setting::getVal('min_loan_amount', 1000000);
        $maxAmount = (int) Setting::getVal('max_loan_amount', 500000000);

        try {
            $validated = $request->validate([
                'category_id'            => ['required', 'exists:loan_categories,id'],
                'amount'                 => ['required', 'numeric', "min:{$minAmount}", "max:{$maxAmount}"],
                'duration'               => ['required', 'integer', 'in:3,6,12,24'],
                'purpose'                => ['required', 'string', 'max:255'],
                'collateral_currency_id' => ['nullable', 'exists:currencies,id'],
                'description'            => ['nullable', 'string', 'max:5000'],
            ]);
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $msg) {
                    $errors[] = ['field' => $field, 'message' => $msg];
                }
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed.',
                'errors'  => $errors,
            ], 422);
        }

        // Delegate to service layer (credit scoring, collateral LTV, etc.)
        $loan = $this->loanRequestService->createLoanRequest($user, $validated);
        $loan->load(['category', 'currency']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Loan application submitted successfully. Awaiting admin review.',
            'data'    => [
                'id'            => $loan->id,
                'amount'        => (float) $loan->amount,
                'duration'      => $loan->duration,
                'interest_rate' => (float) $loan->interest_rate,
                'risk_grade'    => $loan->risk_grade,
                'status'        => $loan->status,
                'purpose'       => $loan->purpose,
                'category'      => $loan->category->name,
                'currency'      => $loan->currency->code,
                'created_at'    => $loan->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Get list of open marketplace loans (Paginated JSON).
     */
    public function index(Request $request): JsonResponse
    {
        $limit = $request->query('per_page', 10);
        if ($limit > 50) {
            $limit = 50; // Cap to prevent abuse
        }

        $loans = LoanRequest::with(['borrower.profile', 'category', 'currency'])
            ->where('status', LoanRequest::STATUS_OPEN_FUNDING)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'status'  => 'success',
            'message' => 'Marketplace loans retrieved successfully',
            'data'    => $loans->items(),
            'meta'    => [
                'current_page' => $loans->currentPage(),
                'per_page'     => $loans->perPage(),
                'total'        => $loans->total(),
                'total_pages'  => $loans->lastPage(),
            ]
        ]);
    }

    /**
     * Get detail of a specific loan request (JSON).
     */
    public function show(LoanRequest $loan): JsonResponse
    {
        // Load relationships
        $loan->load(['borrower.profile', 'category', 'currency', 'fundings.lender.profile']);

        return response()->json([
            'status'  => 'success',
            'message' => 'Loan details retrieved successfully',
            'data'    => [
                'id'                 => $loan->id,
                'amount'             => (float) $loan->amount,
                'interest_rate'      => (float) $loan->interest_rate,
                'duration'           => $loan->duration,
                'purpose'            => $loan->purpose,
                'risk_grade'         => $loan->risk_grade,
                'status'             => $loan->status,
                'funded_percentage'  => (float) $loan->funded_percentage,
                'collateral_amount'  => (float) $loan->collateral_amount,
                'borrower' => [
                    'name'  => $loan->borrower->profile->full_name ?? $loan->borrower->email,
                    'email' => $loan->borrower->email,
                ],
                'category' => $loan->category->name,
                'currency' => $loan->currency->code,
                'fundings' => $loan->fundings->map(function ($fund) {
                    return [
                        'lender_name' => $fund->lender->profile->full_name ?? $fund->lender->email,
                        'amount'      => (float) $fund->amount,
                        'funded_at'   => $fund->created_at->toIso8601String(),
                    ];
                })
            ]
        ]);
    }
}
