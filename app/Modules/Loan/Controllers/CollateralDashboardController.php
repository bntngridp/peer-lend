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

        // Calculate total pledged loan value
        $totalPledgedValue = $cryptoLoans->sum('amount');
        $weightedAvgLtv    = $cryptoLoans->count() > 0 ? $cryptoLoans->avg('current_ltv') : 0;

        // Group loans by collateral currency for Collateral Distribution card
        $collateralDistribution = [];
        if ($totalPledgedValue > 0) {
            foreach ($cryptoLoans->groupBy('collateral_currency_id') as $currencyId => $loans) {
                $currency = $loans->first()->collateralCurrency;
                $code = $currency?->code ?? 'OTHER';
                $name = $currency?->name ?? 'Digital Asset';
                $totalAmount = $loans->sum('amount');
                $totalLocked = $loans->sum('collateral_amount');
                $percentage  = round(($totalAmount / $totalPledgedValue) * 100, 1);

                $collateralDistribution[] = [
                    'code'          => $code,
                    'name'          => $name,
                    'total_amount'  => $totalAmount,
                    'total_locked'  => $totalLocked,
                    'percentage'    => $percentage,
                ];
            }
        }

        // Calculate dynamic risk tier amounts
        $atRiskCount      = $cryptoLoans->filter(fn ($l) => $l->current_ltv >= 75.0)->count();
        $warningCount     = $cryptoLoans->filter(fn ($l) => $l->current_ltv >= 60.0 && $l->current_ltv < 75.0)->count();
        $healthyCount     = $cryptoLoans->filter(fn ($l) => $l->current_ltv < 60.0)->count();

        $highRiskAmount   = $cryptoLoans->filter(fn ($l) => $l->current_ltv >= 75.0)->sum('amount');
        $mediumRiskAmount = $cryptoLoans->filter(fn ($l) => $l->current_ltv >= 60.0 && $l->current_ltv < 75.0)->sum('amount');
        $lowRiskAmount    = $cryptoLoans->filter(fn ($l) => $l->current_ltv < 60.0)->sum('amount');

        return view('collateral.index', compact(
            'cryptoLoans',
            'cryptoCurrencies',
            'totalPledgedValue',
            'weightedAvgLtv',
            'collateralDistribution',
            'atRiskCount',
            'warningCount',
            'healthyCount',
            'highRiskAmount',
            'mediumRiskAmount',
            'lowRiskAmount'
        ));
    }
}
