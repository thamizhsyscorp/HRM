<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceEvent;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display attendance logs with tailored views for HR and employees.
     * HR: Shows every employee's attendance today (with date, department, status filters).
     * Employees: Shows their everyday attendance for this month (with date range filters).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');
        $isDeptManager = $user->hasRole('manager') || $user->hasRole('department-manager') || $user->hasRole('pm');
        $isManager = ($isSuperAdmin || $isHr || $isDeptManager);

        $statusFilter = $request->input('status');

        if (!$isManager) {
            // EMPLOYEE VIEW: Everyday attendance of this month with "start_date to end_date" filters
            if (!$user->employee) {
                abort(404, 'No employee record linked to user account.');
            }
            $targetEmployee = $user->employee;
            $empId = $targetEmployee->id;

            $startDate = $request->input('start_date', today()->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', today()->toDateString());

            // Support single-date queries if passed
            if ($request->filled('date') && !$request->filled('start_date')) {
                $startDate = $request->input('date');
                $endDate = $request->input('date');
            }

            $query = Attendance::with(['events', 'employee.workLocation'])
                ->where('employee_id', $empId)
                ->whereBetween('attendance_date', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            $attendances = $query->orderBy('attendance_date', 'desc')->get();

            // Employee Summary Metrics for the date range
            $employeeDaysCount = $attendances->count();
            $employeePresentCount = $attendances->whereIn('status', ['PRESENT', 'LATE', 'HALF_DAY'])->count();
            $employeeTotalMinutes = $attendances->sum('worked_minutes');
            $employeeTotalHours = round($employeeTotalMinutes / 60, 1);
            $employeeTurnoutRate = $employeeDaysCount > 0 ? round(($employeePresentCount / $employeeDaysCount) * 100, 1) : 0;

            $selectedDate = $endDate;
            $departments = collect();
            $departmentId = null;
            $totalActiveEmployees = 1;
            $presentCount = $employeePresentCount;
            $activePunchesCount = 0;
        } else {
            // HR / SUPER ADMIN / MANAGER VIEW: Every employee's attendance today (or selected date)
            $selectedDate = $request->input('date', today()->toDateString());
            $startDate = $selectedDate;
            $endDate = $selectedDate;
            $departmentId = $request->input('department_id');

            // If Department Manager, scope to their department
            if ($isDeptManager && !$isSuperAdmin && !$isHr && $user->employee?->department_id) {
                $departmentId = $user->employee->department_id;
                $departments = Department::where('id', $departmentId)->get();
            } else {
                $departments = Department::where('is_active', true)->orderBy('name')->get();
            }

            $query = Attendance::with(['employee.department', 'employee.designation', 'employee.workLocation', 'events'])
                ->whereDate('attendance_date', $selectedDate);

            if ($departmentId) {
                $query->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            $attendances = $query->latest('first_check_in_at')->get();

            if ($departmentId) {
                $totalActiveEmployees = Employee::where('employment_status', 'ACTIVE')->where('department_id', $departmentId)->count();
                $presentCount = Attendance::whereDate('attendance_date', $selectedDate)
                    ->where('status', 'PRESENT')
                    ->whereHas('employee', fn($q) => $q->where('department_id', $departmentId))
                    ->count();
                $activePunchesCount = Attendance::whereDate('attendance_date', $selectedDate)
                    ->whereNotNull('first_check_in_at')
                    ->whereNull('last_check_out_at')
                    ->whereHas('employee', fn($q) => $q->where('department_id', $departmentId))
                    ->count();
            } else {
                $totalActiveEmployees = Employee::where('employment_status', 'ACTIVE')->count();
                $presentCount = Attendance::whereDate('attendance_date', $selectedDate)
                    ->where('status', 'PRESENT')
                    ->count();
                $activePunchesCount = Attendance::whereDate('attendance_date', $selectedDate)
                    ->whereNotNull('first_check_in_at')
                    ->whereNull('last_check_out_at')
                    ->count();
            }

            $employeeDaysCount = 0;
            $employeePresentCount = 0;
            $employeeTotalHours = 0;
            $employeeTurnoutRate = 0;
            $targetEmployee = null;
        }

        return view('attendance.index', compact(
            'attendances',
            'selectedDate',
            'startDate',
            'endDate',
            'departmentId',
            'statusFilter',
            'departments',
            'totalActiveEmployees',
            'presentCount',
            'activePunchesCount',
            'employeeDaysCount',
            'employeePresentCount',
            'employeeTotalHours',
            'employeeTurnoutRate',
            'targetEmployee',
            'isSuperAdmin',
            'isHr'
        ));
    }

    /**
     * Record employee check-in.
     */
    public function checkIn(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->withErrors(['attendance' => 'No employee record is linked to your user account.']);
        }

        $validated = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ], [
            'latitude.required' => 'GPS location is mandatory to check in. Please allow browser location access.',
            'longitude.required' => 'GPS location is mandatory to check in. Please allow browser location access.',
        ]);

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($attendance && $attendance->first_check_in_at && !$attendance->last_check_out_at) {
            return back()->with('status', 'You are already checked in for today.');
        }

        if (!$attendance) {
            $shiftStart = Carbon::parse($today->toDateString() . ' 09:30:00');
            $lateMinutes = now()->gt($shiftStart) ? max(0, $shiftStart->diffInMinutes(now())) : 0;

            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'attendance_date' => $today,
                'first_check_in_at' => now(),
                'status' => 'PRESENT',
                'late_minutes' => $lateMinutes,
                'is_manual' => false,
            ]);
        } else {
            $attendance->update([
                'status' => 'PRESENT',
                'last_check_out_at' => null,
            ]);
        }

        AttendanceEvent::create([
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id,
            'event_type' => 'CHECK_IN',
            'recorded_at' => now(),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'location_accuracy_meters' => $request->input('accuracy'),
            'within_geofence' => true,
            'source' => 'WEB',
            'created_by' => $user->id,
            'remarks' => $request->input('remarks', 'Web Dashboard Check-In'),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'ATTENDANCE_CHECK_IN',
            'auditable_type' => Attendance::class,
            'auditable_id' => $attendance->id,
            'new_values' => ['first_check_in_at' => now()->toDateTimeString(), 'status' => $attendance->status],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'You have successfully checked in at ' . now()->format('h:i A') . ' with verified GPS location.');
    }

    /**
     * Record employee check-out.
     */
    public function checkOut(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->withErrors(['attendance' => 'No employee record is linked to your user account.']);
        }

        $validated = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'accuracy' => ['nullable', 'numeric'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ], [
            'latitude.required' => 'GPS location is mandatory to check out. Please allow browser location access.',
            'longitude.required' => 'GPS location is mandatory to check out. Please allow browser location access.',
        ]);

        $today = Carbon::today();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->first_check_in_at) {
            return back()->withErrors(['attendance' => 'You must check in before checking out.']);
        }

        $checkInTime = Carbon::parse($attendance->first_check_in_at);
        $workedMinutes = max(0, $checkInTime->diffInMinutes(now()));

        $attendance->update([
            'last_check_out_at' => now(),
            'worked_minutes' => $workedMinutes,
        ]);

        AttendanceEvent::create([
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id,
            'event_type' => 'CHECK_OUT',
            'recorded_at' => now(),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'location_accuracy_meters' => $request->input('accuracy'),
            'within_geofence' => true,
            'source' => 'WEB',
            'created_by' => $user->id,
            'remarks' => $request->input('remarks', 'Web Dashboard Check-Out'),
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'ATTENDANCE_CHECK_OUT',
            'auditable_type' => Attendance::class,
            'auditable_id' => $attendance->id,
            'new_values' => ['last_check_out_at' => now()->toDateTimeString(), 'worked_minutes' => $workedMinutes],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $hours = round($workedMinutes / 60, 1);
        return back()->with('success', "You have checked out at " . now()->format('h:i A') . ". Total time worked today: {$hours} hrs.");
    }

    /**
     * Update an employee's check-in/out time and status (HR / Admin only).
     */
    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager')) {
            abort(403, 'Unauthorized action. Only HR and Administrators can modify attendance logs.');
        }

        $validated = $request->validate([
            'first_check_in_time' => ['nullable', 'string'],
            'last_check_out_time' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:PRESENT,HALF_DAY,LATE,ABSENT,PENDING'],
            'admin_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $dateStr = Carbon::parse($attendance->attendance_date)->toDateString();
        $firstCheckIn = null;
        $lastCheckOut = null;
        $workedMinutes = 0;

        if (!empty($validated['first_check_in_time'])) {
            $firstCheckIn = Carbon::parse($dateStr . ' ' . $validated['first_check_in_time']);
        }

        if (!empty($validated['last_check_out_time'])) {
            $lastCheckOut = Carbon::parse($dateStr . ' ' . $validated['last_check_out_time']);
        }

        if ($firstCheckIn && $lastCheckOut) {
            $workedMinutes = max(0, $firstCheckIn->diffInMinutes($lastCheckOut));
        } elseif ($firstCheckIn && $attendance->worked_minutes) {
            $workedMinutes = $attendance->worked_minutes;
        }

        $oldValues = [
            'first_check_in_at' => (string) $attendance->first_check_in_at,
            'last_check_out_at' => (string) $attendance->last_check_out_at,
            'status' => $attendance->status,
        ];

        $attendance->update([
            'first_check_in_at' => $firstCheckIn,
            'last_check_out_at' => $lastCheckOut,
            'worked_minutes' => $workedMinutes,
            'status' => $validated['status'],
            'is_manual' => true,
            'admin_remarks' => $validated['admin_remarks'] ?: 'Attendance time adjusted by HR/Admin',
            'modified_by' => $user->id,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'ATTENDANCE_TIME_EDITED_BY_HR',
            'auditable_type' => Attendance::class,
            'auditable_id' => $attendance->id,
            'old_values' => $oldValues,
            'new_values' => [
                'first_check_in_at' => (string) $firstCheckIn,
                'last_check_out_at' => (string) $lastCheckOut,
                'status' => $validated['status'],
                'admin_remarks' => $attendance->admin_remarks,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $empName = $attendance->employee->full_name ?? 'Employee';
        return back()->with('success', "Attendance record for {$empName} on {$dateStr} was updated successfully by HR.");
    }

    /**
     * Display Monthly Attendance Calendar for employees & HR.
     */
    public function calendar(Request $request): View
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        if ($month < 1) { $month = 12; $year--; }
        if ($month > 12) { $month = 1; $year++; }

        $activeEmployees = collect();
        if ($isSuperAdmin || $isHr) {
            $activeEmployees = Employee::with('department')->where('employment_status', 'ACTIVE')->orderBy('first_name')->get();
            $targetEmployeeId = $request->input('employee_id');
            if ($targetEmployeeId) {
                $targetEmployee = Employee::find($targetEmployeeId);
            } else {
                $targetEmployee = $user->employee ?: $activeEmployees->first();
            }
        } else {
            $targetEmployee = $user->employee;
        }

        if (!$targetEmployee) {
            abort(404, 'No employee record linked to view calendar.');
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $daysInMonth = $startDate->daysInMonth;
        $firstDayOfWeek = $startDate->dayOfWeek; // 0 (Sun) to 6 (Sat)

        // Holidays in this month
        $holidays = Holiday::whereYear('holiday_date', $year)
            ->whereMonth('holiday_date', $month)
            ->get()
            ->keyBy(function ($h) {
                return Carbon::parse($h->holiday_date)->toDateString();
            });

        // Attendances in this month
        $attendances = Attendance::where('employee_id', $targetEmployee->id)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->get()
            ->keyBy(function ($a) {
                return Carbon::parse($a->attendance_date)->toDateString();
            });

        // Leave Requests covering dates in this month
        $leaveRequests = LeaveRequest::where('employee_id', $targetEmployee->id)
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhereBetween('end_date', [$startDate->toDateString(), $endDate->toDateString()])
                  ->orWhere(function($sub) use ($startDate, $endDate) {
                      $sub->where('start_date', '<=', $startDate->toDateString())
                          ->where('end_date', '>=', $endDate->toDateString());
                  });
            })
            ->with('leaveType')
            ->get();

        $today = Carbon::today();
        $calendarDays = [];
        $presentCount = 0;
        $absentCount = 0;
        $holidayCount = 0;
        $totalWorkedMinutes = 0;

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = Carbon::createFromDate($year, $month, $day);
            $dateStr = $currentDate->toDateString();
            $dayOfWeek = $currentDate->dayOfWeek;
            $isSunday = ($dayOfWeek === 0);
            $isHoliday = $holidays->has($dateStr);
            $holidayObj = $isHoliday ? $holidays->get($dateStr) : null;
            $att = $attendances->get($dateStr);

            // Check if there is an applied leave for this day
            $leaveMatch = $leaveRequests->first(function($lr) use ($dateStr) {
                return $dateStr >= Carbon::parse($lr->start_date)->toDateString() 
                    && $dateStr <= Carbon::parse($lr->end_date)->toDateString();
            });

            $dayStatus = 'FUTURE';
            $color = 'gray';
            $label = 'Upcoming';
            $details = null;
            $leaveReason = null;
            $leaveType = null;
            $leaveStatus = null;

            if ($leaveMatch) {
                $leaveReason = $leaveMatch->reason;
                $leaveType = $leaveMatch->leaveType->name ?? 'Leave';
                $leaveStatus = $leaveMatch->status;
            }

            if ($isHoliday || $isSunday) {
                $dayStatus = 'HOLIDAY';
                $color = 'yellow';
                $label = $holidayObj ? $holidayObj->name : 'Sunday Off';
                $holidayCount++;
            } elseif ($att && (in_array($att->status, ['PRESENT', 'HALF_DAY', 'LATE']) || $att->first_check_in_at)) {
                $dayStatus = 'PRESENT';
                $color = 'green';
                $workedHours = round(($att->worked_minutes ?? 0) / 60, 1);
                $label = "Present ({$workedHours}h)";
                $presentCount++;
                $totalWorkedMinutes += ($att->worked_minutes ?? 0);
                $details = [
                    'check_in' => $att->first_check_in_at ? Carbon::parse($att->first_check_in_at)->format('h:i A') : '—',
                    'check_out' => $att->last_check_out_at ? Carbon::parse($att->last_check_out_at)->format('h:i A') : ($att->first_check_in_at ? 'Active' : '—'),
                    'worked_hours' => $workedHours,
                    'status' => $att->status,
                    'is_manual' => $att->is_manual,
                ];
            } elseif ($currentDate->lte($today)) {
                $dayStatus = 'ABSENT';
                $color = 'red';
                $label = $leaveMatch ? "Absent ({$leaveType})" : 'Absent';
                $absentCount++;

                if (!$leaveReason) {
                    if ($att && $att->admin_remarks) {
                        $leaveReason = $att->admin_remarks;
                    } else {
                        $leaveReason = 'Unexcused Absence (No check-in punch or leave application registered)';
                    }
                }
            } else {
                $dayStatus = 'FUTURE';
                $color = 'gray';
                $label = 'Upcoming';
            }

            $calendarDays[] = [
                'day' => $day,
                'date' => $dateStr,
                'dayOfWeek' => $dayOfWeek,
                'dayName' => $currentDate->format('D'),
                'isToday' => $currentDate->isToday(),
                'status' => $dayStatus,
                'color' => $color,
                'label' => $label,
                'holiday' => $holidayObj ? [
                    'id' => $holidayObj->id,
                    'name' => $holidayObj->name,
                    'date' => $dateStr,
                    'type' => $holidayObj->type,
                    'is_optional' => (bool) $holidayObj->is_optional,
                    'description' => $holidayObj->description,
                ] : null,
                'details' => $details,
                'leave_reason' => $leaveReason,
                'leave_type' => $leaveType,
                'leave_status' => $leaveStatus,
            ];
        }

        $totalWorkedHours = round($totalWorkedMinutes / 60, 1);
        $monthName = $startDate->format('F Y');
        $leaveTypes = \App\Models\LeaveType::where('is_active', true)->get();

        $prevMonthDate = $startDate->copy()->subMonth();
        $nextMonthDate = $startDate->copy()->addMonth();

        return view('attendance.calendar', compact(
            'targetEmployee',
            'activeEmployees',
            'isSuperAdmin',
            'isHr',
            'year',
            'month',
            'monthName',
            'firstDayOfWeek',
            'daysInMonth',
            'calendarDays',
            'presentCount',
            'absentCount',
            'holidayCount',
            'totalWorkedHours',
            'leaveTypes',
            'prevMonthDate',
            'nextMonthDate'
        ));
    }
}
