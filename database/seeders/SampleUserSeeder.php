<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceEvent;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkLocation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Work Location
        $location = WorkLocation::firstOrCreate(
            ['code' => 'HQ-01'],
            [
                'name' => 'Main Tech Campus',
                'address' => '100 IT Corridor, OMR, Chennai, Tamil Nadu',
                'latitude' => 12.9715987,
                'longitude' => 80.2452345,
                'allowed_radius_meters' => 200,
                'geofence_enabled' => true,
                'is_active' => true,
            ]
        );

        // 2. Departments
        $hrDept = Department::firstOrCreate(['code' => 'HR'], [
            'name' => 'Human Resources',
            'description' => 'Talent acquisition, operations and employee relations',
            'is_active' => true,
        ]);

        $engDept = Department::firstOrCreate(['code' => 'ENG'], [
            'name' => 'Engineering',
            'description' => 'Software engineering, architecture and QA',
            'is_active' => true,
        ]);

        $finDept = Department::firstOrCreate(['code' => 'FIN'], [
            'name' => 'Finance & Payroll',
            'description' => 'Financial accounting, payroll processing and audits',
            'is_active' => true,
        ]);

        // 3. Designations
        $hrManagerDesig = Designation::firstOrCreate(
            ['department_id' => $hrDept->id, 'name' => 'HR Manager'],
            ['code' => 'HR-MGR', 'description' => 'Head of People Operations', 'is_active' => true]
        );

        $srEngineerDesig = Designation::firstOrCreate(
            ['department_id' => $engDept->id, 'name' => 'Senior Software Engineer'],
            ['code' => 'ENG-SR', 'description' => 'Full-stack application development', 'is_active' => true]
        );

        $qaEngineerDesig = Designation::firstOrCreate(
            ['department_id' => $engDept->id, 'name' => 'QA Automation Engineer'],
            ['code' => 'ENG-QA', 'description' => 'Quality assurance and test automation', 'is_active' => true]
        );

        // 4. Retrieve Roles
        $superAdminRole = Role::where('slug', 'super-admin')->first();
        $hrRole = Role::where('slug', 'hr-manager')->first();
        $employeeRole = Role::where('slug', 'employee')->first();

        // 5. Create Sample Users & Employees

        // --- SUPER ADMIN ---
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@hrm.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if ($superAdminRole) {
            $adminUser->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }

        Employee::updateOrCreate(
            ['employee_code' => 'EMP-000'],
            [
                'user_id' => $adminUser->id,
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'work_email' => 'admin@hrm.test',
                'personal_email' => 'admin.personal@hrm.test',
                'phone' => '+91 99999 00000',
                'department_id' => $hrDept->id,
                'designation_id' => $hrManagerDesig->id,
                'work_location_id' => $location->id,
                'joining_date' => Carbon::parse('2022-01-01'),
                'employment_status' => 'ACTIVE',
                'employment_type' => 'FULL_TIME',
                'gender' => 'Male',
            ]
        );

        // --- HR MANAGER ---
        $hrUser = User::updateOrCreate(
            ['email' => 'hr@hrm.test'],
            [
                'name' => 'Sarah Jenkins',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if ($hrRole) {
            $hrUser->roles()->syncWithoutDetaching([$hrRole->id]);
        }

        $hrEmployee = Employee::updateOrCreate(
            ['employee_code' => 'EMP-001'],
            [
                'user_id' => $hrUser->id,
                'first_name' => 'Sarah',
                'last_name' => 'Jenkins',
                'work_email' => 'hr@hrm.test',
                'personal_email' => 'sarah.jenkins@personal.test',
                'phone' => '+91 98765 00001',
                'department_id' => $hrDept->id,
                'designation_id' => $hrManagerDesig->id,
                'work_location_id' => $location->id,
                'joining_date' => Carbon::parse('2023-01-10'),
                'employment_status' => 'ACTIVE',
                'employment_type' => 'FULL_TIME',
                'gender' => 'Female',
            ]
        );

        // Set HR Manager on Department
        $hrDept->update(['manager_employee_id' => $hrEmployee->id]);

        // --- REGULAR EMPLOYEE ---
        $empUser = User::updateOrCreate(
            ['email' => 'employee@hrm.test'],
            [
                'name' => 'Alex Rivera',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if ($employeeRole) {
            $empUser->roles()->syncWithoutDetaching([$employeeRole->id]);
        }

        $regularEmployee = Employee::updateOrCreate(
            ['employee_code' => 'EMP-002'],
            [
                'user_id' => $empUser->id,
                'first_name' => 'Alex',
                'last_name' => 'Rivera',
                'work_email' => 'employee@hrm.test',
                'personal_email' => 'alex.rivera@personal.test',
                'phone' => '+91 98765 00002',
                'department_id' => $engDept->id,
                'designation_id' => $srEngineerDesig->id,
                'manager_id' => $hrEmployee->id,
                'work_location_id' => $location->id,
                'joining_date' => Carbon::parse('2023-06-15'),
                'employment_status' => 'ACTIVE',
                'employment_type' => 'FULL_TIME',
                'gender' => 'Male',
            ]
        );

        // --- ADDITIONAL SAMPLE EMPLOYEE ---
        $qaUser = User::updateOrCreate(
            ['email' => 'priya.nair@hrm.test'],
            [
                'name' => 'Priya Nair',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if ($employeeRole) {
            $qaUser->roles()->syncWithoutDetaching([$employeeRole->id]);
        }

        $qaEmployee = Employee::updateOrCreate(
            ['employee_code' => 'EMP-003'],
            [
                'user_id' => $qaUser->id,
                'first_name' => 'Priya',
                'last_name' => 'Nair',
                'work_email' => 'priya.nair@hrm.test',
                'phone' => '+91 98765 00003',
                'department_id' => $engDept->id,
                'designation_id' => $qaEngineerDesig->id,
                'manager_id' => $hrEmployee->id,
                'work_location_id' => $location->id,
                'joining_date' => Carbon::parse('2024-02-01'),
                'employment_status' => 'ACTIVE',
                'employment_type' => 'FULL_TIME',
                'gender' => 'Female',
            ]
        );

        // 6. Sample Attendance (Today)
        $today = Carbon::today();
        
        // Alex Rivera present today
        $alexAttendance = Attendance::updateOrCreate(
            ['employee_id' => $regularEmployee->id, 'attendance_date' => $today->toDateString()],
            [
                'first_check_in_at' => $today->copy()->setTime(9, 5, 0),
                'worked_minutes' => 360,
                'late_minutes' => 5,
                'early_leave_minutes' => 0,
                'status' => 'PRESENT',
                'is_manual' => false,
            ]
        );

        AttendanceEvent::firstOrCreate(
            ['attendance_id' => $alexAttendance->id, 'event_type' => 'CHECK_IN'],
            [
                'employee_id' => $regularEmployee->id,
                'recorded_at' => $today->copy()->setTime(9, 5, 0),
                'latitude' => 12.9715980,
                'longitude' => 80.2452340,
                'within_geofence' => true,
                'source' => 'WEB',
                'remarks' => 'Office Web Check-in',
            ]
        );

        // Sarah Jenkins present today
        Attendance::updateOrCreate(
            ['employee_id' => $hrEmployee->id, 'attendance_date' => $today->toDateString()],
            [
                'first_check_in_at' => $today->copy()->setTime(8, 55, 0),
                'worked_minutes' => 370,
                'late_minutes' => 0,
                'early_leave_minutes' => 0,
                'status' => 'PRESENT',
                'is_manual' => false,
            ]
        );

        // 7. Sample Leave Requests
        $clType = LeaveType::where('code', 'CL')->first();
        $slType = LeaveType::where('code', 'SL')->first();

        if ($clType) {
            LeaveRequest::firstOrCreate(
                [
                    'employee_id' => $regularEmployee->id,
                    'start_date' => $today->copy()->addDays(5)->toDateString(),
                ],
                [
                    'leave_type_id' => $clType->id,
                    'end_date' => $today->copy()->addDays(6)->toDateString(),
                    'total_days' => 2,
                    'reason' => 'Attending family function',
                    'status' => 'PENDING',
                ]
            );
        }

        if ($slType) {
            LeaveRequest::firstOrCreate(
                [
                    'employee_id' => $qaEmployee->id,
                    'start_date' => $today->copy()->subDays(2)->toDateString(),
                ],
                [
                    'leave_type_id' => $slType->id,
                    'end_date' => $today->copy()->subDays(2)->toDateString(),
                    'total_days' => 1,
                    'reason' => 'Viral fever recovery',
                    'status' => 'APPROVED',
                    'reviewed_by' => $hrUser->id,
                    'reviewed_at' => $today->copy()->subDays(2)->setTime(10, 0, 0),
                    'review_remarks' => 'Approved by HR',
                ]
            );
        }

        // 8. Company Holidays (2026)
        $sampleHolidays = [
            ['name' => "New Year's Day", 'holiday_date' => '2026-01-01', 'type' => 'PUBLIC', 'description' => 'Global New Year celebration'],
            ['name' => 'Pongal / Makar Sankranti', 'holiday_date' => '2026-01-15', 'type' => 'PUBLIC', 'description' => 'Harvest festival holiday'],
            ['name' => 'Republic Day', 'holiday_date' => '2026-01-26', 'type' => 'NATIONAL', 'description' => 'National Republic Day'],
            ['name' => 'International Labour Day', 'holiday_date' => '2026-05-01', 'type' => 'PUBLIC', 'description' => 'May Day public holiday'],
            ['name' => 'Independence Day', 'holiday_date' => '2026-08-15', 'type' => 'NATIONAL', 'description' => 'National Independence Day'],
            ['name' => 'Gandhi Jayanti', 'holiday_date' => '2026-10-02', 'type' => 'NATIONAL', 'description' => 'Mahatma Gandhi Birthday'],
            ['name' => 'Deepavali / Diwali', 'holiday_date' => '2026-11-08', 'type' => 'PUBLIC', 'description' => 'Festival of lights corporate holiday'],
            ['name' => 'Company Annual Foundation Day', 'holiday_date' => '2026-11-14', 'type' => 'COMPANY', 'description' => 'Syscorp enterprise founding anniversary'],
            ['name' => 'Guru Nanak Jayanti', 'holiday_date' => '2026-11-24', 'type' => 'PUBLIC', 'description' => 'Religious & cultural public holiday'],
            ['name' => 'Christmas Day', 'holiday_date' => '2026-12-25', 'type' => 'PUBLIC', 'description' => 'Christmas festivities'],
        ];

        foreach ($sampleHolidays as $holiday) {
            \App\Models\Holiday::updateOrCreate(
                ['holiday_date' => $holiday['holiday_date']],
                $holiday
            );
        }

        // 9. Sample Past Attendance Logs for November 2026 (EMP-002) for rich calendar demonstration
        $novemberPunches = [
            '2026-11-02' => ['in' => '09:02', 'out' => '18:15', 'status' => 'PRESENT', 'minutes' => 553],
            '2026-11-03' => ['in' => '08:58', 'out' => '18:05', 'status' => 'PRESENT', 'minutes' => 547],
            '2026-11-04' => ['in' => '09:12', 'out' => '18:30', 'status' => 'PRESENT', 'minutes' => 558],
            '2026-11-05' => ['in' => '09:00', 'out' => '17:55', 'status' => 'PRESENT', 'minutes' => 535],
            '2026-11-06' => ['in' => '09:05', 'out' => '18:00', 'status' => 'PRESENT', 'minutes' => 535],
            // 2026-11-07 Saturday (unpunched -> will show absent)
            // 2026-11-08 Sunday -> Holiday (Diwali)
            '2026-11-09' => ['in' => '09:15', 'out' => '18:20', 'status' => 'PRESENT', 'minutes' => 545],
            '2026-11-10' => ['in' => '08:55', 'out' => '18:10', 'status' => 'PRESENT', 'minutes' => 555],
            // 2026-11-11 unpunched -> absent
            '2026-11-12' => ['in' => '09:05', 'out' => '18:00', 'status' => 'PRESENT', 'minutes' => 535],
            '2026-11-13' => ['in' => '09:00', 'out' => '18:15', 'status' => 'PRESENT', 'minutes' => 555],
            // 2026-11-14 Saturday -> Company Foundation Day
            // 2026-11-15 Sunday
            '2026-11-16' => ['in' => '09:04', 'out' => '18:08', 'status' => 'PRESENT', 'minutes' => 544],
            '2026-11-17' => ['in' => '09:10', 'out' => '18:12', 'status' => 'PRESENT', 'minutes' => 542],
            '2026-11-18' => ['in' => '08:50', 'out' => '18:00', 'status' => 'PRESENT', 'minutes' => 550],
            '2026-11-19' => ['in' => '09:00', 'out' => '18:10', 'status' => 'PRESENT', 'minutes' => 550],
        ];

        foreach ($novemberPunches as $date => $punch) {
            Attendance::updateOrCreate(
                ['employee_id' => $regularEmployee->id, 'attendance_date' => $date],
                [
                    'first_check_in_at' => Carbon::parse($date . ' ' . $punch['in']),
                    'last_check_out_at' => Carbon::parse($date . ' ' . $punch['out']),
                    'worked_minutes' => $punch['minutes'],
                    'late_minutes' => 0,
                    'early_leave_minutes' => 0,
                    'status' => $punch['status'],
                    'is_manual' => false,
                ]
            );
        }

        // 10. Sample Company Announcements
        $sampleAnnouncements = [
            [
                'title' => 'Mandatory Geolocation Check-In & Check-Out Policy',
                'content' => "Effective immediately, all employee punches (check-in and check-out) must capture verified GPS coordinates. Please allow location access in your browser when checking in at office premises or client sites.",
                'category' => 'POLICY',
                'priority' => 'HIGH',
                'created_by' => $hrUser->id,
                'is_pinned' => true,
            ],
            [
                'title' => 'Upcoming Company Holiday — Guru Nanak Jayanti (Nov 24)',
                'content' => 'Dear Team, please note that Tuesday, November 24, 2026, will be observed as a closed public holiday. Regular office operations will resume on Wednesday, November 25.',
                'category' => 'HOLIDAY',
                'priority' => 'MEDIUM',
                'created_by' => $hrUser->id,
                'is_pinned' => false,
            ],
            [
                'title' => 'Q4 Townhall & Annual Tech Innovation Showcase',
                'content' => 'Join us on Friday, December 4 at 4:00 PM IST in the Main Auditorium or virtually for our executive quarterly address, team awards, and project demos.',
                'category' => 'EVENT',
                'priority' => 'GENERAL',
                'created_by' => $adminUser->id,
                'is_pinned' => false,
            ],
        ];

        foreach ($sampleAnnouncements as $ann) {
            \App\Models\Announcement::updateOrCreate(
                ['title' => $ann['title']],
                $ann
            );
        }
    }
}
