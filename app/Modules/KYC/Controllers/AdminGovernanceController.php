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

    public function analytics()
    {
        return view('admin.analytics.index');
    }
}
