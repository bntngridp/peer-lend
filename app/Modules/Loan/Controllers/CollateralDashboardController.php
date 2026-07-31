<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoanRequest as Loan;
use App\Models\Currency;
use Illuminate\Http\Request;

class CollateralDashboardController extends Controller
{
    public function index(Request $request)
    {
        $cryptoLoans = Loan::with(['borrower.profile', 'collateralCurrency'])
            ->whereNotNull('collateral_currency_id')
            ->get();

        $cryptoCurrencies = Currency::where('type', 'crypto')->get();

        return view('collateral.index', compact('cryptoLoans', 'cryptoCurrencies'));
    }
}
