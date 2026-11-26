<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\User;
use App\Services\QrAttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileQrScanAndLeaveRevokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\HrmDatabaseSeeder::class);
        $this->seed(\Database\Seeders\SampleUserSeeder::class);
    }

    private function createEmployeeUser(string $email = 'emp_test@hrm.test'): array
    {
        $user = User::factory()->create(['email' => $email]);
        $role = Role::where('slug', 'employee')->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-' . strtoupper(substr(md5($email), 0, 6)),
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'work_email' => $email,
            'department_id' => Department::first()->id,
            'designation_id' => Designation::first()->id,
            'joining_date' => now(),
            'employment_status' => 'ACTIVE',
            'employment_type' => 'FULL_TIME',
        ]);

        return [$user, $employee];
    }

    public function test_qr_scan_checkout_deducts_one_hour_lunch_break_daily(): void
    {
        [$employeeUser, $employee] = $this->createEmployeeUser('worker_checkout@hrm.test');

        // Pre-create check-in 8 hours ago
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today(),
            'first_check_in_at' => Carbon::now()->subHours(8),
            'status' => 'PRESENT',
            'is_manual' => false,
        ]);

        $token = QrAttendanceService::getDailyToken();

        $response = $this->actingAs($employeeUser)->postJson('/attendance/qr-punch', [
            'qr_token' => $token,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'accuracy' => 10,
            'action' => 'check_out',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'action' => 'CHECK_OUT',
        ]);

        $attendance->refresh();
        // 8 hours elapsed = 480 mins. Deducting 60 mins lunch break = 420 mins (7.0 hours).
        $this->assertNotNull($attendance->last_check_out_at);
        $this->assertEquals(420, $attendance->worked_minutes);
    }

    public function test_qr_scan_is_blocked_when_employee_is_on_approved_leave_today(): void
    {
        [$employeeUser, $employee] = $this->createEmployeeUser('worker_leave@hrm.test');
        $leaveType = LeaveType::first();

        // Approved leave for today
        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today(),
            'total_days' => 1,
            'reason' => 'Family function',
            'status' => 'APPROVED',
            'reviewed_at' => now(),
        ]);

        $token = QrAttendanceService::getDailyToken();

        // Attempt QR check-in
        $response = $this->actingAs($employeeUser)->postJson('/attendance/qr-punch', [
            'qr_token' => $token,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'accuracy' => 10,
            'action' => 'check_in',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'status' => 'error',
        ]);
        $this->assertStringContainsString('approved leave', $response->json('message'));

        // Attempt Web check-in
        $webResponse = $this->actingAs($employeeUser)->post('/attendance/check-in', [
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'accuracy' => 10,
        ]);

        $webResponse->assertSessionHasErrors('attendance');
    }

    public function test_employee_can_revoke_approved_leave_and_reporting_manager_or_hr_approves_then_qr_unblocked(): void
    {
        $hrUser = User::factory()->create(['email' => 'hr_reviewer@hrm.test']);
        $hrRole = Role::where('slug', 'hr-manager')->first();
        if ($hrRole) {
            $hrUser->roles()->attach($hrRole->id);
        }

        [$employeeUser, $employee] = $this->createEmployeeUser('worker_revoke@hrm.test');
        $leaveType = LeaveType::first();

        // 1. Employee has an approved leave for today
        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today(),
            'total_days' => 1,
            'reason' => 'Not feeling well',
            'status' => 'APPROVED',
            'reviewed_at' => now(),
        ]);

        // 2. Employee decides they don't need the leave and submits a revoke request
        $revokeResponse = $this->actingAs($employeeUser)->post("/leaves/{$leave->id}/revoke", [
            'revoke_reason' => 'Feeling better and want to report to work today.',
        ]);

        $revokeResponse->assertRedirect();
        $revokeResponse->assertSessionHas('success');

        $leave->refresh();
        $this->assertEquals('REVOKE_REQUESTED', $leave->status);

        // While pending review, QR check-in is STILL BLOCKED! Only after revoke accepted can they mark attendance!
        $token = QrAttendanceService::getDailyToken();
        $blockedWhilePendingResponse = $this->actingAs($employeeUser)->postJson('/attendance/qr-punch', [
            'qr_token' => $token,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'accuracy' => 10,
            'action' => 'check_in',
        ]);
        $blockedWhilePendingResponse->assertStatus(422);
        $this->assertStringContainsString('awaiting approval', $blockedWhilePendingResponse->json('message'));

        // 3. HR or Reporting Manager approves the revocation application
        $reviewResponse = $this->actingAs($hrUser)->post("/leaves/{$leave->id}/review", [
            'status' => 'REVOKED',
            'review_remarks' => 'Revocation approved by HR. Employee reporting to duty.',
        ]);

        $reviewResponse->assertRedirect();
        $reviewResponse->assertSessionHas('success');

        $leave->refresh();
        $this->assertEquals('REVOKED', $leave->status);

        // 4. Now that leave is revoked, QR check-in is unblocked (Green state) and succeeds!
        $qrResponse = $this->actingAs($employeeUser)->postJson('/attendance/qr-punch', [
            'qr_token' => $token,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'accuracy' => 10,
            'action' => 'check_in',
        ]);

        $qrResponse->assertStatus(200);
        $qrResponse->assertJson([
            'status' => 'success',
            'action' => 'CHECK_IN',
        ]);

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', Carbon::today())
            ->first();

        $this->assertNotNull($todayAttendance);
        $this->assertNotNull($todayAttendance->first_check_in_at);
    }

    public function test_kiosk_recent_punches_api_returns_marked_attendance_notification(): void
    {
        $user = User::factory()->create(['email' => 'thamizh@hrm.test']);
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-THAMIZH',
            'first_name' => 'Thamizh',
            'last_name' => 'Arasan',
            'work_email' => 'thamizh@hrm.test',
            'department_id' => Department::first()->id,
            'designation_id' => Designation::first()->id,
            'joining_date' => now(),
            'employment_status' => 'ACTIVE',
            'employment_type' => 'FULL_TIME',
        ]);

        $token = QrAttendanceService::getDailyToken();

        // Punch in via QR
        $this->actingAs($user)->postJson('/attendance/qr-punch', [
            'qr_token' => $token,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'accuracy' => 10,
            'action' => 'check_in',
        ]);

        // Kiosk receives real-time punch notification
        $response = $this->getJson('/api/attendance/recent-punches');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'latest_id',
            'punches' => [
                '*' => [
                    'id',
                    'first_name',
                    'full_name',
                    'event_type',
                    'type_label',
                    'time',
                    'message',
                ]
            ]
        ]);

        $latest = $response->json('punches.0');
        $this->assertEquals('Thamizh', $latest['first_name']);
        $this->assertEquals('Thamizh, your attendance MARKED!', $latest['message']);
        $this->assertEquals('CHECK_IN', $latest['event_type']);
        $this->assertArrayHasKey('active_qr', $response->json());
    }

    public function test_qr_code_rotates_on_scan_and_old_qr_is_disabled(): void
    {
        [$user1, $emp1] = $this->createEmployeeUser('employee1_rotate@hrm.test');
        [$user2, $emp2] = $this->createEmployeeUser('employee2_rotate@hrm.test');

        $initialToken = QrAttendanceService::getDailyToken();

        // 1. Employee 1 scans initial QR code to check in
        $response1 = $this->actingAs($user1)->postJson('/attendance/qr-punch', [
            'qr_token' => $initialToken,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'accuracy' => 10,
            'action' => 'check_in',
        ]);

        $response1->assertStatus(200);
        $response1->assertJson(['status' => 'success']);

        // 2. Employee 2 attempts to scan the same (consumed) QR code -> must fail with 422
        $response2 = $this->actingAs($user2)->postJson('/attendance/qr-punch', [
            'qr_token' => $initialToken,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'accuracy' => 10,
            'action' => 'check_in',
        ]);

        $response2->assertStatus(422);
        $this->assertStringContainsString('expired', strtolower($response2->json('message')));

        // 3. Employee 2 scans the newly rotated QR token -> succeeds
        $newToken = QrAttendanceService::getDailyToken();
        $this->assertNotEquals($initialToken, $newToken);

        $response3 = $this->actingAs($user2)->postJson('/attendance/qr-punch', [
            'qr_token' => $newToken,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'accuracy' => 10,
            'action' => 'check_in',
        ]);

        $response3->assertStatus(200);
        $response3->assertJson(['status' => 'success']);
    }

    public function test_super_admin_does_not_see_take_home_salary_menu(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first() ?: User::factory()->create(['email' => 'admin_test@hrm.test']);
        $adminRole = Role::where('slug', 'super-admin')->first();
        if ($adminRole && !$admin->roles->contains($adminRole->id)) {
            $admin->roles()->attach($adminRole->id);
        }

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Super Admin • Owner');
        $response->assertSee('Company Payroll Ledger');
        $response->assertDontSee('My Salary & Take-Home');
    }

    public function test_badges_display_for_announcements_and_pending_leave_approvals(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();
        [$worker, $employee] = $this->createEmployeeUser('worker_badge@hrm.test');
        $leaveType = LeaveType::first();

        // Create a pending leave request
        LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(6),
            'total_days' => 2,
            'reason' => 'Family vacation',
            'status' => 'PENDING',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('pending');
        $response->assertSee('Notifications & Updates', false);
        $response->assertSee('Leave Approvals Pending');
    }
}
