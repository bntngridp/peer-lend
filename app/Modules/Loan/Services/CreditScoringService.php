<?php

namespace App\Modules\Loan\Services;

use App\Models\InterestRate;
use App\Models\LoanRequest;
use App\Models\User;

class CreditScoringService
{
    // ─── Grade Thresholds ────────────────────────────────────────────────────

    const GRADE_A_MIN = 80;
    const GRADE_B_MIN = 60;
    const GRADE_C_MIN = 40;

    // ─── Score Weights ────────────────────────────────────────────────────────

    const SCORE_KYC_APPROVED         = 40;
    const SCORE_PER_COMPLETED_LOAN   = 10;
    const SCORE_PER_LATE_LOAN        = -15;
    const SCORE_PROFILE_COMPLETE     = 10;
    const SCORE_ACCOUNT_AGE_BONUS    = 10; // Account older than 30 days

    /**
     * Calculate credit score for a borrower and return grade + interest rate.
     *
     * @return array{score: int, grade: string, interest_rate: string, interest_min: string, interest_max: string}
     */
    public function calculateScore(User $borrower): array
    {
        $score = 0;

        // 1. KYC Status — most critical factor (+40 pts)
        $kyc = $borrower->kyc;
        if ($kyc && $kyc->status === 'approved') {
            $score += self::SCORE_KYC_APPROVED;
        }

        // 2. Loan History — completed loans (+10 pts each)
        $completedLoans = LoanRequest::where('borrower_id', $borrower->id)
            ->where('status', LoanRequest::STATUS_COMPLETED)
            ->count();
        $score += $completedLoans * self::SCORE_PER_COMPLETED_LOAN;

        // 3. Loan History — liquidated/defaulted loans (-15 pts each)
        $defaultedLoans = LoanRequest::where('borrower_id', $borrower->id)
            ->where('status', LoanRequest::STATUS_LIQUIDATED)
            ->count();
        $score += $defaultedLoans * self::SCORE_PER_LATE_LOAN;

        // 4. Profile Completeness (+10 pts)
        $profile = $borrower->profile;
        if ($profile && $profile->full_name && $profile->phone) {
            $score += self::SCORE_PROFILE_COMPLETE;
        }

        // 5. Account Age Bonus (+10 pts if account is older than 30 days)
        if ($borrower->created_at && $borrower->created_at->diffInDays(now()) >= 30) {
            $score += self::SCORE_ACCOUNT_AGE_BONUS;
        }

        // Clamp score between 0 and 100
        $score = max(0, min(100, $score));

        $grade = $this->resolveGrade($score);
        $interestRate = $this->resolveMidpointRate($grade);

        $rateConfig = InterestRate::rangeForGrade($grade);

        return [
            'score'         => $score,
            'grade'         => $grade,
            'interest_rate' => $interestRate,
            'interest_min'  => $rateConfig ? (string)$rateConfig->min_rate : $interestRate,
            'interest_max'  => $rateConfig ? (string)$rateConfig->max_rate : $interestRate,
        ];
    }

    /**
     * Convert numeric score to risk grade letter.
     */
    public function resolveGrade(int $score): string
    {
        return match (true) {
            $score >= self::GRADE_A_MIN => 'A',
            $score >= self::GRADE_B_MIN => 'B',
            $score >= self::GRADE_C_MIN => 'C',
            default                     => 'D',
        };
    }

    /**
     * Return the midpoint interest rate for a given grade.
     */
    public function resolveMidpointRate(string $grade): string
    {
        $rateConfig = InterestRate::rangeForGrade($grade);
        if (!$rateConfig) {
            return '12.00';
        }

        $midpoint = bcdiv(bcadd((string)$rateConfig->min_rate, (string)$rateConfig->max_rate, 4), '2', 2);
        return $midpoint;
    }
}
