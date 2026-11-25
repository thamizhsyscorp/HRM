<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SalaryComponent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HrmDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $roles = [
                ['name' => 'Super Admin', 'slug' => 'super-admin', 'description' => 'Full system access'],
                ['name' => 'HR Manager', 'slug' => 'hr-manager', 'description' => 'HR and employee operations'],
                ['name' => 'Department Manager', 'slug' => 'manager', 'description' => 'Departmental manager access'],
                ['name' => 'Employee', 'slug' => 'employee', 'description' => 'Employee self-service access'],
            ];

            foreach ($roles as $data) {
                Role::updateOrCreate(['slug' => $data['slug']], $data);
            }

            $modules = [
                'employees' => ['view-employees', 'create-employees', 'update-employees', 'delete-employees'],
                'attendance' => ['view-attendance', 'manage-attendance', 'correct-attendance'],
                'leave' => ['view-leaves', 'apply-leave', 'approve-leaves'],
                'payroll' => ['view-payroll', 'process-payroll', 'approve-payroll', 'view-own-payslip'],
                'settings' => ['manage-settings', 'manage-users', 'manage-roles'],
                'reports' => ['view-reports', 'export-reports'],
            ];

            foreach ($modules as $module => $slugs) {
                foreach ($slugs as $slug) {
                    Permission::updateOrCreate(
                        ['slug' => $slug],
                        ['name' => ucwords(str_replace('-', ' ', $slug)), 'module' => $module]
                    );
                }
            }

            $allPermissions = Permission::all();
            Role::where('slug', 'super-admin')->first()?->permissions()->sync($allPermissions->modelKeys());

            $hrPermissions = Permission::whereIn('slug', [
                'view-employees', 'create-employees', 'update-employees',
                'view-attendance', 'manage-attendance', 'correct-attendance',
                'view-leaves', 'approve-leaves', 'view-payroll', 'process-payroll',
                'approve-payroll', 'view-reports', 'export-reports',
            ])->get();
            Role::where('slug', 'hr-manager')->first()?->permissions()->sync($hrPermissions->modelKeys());

            $managerPermissions = Permission::whereIn('slug', [
                'view-employees', 'view-attendance', 'view-leaves',
                'view-reports', 'view-own-payslip', 'apply-leave',
            ])->get();
            Role::where('slug', 'manager')->first()?->permissions()->sync($managerPermissions->modelKeys());

            $employeePermissions = Permission::whereIn('slug', [
                'view-own-payslip', 'apply-leave',
            ])->get();
            Role::where('slug', 'employee')->first()?->permissions()->sync($employeePermissions->modelKeys());

            $leaveTypes = [
                ['name' => 'Casual Leave', 'code' => 'CL', 'annual_quota' => 12, 'is_paid' => true],
                ['name' => 'Sick Leave', 'code' => 'SL', 'annual_quota' => 12, 'is_paid' => true],
                ['name' => 'Earned Leave', 'code' => 'EL', 'annual_quota' => 15, 'is_paid' => true],
                ['name' => 'Unpaid Leave', 'code' => 'LWP', 'annual_quota' => 0, 'is_paid' => false],
            ];
            foreach ($leaveTypes as $data) {
                LeaveType::updateOrCreate(['code' => $data['code']], $data + ['requires_approval' => true, 'is_active' => true]);
            }

            $components = [
                ['name' => 'Basic Salary', 'code' => 'BASIC', 'category' => 'EARNING', 'calculation_type' => 'FIXED', 'is_taxable' => true],
                ['name' => 'House Rent Allowance', 'code' => 'HRA', 'category' => 'EARNING', 'calculation_type' => 'FIXED', 'is_taxable' => true],
                ['name' => 'Conveyance Allowance', 'code' => 'CONVEYANCE', 'category' => 'EARNING', 'calculation_type' => 'FIXED', 'is_taxable' => true],
                ['name' => 'Special Allowance', 'code' => 'SPECIAL', 'category' => 'EARNING', 'calculation_type' => 'FIXED', 'is_taxable' => true],
                ['name' => 'Provident Fund', 'code' => 'PF', 'category' => 'DEDUCTION', 'calculation_type' => 'PERCENTAGE', 'is_taxable' => false, 'is_statutory' => true],
                ['name' => 'Employee State Insurance', 'code' => 'ESI', 'category' => 'DEDUCTION', 'calculation_type' => 'PERCENTAGE', 'is_taxable' => false, 'is_statutory' => true],
                ['name' => 'Professional Tax', 'code' => 'PT', 'category' => 'DEDUCTION', 'calculation_type' => 'FIXED', 'is_taxable' => false, 'is_statutory' => true],
                ['name' => 'Income Tax', 'code' => 'TDS', 'category' => 'DEDUCTION', 'calculation_type' => 'FIXED', 'is_taxable' => false, 'is_statutory' => true],
                ['name' => 'Loss of Pay', 'code' => 'LOP', 'category' => 'DEDUCTION', 'calculation_type' => 'FIXED', 'is_taxable' => false],
            ];
            foreach ($components as $data) {
                SalaryComponent::updateOrCreate(
                    ['code' => $data['code']],
                    $data + ['is_active' => true, 'is_statutory' => false]
                );
            }
        });
    }
}
