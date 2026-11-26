<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeSalaryDeductionPercentageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_create_employee_with_percentage_deductions(): void
    {
        $adminUser = User::where('email', 'admin@hrm.test')->first();
        $this->assertNotNull($adminUser);

        $dept = Department::first();
        $desig = Designation::where('department_id', $dept->id)->first();
        $this->assertNotNull($desig);

        $response = $this->actingAs($adminUser)->post('/employees', [
            'first_name' => 'Aarav',
            'last_name' => 'Sundaram',
            'email' => 'aarav.sundaram@hrm.test',
            'password' => 'secret123',
            'employee_code' => 'EMP-PCT-01',
            'department_id' => $dept->id,
            'designation_id' => $desig->id,
            'employment_type' => 'FULL_TIME',
            'joining_date' => '2026-11-20',
            // Salary with percentages
            'basic_salary' => 50000,
            'hra' => 20000,
            'conveyance' => 3000,
            'special_allowance' => 7000,
            'provident_fund_percentage' => 12, // 12% of 50,000 = 6,000
            'other_deductions_percentage' => 5, // 5% of 50,000 = 2,500
            'professional_tax' => 200,
        ]);

        $response->assertRedirect('/employees');

        $emp = Employee::where('employee_code', 'EMP-PCT-01')->first();
        $this->assertNotNull($emp);

        // Check PF component
        $pfComp = $emp->salaryComponents()->whereHas('salaryComponent', fn($q) => $q->where('code', 'PF'))->first();
        $this->assertNotNull($pfComp);
        $this->assertEquals(6000.00, (float) $pfComp->amount);

        // Check TDS (other deductions) component
        $tdsComp = $emp->salaryComponents()->whereHas('salaryComponent', fn($q) => $q->where('code', 'TDS'))->first();
        $this->assertNotNull($tdsComp);
        $this->assertEquals(2500.00, (float) $tdsComp->amount);

        // Check total earnings & net take-home calculation
        $gross = $emp->salaryComponents()->whereHas('salaryComponent', fn($q) => $q->where('category', 'EARNING'))->sum('amount');
        $deductions = $emp->salaryComponents()->whereHas('salaryComponent', fn($q) => $q->where('category', 'DEDUCTION'))->sum('amount');
        $this->assertEquals(80000.00, (float) $gross); // 50000 + 20000 + 3000 + 7000
        $this->assertEquals(8700.00, (float) $deductions); // 6000 + 2500 + 200
        $takeHome = $gross - $deductions;
        $this->assertEquals(71300.00, (float) $takeHome);
    }

    public function test_can_update_employee_with_percentage_deductions(): void
    {
        $adminUser = User::where('email', 'admin@hrm.test')->first();
        $emp = Employee::first();
        $this->assertNotNull($emp);

        $response = $this->actingAs($adminUser)->put("/employees/{$emp->id}", [
            'first_name' => $emp->first_name,
            'last_name' => $emp->last_name,
            'email' => $emp->work_email,
            'employee_code' => $emp->employee_code,
            'department_id' => $emp->department_id,
            'designation_id' => $emp->designation_id,
            'employment_status' => 'ACTIVE',
            'employment_type' => 'FULL_TIME',
            'joining_date' => $emp->joining_date ? \Carbon\Carbon::parse($emp->joining_date)->format('Y-m-d') : '2026-01-01',
            'role_slug' => 'employee',
            'basic_salary' => 60000,
            'hra' => 24000,
            'conveyance' => 3000,
            'special_allowance' => 8000,
            'provident_fund_percentage' => 10, // 10% of 60,000 = 6,000
            'other_deductions_percentage' => 2.5, // 2.5% of 60,000 = 1,500
            'professional_tax' => 200,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect("/employees/{$emp->id}");

        $emp->refresh();
        $pfComp = $emp->salaryComponents()->whereHas('salaryComponent', fn($q) => $q->where('code', 'PF'))->first();
        $this->assertNotNull($pfComp);
        $this->assertEquals(6000.00, (float) $pfComp->amount);

        $tdsComp = $emp->salaryComponents()->whereHas('salaryComponent', fn($q) => $q->where('code', 'TDS'))->first();
        $this->assertNotNull($tdsComp);
        $this->assertEquals(1500.00, (float) $tdsComp->amount);
    }
}
