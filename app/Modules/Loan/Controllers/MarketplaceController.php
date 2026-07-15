<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest;
use App\Modules\Loan\Services\LoanFundingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function __construct(
        private readonly LoanFundingService $loanFundingService
    ) {}

    /**
     * List all public loan listings open for investor funding.
     */
    public function index(): View
    {
        $loans = LoanRequest::with(['borrower.profile', 'category', 'currency'])
            ->openFunding()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('marketplace.index', compact('loans'));
    }

    /**
     * Show detail investor preview for a specific loan listing.
     */
    public function show(LoanRequest $loan): View
    {
        $loan->load(['borrower.profile', 'category', 'currency', 'collateralCurrency']);
        return view('marketplace.show', compact('loan'));
    }

    /**
     * Fund a portion of a loan.
     */
    public function fund(Request $request, LoanRequest $loan): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:100000', 'max:500000000'], // Min investment Rp 100k
        ]);

        // Security check: Lenders cannot fund their own loan requests
        if ($loan->borrower_id === Auth::id()) {
            return back()->with('error', 'You cannot fund your own loan request application.');
        }

        try {
            $this->loanFundingService->fundLoan(
                Auth::user(),
                $loan,
                $request->amount
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('marketplace.show', $loan->id)
            ->with('success', 'Thank you for your investment! The funds are successfully held for this loan.');
    }
}
