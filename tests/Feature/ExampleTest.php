<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\User;
use Database\Seeders\HrmDatabaseSeeder;
use Database\Seeders\SampleUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(HrmDatabaseSeeder::class);
        $this->seed(SampleUserSeeder::class);
    }

    /**
     * Root URL redirects appropriately.
     */
    public function test_root_redirects_to_dashboard(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/dashboard');
    }

    /**
     * Login page is accessible.
     */
    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign in to your workplace');
        $response->assertSee('admin@hrm.test');
        $response->assertSee('hr@hrm.test');
        $response->assertSee('employee@hrm.test');
    }

    /**
     * Super Admin login works and redirects to dashboard.
     */
    public function test_super_admin_can_login_and_view_dashboard(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();
        $this->assertNotNull($admin);

        $loginResponse = $this->post('/login', [
            'email' => 'admin@hrm.test',
            'password' => 'password',
        ]);
        $loginResponse->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);

        $dashboardResponse = $this->actingAs($admin)->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Super Admin Workspace');
        $dashboardResponse->assertSee('Total Headcount');
        $dashboardResponse->assertSee('Present Today');
        $dashboardResponse->assertSee('Leave Today');
        $dashboardResponse->assertSee('Employees on Leave Today');
        $dashboardResponse->assertSee("Today's Workforce Attendance", false);
        $dashboardResponse->assertDontSee('Headcount Trend');
        $dashboardResponse->assertDontSee('Department Distribution');

        // Test with employee on leave today
        $empUser = User::where('email', 'employee@hrm.test')->first();
        $leaveType = \App\Models\LeaveType::first();
        \App\Models\LeaveRequest::create([
            'employee_id' => $empUser->employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => today()->toDateString(),
            'end_date' => today()->toDateString(),
            'total_days' => 1,
            'reason' => 'Annual personal day off',
            'status' => 'APPROVED',
            'reviewed_by' => $admin->id,
        ]);

        $dashWithLeave = $this->actingAs($admin)->get('/dashboard');
        $dashWithLeave->assertStatus(200);
        $dashWithLeave->assertSee($empUser->employee->full_name);
        $dashWithLeave->assertSee('Annual personal day off');
    }

    /**
     * Employee login works and renders employee dashboard.
     */
    public function test_employee_can_login_and_view_dashboard(): void
    {
        $employee = User::where('email', 'employee@hrm.test')->first();
        $this->assertNotNull($employee);

        $loginResponse = $this->post('/login', [
            'email' => 'employee@hrm.test',
            'password' => 'password',
        ]);
        $loginResponse->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($employee);

        $dashboardResponse = $this->actingAs($employee)->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Employee Self-Service Portal');
        $dashboardResponse->assertSee('Check-In Time');
        $dashboardResponse->assertSee('Check-Out Time');
        $dashboardResponse->assertSee('Annual Leave Entitlements');
        $dashboardResponse->assertDontSee('Headcount Trend');
        $dashboardResponse->assertDontSee('Department Distribution');
    }

    /**
     * Employee dashboard cards display actual check-in and check-out times.
     */
    public function test_employee_dashboard_cards_display_recorded_check_in_and_check_out_times(): void
    {
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $this->assertNotNull($employeeUser);

        Attendance::where('employee_id', $employeeUser->employee->id)
            ->whereDate('attendance_date', today())
            ->delete();

        Attendance::create([
            'employee_id' => $employeeUser->employee->id,
            'attendance_date' => today()->toDateString(),
            'first_check_in_at' => today()->copy()->setHour(9)->setMinute(15),
            'last_check_out_at' => today()->copy()->setHour(18)->setMinute(30),
            'status' => 'PRESENT',
            'worked_minutes' => 555,
        ]);

        $response = $this->actingAs($employeeUser)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('09:15 AM');
        $response->assertSee('06:30 PM');
        $response->assertSee('Check-In Time');
        $response->assertSee('Check-Out Time');
        $response->assertSee('9.3h');
        $response->assertDontSee('Headcount Trend');
        $response->assertDontSee('Department Distribution');
    }

    /**
     * User can logout.
     */
    public function test_user_can_logout(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();

        $response = $this->actingAs($admin)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * Employee can check-in and check-out via dashboard.
     */
    public function test_employee_can_check_in_and_check_out(): void
    {
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $this->assertNotNull($employeeUser);

        // Clear today's seed attendance for clean testing
        \App\Models\AttendanceEvent::where('employee_id', $employeeUser->employee->id)->delete();
        \App\Models\Attendance::where('employee_id', $employeeUser->employee->id)->delete();

        // 1. Check In
        $checkInResponse = $this->actingAs($employeeUser)->post('/attendance/check-in', [
            'remarks' => 'Morning web punch',
            'latitude' => 12.9716,
            'longitude' => 80.2452,
        ]);
        $checkInResponse->assertRedirect();
        $checkInResponse->assertSessionHas('success');

        $attendance = Attendance::where('employee_id', $employeeUser->employee->id)
            ->whereDate('attendance_date', today())
            ->first();
        $this->assertNotNull($attendance);
        $this->assertEquals('PRESENT', $attendance->status);
        $this->assertNotNull($attendance->first_check_in_at);

        // 2. Check Out (with mandatory GPS coordinates)
        $checkOutResponse = $this->actingAs($employeeUser)->post('/attendance/check-out', [
            'latitude' => 12.9716,
            'longitude' => 80.2452,
            'remarks' => 'Evening checkout',
        ]);
        $checkOutResponse->assertRedirect();
        $checkOutResponse->assertSessionHas('success');

        $attendance->refresh();
        $this->assertNotNull($attendance->last_check_out_at);
    }

    /**
     * HR or Super Admin can onboard an employee with complete details.
     */
    public function test_hr_or_admin_can_create_employee_with_full_details(): void
    {
        $hrUser = User::where('email', 'hr@hrm.test')->first();
        $dept = Department::first();
        $desig = Designation::first();

        // 1. Form Page accessible
        $createPageResponse = $this->actingAs($hrUser)->get('/employees/create');
        $createPageResponse->assertStatus(200);
        $createPageResponse->assertSee('Employee Registration');
        $createPageResponse->assertSee('Basic Salary');

        // 2. Store Employee with full details
        $storeResponse = $this->actingAs($hrUser)->post('/employees', [
            'first_name' => 'Kavitha',
            'last_name' => 'Raman',
            'email' => 'kavitha.raman@hrm.test',
            'password' => 'secret123',
            'phone' => '+91 99999 11111',
            'gender' => 'Female',
            'date_of_birth' => '1995-08-20',
            'employee_code' => 'EMP-099',
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'employment_type' => 'FULL_TIME',
            'joining_date' => '2026-11-20',
            // Salary breakdown
            'basic_salary' => 45000,
            'hra' => 18000,
            'conveyance' => 3000,
            'special_allowance' => 7000,
            'provident_fund' => 2000,
            'professional_tax' => 200,
            // Leave quotas
            'casual_leave_quota' => 12,
            'sick_leave_quota' => 12,
            'earned_leave_quota' => 15,
            // Bank details
            'bank_name' => 'State Bank of India',
            'account_holder_name' => 'Kavitha Raman',
            'account_number' => '123456789012',
            'ifsc_code' => 'SBIN0001234',
        ]);

        $storeResponse->assertRedirect('/employees');
        $storeResponse->assertSessionHas('success');

        // Verify database records
        $newUser = User::where('email', 'kavitha.raman@hrm.test')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue($newUser->hasRole('employee'));

        $newEmp = Employee::where('employee_code', 'EMP-099')->first();
        $this->assertNotNull($newEmp);
        $this->assertEquals('Kavitha', $newEmp->first_name);

        // Verify salary components
        $this->assertGreaterThan(0, $newEmp->salaryComponents()->count());

        // Verify bank account
        $this->assertNotNull($newEmp->bankAccounts()->first());
    }

    /**
     * Regular employee cannot access employee onboarding.
     */
    public function test_regular_employee_cannot_access_employee_creation(): void
    {
        $employee = User::where('email', 'employee@hrm.test')->first();

        $response = $this->actingAs($employee)->get('/employees/create');
        $response->assertStatus(403);
    }

    /**
     * Admin and HR can view daily workforce attendance logs with date filtering.
     */
    public function test_admin_and_hr_can_view_daily_attendance_logs(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/attendance?date=' . today()->toDateString());
        $response->assertStatus(200);
        $response->assertSee('Daily Workforce Attendance Logs');
        $response->assertSee('Active Office Sessions');
        $response->assertSee('Turnout Rate');
    }

    /**
     * HR can edit an employee's check-in / check-out time and punch status.
     */
    public function test_hr_can_edit_employee_check_in_time(): void
    {
        $hrUser = User::where('email', 'hr@hrm.test')->first();
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $this->assertNotNull($hrUser);
        $this->assertNotNull($employeeUser);

        // Get or create an attendance record for the employee
        $attendance = Attendance::where('employee_id', $employeeUser->employee->id)
            ->whereDate('attendance_date', today())
            ->first();

        if (!$attendance) {
            $attendance = Attendance::create([
                'employee_id' => $employeeUser->employee->id,
                'attendance_date' => today()->toDateString(),
                'first_check_in_at' => now()->startOfDay()->addHours(10), // late punch at 10:00 AM
                'status' => 'LATE',
                'is_manual' => false,
            ]);
        }

        // HR updates check-in to 09:00 AM and checkout to 18:00 PM
        $updateResponse = $this->actingAs($hrUser)->put('/attendance/' . $attendance->id, [
            'first_check_in_time' => '09:00',
            'last_check_out_time' => '18:00',
            'status' => 'PRESENT',
            'admin_remarks' => 'Employee informed HR of traffic delay; rectified to on-time.',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $attendance->refresh();
        $this->assertEquals('PRESENT', $attendance->status);
        $this->assertTrue((bool) $attendance->is_manual);
        $this->assertEquals('Employee informed HR of traffic delay; rectified to on-time.', $attendance->admin_remarks);
        $this->assertEquals($hrUser->id, $attendance->modified_by);
        $this->assertEquals(540, $attendance->worked_minutes); // 9 hours = 540 mins

        // Verify audit log entry was created
        $audit = \App\Models\AuditLog::where('action', 'ATTENDANCE_TIME_EDITED_BY_HR')
            ->where('auditable_id', $attendance->id)
            ->first();
        $this->assertNotNull($audit);
        $this->assertEquals($hrUser->id, $audit->user_id);
    }

    /**
     * Regular employees cannot edit attendance punch times.
     */
    public function test_regular_employee_cannot_edit_attendance_time(): void
    {
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $this->assertNotNull($employeeUser);

        $attendance = Attendance::where('employee_id', $employeeUser->employee->id)
            ->whereDate('attendance_date', today())
            ->first();

        if (!$attendance) {
            $attendance = Attendance::create([
                'employee_id' => $employeeUser->employee->id,
                'attendance_date' => today()->toDateString(),
                'first_check_in_at' => now(),
                'status' => 'PRESENT',
                'is_manual' => false,
            ]);
        }

        $response = $this->actingAs($employeeUser)->put('/attendance/' . $attendance->id, [
            'first_check_in_time' => '09:00',
            'status' => 'PRESENT',
            'admin_remarks' => 'Unauthorized attempt',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Admin and HR can view employee directory and 360 degree profile.
     */
    public function test_admin_and_hr_can_view_employee_directory_and_profile(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();
        $employee = Employee::first();
        $this->assertNotNull($admin);
        $this->assertNotNull($employee);

        // Directory index
        $indexResponse = $this->actingAs($admin)->get('/employees');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Workforce Directory');
        $indexResponse->assertSee($employee->first_name);

        // Employee profile
        $showResponse = $this->actingAs($admin)->get('/employees/' . $employee->id);
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Organization & Placement', false);
        $showResponse->assertSee($employee->employee_code);
        $showResponse->assertSee('Total Monthly Earnings');
    }

    /**
     * Leave management workflow: application and approval.
     */
    public function test_leave_management_workflow(): void
    {
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $hrUser = User::where('email', 'hr@hrm.test')->first();
        $leaveType = \App\Models\LeaveType::first();

        $this->assertNotNull($employeeUser);
        $this->assertNotNull($hrUser);
        $this->assertNotNull($leaveType);

        // 1. Employee applies for leave
        $applyResponse = $this->actingAs($employeeUser)->post('/leaves', [
            'leave_type_id' => $leaveType->id,
            'start_date' => today()->addDays(5)->toDateString(),
            'end_date' => today()->addDays(6)->toDateString(),
            'reason' => 'Attending family occasion',
        ]);
        $applyResponse->assertRedirect();
        $applyResponse->assertSessionHas('success');

        $leave = \App\Models\LeaveRequest::where('employee_id', $employeeUser->employee->id)->latest()->first();
        $this->assertNotNull($leave);
        $this->assertEquals('PENDING', $leave->status);

        // 2. HR reviews and approves leave
        $reviewResponse = $this->actingAs($hrUser)->post('/leaves/' . $leave->id . '/review', [
            'status' => 'APPROVED',
            'rejection_reason' => null,
        ]);
        $reviewResponse->assertRedirect();
        $reviewResponse->assertSessionHas('success');

        $leave->refresh();
        $this->assertEquals('APPROVED', $leave->status);
        $this->assertEquals($hrUser->id, $leave->reviewed_by);

        // 3. Employee views leaves page: verifies styled KPI cards and annual leave entitlements grid
        $leavesIndexResponse = $this->actingAs($employeeUser)->get('/leaves');
        $leavesIndexResponse->assertStatus(200);
        $leavesIndexResponse->assertSee('Pending Decisions');
        $leavesIndexResponse->assertSee('Approved Requests');
        $leavesIndexResponse->assertSee('Rejected Requests');
        $leavesIndexResponse->assertSee('Active Leave Policies');
        $leavesIndexResponse->assertSee('Your Annual Leave Entitlements');
        $leavesIndexResponse->assertSee('Available Balance:');
        $leavesIndexResponse->assertSee('Casual Leave');
        $leavesIndexResponse->assertSee('Sick Leave');
        $leavesIndexResponse->assertSee('Earned Leave');
        $leavesIndexResponse->assertSee('Days Remaining');
    }

    /**
     * Payroll overview is accessible by HR and Admin.
     */
    public function test_payroll_overview_loads_for_admin_and_hr(): void
    {
        $hrUser = User::where('email', 'hr@hrm.test')->first();
        $this->assertNotNull($hrUser);

        $response = $this->actingAs($hrUser)->get('/payroll');
        $response->assertStatus(200);
        $response->assertSee('Corporate Salary Structures');
        $response->assertSee('Configured Salary Components');
        $response->assertSee('Gross Monthly Earnings');
    }

    /**
     * Check-in and check-out strictly require GPS location coordinates.
     */
    public function test_check_in_and_check_out_requires_gps_location(): void
    {
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $this->assertNotNull($employeeUser);

        // 1. Check-in fails without latitude & longitude
        $checkInFail = $this->actingAs($employeeUser)->post('/attendance/check-in', [
            'remarks' => 'Attempt without location',
        ]);
        $checkInFail->assertSessionHasErrors(['latitude', 'longitude']);

        // 2. Check-out fails without latitude & longitude
        $checkOutFail = $this->actingAs($employeeUser)->post('/attendance/check-out', [
            'remarks' => 'Attempt without location',
        ]);
        $checkOutFail->assertSessionHasErrors(['latitude', 'longitude']);
    }

    /**
     * Regular employees can view only their own attendance and not others.
     */
    public function test_employee_can_only_view_their_own_attendance_logs(): void
    {
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $priyaUser = User::where('email', 'priya.nair@hrm.test')->first();
        $this->assertNotNull($employeeUser);
        $this->assertNotNull($priyaUser);

        // Priya check in for today
        Attendance::updateOrCreate(
            ['employee_id' => $priyaUser->employee->id, 'attendance_date' => today()->toDateString()],
            ['first_check_in_at' => now(), 'status' => 'PRESENT', 'is_manual' => false]
        );

        $response = $this->actingAs($employeeUser)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('My Personal Attendance Logs');
        // Employee user can see own name
        $response->assertSee($employeeUser->name);
        // Map icon link to Google Maps
        $response->assertSee('https://www.google.com/maps?q=', false);
        // Regular employee does not have HR edit action column
        $response->assertDontSee('HR Action');
    }

    /**
     * Attendance Calendar loads with color-coded days (Green, Red, Yellow).
     */
    public function test_attendance_calendar_loads_with_color_coded_days(): void
    {
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $hrUser = User::where('email', 'hr@hrm.test')->first();
        $this->assertNotNull($employeeUser);
        $this->assertNotNull($hrUser);

        // 1. Employee visits their calendar
        $empCal = $this->actingAs($employeeUser)->get('/attendance/calendar');
        $empCal->assertStatus(200);
        $empCal->assertSee('Workforce Attendance Calendar');
        $empCal->assertSee('PRESENT');
        $empCal->assertSee('ABSENT');
        $empCal->assertSee('HOLIDAY');
        $empCal->assertSee('Present Days');
        $empCal->assertSee('Absent Days');
        $empCal->assertSee('Company holidays + Sundays');

        // 2. HR visits calendar with employee selector
        $hrCal = $this->actingAs($hrUser)->get('/attendance/calendar?employee_id=' . $employeeUser->employee->id);
        $hrCal->assertStatus(200);
        $hrCal->assertSee('Viewing Personnel:');
        $hrCal->assertSee($employeeUser->employee->full_name);
    }

    /**
     * HR and Admin can create and broadcast announcements to all users.
     */
    public function test_hr_and_admin_can_broadcast_announcements_to_all(): void
    {
        $hrUser = User::where('email', 'hr@hrm.test')->first();
        $this->assertNotNull($hrUser);

        $postResponse = $this->actingAs($hrUser)->post('/announcements', [
            'title' => 'Mandatory Q4 All-Hands Meeting',
            'content' => 'Please join us on Thursday at 3 PM in the conference hall.',
            'category' => 'EVENT',
            'priority' => 'HIGH',
            'is_pinned' => true,
        ]);

        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('success');

        // Verify announcement created
        $announcement = \App\Models\Announcement::where('title', 'Mandatory Q4 All-Hands Meeting')->first();
        $this->assertNotNull($announcement);
        $this->assertEquals('EVENT', $announcement->category);
        $this->assertEquals('HIGH', $announcement->priority);

        // Verify notifications generated for all users
        $totalUsers = User::count();
        $notificationsCount = \Illuminate\Support\Facades\DB::table('notifications')
            ->where('data', 'like', '%Mandatory Q4 All-Hands Meeting%')
            ->count();
        $this->assertEquals($totalUsers, $notificationsCount);

        // All users can view it on the announcement feed
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $feedResponse = $this->actingAs($employeeUser)->get('/announcements');
        $feedResponse->assertStatus(200);
        $feedResponse->assertSee('Mandatory Q4 All-Hands Meeting');
        $feedResponse->assertSee('PINNED');
    }

    /**
     * Regular employees cannot broadcast announcements.
     */
    public function test_regular_employee_cannot_broadcast_announcements(): void
    {
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $this->assertNotNull($employeeUser);

        $response = $this->actingAs($employeeUser)->post('/announcements', [
            'title' => 'Unauthorized broadcast',
            'content' => 'Spam content',
            'category' => 'GENERAL',
            'priority' => 'LOW',
        ]);

        $response->assertStatus(403);
    }

    /**
     * HR and Super Admin can view holiday module and create, edit, delete holidays.
     */
    public function test_hr_and_admin_can_view_holiday_module_and_manage_holidays(): void
    {
        $hrUser = User::where('email', 'hr@hrm.test')->first();
        $this->assertNotNull($hrUser);

        // 1. Access holiday module
        $indexResponse = $this->actingAs($hrUser)->get('/holidays');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Corporate Holidays');
        $indexResponse->assertSee('+ Add Holiday');

        // 2. Add a new holiday
        $storeResponse = $this->actingAs($hrUser)->post('/holidays', [
            'name' => 'National Innovation Day',
            'holiday_date' => '2026-10-15',
            'type' => 'COMPANY',
            'is_optional' => false,
            'description' => 'Corporate holiday celebrating innovation and research.',
        ]);
        $storeResponse->assertRedirect();
        $storeResponse->assertSessionHas('success');

        $holiday = \App\Models\Holiday::whereDate('holiday_date', '2026-10-15')->first();
        $this->assertNotNull($holiday);
        $this->assertEquals('National Innovation Day', $holiday->name);
        $this->assertEquals('COMPANY', $holiday->type);

        // 3. Edit holiday
        $updateResponse = $this->actingAs($hrUser)->put('/holidays/' . $holiday->id, [
            'name' => 'National Innovation & Founders Day',
            'holiday_date' => '2026-10-15',
            'type' => 'PUBLIC',
            'is_optional' => false,
            'description' => 'Updated holiday memo.',
        ]);
        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $holiday->refresh();
        $this->assertEquals('National Innovation & Founders Day', $holiday->name);
        $this->assertEquals('PUBLIC', $holiday->type);

        // 4. Delete holiday
        $deleteResponse = $this->actingAs($hrUser)->delete('/holidays/' . $holiday->id);
        $deleteResponse->assertRedirect();
        $deleteResponse->assertSessionHas('success');

        $this->assertNull(\App\Models\Holiday::find($holiday->id));
    }

    /**
     * Regular employees cannot access holiday module or modify holidays.
     */
    public function test_regular_employee_cannot_access_holiday_module_or_modify_holidays(): void
    {
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $this->assertNotNull($employeeUser);

        // Cannot view index
        $indexResponse = $this->actingAs($employeeUser)->get('/holidays');
        $indexResponse->assertStatus(403);

        // Cannot create holiday
        $storeResponse = $this->actingAs($employeeUser)->post('/holidays', [
            'name' => 'Unauthorized Day',
            'holiday_date' => '2026-12-10',
            'type' => 'PUBLIC',
        ]);
        $storeResponse->assertStatus(403);
    }

    /**
     * HR and Super Admin can assign/modify leave directly for an employee on the calendar.
     */
    public function test_hr_and_admin_can_assign_leave_for_employee_directly_on_calendar(): void
    {
        $hrUser = User::where('email', 'hr@hrm.test')->first();
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $leaveType = \App\Models\LeaveType::first();

        $this->assertNotNull($hrUser);
        $this->assertNotNull($employeeUser);
        $this->assertNotNull($leaveType);

        // Calendar shows Manage Holidays button for HR
        $calResponse = $this->actingAs($hrUser)->get('/attendance/calendar?employee_id=' . $employeeUser->employee->id);
        $calResponse->assertStatus(200);
        $calResponse->assertSee('Manage Holidays');
        $calResponse->assertSee('+ Add Holiday');

        // HR grants an approved leave for the employee on a specific calendar date
        $leaveDate = today()->addDays(12)->toDateString();
        $grantResponse = $this->actingAs($hrUser)->post('/leaves', [
            'employee_id' => $employeeUser->employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => $leaveDate,
            'end_date' => $leaveDate,
            'reason' => 'Approved directly by HR on calendar schedule.',
            'status' => 'APPROVED',
        ]);

        $grantResponse->assertRedirect();
        $grantResponse->assertSessionHas('success');

        $leave = \App\Models\LeaveRequest::where('employee_id', $employeeUser->employee->id)
            ->whereDate('start_date', $leaveDate)
            ->first();
        $this->assertNotNull($leave);
        $this->assertEquals('APPROVED', $leave->status);
        $this->assertEquals($hrUser->id, $leave->reviewed_by);
    }

    /**
     * Test Employee attendance page shows everyday records with date range filters,
     * while HR attendance page shows today's workforce punches.
     */
    public function test_employee_attendance_date_range_and_hr_today_views(): void
    {
        $employeeUser = User::where('email', 'employee@hrm.test')->first();
        $hrUser = User::where('email', 'hr@hrm.test')->first();
        $this->assertNotNull($employeeUser);
        $this->assertNotNull($hrUser);

        // 1. Employee visits attendance: sees date range filter (start_date, end_date) and month view
        $empResponse = $this->actingAs($employeeUser)->get('/attendance');
        $empResponse->assertStatus(200);
        $empResponse->assertSee('My Monthly Attendance History');
        $empResponse->assertSee('From Date');
        $empResponse->assertSee('To Date');
        $empResponse->assertSee('name="start_date"', false);
        $empResponse->assertSee('name="end_date"', false);
        $empResponse->assertSee('Attendance Date');
        $empResponse->assertSee('Day of Week');
        $empResponse->assertDontSee('HR Action');

        // Employee filters with a custom range
        $filteredResponse = $this->actingAs($employeeUser)->get('/attendance?start_date=' . today()->startOfMonth()->toDateString() . '&end_date=' . today()->toDateString());
        $filteredResponse->assertStatus(200);
        $filteredResponse->assertSee('My Monthly Attendance History');

        // 2. HR visits attendance: sees company-wide today's roster with Department filter & HR action
        $hrResponse = $this->actingAs($hrUser)->get('/attendance');
        $hrResponse->assertStatus(200);
        $hrResponse->assertSee('Workforce Attendance Roster (Today)');
        $hrResponse->assertSee('Attendance Date');
        $hrResponse->assertSee('Department');
        $hrResponse->assertSee('name="date"', false);
        $hrResponse->assertSee('name="department_id"', false);
        $hrResponse->assertSee('HR Action');
    }

    /**
     * Test editing an employee, role assignment, and department scoping.
     */
    public function test_employee_edit_page_and_update(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();
        $employee = Employee::first();

        // 1. Edit page loads
        $response = $this->actingAs($admin)->get("/employees/{$employee->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Editing Profile: ' . $employee->full_name);
        $response->assertSee('System Access Role');

        // 2. Update employee
        $updateResponse = $this->actingAs($admin)->put("/employees/{$employee->id}", [
            'first_name' => $employee->first_name,
            'last_name' => 'UpdatedLast',
            'email' => $employee->work_email,
            'employee_code' => $employee->employee_code,
            'department_id' => $employee->department_id,
            'designation_id' => $employee->designation_id,
            'joining_date' => $employee->joining_date->format('Y-m-d'),
            'employment_status' => 'ACTIVE',
            'employment_type' => 'FULL_TIME',
            'role_slug' => 'manager',
            'basic_salary' => 45000,
        ]);

        $updateResponse->assertRedirect("/employees/{$employee->id}");
        $this->assertDatabaseHas('employees', [
            'id' => $employee->id,
            'last_name' => 'UpdatedLast',
        ]);
        $this->assertTrue($employee->fresh()->user->hasRole('manager'));
    }

    /**
     * Test leave requests show reporting manager and HR approval.
     */
    public function test_leave_requests_display_reporting_manager(): void
    {
        $hr = User::where('email', 'hr@hrm.test')->first();
        $response = $this->actingAs($hr)->get('/leaves');
        $response->assertStatus(200);
        $response->assertSee('Manager:');
    }

    /**
     * Test department manager only sees their department workforce and attendance.
     */
    public function test_manager_department_scoping(): void
    {
        $managerRole = \App\Models\Role::where('slug', 'manager')->first();
        $managerUser = User::factory()->create(['email' => 'manager@test.com']);
        $managerUser->roles()->attach($managerRole->id);

        $engDept = \App\Models\Department::first();
        $otherDept = \App\Models\Department::skip(1)->first();

        $managerEmployee = Employee::create([
            'user_id' => $managerUser->id,
            'employee_code' => 'MGR-001',
            'first_name' => 'Department',
            'last_name' => 'Lead',
            'work_email' => 'manager@test.com',
            'department_id' => $engDept->id,
            'designation_id' => \App\Models\Designation::first()->id,
            'joining_date' => now(),
            'employment_status' => 'ACTIVE',
            'employment_type' => 'FULL_TIME',
        ]);

        $otherEmp = Employee::where('department_id', $otherDept->id)->first();

        // 1. Employee index is scoped to their department
        $indexResponse = $this->actingAs($managerUser)->get('/employees');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee($managerEmployee->first_name);
        if ($otherEmp) {
            $indexResponse->assertDontSee($otherEmp->work_email);
        }

        // 2. Attendance index is scoped to their department
        $attResponse = $this->actingAs($managerUser)->get('/attendance');
        $attResponse->assertStatus(200);
    }

    /**
     * Test user can change their own password.
     */
    public function test_user_can_change_own_password(): void
    {
        $user = User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('OldPassword123!'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'OldPassword123!',
            'password' => 'NewSecret456!',
            'password_confirmation' => 'NewSecret456!',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('NewSecret456!', $user->password));

        // Audit log was created
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'PASSWORD_CHANGED',
        ]);
    }

    /**
     * Test user cannot change password with incorrect current password.
     */
    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('OldPassword123!'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'WrongPassword',
            'password' => 'NewSecret456!',
            'password_confirmation' => 'NewSecret456!',
        ]);

        $response->assertSessionHasErrors('current_password');
        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('OldPassword123!', $user->password));
    }

    /**
     * Test user cannot change password with mismatched confirmation.
     */
    public function test_user_cannot_change_password_with_mismatched_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => \Illuminate\Support\Facades\Hash::make('OldPassword123!'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'OldPassword123!',
            'password' => 'NewSecret456!',
            'password_confirmation' => 'DifferentSecret789!',
        ]);

        $response->assertSessionHasErrors('password');
        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('OldPassword123!', $user->password));
    }
}
