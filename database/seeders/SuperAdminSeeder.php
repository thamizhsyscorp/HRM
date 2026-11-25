<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkLocation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Default Work Location
        $location = WorkLocation::firstOrCreate(
            ['code' => 'HQ-01'],
            [
                'name' => 'Main Headquarters',
                'address' => 'Corporate Head Office',
                'latitude' => 12.9715987,
                'longitude' => 80.2452345,
                'allowed_radius_meters' => 500,
                'geofence_enabled' => false,
                'is_active' => true,
            ]
        );

        // 2. Default Executive Department & Designation for Super Admin
        $adminDept = Department::firstOrCreate(
            ['code' => 'EXEC'],
            [
                'name' => 'Executive Management',
                'description' => 'Executive and System Administration',
                'is_active' => true,
            ]
        );

        $adminDesig = Designation::firstOrCreate(
            ['code' => 'SUPER-ADMIN'],
            [
                'department_id' => $adminDept->id,
                'name' => 'System Administrator',
                'description' => 'Full administrative access',
                'is_active' => true,
            ]
        );

        // 3. Super Admin Role
        $superAdminRole = Role::where('slug', 'super-admin')->first();

        // 4. Create Super Admin User (configurable via ENV)
        $email = env('SUPER_ADMIN_EMAIL', 'admin@hrm.test');
        $name = env('SUPER_ADMIN_NAME', 'Super Admin');
        $password = env('SUPER_ADMIN_PASSWORD', 'password');

        $superAdminUser = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        if ($superAdminRole) {
            $superAdminUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }

        // 5. Create Super Admin Employee Profile
        $adminEmployee = Employee::where('user_id', $superAdminUser->id)
            ->orWhere('employee_code', 'EMP-000')
            ->first();

        if ($adminEmployee) {
            $adminEmployee->update([
                'user_id' => $superAdminUser->id,
                'employee_code' => $adminEmployee->employee_code ?: 'EMP-000',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'work_email' => $email,
                'department_id' => $adminDept->id,
                'designation_id' => $adminDesig->id,
                'work_location_id' => $location->id,
                'joining_date' => Carbon::parse('2024-01-01'),
                'employment_status' => 'ACTIVE',
                'employment_type' => 'FULL_TIME',
            ]);
        } else {
            $adminEmployee = Employee::create([
                'user_id' => $superAdminUser->id,
                'employee_code' => 'EMP-000',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'work_email' => $email,
                'department_id' => $adminDept->id,
                'designation_id' => $adminDesig->id,
                'work_location_id' => $location->id,
                'joining_date' => Carbon::parse('2024-01-01'),
                'employment_status' => 'ACTIVE',
                'employment_type' => 'FULL_TIME',
            ]);
        }

        $adminDept->update(['manager_employee_id' => $adminEmployee->id]);
    }
}
