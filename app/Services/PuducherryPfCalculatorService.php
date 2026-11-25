<?php

namespace App\Services;

class PuducherryPfCalculatorService
{
    /**
     * Statutory EPFO Wage Ceiling in India (including Puducherry)
     * Effective from September 17, 2026: ₹25,000 per month (previously ₹15,000).
     */
    public const STATUTORY_CEILING = 25000.00;
    public const PREVIOUS_CEILING = 15000.00;

    /**
     * Statutory Contribution Rates
     */
    public const EMPLOYEE_PF_RATE = 0.12;      // 12% of PF Wages
    public const EMPLOYER_TOTAL_RATE = 0.12;   // 12% of PF Wages
    public const EMPLOYER_EPS_RATE = 0.0833;   // 8.33% to Employees' Pension Scheme (capped at ceiling)
    public const EMPLOYER_EPF_DIFF_RATE = 0.0367; // 3.67% to Employees' Provident Fund
    public const EDLI_RATE = 0.005;            // 0.5% Employees' Deposit Linked Insurance
    public const ADMIN_CHARGES_RATE = 0.005;    // 0.5% EPFO Admin charges

    /**
     * Puducherry Municipal Half-Yearly Professional Tax maximum is ₹1,250
     * Monthly standard provision: ₹208.33
     */
    public const PUDUCHERRY_MAX_PT_HALF_YEARLY = 1250.00;

    public function calculate(
        float $basic,
        float $da = 0.0,
        float $hra = 0.0,
        float $otherAllowances = 0.0,
        string $policy = 'capped',
        bool $isFirstTimeMember = false,
        bool $isOptedOut = false,
        ?int $payableDays = null,
        ?int $daysInMonth = null
    ): array {
        $daysInMonth = $daysInMonth ?: 30;
        $payableDays = $payableDays !== null ? $payableDays : $daysInMonth;

        $pfWages = round($basic + $da, 2);
        $grossSalary = round($basic + $da + $hra + $otherAllowances, 2);

        $canOptOut = $isFirstTimeMember && ($pfWages > self::STATUTORY_CEILING);
        $optedOut = $canOptOut && $isOptedOut;

        if ($optedOut) {
            $applicablePfBase = 0.0;
            $employeePf = 0.0;
            $employerTotal = 0.0;
            $employerEps = 0.0;
            $employerEpf = 0.0;
            $edli = 0.0;
            $adminCharges = 0.0;
        } else {
            if ($policy === 'actual') {
                $applicablePfBase = $pfWages;
            } else {
                $applicablePfBase = min($pfWages, self::STATUTORY_CEILING);
            }

            $employeePf = round($applicablePfBase * self::EMPLOYEE_PF_RATE, 2);
            $employerTotal = round($applicablePfBase * self::EMPLOYER_TOTAL_RATE, 2);

            $epsBase = min($applicablePfBase, self::STATUTORY_CEILING);
            $employerEps = round($epsBase * self::EMPLOYER_EPS_RATE, 2);
            $employerEpf = max(0.0, round($employerTotal - $employerEps, 2));

            $edli = round(min($applicablePfBase, self::STATUTORY_CEILING) * self::EDLI_RATE, 2);
            $adminCharges = round($applicablePfBase * self::ADMIN_CHARGES_RATE, 2);
        }

        $puducherryPtMonthly = $this->calculatePuducherryMonthlyPt($grossSalary);
        $totalDeductions = round($employeePf + $puducherryPtMonthly, 2);
        $netMonthlySalary = max(0.0, round($grossSalary - $totalDeductions, 2));

        $dailyRate = $daysInMonth > 0 ? round($netMonthlySalary / $daysInMonth, 2) : 0.0;
        $takeHomeByToday = round($dailyRate * $payableDays, 2);
        $totalEpfoRemittance = round($employeePf + $employerTotal, 2);

        return [
            'region' => 'Puducherry (UT) & Central EPFO',
            'statutory_ceiling' => self::STATUTORY_CEILING,
            'previous_ceiling' => self::PREVIOUS_CEILING,
            'policy' => $policy,
            'is_opted_out' => $optedOut,
            'can_opt_out' => $canOptOut,
            'basic_salary' => $basic,
            'dearness_allowance' => $da,
            'pf_wages' => $pfWages,
            'hra' => $hra,
            'other_allowances' => $otherAllowances,
            'gross_salary' => $grossSalary,
            'applicable_pf_base' => $applicablePfBase,
            'employee_pf_rate' => self::EMPLOYEE_PF_RATE * 100,
            'employee_pf_deduction' => $employeePf,
            'puducherry_pt' => $puducherryPtMonthly,
            'total_deductions' => $totalDeductions,
            'employer_total_contribution' => $employerTotal,
            'employer_eps_contribution' => $employerEps,
            'employer_epf_contribution' => $employerEpf,
            'edli_contribution' => $edli,
            'epfo_admin_charges' => $adminCharges,
            'total_epfo_remittance' => $totalEpfoRemittance,
            'net_monthly_salary' => $netMonthlySalary,
            'days_in_month' => $daysInMonth,
            'payable_days' => $payableDays,
            'daily_net_rate' => $dailyRate,
            'take_home_by_today' => $takeHomeByToday,
        ];
    }

    public function calculatePuducherryMonthlyPt(float $monthlyGross): float
    {
        $halfYearlyGross = $monthlyGross * 6;

        if ($halfYearlyGross <= 25000) {
            return 0.0;
        } elseif ($halfYearlyGross <= 50000) {
            return round(250.0 / 6, 2);
        } elseif ($halfYearlyGross <= 75000) {
            return round(500.0 / 6, 2);
        } elseif ($halfYearlyGross <= 100000) {
            return round(750.0 / 6, 2);
        } elseif ($halfYearlyGross <= 125000) {
            return round(1000.0 / 6, 2);
        } else {
            return round(self::PUDUCHERRY_MAX_PT_HALF_YEARLY / 6, 2);
        }
    }
}

