<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\LoanRequest as Loan;
use Illuminate\Http\Request;

class CollateralDashboardController extends Controller
{
    public function index(Request $request)
    {
        $cryptoLoans = Loan::with(['borrower.profile', 'collateralCurrency', 'currency'])
            ->whereNotNull('collateral_currency_id')
            ->orderBy('created_at', 'desc')
            ->get();

        $cryptoCurrencies = Currency::where('type', 'crypto')->get();

        // Calculate summary metrics
        $totalPledgedValue = $cryptoLoans->sum('amount');
        $weightedAvgLtv    = $cryptoLoans->count() > 0 ? $cryptoLoans->avg('current_ltv') : 0;
        $atRiskCount       = $cryptoLoans->filter(fn ($l) => $l->current_ltv >= 75.0)->count();
        $warningCount      = $cryptoLoans->filter(fn ($l) => $l->current_ltv >= 60.0 && $l->current_ltv < 75.0)->count();
        $healthyCount      = $cryptoLoans->filter(fn ($l) => $l->current_ltv < 60.0)->count();

        return view('collateral.index', compact(
            'cryptoLoans',
            'cryptoCurrencies',
            'totalPledgedValue',
            'weightedAvgLtv',
            'atRiskCount',
            'warningCount',
            'healthyCount'
        ));
    }
}
