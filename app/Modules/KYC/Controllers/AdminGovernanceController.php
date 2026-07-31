<?php

namespace App\Modules\KYC\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Loan\Models\Loan;
use App\Modules\Wallet\Models\Transaction;
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
        $transactions = Transaction::with('wallet.user')->latest()->paginate(15);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function analytics()
    {
        return view('admin.analytics.index');
    }
}
