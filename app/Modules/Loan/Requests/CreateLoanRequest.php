<?php

namespace App\Modules\Loan\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class CreateLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for a new loan application.
     * Note: risk_grade and interest_rate are NOT user-submitted.
     *       They are auto-assigned by CreditScoringService in LoanRequestService.
     */
    public function rules(): array
    {
        $minAmount = (int) Setting::getVal('min_loan_amount', 1000000);
        $maxAmount = (int) Setting::getVal('max_loan_amount', 500000000);

        return [
            'category_id'            => ['required', 'exists:loan_categories,id'],
            'amount'                 => ['required', 'numeric', "min:{$minAmount}", "max:{$maxAmount}"],
            'duration'               => ['required', 'integer', 'in:3,6,12,24'],
            'purpose'                => ['required', 'string', 'max:255'],
            'collateral_currency_id' => ['nullable', 'exists:currencies,id'],
            'description'            => ['nullable', 'string', 'max:5000'],
        ];
    }
}
