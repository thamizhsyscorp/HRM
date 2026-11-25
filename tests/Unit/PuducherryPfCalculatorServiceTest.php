<?php

namespace Tests\Unit;

use App\Services\PuducherryPfCalculatorService;
use Tests\TestCase;

class PuducherryPfCalculatorServiceTest extends TestCase
{
    private PuducherryPfCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PuducherryPfCalculatorService();
    }

    public function test_pf_deduction_on_15000_wage_base(): void
    {
        $result = $this->service->calculate(basic: 15000, da: 0, hra: 5000, otherAllowances: 2000);

        $this->assertEquals(15000.0, $result['pf_wages']);
        $this->assertEquals(1800.0, $result['employee_pf_deduction']);
        $this->assertEquals(1800.0, $result['employer_total_contribution']);
        $this->assertEquals(1249.50, $result['employer_eps_contribution']);
        $this->assertEquals(550.50, $result['employer_epf_contribution']);
        $this->assertEquals(3600.0, $result['total_epfo_remittance']);
    }

    public function test_pf_deduction_on_25000_wage_base(): void
    {
        $result = $this->service->calculate(basic: 25000, da: 0, hra: 10000, otherAllowances: 5000);

        $this->assertEquals(25000.0, $result['pf_wages']);
        $this->assertEquals(25000.0, $result['applicable_pf_base']);
        $this->assertEquals(3000.0, $result['employee_pf_deduction']);
        $this->assertEquals(3000.0, $result['employer_total_contribution']);
        $this->assertEquals(2082.50, $result['employer_eps_contribution']);
        $this->assertEquals(917.50, $result['employer_epf_contribution']);
        $this->assertEquals(6000.0, $result['total_epfo_remittance']);
    }

    public function test_pf_deduction_above_ceiling_with_capped_policy(): void
    {
        $result = $this->service->calculate(
            basic: 40000,
            da: 5000,
            hra: 15000,
            otherAllowances: 10000,
            policy: 'capped'
        );

        $this->assertEquals(45000.0, $result['pf_wages']);
        $this->assertEquals(25000.0, $result['applicable_pf_base']);
        $this->assertEquals(3000.0, $result['employee_pf_deduction']);
        $this->assertEquals(3000.0, $result['employer_total_contribution']);
        $this->assertEquals(6000.0, $result['total_epfo_remittance']);
    }

    public function test_pf_deduction_above_ceiling_with_actual_policy(): void
    {
        $result = $this->service->calculate(
            basic: 40000,
            da: 5000,
            hra: 15000,
            otherAllowances: 10000,
            policy: 'actual'
        );

        $this->assertEquals(45000.0, $result['pf_wages']);
        $this->assertEquals(45000.0, $result['applicable_pf_base']);
        $this->assertEquals(5400.0, $result['employee_pf_deduction']);
        $this->assertEquals(5400.0, $result['employer_total_contribution']);
        $this->assertEquals(2082.50, $result['employer_eps_contribution']);
        $this->assertEquals(3317.50, $result['employer_epf_contribution']);
        $this->assertEquals(10800.0, $result['total_epfo_remittance']);
    }

    public function test_pf_opt_out_for_eligible_new_employee(): void
    {
        $result = $this->service->calculate(
            basic: 30000,
            da: 0,
            hra: 10000,
            otherAllowances: 5000,
            policy: 'capped',
            isFirstTimeMember: true,
            isOptedOut: true
        );

        $this->assertTrue($result['can_opt_out']);
        $this->assertTrue($result['is_opted_out']);
        $this->assertEquals(0.0, $result['applicable_pf_base']);
        $this->assertEquals(0.0, $result['employee_pf_deduction']);
        $this->assertEquals(0.0, $result['employer_total_contribution']);
        $this->assertEquals(0.0, $result['total_epfo_remittance']);
    }

    public function test_puducherry_professional_tax_monthly(): void
    {
        $pt = $this->service->calculatePuducherryMonthlyPt(50000);
        $this->assertEquals(208.33, $pt);

        $lowPt = $this->service->calculatePuducherryMonthlyPt(3000);
        $this->assertEquals(0.0, $lowPt);
    }
}

