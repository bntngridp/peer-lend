<?php

namespace App\Modules\Loan\Requests;

use App\Models\InterestRate;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class CreateLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $minAmount = (int) Setting::getVal('min_loan_amount', 1000000);
        $maxAmount = (int) Setting::getVal('max_loan_amount', 500000000);

        return [
            'category_id'            => ['required', 'exists:loan_categories,id'],
            'amount'                 => ['required', 'numeric', "min:{$minAmount}", "max:{$maxAmount}"],
            'risk_grade'             => ['required', 'in:A,B,C,D'],
            'interest_rate'          => ['required', 'numeric', function ($attribute, $value, $fail) {
                $grade = $this->input('risk_grade');
                if ($grade) {
                    $limits = InterestRate::rangeForGrade($grade);
                    if ($limits && ! $limits->isValidRate($value)) {
                        $fail("The interest rate for Grade {$grade} must be between {$limits->min_rate}% and {$limits->max_rate}%.");
                    }
                }
            }],
            'duration'               => ['required', 'integer', 'in:3,6,12,24'],
            'purpose'                => ['required', 'string', 'max:255'],
            'collateral_currency_id' => ['nullable', 'exists:currencies,id'],
            'description'            => ['nullable', 'string', 'max:5000'],
        ];
    }
}
