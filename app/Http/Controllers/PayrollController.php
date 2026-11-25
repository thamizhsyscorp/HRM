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
        $workforceTakeHomeByToday = $daysInMonth > 0 ? round(($netMonthlyPayroll / $daysInMonth) * $elapsedDays, 2) : 0;
        $workforceGrossByToday = $daysInMonth > 0 ? round(($totalMonthlyEarnings / $daysInMonth) * $elapsedDays, 2) : 0;
        $workforceDeductionsByToday = max(0, $workforceGrossByToday - $workforceTakeHomeByToday);

        // Periods & Runs
        $payrollPeriods = PayrollPeriod::latest('start_date')->take(6)->get();

        // Real-time Employee Take-Home Ledger for all active employees
        $allEmployees = Employee::with(['department', 'designation', 'salaryComponents.salaryComponent', 'bankAccounts'])
            ->where('employment_status', 'ACTIVE')
            ->orderBy('first_name')
            ->get();

        $employeeTakeHomes = $allEmployees->map(function ($emp) use ($today, $daysInMonth, $elapsedDays) {
            $monthlyGross = $emp->salaryComponents
                ->where('salaryComponent.category', 'EARNING')
                ->sum('amount');
            $monthlyDeductions = $emp->salaryComponents
                ->where('salaryComponent.category', 'DEDUCTION')
                ->sum('amount');
            $monthlyNet = max(0, $monthlyGross - $monthlyDeductions);

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

            $recordedDays = $presentPunches + ($halfDayPunches * 0.5) + $approvedLeaves;
            $payableDays = $recordedDays > 0 ? min($elapsedDays, (int)$recordedDays) : $elapsedDays;

            $dailyRate = $daysInMonth > 0 ? round($monthlyNet / $daysInMonth, 2) : 0;
            $dailyGross = $daysInMonth > 0 ? round($monthlyGross / $daysInMonth, 2) : 0;

            $takeHomeByToday = round($dailyRate * $payableDays, 2);
            $grossByToday = round($dailyGross * $payableDays, 2);
            $deductionsByToday = max(0, $grossByToday - $takeHomeByToday);

            return [
                'employee' => $emp,
                'monthly_gross' => $monthlyGross,
                'monthly_deductions' => $monthlyDeductions,
                'monthly_net' => $monthlyNet,
                'daily_rate' => $dailyRate,
                'payable_days' => $payableDays,
                'take_home_by_today' => $takeHomeByToday,
                'gross_by_today' => $grossByToday,
                'deductions_by_today' => $deductionsByToday,
                'bank_account' => $emp->bankAccounts->first(),
            ];
        });

        // Personal Take-Home details for logged in user (if employee)
        $mySalaryComponents = collect();
        $myTakeHomeData = null;

        if ($user->employee) {
            $mySalaryComponents = EmployeeSalaryComponent::with('salaryComponent')
                ->where('employee_id', $user->employee->id)
                ->get();

            $myGross = $mySalaryComponents->where('salaryComponent.category', 'EARNING')->sum('amount');
            $myDeductions = $mySalaryComponents->where('salaryComponent.category', 'DEDUCTION')->sum('amount');
            $myNet = max(0, $myGross - $myDeductions);

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

            $myRecordedDays = $myPresent + ($myHalfDay * 0.5) + $myLeaves;
            $myPayableDays = $myRecordedDays > 0 ? min($elapsedDays, (int)$myRecordedDays) : $elapsedDays;

            $myDailyRate = $daysInMonth > 0 ? round($myNet / $daysInMonth, 2) : 0;
            $myTakeHomeByToday = round($myDailyRate * $myPayableDays, 2);
            $myGrossByToday = $daysInMonth > 0 ? round(($myGross / $daysInMonth) * $myPayableDays, 2) : 0;
            $myDeductionsByToday = max(0, $myGrossByToday - $myTakeHomeByToday);

            $myTakeHomeData = [
                'monthly_gross' => $myGross,
                'monthly_deductions' => $myDeductions,
                'monthly_net' => $myNet,
                'daily_rate' => $myDailyRate,
                'payable_days' => $myPayableDays,
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
            'workforceTakeHomeByToday',
            'workforceGrossByToday',
            'workforceDeductionsByToday',
            'employeeTakeHomes',
            'myTakeHomeData'
        ));
    }
}
