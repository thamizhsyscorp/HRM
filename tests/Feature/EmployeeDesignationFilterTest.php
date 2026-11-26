<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeDesignationFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_employee_create_page_contains_department_designation_filter_script(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();

        $response = $this->actingAs($admin)->get('/employees/create');

        $response->assertStatus(200);
        $response->assertSee('id="departmentSelect"', false);
        $response->assertSee('id="designationSelect"', false);
        $response->assertSee('filterDesignationsByDepartment', false);
        $response->assertSee('allDesignations', false);
    }

    public function test_employee_edit_page_contains_department_designation_filter_script(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();
        $employee = Employee::first();

        $response = $this->actingAs($admin)->get("/employees/{$employee->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('id="departmentSelectEdit"', false);
        $response->assertSee('id="designationSelectEdit"', false);
        $response->assertSee('filterDesignationsByDepartmentEdit', false);
        $response->assertSee('allDesignationsEdit', false);
    }
}
