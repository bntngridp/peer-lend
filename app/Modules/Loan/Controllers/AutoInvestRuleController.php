<?php

namespace App\Modules\Loan\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AutoInvestRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutoInvestRuleController extends Controller
{
    /**
     * Update or save Lender Auto-Invest Configuration.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_active'               => ['nullable', 'boolean'],
            'min_grade'               => ['required', 'in:A,B,C,D'],
            'max_grade'               => ['required', 'in:A,B,C,D'],
            'max_allocation_per_loan' => ['required', 'numeric', 'min:100000', 'max:100000000'],
            'max_ltv'                 => ['required', 'numeric', 'min:10', 'max:100'],
        ]);

        $rule = AutoInvestRule::updateOrCreate(
            ['lender_id' => Auth::id()],
            [
                'is_active'               => $request->has('is_active'),
                'min_grade'               => $validated['min_grade'],
                'max_grade'               => $validated['max_grade'],
                'max_allocation_per_loan' => $validated['max_allocation_per_loan'],
                'max_ltv'                 => $validated['max_ltv'],
            ]
        );

        return redirect()->route('dashboard')->with('success', 'Konfigurasi Auto-Invest berhasil disimpan!');
    }
}
