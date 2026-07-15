<?php

namespace App\Modules\Wallet\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Modules\Wallet\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService
    ) {}

    /**
     * View wallet balances and transaction history.
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Fetch all user wallets with loaded currency info
        $wallets = Wallet::with('currency')
            ->where('user_id', $user->id)
            ->get();

        // Fetch active currencies for deposit/withdraw dropdown selection
        $currencies = Currency::active()->get();

        // Fetch transaction history
        $transactions = WalletTransaction::whereHas('wallet', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('wallet.index', compact('wallets', 'currencies', 'transactions'));
    }

    /**
     * Deposit funds to available wallet balance.
     */
    public function deposit(Request $request): RedirectResponse
    {
        $request->validate([
            'currency_id' => ['required', 'exists:currencies,id'],
            'amount'      => ['required', 'numeric', 'min:10000', 'max:100000000'], // Rp 10k - 100jt limit
        ]);

        $this->walletService->deposit(
            Auth::user(),
            $request->currency_id,
            $request->amount,
            "Deposit of " . number_format($request->amount, 2)
        );

        return redirect()->route('wallet.index')
            ->with('success', 'Funds deposited successfully to available balance.');
    }

    /**
     * Withdraw funds from available wallet balance.
     */
    public function withdraw(Request $request): RedirectResponse
    {
        $request->validate([
            'currency_id' => ['required', 'exists:currencies,id'],
            'amount'      => ['required', 'numeric', 'min:10000', 'max:50000000'], // Rp 10k - 50jt limit
        ]);

        try {
            $this->walletService->withdraw(
                Auth::user(),
                $request->currency_id,
                $request->amount,
                "Withdrawal of " . number_format($request->amount, 2)
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('wallet.index')
            ->with('success', 'Funds withdrawn successfully.');
    }
}
