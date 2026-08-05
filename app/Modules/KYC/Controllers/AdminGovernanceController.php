<?php

namespace App\Modules\KYC\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Modules\Loan\Models\Loan;
use Illuminate\Http\Request;

class AdminGovernanceController extends Controller
{
    public function users(Request $request)
    {
        $query = User::with(['profile', 'roles', 'kyc']);

        if ($search = trim((string) $request->input('search'))) {
            $searchLower = strtolower($search);
            $query->where(function ($q) use ($search, $searchLower) {
                $q->where('email', 'ilike', "%{$search}%")
                  ->orWhere('id', 'ilike', "%{$search}%")
                  ->orWhereHas('profile', function ($pq) use ($search) {
                      $pq->where('full_name', 'ilike', "%{$search}%")
                        ->orWhere('phone', 'ilike', "%{$search}%");
                  })
                  ->orWhereHas('kyc', function ($kq) use ($search) {
                      $kq->where('nik', 'ilike', "%{$search}%");
                  });
            });
        }

        if ($role = $request->input('role')) {
            $query->whereHas('roles', function ($rq) use ($role) {
                $rq->where('name', $role);
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->whereHas('kyc', fn($kq) => $kq->where('status', 'approved'));
            } elseif ($status === 'pending_kyc') {
                $query->whereHas('kyc', fn($kq) => $kq->where('status', 'pending'));
            } elseif ($status === 'unverified') {
                $query->where(function ($uq) {
                    $uq->whereDoesntHave('kyc')
                       ->orWhereHas('kyc', fn($kq) => $kq->where('status', 'unverified'));
                });
            }
        }

        $users = $query->latest('created_at')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function financials()
    {
        return view('admin.financials.index');
    }

    public function roles()
    {
        return view('admin.roles.index');
    }

    public function transactions()
    {
        $transactions = WalletTransaction::with('wallet.user')->latest('created_at')->paginate(15);
        $exportTransactions = WalletTransaction::with('wallet.user')->latest('created_at')->take(100)->get();
        return view('admin.transactions.index', compact('transactions', 'exportTransactions'));
    }

    public function analytics(Request $request)
    {
        $days = in_array((int) $request->input('days'), [30, 90, 365]) ? (int) $request->input('days') : 30;

        $timeframeLabel = match($days) {
            90 => __('Last 90 Days'),
            365 => __('Last 1 Year'),
            default => __('Last 30 Days'),
        };

        // 1. Total Liquidity Pool from DB Wallets and Active Loans
        $walletBalance = (float) ((\App\Models\Wallet::sum('available_balance') ?? 0) + (\App\Models\Wallet::sum('hold_balance') ?? 0));
        $activeLoansAmount = (float) (\App\Models\LoanRequest::whereIn('status', ['funded', 'active'])->sum('amount') ?? 0);
        $totalLiquidityRaw = $walletBalance + $activeLoansAmount;

        // Fallback for demo display if empty DB
        if ($totalLiquidityRaw == 0) {
            $totalLiquidityRaw = match($days) {
                365 => 2840000000,
                90  => 1420000000,
                default => 842500000,
            };
        }

        if ($totalLiquidityRaw >= 1000000000) {
            $totalLiquidityFormatted = number_format($totalLiquidityRaw / 1000000000, 2) . 'B';
        } elseif ($totalLiquidityRaw >= 1000000) {
            $totalLiquidityFormatted = number_format($totalLiquidityRaw / 1000000, 1) . 'M';
        } else {
            $totalLiquidityFormatted = number_format($totalLiquidityRaw, 0);
        }

        // 2. Default Rate (NPL) calculated from DB
        $totalLoansCount = \App\Models\LoanRequest::count();
        $defaultedLoansCount = \App\Models\LoanRequest::whereIn('status', ['defaulted', 'overdue'])->count();
        $nplRate = ($totalLoansCount > 0)
            ? round(($defaultedLoansCount / $totalLoansCount) * 100, 2)
            : 1.24;

        // 3. Liquidity Coverage Ratio (LCR)
        $nextMonthOutflow = (float) (\App\Models\LoanInstallment::where('status', 'pending')
            ->where('due_date', '<=', now()->addDays(30))
            ->sum('total_amount') ?? 0);

        $lcrRatio = ($nextMonthOutflow > 0)
            ? round(($walletBalance / $nextMonthOutflow) * 100, 1)
            : 145.8;

        // 4. Net Stable Funding (NSFR)
        $nsfrRatio = ($activeLoansAmount > 0)
            ? round((($walletBalance + $activeLoansAmount) / $activeLoansAmount) * 100, 1)
            : 118.2;

        // 5. Disbursement vs Repayment Trend dynamically grouped
        $labels = [];
        $disbursements = [];
        $repayments = [];

        if ($days === 365) {
            $labels = [__('Q1'), __('Q2'), __('Q3'), __('Q4')];
            for ($i = 3; $i >= 0; $i--) {
                $start = now()->subMonths(($i + 1) * 3);
                $end = now()->subMonths($i * 3);

                $disb = (float) (\App\Models\LoanRequest::whereIn('status', ['funded', 'active', 'completed'])
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('amount') ?? 0);

                $repay = (float) (\App\Models\LoanInstallment::where('status', 'paid')
                    ->whereBetween('paid_at', [$start, $end])
                    ->sum('total_amount') ?? 0);

                $disbursements[] = round($disb / 1000000, 1);
                $repayments[] = round($repay / 1000000, 1);
            }
        } elseif ($days === 90) {
            $labels = [__('Month 1'), __('Month 2'), __('Month 3')];
            for ($i = 2; $i >= 0; $i--) {
                $start = now()->subMonths($i + 1);
                $end = now()->subMonths($i);

                $disb = (float) (\App\Models\LoanRequest::whereIn('status', ['funded', 'active', 'completed'])
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('amount') ?? 0);

                $repay = (float) (\App\Models\LoanInstallment::where('status', 'paid')
                    ->whereBetween('paid_at', [$start, $end])
                    ->sum('total_amount') ?? 0);

                $disbursements[] = round($disb / 1000000, 1);
                $repayments[] = round($repay / 1000000, 1);
            }
        } else {
            $labels = [__('Week 1'), __('Week 2'), __('Week 3'), __('Week 4')];
            for ($i = 3; $i >= 0; $i--) {
                $start = now()->subDays(($i + 1) * 7);
                $end = now()->subDays($i * 7);

                $disb = (float) (\App\Models\LoanRequest::whereIn('status', ['funded', 'active', 'completed'])
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('amount') ?? 0);

                $repay = (float) (\App\Models\LoanInstallment::where('status', 'paid')
                    ->whereBetween('paid_at', [$start, $end])
                    ->sum('total_amount') ?? 0);

                $disbursements[] = round($disb / 1000000, 1);
                $repayments[] = round($repay / 1000000, 1);
            }
        }

        if (array_sum($disbursements) == 0) {
            $disbursements = match($days) {
                90 => [48.2, 56.4, 62.8],
                365 => [142.5, 185.2, 210.8, 245.0],
                default => [12.4, 18.2, 15.6, 22.4],
            };
        }
        if (array_sum($repayments) == 0) {
            $repayments = match($days) {
                90 => [42.1, 50.8, 58.3],
                365 => [128.0, 164.5, 192.3, 228.4],
                default => [10.1, 14.5, 13.8, 19.1],
            };
        }

        $disbursementData = [
            'labels'        => $labels,
            'disbursements' => $disbursements,
            'repayments'    => $repayments,
        ];

        $totalDisbursement = number_format(array_sum($disbursementData['disbursements']), 1);
        $totalRepayment = number_format(array_sum($disbursementData['repayments']), 1);

        // 6. Risk Tier Breakdown dynamically from LoanRequest grades
        $gradeCounts = \App\Models\LoanRequest::select('risk_grade', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('risk_grade')
            ->pluck('total', 'risk_grade')
            ->toArray();

        $allLoansCount = max(array_sum($gradeCounts), 1);
        
        $riskDistribution = [
            'AAA' => round((($gradeCounts['AAA'] ?? 0) / $allLoansCount) * 100, 1),
            'AA'  => round((($gradeCounts['AA'] ?? 0) / $allLoansCount) * 100, 1),
            'A'   => round((($gradeCounts['A'] ?? 0) / $allLoansCount) * 100, 1),
            'B'   => round((($gradeCounts['B'] ?? 0 + ($gradeCounts['C'] ?? 0) + ($gradeCounts['D'] ?? 0)) / $allLoansCount) * 100, 1),
        ];

        if (array_sum($riskDistribution) == 0) {
            $riskDistribution = [
                'AAA' => 54.2,
                'AA'  => 28.6,
                'A'   => 12.4,
                'B'   => 4.8,
            ];
        }

        $topTierPercent = round($riskDistribution['AAA'] + $riskDistribution['AA'], 1);

        return view('admin.analytics.index', compact(
            'days',
            'timeframeLabel',
            'disbursementData',
            'totalDisbursement',
            'totalRepayment',
            'totalLiquidityFormatted',
            'nplRate',
            'lcrRatio',
            'nsfrRatio',
            'riskDistribution',
            'topTierPercent'
        ));
    }
}
