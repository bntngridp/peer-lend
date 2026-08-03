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
        $users = User::with('profile')->paginate(10);
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
