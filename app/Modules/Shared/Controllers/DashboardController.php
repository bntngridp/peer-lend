<?php

namespace App\Modules\Shared\Controllers;

use App\Models\LoanFunding;
use App\Models\LoanInstallment;
use App\Models\LoanRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show the dashboard view with role-appropriate statistics.
     */
    public function index(): View
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        // Determine if user acts as lender (has any loan fundings) or borrower
        $hasLenderActivity = LoanFunding::where('lender_id', $user->id)->exists();

        if ($hasLenderActivity) {
            return $this->lenderDashboard($user);
        }

        return $this->borrowerDashboard($user);
    }

    // ─── Admin Dashboard ──────────────────────────────────────────────────────

    private function adminDashboard(): View
    {
        $stats = [
            // User & KYC
            'total_users'          => User::count(),
            'kyc_pending'          => \App\Models\KYC::where('status', 'pending')->count(),
            'kyc_approved'         => \App\Models\KYC::where('status', 'approved')->count(),

            // Loan summary
            'loans_pending'        => LoanRequest::where('status', LoanRequest::STATUS_PENDING)->count(),
            'loans_active'         => LoanRequest::where('status', LoanRequest::STATUS_ACTIVE)->count(),
            'loans_completed'      => LoanRequest::where('status', LoanRequest::STATUS_COMPLETED)->count(),
            'loans_total'          => LoanRequest::count(),

            // Financial
            'total_disbursed'      => LoanRequest::where('status', LoanRequest::STATUS_ACTIVE)
                ->orWhere('status', LoanRequest::STATUS_COMPLETED)
                ->sum('amount'),

            'total_platform_fees'  => WalletTransaction::where('type', 'fee')->sum('amount'),

            // Installment health
            'installments_overdue' => LoanInstallment::where('status', LoanInstallment::STATUS_PENDING)
                ->where('due_date', '<', now())
                ->count(),

            // Recent loans for activity feed
            'recent_loans'         => LoanRequest::with(['borrower.profile', 'category'])
                ->latest()
                ->limit(8)
                ->get(),

            // Monthly loan chart data (last 6 months)
            'monthly_loans'        => $this->getMonthlyLoanData(),
        ];

        return view('dashboard', ['role' => 'admin', 'stats' => $stats]);
    }

    // ─── Borrower Dashboard ───────────────────────────────────────────────────

    private function borrowerDashboard(User $user): View
    {
        $wallet = Wallet::firstWhere('user_id', $user->id);

        $activeLoans = LoanRequest::where('borrower_id', $user->id)
            ->where('status', LoanRequest::STATUS_ACTIVE)
            ->with('installments')
            ->get();

        // Find the next upcoming installment across all active loans
        $nextInstallment = LoanInstallment::whereHas('loan', fn ($q) =>
            $q->where('borrower_id', $user->id)->where('status', LoanRequest::STATUS_ACTIVE)
        )
            ->where('status', LoanInstallment::STATUS_PENDING)
            ->orderBy('due_date')
            ->first();

        // Calculate charts data: paid installments vs unpaid installments
        $totalInstallmentsCount = LoanInstallment::whereHas('loan', fn ($q) =>
            $q->where('borrower_id', $user->id)
        )->count();
        $paidInstallmentsCount = LoanInstallment::whereHas('loan', fn ($q) =>
            $q->where('borrower_id', $user->id)
        )->where('status', 'paid')->count();

        $stats = [
            'wallet_balance'       => $wallet?->balance ?? '0.00',
            'active_loans_count'   => $activeLoans->count(),
            'total_borrowed'       => LoanRequest::where('borrower_id', $user->id)
                ->whereIn('status', [LoanRequest::STATUS_ACTIVE, LoanRequest::STATUS_COMPLETED])
                ->sum('amount'),
            'kyc_status'           => $user->kyc?->status ?? 'not_submitted',
            'next_installment'     => $nextInstallment,
            'active_loans'         => $activeLoans,
            'recent_transactions'  => WalletTransaction::where('wallet_id', $wallet?->id)
                ->latest()
                ->limit(5)
                ->get(),
            'chart_paid_count'     => $paidInstallmentsCount,
            'chart_unpaid_count'   => max(0, $totalInstallmentsCount - $paidInstallmentsCount),
        ];

        return view('dashboard', ['role' => 'borrower', 'stats' => $stats]);
    }

    // ─── Lender Dashboard ─────────────────────────────────────────────────────

    private function lenderDashboard(User $user): View
    {
        $wallet = Wallet::firstWhere('user_id', $user->id);

        $fundings = LoanFunding::where('lender_id', $user->id)
            ->with(['loan.borrower.profile', 'loan.category'])
            ->latest()
            ->get();

        // Calculate total interest earned from repayments to this lender
        $totalInterestEarned = WalletTransaction::where('wallet_id', $wallet?->id)
            ->where('type', 'repayment_interest')
            ->sum('amount');

        $totalPrincipalReturned = WalletTransaction::where('wallet_id', $wallet?->id)
            ->where('type', 'repayment_principal')
            ->sum('amount');

        // Fetch or create Lender Auto-Invest Rule config
        $autoInvestRule = \App\Models\AutoInvestRule::firstOrCreate(
            ['lender_id' => $user->id],
            [
                'is_active' => false,
                'min_grade' => 'D',
                'max_grade' => 'A',
                'max_allocation_per_loan' => 1000000.00,
                'max_ltv' => 80.00
            ]
        );

        // Calculate distribution of funded loans by risk grade
        $gradeDistribution = [
            'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0
        ];
        foreach ($fundings as $f) {
            $g = $f->loan?->risk_grade;
            if ($g && isset($gradeDistribution[$g])) {
                $gradeDistribution[$g] += (float) $f->amount;
            }
        }

        $stats = [
            'wallet_balance'          => $wallet?->balance ?? '0.00',
            'kyc_status'              => $user->kyc?->status ?? 'not_submitted',
            'total_invested'          => $fundings->sum('amount'),
            'active_investments'      => $fundings->filter(fn ($f) => in_array(
                $f->loan?->status,
                [LoanRequest::STATUS_OPEN_FUNDING, LoanRequest::STATUS_FUNDED, LoanRequest::STATUS_ACTIVE]
            ))->count(),
            'completed_investments'   => $fundings->filter(fn ($f) =>
                $f->loan?->status === LoanRequest::STATUS_COMPLETED
            )->count(),
            'total_interest_earned'   => $totalInterestEarned,
            'total_principal_returned' => $totalPrincipalReturned,
            'fundings'                => $fundings->take(10),
            'recent_transactions'     => WalletTransaction::where('wallet_id', $wallet?->id)
                ->latest()
                ->limit(5)
                ->get(),
            'auto_invest_rule'        => $autoInvestRule,
            'grade_chart_data'        => array_values($gradeDistribution),
        ];

        return view('dashboard', ['role' => 'lender', 'stats' => $stats]);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Returns loan count per month for the last 6 months for admin chart.
     */
    private function getMonthlyLoanData(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $data[] = [
                'label' => $month->format('M Y'),
                'count' => LoanRequest::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }
        return $data;
    }
}
