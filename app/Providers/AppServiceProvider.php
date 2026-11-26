<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\LeaveRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || $this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $isSuperAdmin = $user->hasRole('super-admin');
                $isHr = $user->hasRole('hr-manager');
                $isManager = $user->hasRole('manager') || $user->hasRole('department-manager') || $user->hasRole('pm');

                // 1. Pending Leave Approvals & Revocations Count & Quick List
                if ($isSuperAdmin || $isHr) {
                    $badgePendingLeaves = LeaveRequest::where('status', 'PENDING')->count();
                    $badgePendingRevocations = LeaveRequest::where('status', 'REVOKE_REQUESTED')->count();
                    $pendingLeaveItems = LeaveRequest::with(['employee.department', 'leaveType'])
                        ->whereIn('status', ['PENDING', 'REVOKE_REQUESTED'])
                        ->latest()
                        ->take(4)
                        ->get();
                } elseif ($isManager && $user->employee?->department_id) {
                    $deptId = $user->employee->department_id;
                    $badgePendingLeaves = LeaveRequest::where('status', 'PENDING')
                        ->whereHas('employee', fn($q) => $q->where('department_id', $deptId))
                        ->count();
                    $badgePendingRevocations = LeaveRequest::where('status', 'REVOKE_REQUESTED')
                        ->whereHas('employee', fn($q) => $q->where('department_id', $deptId))
                        ->count();
                    $pendingLeaveItems = LeaveRequest::with(['employee.department', 'leaveType'])
                        ->whereIn('status', ['PENDING', 'REVOKE_REQUESTED'])
                        ->whereHas('employee', fn($q) => $q->where('department_id', $deptId))
                        ->latest()
                        ->take(4)
                        ->get();
                } else {
                    $empId = $user->employee?->id;
                    $badgePendingLeaves = $empId ? LeaveRequest::where('employee_id', $empId)->where('status', 'PENDING')->count() : 0;
                    $badgePendingRevocations = $empId ? LeaveRequest::where('employee_id', $empId)->where('status', 'REVOKE_REQUESTED')->count() : 0;
                    $pendingLeaveItems = $empId ? LeaveRequest::with(['employee.department', 'leaveType'])
                        ->where('employee_id', $empId)
                        ->whereIn('status', ['PENDING', 'REVOKE_REQUESTED'])
                        ->latest()
                        ->take(4)
                        ->get() : collect();
                }

                // 2. Announcements Count & Recent Bulletins
                $badgeRecentAnnouncements = Announcement::where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->where('created_at', '>=', now()->subDays(14))
                ->count();

                if ($badgeRecentAnnouncements === 0) {
                    $badgeRecentAnnouncements = min(3, Announcement::count());
                }

                $recentAnnouncementItems = Announcement::latest()
                    ->take(3)
                    ->get();

                // 3. Total Notification count for Bell Badge
                $badgeTotalNotifications = $badgePendingLeaves + $badgePendingRevocations + $badgeRecentAnnouncements;

                $view->with([
                    'badgePendingLeaves' => $badgePendingLeaves,
                    'badgePendingRevocations' => $badgePendingRevocations,
                    'badgeRecentAnnouncements' => $badgeRecentAnnouncements,
                    'badgeTotalNotifications' => $badgeTotalNotifications,
                    'badgePendingLeaveItems' => $pendingLeaveItems,
                    'badgeRecentAnnouncementItems' => $recentAnnouncementItems,
                ]);
            } else {
                $view->with([
                    'badgePendingLeaves' => 0,
                    'badgePendingRevocations' => 0,
                    'badgeRecentAnnouncements' => 0,
                    'badgeTotalNotifications' => 0,
                    'badgePendingLeaveItems' => collect(),
                    'badgeRecentAnnouncementItems' => collect(),
                ]);
            }
        });
    }
}
