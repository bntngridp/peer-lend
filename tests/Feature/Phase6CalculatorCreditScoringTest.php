<?php

namespace Tests\Feature;

use App\Models\InterestRate;
use App\Models\LoanRequest;
use App\Models\User;
use App\Modules\Loan\Services\CreditScoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase6CalculatorCreditScoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed interest rates used by CreditScoringService and Calculator
        // Use upsert to avoid duplicate key violations when seeder data already exists
        InterestRate::upsert([
            ['risk_grade' => 'A', 'min_rate' => 8.00,  'max_rate' => 10.00, 'created_at' => now(), 'updated_at' => now()],
            ['risk_grade' => 'B', 'min_rate' => 11.00, 'max_rate' => 14.00, 'created_at' => now(), 'updated_at' => now()],
            ['risk_grade' => 'C', 'min_rate' => 15.00, 'max_rate' => 18.00, 'created_at' => now(), 'updated_at' => now()],
            ['risk_grade' => 'D', 'min_rate' => 19.00, 'max_rate' => 24.00, 'created_at' => now(), 'updated_at' => now()],
        ], ['risk_grade'], ['min_rate', 'max_rate', 'updated_at']);
    }

    // ─── Loan Calculator Tests ─────────────────────────────────────────────────

    public function test_calculator_page_is_publicly_accessible(): void
    {
        $response = $this->get(route('calculator.index'));
        $response->assertStatus(200);
        $response->assertSee('Kalkulator Pinjaman');
    }

    public function test_calculator_api_returns_correct_installment_for_grade_a(): void
    {
        $response = $this->postJson(route('calculator.calculate'), [
            'amount'     => 10000000,
            'duration'   => 12,
            'risk_grade' => 'A',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'grade', 'annual_rate', 'rate_range',
                'monthly_payment', 'total_payment', 'total_interest',
                'origination_fee', 'amount', 'duration', 'schedule',
            ])
            ->assertJson([
                'success' => true,
                'grade'   => 'A',
            ]);

        // Grade A midpoint is (8 + 10) / 2 = 9.00
        $this->assertEquals(9.00, (float) $response->json('annual_rate'));

        // Schedule should have exactly 12 entries
        $this->assertCount(12, $response->json('schedule'));
    }

    public function test_calculator_api_returns_correct_installment_for_grade_d(): void
    {
        $response = $this->postJson(route('calculator.calculate'), [
            'amount'     => 5000000,
            'duration'   => 6,
            'risk_grade' => 'D',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'grade' => 'D']);

        // Grade D midpoint is (19 + 24) / 2 = 21.50
        $this->assertEquals(21.50, (float) $response->json('annual_rate'));
        $this->assertCount(6, $response->json('schedule'));
    }

    public function test_calculator_api_rejects_invalid_duration(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->post(route('calculator.calculate'), [
            'amount'     => 5000000,
            'duration'   => 5,  // Invalid: not in [3, 6, 12, 24]
            'risk_grade' => 'A',
        ]);
    }

    // ─── Credit Scoring Service Tests ─────────────────────────────────────────

    public function test_credit_scoring_assigns_grade_d_to_new_user_without_kyc(): void
    {
        $user = User::factory()->create();
        // No KYC, no profile, no loan history = score 0 → Grade D

        $scoring = app(CreditScoringService::class)->calculateScore($user);

        $this->assertArrayHasKey('score', $scoring);
        $this->assertArrayHasKey('grade', $scoring);
        $this->assertArrayHasKey('interest_rate', $scoring);
        $this->assertEquals('D', $scoring['grade']);
        $this->assertEquals(0, $scoring['score']);
    }

    public function test_credit_scoring_resolves_grade_correctly_by_score(): void
    {
        $service = app(CreditScoringService::class);

        $this->assertEquals('A', $service->resolveGrade(80));
        $this->assertEquals('A', $service->resolveGrade(100));
        $this->assertEquals('B', $service->resolveGrade(60));
        $this->assertEquals('B', $service->resolveGrade(79));
        $this->assertEquals('C', $service->resolveGrade(40));
        $this->assertEquals('C', $service->resolveGrade(59));
        $this->assertEquals('D', $service->resolveGrade(0));
        $this->assertEquals('D', $service->resolveGrade(39));
    }

    public function test_credit_scoring_midpoint_rate_is_correct_for_all_grades(): void
    {
        $service = app(CreditScoringService::class);

        // (8 + 10) / 2 = 9.00
        $this->assertEquals('9.00', $service->resolveMidpointRate('A'));

        // (11 + 14) / 2 = 12.50
        $this->assertEquals('12.50', $service->resolveMidpointRate('B'));

        // (15 + 18) / 2 = 16.50
        $this->assertEquals('16.50', $service->resolveMidpointRate('C'));

        // (19 + 24) / 2 = 21.50
        $this->assertEquals('21.50', $service->resolveMidpointRate('D'));
    }
}
