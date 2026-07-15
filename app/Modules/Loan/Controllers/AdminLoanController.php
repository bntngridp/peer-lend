<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Modules\Loan\Services\LoanRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminLoanController extends Controller
{
    public function __construct(
        private readonly LoanRequestService $loanRequestService
    ) {}

    /**
     * List all loan applications for administration review.
     */
    public function index(): View
    {
        $loans = LoanRequest::with(['borrower.profile', 'category'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.loans.index', compact('loans'));
    }

    /**
     * Show details of a specific loan request.
     */
    public function show(LoanRequest $loan): View
    {
        $loan->load(['borrower.profile', 'category', 'currency', 'collateralCurrency']);
        return view('admin.loans.show', compact('loan'));
    }

    /**
     * Approve a pending loan, moving it to open_funding status.
     */
    public function approve(LoanRequest $loan): RedirectResponse
    {
        try {
            $this->loanRequestService->approveLoanRequest($loan, Auth::user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('admin.loans.index')
            ->with('success', "Loan request for user {$loan->borrower->email} has been approved and is now open for funding.");
    }

    /**
     * Disburse a fully funded loan, transferring funds to borrower and creating schedule.
     */
    public function disburse(LoanRequest $loan, \App\Modules\Loan\Services\RepaymentService $repaymentService): RedirectResponse
    {
        try {
            $repaymentService->disburse($loan);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('admin.loans.index')
            ->with('success', "Loan #{$loan->id} has been successfully active and disbursed to the borrower.");
    }
}
