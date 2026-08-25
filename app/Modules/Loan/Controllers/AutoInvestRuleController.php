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
    public function update(Request $request): \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $existingRule = AutoInvestRule::firstOrNew(['lender_id' => Auth::id()]);

        $validated = $request->validate([
            'is_active'               => ['nullable', 'boolean'],
            'min_grade'               => ['nullable', 'in:A,B,C,D'],
            'max_grade'               => ['nullable', 'in:A,B,C,D'],
            'max_allocation_per_loan' => ['nullable', 'numeric', 'min:100000', 'max:100000000'],
            'max_ltv'                 => ['nullable', 'numeric', 'min:10', 'max:100'],
        ]);

        $existingRule->is_active = $request->boolean('is_active');
        
        if (isset($validated['min_grade'])) {
            $existingRule->min_grade = $validated['min_grade'];
        } elseif (!$existingRule->exists) {
            $existingRule->min_grade = 'D';
        }

        if (isset($validated['max_grade'])) {
            $existingRule->max_grade = $validated['max_grade'];
        } elseif (!$existingRule->exists) {
            $existingRule->max_grade = 'A';
        }

        if (isset($validated['max_allocation_per_loan'])) {
            $existingRule->max_allocation_per_loan = $validated['max_allocation_per_loan'];
        } elseif (!$existingRule->exists) {
            $existingRule->max_allocation_per_loan = 1000000.00;
        }

        if (isset($validated['max_ltv'])) {
            $existingRule->max_ltv = $validated['max_ltv'];
        } elseif (!$existingRule->exists) {
            $existingRule->max_ltv = 80.00;
        }

        $existingRule->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $existingRule->is_active ? 'Investasi Otomatis berhasil diaktifkan!' : 'Investasi Otomatis dinonaktifkan.',
                'data' => $existingRule
            ]);
        }

        $msg = $request->has('min_grade') 
            ? 'Aturan Investasi Otomatis berhasil diperbarui!' 
            : ($existingRule->is_active ? 'Investasi Otomatis berhasil diaktifkan!' : 'Investasi Otomatis dinonaktifkan.');

        return redirect()->route('dashboard')->with('success', $msg);
    }
}
