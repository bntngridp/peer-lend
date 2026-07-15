<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoanInstallment;
use App\Modules\Loan\Services\RepaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RepaymentController extends Controller
{
    public function __construct(
        private readonly RepaymentService $repaymentService
    ) {}

    /**
     * Pay a specific loan installment.
     */
    public function pay(LoanInstallment $installment): RedirectResponse
    {
        $loan = $installment->loan;

        // Security check: Only the loan borrower can pay the installment
        if ($loan->borrower_id !== Auth::id()) {
            abort(403, 'You do not have permission to pay this installment.');
        }

        try {
            $this->repaymentService->payInstallment(Auth::user(), $installment);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', "Installment #{$installment->installment_number} paid successfully!");
    }
}
