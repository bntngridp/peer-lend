<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\InterestRate;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanCalculatorController extends Controller
{
    /**
     * Show the interactive loan calculator page.
     */
    public function index(): View
    {
        $interestRates = InterestRate::all()->keyBy('risk_grade');

        $minAmount = (int) Setting::getVal('min_loan_amount', 1000000);
        $maxAmount = (int) Setting::getVal('max_loan_amount', 500000000);

        return view('calculator.index', compact('interestRates', 'minAmount', 'maxAmount'));
    }

    /**
     * Calculate loan installment breakdown (AJAX endpoint).
     * Returns JSON with monthly installment, total payment, amortization schedule.
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'     => ['required', 'numeric', 'min:100000'],
            'duration'   => ['required', 'integer', 'in:3,6,12,24'],
            'risk_grade' => ['required', 'in:A,B,C,D'],
        ]);

        $amount    = (string) $validated['amount'];
        $duration  = (int)    $validated['duration'];
        $grade     = strtoupper($validated['risk_grade']);

        // Fetch interest rate range and take the midpoint
        $rateConfig = InterestRate::rangeForGrade($grade);
        if (!$rateConfig) {
            return response()->json(['error' => 'Interest rate configuration not found.'], 422);
        }

        $annualRate  = bcdiv(bcadd((string)$rateConfig->min_rate, (string)$rateConfig->max_rate, 4), '2', 4);
        $monthlyRate = bcdiv($annualRate, '1200', 10); // Convert annual % to monthly decimal

        // Monthly installment using flat-rate formula (simplified P2P lending):
        // M = (P × (r / (1 - (1 + r)^-n)))
        // We use flat interest for simplicity: total interest = P × annual_rate% × years
        $years         = bcdiv((string)$duration, '12', 6);
        $totalInterest = bcmul(bcmul($amount, bcdiv($annualRate, '100', 10), 4), $years, 4);
        $totalPayment  = bcadd($amount, $totalInterest, 4);
        $monthlyPayment = bcdiv($totalPayment, (string)$duration, 4);

        // Origination fee — 1% of loan amount (simulated)
        $originationFee = bcmul($amount, '0.01', 4);

        // Build amortization schedule (simplified flat rate)
        $schedule   = [];
        $remaining  = $amount;
        $monthlyPrincipal = bcdiv($amount, (string)$duration, 4);
        $monthlyInterestAmt = bcdiv($totalInterest, (string)$duration, 4);

        for ($month = 1; $month <= $duration; $month++) {
            if ($month === $duration) {
                $remaining = '0.00';
            } else {
                $remaining = bcsub($remaining, $monthlyPrincipal, 4);
            }

            $schedule[] = [
                'month'            => $month,
                'payment'          => $this->formatRupiah($monthlyPayment),
                'principal'        => $this->formatRupiah($monthlyPrincipal),
                'interest'         => $this->formatRupiah($monthlyInterestAmt),
                'remaining'        => $this->formatRupiah(max(0, (float)$remaining)),
            ];
        }

        return response()->json([
            'success'          => true,
            'grade'            => $grade,
            'annual_rate'      => $annualRate,
            'rate_range'       => "{$rateConfig->min_rate}% – {$rateConfig->max_rate}%",
            'monthly_payment'  => $this->formatRupiah($monthlyPayment),
            'total_payment'    => $this->formatRupiah($totalPayment),
            'total_interest'   => $this->formatRupiah($totalInterest),
            'origination_fee'  => $this->formatRupiah($originationFee),
            'amount'           => $this->formatRupiah($amount),
            'duration'         => $duration,
            'schedule'         => $schedule,
        ]);
    }

    /**
     * Format a numeric value as Indonesian Rupiah string.
     */
    private function formatRupiah(mixed $value): string
    {
        return 'Rp ' . number_format((float)$value, 0, ',', '.');
    }
}
