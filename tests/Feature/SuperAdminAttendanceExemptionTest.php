<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAttendanceExemptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_super_admin_does_not_see_punch_station_on_dashboard(): void
    {
        $superAdmin = User::where('email', 'admin@hrm.test')->first();

        $response = $this->actingAs($superAdmin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('Real-Time Geofence Punch Station');
        $response->assertDontSee('Scan QR to Check In');
    }

    public function test_regular_employee_sees_punch_station_on_dashboard(): void
    {
        $empUser = User::factory()->create(['email' => 'staff_worker@test.com']);
        $employeeRole = Role::where('slug', 'employee')->first();
        if ($employeeRole) {
            $empUser->roles()->attach($employeeRole->id);
        }

        Employee::create([
            'user_id' => $empUser->id,
            'employee_code' => 'EMP-TEST-99',
            'first_name' => 'Staff',
            'last_name' => 'Worker',
            'work_email' => 'staff_worker@test.com',
            'department_id' => \App\Models\Department::first()?->id,
            'designation_id' => \App\Models\Designation::first()?->id,
            'work_location_id' => \App\Models\WorkLocation::first()?->id,
            'joining_date' => now()->subMonth(),
            'employment_status' => 'ACTIVE',
            'employment_type' => 'FULL_TIME',
        ]);

        $response = $this->actingAs($empUser)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Real-Time Geofence Punch Station');
        $response->assertSee('Scan QR to Check In');
    }

    public function test_super_admin_is_exempt_from_check_in(): void
    {
        $superAdmin = User::where('email', 'admin@hrm.test')->first();

        $response = $this->actingAs($superAdmin)->post('/attendance/check-in', [
            'latitude' => 13.0827,
            'longitude' => 80.2707,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Super Admin accounts are exempt from attendance check-in.');
    }

    public function test_super_admin_calendar_defaults_to_workforce_employee(): void
    {
        $superAdmin = User::where('email', 'admin@hrm.test')->first();

        $response = $this->actingAs($superAdmin)->get('/attendance/calendar');

        $response->assertStatus(200);
        // Should not mark the viewing employee as unexcused absent super admin
        $response->assertDontSee('Unexcused Absence (No check-in punch or leave application registered)');
    }

    public function test_super_admin_popover_shows_company_payroll_and_settings_not_personal_salary(): void
    {
        $superAdmin = User::where('email', 'admin@hrm.test')->first();

        $response = $this->actingAs($superAdmin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Company Payroll Ledger');
        $response->assertSee('Company Settings');
        $response->assertDontSee('My Salary & Take-Home');
    }

    public function test_super_admin_is_not_listed_in_staff_take_home_ledger(): void
    {
        $superAdmin = User::where('email', 'admin@hrm.test')->first();

        $response = $this->actingAs($superAdmin)->get('/payroll');

        $response->assertStatus(200);
        $response->assertSee('Company Payroll & Workforce Disbursals');
        // Employee take home table shouldn't list Super Admin as receiving hourly/attended wages
        $response->assertDontSee('EMP-000');
    }
}
