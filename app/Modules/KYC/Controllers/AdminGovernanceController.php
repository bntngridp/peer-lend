<?php

namespace App\Modules\KYC\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\FeeConfiguration;
use App\Models\InterestRate;
use App\Models\LoanInstallment;
use App\Models\LoanRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminGovernanceController extends Controller
{
    // ─── Users ────────────────────────────────────────────────────────────────

    public function users(Request $request)
    {
        $query = User::with(['profile', 'roles', 'kyc']);

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
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

    public function showUser(User $user)
    {
        $user->load([
            'profile',
            'roles',
            'kyc',
            'wallets.currency',
            'loanRequests' => fn($q) => $q->latest()->take(10),
            'fundings.loan' => fn($q) => $q->latest()->take(10),
            'auditLogs' => fn($q) => $q->latest()->take(20),
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function toggleUserStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', __('You cannot suspend your own admin account.'));
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $actionName = $user->is_active ? 'user_reactivated' : 'user_suspended';
        $statusText = $user->is_active ? __('activated') : __('suspended');

        app(\App\Modules\Shared\Services\AuditLogService::class)->log(
            $actionName,
            User::class,
            $user->id,
            auth()->user(),
            ['is_active' => $user->is_active]
        );

        return back()->with('success', __('User account :email has been :status successfully.', [
            'email'  => $user->email,
            'status' => $statusText,
        ]));
    }

    // ─── Transactions ─────────────────────────────────────────────────────────

    public function transactions()
    {
        $transactions = WalletTransaction::with(['wallet.user', 'payment'])->latest('created_at')->paginate(15);
        $exportTransactions = WalletTransaction::with(['wallet.user', 'payment'])->latest('created_at')->take(100)->get();
        return view('admin.transactions.index', compact('transactions', 'exportTransactions'));
    }

    // ─── Financial Configuration ──────────────────────────────────────────────

    public function financials()
    {
        $interestRates = InterestRate::all()->keyBy('risk_grade');

        $feeConfigs = FeeConfiguration::all()->keyBy('type');

        $currencySettings = [
            'idr'  => Setting::getVal('currency_idr_enabled', '1') === '1',
            'usdt' => Setting::getVal('currency_usdt_enabled', '1') === '1',
            'eth'  => Setting::getVal('currency_eth_enabled', '1') === '1',
            'btc'  => Setting::getVal('currency_btc_enabled', '1') === '1',
        ];

        return view('admin.financials.index', compact(
            'interestRates',
            'feeConfigs',
            'currencySettings'
        ));
    }

    public function updateRates(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'grade_a_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_a_max' => ['required', 'numeric', 'min:0', 'max:100', 'gte:grade_a_min'],
            'grade_b_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_b_max' => ['required', 'numeric', 'min:0', 'max:100', 'gte:grade_b_min'],
            'grade_c_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_c_max' => ['required', 'numeric', 'min:0', 'max:100', 'gte:grade_c_min'],
            'grade_d_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'grade_d_max' => ['required', 'numeric', 'min:0', 'max:100', 'gte:grade_d_min'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'rates')->withInput();
        }

        DB::transaction(function () use ($request) {
            foreach (['a', 'b', 'c', 'd'] as $g) {
                InterestRate::updateOrCreate(
                    ['risk_grade' => strtoupper($g)],
                    [
                        'min_rate' => (float) $request->input("grade_{$g}_min"),
                        'max_rate' => (float) $request->input("grade_{$g}_max"),
                    ]
                );
            }
        });

        return redirect()->route('admin.financials.index')
            ->with('success', __('Interest rate brackets updated successfully.'));
    }

    public function updateFees(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'origination_fee' => ['required', 'numeric', 'min:0', 'max:100'],
            'service_fee'     => ['required', 'numeric', 'min:0'],
            'penalty_rate'    => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'fees')->withInput();
        }

        DB::transaction(function () use ($request) {
            FeeConfiguration::updateOrCreate(
                ['type' => 'origination_fee'],
                ['value' => (float) $request->input('origination_fee'), 'value_type' => 'percentage', 'is_active' => true]
            );
            FeeConfiguration::updateOrCreate(
                ['type' => 'service_fee'],
                ['value' => (float) $request->input('service_fee'), 'value_type' => 'fixed', 'is_active' => true]
            );
            FeeConfiguration::updateOrCreate(
                ['type' => 'penalty_rate'],
                ['value' => (float) $request->input('penalty_rate'), 'value_type' => 'percentage', 'is_active' => true]
            );
        });

        return redirect()->route('admin.financials.index')
            ->with('success', __('Fee schedule saved successfully.'));
    }

    public function updateCurrencies(Request $request): RedirectResponse
    {
        $currencies = ['idr', 'usdt', 'eth', 'btc'];
        foreach ($currencies as $code) {
            Setting::setVal(
                "currency_{$code}_enabled",
                $request->has("currency_{$code}") ? '1' : '0',
                "Whether {$code} currency is enabled for platform use"
            );
        }

        return redirect()->route('admin.financials.index')
            ->with('success', __('Currency settings updated successfully.'));
    }

    // ─── Role Management ──────────────────────────────────────────────────────

    public function roles()
    {
        $roles       = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name'          => ['required', 'string', 'max:50', 'unique:roles,name', 'regex:/^[a-z0-9_]+$/'],
            'description'   => ['nullable', 'string', 'max:255'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ], [
            'name.regex' => __('Role name may only contain lowercase letters, numbers, and underscores.'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator, 'role_create')->withInput();
        }

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name'        => $request->input('name'),
                'description' => $request->input('description'),
                'guard_name'  => 'web',
            ]);

            if ($request->filled('permissions')) {
                $role->permissions()->sync($request->input('permissions', []));
            }
        });

        return redirect()->route('admin.roles.index')
            ->with('success', __('Role ":name" created successfully.', ['name' => $request->input('name')]));
    }

    public function updatePermissions(Request $request, Role $role): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index')
            ->with('success', __('Permissions for ":name" updated successfully.', ['name' => $role->name]));
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        $systemRoles = ['admin', 'borrower', 'lender', 'collection_officer', 'customer_service'];

        if (in_array($role->name, $systemRoles)) {
            return back()->with('error', __('System roles cannot be deleted.'));
        }

        $userCount = $role->users()->count();
        if ($userCount > 0) {
            return back()->with('error', __('Cannot delete ":name" — it is assigned to :count user(s). Reassign users first.', [
                'name'  => $role->name,
                'count' => $userCount,
            ]));
        }

        $roleName = $role->name;
        $role->permissions()->detach();
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', __('Role ":name" deleted successfully.', ['name' => $roleName]));
    }

    // ─── Analytics ────────────────────────────────────────────────────────────

    public function analytics(Request $request)
    {
        $days = in_array((int) $request->input('days'), [30, 90, 365]) ? (int) $request->input('days') : 30;

        $timeframeLabel = match($days) {
            90  => __('Last 90 Days'),
            365 => __('Last 1 Year'),
            default => __('Last 30 Days'),
        };

        $walletBalance      = (float) (Wallet::sum('available_balance') + Wallet::sum('hold_balance'));
        $activeLoansAmount  = (float) (LoanRequest::whereIn('status', ['funded', 'active'])->sum('amount') ?? 0);
        $totalLiquidityRaw  = $walletBalance + $activeLoansAmount;

        $totalLiquidityFormatted = $totalLiquidityRaw >= 1_000_000_000
            ? number_format($totalLiquidityRaw / 1_000_000_000, 2) . 'B'
            : ($totalLiquidityRaw >= 1_000_000
                ? number_format($totalLiquidityRaw / 1_000_000, 1) . 'M'
                : number_format($totalLiquidityRaw, 0));

        $totalLoansCount     = LoanRequest::count();
        $defaultedLoansCount = LoanRequest::whereIn('status', ['defaulted', 'overdue'])->count();
        $nplRate = ($totalLoansCount > 0)
            ? round(($defaultedLoansCount / $totalLoansCount) * 100, 2)
            : 0.0;

        $nextMonthOutflow = (float) (LoanInstallment::where('status', 'pending')
            ->where('due_date', '<=', now()->addDays(30))
            ->sum('total_amount') ?? 0);

        $lcrRatio = ($nextMonthOutflow > 0)
            ? round(($walletBalance / $nextMonthOutflow) * 100, 1)
            : ($walletBalance > 0 ? 100.0 : 0.0);

        $nsfrRatio = ($activeLoansAmount > 0)
            ? round((($walletBalance + $activeLoansAmount) / $activeLoansAmount) * 100, 1)
            : ($walletBalance > 0 ? 100.0 : 0.0);

        $labels = $disbursements = $repayments = [];

        $periods = match($days) {
            365 => [['label' => __('Q1'), 'offset' => 3], ['label' => __('Q2'), 'offset' => 2], ['label' => __('Q3'), 'offset' => 1], ['label' => __('Q4'), 'offset' => 0]],
            90  => [['label' => __('Month 1'), 'offset' => 2], ['label' => __('Month 2'), 'offset' => 1], ['label' => __('Month 3'), 'offset' => 0]],
            default => [['label' => __('Week 1'), 'offset' => 3], ['label' => __('Week 2'), 'offset' => 2], ['label' => __('Week 3'), 'offset' => 1], ['label' => __('Week 4'), 'offset' => 0]],
        };

        $unit     = $days === 365 ? 'months' : ($days === 90 ? 'months' : 'days');
        $duration = $days === 365 ? 3 : ($days === 90 ? 1 : 7);

        foreach ($periods as $period) {
            $labels[]       = $period['label'];
            $start          = now()->sub("{$unit}", ($period['offset'] + 1) * $duration);
            $end            = now()->sub("{$unit}", $period['offset'] * $duration);
            $disbursements[] = round((float)(LoanRequest::whereIn('status', ['funded', 'active', 'completed'])->whereBetween('created_at', [$start, $end])->sum('amount') ?? 0) / 1_000_000, 1);
            $repayments[]   = round((float)(LoanInstallment::where('status', 'paid')->whereBetween('paid_at', [$start, $end])->sum('total_amount') ?? 0) / 1_000_000, 1);
        }

        $disbursementData  = ['labels' => $labels, 'disbursements' => $disbursements, 'repayments' => $repayments];
        $totalDisbursement = number_format(array_sum($disbursementData['disbursements']), 1);
        $totalRepayment    = number_format(array_sum($disbursementData['repayments']), 1);

        $gradeCounts = LoanRequest::select('risk_grade', DB::raw('count(*) as total'))
            ->whereNotNull('risk_grade')
            ->groupBy('risk_grade')->pluck('total', 'risk_grade')->toArray();

        $totalGradeLoans = array_sum($gradeCounts);
        if ($totalGradeLoans > 0) {
            $riskDistribution = [
                'AAA' => round((($gradeCounts['AAA'] ?? 0) / $totalGradeLoans) * 100, 1),
                'AA'  => round((($gradeCounts['AA']  ?? 0) / $totalGradeLoans) * 100, 1),
                'A'   => round((($gradeCounts['A']   ?? 0) / $totalGradeLoans) * 100, 1),
                'B'   => round((($gradeCounts['B'] ?? 0 + ($gradeCounts['C'] ?? 0) + ($gradeCounts['D'] ?? 0)) / $totalGradeLoans) * 100, 1),
            ];
        } else {
            $riskDistribution = ['AAA' => 0.0, 'AA' => 0.0, 'A' => 0.0, 'B' => 0.0];
        }

        $topTierPercent = round($riskDistribution['AAA'] + $riskDistribution['AA'], 1);

        return view('admin.analytics.index', compact(
            'days', 'timeframeLabel', 'disbursementData', 'totalDisbursement', 'totalRepayment',
            'totalLiquidityFormatted', 'nplRate', 'lcrRatio', 'nsfrRatio', 'riskDistribution', 'topTierPercent'
        ));
    }
}
