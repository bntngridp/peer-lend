<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoanInstallment;
use App\Modules\Loan\Services\RepaymentService;
use Illuminate\Contracts\View\View;
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

    /**
     * View official payment receipt / nota pembayaran for a paid installment.
     */
    public function receipt(LoanInstallment $installment): View
    {
        $loan = $installment->loan;
        $user = Auth::user();

        // Security check: Borrower, Lender, or Staff can view receipt
        $isBorrower = $loan->borrower_id === $user->id;
        $isLender   = $loan->fundings()->where('lender_id', $user->id)->exists();
        $isAdmin    = $user->hasAnyRole(['admin', 'customer_service', 'collection_officer']);

        if (!$isBorrower && !$isLender && !$isAdmin) {
            abort(403, 'You do not have permission to view this payment receipt.');
        }

        if (!$installment->isPaid()) {
            abort(404, 'Payment receipt is only available for paid installments.');
        }

        $repayment = \App\Models\LoanRepayment::where('installment_id', $installment->id)->latest()->first();

        return view('loans.receipt', compact('installment', 'loan', 'repayment'));
    }
}
