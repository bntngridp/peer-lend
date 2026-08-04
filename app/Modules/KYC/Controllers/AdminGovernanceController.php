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

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhereHas('profile', function ($pq) use ($search) {
                      $pq->where('full_name', 'like', "%{$search}%");
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
        return view('admin.transactions.index', compact('transactions'));
    }

    public function analytics(Request $request)
    {
        $days = in_array((int) $request->input('days'), [30, 90, 365]) ? (int) $request->input('days') : 30;

        $timeframeLabel = match($days) {
            90 => __('Last 90 Days'),
            365 => __('Last 1 Year'),
            default => __('Last 30 Days'),
        };

        $disbursementData = match($days) {
            90 => [
                'labels' => [__('Month 1'), __('Month 2'), __('Month 3')],
                'disbursements' => [48.2, 56.4, 62.8],
                'repayments' => [42.1, 50.8, 58.3],
            ],
            365 => [
                'labels' => [__('Q1'), __('Q2'), __('Q3'), __('Q4')],
                'disbursements' => [142.5, 185.2, 210.8, 245.0],
                'repayments' => [128.0, 164.5, 192.3, 228.4],
            ],
            default => [
                'labels' => [__('Week 1'), __('Week 2'), __('Week 3'), __('Week 4')],
                'disbursements' => [12.4, 18.2, 15.6, 22.4],
                'repayments' => [10.1, 14.5, 13.8, 19.1],
            ],
        };

        $totalDisbursement = number_format(array_sum($disbursementData['disbursements']), 1);
        $totalRepayment = number_format(array_sum($disbursementData['repayments']), 1);

        return view('admin.analytics.index', compact(
            'days',
            'timeframeLabel',
            'disbursementData',
            'totalDisbursement',
            'totalRepayment'
        ));
    }
}
