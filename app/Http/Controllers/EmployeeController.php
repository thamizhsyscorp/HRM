<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeBankAccount;
use App\Models\EmployeeSalaryComponent;
use App\Models\LeaveType;
use App\Models\Role;
use App\Models\SalaryComponent;
use App\Models\SalaryStructure;
use App\Models\User;
use App\Models\WorkLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display workforce directory with department filter and search.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');
        $isManager = $user->hasRole('manager') || $user->hasRole('department-manager') || $user->hasRole('pm');

        $query = Employee::with(['department', 'designation', 'user', 'manager', 'salaryComponents']);

        // Department Scoping for Managers
        $managedDepartment = null;
        if ($isManager && !$isSuperAdmin && !$isHr && $user->employee?->department_id) {
            $managedDeptId = $user->employee->department_id;
            $query->where('department_id', $managedDeptId);
            $departments = Department::where('id', $managedDeptId)->withCount('employees')->get();
            $managedDepartment = $user->employee->department;
        } else {
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }
            $departments = Department::where('is_active', true)->withCount('employees')->orderBy('name')->get();
        }

        if ($request->filled('status')) {
            $query->where('employment_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', $search)
                    ->orWhere('last_name', 'like', $search)
                    ->orWhere('employee_code', 'like', $search)
                    ->orWhere('work_email', 'like', $search);
            });
        }

        $employees = $query->orderBy('first_name')->paginate(12)->withQueryString();

        $totalEmployees = Employee::count();
        $activeCount = Employee::where('employment_status', 'ACTIVE')->count();

        return view('employees.index', compact(
            'employees',
            'departments',
            'totalEmployees',
            'activeCount',
            'isSuperAdmin',
            'isHr',
            'isManager',
            'managedDepartment'
        ));
    }

    /**
     * Show detailed employee profile view.
     */
    public function show(Employee $employee): View
    {
        $employee->load([
            'department',
            'designation',
            'manager',
            'directReports',
            'workLocation',
            'user.roles',
            'salaryComponents.salaryComponent',
            'bankAccounts',
            'attendances' => fn($q) => $q->with('events')->latest('attendance_date')->take(7),
            'leaveRequests' => fn($q) => $q->with('leaveType')->latest()->take(5),
        ]);

        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isHr = $user->hasRole('hr-manager');

        return view('employees.show', compact('employee', 'isSuperAdmin', 'isHr'));
    }

    /**
     * Show form to create a new employee.
     */
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager')) {
            abort(403, 'Unauthorized action. Only HR and Administrators can create employees.');
        }

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $designations = Designation::where('is_active', true)->orderBy('name')->get();
        $managers = Employee::where('employment_status', 'ACTIVE')->orderBy('first_name')->get();
        $workLocations = WorkLocation::where('is_active', true)->get();
        $salaryComponents = SalaryComponent::where('is_active', true)->get();
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $roles = Role::orderBy('id')->get();
        $nextEmployeeCode = 'EMP-' . str_pad(Employee::withTrashed()->count() + 1, 3, '0', STR_PAD_LEFT);

        return view('employees.create', compact(
            'departments',
            'designations',
            'managers',
            'workLocations',
            'salaryComponents',
            'leaveTypes',
            'nextEmployeeCode',
            'roles'
        ));
    }

    /**
     * Store newly created employee with complete profile, user account, salary components, and bank info.
     */
    public function store(Request $request): RedirectResponse
    {
        $currentUser = Auth::user();
        if (!$currentUser->hasRole('super-admin') && !$currentUser->hasRole('hr-manager')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            // Personal & Login Details
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:190', 'unique:users,email', 'unique:employees,work_email'],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],

            // Job & Organization
            'employee_code' => ['required', 'string', 'max:40', 'unique:employees,employee_code'],
            'department_id' => ['required', 'exists:departments,id'],
            'designation_id' => ['required', 'exists:designations,id'],
            'manager_id' => ['nullable', 'exists:employees,id'],
            'work_location_id' => ['nullable', 'exists:work_locations,id'],
            'joining_date' => ['required', 'date'],
            'employment_type' => ['required', 'string', 'in:FULL_TIME,PART_TIME,CONTRACT,INTERN'],
            'role_slug' => ['nullable', 'string', 'exists:roles,slug'],
            'is_department_head' => ['nullable', 'boolean'],

            // Salary Breakdown
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'hra' => ['nullable', 'numeric', 'min:0'],
            'conveyance' => ['nullable', 'numeric', 'min:0'],
            'special_allowance' => ['nullable', 'numeric', 'min:0'],
            'provident_fund' => ['nullable', 'numeric', 'min:0'],
            'provident_fund_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'other_deductions' => ['nullable', 'numeric', 'min:0'],
            'other_deductions_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'professional_tax' => ['nullable', 'numeric', 'min:0'],

            // Bank Account
            'bank_name' => ['nullable', 'string', 'max:120'],
            'account_holder_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'branch_name' => ['nullable', 'string', 'max:120'],
        ]);

        $employee = DB::transaction(function () use ($validated, $request, $currentUser) {
            // 1. Create User Login Account
            $fullName = trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? ''));
            $user = User::create([
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?: 'password'),
                'email_verified_at' => now(),
            ]);

            // Assign Chosen Role (defaults to employee)
            $roleSlug = $validated['role_slug'] ?? 'employee';
            $assignedRole = Role::where('slug', $roleSlug)->first() ?? Role::where('slug', 'employee')->first();
            if ($assignedRole) {
                $user->roles()->attach($assignedRole->id);
            }

            // 2. Create Employee Record
            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_code' => $validated['employee_code'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'work_email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'address' => $validated['address'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'department_id' => $validated['department_id'],
                'designation_id' => $validated['designation_id'],
                'manager_id' => $validated['manager_id'] ?? null,
                'work_location_id' => $validated['work_location_id'] ?? null,
                'joining_date' => $validated['joining_date'],
                'employment_status' => 'ACTIVE',
                'employment_type' => $validated['employment_type'],
            ]);

            // If assigned role is manager and department head requested:
            if ($roleSlug === 'manager' && $request->boolean('is_department_head')) {
                Department::where('id', $validated['department_id'])->update(['manager_employee_id' => $employee->id]);
            }

            // 3. Setup Default Salary Structure & Components
            $salaryStructure = SalaryStructure::firstOrCreate(
                ['name' => 'Standard Executive Structure', 'currency' => 'INR'],
                ['description' => 'Corporate salary structure for full-time employees', 'is_active' => true]
            );

            $basicSalary = (float) $request->input('basic_salary', 35000);

            // Calculate PF: percentage of basic salary if provided, else direct amount
            if ($request->filled('provident_fund_percentage')) {
                $pfAmount = round($basicSalary * ((float) $request->input('provident_fund_percentage') / 100), 2);
            } else {
                $pfAmount = (float) $request->input('provident_fund', 1800);
            }

            // Calculate Other Deductions (TDS): percentage of basic salary if provided, else direct amount
            if ($request->filled('other_deductions_percentage')) {
                $otherDeductionsAmount = round($basicSalary * ((float) $request->input('other_deductions_percentage') / 100), 2);
            } else {
                $otherDeductionsAmount = (float) $request->input('other_deductions', $request->input('tds', 0));
            }

            $salaryInputs = [
                'BASIC' => $basicSalary,
                'HRA' => (float) $request->input('hra', 14000),
                'CONVEYANCE' => (float) $request->input('conveyance', 2000),
                'SPECIAL' => (float) $request->input('special_allowance', 6000),
                'PF' => $pfAmount,
                'PT' => (float) $request->input('professional_tax', 200),
                'TDS' => $otherDeductionsAmount,
            ];

            foreach ($salaryInputs as $code => $amt) {
                if ($amt > 0) {
                    $component = SalaryComponent::where('code', $code)->first();
                    if ($component) {
                        EmployeeSalaryComponent::create([
                            'employee_id' => $employee->id,
                            'salary_structure_id' => $salaryStructure->id,
                            'salary_component_id' => $component->id,
                            'amount' => $amt,
                            'effective_from' => $validated['joining_date'] ?: today()->toDateString(),
                        ]);
                    }
                }
            }

            // 4. Create Bank Account (if provided)
            if (!empty($validated['account_number']) && !empty($validated['bank_name'])) {
                EmployeeBankAccount::create([
                    'employee_id' => $employee->id,
                    'account_holder_name' => $validated['account_holder_name'] ?: $fullName,
                    'bank_name' => $validated['bank_name'],
                    'account_number_encrypted' => encrypt($validated['account_number']),
                    'ifsc_code' => strtoupper($validated['ifsc_code'] ?: 'HDFC0001234'),
                    'branch_name' => $validated['branch_name'] ?? null,
                    'is_primary' => true,
                    'is_verified' => true,
                ]);
            }

            // 5. Audit Log
            AuditLog::create([
                'user_id' => $currentUser->id,
                'action' => 'EMPLOYEE_REGISTERED',
                'auditable_type' => Employee::class,
                'auditable_id' => $employee->id,
                'new_values' => [
                    'employee_code' => $employee->employee_code,
                    'name' => $fullName,
                    'email' => $employee->work_email,
                    'department_id' => $employee->department_id,
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return $employee;
        });

        return redirect()->route('employees.index')->with(
            'success',
            "Employee {$employee->full_name} ({$employee->employee_code}) was successfully onboarded with user login, salary structure, and leave allocations!"
        );
    }

    /**
     * Show form to edit an existing employee.
     */
    public function edit(Employee $employee): View
    {
        $user = Auth::user();
        if (!$user->hasRole('super-admin') && !$user->hasRole('hr-manager')) {
            abort(403, 'Unauthorized action. Only HR and Administrators can edit employees.');
        }

        $employee->load([
            'department',
            'designation',
            'manager',
            'workLocation',
            'user.roles',
            'salaryComponents.salaryComponent',
            'bankAccounts',
        ]);

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $designations = Designation::where('is_active', true)->orderBy('name')->get();
        $managers = Employee::where('employment_status', 'ACTIVE')
            ->where('id', '!=', $employee->id)
            ->orderBy('first_name')
            ->get();
        $workLocations = WorkLocation::where('is_active', true)->get();
        $salaryComponents = SalaryComponent::where('is_active', true)->get();
        $roles = Role::orderBy('id')->get();

        $currentRoleSlug = $employee->user?->roles?->first()?->slug ?? 'employee';
        $primaryBank = $employee->bankAccounts->first();

        $salaryMap = [];
        foreach ($employee->salaryComponents as $sc) {
            if ($sc->salaryComponent) {
                $salaryMap[$sc->salaryComponent->code] = (float) $sc->amount;
            }
        }

        $isDeptHead = $employee->department && $employee->department->manager_employee_id === $employee->id;

        return view('employees.edit', compact(
            'employee',
            'departments',
            'designations',
            'managers',
            'workLocations',
            'salaryComponents',
            'roles',
            'currentRoleSlug',
            'primaryBank',
            'salaryMap',
            'isDeptHead'
        ));
    }

    /**
     * Update employee profile, user account, salary breakdown, and bank information.
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $currentUser = Auth::user();
        if (!$currentUser->hasRole('super-admin') && !$currentUser->hasRole('hr-manager')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            // Personal & Login Details
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => [
                'required', 'string', 'email', 'max:190',
                'unique:users,email,' . ($employee->user_id ?? 0),
                'unique:employees,work_email,' . $employee->id,
            ],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],

            // Job & Organization
            'employee_code' => ['required', 'string', 'max:40', 'unique:employees,employee_code,' . $employee->id],
            'department_id' => ['required', 'exists:departments,id'],
            'designation_id' => ['required', 'exists:designations,id'],
            'manager_id' => ['nullable', 'exists:employees,id', 'different:id'],
            'work_location_id' => ['nullable', 'exists:work_locations,id'],
            'joining_date' => ['required', 'date'],
            'employment_status' => ['required', 'string', 'in:ACTIVE,PROBATION,INACTIVE,SUSPENDED,TERMINATED'],
            'employment_type' => ['required', 'string', 'in:FULL_TIME,PART_TIME,CONTRACT,INTERN'],
            'role_slug' => ['required', 'string', 'exists:roles,slug'],
            'is_department_head' => ['nullable', 'boolean'],

            // Salary Breakdown
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'hra' => ['nullable', 'numeric', 'min:0'],
            'conveyance' => ['nullable', 'numeric', 'min:0'],
            'special_allowance' => ['nullable', 'numeric', 'min:0'],
            'provident_fund' => ['nullable', 'numeric', 'min:0'],
            'provident_fund_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'other_deductions' => ['nullable', 'numeric', 'min:0'],
            'other_deductions_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'professional_tax' => ['nullable', 'numeric', 'min:0'],

            // Bank Account
            'bank_name' => ['nullable', 'string', 'max:120'],
            'account_holder_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'ifsc_code' => ['nullable', 'string', 'max:20'],
            'branch_name' => ['nullable', 'string', 'max:120'],
        ]);

        DB::transaction(function () use ($validated, $request, $employee, $currentUser) {
            $fullName = trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? ''));

            // 1. Update Linked User
            if ($employee->user) {
                $userData = [
                    'name' => $fullName,
                    'email' => $validated['email'],
                ];
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }
                $employee->user->update($userData);

                // Update Role
                $role = Role::where('slug', $validated['role_slug'])->first();
                if ($role) {
                    $employee->user->roles()->sync([$role->id]);
                }
            }

            // 2. Update Employee
            $employee->update([
                'employee_code' => $validated['employee_code'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'] ?? null,
                'work_email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'address' => $validated['address'] ?? null,
                'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
                'department_id' => $validated['department_id'],
                'designation_id' => $validated['designation_id'],
                'manager_id' => $validated['manager_id'] ?? null,
                'work_location_id' => $validated['work_location_id'] ?? null,
                'joining_date' => $validated['joining_date'],
                'employment_status' => $validated['employment_status'],
                'employment_type' => $validated['employment_type'],
            ]);

            // Department lead synchronization
            if ($validated['role_slug'] === 'manager' && $request->boolean('is_department_head')) {
                Department::where('id', $validated['department_id'])->update(['manager_employee_id' => $employee->id]);
            }

            // 3. Update / Upsert Salary Components
            $salaryStructure = SalaryStructure::firstOrCreate(
                ['name' => 'Standard Executive Structure', 'currency' => 'INR'],
                ['description' => 'Corporate salary structure for full-time employees', 'is_active' => true]
            );

            $basicSalary = (float) $request->input('basic_salary', 0);

            // Calculate PF: percentage of basic salary if provided, else direct amount
            if ($request->filled('provident_fund_percentage')) {
                $pfAmount = round($basicSalary * ((float) $request->input('provident_fund_percentage') / 100), 2);
            } else {
                $pfAmount = (float) $request->input('provident_fund', 0);
            }

            // Calculate Other Deductions (TDS): percentage of basic salary if provided, else direct amount
            if ($request->filled('other_deductions_percentage')) {
                $otherDeductionsAmount = round($basicSalary * ((float) $request->input('other_deductions_percentage') / 100), 2);
            } else {
                $otherDeductionsAmount = (float) $request->input('other_deductions', $request->input('tds', 0));
            }

            $salaryInputs = [
                'BASIC' => $basicSalary,
                'HRA' => (float) $request->input('hra', 0),
                'CONVEYANCE' => (float) $request->input('conveyance', 0),
                'SPECIAL' => (float) $request->input('special_allowance', 0),
                'PF' => $pfAmount,
                'PT' => (float) $request->input('professional_tax', 0),
                'TDS' => $otherDeductionsAmount,
            ];

            foreach ($salaryInputs as $code => $amt) {
                $component = SalaryComponent::where('code', $code)->first();
                if ($component) {
                    EmployeeSalaryComponent::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'salary_component_id' => $component->id,
                        ],
                        [
                            'salary_structure_id' => $salaryStructure->id,
                            'amount' => $amt,
                            'effective_from' => $validated['joining_date'],
                        ]
                    );
                }
            }

            // 4. Update Bank Account
            if (!empty($validated['bank_name']) && !empty($validated['account_number'])) {
                $bankAccount = $employee->bankAccounts()->first();
                if ($bankAccount) {
                    $bankAccount->update([
                        'bank_name' => $validated['bank_name'],
                        'account_holder_name' => $validated['account_holder_name'] ?: $fullName,
                        'account_number_encrypted' => encrypt($validated['account_number']),
                        'ifsc_code' => strtoupper($validated['ifsc_code'] ?: 'HDFC0001234'),
                        'branch_name' => $validated['branch_name'] ?? null,
                    ]);
                } else {
                    EmployeeBankAccount::create([
                        'employee_id' => $employee->id,
                        'account_holder_name' => $validated['account_holder_name'] ?: $fullName,
                        'bank_name' => $validated['bank_name'],
                        'account_number_encrypted' => encrypt($validated['account_number']),
                        'ifsc_code' => strtoupper($validated['ifsc_code'] ?: 'HDFC0001234'),
                        'branch_name' => $validated['branch_name'] ?? null,
                        'is_primary' => true,
                        'is_verified' => true,
                    ]);
                }
            }

            // 5. Audit Log
            AuditLog::create([
                'user_id' => $currentUser->id,
                'action' => 'EMPLOYEE_UPDATED',
                'auditable_type' => Employee::class,
                'auditable_id' => $employee->id,
                'new_values' => [
                    'employee_code' => $employee->employee_code,
                    'name' => $fullName,
                    'department_id' => $employee->department_id,
                    'role' => $validated['role_slug'],
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        });

        return redirect()->route('employees.show', $employee)->with(
            'success',
            "Profile for {$employee->full_name} ({$employee->employee_code}) was successfully updated!"
        );
    }
}
