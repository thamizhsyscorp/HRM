<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeSalaryComponent;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\SalaryComponent;
use App\Models\WorkLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the application dashboard tailored to the user's role.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');
        $isDeptManager = $user->hasRole('manager') || $user->hasRole('department-manager') || $user->hasRole('pm');
        $isEmployee = $user->hasRole('employee');

        // General Counts (Workforce staff requiring attendance, excluding Super Admin)
        $staffCount = Employee::whereDoesntHave('user.roles', fn($q) => $q->where('slug', 'super-admin'))->count();
        $totalEmployees = $staffCount > 0 ? $staffCount : Employee::count();
        $today = Carbon::today();
        $daysInMonth = $today->daysInMonth;
        $elapsedDays = $today->day;
        
        // Present today count
        $presentToday = Attendance::whereDate('attendance_date', $today)
            ->where('status', 'PRESENT')
            ->count();

        $pendingLeavesCount = LeaveRequest::where('status', 'PENDING')->count();
        $departmentsCount = Department::where('is_active', true)->count();

        // Employees on Leave Today (Approved leave covering today)
        $todayDate = $today->toDateString();
        $employeesOnLeaveToday = LeaveRequest::with(['employee.department', 'employee.designation', 'employee.manager', 'leaveType'])
            ->where('status', 'APPROVED')
            ->whereDate('start_date', '<=', $todayDate)
            ->whereDate('end_date', '>=', $todayDate)
            ->get();
        $onLeaveTodayCount = $employeesOnLeaveToday->count();

        // Turnout Rate
        $turnoutRate = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0;

        // Scoping for Department Managers
        if ($isDeptManager && !$isSuperAdmin && !$isHr && $user->employee?->department_id) {
            $deptId = $user->employee->department_id;
            $totalEmployees = Employee::where('department_id', $deptId)->count();
            $presentToday = Attendance::whereDate('attendance_date', $today)
                ->where('status', 'PRESENT')
                ->whereHas('employee', fn($q) => $q->where('department_id', $deptId))
                ->count();
            $pendingLeavesCount = LeaveRequest::where('status', 'PENDING')
                ->whereHas('employee', fn($q) => $q->where('department_id', $deptId))
                ->count();
            $employeesOnLeaveToday = LeaveRequest::with(['employee.department', 'employee.designation', 'employee.manager', 'leaveType'])
                ->where('status', 'APPROVED')
                ->whereDate('start_date', '<=', $todayDate)
                ->whereDate('end_date', '>=', $todayDate)
                ->whereHas('employee', fn($q) => $q->where('department_id', $deptId))
                ->get();
            $onLeaveTodayCount = $employeesOnLeaveToday->count();
            $turnoutRate = $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0;
        }

        // Dropdown & Form master data
        $departments = Department::where('is_active', true)->withCount('employees')->orderBy('name')->get();
        $designations = Designation::where('is_active', true)->orderBy('name')->get();
        $managers = Employee::where('employment_status', 'ACTIVE')->orderBy('first_name')->get();
        $workLocations = WorkLocation::where('is_active', true)->get();
        $salaryComponents = SalaryComponent::where('is_active', true)->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $nextEmployeeCode = 'EMP-' . str_pad(Employee::withTrashed()->count() + 1, 3, '0', STR_PAD_LEFT);
        $latestAnnouncement = Announcement::with('author')->latest()->first();

        // Total Monthly Earnings & Deductions across workforce
        $totalMonthlyEarnings = EmployeeSalaryComponent::whereHas('salaryComponent', fn($q) => $q->where('category', 'EARNING'))->sum('amount');
        $totalMonthlyDeductions = EmployeeSalaryComponent::whereHas('salaryComponent', fn($q) => $q->where('category', 'DEDUCTION'))->sum('amount');
        $netMonthlyWorkforce = max(0, $totalMonthlyEarnings - $totalMonthlyDeductions);
        $workforceTakeHomeByToday = $daysInMonth > 0 ? round(($netMonthlyWorkforce / $daysInMonth) * $elapsedDays, 2) : 0;

        // Recent Leave Requests & Activities
        if ($isSuperAdmin || $isHr) {
            $recentLeaves = LeaveRequest::with(['employee.department', 'employee.manager', 'leaveType'])
                ->latest()
                ->take(6)
                ->get();
            $recentEmployees = Employee::with(['department', 'designation', 'user', 'salaryComponents'])
                ->latest()
                ->take(8)
                ->get();
            $todayAttendances = Attendance::with(['employee.department', 'employee.designation', 'employee.workLocation', 'events'])
                ->whereDate('attendance_date', $today)
                ->latest('updated_at')
                ->get();
        } elseif ($isDeptManager && $user->employee?->department_id) {
            $deptId = $user->employee->department_id;
            $recentLeaves = LeaveRequest::with(['employee.department', 'employee.manager', 'leaveType'])
                ->whereHas('employee', fn($q) => $q->where('department_id', $deptId))
                ->latest()
                ->take(6)
                ->get();
            $recentEmployees = Employee::with(['department', 'designation', 'user', 'salaryComponents'])
                ->where('department_id', $deptId)
                ->latest()
                ->take(8)
                ->get();
            $todayAttendances = Attendance::with(['employee.department', 'employee.designation', 'employee.workLocation', 'events'])
                ->whereDate('attendance_date', $today)
                ->whereHas('employee', fn($q) => $q->where('department_id', $deptId))
                ->latest('updated_at')
                ->get();
        } else {
            $employeeRecord = $user->employee;
            $recentLeaves = $employeeRecord
                ? LeaveRequest::where('employee_id', $employeeRecord->id)
                    ->with('leaveType')
                    ->latest()
                    ->take(6)
                    ->get()
                : collect();
            $recentEmployees = collect();
            $todayAttendances = collect();
        }

        // Today's attendance for the logged-in employee
        $myAttendanceToday = null;
        $myLeaveToday = null;
        $myAttendanceEvents = collect();
        $myPendingLeavesCount = 0;
        $myTotalAvailableLeave = 18;
        $leaveBalances = [
            'CL' => ['name' => 'Casual Leave', 'quota' => 12, 'used' => 0, 'available' => 12],
            'SL' => ['name' => 'Sick Leave', 'quota' => 12, 'used' => 0, 'available' => 12],
            'EL' => ['name' => 'Earned Leave', 'quota' => 15, 'used' => 0, 'available' => 15],
        ];

        // Real-Time Personal Take-Home by Today calculation
        $takeHomeByToday = 0;
        $myDailyRate = 0;
        $myMonthlyNet = 0;
        $myPayableDays = $elapsedDays;

        if ($user->employee) {
            $empId = $user->employee->id;
            $myAttendanceToday = Attendance::where('employee_id', $empId)
                ->whereDate('attendance_date', $today)
                ->first();

            $myLeaveToday = LeaveRequest::where('employee_id', $empId)
                ->whereIn('status', ['APPROVED', 'REVOKE_REQUESTED'])
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->first();

            if ($myAttendanceToday) {
                $myAttendanceEvents = $myAttendanceToday->events()->latest()->take(5)->get();
            }

            // Calculate leave balances
            $myTotalAvailableLeave = 0;
            foreach ($leaveTypes as $type) {
                $used = LeaveRequest::where('employee_id', $empId)
                    ->where('leave_type_id', $type->id)
                    ->where('status', 'APPROVED')
                    ->sum('total_days');

                $avail = max(0, (int) $type->annual_quota - (int) $used);
                $myTotalAvailableLeave += $avail;

                $leaveBalances[$type->code] = [
                    'name' => $type->name,
                    'quota' => (int) $type->annual_quota,
                    'used' => (int) $used,
                    'available' => $avail,
                ];
            }

            $myPendingLeavesCount = LeaveRequest::where('employee_id', $empId)
                ->where('status', 'PENDING')
                ->count();

            // Personal salary calculation
            $myEarnings = EmployeeSalaryComponent::where('employee_id', $empId)
                ->whereHas('salaryComponent', fn($q) => $q->where('category', 'EARNING'))
                ->sum('amount');
            $myDeductions = EmployeeSalaryComponent::where('employee_id', $empId)
                ->whereHas('salaryComponent', fn($q) => $q->where('category', 'DEDUCTION'))
                ->sum('amount');
            $myMonthlyNet = max(0, $myEarnings - $myDeductions);

            // Attended days count this month
            $presentPunches = Attendance::where('employee_id', $empId)
                ->whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->whereIn('status', ['PRESENT', 'LATE'])
                ->count();
            $halfDayPunches = Attendance::where('employee_id', $empId)
                ->whereMonth('attendance_date', $today->month)
                ->whereYear('attendance_date', $today->year)
                ->where('status', 'HALF_DAY')
                ->count();
            $approvedLeavesMonth = LeaveRequest::where('employee_id', $empId)
                ->where('status', 'APPROVED')
                ->whereMonth('start_date', $today->month)
                ->whereYear('start_date', $today->year)
                ->sum('total_days');

            $recordedDays = $presentPunches + ($halfDayPunches * 0.5) + $approvedLeavesMonth;
            $myPayableDays = $recordedDays > 0 ? min($elapsedDays, (int)$recordedDays) : $elapsedDays;

            $myDailyRate = $daysInMonth > 0 ? round($myMonthlyNet / $daysInMonth, 2) : 0;
            $takeHomeByToday = round($myDailyRate * $myPayableDays, 2);
        } else {
            // For admin/HR without employee record, default to workforce average
            $takeHomeByToday = $workforceTakeHomeByToday;
        }

        return view('dashboard.index', compact(
            'user',
            'isSuperAdmin',
            'isHr',
            'isEmployee',
            'totalEmployees',
            'presentToday',
            'pendingLeavesCount',
            'departmentsCount',
            'departments',
            'designations',
            'managers',
            'workLocations',
            'salaryComponents',
            'leaveTypes',
            'nextEmployeeCode',
            'recentLeaves',
            'recentEmployees',
            'todayAttendances',
            'myAttendanceToday',
            'myLeaveToday',
            'myAttendanceEvents',
            'leaveBalances',
            'latestAnnouncement',
            'daysInMonth',
            'elapsedDays',
            'takeHomeByToday',
            'myDailyRate',
            'myMonthlyNet',
            'myPayableDays',
            'myPendingLeavesCount',
            'myTotalAvailableLeave',
            'workforceTakeHomeByToday',
            'totalMonthlyEarnings',
            'employeesOnLeaveToday',
            'onLeaveTodayCount',
            'turnoutRate'
        ));
    }
}
