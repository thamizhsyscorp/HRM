@extends('layouts.app')

@section('title', 'Edit Staff Profile: ' . $employee->full_name)
@section('page_title', 'Edit Staff Profile')

@section('content')
<div class="space-y-6 max-w-5xl">

    <!-- Top Navigation Header -->
    <div class="bg-white rounded-2xl border border-slate-100 p-5 sm:p-6 shadow-sm flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 font-mono font-bold text-xs rounded-md bg-blue-50 text-[#1e3a8a] border border-blue-200">{{ $employee->employee_code }}</span>
                <h2 class="text-base sm:text-lg font-extrabold text-slate-900">Editing Profile: {{ $employee->full_name }}</h2>
            </div>
            <p class="text-xs text-slate-400 mt-1">Update employee information, organizational placement, system access roles, compensation, and bank details</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.show', $employee) }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition flex items-center gap-1.5">
                <span>View Profile</span>
            </a>
            <a href="{{ route('employees.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition flex items-center gap-1.5">
                <span>← Staff Directory</span>
            </a>
        </div>
    </div>

    <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- SECTION 1: Personal & Login Credentials -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-7 shadow-sm">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs border border-blue-200">
                    1
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Personal Identity & Login Account</h3>
                    <p class="text-xs text-slate-400">Authenticatable employee user profile and primary contact details</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- First Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">First Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-medium text-slate-900">
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-medium text-slate-900">
                </div>

                <!-- Work Email -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Work Email (Login Username) <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $employee->work_email) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono text-slate-900">
                </div>

                <!-- Change Password -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">New Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep unchanged" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono text-slate-900">
                    <span class="text-[11px] text-slate-400 mt-1 block">Only enter if you wish to reset login credentials</span>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Primary Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" placeholder="+91 98765 00000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono text-slate-900">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Gender</label>
                    <select name="gender" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $employee->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $employee->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $employee->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', optional($employee->date_of_birth)->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                </div>

                <!-- Emergency Contact Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] text-slate-900">
                </div>

                <!-- Emergency Contact Phone -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono text-slate-900">
                </div>

                <!-- Address -->
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Residential Address</label>
                    <input type="text" name="address" value="{{ old('address', $employee->address) }}" placeholder="Street address, city, state" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] text-slate-900">
                </div>
            </div>
        </div>

        <!-- SECTION 2: Organization, Hierarchy & System Access Role -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-7 shadow-sm">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs border border-blue-200">
                    2
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Job Role, Organization Hierarchy & System Access</h3>
                    <p class="text-xs text-slate-400">Department allocation, reporting line manager, and administrative system role</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Employee Code -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Employee ID Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-bold bg-blue-50 text-[#1e3a8a]">
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Department <span class="text-rose-500">*</span></label>
                    <select name="department_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-medium">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }} ({{ $dept->code ?? 'Dept' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Designation -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Designation Title <span class="text-rose-500">*</span></label>
                    <select name="designation_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-medium">
                        @foreach($designations as $desig)
                            <option value="{{ $desig->id }}" {{ old('designation_id', $employee->designation_id) == $desig->id ? 'selected' : '' }}>
                                {{ $desig->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Reporting Manager -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Reporting Manager</label>
                    <select name="manager_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-medium">
                        <option value="">No Direct Manager (Top Management / Executive)</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}" {{ old('manager_id', $employee->manager_id) == $mgr->id ? 'selected' : '' }}>
                                {{ $mgr->full_name }} ({{ $mgr->employee_code }}) &bull; {{ $mgr->designation->name ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- System Access Role -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">System Access Role <span class="text-rose-500">*</span></label>
                    <select name="role_slug" id="roleSelectEdit" onchange="toggleManagerOptionsEdit()" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-bold">
                        <option value="employee" {{ old('role_slug', $currentRoleSlug) === 'employee' ? 'selected' : '' }}>Employee (Standard Self-Service)</option>
                        <option value="manager" {{ old('role_slug', $currentRoleSlug) === 'manager' ? 'selected' : '' }}>Department Manager (Oversees Department Staff & Attendance)</option>
                        <option value="hr-manager" {{ old('role_slug', $currentRoleSlug) === 'hr-manager' ? 'selected' : '' }}>HR Manager (Human Resources & Leave Approver)</option>
                        <option value="super-admin" {{ old('role_slug', $currentRoleSlug) === 'super-admin' ? 'selected' : '' }}>Super Admin (Unrestricted System Access)</option>
                    </select>
                </div>

                <!-- Work Location -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Work Location</label>
                    <select name="work_location_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-medium">
                        @foreach($workLocations as $loc)
                            <option value="{{ $loc->id }}" {{ old('work_location_id', $employee->work_location_id) == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Employment Status -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Employment Status <span class="text-rose-500">*</span></label>
                    <select name="employment_status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900 font-bold">
                        <option value="ACTIVE" {{ old('employment_status', $employee->employment_status) === 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                        <option value="PROBATION" {{ old('employment_status', $employee->employment_status) === 'PROBATION' ? 'selected' : '' }}>PROBATION</option>
                        <option value="INACTIVE" {{ old('employment_status', $employee->employment_status) === 'INACTIVE' ? 'selected' : '' }}>INACTIVE</option>
                        <option value="SUSPENDED" {{ old('employment_status', $employee->employment_status) === 'SUSPENDED' ? 'selected' : '' }}>SUSPENDED</option>
                        <option value="TERMINATED" {{ old('employment_status', $employee->employment_status) === 'TERMINATED' ? 'selected' : '' }}>TERMINATED</option>
                    </select>
                </div>

                <!-- Employment Type -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Employment Type <span class="text-rose-500">*</span></label>
                    <select name="employment_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                        <option value="FULL_TIME" {{ old('employment_type', $employee->employment_type) === 'FULL_TIME' ? 'selected' : '' }}>Full Time (Permanent)</option>
                        <option value="PART_TIME" {{ old('employment_type', $employee->employment_type) === 'PART_TIME' ? 'selected' : '' }}>Part Time</option>
                        <option value="CONTRACT" {{ old('employment_type', $employee->employment_type) === 'CONTRACT' ? 'selected' : '' }}>Contractual</option>
                        <option value="INTERN" {{ old('employment_type', $employee->employment_type) === 'INTERN' ? 'selected' : '' }}>Intern</option>
                    </select>
                </div>

                <!-- Joining Date -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Joining Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', optional($employee->joining_date)->format('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] bg-white text-slate-900">
                </div>

                <!-- Department Lead Option (shows when Manager role selected) -->
                <div id="managerDeptOptionEdit" class="{{ old('role_slug', $currentRoleSlug) === 'manager' ? '' : 'hidden' }} sm:col-span-2 lg:col-span-3 p-3.5 rounded-xl bg-blue-50/80 border border-blue-200">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="is_department_head" value="1" {{ old('is_department_head', $isDeptHead) ? 'checked' : '' }} class="rounded border-slate-300 text-[#1e3a8a] focus:ring-[#1e3a8a] w-4 h-4">
                        <span class="text-xs font-bold text-[#1e3a8a]">Designate as Head / Lead of the Selected Department</span>
                    </label>
                    <span class="text-[11px] text-slate-500 block mt-1 pl-6">Empowers employee to supervise workforce logs, attendance timestamps, and department leave applications.</span>
                </div>
            </div>
        </div>

        <!-- SECTION 3: Salary Components & Compensation (INR) -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-7 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs border border-blue-200">
                        3
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Monthly Compensation & Statutory Structure</h3>
                        <p class="text-xs text-slate-400">Configures employee initial salary breakdown components (INR)</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400 font-medium">Monthly Gross</span>
                    <p id="previewMonthlyGrossEdit" class="text-xl font-extrabold text-[#1e3a8a] font-mono">₹{{ number_format(($salaryMap['BASIC'] ?? 35000) + ($salaryMap['HRA'] ?? 14000) + ($salaryMap['CONVEYANCE'] ?? 2000) + ($salaryMap['SPECIAL'] ?? 6000), 2) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Basic Pay -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Basic Salary (Monthly) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpBasicEdit" name="basic_salary" value="{{ old('basic_salary', $salaryMap['BASIC'] ?? 35000) }}" oninput="updateSalaryPreviewEdit()" required min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                </div>

                <!-- HRA -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">House Rent Allowance (HRA)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpHraEdit" name="hra" value="{{ old('hra', $salaryMap['HRA'] ?? 14000) }}" oninput="updateSalaryPreviewEdit()" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                </div>

                <!-- Conveyance -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Conveyance Allowance</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpConveyanceEdit" name="conveyance" value="{{ old('conveyance', $salaryMap['CONVEYANCE'] ?? 2000) }}" oninput="updateSalaryPreviewEdit()" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                </div>

                <!-- Special Allowance -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Special Allowance</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpSpecialEdit" name="special_allowance" value="{{ old('special_allowance', $salaryMap['SPECIAL'] ?? 6000) }}" oninput="updateSalaryPreviewEdit()" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                </div>

                <!-- Provident Fund -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Provident Fund (PF Deduction)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpPfEdit" name="provident_fund" value="{{ old('provident_fund', $salaryMap['PF'] ?? 1800) }}" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                </div>

                <!-- Professional Tax -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Professional Tax (PT)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-semibold text-sm">₹</span>
                        <input type="number" id="inpPtEdit" name="professional_tax" value="{{ old('professional_tax', $salaryMap['PT'] ?? 200) }}" min="0" class="w-full pl-8 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono font-semibold text-slate-900">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 4: Bank Account & Disbursal -->
        <div class="bg-white rounded-2xl border border-slate-100 p-6 sm:p-7 shadow-sm">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4 mb-6">
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1e3a8a] font-extrabold flex items-center justify-center text-xs border border-blue-200">
                    4
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Bank Account Details (Disbursal)</h3>
                    <p class="text-xs text-slate-400">Employee account for automated payroll disbursements</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $primaryBank?->bank_name) }}" placeholder="e.g. State Bank of India" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] text-slate-900 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Account Holder Name</label>
                    <input type="text" name="account_holder_name" value="{{ old('account_holder_name', $primaryBank?->account_holder_name ?? $employee->full_name) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] text-slate-900 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Account Number</label>
                    @php
                        $decryptedAcct = '';
                        if ($primaryBank?->account_number_encrypted) {
                            try { $decryptedAcct = decrypt($primaryBank->account_number_encrypted); } catch (\Exception $e) {}
                        }
                    @endphp
                    <input type="text" name="account_number" value="{{ old('account_number', $decryptedAcct) }}" placeholder="e.g. 123456789012" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono text-slate-900 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">IFSC Code</label>
                    <input type="text" name="ifsc_code" value="{{ old('ifsc_code', $primaryBank?->ifsc_code) }}" placeholder="e.g. SBIN0001234" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] font-mono uppercase text-slate-900 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Branch Name</label>
                    <input type="text" name="branch_name" value="{{ old('branch_name', $primaryBank?->branch_name) }}" placeholder="Branch location" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-[#1e3a8a] focus:border-[#1e3a8a] text-slate-900 font-medium">
                </div>
            </div>
        </div>

        <!-- Submit Toolbar -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('employees.show', $employee) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition">
                Cancel
            </a>
            <button type="submit" class="px-7 py-3 rounded-xl bg-[#1e3a8a] hover:bg-[#1e40af] text-white text-xs font-extrabold shadow-sm transition cursor-pointer flex items-center gap-2">
                <span>Save Profile Changes</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function updateSalaryPreviewEdit() {
        const basic = parseFloat(document.getElementById('inpBasicEdit').value) || 0;
        const hra = parseFloat(document.getElementById('inpHraEdit').value) || 0;
        const conveyance = parseFloat(document.getElementById('inpConveyanceEdit').value) || 0;
        const special = parseFloat(document.getElementById('inpSpecialEdit').value) || 0;
        const total = basic + hra + conveyance + special;
        document.getElementById('previewMonthlyGrossEdit').textContent = '₹' + total.toLocaleString('en-IN');
    }

    function toggleManagerOptionsEdit() {
        const role = document.getElementById('roleSelectEdit')?.value;
        const opt = document.getElementById('managerDeptOptionEdit');
        if (opt) {
            if (role === 'manager') {
                opt.classList.remove('hidden');
            } else {
                opt.classList.add('hidden');
            }
        }
    }
</script>
@endsection
