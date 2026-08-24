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
    public function index(Request $request): View
    {
        $query = LoanRequest::with(['borrower.profile', 'category', 'currency'])
            ->openFunding();

        if ($request->filled('risk_grade')) {
            $query->where('risk_grade', $request->risk_grade);
        }

        if ($request->filled('term')) {
            $query->where('duration', $request->term);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%{$search}%")
                  ->orWhereHas('borrower.profile', function ($qp) use ($search) {
                      $qp->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->get('sort') === 'interest_desc') {
            $query->orderBy('interest_rate', 'desc');
        } elseif ($request->get('sort') === 'amount_desc') {
            $query->orderBy('amount', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $loans = $query->paginate(15)->withQueryString();

        return view('marketplace.index', compact('loans'));
    }

    /**
     * Show detail investor preview for a specific loan listing.
     */
    public function show(LoanRequest $loan): View
    {
        $loan->load(['borrower.profile', 'category', 'currency', 'collateralCurrency', 'fundings.lender.profile']);
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
