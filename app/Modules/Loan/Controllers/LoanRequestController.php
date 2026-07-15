<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\InterestRate;
use App\Models\LoanCategory;
use App\Models\LoanRequest;
use App\Modules\Loan\Requests\CreateLoanRequest;
use App\Modules\Loan\Services\LoanRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoanRequestController extends Controller
{
    public function __construct(
        private readonly LoanRequestService $loanRequestService
    ) {}

    /**
     * List all loans applied by the authenticated borrower.
     */
    public function index(): View
    {
        $loans = LoanRequest::with(['category', 'currency'])
            ->where('borrower_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('loans.index', compact('loans'));
    }

    /**
     * Show the apply loan form.
     */
    public function create(): View
    {
        $categories = LoanCategory::all();
        
        // Collateral options are only crypto assets
        $cryptoCurrencies = Currency::crypto()->active()->get();

        // Get interest rate limits to display on UI
        $interestRates = InterestRate::all();

        return view('loans.create', compact('categories', 'cryptoCurrencies', 'interestRates'));
    }

    /**
     * Store the submitted loan request in database.
     */
    public function store(CreateLoanRequest $request): RedirectResponse
    {
        $this->loanRequestService->createLoanRequest(
            Auth::user(),
            $request->validated()
        );

        return redirect()->route('loans.index')
            ->with('success', 'Your loan application has been submitted successfully and is awaiting review.');
    }

    /**
     * View amortization schedule and installments of a specific active loan.
     */
    public function installments(LoanRequest $loan): View
    {
        // Security check: Only the borrower can view their own loan's installments
        if ($loan->borrower_id !== Auth::id()) {
            abort(403, 'You do not have permission to view this loan schedule.');
        }

        $installments = $loan->installments;

        return view('loans.installments', compact('loan', 'installments'));
    }
}
