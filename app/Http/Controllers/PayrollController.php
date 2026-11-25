<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSalaryComponent;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\SalaryComponent;
use App\Models\SalaryStructure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PayrollController extends Controller
{
    /**
     * Display Payroll, Salary Structures, and Component Breakdowns with Real-Time Take-Home by Today.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');

        $today = Carbon::today();
        $daysInMonth = $today->daysInMonth;
        $elapsedDays = $today->day;

        // Calculate Corporate Working Days in Month (excluding Sundays and 2nd/4th Saturdays: 23 working days in Nov 2026)
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        $workingDaysInMonth = 0;
        $cur = $startOfMonth->copy();
        while ($cur->lte($endOfMonth)) {
            $isSunday = $cur->isSunday();
            $is2ndOr4thSat = $cur->isSaturday() && in_array(ceil($cur->day / 7), [2, 4]);
            if (!$isSunday && !$is2ndOr4thSat) {
                $workingDaysInMonth++;
            }
            $cur->addDay();
        }
        $workingDaysInMonth = max(1, $workingDaysInMonth);

        // Public and Company Paid Holidays in this month up to today
        $paidHolidaysElapsed = \App\Models\Holiday::whereYear('holiday_date', $today->year)
            ->whereMonth('holiday_date', $today->month)
            ->whereDate('holiday_date', '<=', $today)
            ->count();

        // Salary Structures
        $salaryStructures = SalaryStructure::withCount('employeeComponents')->where('is_active', true)->get();

        // Components categorized
        $earnings = SalaryComponent::where('category', 'EARNING')->where('is_active', true)->get();
        $deductions = SalaryComponent::where('category', 'DEDUCTION')->where('is_active', true)->get();

        // Total calculated monthly payroll expense across all active employees
        $totalMonthlyEarnings = EmployeeSalaryComponent::whereHas('salaryComponent', fn($q) => $q->where('category', 'EARNING'))
            ->sum('amount');
        $totalMonthlyDeductions = EmployeeSalaryComponent::whereHas('salaryComponent', fn($q) => $q->where('category', 'DEDUCTION'))
            ->sum('amount');
        $netMonthlyPayroll = max(0, $totalMonthlyEarnings - $totalMonthlyDeductions);

        // Workforce Take-Home by Today (accrued gross and net to date)
        $workforceTakeHomeByToday = $workingDaysInMonth > 0 ? round(($netMonthlyPayroll / $workingDaysInMonth) * min($workingDaysInMonth, ($elapsedDays + $paidHolidaysElapsed)), 2) : 0;
        $workforceGrossByToday = $workingDaysInMonth > 0 ? round(($totalMonthlyEarnings / $workingDaysInMonth) * min($workingDaysInMonth, ($elapsedDays + $paidHolidaysElapsed)), 2) : 0;
        $workforceDeductionsByToday = max(0, $workforceGrossByToday - $workforceTakeHomeByToday);

        // Periods & Runs
        $payrollPeriods = PayrollPeriod::latest('start_date')->take(6)->get();

        // Real-time Employee Take-Home Ledger for all active employees
        $allEmployees = Employee::with(['department', 'designation', 'salaryComponents.salaryComponent', 'bankAccounts'])
            ->where('employment_status', 'ACTIVE')
            ->orderBy('first_name')
            ->get();

        $employeeTakeHomes = $allEmployees->map(function ($emp) use ($today, $daysInMonth, $elapsedDays, $workingDaysInMonth, $paidHolidaysElapsed) {
            $basicSalary = (float)($emp->salaryComponents->firstWhere('salaryComponent.code', 'BASIC')?->amount ?? 0);
            $monthlyGross = (float)$emp->salaryComponents
                ->where('salaryComponent.category', 'EARNING')
                ->sum('amount');
            $monthlyDeductions = (float)$emp->salaryComponents
                ->where('salaryComponent.category', 'DEDUCTION')
                ->sum('amount');
            $monthlyNet = max(0, $monthlyGross - $monthlyDeductions);

            $deductionItems = $emp->salaryComponents
                ->where('salaryComponent.category', 'DEDUCTION')
                ->map(fn($c) => [
                    'name' => $c->salaryComponent->name,
                    'code' => $c->salaryComponent->code,
                    'amount' => (float)$c->amount,
                ]);

            // Attended punches this month
            $presentPunches = Attendance::where('employee_id', $emp->id)
                ->whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->whereIn('status', ['PRESENT', 'LATE'])
                ->count();
            $halfDayPunches = Attendance::where('employee_id', $emp->id)
                ->whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->where('status', 'HALF_DAY')
                ->count();
            $approvedLeaves = LeaveRequest::where('employee_id', $emp->id)
                ->where('status', 'APPROVED')
                ->whereMonth('start_date', $today->month)
                ->whereYear('start_date', $today->year)
                ->sum('total_days');

            $attendedDays = $presentPunches + ($halfDayPunches * 0.5);

            // Holidays are also paid and added into the accrued amount
            $paidDays = min($workingDaysInMonth, $attendedDays + $approvedLeaves + $paidHolidaysElapsed);

            $dailyRate = $workingDaysInMonth > 0 ? round($monthlyNet / $workingDaysInMonth, 2) : 0;
            $dailyGross = $workingDaysInMonth > 0 ? round($monthlyGross / $workingDaysInMonth, 2) : 0;

            $takeHomeByToday = round($dailyRate * $paidDays, 2);
            $grossByToday = round($dailyGross * $paidDays, 2);
            $deductionsByToday = max(0, $grossByToday - $takeHomeByToday);

            return [
                'employee' => $emp,
                'basic_salary' => $basicSalary > 0 ? $basicSalary : ($monthlyGross * 0.5),
                'monthly_gross' => $monthlyGross,
                'monthly_deductions' => $monthlyDeductions,
                'deduction_items' => $deductionItems,
                'monthly_net' => $monthlyNet,
                'daily_rate' => $dailyRate,
                'attended_days' => $attendedDays,
                'working_days' => $workingDaysInMonth,
                'paid_days' => $paidDays,
                'payable_days' => $paidDays,
                'take_home_by_today' => $takeHomeByToday,
                'gross_by_today' => $grossByToday,
                'deductions_by_today' => $deductionsByToday,
                'bank_account' => $emp->bankAccounts->first(),
            ];
        });

        // Personal Take-Home details for logged in user (if employee)
        $mySalaryComponents = collect();
        $myTakeHomeData = null;
        $myDeductionsList = collect();
        $myBasic = 0;

        if ($user->employee) {
            $mySalaryComponents = EmployeeSalaryComponent::with('salaryComponent')
                ->where('employee_id', $user->employee->id)
                ->get();

            $myBasic = (float)($mySalaryComponents->firstWhere('salaryComponent.code', 'BASIC')?->amount ?? 0);
            $myGross = (float)$mySalaryComponents->where('salaryComponent.category', 'EARNING')->sum('amount');
            $myDeductions = (float)$mySalaryComponents->where('salaryComponent.category', 'DEDUCTION')->sum('amount');
            $myNet = max(0, $myGross - $myDeductions);
            $myDeductionsList = $mySalaryComponents->where('salaryComponent.category', 'DEDUCTION');

            if ($myBasic <= 0 && $myGross > 0) {
                $myBasic = $myGross * 0.5;
            }

            $myPresent = Attendance::where('employee_id', $user->employee->id)
                ->whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->whereIn('status', ['PRESENT', 'LATE'])
                ->count();
            $myHalfDay = Attendance::where('employee_id', $user->employee->id)
                ->whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->where('status', 'HALF_DAY')
                ->count();
            $myLeaves = LeaveRequest::where('employee_id', $user->employee->id)
                ->where('status', 'APPROVED')
                ->whereMonth('start_date', $today->month)
                ->whereYear('start_date', $today->year)
                ->sum('total_days');

            $myAttendedDays = $myPresent + ($myHalfDay * 0.5);

            // Holidays are also paid and added into the amount
            $myPaidDays = min($workingDaysInMonth, $myAttendedDays + $myLeaves + $paidHolidaysElapsed);

            $myDailyRate = $workingDaysInMonth > 0 ? round($myNet / $workingDaysInMonth, 2) : 0;
            $myTakeHomeByToday = round($myDailyRate * $myPaidDays, 2);
            $myGrossByToday = $workingDaysInMonth > 0 ? round(($myGross / $workingDaysInMonth) * $myPaidDays, 2) : 0;
            $myDeductionsByToday = max(0, $myGrossByToday - $myTakeHomeByToday);

            $myTakeHomeData = [
                'basic_salary' => $myBasic,
                'monthly_gross' => $myGross,
                'monthly_deductions' => $myDeductions,
                'monthly_net' => $myNet,
                'daily_rate' => $myDailyRate,
                'attended_days' => $myAttendedDays,
                'working_days' => $workingDaysInMonth,
                'paid_days' => $myPaidDays,
                'payable_days' => $myPaidDays,
                'take_home_by_today' => $myTakeHomeByToday,
                'gross_by_today' => $myGrossByToday,
                'deductions_by_today' => $myDeductionsByToday,
                'remaining_take_home' => max(0, $myNet - $myTakeHomeByToday),
            ];
        }

        return view('payroll.index', compact(
            'salaryStructures',
            'earnings',
            'deductions',
            'totalMonthlyEarnings',
            'totalMonthlyDeductions',
            'netMonthlyPayroll',
            'payrollPeriods',
            'mySalaryComponents',
            'isSuperAdmin',
            'isHr',
            'daysInMonth',
            'elapsedDays',
            'workingDaysInMonth',
            'paidHolidaysElapsed',
            'workforceTakeHomeByToday',
            'workforceGrossByToday',
            'workforceDeductionsByToday',
            'employeeTakeHomes',
            'myTakeHomeData',
            'myBasic',
            'myDeductionsList'
        ));
    }
}
