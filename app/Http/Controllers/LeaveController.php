<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeaveController extends Controller
{
    /**
     * Display leave requests, leave quotas, and applications.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');
        $isDeptManager = $user->hasRole('manager') || $user->hasRole('department-manager') || $user->hasRole('pm');

        $statusFilter = $request->input('status', 'ALL');

        if ($isSuperAdmin || $isHr) {
            $query = LeaveRequest::with(['employee.department', 'employee.manager', 'leaveType', 'reviewer']);
            if ($statusFilter !== 'ALL') {
                $query->where('status', $statusFilter);
            }
            $leaveRequests = $query->latest()->paginate(10)->withQueryString();
        } elseif ($isDeptManager && $user->employee?->department_id) {
            // Department Manager sees leave requests of employees in their department
            $query = LeaveRequest::with(['employee.department', 'employee.manager', 'leaveType', 'reviewer'])
                ->whereHas('employee', function ($q) use ($user) {
                    $q->where('department_id', $user->employee->department_id);
                });
            if ($statusFilter !== 'ALL') {
                $query->where('status', $statusFilter);
            }
            $leaveRequests = $query->latest()->paginate(10)->withQueryString();
        } else {
            $empId = $user->employee?->id;
            $query = LeaveRequest::with(['employee.manager', 'leaveType', 'reviewer'])->where('employee_id', $empId);
            if ($statusFilter !== 'ALL') {
                $query->where('status', $statusFilter);
            }
            $leaveRequests = $query->latest()->paginate(10)->withQueryString();
        }

        // Leave master data
        $leaveTypes = LeaveType::where('is_active', true)->get();

        // Calculate balances for logged in user (if employee)
        $leaveBalances = [];
        if ($user->employee) {
            $empId = $user->employee->id;
            foreach ($leaveTypes as $type) {
                $used = LeaveRequest::where('employee_id', $empId)
                    ->where('leave_type_id', $type->id)
                    ->where('status', 'APPROVED')
                    ->sum('total_days');

                $leaveBalances[$type->code] = [
                    'id' => $type->id,
                    'name' => $type->name,
                    'quota' => (int) $type->annual_quota,
                    'used' => (int) $used,
                    'available' => max(0, (int) $type->annual_quota - (int) $used),
                ];
            }
        }

        // Metrics
        $pendingCount = LeaveRequest::where('status', 'PENDING')->count();
        $approvedCount = LeaveRequest::where('status', 'APPROVED')->count();
        $revokeRequestedCount = LeaveRequest::where('status', 'REVOKE_REQUESTED')->count();
        $rejectedCount = LeaveRequest::where('status', 'REJECTED')->count();

        return view('leaves.index', compact(
            'leaveRequests',
            'leaveTypes',
            'leaveBalances',
            'statusFilter',
            'pendingCount',
            'approvedCount',
            'revokeRequestedCount',
            'rejectedCount',
            'isSuperAdmin',
            'isHr'
        ));
    }

    /**
     * Submit an employee leave application or directly assign leave (HR/Admin).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');
        $isManager = ($isSuperAdmin || $isHr);

        if ($isManager && $request->filled('employee_id')) {
            $employee = Employee::findOrFail($request->input('employee_id'));
            $isDirectGrant = true;
        } else {
            $employee = $user->employee;
            $isDirectGrant = false;
        }

        if (!$employee) {
            return back()->withErrors(['leave' => 'No employee record is linked to this action.']);
        }

        $dateRule = $isManager ? ['required', 'date'] : ['required', 'date', 'after_or_equal:today'];

        $validated = $request->validate([
            'employee_id' => ['nullable', 'exists:employees,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => $dateRule,
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:500'],
            'status' => ['nullable', 'string', 'in:PENDING,APPROVED,REJECTED'],
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $status = $isDirectGrant ? ($request->input('status') ?: 'APPROVED') : 'PENDING';

        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'status' => $status,
            'reviewed_by' => $status === 'APPROVED' ? $user->id : null,
            'reviewed_at' => $status === 'APPROVED' ? now() : null,
            'review_remarks' => $status === 'APPROVED' ? 'Recorded by ' . $user->name : null,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => $status === 'APPROVED' ? 'LEAVE_DIRECTLY_RECORDED' : 'LEAVE_APPLICATION_SUBMITTED',
            'auditable_type' => LeaveRequest::class,
            'auditable_id' => $leave->id,
            'new_values' => ['employee_id' => $employee->id, 'start_date' => $leave->start_date, 'end_date' => $leave->end_date, 'total_days' => $totalDays, 'status' => $status],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $msg = $isDirectGrant 
            ? "Leave for {$employee->full_name} ({$totalDays} day(s)) saved as {$status}."
            : "Your leave application for {$totalDays} day(s) has been submitted for review.";

        return back()->with('success', $msg);
    }

    /**
     * Approve, reject, or revoke a leave request (HR, Super Admin, or Reporting Manager).
     */
    public function review(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $user = Auth::user();
        $employee = $leaveRequest->employee;
        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');
        $isReportingManager = $employee && $user->employee && (
            $user->employee->id === $employee->manager_id ||
            ($user->employee->department_id === $employee->department_id && ($user->hasRole('manager') || $user->hasRole('department-manager') || $user->hasRole('pm')))
        );

        if (!$isSuperAdmin && !$isHr && !$isReportingManager) {
            abort(403, 'Unauthorized action. Only HR and Reporting Managers can review leave requests.');
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:APPROVED,REJECTED,REVOKED'],
            'review_remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $reviewRemarks = $request->input('review_remarks') ?: ($validated['status'] === 'REVOKED' ? "Revocation approved by {$user->name}" : "Reviewed by {$user->name}");
        $oldStatus = $leaveRequest->status;

        $leaveRequest->update([
            'status' => $validated['status'],
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
            'review_remarks' => $reviewRemarks,
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'LEAVE_STATUS_' . $validated['status'],
            'auditable_type' => LeaveRequest::class,
            'auditable_id' => $leaveRequest->id,
            'new_values' => ['status' => $validated['status'], 'review_remarks' => $leaveRequest->review_remarks, 'old_status' => $oldStatus],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $empName = $leaveRequest->employee->full_name ?? 'Employee';
        $statusMsg = $validated['status'] === 'REVOKED' ? 'Revocation Approved (Cancelled)' : $validated['status'];
        return back()->with('success', "Leave application for {$empName} has been updated to {$statusMsg}.");
    }

    /**
     * Request to revoke an existing leave application (Employee, HR, or Super Admin).
     */
    public function revoke(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $user = Auth::user();
        $employee = $user->employee;
        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');
        $isOwner = $employee && $employee->id === $leaveRequest->employee_id;

        if (!$isOwner && !$isSuperAdmin && !$isHr) {
            abort(403, 'Unauthorized action. You can only revoke your own leave applications.');
        }

        if (!in_array($leaveRequest->status, ['PENDING', 'APPROVED', 'REVOKE_REQUESTED'])) {
            return back()->withErrors(['leave' => 'Only Pending or Approved leave applications can be revoked.']);
        }

        $reason = $request->input('revoke_reason', 'Employee requested leave revocation');

        if ($leaveRequest->status === 'PENDING') {
            // Pending requests can be cancelled directly
            $leaveRequest->update([
                'status' => 'REVOKED',
                'review_remarks' => 'Cancelled by employee: ' . $reason,
                'reviewed_at' => now(),
            ]);
            $msg = 'Your pending leave application has been cancelled.';
        } else {
            // Approved leave requires HR & Reporting Manager review to revoke
            $leaveRequest->update([
                'status' => 'REVOKE_REQUESTED',
                'review_remarks' => 'Revocation requested: ' . $reason,
            ]);
            $msg = 'Leave revocation request submitted. Sent to HR and your Reporting Manager for approval.';
        }

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'LEAVE_REVOCATION_REQUESTED',
            'auditable_type' => LeaveRequest::class,
            'auditable_id' => $leaveRequest->id,
            'new_values' => ['status' => $leaveRequest->status, 'remarks' => $leaveRequest->review_remarks],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', $msg);
    }
}
