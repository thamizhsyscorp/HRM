<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceEvent;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkLocation;
use App\Services\QrAttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\HrmDatabaseSeeder::class);
        $this->seed(\Database\Seeders\SampleUserSeeder::class);
    }

    /**
     * Test dedicated QR API returns JSON payload and SVG.
     */
    public function test_daily_qr_api_returns_valid_payload(): void
    {
        $response = $this->getJson('/api/attendance/daily-qr');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'type',
                'date',
                'token',
                'company',
                'expires_at',
                'qr_svg',
            ]
        ]);

        $this->assertEquals(Carbon::today()->toDateString(), $response->json('data.date'));
        $this->assertTrue(QrAttendanceService::validateToken($response->json('data.token'))['valid']);
    }

    /**
     * Test dedicated QR image stream endpoint returns SVG image.
     */
    public function test_daily_qr_image_stream_returns_svg(): void
    {
        $response = $this->get('/api/attendance/daily-qr/image');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    /**
     * Test office Kiosk view loads properly.
     */
    public function test_office_kiosk_view_loads(): void
    {
        $user = User::where('email', 'admin@hrm.test')->first();

        $response = $this->actingAs($user)->get('/attendance/qr-kiosk');

        $response->assertStatus(200);
        $response->assertSee('HRM Kiosk');
        $response->assertSee('Active Daily QR');
    }

    /**
     * Test employee can check in by scanning today's daily QR code.
     */
    public function test_employee_can_check_in_via_qr_scan(): void
    {
        $user = User::factory()->create(['email' => 'emp_qr@test.com']);
        $employeeRole = Role::where('slug', 'employee')->first();
        $user->roles()->attach($employeeRole->id);

        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-QR-01',
            'first_name' => 'Kiran',
            'last_name' => 'Kumar',
            'work_email' => 'emp_qr@test.com',
            'department_id' => Department::first()->id,
            'designation_id' => Designation::first()->id,
            'joining_date' => now(),
            'employment_status' => 'ACTIVE',
            'employment_type' => 'FULL_TIME',
        ]);

        $todayToken = QrAttendanceService::getDailyToken();

        $response = $this->actingAs($user)->postJson('/attendance/qr-punch', [
            'qr_token' => $todayToken,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'accuracy' => 15,
            'action' => 'check_in',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'action' => 'CHECK_IN',
        ]);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'status' => 'PRESENT',
        ]);

        $this->assertDatabaseHas('attendance_events', [
            'employee_id' => $employee->id,
            'event_type' => 'CHECK_IN',
            'source' => 'QR_CODE',
        ]);
    }

    /**
     * Test employee can check out by scanning today's daily QR code.
     */
    public function test_employee_can_check_out_via_qr_scan(): void
    {
        $user = User::factory()->create(['email' => 'emp_checkout@test.com']);
        $employeeRole = Role::where('slug', 'employee')->first();
        $user->roles()->attach($employeeRole->id);

        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-QR-02',
            'first_name' => 'Meena',
            'last_name' => 'Ravi',
            'work_email' => 'emp_checkout@test.com',
            'department_id' => Department::first()->id,
            'designation_id' => Designation::first()->id,
            'joining_date' => now(),
            'employment_status' => 'ACTIVE',
            'employment_type' => 'FULL_TIME',
        ]);

        // Pre-create check-in earlier today
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => Carbon::today(),
            'first_check_in_at' => Carbon::now()->subHours(8),
            'status' => 'PRESENT',
            'is_manual' => false,
        ]);

        $todayToken = QrAttendanceService::getDailyToken();

        $response = $this->actingAs($user)->postJson('/attendance/qr-punch', [
            'qr_token' => $todayToken,
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
        $this->assertNotNull($attendance->last_check_out_at);
        $this->assertGreaterThan(0, $attendance->worked_minutes);

        $this->assertDatabaseHas('attendance_events', [
            'employee_id' => $employee->id,
            'event_type' => 'CHECK_OUT',
            'source' => 'QR_CODE',
        ]);
    }

    /**
     * Test invalid or expired QR code is rejected with 422.
     */
    public function test_invalid_qr_code_is_rejected(): void
    {
        $user = User::where('email', 'admin@hrm.test')->first();

        $response = $this->actingAs($user)->postJson('/attendance/qr-punch', [
            'qr_token' => 'invalid-expired-token-xyz',
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'action' => 'check_in',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
        ]);
    }

    /**
     * Test QR punch with work location geofencing calculates distance accurately.
     */
    public function test_qr_punch_calculates_office_geofence_distance(): void
    {
        $location = WorkLocation::create([
            'name' => 'HQ Tech Park Geo',
            'code' => 'HQ-GEO-99',
            'address' => 'Chennai, India',
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'allowed_radius_meters' => 200,
            'geofence_enabled' => true,
            'is_active' => true,
        ]);

        $user = User::factory()->create(['email' => 'emp_geo@test.com']);
        $employeeRole = Role::where('slug', 'employee')->first();
        $user->roles()->attach($employeeRole->id);

        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => 'EMP-QR-03',
            'first_name' => 'Raj',
            'last_name' => 'Kumar',
            'work_email' => 'emp_geo@test.com',
            'department_id' => Department::first()->id,
            'designation_id' => Designation::first()->id,
            'work_location_id' => $location->id,
            'joining_date' => now(),
            'employment_status' => 'ACTIVE',
            'employment_type' => 'FULL_TIME',
        ]);

        $todayToken = QrAttendanceService::getDailyToken();

        // Punch at the exact coordinates
        $response = $this->actingAs($user)->postJson('/attendance/qr-punch', [
            'qr_token' => $todayToken,
            'latitude' => 13.0827,
            'longitude' => 80.2707,
            'action' => 'check_in',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'within_geofence' => true,
            'distance_meters' => 0,
        ]);

        $event = AttendanceEvent::where('employee_id', $employee->id)->where('event_type', 'CHECK_IN')->first();
        $this->assertNotNull($event);
        $this->assertEquals(0, $event->distance_from_office_meters);
        $this->assertTrue((bool)$event->within_geofence);
    }

    /**
     * Test HR or Super Admin can regenerate a fresh QR code for today.
     */
    public function test_hr_or_super_admin_can_regenerate_daily_qr_code(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();

        // 1. Initial code
        $initialToken = QrAttendanceService::getDailyToken();

        // 2. HR/Admin regenerates
        $response = $this->actingAs($admin)->postJson('/attendance/qr/regenerate');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $newToken = $response->json('data.token');
        $this->assertNotEquals($initialToken, $newToken);

        // 3. Old token is now invalid/deactivated
        $validateOld = QrAttendanceService::validateToken($initialToken);
        $this->assertFalse($validateOld['valid']);

        // 4. New token is active
        $validateNew = QrAttendanceService::validateToken($newToken);
        $this->assertTrue($validateNew['valid']);
    }

    /**
     * Test standard employees cannot regenerate daily QR code.
     */
    public function test_standard_employee_cannot_regenerate_daily_qr(): void
    {
        $empUser = User::factory()->create(['email' => 'regular_emp@test.com']);
        $employeeRole = Role::where('slug', 'employee')->first();
        $empUser->roles()->attach($employeeRole->id);

        $response = $this->actingAs($empUser)->postJson('/attendance/qr/regenerate');

        $response->assertStatus(403);
    }

    /**
     * Test QR code automatically expires after 12:00 AM midnight.
     */
    public function test_qr_code_expires_at_midnight(): void
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $expiredQr = \App\Models\DailyQrCode::create([
            'qr_date' => $yesterday,
            'token' => 'qr_yesterday_token_123',
            'version' => 1,
            'expires_at' => Carbon::yesterday()->endOfDay(), // Expired at 12:00 AM
            'is_active' => true,
        ]);

        $validate = QrAttendanceService::validateToken($expiredQr->token);
        $this->assertFalse($validate['valid']);
        $this->assertStringContainsString('expired at 12:00 AM', $validate['message']);
    }
}
